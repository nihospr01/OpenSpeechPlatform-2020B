/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

/**
 * @file osp_tcp.h
 * This layer is the interface to the client side of the OSP application.
 */

#ifndef OSP_TCP_H__
#define OSP_TCP_H__

#include <stddef.h>
#include "constants.h"

typedef struct osp_tcp_t *Osp_tcp;

#define OSP_WRONG_VERSION		0x00
#define OSP_REQ_UPDATE_VALUES	0x01	///< Request to set the values in the real-time OSP C application
#define	OSP_REQ_GET_UNDERRUNS	0x02	///< Request to get number of underruns from real-time OSP application
#define OSP_REQ_USER_ID			0x03
#define OSP_REQ_USER_ACTION		0x04
#define OSP_REQ_GET_NUM_BANDS	0x05
#define OSP_DISCONNECT			0x06
#define OSP_REQ_LAST			0x07	///< This exists so we can test if an inc packet is one we recognize

/**
 * @brief Initialization function for the OSP TCP layer
 *
 * @see osp_tcp_t
 * @param port The port in which to listen on for a client connection
 * @return Returns the allocated instance of the OSP TCP layer data structure ("object")
 */
Osp_tcp osp_tcp_init(int port);

/**
 * @brief Blocking call that sets up server socket and waits for client connection
 * In order to start a socket for a client (Android tablet, for instance), this must
 * be called.  It will block until a client establishes a connection, at which point it
 * will return 0.
 *
 * @see osp_tcp_t
 * @param osp_tcp The instance of the Osp_tcp variable that was returned from 
 * osp_tcp_init
 * @return Returns 0 on success, or -1 if there's an error setting up the socket
 * 
 */
int osp_tcp_connect(Osp_tcp osp_tcp);

/**
 * @brief Blocking call that returns the request sent by the connected client.
 * The client can make a number of requests including updating the values running in the OSP
 * real-time code (knee points, attack, release, gains, etc).  The client can also request
 * the number of underruns that have been counted from the audio driver, the number of bands
 * that have been initially set up, etc.
 *
 * @see osp_tcp_t
 * @see e_osp_tcp_req_t
 * @param osp_tcp The instance of the Osp_tcp variable that was returned from osp_tcp_init
 * @return Returns the request sent by the client
 */
char osp_tcp_read_req(Osp_tcp osp_tcp);

/**
 * @brief Reads data from the TCP stream into the osp_user_data structure
 *
 * @see osp_user_data
 * @see osp_tcp_t
 * @param osp_tcp The instance of the Osp_tcp variable that was returned from osp_tcp_init
 * @param data The osp_user_data structure that will be filled with data from the stream
 * @param size The size of the data, in bytes, that are to be read from the stream
 * since we're reading into a struct of known size, passing a size variable might be redundant
 * @return Returns 0 if the stream is no longer present (disconnect from client side, not error), -1
 * if there is an error reading from the server stream.  Otherwise, the number of bytes read from
 * the stream is returned
 */
ssize_t osp_tcp_read_values(Osp_tcp osp_tcp, osp_user_data *data, unsigned int size);
/**
 * @brief Sends the number of underruns to client
 *
 * @see osp_tcp_t
 * @param osp_tcp The instance of the Osp_tcp variable that was returned from osp_tcp_init
 * @param underruns Number of underruns to report
 * @return Returns 0 if success, -1 if there was an error writing to the stream
 */
int osp_tcp_send_underruns(Osp_tcp osp_tcp, unsigned int underruns);

ssize_t osp_tcp_read_user_packet(Osp_tcp osp_tcp, char *message, unsigned int size);


/**
 * @brief Sends the number of bands to client
 *
 * @see osp_tcp_t
 * @param osp_tcp The instance of the Osp_tcp variable that was returned from osp_tcp_init
 * @param bands Number of bands to report
 * @return Returns 0 if success, -1 if there was an error writing to the stream
 */
int osp_tcp_send_num_bands(Osp_tcp osp_tcp, unsigned char bands);

/**
 * @brief Closes the TCP stream and frees the Osp_tcp instance
 *
 * @see osp_tcp_t
 * @param osp_tcp The instance of the Osp_tcp variable that was returned from osp_tcp_init
 */
void osp_tcp_close(Osp_tcp osp_tcp);

#endif /* OSP_TCP_H__ */
