//
// Created by nal on 4/26/18.
//

#ifndef OSP_CLION_TEST_H
#define OSP_CLION_TEST_H

#define RAVG(avg_time,s,e) (.9 * avg_time + .1*((double)e.tv_nsec * 1e-6 - (double)s.tv_nsec * 1e-6 + (double)(long)(e.tv_sec - s.tv_sec) * 1000))
#define CLOCK(s) (clock_gettime(CLOCK_REALTIME, &s))

double downsample_avg;
double subband[6];
double wdrc_avg[6];
double speech[6];
double afc;
double afc_filter;
double afc_update;
double upsample_avg;
void * test (void * tid);

#endif //OSP_CLION_TEST_H
