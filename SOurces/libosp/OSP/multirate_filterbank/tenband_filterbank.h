//======================================================================================================================
/** @file sokolovaharris_filterbank.h
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

#ifndef OPENSPEECHPLATFORMLIBRARIES_SOKOLOVAHARRIS_FILTERBANK_H
#define OPENSPEECHPLATFORMLIBRARIES_SOKOLOVAHARRIS_FILTERBANK_H


#include <cstddef>
#include <OSP/filter/fir_formii.h>
#include <OSP/resample/polyphase_hb_downsampler.h>
#include <OSP/circular_buffer/circular_buffer.hpp>
#include <OSP/resample/polyphase_hb_upsampler.h>
#include <atomic>

#define NUM_BANDS 10
#define NUM_TAPS 93
/**
 * @brief The Ten Band Filter bank is a 10 Band Multirate Filter Bank with its center frequencies located at
 *        250Hz, 500Hz, 750Hz, 1kHz, 1.5kHz, 2kHz, 3kHz, 4kHz, 6kHz, 8kHz.
 */
class tenband_filterbank {

public:
    /**
     * @brief The consturctor for the Ten Band Filter bank
     * @param max_frame_size [in] - The maximum size of buffer that would need to be processed at any time step.
     * @param aligned [in] - Enabling the delay blocks to compensate for the various group delays.
     */
    tenband_filterbank(size_t max_frame_size, bool aligned);
    /**
     * @brief The desturctor for the Ten Band Filter bank
     */
    ~tenband_filterbank();
    /**
     * @brief This function decomposes the incoming frame of data into the 10 subbands
     * @param[in] in  An array of data the size of frame_size.
     * @param[out] out A 2D 10 by frame_size array of data for the 10 subbands
     * @param[in] frame_size The amount of data contained in the incoming array.
     */
    void process(float *in, float **out, size_t frame_size);
    /**
     * @brief The compensation for the various group delay can be toggled on and off live.
     * @param[in] aligned Enabling the delay blocks to compensate for the various group delays.
     */
    void set(bool aligned);
    /**
     * @brief Used to read the current status of the aligned variable.
     * @param[out] aligned Grabbing the current status of the aligned variable.
     */
    void get(bool &aligned);

private:

    fir_formii *filters[NUM_BANDS];
    polyphase_hb_downsampler *downsample[3];
    polyphase_hb_upsampler *stage1_upsample[8];
    polyphase_hb_upsampler *stage2_upsample[6];
    polyphase_hb_upsampler *stage3_upsample[4];

    circular_buffer *delay_blocks[10];

    float *in_16;
    float *in_8;
    float *in_4;

    float *out_4[4];
    float *out_8[6];
    float *out_16[8];

    std::atomic_bool aligned_;


};




#endif //OPENSPEECHPLATFORMLIBRARIES_SOKOLOVAHARRIS_FILTERBANK_H
