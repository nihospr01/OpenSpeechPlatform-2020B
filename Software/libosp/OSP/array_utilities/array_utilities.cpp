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


float
array_sum(const float *arr, size_t len) {
    float total;
    size_t i, len_4;

    total = 0;
    len_4 = len >> 2u;
    for (i = 0; i < len; i+=4) {
        total = total + arr[i] + arr[i+1] + arr[i+2] + arr[i+3];
    }

    if(len & 0x3){
        for(i=len_4*4; i < len; i++){
            total += arr[i];
        }
    }

    return total;
}

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
