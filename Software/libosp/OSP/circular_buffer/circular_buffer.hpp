#ifndef OSP_CIRCULAR_BUFFER_H
#define OSP_CIRCULAR_BUFFER_H

#include <cstddef>
#include <mutex>

/**
 *  @brief Circular Buffer Class
 */
class circular_buffer {

public:

    /**
     * @brief Circular buffer constructor
     * @param size The maximum size you would want your circular buffer to be
     * @param reset The value you want to reset all of the values in the circular buffer to
     */
    explicit circular_buffer(size_t size, float reset);

    /**
     * @brief Default destructor
     */
    ~circular_buffer();

    /**
     * @brief This is the set command for the circular buffer
     * @param item The buffer of data you want to put in the circular buffer
     * @param buf_size The size of the buffer.
     */
    void set(const float *item, size_t buf_size);

    /**
     * @brief This is the get function for the circular buffer
     * @param data A buffer to put your data in.
     * @param buf_size The amount of data you want from the circular buffer
     */
    void get(float* data, size_t buf_size);

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

    std::mutex mutex_;
    float* buf_;
    size_t head_;
    size_t size_;
    size_t mask_;
    float reset_;

private:

};

#endif //OSP_CIRCULAR_BUFFER_H