#!/usr/bin/env python3
from time import time
import requests
import sys
import json
import urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

# Get Auth Token
headers = {
    'Content-Type': 'application/json',
    'cache-control': 'no-cache',
}

adata = {
    "audio_filename":"/opt2/OSP/ewsnodejs-server/src/utils/audio/tomsdiner.wav",
    "audio_reset":0,
    "audio_repeat":0,
    "audio_play":1,
    "alpha":1
}

adata2 = {
    "audio_filename":"/opt/ews-backend/src/utils/audio/Ava_Luna_-_02_-_Cement_Lunch.wav",
    "audio_reset":0,
    "audio_repeat":0,
    "audio_play":1,
    "alpha":1
}


send_data = {
    'method': 'set',
    'data': {
        'left': adata,
        'right': adata
    }
}

send_data = json.dumps(send_data)
print(send_data)

# send_data = "{\"method\": \"set\", \"data\": {\"left\": {\"audio_filename\": \"/../../../opt/ews-backend/src/utils/audio/tomsdiner.wav\", \"audio_reset\": 0, \"audio_repeat\": 0, \"audio_play\": 1, \"alpha\": 1},\"right\": {\"audio_filename\": \"/../../../opt/ews-backend/src/utils/audio/tomsdiner.wav\",\"audio_reset\": 0,\"audio_repeat\": 0,\"audio_play\": 1,\"alpha\": 1}}}"


URL='http://localhost:5000/api/param/setparam'

starttime = time()
response = requests.post(URL, headers=headers, data=send_data)
runtime = time() - starttime

print("Response Code:", response.status_code)

if response.status_code != 200:
    print(f"ERROR: Response code: {response.status_code}")
    sys.exit(1)

print(f"Got response in {runtime:.4f} seconds")
# print('-'*40)
# rj = response.json()
# print(rj['access_token'])
# print('-'*40)
print(response)