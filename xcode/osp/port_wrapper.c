/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */


#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "port_wrapper.h"

#include "resample.h"
#include "coeffs.h"
#include "beam_forming.h"
#include "utilities.h"

#define NUM_INPUT_CHANNELS		(2)
#define NUM_OUTPUT_CHANNELS		(2)

/* Select sample format. */
#define PA_SAMPLE_TYPE		(paFloat32|paNonInterleaved)

/* set 0 (lowest) latency */
#ifdef __linux__
	#define DESIRED_LATENCY (.1)
#else
	#define DESIRED_LATENCY (0.001)
#endif

#define DEVICE_USE (0)

// TODO these globals need to go away. They're just making everyone's lives
// easier for right now.
static PaStream *stream;
static PaError err_message;

static Resample resampleL;
static Resample resampleR;

static Beam_Form beam_formL;
static Beam_Form beam_formR;

static int _internal_pa_init(PaStreamParameters *inputParameters, PaStreamParameters *outputParameters, unsigned int sample_rate)
{
    int numDevices;
    int i;

	float resample_32_96_taps[RESAMP_32_96_TAPS];
	float resample_96_32_taps[RESAMP_96_32_TAPS];

	//
	// Load the resampler taps
	if (load_filter_taps("resample_32_96.flt", resample_32_96_taps, RESAMP_32_96_TAPS) < 0) {
		return -1;
	}

	if (load_filter_taps("resample_96_32.flt", resample_96_32_taps, RESAMP_96_32_TAPS) < 0) {
		return -1;
	}

	// Initialize resampler Left
	if ((resampleL = resample_init(resample_32_96_taps, ARRAY_SIZE(resample_32_96_taps),
									resample_96_32_taps, ARRAY_SIZE(resample_96_32_taps))) == NULL) {
		return -1;
	}

	// Initialize resampler Right
	if ((resampleR = resample_init(resample_32_96_taps, ARRAY_SIZE(resample_32_96_taps),
									resample_96_32_taps, ARRAY_SIZE(resample_96_32_taps))) == NULL) {
		return -1;
	}

	// Init beam forming stuff
	if ((beam_formL = beam_form_init(0, 1, sample_rate)) == NULL) {
		return -1;
	}

	if ((beam_formR = beam_form_init(0, 1, sample_rate)) == NULL) {
		return -1;
	}

    printf("patest_read_write_wire.c\n");
    fflush(stdout);

    err_message= Pa_Initialize();
    if (err_message != paNoError) {
        return -1;
    }

    // get number of devices
    numDevices = Pa_GetDeviceCount();
    if( numDevices < 0 )
    {
        printf( "ERROR: Pa_CountDevices returned 0x%x\n", numDevices );
        return -1;
    }
    else{
        printf("\n # of audio devices = %d\n", numDevices);
    }

    // get info about devices
    const   PaDeviceInfo *deviceInfo;
    for( i=0; i<numDevices; i++ )
    {
        deviceInfo = Pa_GetDeviceInfo( i );
        printf("device %d  = %s\n", i, deviceInfo->name);
    }

    inputParameters->device = Pa_GetDefaultInputDevice(); /* default input device */
    printf("Input device # %d.\n", inputParameters->device);
    printf("Input LL: %g s\n", Pa_GetDeviceInfo(inputParameters->device)->defaultLowInputLatency);
    printf("Input HL: %g s\n", Pa_GetDeviceInfo(inputParameters->device)->defaultHighInputLatency);
    inputParameters->channelCount = NUM_INPUT_CHANNELS;
    inputParameters->sampleFormat = PA_SAMPLE_TYPE;
//    inputParameters.suggestedLatency = Pa_GetDeviceInfo(inputParameters.device)->defaultHighInputLatency ; // might need 0
    inputParameters->suggestedLatency = DESIRED_LATENCY ;

    inputParameters->hostApiSpecificStreamInfo = NULL;

    outputParameters->device = Pa_GetDefaultOutputDevice(); /* default output device */
    printf("Output device # %d.\n", outputParameters->device);
    printf("Output LL: %g s\n", Pa_GetDeviceInfo( outputParameters->device)->defaultLowOutputLatency);
    printf("Output HL: %g s\n", Pa_GetDeviceInfo( outputParameters->device)->defaultHighOutputLatency);
    outputParameters->channelCount = NUM_OUTPUT_CHANNELS;
    outputParameters->sampleFormat = PA_SAMPLE_TYPE;
//    outputParameters.suggestedLatency = Pa_GetDeviceInfo(outputParameters.device)->defaultHighOutputLatency; // might need 0
    outputParameters->suggestedLatency = DESIRED_LATENCY;


    outputParameters->hostApiSpecificStreamInfo = NULL;

	return 0;
}

static int pa_main_callback(const void *inputBuffer,
                     void *outputBuffer,
                     size_t frameCount,
                     const PaStreamCallbackTimeInfo* timeInfo,
                     PaStreamCallbackFlags statusFlags,
                     void *userData )
{
	if (inputBuffer == NULL) {
		memset(outputBuffer, 0x00, frameCount * sizeof(float) * NUM_OUTPUT_CHANNELS);
		return paContinue;
	}

    int i;
	size_t ret;

	float inL[96];
	float inR[96];

	float inL32[32];
	float inR32[32];

	float outL32[32];
	float outR32[32];

	pa_user_data *pa_data = (pa_user_data *)userData;

	// get pointers to non-interleaved input buffers
	float *in_left_front = ((float **) inputBuffer)[0];
	float *in_right_front = ((float **) inputBuffer)[1];
	float *in_left_rear = ((float **) inputBuffer)[2];
	float *in_right_rear = ((float **) inputBuffer)[3];
	// get pointers to non-interleaved output buffers
	float *outL = ((float **) outputBuffer)[0];
	float *outR = ((float **) outputBuffer)[1];

	if (statusFlags == paInputUnderflow || statusFlags == paOutputUnderflow) {
		pa_data->aux_data.underruns++;
		//fprintf(stderr, ".");
	}

	if (frameCount != 96) {
		fprintf(stderr, "PW: Incompatible frame size. Expected 96, got %lu\n", frameCount);
		return paContinue;
	}

	if (pa_data->user_data->rear_mics) {
		// Beam forming for multiple channels
		//beam_form_proc(beam_formL, in_left_front, in_left_rear, inL, frameCount);
		//beam_form_proc(beam_formR, in_right_front, in_right_rear, inR, frameCount);
		for (i = 0; i < (int)frameCount; i++) {
			inL[i] = (in_left_front[i] + in_left_rear[i]) / 2;
			inR[i] = (in_right_front[i] + in_right_rear[i]) / 2;
		}
	} else {
		for (i = 0; i < (int)frameCount; i++) {
			inL[i] = in_left_front[i];
			inR[i] = in_right_front[i];
		}
	}

	ret = resample_96_32(resampleL, inL, inL32, frameCount);
	resample_96_32(resampleR, inR, inR32, frameCount);

	if (pa_data->user_data->no_op) {
		for (i = 0; i < ret; i++) {
			outL32[i] = inL32[i];
			outR32[i] = inR32[i];
		}
	} else {
		osp_process_audio(pa_data->user_data,
						inL32, inR32,
						outL32, outR32, ret);

		//Attenuation factor multiplication after the OSP process
		for (i = 0; i < ret; i++) {
			outL32[i] = outL32[i] * pa_data->aux_data.attenuation_factor * pa_data->aux_data.gain_factor;
			outR32[i] = outR32[i] * pa_data->aux_data.attenuation_factor * pa_data->aux_data.gain_factor;
		}

	}

	resample_32_96(resampleL, outL32, outL, ret);
	resample_32_96(resampleR, outR32, outR, ret);

    return paContinue;  // aka 0
}

static int pa_loopback_callback(const void *inputBuffer,
                     void *outputBuffer,
                     unsigned long frameCount,
                     const PaStreamCallbackTimeInfo* timeInfo,
                     PaStreamCallbackFlags statusFlags,
                     void *userData )
{
	if (inputBuffer == NULL) {
		memset(outputBuffer, 0x00, frameCount * sizeof(float) * NUM_OUTPUT_CHANNELS);
		return paContinue;
	}

    int i;
	size_t ret;

	float inL32[32];
	float inR32[32];

	float outL32[32];
	float outR32[32];

	pa_loopback_data *loopback_data = (pa_loopback_data *)userData;

	// get pointers to non-interleaved input buffers
	float *inL = ((float **) inputBuffer)[0];
	float *inR = ((float **) inputBuffer)[1];

	// get pointers to non-interleaved output buffers
	float *outL = ((float **) outputBuffer)[0];
	float *outR = ((float **) outputBuffer)[1];

	if (statusFlags == paInputUnderflow || statusFlags == paOutputUnderflow) {
		//pa_data->aux_data->underruns++;
		//fprintf(stderr, ".");
	}

	if (frameCount != 96) {
		fprintf(stderr, "Incompatible frame size: %lu\n", frameCount);
		return paContinue;
	}

	ret = resample_96_32(resampleL, inL, inL32, frameCount);
	resample_96_32(resampleR, inR, inR32, frameCount);

	if (loopback_data->osp_data->no_op) {
		for (i = 0; i < ret; i++) {
			outL32[i] = inL32[i];
			outR32[i] = inR32[i];
		}
	} else {
		// Prepped for multiple channels, but just feeding the same one
		// twice for now
		osp_process_audio(loopback_data->osp_data,
						inL32, inR32,
						outL32, outR32, ret);
	}

	// Here we're putting the processed data (downsample->osp->upsample) onto the file context structure
	resample_32_96(resampleL, outL32, &(loopback_data->file_ctx->outL[loopback_data->file_ctx->index]), ret);
	resample_32_96(resampleR, outR32, &(loopback_data->file_ctx->outR[loopback_data->file_ctx->index]), ret);

	// Put the input file data from the file context structure onto the output buffer
	for (i = 0; i < (int)frameCount; i++, loopback_data->file_ctx->index++) {
		outL[i] = loopback_data->file_ctx->inL[loopback_data->file_ctx->index];
		outR[i] = loopback_data->file_ctx->inR[loopback_data->file_ctx->index];
	}

	// Data gets put onto the file output buffer in resampling. Need to check if we're done.
	if (loopback_data->file_ctx->index > loopback_data->file_ctx->length) {
		loopback_data->done = 1;
		return paComplete;
	}

    return paContinue;
}

int pa_stop_stream()
{
    err_message= Pa_StopStream(stream);
    if (err_message!= paNoError) {
        fprintf(stderr, "An error occured while using the portaudio stream\n");
        fprintf(stderr, "Error number: %d\n", err_message);
        fprintf(stderr, "Error message: %s\n", Pa_GetErrorText(err_message));
        return -1;
    }

    return 0;
}

int pa_loopback_run(pa_loopback_data *loopback_data, unsigned int samp_rate, unsigned int frames_per_buffer)
{
    PaStreamParameters inputParameters, outputParameters;
	PaError err;

	printf("Samp rate: %d\n", samp_rate);

	if (_internal_pa_init(&inputParameters, &outputParameters, samp_rate) < 0) {
		printf("Error initializing the internal PA stuff\n");
		return -1;
	}

	if ((err = Pa_IsFormatSupported(&inputParameters, &outputParameters, samp_rate)) != paFormatIsSupported) {
		printf("That format is not supported. Press any key to exit\n");
		getchar();
		return -1;
	}

	loopback_data->done = 0;

    /* -- setup -- */
    err_message= Pa_OpenStream(
                        &stream,
                        &inputParameters,
                        &outputParameters,
                        samp_rate,
                        frames_per_buffer,
                        paClipOff,      	/* we won't output out of range samples so don't bother clipping them */
                        pa_loopback_callback,
                        loopback_data);

    if (err_message != paNoError) {
		fprintf(stderr, "Error on opening loopback stream\n");
        return -1;
    }

	if (pa_start_stream() < 0) {
		fprintf(stderr, "Error on starting loopback stream\n");
		return -1;
	}

	// Wait for stream to finish
	while (loopback_data->done == 0) {
		Pa_Sleep(100);
	}

	if (pa_stop_stream() < 0) {
		fprintf(stderr, "Error on stopping loopback stream\n");
		return -1;
	}

	pa_close();

    return 0;
}

int pa_init(pa_user_data *pa_data, unsigned int samp_rate, unsigned int frames_per_buffer)
{
    PaStreamParameters inputParameters, outputParameters;
	PaError err;

	printf("Samp rate: %d\n", samp_rate);

	if (_internal_pa_init(&inputParameters, &outputParameters, samp_rate) < 0) {
		printf("Error initializing the internal PA stuff\n");
		return -1;
	}

	pa_data->aux_data.underruns = 0;

	if (pa_data->user_data->rear_mics) {
		printf("Enabling rear mics\n");
    	inputParameters.channelCount = 4;
	}

	if ((err = Pa_IsFormatSupported(&inputParameters, &outputParameters, samp_rate)) != paFormatIsSupported) {
		printf("That format is not supported. Press any key to exit\n");
		getchar();
		return -1;
	}

    /* -- setup -- */
    err_message= Pa_OpenStream(
                        &stream,
                        &inputParameters,
                        &outputParameters,
                        samp_rate,
                        frames_per_buffer,
                        paClipOff,      	/* we won't output out of range samples so don't bother clipping them */
                        pa_main_callback,
                        pa_data);

    if (err_message!= paNoError) {
        return -1;
    }

    return 0;
}

int pa_start_stream()
{
    err_message= Pa_StartStream(stream);
    if (err_message!= paNoError) {
        fprintf(stderr, "An error occured while using the portaudio stream\n");
        fprintf(stderr, "Error number: %d\n", err_message);
        fprintf(stderr, "Error message: %s\n", Pa_GetErrorText(err_message));
        return -1;
    }

    printf("Starting stream\n");

    return 0;
}


void pa_close()
{
	// Close resampler
	if (resample_destroy(resampleL) < 0) {
		fprintf(stderr, "Error destroying resampler\n");
	}

	// Close resampler
	if (resample_destroy(resampleR) < 0) {
		fprintf(stderr, "Error destroying resampler\n");
	}

	// Close osp stuff
//	osp_close();

    if (stream) {
        Pa_CloseStream(stream);
    }

	beam_form_destroy(beam_formL);
	beam_form_destroy(beam_formR);

    Pa_Terminate();
}
