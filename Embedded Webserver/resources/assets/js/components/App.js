import React, { Component } from 'react';
import Login from './Login';
import Questions from './Questions';
import Result from './Result';
import './bootstrap/bootstrap.min.css';
import './open-iconic-master/font/css/open-iconic-bootstrap.css';

class App extends Component {
  constructor() {
    super();
    this.state = {
      screen: 'login',
      selected_word_indices: [],
      testerID: '',
      participantID: ''
    };
  }

  handleTesterIDChange(id) {
    this.setState({ testerID: id });
  }

  handleParticipantIDChange(id) {
    this.setState({ participantID: id });
  }

  componentDidMount() {
    this.load_word_set().then(data => {
      this.word_sets = data;
      this.setState({
        selected_word_indices: this.getInitialArray(this.word_sets.length)
      });
    });
  }

  async load_word_set() {
    const response = await fetch('word_sets.json');
    const word_sets = await response.json();
    return word_sets;
  }

  getInitialArray(size) {
    let arr = Array(size);
    arr.fill(-1, 0, arr.length);
    return arr;
  }

  reset() {
    this.setState({
      screen: 'login',
      selected_word_indices: this.getInitialArray(this.word_sets.length)
    });
  }

  next_screen() {
    if (this.state.screen === 'login') {
      fetch('/api/connect', {
        body: JSON.stringify({'URI' :   this.state.testerID + '_' + this.state.participantID }), // must match 'Content-Type' header
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
      this.setState({ screen: 'questions' });
    } else if (this.state.screen === 'questions') {
      this.setState({ screen: 'result' });
    } else {
      this.reset();
    }
  }

  play_audio(question_number) {
    fetch('/api/4afc', {
      body: JSON.stringify(this.word_sets[question_number].audio), // must match 'Content-Type' header
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

  select_word(question_number, word_index) {
    const selected_word_indices = this.state.selected_word_indices.slice();
    selected_word_indices[question_number] = word_index;
    this.setState({ selected_word_indices: selected_word_indices });
  }

  render() {
    if (this.state.screen === 'login') {
      return (
        <Login
          next_screen={this.next_screen.bind(this)}
          testerID={this.state.testerID}
          participantID={this.state.participantID}
          onTesterIDChange={this.handleTesterIDChange.bind(this)}
          onParticipantIDChange={this.handleParticipantIDChange.bind(this)}
        />
      );
    } else if (this.state.screen === 'questions') {
      return (
        <Questions
          word_sets={this.word_sets}
          selected_word_indices={this.state.selected_word_indices}
          select_word={this.select_word.bind(this)}
          next_screen={this.next_screen.bind(this)}
          play_audio={this.play_audio.bind(this)}
        />
      );
    } else {
      return (
        <Result
          word_sets={this.word_sets}
          selected_word_indices={this.state.selected_word_indices}
          testerID={this.state.testerID}
          participantID={this.state.participantID}
          next_screen={this.next_screen.bind(this)}
        />
      );
    }
  }
}

export default App;
