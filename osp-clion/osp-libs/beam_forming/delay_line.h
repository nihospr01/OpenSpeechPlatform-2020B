/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */


#ifndef delay_line_h
#define delay_line_h

//#define INTERPOLATED

typedef struct delay_line_t *Delay_Line;

/**
 * @brief Initializes Delay Line structure.
 *
 * @see delay_line_tick
 * @param delay The number of samples to delay when calling delay_line_tick
 * @return Returns the Delay_Line data structure
 *	
 */
Delay_Line delay_line_init(unsigned int delay);

/**
 * @brief Returns an output sample that has been delayed by a set number
 * When you call delay_line_tick with a sample, you are putting a sample
 * into its internal circular buffer, and you are getting a sample that
 * has previously been put onto its circular buffer N samples/ticks ago
 * (where N is the delay parameter in delay_line_init).
 *
 * For example, if you set the delay_line_init with a delay of 4 samples,
 * the input and output of delay_line_tick are shown below, where each row
 * represents calling the delay_line_tick function
 * In	Out
 * 1	0
 * 1	0
 * 1	0
 * 1	0
 * 0	1
 * 0	1
 * 0	1
 * 0	1
 * 0	0
 * 0	0
 * ...
 *
 * @param del_line The Delay_Line struct returned from delay_line_init
 * @param input_sample The input sample that will be returned N ticks in the future
 *
 * @return A sample that was previously passed into delay_line_tick N ticks in the 
 * past
 *
 */
float delay_line_tick(Delay_Line del_line, float input_sample);

/**
 * @brief Frees the Delay_Line data structure
 *
 * @param del_line Delay_Line data structure to be freed
 * @return 0 on success, -1 otherwise
 */
int delay_line_free(Delay_Line del_line);

/**
 * @brief Flushes internal circular buffer that contains previous samples put on by 
 * calls to delay_line_tick
 *
 * @param del_line The data structure containing delay_line details
 * @return 0 on success, -1 otherwise. Need to fix this
 */
int delay_line_flush(Delay_Line del_line);

#endif /* delay_line_h */
