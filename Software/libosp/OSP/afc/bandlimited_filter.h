
//
// Created by Kuan-Lin Chen on 2018/10/10.
//

#ifndef OSP_BANDLIMITED_FILTER_H
#define OSP_BANDLIMITED_FILTER_H

#define BANDLIMITED_FILTER_SIZE 3


float bandlimited_filter[BANDLIMITED_FILTER_SIZE] = {
        1.0f,
        -1.8f,
        0.81f
};

/*
float bandlimited_filter[BANDLIMITED_FILTER_SIZE] = {
        -0.0876f,
        -0.7904f,
        0.7904f,
        0.0876f
};*/
#endif //OSP_BANDLIMITED_FILTER_H
