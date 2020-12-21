
# [Open Speech Platform - Server API](../api.md)

## POST /listener/password

Changes the listener password

### HTTP request headers

- **Content-Type**: application/json

### Parameters
Name | Type | Description | Notes
--- | --- | --- | ---
**listenerID** | String | listener name
**newPassword** | String | new password for listener

### Return type

None

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Error

    In testing, this API call always returns 200, even if the user does not exist.

### Examples

```python
import json
import requests

headers = {"Content-Type": "application/json"}
send_data = {"listenerID": "martin", "newPassword": "foo"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/listener/password"
response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)
```

```
> ccat add  url --header "Content-Type: application/json" --data '{"listenerID": "martin", "newPassword": "foo"}' http://localhost:5000/api/listener/password
```
