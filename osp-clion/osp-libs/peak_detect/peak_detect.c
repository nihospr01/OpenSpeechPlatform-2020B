/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#include "peak_detect.h"

#include <math.h>
#include <limits.h>
#include <stdlib.h>
#include <string.h>

/**
 * @brief Peak Detect data structure
 *
 */
struct peak_detect_t {
	float fsamp;	///< Sampling rate in Hz
	// float level;	///< dB SPL level corresponding to the input signal RMS
	float *prev_peak;	///< Pointer to the array of peak values of the last sample in previous frame for each band
	unsigned int num_bands;	///< Number of sub-bands
};


Peak_detect peak_detect_init(float fsamp, unsigned int num_bands)
{
	struct peak_detect_t *pd;

	if ((pd = (struct peak_detect_t*)malloc(sizeof(struct peak_detect_t))) == NULL) {
		return NULL;
	}

	if ((pd->prev_peak = malloc(sizeof(float) * num_bands)) == NULL) {
		goto abort;
	}
	// Setting prev_peak to be zero for each band for the first time. 
	memset(pd->prev_peak, 0x00, sizeof(&pd->prev_peak));

	pd->fsamp = fsamp;
	pd->num_bands = num_bands;

	return pd;

abort:
	free(pd);
	return NULL;
}


void peak_to_spl(float *peak, float *pdb, size_t len)
{
	int i;
	for(i = 0; i < len; i++){
		pdb[i] = DEFAULT_LEVEL + 20 * log10(peak[i] + DBL_EPSILON);	// To avoid log of 0
	}
}


void peak_detect(Peak_detect pd,
						float *input,
						size_t len,
						float *peak,
						float attack_time,
						float release_time,
						int band_num)
{
	float curr_inp;
	int i;
 
	// Compute the filter time constants
	float att = 0.001 * attack_time * pd->fsamp / 2.425; // ANSI attack time => filter time constant
	float alpha = att / (1.0 + att);

	float rel = 0.001 * release_time * pd->fsamp / 1.782; // ANSI release time => filter time constant
	float beta = rel / (1.0 + rel);

	// Setting first sample peak value to the last sample peak value in previous frame.
	peak[0] = pd->prev_peak[band_num]; 	// TODO: Handle prev_peak transistion between frames in a better way

 	// Loop to peak detect the signal
	for(i = 1; i < len; i++) {
		curr_inp = input[i] > 0 ? input[i] : -input[i]; // Get the rectified signal
		if (curr_inp >= peak[i - 1]) {
			peak[i] = alpha * peak[i - 1] + (1 - alpha) * curr_inp;
		} else {
			peak[i] = beta * peak[i - 1];
		}
	}

	// Setting prev_peak to be the peak of last sample of this frame for band given by band_num
	pd->prev_peak[band_num] = peak[len - 1];
}


int peak_detect_destroy(Peak_detect pd)
{
	if (pd != NULL) {
		if (pd->prev_peak != NULL) {
			free(pd->prev_peak);
		}

		free(pd);
	} else {
		return -1;
	}

	return 0;
}
