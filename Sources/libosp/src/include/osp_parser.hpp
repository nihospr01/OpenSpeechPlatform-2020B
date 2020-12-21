#ifndef OSP_PARSER_HPP__
#define OSP_PARSER_HPP__

// Parses the json into the params:: variables
// Updates parameters in active algorithms

#include <osp_params.hpp>
#include <osp_process.hpp>
#include <fstream>
#include <string>
#include <streambuf>
#include <iostream>
#include <cstdio>
#include <vector>
#include <set>

#define JSON_USE_IMPLICIT_CONVERSIONS 0
#include "nlohmann/json.hpp"
using json = nlohmann::json;

class OspParser {
   public:
    OspParser();
    ~OspParser() {};
    std::string parse(const std::string);
    OspProcess **process = {0};
    void set_params(const json j, bool first);

   private:
    std::string get_params(void);
    void set_channel(const json j, const int channel, bool first, std::set<int> &change);
    void set_params_common(const int channels);
    void switch_bands(int num_bands);
    void restart(int num_bands);
};

OspParser::OspParser() {
    json ijson;
    bool parsed_ok = false;
    if (!defaults::conf_filename.empty()) {
        try {
            std::ifstream i(defaults::conf_filename);
            i >> ijson;
            parsed_ok = true;
        } catch (json::parse_error &e) {
            std::cout << "\nERROR: Failed to parse " << defaults::conf_filename << '\n';
            std::cout << e.what() << "\nUsing builtin defaults instead.\n" << std::endl;
        }
    }
    if (!parsed_ok)
        ijson = json::parse(defaults::default_json);

    // build a parameter map for easy lookups
    size_t num_params = sizeof(params::par) / sizeof(ParamValue);
    for (size_t i = 0; i < num_params; ++i) params::pars.insert(make_pair(params::par[i].name, &params::par[i]));

    // set the initial parameter values from the json defaults
    set_params(ijson, true);

    // cout << "Finished Parsing Parameters" << endl;
}

/**
 * @brief Gets the status of the parameters
 * 
 * @return json string 
 */
std::string OspParser::get_params() {
    json j, jchan[2];
    int *ival;
    float *fval;
    std::vector<float> *vec;
    std::string *sval;

    // Tell OspProcess to have all the algorithm modules
    // report their current values.  Updates the params
    // namespace.
    if (process && *process)
        (*process)->get_params();

    size_t num_params = sizeof(params::par) / sizeof(ParamValue);
    for (int chan = 0; chan < 2; chan++) {
        for (size_t i = 0; i < num_params; ++i) {
            ParamValue *p = &params::par[i];
            if (p->flags & kCmd)
                continue;
            if (p->flags & kLeft && chan == 1)
                continue;
            // cout << "Parse " << p->name << " chan:" << chan << endl;
            switch (p->type) {
                case kFloat:
                    fval = (float *)(p->param);
                    jchan[chan][p->name] = fval[chan];
                    break;
                case kInt:
                    ival = (int *)(p->param);
                    jchan[chan][p->name] = ival[chan];
                    break;
                case kArray:
                    vec = (std::vector<float> *)(p->param);
                    jchan[chan][p->name] = vec[chan];
                    break;
                case kStr:
                    sval = (std::string *)(p->param);
                    jchan[chan][p->name] = sval[chan];
                    break;
                default:
                    break;
            }
        }
    }
    j["left"] = jchan[0];
    j["right"] = jchan[1];
    j["num_bands"] = params::num_bands;
    return j.dump();
}

// Set common group parameters.  These don't
// require an active process.  Normally nothing
// needs to be done here, but gain is special. 
void OspParser::set_params_common(const int channels) {
    for (int ch = 0; ch < 2; ch++) {
        if (channels == 0 && ch ==1)
            continue;
        if (channels == 1 && ch == 0)
            continue;

        params::gain_[ch] = powf(10.0f, params::gain[ch] / 20.0f);
    }
}

void OspParser::restart(int num_bands) {
    osp::reset = num_bands;    // set reset state
    osp::running = 0;          // tell main loop to exit
}

void OspParser::switch_bands(int num_bands) {
    if (num_bands == params::num_bands)
        return;
    std::cout << "Switch Bands " << num_bands << std::endl;
    restart(num_bands);
}

/**
 * @brief Sets Parameters from json
 * 
 * @param j A json parameter string 
 */
void OspParser::set_params(const json j, bool first) {
    std::set<int> lchange = {};
    std::set<int> rchange = {};

    if (j.contains("num_bands")) {
        switch_bands(j["num_bands"].get<int>());
    }
    if (j.contains("restart")) {
        restart(params::num_bands);
        return;
    }
    if (j.contains("left"))
        set_channel(j["left"], 0, first, lchange);
    if (j.contains("right"))
        set_channel(j["right"], 1, first, rchange);

    // gcount[grp] = 0(left), 1(right) or 2(both)
    std::map<int, int> gcount;
    for (const int num : lchange) gcount[num]++;
    for (const int num : rchange) gcount[num]++;
    for (auto &[k, v] : gcount) {
        if (v == 2)
            continue;
        if (lchange.find(k) != lchange.end())
            v -= 1;  // left channel only. Set flag to 0
    }

    for (auto &[grp, ch] : gcount) {
        if (grp == 0)
            set_params_common(ch);
        else if (process && *process)
            (*process)->set_params((PGroupType)grp, ch);
    }
}

/**
 * @brief Parses a single channel of json values
 * 
 * @param j json data
 * @param channel 0=left, 1=right
 * @param change Output set of groups that have changed.
 */
void OspParser::set_channel(const json j, const int channel, bool first, std::set<int> &change) {
    int *ival;
    float *fval;
    std::string *sval;
    std::vector<float> *vec;
    std::vector<float> jvec;

    for (auto &[k, v] : j.items()) {
        ParamValue *p = params::pars[k];
        if (p == nullptr) {
            std::cout << "Ignored " << k << std::endl;
            continue;
        }
        if ((p->flags & kLeft) && channel == 1)
            continue;
        switch (p->type) {
            case kFloat:
                fval = (float *)(p->param);
                if (first || p->flags & kCmd || fval[channel] != v.get<float>()) {
                    fval[channel] = v.get<float>();
                    change.insert(p->group);
                }
                break;
            case kInt:
                ival = (int *)(p->param);
                if (first || p->flags & kCmd || ival[channel] != v.get<int>()) {
                    ival[channel] = v.get<int>();
                    change.insert(p->group);
                }
                break;
            case kArray:
                vec = (std::vector<float> *)(p->param);
                try {
                    jvec = v.get<std::vector<float>>();
                } catch (const std::exception &e) {
                    try {
                        float f = v.get<float>();
                        jvec = vec[channel];
                        std::fill(jvec.begin(), jvec.end(), f);
                    } catch (const std::exception &e) {
                        std::cerr << "Value is not an array or scalar" << '\n';
                    }
                }
                if (jvec.size() != (size_t)params::num_bands) {
                    std::cerr << "Array size should match number of bands (" 
                        << params::num_bands << ")\n";
                    break;
                }
                if (first || vec[channel] != jvec) {
                    vec[channel] = jvec;
                    change.insert(p->group);
                }
                break;
            case kStr:
                sval = (std::string *)(p->param);
                if (first || p->flags & kCmd || sval[channel] != v.get<std::string>()) {
                    sval[channel] = v.get<std::string>();
                    change.insert(p->group);
                }
                break;

            default:
                break;
        }
    }
}

/**
 * @brief Parses incoming json message.
 * 
 * Parses the incoming json message and calls
 * set_params() or get_params().
 * 
 * @param json_msg 
 * @return string "success" or "Failed" for "set",
 *                a json string for "get".
 */
std::string OspParser::parse(const std::string json_msg) {
    json j;
    try {
        j = json::parse(json_msg);
    } catch (json::parse_error &e) {
        std::cout << "\nERROR: Failed to parse\n";
        std::cout << e.what() << std::endl;
        return "FAILED";
    }

    std::string method = j["method"].get<std::string>();
    if (j == nullptr)
        return "FAILED";

    if (method == "get")
        return get_params();
    if (method == "set") {
        set_params(j["data"], false);
        return "success";
    }
    return "FAILED";
}

#endif  // OSP_PARSER_HPP__