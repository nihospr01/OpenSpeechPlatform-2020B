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
import { parseTreeNodeSingleEar, NUM_LEVEL } from "utils/BST/BSTUtil";
import withUserAuth from "context/withUserAuth";
import { withRouter } from "react-router-dom";
import { compose } from "recompose";
import { Link } from "react-router-dom";
import PropTypes from "prop-types";
import { TextField } from "@material-ui/core";
import { axios } from 'utils/utils';

import Timer from "components/Timer.jsx";
import data from "./test.json";
import images from "utils/images/hearingLossDemo";

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

//TODO:
//1. compare with left and right root node wheh reaching the root node after going n levels up
//2. read the csv file based on the option 
class AssessmentPage extends Component {
  state = {
    playCurrent: true,
    currentNode: 1,
    nextNode: 2,
    rootNode: 1,
    prevVisited: 0,
    prevOption:0,
    currentDepth:0,
    //iterate for five times after reaching the leaf node
    currentIteration: 1,
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
        console.log(audioConfig);  
        const pathResponse = await axios.get("/api/researcher/audioPath");
        let audioPath = pathResponse.data;
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
    console.log(filePath);
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

  // TODO: read csv file based on the option setting
  // Construct the BST graph
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
    const option = sessionStorage.getItem("option");
    const currentNode = currentNodeString ? parseInt(currentNodeString) : 1;
    console.log(currentNode);
    this.setState({
      graph,
      nodes,
      currentNode,
      rootNode: currentNode,
      // Next node is the left child
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

  handleLog = (option, currentNode, nextNode, currentEar) => {
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
    return currentLog;

  }

  // Option: 0, Next is better than parent
  // Option: 1, Parent is the same with next
  // Option 2: Parent is better then next
  handleContinue = async option => {
    let {
      currentNode,
      nextNode,
      prevVisited,
      currentIteration,
      prevOption,
      graph,
      currentEar,
      currentDepth,
      rootNode,
      log,
    } = this.state;

    let currentLog = this.handleLog(option, currentNode, nextNode, currentEar);
    log.push(currentLog);

    let reachTerminalNode = false;
    // Next is better than parent node, continue on next node if it is not leaf
    if(option === 0 ){
      if(graph[nextNode] != null){
        currentNode = nextNode;
        nextNode = graph[nextNode][0];
        currentDepth += 1;
        prevVisited = -1;
        prevOption = -1;
        console.log('Next node is better than the parent, next node is new parent');
      }else{
        reachTerminalNode = true;
        console.log('Next node is better than the parent, next node is leaf node');
      }
    }
    // Parent is the same with the next node
    else if(option ===1){
      // Current next node is right child, parent is same with both left and right child, terminal point
      if(prevVisited === currentNode && prevOption === 1){
        reachTerminalNode = true;
        console.log('Parent is same as the left child and right child, terminal point');
      }else if(prevVisited === currentNode && prevOption === 2){
        // Current next node is right child, parent is better than left the same with right child, continue with right child
        currentNode = nextNode;
        nextNode = graph[nextNode][0];
        currentDepth += 1;
        prevVisited = -1;
        prevOption = -1;
        console.log('Parent is better than left child and the same with right child, right child is the new parent');
      }else{
        // Current next node is left child, parent is same with left child, compare with the right child 
        nextNode = graph[currentNode][1];
        prevVisited = currentNode;
        prevOption = option;
        console.log('Parent is same with the left child, right child is next node');
      }
    }else{
      // Current next node is right child, parent is better then right child, reach the terminal 
      if(prevVisited === currentNode ){
        reachTerminalNode = true;
        console.log('Parent is better than/same with right and left child, ternimal point');
      }else{
        // Current next node is left child, parent is better then left child, compare with the right child is not leaf
        nextNode = graph[currentNode][1];
        console.log('Parent is better than left child, right child is next node');
      }
    }

    // Reach terminal node, go n levels up and continue with for different stimuli
    //TODO: compare with other root nodes when reach root after going n levels up
    if(reachTerminalNode){
      if(currentIteration === 5){
        console.log('Reach terminal node, finish 5 iterations, done');
        this.setState({ 
          completed: true,
          log });
          await this.mute();
        return;
      }else{
        // Get current node level
        console.log('Reach terminal node, go N levels up');
        currentDepth -= NUM_LEVEL;
        // after going n levels up, reach the root node, compare with other rootnode
        currentNode = currentDepth < 0 ? rootNode: ( Math.pow(2, currentDepth+1) - 1 ) * 5 +  Math.pow(2, currentDepth+1) * (rootNode -1) + 1;
        nextNode = graph[currentNode][0];
        prevVisited = -1;
        prevOption = -1;
        currentDepth = currentDepth < 0 ? 0 : currentDepth;
        currentIteration++;
      }
    }
    console.log("NewCurrentNode:",currentNode, "NewNextNode:", nextNode);
    await this.playFile();
      this.setState(
        {
          currentNode,
          nextNode,
          currentDepth,
          currentIteration,
          prevOption,
          prevVisited,
          log
        },
        () => this.handlePlay(true)
      );
  };

  onSubmit = async () => {
    const { updateUser, user, loginMode } = this.props.context;
    const {
      nodes,
      currentEar,
      currentNode,
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
                ? nodes[currentNode]["left"]
                : tmpParameters,
            right:
              currentEar === false
                ? nodes[currentNode]["right"]
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
            ? nodes[currentNode]["left"]
            : nodes[currentNode]["right"]
        );
      }
    } catch (error) {
      alert(error.message);
      console.log(error);
    }

    // Reset state for the new ear
    this.setState({
      currentNode: 1,
      nextNode: 2,
      rootNode: 1,
      currentVisited: [],
      currentDepth:0,
      currentIteration: 1,
    })
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
      currentFile,
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
          {<Timer filename={currentFile} />}
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
