/**
 * Author:    Satyam Gaba <sgaba@ucsd.edu>
 * Created:   05/10/2020
 * 
 **/

#include <iio.h>
#include <thread>  //for lsl_cpp
#include <chrono>
#include <lsl_cpp.h>
#include <iostream>
#include <ctime>
#include <cstdlib>
#include <cstring>
#include <fstream>

using namespace std;

#define num_chn 6 //7
#define num_dev 3 // 3
// BMI-160 definitions
#define IMU1 "iio:device1"
// #define IMU1 "bmi160"
#define IMU2 "iio:device2"
#define IMU3 "iio:device3"
#define TIMESTAMP "timestamp"
#define AX "accel_x"
#define AY "accel_y"
#define AZ "accel_z"
#define GX "anglvel_x"
#define GY "anglvel_y"
#define GZ "anglvel_z"
#define TRIG1 "trigger1"
#define TRIG2 "trigger2"
#define TRIG3 "trigger3"

#define BUFFER_LEN  4 //8

#define GRAVITY 9.805f
#define SCALE 10.0f

static bool enable_failed = false;
bool stop = false;
static bool get_imu_dev(struct iio_context *ctx, 
            struct iio_device **dev, const char *devID){
    *dev = iio_context_find_device(ctx, devID);
    if (!*dev) {
	printf("Could not find BMI160 with ID: %s \n", devID);
	return false;
    }
    return true;
}

static void enable_dev_channel(struct iio_device *dev, const char *name){
    struct iio_channel *ch;
    if (enable_failed)
	return;
    ch =  iio_device_find_channel(dev, name, false);
    if (ch == NULL){
	enable_failed = true;
	printf("Enabling channel %s failed!\n", name);
	return;
    }
    else{printf("Enabling channel %s\n", name);}
    iio_channel_enable(ch);
}

static void setup_trigger(struct iio_context *ctx, 
                struct iio_device *dev, struct iio_device **trig,
                const char *trigID){
    *trig = iio_context_find_device(ctx, trigID);
    if (!*trig) {
	printf("Could not find trigger with ID: %s \n", trigID);
	return;
    }
    int success = iio_device_set_trigger(dev, *trig);
    if (success != 0)
	printf("Set trigger %s failed \n", trigID);
    return;
}

// define a prototype to write to file
int writeData(string, int, int); 

int main(){
    static struct iio_context *ctx = NULL;
    static struct iio_device *imu1 = NULL;
    static struct iio_device *imu2 = NULL;
    static struct iio_device *imu3 = NULL;
    static struct iio_channel *chn = NULL;
    static struct iio_device *trig1 = NULL;
    static struct iio_device *trig2 = NULL;
    static struct iio_device *trig3 = NULL;
    static struct iio_buffer *buf1 = NULL;
    static struct iio_buffer *buf2 = NULL;
    static struct iio_buffer *buf3 = NULL;

    // Listen setup
    ctx = iio_create_default_context();
    if (ctx == NULL) {
	printf("Could not acquire IIO context\n");
    }
    
	cout << "testing" ;


    // enable all the channels
    string chn_names[num_chn] = {AX,AY,AZ,GX,GY,GZ};//{TIMESTAMP, AX, AY, AZ, GX, GY, GZ};
    string trig_names[num_dev] = {TRIG1, TRIG2, TRIG3};
    static struct iio_device *devices[num_dev] = {imu1, imu2, imu3};
    static struct iio_device *triggers[num_dev] = {trig1, trig2, trig3};
    static struct iio_buffer *buffers[num_dev] = {buf1, buf2, buf3};
    static struct iio_channel *channels[num_dev][num_chn];
    for (int d=0; d<num_dev; d=d+1){
        for (int c=0; c<num_chn; c=c+1){
           enable_dev_channel(devices[d], chn_names[c].c_str()); 
           cout << "    " << AX << "  &  " << chn_names[c]<<endl;
           channels[d][c] = iio_device_find_channel(devices[d], chn_names[c].c_str(), false);
        } 
        setup_trigger(ctx, devices[d], &triggers[d], trig_names[d].c_str());
        buffers[d] = iio_device_create_buffer(devices[d], BUFFER_LEN, false);
    }
    

    //read scaling factors
    float acc_scale;
    float gyr_scale;
    string acc_scale_file = "/sys/bus/iio/devices/iio:device1/in_accel_scale";
    string gyr_scale_file = "/sys/bus/iio/devices/iio:device1/in_anglvel_scale";

    string line;
    //read acceleration scale
    ifstream accfile (acc_scale_file);
    if (accfile.is_open()){
        while(getline(accfile,line)){
            acc_scale = stof(line); //string to float
        }
    }
    //read gyroscope scale
    ifstream gyrfile (gyr_scale_file);
    if (gyrfile.is_open()){
        while(getline(gyrfile,line)){
            gyr_scale = stof(line); //string to float
        }
    }


    //initialize lsl
    string name = "my_name";
    string type = "imu_data";
    int tot_chn = num_chn * num_dev;
    lsl::stream_info info(name,type,tot_chn); //all channels stream
    lsl::stream_outlet outlet(info);
    //vector to send each sample through lsl
    vector<float> send_sample;

    int16_t valz;
    float scale;

    while(true){
        int num_buf = BUFFER_LEN;
        // float samples[tot_chn][num_buf];
        float samples[tot_chn][num_buf];

        //open each device
        for (int d=0; d<num_dev; d=d+1){
            size_t read = iio_buffer_refill(buffers[d]);
            if (read < 0){
                cout << "buffer refill failed" <<endl;
            }
            // open each channel
            for (int c=0; c<num_chn; c=c+1){
                if (c==1||2||3) scale = acc_scale;
                else if (c==4||5||7) scale = gyr_scale;
                // read buffer
                int idx = 0;
                for (void *ptr = iio_buffer_first(buffers[d], channels[d][c]);
                    ptr < iio_buffer_end(buffers[d]);
                    ptr += iio_buffer_step(buffers[d])) {
                    valz = *((int16_t*)ptr);
                    samples[(d*num_chn)+c][idx] = (float)valz * scale;
                    idx+=1;
                }
            }
        }

        //send over lsl
        if(outlet.have_consumers()){
            cout << "consumer found"<< endl;
            for(int b = 0; b < num_buf ; b++){
                for (int c = 0; c < tot_chn; c++ ){
                    send_sample.push_back(samples[c][b]);
                }  
                outlet.push_sample(send_sample);
                send_sample.clear();
            }
        }
    }
    return 0;
}
