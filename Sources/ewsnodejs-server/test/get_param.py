#!/usr/bin/env python3
import requests

URL='http://localhost:5000/api/param/getparam'
response = requests.post(URL)
print("Response Code:", response.status_code)
print(response.json())
