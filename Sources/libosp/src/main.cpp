#include <string>
#include <iostream>
#include <fstream>

#include <config.hpp>  // initial json config
#include <config10.hpp>  // initial 10-band json config
#include <osp_defaults.hpp>
#include <osp_run.hpp>

#include <unistd.h>
#include <syslog.h>
#include <signal.h>
#include <getopt.h>
#include <string.h>
#include <fcntl.h>

#include "OspVersion.hpp"

static int pid_fd = -1;
static std::string pid_filename;
static std::string app_name;

/**
 * @brief Redirect stdout and stderr to a log file
 *
 * @param log_name
 */
void redirect_io(std::string log_name) {
    int newfd = open(log_name.c_str(), O_WRONLY | O_APPEND | O_CREAT, S_IRUSR | S_IWUSR | S_IRGRP | S_IROTH);
    if (newfd == -1) {
        syslog(LOG_ERR, "Can not open log file: %s, error: %s", log_name.c_str(), strerror(errno));
        return;
    }
    dup2(newfd, fileno(stdout));
    dup2(newfd, fileno(stderr));
    close(newfd);
}

/**
 * @brief Callback function for handling signals
 *
 * @param sig
 */
void handle_signal(int sig) {
    if (sig == SIGINT) {
        std::cout << "Got SIGINT. Stopping daemon" << std::endl;
        /* Unlock and close lockfile */
        if (pid_fd != -1) {
            if (lockf(pid_fd, F_ULOCK, 0)) {
                std::cout << "Failed to lock pid file." << std::endl;
                exit(-1);
            }
            close(pid_fd);
        }
        if (!pid_filename.empty()) {
            unlink(pid_filename.c_str());
        }
        osp::running = 0;
        /* Reset signal handling to default behavior */
        signal(SIGINT, SIG_DFL);
    } else if (sig == SIGHUP) {
        std::cout << "GOT SIGHUP!!\n";
    } else if (sig == SIGCHLD) {
        std::cout << "Received SIGCHLD\n";
    }
}


void print_help(void) {
    std::cout << "\n Usage: " << app_name << " [OPTIONS]\n";
    std::cout << "  Options:\n";
    std::cout << "   -c --conf_file filename   Read configuration from the file\n";
    std::cout << "   -l --log_file  filename   Write logs to the file\n";
    std::cout << "   -d --daemon               Daemonize this application\n";
    std::cout << "   -p --pid_file  filename   PID file used by daemonized app\n";
    std::cout << "   -i --input_device num     input device number\n";
    std::cout << "   -o --output_device num    output device number\n";
    std::cout << "   -I --input_channels num   number of input channels\n";
    std::cout << "   -O --output_channels num  number of output channels\n";
    std::cout << "   -m --multithreaded bool   multithreaded\n\n";
}

/* Main function */
int main(int argc, char *argv[]) {
    const struct option long_options[] = {{"conf_file", required_argument, 0, 'c'},
                                          {"log_file", required_argument, 0, 'l'},
                                          {"daemon", no_argument, 0, 'd'},
                                          {"pid_file", required_argument, 0, 'p'},
                                          {"input_device", required_argument, 0, 'i'},
                                          {"output_device", required_argument, 0, 'o'},
                                          {"input_channels", required_argument, 0, 'I'},
                                          {"output_channels", required_argument, 0, 'O'},
                                          {"multithreaded", no_argument, 0, 'm'},
                                          {NULL, 0, 0, 0}};
    int value, option_index = 0;
    std::string log_name;
    int make_daemon = 0;

    std::cout << "OSP (RT-MHA) Version " << OSP_VERSION << std::endl;

    if(getenv("OSP_MEDIA") == nullptr) {
        std::cout << "Error: OSP_MEDIA is not set\n";
        std::cout << "Please set it to your media directory and restart." << std::endl;
        exit(EXIT_FAILURE);
    }
    
    app_name = argv[0];

    /* Try to process all command line arguments */
    while ((value = getopt_long(argc, argv, "c:l:p:dhi:o:I:O:m", long_options, &option_index)) != -1) {
        switch (value) {
            case 'c':
                defaults::conf_filename = optarg;
                break;
            case 'l':
                log_name = optarg;
                break;
            case 'p':
                pid_filename = optarg;
                break;
            case 'd':
                make_daemon = 1;
                break;
            case 'i':
                defaults::input_device = std::stoi(optarg);
                break;
            case 'o':
                defaults::output_device = std::stoi(optarg);
                break;
            case 'I':
                defaults::input_channels = std::stoi(optarg);
                break;
            case 'O':
                defaults::output_channels = std::stoi(optarg);
                break;
            case 'm':
                defaults::multithreaded = true;
                break;
            case 'h':
                print_help();
                return EXIT_SUCCESS;
            default:
                break;
        }
    }


    if (make_daemon == 1) {
        if (daemon(1, 1)) {
            std::cout << "Failed to start daemon" << std::endl;
            exit(errno);
        }

        /* Write PID to lockfile */
        if (!pid_filename.empty()) {
            pid_fd = open(pid_filename.c_str(), O_RDWR | O_CREAT, 0640);
            if (pid_fd < 0) {
                exit(EXIT_FAILURE);
            }
            if (lockf(pid_fd, F_TLOCK, 0) < 0) {
                exit(EXIT_FAILURE);
            }
            dprintf(pid_fd, "%d\n", getpid());
        }
    }

    // redirect stdout and stderr to log file
    if (!log_name.empty())
        redirect_io(log_name);

    // Open the system log
    openlog(argv[0], LOG_PID | LOG_CONS, LOG_DAEMON);
    syslog(LOG_INFO, "Started %s", app_name.c_str());

    // set up signal handler
    struct sigaction a;
    a.sa_handler = handle_signal;
    a.sa_flags = 0;
    sigemptyset(&a.sa_mask);
    sigaction(SIGINT, &a, NULL);
    sigaction(SIGHUP, &a, NULL);

    std::cout << "New process started at PID " << getpid() << std::endl;


    // flag set in signal handler
    osp::running = 1;

    // does not return until terminated by command or signal
    if (osp_run())
        return EXIT_FAILURE;

    syslog(LOG_INFO, "Stopped %s", app_name.c_str());
    closelog();

    return EXIT_SUCCESS;
}