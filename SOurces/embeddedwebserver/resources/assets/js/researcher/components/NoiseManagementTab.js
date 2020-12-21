import React, { Component } from 'react';

export class NoiseManagementTab extends Component {
  transmit() {
    const { noiseManagement, spectralSubtractionParameter } = this.props;

    fetch('/api/noise', {
      body: JSON.stringify({
        noise_estimation_type: noiseManagement,
        spectral_subtraction_param: spectralSubtractionParameter
      }), // must match 'Content-Type' header
      cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
      credentials: 'same-origin', // include, same-origin, *omit
      headers: {
        'user-agent': 'Mozilla/4.0 MDN Example',
        'content-type': 'application/json'
      },
      method: 'POST', // *GET, POST, PUT, DELETE, etc.
      mode: 'no-cors', // no-cors, cors, *same-origin
      redirect: 'follow', // manual, *follow, error
      referrer: 'no-referrer' // *client, no-referrer
    });
  }

  render() {
    const {
      noiseManagement,
      spectralSubtractionParameter,
      handleNoiseManagementChange,
      handleSpectralSubtractionParameterChange
    } = this.props;

    return (
      <React.Fragment>
        <div className="my-3">
          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              checked={noiseManagement === 1}
              onChange={() => handleNoiseManagementChange(1)}
            />
            <label className="form-check-label" onClick={() => handleNoiseManagementChange(1)}>
              Arslan power averaging​ procedure
            </label>
          </div>
          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              checked={noiseManagement === 2}
              onChange={() => handleNoiseManagementChange(2)}
            />
            <label className="form-check-label" onClick={() => handleNoiseManagementChange(2)}>
              Hirsch and Ehrlicher weighted noise averaging procedure
            </label>
          </div>
          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              checked={noiseManagement === 3}
              onChange={() => handleNoiseManagementChange(3)}
            />
            <label className="form-check-label" onClick={() => handleNoiseManagementChange(3)}>
              Cohen and Berdugo MCRA Procedure
            </label>
          </div>
          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              checked={noiseManagement === 0}
              onChange={() => handleNoiseManagementChange(0)}
            />
            <label className="form-check-label" onClick={() => handleNoiseManagementChange(0)}>
              None
            </label>
          </div>
        </div>

        <div className="my-3">
          <label>Spectral Subtraction Parameter (0 to 1)</label>
          <input
            value={spectralSubtractionParameter}
            onChange={e => handleSpectralSubtractionParameterChange(e)}
            type="number"
            className="form-control"
            style={{ width: 'auto' }}
          />
        </div>

        <button className="btn btn-outline-primary my-3" onClick={() => this.transmit()}>
          Transmit
        </button>
      </React.Fragment>
    );
  }
}
