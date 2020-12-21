#!/usr/bin/env python3
import json
import requests

headers = {"Content-Type": "application/json"}
send_data = {"researcherID": "martin2", "password": "foo2"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/researcher/login"
response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)
print(response.json())
if response.status_code != 200:
    print(response)
