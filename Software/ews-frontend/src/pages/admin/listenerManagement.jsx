import React, { Component } from "react";
import { axios } from "utils/utils";
import { withStyles } from "@material-ui/styles";
import Paper from "@material-ui/core/Paper";
import Checkbox from "@material-ui/core/Checkbox";
import Toolbar from "@material-ui/core/Toolbar";
import Typography from "@material-ui/core/Typography";
import Button from "@material-ui/core/Button";
import Table from "@material-ui/core/Table";
import TableHead from "@material-ui/core/TableHead";
import TableRow from "@material-ui/core/TableRow";
import TableBody from "@material-ui/core/TableBody";
import TableCell from "@material-ui/core/TableCell";
import { withRouter } from "react-router-dom";
import { compose } from "recompose";
import withUserAuth from "context/withUserAuth";
import ListenerSignup from "components/listenerSignup";
import ListenerListRow from "components/listenerListRow";

const styles = (theme) => ({
  root: {
    flexGrow: 1,
  },
  content: {
    padding: theme.spacing(3),
  },
  listenerSection: {
    marginTop: 30,
    width: "90%",
    maxWidth: 1000,
  },
  listenerSectionContent: {
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
  },
  topBarButton: {
    marginLeft: 30,
  },
  exportButton: {
    marginLeft: "auto",
  },
});

class ListnerManagement extends Component {
  state = {
    signupDialogOpen: false,
    listenerList: [],
    selected: [],
  };

  componentDidMount = async () => {
    try {
      const { updateUser } = this.props.context;
      await updateUser();
      const { user } = this.props.context;

      const response = await axios.get(`/api/listener/${user}`);
      console.log(response.data);
      this.setState({
        listenerList: response.data,
        selected: new Array(response.data.length).fill(false),
      });
    } catch (error) {
      alert(error);
      console.log(error);
    }
  };

  handleSignupDialogOpen = () => {
    this.setState({
      signupDialogOpen: true,
    });
  };

  handleSignupDialogClose = () => {
    this.setState({
      signupDialogOpen: false,
    });
  };

  handleSelectAllClick = (e) => {
    this.setState({
      selected: new Array(this.state.selected.length).fill(e.target.checked),
    });
  };

  handleSelectClick = (index, e) => {
    let { selected } = this.state;
    selected[index] = e.target.checked;
    this.setState({
      selected,
    });
  };

  handleExport = () => {
    const { selected, listenerList } = this.state;
    let exportFiles = {
      patient_history: "",
      PTA: "",
      use_assessment: "",
    };
    exportFiles["4AFC"] = "";
    selected.forEach((select, index) => {
      if (select) {
        const userLog = JSON.parse(listenerList[index].userLog);
        const listenerHeader = `ListenerId,${listenerList[index].listenerID}\n`;
        exportFiles.patient_history = exportFiles.patient_history.concat(
          listenerHeader,
          this.handlePatientHistory(userLog.history),
          "\n"
        );
        exportFiles["4AFC"] = exportFiles["4AFC"].concat(
          listenerHeader,
          this.handleAFCExport(userLog.AFC),
          "\n"
        );
        exportFiles.PTA = exportFiles.PTA.concat(
          listenerHeader,
          this.handlePTAExport(userLog.PTA),
          "\n"
        );
        exportFiles.use_assessment = exportFiles.use_assessment.concat(
          listenerHeader,
          this.handleAssessmentExport(userLog.assessment),
          "\n"
        );
      }
    });

    ["patient_history", "4AFC", "PTA", "use_assessment"].forEach((curr) => {
      const element = document.createElement("a");
      const file = new Blob([exportFiles[curr]], {
        type: "text/csv;encoding:utf-8",
      });
      element.href = URL.createObjectURL(file);
      element.download = `${curr}_log.csv`;
      document.body.appendChild(element); // Required for this to work in FireFox
      element.click();
    });
  };

  handlePatientHistory = (history) => {
    if (!history) {
      return "N/A\n";
    }
    return "".concat(
      "Patient History Log on, ",
      history.logOn,
      "\n",
      "Medical History\n",
      "Left ear Condition,",
      history.medicalHistory.leftEar,
      "\n",
      "Right ear Condition,",
      history.medicalHistory.rightEar,
      "\n",
      "Illnesses had before\n",
      history.illnesses.length !== 0
        ? "," +
            Object.keys(history.illnesses).join(",") +
            "\n" +
            "When did it occur," +
            Object.keys(history.illnesses).map((illness) => {
              return "".concat(history.illnesses[illness]["occur"], ",");
            }) +
            "\n" +
            "How did it happen," +
            Object.keys(history.illnesses).map((illness) => {
              return "".concat(history.illnesses[illness]["happen"], ",");
            }) +
            "\n" +
            "When did it resolve," +
            Object.keys(history.illnesses).map((illness) => {
              return "".concat(history.illnesses[illness]["resolve"], ",");
            }) +
            "\n"
        : "None\n",
      "Development History\n",
      `Age started to speak, ${history.ageSpeak}\n\n`,
      "Education History\n",
      `The highest education obtained, ${history.highestEdu} \n`,
      "Family History\n",
      "Illnesses family members had before \n",
      history.illnessesFam.length !== 0
        ? "," +
            Object.keys(history.illnessesFam).join(",") +
            "\n" +
            "Relation(s)," +
            Object.keys(history.illnessesFam).map((illness) => {
              return "".concat(history.illnessesFam[illness]["relation"], ",");
            }) +
            "\n" +
            "When did it occur," +
            Object.keys(history.illnessesFam).map((illness) => {
              return "".concat(history.illnessesFam[illness]["occur"], ",");
            }) +
            "\n" +
            "How did it happen," +
            Object.keys(history.illnessesFam).map((illness) => {
              return "".concat(history.illnessesFam[illness]["happen"], ",");
            }) +
            "\n" +
            "When did it resolve," +
            Object.keys(history.illnessesFam).map((illness) => {
              return "".concat(history.illnessesFam[illness]["resolve"], ",");
            }) +
            "\n"
        : "None\n"
    );
  };

  handleAFCExport = (AFC) => {
    if (!AFC) {
      return "N/A\n";
    }

    let ans = "";
    AFC.log.forEach((currLog, index) => {
      ans = ans.concat(
        `Question ${index}\n`,
        "Current options,",
        currLog.currentOptions.join(","),
        "\n",
        "Correct answer,",
        currLog.currentWord,
        "\n",
        "Selected,",
        currLog.option,
        "\n"
      );
    });
    return "".concat(`4AFC Log on,${AFC.logOn}\n`,ans);
  };

  handlePTAExport = (PTA) => {
    if (!PTA) {
      return "N/A\n";
    }
    let res = "";
    Object.keys(PTA.left).forEach((freq, index) => {
      res = res.concat(
        `${freq},`,
        PTA.left[freq] === "" ? "N/A," : `${PTA.left[freq]},`,
        PTA.right[freq] === "" ? "N/A\n" : `${PTA.right[freq]}\n`
      );
    });
    return "".concat(
      `PTA Log on,${PTA.logOn}\n`,
      `AudiologistID, ${PTA.audiologistID}\n`,
      "Frequency(Hz),Left Ear(dBspl), Right Ear(dBspl)\n",
      res
    );
  };

  handleAssessmentExport = (assessment) => {
    if (!assessment) {
      return "N/A\n";
    }
    return "".concat(
      `User Assessment Log on,${assessment.logOn}\n`,
      // assessment.log.forEach((currLog, index) => {
      //   return `Question ${index + 1}: ${currLog} \n`;
      // })
      assessment.log.join('\n')
    );
  };

  render() {
    const { classes } = this.props;
    const { listenerList, selected } = this.state;

    console.log(selected);
    return (
      <div className={classes.content}>
        <Paper className={classes.listenerSection}>
          <Toolbar>
            <Typography color="textSecondary" gutterBottom>
              My Listeners
            </Typography>
            <Button
              color="primary"
              variant="contained"
              className={classes.topBarButton}
              onClick={this.handleSignupDialogOpen}
            >
              Create New Listener
            </Button>
            <Button
              color="primary"
              variant="contained"
              className={classes.exportButton}
              onClick={this.handleExport}
            >
              Export
            </Button>
          </Toolbar>
          <ListenerSignup
            open={this.state.signupDialogOpen}
            handleClose={this.handleSignupDialogClose}
          />
          <div className={classes.listenerSectionContent}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell padding="checkbox">
                    <Checkbox
                      //indeterminate={numSelected > 0 && numSelected < rowCount}
                      //checked={rowCount > 0 && numSelected === rowCount}
                      onChange={this.handleSelectAllClick}
                      inputProps={{ "aria-label": "select all desserts" }}
                    />
                  </TableCell>
                  {["Listener ID", "Created At", "Updated At", "Action"].map(
                    (text) => (
                      <TableCell key={text}>
                        <Typography variant="subtitle1" color="textSecondary">
                          {text}
                        </Typography>
                      </TableCell>
                    )
                  )}
                </TableRow>
              </TableHead>
              <TableBody>
                {listenerList.map((listener, index) => (
                  <ListenerListRow
                    key={`listener_${index}_${selected[index]}`}
                    listener={listener}
                    selected={selected[index]}
                    onClick={(e) => this.handleSelectClick(index, e)}
                  />
                ))}
              </TableBody>
            </Table>
          </div>
        </Paper>
      </div>
    );
  }
}

export default compose(
  withUserAuth,
  withRouter,
  withStyles(styles)
)(ListnerManagement);
