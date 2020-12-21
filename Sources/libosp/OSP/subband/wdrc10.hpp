//
// Martin Hunt 10/12/2020
//

#ifndef OSP_WDRC10_H
#define OSP_WDRC10_H

#include <memory>
#include <cmath>
#include <atomic>
#include <OSP/GarbageCollector/GarbageCollector.hpp>

// Hilbert filter for 10-Band WDRC

class Hilbert {
   private:
    // FILTER COEFFICIENTS
    const float c0 = -0.172636;
    const float c1 = -0.519974;
    const float c2 = -0.843466;

    // The input signal buffers, which holds 4 delays: in[n-1], in[n-2], in[n-3], in[n-4]
    float in_delay[4] = {0};        // The 4 registers for the delayed input signal
    float out_real_delay[4] = {0};  // The 4 registers for the delayed real output
    float out_imag_delay[2] = {0};  // The 2 registers for the delayed imaginary output

   public:
    Hilbert(){};
    ~Hilbert(){};
    void process(float *in, float *out_real, float *out_imag, const int length) {
        float temp;
        for (int i = 0; i < length; i++) {
            // Calculating real output sample
            temp = c0 * c2 * in[i] + in_delay[3] + (c0 + c2) * (in_delay[1] - out_real_delay[1]) -
                   c0 * c2 * out_real_delay[3];

            // shift the buffer
            out_real_delay[3] = out_real_delay[2];
            out_real_delay[2] = out_real_delay[1];
            out_real_delay[1] = out_real_delay[0];
            out_real_delay[0] = temp;

            // Calculating imaginary output sample
            temp = c1 * (in[i] - out_imag_delay[1]) + in_delay[1];

            // Shift the buffer
            out_imag_delay[1] = out_imag_delay[0];
            out_imag_delay[0] = temp;

            in_delay[3] = in_delay[2];
            in_delay[2] = in_delay[1];
            in_delay[1] = in_delay[0];
            in_delay[0] = in[i];

            // Writing the output
            out_real[i] = out_real_delay[0];  // Output the real sample, which is now called [n-1]
            out_imag[i] = out_imag_delay[1];  // The imaginary path has a n extra delay, hence the [n-2]
        }
    }
};


/**
 * @brief Wide Dynamic Range Compression (WDRC) Class for 10-Band
 * @details Applying WDRC to a subband signal from an analysis filterbank
 */

class wdrc10 {

public:
    /**
     * @brief wdrc constructor
     * @param[in] gain50 Gain at 50 dB SPL of input level
     * @param[in] gain80 Gain at 80 dB SPL of input level
     * @param[in] kneelow Lower knee-point
     * @param[in] mpo Maximum power output (MPO)
     */
    explicit wdrc10(float gain50, float gain80, float kneelow, float mpo);

    /**
     * @brief wdrc destructor
     */
    ~wdrc10();

    /**
     * @brief Setting WDRC parameters
     * @param[in] gain50 Gain at 50 dB SPL of input level
     * @param[in] gain80 Gain at 80 dB SPL of input level
     * @param[in] kneelow Lower knee-point
     * @param[in] mpo MPO
     */
    void set_param(float gain50, float gain80, float kneelow, float mpo);

    /**
     * @brief Getting WDRC parameters
     * @param[out] gain50 Gain at 50 dB SPL of input level
     * @param[out] gain80 Gain at 80 dB SPL of input level
     * @param[out] kneelow Lower knee-point
     * @param[out] mpo MPO
     */
    void get_param(float &gain50, float &gain80, float &knee_low, float &mpo_limit);

    /**
     * @brief Perform 10-Band WDRC
     * @details The peak detector output in dB SPL is needed as one of the inputs.
     * The gain at 50 and 80 dB SPL is specified for the frequency sub-band, along with the lower and upper kneepoints
     * in dB SPL. The compressor is linear below the lower kneepoint and applies compression limiting above the upper
     * kneepoint
     * @param[in] input The input signal (1-D array)
     * @param[out] output Pointer to a signal (1-D array) where the compressed output of the subband signal will be
     * written, i.e., the output of WDRC
     * @param[in] length Length of the signals
     */
    void process(float *input, float *output, const int length);

   private:
    Hilbert hilbert;

    float gain50_, gain80_;

    // Need closed form solution for alpha1 and alpha2 (Discuss with Hari ANSI 3.22)
    const float alpha1 = 0.0129;  // Controls attack time
    const float alpha2 = 0.0028;  // Controls release time

    // An FIR derivative filter
    const float fir_deriv[21] = {-2.2957747e-09,
                                 2.1385638e-06,
                                 -5.4378077e-05,
                                 0.00055749953,
                                 -0.0034185853,
                                 0.014757773,
                                 -0.049202964,
                                 0.13588695,
                                 -0.33726451,
                                 0.90691596,
                                 0,
                                 -0.90691596,
                                 0.33726451,
                                 -0.13588695,
                                 0.049202964,
                                 -0.014757773,
                                 0.0034185853,
                                 -0.00055749953,
                                 5.4378077e-05,
                                 -2.1385638e-06,
                                 2.2957747e-09};
    // Saving the gain of the last sample of the frame to be used
    // as the initial gain for the next frame.
    float saved_gain = 1;

    struct wdrc10_param_t {
        float m;  // The slope of the compression region equation
        float b;  // The 'y' intercept of the compression region equation
        float c;  // The 'y' intercept of the linear region
        float mpo;
        float kneelow;
        float kneeup;
    };

    std::shared_ptr<wdrc10_param_t> currentParam;
    GarbageCollector releasePool;

};



#endif //OSP_WDRC10_H
