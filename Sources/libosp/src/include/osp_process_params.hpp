#include "osp_process.hpp"

// set/get params code for class OspProcess

/**
 * @brief Updates parameters
 * Called by OspParse.
 * @param group The group (beamforming, freping, etc) that has updates
 * @param channels 0=left, 1=right, 2=both
 * @return string "success" or "FAILED"
 */
std::string OspProcess::set_params(PGroupType group, int channels) {
    // cout << "Group: " << group << " Chan: " << channels << endl;

    for (int ch = 0; ch < 2; ch++) {
        if (channels == 0 && ch == 1)
            continue;
        if (channels == 1 && ch == 0)
            continue;

        switch (group) {
            case kCommon:
                // Common (or global) parameters.  Not part of any algorithm.
                break;
            case kBf:
                // send_param to beamformer
                bf->set_params(params::bf_mu, params::bf_rho, params::bf_delta, params::bf_alpha, params::bf_beta,
                               params::bf_p, params::bf_c, params::bf_type);
                bf->set_bf_params(params::bf, params::bf_nc_on_off, params::bf_amc_on_off, params::nc_thr,
                                  params::amc_thr, params::amc_forgetting_factor);
                break;

            case kFp: {
                // we reduce the gain temporarily to eliminate pops
                float saved_gain = params::gain_[ch];
                params::gain_[ch] = 1e-50;
                for (int band = 0; band < params::num_bands; band++)
                    fp_[ch][band]->set_params(params::freping_alpha[ch][band], params::freping[ch]);
                usleep(20000);
                params::gain_[ch] = saved_gain;
            } break;
            case kAudio:
                if (params::audio_filename.length() == 0) {
                    std::cout << "Error: empty audio filename" << std::endl;
                    break;
                }
                fplay.set_params(params::audio_filename, params::audio_reset, params::audio_repeat, params::audio_play);
                params::audio_play = 0;
                params::audio_reset = 0;
                break;
            case kSpeech:
                for (int band = 0; band < params::num_bands; band++)
                    noiseMangement[ch][band]->set_param(params::noise_estimation_type[ch], params::spectral_type[ch],
                                                        params::spectral_subtraction[ch]);
                break;
            case kWdrc: {
                float global_mpo_attack = 0;
                float global_mpo_release = 0;
                for (int band = 0; band < params::num_bands; band++) {
                    global_mpo_attack += params::attack[ch][band];
                    global_mpo_release += params::release[ch][band];
                }
                global_mpo_attack /= params::num_bands;
                global_mpo_release /= params::num_bands;

                if (params::num_bands == 10)
                    filterbank[ch]->set(params::aligned[ch]);

                pd_global_mpo[ch]->set_param(global_mpo_attack, global_mpo_release);
                global_mpo[ch]->set_param(1.0, 1.0, 0.0, params::global_mpo[ch]);
                for (int band = 0; band < params::num_bands; band++) {
                    peakDetect[ch][band]->set_param(params::attack[ch][band], params::release[ch][band]);
                    wdrcs[ch][band]->set_param(params::g50[ch][band], params::g80[ch][band], params::knee_low[ch][band],
                                               params::mpo_band[ch][band]);
                }
            } break;
            case kAfc: {
                if (params::afc_reset[ch]) {
                    params::afc_reset[ch] = 0;
                    afcs_[ch]->reset(afc_init_filter, AFC_INIT_FILTER_SIZE);
                }
                size_t afc_delay_in_samples = static_cast<size_t>(32.0f * params::afc_delay[ch]);
                afcs_[ch]->set_delay(afc_delay_in_samples);
                afcs_[ch]->set_afc_on_off(params::afc[ch]);
                // set underlying adaptive filter parameters
                afcs_[ch]->set_params(params::afc_mu[ch], params::afc_rho[ch], defaults::afc_delta, defaults::afc_alpha,
                                      defaults::afc_beta, defaults::afc_p, defaults::afc_c, params::afc_type[ch]);
            } break;
            default:
                break;
        }
    }
    return "success";
}

/**
 * @brief Get the parameters from each module
 * 
 * The values are cached in params::, so the main
 * reason to do this is to verify that the values
 * were actually set.
 */
void OspProcess::get_params() {
    float t1, t2, t3;
    float delta, alpha, beta, p, c;
    size_t delay_len;

    bf->get_params(params::bf_mu, params::bf_rho, params::bf_delta, params::bf_alpha, params::bf_beta, params::bf_p,
                   params::bf_c, params::bf_type);
    bf->get_bf_params(params::bf, params::bf_nc_on_off, params::bf_amc_on_off, params::nc_thr, params::amc_thr,
                      params::amc_forgetting_factor);
    int finished;
    fplay.get_params(finished);
    params::audio_playing = finished ? 0 : 1;

    for (int ch = 0; ch < 2; ch++) {
        global_mpo[ch]->get_param(t1, t2, t3, params::global_mpo[ch]);
        afcs_[ch]->get_delay(delay_len);
        params::afc_delay[ch] = (float)delay_len / 32.0;
        afcs_[ch]->get_afc_on_off(params::afc[ch]);
        afcs_[ch]->get_params(params::afc_mu[ch], params::afc_rho[ch], delta, alpha, beta, p, c, params::afc_type[ch]);

        if (params::num_bands == 10) {
            bool temp;
            filterbank[ch]->get(temp);
            if (temp)
                params::aligned[ch] = 1;
            else
                params::aligned[ch] = 0;
        }

        for (int band = 0; band < params::num_bands; band++) {
            fp_[ch][band]->get_params(params::freping_alpha[ch][band], params::freping[ch]);
            noiseMangement[ch][band]->get_param(params::noise_estimation_type[ch], params::spectral_type[ch],
                                                params::spectral_subtraction[ch]);
            peakDetect[ch][band]->get_param(params::attack[ch][band], params::release[ch][band]);
            wdrcs[ch][band]->get_param(params::g50[ch][band], params::g80[ch][band], params::knee_low[ch][band],
                                       params::mpo_band[ch][band]);
        }
    }
}
