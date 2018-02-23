/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

/**
 * @file speech_enhancement.h
 */

#ifndef SPEECH_ENHANCEMENT_H__
#define SPEECH_ENHANCEMENT_H__

#include <math.h>
#include <stddef.h>
#include "array_utilities.h"

/*
 * @brief Function to compute the spectral subtraction gains as a function of
 *  frequency band and apply them to the noisy speech. The noise power is
 *  estimated from the noisy speech using minina statistics. Two versions of
 *  spectral subtraction are implemented: Power spectral subtraction with a
 *  fixed oversubtraction factor.
 *	@param input Pointer to the signal array at the frequency sub-band
 *  @param ntype Noise estimation procedure: 1 = Arslan et al. (ICASSP '95) averaging procedure, 2 = Hirsch & Ehrlicher (ICASSP '95) wgt ave procedure, 3 = Cohen & Berdugo MCRA (IEEE Sig Proc Let 9, pp 12-15, 2002)
 *  @param stype Spectral subtraction algorithm: 0 = skip, 1 = oversubtraction (Wiener filter if factor = 1)
 *  @param sparam Spectral subtraction parameters, If stype = 1: param = oversubtraction factor
 *  @param fsamp sampling rate in Hz
 *  @param out Pointer to an array where the enhanced speech data is written to.
 */


void speech_enhancement(float *input,
                        int ntype,
                        int stype,
                        float sparam,
                        size_t len,
                        int fsamp,
                        float *out);

#endif /* SPEECH_ENHANCEMENT_H__ */
