//
// Created by dsengupt on 9/24/18.
//

#ifndef OSP_CLION_C_OSP_STRUCTURE_H
#define OSP_CLION_C_OSP_STRUCTURE_H

#include <cereal/cereal.hpp>
#include "filter_coef.h"
#include "memory.h"


struct DEFAULTS {

    // Default osp user data values
    static const int NUM_CHANNEL = 2;
    static const int EN_HA = 1;
    static const int AFC = 3;
    static const int REAR_MIC = 0;
    static const int SAMP_FREQ = 48000;

    /*** Peak detect defaults ***/
    static const int ATTACK_TIME = 5;    ///< Attack time in msec
    static const int RELEASE_TIME = 20;    ///< Release time in msec

    /*** WDRC defaults ***/
    static const int G50 = 0;
    static const int G80 = 0;
    static const int KNEE_LOW = 45;    ///< Lower kneepoint in dB SPL. Using the same value for all the bands
    static const int KNEE_HIGH = 120;    ///< Upper kneepoint in dB SPL. Using the same value for all the bands
    static const int GLOBAL_MPO = 120; /// The global MPO

    static const int NOISE_ESTIMATION = 0;
    static const int SPECTRAL_SUB = 0;
    static const int SPECTRAL_TYPE = 0;
    static const int ATTENUATION = -20;

    /*** Freping defaults ***/
    static const int FREPING_ON_OFF = 0;
    static constexpr float FREPING_ALPHA = 0;

    /*** AFC defaults ***/
    struct AFC {
        static const int ON_OFF = 1; /// AFC ON/OFF
        static const int RESET = 0; /// reset the taps of AFC filter to default values
        static constexpr float DELAY = 4.6875; /// delay in millisecond for 32k Hz before bandlimited filter (originally, the delay is in number of samples, 150)
        static constexpr float MU = 0.005; /// step size
        static constexpr float RHO = 0.9; /// forgetting factor, changed to make convergence faster for SDSU.
        static const int PE = 0; /// power estimate
        static constexpr float DELTA = 1e-6; /// IPNLMS
        static const int ALPHA = 0; /// IPNLMS
        static const int BETA = 5; /// IPNLMS
        static constexpr float P = 1.5; /// SLMS
        static constexpr float C = 1e-6; /// SLMS
    };

    /*** Beamformer defaults ***/
    struct BF {
        static const int ON_OFF = 0;
        static const int TYPE = 3;
        static constexpr float MU = 0.01;
        static constexpr float RHO = 0.985;
        static constexpr float DELTA = 1e-6;
        static constexpr float C = 1e-3;
        static const int PW = 0;
        static constexpr float P = 1.3;
        static const int ALPHA = 0;
        static const int BETA = 150;
        static const int FIR_LENGTH = 319;
        static const int DELAY_LEN = 160;
        static const int NC_ON_OFF = 0;
        static const int AMC_ON_OFF = 0;
        static constexpr float NC_THR = 1.0;
        static constexpr float AMC_THR = 2.0;
        static constexpr float AMC_FORGETTING_FACTOR = 0.8;
    };
};


/**
 * Please note that any variables added to this structure must have the same name in the parser.
 */

/**
 * @brief general data structure shared between client and C application
 */
typedef struct osp_user_data_t {
    int en_ha = DEFAULTS::EN_HA;					///< No operation.  The audio is passed from input to output in the audio callback
    int rear_mics = DEFAULTS::REAR_MIC;				///< Read mics on/off
    float gain = DEFAULTS::ATTENUATION;

    // Amplification parameters
    std::vector<float> g50 = std::vector<float>(SIX_BANDS, DEFAULTS::G50);			///< The gain values at 50 dB SPL input level
    std::vector<float> g80 = std::vector<float>(SIX_BANDS,DEFAULTS::G80 );			///< The gain values at 80 dB SPL input level
    std::vector<float> knee_low = std::vector<float>(SIX_BANDS,DEFAULTS::KNEE_LOW );	///< Lower kneepoints for all bands
    std::vector<float> mpo_band = std::vector<float>(SIX_BANDS,DEFAULTS::KNEE_HIGH );	///< MPO for all bands (upper kneepoints for all bands)
    std::vector<float> attack = std::vector<float>(SIX_BANDS,DEFAULTS::ATTACK_TIME );		///< Attack time for WDRC for all bands
    std::vector<float> release = std::vector<float>(SIX_BANDS,DEFAULTS::RELEASE_TIME );		///< Release time for WDRC for all bands
    float global_mpo = DEFAULTS::GLOBAL_MPO; /// The global MPO

    // Freping parameter
    int freping = DEFAULTS::FREPING_ON_OFF;
    std::vector<float> freping_alpha = std::vector<float>(SIX_BANDS,DEFAULTS::FREPING_ALPHA);

    // Noise management parameters

    int noise_estimation_type = DEFAULTS::NOISE_ESTIMATION; ///< Choose type of Noise estimation technique
    int spectral_type = DEFAULTS::SPECTRAL_TYPE;
    float spectral_subtraction = DEFAULTS::SPECTRAL_SUB; ///< Spectral subtraction Param

    // Adaptive Feedback management parameters
    int afc = DEFAULTS::AFC::ON_OFF; /// AFC ON/OFF
    int afc_reset = DEFAULTS::AFC::RESET; /// reset the taps of AFC filter to default values (not a state, afc_reset is actually a signal)
    int afc_type = DEFAULTS::AFC; ///< AFC Type -1: return y_hat=0, 0: stop adaptation, 1: FXLMS, 2: IPNLMS, 3: SLMS
    float afc_delay = DEFAULTS::AFC::DELAY;
    float afc_mu = DEFAULTS::AFC::MU; /// step size
    float afc_rho = DEFAULTS::AFC::RHO; /// forgetting factor
    float afc_power_estimate = DEFAULTS::AFC::PE; /// power estimate
    float afc_delta = DEFAULTS::AFC::DELTA; /// IPNLMS
    float afc_alpha = DEFAULTS::AFC::ALPHA; /// IPNLMS
    float afc_beta = DEFAULTS::AFC::BETA; /// IPNLMS
    float afc_p = DEFAULTS::AFC::P; /// SLMS
    float afc_c = DEFAULTS::AFC::C; /// SLMS

    // Beamformer parameters
    int bf = DEFAULTS::BF::ON_OFF;
    int bf_type = DEFAULTS::BF::TYPE;
    float bf_mu = DEFAULTS::BF::MU;
    float bf_rho = DEFAULTS::BF::RHO;
    float bf_delta = DEFAULTS::BF::DELTA;
    float bf_c = DEFAULTS::BF::C;
    float bf_power_estimate = DEFAULTS::BF::PW;
    float bf_p = DEFAULTS::BF::P;
    float bf_alpha = DEFAULTS::BF::ALPHA;
    float bf_beta = DEFAULTS::BF::BETA;
    int bf_fir_length = DEFAULTS::BF::FIR_LENGTH;
    int bf_delay_len = DEFAULTS::BF::DELAY_LEN;
    int bf_nc_on_off = DEFAULTS::BF::NC_ON_OFF;
    int bf_amc_on_off = DEFAULTS::BF::AMC_ON_OFF;
    float nc_thr = DEFAULTS::BF::NC_THR;
    float amc_thr = DEFAULTS::BF::AMC_THR;
    float amc_forgetting_factor = DEFAULTS::BF::AMC_FORGETTING_FACTOR;


    // File I/O parameters
    float alpha = 0.0f;
    std::string audio_filename;
    std::string audio_recordfile = "sample.wav";
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
                 CEREAL_NVP(freping),
                 CEREAL_NVP(freping_alpha),
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
