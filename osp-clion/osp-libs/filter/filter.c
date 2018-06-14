/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */


#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <assert.h>
#include <math.h>

#include "filter.h"

/**
 * @brief Structure containing filter information
 */
struct filter_t {
    float  *taps;		///< Taps of the filter (loaded in at init)
    float  *buf;		///< Internal buffer.  Keeps track of old samples between frames
    int     length;		///< Length of the filter
    int     index;		///< Internal index.  Keeps track of where we are on internal buffer
	unsigned int buf_len;
	unsigned char exp;
};

Filter filter_init(const float *taps, int tap_length)
{
    int i;
	unsigned int buf_sz;
    struct filter_t *obj;

    if ((obj = (struct filter_t*)malloc(sizeof(struct filter_t))) == NULL) {
        goto abort;
    }

    /* Getting a buffer size as a power of 2 */
	buf_sz = tap_length + 96;
    buf_sz |= buf_sz >> 1;
    buf_sz |= buf_sz >> 2;
    buf_sz |= buf_sz >> 4;
    buf_sz |= buf_sz >> 8;
    buf_sz |= buf_sz >> 16; 
	buf_sz++;

	buf_sz = 1024;
	obj->exp = 11;

	//printf("Buffer size = %d, exp = %d\n", buf_sz, obj->exp);

	obj->buf_len = buf_sz;

    obj->length = tap_length;
    if ((obj->taps = malloc(tap_length * sizeof(float))) == NULL) {
        goto abort;
    }

    if ((obj->buf = malloc(buf_sz * sizeof(float))) == NULL) {
        free(obj->taps);
        goto abort;
    }

    for (i = 0; i < tap_length; i++) {
        obj->taps[i] = taps[tap_length - i - 1];
        // obj->taps[i] = taps[i];
    }

	for (i = 0; i < buf_sz; i++) {
        obj->buf[i] = 0.0; // Might not be needed
	}

    obj->index = 0;

    return obj;

abort:
    free(obj);
    return NULL;
}

int filter_update_taps(Filter obj, float *taps, int tap_length)
{
	int i;

	// can't change tap length on the fly.  In the future, we could with
	// realloc, but we'll do that later.
	if (tap_length != obj->length) {
		return -1;
	}

	for (i = 0; i < tap_length; i++) {
		obj->taps[i] = taps[tap_length - i - 1];
	}

	return 0;
}

int filter_get_taps(Filter obj, float *taps)
{
	int i;

	for (i = 0; i < obj->length; i++) {
		taps[i] = obj->taps[obj->length -i - 1];
	}

	return obj->length;
}

int filter_get_internal_buffer(Filter obj, float *buf)
{
	int i;

	for (i = 0; i < obj->length; i++) {
		buf[i] = obj->buf[i];
	}

	return obj->index;
}

int filter_get_delay(Filter obj)
{
    return (obj->length / 2);
}

void DSPF_sp_fircirc (float x[], float h[], float r[],
						int index, int csize, int nh, size_t nr)
{
	int i, j;
	//Circular Buffer block size = ((2^(csize + 1)) / 4)
	//floating point numbers
	int mod = (1 << (csize - 1));
	float r0;
	for (i = 0; i < nr; i++) {
		r0 = 0;
		for (j = 0; j < nh; j++) {
			//Operation ”% mod” is equivalent to ”& (mod −1)”
			// r0 += x[(i + j + index) % mod] * h[j];
            r0 += x[(i + j + index) & (mod - 1)] * h[j];
		}

		r[i] = r0;
	}
}

void filter_proc(Filter obj, float *in, float *out, size_t len)
{
    int i;

#if 1
	unsigned int buf_len;
	unsigned int indx;
	float *buf = obj->buf;
	float *taps = obj->taps;
	int length = obj->length;	
	unsigned char exp = obj->exp;	

	buf_len = obj->buf_len;
	indx = obj->index;

	for (i = 0; i < len; i++) {
		obj->buf[(indx + i) & (buf_len - 1)] = in[i];
	}

	indx = (indx + len) & (buf_len - 1);

	DSPF_sp_fircirc(buf, taps, out, (indx - length + buf_len - len + 1) & (buf_len - 1), exp, length, len);

	obj->index = indx;
#endif

#if 0
	// Saving params as local vars increases real-time performance
    float  *taps = obj->taps;
    float  *buf = obj->buf;
    int     length = obj->length;
    int     index = obj->index;

    for(n = 0; n < len; n++) {
        out[n] = 0.0;
        obj->buf[index] = in[n]; /* nuke the oldest sample with a new sample */
        for (k = 0; k < length; k++) {
			i = k + index + 1;
            out[n] += taps[length - 1 - k] * obj->buf[i % length];
        }

        index = (index + 1) % length;
    }

    obj->index = index;
#endif
}

void filter_flush(Filter obj, float *out)
{
    int delay;
    float in[obj->length];
    memset(in, 0x00, obj->length * sizeof(float));

    delay = filter_get_delay(obj);
    /* this flushes out buffer for the last n samples. For file based processing */
    filter_proc(obj, in, out, delay);
}

int filter_destroy(Filter obj)
{
    if (obj != NULL) {
        if (obj->taps != NULL) {
           free(obj->taps);
        } else {
            return -1;
        }

        if (obj->buf != NULL) {
            free(obj->buf);
        } else {
            return -1;
        }

        free(obj);
    } else {
        return -1;
    }

    return 0;
}
