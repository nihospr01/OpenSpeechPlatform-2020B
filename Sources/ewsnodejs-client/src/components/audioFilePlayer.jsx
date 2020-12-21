import React, { Component } from "react";
import { axios } from "utils/utils";
import { withStyles } from "@material-ui/styles";
import Paper from '@material-ui/core/Paper';
import Grid from "@material-ui/core/Grid";
import Typography from "@material-ui/core/Typography";
import ToggleButton from "@material-ui/lab/ToggleButton";
import PauseIcon from "@material-ui/icons/Pause";
import PlayArrowIcon from "@material-ui/icons/PlayArrow";
import SyncDisabledIcon from "@material-ui/icons/SyncDisabled";
import LoopIcon from "@material-ui/icons/Loop";
import StopIcon from "@material-ui/icons/Stop";
import Select from "react-select";
import Switch from '@material-ui/core/Switch';
import { AutoComplete } from "antd";

const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    content: {
        padding: theme.spacing(3),
        width: 'auto',
        minWidth: 350,
        maxWidth: 600,
        // margin: 'auto',
        backgroundColor: '#7FFFD4'
    },
    icon: {
        width: 50,
        height: 50,
    },
    groupButton: {
        width: 320,
        color: "gray",
    },
});

// const options = ["live", "Audio file"];
const defaultTranscript = "This file has no transcript.";
const checkWordFreq = 10;

class AudioFilePlayer extends Component {
    state = {
        // 0 - audio, 1 - live file
        // alpha: 1,
        fileList: [],
        currentFile: "",
        currentCaption: "",
        audioPath: "",
        audio_repeat: 0,
        audio_play: 0,
        showTranscript: false,
        playbackStartTime: 0,
        isFilePlaying: 0,
        prevWords: [],
        currWord: "",
        restWords: []
    };

    componentDidMount = async () => {
        try {
            const fileResponse = await axios.get("/api/researcher/fileNames");
            const pathResponse = await axios.get("/api/researcher/audioPath");
            let fileList = [];
            let audioPath = pathResponse.data;
            fileResponse.data.forEach((element) => {
                fileList.push(
                    {
                        value: element.value,
                        label: element.value,
                        transcript: element.transcript
                    }
                );
            });

            this.setState({ fileList, audioPath });
            setInterval(this.getCurrentWord, checkWordFreq);
            setInterval(this.updateParam, 100);
            // this.updateParam();
        } catch (error) {
            alert(error.response.data);
        }
    };

    componentWillUnmount = async () => {
        this.pause();
    }

    updateParam = async () => {
        try {
            const response = await axios.post("/api/param/getparam");
            let data = response.data;
            // console.log(data.left.audio_playing);
            this.setState({
                audio_play: data.left.audio_playing,
                isFilePlaying: data.left.audio_playing,
                audio_repeat: data.left.audio_repeat,
            });
        } catch (error) {
            throw error;
        }
    }

    handleFileChange = async (file) => {
        this.pause();

        this.setState({
            currentFile: file.value,
        });

        if(this.props.onChangeAudioFile){
            this.props.onChangeAudioFile(file.value);
        } 

        // If the file doesn't have transcript, set currentCaption as default message. The
        // reason we pair the message with 'punct' key is to make the render more conveniently.
        if (!file.transcript) {
            console.log("No transcript found");
            this.setState({
                currentCaption: {punct: defaultTranscript},
                isFilePlaying: 0,
                prevWords: [],
                currWord: "",
                restWords: defaultTranscript
            });
            return;
        }
        // If the file has transcript, get the transcript.
        try {
            const response = await axios.post("/api/researcher/jsonFile", {data: this.state.audioPath.concat(file.transcript)});
            // console.log(response.data);
            this.setState({
                currentCaption: response.data,
                isFilePlaying: 0,
                prevWords: [],
                currWord: "",
                restWords: response.data.punct
            });
        } catch (error) {
            alert(error.response.data);
        }
    };

    getCurrentWord = () => {
        if (!this.state.currentFile || !this.state.currentCaption) {
            return;
        }

        if (this.state.isFilePlaying === 0) {
            return;
        } else if (this.state.currentCaption.punct === defaultTranscript) {
            return;
        } else {
            const words = this.state.currentCaption.punct.split(' ');
            const wordsInfo = this.state.currentCaption.words;
            const fileLength = this.state.currentCaption.length;
            const currentTime = Date.now();
            const timeLapsed = (currentTime - this.state.playbackStartTime) % fileLength;
            // console.log(timeLapsed); // Why is it not a floating point number sometimes?
            let index = 0;
            while (index < wordsInfo.length - 1 && timeLapsed > wordsInfo[index + 1].start) {
                index++;
            }

            let prevWords = "";
            let currWord = "";
            let restWords = "";
            if (index === 0) {
                prevWords = words.slice(0, index).join(' ');
                currWord = words[index] + " ";
                // Assume there are more than 1 word, i.e. the max index > 0.
                restWords = words.slice(index + 1).join(' ');
            } else if (index === wordsInfo.length - 1) {
                prevWords = words.slice(0, index).join(' ') + " ";
                currWord = words[index];
                restWords = "";               
            } else {
                prevWords = words.slice(0, index).join(' ') + " ";
                currWord = words[index] + " ";
                restWords = words.slice(index + 1).join(' ');
            }
            this.setState({prevWords, currWord, restWords});
        }
    };

    handleCaptionToggle = (event) => {
        this.setState({
            showTranscript: event.target.checked
        });
    }

    playFile = async () => {
        // Check if there is any preparation to do, like sending some HA parameters,
        // before we send the audio file data to RT-MHA
        const {preparePlay} = this.props;
        if (preparePlay) {
            preparePlay();
        }

        const {
            currentFile,
            audioPath,
        } = this.state;

        // Prepare the data to send to RT-MHA
        const filePath = audioPath.concat(currentFile);
        const audioData = {
            audio_filename: filePath,
            alpha: 1,
            audio_play: 1
        };
        const dataToSend = {
            left: audioData,
            right: audioData
        };

        try {
            // Send data to RT-MHA
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: dataToSend,
            });
            const data = response.data;
            console.log(data);

            // Set the states for closed captioning feature
            this.setState({
                audio_play: 1,
                isFilePlaying: 1,
                playbackStartTime: Date.now() // Get the current time as the start point at which we start to play the file
            });
        } catch (error) {
            alert(error.response.data);
        }
    }

    pause = async () => {
        const {
            currentFile,
            audioPath
        } = this.state;

        // Prepare the data to send to RT-MHA
        const filePath = audioPath.concat(currentFile);
        const audioData = {
            audio_filename: filePath,
            audio_play: 0,
        };
        const dataToSend = {
            left: audioData,
            right: audioData
        };

        try {
            // Send data to RT-MHA
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: dataToSend,
            });
            const data = response.data;
            console.log(data);

            this.setState({
                audio_play: 0,
                isFilePlaying: 0
            });
        } catch (error) {
            alert(error.response.data);
        }
    }

    handleRepeat = async () => {
        const {
            currentFile,
            audioPath
        } = this.state;

        // Prepare the data to send to RT-MHA
        const filePath = audioPath.concat(currentFile);
        const audioData = {
            // audio_filename: filePath,
            // alpha: 1,
            audio_repeat: 1,
            audio_play: 1
        };

        const dataToSend = {
            left: audioData,
            right: audioData
        };

        try {
            const response = await axios.post("/api/param/setParam", {
                method: "set",
                data: dataToSend
            });
            console.log(response.data);

            this.setState({
                audio_repeat: 1,
                audio_play: 1,
                isFilePlaying: 1,
                playbackStartTime: Date.now()
            })
        } catch (error) {
            alert(error.repsonse.data);
        }
    }

    cancelRepeat = async () => {
        const {
            currentFile,
            audioPath
        } = this.state;

        // Prepare the data to send to RT-MHA
        const filePath = audioPath.concat(currentFile);
        const audioData = {
            // audio_filename: filePath,
            audio_repeat: 0,
        };
        const dataToSend = {
            left: audioData,
            right: audioData
        };

        try {
            // Send data to RT-MHA
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: dataToSend,
            });
            const data = response.data;
            console.log(data);

            this.setState({
                audio_repeat: 0,
                audio_play: 0,
                isFilePlaying: 0
            });
        } catch (error) {
            alert(error.response.data);
        }
    }

    // Handle change for all play icons and stimulus options
    handlePlayChange = async (type, value) => {
        const {
            currentFile,
            // alpha,
            audioPath,
            audio_repeat,
            audio_play,
        } = this.state;
        let filePath = audioPath.concat(currentFile);

        // Switch between live data and stimulus file,
        // if (type === "alpha") {
        //     filePath = value === 1 ? audioPath.concat(currentFile) : "";
        // } else {
        //     filePath = alpha === 1 ? audioPath.concat(currentFile) : "";
        // }
        let audioData = {
            audio_filename: filePath,
            audio_reset:0,
            audio_repeat,
            audio_play,
            // alpha,
            //Add new value to override old value
            [type]: value,
        };
        const dataInput = {
            left: audioData,
            right: audioData,
        };
        try {
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: dataInput,
            });
            const data = response.data;
            console.log(data);
            this.setState({
                [type]: value,
            });
            console.log(this.state);

            if (type === 'audio_play' && value === 1) {
                this.setState({
                    isFilePlaying: 1,
                    playbackStartTime: Date.now()
                });
            }
        } catch (error) {
            alert(error.response.data);
        }
    };

    render() {
        const { classes } = this.props;
        const { alpha, audio_play, audio_repeat } = this.state;
        return (
            <div className={classes.root}>
                <Grid container direction="column" className={classes.content}>
                    <Grid item>
                        <Select
                            onChange={this.handleFileChange}
                            options={this.state.fileList}
                        />
                    </Grid>
                    <Grid item>
                        <div>
                            Show Transcript
                            <Switch
                                checked={this.state.showTranscript}
                                onChange={this.handleCaptionToggle}
                                color="primary"
                            />
                        </div>
                    </Grid>
                    <Grid item>
                        {this.state.showTranscript && (
                            <p>
                                {this.state.prevWords}
                                <font color="red">{this.state.currWord}</font>
                                {this.state.restWords}
                            </p>
                        )}
                    </Grid>
                    <Grid
                        container
                        spacing={3}
                        justify="space-between"
                        alignItems="center"
                    >
                        <div>
                            {audio_repeat === 0 ? (
                                <SyncDisabledIcon
                                    // onClick={() =>
                                    //     this.handlePlayChange(
                                    //         "audio_repeat",
                                    //         1
                                    //     )
                                    // }
                                    onClick={this.handleRepeat}
                                    className={classes.icon}
                                />
                            ) : (
                                <LoopIcon
                                    // onClick={() =>
                                    //     this.handlePlayChange(
                                    //         "audio_repeat",
                                    //         0
                                    //     )
                                    // }
                                    onClick={this.cancelRepeat}
                                    className={classes.icon}
                                />
                            )}
                            <Typography>
                                {audio_repeat === 1
                                    ? "Repeat"
                                    : "Stop Repeat"}
                            </Typography>
                        </div>

                        <div>
                            {audio_play === 0 ? (
                                <PlayArrowIcon
                                    className={classes.icon}
                                    onClick={this.playFile}
                                />
                            ) : (
                                <PauseIcon
                                    onClick={this.pause}
                                    className={classes.icon}
                                />
                            )}
                            <Typography>{"Play/Pause"}</Typography>
                        </div>

                        <div>
                            <StopIcon
                                className={classes.icon}
                                onClick={() =>
                                    this.handlePlayChange("audio_reset", 1)
                                }
                            />
                            <Typography>{"Reset"}</Typography>
                        </div>
                    </Grid>
                </Grid>
            </div>
        );
    }
}

export default withStyles(styles)(AudioFilePlayer);
