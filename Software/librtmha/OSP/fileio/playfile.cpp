#include <stdio.h>
#include <string.h>
#include <string>
#include <stdint.h>
#include <unistd.h>
#include "playfile.h"

using namespace std;

//file_play::file_play(const char* f,int r,int s,int re, int cp)
file_play::file_play()
{
    std::shared_ptr<playfile_param_t> data_current = std::make_shared<playfile_param_t> ();
    rootPath = "/opt/EWS/public";
   // data_current->audiofile = new AudioFile<float>();
    data_current->numSamples = 0;
    data_current->current_position_l=data_current->numSamples;
    data_current->current_position_r=data_current->numSamples;
    data_current->repeat=0;
    data_current->finish = true;
    releasePool.add (data_current);
    std::atomic_store (&currentParam, data_current);

}

void file_play::rtmha_play(int num_sample, float * out, int channel){
    std::shared_ptr<playfile_param_t> data_current = std::atomic_load(&currentParam);
	int playback_channel = channel;
	int current_position;
	if (playback_channel==0)
	{
		current_position=data_current->current_position_l;
	}
	else
	{
		current_position=data_current->current_position_r;
	}
	if(data_current->mono)
		playback_channel = 0;

	if (current_position + num_sample < data_current->numSamples && ! data_current->finish) {

		memcpy(out, &data_current->audiofile->samples[playback_channel][current_position],sizeof(float) * num_sample);
		current_position = current_position + num_sample ;
	} else if(! data_current->finish){
		int temp = current_position + num_sample - data_current->numSamples;
		int remain = data_current->numSamples - current_position;
		if (data_current->repeat==0)
		{
			memcpy(out, &data_current->audiofile->samples[playback_channel][current_position],sizeof(float) * remain);
            data_current->finish = 1;
			printf("Played\n");

		}
		else
		{
			memcpy(out, &data_current->audiofile->samples[playback_channel][current_position],sizeof(float) * remain);
			memcpy(&out[remain], &data_current->audiofile->samples[playback_channel][0], sizeof(float) * temp);
			current_position = current_position + num_sample - data_current->numSamples;

		}
	} else{
	    for(int i = 0; i < num_sample; i++)
	        out[i] = 0;
	}
	if (playback_channel==0)
	{
        data_current->current_position_l=current_position;
	}
	else
	{
        data_current->current_position_r=current_position;
	}
}


void file_play::set_params(const char* file_, int reset_, int repeat_, int play_)
{
    std::shared_ptr<playfile_param_t> data_next = std::make_shared<playfile_param_t> ();
    data_next->filename = rootPath + std::string(file_);
    std::cout<<data_next->filename <<std::endl;
    data_next->audiofile = new AudioFile<float>();
    data_next->audiofile->load(data_next->filename);
    data_next->audiofile->printSummary();
    data_next->numSamples = data_next->audiofile->getNumSamplesPerChannel();
    data_next->mono = data_next->audiofile->isMono();
    data_next->reset=reset_;
    data_next->play=play_;
    if (data_next->play==1){
        data_next->current_position_l=0;
        data_next->current_position_r=0;
    	printf("Start\n");
        data_next->finish=false;
    }
    else{
        data_next->current_position_l = data_next->numSamples;
        data_next->current_position_r = data_next->numSamples;
        data_next->finish = true;
    }

    data_next->repeat=repeat_;
    releasePool.add (data_next);
    std::atomic_store (&currentParam, data_next);
    releasePool.release();

}


file_play::~file_play()
{
    releasePool.release();
	//delete audiofile;
}
