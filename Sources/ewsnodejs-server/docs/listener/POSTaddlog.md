
# [Open Speech Platform - Server API](../api.md)

## POST /listener/addlog

Add or modify the JSON userLog

    This allows addition and modification of the User JSON database.
    It can set the value of existing flags, but does not appear to be able to create
    them.  It can modify or add to the JSON userLog.

### HTTP request headers

- **Content-Type**: application/json

### Parameters
Name | Type | Description | Notes
--- | --- | --- | ---
**listenerID** | String | listener name
**newLog** | JSON | object to store
**flag** | String | existing flag name
**flagValue** | - | new value for **flag**

### Return type

None

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Error


### Examples

```python
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
```

The program above outputs:
```
Response Code: 200
Verifying...
{
    "AFCDone": true,
    "PTADone": false,
    "assessmentDone": false,
    "createdAt": "2020-09-16T17:46:21.185Z",
    "historyDone": true,
    "id": 9,
    "leftEarIsWorse": true,
    "listenerID": "martin",
    "password": "foo",
    "preferredParameters": null,
    "researcher": "martin",
    "updatedAt": "2020-09-16T19:15:38.640Z",
    "userLog": "{\"history\":{\"logOn\":\"9/16/2020, 1:49:27 PM\",\"medicalHistory\":{\"leftEar\":\"Normal Hearing\",\"rightEar\":\"Normal Hearing\"},\"illnesses\":{},\"ageSpeak\":\"0 - 2 years old.\",\"highestEdu\":\"Post-Graduate/Master\u2019s level education.\",\"illnessesFam\":{}},\"newLogData\":{\"Test1Done\":true,\"Test2Done\":true}}"
}

UserLog:
{
    "history": {
        "ageSpeak": "0 - 2 years old.",
        "highestEdu": "Post-Graduate/Master\u2019s level education.",
        "illnesses": {},
        "illnessesFam": {},
        "logOn": "9/16/2020, 1:49:27 PM",
        "medicalHistory": {
            "leftEar": "Normal Hearing",
            "rightEar": "Normal Hearing"
        }
    },
    "newLogData": {
        "Test1Done": true,
        "Test2Done": true
    }
}
```
