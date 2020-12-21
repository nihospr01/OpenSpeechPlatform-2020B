#!/usr/bin/env python3
import json
import requests

URL = "http://localhost:5000/api/param/amplification"

response = requests.get(URL)
if response.status_code == 200:
    for profile in response.json():
        print(profile['id'], profile['profileName'], profile['bandNumber'])
else:
    print("Response Code:", response.status_code)
