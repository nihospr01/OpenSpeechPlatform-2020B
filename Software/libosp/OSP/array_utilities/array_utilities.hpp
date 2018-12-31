//
// Created by Kuan-Lin Chen on 11/21/18.
//

/**
 * @file array_utilities.hpp
 */

#ifndef OSP_ARRAY_UTILITIES_H
#define OSP_ARRAY_UTILITIES_H

#include <cstddef>

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
float array_sum(const float *arr, size_t len);

/**
 * @brief Function to calculate the dot-product of two 1-D vectors/arrays
 * @param in1 Pointer to the first vector
 * @param in2 Pointer to the second vector
 * @param len Length of the vectors
 * @return Dot product (inner product) of the two vectors
 * @warning Assumes both the vectors are of same length and takes only one length parameter
 */
float array_dot_product(const float *in1, const float *in2, size_t len);

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
void array_add_array(float *in1, const float *in2, size_t len);

/**
 * @brief Function to do element wise subtraction of two arrays
 * @param in1 Pointer to the first array
 * @param in2 Pointer to the second array
 * @param len Length of the arrays
 * @warning Assumes both the arrays are of same length and takes only one length parameter
 */
void array_subtract_array(float *in1, const float *in2, size_t len);

/**
 * @brief Function to do element wise multiplication of two arrays
 * @param in1 Pointer to the first array
 * @param in2 Pointer to the second array
 * @param len Length of the arrays
 * @warning Assumes both the arrays are of same length and takes only one length parameter
 */
void array_element_multiply_array(float *in1, const float *in2, size_t len);

/**
 * @brief Function to do element wise division of two arrays
 * @param in1 Pointer to the first array
 * @param in2 Pointer to the second array
 * @param len Length of the arrays
 * @warning Assumes both the arrays are of same length and takes only one length parameter
 */
void array_element_divide_array(float *in1, const float *in2, size_t len);

/**
 * @brief Function to return the minimum of the elements of an array
 * @param arr Pointer to the array
 * @param len Length of the array
 * @return Minimum of the array elements
 */
float array_min(const float *arr, size_t len);

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
void array_square(const float *in, float *out, size_t len);

/**
 * @brief Function to calculate the mean square of the elements of an array
 * @param arr Pointer to the array
 * @param len Length of the array
 * @return Mean square of the array elements
 */
float array_mean_square(const float *arr, size_t len);

/**
 * @brief Function to print an array for debugging
 * @param str String to use for debugging
 * @param arr Pointer to the array
 * @param len Length of the array
 */
void array_print(const char *str, float *arr, size_t len);

#endif //OSP_ARRAY_UTILITIES_H
