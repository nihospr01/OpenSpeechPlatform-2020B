/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#include <stdio.h>
#include <string.h>
#include <signal.h>
#include <stdlib.h>
#include <unistd.h>
#include <limits.h>
#include <execinfo.h>
#include <math.h>
#include <pthread.h>
#include <errno.h>

#include <sys/types.h>
#include <ifaddrs.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <netinet/in.h>

#include "osp_tcp.h"
#include "osp_process.h"
#include "resample.h"
#include "utilities.h"
#include "port_wrapper.h"
#include "constants.h"
#include "logger.h"

/*** Main constants ***/
#define UI_PORT				8001
#define SAMPLE_RATE 		96000
#define FRAMES_PER_BUFFER	96

#define D_ATTENUATION_FACTOR	(0.025)

#if !defined(ARRAY_SIZE)
#define ARRAY_SIZE(x) (sizeof((x)) / sizeof((x)[0]))
#endif

#define OPT_STRING "Enter \"a\" to enable hearing aid algorithm, \"d\" to disable \"q\" to quit\n"

static Resample resampleL; ///< Resample structure for left channel
static Resample resampleR; ///< Resample structure for right channel

static inline float log2lin(float db)
{
	if (db == 0) {
		return 1;
	}

	return powf(10, (db/20.0));
}

static inline void usage(const char *s)
{
	printf("usage: %s [-hardt] [-l <Loopback File>]\n", s);
	printf("options:\n");
	printf("\t-h\tshow this usage\n");
	printf("\t-a\tstart application with AFC set to OFF\n");
	printf("\t-r\tstart application with rear mics disabled (two channel only mode)\n");
	printf("\t-d\tset the attenuation factor (gain in dB)\n");
	printf("\t-t\tstart application in TCP daemon mode\n");
	printf("\t-T\tinitialize with selected AFC. 0=FXLMS and 1=PNLMS\n");
	printf("\t-M\tEnable/Disable MPO. 0= Disable MPO and 1 = Enable MPO\n");
	printf("\t-l\t<Loopback File>\tRun in loopback mode where <Loopback File> is input\n");
}

static void * announce_presence(void * tid)
{
	int clientSocket;
	struct sockaddr_in serverAddr;
	struct sockaddr_in localAddr;
	socklen_t addr_size;
	int broadcastEnable = 1;
	struct ifaddrs * ifAddrStruct = NULL;
	struct ifaddrs * ifa = NULL;
	void * tmpAddrPtr = NULL;
	char addressBuffer[INET_ADDRSTRLEN];
	//char addressBuffer[INET6_ADDRSTRLEN];
	char hostname[256];

	// Get hostname
	gethostname(hostname, 256);

	// Create UDP socket
	clientSocket = socket(PF_INET, SOCK_DGRAM, 0);

	// Configure settings in address struct
	localAddr.sin_family = AF_INET;
	localAddr.sin_port = htons(8001);
	inet_aton("192.168.2.1", &localAddr.sin_addr);
	memset(localAddr.sin_zero, '\0', sizeof localAddr.sin_zero);

	/* Now bind the host address using bind() call.*/
	if (bind(clientSocket, (struct sockaddr *) &localAddr, sizeof(localAddr)) < 0) {
		perror("bind()");
	}

	// Configure settings in address struct
	serverAddr.sin_family = AF_INET;
	serverAddr.sin_port = htons(8001);
	//serverAddr.sin_addr.s_addr = htonl(INADDR_BROADCAST);
	inet_aton("192.168.2.255", &serverAddr.sin_addr);
	memset(serverAddr.sin_zero, '\0', sizeof serverAddr.sin_zero);

	// Initialize size variable to be used later on
	addr_size = sizeof serverAddr;

	if(setsockopt(clientSocket, SOL_SOCKET, SO_BROADCAST, &broadcastEnable, sizeof(broadcastEnable)) == -1) {
		perror("setsockopt()");
	}

	if(getifaddrs(&ifAddrStruct) == -1) {
		perror("getifaddrs()");
	}

	for (ifa = ifAddrStruct; ifa != NULL; ifa = ifa->ifa_next) {
		if (!ifa->ifa_addr) {
			continue;
		}

		if (ifa->ifa_addr->sa_family == AF_INET) { // check it is IP4
																							 // is a valid IP4 Address
			tmpAddrPtr = &((struct sockaddr_in *)ifa->ifa_addr)->sin_addr;
			inet_ntop(AF_INET, tmpAddrPtr, addressBuffer, INET_ADDRSTRLEN);
		}
//		else if (ifa->ifa_addr->sa_family == AF_INET6) { // check it is IP6
//																											 // is a valid IP6 Address
//			tmpAddrPtr=&((struct sockaddr_in6 *)ifa->ifa_addr)->sin6_addr;
//			inet_ntop(AF_INET6, tmpAddrPtr, addressBuffer, INET6_ADDRSTRLEN);
//			printf("%s IP Address %s\n", ifa->ifa_name, addressBuffer);
//		}
	}

	if (ifAddrStruct != NULL)
		freeifaddrs(ifAddrStruct);

	while(1)
	{
		// Send message to server
		errno = 0;
		if(sendto(clientSocket,hostname,strlen(hostname),0,(struct sockaddr *)&serverAddr,addr_size) == -1)
			perror("sendto()");

		sleep(1);
	}
}

static void run_loopback(osp_user_data *osp_data, float *inL, float *inR, float *outL, float *outR, size_t len)
{
	float inL32[len / 3];
	float inR32[len / 3];

	float outL32[len / 3];
	float outR32[len / 3];

	size_t ret;
	int i;

	if (len % 3 != 0) {
		fprintf(stderr, "Incompatible frame size: %lu\n", len);
	}

	ret =
	resample_96_32(resampleL, inL, inL32, len);
	resample_96_32(resampleR, inR, inR32, len);

	if (osp_data->no_op) {
		for (i = 0; i < ret; i++) {
			outL32[i] = inL32[i];
			outR32[i] = inR32[i];
		}
	} else {
		// Prepped for multiple channels, but just feeding the same one
		// twice for now for beamforming
		osp_process_audio(osp_data,
											inL32, inR32,
											outL32, outR32, ret);
	}

	resample_32_96(resampleL, outL32, outL, len / 3);
	resample_32_96(resampleR, outR32, outR, len / 3);
}

//static void run_loopback_32(osp_user_data *osp_data, float *inL, float *inR, float *outL, float *outR, unsigned long len)
//{
//	unsigned long i;
//
//	if (len != 32) {
//		fprintf(stderr, "Incompatible frame size: %lu\n", len);
//	}
//
//	if (osp_data->no_op) {
//		for (i = 0; i < len; i++) {
//			outL[i] = inL[i];
//			outR[i] = inR[i];
//		}
//	} else {
//		// Prepped for multiple channels, but just feeding the same one
//		// twice for now for beamforming
//		osp_process_audio(osp_data,
//						inL, inR,
//						outL, outR, len);
//	}
//}

static int file_loopback_init(file_loopback_context *ctx, const char *in_file, const char *out_file)
{
	short in_s;

	if ((ctx->input_file = fopen(in_file, "rb")) == NULL) {
		return -1;
	}

	if ((ctx->output_file = fopen(out_file, "wb")) == NULL) {
		return -1;
	}

	ctx->index = 0;

	// Get size of the file in bytes.
	memcpy(ctx->wav_header, ctx->input_file, sizeof(ctx->wav_header));
	fseek(ctx->input_file, 0L, SEEK_END);
	// Length of file in bytes, divided by size of a short, divided by channels
	ctx->length = ftell(ctx->input_file) - 44;
	fseek(ctx->input_file, 44, SEEK_SET); // Past wav header
	ctx->length = ctx->length / sizeof(short) / 2; // Offset by the header

	// Allocate memory for the input channels
	ctx->inL = malloc(sizeof(float) * ctx->length);
	ctx->inR = malloc(sizeof(float) * ctx->length);

	// Allocate memory for the output channels
	ctx->outL = malloc(sizeof(float) * ctx->length);
	ctx->outR = malloc(sizeof(float) * ctx->length);

	// Read contents into Right and Left buffers
	printf("Reading file into memory, and handling clipping...\n");
	for (ctx->index = 0; ctx->index < ctx->length; ctx->index++) {
		if (fread(&in_s, sizeof(in_s), 1, ctx->input_file) < 1) {
			fprintf(stderr, "Error reading input file (1) at index %ld\n", ctx->index);
			return -1;
		}

		ctx->inL[ctx->index] = (float)in_s / (float)((SHRT_MAX));

		if (fread(&in_s, sizeof(in_s), 1, ctx->input_file) < 1) {
			fprintf(stderr, "Error reading input file (2) at index %ld\n", ctx->index);
			return -1;
		}

		ctx->inR[ctx->index] = (float)in_s / (float)((SHRT_MAX));

	}

	ctx->index = 0; // Have to set it 0 somewhere. idk where, though
	printf("\n"); // Clear clipping output

	return 0;
}

static void file_loopback_close(file_loopback_context *ctx)
{
	free(ctx->inL);
	free(ctx->inR);

	fclose(ctx->input_file);
	fclose(ctx->output_file);
}

static int file_context_write(file_loopback_context *file_ctx)
{
	// Detect clipping and fix, so there's no under/overflows when converting
	printf("Detecting clipping and writing output to file \n");
	for (file_ctx->index = 0; file_ctx->index < file_ctx->length; file_ctx->index++) {
		if (file_ctx->outL[file_ctx->index] > 1) {
			printf("C");
			//file_ctx->outL[file_ctx->index] = 1;
		} else if (file_ctx->outL[file_ctx->index] < -1) {
			printf("C");
			//file_ctx->outL[file_ctx->index] = -1;
		}

		// Write sample to file in full float format
		if (fwrite(&file_ctx->outL[file_ctx->index], sizeof(float), 1, file_ctx->output_file) < 1) {
			perror("Error in file_context_write writing left sample\n");
			return -1;
		}

		// if (file_ctx->outR[file_ctx->index] > 1) {
		// 	printf("C");
		// 	//file_ctx->outR[file_ctx->index] = 1;
		// } else if (file_ctx->outR[file_ctx->index] < -1) {
		// 	printf("C");
		// 	//file_ctx->outR[file_ctx->index] = -1;
		// }
		//
		// // Write sample to file in full float format
		// if (fwrite(&file_ctx->outR[file_ctx->index], sizeof(float), 1, file_ctx->output_file) < 1) {
		// 	perror("Error in file_context_write writing left sample\n");
		// 	return -1;
		// }

	}

	printf("\n"); // Clear clipping printouts

	return 0;
}


// Called when loopback is selected with file
static int file_loopback_run(const char *in_file, const char *out_file, unsigned int frames_per_buffer)
{
	unsigned long i;

	file_loopback_context file_ctx;
	osp_user_data osp_data;

	osp_data_init(&osp_data);
	osp_data.feedback = 1;

	printf("Initializing file context for file_loopback\n");
	if (file_loopback_init(&file_ctx, in_file, out_file) < 0) {
		return -1;
	}

	// Loop through the file and do the processing
	printf("Running through loopback processing...\n");
	for (i = 0; i < file_ctx.length; i += frames_per_buffer) {
		//run_loopback_32(&osp_data, file_ctx.inL + i, file_ctx.inR + i, file_ctx.outL + i, file_ctx.outR + i, frames_per_buffer);
		run_loopback(&osp_data, file_ctx.inL + i, file_ctx.inR + i, file_ctx.outL + i, file_ctx.outR + i, frames_per_buffer);
	}

	if (file_context_write(&file_ctx) < 0) {
		printf("Error writing file\n");
		return -1;
	}

	file_loopback_close(&file_ctx);

	return 0;
}

static int run_pa_loopback(unsigned int sample_rate, unsigned int frames_per_buffer, const char *in_file, const char *out_file)
{
	pa_loopback_data loopback_data;
	osp_user_data osp_data;
	file_loopback_context file_ctx;

	osp_data_init(&osp_data);

	loopback_data.file_ctx = &file_ctx;
	loopback_data.osp_data = &osp_data;

	// Initialize file context struct
	printf("Initializing file context for pa_loopback\n");
	if (file_loopback_init(loopback_data.file_ctx, in_file, out_file) < 0) {
		printf("Error initializing file context for pa_loopback mode\n");
		return -1;
	}

	loopback_data.osp_data->no_op = 0;
	if (pa_loopback_run(&loopback_data, sample_rate, frames_per_buffer) < 0) {
		printf("Error running portaudio loopback mode\n");
	}

	if (file_context_write(loopback_data.file_ctx) < 0) {
		printf("Error writing file\n");
	}

	file_loopback_close(loopback_data.file_ctx);

	return 0;
}

// Called when loopback is selected without file (null test)
static void run_loopback_null(unsigned long count, unsigned int frames_per_buffer)
{
	float inL[frames_per_buffer];
	float inR[frames_per_buffer];
	float outL[frames_per_buffer];
	float outR[frames_per_buffer];

	unsigned long i = 0;

	printf("Running null loopback mode for %ld iterations\n", count);

	osp_user_data osp_data;

	osp_data_init(&osp_data);

	while (i < count) {
		run_loopback(&osp_data, inL, inR, outL, outR, frames_per_buffer);
		i++;
	}
}

static int init_pa(pa_user_data *pa_data, unsigned int samp_rate, unsigned int frames_per_buffer)
{
	// init PA stream
	if (pa_init(pa_data, samp_rate, frames_per_buffer) < 0) {
		fprintf(stderr, "Error initializing port audio\n");
		return -1;
	}

	// start PA stream
	if (pa_start_stream() < 0) {
		fprintf(stderr, "Error starting audio stream\n");
		return -1;
	}

	return 0;
}

static int close_pa()
{
	// stop PA stream
	if (pa_stop_stream() < 0) {
		fprintf(stderr, "Error stopping audio stream\n");
		return -1;
	}

	// close PA stream
	pa_close();

	return 0;
}

static int run_pa_key(osp_user_data *osp_data, unsigned int samp_rate, unsigned int frames_per_buffer, float attenuation_factor)
{
	char input = 0;
	pa_user_data pa_data;
	pa_data.user_data = osp_data;
	pa_data.aux_data.attenuation_factor = attenuation_factor;
	pa_data.aux_data.gain_factor = 1;

	printf("Feedback is %d\n", osp_data->feedback);

	if (init_pa(&pa_data, samp_rate, frames_per_buffer) < 0) {
		printf("Error initializing port audio stuff\n");
		return -1;
	}

	for (;;) {
		input = getchar();
		if (input == 'a') {
			fprintf(stdout, "Turning ON heading aid algorithm...\n");
			osp_data->no_op = 0;
		} else if (input == 'd') {
			fprintf(stdout, "Turning OFF heading aid algorithm...\n");
			osp_data->no_op = 1;
		} else if (input == 'h') {
			char ch;
			int i = 0;
			char gain_s[5] = {0};
			float gain_factordB = 0;
			float gain_factor = 1;

			while ((ch = getchar()) != '\n') {
				if (i > (int)sizeof(gain_s)) {
					fprintf(stdout, "That value is too high\n");
					break;
				}

				if ((ch > '9' || ch < '0') && ch != '.') {
					continue;
				}

				gain_s[i] = ch;
				i++;
			}

			gain_factordB = strtof(gain_s, (char **)NULL);
			if (gain_factordB < 0.0 || gain_factordB > 1000.0) {
				fprintf(stdout, "[[[ DANGER ]]] Application will not set gain that high\n");
				continue;
			}

			gain_factor = log2lin(gain_factordB);
			fprintf(stdout, "Setting gain_factordB to %f (%f)\n", gain_factordB, gain_factor);
			pa_data.aux_data.gain_factor = gain_factor;
		} else if (input == 's') {
			osp_data->afc ^= 1;
			if (osp_data->afc) {
				fprintf(stdout, "Turning ON AFC\n");
			} else {
				fprintf(stdout, "Turning OFF AFC\n");
			}
		} else if (input == 'g') {
			char ch;
			int i = 0;
			char gain_s[5] = {0};
			int gain;

			while ((ch = getchar()) != '\n') {
				if (i > (int)sizeof(gain_s)) {
					fprintf(stdout, "That value is too high\n");
					break;
				}

				if ((ch > '9' || ch < '0')) {
					continue;
				}

				gain_s[i] = ch;
				i++;
			}

			gain = (int)strtol(gain_s, (char **)NULL, 10);
			if (gain < 0 || gain > 100) {
				fprintf(stdout, "[[[ DANGER ]]] Application will not set gain that high\n");
				continue;
			}

#if 0
			if ((gain + pa_data.aux_data.attenuation_factor) > 50) {
				printf("[[[ WARNING ]]] This is above the recommended gain setting. Gain: %f, attenuation: %f\n",
							 gain, pa_data.aux_data.attenuation_factor);
			}
#endif

			fprintf(stdout, "Setting gain to %d\n", gain);
			osp_data_set_gain(osp_data, gain);
		} else if (input == 'c') {
			osp_dump_afc_filter();
		} else if (input == 'f') {
			osp_data->feedback ^= 1;
			if (osp_data->feedback) {
				fprintf(stdout, "Turning ON feedback\n");
			} else {
				fprintf(stdout, "Turning OFF feedback\n");
			}
		} else if (input == '1') {
			printf("Setting N2\n");
			osp_data_set_n2(osp_data);
		} else if (input == '2') {
			printf("Setting N4\n");
			osp_data_set_n4(osp_data);
		} else if (input == '3') {
			printf("Setting S2\n");
			osp_data_set_s2(osp_data);
		} else if (input == '0') {
			printf("Setting NH\n");
			osp_data_set_nh(osp_data);
		} else if (input == 'q') {
			break;
		}

		printf("%s", OPT_STRING);
	}

	printf("Key mode completed: summary:\n");
	printf("Underruns: %d\n", pa_data.aux_data.underruns);

	if (close_pa() < 0) {
		printf("Error closing down port audio stuff\n");
		return -1;
	}

	return 0;
}

static int run_pa_tcp(osp_user_data *osp_data, unsigned int samp_rate, unsigned int frames_per_buffer, float attenuation_factor)
{
	ssize_t ret;
	int i;
	FILE *log_file = NULL;
	int tcp_running = 1;
	char message[512];

	unsigned int num_fails = 0;

	Osp_tcp osp_tcp;
	char req;

	pa_user_data pa_data;
	pa_data.user_data = osp_data;
	pa_data.aux_data.attenuation_factor = attenuation_factor;
	pa_data.aux_data.gain_factor = 1;

	osp_user_data *osp_tmp_data;

	osp_tmp_data = malloc(sizeof(osp_user_data));
	memcpy(osp_tmp_data, osp_data, sizeof(osp_user_data));
	printf("REAR MICS = %d\n", osp_tmp_data->rear_mics);

	// init TCP connection
	printf("Initializing TCP connection.\n");
	if ((osp_tcp = osp_tcp_init(UI_PORT)) == NULL) {
		printf("There was an error initializing OSP TCP layer\n");
		return -1;
	}

	if (init_pa(&pa_data, samp_rate, frames_per_buffer) < 0) {
		printf("Error initializing port audio stuff\n");
		return -1;
	}

	printf("Entering infinite for loop\n");
	for (;;) {
		fprintf(stderr, "Waiting for a connection from the client...");
		if (osp_tcp_connect(osp_tcp) < 0) {
			printf("Error on getting server connection\n");
			if (num_fails < 5) {
				num_fails++;
				continue;
			}

			fprintf(stderr, "Failed to set up TCP server 5 times, aborting\n");
			break;
		}

		printf("done. Client connected\n");
		pa_data.aux_data.underruns = 0;
		tcp_running = 1;

		while (tcp_running == 1) {
#if 0
			ret = read_tcp_server_stream(ui_connfd, &req, sizeof(req));
			if (ret < 0) {
				perror("Failed to read connection, resetting\n");
				break;
			} else if (ret == 0){
				printf("Client side closed, resetting connection\n");
				break;
			}
#endif

			if ((req = osp_tcp_read_req(osp_tcp)) < 0) {
				printf("Failed to read connection, resetting\n");
				tcp_running = 0;
				break;
			} else if (req == OSP_WRONG_VERSION) {
				printf("Wrong version OSP packet from client\n");
			}

			fprintf(stderr, "Got req %d\n", req);

			switch (req) {
				case OSP_REQ_UPDATE_VALUES:
					printf("Request to update OSP values\n");
#if 0
					ret = read_tcp_server_stream(ui_connfd, (char *)osp_tmp_data, sizeof(osp_user_data));
#endif
					if ((ret = osp_tcp_read_values(osp_tcp, osp_tmp_data, sizeof(osp_user_data))) < 0) {
						printf("Failed to read \"values\" packet from client\n");
						tcp_running = 0;
						break;
					} else if (ret == 0) {
						tcp_running = 0;
						printf("There was a disconnect trying to read \"values\" packet from client\n");
						break;
					}

#if 0
					if (file_logger_log_osp_data(log_file, osp_tmp_data) < 0) {
						fprintf(stderr, "Failed to log values to log file\n");
					}
#endif

					printf("Read %zd, sizeof osp_user_data %lu\n", ret, sizeof(osp_user_data));
					printf("Got no op: %d\n", osp_tmp_data->no_op);
					printf("AFC: %d\n", osp_tmp_data->afc);
					printf("Rear Mics: %d\n", osp_tmp_data->rear_mics);
					printf("Feedback: %d\n", osp_tmp_data->feedback);

					for (i = 0; i < NUM_BANDS; i++) {
						osp_data->g50[i] = osp_tmp_data->g50[i];
						osp_data->g80[i] = osp_tmp_data->g80[i];
						printf("Gains: G50: %d, G80: %d\n", osp_data->g50[i], osp_data->g80[i]);
					}

					for (i = 0; i < NUM_BANDS; i++) {
						osp_data->knee_low[i] = osp_tmp_data->knee_low[i];
						osp_data->knee_high[i] = osp_tmp_data->knee_high[i];
						printf("knee low: %d, knee high: %d\n", osp_data->knee_low[i], osp_data->knee_high[i]);
					}

					for (i = 0; i < NUM_BANDS; i++) {
						osp_data->attack[i] = osp_tmp_data->attack[i];
						osp_data->release[i] = osp_tmp_data->release[i];
						printf("Attack: %d, Release: %d\n", osp_data->attack[i], osp_data->release[i]);
					}

					printf("\n");
					break;
				case OSP_REQ_GET_UNDERRUNS:
					printf("Received underrun request packet\n");
					//fprintf(stderr, "Sending underruns %d\n", pa_data.aux_data.underruns);
					if (osp_tcp_send_underruns(osp_tcp, pa_data.aux_data.underruns) < 0) {
						printf("Error sending underrun packet to client\n");
						tcp_running = 0;
					}
					break;
				case OSP_REQ_GET_NUM_BANDS:
					printf("Request to report number of bands\n");
					if (osp_tcp_send_num_bands(osp_tcp, osp_get_num_bands()) < 0) {
						printf("Failed to notify client to num of bands\n");
						tcp_running = 0;
					}
					break;
				case OSP_REQ_USER_ID:
					// Open log with given username
					osp_tcp_read_user_packet(osp_tcp, message, sizeof(message));

					if ((log_file = file_logger_init(message)) == NULL) {
						fprintf(stderr, "Failed to open Log File\n");
						tcp_running = 0;
					}

					printf("Got userID packet from host: %s\n", message);
					break;
				case OSP_REQ_USER_ACTION:
					// Log the string straight to the log
					osp_tcp_read_user_packet(osp_tcp, message, sizeof(message));
					file_logger_log_message(log_file, message);
					printf("Got userAction packet from host\n");
					break;
				case OSP_DISCONNECT:
					file_logger_close(log_file);
					tcp_running = 0;
					break;
				default:
					printf("Did not recognize packet received\n");
					break;
			}
		}
	}

	// close TCP stuff
	osp_tcp_close(osp_tcp);

	// Close PA stuff
	if (close_pa() < 0) {
		printf("Error closing down port audio stuff\n");
		return -1;
	}

	return 0;
}



int main(int argc, char **argv)
{
	int c;
	unsigned int tcp_mode;
	unsigned int adaptation_type;
	unsigned int ansi_test_onoff;
	float resample_32_96_taps[RESAMP_32_96_TAPS];
	float resample_96_32_taps[RESAMP_96_32_TAPS];
	float attenuation_factor = D_ATTENUATION_FACTOR;

	pthread_t announce_thread;

	osp_user_data osp_data;
	osp_data_init(&osp_data);

	if (argc < 2) {
		usage(argv[0]);
	}

	adaptation_type = 2;
	tcp_mode = 0;
	while ((c = getopt(argc, argv, "ad:M:l:p:rtT:")) != -1) {
		switch (c) {
			case 'M':
				ansi_test_onoff = (unsigned char)strtol(optarg, (char **)NULL, 10);
				if(ansi_test_onoff == 0) {

						osp_data.mpo_on = 0;
						attenuation_factor = 0;
						printf("MPO disabled for ANSI test\n");
						printf("Attenuation factor is set as %f dB\n", attenuation_factor);
						attenuation_factor = log2lin(attenuation_factor);
						printf("Attenuation factor in linear scale is %f\n", attenuation_factor);

					}
					else{
						osp_data.mpo_on = 1;
						printf("MPO enabled\n");
				}
				break;
			case 'd':
				if (optarg == NULL) {
					printf("No argument for attenuation_factor.  Setting default %f\n", attenuation_factor);
					break;

				}

				attenuation_factor = strtof(optarg, (char **)NULL);

				printf("Attenuation factor is %f dB\n", attenuation_factor);
				attenuation_factor = log2lin(attenuation_factor);
				printf("Attenuation factor is %f\n", attenuation_factor);
				break;
			case 'l':
				if (strcmp(optarg, "NULL") == 0 || strcmp(optarg, "null") == 0) {
					printf("Loopback mode specified, using no file, just empty buffers\n");
					run_loopback_null(10000, FRAMES_PER_BUFFER);
				} else {
					printf("Loopback mode specified, setting up file %s\n", optarg);
					file_loopback_run(optarg, "output_file_test", 96);
				}
				break;
			case 'p':
				if (strcmp(optarg, "NULL") == 0 || strcmp(optarg, "null") == 0) {
					printf("Portaudio loopback mode specified, using no file, just empty buffers\n");
				} else {
					if (run_pa_loopback(SAMPLE_RATE, FRAMES_PER_BUFFER,
															optarg, "output_file_pa") < 0) {
						printf("There was an error running the PA mode with files\n");
					}
					printf("Portaudio loopback mode specified, setting up file %s\n", optarg);
				}
				break;
			case 't':
				printf("Using TCP daemon mode\n");
				tcp_mode = 1;
				break;
			case 'r':
				printf("Disabling Rear mics\n");
				osp_data.rear_mics = 0;
				break;
			case 'a':
				printf("AFC has been disabled\n");
				osp_data.afc = 0;
				break;
			case 'T':
				if (optarg == NULL) {
					printf("No argument for AFC adaptation type, setting to PNLMS\n");
					adaptation_type = 1;
				}

				adaptation_type = (unsigned char)strtol(optarg, (char **)NULL, 10);
				switch (adaptation_type) {
					case 0:
						printf("Setting adaptation type to FXLMS\n");
						break;
					case 1:
					default:
						printf("Setting adaptation type to PNLMS\n");
				}
				break;
			case 'h':
				usage(argv[0]);
				return 0;
				break;
			default:
				usage(argv[0]);
				return 0;
		}
	}

	// Initialize osp stuff
	if (osp_init(FRAME_SIZE, SAMPLE_RATE, adaptation_type) < 0) {
		printf("Error initializing osp\n");
		return -1;
	}

	// Load the resampler taps
	if (load_filter_taps("resample_32_96.flt", resample_32_96_taps, RESAMP_32_96_TAPS) < 0) {
		return -1;
	}

	if (load_filter_taps("resample_96_32.flt", resample_96_32_taps, RESAMP_96_32_TAPS) < 0) {
		return -1;
	}

	// Initialize resampler Left
	if ((resampleL = resample_init(resample_32_96_taps, ARRAY_SIZE(resample_32_96_taps),
																 resample_96_32_taps, ARRAY_SIZE(resample_96_32_taps))) == NULL) {
		return -1;
	}

	// Initialize resampler Right
	if ((resampleR = resample_init(resample_32_96_taps, ARRAY_SIZE(resample_32_96_taps),
																 resample_96_32_taps, ARRAY_SIZE(resample_96_32_taps))) == NULL) {
		return -1;
	}

	// start announcement thread
	if(pthread_create(&announce_thread, NULL, announce_presence, NULL))
	{
		fprintf(stderr, "Error pthread_create() failed to create announcement thread\n");
	}


	//////////// Where the magic happens ///////////////
	if (tcp_mode) {
		if (run_pa_tcp(&osp_data, SAMPLE_RATE, FRAMES_PER_BUFFER, attenuation_factor) < 0) {
			printf("Error starting tcp version of live app.\n");
		}
	} else {
		if (run_pa_key(&osp_data, SAMPLE_RATE, FRAMES_PER_BUFFER, attenuation_factor) < 0) {
			printf("Error starting key version of live app.\n");
		}
	}

	// Close resampler
	if (resample_destroy(resampleL) < 0) {
		fprintf(stderr, "Error destroying resampler\n");
	}

	// Close resampler
	if (resample_destroy(resampleR) < 0) {
		fprintf(stderr, "Error destroying resampler\n");
	}

	osp_close();

	return 0;
}
