/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#ifndef CIRCULAR_BUFFER_H__
#define CIRCULAR_BUFFER_H__

typedef struct circular_buffer_t *Circular_buffer;

Circular_buffer circular_buffer_init(size_t buf_len);

int circular_buffer_update_buffer(Circular_buffer obj, float *new_vals, size_t new_vals_len);

size_t circular_buffer_get_internal_buffer(Circular_buffer obj, float *buf);

void circular_buffer_flush(Circular_buffer obj, float *out);

int circular_buffer_destroy(Circular_buffer obj);

size_t circular_buffer_get_buffer_size(Circular_buffer obj);

unsigned char circular_buffer_get_exp(Circular_buffer obj);

#endif /* CIRCULAR_BUFFER_H__ */
