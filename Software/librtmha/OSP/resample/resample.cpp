#include <memory>
#include <cstring>
#include <OSP/filter/fir_formii.h>
#include <OSP/circular_buffer/circular_buffer.hpp>
#include <OSP/resample/resample.hpp>

resample::resample(float *taps, size_t tap_size, size_t max_in_buf_size, int interp_factor, int deci_factor) {
    this->tap_size = tap_size;
    resamp_filter = new fir_formii(taps, tap_size);
    interpolation_factor = interp_factor;
    decimation_factor = deci_factor;
    up_sample_buf = new float[max_in_buf_size * interp_factor];
    std::memset(up_sample_buf, 0, max_in_buf_size*interp_factor*sizeof(float));
    to_dec = new float[max_in_buf_size * interp_factor];
}

resample::~resample() {
    delete resamp_filter;
    delete up_sample_buf;
    delete to_dec;
}

void resample::resamp(float *data_in, size_t in_size, float *data_out, size_t *out_size) {
    size_t filtered_size = in_size * interpolation_factor;
    this->interpolate(data_in, in_size);
    resamp_filter->process(up_sample_buf, to_dec, filtered_size);
    this->decimate(to_dec, filtered_size, data_out, *out_size);
}

inline void resample::interpolate(float *data_in,  size_t in_len) {
    for (size_t i = 0; i < in_len; i++) {
        up_sample_buf[i*interpolation_factor] = data_in[i];
    }
}

inline void resample::decimate(const float *data_in, size_t in_len, float *data_out, size_t &out_len) {
    size_t i;
    size_t dec_factor = (size_t)this->decimation_factor;
    out_len = (in_len / dec_factor);
    for (i = 0; i < out_len; i ++) {
        data_out[i] = data_in[i*dec_factor];
    }

}
