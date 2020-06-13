//
// Created by dsengupt on 9/19/18.
//

#ifndef OSP_RESAMPLE_H
#define OSP_RESAMPLE_H

#include <cstddef>
#include <OSP/filter/fir_formii.h>

/**
 * @brief Resample Class
 * @details Resampling class implements L/M-fold resampling
 */
class resample {

public:
    /**
     * @brief Resample constructor
     * @param[in] taps The filter taps of the lowpass filter (to reject images and prevent aliasing)
     * @param[in] tap_size The number of taps of the lowpass filter
     * @param[in] max_in_buf_size The maximum input buffer size
     * @param[in] interp_factor The interpolation factor L (to implement L-fold expander)
     * @param[in] deci_factor The decimation factor M (to implement M-fold decimator)
     */
    explicit resample(float* taps, size_t tap_size, size_t max_in_buf_size, int interp_factor, int deci_factor);

    /**
     * @brief Resample destructor
     */
    ~resample();

    /**
     * @brief Getting the resampled signal
     * @param[in] data_in The signal in original sampling rate
     * @param[in] in_size The size of the original signal
     * @param[out] data_out The resampled signal
     * @param[out] out_size The size of the resampled signal
     */
    void resamp(float *data_in, size_t in_size, float* data_out, size_t* out_size);

private:

    /**
     * @brief The expander
     * @param[in] data_in The input signal
     * @param[in] in_len The size of the input signal
     */
    void interpolate(float* data_in, size_t in_len);

    /**
     * @brief The decimator
     * @param data_in The input signal
     * @param in_len The size of the input signal
     * @param data_out The decimated signal (output of the decimator)
     * @param out_len The size of the decimated signal
     */
    void decimate(const float *data_in, size_t in_len, float* data_out, size_t &out_len);

    int interpolation_factor;
    int decimation_factor;
    size_t tap_size;
    //circular_buffer *buf_after_inter;
    fir_formii *resamp_filter;
    float *up_sample_buf;
    float *to_dec;
};

#endif //OSP_RESAMPLE_H
