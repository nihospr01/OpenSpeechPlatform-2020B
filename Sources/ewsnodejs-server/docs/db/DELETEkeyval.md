
# [Open Speech Platform - Server API](../api.md)

## DELETE /db/:table

Delete a key-value entry for a given table

### HTTP request headers

**Content-Type**: application/json

### Path Parameters

Name | Type | Description
--- | --- | ---
**table** | String | unique table name

### JSON Parameters

Name | Type | Description
--- | --- | ---
**key** | String | unique key

### Return type

None

### Responses

Code | Meaning
--- | ---
200 | Success
404 | Key or Table not found
500 | Error


### Examples

```python
URL = "http://localhost:5000/api/db/test_table"
headers = {"Content-Type": "application/json"}
body = json.dumps({"key": "mykeyname"})
res = requests.delete(URL, headers=headers, data=body)
assert res.status_code == 200
assert res.text == ""
```

