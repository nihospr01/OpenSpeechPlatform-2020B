/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#include <stdlib.h>

#include "resample.h"
#include "filter.h"

#define SIZE_32		32
#define SIZE_48		48

/**
 *	@brief Data struct containing variables for resampler
 */
struct resample_t {
	Filter upsample;	///< Upsample filter
	Filter downsample;	///< downsample filter
};

static size_t interpolate(float *input, float *output, size_t input_len, int int_factor)
{
	int i, j;
	for (i = 0; i < input_len; i++) {
		for (j = 0; j < int_factor; j++) {
			if (j == 0) {
				output[(i * int_factor) + j] = input[i];
			} else {
				output[(i * int_factor) + j] = 0;
			}
		}
	}

	return (input_len * int_factor);
}

static size_t decimate(float *input, float *output, size_t input_len, int dec_factor)
{
	int i;
	for (i = 0; i < input_len; i += dec_factor) {
		output[i / dec_factor] = input[i];
	}

	return (input_len / dec_factor);
}

Resample resample_init(const float *upsample_taps, int up_len, const float *downsample_taps, int down_len)
{
	struct resample_t *obj;

	if ((obj = (struct resample_t*)malloc(sizeof(struct resample_t))) == NULL) {
		return NULL;
	}

	if ((obj->upsample = filter_init(upsample_taps, up_len)) == NULL) {
		goto abort;
	}

	if ((obj->downsample = filter_init(downsample_taps, down_len)) == NULL) {
		goto abort1;
	}

	return obj;

abort1:
	free(obj->upsample);
abort:
	free(obj);
	return NULL;
}

size_t resample_48_32(Resample obj, float *in_48, float *out_32, int size_input)
{
	// Hack
	if (size_input != SIZE_48) {
		return -1;
	}

	float interpolated[48 * 2];
	float filtered[48 * 2];	
	size_t ret;

	// Takes input, interpolates by 2, output: 48 * 2 = 96
	ret = interpolate(in_48, interpolated, SIZE_48, 2);

	// Takes 96, gives 96
	filter_proc(obj->downsample, interpolated, filtered, ret);

	// Takes 96, gives 32
	ret = decimate(filtered, out_32, ret, 3);
	return ret;
}

size_t resample_32_48(Resample obj, float *in_32, float *out_48, size_t size_input)
{
	// Hack
	if (size_input != SIZE_32) {
		return -1;
	}

	float interpolated[SIZE_32 * 3];
	float filtered[SIZE_32 * 3];
	size_t ret;

	// Takes input, interpolates by 3, output: 32 * 3 = 96
	ret = interpolate(in_32, interpolated, SIZE_32, 3);

	// Takes 96, gives 96,
	filter_proc(obj->upsample, interpolated, filtered, ret);

	// Takes 96, gives 48
	ret = decimate(filtered, out_48, ret, 2);
	return ret;
}

size_t resample_96_32(Resample obj, float *in_96, float *out_32, size_t size_input)
{
	size_t ret;
	float filtered[size_input]; // Get rid of all these intermediate dummy buffers

	filter_proc(obj->downsample, in_96, filtered, size_input);
	ret = decimate(filtered, out_32, size_input, 3);
	return ret;
}

size_t resample_32_96(Resample obj, float *in_32, float *out_96, size_t size_input)
{
	size_t ret;
	float filtered[size_input * 3];

	ret = interpolate(in_32, filtered, size_input, 3);
	filter_proc(obj->upsample, filtered, out_96, size_input * 3);
	return ret;
}

int resample_destroy(Resample obj)
{
	if (obj != NULL) {
		if (obj->upsample) {
			filter_destroy(obj->upsample);	
		} else {
			return -1;
		}

		if (obj->downsample) {
			filter_destroy(obj->downsample);	
		} else {
			return -1;
		}

		free(obj);
	} else {
		return -1;
	}

	return 0;
}
