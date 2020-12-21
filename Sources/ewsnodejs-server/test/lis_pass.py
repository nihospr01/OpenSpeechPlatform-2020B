#!/usr/bin/env python3
import json
import requests

headers = {"Content-Type": "application/json"}
send_data = {"listenerID": "martin", "newPassword": "foo"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/listener/password"
response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)
