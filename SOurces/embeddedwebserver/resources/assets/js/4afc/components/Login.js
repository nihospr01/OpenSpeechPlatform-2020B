import React, { Component } from "react";

class Login extends Component {
  handleTesterIDChange(e) {
    this.props.onTesterIDChange(e.target.value);
  }

  handleParticipantIDChange(e) {
    this.props.onParticipantIDChange(e.target.value);
  }
  render() {
    return (
      <div className="col-12 col-md-6 offset-md-3 col-lg-4 offset-lg-4 my-5 text-center">
        <h1>4AFC Web App</h1>

        <div className="input-group my-3">
          <input
            type="text"
            className="form-control"
            placeholder="Tester ID"
            value={this.props.testerID}
            onChange={this.handleTesterIDChange.bind(this)}
          />
        </div>

        <div className="input-group my-3">
          <input
            type="text"
            className="form-control"
            placeholder="Participant ID"
            value={this.props.participantID}
            onChange={this.handleParticipantIDChange.bind(this)}
          />
        </div>

        <div className="my-3">
          <button
            type="button"
            className="btn btn-primary"
            id="log"
            onClick={this.props.next_screen}
          >
            <span className="oi oi-account-login" /> Log In
          </button>
        </div>
      </div>
    );
  }
}

export default Login;
