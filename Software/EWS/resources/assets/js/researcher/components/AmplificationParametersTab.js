import React, { Component } from 'react';
import { FREQUENCIES, ROW_HEADERS } from './constants';

export class AmplificationParametersTab extends Component {
  transmit() {
    const { params, updateOldParams } = this.props;

    fetch('/api/params', {
      body: JSON.stringify({
        noOp: 0,
        afc: 1,
        feedback: 0,
        rear: 0,
        g50: params[1].slice(0, 6),
        g80: params[3].slice(0, 6),
        kneelow: params[4].slice(0, 6),
        mpoLimit: params[5].slice(0, 6),
        attackTime: params[6].slice(0, 6),
        releaseTime: params[7].slice(0, 6)
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
    }).then(function(response) {
      return response.json();
    })
    .then(function(myJson) {
      console.log(JSON.stringify(myJson));
      if(myJson.status == 1){
        updateOldParams();
      }
      else{
        //popup
        alert("Parameters not updated");
      }


    });
  }

  render() {
    const { control, params, oldParams, handleControlChange, handleParamChange, resetParams } = this.props;
    return (
      <React.Fragment>
        <Control control={control} handleControlChange={e => handleControlChange(e)} />
        <Table
          frequencies={FREQUENCIES}
          rowHeaders={ROW_HEADERS}
          params={params}
          oldParams={oldParams}
          handleParamChange={handleParamChange}
        />
        <button className="btn btn-outline-primary" onClick={() => resetParams()}>
          Reset
        </button>
        <button id="transmitButton" className="btn btn-outline-primary" onClick={() => this.transmit()}>
          Transmit
        </button>
      </React.Fragment>
    );
  }
}

class Table extends Component {
  render() {
    const { frequencies, rowHeaders, params, oldParams, handleParamChange } = this.props;
    return (
      <table className="table table-striped">
        <thead>
          <tr>
            <th scope="col">
              <h6>Frequency</h6>
            </th>
            {frequencies.map((frequency, index) => (
              <th key={index} scope="col">
                <h6>{frequency}</h6>
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {rowHeaders.map((header, index) => {
            return (
              <Row
                th={header}
                params={params[index]}
                oldParams={oldParams[index]}
                row={index}
                handleParamChange={handleParamChange}
                key={index}
              />
            );
          })}
        </tbody>
      </table>
    );
  }
}

class Row extends Component {
  render() {
    return (
      <tr>
        <th scope="row">
          <h6>{this.props.th}</h6>
        </th>
        {this.props.params.map((val, index) => (
          <td key={index} className={val === this.props.oldParams[index] ? '' : 'table-warning'}>
            <input
              style={{
                border: 'none',
                background: 'none',
                fontSize: '18px',
                width: '100%'
              }}
              value={Math.round(val * 100) / 100}
              onChange={e => this.props.handleParamChange(e, this.props.row, index)}
              type="number"
            />
          </td>
        ))}
      </tr>
    );
  }
}

class Control extends Component {
  render() {
    const { control, handleControlChange } = this.props;
    return (
      <div className="my-3">
        <div>Control Via:</div>
        <div className="form-check d-inline pl-5">
          <input
            className="form-check-input"
            type="radio"
            checked={control === 0}
            onChange={() => handleControlChange(0)}
          />
          <label className="form-check-label" onClick={() => handleControlChange(0)}>
            G50/G80
          </label>
        </div>
        <div className="form-check d-inline pl-5">
          <input
            className="form-check-input"
            type="radio"
            checked={control === 1}
            onChange={() => handleControlChange(1)}
          />
          <label className="form-check-label" onClick={() => handleControlChange(1)}>
            CompRatio/G65
          </label>
        </div>
      </div>
    );
  }
}
