//
// Created by Kuan-Lin Chen on 10/5/18.
//

#ifndef OSP_AFC_H
#define OSP_AFC_H

#include <OSP/adaptive_filter/adaptive_filter.hpp>

#define MAX_DELAY_LEN 256 // The maximum delay length in AFC

/**
 * @brief Adaptive Feedback Cancellation (AFC) Class
 * @details Under the FXLMS framework, this AFC class utilizes an adaptive filter to estimate the feedback signal, namely, y_hat.
 */
class afc: public adaptive_filter {

public:
    /**
     * @brief AFC constructor
     * @param[in] bandlimited_filter_taps The filter taps for bandlimited filter in AFC
     * @param[in] bandlimited_filter_tap_len The number of taps of bandlimited filter in AFC
     * @param[in] prefilter_taps The filter taps for whitening filter in AFC
     * @param[in] prefilter_tap_len The number of taps of prefilter in AFC
     * @param[in] adaptive_filter_taps The initial filter taps for adaptive filter in AFC
     * @param[in] adaptive_filter_tap_len The number of filter taps of adaptive filter in AFC
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
     * @param[in] delay_len The number of delay in samples
     * @param[in] afc_on_off A flag to turn the AFC on (0: OFF, 1: ON)
     * @see adaptive_filter
     */
    explicit afc(float *bandlimited_filter_taps, size_t bandlimited_filter_tap_len, float *prefilter_taps,
                 size_t prefilter_tap_len, float *adaptive_filter_taps, size_t adaptive_filter_tap_len, size_t max_frame_size,
                 int adaptation_type, float mu, float delta, float rho, float alpha, float beta, float p, float c,
                 float power_estimate, size_t delay_len, int afc_on_off);

    /**
     * @brief AFC destructor
     */
    ~afc();

    /**
     * @brief Getting y_hat signal (an estimated feedback signal)
     * @param[out] y_hat An estimated feedback signal
     * @param[in] e An error signal for AFC (the output of hearing aid processing)
     * @param[in] s An input signal for AFC (the input of hearing aid processing)
     * @param[in] ref_size The size of each signal (e and s have the same size)
     * @return A flag indicating the success of getting correct y_hat according to the adaptation type
     */
    int get_y_hat(float *y_hat, float *e, float *s, size_t ref_size);

    /**
     * @brief Getting the length of delay line in samples
     * @param[out] delay_len The number of delay in samples
     */
    void get_delay(size_t &delay_len);

    /**
     * @brief Setting the length of delay line in samples
     * @param[in] delay_len The number of delay in samples
     * @return A flag indicating the success of setting delay_len
     */
    int set_delay(size_t delay_len);

    /**
     * @brief Setting the ON/OFF for the AFC
     * @param[in] afc_on_off A flag to turn the AFC on (False: OFF, True: ON)
     */
    void set_afc_on_off(int afc_on_off);

    /**
     * @brief Getting the ON/OFF for the AFC
     * @param[in] afc_on_off A flag indicating the ON/OFF of AFC (False: OFF, True: ON)
     */
    void get_afc_on_off(int &afc_on_off);

    /**
     * @brief Reset the AFC filter to all zeros
     * @param[in] default_taps The default AFC filter
     * @param[in] len Length of the default AFC filter
     */
    void reset(float* default_taps, size_t len);

private:
    filter* bandlimited_filter_;
    filter* prefilter_e_;
    filter* prefilter_u_;
    size_t delay_len_;
    circular_buffer* delay_buffer_;
    float* u_;
    int afc_on_off_;
};

#endif //OSP_AFC_H
