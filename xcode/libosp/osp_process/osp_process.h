/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

/**
 * @brief This file groups all of the different OSP library calls in one place.
 * This is more of an example way of using the different parts of the OSP libraries
 * to implement a hearing aid algorith with MHA and AFC all in one place.
 */

#ifndef OSP_PROCESS_H__
#define OSP_PROCESS_H__

#include "coeffs.h"
#include "constants.h"

/**
 * @brief Wrapper function to initalize all the modules of Master Hearing Aid (MHA)
 * 
 * @param frame_size The number of samples in a frame. i.e. the number of samples to process
 * @param sample_rate The sample rate at which all MHA processing will be done
 * @param afc_adaptation_type The type of AFC adaptation
 * @return 0 if successful initialization. -1 otherwise
 */
int osp_init(unsigned int frame_size, int sample_rate, unsigned char afc_adaptation_type);

/**
 * @brief Function to perform Master Hearing Aid (MHA) processing on the input signal for both channels
 * 
 * If STEREO is false, only the right channel is processed
 * 
 * @see osp_user_data
 * @param osp The instance of the osp_user_data structure that was initialized in osp_data_init. This contains all the MHA parameters.
 * @param x_nL Pointer to the array containing left channel input
 * @param x_nR Pointer to the array containing right channel input
 * @param outL Pointer to the array to store the output of MHA processing on left channel input
 * @param outR Pointer to the array to store the output of MHA processing on right channel input
 * @param len The length of the input signal e_n that is given for processing. i.e. frame length.
 */
void osp_process_audio(osp_user_data *osp,
					float *x_nL, float *x_nR,
					float *outL, float *outR,
					size_t len);

/**
 * @brief Function to get the number of Sub-Bands
 *
 * @return int Number of Sub-Bands give by NUM_BANDS
 */
unsigned int osp_get_num_bands();

/**
 * @brief Wrapper function to free all the modules of Master Hearing Aid (MHA)
 *
 */
void osp_close();

/**
 * @brief Function to dump AFC filter taps for both left and right channels
 *
 * File names can be changed by changing AFC_FILTER_TAP_FILE_L, AFC_FILTER_TAP_FILE_R in constants.h
 * 
 */
void osp_dump_afc_filter();

/**
 * @brief Helper function for initializing Master Hearing Aid (MHA) parameters
 *
 * @see osp_user_data
 * @see osp_data_set_gain, osp_data_set_nh, osp_data_set_n2 ,osp_data_set_n4 ,osp_data_set_s2
 * @param user_data Pointer to an osp_user_data instance which will be allocated in the function
 */
void osp_data_init(osp_user_data *user_data);

/**
 * @brief Helper function for initializing gain parameters for WDRC with a constant gain
 *
 * @see osp_user_data
 * @see osp_data_set_gain, osp_data_set_nh, osp_data_set_n2 ,osp_data_set_n4 ,osp_data_set_s2
 * @param user_data Pointer to the instance of osp_user_data initialized by osp_data_init
 * @param gain The constant gain value
 */
void osp_data_set_gain(osp_user_data *user_data, int gain);

/**
 * @brief Helper function for initializing gain parameters for WDRC with NH audiogram
 *
 * @see osp_user_data
 * @see osp_data_set_gain, osp_data_set_nh, osp_data_set_n2 ,osp_data_set_n4 ,osp_data_set_s2
 * @param user_data Pointer to the instance of osp_user_data initialized by osp_data_init
 */
void osp_data_set_nh(osp_user_data *user_data);

/**
 * @brief Helper function for initializing gain parameters for WDRC with N2 audiogram
 *
 * @see osp_user_data
 * @see osp_data_set_gain, osp_data_set_nh, osp_data_set_n2 ,osp_data_set_n4 ,osp_data_set_s2
 * @param user_data Pointer to the instance of osp_user_data initialized by osp_data_init
 */
void osp_data_set_n2(osp_user_data *user_data);

/**
 * @brief Helper function for initializing gain parameters for WDRC with N4 audiogram
 *
 * @see osp_user_data
 * @see osp_data_set_gain, osp_data_set_nh, osp_data_set_n2 ,osp_data_set_n4 ,osp_data_set_s2
 * @param user_data Pointer to the instance of osp_user_data initialized by osp_data_init
 */
void osp_data_set_n4(osp_user_data *user_data);

/**
 * @brief Helper function for initializing gain parameters for WDRC with S2 audiogram
 *
 * @see osp_user_data
 * @see osp_data_set_gain, osp_data_set_nh, osp_data_set_n2 ,osp_data_set_n4 ,osp_data_set_s2
 * @param user_data Pointer to the instance of osp_user_data initialized by osp_data_init
 */
void osp_data_set_s2(osp_user_data *user_data);


#endif /* OSP_PROCESS_H__ */
