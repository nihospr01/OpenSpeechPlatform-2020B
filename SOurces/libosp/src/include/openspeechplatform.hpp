
struct callback_data {
    float **in;
    float **out;
    controls *main_controls;
};

int openspeechplatform(PARSER *parser, MHA *masterHA, controls *main_controls) {
#ifdef STOPWATCH1
    filename = "/root/data/overall" + std::to_string(main_controls->bench_type) + "_" +
               std::to_string(main_controls->k_size) + "_" + std::to_string(main_controls->k_iter) +
               GetCurrentTimeForFileName() + ".csv";
#endif

    callback_data *cb_data = new callback_data();

    cb_data->in = new float *[2];
    cb_data->out = new float *[2];
    for (int i = 0; i < 2; i++) {
        cb_data->in[i] = new float[main_controls->buf_size];
        std::memset(cb_data->in[i], 0, sizeof(float) * main_controls->buf_size);
        cb_data->out[i] = new float[main_controls->buf_size];
        std::memset(cb_data->out[i], 0, sizeof(float) * main_controls->buf_size);
    }
    cb_data->main_controls = main_controls;
    // cb_data->masterHA = masterHA;

    auto cb = [](const void *inputBuffer, void *outputBuffer, unsigned long framesPerBuffer,
                 const PaStreamCallbackTimeInfo *timeInfo, PaStreamCallbackFlags statusFlags, void *userData) -> int {
#ifdef OVERALL
        start_time = std::chrono::steady_clock::now();
#endif
        (void)timeInfo;
        (void)&statusFlags;

        auto cb_data = (callback_data<MHA> *)userData;

        float **in = cb_data->in;
        float **out = cb_data->out;
        auto masterHA = cb_data->masterHA;
        auto main_controls = cb_data->main_controls;
        int in_chan = main_controls->input_channels;
        int out_chan = main_controls->output_channels;

        if (in_chan == 0) {
            // caution! 'in' contains noise. process()
            // should multiply it by 0 because
            // alpha should be set to 1 if the mic
            // is off.
            masterHA->process(in, out, framesPerBuffer);
        } else if (in_chan == 1) {
            auto temp = (float **)inputBuffer;
            std::memcpy(in[0], temp[0], framesPerBuffer * sizeof(float));
            std::memcpy(in[1], temp[0], framesPerBuffer * sizeof(float));
            masterHA->process(in, out, framesPerBuffer);
        } else {
            masterHA->process((float **)inputBuffer, out, framesPerBuffer);
        }

        if (out_chan == 1) {
            std::memcpy(outputBuffer, out[0], framesPerBuffer * sizeof(float));
        } else {
            float **temp = (float **)outputBuffer;
            std::memcpy(temp[0], out[0], framesPerBuffer * sizeof(float));
            std::memcpy(temp[1], out[1], framesPerBuffer * sizeof(float));
        }
#ifdef OVERALL
        if (counter < RECORD_TIME) {
            end_time = std::chrono::steady_clock::now();
            dur[counter] =
                std::chrono::duration_cast<std::chrono::duration<double>>(end_time - start_time).count() * 1000000;
            counter++;
        } else {
            std::ofstream outfile;
            outfile.open(filename);
            for (int i = 0; i < RECORD_TIME; i++) {
                outfile << dur[i] << std::endl;
            }
            outfile.close();
#ifdef STATS
            printStats();
#endif
            exit(0);
        }
#endif
        return paContinue;
    };

#ifdef __linux__
    cpu_set_t cpuset;
    CPU_ZERO(&cpuset);
    CPU_SET(0, &cpuset);
    int rc = pthread_setaffinity_np(pthread_self(), sizeof(cpu_set_t), &cpuset);
    if (rc != 0) {
        std::cerr << "Error calling pthread_setaffinity_np: " << rc << std::endl;
    }
#endif

    // starts OSP: Console thread
    // std::thread *monitoring = new std::thread(&monitor<PARSER, MHA, DATA>, parser, masterHA, main_controls);

#ifdef __linux__
    rc = pthread_setname_np(pthread_self(), "OSP: EWS");
    if (rc != 0)
        std::cerr << "pthread_setname_np failed" << std::endl;
#endif
    ews_connection<PARSER, MHA, DATA> server(parser, masterHA, 8001);

    struct sched_param param;
    int pol;
    int s = pthread_getschedparam(pthread_self(), &pol, &param);
    if (s != 0)
        std::cerr << __func__ << "pthread_getschedparam failed" << std::endl;
    param.sched_priority = 3;
    s = pthread_setschedparam(pthread_self(), SCHED_FIFO, &param);
    if (s != 0)
        std::cerr << __func__ << "pthread_setschedparam failed" << std::endl;

#ifdef __linux__
    CPU_ZERO(&cpuset);
    CPU_SET(3, &cpuset);
    rc = pthread_setaffinity_np(pthread_self(), sizeof(cpu_set_t), &cpuset);
    if (rc != 0) {
        std::cerr << "Error calling pthread_setaffinity_np: " << rc << std::endl;
    }

    rc = pthread_setname_np(pthread_self(), "OSP: Callbk");
    if (rc != 0)
        std::cerr << "pthread_setname_np failed" << std::endl;
#endif

    portaudio_wrapper *audio;
    if (main_controls->file_test)
        main_controls->input_channels = 0;
    if (main_controls->input_device == -1) {
        audio = new portaudio_wrapper(main_controls->input_channels, main_controls->output_channels, cb,
                                      (void *)cb_data, main_controls->samp_freq, main_controls->buf_size);
    } else {
        audio = new portaudio_wrapper(main_controls->input_device, main_controls->input_channels,
                                      main_controls->output_device, main_controls->output_channels, cb, (void *)cb_data,
                                      main_controls->samp_freq, main_controls->buf_size);
    }
    try {
        audio->start_stream();
    } catch (const char *msg) {
        std::cerr << msg << std::endl;
    }
    // std::cout << "Input latency: " << audio->input_latency() << std::endl;
    // std::cout << "Output latency: " << audio->output_latency() << std::endl;

    param.sched_priority = 1;
    s = pthread_setschedparam(pthread_self(), SCHED_FIFO, &param);
    if (s != 0)
        std::cerr << __func__ << "pthread_setschedparam failed" << std::endl;

#ifdef __linux__
    rc = pthread_setname_np(pthread_self(), "OSP: Main");
    if (rc != 0)
        std::cerr << "pthread_setname_np failed" << std::endl;
#endif

    // waits until console exits
    monitoring->join();

    audio->stop_stream();
    delete audio;

    return 0;
}
