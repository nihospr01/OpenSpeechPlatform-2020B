/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#ifndef _P25_UTIL_LOGGER_H_
#define _P25_UTIL_LOGGER_H_

#include <stdio.h>
#include <stdlib.h>

#define MAX_WRITE_LEN	200

#define _TI_DSP
#ifdef _TI_DSP
	#define _PRINT(fmt, args...) do { \
	} while (0)
#else /* _TI_DSP */
	#define _PRINT(fmt, args...) \
		printf(fmt, ##args);
#endif /* _TI_DSP */

#ifdef _DEBUG

static inline void dump(char *data, int len)
{
	int i;

	if (len == 0)
		return;

	_PRINT("%02hhx ", data[0]); 
 	for (i = 1; i < len; i++) { 
 		if ((i % 8) == 0) 
 			_PRINT(" "); 
 		if ((i % 16) == 0) 
 			_PRINT("\n"); 
 		_PRINT("%02hhx ", data[i]); 
 	} 
	_PRINT("\n");
}

#define DBG(fmt, args...)	do {					\
	_PRINT("[debug] %s() " fmt, __FUNCTION__, ##args);		\
} while (0)

#define DBGDATA(data, len)	do {					\
	_PRINT("[debug] %s()\n",  __FUNCTION__);			\
	dump(data, len);						\
} while (0)

#else /* _DEBUG */

#define DBG(fmg, args...)
#define DBGDATA(data, len)
	
#endif /* _DEBUG */

#define ASSERT(cond, fmt, args...) do {					\
	if (!(cond)) {							\
		_PRINT("[assert] %s() " fmt, __FUNCTION__, ##args);	\
		exit(1);						\
	}								\
} while(0)

#define INFO(fmt, args...)						\
	_PRINT("[info] %s() " fmt, __FUNCTION__, ##args);

#define ERROR(fmt, args...)						\
	_PRINT("[error] %s() " fmt, __FUNCTION__, ##args);

#endif /* _P25_UTIL_LOGGER_H_ */
