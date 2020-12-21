#!/usr/bin/env python3
import json
import requests

URL = "http://localhost:5000/api/researcher/filenames"
response = requests.get(URL)
for item in response.json():
    print(item)
