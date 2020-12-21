
# [Open Speech Platform - Server API](../api.md)

## POST /researcher/delete

Deletes a file

    Currently deletes a single file from src/utils/audio.

### Parameters


Name | Type | Description
--- | --- | ---
**filename** | String | file name

### Return type

None

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Error


### Examples

```
> curl --header "Content-Type: application/json" --data '{"filename": "my_unwanted_file.wav"}' --request POST http://localhost:5000/api/researcher/delete
```

```python
import json
import requests

headers = {"Content-Type": "application/json"}
send_data = {"filename": "my_unwanted_file.wav"}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/researcher/delete"
response = requests.post(URL, headers=headers, data=send_data)
print(response.status_code)
```

---
