
# [Open Speech Platform - Server API](../api.md)

## GET /researcher

Gets the list of researchers.

### Return type

Array of researcher names.

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Database error.


### Examples

```
> curl http://localhost:5000/api/researcher/
["martin","hari"]
```


---

