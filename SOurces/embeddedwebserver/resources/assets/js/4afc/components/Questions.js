import React, { Component } from "react";
import "./question.css";

class Questions extends Component {
  constructor(props) {
    super(props);
    this.state = { question_number: 0 };
  }

  next() {
    this.setState({
      question_number: this.state.question_number + 1
    });
  }

  render() {
    return (
      <div className="text-center">
        <div className="progress mb-3">
          <div
            className="progress-bar"
            role="progressbar"
            style={{
              width: (this.state.question_number + 1) / 4 * 100 + "%"
            }}
          />
        </div>

        <h3>Press play and select the word you hear</h3>

        <div className="my-1">
          <button
            className="play-button"
            onClick={() => {
              this.props.play_audio(this.state.question_number);
            }}
          >
            <span className="oi oi-play-circle" />
          </button>
        </div>

        <Words
          question_number={this.state.question_number}
          words={this.props.word_sets[this.state.question_number].words}
          selected_word_indices={
            this.props.selected_word_indices[this.state.question_number]
          }
          select_word={this.props.select_word}
        />

        <div className="row m-3">
          {this.state.question_number !== this.props.word_sets.length - 1 ? (
            <button
              className="btn btn-lg btn-primary col-12"
              id="next"
              onClick={() => this.next()}
            >
              <span>Next</span>
            </button>
          ) : (
            <button
              className="btn btn-lg btn-primary col-12"
              id="next"
              onClick={this.props.next_screen}
            >
              <span>Done</span>
            </button>
          )}
        </div>
      </div>
    );
  }
}

class Words extends Component {
  render() {
    return (
      <div className="row center mx-3">
        {this.props.words.map((word, index) => {
          return (
            <WordButton
              key={index}
              word={word}
              select_word={() =>
                this.props.select_word(this.props.question_number, index)
              }
              selected={this.props.selected_word_indices === index}
            />
          );
        })}
      </div>
    );
  }
}

class WordButton extends Component {
  render() {
    let className = "word btn btn-lg word col-19 col-sm-6";

    if (this.props.selected) {
      className += " btn-primary";
    } else {
      className += " btn-outline-primary";
    }

    return (
      <button className={className} onClick={this.props.select_word.bind(this)}>
        {this.props.word}
      </button>
    );
  }
}

export default Questions;
