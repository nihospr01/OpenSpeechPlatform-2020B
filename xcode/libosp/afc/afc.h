/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#ifndef AFC_H__
#define AFC_H__

#define AFC_NUM_FRAMES		7	///< Amount of frames required for AFC processing
#define AFC_FILTER_TAP_LEN	192	///< AFC fitler tap length

#if !defined(ARRAY_SIZE)
    #define ARRAY_SIZE(x) (sizeof((x)) / sizeof((x)[0]))
#endif

typedef struct afc_t *Afc;

/**
 * @brief Function to initialize the afc data struct
 *
 * Allocates memory to the Afc instance and initialize its parameters.
 * 
 * @param afc_filter_taps AFC filter initalization taps, W(z) initialization
 * @param afc_filter_tap_len AFC filter tap length
 * @param prefilter_taps Pre-whitening filter taps, A(z)
 * @param prefilter_tap_len Pre-whitening filter tap length
 * @param band_limited_filter_taps Band-limited filter taps, H(z)
 * @param band_limited_filter_tap_len Band-limited filter tap length
 * @param frame_size Frame size to process
 * @param adaptation_type AFC adaptation type. 1 = (FXLMS + PNLMS), 0 = FXLMS
 * @return Afc Returns the Afc instance/object if memory is succesfully allocated or returns NULL. 
 */
Afc afc_init(const float *afc_filter_taps, int afc_filter_tap_len,
				const float *prefilter_taps, int prefilter_tap_len,
				const float *band_limited_filter_taps, int band_limited_filter_tap_len,
				int frame_size, unsigned int adaptation_type);

/**
 * @brief Function to update the AFC filter taps. i.e. perform AFC
 * 
 * @see afc_t 
 * @param afc The instance of the Afc structure that was returned from afc_init  
 * @param s Pointer to the output signal array of WDRC
 * @param e Pointer to the error signal array
 * @param frame_size Number of samples in the frame to process
 * @return int -1 for wrong frame size. 0 if successful
 */
int afc_update_taps(Afc afc, float *s, float *e, size_t frame_size);

/**
 * @brief Function to get the estimate of the feedback signal, y_hat(n) 
 * 
 * @see afc_t 
 * @param afc The instance of the Afc structure that was returned from afc_init
 * @param y_est Pointer to the array that estimated feedback signal will be written into
 * @param len Number of samples of the estimated feedback signal requested
 */
void afc_get_y_estimated(Afc afc, float *y_est, size_t len);

/**
 * @brief Function to get the AFC filter taps
 * 
 * @see afc_t  
 * @param afc The instance of the Afc structure that was returned from afc_init
 * @param taps Pointer to the array that afc filter taps will be written into
 * @return int AFC filter tap length
 */
int afc_get_taps(Afc afc, float *taps);

/**
 * @brief Function to close and free the Afc instance
 *
 * @see afc_t		
 * @param afc The instance of the Afc structure that was returned from afc_init
 * @return int 0 if successful. -1 otherwise
 */
int afc_destroy(Afc afc);

#endif /* AFC_H__ */
