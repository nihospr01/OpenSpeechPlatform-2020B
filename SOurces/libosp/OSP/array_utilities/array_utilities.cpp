//======================================================================================================================
/** @file array_utilities.cpp
 *  @author Open Speech Platform (OSP) Team, UCSD
 *  @copyright Copyright (C) 2020 Regents of the University of California Redistribution and use in source and binary
 *  forms, with or without modification, are permitted provided that the following conditions are met:
 *
 *      1. Redistributions of source code must retain the above copyright notice, this list of conditions and the
 *      following disclaimer.
 *
 *      2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 *      following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 *  INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 *  SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *  SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 *  WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
 *  USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
//======================================================================================================================

//
// Last Modified by Kuan-Lin Chen on 2018/11/21.
//

#include <cstdio>
#include <OSP/array_utilities/array_utilities.hpp>

void
array_flip(float *arr, size_t len) {
    float temp;
    size_t start = 0;
    size_t end = len - 1;
    while (start < end)
    {
        temp = arr[start];
        arr[start] = arr[end];
        arr[end] = temp;
        start++;
        end--;
    }
}


/**
 * @brief Add all the numbers in an array
 * 
 * @param arr input array
 * @param len sizeof array
 * @return float Sum of the array
 */
float array_sum(const float *arr, size_t len) {
    float total = 0.0;
    for (int i = 0; i < (int)len; i++) total += arr[i];
    return total;
}

#if 0
// the previous array_sum() attempted to do some
// pipeline optimization.  Even if it had been done correctly
// it probably would not have produced faster results
// on an ARM with the small array sizes we use.  
// For reference, here is a version that works.
// We could benchmark it if needed.

float array_sum(const float *arr, size_t len) {
  float total[4] = {0,0,0,0};
  int slen = len & (~3);

  for (int i = 0; i < slen; i += 4) {
    total[0] += arr[i];
    total[1] += arr[i + 1];
    total[2] += arr[i + 2];
    total[3] += arr[i + 3];
  }
  if ((int)len > slen) {
    for (int i = slen; i < (int)len; i++) {
      total[0] += arr[i];
    }
  }
  return total[0] + total[1] + total[2] + total[3];
}
#endif

float
array_dot_product(const float *in1, const float *in2, size_t len) {
    size_t i;
    float res = 0;

    for(i = 0; i < len; i++) {
        res += (in1[i] * in2[i]);
    }
    return res;
}

void
array_right_shift(float *arr, size_t len) {
    size_t i;
    for(i = len-1; i > 0; i--) {
        arr[i] = arr[i-1];
    }
    arr[0] = 0;
}

void
array_multiply_const(float *arr, float constant, size_t len) {
    size_t i;

    for (i = 0; i < len; i++) {
        arr[i] *= constant;
    }
}

void
array_add_const(float *arr, float constant, size_t len) {
    size_t i;

    for (i = 0; i < len; i++) {
        arr[i] += constant;
    }
}

void
array_add_array(float *in1, const float *in2, size_t len) {
    size_t i;

    for (i = 0; i < len; i++) {
        in1[i] = in1[i] + in2[i];
    }
}

void
array_subtract_array(float *in1, const float *in2, size_t len) {
    size_t i;

    for (i = 0; i < len; i++) {
        in1[i] = in1[i] - in2[i];
    }
}

void
array_element_multiply_array(float *in1, const float *in2, size_t len) {
    size_t i;

    for(i = 0; i < len; i++) {
        in1[i] = in1[i] * in2[i];
    }
}

void
array_element_divide_array(float *in1, const float *in2, size_t len) {
    size_t i;

    for(i = 0; i < len; i++) {
        in1[i] = in1[i] / in2[i];
    }
}

float
array_min(const float *arr, size_t len) {
    float min;
    size_t i;

    min = arr[0];
    for (i = 1; i < len; i++) {
        if(arr[i] < min){
            min = arr[i];
        }
    }

    return min;
}

float
array_mean(float *arr, size_t len) {
    float total = array_sum(arr, len);

    return (total / len);
}

void
array_square(const float *in, float *out, size_t len) {
    size_t i;

    for (i = 0; i < len; i++) {
        out[i] = in[i] * in[i];
    }
}

float
array_mean_square(const float *arr, size_t len) {
    return array_dot_product(arr,arr, len) / len;
}

void
array_print(const char *str, float *arr, size_t len) {
    size_t i;

    printf("\n%s\n", str);
    for (i = 0; i < len; i++) {
        if (i % 16 == 0) { printf("\n"); }
        printf("%.5e ", arr[i]);
    }
    printf("\n");
}
