/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#ifndef UTILITIES_H__
#define UTILITIES_H__

#if !defined(ARRAY_SIZE)
    #define ARRAY_SIZE(x) (sizeof((x)) / sizeof((x)[0]))
#endif

/**
 * @brief Load filter taps from file.
 *
 * @param file The file in which to load 32bit float taps from
 * @param taps The array in which the taps will be loaded into
 * @param len The number of taps to load into the "taps" array
 *
 * @return Returns the number of taps loaded.  This should be the same as the "len"
 * parameter.  This way the developer can test if this is the case, and return an
 * error if it is not
 */
int load_filter_taps(const char *file, float *taps, int len);

/**
 * @brief Save filter taps to a file
 *
 * @param file File to save filter taps to
 * @param taps Array of filter taps to save to file
 * @param len Length of the "taps" array: the number of taps to save
 *
 * @return Returns 0 on success, -1 otherwise
 */
int save_filter_taps(const char *file, float *taps, int len);

#endif /* UTILITIES_H__ */
