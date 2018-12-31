#include <memory>
#include <math.h>
#include <shared_mutex>
#include <OSP/circular_buffer/circular_buffer.hpp>

circular_buffer::circular_buffer(size_t size, float reset) {


    size_ = (size_t) pow(2.0, ceil(log2((double) size)));
    buf_ = new float[size_];
    reset_ = reset;
    for (size_t i = 0; i < size_; i++) {
        buf_[i] = reset_;
    }
    head_ = 0;
    mask_ = size_ - 1;
}


circular_buffer::~circular_buffer() = default;


void
circular_buffer::set(const float *item, size_t buf_size) {
    mutex_.lock();
    for (size_t i = 0; i < buf_size; i++) {
        buf_[head_] = item[i];
        head_ = (head_ + 1) & mask_;
    }
    mutex_.unlock();

};

void
circular_buffer::get(float *data, size_t buf_size) {
    mutex_.lock();
    size_t read_head = (head_ - buf_size) & mask_;
    for (size_t i = 0; i < buf_size; i++) {
        data[i] = buf_[read_head];
        read_head = (read_head + 1) & mask_;
    }
    mutex_.unlock();
}

void
circular_buffer::reset() {
    mutex_.lock();
    for (size_t i = 0; i < size_; i++) {
        buf_[i] = reset_;
    }
    mutex_.unlock();
}

size_t
circular_buffer::size() const {
    return size_;
}
