#ifndef OSP_RUN_HPP__
#define OSP_RUN_HPP__

#include <iostream>

#include <unistd.h>  // for sleep
#include <osp.hpp>

#include <osp_parser.hpp>
#include <osp_process.hpp>
#include <osp_audio.hpp>

/**
 * @brief Set thread name, affinity and priority
 *
 * @param thread_name If not NULL, will set the name of the current thread.
 * @param cpu_num If > 0, will set the cpu affinity
 * @param policy If > 0 will set the scheduling policy
 * @param prio priority
 */
void set_cpu_pri(const char *thread_name, int cpu_num, int policy, int prio) {
#ifdef __linux__
    int rc;

    if (thread_name != NULL) {
        rc = pthread_setname_np(pthread_self(), thread_name);
        if (rc != 0)
            std::cerr << "pthread_setname_np failed" << std::endl;
    }


    if (cpu_num >= 0) {
        cpu_set_t cpuset;
        CPU_ZERO(&cpuset);
        CPU_SET(cpu_num, &cpuset);
        rc = pthread_setaffinity_np(pthread_self(), sizeof(cpu_set_t), &cpuset);
        if (rc != 0) {
            std::cerr << "Error calling pthread_setaffinity_np: " << rc << std::endl;
        }
    }

    if (policy >= 0) {
        struct sched_param param;

        if (policy == SCHED_OTHER && prio != 0)
            std::cerr << "SCHED_OTHER only supports priority = 0" << std::endl;

        param.sched_priority = prio;
        rc = pthread_setschedparam(pthread_self(), policy, &param);
        if (rc != 0)
            std::cerr << __func__ << " : pthread_setschedparam failed" << std::endl;
    }
#endif  // __linux__
}


/**
 * @brief Starts OSP processing
 *
 * Starts up all the OSP threads and runs until
 * the network handler exits (which will
 * only happen on SIGINT)
 */
int osp_run() {
    // Has to complete setup before audio
    // Will be updated by network commands
    // OspController *controller = new OspController();

    OspParser *parser = new OspParser();

    do {
        set_cpu_pri("OSP", 0, SCHED_OTHER, 0);

        if (osp::reset) {
            parser->process = NULL;
            params::num_bands = osp::reset;
            std::cout << "BANDS " << params::num_bands << std::endl;
            osp::reset = 0;
            osp::running = 1;
            std::string jdefs;
            if (params::num_bands == 6)
                jdefs = defaults::default_json;
            else
                jdefs = defaults::default10_json;
            parser->set_params(json::parse(jdefs), true);  // load in defaults for new band
        }

        OspProcess *current = new OspProcess();
        // we need to pass a pointer to the OspProcess pointer
        // so it can remain constant while we change OspProcess
        // when bands switch.
        OspProcess **process = &current;
        parser->process = process;

        // we want audio on a high priority
        set_cpu_pri("OSP: AudioCB", 3, SCHED_FIFO, 3);
        portaudio_wrapper *audio = osp_audio(process);
        if (audio == nullptr) {
            delete parser;
            delete *process;
            return -1;
        }

        // set scheduler and priority to normal
        set_cpu_pri("OSP: Net", 0, SCHED_OTHER, 0);

        std::cout << "OSP RUNNING" << std::endl;

        (*process)->enable(1, 1);  // enable audio

        // loop here processing incoming commands
        osp_net(parser);

        std::cout << "OSP STOPPING" << std::endl;
        (*process)->enable(1, 1);  // enable audio during shutdown so threads get called
        usleep(10000);

        std::cout << "stopping stream" << std::endl;
        audio->stop_stream();
        std::cout << "stream stopped" << std::endl;

        audio->delete_stream();
        std::cout << "stream deleted" << std::endl;

        delete *process;
        delete audio;
    } while (osp::reset);

    delete parser;
    return 0;
}
#endif  // OSP_RUN_HPP__