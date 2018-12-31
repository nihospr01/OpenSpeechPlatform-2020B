//
// Created by dsengupt on 9/24/18.
//

#ifndef OSP_CLION_C_OSP_PARSER_H
#define OSP_CLION_C_OSP_PARSER_H

#include <cereal/cereal.hpp>
#include <cereal/types/memory.hpp>
#include <cereal/archives/json.hpp>
#include <cereal/types/base_class.hpp>
#include <cereal/archives/binary.hpp>
#include <cereal/types/vector.hpp>
#include <cereal/types/string.hpp>
#include <cereal/types/polymorphic.hpp>
#include "cxxopts.hpp"
#include "osp_param.h"
#include "control_param.h"

class osp_parser{

public:
    explicit osp_parser(){
        channel = 0;
        options = new cxxopts::Options("osp", "Welcome to the Open Speech Platform");
        options
                ->positional_help("[optional args]")
                .show_positional_help();
        options
                ->add_options("Parameters")
                        ("channel", "0 - Both Channel; 1 - Left Channel; 2 - Right Channel ", cxxopts::value<int>()->default_value("0"))
                        ("en_ha", "Enable hearing aid algorithm: false - Disable; true - Enable", cxxopts::value<int>()->default_value("1"))
                        ("rear_mics", "Enable rear microphones: false - Disable; true - Enable", cxxopts::value<int>()->default_value("0"))
                        ("gain", "Set the gain in dB", cxxopts::value<float>()->default_value("-20"))
                        ("g50", "Set the gain values at 50 dB SPL input level", cxxopts::value<std::string>())
                        ("g80", "Set the gain values at 80 dB SPL input level", cxxopts::value<std::string>())
                        ("knee_low", "Set the lower knee points for the bands", cxxopts::value<std::string>())
                        ("knee_high", "Set the upper knee points for the bands", cxxopts::value<std::string>())
                        ("attack", "Set the attack time for WDRC for the bands", cxxopts::value<std::string>())
                        ("release", "Set the release time for WDRC for the bands", cxxopts::value<std::string>())
                        ("mpo", "Set the mpo limit for all the bands", cxxopts::value<float>()->default_value("100"))
                        ("afc", "Adaptive Feedback Cancellation: -1 - Reset AFC; 0 - AFC off; 1 - FXLMS; 2 - PMLMS; 3 - SLMS", cxxopts::value<int>()->default_value("3"))
                        ("afc_delay", "Delay to adjust for device to device variation in feedback path", cxxopts::value<size_t>()->default_value("150"))
                        ("afc_mu", "Adjust the step size for feedback management", cxxopts::value<float>()->default_value("0.005"))
                        ("afc_rho", "Adjust the forgetting factor for feedback management", cxxopts::value<float>()->default_value("0.985"))
                        ("afc_power_estimate", "Adjust the power estimate for feedback management", cxxopts::value<float>()->default_value("0.0"))
                        ("noise_estimation_type", "Noise Estimation type: 0 - Disable; 1 - Arslan; 2 - Hirsch and Ehrlicher; 3 - Cohen and Berdugo", cxxopts::value<int>()->default_value("0"))
                        ("spectral_type", "Enable spectral subtraction: 0 - Disable; 1 - Enable", cxxopts::value<int>()->default_value("0"))
                        ("spectral_subtraction", "Amount to subtract between range: [0,1)", cxxopts::value<float>()->default_value("0.0"));

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
    ~osp_parser() = default;
    void parse(int argc, char* argv[], osp_user_data *user_data){
        controls temp;
        parse(argc, argv, user_data, &temp);
    }
    void parse(int argc, char* argv[], osp_user_data *user_data, controls *main_controls, int initial = 0){

        osp_user_data* left = &user_data[0];
        osp_user_data* right = &user_data[1];

    
        try {
            auto result = options->parse(argc, argv);
            if(result.count("channel")){
                channel = result["channel"].as<int>();
                if(channel < 0 || channel > 2){
                    channel = 0;
                }
            }
            if(result.count("en_ha")){
                if(channel == 0 || channel == 1)
                    left->en_ha = result["en_ha"].as<int>();
                if(channel == 0 || channel == 2)
                    right->en_ha = result["en_ha"].as<int>();
            }
            if(result.count("afc")){
                if(channel == 0 || channel == 1)
                    left->afc = result["afc"].as<int>();
                if(channel == 0 || channel == 2)
                    right->afc = result["afc"].as<int>();
            }
            if(result.count("afc_delay")){
                if(channel == 0 || channel == 1)
                    left->afc_delay = result["afc_delay"].as<size_t>();
                if(channel == 0 || channel == 2)
                    right->afc_delay = result["afc_delay"].as<size_t>();
            }
            if(result.count("afc_mu")){
                if(channel == 0 || channel == 1)
                    left->afc_mu = result["afc_mu"].as<float>();
                if(channel == 0 || channel == 2)
                    right->afc_mu = result["afc_mu"].as<float>();
            }
            if(result.count("afc_rho")){
                if(channel == 0 || channel == 1)
                    left->afc_rho = result["afc_rho"].as<float>();
                if(channel == 0 || channel == 2)
                    right->afc_rho = result["afc_rho"].as<float>();
            }
            if(result.count("afc_power_estimate")){
                if(channel == 0 || channel == 1)
                    left->afc_power_estimate = result["afc_power_estimate"].as<float>();
                if(channel == 0 || channel == 2)
                    right->afc_power_estimate = result["afc_power_estimate"].as<float>();
            }
            if(result.count("noise_estimation_type")){
                if(channel == 0 || channel == 1)
                    left->noise_estimation_type = result["noise_estimation_type"].as<int>();
                if(channel == 0 || channel == 2)
                    right->noise_estimation_type = result["noise_estimation_type"].as<int>();
            }
            if(result.count("spectral_type")){
                if(channel == 0 || channel == 1)
                    left->spectral_type = result["spectral_type"].as<int>();
                if(channel == 0 || channel == 2)
                    right->spectral_type = result["spectral_type"].as<int>();
            }
            if(result.count("spectral_subtraction")){
                if(channel == 0 || channel == 1)
                    left->spectral_subtraction = result["spectral_subtraction"].as<float>();
                if(channel == 0 || channel == 2)
                    right->spectral_subtraction = result["spectral_subtraction"].as<float>();
            }
            if(result.count("g50")){

                auto& g50 = result["g50"].as<std::string>();
                std::istringstream iss(g50);
                std::vector<std::string> g50_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<','>>());
                if(g50_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if(channel == 0 || channel == 1)
                            left->g50[j] = stof(g50_arr[j]);
                        if(channel == 0 || channel == 2)
                            right->g50[j] = stof(g50_arr[j]);
                    }
                }
                else{
                    std::cout << "For g50 please have " << NUM_BANDS << " bands worth of data."  << std::endl;
                }
            }
            if(result.count("g80")){

                auto& g80 = result["g80"].as<std::string>();
                std::istringstream iss(g80);
                std::vector<std::string> g80_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<','>>());
                if(g80_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if(channel == 0 || channel == 1)
                            left->g80[j] = stof(g80_arr[j]);
                        if(channel == 0 || channel == 2)
                            right->g80[j] = stof(g80_arr[j]);
                    }
                }
                else{
                    std::cout << "For g80 please have " << NUM_BANDS << " bands worth of data."  << std::endl;
                }
            }
            if(result.count("knee_low")){

                auto& knee_low = result["knee_low"].as<std::string>();
                std::istringstream iss(knee_low);
                std::vector<std::string> knee_low_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<','>>());
                if(knee_low_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if(channel == 0 || channel == 1)
                            left->knee_low[j] = stof(knee_low_arr[j]);
                        if(channel == 0 || channel == 2)
                            right->knee_low[j] = stof(knee_low_arr[j]);
                    }
                }
                else{
                    std::cout << "For knee_low please have " << NUM_BANDS << " bands worth of data."  << std::endl;
                }
            }
            if(result.count("knee_high")){

                auto& knee_high = result["knee_high"].as<std::string>();
                std::istringstream iss(knee_high);
                std::vector<std::string> knee_high_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<','>>());
                if(knee_high_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if(channel == 0 || channel == 1)
                            left->knee_high[j] = stof(knee_high_arr[j]);
                        if(channel == 0 || channel == 2)
                            right->knee_high[j] = stof(knee_high_arr[j]);
                    }
                }
                else{
                    std::cout << "For knee_high please have " << NUM_BANDS << " bands worth of data."  << std::endl;
                }
            }
            if(result.count("attack")){

                auto& attack = result["attack"].as<std::string>();
                std::istringstream iss(attack);
                std::vector<std::string> attack_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<','>>());
                if(attack_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if(channel == 0 || channel == 1)
                            left->attack[j] = stof(attack_arr[j]);
                        if(channel == 0 || channel == 2)
                            right->attack[j] = stof(attack_arr[j]);
                    }
                }
                else{
                    std::cout << "For attack please have " << NUM_BANDS << " bands worth of data."  << std::endl;
                }
            }
            if(result.count("release")){

                auto& release = result["release"].as<std::string>();
                std::istringstream iss(release);
                std::vector<std::string> release_arr((std::istream_iterator<WordDelimitedBy<','>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<','>>());
                if(release_arr.size() == NUM_BANDS) {
                    for (int j = 0; j < NUM_BANDS; j++) {
                        if(channel == 0 || channel == 1)
                            left->release[j] = stof(release_arr[j]);
                        if(channel == 0 || channel == 2)
                            right->release[j] = stof(release_arr[j]);
                    }
                }
                else{
                    std::cout << "For release please have " << NUM_BANDS << " bands worth of data."  << std::endl;
                }
            }
            if(result.count("mpo")){
                if(channel == 0 || channel == 1)
                    left->mpo = result["mpo"].as<float>();
                if(channel == 0 || channel == 2)
                    right->mpo = result["mpo"].as<float>();
            }
            if(result.count("gain")){
                if(channel == 0 || channel == 1)
                    left->gain = result["gain"].as<float>();
                if(channel == 0 || channel == 2)
                    right->gain = result["gain"].as<float>();
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
                std::cout << options->help({"OSP Parameters", "Control Signals"}) << std::endl;
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