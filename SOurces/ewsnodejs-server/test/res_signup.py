import json
import requests

headers = {"Content-Type": "application/json"}
send_data = {"researcherID": "martinX", "password": "foo"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/researcher/signup"
response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)
print(response.json())

