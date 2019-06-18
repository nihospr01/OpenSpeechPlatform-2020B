//
// Created by dsengupt on 9/19/18.
//

#ifndef OSP_NOISE_MANAGEMENT_H
#define OSP_NOISE_MANAGEMENT_H

#include <mutex>
#include <cstddef>
#include <atomic>

/**
 * @brief Noise Management Class
 * @details Speech enhancement using peak and valley detection, noise estimation and spectral subtraction
 */
class noise_management{

public:
    /**
     * @brief Noise management constructor
     * @param[in] ntype The type of noise estimation, 1: using limits on change (ref: Arslan et al.), 2: using the weighted averaging of Hirsch and Ehrlicher, 3: using MCRA of Cohen and Berdugo
     * @param[in] stype The type of spectral subtraction, 0: normal, 1: oversubtraction
     * @param[in] sparam A parameter for spectral subtraction
     * @param[in] fsamp The sampling rate
     */
    explicit noise_management(int ntype, int stype, float sparam, float fsamp);
    /**
     * @brief Noise management destructor
     */
    ~noise_management();

    /**
     * @brief Setting all parameters in noise management
     * @param[in] ntype See constructor
     * @param[in] stype See constructor
     * @param[in] sparam See constructor
     */
    void set_param(int ntype, int stype, float sparam);

    /**
     * @brief Getting all parameters in noise management
     * @param[in] ntype See constructor
     * @param[in] stype See constructor
     * @param[in] sparam See constructor
     */
    void get_param(int &ntype, int &stype, float &sparam);

    /**
     * @brief A function to perform speech enhancement
     * @param[in] data_in The input signal
     * @param[in] in_len Length of the input signal
     * @param[out] data_out The output signal, i.e., the enhanced speech signal
     */
    void speech_enhancement(float* data_in, size_t in_len, float* data_out);

private:

    struct  nm_t{
        int ntype;
        int stype;
        float sparam;
    };

    std::atomic<nm_t*> global_current;
    std::atomic<nm_t*> global_next;

    float fsamp;
    float att;
    float rel;
    //
    float aLP;
    float rLP;
    float tau;
    float aLPv;
    float rLPv;
    float inc;
    float dec;
    float lin_inc;
    float lin_dec;
    float a1;
    float a2;
    float aHE;
    float bHE;
    float aCB;
    float bCB;
    float xabs;
    //
    float delta;
    //
    float n1;
    float prob;
    float p;
    float b;
    //
};

#endif //OSP_NOISE_MANAGEMENT_H
