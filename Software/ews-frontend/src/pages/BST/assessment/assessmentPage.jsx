import React, { Component } from "react";
import { withStyles } from "@material-ui/styles";
import Paper from "@material-ui/core/Paper";
import Grid from "@material-ui/core/Grid";
import Button from "@material-ui/core/Button";
import ButtonGroup from "@material-ui/core/ButtonGroup";
import PlayCircleOutlineIcon from "@material-ui/icons/PlayCircleOutline";
import * as d3 from "d3";
import treeGraph from "utils/BST/treeGraph.csv";
import treeNodes from "utils/BST/treeNodes.csv";
import { parseTreeNodeSingleEar } from "utils/BST/BSTUtil";
import withUserAuth from "context/withUserAuth";
import { withRouter } from "react-router-dom";
import { compose } from "recompose";
import { Link } from "react-router-dom";
import PropTypes from "prop-types";
import { TextField } from "@material-ui/core";
import { axios } from 'utils/utils';

import Timer from "components/Timer.jsx";
import data from "./test.json";

const styles = theme => ({
  root: {
    flexGrow: 1
  },
  paper: {
    minHeight: "90vh"
  },
  button: {
    minWidth: 250,
    height: 50,
    margin: 10
  },
  grid: {
    marginLeft: 10,
    fontSize: 16
  },
  margin: {
    margin: 10
  },
  extendedIcon: {
    marginRight: 8
  },
  buttonGroup: {
    width: "40vw",
    height: 100,
    marginTop: 30,
    marginBottom: 30
  }
});

class AssessmentPage extends Component {
  //Jadie's Code
  state = {
    playCurrent: true,
    currentNode: 1,
    nextNode: 2,
    currentQueue: [1],
    currentVisited: {},
    graph: {},
    nodes: {},
    firstTimeAccess: true,
    completed: false,
    currentEar: true,
    audioConfig: {},
    currentFile: data,
    audioPath: "",
    log: [],
    numQuestion: 1
  };

  componentDidMount = async () => {
    const { graph } = this.state;
    const { assessmentDone } = this.props.context;
    if (Object.keys(graph).length === 0 && assessmentDone !== true) {
      await this.getWorseEar();
      await this.constructGraph();
      try {
        const configResponse = await axios.get(
          "/api/researcher/userAssessmentConfig"
        );
        const audioConfig = configResponse.data;
        const pathResponse = await axios.get("/api/researcher/audioPath");
        const audioPath = pathResponse.data;
        this.setState({ audioConfig, audioPath }, async () => {
          await this.playFile();
        });
      } catch (error) {
        alert(error.response.data);
      }
      this.handlePlay(true);
    }
  };

  getFilePath = () => {
    const { audioConfig, audioPath, currentEar } = this.state;
    return audioPath.concat(
      this.getRandom(audioConfig[currentEar ? "left" : "right"])
    );
  };

  getRandom = filenames => {
    return filenames[Math.floor(Math.random() * filenames.length)];
  };

  playFile = async () => {
    const filePath = this.getFilePath();
    const dataInput = {
      left: {
        audio_filename: filePath,
        audio_reset: 1,
        audio_repeat: 1,
        audio_play: 1,
        alpha: 1.0
      },
      right: {
        audio_filename: filePath,
        audio_reset: 1,
        audio_repeat: 1,
        audio_play: 1,
        alpha: 1.0
      }
    };
    try {
      const response = await axios.post("/api/param/setparam", {
        method: "set",
        data: dataInput
      });
      const data = response.data;
      console.log(data);
    } catch (error) {
      alert(error.response.data);
    }
  };

  mute = async () => {
    const dataInput = {
      left: {
        alpha: 0.0
      },
      right: {
        alpha: 0.0
      }
    };
    try {
      const response = await axios.post("/api/param/setparam", {
        method: "set",
        data: dataInput
      });
      const data = response.data;
      console.log(data);
    } catch (error) {
      alert(error.response.data);
    }
  };

  getWorseEar = () => {
    const { leftEarIsWorse } = this.props.context;
    this.setState({
      currentEar: this.props.match.params.currentIsLeft === "true",
      firstTimeAccess:
        leftEarIsWorse === (this.props.match.params.currentIsLeft === "true")
    });
    console.log(leftEarIsWorse);
  };

  constructGraph = async () => {
    const graph = {};
    const nodes = {};
    const edges = [];
    await d3.csv(treeGraph, treeGraph => {
      edges.push([treeGraph["ParentNodeNo"], treeGraph["NodeNo"]]);
    });
    edges.forEach(edge => {
      graph[edge[0]] = graph[edge[0]] ? graph[edge[0]] : [];
      graph[edge[0]].push(edge[1]);
    });

    await d3.csv(treeNodes, treeNode => {
      nodes[treeNode["NodeNo"]] = parseTreeNodeSingleEar(
        treeNode,
        this.state.currentEar
      );
    });

    const currentNodeString = sessionStorage.getItem("rootNode");
    const currentNode = currentNodeString ? parseInt(currentNodeString) : 1;
    console.log(currentNode);
    this.setState({
      graph,
      nodes,
      currentNode,
      nextNode: graph[currentNode][0],
      currentQueue: [currentNode]
    });
  };

  handlePlay = async isCurrent => {
    const { nodes, currentNode, nextNode } = this.state;
    try {
      this.setState({ showMessage: !this.state.showMessage });
      //console.log('I am huge')//this.state.showMessage);
      const response = await axios.post("/api/param/setparam", {
        method: "set",
        data: isCurrent ? nodes[currentNode] : nodes[nextNode]
      });
      const data = response.data;
      console.log(data);
    } catch (error) {
      alert(error.response.data);
    }
    this.setState({ playCurrent: isCurrent });
  };

  handleContinue = async option => {
    const {
      currentNode,
      nextNode,
      currentQueue,
      currentVisited,
      graph,
      currentEar,
      log,
    } = this.state;
    currentVisited[currentNode] = true;
    currentVisited[nextNode] = true;
    let newCurrentNode = currentNode;
    let newNextNode = nextNode;
    if (option === 0) {
      let nodeIndex = currentQueue.indexOf(currentNode);
      currentQueue.splice(nodeIndex, 1);
      newCurrentNode = nextNode;
    }
    if (option !== 2) {
      currentQueue.push(nextNode);
    }
    let currentLog = "";
    switch (option) {
      case 0:
        currentLog = currentLog.concat(
          "User thinks NEW(",
          nextNode,
          ") is better than CURRENT(",
          currentNode,
          ") for the ",
          currentEar ? "LEFT ear" : "RIGHT ear"
        );
        break;
      case 1:
        currentLog = currentLog.concat(
          "User thinks NEW(",
          nextNode,
          ") is the same as CURRENT(",
          currentNode,
          ") for the ",
          currentEar ? "LEFT ear" : "RIGHT ear"
        );
        break;
      case 2:
        currentLog = currentLog.concat(
          "User thinks NEW(",
          nextNode,
          ") is worse than CURRENT(",
          currentNode,
          ") for the ",
          currentEar ? "LEFT ear" : "RIGHT ear"
        );
        break;
      default:
        break;
    }
    log.push(currentLog);
    this.setState(log);

    if (currentQueue.length === 0) {
      this.setState({ completed: true });
      return;
    }
    currentQueue.forEach(node => {
      if (node in graph) {
        graph[node].forEach(siblingNode => {
          if (newNextNode === nextNode && !(siblingNode in currentVisited)) {
            newNextNode = siblingNode;
          }
        });
      }
    });
    // console.log(newCurrentNode, newNextNode)
    if (newNextNode === nextNode) {
      this.setState({ completed: true });
      await this.mute();
    } else {
      await this.playFile();
      this.setState(
        {
          currentNode: newCurrentNode,
          nextNode: newNextNode,
          currentQueue,
          currentVisited
        },
        () => this.handlePlay(true)
      );
    }
  };

  onSubmit = async () => {
    const { updateUser, user, loginMode } = this.props.context;
    const {
      currentQueue,
      nodes,
      currentEar,
      log,
      firstTimeAccess
    } = this.state;
    if (loginMode === "researcher") {
      return;
    }
    try {
      if (!firstTimeAccess) {
        await axios.post("/api/listener/addLog", {
          listenerID: user,
          newLog: {
            assessment: {
              logOn: new Date().toLocaleString(),
              log
            }
          },
          flag: "assessmentDone",
          flagValue: true
        });
        sessionStorage.setItem("assessmentDone", true);
        updateUser();
        const tmpParameters = sessionStorage.getItem("tmpParameters");
        await axios.post("/api/listener/updateParameters", {
          listenerID: user,
          parameters: JSON.stringify({
            left:
              currentEar === true
                ? nodes[currentQueue[0]]["left"]
                : tmpParameters,
            right:
              currentEar === false
                ? nodes[currentQueue[0]]["right"]
                : tmpParameters
          })
        });
      } else {
        await axios.post("/api/listener/addLog", {
          listenerID: user,
          newLog: {
            assessment: {
              logOn: new Date().toLocaleString(),
              log
            }
          },
          flag: "assessmentDone",
          flagValue: false
        });
        sessionStorage.setItem(
          "tmpParameters",
          currentEar === true
            ? nodes[currentQueue[0]]["left"]
            : nodes[currentQueue[0]]["right"]
        );
      }
    } catch (error) {
      alert(error.message);
      console.log(error);
    }
  };

  render() {
    const { classes, showNodeNumber } = this.props;
    const {
      playCurrent,
      firstTimeAccess,
      completed,
      currentEar,
      currentNode,
      nextNode,
      currentFile
    } = this.state;

    return (
      <Paper className={classes.paper}>
        <Grid container spacing={1}>
          <Grid item xs={12} className={classes.grid}>
            Use your {currentEar ? "LEFT" : "RIGHT"} ear:
          </Grid>
          <Grid item xs={12} className={classes.grid}>
            Please click the one you think is better:
          </Grid>
          <Timer filename={currentFile} />
          <Grid
            container
            direction="row"
            justify="space-around"
            alignItems="center"
          >
            <ButtonGroup
              size="large"
              color="primary"
              aria-label="large outlined primary button group"
            >
              <Button
                className={classes.buttonGroup}
                variant={!playCurrent ? "outlined" : "contained"}
                startIcon={<PlayCircleOutlineIcon />}
                onClick={() => this.handlePlay(true)}
              >
                Current
              </Button>
              <Button
                className={classes.buttonGroup}
                variant={playCurrent ? "outlined" : "contained"}
                startIcon={<PlayCircleOutlineIcon />}
                onClick={() => this.handlePlay(false)}
              >
                Next
              </Button>
            </ButtonGroup>
          </Grid>
          {!completed ? (
            [
              <Grid
                container
                direction="column"
                justify="flex-end"
                alignItems="center"
                key="options"
              >
                <Button
                  className={classes.button}
                  variant="outlined"
                  color="primary"
                  onClick={() => this.handleContinue(0)}
                >
                  New is Better than Current
                </Button>
                <Button
                  className={classes.button}
                  variant="outlined"
                  color="primary"
                  onClick={() => this.handleContinue(1)}
                >
                  New is the Same as Current
                </Button>
                <Button
                  className={classes.button}
                  variant="outlined"
                  color="primary"
                  onClick={() => this.handleContinue(2)}
                >
                  New is Worse than Current
                </Button>
              </Grid>,
              showNodeNumber ? (
                <Grid
                  container
                  direction="row"
                  justify="center"
                  key="node_helpers"
                >
                  <TextField
                    className={classes.buttonGroup}
                    variant="outlined"
                    value={"Node ".concat(currentNode)}
                    inputProps={{
                      style: { textAlign: "center" }
                    }}
                    disabled={true}
                  />
                  <TextField
                    className={classes.buttonGroup}
                    variant="outlined"
                    value={"Node ".concat(nextNode)}
                    inputProps={{
                      style: { textAlign: "center" }
                    }}
                    disabled={true}
                  />
                </Grid>
              ) : null
            ]
          ) : (
            <Grid
              container
              direction="column"
              justify="flex-end"
              alignItems="center"
            >
              {firstTimeAccess
                ? `You've finished ${currentEar ? "LEFT" : "RIGHT"} ear.`
                : "Congrats!! You've finished the user assessment!"}
              {firstTimeAccess && !showNodeNumber ? (
                <Button
                  className={classes.button}
                  variant="outlined"
                  color="primary"
                  onClick={this.onSubmit}
                  component={Link}
                  to="/assessmentpageSetup/second"
                >
                  Proceed to the next ear
                </Button>
              ) : (
                <Button
                  className={classes.button}
                  variant="outlined"
                  color="primary"
                  onClick={this.onSubmit}
                  component={Link}
                  to="/researcherpage"
                >
                  Back to main menu
                </Button>
              )}
            </Grid>
          )}
        </Grid>
      </Paper>
    );
  }
}

AssessmentPage.propTypes = {
  showNodeNumber: PropTypes.bool.isRequired
};

export default compose(
  withUserAuth,
  withRouter,
  withStyles(styles)
)(AssessmentPage);
