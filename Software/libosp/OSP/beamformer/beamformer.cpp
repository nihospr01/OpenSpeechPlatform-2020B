#include <OSP/beamformer/beamformer.hpp>

beamformer::beamformer(size_t delay_len, float *adaptive_filter_taps, size_t adaptive_filter_tap_len,
                       size_t max_frame_size,
                       int adaptation_type, float mu, float delta, float rho, float alpha, float beta, float p,
                       float c, float power_estimate) : adaptive_filter(adaptive_filter_taps, adaptive_filter_tap_len,
                                                                        max_frame_size, adaptation_type, mu, delta, rho,
                                                                        alpha, beta, p, c, power_estimate) {
    delay_len_ = delay_len;
    delay_buffer_ = new circular_buffer(max_frame_size + delay_len_, 0.0f);
}

beamformer::~beamformer() {
    delete delay_buffer_;
}

int
beamformer::get_e(float *e, const float *x_l, const float *x_r, size_t ref_size) {
    // place holders
    auto u = new float[ref_size];
    auto v = new float[ref_size];
    auto d = new float[ref_size];
    auto y = new float[ref_size];
    // compute u(n) and v(n)
    for (size_t i = 0; i < ref_size; i++) {
        u[i] = (x_l[i] - x_r[i]) * 0.5f;
        v[i] = (x_l[i] + x_r[i]) * 0.5f;
    }
    // compute y(n)
    this->cirfir(u, y, ref_size);
    // compute d(n)
    delay_buffer_->set(v, ref_size); // put a new frame to buffer
    size_t delay_head_backup = delay_buffer_->head_; // backup the position of current head
    delay_buffer_->head_ =
            (delay_buffer_->head_ - delay_len_) & delay_buffer_->mask_; // shift the position of head by delay_len_
    delay_buffer_->get(d, ref_size); // get d(n)
    delay_buffer_->head_ = delay_head_backup; // restore the head
    // compute e(n)
    for (size_t i = 0; i < ref_size; i++) {
        e[i] = d[i] - y[i];
    }
    // update the taps of the adaptive filter
    int check = this->update_taps(u, e, ref_size);
    delete[] u;
    delete[] v;
    delete[] d;
    delete[] y;
    return check;
}
