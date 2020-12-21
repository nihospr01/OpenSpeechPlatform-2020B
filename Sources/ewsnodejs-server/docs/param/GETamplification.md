
# [Open Speech Platform - Server API](../api.md)

## GET /param/amplification

Gets all amplification profiles from the database

### Return type

JSON array of profiles

### Responses

Code | Meaning
--- | ---
200 | Profiles successfully retreived
500 | Database error.


### Examples

```
> curl --request GET http://localhost:5000/api/param/amplification

[{"id":1,"profileName":"Martin_profile1","bandNumber":6,"parameters":"{\"left\"...
```


```python
import json
import requests

URL = "http://localhost:5000/api/param/amplification"

response = requests.get(URL)
if response.status_code == 200:
    for profile in response.json():
        print(profile['id'], profile['profileName'], profile['bandNumber'])
else:
    print("Response Code:", response.status_code)

```

---
