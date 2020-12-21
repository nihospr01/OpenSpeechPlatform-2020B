/**
 * Author:    Satyam Gaba <sgaba@ucsd.edu>
 * Created:   06/10/2020
 * 
 **/

// #include "libimu.h"
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
#include "libimu.cpp"


// define libimu
const int num_dev = 3;
const char *dev_names[]   = {"iio:device1", "iio:device2", "iio:device3"};
const char *trigger_str[] = {"trigger1", "trigger2","trigger3"};
const int buffer_length = 1;

// define lsl
std::string lsl_stream_name = "my_name";
std::string lsl_stream_type = "imu_data";

/* simple configuration and streaming */
int main ()
{	
	// Listen to ctrl+c and assert
	signal(SIGINT, handle_sig);

	send_imu_data(dev_names, trigger_str, num_dev, buffer_length, lsl_stream_name, lsl_stream_type);

	return 0;
}
