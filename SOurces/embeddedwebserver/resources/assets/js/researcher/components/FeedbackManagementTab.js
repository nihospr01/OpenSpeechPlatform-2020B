import React, { Component } from 'react';

export class FeedbackManagementTab extends Component {
  transmit() {
    const { feedbackAlgorithmType, mu, rho } = this.props;

    fetch('/api/feedback', {
      body: JSON.stringify({
        feedback_algorithm_type: feedbackAlgorithmType,
        mu: mu,
        rho: rho
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
      feedbackAlgorithmType,
      mu,
      rho,
      handleFeedbackAlgorithmTypeChange,
      handleMuChange,
      handleRhoChange
    } = this.props;

    return (
      <React.Fragment>
        <div className="my-3">
          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              checked={feedbackAlgorithmType === 0}
              onChange={() => handleFeedbackAlgorithmTypeChange(0)}
            />
            <label className="form-check-label" onClick={() => handleFeedbackAlgorithmTypeChange(0)}>
              FxLMS
            </label>
          </div>

          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              checked={feedbackAlgorithmType === 1}
              onChange={() => handleFeedbackAlgorithmTypeChange(1)}
            />
            <label className="form-check-label" onClick={() => handleFeedbackAlgorithmTypeChange(1)}>
              P​NLMS
            </label>
          </div>

          <div className="form-check">
            <input
              className="form-check-input"
              type="radio"
              checked={feedbackAlgorithmType === 2}
              onChange={() => handleFeedbackAlgorithmTypeChange(2)}
            />
            <label className="form-check-label" onClick={() => handleFeedbackAlgorithmTypeChange(2)}>
              SLMS
            </label>
          </div>
        </div>

        {feedbackAlgorithmType === 2 && (
          <div>
            For SLMS please refer to "Lee, Ching-Hua, Bhaskar D. Rao, and Harinath Garudadri. Sparsity promoting LMS for
            adaptive feedback cancellation. In Signal Processing Conference (EUSIPCO), 2017 25th European, pp. 226-230.
            IEEE, 2017."
          </div>
        )}

        <div className="my-3">
          <label>
            MU - Step size parameter: a positive constant. A higher value results in faster tracking at the cost of
            higher steady state error{' '}
          </label>
          <input
            value={mu}
            onChange={e => handleMuChange(e)}
            type="number"
            className="form-control my-1"
            style={{ width: 'auto' }}
          />

          <label>RHO - Forgetting factor for power estimation: a constant between 0 and 1. (> 0.9 is suggested)</label>
          <input
            value={rho}
            onChange={e => handleRhoChange(e)}
            type="number"
            className="form-control my-1"
            style={{ width: 'auto' }}
          />
        </div>

        <button
          className="btn btn-outline-primary my-3"
          onClick={() => this.transmit()}
          disabled={feedbackAlgorithmType === 2 ? 'disabled' : ''}
        >
          Transmit
        </button>
      </React.Fragment>
    );
  }
}
