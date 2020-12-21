#!/usr/bin/env python3
import requests

# send_data = {'file': ('FileName', 'Contents of the file')}
send_data = {'file': open('XYZZY', 'rb')}
URL = "http://localhost:5000/api/researcher/upload"
response = requests.post(URL, files=send_data)
print(response.status_code)
