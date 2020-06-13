//======================================================================================================================
/** @file beamformer.cpp
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
// Last Modified by Louis Pisha Chen on 2019-06-14.
//

#include <OSP/beamformer/beamformer.hpp>
#include <OSP/array_utilities/array_utilities.hpp>
#include <cmath>
#include <cstring>

beamformer::beamformer(size_t delay_len, float *adaptive_filter_taps, size_t adaptive_filter_tap_len,
                       size_t max_frame_size,
                       int adaptation_type, float mu, float delta, float rho, float alpha, float beta, float p,
                       float c, float power_estimate, int bf_on_off, int bf_nc_on_off, int bf_amc_on_off, float nc_thr, float amc_thr, float amc_forgetting_factor)
                       : adaptive_filter(adaptive_filter_taps, adaptive_filter_tap_len, max_frame_size, adaptation_type, mu, delta, rho, alpha, beta, p, c, power_estimate) {
    std::shared_ptr<beamformer_param_t> data_current = std::make_shared<beamformer_param_t> ();
    delay_len_ = delay_len;
    delay_buffer_ = new circular_buffer(max_frame_size + delay_len_, 0.0f);
    pe_v_ = 0;
    pe_u_ = 0;
    u = new float[max_frame_size];
    v = new float[max_frame_size];
    d = new float[max_frame_size];
    y = new float[max_frame_size];
    e = new float[max_frame_size];
    w = new float[adaptive_filter_tap_len];
    data_current->bf_on_off_ = bf_on_off;
    data_current->bf_nc_on_off_ = bf_nc_on_off;
    data_current->bf_amc_on_off_ = bf_amc_on_off;
    data_current->nc_thr_ = nc_thr;
    data_current->amc_thr_ = amc_thr;
    data_current->amc_forgetting_factor_ = amc_forgetting_factor;
    releasePool.add (data_current);
    std::atomic_store (&currentParam, data_current);
}

beamformer::~beamformer() {
    delete[] u;
    delete[] v;
    delete[] d;
    delete[] y;
    delete[] e;
    delete[] w;
    delete delay_buffer_;
    releasePool.release();
}

void
beamformer::get_e(float *e_l, float *e_r, const float *x_l, const float *x_r, size_t ref_size) {
    std::shared_ptr<beamformer_param_t> data_current = std::atomic_load(&currentParam);
    if (!data_current->bf_on_off_) {
        std::memcpy(e_l,x_l,ref_size*sizeof(float));
        std::memcpy(e_r,x_r,ref_size*sizeof(float));
	return;
    }
    // compute u(n) and v(n)
    for (size_t i = 0; i < ref_size; i++) {
        u[i] = (x_l[i] - x_r[i]) * 0.5f;
        v[i] = (x_l[i] + x_r[i]) * 0.5f;
    }
    // compute y(n)
    this->cirfir(u, y, ref_size);
    // compute d(n)
    delay_buffer_->set(v, ref_size); // put a new frame to buffer
    size_t delay_head_backup = delay_buffer_->head_; // backup the position of current head
    delay_buffer_->head_ =
            (delay_buffer_->head_ - delay_len_) & delay_buffer_->mask_; // shift the position of head by delay_len_
    delay_buffer_->get(d, ref_size); // get d(n)
    delay_buffer_->head_ = delay_head_backup; // restore the head
    // compute e(n)
    for (size_t i = 0; i < ref_size; i++) {
        e[i] = d[i] - y[i];
    }

    std::memcpy(e_l,e,ref_size*sizeof(float));
    std::memcpy(e_r,e,ref_size*sizeof(float));
}


int
beamformer::update_bf_taps(size_t ref_size) {
    std::shared_ptr<beamformer_param_t> data_current = std::atomic_load(&currentParam);
    if (!data_current->bf_on_off_) {
        return 0;
    }
    // adaptation flag
    int adaptation_on_off = 1;

    // compute the power estimate for the adaptation mode controller
    float amc_forgetting_factor = data_current->amc_forgetting_factor_;
    pe_u_ = amc_forgetting_factor * pe_u_ + (1 - amc_forgetting_factor) * (array_mean_square(u, ref_size));
    pe_v_ = amc_forgetting_factor * pe_v_ + (1 - amc_forgetting_factor) * (array_mean_square(v, ref_size));

    // apply the adaptation mode controller
    if(data_current->bf_amc_on_off_) {
        float ratio = pe_v_/pe_u_;
        if(ratio>data_current->amc_thr_) {
            adaptation_on_off = 0;
        }
    }

    // update the taps of the adaptive filter
    if(adaptation_on_off) {
        this->update_taps(u, e, ref_size);
        // norm-constrained adaptation for beamforming
        if(data_current->bf_nc_on_off_) {
            size_t taps_len = this->get_size();
            this->get_taps(w, taps_len);
            float omg = array_dot_product(w, w, taps_len);
            if(omg>data_current->nc_thr_){
                float scaling_factor = std::sqrt(data_current->nc_thr_/omg);
                array_multiply_const(w, scaling_factor, taps_len);
                this->set_taps(w, taps_len);
            }
        }
    }
    return adaptation_on_off;
}

void
beamformer::get_bf_params(int &bf_on_off, int &bf_nc_on_off, int &bf_amc_on_off, float &nc_thr, float &amc_thr, float &amc_forgetting_factor) {
    std::shared_ptr<beamformer_param_t> data_current = std::atomic_load(&currentParam);
    bf_on_off = data_current->bf_on_off_;
    bf_nc_on_off = data_current->bf_nc_on_off_;
    bf_amc_on_off = data_current->bf_amc_on_off_;
    nc_thr = data_current->nc_thr_;
    amc_thr = data_current->amc_thr_;
    amc_forgetting_factor = data_current->amc_forgetting_factor_;
}

void
beamformer::set_bf_params(int bf_on_off, int bf_nc_on_off, int bf_amc_on_off, float nc_thr, float amc_thr, float amc_forgetting_factor) {
    std::shared_ptr<beamformer_param_t> data_next = std::make_shared<beamformer_param_t> ();
    data_next->bf_on_off_ = bf_on_off;
    data_next->bf_nc_on_off_ = bf_nc_on_off;
    data_next->bf_amc_on_off_ = bf_amc_on_off;
    data_next->nc_thr_ = nc_thr;
    data_next->amc_thr_ = amc_thr;
    data_next->amc_forgetting_factor_ = amc_forgetting_factor;
    releasePool.add (data_next);
    std::atomic_store (&currentParam, data_next);
    releasePool.release();
}
