import React, { Component } from 'react';
import { AmplificationParametersTab } from './AmplificationParametersTab';
import { NoiseManagementTab } from './NoiseManagementTab';
import { FeedbackManagementTab } from './FeedbackManagementTab';
import { INITIAL_PARAMS } from './constants';

class App extends Component {
  constructor() {
    super();
    this.state = {
      tab: 0,

      control: 0,
      params: INITIAL_PARAMS,
      oldParams: INITIAL_PARAMS,

      noiseManagement: 0,
      spectralSubtractionParameter: 0,

      feedbackAlgorithmType: 0,
      mu: 0.005,
      rho: 0.985,

      paramsFileName: 'params.json'
    };
  }

  handleTabChange(value) {
    this.setState({ tab: value });
  }

  // AmplificationParametersTab

  handleControlChange(value) {
    if (value === 0) {
      this.allg50g80();
    } else if (value === 1) {
      this.allcrg65();
    }

    this.setState({ control: value });
  }

  handleParamChange(event, row, col) {
    this.setParam(event.target.value, row, col);
  }

  setParam(value, row, col) {
    let newParams = this.state.params.map(arr => arr.slice());
    const param = parseFloat(value);

    const { control } = this.state;

    newParams[row][col] = param;

    if (col === 6) {
      for (let i = 0; i < 6; i++) {
        newParams[row][i] = param;
      }
    }

    if (control === 0 && (row === 1 || row === 3)) {
      newParams = this.g50g80(col, newParams);
      if (col === 6) {
        for (let i = 0; i < 6; i++) {
          newParams = this.g50g80(i, newParams);
        }
      }
    } else if (control === 1 && (row === 0 || row === 2)) {
      newParams = this.crg65(col, newParams);
      if (col === 6) {
        for (let i = 0; i < 6; i++) {
          newParams = this.crg65(i, newParams);
        }
      }
    }

    this.setState({ params: newParams });
  }

  crg65(col, newParams) {
    const cr = newParams[0][col];

    if (cr !== 0) {
      const g65 = newParams[2][col];
      const slope = (1 - cr) / cr;
      const g50 = g65 - slope * 15;
      const g80 = g65 + slope * 15;

      newParams[1][col] = g50;
      newParams[3][col] = g80;
    }

    return newParams;
  }

  g50g80(col, newParams) {
    const g50 = newParams[1][col];
    const g80 = newParams[3][col];
    const slope = (g80 - g50) / 30;
    const g65 = g50 + slope * 15;

    newParams[2][col] = g65;

    if (slope !== -1) {
      const cr = 1 / (1 + slope);
      newParams[0][col] = cr;
    }

    return newParams;
  }

  allcrg65() {
    let newParams = this.state.params.map(arr => arr.slice());

    for (let i = 0; i < 6; i++) {
      newParams = this.crg65(i, newParams);
    }

    this.setState({ params: newParams });
  }

  allg50g80() {
    let newParams = this.state.params.map(arr => arr.slice());

    for (let i = 0; i < 6; i++) {
      newParams = this.g50g80(i, newParams);
    }

    this.setState({ params: newParams });
  }

  resetParams() {
    this.setState({
      params: INITIAL_PARAMS
    });
  }

  updateOldParams() {
    this.setState({ oldParams: this.state.params });
  }

  // NoiseManagementTab
  handleNoiseManagementChange(value) {
    this.setState({ noiseManagement: value });
  }

  handleSpectralSubtractionParameterChange(e) {
    this.setState({ spectralSubtractionParameter: parseFloat(e.target.value) });
  }

  // FeedbackManagementTab
  handleFeedbackAlgorithmTypeChange(value) {
    this.setState({ feedbackAlgorithmType: value });
  }

  handleMuChange(e) {
    this.setState({ mu: parseFloat(e.target.value) });
  }

  handleRhoChange(e) {
    this.setState({ rho: parseFloat(e.target.value) });
  }

  handleParamsFileNameChange(e) {
    this.setState({ paramsFileName: e.target.value });
  }

  loadParamsFromFile() {
    var file = document.getElementById('paramsFile').files[0];
    var reader = new FileReader();
    reader.readAsText(file, 'UTF-8');
    reader.onload = e => {
      const state = JSON.parse(e.target.result);
      this.setState(state);
    };
  }

  render() {
    const {
      control,
      params,
      oldParams,
      noiseManagement,
      spectralSubtractionParameter,
      feedbackAlgorithmType,
      mu,
      rho
    } = this.state;

    let tab;
    switch (this.state.tab) {
      case 0:
        tab = (
          <AmplificationParametersTab
            control={control}
            params={params}
            oldParams={oldParams}
            handleControlChange={this.handleControlChange.bind(this)}
            handleParamChange={this.handleParamChange.bind(this)}
            resetParams={this.resetParams.bind(this)}
            updateOldParams={this.updateOldParams.bind(this)}
          />
        );
        break;
      case 1:
        tab = (
          <NoiseManagementTab
            noiseManagement={noiseManagement}
            spectralSubtractionParameter={spectralSubtractionParameter}
            handleNoiseManagementChange={this.handleNoiseManagementChange.bind(this)}
            handleSpectralSubtractionParameterChange={this.handleSpectralSubtractionParameterChange.bind(this)}
          />
        );
        break;
      case 2:
        tab = (
          <FeedbackManagementTab
            feedbackAlgorithmType={feedbackAlgorithmType}
            mu={mu}
            rho={rho}
            handleFeedbackAlgorithmTypeChange={this.handleFeedbackAlgorithmTypeChange.bind(this)}
            handleMuChange={this.handleMuChange.bind(this)}
            handleRhoChange={this.handleRhoChange.bind(this)}
          />
        );
        break;
      default:
        tab = '';
        break;
    }
    return (
      <React.Fragment>
        <Tabs tab={this.state.tab} handleTabChange={this.handleTabChange.bind(this)} />
        {tab}
        <div className="my-5">
          <input
            className="form-control"
            style={{ width: 'auto' }}
            type="text"
            value={this.state.paramsFileName}
            onChange={this.handleParamsFileNameChange.bind(this)}
          />
          <a
            className="btn btn-info my-1"
            href={'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(this.state))}
            download={this.state.paramsFileName}
          >
            Save Params to File
          </a>
        </div>
        <input id="paramsFile" className="form-control-file" type="file" />
        <button className="btn btn-info my-1" onClick={this.loadParamsFromFile.bind(this)}>
          Load Params from File
        </button>
      </React.Fragment>
    );
  }
}

class Tabs extends Component {
  render() {
    const { tab, handleTabChange } = this.props;
    const active = 'btn btn-primary btn-block';
    const inactive = 'btn btn-outline-primary btn-block';
    return (
      <div className="row m-0">
        <div className="col p-0">
          <button className={tab === 0 ? active : inactive} onClick={() => handleTabChange(0)}>
            Amplification Parameters
          </button>
        </div>
        <div className="col p-0">
          <button className={tab === 1 ? active : inactive} onClick={() => handleTabChange(1)}>
            Noise Management
          </button>
        </div>
        <div className="col p-0">
          <button className={tab === 2 ? active : inactive} onClick={() => handleTabChange(2)}>
            Feedback Management
          </button>
        </div>
      </div>
    );
  }
}

export default App;
