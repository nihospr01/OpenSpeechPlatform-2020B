//
// Created by Dhiman Sengupta on 10/9/18.
//

#ifndef OSP_CLION_CXX_CONTROL_PARAM_HPP
#define OSP_CLION_CXX_CONTROL_PARAM_HPP

#include <memory.h>

#define D_SAMP_FREQ 48000
#define D_BUF 48
#define D_MULTI false

typedef struct control_t{
    int input_device = -1;
    int output_device = -1;
    bool endloop = false;
    bool multithread = D_MULTI;
    int samp_freq = D_SAMP_FREQ;
    size_t buf_size = D_BUF;
}controls;

#endif //OSP_CLION_CXX_CONTROL_PARAM_HPP
