//
// Created by Kuan-Lin Chen on 10/19/18.
//
#include <iostream>
#include <array_file/array_file.hpp>
#include <afc/afc.hpp> /// include the module header which will be tested
#include <afc/bandlimited_filter.h> /// include the module header which will be tested
#include <afc/prefilter.h> /// include the module header which will be tested
#include <math.h>

int main(){
    /// parameters which need to be set (begin)
    int adaptation_type = 1;
    float mu = 0.005;
    float rho = 0.985;
    float delta = 1e-6;
    float c = 1e-6;
    float pw = 0;
    float p = 1.5;
    float alpha = 0;
    float beta = 150;
    size_t tap_len = 192;
    size_t frame_size = 32; // the frame size which corresponds to the golden data generation
    array_file s("../golden/afc_test_s1.dat"); // golden
    array_file e("../golden/afc_test_e1.dat"); // golden
    array_file y_hat("../golden/afc_test_y_hat1.dat"); // golden
    /// parameters which need to be set (end)

    // we need to check the array lengths between golden input x and output y
    // because we are doing frame-based processing
    if(s.get_len()!=e.get_len()){
        std::cout<<"array lengths are different. abort."<<std::endl;
        return -1;
    }

    // placeholders
    int total_frame = (int) floor((double)e.get_len()/(double)frame_size); // total number of frame buffers
    int total_sample = total_frame*(int)frame_size; // total number of samples which will be computed and compared
    auto y_hat_c = new float[total_sample]; // frame buffer for the output
    double sum_square_error = 0; // for computing the accumulated squared error
    double MSE; // mean squared error
    double MSE_dB; // mean squared error in dB scale
    float y_hat_n; // to hold the sample y[n]
    uint32_t a_i,b_i; // to view float as int

    /// module testing (begin)
    afc afc_b(bandlimited_filter,BANDLIMITED_FILTER_SIZE,prefilter,PREFILTER_SIZE,nullptr,tap_len,frame_size,adaptation_type,mu,delta,rho,alpha,beta,p,c,pw,0); // construct a filter object
    for(int k=0;k<total_frame;k++){
        afc_b.get_y_hat(y_hat_c+k*frame_size,e.get_ptr()+k*frame_size,s.get_ptr()+k*frame_size,frame_size); // AFC
    }
    /// module testing (end)

    // compute the accumulated squared error and log all registers
    for(int n=0;n<total_sample;n++){
        y_hat_n = *(y_hat.get_ptr()+n);
        a_i = *(uint32_t*) & y_hat_c[n];
        b_i = *(uint32_t*) & y_hat_n;
        sum_square_error = (y_hat_c[n]-y_hat_n)*(y_hat_c[n]-y_hat_n) + sum_square_error;
        printf("y_hat_c[%d] = %08X, y_hat[%d] = %08X, error = %d\n", n, a_i, n, b_i, a_i - b_i);
    }
    // free the memory
    delete[] y_hat_c;

    // compute the MSE as well as its dB scale
    MSE = sum_square_error / (double) (frame_size*total_frame); // divide the summation of squared errors by the total number of samples
    MSE_dB = 10 * log10(MSE); // compute the MSE in dB scale
    printf("afc_test MSE: %.10f (%.2f dB)\n",MSE,MSE_dB);
    return 0;
}
