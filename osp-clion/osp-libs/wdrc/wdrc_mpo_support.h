/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

/**
 * @file wdrc_mpo_support.h
 */

#ifndef WDRC_MPO_SUPPORT_H__
#define WDRC_MPO_SUPPORT_H__

#include <stddef.h>


/**
 * @brief Function to apply dynamic-range compression to a single sub-band of the output of the analysis filter bank.
 *
 * The peak detector output in dB SPL is needed as one of the inputs. The gain at 50 and 80 dB SPL is specified 
 * for the frequency sub-band, along with the lower and upper kneepoints in dB SPL. 
 * The compressor is linear below the lower kneepoint and applies compression limiting above the upper kneepoint
 *
 * @param pdB Pointer to the peak detector output array of the sub-band in dB SPL (output from peak_to_spl)
 * @param gain50 Gain in dB at the sub-band frequency for an input at 50 dB SPL
 * @param gain80 Gain in dB at the sub-band frequency for an input at 80 dB SPL
 * @param knee_low Lower kneepoint in dB SPL for the sub-band
 * @param len Length of the input array
 * @param wdrcdB Pointer to an array where the compressed output of the sub-band will be written. i.e. the output of WDRC
 */
void wdrc_mpo_support(float *pdB,
			float gain50,
			float gain80,
			float knee_low,
			size_t len,
			float *wdrcdB);

#endif /* WDRC_MPO_SUPPORT_H__ */
