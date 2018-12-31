//
// Created by dsengupt on 9/19/18.
//

#ifndef OSP_PEAK_DETECT_H
#define OSP_PEAK_DETECT_H

#include <memory>
#include <mutex>

#define FP_EPSILON 2.2204460492503131e-16 // Small constant to avoid taking the log of zero
#define DEFAULT_LEVEL 100 // Default Sound pressure level (SPL) corresponding to signal RMS value of 1 (note that SPL is measured in dB)

/**
 * @brief Peak Detector Class
 * @details This peak detector implements the algorithm according to Eq. (8.1) in [James M. Kates, Digital hearing aids, Plural publishing, 2008].
 */

class peak_detect{

public:

    /**
     * @brief Peak detector constructor
     * @param[in] fsamp The sampling rate of the system
     * @param[in] attack_time Attack time in milliseconds
     * @param[in] release_time Release time in milliseconds
     */
    explicit peak_detect(float fsamp, float attack_time, float release_time);

    /**
     * @brief Peak detector destructor
     */
    ~peak_detect();

    /**
     * @brief Setting the parameters for peak detector (to have alpha and beta)
     * @param[in] attack_time Attack time in milliseconds
     * @param[in] release_time Release time in milliseconds
     */
    void set_param(float attack_time, float release_time);

    /**
     * @brief Getting the parameters from peak detector (in terms of attach time and release time)
     * @param[out] attack_time attack_time Attack time in milliseconds
     * @param[out] release_time release_time Release time in milliseconds
     */
    void get_param(float &attack_time, float &release_time);

    /**
     * @brief Getting the output from the peak detector in SPL
     * @param[in] data_in The input signal
     * @param[in] in_len The size of the input signal
     * @param[out] pdb_out The output of peak detector in SPL
     */
    void get_spl(float* data_in, size_t in_len, float* pdb_out);

private:

    /**
     * @brief A function to convert the output from the peak detector into SPL
     * @details The SPL output will be used by the wide dynamic-range compression (wdrc) function.
     * The function assumes that an RMS signal value of 1 corresponds to DEFAULT_LEVEL(100) dB SPL and converts the signal accordingly.
     * The input WAV file therefore needs to be set to RMS = 1 at the start of processing
     * @param[in] peak_in Pointer to the signal to be converted to SPL, i.e., the output from the peak detector
     * @param[in] in_len Length of the input signal to be converted to SPL
     * @param[out] pdb_out Pointer to the signal converted to SPL, i.e., the output of this function
     */
    void peak_to_spl(float* peak_in, size_t in_len, float* pdb_out);

    float fsamp_;
    float attack_time_;
    float release_time_;
    float att_;
    float alpha_;
    float rel_;
    float beta_;
    float prev_peak;
    std::mutex mutex_;
};

#endif //OSP_PEAK_DETECT_H
