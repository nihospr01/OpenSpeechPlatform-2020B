//
// Created by dsengupt on 9/18/18.
//

#ifndef OSP_FILTER_H
#define OSP_FILTER_H

#include <cstddef>
#include <OSP/circular_buffer/circular_buffer.hpp>

/**
 * @brief Filter Class
 * @details This filter class implements the FIR filter
 */
class filter {

public:
    /**
     * @brief Filter constructor
     * @param[in] taps The filter taps for this FIR filter
     * @param[in] tap_size The number of taps of this FIR filter
     * @param[in] cir_buf The circular buffer for this FIR filter to perform frame-based convolution
     * @param[in] max_buf_size The maximum size of circular buffer you need to specify if there is no circular buffer given in cir_buf
     */
    explicit filter(float* taps, size_t tap_size, circular_buffer* cir_buf, size_t max_buf_size);

    /**
     * @brief Filter destructor
     */
    ~filter();

    /**
     * @brief Setting the filter taps
     * @param[in] taps The filter taps (an 1-D array)
     * @param[in] buf_size The size of the filter taps (this should be the same as tap_size passed in constructor)
     * @return A flag indicating the success of setting the filter taps
     */
    int set_taps(const float* taps, size_t buf_size);

    /**
     * @brief Getting the filter taps
     * @param[out] taps The filter taps (1-D array)
     * @param[in] buf_size The size of the filter taps (this should be the same as tap_size passed in constructor)
     * @return A flag indicating the success of getting the filter taps
     */
    int get_taps(float* taps, size_t buf_size);

    /**
     * @brief Getting the output of this FIR filter by performing frame-based convolution
     * @param[in] data_in The input signal
     * @param[out] data_out The output signal
     * @param num_samp The size of input and output signal (data_in and data_out should have the same size)
     */
    void cirfir(float* data_in, float* data_out, size_t num_samp);

    /**
     * @brief Getting the number of taps of this FIR filter
     * @return The number of taps of this FIR filter
     */
    size_t get_size();

    /**
     * @brief Frame-based convolution for FIR filtering
     * @param[out] data_out The output signal
     * @param[in] num_samp The size of input and output signal
     */
    void cirfir(float* data_out, size_t num_samp);

private:
    float* tap_;
    size_t size_;
    circular_buffer* cir_buf_;
    std::mutex mutex_;
    bool cir_buf_created_;
};

#endif //OSP_FILTER_H
