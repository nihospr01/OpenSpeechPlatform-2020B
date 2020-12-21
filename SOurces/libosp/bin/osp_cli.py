#!/usr/bin/env python3
# This implements a command-line interface to the osp process (RT-MHA)
import sys
import atexit
import code
import os
import readline
import shlex
import time
import argparse
import socket
import select
import json

try:
    from colors import color
except ModuleNotFoundError:
    print("Module 'color' not found. To install, run")
    print('"pip3 install ansicolors"')
    sys.exit(0)

parameters = {
"alpha": "The mixer of virtual sound (file playback) and actual sound (from the mics) where 0 is complete actual sound and 1 is complete virtual sound",
"en_ha":"Enable hearing aid algorithm. 0=off, 1=on",
"gain":"The gain in dB",
"global_mpo":"Set the global mpo limit",
# "rear_mics":"Enable rear microphones. 0=off, 1=on",
"afc":"Enable Adaptive Feedback Cancellation. 0=off, 1=on",
"afc_delay":"Delay in millisecond to adjust for device to device variation in feedback path",
"afc_mu":"Adjust the step size for feedback management",
"afc_power_estimate":"Adjust the power estimate for feedback management",
"afc_reset":"Reset the taps of AFC filter to default values. A signal, not a state!",
"afc_rho":"Adjust the forgetting factor for feedback management",
"afc_type":"Adaptation type for AFC. 0: Stop adaptation, 1: FXLMS, 2: IPNLMS, 3: SLMS",
"amc_forgetting_factor":"Adjust the forgetting factor of the power estimate for GSC beamforming",
"amc_thr":"Adjust the threshold of the AMC for GSC beamforming",
"audio_filename":"Audio file to play.  Absolute pathname or relative to OSP_MEDIA",
"audio_play":"Play the audio",
"audio_playing":"[Readonly] Is audio playing?",
"audio_recordfile":"Filename for recordings",
"audio_repeat":"Repeat the audio file",
"audio_reset":"Reset audio",
"bf":"Enable Beamformer. 0=off, 1=on",
"bf_alpha":"A number between -1 to 1 for different degrees of sparsity in IPNLMS-l_0",
"bf_amc_on_off":"Enable adaptation mode control beamformer. 0=off, 1=on",
"bf_beta":"A number between 0 to 500 for different degrees of sparsity in IPNLMS-l_0",
"bf_c":"A small positive number for preventing stagnation in SLMS",
"bf_delay_len":"The length of delay line in samples for beamformer",
"bf_delta":"A small positive number to prevent dividing zero",
"bf_fir_length":"The number of filter taps of adaptive filter in beamformer",
"bf_mu":"Adjust the step size for GSC beamforming (Help has wrong default)",
"bf_nc_on_off":" Enable norm-constrained beamformer: 0=off, 1=on",
"bf_p":"A number between 0 to 2 for fitting different degrees of sparsity in SLMS",
"bf_power_estimate":"Adjust the power estimate for beamforming [NOT USED]",
"bf_rho":"Adjust the forgetting factor for beamforming",
"bf_type":"Adaptation type for GSC beamforming. 0: Stop adaptation, 1: Modified LMS, 2: IPNLMS, 3: SLMS",
"nc_thr":"Adjust the threshold of the norm-constrained adaptation for GSC beamforming ",
"freping":"Enable freping: 0=off, 1=on",
"freping_alpha [num_bands]":"Set the alpha parameter for freping",
"attack [num_bands]":"Set the attack time for WDRC for the bands",
"g50 [num_bands]":"Set the gain values at 50 dB SPL input level",
"g80 [num_bands]":"Set the gain values at 80 dB SPL input level",
"knee_low [num_bands]":"Set the lower knee points for the bands",
"mpo_band [num_bands]":"Set the MPO (upper knee points) for the bands",
"release [num_bands]":"Set the release time for WDRC for the bands",
"restart":"Restart osp (can take several seconds)",
"noise_estimation_type":"Noise Estimation type: 0=Disable; 1=Arslan; 2=Hirsch and Ehrlicher; 3=Cohen and Berdugo",
"spectral_subtraction":"Amount to subtract between range: [0,1)",
"spectral_type":"Enable spectral subtraction: 0=Disable, 1=Enable",
"aligned":"Enable filterbank alignment. [Multirate (10 band) only]",
"num_bands":"Sets the number of bands to 6 or 10.  Changing the number of bands resets all the parameters"
}

def dirtree(path):
    for dir_, _, files in os.walk(path):
        for f in files:
            if os.path.splitext(f)[1] == '.wav':
                rel_dir = os.path.relpath(dir_, path)
                if rel_dir != ".":
                    f = os.path.join(rel_dir, f)
                print(f)

def print_params(par):
    if par.startswith("play "):
        path = os.environ.get("OSP_MEDIA")
        if path is None:
            print(color("OSP_MEDIA is not set.", "red"))
            return
        dirtree(path)
        return

    for k in parameters:
        if k.startswith(par):
            print(k.ljust(20),parameters[k])

class Timer():
    def __init__(self, message):
        self.message = message
    def __enter__(self):
        self.start = time.time()
        return None 
    def __exit__(self, _type, value, traceback):
        elapsed_time = (time.time() - self.start) * 1000
        print(self.message.format(elapsed_time))

def send_command(message):
    global hostname
    # Create a TCP/IP socket
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

    # Connect the socket to the port where the server is listening
    server_address = (hostname, 8001)
    
    try:
        sock.connect(server_address)
    except ConnectionRefusedError:
        return "ERROR: osp not running"

    sent = False
    while True:
        readable, writable, exceptional = select.select([sock], [sock], [sock])
        if sock in writable and not sent:
            sock.sendall(message)
            sent = True
        if sock in readable:
            data = sock.recv(8096)
            if data:
                sock.close()
                return data.decode()
            else:
                return "FAILED"
        if sock in exceptional:
            sock.close()
            return "ERROR"

def convert_string_to_value(value):
    " The user typed in a value. Find the correct type."

    # try to cast to int or float
    done = False
    try:
        val = int(value)
        done = True
    except:
        pass
    if not done:
        try:
            val = float(value)
            done = True
        except:
            pass
    if not done:
        try:
            # this is another workaround.
            # The OSP parser cannot handle booleans
            vlow = value.lower()
            if vlow == 'true':
                val = True
            elif vlow == 'false':
                val = False
            if isinstance(val, bool):
                if val:
                    val = 1
                else:
                    val = 0
                done = True
        except:
            pass
    if not done:
        # list (array)
        try:
            val = value.lstrip('[').rstrip(']')
            val = [float(x) for x in val.split(',')]
        except:
            val = value
    return val

class HistoryConsole(code.InteractiveConsole):
    def __init__(self, locals=None, filename="<console>",
                 histfile=os.path.expanduser("~/.osp-history")):
        code.InteractiveConsole.__init__(self, locals, filename)
        self.init_history(histfile)

    def init_history(self, histfile):
        readline.parse_and_bind("tab: complete")
        if hasattr(readline, "read_history_file"):
            try:
                readline.read_history_file(histfile)
            except FileNotFoundError:
                pass
            atexit.register(self.save_history, histfile)

    def save_history(self, histfile):
        readline.set_history_length(10000)
        readline.write_history_file(histfile)


class CommandRunner(object):
    "Sends commands and receives responses"
    print_time = False

    def __init__(self):
        self.commands_ = {}
        self.varlist_ = {}
        
    def command(self, name, fn):
        self.commands_[name] = fn

    def varlist(self, name, fn):
        self.varlist_[name] = fn

    def get_kvlist(self, json_res, varname):
        "return a list of keys, left values, right values"
        keys = sorted([k for k in json_res['left']])
        if varname:
            keys = [k for k in keys if k.startswith(varname)]
        chan = {}
        for channel in ['left', 'right']:
            r = json_res[channel]
            chan[channel] = []
            for k in keys:
                if k not in r:
                    chan[channel].append("---");
                    continue
                if isinstance(r[k], str):
                    chan[channel].append(r[k])
                elif isinstance(r[k], int):
                    chan[channel].append(r[k])
                elif isinstance(r[k], list):
                    chan[channel].append(','.join([f'{x:.4f}'.rstrip('0').rstrip('.') for x in r[k]]))
                else:
                    chan[channel].append(f'{r[k]:.4f}'.rstrip('0'))
        return zip(keys, chan['left'], chan['right'])

    def get_vars(self, varname=None):
        "get selected variables"
        cmd = '{"method": "get"}'.encode()
        if CommandRunner.print_time:
            with Timer(color("[{0:.3f} ms]", "yellow")):
                result = send_command(cmd)
        else:
            result = send_command(cmd)

        # print("RESULT: ", result)
        
        if result == 'FAILED' or result.startswith('ERROR'):
            print(color(result, "red"))
            return

        result = json.loads(result)
        vlist = list(self.get_kvlist(result, varname))

        acol = max([len(color(v[1], 'yellow')) for v in vlist]) + 2
        rcol = 'yellow'
        if 'num_bands' in result:
            print(f"{color('num_bands', 'green').ljust(31)}{color(result['num_bands'], rcol).ljust(acol)}")
        print("-" * 23)
        for v in vlist:
            print(f"{color(v[0], 'green').ljust(31)}{color(v[1], rcol).ljust(acol)}{color(v[2], rcol)}")

    def set_var(self, varname, value, channel=None):
        # print(f"SET: {varname} = {value}aud for channel {channel}")

        val = convert_string_to_value(value)
        send_data = {'method': 'set', 'data': {}}

        if varname == "restart":
            send_data['data']['restart'] = 1;
        elif varname == "num_bands":
            send_data['data']['num_bands'] = val;
        else:
            sdict = {varname : val}
            if channel ==  'left' or channel is None:
                send_data['data']['left'] = sdict
            if channel ==  'right' or channel is None:
                send_data['data']['right'] = sdict

        send_data = json.dumps(send_data)
        print(color("Sending " + send_data, "gray"))

        if CommandRunner.print_time:
            with Timer(color("[{0:.3f} ms]", "yellow")):
                res = send_command(send_data.encode())
        else:
            res = send_command(send_data.encode())
        if res != 'success':
            print(color(res, 'red'))

    def run(self, msg):
        " run what the user typed"
        msg = msg.lstrip()
        if len(msg) == 0 or msg[0] == '#':
            return
        # print(f"RUN: {msg}")

        if msg.endswith('?'):
            print_params(msg[0:-1])
            return

        # is it a json command to send?
        if msg[0] == '{':
            msg = msg.encode()
            if CommandRunner.print_time:
                with Timer(color("[{0:.3f} ms]", "yellow")):
                    result = send_command(msg)
            else:
                    result = send_command(msg)
            if result == 'FAILED' or result == 'ERROR':
                print(color(result, "red"))
            elif result == 'success':
                print(color(result, "green"))
            else:
                try:
                    result = json.loads(result)
                    print(color(json.dumps(result, sort_keys=True, indent=4), "green"))
                except:
                    print(color(f"Unexpected result: {result}", "red"))
            return
        
        # check to equals sign
        set_cmd = '=' in msg
        if set_cmd:
            # ensure spaces around equals
            msg = ' = '.join(msg.split('='))

        tokens = shlex.split(msg, comments=True)
        # print(f"token={tokens}")
        if tokens:
            if set_cmd:
                channel = None
                offset = 0
                if tokens[0].lower() == 'left':
                    channel = 'left'
                    offset = 1
                if tokens[0].lower() == 'right':
                    channel = 'right'
                    offset = 1
                if tokens[offset + 1] != '=':
                    print("ERROR: set command format is \"[left|right] {parameter} = {value}\"")
                    return
                self.set_var(tokens[offset], tokens[offset + 2], channel)
                return

            command, args = tokens[0], tokens[1:]
            if command in self.commands_:
                result = self.commands_[command](*args)
                if result is not None:
                    print(result)
                return
        self.get_vars(command)



class Console(object):

    banner="OSP Console.  Enter \"help\" for help."

    def __init__(self, runner):
        self.runner = runner

    def run(self, fd):
        for line in fd:
            print("\nOSP> " + color(line, "blue"))
            self.runner.run(line)

    def interact(self, locals=None):
        class OSPConsole(HistoryConsole):
            def runsource(code_console, source, filename=None, symbol=None):
                # Return True if more input needed, else False.
                try:
                    self.runner.run(source)
                except SystemExit:
                    raise
                except:
                    code_console.showtraceback()
                return False

        sys.ps1 = 'OSP> '
        OSPConsole(locals=locals, filename="<demo>").interact(banner=Console.banner)

    def run_in_main(self, fd=None, interact=False):
        if fd is None:
            fd = sys.stdin
        if fd.isatty():
            self.interact()
        else:
            try:
                self.run(fd=fd)
            except Exception as err:
                print(err, file=sys.stderr)
                return 1
            Console.banner = ""
            self.interact()
        return 0

class Commands:    
    def __init__(self, runner):
        self.runner = runner

    def exit(self):
        sys.exit(0)

    def get(self):
        print(color('Sending {"method": "get"}', "grey"))
        # self.runner.run('{"method": "get"}')
        self.runner.get_vars()

    def play(self, file="MPEG_es01_s.wav"):
        cmd = '{"method": "set", "data": {"left": {"audio_filename": "%s", "audio_play": 1, "alpha": 1},"right": {"alpha": 1}}}' % file
        print(color(f'Sending {cmd}', "grey"))
        self.runner.run(cmd)

    def stop(self):
        cmd = '{"method": "set", "data": {"left": {"audio_play": 0}}}'
        print(color(f'Sending {cmd}', "grey"))
        self.runner.run(cmd)

    def help(self):
        print(color("====== Commands ======", "grey"))
        print(color("help", "red"))
        print("\tThis menu.")
        print(color("play [file]", "red"))
        print("\tPlay a file. \"play ?\" lists files.")
        print("\tDefaults to \"MPEG_es01_s.wav\"")
        print(color("stop", "red"))
        print("\tStop playing. Requires latest osp with OSP_MEDIA set.")
        print(color("sleep [secs]", "red"))
        print("\tSleep for a number of seconds. Default 1")
        print(color("source <filename>", "red"))
        print("\tRead commands from a file.")
        print(color("repeat cmd num [delay]", "red"))
        print("\tRepeat a command \"num\" times with an optional")
        print("\tdelay of \"delay\" ms between.")
        print(color("time [on|off]", "red"))
        print("\tEnable|Disable printing the execution time for commands")
        print(color("exit (or control-D)", "red"))
        print("\tExit this program.")
        print(color("====== Reading Data ======", "grey"))
        print(color("get", "green")+"\tGet all data")
        print(color("?", "green"))
        print("\tPrint help on parameters.  \"{pat}?\" will match all parameters\n\tbeginning with {pat}")
        print("\nAnything that is JSON (starts with '{') is sent directly to RT-MHA.")
        print("\nType a string and all parameters that start with that string will be displayed.")
        print("\nSet a parameter with \"[left | right] {param} = {value}")
        print(color("====== Setting Parameters ======", "grey"))
        print(color("{param}={value}", "green")+"\tSet a parameter to a value.\n\n")

    def source(self, filename=None):
        if filename is None:
            print("Filename is required")
            return

        with open(filename) as fd:
            print(color(40*'=', "grey"))
            for line in fd:

                print("\nOSP> " + color(line, "blue"))
                self.runner.run(line)
            print(color(40*'=' + '\n', "grey"))

    def time(self, val):
        val = val.lower() 
        CommandRunner.print_time = val == 'on'

    def var(self, varname):
        self.runner.get_vars(varname)
  
    def sleep(self, secs=1):
        time.sleep(float(secs))

    def repeat(self, cmd, num, delay=None):
        print(f"REPEAT \"{cmd}\" num={num} delay={delay}")
        num = int(num)
        for i in range(num):
            self.runner.run(cmd)
            if delay:
                time.sleep(float(delay)/1000)

def main(fd=None):
    runner = CommandRunner()
    commands = Commands(runner)
    runner.command('help', commands.help)
    runner.command('repeat', commands.repeat)
    runner.command('source', commands.source)
    runner.command('get', commands.get)
    runner.command('time', commands.time)
    runner.command('exit', commands.exit)
    runner.command('sleep', commands.sleep)
    runner.command('play', commands.play)
    runner.command('stop', commands.stop)

    return Console(runner).run_in_main(fd)


if __name__ == '__main__':
    global hostname
    parser = argparse.ArgumentParser(prog='osp_cli')
    parser.add_argument('-f', '--file', help='Filename to source', type=open)
    parser.add_argument('-n', '--hostname', help='Hostname', default="localhost")
    args = parser.parse_args()
    hostname = args.hostname
    sys.exit(main(args.file))
