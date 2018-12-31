//
// Created by Kuan-Lin Chen on 10/7/18.
//

#ifndef OSP_BEAMFORMER_H
#define OSP_BEAMFORMER_H

#include <cstddef>
#include <OSP/adaptive_filter/adaptive_filter.hpp>

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
     * @see adaptive_filter
     */
    explicit beamformer(size_t delay_len, float *adaptive_filter_taps, size_t adaptive_filter_tap_len, size_t max_frame_size,
                        int adaptation_type, float mu, float delta, float rho, float alpha, float beta, float p,
                        float c, float power_estimate);

    /**
     * @brief Beamformer destructor
     */
    ~beamformer();

    /**
     * @brief Getting e signal (the output signal of this beamformer)
     * @param[out] e The output signal of this beamformer
     * @param[in] x_l The input signal from the left channel
     * @param[in] x_r the input signal from the right channel
     * @param[in] ref_size The size of each input signal (x_l and x_r have the same size)
     * @return A flag indicating the success of adaptation in adaptive filter
     */
    int get_e(float *e, const float *x_l, const float *x_r, size_t ref_size);

private:
    circular_buffer* delay_buffer_;
    size_t delay_len_;
};


#endif //OSP_BEAMFORMER_H
