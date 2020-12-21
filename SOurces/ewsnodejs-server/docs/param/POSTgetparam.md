
# [Open Speech Platform - Server API](../api.md)

## POST /param/getparam

Gets RT-MHA parameters

    Gets the current RT-MHA (osp) parameters

### Return type

Json-encoded parameters

### Responses

Code | Meaning
--- | ---
200 | Parameters successfully received from MHA
500 | Getting params failed. Please make sure MHA is running

### Examples

```
> curl --request POST http://localhost:5000/api/param/getparam
{"left":{"en_ha":1,"rear_mics":0,"gain":-20.0,"g50":[0.0,0.0,0.0,0.0,0.0,0.0],"g80":[0.0,0.0,0.0,0.0,0.0,0.0],"knee_low":[45.0,45.0,45.0,45.0,45.0,45.0],"mpo_band":[120.0,120.0,120.0,120.0,120.0,120.0],"attack":[5.0,5.0,5.0,5.0,5.0,5.0],"release":[20.0,20.0,20.0,20.0,20.0,20.0],"global_mpo":120.0,"noise_estimation_type":0,"spectral_type":0,"spectral_subtraction":0.0,"freping":0,"freping_alpha":[0.0,0.0,0.0,0.0,0.0,0.0],"afc":1,"afc_reset":0,"afc_type":3,"afc_delay":0.0,"afc_mu":0.004999999888241291,"afc_rho":0.8999999761581421,"afc_power_estimate":0.0,"bf":0,"bf_type":3,"bf_mu":0.009999999776482582,"bf_rho":0.9850000143051148,"bf_delta":9.999999974752428e-7,"bf_c":0.0010000000474974514,"bf_power_estimate":0.0,"bf_p":1.2999999523162842,"bf_alpha":0.0,"bf_beta":150.0,"bf_fir_length":319,"bf_delay_len":160,"bf_nc_on_off":0,"bf_amc_on_off":0,"nc_thr":1.0,"amc_thr":2.0,"amc_forgetting_factor":0.800000011920929,"alpha":1.0,"audio_filename":"","audio_reset":0,"audio_play":0,"audio_repeat":0,"audio_recordfile":"sample.wav","record_start":0,"record_stop":0,"record_length":5.0},"right":{"en_ha":1,"rear_mics":0,"gain":-20.0,"g50":[0.0,0.0,0.0,0.0,0.0,0.0],"g80":[0.0,0.0,0.0,0.0,0.0,0.0],"knee_low":[45.0,45.0,45.0,45.0,45.0,45.0],"mpo_band":[120.0,120.0,120.0,120.0,120.0,120.0],"attack":[5.0,5.0,5.0,5.0,5.0,5.0],"release":[20.0,20.0,20.0,20.0,20.0,20.0],"global_mpo":120.0,"noise_estimation_type":0,"spectral_type":0,"spectral_subtraction":0.0,"freping":0,"freping_alpha":[0.0,0.0,0.0,0.0,0.0,0.0],"afc":1,"afc_reset":0,"afc_type":3,"afc_delay":0.0,"afc_mu":0.004999999888241291,"afc_rho":0.8999999761581421,"afc_power_estimate":0.0,"bf":0,"bf_type":3,"bf_mu":0.009999999776482582,"bf_rho":0.9850000143051148,"bf_delta":9.999999974752428e-7,"bf_c":0.0010000000474974514,"bf_power_estimate":0.0,"bf_p":1.2999999523162842,"bf_alpha":0.0,"bf_beta":150.0,"bf_fir_length":319,"bf_delay_len":160,"bf_nc_on_off":0,"bf_amc_on_off":0,"nc_thr":1.0,"amc_thr":2.0,"amc_forgetting_factor":0.800000011920929,"alpha":1.0,"audio_filename":"","audio_reset":0,"audio_play":0,"audio_repeat":0,"audio_recordfile":"sample.wav","record_start":0,"record_stop":0,"record_length":5.0}}
```

``` python
#!/usr/bin/env python3
import requests
URL='http://localhost:5000/api/param/getparam'
response = requests.post(URL)
print("Response Code:", response.status_code)
print(response.json())
```

```
> get_param.py
Response Code: 200
{'left': {'en_ha': 1, 'rear_mics': 0, 'gain': -20.0, 'g50': [0.0, 0.0, 0.0, 0.0, 0.0, 0.0], 'g80': [0.0, 0.0, 0.0, 0.0, 0.0, 0.0], 'knee_low': [45.0, 45.0, 45.0, 45.0, 45.0, 45.0], 'mpo_band': [120.0, 120.0, 120.0, 120.0, 120.0, 120.0], 'attack': [5.0, 5.0, 5.0, 5.0, 5.0, 5.0], 'release': [20.0, 20.0, 20.0, 20.0, 20.0, 20.0], 'global_mpo': 120.0, 'noise_estimation_type': 0, 'spectral_type': 0, 'spectral_subtraction': 0.0, 'freping': 0, 'freping_alpha': [0.0, 0.0, 0.0, 0.0, 0.0, 0.0], 'afc': 1, 'afc_reset': 0, 'afc_type': 3, 'afc_delay': 0.0, 'afc_mu': 0.004999999888241291, 'afc_rho': 0.8999999761581421, 'afc_power_estimate': 0.0, 'bf': 0, 'bf_type': 3, 'bf_mu': 0.009999999776482582, 'bf_rho': 0.9850000143051147, 'bf_delta': 9.999999974752427e-07, 'bf_c': 0.0010000000474974513, 'bf_power_estimate': 0.0, 'bf_p': 1.2999999523162842, 'bf_alpha': 0.0, 'bf_beta': 150.0, 'bf_fir_length': 319, 'bf_delay_len': 160, 'bf_nc_on_off': 0, 'bf_amc_on_off': 0, 'nc_thr': 1.0, 'amc_thr': 2.0, 'amc_forgetting_factor': 0.800000011920929, 'alpha': 1.0, 'audio_filename': '', 'audio_reset': 0, 'audio_play': 0, 'audio_repeat': 0, 'audio_recordfile': 'sample.wav', 'record_start': 0, 'record_stop': 0, 'record_length': 5.0}, 'right': {'en_ha': 1, 'rear_mics': 0, 'gain': -20.0, 'g50': [0.0, 0.0, 0.0, 0.0, 0.0, 0.0], 'g80': [0.0, 0.0, 0.0, 0.0, 0.0, 0.0], 'knee_low': [45.0, 45.0, 45.0, 45.0, 45.0, 45.0], 'mpo_band': [120.0, 120.0, 120.0, 120.0, 120.0, 120.0], 'attack': [5.0, 5.0, 5.0, 5.0, 5.0, 5.0], 'release': [20.0, 20.0, 20.0, 20.0, 20.0, 20.0], 'global_mpo': 120.0, 'noise_estimation_type': 0, 'spectral_type': 0, 'spectral_subtraction': 0.0, 'freping': 0, 'freping_alpha': [0.0, 0.0, 0.0, 0.0, 0.0, 0.0], 'afc': 1, 'afc_reset': 0, 'afc_type': 3, 'afc_delay': 0.0, 'afc_mu': 0.004999999888241291, 'afc_rho': 0.8999999761581421, 'afc_power_estimate': 0.0, 'bf': 0, 'bf_type': 3, 'bf_mu': 0.009999999776482582, 'bf_rho': 0.9850000143051147, 'bf_delta': 9.999999974752427e-07, 'bf_c': 0.0010000000474974513, 'bf_power_estimate': 0.0, 'bf_p': 1.2999999523162842, 'bf_alpha': 0.0, 'bf_beta': 150.0, 'bf_fir_length': 319, 'bf_delay_len': 160, 'bf_nc_on_off': 0, 'bf_amc_on_off': 0, 'nc_thr': 1.0, 'amc_thr': 2.0, 'amc_forgetting_factor': 0.800000011920929, 'alpha': 1.0, 'audio_filename': '', 'audio_reset': 0, 'audio_play': 0, 'audio_repeat': 0, 'audio_recordfile': 'sample.wav', 'record_start': 0, 'record_stop': 0, 'record_length': 5.0}}
```

---
