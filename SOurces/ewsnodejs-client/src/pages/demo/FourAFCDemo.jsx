import React, { Component } from 'react';
import { withStyles } from '@material-ui/styles';
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import Button from '@material-ui/core/Button';
import PlayCircleOutlineIcon from '@material-ui/icons/PlayCircleOutline';
import IconButton from '@material-ui/core/IconButton';
import { axios } from 'utils/utils';
import withUserAuth from 'context/withUserAuth';
import { Link, withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import HelpIcon from "@material-ui/icons/Help";
import Tooltip from "@material-ui/core/Tooltip";
import Box from '@material-ui/core/Box';
import useMediaQuery from '@material-ui/core/useMediaQuery';
import { useTheme } from '@material-ui/core/styles';
import MobileStepper from '@material-ui/core/MobileStepper';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableContainer from '@material-ui/core/TableContainer';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import MenuItem from '@material-ui/core/MenuItem';
import FormControl from '@material-ui/core/FormControl';
import Select from '@material-ui/core/Select';
import InputLabel from '@material-ui/core/InputLabel';
import Radio from '@material-ui/core/Radio';
import RadioGroup from '@material-ui/core/RadioGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Stepper from "@material-ui/core/Stepper";
import Step from "@material-ui/core/Step";
import StepLabel from "@material-ui/core/StepLabel";
import StepContent from "@material-ui/core/StepContent";
import VideoPlayer from 'components/VideoPlayer';
import AudioPlayer from 'components/AudioPlayer';

const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    paper: {
        height: '90',
        width: '98%',
    },
    button: {
        width: '60%',
        height: 70,
        fontSize: 35,
        margin: 5,
    },
    restartButton: {
        width: '50%',
        fontSize: 30,
        margin: 15,
    },
    grid: {
        marginLeft: 10,
        fontSize: 20,
    },
    margin: {
        margin: 10,
    },
    extendedIcon: {
        marginRight: 8,
    },
    tooltip: {
        fontSize: 20,
    },
    buttonGroup: {
        width: 100,
        height: 100,
    },
    iconHover: {
        float: 'right',
        '&:hover': {
          color: "grey",
        },
        alignItems: "right",
        color: "inherit",
    },
    progress: {
        width: '99%',
        height: 40,
        flexGrow: 1,
        alignContent: "center",
    },
    table: {
        width: '70%',
        justifyContent: "center",
        alignItems: "center",
      },
      formControl: {
        margin: theme.spacing(1),
        minWidth: 120,
      },
});

class fourAFCDemo extends Component {
    state = {
        completed: false,
        currentWord: '',
        audioConfig: [], //contains sets of audio files and answer choices
        audioPath: '',
        currentAudioFile: '',
        currentOptions: ['', '', '', ''],
        currentCount: 0,
        logs: [],
        correctAnswers: [],
        chosenAnswers: [],
        open: false,
        subStep: 0,
        correctChoices: 0,
        delay: 1000, //start delay at 1 second
        numSets: 4,
        randomOrder: true,
        stimulusFile: "/BoothroydCCT.json",
        step:0,
        playInBrowser: false,
        playVideo: false,
    };

    componentDidMount = async () => {
        try {
            const { user, loginMode } = this.props.context;
            if (loginMode !== 'researcher') {
                const parameterResponse = await axios.get(`/api/listener/getParameters/${user}`);
                this.apply(parameterResponse.data);
            }
            const pathResponse = await axios.get("/api/researcher/audioPath");
            const audioPath = pathResponse.data;
            await this.setState({ audioPath });

            //set the appropriate set of stimuli
            const response = await axios.post("/api/researcher/jsonFile", {data: this.state.audioPath.concat(this.state.stimulusFile)});
            const audioConfig = response.data["words"]
            await this.setState({ audioConfig });
        }
        catch(error) {
            console.log(error);
            alert(error.response.data);
        }
    }

    /*to reset the page */
    reset = async () => {
        await this.setState({
            completed: false,
            currentWord: '',
            audioConfig: [], //contains sets of audio files and answer choices
            audioPath: '',
            currentAudioFile: '',
            currentOptions: ['', '', '', ''],
            currentCount: 0,
            logs: [],
            correctAnswers: [],
            chosenAnswers: [],
            open: false,
            subStep: 0,
            correctChoices: 0,
            delay: 1000, //start delay at 1 second
            numSets: 4,
            randomOrder: true,
            stimulusFile: "/BoothroydCCT.json",
            step:0,
            playInBrowser: false,
            playVideo: false,
        });
        try {
            const { user, loginMode } = this.props.context;
            if (loginMode !== 'researcher') {
                const parameterResponse = await axios.get(`/api/listener/getParameters/${user}`);
                this.apply(parameterResponse.data);
            }
            const pathResponse = await axios.get("/api/researcher/audioPath");
            const audioPath = pathResponse.data;
            await this.setState({ audioPath });

            //set the appropriate set of stimuli
            const response = await axios.post("/api/researcher/jsonFile", {data: this.state.audioPath.concat(this.state.stimulusFile)});
            const audioConfig = response.data["words"]
            await this.setState({ audioConfig });
        }
        catch(error) {
            console.log(error);
            alert(error.response.data);
        }
    }

    /* for settings button (responsive dialog box)*/
    ResponsiveDialog = async () => {
        //const [open, setOpen] = React.useState(false);
        const theme = useTheme();
        const fullScreen = useMediaQuery(theme.breakpoints.down('sm'));
    }

    /* when user clicks on the settings button*/
    handleClickOpen = async () => {
        this.setState({
            open: true,
        });
    };

    /* when user clicks "exit settings" settings button*/
    handleClickClose = async () => {
        this.setState({
            open: false,
        });
    };

    handleQuestionOrderChange = (event) => {
        this.setState({
            randomOrder: event.target.value,
        });
    };

    handleQuestionOrderChange = async () => {
        const {
            randomOrder,
        } = this.state;
        await this.setState({ 
            randomOrder: !randomOrder,
        });
    }

    /*helper function for the drop down menu for audio output */
    handleAudioOutputChange = async (event) => {
        this.setState({
            playInBrowser: event.target.value,
        });
    };

    handleAudioOutputChange = async () => {
        const {
            playInBrowser,
        } = this.state;
        await this.setState({ 
            playInBrowser: !playInBrowser,
        });
    }

    /*helper function for the drop down menu for video display */
    handleDisplayVideoChange = async (event) => {
        this.setState({
            playVideo: event.target.value,
        });
    };

    handleDisplayVideoChange = async () => {
        const {
            playVideo,
        } = this.state;
        await this.setState({ 
            playVideo: !playVideo,
        });
    }

    /*applies the current RTMHA parameters if applicable
    the current RTMHA parameters (from getParameters) is passed as an argument*/
    apply = async (parameters) => {
        if (parameters === '') {
            return;
        }
        try {
            const response = await axios.post("/api/param/setparam", {
                method: 'set',
                data: parameters,
            });
            const data = response.data;
            console.log(data);
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    /*when user clicks on the play button send audio path info to rtmha (setparam)*/
    handlePlay = async () => {
        const {
            audioPath,
            currentAudioFile,
            playInBrowser,
        } = this.state;

        if (playInBrowser) {
            
        } else {
            const dataInput ={
                left: {
                    audio_filename: audioPath.concat(currentAudioFile),
                    audio_reset: 1,
                    audio_repeat: 0,
                    audio_play: 1,
                    alpha: 1.0,
                },
                right: {
                    audio_filename: audioPath.concat(currentAudioFile),
                    audio_reset: 1,
                    audio_repeat: 0,
                    audio_play: 1,
                    alpha: 1.0,
                },
            };
            try {
                const response = await axios.post("/api/param/setparam", {
                    method: 'set',
                    data: dataInput,
                });
                const data = response.data;
                console.log(data);
            }
            catch(error) {
                alert(error.response.data);
            }
        }
    }

    /*sleep function that allows for a delay to happen before next event*/
    sleep = ms => new Promise(resolve => {
        setTimeout(
            () => {resolve()},
            ms
        );
    });

    fetchWord = async () => {
        const {
            audioConfig,
            delay,
            randomOrder,
        } = this.state;
        console.log(audioConfig)
        let index = 0; 
        if (randomOrder) { //if the user wants a random order, find a random index
            index = Math.floor(Math.random()*audioConfig.length); //picks random index (random audiofile)
        }
        const currentOption = audioConfig[index];
        audioConfig.splice(index, 1);
        await this.setState({
            audioConfig: audioConfig,
            currentWord: currentOption["answer"], //holds correct answer for current audiofile
            currentOptions: [                       //holds answer choices for current audiofile
                currentOption["option_A"],
                currentOption["option_B"],
                currentOption["option_C"],
                currentOption["option_D"],
            ],
            currentCount: this.state.currentCount + 1, //number of times word has been fetched
            currentAudioFile: currentOption["filename"], //holds current audiofile chosen
        });
        //play the next word after a delay when we go to the next set of choices
        setTimeout(() => this.handlePlay(), delay);
    }

    /*If user selected the correct word, increment the number of correct choices*/
    checkIfCorrectWord = async (option) => {
        let { 
            //currentOptions,
            currentWord,
            correctChoices,
        } = this.state;
        if (option == currentWord) {
            await this.setState((prevState) => ({
                correctChoices: prevState.correctChoices + 1,
            }));
            return "white";
        }
        return "yellow";
    };

     /*helper function to increment progress bar*/
     handleNext = () => {
        this.setState((prevState) => ({
            subStep: prevState.subStep + 1,
        }));
    };

    /*helper function for the drop down menu for number of questions */
    handleNumQuestionChange = async (event) => {
        this.setState({
            numSets: event.target.value,
        });
    };

    /*helper function for the drop down menu for stimuli selection*/
    handleStimuliChange = async (event) => {
        await this.setState({
            stimulusFile: event.target.value,
        });
        //change the audio configuration to represent the stimuli selected
        const response = await axios.post("/api/researcher/jsonFile", {data: this.state.audioPath.concat(this.state.stimulusFile)});
        const audioConfig = response.data["words"]
        await this.setState({ audioConfig });
    };

    handleContinue = async (option) => {
        let {
            currentCount,
            currentOptions,
            currentWord,
            logs,
            numSets,
            correctAnswers,
            chosenAnswers,
        } = this.state;
        const result = await this.checkIfCorrectWord(option); //called to check if user selected the correct word, returns yellow if incorrect answer
        console.log(logs)
        console.log(correctAnswers)
        console.log(chosenAnswers)
        logs.push({             //adds info from current "run" to log: user's answer, answer choices, and correct answer
            currentOptions,
            currentWord,
            option,
            result
        });
        correctAnswers.push({currentWord});
        chosenAnswers.push({option});
        this.setState({
            logs,
            correctAnswers,
            chosenAnswers,
        })
        if (currentCount >= numSets) {    //if all iterations have been completed, mark test as completed
            await this.setState({
                completed: true
            });
        } else {
            this.fetchWord();   //if not done with iterations, proceed to next word
        }
        this.handleNext(); //increment the progress bar
    }

    /* To test /testPython in paramController.js */
    getData = async() => {
        try {
            const respnse = await axios.post("/api/param/testPython")
            const data = respnse.data
            console.log(data)
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    getSettingOption = () => {
        const { classes } = this.props;
        const {
            delay,
            numSets,
            randomOrder,
            stimulusFile,
            playInBrowser,
            playVideo,
        } = this.state;
        return (
            <Grid>
                <Box mt={2} lineHeight="normal" fontSize={18} >  
                    Stimuli Selection:
                </Box>
                <Box mt={1} lineHeight="normal" fontSize={15} >  
                    Select the stimuli you would like to use for the test (default BoothroydCCT).
                </Box>
                <Grid>
                    <FormControl className={classes.formControl}>
                        <Select
                            labelId="demo-simple-select-label"
                            id="demo-simple-select"
                            value={stimulusFile}
                            onChange={this.handleStimuliChange}
                        >
                            <MenuItem value={"/BoothroydCCT.json"}>Boothroyd CCT</MenuItem>
                        </Select>
                    </FormControl>
                </Grid>
                <Box mt={2} lineHeight="normal" fontSize={18} >  
                    Number of Test Questions:
                </Box>
                <Box mt={1} lineHeight="normal" fontSize={15} >  
                    Select the number of questions for the test (default 4).
                </Box>
                <Grid>
                    <FormControl className={classes.formControl}>
                        <Select
                            labelId="demo-simple-select-label"
                            id="demo-simple-select"
                            value={numSets}
                            onChange={this.handleNumQuestionChange}
                        >
                            <MenuItem value={4}>4</MenuItem>
                            <MenuItem value={8}>8</MenuItem>
                            <MenuItem value={16}>16</MenuItem>
                            <MenuItem value={32}>32</MenuItem>
                        </Select>
                    </FormControl>
                </Grid>
                <Box mt={2} lineHeight="normal" fontSize={18} >  
                    Order of Questions:
                </Box>
                <Box mt={1} mb={1} lineHeight="normal" fontSize={15} >  
                    Choose between random or sequential question order (default random).
                </Box>
                <Grid item>
                    <FormControl component="fieldset">
                        <RadioGroup aria-label="qorder" name="qorder" value={randomOrder} onChange={this.handleQuestionOrderChange}>
                            <FormControlLabel value={true} control={<Radio />} label="Random" />
                            <FormControlLabel value={false} control={<Radio />} label="Sequential" />
                        </RadioGroup>
                    </FormControl>
                </Grid>
                <Box mt={2} lineHeight="normal" fontSize={18} >  
                    Audio Output:
                </Box>
                <Box mt={1} mb={1} lineHeight="normal" fontSize={15} >  
                    Choose between playing audio/ video through the browser or through the device (default device).
                </Box>
                <Grid item>
                    <FormControl component="fieldset">
                        <RadioGroup aria-label="qorder" name="qorder" value={playInBrowser} onChange={this.handleAudioOutputChange}>
                            <FormControlLabel value={false} control={<Radio />} label="Device" />
                            <FormControlLabel value={true} control={<Radio />} label="Browser" />
                        </RadioGroup>
                    </FormControl>
                </Grid>
                {
                    /*playInBrowser ?
                    <Grid>
                        <Box mt={2} lineHeight="normal" fontSize={18} >  
                            Video Display:
                        </Box>
                        <Box mt={1} mb={1} lineHeight="normal" fontSize={15} >  
                            Select if you would like video to be played if available (default no). Audio Output must be set to browser to enable this.
                        </Box>
                        <Grid item>
                            <FormControl component="fieldset">
                                <RadioGroup aria-label="qorder" name="qorder" value={playVideo} onChange={this.handleDisplayVideoChange}>
                                    <FormControlLabel value={true} control={<Radio />} label="Video" />
                                    <FormControlLabel value={false} control={<Radio />} label="No Video" />
                                </RadioGroup>
                            </FormControl>
                        </Grid> 
                    </Grid>  
                    :
                    <Grid></Grid>*/
                }
            </Grid>
        );
    };

    get4AFCTest = () => {
        const { classes } = this.props;
        const { 
            completed,
            currentOptions,
            subStep,
            correctChoices,
            delay,
            numSets,
            playInBrowser,
            currentAudioFile,
            playVideo,
        } = this.state;
        return (
            <Grid>
                <Grid>
                {
                    !completed ?
                    <Grid item xs={12} className={classes.grid}>
                        Please click the word you heard:
                        <Tooltip title={<span style={{ fontSize: "20px"}}> Click the play button to hear the audio, then choose the word that matches best to the audio</span>}>
                            <IconButton aria-label="help">
                            <HelpIcon />
                            </IconButton>
                        </Tooltip>
                    </Grid>
                    :
                    <Grid></Grid>
                    }
                    <MobileStepper
                        variant="progress"
                        steps={numSets+1}
                        position="static"
                        activeStep={subStep}
                        className={classes.progress}
                        nextButton={
                            <Button size="small"></Button>
                        }
                        backButton={
                            <Button size="small"></Button>
                        }
                    />
                </Grid>
                <Grid>
                {
                    !completed ? 
                    <Grid
                    >
                        {
                            playInBrowser ?
                            <Grid>
                                <AudioPlayer
                                    audioName = {currentAudioFile}
                                />
                            {
                                playVideo ?
                                <Grid></Grid>
                                :
                                <Grid></Grid>
                            }
                            </Grid>
                            :
                            <Grid 
                                container
                                direction="row"
                                justify="space-around"
                                alignItems="center"
                            >
                                <IconButton 
                                    variant={"contained"} 
                                    onClick={() => setTimeout(() => this.handlePlay(), delay)} //play audio with delay
                                >
                                    <PlayCircleOutlineIcon className={classes.buttonGroup}/>
                                </IconButton>
                            </Grid>
                        }
                        <Grid 
                            container
                            direction="column"
                            justify="flex-end"
                            alignItems="center"
                        >
                            <Button
                                className={classes.button} 
                                variant="outlined" 
                                color="primary"
                                onClick={() => this.handleContinue(currentOptions[0])}
                            >
                                {currentOptions[0]}
                            </Button>
                            <Button
                                className={classes.button} 
                                variant="outlined" 
                                color="primary"
                                onClick={() => this.handleContinue(currentOptions[1])}
                            >
                                {currentOptions[1]}
                            </Button>
                            <Button
                                className={classes.button} 
                                variant="outlined" 
                                color="primary"
                                onClick={() => this.handleContinue(currentOptions[2])}
                            >
                                {currentOptions[2]}
                            </Button>
                            <Button
                                className={classes.button} 
                                variant="outlined" 
                                color="primary"
                                onClick={() => this.handleContinue(currentOptions[3])}
                            >
                                {currentOptions[3]}
                            </Button>
                        </Grid> 
                    </Grid> :
                    <Grid 
                        container
                        direction="column"
                        justify="flex-end"
                        alignItems="center"
                        className={classes.grid}
                    >
                        <p>You are finished</p>
                        <p>Word-Level Accuracy: {(correctChoices*100/numSets).toFixed(2)}%</p>
                        <TableContainer component={Paper} className={classes.table}>
                            <Table /*className={classes.table}*/ aria-label="simple table">
                                <TableHead>
                                    <TableRow>
                                        <TableCell>Answer Options</TableCell>
                                        <TableCell align="center">Your Answer</TableCell>
                                        <TableCell align="center">Correct Answer</TableCell>
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {this.state.logs.map((log,index) => {
                                        return (
                                        <TableRow key={index}>
                                            <TableCell component="th" scope="row" style={{backgroundColor:log.result}}>
                                                (A) {log.currentOptions[0]} · (B) {log.currentOptions[1]} · (C) {log.currentOptions[2]} · (D) {log.currentOptions[3]}
                                                </TableCell>
                                            <TableCell align="center" style={{backgroundColor:log.result}}>{log.option}</TableCell>
                                            <TableCell align="center" style={{backgroundColor:log.result}}>{log.currentWord}</TableCell>
                                        </TableRow>
                                        );
                                    })}
                                </TableBody>
                            </Table>
                        </TableContainer>{}
                        <Button className={classes.restartButton} variant="outlined" onClick={()=>this.reset()}>Click to Restart Test!</Button>
                    </Grid>
                }
                </Grid>
            </Grid>
        );
    };

    getStepContent = (index) => {
        switch (index) {
            case 0:
                return this.getSettingOption();
            case 1:
                return this.get4AFCTest();
            default:
                return;
        }
    };

    handleStepChange = async (change) => {
        await this.setState((prevState) => ({
            step: prevState.step + change,
        }));
        if (this.state.step === 1){
            this.fetchWord();
        }
    };

    render() {
        const { classes } = this.props;
        const { step } = this.state;
        return (
            <Paper className={classes.paper}>
                <Stepper orientation="vertical" activeStep={step}>
                    {[
                        "Settings for 4AFC Test",
                        "4AFC Test",
                    ].map((label, index) => (
                        <Step key={label}>
                            <StepLabel>{label}</StepLabel>
                            <StepContent>
                                {this.getStepContent(index)}
                                <div className={classes.actionsContainer} style={{display: 'flex', flexDirection: 'row'}}>
                                    {step === 1 ? (
                                        <Grid></Grid>
                                    ) : (
                                        <Button
                                            variant="contained"
                                            color="primary"
                                            disabled={step === 1 && this.state.maxSubstepReached < 5}
                                            onClick={() =>
                                                this.handleStepChange(1)
                                            }
                                        >
                                            Next
                                        </Button>
                                    )}
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
)(fourAFCDemo);


/* for audio repeat and audio delay, not currently possible/ needed

helper function for the drop down menu for delay time
handleDelayChange = async (event) => {
    this.setState({
        delay: event.target.value,
    });
};

<Box mt={2} lineHeight="normal" fontSize={18} >  
    Audio Delay:
</Box>
<Box mt={1} lineHeight="normal" fontSize={15} >  
    Select the delay before audio is played (deafult one second).
</Box>
<Grid>
    <FormControl className={classes.formControl}>
        <InputLabel id="demo-simple-select-label">Audio Delay</InputLabel>
        <Select
            labelId="demo-simple-select-label"
            id="demo-simple-select"
            value={delay}
            onChange={this.handleDelayChange}
        >
            <MenuItem value={0}><em>None</em></MenuItem>
            <MenuItem value={1000}>One Second</MenuItem>
            <MenuItem value={2000}>Two Seconds</MenuItem>
            <MenuItem value={3000}>Three Seconds</MenuItem>
        </Select>
    </FormControl>
</Grid>

<Box mt={2} lineHeight="normal" fontSize={18} >  
    Audio Repeat:
</Box>
<Box mt={1} mb={1} lineHeight="normal" fontSize={15} >  
    Choose between single play upon press and audio on loop (default single play).
</Box>
<Grid item>
    <FormControl component="fieldset">
        <RadioGroup aria-label="audiorepeat" name="audiorepeat" value={audioRepeat} onChange={this.handleRepeatChange}>
            <FormControlLabel value={true} control={<Radio />} label="Loop Audio" />
            <FormControlLabel value={false} control={<Radio />} label="Single Play Upon Press" />
        </RadioGroup>
    </FormControl>
</Grid>

handleRepeatChange = (event) => {
    this.setState({
        audioRepeat: event.target.value,
    });
};

handleRepeatChange = async () => {
    const {
        audioRepeat,
    } = this.state;
    await this.setState({ 
        audioRepeat: !audioRepeat,
    });
    this.handlePlay();
}
*/