#include <OSP/subband/wdrc10.hpp>
#include <cmath>

/**
 * @brief Fast Approximation of the Magnitude of a Complex Number
 *
 * https://en.wikipedia.org/wiki/Alpha_max_plus_beta_min_algorithm
 *
 * This should have an max error around 1%.  If 4% error
 * is acceptable, we could double the speed of this calculation.
 *
 * @param num1 one side or part
 * @param num2 one side or part
 * @return float  The magnitude
 */
static float alpha_beta_mag(float num1, float num2) {
    float sum1, sum2;

    num1 = fabs(num1);
    num2 = fabs(num2);

    // Find two sums
    if (num1 > num2) {
        sum1 = 0.989 * num1 + 0.197 * num2;
        sum2 = 0.833 * num1 + 0.564 * num2;
    } else {
        sum1 = 0.989 * num2 + 0.197 * num1;
        sum2 = 0.833 * num2 + 0.564 * num1;
    }

    // Return the largest sum
    if (sum1 > sum2) {
        return sum1;
    }
    return sum2;
}

/**
 * @brief Compute the magnitude of the elements in the array
 *
 * @param arr_real The real components
 * @param arr_imag  The imaginary components
 * @param arr_mag  The output array for the magnitude
 * @param len The length of the arrays
 */
static void compute_magnitude(float *arr_real, float *arr_imag, float *arr_mag, const int len) {
    for (int i = 0; i < len; i++) arr_mag[i] = alpha_beta_mag(arr_real[i], arr_imag[i]);
}

wdrc10::wdrc10(float gain50, float gain80, float kneelow, float mpo) : gain50_(gain50), gain80_(gain80) {

    std::shared_ptr<wdrc10_param_t> data_current = std::make_shared<wdrc10_param_t>();
    data_current->m = (30 + gain80 - gain50) / 30;
    data_current->b = 50 + gain50 - data_current->m * 50;
    data_current->c = data_current->m * kneelow + data_current->b - kneelow;
    data_current->kneeup = (mpo - data_current->b) / data_current->m;
    data_current->kneelow = kneelow;
    data_current->mpo = mpo;
    releasePool.add(data_current);
    std::atomic_store(&currentParam, data_current);
}

wdrc10::~wdrc10() {
    releasePool.release();
}

void wdrc10::set_param(float gain50, float gain80, float kneelow, float mpo) {
    std::shared_ptr<wdrc10_param_t> data_next = std::make_shared<wdrc10_param_t>();

    gain50_ = gain50;
    gain80_ = gain80;
    data_next->kneelow = kneelow;
    data_next->mpo = mpo;
    data_next->m = (30 + gain80 - gain50) / 30;
    data_next->b = 50 + gain50 - data_next->m * 50;
    data_next->c = data_next->m * kneelow + data_next->b - kneelow;
    data_next->kneeup = (mpo - data_next->b) / data_next->m;

    releasePool.add(data_next);
    std::atomic_store(&currentParam, data_next);
    releasePool.release();
}

void wdrc10::get_param(float &gain50, float &gain80, float &kneelow, float &mpo) {
    std::shared_ptr<wdrc10_param_t> data_current = std::atomic_load(&currentParam);
    gain50 = gain50_;
    gain80 = gain80_;
    kneelow = data_current->kneelow;
    mpo = data_current->mpo;
}

void wdrc10::process(float *input, float *output, const int length) {
    std::shared_ptr<wdrc10_param_t> data_current = std::atomic_load(&currentParam);
    float kneelow = data_current->kneelow;
    float kneeup = data_current->kneeup;
    float mpo = data_current->mpo;
    float m = data_current->m;
    float b = data_current->b;
    float c = data_current->c;

    float R;
    float error;
    float mag_log;
    float magnitude[length];
    float out_real[length];
    float out_imag[length];

    hilbert.process(input, out_real, out_imag, length);
    compute_magnitude(out_real, out_imag, magnitude, length);

    // These are needed for my FIR DERIVATIVE filter. Please replace with OSP FIR Filter
    float reg[21] = {0};
    float deriv;

    for (int i = 0; i < length; i++) {
        // Get output for FIR DERIVATIVE Filter
        // use fir formii
        // (Please replace this with OSP FIR Filter, this is just the best I can do)
        for (int j = 20; j > 0; j--) {
            reg[j] = reg[j - 1];
        }
        reg[0] = magnitude[i];

        deriv = 0;
        for (int j = 0; j < 21; j++) {
            deriv += reg[j] * fir_deriv[j];
        }
        // End FIR DERIVATIVE Filter

        mag_log = 20 * log10(magnitude[i]);

        // Finding the desired output magnitude for the given input magnitude (WDRC)
        if (mag_log < kneelow) {
            R = mag_log + c;
        } else if (mag_log > kneeup) {
            R = mpo;
        } else {
            R = m * mag_log + b;
        }

        // Finding error between desired magnitude and current magnitude
        if (deriv >= 0) {
            error = alpha1 * (R - mag_log - 20 * log10f(saved_gain));
        } else {
            error = alpha2 * (R - mag_log - 20 * log10f(saved_gain));
        }

        // Save gain:
        output[i] = input[i] * saved_gain;  // Multiply the signal by the gain

        // Update gain:
        saved_gain *= powf(10, (error / 20));
    }
}
