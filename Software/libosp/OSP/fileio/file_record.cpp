//
// Created by Dhiman Sengupta on 2/28/19.
//

#include <stdio.h>
#include <string.h>
#include <stdint.h>
#include <unistd.h>
#include <memory.h>
#include "file_record.h"

using namespace std;

file_record::file_record(std::string file_, float seconds)
{
    rootPath = "/opt/EWS/public/record/";
    file = file_;
    numSamples_before=2880000;
    numSamples_after=2880000;
    buffer.resize(2);
    temp_buffer.resize(2);
    buffer_before.resize(2);
    buffer_after.resize(2);
    buffer_before[0].resize(numSamples_before);
    buffer_before[1].resize(numSamples_before);
    buffer_after[0].resize(numSamples_after);
    buffer_after[1].resize(numSamples_after);
    numSamples = (int)(48000.0f*seconds);
    current_position_l=0;
    current_position_r=0;
    current_before_l=0;
    current_before_r=0;
    current_after_l =0;
    current_after_r =0;
    stop_after=0;
    start_before=0;
    finish = 0;
    stop=0;
    t=-1;
    write_in_process = new rk_sema;
    rk_sema_init(write_in_process, 0);
    record_thread= new std::thread(&file_record::write_to_file, this);
#ifdef __linux__
    cpu_set_t cpuset;
    CPU_ZERO(&cpuset);
    CPU_SET(0, &cpuset);
    int rc = pthread_setaffinity_np(record_thread->native_handle(),
                                                sizeof(cpu_set_t), &cpuset);
    if (rc != 0) {
        std::cerr << "Error calling pthread_setaffinity_np: " << rc << "\n";
    }
#endif
    record_thread->detach();

}

void file_record::record_before(int num_sample, float *in, int channel)
{
    int current_position;
    if (channel == 0){current_position= current_before_l;}
    else {current_position= current_before_r;}
    if ((current_position + num_sample < numSamples_before) && !start_before) {
        memcpy(&(*b_buffer)[channel][current_position], in,sizeof(float) * num_sample);
        current_position = current_position + num_sample ;
    }
    else
    {
        if ((current_position + num_sample > numSamples_before) && !start_before)
        {
            int temp = current_position + num_sample - numSamples_before;
            int remain = numSamples_before - current_position;
            memcpy(&(*b_buffer)[channel][current_position],in ,sizeof(float) * remain);
            memcpy(&(*b_buffer)[channel][0], &in[remain],sizeof(float) * temp);
            current_position = current_position + num_sample - numSamples_before;
        }
        else if (stop_before)
        {
            p_buffer=b_buffer;
            rk_sema_post(write_in_process);
            current_before_l=0;current_before_r=0;
            stop_before=0;
        }
    }
    if (channel == 0){current_before_l= current_position;}
    else {current_before_r= current_position;}

}

void file_record::rtmha_record(int num_sample, float * in, int channel){
    int record_channel = channel;
    int current_position;
    if (channel == 0){current_position= current_position_l;}
    else {current_position= current_position_r;}
    if ((current_position + num_sample < numSamples) && !stop && start && !finish) {
        memcpy(&(*t_buffer)[record_channel][current_position], in ,sizeof(float) * num_sample);
        current_position = current_position + num_sample ;
        if (channel == 0){current_position_l= current_position;}
        else {current_position_r= current_position;}

    } else
    {
        if((current_position + num_sample >= numSamples) && !stop && start && !finish){
            std::swap(t_buffer, p_buffer);
            rk_sema_post(write_in_process);
            memset(&(*t_buffer)[0][0], 0, sizeof(float) * numSamples);
            memset(&(*t_buffer)[1][0], 0, sizeof(float) * numSamples);
            current_position_l=0;current_position_r=0;
        }
    }
    if (stop)
    {
        std::swap(t_buffer, p_buffer);
        rk_sema_post(write_in_process);
        stop=0;
        finish=1;
    }

}

void file_record::record_after(int num_sample, float *in, int channel)
{

    int current_position;
    if (channel == 0){current_position= current_after_l;}
    else {current_position= current_after_r;}
    if ((current_position + num_sample < numSamples_after) && start_after) {
        memcpy(&(*a_buffer)[channel][current_position], in, sizeof(float) * num_sample);
        current_position = current_position + num_sample;
    }
    else
    {
        if(!(current_position + num_sample < numSamples_after) && !stop_after)
        {
            p_buffer=a_buffer;
            rk_sema_post(write_in_process);
            current_after_l=0;current_after_r=0;
            stop_after=1;
            start_before=0;}
    }

    if (channel == 0){current_after_l= current_position;}
    else {current_after_r= current_position;}

}

void file_record::set_params( int start_, int stop_, float seconds, const char* file_)
{
    if (start_) {
        numSamples = (int)(48000.0f*seconds);
        file_path = rootPath + std::string(file_);
        file_path= file_path.substr(0, (file_path.size() - 4));
        buffer[0].resize(numSamples);
        buffer[1].resize(numSamples);
        temp_buffer[0].resize(numSamples);
        temp_buffer[1].resize(numSamples);
        current_position_l = 0;
        current_position_r = 0;
        finish = false;
        start=start_;
        stop_before=start_;
        start_before=start_;
        t_buffer=&temp_buffer;
        p_buffer=&buffer;
        printf("Recording\n");
    }
    if (stop_){
        stop=stop_;
        start_after=stop_;
    }

}


void file_record::get_params( float& seconds){
    seconds = ((float)numSamples) / 48000.0f;
    printf("%d %f\n", numSamples, seconds);
}


void file_record::get_params( float& seconds, std::string& file_){
    seconds = ((float)numSamples) / 48000.0f;
    file_ = this->file;
    printf("%d %f\n", numSamples, seconds);
}

void file_record:: write_to_file()
{
    while(1)
    {
        rk_sema_wait(write_in_process);
        AudioFile<float> audioFile;
        bool ok = audioFile.setAudioBuffer (*p_buffer);
        if(!ok){
            printf("err Recording");
            return;
        }
        audioFile.setBitDepth (16);
        audioFile.setSampleRate (48000);
        t=t+1;
        audioFile.save (file_path + std::string("-") +to_string(t) + std::string(".wav"), AudioFileFormat::Wave);
        printf("Recorded\n");
    }

}

file_record::~file_record()
{
    delete record_thread;
    delete write_in_process;
}
