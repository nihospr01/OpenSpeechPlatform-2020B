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

#include "circular_buffer.h"

struct circular_buffer_t {
    float					*buf;
    size_t				length;
    size_t				index;
		size_t				buf_len;
		unsigned char exp;
};

Circular_buffer circular_buffer_init(size_t buf_len)
{
	size_t buf_sz;
    struct circular_buffer_t *obj;

    if ((obj = (struct circular_buffer_t*)malloc(sizeof(struct circular_buffer_t))) == NULL) {
        goto abort;
    }

    /* Getting a buffer size as a power of 2 */
	buf_sz = buf_len + 96;
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

    obj->length = buf_len;

    if ((obj->buf = (float *)calloc(buf_sz, sizeof(float))) == NULL) {
        goto abort;
    }

	// for (i = 0; i < buf_sz; i++) {
 //        obj->buf[i] = 0.0; // Might not be needed
	// }

    obj->index = 0;

    return obj;

abort:
    free(obj);
    return NULL;
}

int circular_buffer_update_buffer(Circular_buffer obj, float *new_frame, size_t new_frame_len)
{
	int i;
	size_t buf_len = obj->buf_len;
	size_t index = obj->index;

	for(i = 0; i < new_frame_len; i++) {
		// buf[(index + i) & (buf_len - 1)] = new_frame[i];
		obj->buf[(index + i) % buf_len] = new_frame[i];
	}
	// obj->index = (index + new_frame_len) & (buf_len - 1);
	index = (index + new_frame_len) % buf_len;
	obj->index = index;
	
	return 0;
}

size_t circular_buffer_get_internal_buffer(Circular_buffer obj, float *buf){
	size_t i;

	for (i = 0; i < obj->buf_len; i++) {
		buf[i] = obj->buf[i];
	}

	return obj->index;
}


size_t circular_buffer_get_buffer_size(Circular_buffer obj){
	return obj->buf_len;
}

unsigned char circular_buffer_get_exp(Circular_buffer obj){
	return obj->exp;
}

void circular_buffer_flush(Circular_buffer obj, float *out){

}

int circular_buffer_destroy(Circular_buffer obj){
	if (obj != NULL) {
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
