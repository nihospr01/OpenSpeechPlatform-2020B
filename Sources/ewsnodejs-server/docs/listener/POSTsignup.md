
# [Open Speech Platform - Server API](../api.md)

## POST /listener/signup

Create a new listener account.

### HTTP request headers

- **Content-Type**: application/json

### Parameters
Name | Type | Description | Notes
--- | --- | --- | ---
**listenerID** | String | listener name
**researcher** | String | researcher name
**password** | String | password for listener

### Return type

Name | Type | Description
--- | --- | ---
**id** | Integer | unique id
**listenerID** | String | listener name
**researcher** | String | researcher name
**password** | String | password (unencrypted)
**historyDone** | Bool
**PTADone** | Bool
**assessmentDone** | Bool
**AFCDone**
**leftEarIsWorse**
**createdAt** | String | time
**updatedAt** | String | time

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Listener already exists or Database error


### Examples

```python
import json
import requests

headers = {"Content-Type": "application/json"}
send_data = {"listenerID": "PeterParker", "researcher": "martin", "password": "foo"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/listener/signup"
response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)
print(response.json())
```
