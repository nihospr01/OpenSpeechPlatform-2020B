//======================================================================================================================
/** @file filter.cpp
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
// Last Modified by Dhiman Sengupta on 2019-03-08.
//

#include <memory>
#include <mutex>
#include <OSP/filter/filter.hpp>

filter::filter(float *taps, size_t tap_size, circular_buffer *cir_buf, size_t max_buf_size) {
    auto data_current = new float[tap_size];
    auto data_next = new float[tap_size];
    std::atomic_exchange(&tap_current, data_current);
    std::atomic_exchange(&tap_next, data_next);


    size_ = tap_size;
    float *taps_ptr = taps;
    if (taps == nullptr) {
        taps = new float[tap_size];
        for (size_t i = 0; i < tap_size; i++) {
            taps[i] = 0;
        }
    }
    if (cir_buf != nullptr) {
        cir_buf_ = cir_buf;
        cir_buf_created_ = false;
    } else {
        cir_buf_ = new circular_buffer(tap_size + max_buf_size, 0);
        cir_buf_created_ = true;
    }
    set_taps(taps, size_);
    if (taps_ptr == nullptr) {
        delete[] taps;
    }
}

filter::~filter() {
    if (cir_buf_created_) {
        delete cir_buf_;
    }
    delete[] tap_current;
    delete[] tap_next;
}

int
filter::set_taps(const float *taps, size_t buf_size) {

    auto tap_ = tap_next.load();

    if (buf_size != this->size_) {
        return -1;
    }
    for (size_t i = 0; i < buf_size; i++) {
        tap_[i] = taps[i];
    }

    tap_next.exchange(tap_current.exchange(tap_next));
    return 0;
}

int
filter::get_taps(float *taps, size_t buf_size) {
    auto tap_ = tap_current.load();
    if (buf_size != this->size_) {
        return -1;
    }
    for (size_t i = 0; i < buf_size; i++) {
        taps[i] = tap_[i];
    }
    return 0;
}

void
filter::cirfir(float *data_in, float *data_out, size_t num_samp) {
    cir_buf_->set(data_in, num_samp);
    this->cirfir(data_out, num_samp);
}

size_t
filter::get_size() {
    return size_;
}

void
filter::cirfir(float *data_out, size_t num_samp) {
    auto tap_ = tap_current.load();
    float temp_data_out;
    auto mask = (int) cir_buf_->mask_;
    auto head = (int) cir_buf_->head_;
    auto size = (int) size_;
    float *data = cir_buf_->buf_;
    size_t index = (head + mask - size - num_samp + 2) & mask;
    for (size_t i = 0; i < num_samp; i++) {
        temp_data_out = 0;
        for (int j = 0; j < size; j++) {
            temp_data_out += data[(i + j + index) & mask] * tap_[size - j - 1];
        }
        data_out[i] = temp_data_out;
    }
}
