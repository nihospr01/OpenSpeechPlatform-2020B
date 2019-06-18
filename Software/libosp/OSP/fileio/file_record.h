//
// Created by Dhiman Sengupta on 2/28/19.
//

#ifndef TEMP_FILE_RECORD_HPP
#define TEMP_FILE_RECORD_HPP
#include <OSP/fileio/AudioFile.h>
#include <string>
#include <thread>
#include <OSP/fileio/sema.hpp>
#include <sys/time.h>
#include <stdio.h>
#include <atomic>

//struct timeval now;
//int rc;

class file_record{
public:
    AudioFile<float>::AudioBuffer buffer;
    AudioFile<float>::AudioBuffer temp_buffer;
    AudioFile<float>::AudioBuffer buffer_before;
    AudioFile<float>::AudioBuffer buffer_after;
    std::string rootPath;
    std::atomic<bool> write;
    AudioFile<float>::AudioBuffer *p_buffer=0;
    AudioFile<float>::AudioBuffer *t_buffer=0;
    AudioFile<float>::AudioBuffer *b_buffer=&buffer_before;
    AudioFile<float>::AudioBuffer *a_buffer=&buffer_after;
    int reset;
    int start=0;
    int stop=0;
    int start_before;
    int start_after=0;
    int stop_before=0;
    int stop_after;
    int record;
    int repeat;
    std::string file_path;
    int current_position_l;
    int current_position_r;
    int current_before_l;
    int current_before_r;
    int current_after_l;
    int current_after_r;
    int numSamples;
    int numSamples_before;
    int numSamples_after;
    int finish;
    int mono;
    int t;
    rk_sema *write_in_process;
    file_record();
    std::thread *record_thread;
    void rtmha_record(int num_sample, float * in,int channel);
    void record_before(int num_sample, float *in, int channel);
    void record_after(int num_sample, float *in, int channel);
    void set_params( int start_,int stop_,float seconds,const char* file_);
    void get_params(float& seconds);
    void write_to_file();
    ~file_record();
};

#endif //TEMP_FILE_RECORD_HPP
