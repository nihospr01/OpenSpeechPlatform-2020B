//
// Created by dsengupt on 9/24/18.
//

#ifndef OSP_CLION_C_OSP_PARSER_MULTIRATE_H
#define OSP_CLION_C_OSP_PARSER_MULTIRATE_H

#include <cereal/cereal.hpp>
#include <cereal/types/memory.hpp>
#include <cereal/archives/json.hpp>
#include <cereal/types/base_class.hpp>
#include <cereal/archives/binary.hpp>
#include <cereal/types/vector.hpp>
#include <cereal/types/string.hpp>
#include <cereal/types/polymorphic.hpp>
#include "cxxopts.hpp"
#include "osp_param_multirate.h"
#include "control_param.h"
#include "stopwatch.h"

class osp_parser_multirate{

public:
    explicit osp_parser_multirate(){
        channel = 0;
        options = new cxxopts::Options("osp", "Welcome to the Open Speech Platform");
        options
                ->positional_help("[optional args]")
                .show_positional_help();
        options
                ->allow_unrecognised_options()
                .add_options("Parameters")
                        ("channel", "Set channel: 0 - Both Channel; 1 - Left Channel; 2 - Right Channel ", cxxopts::value<int>()->default_value("0"))
                        ("en_ha", "Enable hearing aid algorithm: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("1"))
                        ("rear_mics", "Enable rear microphones: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("0"))
                        ("aligned", "Enable filterbank alignment: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("1"))
                        ("gain", "Set the gain in dB", cxxopts::value<float>()->default_value("-20"))
                        ("g50", "Set the gain values at 50 dB SPL input level", cxxopts::value<std::string>())
                        ("g80", "Set the gain values at 80 dB SPL input level", cxxopts::value<std::string>())
                        ("knee_low", "Set the lower knee points for the bands", cxxopts::value<std::string>())
                        ("mpo_band", "Set the MPO (upper knee points) for the bands", cxxopts::value<std::string>())
                        ("attack", "Set the attack time for WDRC for the bands", cxxopts::value<std::string>())
                        ("release", "Set the release time for WDRC for the bands", cxxopts::value<std::string>())
                        ("global_mpo", "Set the global mpo limit", cxxopts::value<float>()->default_value("120"))
                        ("freping", "Enable freping: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("0"))
                        ("freping_alpha", "Set the alpha parameter for freping", cxxopts::value<std::string>())
                        ("afc", "Enable Adaptive Feedback Cancellation: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("1"))
                        ("afc_reset", "Reset the taps of AFC filter to default values: 0 - Nothing; 1 - Reset", cxxopts::value<int>()->default_value("0"))
                        ("afc_type", "Adaptation type for Adaptive Feedback Cancellation: 0 - Stop adaptation; 1 - FXLMS; 2 - IPNLMS; 3 - SLMS", cxxopts::value<int>()->default_value("3"))
                        ("afc_delay", "Delay in millisecond to adjust for device to device variation in feedback path", cxxopts::value<float>()->default_value("4.6875"))
                        ("afc_mu", "Adjust the step size for feedback management", cxxopts::value<float>()->default_value("0.005"))
                        ("afc_rho", "Adjust the forgetting factor for feedback management", cxxopts::value<float>()->default_value("0.985"))
                        ("afc_power_estimate", "Adjust the power estimate for feedback management", cxxopts::value<float>()->default_value("0.0"))
                        ("bf", "Enable beamformer: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("0"))
                        ("bf_type", "Adaptation type for GSC beamforming: 0 - Stop adaptation; 1 - Modified LMS; 2 - IPNLMS; 3 - SLMS", cxxopts::value<int>()->default_value("3"))
                        ("bf_mu", "Adjust the step size for GSC beamforming", cxxopts::value<float>()->default_value("0.05"))
                        ("bf_rho", "Adjust the forgetting factor for beamforming", cxxopts::value<float>()->default_value("0.985"))
                        ("bf_power_estimate", "Adjust the power estimate for beamforming", cxxopts::value<float>()->default_value("0.0"))
                        ("bf_nc_on_off", "Enable norm-constrained beamformer: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("0"))
                        ("bf_amc_on_off", "Enable adaptation mode control beamformer: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("0"))
                        ("nc_thr", "Adjust the threshold of the norm-constrained adaptation for GSC beamforming", cxxopts::value<float>()->default_value("1.0"))
                        ("amc_thr", "Adjust the threshold of the adaptation mode controller for GSC beamforming", cxxopts::value<float>()->default_value("2.0"))
                        ("amc_forgetting_factor", "Adjust the forgetting factor of the power estimate for GSC beamforming", cxxopts::value<float>()->default_value("0.8"))
                        ("noise_estimation_type", "Noise Estimation type: 0 - Disable; 1 - Arslan; 2 - Hirsch and Ehrlicher; 3 - Cohen and Berdugo", cxxopts::value<int>()->default_value("0"))
                        ("spectral_type", "Enable spectral subtraction: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("0"))
                        ("spectral_subtraction", "Amount to subtract between range: [0,1)", cxxopts::value<float>()->default_value("0.0"))
                        ("alpha", "The mixer of virtual sound and actual sound where 0 is complete actual sound and 1 is complete virtual sound", cxxopts::value<float>()->default_value("0.0"))
                        ("audio_filename", "Path to the audio file", cxxopts::value<std::string>()->default_value(""))
                        ("audio_reset", "Reset audio", cxxopts::value<int>()->default_value("0"))
                        ("audio_play", "Play the audio", cxxopts::value<int>()->default_value("0"))
                        ("audio_repeat", "Play audio in repeat", cxxopts::value<int>()->default_value("0"))
                        ("record_start", "Start recording file", cxxopts::value<int>()->default_value("0"))
                        ("record_stop", "Stop recording file", cxxopts::value<int>()->default_value("0"))
                        ("audio_recordfile", "Name of the file to record", cxxopts::value<std::string>()->default_value(""))
                        ("record_length", "How long do you want to record", cxxopts::value<float>()->default_value("5.0"));
        options
                ->add_options("Control Signals")
                        ("samp_freq", "Set the sampling frequency for the mic and reciever", cxxopts::value<int>()->default_value("48000"))
                        ("input_device", "Please indicate which device you want to use for input", cxxopts::value<int>())
                        ("output_device", "Please indicate which device you want to use for output", cxxopts::value<int>())
                        ("multi_thread", "Please indicate if you want OSP to run in multiple threads", cxxopts::value<int>())
                        ("q,quit", "Quit OSP")
                        ("p,print", "Prints out the current user data structure")
                        ("h,help", "Prints out the help");
    };
    ~osp_parser_multirate() = default;
    void parse(int argc, char* argv[], osp_user_data_multirate *user_data){
        controls temp;
        parse(argc, argv, user_data, &temp);
    }
    void parse(int argc, char* argv[], osp_user_data_multirate *user_data, controls *main_controls, int initial = 0){

        osp_user_data_multirate* left = &user_data[0];
        osp_user_data_multirate* right = &user_data[1];

    
        try {
            auto result = options->parse(argc, argv);
            if (result.count("channel")) {
                channel = result["channel"].as<int>();
                if (channel < 0 || channel > 2) {
                    channel = 0;
                }
            }
            if (result.count("freping")) {
                if (channel == 0 || channel == 1)
                    left->freping = result["freping"].as<int>();
                if (channel == 0 || channel == 2)
                    right->freping = result["freping"].as<int>();
            }
            if (result.count("freping_alpha")) {

                auto &freping_alpha = result["freping_alpha"].as<std::string>();
                std::istringstream iss(freping_alpha);
                std::vector<std::string> freping_alpha_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<','>>());
                if (freping_alpha_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if (channel == 0 || channel == 1)
                            left->freping_alpha[j] = stof(freping_alpha_arr[j]);
                        if (channel == 0 || channel == 2)
                            right->freping_alpha[j] = stof(freping_alpha_arr[j]);
                    }
                } else {
                    std::cout << "For freping_alpha please have " << NUM_BANDS << " bands worth of data." << std::endl;
                }
            }
            if (result.count("en_ha")) {
                if (channel == 0 || channel == 1)
                    left->en_ha = result["en_ha"].as<int>();
                if (channel == 0 || channel == 2)
                    right->en_ha = result["en_ha"].as<int>();
            }
            if (result.count("afc")) {
                if (channel == 0 || channel == 1)
                    left->afc = result["afc"].as<int>();
                if (channel == 0 || channel == 2)
                    right->afc = result["afc"].as<int>();
            }
            if (result.count("afc_reset")) {
                if (channel == 0 || channel == 1)
                    left->afc_reset = result["afc_reset"].as<int>();
                if (channel == 0 || channel == 2)
                    right->afc_reset = result["afc_reset"].as<int>();
            }
            if (result.count("afc_type")) {
                if (channel == 0 || channel == 1)
                    left->afc_type = result["afc_type"].as<int>();
                if (channel == 0 || channel == 2)
                    right->afc_type = result["afc_type"].as<int>();
            }
            if (result.count("afc_delay")) {
                if (channel == 0 || channel == 1)
                    left->afc_delay = result["afc_delay"].as<float>();
                if (channel == 0 || channel == 2)
                    right->afc_delay = result["afc_delay"].as<float>();
            }
            if (result.count("afc_mu")) {
                if (channel == 0 || channel == 1)
                    left->afc_mu = result["afc_mu"].as<float>();
                if (channel == 0 || channel == 2)
                    right->afc_mu = result["afc_mu"].as<float>();
            }
            if (result.count("afc_rho")) {
                if (channel == 0 || channel == 1)
                    left->afc_rho = result["afc_rho"].as<float>();
                if (channel == 0 || channel == 2)
                    right->afc_rho = result["afc_rho"].as<float>();
            }
            if (result.count("afc_power_estimate")) {
                if (channel == 0 || channel == 1)
                    left->afc_power_estimate = result["afc_power_estimate"].as<float>();
                if (channel == 0 || channel == 2)
                    right->afc_power_estimate = result["afc_power_estimate"].as<float>();
            }
            if (result.count("bf")) {
                if (channel == 0 || channel == 1)
                    left->bf = result["bf"].as<int>();
                if (channel == 0 || channel == 2)
                    right->bf = result["bf"].as<int>();
            }
            if(result.count("bf_type")){
                if(channel == 0 || channel == 1)
                    left->bf_type = result["bf_type"].as<int>();
                if(channel == 0 || channel == 2)
                    right->bf_type = result["bf_type"].as<int>();
            }
            if (result.count("bf_mu")) {
                if (channel == 0 || channel == 1)
                    left->bf_mu = result["bf_mu"].as<float>();
                if (channel == 0 || channel == 2)
                    right->bf_mu = result["bf_mu"].as<float>();
            }
            if(result.count("bf_rho")){
                if(channel == 0 || channel == 1)
                    left->bf_rho = result["bf_rho"].as<float>();
                if(channel == 0 || channel == 2)
                    right->bf_rho = result["bf_rho"].as<float>();
            }
            if(result.count("bf_power_estimate")){
                if(channel == 0 || channel == 1)
                    left->bf_power_estimate = result["bf_power_estimate"].as<float>();
                if(channel == 0 || channel == 2)
                    right->bf_power_estimate = result["bf_power_estimate"].as<float>();
            }
            if (result.count("bf_nc_on_off")) {
                if (channel == 0 || channel == 1)
                    left->bf_nc_on_off = result["bf_nc_on_off"].as<int>();
                if (channel == 0 || channel == 2)
                    right->bf_nc_on_off = result["bf_nc_on_off"].as<int>();
            }
            if (result.count("aligned")) {
                if (channel == 0 || channel == 1)
                    left->aligned = (bool)result["aligned"].as<int>();
                if (channel == 0 || channel == 2)
                    right->aligned = (bool)result["aligned"].as<int>();
            }
            if (result.count("bf_amc_on_off")) {
                if (channel == 0 || channel == 1)
                    left->bf_amc_on_off = result["bf_amc_on_off"].as<int>();
                if (channel == 0 || channel == 2)
                    right->bf_amc_on_off = result["bf_amc_on_off"].as<int>();
            }
            if(result.count("nc_thr")){
                if(channel == 0 || channel == 1)
                    left->nc_thr = result["nc_thr"].as<float>();
                if(channel == 0 || channel == 2)
                    right->nc_thr = result["nc_thr"].as<float>();
            }
            if(result.count("amc_thr")){
                if(channel == 0 || channel == 1)
                    left->amc_thr = result["amc_thr"].as<float>();
                if(channel == 0 || channel == 2)
                    right->amc_thr = result["amc_thr"].as<float>();
            }
            if(result.count("amc_forgetting_factor")){
                if(channel == 0 || channel == 1)
                    left->amc_forgetting_factor = result["amc_forgetting_factor"].as<float>();
                if(channel == 0 || channel == 2)
                    right->amc_forgetting_factor = result["amc_forgetting_factor"].as<float>();
            }
            if (result.count("noise_estimation_type")) {
                if (channel == 0 || channel == 1)
                    left->noise_estimation_type = result["noise_estimation_type"].as<int>();
                if (channel == 0 || channel == 2)
                    right->noise_estimation_type = result["noise_estimation_type"].as<int>();
            }
            if (result.count("spectral_type")) {
                if (channel == 0 || channel == 1)
                    left->spectral_type = result["spectral_type"].as<int>();
                if (channel == 0 || channel == 2)
                    right->spectral_type = result["spectral_type"].as<int>();
            }
            if (result.count("spectral_subtraction")) {
                if (channel == 0 || channel == 1)
                    left->spectral_subtraction = result["spectral_subtraction"].as<float>();
                if (channel == 0 || channel == 2)
                    right->spectral_subtraction = result["spectral_subtraction"].as<float>();
            }
            if (result.count("g50")) {

                auto &g50 = result["g50"].as<std::string>();
                std::istringstream iss(g50);
                std::vector<std::string> g50_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<','>>());
                if (g50_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if (channel == 0 || channel == 1)
                            left->g50[j] = stof(g50_arr[j]);
                        if (channel == 0 || channel == 2)
                            right->g50[j] = stof(g50_arr[j]);
                    }
                } else {
                    std::cout << "For g50 please have " << NUM_BANDS << " bands worth of data." << std::endl;
                }
            }
            if (result.count("g80")) {

                auto &g80 = result["g80"].as<std::string>();
                std::istringstream iss(g80);
                std::vector<std::string> g80_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<','>>());
                if (g80_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if (channel == 0 || channel == 1)
                            left->g80[j] = stof(g80_arr[j]);
                        if (channel == 0 || channel == 2)
                            right->g80[j] = stof(g80_arr[j]);
                    }
                } else {
                    std::cout << "For g80 please have " << NUM_BANDS << " bands worth of data." << std::endl;
                }
            }
            if (result.count("knee_low")) {

                auto &knee_low = result["knee_low"].as<std::string>();
                std::istringstream iss(knee_low);
                std::vector<std::string> knee_low_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                      std::istream_iterator<WordDelimitedBy<','>>());
                if (knee_low_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if (channel == 0 || channel == 1)
                            left->knee_low[j] = stof(knee_low_arr[j]);
                        if (channel == 0 || channel == 2)
                            right->knee_low[j] = stof(knee_low_arr[j]);
                    }
                } else {
                    std::cout << "For knee_low please have " << NUM_BANDS << " bands worth of data." << std::endl;
                }
            }
            if (result.count("mpo_band")) {

                auto &knee_high = result["mpo_band"].as<std::string>();
                std::istringstream iss(knee_high);
                std::vector<std::string> knee_high_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                       std::istream_iterator<WordDelimitedBy<','>>());
                if (knee_high_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if (channel == 0 || channel == 1)
                            left->mpo_band[j] = stof(knee_high_arr[j]);
                        if (channel == 0 || channel == 2)
                            right->mpo_band[j] = stof(knee_high_arr[j]);
                    }
                } else {
                    std::cout << "For mpo_band please have " << NUM_BANDS << " bands worth of data." << std::endl;
                }
            }
            if (result.count("attack")) {

                auto &attack = result["attack"].as<std::string>();
                std::istringstream iss(attack);
                std::vector<std::string> attack_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                    std::istream_iterator<WordDelimitedBy<','>>());
                if (attack_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if (channel == 0 || channel == 1)
                            left->attack[j] = stof(attack_arr[j]);
                        if (channel == 0 || channel == 2)
                            right->attack[j] = stof(attack_arr[j]);
                    }
                } else {
                    std::cout << "For attack please have " << NUM_BANDS << " bands worth of data." << std::endl;
                }
            }
            if (result.count("release")) {

                auto &release = result["release"].as<std::string>();
                std::istringstream iss(release);
                std::vector<std::string> release_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                     std::istream_iterator<WordDelimitedBy<','>>());
                if (release_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if (channel == 0 || channel == 1)
                            left->release[j] = stof(release_arr[j]);
                        if (channel == 0 || channel == 2)
                            right->release[j] = stof(release_arr[j]);
                    }
                } else {
                    std::cout << "For release please have " << NUM_BANDS << " bands worth of data." << std::endl;
                }
            }
            if (result.count("global_mpo")) {
                if (channel == 0 || channel == 1)
                    left->global_mpo = result["global_mpo"].as<float>();
                if (channel == 0 || channel == 2)
                    right->global_mpo = result["global_mpo"].as<float>();
            }
            if (result.count("gain")) {
                if (channel == 0 || channel == 1)
                    left->gain = result["gain"].as<float>();
                if (channel == 0 || channel == 2)
                    right->gain = result["gain"].as<float>();
            }
            if(result.count("alpha")){
                if (channel == 0 || channel == 1)
//                    std::cout<<result["audio_filename"].as<std::string>()<<std::endl;
                    left->alpha = result["alpha"].as<float>();

                if (channel == 0 || channel == 2)
                    right->alpha = result["alpha"].as<float>();
            }
            //options for playing audio
            if (result.count("audio_filename")) {
//                auto& filename = result["audio_filename"].as<std::string>();
                if (channel == 0 || channel == 1)
//                    std::cout<<result["audio_filename"].as<std::string>()<<std::endl;
                    left->audio_filename = result["audio_filename"].as<std::string>();

                if (channel == 0 || channel == 2)
                    right->audio_filename = result["audio_filename"].as<std::string>();
            }
            if (result.count("audio_recordfile")) {
//                auto& filename = result["audio_filename"].as<std::string>();
                if (channel == 0 || channel == 1)
//                    std::cout<<result["audio_filename"].as<std::string>()<<std::endl;
                    left->audio_recordfile = result["audio_recordfile"].as<std::string>();

                if (channel == 0 || channel == 2)
                    right->audio_recordfile = result["audio_recordfile"].as<std::string>();
            }
            if(result.count("record_length")){
                if (channel == 0 || channel == 1)
                    left->record_length = result["record_length"].as<float>();
                if (channel == 0 || channel == 2)
                    right->record_length = result["record_length"].as<float>();
            }
            if(result.count("audio_play")){
                if (channel == 0 || channel == 1)
                    left->audio_play = result["audio_play"].as<int>();
                if (channel == 0 || channel == 2)
                    right->audio_play = result["audio_play"].as<int>();
            }
            if(result.count("audio_reset")){
                if (channel == 0 || channel == 1)
                    left->audio_reset = result["audio_reset"].as<int>();
                if (channel == 0 || channel == 2)
                    right->audio_reset = result["audio_reset"].as<int>();
            }
            if(result.count("audio_repeat")){
                if (channel == 0 || channel == 1)
                    left->audio_repeat = result["audio_repeat"].as<int>();
                if (channel == 0 || channel == 2)
                    right->audio_repeat = result["audio_repeat"].as<int>();
            }
            if(result.count("record_start")){
                if (channel == 0 || channel == 1)
                    left->record_start = result["record_start"].as<int>();
                if (channel == 0 || channel == 2)
                    right->record_start = result["record_start"].as<int>();
            }
            if(result.count("record_stop")){
                if (channel == 0 || channel == 1)
                    left->record_stop = result["record_stop"].as<int>();
                if (channel == 0 || channel == 2)
                    right->record_stop = result["record_stop"].as<int>();
            }
            if(result.count("quit")){
                main_controls->endloop = true;
            }
            if(result.count("input_device")){
                if(initial == 0){
                    std::cout << "Can't change Input device after startup" << std::endl;
                }
                else {
                    main_controls->input_device = result["input_device"].as<int>();
                }
            }
            if(result.count("output_device")){
                if(initial == 0){
                    std::cout << "Can't change Output device after startup" << std::endl;
                }
                else {
                    main_controls->output_device = result["output_device"].as<int>();
                    std::cout << main_controls->output_device << std::endl;
		        }
            }
            if(result.count("multi_thread")){
                if(initial == 0){
                    std::cout << "Can't change multithreading after startup" << std::endl;
                }
                else {
                    main_controls->multithread = (bool) result["multi_thread"].as<int>();
                }
            }
            if(result.count("samp_freq")){
                if(initial == 0){
                    std::cout << "Can't change sample frequency after startup" << std::endl;
                }
                else {
                    int temp = result["samp_freq"].as<int>();
                    if(temp == 48000) {
                        main_controls->samp_freq = 48000;
                        main_controls->buf_size = 48;
                    }
                    else{
                        std::cout << "Can't use " << temp << " as the sampling frequency" << std::endl;
                    }
                }
            }
            if(result.count("print")){
                std::stringstream os;
                cereal::JSONOutputArchive archive(os);
                archive( CEREAL_NVP(*left), CEREAL_NVP(*right));
                std::string str = os.str();
                str.erase(std::remove(str.begin(), str.end(), '\n'), str.end());
                str.erase(std::remove(str.begin(), str.end(), ' '), str.end());
                std::cout << str << "}" << std::endl;
            }
            if (result.count("help")) {
                std::cout << options->help({"Parameters", "Control Signals"}) << std::endl;
            }

            std::cout << "Done" << std::endl;
    
        } catch (const cxxopts::OptionException &e) {
            std::cout << "error parsing options: " << e.what() << std::endl;
        }
    };

private:
    cxxopts::Options *options;
    int channel;


};


#endif //OSP_CLION_C_OSP_PARSER_H
