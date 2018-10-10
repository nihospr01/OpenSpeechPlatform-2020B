/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#include "osp_tcp.h"
#include "tcplib.h"
#include <stdlib.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "jsmn.h"
#include <stdio.h> // Only for perror

/**
 * @brief Data structure containing OSP TCP layer internal variables
 */
struct osp_tcp_t {
	int conn_fd;	///< The socket file descriptor
	int sock;		///< The socket in to establish a connection
	int port;		///< The port to listen on for a connection
};

static int jsoneq(const char *json, jsmntok_t *tok, const char *s) {
	if (tok->type == JSMN_STRING && (int) strlen(s) == tok->end - tok->start &&
			strncmp(json + tok->start, s, tok->end - tok->start) == 0) {
		return 0;
	}
	return -1;
}

int get_int_from_SubString (const char* input, int offset, int len)
{
	char dest[512];
	int value;
	unsigned long int input_len = strlen (input);
	
	if (offset + len > input_len)
	{
		return -1;
	}
	
	strncpy (dest, input + offset, len);
	dest[len] = '\0';
	value = atoi(dest);
	return value;
}

int get_float_from_SubString (const char* input, int offset, int len)
{
	char dest[512];
	int value;
	unsigned long int input_len = strlen (input);
	
	if (offset + len > input_len)
	{
		return -1;
	}
	
	strncpy (dest, input + offset, len);
	dest[len] = '\0';
	value = atof(dest);
	return value;
}

Osp_tcp osp_tcp_init(int port)
{
	struct osp_tcp_t *osp_tcp;

	if ((osp_tcp = (struct osp_tcp_t*)malloc(sizeof(struct osp_tcp_t))) == NULL) {
		return NULL;
	}

	osp_tcp->conn_fd = 0;
	osp_tcp->port = -1;

	if ((osp_tcp->sock = start_tcp_server(port)) < 0) {
		goto abort;
	}

	return osp_tcp;

abort:
	free(osp_tcp);
	return NULL;
}

int osp_tcp_connect(Osp_tcp osp_tcp)
{
	osp_tcp->conn_fd = 0;	

	do {
		if ((osp_tcp->conn_fd = get_tcp_server_connection(osp_tcp->sock)) < 0) {
			return -1;
		}
	} while (!osp_tcp->conn_fd);

	return 0;
}

int osp_tcp_read_req(Osp_tcp osp_tcp)
{
	ssize_t ret;
	jsmn_parser p;
	jsmntok_t t[128]; /* We expect no more than 128 tokens */
	
	jsmn_init(&p);
	int req[2];
	int r,i;
//	int length_input;
	char JSON_STRING[512];
	
	// Read in the request byte
	ret = read_tcp_server_stream(osp_tcp->conn_fd, JSON_STRING, READ_REQUEST_LEN_JSON);
//	for(i=0;i<READ_REQUEST_LEN_JSON+1;i++)
//	{
//		if(JSON_STRING[i]=='}')
//		{
//			JSON_STRING[i+1]= '\0';
//			break;
//		}
//	}
	JSON_STRING[READ_REQUEST_LEN_JSON] = '\0';
	printf("\n\n%s\n\n",JSON_STRING);
	
	if (ret < 0) {
		perror("Failed to read connection\n");
		return -1;
	} else if (ret == 0){ 
		return OSP_DISCONNECT;
	}
	r = jsmn_parse(&p, JSON_STRING, strlen(JSON_STRING), t, sizeof(t)/sizeof(t[0]));
	if (r < 0) {
		printf("Failed to parse JSON: %d\n", r);
		return 1;
	}
//	printf("r values is %d\n", r);
	
	/* Assume the top-level element is an object */
	if (r < 1 || t[0].type != JSMN_OBJECT) {
		printf("Object expected\n");
		return 1;
	}
	
	/* Loop over all keys of the root object */
	for (i = 1; i < r; i++) {
		if (jsoneq(JSON_STRING, &t[i], "REQUEST_ACTION") == 0) {
			/* We may use strndup() to fetch string value */
			//printf("REQUEST_ACTION: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING + t[i+1].start);
			
			req[0] = get_int_from_SubString(JSON_STRING, t[i+1].start, t[i+1].end-t[i+1].start);
			printf("REQUEST_ACTION: %d\n",req[0]);
			i++;
		} else if (jsoneq(JSON_STRING, &t[i], "VERSION") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("VERSION: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING + t[i+1].start);
			req[1] = get_int_from_SubString(JSON_STRING, t[i+1].start, t[i+1].end-t[i+1].start);
			printf("VERSION: %d\n",req[1]);
			i++;
		} else {
			printf("Unexpected key: %.*s\n", t[i].end-t[i].start,
						 JSON_STRING + t[i].start);
		}
		
	}
	
	// Not a valid request.  Should never reach here, since we're writing the Android
	// and C code.  The only way we'd get here is if the versions of code running on
	// Android and C aren't the same.  And we should be checking that a different way
	if (req[0] > 8) {
		return -1;
	}

	if (req[1] != VERSION) {
		fprintf(stderr, "NOT CORRECT VERSION. C VERSION IS %d, GOT ANDROID VERSION %d\n", VERSION, req[1]);
		return OSP_WRONG_VERSION;
	}

	return req[0];
}

ssize_t osp_tcp_read_values(Osp_tcp osp_tcp, osp_user_data *data, unsigned int size)
{
	ssize_t ret;
	jsmn_parser p;
	jsmntok_t t[128]; /* We expect no more than 128 tokens */
	jsmn_init(&p);
	int r,i,j;

	char JSON_STRING_UPDATE[512];
	// Read in the request byte
	ret = read_tcp_server_stream(osp_tcp->conn_fd, JSON_STRING_UPDATE, HA_STATE_JSON);
	for(i=0;i<HA_STATE_JSON+1;i++)
	{
		if(JSON_STRING_UPDATE[i]=='}')
		{
			JSON_STRING_UPDATE[i+1]= '\0';
			break;
		}
	}
	
	JSON_STRING_UPDATE[HA_STATE_JSON] = '\0';
	
	printf(" \n\n%s\n\n", JSON_STRING_UPDATE);
	
	if (ret < 0) {
		perror("Failed to read connection\n");
		return -1;
	} else if (ret == 0){ 
		return 0;
	}

	r = jsmn_parse(&p, JSON_STRING_UPDATE, strlen(JSON_STRING_UPDATE), t, sizeof(t)/sizeof(t[0]));
	if (r < 0) {
		printf("Failed to parse JSON: %d\n", r);
		return 1;
	}
//	printf("r values is %d\n", r);
	
	/* Assume the top-level element is an object */
	if (r < 1 || t[0].type != JSMN_OBJECT) {
		printf("Object expected\n");
		return 1;
	}
	
	/* Loop over all keys of the root object */
	for (i = 1; i < r; i++) {
		if (jsoneq(JSON_STRING_UPDATE, &t[i], "noOp") == 0) {
			/* We may use strndup() to fetch string value */
			//printf("noOp: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->no_op = get_int_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}else if (jsoneq(JSON_STRING_UPDATE, &t[i], "afc") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("afc: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->afc = get_int_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "feedback") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("feedback: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->feedback = get_int_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "rear") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("rear: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->rear_mics = get_int_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "g50") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("g50: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			if (t[i+1].type != JSMN_ARRAY) {
				continue; /* We expect groups to be an array of strings */
			}
			for (j = 0; j < t[i+1].size; j++) {
				jsmntok_t *g = &t[i+j+2];
				//printf("  * %.*s\n", g->end - g->start, JSON_STRING_UPDATE + g->start);
				data->g50[j] = get_int_from_SubString(JSON_STRING_UPDATE, g->start, g->end - g->start);
			}
			i += t[i+1].size + 1;
			
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "g80") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("g80: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			if (t[i+1].type != JSMN_ARRAY) {
				continue; /* We expect groups to be an array of strings */
			}
			for (j = 0; j < t[i+1].size; j++) {
				jsmntok_t *g = &t[i+j+2];
				//printf("  * %.*s\n", g->end - g->start, JSON_STRING_UPDATE + g->start);
				data->g80[j] = get_int_from_SubString(JSON_STRING_UPDATE, g->start, g->end - g->start);
			}
			i += t[i+1].size + 1;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "kneelow") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("kneelow: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			if (t[i+1].type != JSMN_ARRAY) {
				continue; /* We expect groups to be an array of strings */
			}
			for (j = 0; j < t[i+1].size; j++) {
				jsmntok_t *g = &t[i+j+2];
				//printf("  * %.*s\n", g->end - g->start, JSON_STRING_UPDATE + g->start);
				data->knee_low[j] = get_int_from_SubString(JSON_STRING_UPDATE, g->start, g->end - g->start);
			}
			i += t[i+1].size + 1;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "mpoLimit") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("mpoLimit: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			if (t[i+1].type != JSMN_ARRAY) {
				continue; /* We expect groups to be an array of strings */
			}
			for (j = 0; j < t[i+1].size; j++) {
				jsmntok_t *g = &t[i+j+2];
				//printf("  * %.*s\n", g->end - g->start, JSON_STRING_UPDATE + g->start);
				data->knee_high[j] = get_int_from_SubString(JSON_STRING_UPDATE, g->start, g->end - g->start);
			}
			i += t[i+1].size + 1;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "attackTime") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("attackTime: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			if (t[i+1].type != JSMN_ARRAY) {
				continue; /* We expect groups to be an array of strings */
			}
			for (j = 0; j < t[i+1].size; j++) {
				jsmntok_t *g = &t[i+j+2];
				//printf("  * %.*s\n", g->end - g->start, JSON_STRING_UPDATE + g->start);
				data->attack[j] = get_int_from_SubString(JSON_STRING_UPDATE, g->start, g->end - g->start);
			}
			i += t[i+1].size + 1;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "releaseTime") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("releaseTime: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			if (t[i+1].type != JSMN_ARRAY) {
				continue; /* We expect groups to be an array of strings */
			}
			for (j = 0; j < t[i+1].size; j++) {
				jsmntok_t *g = &t[i+j+2];
				//printf("  * %.*s\n", g->end - g->start, JSON_STRING_UPDATE + g->start);
				data->release[j] = get_int_from_SubString(JSON_STRING_UPDATE, g->start, g->end - g->start);
			}
			i += t[i+1].size + 1;
		}
		
		// Noise management parameters
		
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "noise_estimation_type") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("afc: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->noise_estimation_type = get_int_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "spectral_subtraction") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("afc: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->spectral_subtraction = get_int_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "spectral_subtraction_param") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("afc: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->spectral_subtraction_param = get_float_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		
		// feedback management parameters
		
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "feedback_algorithm_type") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("afc: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->feedback_algorithm_type = get_int_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "mu") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("afc: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->mu = get_float_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "rho") == 0) {
			/* We may additionally check if the value is either "true" or "false" */
			//printf("afc: %.*s\n", t[i+1].end-t[i+1].start, JSON_STRING_UPDATE + t[i+1].start);
			data->rho = get_float_from_SubString(JSON_STRING_UPDATE, t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		else {
			printf("Unexpected key: %.*s\n", t[i].end-t[i].start,
						 JSON_STRING_UPDATE + t[i].start);
		}
		
	}
	return ret;
}

ssize_t osp_tcp_read_user_packet(Osp_tcp osp_tcp, char *message, unsigned int size)
{
	ssize_t ret;
	jsmn_parser p,p2;
	jsmntok_t t[128],t2[128]; /* We expect no more than 128 tokens */
	jsmn_init(&p);
	jsmn_init(&p2);
	int r,i;
	int req = 0;
	char JSON_STRING_USER[512];
	
	ret = read_tcp_server_stream(osp_tcp->conn_fd, JSON_STRING_USER, USER_LEN_JSON);
	JSON_STRING_USER[USER_LEN_JSON] = '\0';
	printf("\n\n%s\n\n", JSON_STRING_USER);
	if (ret < 0) {
		perror("Failed to read connection\n");
		return -1;
	} else if (ret == 0){
		return OSP_DISCONNECT;
	}

	r = jsmn_parse(&p, JSON_STRING_USER, strlen(JSON_STRING_USER), t, sizeof(t)/sizeof(t[0]));
	if (r < 0) {
		printf("Failed to parse JSON: %d\n", r);
		return 1;
	}
//	printf("r values is %d\n", r);

	/* Assume the top-level element is an object */
	if (r < 1 || t[0].type != JSMN_OBJECT) {
		printf("Object expected\n");
		return 1;
	}

	/* Loop over all keys of the root object */
	for (i = 1; i < r; i++) {
		if (jsoneq(JSON_STRING_USER, &t[i], "LS") == 0) {
			/* We may use strndup() to fetch string value */

			req = get_int_from_SubString(JSON_STRING_USER, t[i+1].start, t[i+1].end-t[i+1].start);
			printf("Length of String: %d\n",req);
			i++;
		}
		else {
			printf("Unexpected key: %.*s\n", t[i].end-t[i].start,
						 JSON_STRING_USER + t[i].start);
		}

	}





	if (size < ret) {
		fprintf(stderr, "Not enough memory allocated in message to contain xmission\n");
		return -1;
	}

	ret = read_tcp_server_stream(osp_tcp->conn_fd, JSON_STRING_USER, READ_REQUEST_LEN_JSON);
	for(i = 0; i<1024+1;i++)
	{
		if(JSON_STRING_USER[i] == '}')
		{
			JSON_STRING_USER[i+1] = '\0';
			break;
		}
	}
	JSON_STRING_USER[READ_REQUEST_LEN_JSON] = '\0';
	
	printf("\n\n%s\n", JSON_STRING_USER);
	if (ret < 0) {
		perror("Failed to read connection\n");
		return -1;
	} else if (ret == 0){ 
		return OSP_DISCONNECT;
	}

	r = jsmn_parse(&p2, JSON_STRING_USER, strlen(JSON_STRING_USER), t2, sizeof(t2)/sizeof(t2[0]));
	if (r < 0) {
		printf("Failed to parse JSON: %d\n", r);
		return 1;
	}
//	printf("r values is %d\n", r);
	
	/* Assume the top-level element is an object */
	if (r < 1 || t2[0].type != JSMN_OBJECT) {
		printf("Object expected\n");
		return 1;
	}
	
	/* Loop over all keys of the root object */
	for (i = 1; i < r; i++) {
		if (jsoneq(JSON_STRING_USER, &t2[i], "URI") == 0) {
			/* We may use strndup() to fetch string value */
//			printf("URI: %.*s\n", t2[i+1].end-t2[i+1].start, JSON_STRING_USER + t2[i+1].start);
			strncpy (message, JSON_STRING_USER + t2[i+1].start, t2[i+1].end-t2[i+1].start);
			message[req] = '\0';
			printf("\n\n%s\n\n",message);
			i++;
		}
		else {
			printf("Unexpected key: %.*s\n", t2[i].end-t2[i].start,
						 JSON_STRING_USER + t2[i].start);
		}
		
	}
	message[req] = '\0';
	ret = 3;
	return ret;
}

ssize_t osp_4afc_read_values(Osp_tcp osp_tcp, char *file_path, unsigned int size)
{
	ssize_t ret;
	jsmn_parser p;
	jsmntok_t t[128]; /* We expect no more than 128 tokens */
	jsmn_init(&p);
	int r,i;
	char JSON_STRING_UPDATE[5000];
	// Read in the request byte
	
	ret = read_tcp_server_stream(osp_tcp->conn_fd, JSON_STRING_UPDATE, size);
	
	for(i = 0; i<size+1;i++)
	{
		if(JSON_STRING_UPDATE[i] == '}')
		{
			JSON_STRING_UPDATE[i+1] = '\0';
			break;
		}
	}
//	JSON_STRING_UPDATE[HA_STATE_JSON] = '\0';

	printf(" \n\n%s\n\n", JSON_STRING_UPDATE);
	
	//	printf("\n\n\n%d\n\n\n",strlen(JSON_STRING_UPDATE));
	if (ret < 0) {
		perror("Failed to read connection\n");
		return -1;
	} else if (ret == 0){
		return 0;
	}
	
	r = jsmn_parse(&p, JSON_STRING_UPDATE, strlen(JSON_STRING_UPDATE), t, sizeof(t)/sizeof(t[0]));
	if (r < 0) {
		printf("Failed to parse JSON: %d\n", r);
		return -1;
	}
	//	printf("r values is %d\n", r);
	
	/* Assume the top-level element is an object */
	if (r < 1 || t[0].type != JSMN_OBJECT) {
		printf("Object expected\n");
		return -1;
	}
	
	/* Loop over all keys of the root object */
	for (i = 1; i < r; i++) {
		if (jsoneq(JSON_STRING_UPDATE, &t[i], "Word_set") == 0) {
			
			i++;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "Actual_answer") == 0) {
			strncpy (file_path, JSON_STRING_UPDATE + t[i+1].start, t[i+1].end-t[i+1].start);
			i++;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "Noise") == 0) {
			
			
			i++;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "Ambience_noise") == 0) {
			
			
			i++;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "Fileplayback_level") == 0) {
			
			
			i += t[i+1].size + 1;
			
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "Noise_level") == 0) {
			
			
			i += t[i+1].size + 1;
		}
		else if (jsoneq(JSON_STRING_UPDATE, &t[i], "Ambience_level") == 0) {
			
		}
		else {
			printf("Unexpected key: %.*s\n", t[i].end-t[i].start,
						 JSON_STRING_UPDATE + t[i].start);
		}
		
	}
	
	
	return ret;
}

// These calls are similar, but we might scale one or the other
// in the future
int osp_tcp_send_underruns(Osp_tcp osp_tcp, unsigned int underruns)
{
	if (send_tcp_client_data(osp_tcp->conn_fd, (char *)&underruns, sizeof(underruns)) < 0) {
		printf("Error sending underrun packet back to client\n");
		return -1;
	}
	
	return 0;
}

int osp_tcp_send_num_bands(Osp_tcp osp_tcp, unsigned char bands)
{
	if (send_tcp_client_data(osp_tcp->conn_fd, (char *)&bands, sizeof(bands)) < 0) {
		printf("Error sending underrun packet back to client\n");
		return -1;
	}
	
	return 0;
}

void osp_tcp_close(Osp_tcp osp_tcp)
{
	close_tcp_server_connection(osp_tcp->conn_fd);
	stop_tcp_server(osp_tcp->sock);
	
	if (osp_tcp != NULL) {
		free(osp_tcp);
	}
}
