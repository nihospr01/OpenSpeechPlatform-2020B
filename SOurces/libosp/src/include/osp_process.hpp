#ifndef OSP_PROCESS_HPP__
#define OSP_PROCESS_HPP__

// Processes the audio packets.

#include <osp_params.hpp>
#include <fstream>
#include <string>
#include <streambuf>
#include <iostream>
#include <cstdio>
#include <thread>

#include <OSP/subband/noise_management.hpp>
#include <OSP/subband/peak_detect.hpp>
#include <OSP/subband/wdrc.hpp>
#include <OSP/freping/freping.hpp>
#include <OSP/freping/hamming_window64.h>

#include <OSP/resample/resample.hpp>
#include <OSP/resample/48_32_filter.h>
#include <OSP/resample/32_48_filter.h>
#include <OSP/beamformer/beamformer.hpp>
#include <OSP/afc/afc.hpp>
#include <OSP/afc/bandlimited_filter.h>
#include <OSP/afc/prefilter.h>
#include <OSP/afc/afc_init_filter.h>
#include <OSP/multirate_filterbank/tenband_filterbank.h>

#include <OSP/fileio/playfile.h>
#include "OSP/array_utilities/array_utilities.hpp"
#include "sema.hpp"
#include "filter_coef.h"

class OspProcess {
   public:
    OspProcess();
    ~OspProcess();
    std::string set_params(PGroupType group, int channels);
    void get_params(void);
    void process(float **in, float **out, int buf_size);
    void process_channels(int channel);
    void enable(int audio_on, int en_ha);
    void switch_bands(int start);

    resample *down_sample[2];
    resample *up_sample[2];
    beamformer *bf;
    file_play fplay;

    const int max_buffer_in = 48;
    const int max_dwn_buf = 32;
    float *e_n_lr_[2];
    float *y_hat_[2];
    float *e_n_data[2];
    float *sub_data[defaults::max_bands];

   private:
    size_t out_size_;
    float **volatile input, **volatile output;  // needed for threads

    void mix_alpha(float *in, const int channel, const int buf_size);
    void start(int channel);
    void join(int channel);

    rk_sema *thread_mutex[2], *finish_sema[2];
    std::thread *proc_chan_thread[2];
    std::atomic<int> chan_running[2];

    fir_formii *filters[2][6];
    tenband_filterbank *filterbank[2];
    noise_management *noiseMangement[2][defaults::max_bands];
    peak_detect *peakDetect[2][defaults::max_bands];
    wdrc *wdrcs[2][defaults::max_bands];
    afc *afcs_[2];
    wdrc *global_mpo[2];
    peak_detect *pd_global_mpo[2];
    freping *fp_[2][defaults::max_bands];
};

OspProcess::OspProcess() {
    // allocate sub_band space
    for (int i = 0; i < params::num_bands; i++) sub_data[i] = new float[max_dwn_buf];

    bf = new beamformer(params::bf_delay_len, nullptr, params::bf_fir_length, max_dwn_buf, params::bf_type,
                        params::bf_mu, params::bf_delta, params::bf_rho, params::bf_alpha, params::bf_beta,
                        params::bf_p, params::bf_c, params::bf_power_estimate, params::bf, params::bf_nc_on_off,
                        params::bf_amc_on_off, params::nc_thr, params::amc_thr, params::amc_forgetting_factor);

    for (int ch = 0; ch < defaults::output_channels; ch++) {
        down_sample[ch] = new resample(filter_48_32, FILTER_48_32_SIZE, max_buffer_in, 2, 3);
        up_sample[ch] = new resample(filter_32_48, FILTER_32_48_SIZE, max_dwn_buf, 3, 2);
        y_hat_[ch] = new float[max_buffer_in];
        for (int j = 0; j < max_buffer_in; j++) y_hat_[ch][j] = 0;
        e_n_lr_[ch] = new float[max_dwn_buf];
        e_n_data[ch] = new float[max_dwn_buf];
        if (params::num_bands == 10) {
            filterbank[ch] = new tenband_filterbank(max_dwn_buf, params::aligned[ch]);
        }
        for (int band = 0; band < params::num_bands; band++) {
            if (params::num_bands == 6) {
                filters[ch][band] = new fir_formii(subband_filter[band], BAND_FILT_LEN);
            }
            noiseMangement[ch][band] =
                new noise_management(params::noise_estimation_type[ch], params::spectral_type[ch],
                                     params::spectral_subtraction[ch], defaults::samp_freq);
            peakDetect[ch][band] =
                new peak_detect(defaults::samp_freq, params::attack[ch][band], params::release[ch][band]);
            wdrcs[ch][band] = new wdrc(params::g50[ch][band], params::g80[ch][band], params::knee_low[ch][band],
                                       params::mpo_band[ch][band]);
            fp_[ch][band] = new freping(hamming_window64_length, 32, params::freping_alpha[ch][band], hamming_window64,
                                        params::freping[ch]);
        }
        thread_mutex[ch] = new rk_sema;
        rk_sema_init(thread_mutex[ch], 0);
        finish_sema[ch] = new rk_sema;
        rk_sema_init(finish_sema[ch], 0);
        chan_running[ch] = 1;

        // average attack and release time from each band for the global MPO
        float global_mpo_attack = 0;
        float global_mpo_release = 0;
        for (int band = 0; band < params::num_bands; band++) {
            global_mpo_attack += params::attack[ch][band];
            global_mpo_release += params::release[ch][band];
        }
        global_mpo_attack /= params::num_bands;
        global_mpo_release /= params::num_bands;

        pd_global_mpo[ch] = new peak_detect(defaults::samp_freq, global_mpo_attack, global_mpo_release);
        global_mpo[ch] = new wdrc(1.0, 1.0, 0.0, params::global_mpo[ch]);

        // initialize AFC
        size_t afc_delay_in_samples = static_cast<size_t>(32.0f * params::afc_delay[ch]);
        afcs_[ch] =
            new afc(bandlimited_filter, BANDLIMITED_FILTER_SIZE, prefilter, PREFILTER_SIZE, afc_init_filter,
                    AFC_INIT_FILTER_SIZE, max_buffer_in, params::afc_type[ch], params::afc_mu[ch], defaults::afc_delta,
                    params::afc_rho[ch], defaults::afc_alpha, defaults::afc_beta, defaults::afc_p, defaults::afc_c,
                    defaults::afc_power_estimate, afc_delay_in_samples, params::afc[ch]);
    }


    if (defaults::multithreaded) {
        for (int channel = 0; channel < defaults::output_channels; channel++) {
            proc_chan_thread[channel] = new std::thread(&OspProcess::process_channels, this, channel);
            struct sched_param param;
            param.sched_priority = 90;
            if (pthread_setschedparam(proc_chan_thread[channel]->native_handle(), SCHED_FIFO, &param))
                std::cerr << __func__ << "pthread_setschedparam failed" << std::endl;
#ifdef __linux__
            cpu_set_t cpuset;
            CPU_ZERO(&cpuset);
#ifdef __ARM_ARCH
            CPU_SET(channel + 1, &cpuset);
#else
            unsigned num_cpus = std::thread::hardware_concurrency();
            std::cout << "You have " << num_cpus << " CPUs" << std::endl;

            if (num_cpus < 6)
                CPU_SET(channel + 1, &cpuset);
            else
                CPU_SET(2 * channel + 2, &cpuset);  // skip odd because hyperthreading
#endif
            int rc = pthread_setaffinity_np(proc_chan_thread[channel]->native_handle(), sizeof(cpu_set_t), &cpuset);
            if (rc != 0) {
                std::cerr << "Error calling pthread_setaffinity_np: " << rc << "\n";
            }
            char name[16];
            sprintf(name, "OSP: Chan %d", channel);
            rc = pthread_setname_np(proc_chan_thread[channel]->native_handle(), name);
            if (rc != 0)
                std::cerr << "pthread_setname_np failed" << std::endl;
#endif
        }
    }
}


OspProcess::~OspProcess() {
    delete bf;
    for (int ch = 0; ch < defaults::output_channels; ch++) {
        if (defaults::multithreaded) {
            std::cout << "Join thread chan " << ch << std::endl;
            proc_chan_thread[ch]->join();
            std::cout << "Delete thread" << std::endl;
            delete proc_chan_thread[ch];
        }
        delete down_sample[ch];
        delete up_sample[ch];
        delete[] y_hat_[ch];
        delete[] e_n_lr_[ch];
        delete[] e_n_data[ch];
        delete thread_mutex[ch];
        delete finish_sema[ch];
        delete pd_global_mpo[ch];
        delete global_mpo[ch];
        delete afcs_[ch];
        if (params::num_bands == 10)
            delete filterbank[ch];
        for (int band = 0; band < params::num_bands; band++) {
            if (params::num_bands == 6)
                delete filters[ch][band];
            delete noiseMangement[ch][band];
            delete peakDetect[ch][band];
            delete wdrcs[ch][band];
            delete fp_[ch][band];
        }
    }
    for (int i = 0; i < params::num_bands; i++) delete[] sub_data[i];
}

/**
 * @brief Enable audio and HA processing
 * 
 * @param audio_on 0 for off, 1 for on
 * @param en_ha 0 for off, 1 for on, -1 for keep
 */
void OspProcess::enable(int audio_on, int en_ha) {
    osp::audio_enabled = audio_on;
    if (en_ha >= 0) {
        params::en_ha[0] = en_ha;
        params::en_ha[1] = en_ha;
    }
}

void OspProcess::start(int channel) {
    rk_sema_post(thread_mutex[channel]);
    if (!defaults::multithreaded) {
        process_channels(channel);
    }
}

void OspProcess::join(int channel) {
    rk_sema_wait(finish_sema[channel]);
}

void OspProcess::mix_alpha(float *in, const int channel, const int buf_size) {
    float file_read[buf_size];
    float alpha = params::alpha[channel];

    // if there is a file playing, get the next buffer from it
    if (alpha == 1.0) {
        fplay.rtmha_play(buf_size, in, channel);
        return;
    }
    fplay.rtmha_play(buf_size, file_read, channel);
    if (alpha == 0.0)
        return;
    // in[channel] = file*α + in[channel]*(1-α)
    array_multiply_const(file_read, alpha, buf_size);
    array_multiply_const(in, 1.0f - alpha, buf_size);
    array_add_array(in, file_read, buf_size);
}

void OspProcess::process(float **in, float **out, const int buf_size) {

    for (int channel = 0; channel < defaults::output_channels; channel++) {
        float x_n_data[32];  // 48 bytes downsampled

        mix_alpha(in[channel], channel, buf_size);
        // downsample 48->32
        down_sample[channel]->resamp(in[channel], buf_size, x_n_data, &out_size_);

        // beamforming
        for (int i = 0; i < (int)out_size_; i++)
           e_n_lr_[channel][i] = x_n_data[i] - y_hat_[channel][i];
    }
    bf->get_e(e_n_data[0], e_n_data[1], e_n_lr_[0], e_n_lr_[1], out_size_);

    input = in; output = out;
    for (int channel = 0; channel < defaults::output_channels; channel++) {
        if (params::en_ha[channel] && chan_running[channel]) {
            start(channel);
        } else {
            array_multiply_const(in[channel], params::gain_[channel], buf_size);
        }
    }
    bf->update_bf_taps(out_size_);

    for (int channel = 0; channel < defaults::output_channels; channel++) {
        if (params::en_ha[channel] && chan_running[channel]) {
            join(channel);
        } else {
            std::memcpy(out[channel], in[channel], sizeof(float) * buf_size);
        }
    }
}

void OspProcess::process_channels(int channel) {
    float nm_data[max_dwn_buf];
    float pdb_data[max_dwn_buf];
    float wdrc_data[max_dwn_buf];
    float freping_data[max_dwn_buf];
    float s_n_data[max_dwn_buf];
    size_t resamp_out_size;

    do {
        memset(s_n_data, 0, sizeof(s_n_data));
        rk_sema_wait(thread_mutex[channel]);

        // cout << "Processing " << channel << " On CPU " << sched_getcpu() << endl;
        // filter into bands

        if (params::num_bands == 6) {
            for (int i = 0; i < 6; i++) filters[channel][i]->process(e_n_data[channel], sub_data[i], out_size_);
        } else {
            filterbank[channel]->process(e_n_data[channel], sub_data, out_size_);
        }
        // process each band
        for (int j = 0; j < params::num_bands; j++) {
            noiseMangement[channel][j]->speech_enhancement(sub_data[j], out_size_, nm_data);
            peakDetect[channel][j]->get_spl(nm_data, out_size_, pdb_data);
            wdrcs[channel][j]->process(nm_data, pdb_data, out_size_, wdrc_data);
            fp_[channel][j]->freping_proc(wdrc_data, freping_data);
            array_add_array(s_n_data, freping_data, out_size_);
        }
        // e_n_bf_[channel]->get(s_n_data,out_size_);

        // Gain
        array_multiply_const(s_n_data, params::gain_[channel], out_size_);

        // global MPO
        pd_global_mpo[channel]->get_spl(s_n_data, out_size_, pdb_data);
        global_mpo[channel]->process(s_n_data, pdb_data, out_size_, s_n_data);
        // AFC
        // x(n) is x_n_data, s(n) is s_n_data, out_size for their size
        afcs_[channel]->get_y_hat(y_hat_[channel], e_n_lr_[channel], s_n_data, out_size_);
        
        // Done.  Upsample
        up_sample[channel]->resamp(s_n_data, out_size_, output[channel], &resamp_out_size);

        // array_multiply_const(e_n_data[channel], params::gain_[channel], out_size_);
        // up_sample[channel]->resamp(e_n_data[channel], out_size_, output[channel], &resamp_out_size);

        // recording is undocumented.  uncomment when we figure out how to test
        //recorder->record_before(buf_size, input[channel], channel);
        //recorder->rtmha_record(buf_size, input[channel], channel);
        //recorder->record_after(buf_size, input[channel], channel);
        rk_sema_post(finish_sema[channel]);
        if (defaults::multithreaded && osp::running == 0) {
            chan_running[channel] = 0;
            std::cout << "Exited channel " << channel << std::endl;
            break;
        }
    } while (defaults::multithreaded);
};

#include "osp_process_params.hpp"
#endif  // OSP_PROCESS_HPP__