/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */

#include <stdio.h>
#include <stdlib.h>
#include <limits.h>

int load_filter_taps(const char *file, float *taps, int len)
{
	FILE *in;
	int size;
	
	const char default_filter_path_base[PATH_MAX] = "/usr/local/etc/osp";
	
	const char* filter_path_base;
	filter_path_base = getenv("OSP_FILTER_PATH_BASE");
	if (filter_path_base == NULL) {
		filter_path_base = default_filter_path_base;
	}
	
	char filter_file[PATH_MAX] = "";
	sprintf(filter_file, "%s/%s", filter_path_base,file);

#undef BINARY_TAPS
#ifdef BINARY_TAPS
	if ((in = fopen(filter_file, "rb")) == NULL) {
		printf("Error opening file: %s\n", file);
		perror("1");
		return -1;
	}

	if (fseek(in, 0L, SEEK_END) < 0) {
		printf("Error seeking to end of file: %s\n", file);
		perror("2");
		return -1;
	}

	if ((size = ftell(in)) < 0) {
		printf("Error getting size of file %s\n", file);
		perror("3");
		return -1;
	}

	size = size / sizeof(float);

	if (size != len) {
		printf("The size of filter data does not match requested\n");
		perror("4");
		return -1;
	}

	rewind(in);

	if (fread(taps, sizeof(float), size, in) != (size_t)size) {
		printf("Error reading filter file: %s\n", file);
		perror("5");
		return -1;
	}

	if (fclose(in) != 0) {
		printf("Error on closing filter file: %s\n", file);
		perror("6");
		return -1;
	}

	return size / 4;
#else /* BINARY_TAPS */
	char input_str[256];

	if ((in = fopen(filter_file, "r")) == NULL) {
		printf("Error opening file: %s\n", file);
		perror("1");
		return -1;
	}

	size = 0;
	// First check to see if the size matches.
	// TODO in the future, I'll probably allocate a buffer here so that we
	// load the number of taps that are present in the file instead of relying
	// on the code one level up to specify the size, and so we don't have to test
	// it
	while (fgets(input_str, sizeof(input_str), in)) {
		if (input_str[0] == '#' || input_str[0] == '\n') {
			continue;
		}

		size++;
	}

	if (size != len) {
		printf("The size of the filter data does not match requested\n");
		return -1;
	}

	printf("LOADING FILTER TAPS FROM %s.  NUMBER OF TAPS: %d\n", filter_file, size);

	rewind(in);
	size = 0;
	while (fgets(input_str, sizeof(input_str), in)) {
		if (input_str[0] == '#' || input_str[0] == '\n') {
			continue;
		}

		if (sscanf(input_str, "%f", &taps[size]) == EOF) {
			printf("Error scanning in float filter tap\n");
			return -1;
		}

		size++;
	}

	return size;
#endif /* BINARY_TAPS */
}

int save_filter_taps(const char *file, float *taps, int len)
{
	FILE *out;

	if ((out = fopen(file, "wb")) == NULL) {
		printf("error opening file: %s\n", file);
		perror("1");
		return -1;
	}

	if (fwrite(taps, sizeof(float), len, out) < len) {
		printf("error writing file: %s\n", file);
		perror("2");
		return -1;
	}

	if (fclose(out) != 0) {
		printf("Error on closing filter file: %s\n", file);
		perror("3");
		return -1;
	}

	return 0;	
}
