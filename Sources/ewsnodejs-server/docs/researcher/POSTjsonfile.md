
# [Open Speech Platform - Server API](../api.md)

## POST /researcher/jsonfile

Gets the contents of a json file.  Used to read
transcript json files, but can read any json file on the system!

**NEEDS FIXED!!!**

    * Huge security problem. Reads any json file on the system.
    * Crashes the server if the file does not exist or is not json.
    * Requires absolute path!
    * Should use something other than "data" for a parameter name.
  
### HTTP request headers

- **Content-Type**: application/json

### Parameters
Name | Type | Description | Notes
--- | --- | --- | ---
**data** | String | path

### Return type

A string containing the contents of the file.

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Error


### Examples

```python
import json
import requests

path = requests.get("http://localhost:5000/api/researcher/audiopath").text

path = path + "stims2/stim2.json"
headers = {"Content-Type": "application/json"}
send_data = {"data": path}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/researcher/jsonfile"
response = requests.post(URL, headers=headers, data=send_data)
print(response.json())

```

---
