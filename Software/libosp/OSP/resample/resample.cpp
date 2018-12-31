#include <memory>
#include <OSP/filter/filter.hpp>
#include <OSP/circular_buffer/circular_buffer.hpp>
#include <OSP/resample/resample.hpp>

resample::resample(float *taps, size_t tap_size, size_t max_in_buf_size, int interp_factor, int deci_factor) {
    this->tap_size = tap_size;
    buf_after_inter = new circular_buffer(tap_size + (max_in_buf_size * interp_factor), 0.0f);
    resamp_filter = new filter(taps, tap_size, buf_after_inter, (max_in_buf_size * interp_factor));
    interpolation_factor = interp_factor;
    decimation_factor = deci_factor;
}

resample::~resample() {
    delete resamp_filter;
    delete buf_after_inter;
}

void
resample::resamp(float *data_in, size_t in_size, float *data_out, size_t *out_size) {
    size_t temp = tap_size - 1;
    size_t inter_size = in_size * interpolation_factor + temp;
    auto filtered_size = inter_size - temp;
    float to_dec[filtered_size];
    this->interpolate(data_in, in_size);
    resamp_filter->cirfir(to_dec, filtered_size);
    this->decimate(to_dec, filtered_size, data_out, out_size);
}

void
resample::interpolate(float *data_in, size_t in_len) {
    float temp_zero = 0;
    circular_buffer *temp = this->buf_after_inter;
    for (size_t i = 0; i < in_len; i++) {
        temp->set(data_in + i, 1);
        for (int j = 1; j < this->interpolation_factor; j++) {
            temp->set(&temp_zero, 1);
        }
    }
}

void
resample::decimate(const float *data_in, size_t in_len, float *data_out, size_t *out_len) {
    size_t i;
    int dec_factor = this->decimation_factor;
    float inverse_deci_factor = 1.0f / ((float) dec_factor);
    float out_index;
    for (i = 0; i < in_len; i += dec_factor) {
        out_index = (float) i * inverse_deci_factor;
        data_out[(size_t) out_index] = data_in[i];
    }
    *out_len = (size_t) ((float) in_len * inverse_deci_factor);
}
