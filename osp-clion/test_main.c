//
// Created by nal on 4/26/18.
//

#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include "loopback.h"
#include "test.h"

void * test (void * tid) {
    float inL[48], inR[48], outL[48], outR[48];
    struct timespec s, e;
    int counter = 0;
    double avg_time=1;
    while (1) {
        for (int i = 0; i < 48; i++) {
            inL[i] = ((float) rand() / (float) (RAND_MAX) * 2 - 1 + outL[i]) / 2;
            inR[i] = ((float) rand() / (float) (RAND_MAX) * 2 - 1 + outR[i]) / 2;
        }
        CLOCK(s);
        run_loopback(inL, inR, outL, outR, 48);
        CLOCK(e);
        avg_time = RAVG(avg_time, s, e);
        if(counter%1000 == 0){
            printf("Total is %f\t", avg_time);
            printf("Subband is %f\t", (subband[0] + subband[1] + subband[2] +subband[3] +subband[4] +subband[5])*2);
            printf("WDRC is %f\t", (wdrc_avg[0] + wdrc_avg[1] + wdrc_avg[2] +wdrc_avg[3] +wdrc_avg[4] +wdrc_avg[5])*2);
            printf("Speech is %f\t", (speech[0] + speech[1] + speech[2] +speech[3] +speech[4] +speech[5])*2);
            printf("Afc estimation is %f\t", afc*2);
            printf("Afc filter is %f\t", afc_filter*2);
            printf("Afc update is %f\t", afc_update*2);

        }
        counter ++;

    }
}