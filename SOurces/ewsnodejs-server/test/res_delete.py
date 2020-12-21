#!/usr/bin/env python3
import json
import requests

headers = {"Content-Type": "application/json"}
send_data = {"filename": "XYZZY"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/researcher/delete"
response = requests.post(URL, headers=headers, data=send_data)
print(response.status_code)
