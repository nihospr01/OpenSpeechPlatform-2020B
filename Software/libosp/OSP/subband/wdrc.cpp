#include <OSP/subband/wdrc.hpp>

wdrc::wdrc(float gain50, float gain80, float knee_low, float mpo_limit) {
    gain50_ = gain50;
    gain80_ = gain80;
    knee_low_ = knee_low;
    mpo_limit_ = mpo_limit;

    slope_ = (gain80 - gain50) / 30.0f;    // Compression Ratio = 1.0/(1+slope)
    glow_ = gain50 - slope_ * (50.0f - knee_low);    // Gain in dB at lower kneepoint
}

wdrc::~wdrc() = default;

void
wdrc::set_param(float gain50, float gain80, float knee_low, float mpo_limit) {
    gain50_ = gain50;
    gain80_ = gain80;
    knee_low_ = knee_low;
    slope_ = (gain80 - gain50) / 30.0f;    // Compression Ratio = 1.0/(1+slope)
    glow_ = gain50 - slope_ * (50.0f - knee_low);    // Gain in dB at lower kneepoint
    mpo_limit_ = mpo_limit;
}

void
wdrc::get_param(float &gain50, float &gain80, float &knee_low, float &mpo_limit) {
    gain50 = gain50_;
    gain80 = gain80_;
    knee_low = knee_low_;
    mpo_limit = mpo_limit_;
}

void
wdrc::process(float *input, float *pdb, size_t in_len, float *output) {
    float g; // variable to store gain in dB

    // Compute the compression gain in dB using the peak detector dB values and write compressed signal in out
    for (size_t i = 0; i < in_len; i++) {
        if (pdb[i] < knee_low_) {
            g = glow_;     // Linear gain below lower kneepoint
        } else {
            g = glow_ + slope_ * (pdb[i] - knee_low_);    // Compression in between kneepoints
        }

        if ((pdb[i] + g) >= mpo_limit_) {
            g = mpo_limit_ - pdb[i];     // Above MPO limit

        }

        // Apply the compression gain to the signal in the band
        g = powf(10.0f, g / 20.0f);    // Convert the gain to linear scale
        output[i] = input[i] * g;    // Multiply the signal by the gain
    }
}

