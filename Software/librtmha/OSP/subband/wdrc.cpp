#include <OSP/subband/wdrc.hpp>

wdrc::wdrc(float gain50, float gain80, float knee_low, float mpo_limit) {

    std::shared_ptr<wdrc_param_t> data_current = std::make_shared<wdrc_param_t> ();

    data_current->gain50_ = gain50;
    data_current->gain80_ = gain80;
    data_current->knee_low_ = knee_low;
    data_current->mpo_limit_ = mpo_limit;

    data_current->slope_ = (gain80 - gain50) / 30.0f;    // Compression Ratio = 1.0/(1+slope)
    data_current->glow_ = gain50 - data_current->slope_ * (50.0f - knee_low);    // Gain in dB at lower kneepoint
    releasePool.add (data_current);
    std::atomic_store (&currentParam, data_current);


}

wdrc::~wdrc(){
    releasePool.release();
}

void
wdrc::set_param(float gain50, float gain80, float knee_low, float mpo_limit) {
    std::shared_ptr<wdrc_param_t> data_next = std::make_shared<wdrc_param_t> ();

    data_next->gain50_ = gain50;
    data_next->gain80_ = gain80;
    data_next->knee_low_ = knee_low;
    data_next->slope_ = (gain80 - gain50) / 30.0f;    // Compression Ratio = 1.0/(1+slope)
    data_next->glow_ = gain50 - data_next->slope_ * (50.0f - knee_low);    // Gain in dB at lower kneepoint
    data_next->mpo_limit_ = mpo_limit;

    releasePool.add (data_next);
    std::atomic_store (&currentParam, data_next);
    releasePool.release();


}

void
wdrc::get_param(float &gain50, float &gain80, float &knee_low, float &mpo_limit) {
    std::shared_ptr<wdrc_param_t> data_current = std::atomic_load(&currentParam);
    gain50 = data_current->gain50_;
    gain80 = data_current->gain80_;
    knee_low = data_current->knee_low_;
    mpo_limit = data_current->mpo_limit_;
}

void
wdrc::process(float *input, float *pdb, size_t in_len, float *output) {
    float g; // variable to store gain in dB
    std::shared_ptr<wdrc_param_t> data_current = std::atomic_load(&currentParam);

    float knee_low_ = data_current->knee_low_;
    float glow_ = data_current->glow_;
    float mpo_limit_ = data_current->mpo_limit_;

    // Compute the compression gain in dB using the peak detector dB values and write compressed signal in out
    for (size_t i = 0; i < in_len; i++) {
        if (pdb[i] < knee_low_) {
            g = glow_;     // Linear gain below lower kneepoint
        } else {
            g = glow_ + data_current->slope_ * (pdb[i] - knee_low_);    // Compression in between kneepoints
        }

        if ((pdb[i] + g) >= mpo_limit_) {
            g = mpo_limit_ - pdb[i];     // Above MPO limit

        }

        // Apply the compression gain to the signal in the band
        g = powf(10.0f, g / 20.0f);    // Convert the gain to linear scale
        output[i] = input[i] * g;    // Multiply the signal by the gain
    }
}

