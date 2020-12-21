
# [Open Speech Platform - Server API](../api.md)

## POST /listener/updateParameters

Update the preferred parameters for the user.

    This would be a good place to explain what "preferred parameters" are,
    what the format is, and what apps/demos use them.
    
### HTTP request headers

- **Content-Type**: application/json

### Parameters
Name | Type | Description | Notes
--- | --- | --- | ---
**listenerID** | String | listener name
**parameters** | String | preferred parameters

### Return type

None

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Database error


### Examples

```python
prefpar = "These are the preferred parameters"
headers = {"Content-Type": "application/json"}
send_data = {"listenerID": "martin", "parameters": prefpar}
send_data = json.dumps(send_data)

URL = "http://localhost:5000/api/listener/updateParameters"
response = requests.post(URL, headers=headers, data=send_data)
print("Response Code:", response.status_code)
```

