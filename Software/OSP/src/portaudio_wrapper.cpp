#include "portaudio_wrapper.h"

#include <iostream>

#define PA_SAMPLE_TYPE paFloat32




portaudio_wrapper::portaudio_wrapper(int in_device, int in_num_channel, int out_device, int out_num_channels,
                                     PaStreamCallback callback, void *userData, int sample_rate,
                                     unsigned long frames_per_buffer){
    samp_rate = sample_rate;
    this->frames_per_buffer = frames_per_buffer;
    err = Pa_Initialize();
    if( err != paNoError ) {
        this->delete_stream();
        return;
    }
    if(this->init_stream(in_device, in_num_channel, out_device, out_num_channels, callback, userData)){
        std::cout << "Error Setting Up PortAudio Stream" << std::endl;
    }
}
portaudio_wrapper::portaudio_wrapper(int in_num_channel, int out_num_channels, PaStreamCallback callback, void *userData,
                                     int sample_rate, unsigned long frames_per_buffer) {
    samp_rate = sample_rate;
    this->frames_per_buffer = frames_per_buffer;
    err = Pa_Initialize();
    if( err != paNoError ) {
        this->delete_stream();
        return;
    }
    if(this->init_stream(Pa_GetDefaultInputDevice(), in_num_channel, Pa_GetDefaultOutputDevice(), out_num_channels,
                         callback, userData)){
        std::cout << "Error Setting Up PortAudio Stream" << std::endl;
    }
}
portaudio_wrapper::~portaudio_wrapper() {
    this->delete_stream();
}
int portaudio_wrapper::init_stream(int in_device, int in_num_channel, int out_device, int out_num_channels,
                                    PaStreamCallback callback, void* userData) {

    inputParameters.device = in_device; /* default input device */
    printf( "Input device # %d.\n", inputParameters.device );
    inputInfo = Pa_GetDeviceInfo( inputParameters.device );
    printf( "    Name: %s\n", inputInfo->name );
    printf( "      LL: %g s\n", inputInfo->defaultLowInputLatency );
    printf( "      HL: %g s\n", inputInfo->defaultHighInputLatency );
    outputParameters.device = out_device; /* default output device */
    printf( "Output device # %d.\n", outputParameters.device );
    outputInfo = Pa_GetDeviceInfo( outputParameters.device );
    printf( "   Name: %s\n", outputInfo->name );
    printf( "     LL: %g s\n", outputInfo->defaultLowOutputLatency );
    printf( "     HL: %g s\n", outputInfo->defaultHighOutputLatency );
    printf( "Num channels = %d.\n", in_num_channel );
    inputParameters.channelCount = in_num_channel;
    inputParameters.sampleFormat = PA_SAMPLE_TYPE;
    inputParameters.suggestedLatency = 0.002;// inputInfo->defaultLowInputLatency ;
    inputParameters.hostApiSpecificStreamInfo = NULL;
    outputParameters.channelCount = out_num_channels;
    outputParameters.sampleFormat = PA_SAMPLE_TYPE;
    outputParameters.suggestedLatency =  0.002;//outputInfo->defaultLowOutputLatency;
    outputParameters.hostApiSpecificStreamInfo = NULL;
    /* -- setup -- */
    err = Pa_OpenStream(
            &stream,
            &inputParameters,
            &outputParameters,
            samp_rate,
            frames_per_buffer,
            paClipOff,      /* we won't output out of range samples so don't bother clipping them */
            callback, /* no callback, use blocking API */
            userData ); /* no callback, so no callback userData */
    if( err != paNoError ) {
        this->delete_stream();
        return -1;
    }
    return 0;
}

void portaudio_wrapper::delete_stream() {

    if( stream ) {
        Pa_AbortStream( stream );
        Pa_CloseStream( stream );
    }
    Pa_Terminate();
    if( err & paInputOverflow )
        fprintf( stderr, "Input Overflow.\n" );
    if( err & paOutputUnderflow )
        fprintf( stderr, "Output Underflow.\n" );

}

int portaudio_wrapper::start_stream() {
    err = Pa_StartStream( stream );
    if( err != paNoError ) {
        delete_stream();
        return -1;
    }
    return 0;

}

int portaudio_wrapper::stop_stream() {
    err = Pa_StopStream( stream );
    if( err != paNoError ) {
        delete_stream();
        return -1;
    }
    return 0;

}

