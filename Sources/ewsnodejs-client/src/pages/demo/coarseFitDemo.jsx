import React, { Component } from "react";
import { axios } from "utils/utils";
import { withRouter, Link } from "react-router-dom";
import withUserAuth from "context/withUserAuth";
import { compose } from "recompose";
import { withStyles } from "@material-ui/styles";
import Paper from "@material-ui/core/Paper";
import Grid from "@material-ui/core/Grid";
import Button from "@material-ui/core/Button";
import Stepper from "@material-ui/core/Stepper";
import Step from "@material-ui/core/Step";
import StepLabel from "@material-ui/core/StepLabel";
import StepContent from "@material-ui/core/StepContent";
import FormControlLabel from "@material-ui/core/FormControlLabel";
import Radio from "@material-ui/core/Radio";
import RadioGroup from "@material-ui/core/RadioGroup";
import IconButton from "@material-ui/core/IconButton";
import PlayCircleFilledIcon from '@material-ui/icons/PlayCircleFilled';
import MobileStepper from '@material-ui/core/MobileStepper';
import KeyboardArrowLeft from '@material-ui/icons/KeyboardArrowLeft';
import KeyboardArrowRight from '@material-ui/icons/KeyboardArrowRight';
import * as d3 from "d3";
import treeNodes from "utils/BST/treeNodes.csv";
import { parseTreeNode } from "utils/BST/BSTUtil";

import { optionDescriptions, optionImages, optionOccupancies, optionLossImages }from '../../utils/utils';

import AudioFilePlayer from "components/audioFilePlayer"
import { findLastIndex } from "lodash";

const styles = (theme) => ({
    slider: {
        width: "60vw",
        marginLeft: 30,
        marginRight: 30,
    },
    icon: {
        maxWidth: "10vw",
        marginBottom: 20,
    },
    title: {
        margin: 10,
    },
    optionImg: {
        height: "auto",
        width: "100%",
        maxWidth: 600,
        maxHeight: 300,
    },
    button: {
        margin: 10,
    },
});

const lossRadioColor = ['#0026f5', '#367b21', '#eb3223', '#54bcbe','#a72cb0'];
const lossProfileNames = ["Normal", "Mild", "Medium", "Severe", "Profound"];
class CoarseFitDemo extends Component {
    state = {
        step: 0,
        loss: 0,
        option: 0,
        stimulus: 0,
        currentIsLeft: true,
        nodes:{},
        subStep: 0, // Substeps in step 2: getting hearing loss root node
        maxSubstepReached: 0, // Since the user can go back to previous iterations, we want to keep track of how many iterations the user has went through so far.
        choicesMade: [-1, -1, -1, -1, -1],
        audioFilesChosen: ["", "", "", "", ""]
    };

    componentDidMount = async () => {
        console.log(window.innerWidth);
        this.getWorseEar(this.props.match.params.time);
        await this.constructGraph();

        // Default choice for the first iteration is "Normal"
        const newChoicesMade = [0, -1, -1, -1, -1];
        this.setState({
            choicesMade: newChoicesMade
        });
    }


    getWorseEar = (time) => {
        const { leftEarIsWorse } = this.props.context;
        console.log(leftEarIsWorse)
        this.setState({
            currentIsLeft: time === "first" ? leftEarIsWorse : !leftEarIsWorse,
            time: time,
        });
    };

    componentWillUnmount() {
        this._isMounted = false;
    }

    //TODO: CONTSTRUCT GRAPH BASED ON THE OPTION 
    constructGraph = async () => {
        let nodes = {};
    
        await d3.csv(treeNodes, (treeNode) => {
            nodes[treeNode["NodeNo"]] = parseTreeNode(treeNode);
        });

        this.setState({ nodes });

    };

    setRootNode = () => {
        const { option, loss } = this.state;
        // Set value for getting root node in user assessment
        // Add 1 to match the root number in the .csv file
        sessionStorage.setItem('rootNode',Number(loss) + 1);
        sessionStorage.setItem('option',Number(option));
    }

    //TODO: SEND ROOT NODE BASED ON THE OPTION
    sendHAParam = async () => {
        const { loss, nodes } = this.state;
        let node_data = nodes[Number(loss)+1];
        console.log(node_data)
        try {
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: {
                    left: {
                        // audio_reset: 1,
                        // audio_repeat: 0,
                        // audio_play: 1,
                        alpha: 1.0,
                        ...node_data.left,
                    },
                    right: {
                        // audio_reset: 1,
                        // audio_repeat: 0,
                        // audio_play: 1,
                        alpha: 1.0,
                        ...node_data.right,
                    },

                }
            });
            const data = response.data;
            console.log(data);
        } catch (error) {
            alert(error);
        }
    };


    // Handle value for changes for all radio buttons
    handleRadioChange = (key, event) => {
        // console.log(key);
        // console.log(event.target.value);
        const value = Number(event.target.value);
        this.setState({
            [key]: value,
        }, this.sendHAParam);

        // if (key === "loss") {
        //     const newChoicesMade = this.state.choicesMade;
        //     newChoicesMade[this.state.subStep] = value;
        //     this.setState({
        //         choicesMade: newChoicesMade,
        //     }, () => {
        //         console.log(this.state.choicesMade);
        //     });
            
        // }
    };

    // Step 1: choose between different setting
    getSettingOption = () => {
        const { classes } = this.props;
        const { option } = this.state;

        return (
            <Grid container direction="column" spacing={1} style={{width: "auto"}}>
                <Grid item xs={12} className={classes.title}>
                    Please choose one among three settings:
                </Grid>
                <Grid item direction="row">
                    <RadioGroup
                        value={option}
                        onChange={(e) => this.handleRadioChange("option", e)}
                        row
                    >
                        {["Option1", "Option2", "Option3"].map(
                            (currOption, index) => {
                                return (
                                    <FormControlLabel
                                        key={`${currOption}-radio`}
                                        value={index}
                                        control={<Radio />}
                                        label={currOption}
                                    />
                                );
                            }
                        )}
                    </RadioGroup>

                </Grid>
                <Grid item xs={9} >
                    {optionDescriptions[option]}
                </Grid>
                <Grid item xs={12} style={{display: "flex", flexDirection: "column"}}>
                    <img
                        src={optionImages[option]}
                        alt={`option_image`}
                        className={classes.optionImg}
                    />
                    <img
                        src={optionOccupancies[option]}
                        alt={`option_occupancy_image`}
                        className={classes.optionImg}
                    />
                </Grid>
            </Grid>
        );
    };

    // Step 2: generate different graph based on the step 1 setting and the loss level

    // Helper funcitons
    handleNext = () => {
        this.setState((prevState) => ({
            subStep: prevState.subStep + 1,
        }), () => {
            // If the current substep/iteration is bigger than the maxSubsteps the user has
            // reached so far, it means that the user reaches a new iteration. So we want to
            // let the loss option inherit the previous since the user hasn't changed it.
            if (this.state.subStep === this.state.maxSubstepReached) {
                let newChoicesMade = this.state.choicesMade;
                newChoicesMade[this.state.subStep] = this.state.loss;
                this.setState({
                    choicesMade: newChoicesMade,
                });
            }
        });
    };

    handleBack = () => {
        this.setState((prevState) => ({
            subStep: prevState.subStep - 1
        }));
    };

    // This function allows the user to click 'Next' for each iteration of coarse fit.
    // This function is sent to the audio file player. Whenever after the user clicks
    // the play button and plays some file, we allow the user to proceed to the next
    // iteration.
    setProceedSignal = () => {
        if (this.state.subStep === this.state.maxSubstepReached){
            this.setState((prevState) => ({
                maxSubstepReached: prevState.maxSubstepReached + 1,
            }));
        }
    }

    updateFilesChosen = (currentFile) => {
        const subStep = this.state.subStep;
        let newAudioFilesChosen = this.state.audioFilesChosen;
        newAudioFilesChosen[subStep] = currentFile;
        this.setState({
            audioFilesChosen: newAudioFilesChosen
        });
    }

    getLossAssessment = () => {
        const { classes } = this.props;
        const { loss, option, subStep, maxSubstepReached } = this.state;

        return (
            <Grid container direction="column" spacing={1} style={{width: "auto"}}>
                <Grid item className={classes.title}>
                    Please adjust the hearing loss:
                </Grid>

                <Grid item xs={12} direction="row">
                    <RadioGroup
                        value={loss}
                        onChange={(e) => this.handleRadioChange("loss", e)}
                        row
                    >
                        {lossProfileNames.map(
                            (currLoss, index) => {
                                return (
                                    <FormControlLabel
                                        style={{color:lossRadioColor[index]}}
                                        key={`${currLoss}-radio`}
                                        value={index}
                                        control={<Radio />}
                                        label={currLoss}
                                    />
                                );
                            }
                        )}
                    </RadioGroup>
                </Grid>

                <Grid item xs={12}>
                    <AudioFilePlayer
                        key={this.state.subStep}
                        preparePlay={this.setProceedSignal}
                        onChangeAudioFile={this.updateFilesChosen}
                    />
                </Grid>

                <Grid item xs={12} style={{display: "flex", flexDirection: "column"}}>
                    <img
                        src={optionLossImages[option][loss]}
                        alt={`option_loss_image`}
                        className={classes.optionImg}
                    />
                </Grid>

                {/* <Grid item>
                    <p>Hearing loss levels chosen so far:</p>
                    {this.state.choicesMade.map(
                        (choiceIdx, index) => {
                            if (choiceIdx === -1) {
                                return;
                            }
                            const currLossName = lossProfileNames[choiceIdx];
                            const currLossColor = lossRadioColor[choiceIdx];
                            return (
                                <p>
                                    Iteration {index}------
                                    Audio file chosen:{" "}
                                    {!this.state.audioFilesChosen[index] ?
                                        "None" :
                                        this.state.audioFilesChosen[index]
                                    }------
                                    Hearing loss level:{" "}
                                    <font color={currLossColor}>{currLossName}</font>
                                </p>
                            );
                        }
                    )}
                </Grid>

                <MobileStepper
                    style={{width: "auto", marginBottom: 10, maxWidth: 600}}
                    variant="progress"
                    steps={5}
                    position="static"
                    activeStep={subStep}
                    nextButton={
                        <Button size="small" onClick={this.handleNext} disabled={subStep === 4 || subStep === maxSubstepReached}>
                            Next
                        <KeyboardArrowRight />
                        </Button>
                    }
                    backButton={
                        <Button size="small" onClick={this.handleBack} disabled={subStep === 0}>
                        <KeyboardArrowLeft />
                            Back
                        </Button>
                    }
                /> */}
            </Grid>
        );
    };


    // Step3: Stimulus option (not support yet)
    getStimulusOption = () => {
        const { classes } = this.props;
        const { stimulus } = this.state;

        return (
            <Grid container spacing={1}>
                <Grid item xs={12} className={classes.title}>
                    Please choose the stimulus option for the BST fine tuning:
                </Grid>
                <Grid container direction="row">
                    <RadioGroup
                        value={stimulus}
                        onChange={(e) => this.handleRadioChange("stimulus", e)}
                        row
                    >
                        {["Live data", "Audio file"].map((option, index) => {
                            return (
                                <FormControlLabel
                                    key={`${option}-radio`}
                                    value={index}
                                    control={<Radio />}
                                    label={option}
                                />
                            );
                        })}
                    </RadioGroup>
                </Grid>
            </Grid>
        );
    };

    getStepContent = (index) => {
        switch (index) {
            case 0:
                return this.getSettingOption();
            case 1:
                return this.getLossAssessment();
            // case 2:
            //     return this.getStimulusOption();
            default:
                return;
        }
    };

    handleStepChange = (change) => {
        this.setState((prevState) => ({
            step: prevState.step + change,
        }));
    };

    render() {
        const { classes } = this.props;
        const { step, currentIsLeft } = this.state;

        return (
            <Paper className={classes.paper} style={{width: "100%"}}>
                <Stepper orientation="vertical" activeStep={step}>
                    {[
                        "Setting option",
                        "Loss Assessment",
                        // "Stimulus Option",
                    ].map((label, index) => (
                        <Step key={label}>
                            <StepLabel>{label}</StepLabel>
                            <StepContent>
                                {this.getStepContent(index)}
                                <div className={classes.actionsContainer} style={{display: 'flex', flexDirection: 'row'}}>
                                    <Button
                                        disabled={step === 0}
                                        onClick={() =>
                                            this.handleStepChange(-1)
                                        }
                                        className={classes.button}
                                    >
                                        Back
                                    </Button>

                                    {step === 1 ? (
                                        <Button
                                            variant="contained"
                                            color="primary"
                                            className={classes.button}
                                            component={Link} 
                                            onClick={this.setRootNode}
                                            to={`/`}
                                        >
                                            Submit
                                        </Button>
                                    ) : (
                                        <Button
                                            variant="contained"
                                            color="primary"
                                            // disabled={step === 1 && this.state.maxSubstepReached < 5}
                                            onClick={() =>
                                                this.handleStepChange(1)
                                            }
                                            className={classes.button}
                                        >
                                            Next
                                        </Button>
                                    )}

                                    {/* {step === 1 ? (
                                        <p style={{margin: 'auto'}}>
                                            Please complete all 5 iterations of coarse fits to go to the next step.
                                        </p>
                                    ) : (
                                        <p></p>
                                    )} */}
                                </div>
                            </StepContent>
                        </Step>
                    ))}
                </Stepper>
            </Paper>
        );
    }
}

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(CoarseFitDemo);
