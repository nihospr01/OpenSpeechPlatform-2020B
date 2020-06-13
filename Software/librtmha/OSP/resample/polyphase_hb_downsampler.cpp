//
// Created by nal on 5/3/20.
//

#include <cassert>
#include "polyphase_hb_downsampler.h"
#include <OSP/array_utilities/array_utilities.hpp>
#include <iostream>

polyphase_hb_downsampler::polyphase_hb_downsampler(float *filter_taps, size_t num_taps, size_t max_frame_size) {
    assert((max_frame_size & 0x1) == 0);
    assert((num_taps & 0x1) == 1);
    even_filter_taps = new float[(num_taps + 1) >> 1];
    odd_data = new float[max_frame_size >> 1];
    even_data = new float[max_frame_size >> 1];
    middle_tap = filter_taps[(num_taps - 1) >> 1 ];
    delay = ((num_taps + 1) >> 2 );
    half_frame_size = max_frame_size >> 1;
    for(size_t i = 0; i < num_taps; i += 2){
        even_filter_taps[i >> 1] = filter_taps[i];
        std::cout << "Up Even Filter i = " << i << " = " << even_filter_taps[i >> 1] << std::endl;
    }
    std::cout << "Up Middle Tap " << middle_tap << std::endl;
    std::cout << "Delay = " << delay << std::endl;
    even_filter = new fir_formii(even_filter_taps, (num_taps + 1) >> 1);
    odd_filter = new circular_buffer(delay + half_frame_size, 0);

}
polyphase_hb_downsampler::~polyphase_hb_downsampler() {
    delete[] odd_data;
    delete odd_filter;
    delete[] even_data;
    delete even_filter;
}
void polyphase_hb_downsampler::process(float *in, float *out, size_t frame_size) {

    /* Commutator */
    size_t half_frame = frame_size >> 1;

    for(size_t i = 0 ; i < frame_size; i += 2){
        even_data[ i >> 1 ] = in[i];
        odd_data[ i >> 1 ] = in[i+1];
    }

    even_filter->process(even_data, even_data, half_frame);
    odd_filter->set(odd_data, half_frame);
    odd_filter->delay_block(out, half_frame, delay);

    array_multiply_const(out, middle_tap, half_frame);
    array_add_array(out, even_data, half_frame);
    array_multiply_const(out, 2.0, half_frame);



}