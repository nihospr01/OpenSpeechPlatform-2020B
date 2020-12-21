#!/usr/bin/env python3
import json
import requests

headers = {"Content-Type": "application/json"}

newLog = {
          "newLogData": {
              "Test1Done":  True,
              "Test2Done":  True
          }
        }

send_data = {
    "listenerID": "martin", 
    "newLog": newLog,
    "flag": "AFCDone",
    "flagValue": True 
    }
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/listener/addLog"
response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)

print("Verifying...")

headers = {"Content-Type": "application/json"}
send_data = {"listenerID": "martin", "password": "foo"}
send_data = json.dumps(send_data)
URL = "http://localhost:5000/api/listener/login"
r = requests.post(URL, headers=headers, data=send_data)
if r.ok:
    resp = r.json()
    print(json.dumps(resp, sort_keys=True, indent=4))
    print("\nUserLog:")
    userlog = json.loads( resp['userLog'])
    print(json.dumps(userlog, sort_keys=True, indent=4))
else:
    print("ERROR:", r.status_code)
