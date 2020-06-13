#include "openspeechplatform.hpp"
#include "osp_parser_multirate.hpp"
#include "osp_param_multirate.h"
#include "osp_process_multirate.hpp"
#include "osp_parser.hpp"
#include "osp_param.h"
#include "osp_process.hpp"

#include <pthread.h>

int main(int argc, char* argv[]){


    osp_user_data user_data[DEFAULTS::NUM_CHANNEL];
    controls main_controls;
    auto parser = new osp_parser();
    parser->parse(argc, argv, user_data, &main_controls, 1);

    osp_user_data_multirate user_data_multirate[DEFAULTS_MULTIRATE::NUM_CHANNEL];
    auto parser_multirate = new osp_parser_multirate();
    parser_multirate->parse(argc, argv, user_data_multirate);

    if(main_controls.ref_design == 0){
        auto masterHA = new osp_process(main_controls.samp_freq, main_controls.buf_size, user_data,
                                        main_controls.multithread);
        int ret = openspeechplatform<osp_parser, osp_process, osp_user_data>(parser, masterHA, &main_controls);
        delete parser;
        delete parser_multirate;
        delete masterHA;
        return ret;
    }
    else {
        auto masterHA_multirate = new osp_process_multirate(main_controls.samp_freq, main_controls.buf_size,
                                                            user_data_multirate, main_controls.multithread);
        int ret_multirate = openspeechplatform<osp_parser_multirate, osp_process_multirate, osp_user_data_multirate>(
                parser_multirate, masterHA_multirate, &main_controls);
        delete parser;
        delete parser_multirate;
        delete masterHA_multirate;
        return ret_multirate;
    }




}
