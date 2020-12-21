#ifndef OSP_DEFAULTS_HPP__
#define OSP_DEFAULTS_HPP__ 
#include <string>

/**
 * @brief Namespace for the default values
 * 
 * These values cannot be changed at runtime.
 * Some may be set by command line arguments.
 * 
 */

namespace defaults
{
    int input_device = -1;
    int output_device = -1; 
#ifdef __APPLE__
    int input_channels = 1;
#else
    int input_channels = 2;
#endif
    int output_channels = 2;
    bool multithreaded = false;
    int default_num_bands = 6;
    const int max_bands = 10;  // the maximum number of bands
    const int samp_freq = 48000;

    // AFC
    const float afc_delta = 1e-6;
    const float afc_alpha = 0;
    const float afc_beta = 5;
    const float afc_c = 1e-6;
    const float afc_p = 1.5;
    const float afc_power_estimate = 0;

    std::string conf_filename;
    std::string default_json(OSP_CONFIG, OSP_CONFIG + OSP_CONFIG_len);
    std::string default10_json(OSP_CONFIG10, OSP_CONFIG10 + OSP_CONFIG10_len);
} // namespace defaults

#endif  // OSP_DEFAULTS_HPP__