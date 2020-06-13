/**
 * Author:    Satyam Gaba <sgaba@ucsd.edu>
 * Created:   06/10/2020
 * 
 **/

#include <stdbool.h>
#include <stdint.h>
#include <stdio.h>
#include <string.h>
#include <signal.h>
#include <stdio.h>
#include <errno.h>
#include <getopt.h>
#include <inttypes.h>
#include <cstring>
#include <iio.h>
#include <lsl_cpp.h>
#include <thread>
#include <iostream>
// #include "libimu.h"

#define ARRAY_SIZE(arr) (sizeof(arr) / sizeof((arr)[0]))

#define IIO_ENSURE(expr) { \
	if (!(expr)) { \
		(void) fprintf(stderr, "assertion failed (%s:%d)\n", __FILE__, __LINE__); \
		(void) abort(); \
	} \
}

// // Streaming device
struct imu_device{
	// device identification 
	const char *dev_name;
	const char *trigger_str;
	iio_device *dev;

	/* IIO structs required for streaming */
	iio_context *ctx;
	iio_buffer  *rxbuf;
	iio_channel **channels;
	unsigned int channel_count;

	// hardware trigger
	iio_device *trigger;

	bool has_ts;
};


imu_device *devices;
unsigned int number_devices = 0;

bool stop;
bool has_repeat;

void handle_sig(int sig)
{
	printf("Waiting for process to finish... got signal : %d\n", sig);
	stop = true;
}

/* cleanup and exit */
void shutdown()
{
	int ret;
	for (int i =0; i < number_devices; i++){
		if (devices[i].channels) { free(devices[i].channels); }

		printf("* Destroying buffers %d\n", i);
		if (devices[i].rxbuf) { iio_buffer_destroy(devices[i].rxbuf); }

		printf("* Disassociate trigger %d\n", i);
		if (devices[i].dev) {
			ret = iio_device_set_trigger(devices[i].dev, NULL);
			if (ret < 0) {
				char buf[256];
				iio_strerror(-ret, buf, sizeof(buf));
				fprintf(stderr, "%s (%d) while Disassociate trigger %d\n", buf, ret, i);
			}
		}

		printf("* Destroying context %d\n", i);
		if (devices[i].ctx) { iio_context_destroy(devices[i].ctx); }
	}
	exit(0);
}



int send_imu_data(const char** dev_names, const char** trigger_str,
						const int num_dev,const int buffer_length,
						std::string lsl_stream_name, std::string lsl_stream_type){

	// void **device;
	number_devices = num_dev;
	devices = new imu_device[num_dev];
	for(int i = 0; i<num_dev; i++){
		devices[i].dev_name = dev_names[i];
		devices[i].trigger_str = trigger_str[i];
		std::cout << devices[i].dev_name<<std::endl;;
	}

	// Listen to ctrl+c and assert
	signal(SIGINT, handle_sig);

	unsigned int i, j, major, minor;
	char git_tag[8];
	iio_library_get_version(&major, &minor, git_tag);
	printf("Library version: %u.%u (git tag: %s)\n", major, minor, git_tag);

	/* check for struct iio_data_format.repeat support
	 * 0.8 has repeat support, so anything greater than that */
	has_repeat = ((major * 10000) + minor) >= 8 ? true : false;

	printf("* Acquiring IIO context\n");
	for(int i =0; i<num_dev; i++){
		IIO_ENSURE((devices[i].ctx = iio_create_default_context()) && "No context");
		IIO_ENSURE(iio_context_get_devices_count(devices[i].ctx) > 0 && "No devices");
	}

	for(int i =0; i<num_dev; i++){
		printf("* Acquiring device %s\n", dev_names[i]);
		devices[i].dev = iio_context_find_device(devices[i].ctx, devices[i].dev_name);
		if (!devices[i].dev) {
			perror("No device found");
			shutdown();
		}
	}

	printf("* Initializing IIO streaming channels:\n");
	for (int i = 0; i< num_dev; i++){
		for (int j = 0; j < iio_device_get_channels_count(devices[i].dev); ++j) {
			struct iio_channel *chn = iio_device_get_channel(devices[i].dev, j);
			if (iio_channel_is_scan_element(chn)) {
				printf("%s\n", iio_channel_get_id(chn));
				devices[i].channel_count++;
			}
		}
		if (devices[i].channel_count == 0) {
			printf("No scan elements found in device: %d\n", i);
			shutdown();
		}

		devices[i].channels = (iio_channel **)calloc(devices[i].channel_count, sizeof *devices[i].channels);
		if (!devices[i].channels) {
			perror("Channel array allocation failed");
			shutdown();
		}
		for (int j = 0; j < devices[i].channel_count; ++j) {
		struct iio_channel *chn = iio_device_get_channel(devices[i].dev, j);
		if (iio_channel_is_scan_element(chn))
			devices[i].channels[j] = chn;
		}
	}

	for (int i = 0; i< num_dev; i++){
		printf("* Acquiring trigger %s for device: %d \n", devices[i].trigger_str, i);
		devices[i].trigger = iio_context_find_device(devices[i].ctx, devices[i].trigger_str);
		if (!devices[i].trigger || !iio_device_is_trigger(devices[i].trigger)) {
			perror("No trigger found (try setting up the iio-trig-hrtimer module)");
			shutdown();
		}
		
		printf("* Enabling IIO streaming channels for buffered capture\n");
		for (int j = 0; j < devices[i].channel_count; ++j)
			iio_channel_enable(devices[i].channels[j]);

		printf("* Enabling IIO buffer trigger\n");
		if (iio_device_set_trigger(devices[i].dev, devices[i].trigger)) {
			perror("Could not set trigger\n");
			shutdown();
		}

		printf("* Creating non-cyclic IIO buffers with %d samples\n", buffer_length);
		devices[i].rxbuf = iio_device_create_buffer(devices[i].dev, buffer_length, false);
		if (!devices[i].rxbuf) {
			perror("Could not create buffer");
			shutdown();
		}
	}

	unsigned int total_channel_count = 0;
	for(int i=0; i<num_dev; i++){
		total_channel_count += devices[i].channel_count;
	}

	std::cout << total_channel_count<<std::endl;

	printf("* Initializing lsl outlet with %d channels\n", total_channel_count+1);
	lsl::stream_info info(lsl_stream_name,lsl_stream_type, total_channel_count+1 ); //all channels stream (+1 for sample number)
   	lsl::stream_outlet outlet(info);
   	//empty vector for sample for lsl
	std::vector<float> send_sample;


	printf("* Starting IO streaming (press CTRL+C to cancel)\n");
	for (int i=0; i<num_dev; i++){
		devices[i].has_ts = strcmp(iio_channel_get_id(devices[i].channels[devices[i].channel_count-1]), "timestamp") == 0;
	}
	int64_t last_ts = 0;

	// sample number for lsl
	float sample_number = 0;

	printf("* Reading data from iio devices...\n");
	while (!stop)
	{	
		for (int i=0; i<num_dev; i++){
			ssize_t nbytes_rx;
			/* we use a char pointer, rather than a void pointer, for p_dat & p_end
			 * to ensure the compiler understands the size is a byte, and then we
			 * can do math on it.
			 */
			char *p_dat, *p_end;
			ptrdiff_t p_inc;
			int64_t now_ts;

			// Refill RX buffer
			nbytes_rx = iio_buffer_refill(devices[i].rxbuf);
			if (nbytes_rx < 0) {
				printf("Error refilling buf: %d\n", (int)nbytes_rx);
				shutdown();
			}

			// print device number
			// fprintf(stderr, "Device: %d ", i);

			p_inc = iio_buffer_step(devices[i].rxbuf);
			p_end = (char *)iio_buffer_end(devices[i].rxbuf);

			// Print timestamp delta in ms
			if (devices[i].has_ts)
				for (p_dat = (char *)iio_buffer_first(devices[i].rxbuf, devices[i].channels[devices[i].channel_count-1]); p_dat < p_end; p_dat += p_inc) {
					now_ts = (((int64_t *)p_dat)[0]);
					int64_t latency = last_ts > 0 ? (now_ts - last_ts)/1000/1000 : 0;
					// fprintf(stderr, "[+%04" PRId64 "ms] ", last_ts > 0 ? (now_ts - last_ts)/1000/1000 : 0);
					// fprintf(stderr, "[+%04" PRId64 "ms] ", latency);
	            	send_sample.push_back((float)latency);
					last_ts = now_ts;
				}

			// Print each captured sample
			for (j = 0; j < devices[i].channel_count-1; ++j) {
				const struct iio_data_format *fmt = iio_channel_get_data_format(devices[i].channels[j]);
				unsigned int repeat = has_repeat ? fmt->repeat : 1;

				// fprintf(stderr, "%s ", iio_channel_get_id(devices[i].channels[j]));
				for (p_dat = (char *)iio_buffer_first(devices[i].rxbuf, devices[i].channels[j]); p_dat < p_end; p_dat += p_inc) {
					for (int k = 0; k < repeat; ++k) {
						if (fmt->length/8 == sizeof(int16_t)) {
							if (fmt->with_scale) {
								double val = fmt->scale * ((int16_t *)p_dat)[k];
								// fprintf(stderr, "%f ", val);
	            				send_sample.push_back((float)val); 
							} else {
								// fprintf(stderr, "%" PRIi16 " ", ((int16_t *)p_dat)[k]);
	            				send_sample.push_back( (float)((int16_t *)p_dat)[k] ); 

							}
						} else if (fmt->length/8 == sizeof(int64_t)){
							// fprintf(stderr, "%" PRId64 " ", ((int64_t *)p_dat)[k]);
	        				send_sample.push_back( (float)((int64_t *)p_dat)[k] ); 
						}

					}
				}
			}
			// printf("\n");
		}

		// send samples to lsl
		try{
			bool last_cons;
			bool have_cons;
			if(outlet.have_consumers()){
			    have_cons = true;
				sample_number += 1;
				send_sample.push_back(sample_number);
			    outlet.push_sample(send_sample);
			    printf("Packet sent...\n");
			    last_cons = have_cons;
			} else
				have_cons = false;
				if (have_cons == false & last_cons == true){
					printf("No consumer found for lsl...\n");
				}
				last_cons = have_cons;
		} catch (std::exception& e) {
			std::cerr << "Got an exception in sending samples with lsl: " << e.what() << std::endl;
			std::cout << "Press any key to exit. " << std::endl;
			std::cin.get();
			shutdown();
		}
		// clear the sample vector
		send_sample.clear();
	}
		

	shutdown();

	return 0;
}



// // define libimu
// const int num_dev = 3;
// const char *dev_names[]   = {"iio:device1", "iio:device2", "iio:device3"};
// const char *trigger_str[] = {"trigger1", "trigger2","trigger3"};
// const int buffer_length = 1;

// // define lsl
// std::string lsl_stream_name = "my_name";
// std::string lsl_stream_type = "imu_data";

// /* simple configuration and streaming */
// int main ()
// {	
// 	send_imu_data(dev_names, trigger_str, num_dev, buffer_length, lsl_stream_name, lsl_stream_type);

// 	// Listen to ctrl+c and assert
// 	signal(SIGINT, handle_sig);
// 	return 0;
// }
