//
// Created by Dhiman Sengupta on 10/3/18.
//

#ifndef OSP_WDRC_H
#define OSP_WDRC_H

#include <memory>
#include <cmath>
#include <atomic>
#include <OSP/ReleasePool/ReleasePool.hpp>

/**
 * @brief Wide Dynamic Range Compression (WDRC) Class
 * @details Applying WDRC to a subband signal from an analysis filterbank
 */

class wdrc{

public:
    /**
     * @brief wdrc constructor
     * @param[in] gain50 Gain at 50 dB SPL of input level
     * @param[in] gain80 Gain at 80 dB SPL of input level
     * @param[in] knee_low Lower knee-point
     * @param[in] mpo_limit Maximum power output (MPO)
     */
    explicit wdrc(float gain50, float gain80, float knee_low, float mpo_limit);

    /**
     * @brief wdrc destructor
     */
    ~wdrc();

    /**
     * @brief Setting WDRC parameters
     * @param[in] gain50 Gain at 50 dB SPL of input level
     * @param[in] gain80 Gain at 80 dB SPL of input level
     * @param[in] knee_low Lower knee-point
     * @param[in] mpo_limit MPO
     */
    void set_param(float gain50, float gain80, float knee_low, float mpo_limit);

    /**
     * @brief Getting WDRC parameters
     * @param[out] gain50 Gain at 50 dB SPL of input level
     * @param[out] gain80 Gain at 80 dB SPL of input level
     * @param[out] knee_low Lower knee-point
     * @param[out] mpo_limit MPO
     */
    void get_param(float &gain50, float &gain80, float &knee_low, float &mpo_limit);

    /**
     * @brief Perform WDRC
     * @details The peak detector output in dB SPL is needed as one of the inputs.
     * The gain at 50 and 80 dB SPL is specified for the frequency sub-band, along with the lower and upper kneepoints in dB SPL.
     * The compressor is linear below the lower kneepoint and applies compression limiting above the upper kneepoint
     * @param[in] input The input signal (1-D array)
     * @param[in] pdb The output from the peak detector in SPL, i.e., the output from get_spl member function in peak_detect class
     * @param[in] in_len Length of the input signal
     * @param[out] output Pointer to a signal (1-D array) where the compressed output of the subband signal will be written, i.e., the output of WDRC
     * @see peak_detect
     */
    void process(float* input, float* pdb, size_t in_len, float* output);

private:

    struct wdrc_param_t {

        float mpo_limit_;
        float gain50_;
        float gain80_;
        float slope_;
        float glow_;
        float knee_low_;

    };

    std::shared_ptr<wdrc_param_t> currentParam;
    ReleasePool releasePool;

};

#endif //OSP_WDRC_H
