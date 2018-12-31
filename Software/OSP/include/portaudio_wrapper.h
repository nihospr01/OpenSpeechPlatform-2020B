#ifndef PORTAUDIO_LIBRARY_H
#define PORTAUDIO_LIBRARY_H

#include <portaudio.h>
#include <cstdlib>

#define NUM_CHANNEL 2



class portaudio_wrapper{

public:
    /**
     * Constructor for when the input and output devices are manually entered
     * @param in_device
     * @param in_num_channel
     * @param out_device
     * @param out_num_channels
     * @param callback
     * @param userData
     * @param sample_rate
     * @param frames_per_buffer
     */
    portaudio_wrapper(int in_device, int in_num_channel, int out_device, int out_num_channels,
                      PaStreamCallback callback, void *userData, int sample_rate, unsigned long frames_per_buffer);
    /**
     * Constructor for when the input and output devices are the default devices
     * @param in_num_channel
     * @param out_num_channels
     * @param callback
     * @param userData
     * @param sample_rate
     * @param frames_per_buffer
     */
    portaudio_wrapper( int in_num_channel, int out_num_channels, PaStreamCallback callback, void *userData, int sample_rate,
                       unsigned long frames_per_buffer);
    /**
     * Destructor
     */
    ~portaudio_wrapper();
    /**
     * Initialize the stream
     * @param in_device
     * @param in_num_channel
     * @param out_device
     * @param out_num_channels
     * @param callback
     * @param userData
     * @return 0 if successful
     */
    int init_stream(int in_device, int in_num_channel, int out_device, int out_num_channels,
                    PaStreamCallback callback, void* userData);
    /**
     * Start the stream
     * @return 0 if successful
     */
    int start_stream();
    /**
     * Stop the stream
     * @return 0 if successful
     */
    int stop_stream();

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

/**
 * The sample callback for OSP and Port Audio
 * @tparam S is the class type of the RTMHA
 * @param inputBuffer
 * @param outputBuffer
 * @param framesPerBuffer
 * @param timeInfo
 * @param statusFlags
 * @param userData
 * @return
 */
template <class S>
static int patestCallback( const void *inputBuffer, void *outputBuffer,
                           unsigned long framesPerBuffer,
                           const PaStreamCallbackTimeInfo* timeInfo,
                           PaStreamCallbackFlags statusFlags,
                           void *userData ) {
    (void) timeInfo;
    (void) &statusFlags;
    auto masterHA = (S *) userData;
    auto input = (float *) inputBuffer;
    auto output = (float*) outputBuffer;
    auto in = new float*[NUM_CHANNEL];
    auto out = new float*[NUM_CHANNEL];
    for(int i = 0; i < NUM_CHANNEL; i++){
        in[i] = new float[framesPerBuffer];
        out[i] = new float[framesPerBuffer];
    }
    for(size_t i = 0; i < framesPerBuffer; i++){
        for(size_t j = 0; j < NUM_CHANNEL; j++)
            in[j][i] = input[2*i + j];
    }
    masterHA->process(in, out, framesPerBuffer);
    for(size_t i = 0; i < framesPerBuffer; i++){
        for(size_t j = 0; j < NUM_CHANNEL; j++)
            output[2*i + j] = out[j][i];
    }
    for(int i = 0; i < NUM_CHANNEL; i++){
        delete in[i];
        delete out[i];
    }
    delete[] in;
    delete[] out;
    return 0;

}


#endif