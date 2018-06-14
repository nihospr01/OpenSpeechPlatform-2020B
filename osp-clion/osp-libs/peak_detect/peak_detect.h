/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

/**
 * @file peak_detect.h
 */

#ifndef PEAK_DETECT_H__
#define PEAK_DETECT_H__

#include <stddef.h>

#define DBL_EPSILON 2.2204460492503131e-16 ///< Small constant to avoid taking the log of zero
#define DEFAULT_LEVEL	(100) ///< Default dB SPL level corresponding to signal RMS value of 1 

typedef struct peak_detect_t *Peak_detect;

/**
 * @brief Function to initialize the peak_detect data struct
 *
 * Allocates memory to the Peak_detect instance and initialize its parameters.
 * prev_peak is set to zero for all bands for the first time.
 * 
 * @param fsamp Sampling rate in Hz
 * @param num_bands Number of sub-bands. Required to store prev_peak values for each band
 * @return Peak_detect Returns the Peak_detect instance/object if memory is succesfully allocated or returns NULL. 
 */
Peak_detect peak_detect_init(float fsamp, unsigned int num_bands);

/**
 * @brief Function to convert the output from the peak detector into dB SPL (Sound Pressure Level).
 *
 * The SPL output will be used by the wide dynamic-range compression (wdrc) function. The function assumes that an 
 * RMS signal value of 1 corresponds to DEFAULT_LEVEL(100) dB SPL and converts the signal accordingly.
 * The input WAV file therefore needs to be set to RMS = 1 at the start of processing.
 * 
 * @param peak Pointer to the signal to be conveted to dB SPL. i.e. the output from the peak_detect
 * @param pdb Pointer to the signal converted to dB SPL. i.e. the output of peak_to_spl
 * @param len Length of the input signal to be converted to dB SPL
 */
void peak_to_spl(float *peak, float *pdb, size_t len);

/**
 * @brief Function to peak detect the signal. The attack and release times are specified as ANSI values
 * which are then converted to filter time constants. The peak detector is given by Kates, Eq (8.1).
 * 
 * @see peak_detect_t 
 * @param pd The instance of the Peak_detect structure that was returned from peak_detect_init
 * @param input Pointer to the signal array at the frequency sub-band (output from sub-band filtering)
 * @param len Length of the input signal
 * @param peak Pointer to the array that will have peak detection output. i.e. output from peak_detect
 * @param attack_time Attack time in milliseconds
 * @param release_time Release time in milliseconds
 * @param band_num The sub-band number that is being operated on. Required to get the prev_peak value for that band
 */
void peak_detect(Peak_detect pd,
						float *input,
						size_t len,
						float *peak,
						float attack_time,
						float release_time,
						int band_num);

/**
 * @brief Function to close and free the Peak_detect instance
 *
 * @see peak_detect_t
 * @param pd The instance of the Peak_detect structure that was returned from peak_detect_init
 * @return 0 if successful. -1 otherwise
 */
int peak_detect_destroy(Peak_detect pd);


#endif /* PEAK_DETECT_H__ */
