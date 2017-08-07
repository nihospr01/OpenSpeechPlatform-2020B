
/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

/*
//  delay.c
//  DSP_lib
*/

#include "delay_line.h"
#include <stdlib.h>
#include <string.h>
#include <math.h>

/**
 * @brief Data structure that contains delay_line information
 */
struct delay_line_t
{
	float* 	delay_buffer;		///< The circ. buffer containing old delay_line samples
	long 	delay_size;			///< Size of the delay. Set up on init
	long 	delay_mask;			///< 
	long 	write_pointer;		///< Write pointer of circular buffer
};

Delay_Line delay_line_init(unsigned int delay)
{
	struct delay_line_t * del_line;
	unsigned int new_time_samps;
	
	/* alloc memory for delay line object */
	if ((del_line = (struct delay_line_t*)malloc(sizeof(struct delay_line_t))) == NULL) {
		return NULL;
	}
	
	/* get buffer size in samps (round to nearest power of 2) */
	new_time_samps = delay;

	if (new_time_samps == 0) {
		del_line->delay_size = 0;
		return del_line;
	}
	
	new_time_samps--;

	/* Getting a buffer size as a power of 2 */
	new_time_samps |= new_time_samps >> 1;
	new_time_samps |= new_time_samps >> 2;
	new_time_samps |= new_time_samps >> 4;
	new_time_samps |= new_time_samps >> 8;
	new_time_samps |= new_time_samps >> 16;
	new_time_samps++;
	del_line->delay_size = new_time_samps;

	/* get delay mask */
	del_line->delay_mask = del_line->delay_size - 1;
	
	/* alloc memory for buffer */
	del_line->delay_buffer = (float*)calloc(del_line->delay_size, sizeof(float));
	
	/* initialize write pointer (backwards moving) */
	del_line->write_pointer = del_line->delay_mask;
	
	return del_line;
}

float delay_line_tick(Delay_Line del_line, float input_sample){
	
	long read_pointer;
	float output_sample;

	if (del_line->delay_size == 0) {
		return input_sample;
	}
	
	/* uses circular masks to wrap read/write pointer*/
	
	/* read value from buffer */
	read_pointer = del_line->write_pointer + del_line->delay_size;
	read_pointer &= del_line->delay_mask;
	output_sample = *(del_line->delay_buffer + read_pointer);
	
	/* record delayed input sample*/
	*(del_line->delay_buffer + del_line->write_pointer) = input_sample;
	del_line->write_pointer--;
	del_line->write_pointer &= del_line->delay_mask;
	
	return output_sample;
}

int delay_line_free(Delay_Line del_line)
{
	// check to make sure obj exists
	if (NULL != del_line) {
		// check to make sure obj bufer exists
		if (NULL != del_line->delay_buffer) {
			free(del_line->delay_buffer);
		} else{
			return -1;	// return error
		}
		
		free(del_line);
		del_line = NULL;
		return 0;	// return destroy OK!
	} else{
		return -1;	// return error
	}

	return -1;
}

int delay_line_flush(Delay_Line del_line)
{
	int i;
	
	for (i = 0; i < del_line->delay_size; i++) {
		if (del_line->delay_buffer[i] != 0) {
			return -1;
		}
	}
	
	return 0;
}
