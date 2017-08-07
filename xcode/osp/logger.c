/*
 Copyright 2017  Regents of the University of California
 
 Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 
 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 
 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 */


#include <sys/types.h>
#include <sys/stat.h>
#include <stdio.h>
#include <time.h>
#include <stdlib.h>
#include "constants.h"
#include <unistd.h>

FILE *file_logger_init(char *filename)
{
	FILE *fd;
	time_t t = time(NULL);
	struct tm tm = *localtime(&t);
	char formatted_filename[FILENAME_MAX];
	char file_path[FILENAME_MAX];
	struct stat st = {0};
	
	const char* filter_path_base;
	filter_path_base = getenv("FILE_PATH");
	
	sprintf(file_path, "%s/logs", filter_path_base);
	
	if (stat(file_path, &st) == -1) {
		mkdir(file_path, 0700);
	}
	
	if (filename) {
		sprintf(formatted_filename, "%s/%s_%d_%d_%d.%d:%d:%d.txt", file_path,
						filename, tm.tm_year + 1900, tm.tm_mon + 1, tm.tm_mday, tm.tm_hour, tm.tm_min, tm.tm_sec);
	} else {
		sprintf(formatted_filename, "%s/%d_%d_%d.%d:%d:%d.txt", file_path,
						tm.tm_year + 1900, tm.tm_mon + 1, tm.tm_mday, tm.tm_hour, tm.tm_min, tm.tm_sec);
	}
	
	fd = fopen(formatted_filename, "w");
	if (fd == NULL) {
		fprintf(stderr, "Failed to open file '%s'.  Does directory exist?\n", formatted_filename);
	}
	
	return fd;
}

int file_logger_log_osp_data(FILE *fd, osp_user_data *data)
{
	time_t t = time(NULL);
	struct tm *tm = localtime(&t);
	char header[64];
	int i;
	
	if (fd == NULL) {
		fprintf(stderr, "File is not opened, not writing to log\n");
		return 0;
	}
	
	if (strftime(header, sizeof(header), "%c", tm) > sizeof(header)) { // Should really never be the case
		printf("Outout string was too long\n");
		return -1;
	}
	
	fprintf(fd, "%s\n", header);
	
	sync();
	
	for (i = 0; i < NUM_BANDS; i++) {
		fprintf(fd, "BAND %d\n", i);
		fprintf(fd, "Gain 50: %d, Gain 80: %d, Knee Low: %d, Knee High: %d, attack: %d, release: %d\n",
						data->g50[i], data->g80[i], data->knee_low[i], data->knee_high[i], data->attack[i], data->release[i]);
	}
	
	return 0;
}

void file_logger_log_message(FILE *fd, char *message)
{
	fprintf(fd, "%s\n", message);
}

void file_logger_close(FILE *fd)
{
	if (fd != NULL) {
		fclose(fd);
	}
}
