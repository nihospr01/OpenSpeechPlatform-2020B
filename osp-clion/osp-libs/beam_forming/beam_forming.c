/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */


#include <stdlib.h>
#include <math.h>

#include "beam_forming.h"
#include "delay_line.h"

#define DISTANCE			0.1	///< Distance between front and rear microphones in meters
#define SPEED_OF_SOUND		340.29	///< Speed of sound in meters/second

/**
 * @brief data structure containing relevant beam-forming fields
 */
struct beam_form_t {
	Delay_Line delay_line;	///< instance of delay_line structure
	float beta;				///< Rear microphone delay = beta * DISTANCE / SPEED_OF_SOUND 
	float gain;				///< Rear microphone gain
	unsigned char no_delay;	///< Bypasses delay line code if there's no delay Might remove
};

Beam_Form beam_form_init(float beta, float gain, float sample_rate)
{
	struct beam_form_t *obj;
	float tao;
	int num_samples_delay;

	tao = beta * (DISTANCE / SPEED_OF_SOUND);
	num_samples_delay = (int)trunc(tao * sample_rate);

	if ((obj = (struct beam_form_t*)malloc(sizeof(struct beam_form_t))) == NULL) {
		return NULL;
	}

	if ((obj->delay_line = delay_line_init(num_samples_delay)) == NULL) {
		goto abort;
	}

	if (num_samples_delay == 0) {
		obj->no_delay = 1;
	} else {
		obj->no_delay = 0;
	}

	obj->beta = beta;
	obj->gain = gain;

	return obj;

abort:
	free(obj);
	return NULL;
}

void beam_form_proc(Beam_Form beam_form, float *in_front, float *in_rear, float *output, int len)
{
	int i;

	if (beam_form->no_delay) {
		for (i = 0; i < len; i++) {
			output[i] = (in_front[i] + in_rear[i]) / 2;
		}
	} else {
		for (i = 0; i < len; i++) {
			output[i] = in_front[i] - (beam_form->gain * delay_line_tick(beam_form->delay_line, in_rear[i]));
		}
	}
}

void beam_form_destroy(Beam_Form beam_form)
{
	if (beam_form != NULL) {
		if (beam_form->delay_line != NULL) {
			delay_line_free(beam_form->delay_line);
		}

		free(beam_form);
	}
}
