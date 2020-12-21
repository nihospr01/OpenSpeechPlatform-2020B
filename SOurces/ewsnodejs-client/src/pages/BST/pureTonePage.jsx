import React, { Component } from "react";
import { withStyles } from "@material-ui/styles";
import Paper from "@material-ui/core/Paper";
import Grid from "@material-ui/core/Grid";
import TextField from "@material-ui/core/TextField";
import Table from "@material-ui/core/Table";
import TableBody from "@material-ui/core/TableBody";
import TableCell from "@material-ui/core/TableCell";
import TableHead from "@material-ui/core/TableHead";
import TableRow from "@material-ui/core/TableRow";
import Button from "@material-ui/core/Button";
import { Link, withRouter } from "react-router-dom";
import { compose } from "recompose";
import withUserAuth from "context/withUserAuth";
import { axios } from "utils/utils";

const styles = (theme) => ({
  root: {
    flexGrow: 1,
  },
  grid: {
    marginLeft: 30,
    marginTop: 10,
  },
  button: {
    margin: 10,
  },
  table: {
    margin: 10,
  },
});

const rows = [125, 250, 500, 750, 1000, 1500, 2000, 3000, 4000, 6000, 8000];
const requiredIdx = [1, 2, 4, 6, 8];

class PureTonePage extends Component {
  state = {
    audiologistID: "",
    firstEditable: false,
    secondEditable: false,
    worseEarList: ["", "", "", "", "", "", "", "", "", "", ""],
    betterEarList: ["", "", "", "", "", "", "", "", "", "", ""],
    currentEar: false,
  };

  componentDidMount = () => {
    this.getWorseEar();
  };

  handleIDChange = (event) => {
    const { firstEditable } = this.state;
    this.setState({ audiologistID: event.target.value });
    if (this.validateID(event.target.value) ? !firstEditable : firstEditable) {
      this.setState({ firstEditable: !firstEditable });
    }
  };

  handleTextChange = (isWorseEar, index) => (event) => {
    const { worseEarList, betterEarList } = this.state;
    if (isWorseEar) {
      worseEarList[index] = event.target.value;
      this.setState({ worseEarList });
    } else {
      betterEarList[index] = event.target.value;
      this.setState({ betterEarList });
    }

    if (
      requiredIdx.filter((idx) => betterEarList[idx] === "").length === 0 &&
      requiredIdx.filter((idx) => worseEarList[idx] === "").length === 0
    ) {
      this.setState({ secondEditable: true });
    } else {
      this.setState({ secondEditable: false });
    }
  };

  validateID = (id) => {
    // TODO(YUE YU): Add after backend is implemented.
    if (id.length > 0) {
      return true;
    }
    return false;
  };

  getWorseEar = () => {
    console.log(this.props.context);
    const { leftEarIsWorse } = this.props.context;
    this.setState({
      currentEar: leftEarIsWorse,
    });
  };

  onSubmit = async () => {
    const { updateUser, user, loginMode } = this.props.context;
    const {
      currentEar,
      audiologistID,
      worseEarList,
      betterEarList,
    } = this.state;
    if (loginMode === "researcher") {
      return;
    }
    let leftEarList = currentEar === true ? worseEarList: betterEarList;
    let left = {};
    rows.forEach((value, index) => {left[value] = leftEarList[index]});
    let rightEarList = currentEar === false ? worseEarList: betterEarList;
    let right = {};
    rows.forEach((value, index) => {right[value] = rightEarList[index]});

    try {
      await axios.post("/api/listener/addLog", {
        listenerID: user,
        newLog:
        {
          PTA: {
            logOn: new Date().toLocaleString(),
            audiologistID,
            right,
            left,
          },
        },
        flag: "PTAdone",
        flagValue: true,
      });
      sessionStorage.setItem("PTAdone", true);
      updateUser();
    } catch (error) {
      alert(error.message);
    }
  };

  render() {
    const { classes } = this.props;
    const {
      audiologistID,
      firstEditable,
      secondEditable,
      worseEarList,
      betterEarList,
      currentEar,
    } = this.state;

    return (
      <Paper>
        <Grid container spacing={1}>
          <Grid item xs={6} className={classes.grid}>
            <TextField
              label="Audiologist ID *"
              onChange={this.handleIDChange}
              value={audiologistID}
              variant="outlined"
            />
          </Grid>
          <Table size="small" className={classes.table}>
            <TableHead>
              <TableRow>
                <TableCell>Frequency in Hertz(Hz)</TableCell>
                <TableCell align="right">
                  {currentEar ? "Left Ear(dBspl)" : "Right Ear(dBspl)"}
                </TableCell>
                <TableCell align="right">
                  {currentEar ? "Right Ear(dBspl)" : "Left Ear(dBspl)"}
                </TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {rows.map((row, rowIndex) => (
                <TableRow key={rowIndex}>
                  <TableCell component="th" scope="row">
                    {requiredIdx.includes(rowIndex) ? row + " *" : row}
                  </TableCell>
                  <TableCell align="right">
                    <TextField
                      disabled={!firstEditable}
                      onChange={this.handleTextChange(true, rowIndex)}
                      value={worseEarList[rowIndex]}
                      type="number"
                      inputProps={{
                        step: "5",
                        style: { textAlign: "right" },
                      }}
                    />
                  </TableCell>
                  <TableCell align="right">
                    <TextField
                      disabled={!firstEditable}
                      onChange={this.handleTextChange(false, rowIndex)}
                      value={betterEarList[rowIndex]}
                      type="number"
                      inputProps={{
                        step: "5",
                        style: { textAlign: "right" },
                      }}
                    />
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
          <Grid
            container
            direction="row"
            justify="flex-end"
            alignItems="center"
          >
            <Button
              className={classes.button}
              variant="contained"
              color="primary"
              onClick={this.onSubmit}
              component={Link}
              to="/researcherpage"
              disabled={!firstEditable || !secondEditable}
            >
              Submit
            </Button>
          </Grid>
        </Grid>
      </Paper>
    );
  }
}

export default compose(
  withUserAuth,
  withRouter,
  withStyles(styles)
)(PureTonePage);
