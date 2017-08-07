/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */


/**
 * @brief Contains general, commonly used data structures and constants
 */

#ifndef CONSTANTS_H__
#define CONSTANTS_H__

#define FRAME_SIZE 32

// Might be better to put this in osp_tcp.h?  I don't know right now
#define VERSION	2	///< This is the C version for keeping the networking of packets between the 
					// Android and C code in sync.  If we make changes to the packet structure
					// on the Android side, we have to reflect them on this side.

/*** Beam forming ***/

/*** Filter constants ***/
#define NUM_BANDS			6	///< Number of Sub-Bands
#define BAND_FILT_LEN 		193	///< Sub-Band filter tap length
#define RESAMP_96_32_TAPS	25	///< Number of taps for decimation filter in resampler.c
#define RESAMP_32_96_TAPS	24	///< Number of taps for interpolation filter in resampler.c
#define SYNTHETIC_TAP_LEN	525	///< Synthetic feedback filter tap length

/*** Peak detect defaults ***/
#define D_ATTACK_TIME	5	///< Attack time in msec
#define D_RELEASE_TIME	20	///< Release time in msec
#define D_PD_SAMP_RATE	32000	///< Sample rate that MHA is operating at

/*** WDRC defaults ***/
#define D_KNEE_LOW	45	///< Lower kneepoint in dB SPL. Using the same value for all the bands
#define D_KNEE_HIGH	100	///< Upper kneepoint in dB SPL. Using the same value for all the bands

#define AFC_FILTER_TAP_FILE_L	"./afc_filter_taps_l.bin"	///< AFC filter initialization taps for left channel
#define AFC_FILTER_TAP_FILE_R	"./afc_filter_taps_r.bin"	///< AFC filter initialization taps for right channel

#define CURRENT_VERSION	1	///< Version number of the application. Required to match the version with Android app

/**
 * @brief Struct that contains misc data passed to PortAudio
 */
typedef struct pa_aux_data_t {
	int underruns;	///< Number of underruns detected by PortAudio
	float attenuation_factor;
	float gain_factor;
} pa_aux_data;

// For now this is the shared structure for the interface
// between the Android client and C code.  In the future, there
// should be different structs that are passed back and forth.

/**
 * @brief general data structure shared between client and C application
 */
typedef struct osp_user_data_t {
	int no_op;					///< No operation.  The audio is passed from input to output in the audio callback
	int afc;					///< AFC on/off
	int feedback;				///< Feedback on/off
	int rear_mics;				///< Read mics on/off
	int g50[NUM_BANDS];			///< The gain values at 50 dB SPL input level
	int g80[NUM_BANDS];			///< The gain values at 80 dB SPL input level

	int knee_low[NUM_BANDS];	///< Lower kneepoints for all bands
	int knee_high[NUM_BANDS];	///< Upper kneepoints for all bands
	int attack[NUM_BANDS];		///< Attack time for WDRC for all bands
	int release[NUM_BANDS];		///< Release time for WDRC for all bands
	int mpo_on;					///< toggle for MPO on/off
} osp_user_data;

/**
 * @brief The data structure passed to PortAudio
 */
typedef struct pa_user_data_t {
	osp_user_data *user_data;	///< Osp user data struct
	pa_aux_data aux_data;		///< Auxiliary struct for misc data
} pa_user_data;

#endif /* CONSTANTS_H__ */
