#include <stdio.h>

int main(int argc, char *argv[])
{
	FILE *in_file;
	char input_str[256];
	float input_flt[200] = {0};
	unsigned int float_count;
	int i;

	if (argc != 2) {
		fprintf(stderr, "Need an input file\n");
		return 0;
	}

	if ((in_file = fopen(argv[1], "r")) == NULL) {
		fprintf(stderr, "Error opening filter tap file\n");
	}

	float_count = 0;
	while (fgets(input_str, sizeof(input_str), in_file)) {
		if (input_str[0] == '#' || input_str[0] == '\n') {
			continue;
		}

		if (sscanf(input_str, "%f", &input_flt[float_count]) == EOF) {
			fprintf(stderr, "Error scanning float filter tap\n");
			break;
		}

		//printf("Float[%d]: %e\n", float_count, input_flt);
		float_count++;
	}

	printf("Read in %d floats\n", float_count);
	fclose(in_file);

	for (i = 0; i < 200; i++) {
		printf("Float[%d] = %e\n", i, input_flt[i]);
	}

	return 0;
}
