#include "openspeechplatform.hpp"
#include "osp_parser.hpp"
#include "osp_param.h"
#include "osp_process.hpp"

#include <pthread.h>

int main(int argc, char* argv[]){
    osp_user_data user_data[DEFAULTS::NUM_CHANNEL];
    controls main_controls;
    auto parser = new osp_parser();
    parser->parse(argc, argv, user_data, &main_controls, 1);
    auto masterHA = new osp_process(main_controls.samp_freq, main_controls.buf_size, user_data, main_controls.multithread);
    int ret = openspeechplatform<osp_parser, osp_process, osp_user_data>(parser, masterHA, &main_controls);
    delete parser;
    delete masterHA;

    return ret;
}
