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

#include "logger.h"

#include "osp_process.h"
#include "utilities.h"
#include "filter.h"
#include "coeffs.h"
#include "afc.h"
#include "delay_line.h"
#include "array_utilities.h"

#include "peak_detect.h"
#include "wdrc.h"
#include "wdrc_mpo_support.h"
#include "mpo.h"

#define FEEDBACK_EST_DELAY 100	///< Feedback delay in samples. For file loopback its 0. For real-time processing one needs to account for I/O delay
//#define FEEDBACK_EST_DELAY 38 // Delay for ZoomTac box
//#define FEEDBACK_EST_DELAY 60 // Delay estimated to get the feedback path in the middle
#define STEREO	///< Toggle for stereo and mono
//#define MPO

static Filter filterL[NUM_BANDS];
static Filter filterR[NUM_BANDS];

static Filter synthetic_feedback_filter_left;
static Filter synthetic_feedback_filter_right;

static Peak_detect pdL;
static Peak_detect pdR;

static Afc afc_left;
static Afc afc_right;

static Delay_Line delay_line_left;
static Delay_Line delay_line_right;

/**
 * @brief Function that performs basic Hearing Aid (HA) processing on a single channel
 *
 * The signal is split into Sub-Bands. Peak Detect and WDRC are applied on each Sub-Band.
 * The WDRC outputs are then added across the Sub-Bands. This will be the output of the HA.
 *
 * @see osp_user_data
 * @param osp The instance of the osp_user_data structure that was initialized in osp_data_init. This contains all the MHA parameters.
 * @param filter The filter data structure instance for that channel
 * @param pd The peak detect data structure instance for that channel
 * @param e_n Pointer to the input signal of the HA. This will be the sum of desired input signal plus the feedback signal.
 * @param s_n Pointer to the output signal of the HA. i.e. the output of the HA. This output will be used by AFC to remove feedback.
 * @param len The length of the input signal e_n that is given for processing. i.e. frame length.
 */
static void osp_process_ha(osp_user_data *osp, Filter *filter, Peak_detect pd, float *e_n, float *s_n, size_t len)
{
	int i;
	float signal[len]; // Array to contain WDRC output
	float filtered[len]; // Array to contain Sub-Band filtered signal. i.e. a single band signal

	float peak[len]; // Array to contain Peak Detector output
	float pdb[len];	// Array to contain Peak Detector output in dB SPL
	float wdrcdb[len]; // Array to contain WDRC output in dB

	memset(s_n, 0x00, len * sizeof(float)); // Initializing the output of HA with zeros

	// Doing HA processing by looping through each band
	for (i = 0; i < NUM_BANDS; i++) {
		// Applying Sub-Band filtering on the input signal
		filter_proc(filter[i], e_n, filtered, len);
		// Applying Peak Detect on the Sub-Band filtered signal using attack and relase times specified for that Sub-Band
		peak_detect(pd, filtered, len, peak, osp->attack[i], osp->release[i], i);
		// Converting the Peak Detector output to dB SPL values
		peak_to_spl(peak, pdb, len);
		// MPO limiting

		// #ifdef MPO  // If MPO is activated
		if (osp->mpo_on){
		
			wdrc_mpo_support(pdb, osp->g50[i], osp->g80[i], osp->knee_low[i], len, wdrcdb);
            
			mpo(filtered, pdb, wdrcdb, osp->knee_high[i], len, signal);
		}

		// #else /* MPO */
			// Applying WDRC on filtered signal using the Peak Detector dB SPL values to compare with gains at 50 dB SPL and 80 dB SPL
		else {
			wdrc(filtered, pdb, osp->g50[i], osp->g80[i], osp->knee_low[i], osp->knee_high[i], len, signal);
		}
		// #endif /* MPO */
		// Accumulating the output of HA for each Sub-Band
		array_add_array(s_n, signal, len);
	}
}

int osp_init(unsigned int frame_size, int sample_rate, unsigned char afc_adaptation_type)
{
	int i;
	float band_filter_coeffs[NUM_BANDS][BAND_FILT_LEN]; // Array to contain all Sub-Band filter taps
	float afc_init_taps[AFC_FILTER_TAP_LEN];	// Array to contain AFC filter initialization taps
	float synthetic_filter_taps[SYNTHETIC_TAP_LEN]; // Array to contain synthetic feedback filter taps
	char name[FILENAME_MAX];

	// Initialize delay line on both channels. Will be used to compensate the I/O delay to adjust the delay on feedback signal
	delay_line_left = delay_line_init(FEEDBACK_EST_DELAY);
	delay_line_right = delay_line_init(FEEDBACK_EST_DELAY);

	// Load Sub-Band filter taps. Looks for the exact format of filenames in filter_coefficients folder.
	for (i = 0; i < NUM_BANDS; i++) {
		sprintf(name, "filterbank_coeffs%d.flt", i + 1);
		if (load_filter_taps(name, band_filter_coeffs[i], ARRAY_SIZE((band_filter_coeffs[i]))) < 0) {
			return -1;
		}
	}

    // Initialize filter instances using the above Sub-Band filter taps for left channel
	for (i = 0; i < NUM_BANDS; i++) {
		if ((filterL[i] = filter_init(band_filter_coeffs[i], ARRAY_SIZE((band_filter_coeffs[i])))) == NULL) {
			return -1;
		}
	}

    // Initialize filter instances using the above Sub-Band filter taps for right channel. Same taps are used for both the channels.
	for (i = 0; i < NUM_BANDS; i++) {
		if ((filterR[i] = filter_init(band_filter_coeffs[i], ARRAY_SIZE((band_filter_coeffs[i])))) == NULL) {
			return -1;
		}
	}

	// Load synthetic feedback filter taps
	if (load_filter_taps("fbpath.flt", synthetic_filter_taps, SYNTHETIC_TAP_LEN) < 0) {
		return -1;
	}

	// Load AFC filter taps with initialization taps for left channel
	// If the initialization taps are not specified initialize AFC filter taps with zeros
	if (load_filter_taps("afc_init_taps.flt", afc_init_taps, AFC_FILTER_TAP_LEN) < 0) {
		fprintf(stderr, "AFC filter tap init file not found.  Initializing to 0 instead\n");
    	memset(afc_init_taps, 0x00, AFC_FILTER_TAP_LEN * sizeof(float));
	}

	// Instantiate AFC instance for left channel. hpf_taps, blf_taps are from coeffs.h
	if ((afc_left = afc_init(afc_init_taps, ARRAY_SIZE(afc_init_taps),
						hpf_taps, ARRAY_SIZE(hpf_taps),
						blf_taps, ARRAY_SIZE(blf_taps),
						frame_size, afc_adaptation_type)) == NULL) {
		fprintf(stderr, "Error initializing afc left\n");
		return -1;
	}

	if ((synthetic_feedback_filter_left = filter_init(synthetic_filter_taps, ARRAY_SIZE(synthetic_filter_taps))) == NULL) {
		fprintf(stderr, "Error initializing synthetic feedback filter (left);\n");
		return -1;
	}

	// Load AFC filter taps with initialization taps for right channel
	// If the initialization taps are not specified initialize AFC filter taps with zeros
	if (load_filter_taps("afc_init_taps.flt", afc_init_taps, AFC_FILTER_TAP_LEN) < 0) {
		fprintf(stderr, "AFC filter tap init file not found.  Initializing to 0 instead\n");
    	memset(afc_init_taps, 0x00, AFC_FILTER_TAP_LEN * sizeof(float));
	}


	// Instantiate AFC instance for right channel. hpf_taps, blf_taps are from coeffs.h
	if ((afc_right = afc_init(afc_init_taps, ARRAY_SIZE(afc_init_taps),
						hpf_taps, ARRAY_SIZE(hpf_taps),
						blf_taps, ARRAY_SIZE(blf_taps),
						frame_size, afc_adaptation_type)) == NULL) {
		fprintf(stderr, "Error initializing afc right\n");
		return -1;
	}

	if ((synthetic_feedback_filter_right = filter_init(synthetic_filter_taps, ARRAY_SIZE(synthetic_filter_taps))) == NULL) {
		fprintf(stderr, "Error initializing synthetic feedback filter (right);\n");
		return -1;
	}

	// Initializing peak detect instance for left channel
	printf("Setting peak detect values\n");
	if ((pdL = peak_detect_init(sample_rate, NUM_BANDS)) == NULL) {
		return -1;
	}

	// Initializing peak detect instance for right channel
	if ((pdR = peak_detect_init(sample_rate, NUM_BANDS)) == NULL) {
		return -1;
	}

	return 0;
}

void osp_process_audio(osp_user_data *osp,
					float *x_nL, float *x_nR,
					float *outL, float *outR,
					size_t len)
{
	float y_nL[len]; // Array to store estimated feedback signal on the left channel
	float y_nR[len]; // Array to store estimated feedback signal on the right channel
	float tmp[len];	// Array to store the delay compensated output of MHA
	int i;

	static float yL[FRAME_SIZE];	// Array to store synthetic feedback signal of left channel
	static float yR[FRAME_SIZE];	// Array to store synthetic feedback signal of right channel

#ifdef STEREO // If not stereo, only right channel is processed

// Processing left channel
	if (osp->feedback) {
		// Adding the feedback signal to the input signal. The added signal will be the input to hearing aid
		array_add_array(x_nL, yL, len);
	}

	if (osp->afc) {
		// Get the estimated feedback signal
		afc_get_y_estimated(afc_left, y_nL, len);
		// Subtract the estimated feedback from the input to hearing aid
		// Ideally the subtracted signal should be equal to the input signal
		array_subtract_array(x_nL, y_nL, len);
	}

	// Perform basic Hearing Aid processing (HA) on the left channel
	osp_process_ha(osp, filterL, pdL, x_nL, outL, len); // Perform Sub-Band decomposition, Peak Detect, WDRC on the subtracted signal


	// Gain compensation for AFC bias. Currently not required as AFC can be turned off for ANSI test.
	// if (osp->afc) {
	// 	array_multiply_const(outL, 1.6, len); 
	// }

	/*
	In real-time processing, the output of the hearing aid needs to be delayed for processing the next frame
	inorder to account for buffering delays that will be caused by I/O devices.
	The calibrated delay should be specified to FEEDBACK_EST_DELAY in terms of the number of samples
	*/
	for (i = 0; i < len; i++) {
		tmp[i] = delay_line_tick(delay_line_left, outL[i]);
	}

	// Update AFC filter taps using the current frame output and the current frame subtracted signal
	// These updated AFC filter taps will be used to remove feedback for the next frame
	if (osp->afc) {
		afc_update_taps(afc_left, tmp, x_nL, len);
	}

	if (osp->feedback) {
		filter_proc(synthetic_feedback_filter_left, tmp, yL, len);
	}

#else /* STEREO */
	for (i = 0; i < len; i++) {
		outL[i] = x_nL[i];	// If stereo is disabled, no processing on left channel
	}

#endif /* STEREO */

// Processing right channel
	if (osp->feedback) {
		// Adding the feedback signal to the input signal. The added signal will be the input to hearing aid
		array_add_array(x_nR, yR, len);
	}

	if (osp->afc) {
		// Get the estimated feedback signal
		afc_get_y_estimated(afc_right, y_nR, len);
		// Subtract the estimated feedback from the input to hearing aid
		// Ideally the subtracted signal should be equal to the input signal
		array_subtract_array(x_nR, y_nR, len);
	}

	// Perform basic Hearing Aid processing (HA) on the right channel
	osp_process_ha(osp, filterR, pdR, x_nR, outR, len);


	// Gain compensation for AFC bias. Currently not required as AFC can be turned off for ANSI test.
	// if (osp->afc) {
	// 	array_multiply_const(outR, 1.6, len); 
	// }

	/*
	In real-time processing, the output of the hearing aid needs to be delayed for processing the next frame
	inorder to account for buffering delays that will be caused by I/O devices.
	The calibrated delay should be specified to FEEDBACK_EST_DELAY input terms of the number of samples
	*/
	for (i = 0; i < len; i++) {
		tmp[i] = delay_line_tick(delay_line_right, outR[i]);
	}

	// Update AFC filter taps using the current frame output and the current frame subtracted signal
	// These updated AFC filter taps will be used to remove feedback for the next frame
	if (osp->afc) {
		afc_update_taps(afc_right, tmp, x_nR, len);
	}

	if (osp->feedback) {
		filter_proc(synthetic_feedback_filter_right, tmp, yR, len);
	}
}


unsigned int osp_get_num_bands()
{
	// XXX TODO: for now
	return NUM_BANDS;
}

void osp_close()
{
	int i;

	// Free all Sub-Band filter instances for left channel
	for (i = 0; i < NUM_BANDS; i++) {
		if (filter_destroy(filterL[i]) < 0) {
			fprintf(stderr, "Error closing filter object L\n");
		}
	}

	// Free all Sub-Band filter instances for right channel
	for (i = 0; i < NUM_BANDS; i++) {
		if (filter_destroy(filterR[i]) < 0) {
			fprintf(stderr, "Error closing filter object R\n");
		}
	}

	// Free loopback feedback filters
	filter_destroy(synthetic_feedback_filter_left);
	filter_destroy(synthetic_feedback_filter_right);

	// Free Peak Detect instances for both channels
	peak_detect_destroy(pdL);
	peak_detect_destroy(pdR);

	// Free Delay line instances for both channels
	delay_line_free(delay_line_left);
	delay_line_free(delay_line_right);

	// Free AFC instances for both channels
	afc_destroy(afc_left);
	afc_destroy(afc_right);
}

void osp_dump_afc_filter()
{
	float taps[AFC_FILTER_TAP_LEN];

	fprintf(stdout, "Saving filter taps left...\n");
	afc_get_taps(afc_left, taps);
	save_filter_taps(AFC_FILTER_TAP_FILE_L, taps, AFC_FILTER_TAP_LEN);

	fprintf(stdout, "Saving filter taps right...\n");
	afc_get_taps(afc_right, taps);
	save_filter_taps(AFC_FILTER_TAP_FILE_R, taps, AFC_FILTER_TAP_LEN);
}

void osp_data_init(osp_user_data *user_data)
{
	int i;

	if (user_data == NULL) {
		user_data = malloc(sizeof(osp_user_data));
	}

	// Change the below function to any of the four helper funcitons to use different audiograms for WDRC
	osp_data_set_gain(user_data, 0);

	for (i = 0; i < NUM_BANDS; i++) {
		user_data->knee_low[i] = D_KNEE_LOW;
		user_data->knee_high[i] = D_KNEE_HIGH;
		user_data->attack[i] = D_ATTACK_TIME;
		user_data->release[i] = D_RELEASE_TIME;
	}

	user_data->afc = 1;
	user_data->no_op = 0;
	user_data->feedback = 0;
	user_data->rear_mics = 1;
}

void osp_data_set_gain(osp_user_data *user_data, int gain)
{
	user_data->g50[0] = gain;
	user_data->g50[1] = gain;
	user_data->g50[2] = gain;
	user_data->g50[3] = gain;
	user_data->g50[4] = gain;
	user_data->g50[5] = gain;

	user_data->g80[0] = gain;
	user_data->g80[1] = gain;
	user_data->g80[2] = gain;
	user_data->g80[3] = gain;
	user_data->g80[4] = gain;
	user_data->g80[5] = gain;
}

void osp_data_set_nh(osp_user_data *user_data)
{
	user_data->g50[0] = 0;
	user_data->g50[1] = 0;
	user_data->g50[2] = 0;
	user_data->g50[3] = 0;
	user_data->g50[4] = 0;
	user_data->g50[5] = 0;

	user_data->g80[0] = 0;
	user_data->g80[1] = 0;
	user_data->g80[2] = 0;
	user_data->g80[3] = 0;
	user_data->g80[4] = 0;
	user_data->g80[5] = 0;
}

void osp_data_set_n2(osp_user_data *user_data)
{
	user_data->g50[0] = 2;
	user_data->g50[1] = 6;
	user_data->g50[2] = 17;
	user_data->g50[3] = 21;
	user_data->g50[4] = 20;
	user_data->g50[5] = 20;

	user_data->g80[0] = 1;
	user_data->g80[1] = 1;
	user_data->g80[2] = 4;
	user_data->g80[3] = 8;
	user_data->g80[4] = 14;
	user_data->g80[5] = 14;
}

void osp_data_set_n4(osp_user_data *user_data)
{
	user_data->g50[0] = 17;
	user_data->g50[1] = 24;
	user_data->g50[2] = 34;
	user_data->g50[3] = 36;
	user_data->g50[4] = 33;
	user_data->g50[5] = 33;

	user_data->g80[0] = 8;
	user_data->g80[1] = 12;
	user_data->g80[2] = 19;
	user_data->g80[3] = 25;
	user_data->g80[4] = 24;
	user_data->g80[5] = 24;
}

void osp_data_set_s2(osp_user_data *user_data)
{
	user_data->g50[0] = 2;
	user_data->g50[1] = 7;
	user_data->g50[2] = 19;
	user_data->g50[3] = 28;
	user_data->g50[4] = 33;
	user_data->g50[5] = 33;

	user_data->g80[0] = 1;
	user_data->g80[1] = 1;
	user_data->g80[2] = 4;
	user_data->g80[3] = 18;
	user_data->g80[4] = 19;
	user_data->g80[5] = 19;
}
