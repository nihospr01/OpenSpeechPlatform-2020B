/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

 /*
 * General utilities for manipulating common arrays that we deal with
 * in the AFC, PD, WDRC, etc functions.
 */

#ifndef ARRAY_UTILITIES_H__
#define ARRAY_UTILITIES_H__

/**
 * @brief Function to reverse an array
 * @param arr Pointer to the array
 * @param len Length of the array
 */
void array_flip(float *arr, size_t len);

/**
 * @brief Function to calculate the sum of an array
 * @param arr Pointer to the array
 * @param len Length of the array
 * @return Sum of the array
 */
float array_sum(float *arr, size_t len);

/**
 * @brief Function to calculate the dot-product of two 1-D vectors/arrays
 * @param in1 Pointer to the first vector
 * @param in2 Pointer to the second vector
 * @param len Length of the vectors
 * @return Dot product (inner product) of the two vectors
 * @warning Assumes both the vectors are of same length and takes only one length parameter
 */
float array_dot_product(float *in1, float *in2, size_t len);

/**
 * @brief Function to right shift an array by one place. Left most value will be replaced by zero
 * @param arr Pointer to the array
 * @param len Length of the array
 */
void array_right_shift(float *arr, size_t len);

/**
 * @brief Function to multiply each element of an array by a scalar constant
 * @param arr Pointer to the array
 * @param constant The constant scalar multiplier
 * @param len Length of the array
 */
void array_multiply_const(float *arr, float constant, size_t len);

/**
 * @brief Function to add a scalar constant to each element of an array 
 * @param arr Pointer to the array
 * @param constant The constant scalar adder
 * @param len Length of the array
 */
void array_add_const(float *arr, float constant, size_t len);

/**
 * @brief Function to do element wise addition of two arrays
 * @param in1 Pointer to the first array
 * @param in2 Pointer to the second array
 * @param len Length of the arrays
 * @warning Assumes both the arrays are of same length and takes only one length parameter
 */
void array_add_array(float *in1, float *in2, size_t len);

/**
 * @brief Function to do element wise subtraction of two arrays
 * @param in1 Pointer to the first array
 * @param in2 Pointer to the second array
 * @param len Length of the arrays
 * @warning Assumes both the arrays are of same length and takes only one length parameter
 */
void array_subtract_array(float *in1, float *in2, size_t len);

/**
 * @brief Function to do element wise multiplication of two arrays
 * @param in1 Pointer to the first array
 * @param in2 Pointer to the second array
 * @param len Length of the arrays
 * @warning Assumes both the arrays are of same length and takes only one length parameter
 */
void array_element_multiply_array(float *in1, float *in2, size_t len);

/**
 * @brief Function to return the minimum of the elements of an array
 * @param arr Pointer to the array
 * @param len Length of the array
 * @return Minimum of the array elements
 */
float array_min(float *arr, size_t len);

/**
 * @brief Function to calculate the mean of the elements of an array
 * @param arr Pointer to the array
 * @param len Length of the array
 * @return Mean of the array elements
 */
float array_mean(float *arr, size_t len);

/**
 * @brief Function to populate the output array with square of the elements of an input array
 * @param in Pointer to the input array
 * @param out Pointer to the output array
 * @param len Length of the arrays
 * @warning Assumes that output array already has memory allocated to it
 */
void array_square(float *in, float *out, size_t len);

/**
 * @brief Function to calculate the mean square of the elements of an array
 * @param arr Pointer to the array
 * @param len Length of the array
 * @return Mean square of the array elements
 */
float array_mean_square(float *arr, size_t len);

/**
 * @brief Function to copy an array from source to destination
 * @param dst Pointer to the destination array
 * @param src Pointer to the source array
 * @param len Length of the arrays
 * @warning Assumes that destination array already has memory allocated to it
 */
void memcpy_f(float *dst, float *src, size_t len);

/**
 * @brief Function to print an array for debugging
 * @param str String to use for debugging
 * @param arr Pointer to the array
 * @param len Length of the array
 */
void array_print(const char *str, float *arr, size_t len);


#endif /*ARRAY_UTILITIES_H__ */
