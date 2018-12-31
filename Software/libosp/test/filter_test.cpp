//
// Created by Kuan-Lin Chen on 10/19/18.
//
#include <iostream>
#include <array_file/array_file.hpp>
#include <filter/filter.hpp> /// include the module header which will be tested

int main(){
    /// parameters which need to be set (begin)
    size_t frame_size = 100; // the frame size which corresponds to the golden data generation
    array_file h("../golden/filter_test_h.dat"); // golden
    array_file x("../golden/filter_test_x.dat"); // golden
    array_file y("../golden/filter_test_y.dat"); // golden
    /// parameters which need to be set (end)

    // we need to check the array lengths between golden input x and output y
    // because we are doing frame-based processing
    if(x.get_len()!=y.get_len()){
        std::cout<<"array lengths are different. abort."<<std::endl;
        return -1;
    }

    // placeholders
    int total_frame = (int) floor((double)x.get_len()/(double)frame_size); // total number of frame buffers
    int total_sample = total_frame*(int)frame_size; // total number of samples which will be computed and compared
    auto y_c = new float[frame_size*total_frame]; // frame buffer for the output
    double sum_square_error = 0; // for computing the accumulated squared error
    double MSE; // mean squared error
    double MSE_dB; // mean squared error in dB scale
    float y_n; // to hold the sample y[n]
    uint32_t a_i,b_i; // to view float as int

    /// module testing (begin)
    filter fir(h.get_ptr(), h.get_len(), nullptr, frame_size); // construct a filter object
    for(int k=0;k<total_frame;k++){
        fir.cirfir(x.get_ptr()+k*frame_size,y_c+k*frame_size,frame_size); // filtering
    }
    /// module testing (end)

    // compute the accumulated squared error and log all registers
    for(int n=0;n<total_sample;n++){
        y_n = *(y.get_ptr()+n);
        a_i = *(uint32_t*) & y_c[n];
        b_i = *(uint32_t*) & y_n;
        sum_square_error = (y_c[n]-y_n)*(y_c[n]-y_n) + sum_square_error;
        printf("y_c[%d] = %08X, y[%d] = %08X, error = %d\n", n, a_i, n, b_i, a_i - b_i);
    }
    // free the memory
    delete[] y_c;

    // compute the MSE as well as its dB scale
    MSE = sum_square_error / (double) (frame_size*total_frame); // divide the summation of squared errors by the total number of samples
    MSE_dB = 10 * log10(MSE); // compute the MSE in dB scale
    printf("filter_test MSE: %.10f (%.2f dB)\n",MSE,MSE_dB);
    return 0;
}
