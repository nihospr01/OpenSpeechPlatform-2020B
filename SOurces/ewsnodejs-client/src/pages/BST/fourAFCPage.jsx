import React, { Component } from 'react';
import { withStyles } from '@material-ui/styles';
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import Button from '@material-ui/core/Button';
import PlayCircleOutlineIcon from '@material-ui/icons/PlayCircleOutline';
import IconButton from '@material-ui/core/IconButton';
import { axios } from 'utils/utils';
import { Link } from 'react-router-dom';
import withUserAuth from 'context/withUserAuth';
import { withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import HelpIcon from "@material-ui/icons/Help";
import Tooltip from "@material-ui/core/Tooltip";

const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    paper: {
        height: '90vh',
    },
    button: {
        width: '80vw',
        height: 50,
        margin: 10,
    },
    grid: {
        marginLeft: 10,
        fontSize: 16,
    },
    margin: {
        margin: 10,
    },
    extendedIcon: {
        marginRight: 8,
    },
    buttonGroup: {
        width: 100,
        height: 100,
    },
});

class fourAFCPage extends Component {
    state = {
        completed: false,
        currentWord: '',
        audioConfig: [],
        audioPath: '',
        currentAudioFile: '',
        currentOptions: ['', '', '', ''],
        currentCount: 0,
        logs: []
    };

    componentDidMount = async () => {
        try {
            const { user, loginMode } = this.props.context;
            if (loginMode !== 'researcher') {
                const parameterResponse = await axios.get(`/api/listener/getParameters/${user}`);
                this.apply(parameterResponse.data);
            }
            const configResponse = await axios.get("/api/researcher/4AFCConfig");
            const audioConfig = configResponse.data["words"];
            const pathResponse = await axios.get("/api/researcher/audioPath");
            const audioPath = pathResponse.data;
            await this.setState({ audioConfig, audioPath });
            this.fetchWord();
        }
        catch(error) {
            console.log(error);
            alert(error.response.data);
        }
    }

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

    handlePlay = async () => {
        const {
            audioPath,
            currentAudioFile,
        } = this.state;
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

    fetchWord = () => {
        const {
            audioConfig,
        } = this.state;
        console.log(audioConfig)
        const index = Math.floor(Math.random()*audioConfig.length);
        const currentOption = audioConfig[index];
        audioConfig.splice(index, 1);
        this.setState({
            audioConfig: audioConfig,
            currentWord: currentOption["answer"],
            currentOptions: [
                currentOption["option_A"],
                currentOption["option_B"],
                currentOption["option_C"],
                currentOption["option_D"],
            ],
            currentCount: this.state.currentCount + 1,
            currentAudioFile: currentOption["filename"],
        });
    }

    handleContinue = (option) => {
        let {
            currentCount,
            currentOptions,
            currentWord,
            logs,
        } = this.state;
        console.log(logs)
        logs.push({
            currentOptions,
            currentWord,
            option
        });
        this.setState({
            logs,
        })
        if (currentCount >= 5) {
            this.setState({
                completed: true,
            });
        } else {
            this.fetchWord();
        }
    }

    handleSubmit = async () => {
        const { updateUser, user, loginMode } = this.props.context;
        const { logs } = this.state;
        if (loginMode === "researcher") {
            return;
        }
        try {
            console.log(logs)
            await axios.post("/api/listener/addLog", {
                listenerID: user,
                newLog: {
                    AFC: {
                        logOn:new Date().toLocaleString(),
                        log: logs
                    }
                },
                flag: 'AFCDone',
                flagValue: true,
            });
        }
        catch (error) {
            alert(error.message);
            console.log(error);
        }
        sessionStorage.setItem('AFCDone', true);
        updateUser();
    }

    render() {
        const { classes } = this.props;
        const { 
            completed,
            currentOptions,
        } = this.state;

        return (
            <Paper className={classes.paper}>
                <Grid container spacing={1}>
                    <Grid item xs={12} className={classes.grid}>
                        Please click the word you heard:
                        <Tooltip title="In this section, hit the play button, then choose the word that matches best to the audio">
              <IconButton aria-label="help">
                <HelpIcon />
              </IconButton>
            </Tooltip>
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
                            <PlayCircleOutlineIcon className={classes.buttonGroup}/>
                        </IconButton>
                    </Grid>
                    {
                        !completed ? 
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
                                onClick={() => this.handleContinue('A')}
                            >
                                {currentOptions[0]}
                            </Button>
                            <Button
                                className={classes.button} 
                                variant="outlined" 
                                color="primary"
                                onClick={() => this.handleContinue('B')}
                            >
                                {currentOptions[1]}
                            </Button>
                            <Button
                                className={classes.button} 
                                variant="outlined" 
                                color="primary"
                                onClick={() => this.handleContinue('C')}
                            >
                                {currentOptions[2]}
                            </Button>
                            <Button
                                className={classes.button} 
                                variant="outlined" 
                                color="primary"
                                onClick={() => this.handleContinue('D')}
                            >
                                {currentOptions[3]}
                            </Button>
                        </Grid> :
                        <Grid 
                            container
                            direction="column"
                            justify="flex-end"
                            alignItems="center"
                        >
                            You are finished.
                            {
                                <Button
                                    className={classes.button} 
                                    variant="outlined" 
                                    color="primary"
                                    onClick={this.handleSubmit}
                                    component={Link}
                                    to="/researcherpage"
                                >
                                    Back to main menu
                                </Button>
                            }
                        </Grid>
                    }
                </Grid>
            </Paper>
        );
    }
}

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(fourAFCPage);
