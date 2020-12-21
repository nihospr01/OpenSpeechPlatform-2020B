
# [Open Speech Platform - Server API](../api.md)

## GET /researcher/4AFCConfig

Returns the contents of the 4AFCConfig file.

    Currently returns the contents of src/utils/audio/4AFCConfig.json

### Return type

JSON String

### Responses

Code | Meaning
--- | ---
200 | Success
404 | Not Found


### Examples

```
> curl http://localhost:5000/api/researcher/4AFCConfig
```
