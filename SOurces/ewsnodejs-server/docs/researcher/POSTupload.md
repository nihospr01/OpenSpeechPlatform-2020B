
# [Open Speech Platform - Server API](../api.md)

## POST /researcher/upload

Uploads a file.

    Currently uploads a single file to src/utils/audio.

### Parameters

multipart/form-data

Name | Description
--- | ---
**file** | file contents

### Return type

None

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Error


### Examples

```
> curl -F file=@mylocalfile.wav --request POST http://localhost:5000/api/researcher/upload
```

```python
import requests

# send_data = {'file': ('FileName', 'Contents of the file')}
send_data = {'file': open('FileName', 'rb')}
URL = "http://localhost:5000/api/researcher/upload"
response = requests.post(URL, files=send_data)
print(response.status_code)
```

---
