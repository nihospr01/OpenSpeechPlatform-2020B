
# [Open Speech Platform - Server API](../api.md)

## POST /param/setparam

Sets RT-MHA parameters

    The data is passed along to the RT-MHA (osp)
    process.  Its content depends on the algorithms currently defined in it.

### HTTP request headers

- **Content-Type**: application/json

### Parameters
Name | Type | Description | Notes
--- | --- | --- | ---
**method** | String | set
**data** | String | JSON-encoded parameters for RT-MHA

### Return type

null (empty response body)

### Responses

Code | Meaning
--- | ---
200 | Parameters Successfully Transmited to MHA
500 | Setting Params Failed. Please make sure MHA is running

    Successfully forwarding the parameters to the osp process (MHA) does not
    mean the parameters were interpreted correctly.

### Examples

```
> curl --header "Content-Type: application/json"   --request POST   --data '{"method": "set", "data": {"left": {"audio_filename": "/opt/ews-backend/src/utils/audio/tomsdiner.wav", "audio_reset": 0, "audio_repeat": 0, "audio_play": 1, "alpha":
1}, "right": {"audio_filename": "/opt/ews-backend/src/utils/audio/tomsdiner.wav", "audio_reset": 0, "audio_repeat": 0, "audio_play": 1, "alpha": 1}}}'   http://localhost:5000/api/param/setparam
```


---
