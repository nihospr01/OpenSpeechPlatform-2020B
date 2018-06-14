/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

/*
 * Spatial beamforming using two input microphones through the use of delay and gains.
 */
#ifndef BEAM_FORMING_H__
#define BEAM_FORMING_H__

typedef struct beam_form_t *Beam_Form;

/**
 * @brief Initialization function for the beam forming data struct
 *
 * @see beam_form_t
 * @param beta beta value
 * @param gain gain value
 * @param sample_rate Sample rate set by audio drivers. Used in calculation of angle
 * @return Returns the allocated instance of the beamforming data structure ("object")
 */
Beam_Form beam_form_init(float beta, float gain, float sample_rate);

/**
 * @brief Takes in two channel inputs, and attempts to use beamforming to provide directed output
 * The in_front, in_rear, and output arrays must be allocated so that they are len samples long
 *
 * @see beam_form_t
 * @param beam_form The instance of the Beam_Form variable that was returned from beam_form_init
 * @param in_front Float array of the input samples from the "front" mic that are len floats long
 * @param in_rear Float array of the samples from the "rear" mic that are len floats long
 * @param output Float array containing the processed samples that are len floats long
 * @param len The length of the input 
 */
void beam_form_proc(Beam_Form beam_form, float *in_front, float *in_rear, float *output, int len);

/**
 * @brief Closes and frees the beam_form instance
 *
 * @see beam_form_t
 * @param beam_form The instance of the Beam_Form variable that was returned from beam_form_init
 *
 */
void beam_form_destroy(Beam_Form beam_form);

#endif /* BEAM_FORMING_H__ */
