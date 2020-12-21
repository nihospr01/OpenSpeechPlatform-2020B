
# [Open Speech Platform - Server API](../api.md)

## POST /researcher/login

Login a Researcher

### HTTP request headers

- **Content-Type**: application/json

### Parameters
Name | Type | Description | Notes
--- | --- | --- | ---
**researcherID** | String | researcher name
**password** | String | password

### Return type

Name | Type | Description
--- | --- | ---
**id** | Integer | unique id
**researcherID** | String | researcher name
**password** | String | password (encrypted)
**createdAt** | String | time
**updatedAt** | String | time

### Responses

Code | Meaning
--- | ---
200 | Success
401 | Password Incorrect
404 | User not found
500 | Database error.


### Examples

```python
import json
import requests

headers = {"Content-Type": "application/json"}
send_data = {"researcherID": "martin", "password": "foo"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/researcher/login"
response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)
print(response.json())
```
---
