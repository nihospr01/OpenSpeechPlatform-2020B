import React, { Component } from "react";

class Result extends Component {
  componentWillMount() {
    this.result = this.props.word_sets.map((word_set, index) => {
      const selected_word_index = this.props.selected_word_indices[index];
      const selected_word =
        word_set.words[selected_word_index] || "not selected";
      return { ...word_set, selected_word: selected_word };
    });
  }

  getNumberOfCorrectAnswers() {
    return this.result.filter(
      word_set => word_set.correct_answer === word_set.selected_word
    ).length;
  }

  render() {
    return (
      <div className="col-12 col-lg-8 offset-lg-2 my-5">
        <h3 className="m-2 text-center">
          Result :{" " +
            this.getNumberOfCorrectAnswers() +
            " / " +
            this.result.length}
        </h3>

        <div className="m-2 mt-5">
          Tester ID:{" "}
          {this.props.testerID === "" ? "not entered" : this.props.testerID}
        </div>
        <div className="m-2">
          Participant ID:{" "}
          {this.props.participantID === ""
            ? "not entered"
            : this.props.participantID}
        </div>

        <table className="table table-borderless my-5">
          <thead>
            <tr>
              <th scope="col">Question #</th>
              <th scope="col">Words</th>
              <th scope="col">Correct Answer</th>
              <th scope="col">Participant's Answer</th>
            </tr>
          </thead>
          <tbody>
            {this.result.map((word_set, index) => {
              return (
                <tr key={index}>
                  <td>{index + 1}</td>
                  <td>{word_set.words.join(", ")}</td>
                  <td>{word_set.correct_answer}</td>
                  <td
                    className={
                      word_set.correct_answer === word_set.selected_word
                        ? "table-success"
                        : "table-danger"
                    }
                  >
                    {word_set.selected_word}
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>

        <div className="clearfix">
          <div className="float-left">
            <a
              className="btn btn-info"
              href={
                "data:text/json;charset=utf-8," +
                encodeURIComponent(JSON.stringify(this.result, null, 2))
              }
              download={"result.json"}
            >
              <span className="oi oi-data-transfer-download" /> Download Result
            </a>
          </div>

          <div className="float-right">
            <button
              className="btn btn-primary"
              onClick={this.props.next_screen}
            >
              <span className="oi oi-loop-circular" /> Start Over
            </button>
          </div>
        </div>
      </div>
    );
  }
}

export default Result;
