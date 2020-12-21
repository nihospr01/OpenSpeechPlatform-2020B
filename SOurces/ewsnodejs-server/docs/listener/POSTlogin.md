
# [Open Speech Platform - Server API](../api.md)

## POST /listener/login

Gets login information for a listener

### HTTP request headers

- **Content-Type**: application/json

### Parameters
Name | Type | Description | Notes
--- | --- | --- | ---
**listenerID** | String | listener name
**password** | String | password

### Return type

Name | Type | Description
--- | --- | ---
**id** | Integer | unique id
**listenerID** | String | listener name
**researcher** | String | researcher name
**password** | String | password (unencrypted)
**userLog** | JSON | 
**historyDone** | Bool |
**PTADone** | Bool |
**assessmentDone** | Bool |
**AFCDone** | Bool |
**leftEarIsWorse** | Bool |
**preferredParameters** | |
**createdAt** | String | time
**updatedAt** | String | time

### Example Response
```json
{
    "id": 1,
    "listenerID": "martin",
    "researcher": "bob",
    "password": "foo",
    "userLog": "{\"history\":{\"logOn\":\"9/10/2020, 3:43:54 PM\",\"medicalHistory\":{\"leftEar\":\"Normal Hearing\",\"rightEar\":\"Normal Hearing\"},\"illnesses\":{},\"ageSpeak\":\"0 - 2 years old.\",\"highestEdu\":\"Post-Graduate/Master\u2019s level education.\",\"illnessesFam\":{}}}",
    "historyDone": true,
    "PTADone": false,
    "assessmentDone": false,
    "AFCDone": false,
    "leftEarIsWorse": true,
    "preferredParameters": null,
    "createdAt": "2020-09-10T19:33:39.957Z",
    "updatedAt": "2020-09-10T19:43:54.603Z"
}
```

### Responses

Code | Meaning
--- | ---
200 | Success
401 | Password Incorrect
404 | User not found
500 | Database error.


### Examples

```python
headers = {"Content-Type": "application/json"}
send_data = {"listenerID": "martin", "password": "foo"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/listener/login"
response = requests.post(URL, headers=headers, data=send_data)
```
---
