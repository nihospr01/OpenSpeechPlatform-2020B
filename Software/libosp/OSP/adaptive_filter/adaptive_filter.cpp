#include <cmath>
#include <iostream>
#include <OSP/array_utilities/array_utilities.hpp>
#include <OSP/adaptive_filter/adaptive_filter.hpp>


adaptive_filter::adaptive_filter(float *adaptive_filter_taps, size_t adaptive_filter_tap_len,
                                          size_t max_frame_size,
                                          int adaptation_type, float mu, float delta, float rho, float alpha,
                                          float beta, float p, float c, float power_estimate)
        : filter(adaptive_filter_taps, adaptive_filter_tap_len, nullptr, max_frame_size) {
    u_ref_buf_ = new circular_buffer(adaptive_filter_tap_len + max_frame_size, 0.0f);
    adaptive_filter_tap_len_ = adaptive_filter_tap_len;
    max_frame_size_ = max_frame_size;
    adaptation_type_ = adaptation_type;
    mu_ = mu;
    delta_ = delta;
    rho_ = rho;
    alpha_ = alpha;
    beta_ = beta;
    p_ = p;
    c_ = c;
    power_estimate_ = power_estimate;

    gradient_ = new float[adaptive_filter_tap_len_]; // a place holder for gradient vector
    filter_taps_ = new float[adaptive_filter_tap_len_];
    step_size_weights_ = new float[adaptive_filter_tap_len_]; // a place holder for S(n)
}

adaptive_filter::~adaptive_filter() {
    delete u_ref_buf_;
    delete[] gradient_;
    delete[] filter_taps_;
    delete[] step_size_weights_;
}

int
adaptive_filter::update_taps(float *u_ref, float *e_ref, size_t ref_size) {
    // Are you adapting the filter taps?
    if (adaptation_type_ <= 0) {
        return 0;
    }
    // check the size of reference signal
    if (ref_size > max_frame_size_) {
        return -1;
    }
    // compute the power estimate of the input to coefficient adaptation
    power_estimate_ = rho_ * power_estimate_ +
                      (1 - rho_) * (array_mean_square(u_ref, ref_size) + array_mean_square(e_ref, ref_size));
    // normalize the step size
    float mu = mu_ / (adaptive_filter_tap_len_ * power_estimate_ + delta_);
    // compute the gradient, gradient = [u_ref(n)]*e_ref(n)
    auto *u_ref_frame_buf = new float[ref_size]; // a place holder for the frame which will be undergone dot product with e_ref
    u_ref_buf_->set(u_ref, ref_size); // put new u_ref frame to the buffer
    size_t u_ref_buf_head_backup = u_ref_buf_->head_; // backup the original head position
    for (size_t i = 0; i < adaptive_filter_tap_len_; i++) {
        u_ref_buf_->head_ = (u_ref_buf_->head_ - i) & u_ref_buf_->mask_; // shift the head position to assess next frame
        u_ref_buf_->get(u_ref_frame_buf, ref_size); // get the frame data
        gradient_[i] = array_dot_product(u_ref_frame_buf, e_ref, ref_size); // compute dot product (cross-correlation)
        u_ref_buf_->head_ = u_ref_buf_head_backup; // restore the original head position
    }

    // compute the step-size control matrix, S(n), namely step_size_weights
    int get_check = this->get_taps(filter_taps_,
                                   adaptive_filter_tap_len_); // get the current filter taps to compute S(n)
    // case 1, Modified LMS, S(n) = I (identity)
    // case 2, IPNLMS-l_0, S(n) = (1-alpha)/2+(1+alpha)|h_i(n)|/[(2/M)one_norm(h(n))]
    // case 3, SLMS, S(n) = |h_i(n)|^(2-p)/|(1/M)*sum_over_j(|h_j(n)|^(2-p))|
    switch (adaptation_type_) {
        case 1:
            // no need to compute S(n) for Modified LMS
            break;
        case 2:
            // compute S(n) for IPNLMS-l_0
            get_step_size_weights_IPNLMS(filter_taps_, step_size_weights_, alpha_, beta_, delta_,
                                         adaptive_filter_tap_len_); // get S(n)
            array_element_multiply_array(gradient_, step_size_weights_, adaptive_filter_tap_len_); // S(n)*u(n)e(n)
            break;
        case 3:
            // compute S(n) for SLMS
            get_step_size_weights_SLMS(filter_taps_, step_size_weights_, p_, c_, adaptive_filter_tap_len_); // get S(n)
            array_element_multiply_array(gradient_, step_size_weights_, adaptive_filter_tap_len_); // S(n)*u(n)e(n)
            break;
        default:
            break;
    }
    array_multiply_const(gradient_, mu, adaptive_filter_tap_len_); // mu*S(n)u(n)e(n)
    array_add_array(filter_taps_, gradient_, adaptive_filter_tap_len_); // w(n) + mu*S(n)u(n)e(n)
    int set_check = this->set_taps(filter_taps_, adaptive_filter_tap_len_); // update the filter taps
    delete[] u_ref_frame_buf;
    return get_check | set_check;
}


size_t
adaptive_filter::get_max_frame_size() {
    return max_frame_size_;
}

void
adaptive_filter::get_params(float &mu, float &rho, float &delta, float &alpha, float &beta, float &p, float &c,
                                 int &adaptation_type) {
    mu = mu_;
    rho = rho_;
    delta = delta_;
    alpha = alpha_;
    beta = beta_;
    p = p_;
    c = c_;
    adaptation_type = adaptation_type_;
}

void
adaptive_filter::set_params(float mu, float rho, float delta, float alpha, float beta, float p, float c,
                                 int adaptation_type) {
    mu_ = mu;
    rho_ = rho;
    delta_ = delta;
    alpha_ = alpha;
    beta_ = beta;
    p_ = p;
    c_ = c;
    adaptation_type_ = adaptation_type;
}

int
adaptive_filter::get_adaptation_type() {
    return adaptation_type_;
}

void
adaptive_filter::get_step_size_weights_IPNLMS(float *taps, float *step_size_weights, float alpha, float beta,
                                                   float delta, size_t tap_len) {
    float tmp_sum = 0;
    for (size_t i = 0; i < tap_len; i++) {
        step_size_weights[i] = 1.0f - std::exp(-beta * fabsf(taps[i]));
        tmp_sum = tmp_sum + step_size_weights[i];
    }
    tmp_sum = tmp_sum * 2.0f / (float) tap_len + delta;
    tmp_sum = (1.0f + alpha) / tmp_sum;
    array_multiply_const(step_size_weights, tmp_sum, tap_len);
    array_add_const(step_size_weights, (1.0f - alpha) / 2.0f, tap_len);
}

void
adaptive_filter::get_step_size_weights_SLMS(float *taps, float *step_size_weights, float p, float c, size_t tap_len) {
    float tmp = 0;
    for (size_t i = 0; i < tap_len; i++) {
        step_size_weights[i] = powf(fabsf(taps[i]) + c, 2 - p);
    }
    tmp = 1.0f / array_mean(step_size_weights, tap_len);
    array_multiply_const(step_size_weights, tmp, tap_len);
}