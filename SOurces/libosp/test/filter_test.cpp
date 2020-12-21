//
// Created by Kuan-Lin Chen on 10/19/18.
//
#include "gtest/gtest.h"

#include <iostream>
#include <OSP/array_file/array_file.hpp>
#include <OSP/filter/filter.hpp>  /// include the module header which will be tested
#include <math.h>

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

TEST(libosp, Filter) {
    /// parameters which need to be set (begin)
    size_t frame_size = 100;                        // the frame size which corresponds to the golden data generation
    array_file h("test/golden/filter_test_h.dat");  // golden
    array_file x("test/golden/filter_test_x.dat");  // golden
    array_file y("test/golden/filter_test_y.dat");  // golden
    /// parameters which need to be set (end)

    // we need to check the array lengths between golden input x and output y
    // because we are doing frame-based processing
    ASSERT_EQ(x.get_len(), y.get_len());

    // placeholders
    int total_frame = (int)floor((double)x.get_len() / (double)frame_size);  // total number of frame buffers
    auto y_c = new float[frame_size * total_frame];                          // frame buffer for the output

    /// module testing (begin)
    filter fir(h.get_ptr(), h.get_len(), nullptr, frame_size);  // construct a filter object
    for (int k = 0; k < total_frame; k++) {
        fir.cirfir(x.get_ptr() + k * frame_size, y_c + k * frame_size, frame_size);  // filtering
    }
    /// module testing (end)

    check_results(y.get_ptr(), y_c, y.get_len());

    delete[] y_c;
}

int main(int argc, char **argv) {
    testing::InitGoogleTest(&argc, argv);
    return RUN_ALL_TESTS();
}
