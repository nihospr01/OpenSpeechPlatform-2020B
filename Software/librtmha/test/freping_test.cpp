//
// Created by Kuan-Lin Chen on 10/19/18.
//
#include <iostream>
#include <cmath>
#include <OSP/array_file/array_file.hpp>
#include <OSP/freping/freping.hpp> /// include the module header which will be tested
#include <OSP/freping/hamming_window128.h>
#include <OSP/freping/hamming_window64.h>


int main(){
    /// parameters which need to be set (begin)
    int freping_on_off = 1;
    int allpass_chain_len = 64;
    int frame_size = 32; // the frame size which corresponds to the golden data generation
    float alpha = -0.1;
    array_file x("freping_test_chainlen=64_alpha=-0.1_x.dat"); // golden
    array_file y("freping_test_chainlen=64_alpha=-0.1_y.dat"); // golden
    /// parameters which need to be set (end)

    // we need to check the array lengths between golden input x and output y
    // because we are doing frame-based processing
    if(x.get_len()!=y.get_len()){
        std::cout<<"array lengths are different. abort."<<std::endl;
        return -1;
    }

    // placeholders
    int total_frame = (int) floor((double)x.get_len()/(double)frame_size); // total number of frame buffers
    int total_sample = total_frame*frame_size; // total number of samples which will be computed and compared
    auto y_hat = new float[total_sample]; // frame buffer for the output
    double sum_square_error = 0; // for computing the accumulated squared error
    double MSE; // mean squared error
    double MSE_dB; // mean squared error in dB scale
    float y_n; // to hold the sample y[n]
    uint32_t a_i,b_i; // to view float as int

    /// module testing (begin)
    freping fp(allpass_chain_len,frame_size,alpha,hamming_window64,freping_on_off); // construct a filter object
    for(int k=0;k<total_frame;k++){
        fp.freping_proc(x.get_ptr()+k*frame_size,y_hat+k*frame_size); // beamforming
    }
    /// module testing (end)

    // compute the accumulated squared error and log all registers
    for(int n=0;n<total_sample;n++){
        y_n = *(y.get_ptr()+n);
        a_i = *(uint32_t*) & y_hat[n];
        b_i = *(uint32_t*) & y_n;
        sum_square_error = (y_hat[n]-y_n)*(y_hat[n]-y_n) + sum_square_error;
        printf("y_c[%d] = %08X, y[%d] = %08X, error = %d\n", n, a_i, n, b_i, a_i - b_i);
    }
    // free the memory
    delete[] y_hat;

    // compute the MSE as well as its dB scale
    MSE = sum_square_error / (double) (frame_size*total_frame); // divide the summation of squared errors by the total number of samples
    MSE_dB = 10 * log10(MSE); // compute the MSE in dB scale
    printf("freping_test MSE: %.10f (%.2f dB)\n",MSE,MSE_dB);
    return 0;
}
