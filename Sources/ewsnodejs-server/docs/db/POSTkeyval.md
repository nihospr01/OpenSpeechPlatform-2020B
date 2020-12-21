
# [Open Speech Platform - Server API](../api.md)

## POST /db/:table

Create or Update a key-value entry.

### HTTP request headers

**Content-Type**: application/json

### Path Parameters

Name | Type | Description
--- | --- | ---
**table** | String | unique table name

### JSON Parameters

Name | Type | Description | Notes
--- | --- | --- | ---
**key** | String | unique key
**value** | Blob | corresponding data

### Responses

Code | Meaning
--- | ---
200 | Success
404 | table not found
500 | Error

### Examples

```python
URL = "http://localhost:5000/api/db/test_table"
headers = {"Content-Type": "application/json"}
body = json.dumps({"key": "my_key", "value": "value1"})
res = requests.post(URL, headers=headers, data=body)
assert res.status_code == 200
assert res.text == ""
```
