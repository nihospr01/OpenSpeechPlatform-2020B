/**
 * @file
 * @brief Portaudio interface for Open Speech Platform
 */
#ifndef _PORT_WRAPPER_H__
#define _PORT_WRAPPER_H__

#include <portaudio.h>
#include "osp_process/osp_process.h"

/**
 * @brief Struct containing details for file loopback mode
 */
typedef struct file_loopback_context_t {
	FILE *input_file;			///< Input file
	FILE *output_file;			///< Output file
	unsigned long length;		///< Number of samples in input file
	unsigned long index;		///< Internal index var
	char wav_header[44];		///< Header data embedded in .wav file
	float *inL;					///< Array of input left samples
	float *inR;					///< Array of input right samples
	float *outL;				///< Array of output left samples
	float *outR;				///< Array of output right samples
} file_loopback_context;

/**
 * @brief Wraps around the osp_data, but also contains a file loopback context struct
 * Have to wrap the two structs in one data structure so that we can pass it to the 
 * Port Audio callback.
 *
 * @see file_loopback_context
 * @see osp_user_data
 */
typedef struct pa_loopback_data_t {
	file_loopback_context *file_ctx;
	osp_user_data *osp_data;
	unsigned char done;
} pa_loopback_data;

/*
 * @brief Initializes the Port Audio parameters
 * We also pass it the pa_data struct, which contains the osp_user_data pointer as well as the file 
 * context struct for file loopback mode.
 *
 * @see pa_user_data
 *
 * @param pa_data structure containing the osp_user_data pointer and aux data pointer
 * @param samp_rate The desired sample rate of the audio
 * @param frames_per_buffer The number of samples per buffer returned by PA.
 *
 * @return Returns 0 on success, -1 otherwise
 */
int pa_init(pa_user_data *pa_data, unsigned int samp_rate, unsigned int frames_per_buffer);

/**
 * @brief Initialization for file loopback mode.  Executes same functionality as pa_init
 *
 * @see pa_loopback_data
 *
 * @param loopback_data Structure that contains details about files (input and output), and
 * samples
 * @param samp_rate The sample rate of incoming/outgoing audio
 * @param frames_per_buffer The number of samples per buffer returned by PA.
 *
 * @return Returns 0 on success, -1 otherwise
 */
int pa_loopback_run(pa_loopback_data *loopback_data, unsigned int samp_rate, unsigned int frames_per_buffer);

/**
 * @brief Starts Port Audio stream.  Until this is called, the audio callback will not be fired
 *
 * @return Returns 0 on success, -1 on error
 */
int pa_start_stream();

/**
 * @brief Stops Port Audio stream. The audio callback will cease to be fired, stopping stream
 *
 * @return Returns 0 on success, -1 on error
 */
int pa_stop_stream();

/**
 * @brief Closes Port Audio stream
 *
 * @return Returns 0 on success, -1 on error
 */
void pa_close();

#endif  /*_PORT_WRAPPER_H__ */
