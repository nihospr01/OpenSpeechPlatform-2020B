#!/usr/bin/env python3
import json
import requests

path = requests.get("http://localhost:5000/api/researcher/audiopath").text

path = path + "stims2/stim2.json"
headers = {"Content-Type": "application/json"}
send_data = {"data": path}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/researcher/jsonfile"
response = requests.post(URL, headers=headers, data=send_data)
print(response.json())
