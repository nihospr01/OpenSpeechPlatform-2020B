//
// Created by dsengupt on 9/24/18.
//

#ifndef OSP_CLION_C_OSP_STRUCTURE_H
#define OSP_CLION_C_OSP_STRUCTURE_H

#include <cereal/cereal.hpp>
#include "filter_coef.h"
#include "memory.h"

/**
 * @brief general data structure shared between client and C application
 */

#define NUM_CHANNEL 2

// Default osp user data values

#define D_EN_HA 1
#define D_AFC 3
#define D_REAR_MIC 0
#define D_SAMP_FREQ 48000


/*** Peak detect defaults ***/
#define D_ATTACK_TIME	5	///< Attack time in msec
#define D_RELEASE_TIME	20	///< Release time in msec

/*** WDRC defaults ***/
#define D_G50 0
#define D_G80 0
#define D_KNEE_LOW	45	///< Lower kneepoint in dB SPL. Using the same value for all the bands
#define D_KNEE_HIGH	120	///< Upper kneepoint in dB SPL. Using the same value for all the bands
#define GLOBAL_MPO 120 /// The global MPO

#define D_NOISE_ESTIMATION 0
#define D_SPECTRAL_SUB 0
#define D_SPECTRAL_TYPE 0
#define D_ATTENUATION -20

/*** AFC defaults ***/
#define AFC_ON_OFF 1 /// AFC ON/OFF
#define AFC_RESET 0 /// reset the taps of AFC filter to default values
#define AFC_DELAY 4.6875 /// delay in millisecond for 32k Hz before bandlimited filter (originally, the delay is in number of samples, 150)
#define AFC_MU 0.005 /// step size
#define AFC_RHO 0.985 /// forgetting factor
#define AFC_PE 0 /// power estimate
#define AFC_DELTA 1e-6 /// IPNLMS
#define AFC_ALPHA 0 /// IPNLMS
#define AFC_BETA 5 /// IPNLMS
#define AFC_P 1.5 /// SLMS
#define AFC_C 1e-6 /// SLMS

/*** Beamformer defaults ***/
#define BF_ON_OFF 0
#define BF_TYPE 3
#define BF_MU 0.01
#define BF_RHO 0.985
#define BF_DELTA 1e-6
#define BF_C 1e-3
#define BF_PW 0
#define BF_P 1.3
#define BF_ALPHA 0
#define BF_BETA 150
#define BF_FIR_LENGTH 319
#define BF_DELAY_LEN 160
#define BF_NC_ON_OFF 0
#define BF_AMC_ON_OFF 0
#define NC_THR 1.0
#define AMC_THR 2.0
#define AMC_FORGETTING_FACTOR 0.8

/**
 * Please note that any variables added to this structure must have the same name in the parser.
 */

typedef struct osp_user_data_t {
    int en_ha = D_EN_HA;					///< No operation.  The audio is passed from input to output in the audio callback

    int rear_mics = D_REAR_MIC;				///< Read mics on/off

    float gain = D_ATTENUATION;

    // Amplification parameters

    std::vector<float> g50 = std::vector<float>(NUM_BANDS, D_G50);			///< The gain values at 50 dB SPL input level
    std::vector<float> g80 = std::vector<float>(NUM_BANDS,D_G80 );			///< The gain values at 80 dB SPL input level
    std::vector<float> knee_low = std::vector<float>(NUM_BANDS,D_KNEE_LOW );	///< Lower kneepoints for all bands
    std::vector<float> mpo_band = std::vector<float>(NUM_BANDS,D_KNEE_HIGH );	///< MPO for all bands (upper kneepoints for all bands)
    std::vector<float> attack = std::vector<float>(NUM_BANDS,D_ATTACK_TIME );		///< Attack time for WDRC for all bands
    std::vector<float> release = std::vector<float>(NUM_BANDS,D_RELEASE_TIME );		///< Release time for WDRC for all bands
    float global_mpo = GLOBAL_MPO; /// The global MPO

    // Noise management parameters

    int noise_estimation_type = D_NOISE_ESTIMATION; ///< Choose type of Noise estimation technique
    int spectral_type = D_SPECTRAL_TYPE;
    float spectral_subtraction = D_SPECTRAL_SUB; ///< Spectral subtraction Param

    // Adaptive Feedback management parameters
    int afc = AFC_ON_OFF; /// AFC ON/OFF
    int afc_reset = AFC_RESET; /// reset the taps of AFC filter to default values (not a state, afc_reset is actually a signal)
    int afc_type = D_AFC; ///< AFC Type -1: return y_hat=0, 0: stop adaptation, 1: FXLMS, 2: IPNLMS, 3: SLMS
    float afc_delay = AFC_DELAY;
    float afc_mu = AFC_MU; /// step size
    float afc_rho = AFC_RHO; /// forgetting factor
    float afc_power_estimate = AFC_PE; /// power estimate
    float afc_delta = AFC_DELTA; /// IPNLMS
    float afc_alpha = AFC_ALPHA; /// IPNLMS
    float afc_beta = AFC_BETA; /// IPNLMS
    float afc_p = AFC_P; /// SLMS
    float afc_c = AFC_C; /// SLMS

    // Beamformer parameters
    int bf = BF_ON_OFF;
    int bf_type = BF_TYPE;
    float bf_mu = BF_MU;
    float bf_rho = BF_RHO;
    float bf_delta = BF_DELTA;
    float bf_c = BF_C;
    float bf_power_estimate = BF_PW;
    float bf_p = BF_P;
    float bf_alpha = BF_ALPHA;
    float bf_beta = BF_BETA;
    int bf_fir_length = BF_FIR_LENGTH;
    int bf_delay_len = BF_DELAY_LEN;
    int bf_nc_on_off = BF_NC_ON_OFF;
    int bf_amc_on_off = BF_AMC_ON_OFF;
    float nc_thr = NC_THR;
    float amc_thr = AMC_THR;
    float amc_forgetting_factor = AMC_FORGETTING_FACTOR;


    // File I/O parameters
    float alpha = 0.0f;
    std::string audio_filename;
    std::string audio_recordfile;
    float record_length = 5;
    int audio_reset = 0;
    int audio_repeat = 0;
    int audio_play = 0;
    int record_start = 0;
    int record_stop = 0;

    template<class Archive>
    void serialize(Archive & archive)
    {

        archive( CEREAL_NVP(en_ha),
                 CEREAL_NVP(rear_mics),
                 CEREAL_NVP(gain),
                 CEREAL_NVP(g50),
                 CEREAL_NVP(g80),
                 CEREAL_NVP(knee_low),
                 CEREAL_NVP(mpo_band),
                 CEREAL_NVP(attack),
                 CEREAL_NVP(release),
                 CEREAL_NVP(global_mpo),
                 CEREAL_NVP(noise_estimation_type),
                 CEREAL_NVP(spectral_type),
                 CEREAL_NVP(spectral_subtraction),
                 CEREAL_NVP(afc),
                 CEREAL_NVP(afc_reset),
                 CEREAL_NVP(afc_type),
                 CEREAL_NVP(afc_delay),
                 CEREAL_NVP(afc_mu),
                 CEREAL_NVP(afc_rho),
                 CEREAL_NVP(afc_power_estimate),
                 CEREAL_NVP(bf),
                 CEREAL_NVP(bf_type),
                 CEREAL_NVP(bf_mu),
                 CEREAL_NVP(bf_rho),
                 CEREAL_NVP(bf_delta),
                 CEREAL_NVP(bf_c),
                 CEREAL_NVP(bf_power_estimate),
                 CEREAL_NVP(bf_p),
                 CEREAL_NVP(bf_alpha),
                 CEREAL_NVP(bf_beta),
                 CEREAL_NVP(bf_fir_length),
                 CEREAL_NVP(bf_delay_len),
                 CEREAL_NVP(bf_nc_on_off),
                 CEREAL_NVP(bf_amc_on_off),
                 CEREAL_NVP(nc_thr),
                 CEREAL_NVP(amc_thr),
                 CEREAL_NVP(amc_forgetting_factor),
                 CEREAL_NVP(alpha),
                 CEREAL_NVP(audio_filename),
                 CEREAL_NVP(audio_reset),
                 CEREAL_NVP(audio_play),
                 CEREAL_NVP(audio_repeat),
                 CEREAL_NVP(audio_recordfile),
                 CEREAL_NVP(record_start),
                 CEREAL_NVP(record_stop),
                 CEREAL_NVP(record_length));

        // serialize things by passing them to the archive

    }

} osp_user_data;





#endif //OSP_CLION_C_OSP_STRUCTURE_H
