#include <OSP/subband/peak_detect.hpp>
#include <math.h>

peak_detect::peak_detect(float fsamp, float attack_time, float release_time) {
    fsamp_ = fsamp;
    prev_peak = 0;
    this->set_param(attack_time, release_time);
}

peak_detect::~peak_detect() = default;

void
peak_detect::set_param(float attack_time, float release_time) {
    mutex_.lock();
    this->attack_time_ = attack_time;
    this->release_time_ = release_time;
    att_ = 0.001f * attack_time_ * fsamp_ / 2.425f;
    alpha_ = att_ / (1.0f + att_);
    rel_ = 0.001f * release_time_ * fsamp_ / 1.782f;
    beta_ = rel_ / (1.0f + rel_);
    mutex_.unlock();
}

void
peak_detect::get_param(float &attack_time, float &release_time) {
    mutex_.lock();
    attack_time = this->attack_time_;
    release_time = this->release_time_;
    mutex_.unlock();
}

void
peak_detect::get_spl(float *data_in, size_t in_len, float *pdb_out) {
    mutex_.lock();
    float curr_inp, temp;
    float peak_out[in_len];

    peak_out[0] = prev_peak;
    // Loop to peak detect the signal
    for (size_t i = 1; i < in_len; i++) {
        temp = data_in[i];
        curr_inp = temp > 0 ? temp : -temp; // Get the rectified signal
        if (curr_inp >= peak_out[i - 1]) {
            peak_out[i] = alpha_ * peak_out[i - 1] + (1 - alpha_) * curr_inp;
        } else {
            peak_out[i] = beta_ * peak_out[i - 1];
        }
    }

    // Setting prev_peak to be the peak of last sample of this frame for band given by band_num
    prev_peak = peak_out[in_len - 1];
    this->peak_to_spl(peak_out, in_len, pdb_out);
    mutex_.unlock();
}

void
peak_detect::peak_to_spl(float *peak_in, size_t in_len, float *pdb_out) {
    for (size_t i = 0; i < in_len; i++) {
        pdb_out[i] = DEFAULT_LEVEL + 20 * log10f(peak_in[i] + FP_EPSILON);    // floato avoid log of 0
    }
}

