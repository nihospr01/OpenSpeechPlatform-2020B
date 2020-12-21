//
// Created by nal on 5/3/20.
//

#ifndef OPENSPEECHPLATFORMLIBRARIES_HB_DOWNSAMPLER_H
#define OPENSPEECHPLATFORMLIBRARIES_HB_DOWNSAMPLER_H


#include <cstddef>
#include <OSP/filter/fir_formii.h>
#include <OSP/circular_buffer/circular_buffer.hpp>

class polyphase_hb_downsampler {

public:
    polyphase_hb_downsampler(float *filter_taps, size_t num_taps, size_t max_frame_size);
    ~polyphase_hb_downsampler();
    void process(float *in, float *out, size_t frame_size);

private:
    fir_formii *even_filter;
    circular_buffer *odd_filter;
    float *even_data;
    float *even_filter_taps;
    float *odd_data;
    float middle_tap;
    size_t  delay;
    size_t half_frame_size;


};


#endif //OPENSPEECHPLATFORMLIBRARIES_HB_DOWNSAMPLER_H
