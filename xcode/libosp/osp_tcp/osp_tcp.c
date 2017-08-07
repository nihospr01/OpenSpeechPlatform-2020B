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

#include <stdio.h> // Only for perror

/**
 * @brief Data structure containing OSP TCP layer internal variables
 */
struct osp_tcp_t {
	int conn_fd;	///< The socket file descriptor
	int sock;		///< The socket in to establish a connection
	int port;		///< The port to listen on for a connection
};

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

char osp_tcp_read_req(Osp_tcp osp_tcp)
{
	ssize_t ret;
	char req[2];

	// Read in the request byte
	ret = read_tcp_server_stream(osp_tcp->conn_fd, req, sizeof(req));
	if (ret < 0) {
		perror("Failed to read connection\n");
		return -1;
	} else if (ret == 0){ 
		return OSP_DISCONNECT;
	}

	// Not a valid request.  Should never reach here, since we're writing the Android
	// and C code.  The only way we'd get here is if the versions of code running on
	// Android and C aren't the same.  And we should be checking that a different way
	if (req[0] > OSP_REQ_LAST) {
		return -1;
	}

	printf("Values for req: %d: %d\n", req[0], req[1]);

	if (req[1] != VERSION) {
		fprintf(stderr, "NOT CORRECT VERSION. C VERSION IS %d, GOT ANDROID VERSION %d\n", VERSION, req[1]);
		return OSP_WRONG_VERSION;
	}

	return req[0];
}

ssize_t osp_tcp_read_values(Osp_tcp osp_tcp, osp_user_data *data, unsigned int size)
{
	ssize_t ret;

	// Read in the request byte
	ret = read_tcp_server_stream(osp_tcp->conn_fd, (char *)data, size);
	if (ret < 0) {
		perror("Failed to read connection\n");
		return -1;
	} else if (ret == 0){ 
		return 0;
	}

	return ret;
}

ssize_t osp_tcp_read_user_packet(Osp_tcp osp_tcp, char *message, unsigned int size)
{
	ssize_t ret;
	int req;

	ret = read_tcp_server_stream(osp_tcp->conn_fd, (char *)&req, sizeof(req));
	if (ret < 0) {
		perror("Failed to read connection\n");
		return -1;
	} else if (ret == 0){ 
		return OSP_DISCONNECT;
	}

	if (size < ret) {
		fprintf(stderr, "Not enough memory allocated in message to contain xmission\n");
		return -1;
	}

	ret = read_tcp_server_stream(osp_tcp->conn_fd, message, req);
	if (ret < 0) {
		perror("Failed to read connection\n");
		return -1;
	} else if (ret == 0){ 
		return OSP_DISCONNECT;
	}

	message[req] = '\0';

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
