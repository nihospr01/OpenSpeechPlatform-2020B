#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sched.h>
#include <errno.h>
#include <alsa/asoundlib.h>
#include "loopback.h"
#include "alsa_wrapper.h"



char *pdevice = "hw:0,0";
char *cdevice = "hw:0,0";
snd_pcm_format_t format = SND_PCM_FORMAT_S16_LE;
int rate = 48000;
int channels = 2;
int buffer_size = 0;            /* auto */
int period_size = 0;            /* auto */
int latency_min = 48;           /* in frames / 2 */
int counter = 0;
int latency_max = 480;         /* in frames / 2 */
int loop_sec = 30;              /* seconds */
int block = 0;                  /* block mode */
int use_poll = 0;
int resample = 1;
unsigned long loop_limit;
snd_output_t *output = NULL;
int setparams_stream(snd_pcm_t *handle,
                     snd_pcm_hw_params_t *params,
                     const char *id)
{
    int err;
    unsigned int rrate;
    err = snd_pcm_hw_params_any(handle, params);
    if (err < 0) {
        printf("Broken configuration for %s PCM: no configurations available: %s\n", snd_strerror(err), id);
        return err;
    }
    err = snd_pcm_hw_params_set_rate_resample(handle, params, resample);
    if (err < 0) {
        printf("Resample setup failed for %s (val %i): %s\n", id, resample, snd_strerror(err));
        return err;
    }
    err = snd_pcm_hw_params_set_access(handle, params, SND_PCM_ACCESS_RW_INTERLEAVED);
    if (err < 0) {
        printf("Access type not available for %s: %s\n", id, snd_strerror(err));
        return err;
    }
    err = snd_pcm_hw_params_set_format(handle, params, format);
    if (err < 0) {
        printf("Sample format not available for %s: %s\n", id, snd_strerror(err));
        return err;
    }
    err = snd_pcm_hw_params_set_channels(handle, params, channels);
    if (err < 0) {
        printf("Channels count (%i) not available for %s: %s\n", channels, id, snd_strerror(err));
        return err;
    }
    rrate = rate;
    err = snd_pcm_hw_params_set_rate_near(handle, params, &rrate, 0);
    if (err < 0) {
        printf("Rate %iHz not available for %s: %s\n", rate, id, snd_strerror(err));
        return err;
    }
    if ((int)rrate != rate) {
        printf("Rate doesn't match (requested %iHz, get %iHz)\n", rate, err);
        return -EINVAL;
    }
    return 0;
}
int setparams_bufsize(snd_pcm_t *handle,
                      snd_pcm_hw_params_t *params,
                      snd_pcm_hw_params_t *tparams,
                      snd_pcm_uframes_t bufsize,
                      const char *id)
{
    int err;
    snd_pcm_uframes_t periodsize;
    snd_pcm_hw_params_copy(params, tparams);
    periodsize = bufsize * 2;
    err = snd_pcm_hw_params_set_buffer_size_near(handle, params, &periodsize);
    if (err < 0) {
        printf("Unable to set buffer size %li for %s: %s\n", bufsize * 2, id, snd_strerror(err));
        return err;
    }
    if (period_size > 0)
        periodsize = period_size;
    else
        periodsize /= 2;
    err = snd_pcm_hw_params_set_period_size_near(handle, params, &periodsize, 0);
    if (err < 0) {
        printf("Unable to set period size %li for %s: %s\n", periodsize, id, snd_strerror(err));
        return err;
    }
    return 0;
}
int setparams_set(snd_pcm_t *handle,
                  snd_pcm_hw_params_t *params,
                  snd_pcm_sw_params_t *swparams,
                  const char *id)
{
    int err;
    snd_pcm_uframes_t val;
    err = snd_pcm_hw_params(handle, params);
    if (err < 0) {
        printf("Unable to set hw params for %s: %s\n", id, snd_strerror(err));
        return err;
    }
    err = snd_pcm_sw_params_current(handle, swparams);
    if (err < 0) {
        printf("Unable to determine current swparams for %s: %s\n", id, snd_strerror(err));
        return err;
    }
    err = snd_pcm_sw_params_set_start_threshold(handle, swparams, 0x7fffffff);
    if (err < 0) {
        printf("Unable to set start threshold mode for %s: %s\n", id, snd_strerror(err));
        return err;
    }
    if (!block)
        val = 4;
    else
        snd_pcm_hw_params_get_period_size(params, &val, NULL);
    err = snd_pcm_sw_params_set_avail_min(handle, swparams, val);
    if (err < 0) {
        printf("Unable to set avail min for %s: %s\n", id, snd_strerror(err));
        return err;
    }
    err = snd_pcm_sw_params(handle, swparams);
    if (err < 0) {
        printf("Unable to set sw params for %s: %s\n", id, snd_strerror(err));
        return err;
    }
    return 0;
}
int setparams(snd_pcm_t *phandle, snd_pcm_t *chandle, int *bufsize)
{
    int err, last_bufsize = *bufsize;
    snd_pcm_hw_params_t *pt_params, *ct_params;     /* templates with rate, format and channels */
    snd_pcm_hw_params_t *p_params, *c_params;
    snd_pcm_sw_params_t *p_swparams, *c_swparams;
    snd_pcm_uframes_t p_size, c_size, p_psize, c_psize;
    unsigned int p_time, c_time;
    unsigned int val;
    snd_pcm_hw_params_alloca(&p_params);
    snd_pcm_hw_params_alloca(&c_params);
    snd_pcm_hw_params_alloca(&pt_params);
    snd_pcm_hw_params_alloca(&ct_params);
    snd_pcm_sw_params_alloca(&p_swparams);
    snd_pcm_sw_params_alloca(&c_swparams);
    if ((err = setparams_stream(phandle, pt_params, "playback")) < 0) {
        printf("Unable to set parameters for playback stream: %s\n", snd_strerror(err));
        exit(0);
    }
    if ((err = setparams_stream(chandle, ct_params, "capture")) < 0) {
        printf("Unable to set parameters for playback stream: %s\n", snd_strerror(err));
        exit(0);
    }
    if (buffer_size > 0) {
        *bufsize = buffer_size;
        goto __set_it;
    }
    __again:
    if (buffer_size > 0)
        return -1;
    if (last_bufsize == *bufsize) {
        counter ++;
        *bufsize = latency_min * counter;
    }
    last_bufsize = *bufsize;
    if (*bufsize > latency_max)
        return -1;
    __set_it:
    if ((err = setparams_bufsize(phandle, p_params, pt_params, *bufsize, "playback")) < 0) {
        printf("Unable to set sw parameters for playback stream: %s\n", snd_strerror(err));
        exit(0);
    }
    if ((err = setparams_bufsize(chandle, c_params, ct_params, *bufsize, "capture")) < 0) {
        printf("Unable to set sw parameters for playback stream: %s\n", snd_strerror(err));
        exit(0);
    }
    snd_pcm_hw_params_get_period_size(p_params, &p_psize, NULL);
    if (p_psize > (unsigned int)*bufsize)
        *bufsize = p_psize;
    snd_pcm_hw_params_get_period_size(c_params, &c_psize, NULL);
    if (c_psize > (unsigned int)*bufsize)
        *bufsize = c_psize;
    snd_pcm_hw_params_get_period_time(p_params, &p_time, NULL);
    snd_pcm_hw_params_get_period_time(c_params, &c_time, NULL);
    if (p_time != c_time)
        goto __again;
    snd_pcm_hw_params_get_buffer_size(p_params, &p_size);
    if (p_psize * 2 < p_size) {
        snd_pcm_hw_params_get_periods_min(p_params, &val, NULL);
        if (val > 2) {
            printf("playback device does not support 2 periods per buffer\n");
            exit(0);
        }
        goto __again;
    }
    snd_pcm_hw_params_get_buffer_size(c_params, &c_size);
    if (c_psize * 2 < c_size) {
        snd_pcm_hw_params_get_periods_min(c_params, &val, NULL);
        if (val > 2 ) {
            printf("capture device does not support 2 periods per buffer\n");
            exit(0);
        }
        goto __again;
    }
    if ((err = setparams_set(phandle, p_params, p_swparams, "playback")) < 0) {
        printf("Unable to set sw parameters for playback stream: %s\n", snd_strerror(err));
        exit(0);
    }
    if ((err = setparams_set(chandle, c_params, c_swparams, "capture")) < 0) {
        printf("Unable to set sw parameters for playback stream: %s\n", snd_strerror(err));
        exit(0);
    }
    if ((err = snd_pcm_prepare(phandle)) < 0) {
        printf("Prepare error: %s\n", snd_strerror(err));
        exit(0);
    }
    snd_pcm_dump(phandle, output);
    snd_pcm_dump(chandle, output);
    fflush(stdout);
    return 0;
}
void showstat(snd_pcm_t *handle, size_t frames)
{
    int err;
    snd_pcm_status_t *status;
    snd_pcm_status_alloca(&status);
    if ((err = snd_pcm_status(handle, status)) < 0) {
        printf("Stream status error: %s\n", snd_strerror(err));
        exit(0);
    }
    printf("*** frames = %li ***\n", (long)frames);
    snd_pcm_status_dump(status, output);
}
void showlatency(size_t latency)
{
    double d;
    latency *= 2;
    d = (double)latency / (double)rate;
    printf("Trying latency %li frames, %.3fus, %.6fms (%.4fHz)\n", (long)latency, d * 1000000, d * 1000, (double)1 / d);
}
void showinmax(size_t in_max)
{
    double d;
    printf("Maximum read: %li frames\n", (long)in_max);
    d = (double)in_max / (double)rate;
    printf("Maximum read latency: %.3fus, %.6fms (%.4fHz)\n", d * 1000000, d * 1000, (double)1 / d);
}
void gettimestamp(snd_pcm_t *handle, snd_timestamp_t *timestamp)
{
    int err;
    snd_pcm_status_t *status;
    snd_pcm_status_alloca(&status);
    if ((err = snd_pcm_status(handle, status)) < 0) {
        printf("Stream status error: %s\n", snd_strerror(err));
        exit(0);
    }
    snd_pcm_status_get_trigger_tstamp(status, timestamp);
}
void setscheduler(void)
{
    struct sched_param sched_param;
    if (sched_getparam(0, &sched_param) < 0) {
        printf("Scheduler getparam failed...\n");
        return;
    }
    sched_param.sched_priority = sched_get_priority_max(SCHED_RR);
    if (!sched_setscheduler(0, SCHED_RR, &sched_param)) {
        printf("Scheduler set to Round Robin with priority %i...\n", sched_param.sched_priority);
        fflush(stdout);
        return;
    }
    printf("!!!Scheduler set to Round Robin with priority %i FAILED!!!\n", sched_param.sched_priority);
}
long readbuf(snd_pcm_t *handle, char *buf, long len, size_t *frames, size_t *max)
{
    long r;
    if (!block) {
        do {
            r = snd_pcm_readi(handle, buf, len);
        } while (r == -EAGAIN);
        if (r > 0) {
            *frames += r;
            if ((long)*max < r)
                *max = r;
        }
        // printf("read = %li\n", r);
    } else {
        int frame_bytes = (snd_pcm_format_width(format) / 8) * channels;
        do {
            r = snd_pcm_readi(handle, buf, len);
            if (r > 0) {
                buf += r * frame_bytes;
                len -= r;
                *frames += r;
                if ((long)*max < r)
                    *max = r;
            }
            // printf("r = %li, len = %li\n", r, len);
        } while (r >= 1 && len > 0);
    }
    // showstat(handle, 0);
    return r;
}
long writebuf(snd_pcm_t *handle, char *buf, long len, size_t *frames) {
    long r;
    while (len > 0) {
        r = snd_pcm_writei(handle, buf, len);
        if (r == -EAGAIN)
            continue;
        // printf("write = %li\n", r);
        if (r < 0)
            return r;
        // showstat(handle, 0);
        buf += r * 4;
        len -= r;
        *frames += r;
    }
    return 0;
}
void * alsa_stream( void * tid )
{
    snd_pcm_t *phandle, *chandle;
    float inL[48], outL[48], inR[48], outR[48];
    char *buffer;
    int err, latency, i, j;
    int ok;
    snd_timestamp_t p_tstamp, c_tstamp;
    ssize_t r;
    size_t frames_in, frames_out, in_max;
    int effect = 0;

    err = snd_output_stdio_attach(&output, stdout, 0);
    if (err < 0) {
        printf("Output failed: %s\n", snd_strerror(err));
    }
    loop_limit = loop_sec * rate;
    latency = latency_min;
    buffer = malloc((latency_max * snd_pcm_format_width(format) / 8) * 2);
    setscheduler();
    printf("Playback device is %s\n", pdevice);
    printf("Capture device is %s\n", cdevice);
    printf("Parameters are %iHz, %s, %i channels, %s mode\n", rate, snd_pcm_format_name(format), channels, block ? "blocking" : "non-blocking");
    printf("Poll mode: %s\n", use_poll ? "yes" : "no");
    printf("Loop limit is %li frames, minimum latency = %i, maximum latency = %i\n", loop_limit, latency_min * 2, latency_max * 2);
    if ((err = snd_pcm_open(&phandle, pdevice, SND_PCM_STREAM_PLAYBACK, block ? 0 : SND_PCM_NONBLOCK)) < 0) {
        printf("Playback open error: %s\n", snd_strerror(err));
    }
    if ((err = snd_pcm_open(&chandle, cdevice, SND_PCM_STREAM_CAPTURE, block ? 0 : SND_PCM_NONBLOCK)) < 0) {
        printf("Record open error: %s\n", snd_strerror(err));
    }


    while (1) {
        frames_in = frames_out = 0;
        if (setparams(phandle, chandle, &latency) < 0)
            break;
        showlatency(latency);
        if ((err = snd_pcm_link(chandle, phandle)) < 0) {
            printf("Streams link error: %s\n", snd_strerror(err));
            exit(0);
        }
        if (snd_pcm_format_set_silence(format, buffer, latency*channels) < 0) {
            fprintf(stderr, "silence error\n");
            break;
        }
        if (writebuf(phandle, buffer, latency, &frames_out) < 0) {
            fprintf(stderr, "write error\n");
            break;
        }
        if (writebuf(phandle, buffer, latency, &frames_out) < 0) {
            fprintf(stderr, "write error\n");
            break;
        }
        if ((err = snd_pcm_start(chandle)) < 0) {
            printf("Go error: %s\n", snd_strerror(err));
            exit(0);
        }
        gettimestamp(phandle, &p_tstamp);
        gettimestamp(chandle, &c_tstamp);

        ok = 1;
        in_max = 0;
        while (ok) {
            if (use_poll) {
                /* use poll to wait for next event */
                snd_pcm_wait(chandle, 1000);
            }
            if ((r = readbuf(chandle, buffer, latency, &frames_in, &in_max)) < 0)
                ok = 0;
            else {
                for(i = 0; i < counter; i++){
                    for(j = 0; j < 48; j ++){
                        inL[j] = (((float)(buffer[i*48*2+j*2])) / (1 << 15));
                        inR[j] = (((float)(buffer[i*48*2+j*2+1])) / (1 << 15));
                    }
                    run_loopback(inL, inR, outL, outR, 48);
                    for(j = 0; j < 48; j ++){
                        buffer[i*48*2+j*2] = ((int)((outL[j]) * (1 << 15)));
                        buffer[i*48*2+j*2+1] = ((int)((outR[j]) * (1 << 15)));
                    }
                }
                if (writebuf(phandle, buffer, r, &frames_out) < 0)
                    ok = 0;
            }
        }
        printf("Failure\n");
        printf("Playback:\n");
        showstat(phandle, frames_out);
        printf("Capture:\n");
        showstat(chandle, frames_in);
        showinmax(in_max);
        if (p_tstamp.tv_usec == c_tstamp.tv_usec)
            printf("Hardware sync\n");
        snd_pcm_drop(chandle);
        snd_pcm_nonblock(phandle, 0);
        snd_pcm_drain(phandle);
        snd_pcm_nonblock(phandle, !block ? 1 : 0);

        snd_pcm_unlink(chandle);
        snd_pcm_hw_free(phandle);
        snd_pcm_hw_free(chandle);
    }
    snd_pcm_close(phandle);
    snd_pcm_close(chandle);
}