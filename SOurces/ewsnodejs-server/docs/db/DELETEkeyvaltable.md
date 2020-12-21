
# [Open Speech Platform - Server API](../api.md)

## DELETE /db/table/delete/:table

Delete a key-value table

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
500 | table with that name does not exist


### Examples

```python
URL = "http://localhost:5000/api/db/table/delete/table1"
requests.delete(URL)
```
