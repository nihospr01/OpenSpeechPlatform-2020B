#include <OSP/subband/noise_management.hpp>
#include <OSP/array_utilities/array_utilities.hpp>
#include <math.h>

noise_management::noise_management(int ntype, int stype, float sparam, float fsamp) {
    this->fsamp = fsamp;
    auto data_current = new nm_t;
    auto data_next = new nm_t;
    std::atomic_exchange(&global_current, data_current);
    std::atomic_exchange(&global_next, data_next);
    this->set_param(ntype, stype, sparam);
    /* Attack time in msec */
    att = 3;

    /* Attack filter coefficient */
    aLP = expf(-1.0f / (0.001f * att * fsamp));

    /* Release time in msec */
    rel = 50;

    /* Release filter coefficient */
    rLP = expf(-1.0f / (0.001f * rel * fsamp));

    /* Parameters for the valley detection */

    /* Attack filter coefficient */
    aLPv = expf(-1.0f / (0.01f * fsamp));

    /* Release filter coefficient */
    rLPv = expf(-1.0f / (0.1f * fsamp));

    /* Parameters and storage for the Arslan et al. power averaging */
    /* Maximum increase in dB/sec */
    inc = 10;

    /* Maximum decrease in dB/sec */
    dec = -25;

    lin_inc = powf(10.0f, inc / 20.0f);
    lin_dec = powf(10.0f, dec / 20.0f);

    a1 = pow(lin_inc, 1 / fsamp);
    a2 = pow(lin_dec, 1 / fsamp);

    /* Parameters for the Hirsch and Ehrlicher weighted noise averaging */

    tau = 100;
    /* LP filter coefficient */
    aHE = exp(-1.0f / (0.001f * tau * fsamp));

    /* Speech/noise threshold */
    bHE = 2;

    /* Parameters for the Cohen and Berdugo MCRA */

    delta = 10; //floathreshold for P[speech]

    tau = 10; //floatime constant in msec
    aCB = exp(-1.0f / (0.001f * tau * fsamp)); //LP filter coefficient for ave prob

    tau = 200; //floatime constant in msec
    bCB = exp(-1.0f / (0.001f * tau * fsamp)); //LP filter coefficient for noise ave

    // Parameters for initialization
    prob = 0.5;
    peak_init = 0;
    npow_init = 0;
    valley_init = 0;
}

noise_management::~noise_management(){
    delete global_next;
    delete global_current;
}

void
noise_management::set_param(int ntype, int stype, float sparam) {
    auto data_next = global_next.load();
    data_next->ntype = ntype;
    data_next->stype = stype;
    data_next->sparam = sparam;
    global_next.exchange(global_current.exchange(global_next));
}

void
noise_management::get_param(int &ntype, int &stype, float &sparam) {
    auto data_current = global_current.load();

    ntype = data_current->ntype;
    stype = data_current->stype;
    sparam = data_current->sparam;
}

void
noise_management::speech_enhancement(float *data_in, size_t in_len, float *data_out) {
    float xpow[in_len];
    float npow[in_len];
    float vpow[in_len];
    float peak[in_len];
    float valley[in_len];
    float gain[in_len];

    float n1;
    float p;
    float b;

    auto data_current = global_current.load();
    int ntype = data_current->ntype;
    int stype = data_current->stype;
    float sparam = data_current->sparam;

    // peak[0] = array_mean(data_in, in_len);
    // valley[0] = peak[0];

    peak[0] = (peak_init == 0) ? array_mean(data_in, in_len) : peak_init;
    valley[0] = (valley_init == 0) ? peak[0] : valley_init;

    for (size_t n = 1; n < in_len; n++) {
        xabs = fabsf(data_in[n]);
        /* Peak detect */
        if (xabs >= peak[n - 1])
            peak[n] = aLP * peak[n - 1] + (1 - aLP) * xabs;
        else
            peak[n] = rLP * peak[n - 1];

        /* Valley detect */

        if (xabs <= valley[n - 1])
            valley[n] = aLPv * valley[n - 1] + (1 - aLPv) * xabs;
        else
            valley[n] = valley[n - 1] / rLPv;
    }

    array_square(peak, xpow, in_len);

    /* Noise power estimation */

    npow[0] = (npow_init == 0) ? xpow[0] : npow_init;
    array_square(valley, vpow, in_len);

    if (ntype == 1) {
        /* Power estimation using limits on change (ref: Arslan et al.) */
        for (size_t n = 1; n < in_len; n++) {
            n1 = (xpow[n] < a1 * npow[n - 1]) ? xpow[n] : a1 * npow[n - 1];
            npow[n] = (n1 > a2 * npow[n - 1]) ? n1 : a2 * npow[n - 1];
        }

    } else if (ntype == 2) {
        /* Noise power estimation using the weighted averaging of Hirsch and Ehrlicher */
        for (size_t n = 1; n < in_len; n++) {
            /* Update noise average if new signal sample is close to the noise */
            if (xpow[n] < bHE * npow[n - 1])
                npow[n] = aHE * npow[n - 1] + (1 - aHE) * xpow[n];
                /* Otherwise keep the previous noise estimate */
            else
                npow[n] = npow[n - 1];

        }
    } else if (ntype == 3) {
        /* Noise power estimation using MCRA of Cohen and Berdugo */
        // prob = 0.5; // Prob of first sample being speech
        for (size_t n = 1; n < in_len; n++) {
            // P[speech] for this sample
            if (xpow[n] > delta * vpow[n])
                p = 1;
            else
                p = 0;
            prob = aCB * prob + (1 - aCB) * p; //Smooth P[speech]

            b = bCB + (1 - bCB) * prob; //Convert P[speech] into time constant

            npow[n] = b * npow[n - 1] + (1 - b) * xpow[n]; //Average the noise
            //printf("%f\n",npow[n]);
        }
    }

    /* Spectral subtraction gain (linear scale) */
    if (stype == 1)
        /* oversubtraction */
    {
        for (size_t n = 0; n < in_len; n++) {
            gain[n] = (xpow[n] - npow[n] * sparam + 0.000001f) / (xpow[n] + 0.000001f);
            if (gain[n] <= 0)
                gain[n] = 1E-30;
            data_out[n] = gain[n] * data_in[n];
        }
    } else if (stype == 0) {
        for (size_t n = 0; n < in_len; n++)
            data_out[n] = data_in[n];

    } else {
        printf("Wrong data_in for Spectral subtraction\n");
    }

    peak_init = peak[in_len - 1];
    npow_init = npow[in_len - 1];
    valley_init = valley[in_len - 1];
}
