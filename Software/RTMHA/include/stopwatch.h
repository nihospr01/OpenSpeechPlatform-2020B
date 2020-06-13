//
// Created by nal on 4/8/20.
//

#ifndef OPENSPEECHPLATFORM_STOPWATCH_H
#define OPENSPEECHPLATFORM_STOPWATCH_H


//#define STOPWATCH1
//#define BF_ADAPT

#ifdef STOPWATCH1
#define RECORD_TIME 900000
#include <cstdlib>
#include <fstream>
#include <chrono>
#endif

#ifdef STOPWATCH1
#define OVERALL
#define STATS


#include <algorithm>
#include <iomanip>
#include <sstream>
#include <iostream>

std::string GetCurrentTimeForFileName()
{
    auto time = std::time(nullptr);
    std::stringstream ss;
    ss << std::put_time(std::localtime(&time), "%F_%T"); // ISO 8601 without timezone information.
    std::cout << std::put_time(std::localtime(&time), "%F_%T") << std::endl;
    auto s = ss.str();
    std::replace(s.begin(), s.end(), ':', '-');
    return s;
}



std::chrono::steady_clock::time_point end_time, start_time;
double dur[RECORD_TIME];
int counter;
std::string filename;

#ifdef STATS
std::chrono::steady_clock::time_point stats_start_time[2], stats_end_time[2];

double stats_dur[2];
double DOWN_sum[2] = {0};
double DOWN_sum_sq[2] = {0};
double FB_sum[2] = {0};
double FB_sum_sq[2] = {0};
double WDRC_sum[2] = {0};
double WDRC_sum_sq[2] = {0};
double GMPO_sum[2] = {0};
double GMPO_sum_sq[2] = {0};
double AFC_sum[2] = {0};
double AFC_sum_sq[2] = {0};
double UP_sum[2] = {0};
double UP_sum_sq[2] = {0};

void printStats(){
    double sum = 0;
    double sum_sq = 0;
    double n = (double)counter;
    double DOWN_mean = (DOWN_sum[0] + DOWN_sum[1]) / (2.0*n);
    double DOWN_std = sqrt((DOWN_sum_sq[0] + DOWN_sum_sq[1])/(2.0*n) - DOWN_mean * DOWN_mean);
    std::cout << "Downsample Stats: " << DOWN_mean << ", " << DOWN_std << std::endl;
    double FB_mean = (FB_sum[0] + FB_sum[1]) / (2.0*n);
    double FB_std = sqrt((FB_sum_sq[0] + FB_sum_sq[1])/(2.0*n) - FB_mean * FB_mean);
    std::cout << "Filterbank Stats: " << FB_mean << ", " << FB_std << std::endl;
    double WDRC_mean = (WDRC_sum[0] + WDRC_sum[1]) / (2.0*n);
    double WDRC_std = sqrt((WDRC_sum_sq[0] + WDRC_sum_sq[1])/(2.0*n) - WDRC_mean * WDRC_mean);
    std::cout << "WDRC Stats: " << WDRC_mean << ", " << WDRC_std << std::endl;
    double GMPO_mean = (GMPO_sum[0] + GMPO_sum[1]) / (2.0*n);
    double GMPO_std = sqrt((GMPO_sum_sq[0] + GMPO_sum_sq[1])/(2.0*n) - GMPO_mean * GMPO_mean);
    std::cout << "GMPO Stats: " << GMPO_mean << ", " << GMPO_std << std::endl;
    double AFC_mean = (AFC_sum[0] + AFC_sum[1]) / (2.0*n);
    double AFC_std = sqrt((AFC_sum_sq[0] + AFC_sum_sq[1])/(2.0*n) - AFC_mean * AFC_mean);
    std::cout << "AFC Stats: " << AFC_mean << ", " << AFC_std << std::endl;
    double UP_mean = (UP_sum[0] + UP_sum[1]) / (2.0*n);
    double UP_std = sqrt((UP_sum_sq[0] + UP_sum_sq[1])/(2.0*n) - UP_mean * UP_mean);
    std::cout << "Upsample Stats: " << UP_mean << ", " << UP_std << std::endl;

    for(int i = 0; i < RECORD_TIME; i++){
        sum += dur[i];
        sum_sq += dur[i]*dur[i];
    }

    double Overall_mean = sum / n;
    double Overall_std = sqrt(sum_sq/n - Overall_mean * Overall_mean);
    std::cout << "Overall Stats: " << Overall_mean << ", " << Overall_std << std::endl;

}

#endif


#endif


#endif //OPENSPEECHPLATFORM_STOPWATCH_H
