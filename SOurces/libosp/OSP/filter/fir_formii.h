//======================================================================================================================
/** @file fir_formii.h
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
// Last Modified by Dhiman Sengupta on 2020-05-05.
//

#ifndef OPENSPEECHPLATFORMLIBRARIES_FIR_FORMII_H
#define OPENSPEECHPLATFORMLIBRARIES_FIR_FORMII_H


#include <memory>
#include <cstring>
#include <cmath>
#include <atomic>
#include <OSP/GarbageCollector/GarbageCollector.hpp>

/**
 * @brief Filter Class
 * @details This filter class implements the FIR filter
 */
class fir_formii {

public:
    /**
     * @brief Filter constructor
     * @param[in] taps The filter taps for this FIR filter
     * @param[in] tap_size The number of taps of this FIR filter
     * @param[in] cir_buf The circular buffer for this FIR filter to perform frame-based convolution
     * @param[in] max_buf_size The maximum size of circular buffer you need to specify if there is no circular buffer given in cir_buf
     */
    explicit fir_formii(float* taps, size_t tap_size);

    /**
     * @brief Filter destructor
     */
    ~fir_formii();

    /**
     * @brief Setting the filter taps
     * @param[in] taps The filter taps (an 1-D array)
     * @param[in] buf_size The size of the filter taps (this should be the same as tap_size passed in constructor)
     * @return A flag indicating the success of setting the filter taps
     */
    void set_taps(const float* taps, size_t tap_size);

    /**
     * @brief Getting the filter taps
     * @param[out] taps The filter taps (1-D array)
     * @param[in] buf_size The size of the filter taps (this should be the same as tap_size passed in constructor)
     * @return A flag indicating the success of getting the filter taps
     */
    void get_taps(float* taps, size_t &tap_size);

    /**
     * @brief Getting the output of this FIR filter by performing frame-based convolution
     * @param[in] data_in The input signal
     * @param[out] data_out The output signal
     * @param num_samp The size of input and output signal (data_in and data_out should have the same size)
     */
    void process(const float* data_in, float* data_out, size_t num_samp);

    /**
     * @brief Getting the number of taps of this FIR filter
     * @return The number of taps of this FIR filter
     */
    size_t get_size();
private:
    struct fir_formii_t {

        ~fir_formii_t(){
            delete[] taps;
            delete[] delays1;
            delete[] delays2;
        }

        float *taps;
        float *delays1;
        float *delays2;
        size_t tap_length;

    };

    std::shared_ptr<fir_formii_t> currentParam;
    GarbageCollector releasePool;
};


#endif //OPENSPEECHPLATFORMLIBRARIES_FIR_FORMII_H
