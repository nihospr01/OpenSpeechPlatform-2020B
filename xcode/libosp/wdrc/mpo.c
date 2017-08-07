/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#include "mpo.h"

#include <math.h>

void mpo(float *input,
			float *pdB,
			float *wdrcdB,
			float mpo_limit,
			size_t len,
			float *out)
{
	int i;
	float g; // variable to store gain in dB
	
	// Compute the limiting gain in dB above MPO limit
	for(i = 0; i < len; i++){
		if((pdB[i] + wdrcdB[i]) >= mpo_limit) {
			g = mpo_limit - pdB[i];	 // Above MPO limit
			
		}
		else {
			g = wdrcdB[i];	// No gain below MPO limit
		}

		// Apply the compression gain to the signal in the band
		g = pow(10, g / 20.0);	// Convert the gain to linear scale
		out[i] = input[i] * g;	// Multiply the signal by the gain
	}
}
