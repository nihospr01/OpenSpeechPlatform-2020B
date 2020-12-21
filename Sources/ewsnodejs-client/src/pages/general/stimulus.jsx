import React, { Component } from "react";
import { axios } from "utils/utils";
import { withStyles } from "@material-ui/styles";
import Grid from "@material-ui/core/Grid";
import ToggleButton from "@material-ui/lab/ToggleButton";
import AudioFilePlayer from "components/audioFilePlayer"

const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    content: {
        maxWidth: 480,
        minWidth: 200,
        margin: 'auto',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
    },
    groupButton: {
        width: "auto",
        minWidth: 200,
        color: "gray",
        margin: 'auto'
    },
});

const options = ["live", "Audio file"];

class Stimulus extends Component {
    state = {
        // 0 - audio, 1 - live file
        alpha: 1,
        file: 0,  // TODO: Consider deleting 'file', not sure what is the use of this
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
            console.log(fileResponse.data);
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
        } catch (error) {
            alert(error.response.data);
        }
    };

    // Handle change for all play icons and stimulus options
    handlePlayChange = async (type, value) => {
        const {
            currentFile,
            alpha,
            audioPath,
            audio_repeat,
            audio_play,
        } = this.state;
        let filePath = "";

        // Switch between live data and stimulus file,
        if (type === "alpha") {
            filePath = value === 1 ? audioPath.concat(currentFile) : "";
        } else {
            filePath = alpha === 1 ? audioPath.concat(currentFile) : "";
        }
        let audioData = {
            audio_filename: filePath,
            audio_reset:0,
            audio_repeat,
            audio_play,
            alpha,
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

        } catch (error) {
            alert(error.response.data);
        }
    };

    render() {
        const { classes } = this.props;
        const { alpha, audio_play, audio_repeat } = this.state;
        return (
            <div className={classes.root}>

                <Grid container spacing={3} className={classes.content}>
                    <Grid item style={{width: 'auto'}}>
                        {options.map((value, index) => (
                            <ToggleButton
                                color="primary"
                                className={classes.groupButton}
                                key={`stimulus_${index}`}
                                value={index}
                                selected={index === alpha}
                                onClick={() =>
                                    this.handlePlayChange("alpha", index)
                                }
                            >
                                {value}
                            </ToggleButton>
                        ))}
                    </Grid>

                    <Grid item>
                    {alpha === 1 && (
                        <AudioFilePlayer/>
                    )}
                    </Grid>
                </Grid>

            </div>
        );
    }
}

export default withStyles(styles)(Stimulus);
