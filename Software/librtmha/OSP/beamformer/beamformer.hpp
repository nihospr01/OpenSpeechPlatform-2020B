//======================================================================================================================
/** @file beamformer.hpp
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
// Last Modified by Kuan-Lin Chen on 2019-06-05.
//

#ifndef OSP_BEAMFORMER_H
#define OSP_BEAMFORMER_H

#include <cstddef>
#include <OSP/adaptive_filter/adaptive_filter.hpp>
#include <atomic>
#include <OSP/GarbageCollector/GarbageCollector.hpp>

/**
 * @brief Beamformer Class
 * @details This beamformer class implements the generalized sidelobe canceller (GSC) using SLMS [Lee et al., IHCON 2018].
 */
class beamformer : public adaptive_filter{

public:
    /**
     * @brief Beamformer constructor
     * @param[in] delay_len The length of delay line in samples for beamformer
     * @param[in] adaptive_filter_taps The initial filter taps for adaptive filter in beamformer
     * @param[in] adaptive_filter_tap_len The number of filter taps of adaptive filter in beamformer
     * @param[in] max_frame_size The maximum processing frame size in adaptive filter
     * @param[in] adaptation_type The adaptation type for adaptive filter
     * @param[in] mu A parameter for adaptive filter
     * @param[in] delta A parameter for adaptive filter
     * @param[in] rho A parameter for adaptive filter
     * @param[in] alpha A parameter for adaptive filter
     * @param[in] beta A parameter for adaptive filter
     * @param[in] p A parameter for adaptive filter
     * @param[in] c A parameter for adaptive filter
     * @param[in] power_estimate A parameter for adaptive filter
     * @param[in] bf_on_off A flag for enabling beamformer
     * @param[in] bf_nc_on_off A flag for enabling norm-constrained adaptation
     * @param[in] bf_amc_on_off A flag for enabling adaptation mode controller
     * @param[in] nc_thr A threshold for the norm-constrained adaptation
     * @param[in] amc_thr A threshold for the adaptation mode controller
     * @see adaptive_filter
     */
    explicit beamformer(size_t delay_len, float *adaptive_filter_taps, size_t adaptive_filter_tap_len, size_t max_frame_size,
                        int adaptation_type, float mu, float delta, float rho, float alpha, float beta, float p,
                        float c, float power_estimate, int bf_on_off, int bf_nc_on_off, int bf_amc_on_off, float nc_thr, float amc_thr, float amc_forgetting_factor);

    /**
     * @brief Beamformer destructor
     */
    ~beamformer();

    /**
     * @brief Getting e signal (the output signal of this beamformer)
     * @param[out] e_l The output signal of this beamformer on the left
     * @param[out] e_r The output signal of this beamformer on the right
     * @param[in] x_l The input signal from the left channel
     * @param[in] x_r the input signal from the right channel
     * @param[in] ref_size The size of each input signal (x_l and x_r have the same size)
     */
    void get_e(float *e_l, float *e_r, const float *x_l, const float *x_r, size_t ref_size);
    /**
     * @brief Update the taps of the beamformer
     * @param[in] ref_size The size of each input signal (x_l and x_r have the same size)
     * @return A flag indicating the success of adaptation in adaptive filter
     */
    int update_bf_taps(size_t ref_size);

    /**
     * @brief Getting all parameters from this beamformer
     * @param[out] bf_on_off A flag to turn the beamformer on (0: OFF, 1: ON)
     * @param[out] bf_nc_on_off A flag to turn the norm-constrained adaptation on (0: OFF, 1: ON)
     * @param[out] bf_amc_on_off A flag to turn the adaptation mode controller on (0: OFF, 1: ON)
     * @param[out] nc_thr A threshold for the norm-constrained adaptation
     * @param[out] amc_thr A threshold for the adaptation mode controller
     * @param[out] amc_forgetting_factor A forgetting factor to compute the signal power
     */
    void get_bf_params(int &bf_on_off, int &bf_nc_on_off, int &bf_amc_on_off, float &nc_thr, float &amc_thr, float &amc_forgetting_factor);

    /**
     * @brief Setting all parameters from this beamformer
     * @param[in] bf_on_off A flag to turn the beamformer on (0: OFF, 1: ON)
     * @param[in] bf_nc_on_off A flag to turn the norm-constrained adaptation on (0: OFF, 1: ON)
     * @param[in] bf_amc_on_off A flag to turn the adaptation mode controller on (0: OFF, 1: ON)
     * @param[in] nc_thr A threshold for the norm-constrained adaptation
     * @param[in] amc_thr A threshold for the adaptation mode controller
     * @param[in] amc_forgetting_factor A forgetting factor to compute the signal power
     */
    void set_bf_params(int bf_on_off, int bf_nc_on_off, int bf_amc_on_off, float nc_thr, float amc_thr, float amc_forgetting_factor);

private:
    circular_buffer* delay_buffer_;
    size_t delay_len_;
    float pe_v_; // power estimate for d(n)
    float pe_u_; // power estimate for u(n)
    float *u, *v, *d, *y, *e, *w;

    struct beamformer_param_t {
        int bf_on_off_;
        int bf_nc_on_off_;
        int bf_amc_on_off_;
        float nc_thr_;
        float amc_thr_;
        float amc_forgetting_factor_;
    };

    std::shared_ptr<beamformer_param_t> currentParam;
    GarbageCollector releasePool;
};


#endif //OSP_BEAMFORMER_H
