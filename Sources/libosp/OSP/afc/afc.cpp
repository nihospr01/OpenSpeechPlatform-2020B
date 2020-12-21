//======================================================================================================================
/** @file afc.cpp
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
// Last Modified by Dhiman Sengupta on 2020‑05‑04
//

#include <OSP/afc/afc.hpp>

afc::afc(float *bandlimited_filter_taps, size_t bandlimited_filter_tap_len, float *prefilter_taps,
                  size_t prefilter_tap_len, float *adaptive_filter_taps, size_t adaptive_filter_tap_len,
                  size_t max_frame_size,
                  int adaptation_type, float mu, float delta, float rho, float alpha, float beta, float p, float c,
                  float power_estimate, size_t delay_len, int afc_on_off)
        : adaptive_filter(adaptive_filter_taps, adaptive_filter_tap_len, max_frame_size,
                          adaptation_type, mu, delta, rho, alpha,
                          beta, p, c, power_estimate) {
    std::shared_ptr<afc_param_t> data_current = std::make_shared<afc_param_t> ();
    bandlimited_filter_ = new filter(bandlimited_filter_taps, bandlimited_filter_tap_len, nullptr, max_frame_size);
    prefilter_e_ = new filter(prefilter_taps, prefilter_tap_len, nullptr, max_frame_size);
    prefilter_u_ = new filter(prefilter_taps, prefilter_tap_len, nullptr, max_frame_size);
    u_ref = new float[max_frame_size];
    e_ref = new float[max_frame_size];
    data_current->delay_len_ = delay_len;
    delay_buffer_ = new circular_buffer(MAX_DELAY_LEN + max_frame_size, 0.0f);
    u_ = new float[max_frame_size];
    data_current->afc_on_off_ = afc_on_off;
    releasePool.add (data_current);
    std::atomic_store (&currentParam, data_current);
}

afc::~afc() {
    delete delay_buffer_;
    delete bandlimited_filter_;
    delete prefilter_e_;
    delete prefilter_u_;
    delete[] u_;
    delete[] u_ref;
    delete[] e_ref;
    releasePool.release();
}

int
afc::get_y_hat(float *y_hat, float *e, float *s, size_t ref_size) {
    std::shared_ptr<afc_param_t> data_current = std::atomic_load(&currentParam);
    if (!data_current->afc_on_off_) {
        for (size_t i = 0; i < ref_size; i++) {
            y_hat[i] = 0.0f;
        }
        return 0;
    }
    if (ref_size > this->get_max_frame_size()) {
        return -1;
    }
    // filtering
    prefilter_u_->cirfir(u_, u_ref, ref_size); // compute u_ref, we need this filtering happen one step forward
    // delay_block line
    delay_buffer_->set(s, ref_size); // put a new frame to buffer
    size_t delay_head_backup = delay_buffer_->head_; // backup the position of current head
    delay_buffer_->head_ = (delay_buffer_->head_ - data_current->delay_len_) & delay_buffer_->mask_; // shift the position of head by delay_len_
    delay_buffer_->get(u_, ref_size); // to update the current u(n)
    delay_buffer_->head_ = delay_head_backup; // restore the head
    // filtering
    prefilter_e_->cirfir(e, e_ref, ref_size);
    bandlimited_filter_->cirfir(s, u_, ref_size);
    int check = this->update_taps(u_ref, e_ref, ref_size);
    this->cirfir(u_, y_hat, ref_size);
    return check;
}

void
afc::get_delay(size_t &delay_len) {
    std::shared_ptr<afc_param_t> data_current = std::atomic_load(&currentParam);
    delay_len = data_current->delay_len_;
}


int
afc::set_delay(size_t delay_len) {
    std::shared_ptr<afc_param_t> data_current = std::atomic_load(&currentParam);
    std::shared_ptr<afc_param_t> data_next = std::make_shared<afc_param_t> ();
    if (delay_len <= MAX_DELAY_LEN) {
        data_next->delay_len_ = delay_len;
        data_next->afc_on_off_ = data_current->afc_on_off_;
    } else {
        return -1;
    }
    releasePool.add (data_next);
    std::atomic_store (&currentParam, data_next);
    releasePool.release();
    return 0;
}

void
afc::set_afc_on_off(int afc_on_off) {
    std::shared_ptr<afc_param_t> data_current = std::atomic_load(&currentParam);
    std::shared_ptr<afc_param_t> data_next = std::make_shared<afc_param_t> ();
    data_next->afc_on_off_ = afc_on_off;
    data_next->delay_len_ = data_current->delay_len_;
    releasePool.add (data_next);
    std::atomic_store (&currentParam, data_next);
    releasePool.release();
}

void
afc::get_afc_on_off(int &afc_on_off) {
    std::shared_ptr<afc_param_t> data_current = std::atomic_load(&currentParam);
    afc_on_off = data_current->afc_on_off_;
}

void
afc::reset(float* default_taps, size_t len) {
    this->set_taps(default_taps,len);
}