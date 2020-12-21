
# [Open Speech Platform - Server API](../api.md)

## POST /db/table/create/:table

Create a key-value table

### HTTP request headers

None

### Path Parameters

Name | Type | Description
--- | --- | ---
**table** | String | unique table name

### Return type

None

### Responses

Code | Meaning
--- | ---
200 | Success
404 | table with that name already exists
500 | Error


### Examples

```python
URL = "http://localhost:5000/api/db/table/create/table1"
res = requests.post(URL)
assert res.status_code == 200
assert res.text == ""
```
