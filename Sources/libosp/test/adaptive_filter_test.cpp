//
// Created by Kuan-Lin Chen on 10/19/18.
//
#include "gtest/gtest.h"

#include <iostream>
#include <array_file/array_file.hpp>
#include <adaptive_filter/adaptive_filter.hpp>  /// include the module header which will be tested
#include <math.h>
#include <time.h>

void check_results(float *golden, float *test, unsigned int len) {
    double amin = golden[0];
    double amax = golden[0];
    double merr = 0;
    double mae = 0;
    double sum_square_error = 0;

    for (unsigned int n = 0; n < len; n++) {
        if (golden[n] > amax)
            amax = golden[n];
        if (golden[n] < amin)
            amin = golden[n];

        double abs_error = abs(golden[n] - test[n]);

        // find the maximum absolute error
        if (merr < abs_error)
            merr = abs_error;

        mae += abs_error;

        // sum of squared error
        sum_square_error += (golden[n] - test[n]) * (golden[n] - test[n]);
    }

    double max_error_percent = 100 * merr / (amax - amin);
    ASSERT_LT(max_error_percent, 1e-4);

    printf("max error = %e (%f.2 %%)\n", merr, max_error_percent);
    

    double rmse = sqrt((sum_square_error) / len);
    double nrmse = rmse / (amax - amin);
    ASSERT_LT(nrmse, 1e-6);
    
    printf("rmse=%e  nrmse=%e \n", rmse, nrmse);
}

TEST(libosp, AdaptiveFilter) {
    srand(time(NULL));

    /// parameters which need to be set (begin)
    int adaptation_type = 1;
    float mu = 0.005;
    float rho = 0.985;
    float delta = 1e-6;
    float pw = 0;
    float p = 1.5;
    float alpha = 0;
    float beta = 150;
    size_t frame_size = 20;  // the frame size which corresponds to the golden data generation
    array_file u("test/golden/adaptive_filter_test_u1.dat");  // golden
    array_file e("test/golden/adaptive_filter_test_e1.dat");  // golden
    array_file h("test/golden/adaptive_filter_test_h1.dat");  // golden
    /// parameters which need to be set (end)

    // we need to check the array lengths between golden input u and e
    // because we are doing frame-based processing
    ASSERT_EQ(u.get_len(), e.get_len());

    // placeholders
    int total_frame = (int)floor((double)e.get_len() / (double)frame_size);  // total number of frame buffers
    auto h_c = new float[h.get_len()];                                       // frame buffer for the output

    /// module testing (begin)
    adaptive_filter af(nullptr, h.get_len(), frame_size, adaptation_type, mu, delta, rho, alpha, beta, p, delta, pw);
    for (int k = 0; k < total_frame; k++) {
        af.update_taps(u.get_ptr() + k * frame_size, e.get_ptr() + k * frame_size, frame_size);
    }
    /// module testing (end)

    // compute the accumulated squared error and log all registers
    af.get_taps(h_c, h.get_len());
    check_results(h.get_ptr(), h_c, h.get_len());

    // free the memory
    delete[] h_c;
}

int main(int argc, char **argv) {
    testing::InitGoogleTest(&argc, argv);
    return RUN_ALL_TESTS();
}
