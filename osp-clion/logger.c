#include <stdio.h>
#include <time.h>
#include <stdlib.h>
#include "common/constants.h"
#include <unistd.h>

FILE *file_logger_init(char *filename)
{
	FILE *fd;
	time_t t = time(NULL);
	struct tm tm = *localtime(&t);
	char formatted_filename[FILENAME_MAX];

	if (filename) {
		sprintf(formatted_filename, "%s_%d_%d_%d.%d:%d:%d.txt",
				filename, tm.tm_year + 1900, tm.tm_mon + 1, tm.tm_mday, tm.tm_hour, tm.tm_min, tm.tm_sec);
	} else {
		sprintf(formatted_filename, "%d_%d_%d.%d:%d:%d.txt",
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
