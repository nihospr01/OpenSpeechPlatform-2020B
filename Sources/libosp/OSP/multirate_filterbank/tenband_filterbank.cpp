//======================================================================================================================
/** @file sokolovaharris_filterbank.cpp
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
// Last Modified by Dhiman Sengupta on 2020-05-09.
//

#include <cassert>
#include "tenband_filterbank.h"
#include "sokolovaharris_filtercoef.h"

tenband_filterbank::tenband_filterbank(size_t max_frame_size, bool aligned) {
    // Init filters
    aligned_ = aligned;
    for(int i = 0; i < NUM_BANDS; i++){
        filters[i] = new fir_formii(harris_filter_bank[i], NUM_TAPS);
    }

    //Init Downsamplers

    for(int i = 0; i < 3; i++){
        downsample[i] = new polyphase_hb_downsampler(half_band_filter1
                , half_band_filter1_length
                , max_frame_size >> i);
    }

    in_16 = new float[max_frame_size >> 1];
    in_8 = new float[max_frame_size >> 2];
    in_4 = new float[max_frame_size >> 3];

    //Init Stage1 Upsamplers

    for(int i = 0 ; i < 4; i++){
        stage1_upsample[i] = new polyphase_hb_upsampler(half_band_filter1
                , half_band_filter1_length
                , max_frame_size >> 3);
    }

    for(int i = 4 ; i < 6; i++){
        stage1_upsample[i] = new polyphase_hb_upsampler(half_band_filter1
                , half_band_filter1_length
                , max_frame_size >> 2);
    }

    for(int i = 6 ; i < 8; i++){
        stage1_upsample[i] = new polyphase_hb_upsampler(half_band_filter1
                , half_band_filter1_length
                , max_frame_size >> 1);
    }

    for(int i = 0; i < 8; i ++){
        out_16[i] = new float[max_frame_size >> 1];
    }
    //Init Stage2 Upsamplers

    for(int i = 0; i < 4; i++){
        stage2_upsample[i] = new polyphase_hb_upsampler(half_band_filter2
                , half_band_filter2_length
                , max_frame_size >> 2);
    }
    for(int i = 4; i < 6; i++){
        stage2_upsample[i] = new polyphase_hb_upsampler(half_band_filter2
                , half_band_filter2_length
                , max_frame_size >> 1);
    }

    for(int i = 0; i < 6; i ++){
        out_8[i] = new float[max_frame_size >> 2];
    }

    //Init Stage3 Upsamplers

    for(int i = 0; i < 4; i ++) {
        stage3_upsample[i] = new polyphase_hb_upsampler(half_band_filter3, half_band_filter3_length, max_frame_size >> 1);
    }

    for(int i = 0; i < 4; i ++){
        out_4[i] = new float[max_frame_size >> 3];
    }

    //Init Delay Blocks

    for(int i = 0; i < 10; i++){
        delay_blocks[i] = new circular_buffer(max_frame_size + harris_align[i], 0);
    }


}

tenband_filterbank::~tenband_filterbank() {
    for (auto &filter : filters) {
        delete filter;
    }
    //Init Downsamplers

    for (auto &i : downsample) {
        delete i;
    }

    delete[] in_16;
    delete[] in_8;
    delete[] in_4;

    //Init Stage1 Upsamplers

    for(int i = 0; i < 8; i ++){
        delete stage1_upsample[i];
        delete[] out_16[i];
    }
    //Init Stage2 Upsamplers

    for(int i = 0; i < 6; i ++){
        delete stage2_upsample[i];
        delete[] out_8[i];
    }

    //Init Stage3 Upsamplers

    for(int i = 0; i < 4; i ++){
        delete stage3_upsample[i];
        delete[] out_4[i];
    }

    //Init Delay Blocks

    for (auto &delay_block : delay_blocks) {
        delete delay_block;
    }
}

void tenband_filterbank::process(float *in, float **out, size_t frame_size) {
    bool aligned = aligned_;
    float *in_32 = in;
    //Downsampling 32->16->8->4
    downsample[0]->process(in_32, in_16, frame_size);
    downsample[1]->process(in_16, in_8, frame_size >> 1);
    downsample[2]->process(in_8, in_4, frame_size >> 2);
    //Filtering the 4kHz domain & Upsampling from 4kHz->8kHz->16kHz->32kHz & Delay for alignment
    for (int i = 0; i < 4; i++) {
        filters[i]->process(in_4, out_4[i], frame_size >> 3);
        stage1_upsample[i]->process(out_4[i], out_8[i], frame_size >> 3);
        stage2_upsample[i]->process(out_8[i], out_16[i], frame_size >> 2);
        stage3_upsample[i]->process(out_16[i], out[i], frame_size >> 1);
        if(aligned) {
            delay_blocks[i]->set(out[i], frame_size);
            delay_blocks[i]->delay_block(out[i], frame_size, harris_align[i]);
        }
    }
    //Filtering the 8kHz domain & Upsampling from 8kHz->16kHz->32kHz & Delay for alignment
    for (int i = 4; i < 6; i++) {
        filters[i]->process(in_8, out_8[i], frame_size >> 2);
        stage1_upsample[i]->process(out_8[i], out_16[i], frame_size >> 2);
        stage2_upsample[i]->process(out_16[i], out[i], frame_size >> 1);
        if(aligned) {
            delay_blocks[i]->set(out[i], frame_size);
            delay_blocks[i]->delay_block(out[i], frame_size, harris_align[i]);
        }
    }

    //Filtering the 16kHz domain & Upsampling from 16kHz->32kHz & Delay for alignment
    for (int i = 6; i < 8; i++) {
        filters[i]->process(in_16, out_16[i], frame_size >> 1);
        stage1_upsample[i]->process(out_16[i], out[i], frame_size >> 1);
        if(aligned) {
            delay_blocks[i]->set(out[i], frame_size);
            delay_blocks[i]->delay_block(out[i], frame_size, harris_align[i]);
        }
    }
    //Filtering the 32kHz domain & Delay for alignment
    for (int i = 8; i < 10; i++) {
        filters[i]->process(in_32, out[i], frame_size);
        if(aligned) {
            delay_blocks[i]->set(out[i], frame_size);
            delay_blocks[i]->delay_block(out[i], frame_size, harris_align[i]);
        }
    }
}
void tenband_filterbank::set(bool aligned){
    aligned_ = aligned;
}
void tenband_filterbank::get(bool &aligned) {
    aligned = aligned_;
}