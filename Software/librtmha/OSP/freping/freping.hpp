
//======================================================================================================================
/** @file freping.hpp
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
// Last Modified by Kuan-Lin Chen on 2020-01-16.
//

#ifndef OSP_FREPING_H
#define OSP_FREPING_H

#include <cstring>
#include <OSP/circular_buffer/circular_buffer.hpp>
#include <OSP/array_utilities/array_utilities.hpp>
#include <atomic>
#include <OSP/GarbageCollector/GarbageCollector.hpp>

/**
 * @brief Freping Class
 * @details This freping class provides frequency warping feature in real-time. Please refer to the following paper for details.
 * Ching-Hua Lee, Kuan-Lin Chen, fred harris, Bhaskar D. Rao, and Harinath Garudadri,
 * "On mitigating acoustic feedback in hearing aids with frequency warping by all-pass networks,"
 * in Annual Conference of the International Speech Communication Association (Interspeech), 2019.
 */
class freping {

public:
    /**
     * @brief Freping constructor
     * @param[in] allpass_chain_len The length of the all-pass chain
     * @param[in] frame_size The frame size of the caller (the real-time system)
     * @param[in] alpha The parameter to control the degree of frequency warping (1.0>=alpha>=-1.0)
     */
    explicit freping(int allpass_chain_len,int frame_size, float alpha, float* window, int freping_on_off);

    /**
     * @brief Freping destructor
     */
    ~freping();

    /**
     * @brief Get the parameter alpha
     * @param[out] alpha The parameter to control the degree of frequency warping (1.0>=alpha>=-1.0)
     */
    void get_params(float &alpha, int &freping_on_off);

    /**
     * @brief Set the parameter alpha
     * @param[in] alpha The parameter to control the degree of frequency warping (1.0>=alpha>=-1.0)
     */
    void set_params(float alpha, int freping_on_off);

    /**
     * @breif Get the output signal of freping, the output signal is warped according to the alpha parameter
     * @param[in] in The input frame
     * @param[out] out The output frame
     */
    void freping_proc(float* in, float* out);

protected:
    /**
     * @breif Overlap-add method
     * @param[in] in The overlapped frame which has the twice size of the output frame (e.g., 128 samples)
     * @param[out] out The recovered frame (e.g., 64 samples)
     */
    void overlap_add(const float* in, float* out);

   /**
    * @brief The all-pass chain which has the length of allpass_chain_len
    * @param[in] in The input frame which has the same size of the output frame (e.g., 128 samples)
    * @param[out] out The output frame (e.g., 128 samples)
    * @param[in] alpha_ The parameter to control the degree of frequency warping (1.0>=alpha>=-1.0)
    * @param[in] coeff_ a coefficient for the second IIR filter in the all-pass chain (a function of alpha_)
    */
    void allpass_chain(float* in, float* out, float alpha_, float coeff_);


    /**
     * @breif Applying a window function such as Hamming window to the input frame
     * @param[in] in The input frame which has the same size of the output frame (e.g., 128 samples)
     * @param[out] out The output frame (e.g., 128 samples)
     */
    void windowing(const float* in, float* out);

private:
    int allpass_chain_len_; // the length of the all-pass chain, i.e., the number of IIR filters in the all-pass chain, it should satisfy allpass_chain_len_%frame_size_=0 and allpass_chain_len_>=2*frame_size_
    int frame_size_; // the frame size used by the caller of freping
    int buf_len_; // this should be equal to allpass_chain_len_/2
    float* tmp_iir_in_; // a temporary placeholder to hold the input signal of each IIR filter in the all-pass chain
    float* tmp_iir_out_; // a temporary placeholder to hold the output signal of each IIR filter in the all-pass chain
    float ola_constant_; // the constant needed to be multiplied with the input frame for overlap-add
    float* ola_buf_; // a buffer to hold the previous frame data in overlap-add
    int k_; // the multiple, i.e., k=buf_len_/frame_size_
    int m_; // a temporary placeholder to hold the modulus of run_%k_
    float* overlapped_frame_; // a temporary placeholder to hold the output frame from overlap_framing
    float* windowed_overlapped_frame_; // a temporary placeholder to hold the output frame from windowing
    float* g_; // a temporary placeholder to hold the output frame from allpass_chain
    float* x_buf_; // a placeholder to hold the output data from in_buf_
    float* y_buf_; // a placeholder to hold the output frame from overlap_add
    float* window_; // the windowing function (e.g., Hamming, Kaiser-Bessel, etc) with length allpass_chain_len_
    float * temp_;
    float *old_buf_;
    struct freping_param_t {
        float alpha_; // the parameter to control frequency warping
        float coeff_; // a coefficient for the second IIR filter in the all-pass chain (a function of alpha_)
        int freping_on_off_; // 0: OFF, 1: ON
    };
    std::shared_ptr<freping_param_t> currentParam;
    GarbageCollector releasePool;
};

#endif //OSP_FREPING_H