#include "portaudio_wrapper.h"

#include <iostream>
#include <sstream>

#ifdef __linux__
#include <pa_linux_alsa.h>
#endif

#ifdef __APPLE__
#include <pa_mac_core.h>
#endif

static const PaSampleFormat SAMPLE_TYPE = paFloat32 | paNonInterleaved;

portaudio_wrapper::portaudio_wrapper(int in_device, int in_num_channel, int out_device, int out_num_channels,
                                     PaStreamCallback callback, void *userData, int sample_rate,
                                     unsigned long frames_per_buffer) {
    samp_rate = sample_rate;
    this->frames_per_buffer = frames_per_buffer;
    if (Pa_Initialize() != paNoError) {
        delete_stream();
        return;
    }
    if (in_device == -1)
        in_device = Pa_GetDefaultInputDevice();
    if (out_device == -1)
        out_device = Pa_GetDefaultOutputDevice();

    if (init_stream(in_device, in_num_channel, out_device, out_num_channels, callback, userData)) {
        std::cout << "Error Setting Up PortAudio Stream" << std::endl;
        throw std::runtime_error("Error Creating PortAudio Stream");
    }
}

portaudio_wrapper::~portaudio_wrapper() {
    delete_stream();
}

int portaudio_wrapper::init_stream(int in_device, int in_num_channels, int out_device, int out_num_channels,
                                   PaStreamCallback callback, void *userData) {
    PaError err;

    /* -- setup -- */
    inputInfo = Pa_GetDeviceInfo(in_device);
    inputParameters.device = in_device; /* default input device */
    inputParameters.channelCount = in_num_channels;
    inputParameters.sampleFormat = SAMPLE_TYPE;
    inputParameters.suggestedLatency = .001;
    // inputParameters.suggestedLatency = inputInfo->defaultHighInputLatency;
    inputParameters.hostApiSpecificStreamInfo = NULL;

    outputInfo = Pa_GetDeviceInfo(out_device);
    outputParameters.device = out_device; /* default output device */
    outputParameters.channelCount = out_num_channels;
    outputParameters.sampleFormat = SAMPLE_TYPE;
    outputParameters.suggestedLatency = .001;
    // outputParameters.suggestedLatency = outputInfo->defaultHighOutputLatency;
    outputParameters.hostApiSpecificStreamInfo = NULL;

#ifdef __APPLE__
    PaMacCoreStreamInfo mac;
    PaMacCore_SetupStreamInfo(&mac, paMacCorePro);
    inputParameters.hostApiSpecificStreamInfo = (void *)&mac;
    outputParameters.hostApiSpecificStreamInfo = (void *)&mac;
#endif

    // Open stream
    if (in_num_channels == 0) {
        err = Pa_OpenStream(
            &stream, nullptr, &outputParameters, samp_rate, frames_per_buffer,
            paClipOff | paDitherOff | paPrimeOutputBuffersUsingStreamCallback, /* we won't output out of range samples
                                                                                  so don't bother clipping them */
            callback, userData);
    } else {
        err = Pa_OpenStream(
            &stream, &inputParameters, &outputParameters, samp_rate, frames_per_buffer,
            paClipOff | paDitherOff | paPrimeOutputBuffersUsingStreamCallback, /* we won't output out of range samples
                                                                                  so don't bother clipping them */
            callback, userData);
    }

    if (err != paNoError) {
        delete_stream();
        return -1;
    }

#ifdef __linux__
    PaAlsa_EnableRealtimeScheduling(stream, 1);
#endif

    // Print stats
    // std::stringstream ss;
    // ss << "Input device # " << inputParameters.device << std::endl;
    // ss << "  Name: " << inputInfo->name << std::endl;
    // ss << "  Channels = " << out_num_channels << std::endl;
    // ss << "  LL: " << inputInfo->defaultLowInputLatency << " seconds"  << std::endl;
    // ss << "  HL: " << inputInfo->defaultHighInputLatency << " seconds" << std::endl;
    // ss << "Output device # " << outputParameters.device << std::endl;
    // ss << "  Name: " << outputInfo->name << std::endl;
    // ss << "  Channels = " << in_num_channels << std::endl;
    // ss << "  LL: " << outputInfo->defaultLowOutputLatency << " seconds" << std::endl;
    // ss << "  HL: " << outputInfo->defaultHighOutputLatency << " seconds" << std::endl;
    // std::cout << ss.str() << std::endl;

    return 0;
}

void portaudio_wrapper::delete_stream() {
    Pa_Terminate();
}

int portaudio_wrapper::start_stream() {
    return Pa_StartStream(stream);
}

int portaudio_wrapper::stop_stream() {
    return Pa_StopStream(stream);
}

double portaudio_wrapper::input_latency() {
    return Pa_GetStreamInfo(stream)->inputLatency;
}

double portaudio_wrapper::output_latency() {
    return Pa_GetStreamInfo(stream)->outputLatency;
}
