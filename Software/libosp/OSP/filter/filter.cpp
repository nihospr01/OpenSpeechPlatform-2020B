#include <memory>
#include <mutex>
#include <OSP/filter/filter.hpp>

filter::filter(float *taps, size_t tap_size, circular_buffer *cir_buf, size_t max_buf_size) {
    tap_ = new float[tap_size];
    size_ = tap_size;
    float *taps_ptr = taps;
    if (taps == nullptr) {
        taps = new float[tap_size];
        for (size_t i = 0; i < tap_size; i++) {
            taps[i] = 0;
        }
    }
    if (cir_buf != nullptr) {
        cir_buf_ = cir_buf;
        cir_buf_created_ = false;
    } else {
        cir_buf_ = new circular_buffer(tap_size + max_buf_size, 0);
        cir_buf_created_ = true;
    }
    set_taps(taps, size_);
    if (taps_ptr == nullptr) {
        delete[] taps;
    }
}

filter::~filter() {
    if (cir_buf_created_) {
        delete cir_buf_;
    }
    delete[] tap_;
}

int
filter::set_taps(const float *taps, size_t buf_size) {
    mutex_.lock();
    if (buf_size != this->size_) {
        mutex_.unlock();
        return -1;
    }
    for (size_t i = 0; i < buf_size; i++) {
        this->tap_[i] = taps[i];
    }
    mutex_.unlock();
    return 0;
}

int
filter::get_taps(float *taps, size_t buf_size) {
    mutex_.lock();
    if (buf_size != this->size_) {
        mutex_.unlock();
        return -1;
    }
    for (size_t i = 0; i < buf_size; i++) {
        taps[i] = this->tap_[i];
    }
    mutex_.unlock();
    return 0;
}

void
filter::cirfir(float *data_in, float *data_out, size_t num_samp) {
    cir_buf_->set(data_in, num_samp);
    this->cirfir(data_out, num_samp);
}

size_t
filter::get_size() {
    return size_;
}

void
filter::cirfir(float *data_out, size_t num_samp) {
    mutex_.lock();
    cir_buf_->mutex_.lock();
    float temp_data_out;
    auto mask = (int) cir_buf_->mask_;
    auto head = (int) cir_buf_->head_;
    auto size = (int) size_;
    float *data = cir_buf_->buf_;
    size_t index = (head + mask - size - num_samp + 2) & mask;
    for (size_t i = 0; i < num_samp; i++) {
        temp_data_out = 0;
        for (int j = 0; j < size; j++) {
            temp_data_out += data[(i + j + index) & mask] * tap_[size - j - 1];
        }
        data_out[i] = temp_data_out;
    }
    cir_buf_->mutex_.unlock();
    mutex_.unlock();
}
