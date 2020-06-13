#ifndef FILE_PLAY_HPP
#define FILE_PLAY_HPP

#include <OSP/fileio/AudioFile.h>
#include <OSP/GarbageCollector/GarbageCollector.hpp>

class file_play{
   public:
       std::string rootPath;
       file_play();
       void rtmha_play(int num_sample, float * out,int channel);
       void set_params(const char*,int, int, int);
       ~file_play();
    struct playfile_param_t {
        int reset;
        int play;
        int repeat;
        int current_position_l;
        int current_position_r;
        int numSamples;
        bool finish;
        int mono;
        std::string filename;
        AudioFile<float> *audiofile;
        ~playfile_param_t(){
            if(audiofile) delete audiofile;
            audiofile = nullptr;
        }

    };
    std::shared_ptr<playfile_param_t> currentParam;
    GarbageCollector releasePool;
};

#endif