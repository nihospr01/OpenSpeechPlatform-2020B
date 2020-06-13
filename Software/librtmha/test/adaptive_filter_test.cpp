//
// Created by Kuan-Lin Chen on 10/19/18.
//
#include <iostream>
#include <array_file/array_file.hpp>
#include <adaptive_filter/adaptive_filter.hpp> /// include the module header which will be tested

int main(){
    /// parameters which need to be set (begin)
    int adaptation_type = 1;
    float mu = 0.005;
    float rho = 0.985;
    float delta = 1e-6;
    float pw = 0;
    float p = 1.5;
    float alpha = 0;
    float beta = 150;
    size_t frame_size = 20; // the frame size which corresponds to the golden data generation
    array_file u("../golden/adaptive_filter_test_u1.dat"); // golden
    array_file e("../golden/adaptive_filter_test_e1.dat"); // golden
    array_file h("../golden/adaptive_filter_test_h1.dat"); // golden
    /// parameters which need to be set (end)

    // we need to check the array lengths between golden input u and e
    // because we are doing frame-based processing
    if(u.get_len()!=e.get_len()){
        std::cout<<"array lengths are different. abort."<<std::endl;
        return -1;
    }

    // placeholders
    int total_frame = (int) floor((double)e.get_len()/(double)frame_size); // total number of frame buffers
    auto h_c = new float[h.get_len()]; // frame buffer for the output
    double sum_square_error = 0; // for computing the accumulated squared error
    double MSE; // mean squared error
    double MSE_dB; // mean squared error in dB scale
    float h_n; // to hold the sample h[n]
    uint32_t a_i,b_i; // to view float as int

    /// module testing (begin)
    adaptive_filter af(nullptr,h.get_len(),frame_size,adaptation_type,
                       mu,delta,rho,alpha,beta,p,delta,pw);
    for(int k=0;k<total_frame;k++){
        af.update_taps(u.get_ptr()+k*frame_size,e.get_ptr()+k*frame_size,frame_size);
    }
    /// module testing (end)

    // compute the accumulated squared error and log all registers
    af.get_taps(h_c,h.get_len());
    for(int n=0;n<h.get_len();n++){
        h_n = *(h.get_ptr()+n);
        a_i = *(uint32_t*) & h_c[n];
        b_i = *(uint32_t*) & h_n;
        sum_square_error = (h_c[n]-h_n)*(h_c[n]-h_n) + sum_square_error;
        printf("h_c[%d] = %08X, h[%d] = %08X, error = %d\n", n, a_i, n, b_i, a_i - b_i);
    }
    // free the memory
    delete[] h_c;

    // compute the MSE as well as its dB scale
    MSE = sum_square_error / (double) (h.get_len()); // divide the summation of squared errors by the total number of taps
    MSE_dB = 10 * log10(MSE); // compute the MSE in dB scale
    printf("adaptive_filter_test MSE: %.10f (%.2f dB)\n",MSE,MSE_dB);
    return 0;
}