//
// Created by Kuan-Lin Chen on 10/5/18.
//

#ifndef OSP_ADAPTIVE_FILTER_H
#define OSP_ADAPTIVE_FILTER_H

#include <OSP/filter/filter.hpp>
#include <atomic>
#include <OSP/ReleasePool/ReleasePool.hpp>

/**
 * @brief Adaptive Filter Class
 * @details This adaptive filter class implements several popular LMS-based algorithms including Modified LMS [Greenberg, 1998], IPNLMS-l_0 [Paleologu et al., 2010] and SLMS [Lee et al., 2017].
 */
class adaptive_filter: public filter {

public:
    /**
     * @brief Adaptive filter constructor
     * @param[in] adaptive_filter_taps The initial filter taps for adaptive filter
     * @param[in] adaptive_filter_tap_len The number of filter taps of adaptive filter
     * @param[in] max_frame_size The maximum processing frame size in adaptive filter
     * @param[in] adaptation_type -1: 0 y_hat, 0: stop adaptation, 1: Modified LMS, 2: IPNLMS-l_0, 3: SLMS
     * @param[in] mu The gradient descent step size (learning rate) for LMS-based algorithms
     * @param[in] delta A small positive number to prevent dividing zero
     * @param[in] rho The forgetting factor for power estimate
     * @param[in] alpha A number between -1 to 1 for different degrees of sparsity in IPNLMS-l_0
     * @param[in] beta A number between 0 to 500 for different degrees of sparsity in IPNLMS-l_0
     * @param[in] p A number between 0 to 2 for fitting different degrees of sparsity in SLMS
     * @param[in] c A small positive number for preventing stagnation in SLMS
     * @param[in] power_estimate An initial power estimate for adaptation
     */
    explicit adaptive_filter(float *adaptive_filter_taps, size_t adaptive_filter_tap_len, size_t max_frame_size,
                             int adaptation_type, float mu, float delta, float rho, float alpha,
                             float beta, float p, float c, float power_estimate);
    /**
     * @brief Adaptive filter destructor
     */
    ~adaptive_filter();

    /**
     * @brief To update the taps of this adaptive filter based on the reference signals u_ref and e_ref
     * @param[in] u_ref A reference input signal for adaptation
     * @param[in] e_ref A reference error signal for adaptation
     * @param[in] ref_size The size of each reference signal (u_ref and e_ref have the same size)
     * @return A flag indicating the success of adaptation
     */
    int update_taps(float *u_ref, float *e_ref, size_t ref_size);

    /**
     * @brief Getting the maximum frame size
     * @return Maximum frame size
     */
    size_t get_max_frame_size();

    /**
     * @brief Getting all parameters from this adaptive filter
     * @param[out] mu The gradient descent step size (learning rate) for LMS-based algorithms
     * @param[out] rho The forgetting factor for power estimate
     * @param[out] delta A small positive number to prevent dividing zero
     * @param[out] alpha A number between -1 to 1 for different degrees of sparsity in IPNLMS-l_0
     * @param[out] beta A number between 0 to 500 for different degrees of sparsity in IPNLMS-l_0
     * @param[out] p A number between 0 to 2 for fitting different degrees of sparsity in SLMS
     * @param[out] c A small positive number for preventing stagnation in SLMS
     * @param[out] adaptation_type -1: 0 y_hat, 0: stop adaptation, 1: Modified LMS, 2: IPNLMS-l_0, 3: SLMS
     */
    void get_params(float &mu, float &rho, float &delta, float &alpha, float &beta, float &p, float &c, int &adaptation_type);

    /**
     * @brief Setting all parameters from this adaptive filter
     * @param[in] mu The gradient descent step size (learning rate) for LMS-based algorithms
     * @param[in] rho The forgetting factor for power estimate
     * @param[in] delta A small positive number to prevent dividing zero
     * @param[in] alpha A number between -1 to 1 for different degrees of sparsity in IPNLMS-l_0
     * @param[in] beta A number between 0 to 500 for different degrees of sparsity in IPNLMS-l_0
     * @param[in] p A number between 0 to 2 for fitting different degrees of sparsity in SLMS
     * @param[in] c A small positive number for preventing stagnation in SLMS
     * @param[in] adaptation_type -1: 0 y_hat, 0: stop adaptation, 1: Modified LMS, 2: IPNLMS-l_0, 3: SLMS
     */
    void set_params(float mu, float rho, float delta, float alpha, float beta, float p, float c, int adaptation_type);

protected:
    /**
     * @brief A function to get the adaptation type
     * @return Adaptation type
     */
    int get_adaptation_type();

    /**
     * @brief A function computing the step size control matrix for IPNLMS-l_0
     * @param[in] taps The current filter taps of the adaptive filter
     * @param[out] step_size_weights The step size control matrix (it is an 1-D array due to the diagonal matrix)
     * @param[in] alpha A number between -1 to 1 for different degrees of sparsity in IPNLMS-l_0
     * @param[in] beta A number between 0 to 500 for different degrees of sparsity in IPNLMS-l_0
     * @param[in] delta A small positive number to prevent dividing zero
     * @param[in] tap_len The number of taps of the adaptive filter
     */
    void get_step_size_weights_IPNLMS(float *taps, float *step_size_weights, float alpha, float beta, float delta, size_t tap_len);

    /**
     * @brief A function computing the step size control matrix for SLMS
     * @param[in] taps The current filter taps of the adaptive filter
     * @param[out] step_size_weights The step size control matrix (it is an 1-D array due to the diagonal matrix)
     * @param[in] p A number between 0 to 2 for fitting different degrees of sparsity in SLMS
     * @param[in] c A small positive number for preventing stagnation in SLMS
     * @param[in] tap_len The number of taps of the adaptive filter
     */
    void get_step_size_weights_SLMS(float *taps, float *step_size_weights, float p, float c, size_t tap_len);

private:
    circular_buffer* u_ref_buf_;
    size_t adaptive_filter_tap_len_;
    size_t max_frame_size_;
    float* gradient_;
    float* filter_taps_;
    float* step_size_weights_;
    float *u_ref_frame_buf;

    struct adaptive_filter_param_t {

        int adaptation_type_;
        float mu_; // step size
        float rho_; // forgetting factor
        float power_estimate_; // power estimate
        float delta_; // IPNLMS-l_0
        float alpha_; // IPNLMS-l_0
        float beta_; // IPNLMS-l_0
        float p_; // SLMS
        float c_; // SLMS

    };

    std::shared_ptr<adaptive_filter_param_t> currentParam;
    ReleasePool releasePool;
};

#endif //OSP_ADAPTIVE_FILTER_H