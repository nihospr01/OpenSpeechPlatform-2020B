/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#include "wdrc_mpo_support.h"

void wdrc_mpo_support(float *pdB,
			float gain50,
			float gain80,
			float knee_low,
			size_t len,
			float *wdrcdB)
{
	int i;
	float g; // variable to store gain in dB

	float slope = (gain80 - gain50) / 30.0;	// Compression Ratio = 1.0/(1+slope)
	float glow = gain50 - slope * (50 - knee_low);	// Gain in dB at lower kneepoint
	
	// Compute the compression gain in dB using the peak detector dB values and write compressed signal in out
	for(i = 0; i < len; i++){
		if(pdB[i] < knee_low) {
			g = glow;	 // Linear gain below lower kneepoint
		} 
		else {
			g = glow + slope * (pdB[i] - knee_low);	// Compression in between kneepoints
		}
		wdrcdB[i] = g;
	}
}
