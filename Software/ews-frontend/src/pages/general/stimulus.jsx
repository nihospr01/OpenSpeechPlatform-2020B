import React, { Component } from "react";
import { axios } from "utils/utils";
import { withStyles } from "@material-ui/styles";
import Paper from '@material-ui/core/Paper';
import Grid from "@material-ui/core/Grid";
import Typography from "@material-ui/core/Typography";
import ToggleButton from "@material-ui/lab/ToggleButton";
import Box from "@material-ui/core/Box";
import PauseIcon from "@material-ui/icons/Pause";
import PlayArrowIcon from "@material-ui/icons/PlayArrow";
import SyncDisabledIcon from "@material-ui/icons/SyncDisabled";
import LoopIcon from "@material-ui/icons/Loop";
import StopIcon from "@material-ui/icons/Stop";
import Select from "react-select";

const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    content: {
        padding: theme.spacing(3),
        maxWidth: 480,
        minWidth: 200,
        margin: 'auto'
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

const options = ["live", "Audio file"];

class Stimulus extends Component {
    state = {
        // 0 - audio, 1 - live file
        alpha: 1,
        file: 0,
        fileList: [],
        currentFile: "",
        audioPath: "",
        audio_repeat: 0,
        audio_play: 0,
    };

    componentDidMount = async () => {
        try {
            const fileResponse = await axios.get("/api/researcher/fileNames");
            const pathResponse = await axios.get("/api/researcher/audioPath");
            let fileList = [];
            let audioPath = "/../../.." + pathResponse.data;
            fileResponse.data.forEach((element) => {
                if (element.endsWith(".wav")) {
                    fileList.push({
                        value: element,
                        label: element,
                    });
                }
            });

            this.setState({ fileList, audioPath });
        } catch (error) {
            alert(error.response.data);
        }
    };

    handleFileChange = (file) => {
        this.setState({
            currentFile: file.value,
        });
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
                <Paper >
                <Grid container direction="column" spacing={5} className={classes.content}>
                    {/* <Typography component="div">
                        <Box lineHeight={2}>Stimulus Options:</Box>
                    </Typography> */}
                    <Grid
                        container
                        direction="column"
                        justify="center"
                        alignItems="center"
                    >
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
                            <Select
                                onChange={this.handleFileChange}
                                options={this.state.fileList}
                            />
                        )}
                    </Grid>
                    <Grid
                        container
                        spacing={3}
                        justify="space-between"
                        alignItems="center"
                    >
                        {alpha === 1 && (
                            <div>
                                {audio_repeat === 0 ? (
                                    <SyncDisabledIcon
                                        onClick={() =>
                                            this.handlePlayChange(
                                                "audio_repeat",
                                                1
                                            )
                                        }
                                        className={classes.icon}
                                    />
                                ) : (
                                    <LoopIcon
                                        onClick={() =>
                                            this.handlePlayChange(
                                                "audio_repeat",
                                                0
                                            )
                                        }
                                        className={classes.icon}
                                    />
                                )}
                                <Typography>
                                    {audio_repeat === 1
                                        ? "Repeat"
                                        : "Stop Repeat"}
                                </Typography>
                            </div>
                        )}

                        {alpha === 1 && (
                            <div>
                                {audio_play === 0 ? (
                                    <PlayArrowIcon
                                        className={classes.icon}
                                        onClick={() =>
                                            this.handlePlayChange(
                                                "audio_play",
                                                1
                                            )
                                        }
                                    />
                                ) : (
                                    <PauseIcon
                                        onClick={() =>
                                            this.handlePlayChange(
                                                "audio_play",
                                                0
                                            )
                                        }
                                        className={classes.icon}
                                    />
                                )}
                                <Typography>{"Play / Pause"}</Typography>
                            </div>
                        )}

                        {alpha === 1 && (
                            <div>
                                <StopIcon
                                    className={classes.icon}
                                    onClick={() =>
                                        this.handlePlayChange("audio_reset", 1)
                                    }
                                />
                                <Typography>{"Reset"}</Typography>
                            </div>
                        )}
                    </Grid>
                </Grid>
                </Paper>
            </div>
        );
    }
}

export default withStyles(styles)(Stimulus);
