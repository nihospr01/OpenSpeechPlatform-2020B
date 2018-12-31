//
// Created by Kuan-Lin Chen on 2018/10/10.
//

#ifndef OSP_PREFILTER_H
#define OSP_PREFILTER_H

#define PREFILTER_SIZE 3

float prefilter[PREFILTER_SIZE] = {
        1.0f,
        -2.01f,
        1.0f
};
/*
float prefilter[PREFILTER_SIZE] = {
        0.1170f,
        -0.6034f,
        0.6034f,
        -0.1170f
};*/

#endif //OSP_PREFILTER_H
