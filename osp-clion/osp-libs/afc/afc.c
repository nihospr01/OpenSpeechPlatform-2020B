/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */


#include <stdlib.h>
#include <string.h>
#include <math.h>

#include "afc.h"
#include "filter.h"
#include "array_utilities.h"
#include "circular_buffer.h"

/**
 * @brief AFC data structure
 *
 * Refer to the supported document on AFC for the notations
 */
struct afc_t {
	Filter afc_filter;	///< AFC filter W(z)
	Filter prefilter_e;	///< Pre-whitening filter A(z)
	Filter prefilter_u;	///< Pre-whitening filter A(z)
	Filter band_limited_filter;	///< Band-limited filter H(z)
	unsigned int adaptation_type; // AFC adaptation type. 1 = FXLMS + PNLMS and 0 = FXLMS
	float *u;	///< Band-limited signal u(n) of the HA output s(n)
	float *y_hat;	///< Estimate of feedback signal y_hat(n)
	float power_estimate;	///< Power estimate, sigma^2_hat(n),  of the input to coefficient adaptation bloclk
	int frame_size; ///< Number of samples in the current frame
	float mu;	///< Step size parameter
	float delta;	///< Regularization term
	float rho;	///< Forgetting factor
	float alpha;	///< Parameter for PNLMS
	float beta;	///< Parameter for PNLMS
	float *u_prefiltered_accumulated; ///< AFC_NUM_FRAMES frames accumulated values of pre-filtered output of u(n). i.e. u_f(n)
	Circular_buffer upa;
};

/**
 * @brief Compute the gradient for FXLMS update.
 *
 *
 * @param e_prefiltered Pointer to the array containing pre-filtered input e_f(n)
 * @param u_prefiltered_accumulated Pointer to the buffer of accumulated values of pre-filtered output of u(n) i.e., u_f(n)
 * @param gradient Pointer to the array where the gradient will be written into
 * @param u_prefiltered_acc_len Length of the u_prefiltered_accumulated buffer
 * @param frame_size Length of the frame size which is also the length of e_prefiltered
 */
static void compute_gradient(float *e_prefiltered, float *u_prefiltered_accumulated, float *gradient, size_t u_prefiltered_acc_len, size_t frame_size)
{
	int i, j;
	// int u_prefiltered_acc_len = AFC_NUM_FRAMES * frame_size;

	memset(gradient, 0x00, (AFC_NUM_FRAMES * frame_size - frame_size) * sizeof(float));

	// Last element stores the index, of the circular buffer, to start from for the next frame
	int index = u_prefiltered_accumulated[u_prefiltered_acc_len - 1];
	// printf("%d\n", index);

	// Sliding window dot product
	for(i = 0; i < u_prefiltered_acc_len - frame_size; i++) {
		for(j = i; j < i + frame_size; j++) {
			gradient[i] += (e_prefiltered[j - i] * u_prefiltered_accumulated[(j + index) % (u_prefiltered_acc_len - 1)]);
		}
	}
	// Flip the weighted coefficients
	array_flip(gradient, u_prefiltered_acc_len - frame_size);
}

void DSPF_sp_fircirc_2 (float x[], float h[], float r[],
						size_t index, size_t csize, size_t nh, size_t nr)
{
	int i, j;
	//Circular Buffer block size = ((2^(csize + 1)) / 4)
	//floating point numbers
	int mod = (1 << (csize - 1));
	float r0;
	for (i = 0; i < nr; i++) {
		r0 = 0;
		for (j = 0; j < nh; j++) {
			//Operation ”% mod” is equivalent to ”& (mod −1)”
			// r0 += x[(i + j + index) % mod] * h[j];
            r0 += x[(i + j + index) & (mod - 1)] * h[j];
		}

		r[i] = r0;
	}
}

static void compute_gradient_upa(float *e_prefiltered, Circular_buffer upa, float *gradient, size_t frame_size)
{
	size_t u_prefiltered_acc_len = AFC_NUM_FRAMES * frame_size;
	// float *u_prefiltered_accumulated = upa->buf;
	// unsigned int buf_len = upa->buf_len;
	// unsigned int index = upa->index;
	size_t buf_len = circular_buffer_get_buffer_size(upa); //1024
	unsigned char exp = circular_buffer_get_exp(upa);
	float buf[buf_len];
	size_t index = circular_buffer_get_internal_buffer(upa, buf);
	// printf("%d\n", index);

	memset(gradient, 0x00, (u_prefiltered_acc_len - frame_size) * sizeof(float));

	DSPF_sp_fircirc_2(buf, e_prefiltered, gradient,
			(index - u_prefiltered_acc_len + buf_len + 1) & (buf_len - 1),
			exp, frame_size, u_prefiltered_acc_len - frame_size);

	// Sliding window dot product
	// for(i = 0; i < u_prefiltered_acc_len - frame_size; i++) {
	// 	for(j = i; j < i + frame_size; j++) {
	// 		// gradient[i] += (e_prefiltered[j - i] * buf[(j + index) & (buf_len - 1)]);
	// 		gradient[i] += (e_prefiltered[j - i] * buf[(j + index) % buf_len]);
	// 	}
	// }
	// Flip the weighted coefficients
	array_flip(gradient, u_prefiltered_acc_len - frame_size);
}


/**
 * @brief Updating u_prefiltered_accumulated buffer which is circular
 *
 * Replace the frame_size number of old values with new values in the circular buffer using the last element of the
 * buffer as the start index.
 *
 * @param u_prefiltered_accumulated Pointer to the buffer of accumulated values of pre-filtered output of u(n) i.e., u_f(n)
 * @param new_frame Pointer to the pre-filtered output of u(n) for the new frame
 * @param u_prefiltered_acc_len Length of the u_prefiltered_accumulated buffer
 * @param new_frame_len Length of new_frame which is equal to the frame_size
 */
static void update_u_prefiltered_accumulated(float *u_prefiltered_accumulated, float *new_frame, int u_prefiltered_acc_len, int new_frame_len) {
	int i;
	// Last element stores the index, of the circular buffer, to start from for the next frame
	// Get the index to start in the circular buffer
	int index = (int) u_prefiltered_accumulated[u_prefiltered_acc_len - 1];
	for(i = 0; i < new_frame_len; i++) {
		u_prefiltered_accumulated[(index + i) % (u_prefiltered_acc_len - 1)] = new_frame[i];
	}
	// Update the start index in the circular buffer for the next update
	index = (index + new_frame_len) % (u_prefiltered_acc_len - 1);
	u_prefiltered_accumulated[u_prefiltered_acc_len - 1] = (float) index;
	// printf("Indexs = %d, %f\n", u_prefiltered_acc_len - 1, index);
}

/**
 * @brief Update the proportionate matrix (P(n)) diagonal matrix for PNLMS adaptation
 *
 * Get weights for all step sizes. Step sizes are different for each filter tap
 *
 * @param taps Pointer to the array that contain AFC filter taps
 * @param step_size_weights Pointer to the array that step size weights will be written into
 * @param alpha Parameter for PNLMS
 * @param beta Parameter for PNLMS
 * @param delta Regularization parameter
 * @param len Length of the AFC filter
 */
static void get_step_size_weights(float *taps, float *step_size_weights, float alpha, float beta, float delta, int len)
{
	int i;
	float tmp_sum = 0;
	for(i = 0; i < len; i++) {
		step_size_weights[i] = 1 - exp(-beta * fabsf(taps[i]));
		tmp_sum = tmp_sum + step_size_weights[i];
	}

	tmp_sum = tmp_sum * 2.0 / len + delta;
	tmp_sum = (1.0 + alpha) / tmp_sum;
	array_multiply_const(step_size_weights, tmp_sum, len);
	array_add_const(step_size_weights, (1.0 - alpha) / 2.0, len);
	// float min_step_size_weights = array_min(step_size_weights, len);
	// array_multiply_const(step_size_weights, 1.0 / min_step_size_weights, len);
}


Afc afc_init(const float *afc_filter_taps, int afc_filter_tap_len,
				const float *prefilter_taps, int prefilter_tap_len,
				const float *band_limited_filter_taps, int band_limited_filter_tap_len,
				int frame_size, unsigned int adaptation_type)
{
	struct afc_t *obj;

	if ((obj = (struct afc_t*)malloc(sizeof(struct afc_t))) == NULL) {
		return NULL;
	}

	if ((obj->afc_filter = filter_init(afc_filter_taps, afc_filter_tap_len)) == NULL) {
		goto abort;
	}

	if ((obj->prefilter_e = filter_init(prefilter_taps, prefilter_tap_len)) == NULL) {
		goto abort2;
	}

	if ((obj->prefilter_u = filter_init(prefilter_taps, prefilter_tap_len)) == NULL) {
		goto abort3;
	}

	if ((obj->band_limited_filter = filter_init(band_limited_filter_taps, band_limited_filter_tap_len)) == NULL) {
		goto abort4;
	}

	if ((obj->y_hat = (float *)calloc(frame_size, sizeof(float))) == NULL) {
		goto abort5;
	}

	if ((obj->u = (float *)calloc(frame_size, sizeof(float))) == NULL) {
		goto abort6;
	}

	if ((obj->upa = circular_buffer_init(AFC_NUM_FRAMES * frame_size-1)) == NULL) {
		goto abort7;
	}

	if ((obj->u_prefiltered_accumulated = (float *)calloc(AFC_NUM_FRAMES * frame_size, sizeof(float))) == NULL) {
		goto abort7;
	}

	obj->frame_size = frame_size;
	obj->adaptation_type = adaptation_type;

	obj->power_estimate = 0;
	obj->mu = 0.005;
	obj->delta = 1e-15;
	obj->rho = 0.985;
	obj->alpha = 0;
	obj->beta = 5;

	return obj;

abort7:
	free(obj->u);
abort6:
	free(obj->y_hat);
abort5:
	filter_destroy(obj->band_limited_filter);
abort4:
	filter_destroy(obj->prefilter_u);
abort3:
	filter_destroy(obj->prefilter_e);
abort2:
	filter_destroy(obj->afc_filter);
abort:
	free(obj);
	return NULL;
}


int afc_update_taps(Afc afc, float *s, float *e, size_t frame_size)
{
	float u_prefiltered[frame_size];
	float e_prefiltered[frame_size];
	float mu;
	float afc_filter_update[AFC_FILTER_TAP_LEN];

	float afc_filter_taps[AFC_FILTER_TAP_LEN];
	float step_size_weights[AFC_FILTER_TAP_LEN];
	if (frame_size != afc->frame_size) {
		return -1;
	}

	// Prefiltering u(n)
	filter_proc(afc->prefilter_u, afc->u, u_prefiltered, frame_size);
	// Prefiltering e(n)
	filter_proc(afc->prefilter_e, e, e_prefiltered, frame_size);
	// Compute the power estimate of the input to coefficient adaptation
	afc->power_estimate = (afc->rho * afc->power_estimate) + (1-afc->rho)*(array_mean_square(u_prefiltered, frame_size) + array_mean_square(e_prefiltered, frame_size));
	// Normalize the stepsize
	mu = afc->mu / (AFC_FILTER_TAP_LEN * afc->power_estimate + afc->delta);
	// Update the accumulated buffer of pre-filtered output of u(n). i.e. u_f(n)
	// update_u_prefiltered_accumulated(afc->u_prefiltered_accumulated, u_prefiltered, u_prefiltered_acc_len, frame_size);
	circular_buffer_update_buffer(afc->upa, u_prefiltered, frame_size);

	// int idx = circular_buffer_get_internal_buffer(afc->upa, upa);
	// float *upbig =  afc->u_prefiltered_accumulated;
	// printf("%d, %d",u_prefiltered_acc_len-1, upbig[u_prefiltered_acc_len-1]);


	// Computing the gradient for the FXLMS
	// compute_gradient(e_prefiltered, afc->u_prefiltered_accumulated, afc_filter_update, u_prefiltered_acc_len, frame_size);
	compute_gradient_upa(e_prefiltered, afc->upa, afc_filter_update, frame_size);
	// array_print("Big buffer", afc_filter_update, AFC_FILTER_TAP_LEN);
	// array_print("Circ buffer", afc_filter_update2, AFC_FILTER_TAP_LEN);
	// printf("===========================================\n");
	// Compute FXLMS update. i.e. Gradient * stepsize
	array_multiply_const(afc_filter_update, mu, ARRAY_SIZE(afc_filter_update));
	// Get afc filter taps into afc_filter_taps array
	filter_get_taps(afc->afc_filter, afc_filter_taps);

	switch(afc->adaptation_type) {
		// FXLMS
		case 0:
			break;
		// PNLMS
		case 1:
			default:
			get_step_size_weights(afc_filter_taps, step_size_weights, afc->alpha, afc->beta, afc->delta, AFC_FILTER_TAP_LEN);
			array_element_multiply_array(afc_filter_update, step_size_weights, ARRAY_SIZE(afc_filter_update));
			
	}

	// Compute the updated afc filter taps
	array_add_array(afc_filter_update, afc_filter_taps, ARRAY_SIZE(afc_filter_update));
	// Apply band-limited filter on the output s to get u
	filter_proc(afc->band_limited_filter, s, afc->u, frame_size);
	// Apply afc_filter on band-limited signal u to get y_hat
	filter_proc(afc->afc_filter, afc->u, afc->y_hat, frame_size);

	// Update AFC filter taps with the new taps
	filter_update_taps(afc->afc_filter, afc_filter_update, ARRAY_SIZE(afc_filter_update));

	return 0;
}

void afc_get_y_estimated(Afc afc, float *y_est, size_t len)
{
	size_t i;

	for (i = 0; i < len; i++) {
		y_est[i] = afc->y_hat[i];
	}
}

int afc_get_taps(Afc afc, float *taps)
{
	return filter_get_taps(afc->afc_filter, taps);
}

int afc_destroy(Afc afc)
{
	if (afc != NULL) {
		if (afc->afc_filter) {
			filter_destroy(afc->afc_filter);
		} else {
			return -1;
		}

		if (afc->prefilter_u) {
			filter_destroy(afc->prefilter_u);
		} else {
			return -1;
		}

		if (afc->prefilter_e) {
			filter_destroy(afc->prefilter_e);
		} else {
			return -1;
		}

		if (afc->band_limited_filter) {
			filter_destroy(afc->band_limited_filter);
		} else {
			return -1;
		}

		if (afc->upa) {
			circular_buffer_destroy(afc->upa);
		} else {
			return -1;
		}

		//@TODO Add all the tests for these so we're not doubly freeing anything
		free(afc->u);
		free(afc->u_prefiltered_accumulated);
		free(afc->y_hat);
		free(afc);
	} else {
		return -1;
	}

	return 0;
}
