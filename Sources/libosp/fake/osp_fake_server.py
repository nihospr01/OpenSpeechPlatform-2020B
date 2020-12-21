#!/usr/bin/env python3

"""
This is a fake osp server using ZeroMQ.
It returns "success" for all set commands
and returns some fake data for all "get"
commands.  If it cannot parse the incoming
message, it sends "FAILED".
"""

import time
import zmq
import json

fake_response = \
"""{
    "left": {
        "afc": 1,
        "afc_delay": 4.6875,
        "afc_mu": 0.004999999888241291,
        "afc_power_estimate": 0.0,
        "afc_reset": 0,
        "afc_rho": 0.8999999761581421,
        "afc_type": 3,
        "alpha": 0.0,
        "amc_forgetting_factor": 0.800000011920929,
        "amc_thr": 2.0,
        "attack": [
            5.0,
            5.0,
            5.0,
            5.0,
            5.0,
            5.0
        ],
        "bf": 0,
        "bf_alpha": 0.0,
        "bf_amc_on_off": 0,
        "bf_beta": 150.0,
        "bf_c": 0.0010000000474974513,
        "bf_delay_len": 160,
        "bf_delta": 9.999999974752427e-07,
        "bf_fir_length": 319,
        "bf_mu": 0.009999999776482582,
        "bf_nc_on_off": 0,
        "bf_p": 1.2999999523162842,
        "bf_power_estimate": 0.0,
        "bf_rho": 0.9850000143051147,
        "bf_type": 3,
        "en_ha": 1,
        "freping": 0,
        "freping_alpha": [
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            0.0
        ],
        "g50": [
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            0.0
        ],
        "g80": [
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            0.0
        ],
        "gain": -20.0,
        "global_mpo": 120.0,
        "knee_low": [
            45.0,
            45.0,
            45.0,
            45.0,
            45.0,
            45.0
        ],
        "mpo_band": [
            120.0,
            120.0,
            120.0,
            120.0,
            120.0,
            120.0
        ],
        "nc_thr": 1.0,
        "noise_estimation_type": 0,
        "rear_mics": 0,
        "release": [
            20.0,
            20.0,
            20.0,
            20.0,
            20.0,
            20.0
        ],
        "spectral_subtraction": 0.0,
        "spectral_type": 0
    },
    "right": {
        "afc": 1,
        "afc_delay": 4.6875,
        "afc_mu": 0.004999999888241291,
        "afc_power_estimate": 0.0,
        "afc_reset": 0,
        "afc_rho": 0.8999999761581421,
        "afc_type": 3,
        "alpha": 0.0,
        "amc_forgetting_factor": 0.800000011920929,
        "amc_thr": 2.0,
        "attack": [
            5.0,
            5.0,
            5.0,
            5.0,
            5.0,
            5.0
        ],
        "bf": 0,
        "bf_alpha": 0.0,
        "bf_amc_on_off": 0,
        "bf_beta": 150.0,
        "bf_c": 0.0010000000474974513,
        "bf_delay_len": 160,
        "bf_delta": 9.999999974752427e-07,
        "bf_fir_length": 319,
        "bf_mu": 0.009999999776482582,
        "bf_nc_on_off": 0,
        "bf_p": 1.2999999523162842,
        "bf_power_estimate": 0.0,
        "bf_rho": 0.9850000143051147,
        "bf_type": 3,
        "en_ha": 1,
        "freping": 0,
        "freping_alpha": [
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            0.0
        ],
        "g50": [
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            0.0
        ],
        "g80": [
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            0.0
        ],
        "gain": -20.0,
        "global_mpo": 120.0,
        "knee_low": [
            45.0,
            45.0,
            45.0,
            45.0,
            45.0,
            45.0
        ],
        "mpo_band": [
            120.0,
            120.0,
            120.0,
            120.0,
            120.0,
            120.0
        ],
        "nc_thr": 1.0,
        "noise_estimation_type": 0,
        "rear_mics": 0,
        "release": [
            20.0,
            20.0,
            20.0,
            20.0,
            20.0,
            20.0
        ],
        "spectral_subtraction": 0.0,
        "spectral_type": 0
    }
}"""


context = zmq.Context()
socket = context.socket(zmq.REP)
socket.bind("tcp://*:8001")

while True:
    #  Wait for next request from client
    message = socket.recv()
    print("Received: %s" % message)

    try:
        j = json.loads(message)
        if j['method'] == "get":
            socket.send(fake_response.encode())
            continue        
        if j['method'] == "set":
            socket.send(b"success")
            continue
        print("Unknown method.")
        socket.send(b"FAILED")
    except:
        print("Does not look like json.")
        socket.send(b"FAILED")
