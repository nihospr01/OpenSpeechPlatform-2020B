import React, { Component } from "react";
import { axios } from "utils/utils";
import {  withStyles } from "@material-ui/core/styles";
import Paper from "@material-ui/core/Paper";
import Grid from "@material-ui/core/Grid";
import Button from "@material-ui/core/Button";
import IconButton from "@material-ui/core/IconButton";
import VolumeUpIcon from "@material-ui/icons/VolumeUp";
import AddCircleOutlineIcon from "@material-ui/icons/AddCircleOutline";
import RemoveCircleOutlineIcon from "@material-ui/icons/RemoveCircleOutline";
import PlayCircleOutlineIcon from "@material-ui/icons/PlayCircleOutline";
import * as d3 from "d3";
import treeNodes from "utils/BST/treeNodes.csv";
import rootNodes from "utils/BST/rootNodes.csv";
import { parseTreeNode } from "utils/BST/BSTUtil";
import withUserAuth from "context/withUserAuth";
import { withRouter, Link } from "react-router-dom";
import { compose } from "recompose";

import LinearProgress from "@material-ui/core/LinearProgress";
import HelpIcon from "@material-ui/icons/Help";
import Tooltip from "@material-ui/core/Tooltip";

const BorderLinearProgress = withStyles({
    root: {
        height: 10,
        //backgroundColor: lighten('#ff6c5c', 0.5),
    },
    bar: {
        borderRadius: 20,
        //backgroundColor: '#ff6c5c',
    },
})(LinearProgress);

const styles = (theme) => ({
    slider: {
        width: "60vw",
        marginLeft: 30,
        marginRight: 30,
    },
    icon: {
        maxWidth: "10vw",

        marginRight: 10,
    },
    title: {
        margin: 10,
    },
    img: {
        height: "auto",
        width: "60vw",
    },
    button: {
        margin: 10,
    },
    iconButton: {
        height: 50,
        width: 50,
    },
    iconButtonMarginLeft: {
        // marginRight: '28vw',
    },
    iconButtonMarginRight: {
        // marginLeft: '28vw',
    },
    buttonGroup: {
        width: 200,
        height: 200,
    },
});

class AssessmentSetup extends Component {
    state = {
        nodes: {},
        options: {},
        currentNode: 1,
        currentIsLeft: true,
        time: "first",
    };

    componentDidMount = async () => {
        this._isMounted = true;
        const { options } = this.state;
        await this.getWorseEar(this.props.match.params.time);
        if (Object.keys(options).length === 0) {
            await this.constructGraph();
            await this.handleApply();
        }
    };

    componentWillUnmount() {
        this._isMounted = false;
    }

    getWorseEar = (time) => {
        const { leftEarIsWorse } = this.props.context;
        this.setState({
            currentIsLeft: time === "first" ? leftEarIsWorse : !leftEarIsWorse,
            time: time,
        });
    };

    constructGraph = async () => {
        const nodes = {};
        const options = {};

        await d3.csv(rootNodes, (rootNode) => {
            options[rootNode["NodeNo"]] = rootNode["HearingLoss"];
        });

        await d3.csv(treeNodes, (treeNode) => {
            nodes[treeNode["NodeNo"]] = parseTreeNode(treeNode);
        });

        if (this._isMounted) {
            this.setState({ options, nodes });
        }
    };

    decreaseSound = () => {
        this.setState(
            {
                currentNode:
                    this.state.currentNode > 1 ? this.state.currentNode - 1 : 1,
            },
            this.handleApply
        );
    };

    increaseSound = () => {
        this.setState(
            {
                currentNode:
                    this.state.currentNode < 5 ? this.state.currentNode + 1 : 5,
            },
            this.handleApply
        );
    };

    getMarks = () => {
        const marks = [];
        const { options } = this.state;
        Object.keys(options).forEach((node) => {
            marks.push({
                value: 25 * (node - 1),
                label: node,
            });
        });
        return marks;
    };

    handleApply = async () => {
        const { currentNode, nodes } = this.state;
        try {
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: nodes[currentNode],
            });
            const data = response.data;
            console.log(data);
        } catch (error) {
            alert(error.response.data);
        }
    };

    handleSliderChange = (event, value) => {
        this.setState({ currentNode: value / 25 + 1 });
    };

    earDone = () => {
        const { currentNode } = this.state;
        sessionStorage.setItem("rootNode", parseInt(currentNode));
        // this.setState({ currentIsLeft: false, currentNode: 1 });
    };

    handlePlay = async () => {
        let path = "";
        try {
            const response = await axios.get("/api/researcher/audioPath");
            path = path.concat("/../../..", response.data);
        } catch (error) {
            alert(error.response.data);
        }
        path = path.concat("test.wav");

        console.log(path);
        const dataInput = {
            left: {
                audio_filename: path,
                audio_reset: 1,
                audio_repeat: 0,
                audio_play: 1,
                alpha: 1.0,
            },
            right: {
                audio_filename: path,
                audio_reset: 1,
                audio_repeat: 0,
                audio_play: 1,
                alpha: 1.0,
            },
        };
        try {
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: dataInput,
            });
            const data = response.data;
            console.log(data);
        } catch (error) {
            alert(error.response.data);
        }
    };

    render() {
        const { classes } = this.props;
        const { currentNode, options, currentIsLeft } = this.state;
        return (
            <Paper className={classes.paper}>
                <Grid container spacing={1}>
                    <Grid item xs={12} className={classes.title}>
                        {`Please adjust the volume for your ${
                            currentIsLeft ? "LEFT" : "RIGHT"
                        } ear`}
                        <Tooltip title="In this section, first adjust volumn, then choose the audio that you think is better. The process is repeated for both right ear and left ear">
                            <IconButton aria-label="help">
                                <HelpIcon />
                            </IconButton>
                        </Tooltip>
                    </Grid>
                    <Grid
                        container
                        direction="row"
                        justify="center"
                        alignItems="center"
                    >
                        {/* <VolumeDownIcon className={classes.icon}/> */}
                        <VolumeUpIcon className={classes.icon} />
                        {options ? (
                            // <Slider
                            //     value={(currentNode - 1) * 25}
                            //     aria-labelledby="discrete-slider-always"
                            //     step={25}
                            //     marks={this.getMarks()}
                            //     track={false}
                            //     className={classes.slider}
                            // />
                            <BorderLinearProgress
                                //className={classes.margin}
                                variant="determinate"
                                //color="secondary"
                                value={(currentNode - 1) * 25}
                                style={{ flex: 0.3 }}
                            />
                        ) : null}

                        <div style={{ marginLeft: 10, fontSize: 16 }}>
                            {(currentNode - 1) * 25 + "%"}
                        </div>
                    </Grid>
                    <Grid
                        container
                        direction="row"
                        justify="center"
                        alignItems="center"
                    >
                        <IconButton
                            aria-label="remove"
                            onClick={this.decreaseSound}
                            className={classes.iconButtonMarginLeft}
                            disabled={currentNode === 1}
                        >
                            <RemoveCircleOutlineIcon
                                className={classes.iconButton}
                            />
                        </IconButton>
                        {/* <div>
                            {"Current Volumn: " + (currentNode - 1 ) + " / 4"}
                        </div> */}
                        <IconButton
                            aria-label="add"
                            onClick={this.increaseSound}
                            className={classes.iconButtonMarginRight}
                            disabled={currentNode === 5}
                        >
                            <AddCircleOutlineIcon
                                className={classes.iconButton}
                            />
                        </IconButton>
                    </Grid>
                    <Grid
                        container
                        direction="row"
                        justify="space-around"
                        alignItems="center"
                    >
                        <IconButton
                            variant={"contained"}
                            onClick={() => this.handlePlay()}
                        >
                            <PlayCircleOutlineIcon
                                className={classes.buttonGroup}
                            />
                        </IconButton>
                    </Grid>
                    <Grid
                        container
                        direction="row"
                        justify="flex-end"
                        alignItems="center"
                    >
                        <Button
                            className={classes.button}
                            variant="outlined"
                            color="primary"
                            onClick={this.earDone}
                            component={Link}
                            to={`/assessmentPage/${currentIsLeft}`}
                        >
                            Continue
                        </Button>
                        }
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
)(AssessmentSetup);
