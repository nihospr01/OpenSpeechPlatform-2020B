
# [Open Speech Platform - Server API](../api.md)

## GET /researcher/UserAssessmentConfig

Returns the contents of the UserAssessmentConfig file.

    Currently returns the contents of src/utils/audio/UserAssessmentConfig.json

### Return type

JSON String

### Responses

Code | Meaning
--- | ---
200 | Success
404 | Not Found


### Examples

```
> curl http://localhost:5000/api/researcher/UserAssessmentConfig
{
    "left": [
        "test.wav"
    ],
    "right": [
        "test.wav"
    ]
}
```

---
