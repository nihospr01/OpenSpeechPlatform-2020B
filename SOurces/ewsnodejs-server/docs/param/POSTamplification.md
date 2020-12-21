
# [Open Speech Platform - Server API](../api.md)

## POST /param/amplification

Saves current amplification parameters to the database as a profile

    Parameters are sent and saved as a json blob.

### HTTP request headers

- **Content-Type**: application/json

### Parameters
Name | Type | Description | Notes
--- | --- | --- | ---
**profileName** | String | name of the profile
**bandNumber** | Integer | 6 or 10
**parameters** | String | JSON-encoded parameters for RT-MHA

### Return type

Name | Type | Description
--- | --- | --- | ---
**id** | Integer | unique id
**profileName** | String | name of the profile
**bandNumber** | Integer | 6 or 10
**parameters** | String | JSON-encoded parameters for
**createdAt** | String | time
**updatedAt** | String | time

### Responses

Code | Meaning
--- | ---
200 | Parameters successfully saved
500 | Database error.  Profile already exists.


### Examples

This works, but the parameters are not correct.
```
> curl --header "Content-Type: application/json"   --request POST   --data '{"profileName": "profile3", "bandNumber": 6, "parameters": "parameters here"}'  http://localhost:5000/api/param/amplification
```

Here's a full example with all current parameters:
```python
import json
import requests

headers = {"Content-Type": "application/json"}
params = {
    "left": {
        "afc":0,
        "freping":0,
        "cr":[1,1,1,1,1,1],
        "g50":[0,0,0,0,0,0],
        "g65":[0,0,0,0,0,0],
        "g80":[0,0,0,0,0,0],
        "knee_low":[45,45,45,45,45,45],
        "mpo_band":[120,120,120,120,120,120],
        "attack":[5,5,5,5,5,5],
        "release":[20,20,20,20,20,20],
        "global_mpo":120,
        "freping_alpha":[0,0,0,0,0,0]},
    "right":{
        "afc":0,
        "freping":0,
        "cr":[1,1,1,1,1,1],
        "g50":[0,0,0,0,0,0],
        "g65":[0,0,0,0,0,0],
        "g80":[0,0,0,0,0,0],
        "knee_low":[45,45,45,45,45,45],
        "mpo_band":[120,120,120,120,120,120],
        "attack":[5,5,5,5,5,5],
        "release":[20,20,20,20,20,20],
        "global_mpo":120,
        "freping_alpha":[0,0,0,0,0,0]
    }
}

params = str(params).replace("'", '\"')
send_data = {"profileName": "profile10", "bandNumber": 6, "parameters": params}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/param/amplification"

response = requests.post(URL, headers=headers, data=send_data)
```

