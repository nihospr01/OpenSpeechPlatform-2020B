//
// Created by Dhiman Sengupta on 10/1/18.
//

#ifndef OSP_CLION_CXX_OSP_PROCESS_H
#define OSP_CLION_CXX_OSP_PROCESS_H

#ifdef __linux__
#include <sched.h>
#endif
#include <iostream>
#include <unistd.h>
#include <thread>
#include <OSP/circular_buffer/circular_buffer.hpp>
#include <OSP/array_utilities/array_utilities.hpp>
#include <OSP/subband/noise_management.hpp>
#include <OSP/subband/peak_detect.hpp>
#include <OSP/subband/wdrc.hpp>
#include <OSP/afc/afc.hpp>
#include <OSP/afc/bandlimited_filter.h>
#include <OSP/afc/prefilter.h>
#include <OSP/afc/afc_init_filter.h>
#include <OSP/beamformer/beamformer.hpp>
#include <OSP/resample/resample.hpp>
#include <OSP/resample/48_32_filter.h>
#include <OSP/resample/32_48_filter.h>
#include <OSP/fileio/playfile.h>
#include <OSP/fileio/file_record.h>
//#include "sema.hpp"
#include "osp_param.h"
#include "filter_coef.h"


/**
 * @brief OSP Process Class
 * @details This class groups all of the different OSP library calls in one place.
 * This is more of an example way of using the different parts of the OSP libraries to implement a hearing aid algorithm
 * including adaptive feedback cancellation (AFC), wide dynamic range compression (WDRC) and speech enhancement.
 * A wrapper class to initialize all the modules of a master hearing aid (MHA).
 */
class osp_process{

public:
    /**
     * @brief OSP process constructor
     * @param[in] samp_freq The sampling rate of the input signal
     * @param[in] max_buffer_in The max buffer size of the input signal
     * @param[in] user_data A general data structure shared between client and C/C++ application
     * @param[in] multithread A flag enabling the multi-threading feature
     * @see osp_user_data_t
     */
    explicit osp_process(float samp_freq, size_t max_buffer_in, osp_user_data *user_data, bool multithread){
        this->multithread = multithread;
        if (samp_freq == 48000) {
            max_dwn_buf = max_buffer_in * 2;
            max_dwn_buf = max_dwn_buf / 3;
        } else {
            std::cout << "Error setting up osp process" << std::endl;
            return;
        }
        /// Beamforming
        beamformer_ = new beamformer(user_data[0].bf_delay_len, nullptr, user_data[0].bf_fir_length, max_dwn_buf,
                                     user_data[0].bf_type, user_data[0].bf_mu, user_data[0].bf_delta, user_data[0].bf_rho,
                                     user_data[0].bf_alpha, user_data[0].bf_beta, user_data[0].bf_p, user_data[0].bf_c,
                                     user_data[0].bf_power_estimate, user_data[0].bf,user_data[0].bf_nc_on_off,
                                     user_data[0].bf_amc_on_off,user_data[0].nc_thr,user_data[0].amc_thr,user_data[0].amc_forgetting_factor);
        for(int channel = 0; channel < DEFAULTS::NUM_CHANNEL; channel++) {
            e_n_bf_[channel] = new circular_buffer(max_dwn_buf+BAND_FILT_LEN, 0.0f);
            alpha[channel] = user_data[channel].alpha;
            en_ha[channel] = user_data[channel].en_ha;
            finish[channel] = true;
            gain[channel] = powf(10.0f, user_data[channel].gain / 20.0f);
            down_sample[channel] = new resample(filter_48_32, FILTER_48_32_SIZE, max_buffer_in, 2, 3);
            up_sample[channel] = new resample(filter_32_48, FILTER_32_48_SIZE, max_dwn_buf, 3, 2);

            for (int band = 0; band < NUM_BANDS; band++) {
                filters[channel][band] = new filter(subband_filter[band], BAND_FILT_LEN, e_n_bf_[channel], max_dwn_buf);
                noiseMangement[channel][band] = new noise_management(user_data[channel].noise_estimation_type, user_data[channel].spectral_type,
                                                 user_data[channel].spectral_subtraction, samp_freq);
                peakDetect[channel][band] = new peak_detect(samp_freq, user_data[channel].attack[band], user_data[channel].release[band]);
                wdrcs[channel][band] = new wdrc(user_data[channel].g50[band], user_data[channel].g80[band], user_data[channel].knee_low[band], user_data[channel].mpo_band[band]);
            }
            // average attack and release time from each band for the global MPO
            float global_mpo_attack = 0;
            float global_mpo_release = 0;
            for (int band = 0; band < NUM_BANDS; band++) {
                global_mpo_attack = global_mpo_attack + user_data[channel].attack[band];
                global_mpo_release = global_mpo_release + user_data[channel].release[band];
            }
            global_mpo_attack = global_mpo_attack / NUM_BANDS;
            global_mpo_release = global_mpo_release / NUM_BANDS;

            pd_global_mpo[channel] = new peak_detect(samp_freq,global_mpo_attack,global_mpo_release);
            global_mpo[channel] = new wdrc(1.0,1.0,0.0,user_data[channel].global_mpo);
            thread_mutex[channel] = new rk_sema;
            rk_sema_init(thread_mutex[channel], 0);
            finish_sema[channel] = new rk_sema;
            rk_sema_init(finish_sema[channel], 0);
            // initialize AFC
            size_t afc_delay_in_samples = static_cast<size_t>(32.0f*user_data[channel].afc_delay);
            afcs_[channel] = new afc(bandlimited_filter,BANDLIMITED_FILTER_SIZE,prefilter,PREFILTER_SIZE,afc_init_filter,AFC_INIT_FILTER_SIZE,
                               max_buffer_in,user_data[channel].afc_type,user_data[channel].afc_mu,user_data[channel].afc_delta,user_data[channel].afc_rho,
                               user_data[channel].afc_alpha,user_data[channel].afc_beta,user_data[channel].afc_p,user_data[channel].afc_c,user_data[channel].afc_power_estimate,
                                     afc_delay_in_samples,user_data[channel].afc);
            y_hat_[channel] = new float[max_buffer_in];
            e_n_lr_[channel] = new float[max_dwn_buf];
            for(size_t j=0;j<max_buffer_in;j++){
                y_hat_[channel][j] = 0;
            }
            if(multithread) {
		proc_chan_thread[channel] = new std::thread(&osp_process::process_channels, this, channel);
		struct sched_param param; int pol;
		int s = pthread_getschedparam(proc_chan_thread[channel]->native_handle(), &pol, &param);
		if (s != 0)
		    std::cerr << __func__ << "pthread_getschedparam failed" << std::endl;
		param.sched_priority = 2;
		s = pthread_setschedparam(proc_chan_thread[channel]->native_handle(), SCHED_FIFO, &param);
		if (s != 0)
		    std::cerr << __func__ << "pthread_setschedparam failed" << std::endl;
#ifdef __linux__
                cpu_set_t cpuset;
                CPU_ZERO(&cpuset);
                CPU_SET(channel+1, &cpuset);
                int rc = pthread_setaffinity_np(proc_chan_thread[channel]->native_handle(), sizeof(cpu_set_t), &cpuset);
                if (rc != 0) {
                    std::cerr << "Error calling pthread_setaffinity_np: " << rc << "\n";
                }
                std::stringstream name;
                name << "OSP: Chan " << channel;
                rc = pthread_setname_np(proc_chan_thread[channel]->native_handle(), name.str().c_str());
                if (rc != 0)
                    std::cerr << "pthread_setname_np failed" << std::endl;
#endif
            } else {
		struct sched_param param; int pol;
		int s = pthread_getschedparam(pthread_self(), &pol, &param);
		if (s != 0)
		    std::cerr << __func__ << "pthread_getschedparam failed" << std::endl;
		param.sched_priority = 2;
		s = pthread_setschedparam(pthread_self(), SCHED_FIFO, &param);
		if (s != 0)
		    std::cerr << __func__ << "pthread_setschedparam failed" << std::endl;
	    }
        }
    };

    /**
     * @brief OSP process destructor
     */
    ~osp_process(){
        delete beamformer_;
        for(int channel = 0; channel < DEFAULTS::NUM_CHANNEL; channel++){
            if(multithread) {
		proc_chan_thread[channel]->join();
                delete proc_chan_thread[channel];
            }
            delete up_sample[channel];
            delete down_sample[channel];
            delete[] y_hat_[channel];
            delete[] e_n_lr_[channel];
            delete e_n_bf_[channel];
            delete thread_mutex[channel];
            for(int band = 0; band < NUM_BANDS; band++){
                delete filters[channel][band];
                delete noiseMangement[channel][band];
                delete peakDetect[channel][band];
                delete wdrcs[channel][band];
            }
            delete pd_global_mpo[channel];
            delete global_mpo[channel];
        }
    };

    /**
     * @breif A function to amplify the input signal for all the channels
     * @param[in] in A pointer to the multi-channel input signal
     * @param[out] out A pointer to the multi-channel output signal
     * @param[in] buf_size The length of the input signal which is given for this processing, i.e., the frame length
     */
    void process(float** in, float** out, size_t buf_size){
//        auto start = std::chrono::high_resolution_clock::now();
        this->buf_size = buf_size;

        for(int channel = 0; channel < DEFAULTS::NUM_CHANNEL; channel++) {
            float x_n_data[max_dwn_buf];
            float file_read[48];
            f1.rtmha_play(buf_size,file_read,channel);
            array_multiply_const(file_read, alpha[channel], buf_size);
            array_multiply_const(in[channel], 1.0f - alpha[channel], buf_size);
            array_add_array(in[channel], file_read, buf_size);
            down_sample[channel]->resamp(in[channel], buf_size, x_n_data, &out_size_);
            for(size_t i=0;i<out_size_;i++){
                e_n_lr_[channel][i] = x_n_data[i] - y_hat_[channel][i]; // compute e(n)
            }
        }
        float e_n_data[2][max_dwn_buf];
        /// Beamforming start
        beamformer_->get_e(e_n_data[0],e_n_data[1],e_n_lr_[0],e_n_lr_[1],out_size_);
        for(int channel = 0; channel < DEFAULTS::NUM_CHANNEL; channel++) {
            e_n_bf_[channel]->set(e_n_data[channel],out_size_);
        }
        /// Beamforming end
        input = in;
        output = out;
        for(int j = 0; j < DEFAULTS::NUM_CHANNEL; j++) {
            if(en_ha[j]) {
                start(j);
            }
            else{
                array_multiply_const(in[j], gain[j], buf_size);
            }
        }
        beamformer_->update_bf_taps(out_size_);
        for(int i = 0; i < DEFAULTS::NUM_CHANNEL; i++){
            if(en_ha[i]) {
                join(i);
            }
            else{
                std::memcpy(out[i], in[i], sizeof(float)*buf_size);
            }

        }
//        auto elapsed = std::chrono::high_resolution_clock::now() - start;
//        std::cout << std::chrono::duration_cast<std::chrono::microseconds>(elapsed).count() << std::endl;

    };

    /**
     * @brief Setting all parameters for MHA
     * @param[in] user_data A general data structure shared between client and C/C++ application
     * @see osp_user_data_t
     */
    void set_params(osp_user_data *user_data){
        beamformer_->set_params(user_data[0].bf_mu,user_data[0].bf_rho,user_data[0].bf_delta,user_data[0].bf_alpha,
                                user_data[0].bf_beta,user_data[0].bf_p,user_data[0].bf_c,user_data[0].bf_type);
        beamformer_->set_bf_params(user_data[0].bf,user_data[0].bf_nc_on_off,user_data[0].bf_amc_on_off,user_data[0].nc_thr,user_data[0].amc_thr,user_data[0].amc_forgetting_factor);
        for(int channel = 0; channel < DEFAULTS::NUM_CHANNEL; channel++) {
            alpha[channel] = user_data[channel].alpha;
            gain[channel] = powf(10.0f, user_data[channel].gain / 20.0f);
            en_ha[channel] = user_data[channel].en_ha;

            for (int band = 0; band < NUM_BANDS; band++) {
                noiseMangement[channel][band]->set_param(user_data[channel].noise_estimation_type, user_data[channel].spectral_type, user_data[channel].spectral_subtraction);

                peakDetect[channel][band]->set_param(user_data[channel].attack[band], user_data[channel].release[band]);

                wdrcs[channel][band]->set_param(user_data[channel].g50[band], user_data[channel].g80[band], user_data[channel].knee_low[band], user_data[channel].mpo_band[band]);

            }
            // average attack and release time from each band for the global MPO
            float global_mpo_attack = 0;
            float global_mpo_release = 0;
            for (int band = 0; band < NUM_BANDS; band++) {
                global_mpo_attack = global_mpo_attack + user_data[channel].attack[band];
                global_mpo_release = global_mpo_release + user_data[channel].release[band];
            }
            global_mpo_attack = global_mpo_attack / NUM_BANDS;
            global_mpo_release = global_mpo_release / NUM_BANDS;
            pd_global_mpo[channel]->set_param(global_mpo_attack,global_mpo_release);
            global_mpo[channel]->set_param(1.0,1.0,0.0,user_data[channel].global_mpo);
            afcs_[channel]->set_params(user_data[channel].afc_mu,user_data[channel].afc_rho,user_data[channel].afc_delta,user_data[channel].afc_alpha,
                                 user_data[channel].afc_beta,user_data[channel].afc_p,user_data[channel].afc_c,user_data[channel].afc_type);
            size_t afc_delay_in_samples = static_cast<size_t>(32.0f*user_data[channel].afc_delay);
            afcs_[channel]->set_delay(afc_delay_in_samples);
            afcs_[channel]->set_afc_on_off(user_data[channel].afc);
            if(user_data[channel].afc_reset){
                user_data[channel].afc_reset = 0;
                afcs_[channel]->reset(afc_init_filter,AFC_INIT_FILTER_SIZE);
            }

        }

        auto leftUserData = user_data[0];
        recorder->set_params(leftUserData.record_start,leftUserData.record_stop,leftUserData.record_length,leftUserData.audio_recordfile.c_str());
        if(!leftUserData.audio_filename.empty()){
            f1.set_params(leftUserData.audio_filename.c_str(), leftUserData.audio_reset,
                          leftUserData.audio_repeat, leftUserData.audio_play );
        }
        if(!leftUserData.audio_recordfile.empty()){

        }

    };

    /**
     * @brief Getting all parameters for MHA
     * @param[in] user_data A general data structure shared between client and C/C++ application
     * @see osp_user_data_t
     */
    void get_params(osp_user_data *user_data){
        beamformer_->get_params(user_data[0].bf_mu,user_data[0].bf_rho,user_data[0].bf_delta,user_data[0].bf_alpha,
                                user_data[0].bf_beta,user_data[0].bf_p,user_data[0].bf_c,user_data[0].bf_type);
        beamformer_->get_bf_params(user_data[0].bf,user_data[0].bf_nc_on_off,user_data[0].bf_amc_on_off,user_data[0].nc_thr,user_data[0].amc_thr,user_data[0].amc_forgetting_factor);
        for(int channel = 0; channel < DEFAULTS::NUM_CHANNEL; channel++) {
            user_data[channel].alpha = alpha[channel];
            user_data[channel].gain = log10f(gain[channel]) * 20.0f;
            user_data[channel].en_ha = en_ha[channel];

            for (int band = 0; band < NUM_BANDS; band++) {
                noiseMangement[channel][band]->get_param(user_data[channel].noise_estimation_type, user_data[channel].spectral_type, user_data[channel].spectral_subtraction);

                peakDetect[channel][band]->get_param(user_data[channel].attack[band], user_data[channel].release[band]);

                wdrcs[channel][band]->get_param(user_data[channel].g50[band], user_data[channel].g80[band], user_data[channel].knee_low[band], user_data[channel].mpo_band[band]);
            }
            float g50, g80, knee_low;
            global_mpo[channel]->get_param(g50,g80,knee_low,user_data[channel].global_mpo);
            afcs_[channel]->get_params(user_data[channel].afc_mu,user_data[channel].afc_rho,user_data[channel].afc_delta,user_data[channel].afc_alpha,
                                 user_data[channel].afc_beta,user_data[channel].afc_p,user_data[channel].afc_c,user_data[channel].afc_type);
            size_t afc_delay_in_samples;
            afcs_[channel]->get_delay(afc_delay_in_samples);
            user_data[channel].afc_delay = afc_delay_in_samples/32.0f;
            afcs_[channel]->get_afc_on_off(user_data[channel].afc);
            user_data[channel].afc_reset = 0; // not a state, afc_reset is actually a signal
            recorder->get_params(user_data[channel].record_length);

        }

    };

    /**
     * @brief A function to perform MHA processing on the input signal for all the channels
     * @param[in] channel The channel number
     */
    void process_channels(int channel){
        float sub_data[max_dwn_buf];
        float nm_data[max_dwn_buf];
        float pdb_data[max_dwn_buf];
        float wdrc_data[max_dwn_buf];
        float s_n_data[max_dwn_buf];

        size_t resamp_out_size;
        do{
            memset(s_n_data, 0, sizeof(s_n_data));
            rk_sema_wait(thread_mutex[channel]);

            for (int j = 0; j < NUM_BANDS; j++) {
                filters[channel][j]->cirfir(sub_data, out_size_);
                noiseMangement[channel][j]->speech_enhancement(sub_data, out_size_, nm_data);
                peakDetect[channel][j]->get_spl(nm_data, out_size_, pdb_data);
                wdrcs[channel][j]->process(nm_data, pdb_data, out_size_, wdrc_data);
                array_add_array(s_n_data, wdrc_data, out_size_);
            }

	    //e_n_bf_[channel]->get(s_n_data,out_size_);
            /// Gain
            array_multiply_const(s_n_data, gain[channel], out_size_);
            /// global MPO
            pd_global_mpo[channel]->get_spl(s_n_data,out_size_,pdb_data);
            global_mpo[channel]->process(s_n_data,pdb_data,out_size_,s_n_data);
            /// AFC begin
            // x(n) is x_n_data, s(n) is s_n_data, out_size for their size
            afcs_[channel]->get_y_hat(y_hat_[channel],e_n_lr_[channel],s_n_data,out_size_);
            /// AFC end
            up_sample[channel]->resamp(s_n_data, out_size_, output[channel], &resamp_out_size);
            recorder->record_before(buf_size, input[channel], channel);
            recorder->rtmha_record(buf_size, input[channel], channel);
            recorder->record_after(buf_size, input[channel], channel);
            rk_sema_post(finish_sema[channel]);

        }while(multithread);


    };

    /**
     * @brief A function which takes the input signal and starts the MHA processing
     * @param[in] channel The channel number
     * @param[in] in The input signal for this channel
     */
    void start(int channel){
        rk_sema_post(thread_mutex[channel]);
        if(!multithread){
            this->process_channels(channel);
        }
    }

    /**
     * @brief A function which returns the output signal after the MHA processing
     * @param[in] channel The channel number
     * @param[out] out The output signal for this channel
     */
    void join(int channel){
        rk_sema_wait(finish_sema[channel]);
    }

private:
    float ** volatile input, ** volatile output;
    volatile bool finish[DEFAULTS::NUM_CHANNEL];
    size_t buf_size;
    rk_sema *thread_mutex[DEFAULTS::NUM_CHANNEL], *finish_sema[DEFAULTS::NUM_CHANNEL];
    std::thread *proc_chan_thread[DEFAULTS::NUM_CHANNEL];
    bool multithread;
    float gain[DEFAULTS::NUM_CHANNEL];
    int en_ha[DEFAULTS::NUM_CHANNEL];
    resample* up_sample[DEFAULTS::NUM_CHANNEL];
    resample* down_sample[DEFAULTS::NUM_CHANNEL];
    filter *filters[DEFAULTS::NUM_CHANNEL][NUM_BANDS];
    noise_management *noiseMangement[DEFAULTS::NUM_CHANNEL][NUM_BANDS];
    peak_detect *peakDetect[DEFAULTS::NUM_CHANNEL][NUM_BANDS];
    wdrc *wdrcs[DEFAULTS::NUM_CHANNEL][NUM_BANDS];
    size_t max_dwn_buf;
    afc* afcs_[DEFAULTS::NUM_CHANNEL];
    beamformer* beamformer_;
    size_t out_size_;
    float* e_n_lr_[DEFAULTS::NUM_CHANNEL];
    circular_buffer* e_n_bf_[DEFAULTS::NUM_CHANNEL];
    float* y_hat_[DEFAULTS::NUM_CHANNEL];
    wdrc *global_mpo[DEFAULTS::NUM_CHANNEL];
    peak_detect *pd_global_mpo[DEFAULTS::NUM_CHANNEL];
    float alpha[DEFAULTS::NUM_CHANNEL];
    file_play f1;
    file_record* recorder = new file_record;

};

#endif //OSP_CLION_CXX_OSP_PROCESS_H
