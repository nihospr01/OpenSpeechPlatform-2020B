#include "openspeechplatform.hpp"
#include "osp_parser.hpp"
#include "osp_param.h"
#include "osp_process.hpp"

/**
 * This is the main function to run OSP
 * @param argc Argument counter
 * @param argv Arguments
 * @return 0 if successful
 */

int main(int argc, char* argv[]){
    unsigned num_cpus = std::thread::hardware_concurrency();
    std::cout << num_cpus << " threads available\n";
    osp_user_data user_data[NUM_CHANNEL];
    controls main_controls;
    auto parser = new osp_parser();
    parser->parse(argc, argv, user_data, &main_controls, 1);
    auto masterHA = new osp_process(main_controls.samp_freq, main_controls.buf_size, user_data, main_controls.multithread);
    return openspeechplatform<osp_parser, osp_process, osp_user_data>(parser, masterHA, &main_controls);
}