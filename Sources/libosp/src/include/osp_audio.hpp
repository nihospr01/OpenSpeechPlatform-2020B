#include <iostream>
#include <unistd.h>
#include <thread>
#include <string>
#include <vector>
#include "portaudio_wrapper.h"

#include <osp_params.hpp>
#include <osp_net.hpp>

#ifdef __linux__
#include <sched.h>
#endif

static std::atomic<int> cb_counter(0);

static int audio_cb(const void *inputBuffer, void *outputBuffer, unsigned long buf_size,
                    const PaStreamCallbackTimeInfo *timeInfo, PaStreamCallbackFlags statusFlags, void *userData) {
    (void)timeInfo;
    (void)&statusFlags;
    OspProcess **proc = (OspProcess **)userData;

    cb_counter++;
    if (!osp::audio_enabled) {
        memset(((float **)outputBuffer)[0], 0, buf_size * sizeof(float));
        if (defaults::output_channels == 2)
            memset(((float **)outputBuffer)[1], 0, buf_size * sizeof(float));
        return paContinue;
    }

    float **out = (float **)outputBuffer;

    // set up input buffers
    float **in;
    static float t0[2 * 48];
    static float *buf[2] = {t0, t0 + 48};
    if (defaults::input_channels == 2)
        in = (float **)inputBuffer;
    else {
        in = buf;
        if (defaults::input_channels == 1) {
            auto temp = (float **)inputBuffer;
            in[0] = temp[0];
            if (defaults::output_channels == 2)
                std::memcpy(in[1], temp[0], buf_size * sizeof(float));
        }
    }
    (*proc)->process(in, out, buf_size);
    return paContinue;
};

/**
 * @brief Start Audio Stream
 *
 * Starts the audio stream then waits 4 seconds.
 * If the callback counter is not at least
 * 3000, then something has gone wrong.  Stop
 * the stream, wait a second, then try again.
 *
 * @return portaudio_wrapper*
 */
portaudio_wrapper *osp_audio(OspProcess **process) {
    portaudio_wrapper *audio;

    try {
            audio = new portaudio_wrapper(defaults::input_device, defaults::input_channels, defaults::output_device,
                                  defaults::output_channels, audio_cb, (void *)process, 48000, 48);
    } catch (...) {
        std::cerr << "Error Creating Audio Stream" << std::endl;
        return nullptr;
    }
    // loop until the callbacks start working.  Only necessary for microphone inputs.
    while (true) {
        sleep(1);
        cb_counter = 0;
        // enable audio, but not HA
        (*process)->enable(1, 0);
        try {
            if (audio->start_stream() != -1) {
                if (defaults::input_channels)
                    std::cout << "Input latency: " << audio->input_latency() << std::endl;
                std::cout << "Output latency: " << audio->output_latency() << std::endl;
            }
        } catch (const char *msg) {
            std::cerr << msg << std::endl;
        }
        sleep(3);
        std::cout << cb_counter << std::endl;
        if (cb_counter > 2000) {
            cb_counter = 0;
            // enable HA
            (*process)->enable(1, 1);
            sleep(3);
            std::cout << cb_counter << std::endl;
            if (cb_counter > 2000)
                return audio;
        }
        audio->stop_stream();
        std::cout << "Stream failed.  Retrying..." << std::endl;
    }
    return audio;
}
