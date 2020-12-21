
# [Open Speech Platform - Server API](../api.md)

## GET /db/:table

Get all keys, or the corresponding value for a key in the given table

### Parameters

If no json parameters are included, a list containing all keys will be returned.

Name | Type | Description
--- | --- | ---
**key** | String | unique key

### Return type

If a key was specified:

    Value as blob type.

If no key was specified:

    a json list of keys

### Responses

Code | Meaning
--- | ---
200 | Success
404 | table not found
500 | key not found


### Examples

```python
URL = "http://localhost:5000/api/db/test_table"
headers = {"Content-Type": "application/json"}
body = json.dumps({"key": "mykeyname"})
res = requests.get(URL, headers=headers, data=body)
assert res.text == "value_of_mykeyname"
```

```python
URL = "http://localhost:5000/api/db/test_table"
res = requests.get(URL)
# res.json() is list of keys
```