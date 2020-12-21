
# [Open Speech Platform - Server API](../api.md)

## POST /listener/delete

Deletes a listener

### Parameters


Name | Type | Description
--- | --- | ---
**listenerID** | String | file name

### Return type

None

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Error


### Examples

```
> curl --header "Content-Type: application/json" --data '{"listenerID": "bob"}' --request POST http://localhost:5000/api/listener/delete
```

```python
import json
import requests

headers = {"Content-Type": "application/json"}
send_data = {"listenerID": "bob"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/listener/delete"
response = requests.post(URL, headers=headers, data=send_data)
print(response.status_code)
```

