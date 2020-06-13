//======================================================================================================================
/** @file circular_buffer.hpp
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
// Last Modified by Dhiman Sengupta on 2020-05-04.
//

#ifndef OSP_CIRCULAR_BUFFER_H
#define OSP_CIRCULAR_BUFFER_H

#include <cstddef>
#include <atomic>

/**
 *  @brief Circular Buffer Class
 */
class circular_buffer {

public:

    /**
     * @brief Circular buffer constructor
     * @param[in] size The maximum size you would want your circular buffer to be
     * @param[in] reset The value you want to reset all of the values in the circular buffer to
     */
    explicit circular_buffer(size_t size, float reset);

    /**
     * @brief Default destructor
     */
    ~circular_buffer();

    /**
     * @brief This is the set command for the circular buffer
     * @param[in] item The buffer of data you want to put in the circular buffer
     * @param[in] buf_size The size of the buffer.
     */
    void set(const float *item, size_t buf_size);

    /**
     * @brief This is the get function for the circular buffer
     * @param[out] data A buffer to put your data in.
     * @param[in] buf_size The amount of data you want from the circular buffer
     */
    void get(float* data, size_t buf_size);

    /**
     * @brief This is the get function for the circular buffer with a delay
     * @param[out] data A buffer to put your data in.
     * @param[in] buf_size The amount of data you want from the circular buffer
     * @param[in] delay The delay you want form the delay block in terms of time step
     */
    void delay_block(float *data, size_t buf_size, size_t delay);

    /**
     * @brief This is the reset command for circular buffer. It resets all of the values in the buffer to the default
     * value the user entered in the constructor
     */
    void reset();

    /**
     * @brief Function to get the size of the buffer
     * @return The size of the circular buffer which will be a power of 2
     */
    size_t size() const;

    float* buf_;
    std::atomic<size_t> head_;
    size_t size_;
    size_t mask_;
    float reset_;

private:

};

#endif //OSP_CIRCULAR_BUFFER_H