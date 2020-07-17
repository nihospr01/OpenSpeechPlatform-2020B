#ifndef PORTAUDIO_LIBRARY_H
#define PORTAUDIO_LIBRARY_H

#include <portaudio.h>
#include <cstdlib>


class portaudio_wrapper{

public:
    portaudio_wrapper(int in_device, int in_num_channel, int out_device, int out_num_channels,
                      PaStreamCallback callback, void *userData, int sample_rate, unsigned long frames_per_buffer);
    portaudio_wrapper( int in_num_channel, int out_num_channels, PaStreamCallback callback, void *userData, int sample_rate,
                       unsigned long frames_per_buffer);
    ~portaudio_wrapper();
    int init_stream(int in_device, int in_num_channels, int out_device, int out_num_channels,
                    PaStreamCallback callback, void* userData);
    int start_stream();
    int stop_stream();

    double input_latency();
    double output_latency();
private:
    PaStreamParameters inputParameters, outputParameters;
    PaStream *stream = nullptr;
    PaError err;
    const PaDeviceInfo* inputInfo;
    const PaDeviceInfo* outputInfo;
    int samp_rate;
    unsigned long frames_per_buffer;
    void delete_stream();
};
#endif
