#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <time.h>

static int print_header(FILE *in_file, FILE *out_file, char *file_name)
{
	long size;
	time_t t = time(NULL);
	struct tm tm = *localtime(&t);

	if (fseek(in_file, 0L, SEEK_END) < 0) {
		fprintf(stderr, "Error seeking to end of input file\n");
		return -1;
	}

	if ((size = ftell(in_file)) < 0) {
		fprintf(stderr, "Error getting size of file\n");
		return -1;
	}

	rewind(in_file);

	fprintf(out_file, "# File %s generated on %d-%d-%d %d:%d:%d\n",
		file_name, tm.tm_year + 1900, tm.tm_mon + 1, tm.tm_mday, tm.tm_hour, tm.tm_min, tm.tm_sec);
	fprintf(out_file, "# Number of taps: %lu\n", size / sizeof(float));
	fprintf(out_file, "#\n#\n");

	return 0;
}

int main(int argc, char *argv[])
{
	char *output_file;
	unsigned long string_len;
	unsigned long tap_count;
	float tap;

	FILE *in_file;
	FILE *out_file;

	if (argc != 2) {
		fprintf(stderr, "No input file\n");
		return 0;
	}

	string_len = (unsigned long)strlen(argv[1]);
	if (string_len < 3) {
		fprintf(stderr, "Input file name too long\n");
		return 0;
	}

	output_file = malloc(string_len);

	printf("Opening file %s\n", argv[1]);

	strncpy(output_file, argv[1], string_len - 3);

	output_file[string_len - 3] = 'f';
	output_file[string_len - 2] = 'l';
	output_file[string_len - 1] = 't';
	output_file[string_len] = '\0';

	printf("Dumping to file %s\n", output_file);

	if ((in_file = fopen(argv[1], "rb")) == NULL) {
		fprintf(stderr, "Error opening input file\n");
		goto abort;
	}

	if ((out_file = fopen(output_file, "w")) == NULL) {
		fprintf(stderr, "Error opening output file\n");
		fclose(in_file);
		goto abort;
	}

	tap_count = 0;
	if (print_header(in_file, out_file, output_file) < 0) {
		goto abort2;
	}

	while (fread(&tap, sizeof(float), 1, in_file) == 1) {
		printf("Tap[%lu] = %.10e\n", tap_count, tap);
		fprintf(out_file, "%.10e\n", tap);
		tap_count++;
	}

abort2:
	fclose(in_file);
	fclose(out_file);

abort:
	free(output_file);
	return 0;
}
