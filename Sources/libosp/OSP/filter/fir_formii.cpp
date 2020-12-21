//======================================================================================================================
/** @file fir_formii.cpp
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
// Last Modified by Dhiman Sengupta on 2020-05-05.
//

#include "fir_formii.h"

fir_formii::fir_formii(float *taps, size_t tap_size) {

    std::shared_ptr<fir_formii_t> data_current = std::make_shared<fir_formii_t> ();
    data_current->taps = new float[tap_size];
    data_current->delays1 = new float[tap_size];
    data_current->delays2 = new float[tap_size];
    data_current->tap_length = tap_size;

    memcpy(data_current->taps, taps, tap_size* sizeof(float));
    memset(data_current->delays1, 0, tap_size* sizeof(float));
    memset(data_current->delays2, 0, tap_size* sizeof(float));
    releasePool.add (data_current);
    std::atomic_store (&currentParam, data_current);

}

fir_formii::~fir_formii() {
    releasePool.release();
}

void fir_formii::set_taps(const float *taps, size_t tap_size) {
    std::shared_ptr<fir_formii_t> data_next = std::make_shared<fir_formii_t> ();
    data_next->taps = new float[tap_size];
    data_next->delays1 = new float[tap_size];
    data_next->delays2 = new float[tap_size];
    data_next->tap_length = tap_size;

    memcpy(data_next->taps, taps, tap_size* sizeof(float));
    memset(data_next->delays1, 0, tap_size* sizeof(float));
    memset(data_next->delays2, 0, tap_size* sizeof(float));

    releasePool.add (data_next);
    std::atomic_store (&currentParam, data_next);
    releasePool.release();
}

void fir_formii::get_taps(float *taps, size_t &tap_size) {
    std::shared_ptr<fir_formii_t> data_current = std::atomic_load(&currentParam);

    memcpy(taps, data_current->taps, data_current->tap_length * sizeof(float));
    tap_size = data_current->tap_length;

}

void fir_formii::process(const float *data_in, float *data_out, size_t num_samp) {

    std::shared_ptr<fir_formii_t> data_current = std::atomic_load(&currentParam);

    float* current_delay_block = data_current->delays1;
    float* next_delay_block = data_current->delays2;
    float* temp;

    float* taps = data_current->taps;
    size_t tap_length = data_current->tap_length;

    for(size_t i = 0; i < num_samp; i++){
        for(size_t j = 0; j < tap_length - 1; j++){
            next_delay_block[j] = current_delay_block[j+1] + taps[j] * data_in[i];
        }
        next_delay_block[tap_length - 1] = taps[tap_length - 1] * data_in[i];
        data_out[i] = next_delay_block[0];
        temp = current_delay_block;
        current_delay_block = next_delay_block;
        next_delay_block = temp;
    }

}

size_t fir_formii::get_size() {
    std::shared_ptr<fir_formii_t> data_current = std::atomic_load(&currentParam);
    return data_current->tap_length;
}


