
# [Open Speech Platform - Server API](../api.md)

## GET /researcher/filenames

Gets the list of audio files and transcripts

### Return type

JSON array. Each entry contains
Name | Type | Description
--- | --- | ---
**value** | String | filename
**transcript** | String | transcipt filename

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Database error.


### Examples

```python
import json
import requests

URL = "http://localhost:5000/api/researcher/filenames"
response = requests.get(URL)
for item in response.json():
    print(item)
```
```
> curl http://localhost:5000/api/researcher/filenames
[{"value":"Ava_Luna_-_02_-_Cement_Lunch.wav","transcript":null},{"value":"Mild_Wild_-_02_-_Say_Goodnight.wav","transcript":null},{"value":"stims0/stim0.wav","transcript":"stims0/stim0.json"},{"value":"stims1/stim1.wav","transcript":"stims1/stim1.json"},{"value":"stims2/stim2.wav","transcript":"stims2/stim2.json"},{"value":"stims3/stim3.wav","transcript":"stims3/stim3.json"},{"value":"stims4/stim4.wav","transcript":"stims4/stim4.json"},{"value":"test.wav","transcript":null},{"value":"tomsdiner.wav","transcript":null},{"value":"tomsdiner_1.wav","transcript":null}]
```

---
