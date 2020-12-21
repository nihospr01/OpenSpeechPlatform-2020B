
# [Open Speech Platform - Server API](../api.md)

## GET /listener/{researcher}

Gets the list of listeners for a researcher.

### Return type

Array of listener names.  Each one contains:

Name | Type | Description
--- | --- | ---
**id** | Integer | unique id
**listenerID** | String | listener name
**researcher** | String | researcher name
**password** | String | password (unencrypted)
**userLog** | JSON | 
**historyDone** | Bool |
**PTADone** | Bool |
**assessmentDone** | Bool |
**AFCDone** | Bool |
**leftEarIsWorse** | Bool |
**preferredParameters** | |
**createdAt** | String | time
**updatedAt** | String | time

### Responses

Code | Meaning
--- | ---
200 | Success
500 | Error


### Examples

```
> curl http://localhost:5000/api/listener/martin
```


