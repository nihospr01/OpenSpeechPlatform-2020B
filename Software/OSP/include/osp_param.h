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
                 CEREAL_NVP(afc_power_estimate)); // serialize things by passing them to the archive
    }

} osp_user_data;





#endif //OSP_CLION_C_OSP_STRUCTURE_H
