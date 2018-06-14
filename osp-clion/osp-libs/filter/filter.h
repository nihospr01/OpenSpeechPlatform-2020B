/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */


#ifndef FILTER_H__
#define FILTER_H__

typedef struct filter_t *Filter;

/**
 * @brief Initializes filer data structure
 *
 * @param taps Pointer to filter taps to load into this filter data
 * structure
 * @param tap_length Number of taps pointed to by taps
 *
 * @return Returns the allocated filter data structure
 */
Filter filter_init(const float *taps, int tap_length);

/**
 * @brief Updates the taps in the Filter data structure
 * NOTE: If the tap length is not the same as what was passed into filter_init
 * the function will return an error.  We don't change tap length at runtime
 * just what's loaded into the taps
 *
 * @param obj Filter data structure
 * @param taps New taps to load into old taps' place
 * @param tap_length Number of taps to load into the taps
 *
 * @return Returns 0 on success, -1 if the tap_length parameter does not match
 * the tap length that was set up in filter_init
 * @see filter_init
 */
int filter_update_taps(Filter obj, float *taps, int tap_length);

/**
 * @brief Returns the taps loaded into the taps element in the Filter data structure
 *
 * @param obj Filter data structure
 * @param taps Will contain the currently loaded filter taps
 * @return Returns the length of the internal filter
 */
int filter_get_taps(Filter obj, float *taps);

/**
 * @brief Returns the internal circular buffer
 * 
 * @param obj Filter data structure object
 * @param buf Buffer that will contain the internal circular buffer's elements
 *
 * @return returns the current index of the circular buffer
 */
int filter_get_internal_buffer(Filter obj, float *buf);

/**
 * @brief returns the delay of the current filter
 *
 * @param obj Filter data structure
 *
 * @return Returns the group delay, in samples, of the current filter
 */
int filter_get_delay(Filter obj);

/**
 * @brief Filters input signal with current filter, and returns output signal
 *
 * @param obj Filter data structure
 * @param in Input signal
 * @param out Output signal after filtering
 * @param len The length of the input and output signals (in our implementation, it's
 * one frame size
 *
 */
void filter_proc(Filter obj, float *in, float *out, size_t len);

/**
 * @brief Flushes the internal buffer of the filter.
 * When the last frame has been processed, there will still exist some residual data on
 * the internal circular buffer.  This data would have been used for the next frame's
 * processing, but since we've reached the last frame, it just sits in memory.  In the case
 * of a file-based, non-realtime test where every sample of the output signal is needed, this
 * can be called after the final frame worth of data.  It will return <filter's_group_delay>
 * worth of samples that have been processed with the filter.
 *
 * @param obj Filter data structure
 * @param out Output buffer filled with last processed samples
 */
void filter_flush(Filter obj, float *out);

/**
 *
 * @brief Frees and destroys the filter data structure
 *
 * @param obj Filter data structure
 * @return Returns 0 on success, -1 otherwise
 */
int filter_destroy(Filter obj);

#endif /* FILTER_H__ */
