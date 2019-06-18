#include <cmath>
#include <iostream>
#include <OSP/array_utilities/array_utilities.hpp>
#include <OSP/adaptive_filter/adaptive_filter.hpp>


adaptive_filter::adaptive_filter(float *adaptive_filter_taps, size_t adaptive_filter_tap_len,
                                          size_t max_frame_size,
                                          int adaptation_type, float mu, float delta, float rho, float alpha,
                                          float beta, float p, float c, float power_estimate)
        : filter(adaptive_filter_taps, adaptive_filter_tap_len, nullptr, max_frame_size) {
    std::shared_ptr<adaptive_filter_param_t> data_current = std::make_shared<adaptive_filter_param_t> ();
    u_ref_buf_ = new circular_buffer(adaptive_filter_tap_len + max_frame_size, 0.0f);
    adaptive_filter_tap_len_ = adaptive_filter_tap_len;
    max_frame_size_ = max_frame_size;
    data_current->adaptation_type_ = adaptation_type;
    data_current->mu_ = mu;
    data_current->delta_ = delta;
    data_current->rho_ = rho;
    data_current->alpha_ = alpha;
    data_current->beta_ = beta;
    data_current->p_ = p;
    data_current->c_ = c;
    data_current->power_estimate_ = power_estimate;

    gradient_ = new float[adaptive_filter_tap_len_]; // a place holder for gradient vector
    filter_taps_ = new float[adaptive_filter_tap_len_];
    step_size_weights_ = new float[adaptive_filter_tap_len_]; // a place holder for S(n)
    u_ref_frame_buf = new float[max_frame_size]; // a place holder for the frame which will be undergone dot product with e_ref
    releasePool.add (data_current);
    std::atomic_store (&currentParam, data_current);
}

adaptive_filter::~adaptive_filter() {
    delete u_ref_buf_;
    delete[] gradient_;
    delete[] filter_taps_;
    delete[] step_size_weights_;
    delete[] u_ref_frame_buf;
    releasePool.release();
}

int
adaptive_filter::update_taps(float *u_ref, float *e_ref, size_t ref_size) {
    // Are you adapting the filter taps?
    std::shared_ptr<adaptive_filter_param_t> data_current = std::atomic_load(&currentParam);
    if (data_current->adaptation_type_ <= 0) {
        return 0;
    }
    // check the size of reference signal
    if (ref_size > max_frame_size_) {
        return -1;
    }
    // compute the power estimate of the input to coefficient adaptation
    data_current->power_estimate_ = data_current->rho_ * data_current->power_estimate_ +
                      (1 - data_current->rho_) * (array_mean_square(u_ref, ref_size) + array_mean_square(e_ref, ref_size));
    // normalize the step size
    float mu = data_current->mu_ / (adaptive_filter_tap_len_ * data_current->power_estimate_ + data_current->delta_);
    // compute the gradient, gradient = [u_ref(n)]*e_ref(n)
    u_ref_buf_->set(u_ref, ref_size); // put new u_ref frame to the buffer
    size_t u_ref_buf_head_backup = u_ref_buf_->head_; // backup the original head position
    size_t mask = u_ref_buf_->mask_;
    for (size_t i = 0; i < adaptive_filter_tap_len_; i++) {
        u_ref_buf_->head_ = (u_ref_buf_head_backup - i) & mask; // shift the head position to assess next frame
        u_ref_buf_->get(u_ref_frame_buf, ref_size); // get the frame data
        gradient_[i] = array_dot_product(u_ref_frame_buf, e_ref, ref_size); // compute dot product (cross-correlation)
    }
    u_ref_buf_->head_ = u_ref_buf_head_backup;

    // compute the step-size control matrix, S(n), namely step_size_weights
    int get_check = this->get_taps(filter_taps_,
                                   adaptive_filter_tap_len_); // get the current filter taps to compute S(n)
    // case 1, Modified LMS, S(n) = I (identity)
    // case 2, IPNLMS-l_0, S(n) = (1-alpha)/2+(1+alpha)|h_i(n)|/[(2/M)one_norm(h(n))]
    // case 3, SLMS, S(n) = |h_i(n)|^(2-p)/|(1/M)*sum_over_j(|h_j(n)|^(2-p))|
    switch (data_current->adaptation_type_) {
        case 1:
            // no need to compute S(n) for Modified LMS
            break;
        case 2:
            // compute S(n) for IPNLMS-l_0
            get_step_size_weights_IPNLMS(filter_taps_, step_size_weights_, data_current->alpha_, data_current->beta_, data_current->delta_,
                                         adaptive_filter_tap_len_); // get S(n)
            array_element_multiply_array(gradient_, step_size_weights_, adaptive_filter_tap_len_); // S(n)*u(n)e(n)
            break;
        case 3:
            // compute S(n) for SLMS
            get_step_size_weights_SLMS(filter_taps_, step_size_weights_, data_current->p_, data_current->c_, adaptive_filter_tap_len_); // get S(n)
            array_element_multiply_array(gradient_, step_size_weights_, adaptive_filter_tap_len_); // S(n)*u(n)e(n)
            break;
        default:
            break;
    }
    array_multiply_const(gradient_, mu, adaptive_filter_tap_len_); // mu*S(n)u(n)e(n)
    array_add_array(filter_taps_, gradient_, adaptive_filter_tap_len_); // w(n) + mu*S(n)u(n)e(n)
    int set_check = this->set_taps(filter_taps_, adaptive_filter_tap_len_); // update the filter taps
    return get_check | set_check;
}


size_t
adaptive_filter::get_max_frame_size() {
    return max_frame_size_;
}

void
adaptive_filter::get_params(float &mu, float &rho, float &delta, float &alpha, float &beta, float &p, float &c,
                                 int &adaptation_type) {
    std::shared_ptr<adaptive_filter_param_t> data_current = std::atomic_load(&currentParam);
    mu = data_current->mu_;
    rho = data_current->rho_;
    delta = data_current->delta_;
    alpha = data_current->alpha_;
    beta = data_current->beta_;
    p = data_current->p_;
    c = data_current->c_;
    adaptation_type = data_current->adaptation_type_;
}

void
adaptive_filter::set_params(float mu, float rho, float delta, float alpha, float beta, float p, float c,
                                 int adaptation_type) {
    std::shared_ptr<adaptive_filter_param_t> data_next = std::make_shared<adaptive_filter_param_t> ();
    data_next->mu_ = mu;
    data_next->rho_ = rho;
    data_next->delta_ = delta;
    data_next->alpha_ = alpha;
    data_next->beta_ = beta;
    data_next->p_ = p;
    data_next->c_ = c;
    data_next->adaptation_type_ = adaptation_type;
    releasePool.add (data_next);
    std::atomic_store (&currentParam, data_next);
    releasePool.release();
}

int
adaptive_filter::get_adaptation_type() {
    std::shared_ptr<adaptive_filter_param_t> data_current = std::atomic_load(&currentParam);
    return data_current->adaptation_type_;
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