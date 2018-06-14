/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/time.h>
#include <fcntl.h>
#include <netinet/in.h>
#include <netinet/tcp.h>
#include <arpa/inet.h>
#include "tcplib.h"

/**
 * @brief Starts the server side socket connection based on INET Internet stream-oriented
 *			sockets.
 */
int start_tcp_server(int port)
{
    struct sockaddr_in s_addr;
    int sockfd;
    int val = 1;

    bzero((char *) &s_addr, sizeof(s_addr));

	if ((sockfd = socket(AF_INET, SOCK_STREAM, 0)) == -1) {
		perror("Could not open Socket!");
		return -1;
	}

    if (setsockopt(sockfd,SOL_SOCKET,SO_REUSEADDR,&val,sizeof(val)) == -1) {
		perror("Could not set socket option (SO_REUSEADDR)!");
		return -1;
	}

    if (setsockopt(sockfd,IPPROTO_TCP,TCP_NODELAY,&val,sizeof(val)) == -1) {
		perror("Could not set socket option (TCP_NO_DELAY)!");
		return -1;
	}

    //fcntl(sockfd,F_SETFL,FNDELAY);

	s_addr.sin_family = AF_INET;
	s_addr.sin_port = htons(port);
	s_addr.sin_addr.s_addr = htonl(INADDR_ANY);
	//bzero(&(s_addr.sin_zero),8); //Not used

	if (bind(sockfd, (struct sockaddr *)&s_addr, sizeof(struct sockaddr)) < 0) {
		perror("Unable to bind to port");
		return -1;
	}

	if (listen(sockfd, 1) < 0) {
		perror("Not able to Listen on socket");
		return -1;
	}

    return sockfd;
} 

/**
 * @details
 */
int get_tcp_server_connection(int sock)
{
	socklen_t sin_size;
    int connection = 0;
    struct sockaddr_in c_addr;

	sin_size = sizeof(struct sockaddr_in);
	connection = accept(sock, (struct sockaddr *)&c_addr, &sin_size);

    if (connection > 0) {
        printf("Got connection from (%s , %d) [sock = %d]\n",
                    inet_ntoa(c_addr.sin_addr),ntohs(c_addr.sin_port),sock);
    } else {
        connection = 0;
    }

    return connection;
}

/**
 * @details
 */
int close_tcp_server_connection(int connFd)
{
    return close(connFd);
}

/**
 * @details
 */
void stop_tcp_server(int sock)
{
    int res;
    res = close(sock);
    if (!res) sock = 0;
}

/**
 * @details
 */
ssize_t read_tcp_server_stream(int connFd, char *recv_data, int len)
{
    ssize_t bytes_read = 0;

    if (connFd > 0) {
        bytes_read = recv(connFd,recv_data,len,0);
        return bytes_read;
    }

    return -1;
}

/**
 * @details Starts the client side socket connection based on INET Internet stream-oriented
 *			sockets.
 */
int start_tcp_client(char *ip, int port)
{
    struct sockaddr_in p_addr;
    int sock;

    inet_pton(AF_INET,ip,&(p_addr.sin_addr));
    p_addr.sin_port = htons(port);
    p_addr.sin_family = AF_INET;
	bzero(&(p_addr.sin_zero),8);

	if ((sock = socket(AF_INET, SOCK_STREAM, 0)) == -1) {
		perror("Could not open Socket!");
		return -1;
	}

	if (connect(sock, (struct sockaddr *)&p_addr,
				sizeof(struct sockaddr)) == -1) {
		perror("Could not connect to socket on!");
		return -1;
	}

    return sock;
}

/**
 * @details
 */
int stop_tcp_client(int sock)
{
    return close(sock);
}

/**
 * @details
 */
ssize_t send_tcp_client_data(int sock, char *send_data, int len)
{
#ifdef __linux__	
	return send(sock, send_data, len, MSG_NOSIGNAL);
#else
	return send(sock, send_data, len, SO_NOSIGPIPE);
#endif
}
