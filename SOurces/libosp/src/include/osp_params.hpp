#ifndef OSP_PARAMS_HPP__
#define OSP_PARAMS_HPP__
#include <string>
#include <map>
#include <vector>

/**
 * @brief Namespace for the parameters values
 *
 * Our json parameters are in a flat namespace,
 * so we just put them here for simplicity, rather
 * than wrap them in a global struct which we pass around.
 */

typedef enum { kInt, kFloat, kArray, kStr } ParamType;

enum {
    kLeft = 1,  // Only left channel
    kBoth = 2,  // Both channels
    kCmd = 4    // Command (set only). Don't report
};

typedef enum { kCommon, kBf, kFp, kAudio, kSpeech, kWdrc, kAfc } PGroupType;

typedef struct {
    const std::string name;
    const ParamType type;
    const int flags;
    const PGroupType group;
    void *param;
} ParamValue;

namespace params {
// global
int num_bands = defaults::default_num_bands;

// common
float alpha[2];
float gain[2];  // gain in dB
float gain_[2];
int en_ha[2];
// int rear_mics[2];  not used

// beamforming
int bf;
float bf_alpha;
int bf_amc_on_off;
float bf_beta;
float bf_c;
int bf_delay_len;
float bf_delta;
int bf_fir_length;
float bf_mu;
int bf_nc_on_off;
float bf_p;
float bf_power_estimate;
float bf_rho;
int bf_type;
float amc_forgetting_factor;
float nc_thr;
float amc_thr;

// freping
int freping[2];
std::vector<float> freping_alpha[2];

// audio
std::string audio_filename;
int audio_play;
int audio_repeat;
int audio_reset;
int audio_playing;

// Speech (noise_management)
int noise_estimation_type[2];
int spectral_type[2];
float spectral_subtraction[2];

// WDRC (peak_detect, wdrc)
std::vector<float> attack[2];
std::vector<float> g50[2];
std::vector<float> g80[2];
std::vector<float> knee_low[2];
std::vector<float> mpo_band[2];
std::vector<float> release[2];
float global_mpo[2];
int aligned[2];

// AFC (Adaptive Feedback Cancellation)
int afc[2];
float afc_delay[2];
float afc_mu[2];
float afc_rho[2];
int afc_type[2];
int afc_reset[2];  // command

ParamValue par[] = {
    {.name = "aligned", .type = kInt, .flags = kBoth, .group = kWdrc, .param = &aligned},
    {.name = "alpha", .type = kFloat, .flags = kBoth, .group = kCommon, .param = &alpha},
    {.name = "gain", .type = kFloat, .flags = kBoth, .group = kCommon, .param = &gain},
    {.name = "en_ha", .type = kInt, .flags = kBoth, .group = kCommon, .param = &en_ha},
    // {.name = "rear_mics", .type = kInt, .flags = kBoth, .group = kCommon, .param = &rear_mics},
    // freping
    {.name = "freping", .type = kInt, .flags = kBoth, .group = kFp, .param = &freping},
    {.name = "freping_alpha", .type = kArray, .flags = kBoth, .group = kFp, .param = &freping_alpha},
    // beamforming
    {.name = "bf", .type = kInt, .flags = kLeft, .group = kBf, .param = &bf},
    {.name = "bf_amc_on_off", .type = kInt, .flags = kLeft, .group = kBf, .param = &bf_amc_on_off},
    {.name = "bf_delay_len", .type = kInt, .flags = kLeft, .group = kBf, .param = &bf_delay_len},
    {.name = "bf_fir_length", .type = kInt, .flags = kLeft, .group = kBf, .param = &bf_fir_length},
    {.name = "bf_nc_on_off", .type = kInt, .flags = kLeft, .group = kBf, .param = &bf_nc_on_off},
    {.name = "bf_type", .type = kInt, .flags = kLeft, .group = kBf, .param = &bf_type},
    {.name = "amc_forgetting_factor", .type = kFloat, .flags = kLeft, .group = kBf, .param = &amc_forgetting_factor},
    {.name = "amc_thr", .type = kFloat, .flags = kLeft, .group = kBf, .param = &amc_thr},
    {.name = "bf_alpha", .type = kFloat, .flags = kLeft, .group = kBf, .param = &bf_alpha},
    {.name = "bf_beta", .type = kFloat, .flags = kLeft, .group = kBf, .param = &bf_beta},
    {.name = "bf_c", .type = kFloat, .flags = kLeft, .group = kBf, .param = &bf_c},
    {.name = "bf_delta", .type = kFloat, .flags = kLeft, .group = kBf, .param = &bf_delta},
    {.name = "bf_mu", .type = kFloat, .flags = kLeft, .group = kBf, .param = &bf_mu},
    {.name = "bf_p", .type = kFloat, .flags = kLeft, .group = kBf, .param = &bf_p},
    {.name = "bf_power_estimate", .type = kFloat, .flags = kLeft, .group = kBf, .param = &bf_power_estimate},
    {.name = "bf_rho", .type = kFloat, .flags = kLeft, .group = kBf, .param = &bf_rho},
    {.name = "nc_thr", .type = kFloat, .flags = kLeft, .group = kBf, .param = &nc_thr},
    // audio
    {.name = "audio_filename", .type = kStr, .flags = kLeft, .group = kAudio, .param = &audio_filename},
    {.name = "audio_play", .type = kInt, .flags = kCmd | kLeft, .group = kAudio, .param = &audio_play},
    {.name = "audio_repeat", .type = kInt, .flags = kLeft, .group = kAudio, .param = &audio_repeat},
    {.name = "audio_playing", .type = kInt, .flags = kLeft, .group = kAudio, .param = &audio_playing},
    {.name = "audio_reset", .type = kInt, .flags = kCmd | kLeft, .group = kAudio, .param = &audio_reset},
    // speech enhancement (kSpeech)
    {.name = "noise_estimation_type", .type = kInt, .flags = kBoth, .group = kSpeech, .param = &noise_estimation_type},
    {.name = "spectral_subtraction", .type = kFloat, .flags = kBoth, .group = kSpeech, .param = &spectral_subtraction},
    {.name = "spectral_type", .type = kInt, .flags = kBoth, .group = kSpeech, .param = &spectral_type},
    // WDRC (peak_detect, wdrc)
    {.name = "attack", .type = kArray, .flags = kBoth, .group = kWdrc, .param = &attack},
    {.name = "g50", .type = kArray, .flags = kBoth, .group = kWdrc, .param = &g50},
    {.name = "g80", .type = kArray, .flags = kBoth, .group = kWdrc, .param = &g80},
    {.name = "knee_low", .type = kArray, .flags = kBoth, .group = kWdrc, .param = &knee_low},
    {.name = "mpo_band", .type = kArray, .flags = kBoth, .group = kWdrc, .param = &mpo_band},
    {.name = "release", .type = kArray, .flags = kBoth, .group = kWdrc, .param = &release},
    {.name = "global_mpo", .type = kFloat, .flags = kBoth, .group = kWdrc, .param = &global_mpo},

    // AFC
    {.name = "afc", .type = kInt, .flags = kBoth, .group = kAfc, .param = &afc},
    {.name = "afc_type", .type = kInt, .flags = kBoth, .group = kAfc, .param = &afc_type},
    {.name = "afc_delay", .type = kFloat, .flags = kBoth, .group = kAfc, .param = &afc_delay},
    {.name = "afc_mu", .type = kFloat, .flags = kBoth, .group = kAfc, .param = &afc_mu},
    {.name = "afc_rho", .type = kFloat, .flags = kBoth, .group = kAfc, .param = &afc_rho},
    {.name = "afc_reset", .type = kFloat, .flags = kCmd | kBoth, .group = kAfc, .param = &afc_reset},

};

// FIXME if kLeft then do we need bf[1] or is bf OK?
std::map<std::string, ParamValue *> pars;

}  // namespace params

#endif  // OSP_PARAMS_HPP__