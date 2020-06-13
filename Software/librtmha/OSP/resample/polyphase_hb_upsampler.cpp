//
// Created by Dhiman Sengupta on 5/3/20.
//

#include "polyphase_hb_upsampler.h"
#include <cassert>
#include <OSP/array_utilities/array_utilities.hpp>
#include <iostream>

polyphase_hb_upsampler::polyphase_hb_upsampler(float *filter_taps, size_t num_taps, size_t max_frame_size) {
    assert((max_frame_size & 0x1) == 0);
    assert((num_taps & 0x1) == 1);
    even_filter_taps = new float[(num_taps + 1) >> 1];
    odd_data = new float[max_frame_size ];
    even_data = new float[max_frame_size];
    middle_tap = filter_taps[(num_taps - 1) >> 1 ];
    delay = ((num_taps + 1) >> 2) - 1;
    double_frame_size = max_frame_size << 1;
    for(size_t i = 0; i < num_taps; i += 2){
        even_filter_taps[i >> 1] = filter_taps[i];
        std::cout << "Up Even Filter i = " << i << " = " << even_filter_taps[i >> 1] << std::endl;
    }
    std::cout << "Up Middle Tap " << middle_tap << std::endl;
    even_filter = new fir_formii(even_filter_taps, (num_taps + 1) >> 1);
    odd_filter = new circular_buffer(delay + max_frame_size, 0);

}
polyphase_hb_upsampler::~polyphase_hb_upsampler() {
    delete[] odd_data;
    delete odd_filter;
    delete[] even_data;
    delete even_filter;
}
void polyphase_hb_upsampler::process(float *in, float *out, size_t frame_size) {

    /* Commutator */
    size_t double_frame = frame_size << 1;

    even_filter->process(in, even_data, frame_size);
    odd_filter->set(in, frame_size);
    odd_filter->delay_block(odd_data, frame_size, delay);

    array_multiply_const(odd_data, middle_tap, frame_size);

    for(size_t i = 0 ; i < double_frame; i += 2){
        out[i] = even_data[i >> 1];
        out[i+1] = odd_data[i >> 1];
    }



}