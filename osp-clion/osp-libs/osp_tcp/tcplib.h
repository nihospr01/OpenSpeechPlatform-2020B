/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#ifndef TCPLIB_H__
#define TCPLIB_H__

#include <stddef.h>

/**
 * @brief Initiates TCP protocol server.
 */
int start_tcp_server(int);
/**
 * @brief Gets TCP protocol server.
 */
int get_tcp_server_connection(int);
/**
 * @brief Closes TCP protocol server.
 */
int close_tcp_server_connection(int);
/**
 * @brief Stops the TCP protocol server.
 */
void stop_tcp_server(int);
/**
 * @brief Reads data on server side.
 */
ssize_t read_tcp_server_stream(int, char *, int);
/**
 * @brief Initiates TCP protocol client.
 */
int start_tcp_client(char *, int);
/** 
 * @brief Stops TCP protocol client.
 */
int stop_tcp_client(int);
/**
 * @brief Sends data on client side.
 */
ssize_t send_tcp_client_data(int, char *, int);

#endif /* TCPLIB_H__ */
