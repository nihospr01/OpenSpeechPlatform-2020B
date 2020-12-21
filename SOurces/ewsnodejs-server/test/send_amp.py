#!/usr/bin/env python3
import json
import requests

headers = {"Content-Type": "application/json"}
params = {
    "left": {
        "afc":0,
        "freping":0,
        "cr":[1,1,1,1,1,1],
        "g50":[0,0,0,0,0,0],
        "g65":[0,0,0,0,0,0],
        "g80":[0,0,0,0,0,0],
        "knee_low":[45,45,45,45,45,45],
        "mpo_band":[120,120,120,120,120,120],
        "attack":[5,5,5,5,5,5],
        "release":[20,20,20,20,20,20],
        "global_mpo":120,
        "freping_alpha":[0,0,0,0,0,0]},
    "right":{
        "afc":0,
        "freping":0,
        "cr":[1,1,1,1,1,1],
        "g50":[0,0,0,0,0,0],
        "g65":[0,0,0,0,0,0],
        "g80":[0,0,0,0,0,0],
        "knee_low":[45,45,45,45,45,45],
        "mpo_band":[120,120,120,120,120,120],
        "attack":[5,5,5,5,5,5],
        "release":[20,20,20,20,20,20],
        "global_mpo":120,
        "freping_alpha":[0,0,0,0,0,0]
    }
}

params = str(params).replace("'", '\"')
send_data = {"profileName": "profile11", "bandNumber": 6, "parameters": params}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/param/amplification"

response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)
if response.status_code != 200:
    print(response)
else:
    print(response.json())