
//======================================================================================================================
/** @file freping.cpp
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
// Last Modified by Kuan-Lin Chen on 2020-01-17.
//
#include <OSP/freping/freping.hpp>

freping::freping(int allpass_chain_len, int frame_size, float alpha, float* window, int freping_on_off) {
    std::shared_ptr<freping_param_t> data_current = std::make_shared<freping_param_t> ();
    allpass_chain_len_ = allpass_chain_len;
    frame_size_ = frame_size;
    window_ = new float[allpass_chain_len_];
    memcpy(window_,window,allpass_chain_len_*sizeof(float));
    buf_len_ = allpass_chain_len_/2; /// As long as this is true we don't need circular buf function
    tmp_iir_in_ = new float[allpass_chain_len_];
    tmp_iir_out_ = new float[allpass_chain_len_];
    ola_constant_ = (float)0.5/array_mean(window_,allpass_chain_len_);
    ola_buf_ = new float[buf_len_];
    memset(ola_buf_,0,buf_len_*sizeof(float));
    k_ = buf_len_/frame_size_;
    overlapped_frame_ = new float[allpass_chain_len_];
    windowed_overlapped_frame_ = new float[allpass_chain_len_];
    g_ = new float[allpass_chain_len_];
    x_buf_ = new float[buf_len_];
    y_buf_ = new float[buf_len_];
    old_buf_ = new float[buf_len_];
    memset(y_buf_,0,buf_len_*sizeof(float));
    data_current->alpha_ = alpha;
    data_current->coeff_ = (float)1.0 - data_current->alpha_*data_current->alpha_;
    data_current->freping_on_off_ = freping_on_off;
    releasePool.add(data_current);
    std::atomic_store (&currentParam, data_current);
    m_ = 0;
}

freping::~freping() {
    delete[] window_;
    delete[] tmp_iir_in_;
    delete[] tmp_iir_out_;
    delete[] ola_buf_;
    delete[] overlapped_frame_;
    delete[] windowed_overlapped_frame_;
    delete[] g_;
    delete[] x_buf_;
    delete[] y_buf_;
    delete[] old_buf_;
    releasePool.release();
}


void freping::windowing(const float *in, float *out) {
    for(int i=0;i<allpass_chain_len_;i++) {
        out[i] = in[i]*window_[i];
    }
}

void freping::allpass_chain(float *in, float *out, float alpha_, float coeff_) {
    int len = allpass_chain_len_ - 1;

    if(alpha_==0) {
        memcpy(out,in,allpass_chain_len_* sizeof(float));
        return;
    }
    /// the fist stage of the all-pass chain
    tmp_iir_out_[0] = in[len];
    for(int i=1;i<allpass_chain_len_;i++) {
        tmp_iir_out_[i] = in[len-i] + alpha_*tmp_iir_out_[i-1];
    }
    out[0] = tmp_iir_out_[len];
    /// the second stage of the all-pass chain

    /// Pointer swaping cost less than copying memory
    temp_ = tmp_iir_in_;
    tmp_iir_in_ = tmp_iir_out_;
    tmp_iir_out_ = temp_;
    /********************************/

    tmp_iir_out_[0] = 0;
    for(int i=1;i<allpass_chain_len_;i++) {
        tmp_iir_out_[i] = coeff_*tmp_iir_in_[i-1] + alpha_*tmp_iir_out_[i-1];
    }
    out[1] = tmp_iir_out_[len];
    /// the remaining stages of the all-pass chain
    for(int j=2;j<allpass_chain_len_;j++) {
        /// Pointer swaping cost less than copying memory
        temp_ = tmp_iir_in_;
        tmp_iir_in_ = tmp_iir_out_;
        tmp_iir_out_ = temp_;
        /********************************/
        tmp_iir_out_[0] = -alpha_*tmp_iir_in_[0];
        for(int i=1;i<allpass_chain_len_;i++) {
            tmp_iir_out_[i] = -alpha_*tmp_iir_in_[i] + tmp_iir_in_[i-1] + alpha_*tmp_iir_out_[i-1];
        }
        out[j] = tmp_iir_out_[len];
    }
}

void freping::overlap_add(const float *in, float *out) {
    /**************** Don't Need *****************************
    memcpy(ola_buf_,&ola_buf_[buf_len_],buf_len_*sizeof(float));
    memset(&ola_buf_[buf_len_],0,buf_len_*sizeof(float));
     *******************************************************/
    /// This should do the same thing
    for(int i=0;i<allpass_chain_len_ >> 1;i++) {
        out[i] = ola_buf_[i] + in[i]*ola_constant_;
    }
    for(int i=0;i<allpass_chain_len_ >> 1;i++) {
        ola_buf_[i] = in[(i + (allpass_chain_len_ >> 1))]*ola_constant_;
    }
}

void freping::get_params(float &alpha, int &freping_on_off) {
    std::shared_ptr<freping_param_t> data_current = std::atomic_load(&currentParam);
    alpha = data_current->alpha_;
    freping_on_off = data_current->freping_on_off_;
}

void freping::set_params(float alpha, int freping_on_off) {
    std::shared_ptr<freping_param_t> data_next = std::make_shared<freping_param_t> ();
    data_next->alpha_ = alpha;
    data_next->coeff_ = (float)1.0 - data_next->alpha_*data_next->alpha_;

    /********************** This needs to be done atomically will figure it out later*****/
    // TODO: Make Atomic
    if(freping_on_off == 0 && data_next->freping_on_off_ == 1) {
        memset(ola_buf_,0,allpass_chain_len_* sizeof(float));
        memset(y_buf_,0,buf_len_*sizeof(float));
    }
    /********************************************************************************/
    data_next->freping_on_off_ = freping_on_off;
    releasePool.add(data_next);
    std::atomic_store (&currentParam, data_next);
    releasePool.release();
}

void freping::freping_proc(float *in, float *out) {
    std::shared_ptr<freping_param_t> data_current = std::atomic_load(&currentParam);
    if(!data_current->freping_on_off_) {
        memcpy(out,in,frame_size_*sizeof(float));
        return;
    }
    ///in_buf_->set(in,frame_size_); // Do we really need if frame_size is already a multiple of all_pass_len
    ///m_ = ++run_%k_; /// Why is this useful?
    memcpy(&x_buf_[m_*frame_size_], in, frame_size_*sizeof(float));
    m_++;
    if(m_== k_) {
        ///in_buf_->get(x_buf_,buf_len_);
        /// this->overlap_framing(x_buf_,overlapped_frame_);
        memcpy(overlapped_frame_, old_buf_, buf_len_* sizeof(float) );
        memcpy(&overlapped_frame_[buf_len_], x_buf_, buf_len_* sizeof(float));

        /// Pointer swaping cost less than copying memory
        temp_ = old_buf_;
        old_buf_ = x_buf_;
        x_buf_ = temp_;
        /********************************/

        this->windowing(overlapped_frame_,windowed_overlapped_frame_);
        this->allpass_chain(windowed_overlapped_frame_,g_, data_current->alpha_, data_current->coeff_);
        this->overlap_add(g_,y_buf_);
        m_ = 0;
    }
    memcpy(out,&y_buf_[frame_size_*m_],frame_size_*sizeof(float));
}