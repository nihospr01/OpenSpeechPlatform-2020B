#include <memory>
#include <cmath>
#include <shared_mutex>
#include <OSP/circular_buffer/circular_buffer.hpp>

circular_buffer::circular_buffer(size_t size, float reset) {


    size_ = (size_t) pow(2.0, ceil(log2((double) size)));
    buf_ = new float[size_];
    reset_ = reset;
    for (size_t i = 0; i < size_; i++) {
        buf_[i] = reset_;
    }
    head_.store(0);
    mask_ = size_ - 1;
}


circular_buffer::~circular_buffer() = default;


void
circular_buffer::set(const float *item, size_t buf_size) {
    size_t head = head_.load();
    for (size_t i = 0; i < buf_size; i++) {
        buf_[head] = item[i];
        head = (head + 1) & mask_;
    }
    head_.store(head);

};

void
circular_buffer::get(float *data, size_t buf_size) {
    size_t read_head = (head_.load() - buf_size) & mask_;
    for (size_t i = 0; i < buf_size; i++) {
        data[i] = buf_[read_head];
        read_head = (read_head + 1) & mask_;
    }
}

void
circular_buffer::reset() {
    for (size_t i = 0; i < size_; i++) {
        buf_[i] = reset_;
    }
}

size_t
circular_buffer::size() const {
    return size_;
}
