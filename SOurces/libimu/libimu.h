#pragma once

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

void send_imu_data(const char *dev_names[], const char *trigger_str[], const int num_dev, const int buffer_length, std::string lsl_stream_name, std::string lsl_stream_type); 
void handle_sig(int sig);