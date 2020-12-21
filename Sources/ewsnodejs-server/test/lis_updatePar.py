#!/usr/bin/env python3
import json
import requests

prefpar = "These are the preferred parameters"
headers = {"Content-Type": "application/json"}
send_data = {"listenerID": "martin", "parameters": prefpar}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/listener/updateParameters"
response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)

print("Verifying...")

headers = {"Content-Type": "application/json"}
send_data = {"listenerID": "martin", "password": "foo"}
send_data = json.dumps(send_data)
URL = "http://localhost:5000/api/listener/login"
response = requests.post(URL, headers=headers, data=send_data)
if response.ok:
    p = response.json()['preferredParameters']
    if p == prefpar:
        print("OK")
    else:
        print("FAILED:", p)
else:
    print("Call to listener/login failed")
