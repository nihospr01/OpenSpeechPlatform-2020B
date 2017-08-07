/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#ifndef RESAMPLE_H__
#define RESAMPLE_H__

typedef struct resample_t *Resample;

/**
 * @brief Initialization function for the resample structure
 *
 * @see resample_t
 * @param upsample_taps The taps of the filter to be filtered with the input after being
 * interpolated
 * @param up_len Length of upsample filter
 * @param downsample_taps The taps of the filter to be filtered with the signal before being
 * decimated
 * @param down_len Length of the downsample filter
 *
 * @return Returns the allocated instance of the OSP TCP layer data structure ("object"
 */
Resample resample_init(const float *upsample_taps, int up_len,
						const float *downsample_taps, int down_len);

/**
 * @brief Resamples an input of 32 samples at 32kHz sampling rate, and resamples it to 48kHz
 *
 * @param obj Resample struct
 * @param in_32 Input array of float samples.  Must be 32 elements long
 * @param out_48 Outout array of float samples.  Must be allocated to be 48 elements long.
 * @param size_input The size of the in_32 array.  This is tested internally and must be 32, or else
 * -1 is returned.
 */
size_t resample_32_48(Resample obj, float *in_32, float *out_48, size_t size_input);

/**
 * @brief Resamples an input of 48 samples at 48kHz sampling rate, and resamples it to 32kHz
 *
 * @param obj Resample struct
 * @param in_48 Input array of float samples.  Must be 48 elements long
 * @param out_32 Outout array of float samples.  Must be allocated to be 32 elements long.
 * @param size_input The size of the in_48 array.  This is tested internally and must be 48, or else
 * -1 is returned.
 */
size_t resample_48_32(Resample obj, float *in_48, float *out_32, int size_input);

/**
 * @brief Resamples an input of 96 samples at 96kHz sampling rate, and resamples it to 32kHz
 *
 * @param obj Resample struct
 * @param in_96 Input array of float samples.  Must be 96 elements long
 * @param out_32 Outout array of float samples.  Must be allocated to be 32 elements long.
 * @param size_input The size of the in_96 array.  This is tested internally and must be 96, or else
 * -1 is returned.
 */
size_t resample_96_32(Resample obj, float *in_96, float *out_32, size_t size_input);

/**
 * @brief Resamples an input of 32 samples at 32kHz sampling rate, and resamples it to 96kHz
 *
 * @param obj Resample struct
 * @param in_32 Input array of float samples.  Must be 32 elements long
 * @param out_96 Outout array of float samples.  Must be allocated to be 96 elements long.
 * @param size_input The size of the in_32 array.  This is tested internally and must be 32, or else
 * -1 is returned.
 */
size_t resample_32_96(Resample obj, float *in_32, float *out_96, size_t size_input);

/**
 * @brief Frees the Resample struct
 *
 * @param obj Resample struct
 * @return Returns 0 on success, -1 on error
 */
int resample_destroy(Resample obj);

#endif /* RESAMPLE_H__ */
