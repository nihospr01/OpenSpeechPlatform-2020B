import React, { Component } from "react";
import { axios } from "utils/utils";
import { withRouter, Link } from "react-router-dom";
import withUserAuth from "context/withUserAuth";
import { compose } from "recompose";
import { withStyles } from "@material-ui/styles";
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import Paper from "@material-ui/core/Paper";
import Slider from '@material-ui/core/Slider';
import Input from '@material-ui/core/Input';
import Button from '@material-ui/core/Button';
import SettingsIcon from '@material-ui/icons/Settings';
import DialogTitle from '@material-ui/core/DialogTitle';
import Dialog from '@material-ui/core/Dialog';
import Select from '@material-ui/core/Select';
import MenuItem from '@material-ui/core/MenuItem';
import TextField from '@material-ui/core/TextField';

import AudioFilePlayer from "components/audioFilePlayer"

const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    background: {
        width: "100%",
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',

    },
    container: {
        width: "auto",
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        // padding: 20,
    },
    iconHover: {
        float: 'right',
        '&:hover': {
          color: "grey",
        },
        // alignItems: "right",
        color: "inherit",
    },
    sliderLabel: {
        display: 'flex',
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        width: "auto",
        marginTop: 20
    },
    sliderRow: {
        display: 'flex',
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        width: 'auto',
        minWidth: 400,
    },
    title: {
        marginTop: 40,
    },
    freqLabel: {
        width: "20%",
        marginRight: 10
    },
    inputField: {
        width: "20%",
        marginLeft: 20,
    },
    bottomButtonArea: {
        marginTop: 30,
        display: 'flex',
        width: "100%",
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItmes: 'center'
    },
    submitButton: {
        width: 200
    },
    settingsBar: {
        display: 'flex',
        flexDirection: 'row',
        justifyContent: 'flex-end',
        width: "100%"
    },
    settingsWindow: {
        width: "100%",
        paddingLeft: 30,
        paddingRight: 30,
        paddingBottom: 30,
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'flex-start'
    },
    volumeTitle: {
        display: 'flex',
        flexDirection: 'row',
        justifyContent: 'space-between'
    },
    saveSettingsButton: {
        alignSelf: 'center'
    },
    alertWindow: {
        width: 400,
        display: 'flex',
        justifyContent: 'center',
        paddingLeft: 30,
        paddingRight: 30,
        paddingBottom: 20
    },
    switchMsgWindow: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        padding: 20,
    }
});

const sixBandFreq = ['250', '500', '1000', '2000', '4000', '8000'];
const tenBandFreq = ['250', '500', '750', '1000', '1500', '2000', '3000', '4000', '6000', '8000'];
// The number of bands the user can set as non-zero values at a time
const bandLimit = 3;
// Marks for alpha sliders
const gainSliderMarks = [{value: -20, label: '-20'}, {value: 10, label: '10'}];

class FreppingDemo extends Component {
    constructor(props) {
        super(props);
        // Don't call this.setState() here!
        this.state = {
            showAlert: false,
            freppingAlpha: [0, 0, 0, 0, 0, 0],
            // bandsWarped: 0,
            reachedFrepLimit: false,
            openSettings: false,
            tenBand: false,
            gain: 0,
            showBandSwitchMsg: false,
            minAlpha: -0.5,
            maxAlpha: 0.5,
            alphaRange: [-0.5, 0.5],
            shouldChangeColor: [false, false, false, false, false, false],
            showDes: false
        };
      }

    componentDidMount = async () => {
        try{
            await axios.get("/api/db/frepingDemo/")
            // await this.updateParam();
        } catch(error){
            if (error.response.data == "table not found"){
                await axios.post("/api/db/table/create/frepingDemo");
            }
            throw error;
        }
    }

    /**
     * This function loads the HA profile last saved in the database.
     */
    updateParam = async () => {
        try {
            let url = "/api/db/frepingDemo/" + (this.state.tenBand ? "ten_band" : "six_band");
            const paramsResponse = await axios.get(url);
            // console.log(paramsResponse.data);
            let params = paramsResponse.data;


            // // Call the server API which returns the HA profile
            // const paramsResponse = await axios.get("/api/param/amplification");
            // console.log(paramsResponse.data);
            // // We are using magic numbers for indice of the current profile in the db.
            // let params;
            // if (this.state.tenBand) {
            //     params = JSON.parse(paramsResponse.data[3].parameters);
            // } else {
            //     params = JSON.parse(paramsResponse.data[4].parameters);
            // }

            console.log(params);
            this.checkBandLimit(params.left.freping_alpha);
            this.setState({
                freppingAlpha: [...params.left.freping_alpha],
                gain: params.left.gain
            }, () => {
                this.sendParamsToMHA();
                this.resetShouldChangeColor();
            });
        } catch (error) {
            // this.handleAlertOpen();
            // if (error.response.data == "key not found") {
            //     await axios.
            // }
            throw error;
        }
    }

    /**
     * Handles the close of the alert caused by the MHA error.
     */
    handleAlertClose = () => {
        this.setState({showAlert: false});
    }

    /**
     * Handles the open of the alert window caused by the MHA error.
     */
    handleAlertOpen = () => {
        this.setState({showAlert: true});
    }

    /** 
     * Checks how many bands the user has set to non-zero.
     */
    checkBandLimit = (arr) => {
        let count = 0;
        let i;
        for (i = 0; i < arr.length; i++) {
            if (arr[i] != 0) {
                count++;
            }
        }
        this.setState({
            // bandsWarped: count,
            reachedFrepLimit: count >= 3 ? true : false
        });
    }

    /** 
     * Handles the change from each input field for its corresponding alpha.
     * 
     * @param {*} event the event object sent by input component.
     */
    handleInputChange = async (event) => {
        // The index of input fields, which represents a particular channel that the
        // user made change to.
        const idx = event.target.id;
        const value = event.target.value;
        let newValue;
        // Sanity check of user's input: limit each alpha inside the bound.
        if (value) {
            newValue = Number(value);
            if (newValue > this.state.maxAlpha) {
                newValue = this.state.maxAlpha;
            } else if (newValue < this.state.minAlpha) {
                newValue = this.state.minAlpha;
            }
        } else {
            newValue = 0;
        }

        // Update the frepping state.
        let tempArray = this.state.freppingAlpha;
        // const oldAlpha = tempArray[idx];
        tempArray[idx] = newValue;
        this.setState((prevState) => {
            console.log(prevState);    // Why is this prevState the same as the new state?
            // this.checkFreppingLimit(oldAlpha, newValue);
            this.checkBandLimit(tempArray);
            return {freppingAlpha: tempArray}
        }, () => {
            this.sendParamsToMHA();
            this.handleColorChange(idx, true);
        });
    }

    /**
     * Handles the slider's change.
     * @param {*} event event object sent by slider component
     * @param {*} newValue the new value that the user just set for the slider
     */
    handleSliderChange = async (newValue, index) => {
        // console.log(newValue);
        // console.log(index);
        // const idx = event.target.id;
        let tempArray = this.state.freppingAlpha;
        // const oldAlpha = tempArray[idx];
        tempArray[index] = newValue;
        this.setState({freppingAlpha: tempArray});
    }

    handleSliderChangeDone = async (newValue, index) => {
        this.checkBandLimit(this.state.freppingAlpha);
        this.sendParamsToMHA();
        this.handleColorChange(index, true);
    }

    /**
     * Change the color for components (text and slider) of a particular band
     * that the user has made change to but hasn't saved the change to the database.
     * @param {*} index The index of the band that the user changed
     * @param {*} status The bool value that needs giving to the bool stored in the
     *                   shouldChangeColor array with the given index
     */
    handleColorChange = (index, status) => {
        let tempArray = this.state.shouldChangeColor;
        tempArray[index] = status;
        this.setState({
            shouldChangeColor: tempArray
        });
    }

    // checkFreppingLimit = async (oldValue, newValue) => {
    //     // console.log(oldValue.toString() + ' ' + newValue.toString());
    //     if (oldValue === 0 && newValue !== 0) {
    //         this.setState((prevState) => {
    //             return {bandsWarped: prevState.bandsWarped + 1};
    //         }, () => {
    //             console.log('bandsWarped: ' + this.state.bandsWarped.toString());
    //             if (this.state.bandsWarped === bandLimit) {
    //                 this.setState({
    //                     reachedFrepLimit: true
    //                 });
    //             }
    //         });
    //     } else if (oldValue !== 0 && newValue === 0) {
    //         this.setState((prevState) => {
    //             return {bandsWarped: prevState.bandsWarped === 0 ? 0 : prevState.bandsWarped - 1};
    //         }, () => {
    //             console.log('bandsWarped: ' + this.state.bandsWarped.toString());
    //             this.setState({
    //                 reachedFrepLimit: false
    //             });
    //         });
    //     }
    // }

    /**
     * Reset all bools stored in the shouldChangeColor array to be false, called when
     * the user clicks the 'Submit' button (when the changes the user made have been
     * saved in the database).
     */
    resetShouldChangeColor = () => {
        let i;
        let tempArray = this.state.shouldChangeColor;
        for (i = 0; i < tempArray.length; i++) {
            tempArray[i] = false;
        }
        this.setState({
            shouldChangeColor: tempArray
        });
    }

    /**
     * Called when the user clicked the submit button. Basically it sends the params (in this
     * demo app referring to frepping alpha's) to the database.
     */
    onClickSubmit = async () => {
        // Prepare the data
        const params = this.prepareData();
        console.log(this.state.tenBand);
        console.log(this.state.freppingAlpha);
        console.log(params);

        this.resetShouldChangeColor();

        try {
            // Save the data in the database.
            const response = await axios.post("/api/db/frepingDemo", {
                key: this.state.tenBand ? "ten_band" : "six_band",
                value: JSON.stringify(params)
            });
            console.log(response.data);
        } catch (error) {
            // this.handleAlertOpen();
            console.log(error.response.data);
        }
    }

    prepareData = () => {
        const {freppingAlpha, gain} = this.state;
        const params = {
            left: {
                freping: 1,
                freping_alpha: freppingAlpha,
                gain: gain
            },
            right: {
                freping: 1,
                freping_alpha: freppingAlpha,
                gain: gain
            }
        };
        return params;
    }

    sendParamsToMHA = async () => {
        const params = this.prepareData();
        console.log(params);
        try {
            // Send the data to RT-MHA
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: params,
            });
            console.log(response.data);
        } catch (error) {
            // this.handleAlertOpen();
            console.log(error.response.data);
        }
    }

    /**
     * handle the settings page's close.
     */
    handleSettingsClose = () => {
        this.setState({
            openSettings: false
        });

        // Check whether any alpha value is out of the updated Range
        this.checkOutOfRange();
    }

    /**
     * Handles the settings page's open
     */
    handleSettingsClick = () => {
        this.setState({
            openSettings: true
        });
    }

    /**
     * If the user changes the range for the alpha, then all the current alpha values
     * which are out of the updated range should be pushed inside the new range.
     */
    checkOutOfRange = async () => {
        let temp = this.state.freppingAlpha;
        let i;
        // Push every alpha into the updated range.
        for (i = 0; i < temp.length; i++) {
            if (temp[i] < this.state.minAlpha) {
                temp[i] = this.state.minAlpha;
                this.handleColorChange(i, true);
            } else if (temp[i] > this.state.maxAlpha) {
                temp[i] = this.state.maxAlpha;
                this.handleColorChange(i, true);
            }
        }
        // this.setState({
        //     freppingAlpha: temp
        // }, this.sendParamsToMHA);
        this.setState({
            freppingAlpha: temp
        }, this.sendParamsToMHA)
    }

    /**
     * Handles the band mode switch
     * @param {*} event event object sent by the select component. Here, event.target.value -- 0 means
     *                  the user is using the 6-band mode, 1 means the user is using the 10-band mode.
     */
    handleBandSwitch = async (event) => {
        if (event.target.value) {
            this.setState({
                freppingAlpha: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                shouldChangeColor: [false, false, false, false, false, false, false, false, false, false]
            });
        } else {
            this.setState({
                freppingAlpha: [0, 0, 0, 0, 0, 0],
                shouldChangeColor: [false, false, false, false, false, false]
            });
        }

        this.setState({
            tenBand: event.target.value,
            // bandsWarped: 0,
            reachedFrepLimit: false
        }, ()=>{
            this.updateParam();
        });

        // Show the switch msg to the user.
        this.handleSwitchMsgOpen();

        try {
            const response = await axios.post("/api/researcher/restartRTMHA", {data: event.target.value});
            console.log(response.data);
        } catch (error) {
            console.log(error);
        }
    }

    /**
     * Close the confirmation message window for band mode switch.
     */
    handleSwitchMsgClose = () => {
        this.setState({
            showBandSwitchMsg: false
        });
    }

    /**
     * Open the confirmation message window for band mode switch.
     */
    handleSwitchMsgOpen = () => {
        this.setState({
            showBandSwitchMsg: true
        });
    }

    /**
     * Handle the change for gaim (volume)
     * @param {*} event event object sent by the slider component for the gain
     * @param {*} newValue the new value of the gain
     */
    handleGainChange = async (event, newValue) => {
        this.setState({
            gain: newValue
        });

        const params = {
            left: {
                gain: newValue
            }, 
            right: {
                gain: newValue
            }
        };

        try {
            const response = await axios.post("/api/param/setparam", {
                method: "set",
                data: params,
            });
            console.log(response.data);
        } catch (error) {
            this.handleAlertOpen();
            console.log(error.response.data);
        }
    }

    /**
     * Handles the change for alpha's range
     * @param {*} event event object sent by the slider component for changing alpha's range
     * @param {*} newValue a pair which represents the min and max boundary of the new range.
     *                     Here we can assume newValue[0] is the lower bound, newValue[1] is
     *                     the upper bound.
     */
    handleAlphaRangeChange = (event, newValue) => {
        if (newValue[0] >= 0 || newValue[1] <= 0) {
            return;
        }
        if (newValue[1] - newValue[0] < 0.04) {
            return;
        }
        this.setState({
            alphaRange: newValue,
            minAlpha: newValue[0],
            maxAlpha: newValue[1]
        });
    }

    showDescription = () => {
        this.setState({
            showDes: true
        });
    }

    hideDescription = () => {
        this.setState({
            showDes: false
        });
    }

    render() {
        const { classes } = this.props; 
        const { freppingAlpha, tenBand, gain, showAlert, showBandSwitchMsg, alphaRange,
                minAlpha, maxAlpha, shouldChangeColor, showDes } = this.state;
        let alphaRangeSliderMarks = [
            {value: alphaRange[0], label: alphaRange[0].toString()},
            {value: alphaRange[1], label: alphaRange[1].toString()}
        ];
        let sliderMarks = [
            {value: 0, label: '0'},
            {value: minAlpha, label: minAlpha.toString()},
            {value: maxAlpha, label: maxAlpha.toString()}
        ];
        let gainSliderMarks = [
            {value: -20, label: '-20'},
            {value: 10, label: '10'},
            {value: gain, label: gain.toString()}
        ];

        return(
            <div className={classes.root}>
                <Paper className={classes.background}>
                    <Dialog open={showAlert} onClose={this.handleAlertClose}>
                        <DialogTitle>Error</DialogTitle>
                        <Grid container className={classes.alertWindow}>
                            <Typography>RT-MHA is not running. Please make sure RT-MHA is running before fetching or sending parameters</Typography>
                            <Button onClick={this.handleAlertClose} color="primary" variant="contained">OK</Button>
                        </Grid>
                    </Dialog>

                    <div className={classes.settingsBar}>
                        <SettingsIcon className={classes.iconHover} fontSize="large" onClick={this.handleSettingsClick}></SettingsIcon>
                    </div>

                    <Dialog open={this.state.openSettings} onClose={this.handleSettingsClose}>
                        <DialogTitle>Settings</DialogTitle>
                        <Grid container spacing={3}  className={classes.settingsWindow}>
                            <Grid item>
                                <Typography>Select the number of bands of your device</Typography>
                                <Select
                                    value={tenBand}
                                    onChange={this.handleBandSwitch}
                                >
                                    <MenuItem value={false}>Six Band</MenuItem>
                                    <MenuItem value={true}>Ten Band</MenuItem>
                                </Select>
                                <Dialog open={showBandSwitchMsg} onClose={this.handleSwitchMsgClose}>
                                    <Grid container className={classes.switchMsgWindow}>
                                        <Typography style={{marginBottom: 10}}>
                                            Successfully switched to the {tenBand ? "ten band" : "six band"} mode
                                        </Typography>
                                        <Button onClick={this.handleSwitchMsgClose} color="primary" variant="contained">OK</Button>
                                    </Grid>
                                </Dialog>
                            </Grid>

                            <Grid item>
                                <Typography>Adjust the range of Alpha (the span shouldn't be smaller than 0.2)</Typography>
                                <Slider
                                    min={-0.5}
                                    max={0.5}
                                    value={alphaRange}
                                    step={0.01}
                                    onChange={this.handleAlphaRangeChange}
                                    marks={alphaRangeSliderMarks}
                                />
                            </Grid>

                            <Grid item>
                                <Grid className={classes.volumeTitle}>
                                    <Typography>Volume</Typography>
                                    <Button
                                        color="primary"
                                        onClick={this.updateParam}
                                    >
                                        Load Saved Volume
                                    </Button>
                                </Grid>
                                <Slider
                                    min={-20}
                                    max={10}
                                    step={1}
                                    defaultValue={0}
                                    valueLabelDisplay='auto'
                                    value={gain}
                                    onChange={this.handleGainChange}
                                    marks={gainSliderMarks}
                                />
                            </Grid>
                            <Grid item className={classes.saveSettingsButton}>
                                <Button color='primary' variant="contained" onClick={this.handleSettingsClose}>Done</Button>
                            </Grid>
                        </Grid>
                    </Dialog>

                    <AudioFilePlayer/>

                    <Grid container className={classes.container} spacing={3}>
                        <Typography className={classes.title}>
                            Alpha for different frequencies
                        </Typography>
                        <Typography>
                            You can warp up to 3 bands at a time.
                        </Typography>

                        <Grid item className={classes.sliderLabel}>
                            <Typography>Compression</Typography>
                            <Typography>Expansion</Typography>
                        </Grid>

                        {(tenBand ? tenBandFreq : sixBandFreq).map(
                            (currAlpha, index) => {
                                return (
                                    <Grid item className={classes.sliderRow}>
                                        <Typography
                                            className={classes.freqLabel}
                                            color={shouldChangeColor[index] ? "secondary" : "primary"}
                                        >{currAlpha + " Hz"}</Typography>
                                        <Slider
                                            // id={index}
                                            min={minAlpha}
                                            max={maxAlpha}
                                            step={0.01}
                                            defaultValue={0}
                                            value={freppingAlpha[index]}
                                            onChange={(event, value) => this.handleSliderChange(value, index)}
                                            onChangeCommitted={(event, value) => this.handleSliderChangeDone(value, index)}
                                            valueLabelDisplay="auto"
                                            track={false}
                                            disabled={this.state.reachedFrepLimit && freppingAlpha[index] === 0}
                                            marks={sliderMarks}
                                            color={shouldChangeColor[index] ? "secondary" : "primary"}
                                        />
                                        <TextField
                                            className={classes.inputField}
                                            margin='dense'
                                            value={freppingAlpha[index]}
                                            id={index}
                                            onChange={this.handleInputChange}
                                            inputProps={{
                                                step: 0.01,
                                                // min: -minAlpha,
                                                // max: maxAlpha,
                                                type: 'number',
                                                // 'aria-labelledby': 'input-slider',
                                            }}
                                            disabled={this.state.reachedFrepLimit && freppingAlpha[index] === 0}
                                            color={shouldChangeColor[index] ? "secondary" : "primary"}
                                            // error={freppingAlpha[index]>0.5||freppingAlpha[index]<-0.5}
                                            // helperText="Entry."
                                        />
                                    </Grid>
                                )
                            }
                        )}

                        <Grid item className={classes.bottomButtonArea}>
                            <Button
                                className={classes.submitButton}
                                variant="contained"
                                color="primary"
                                onClick={this.onClickSubmit}
                            >
                                    Save Parameters
                            </Button>
                            <Button
                                className={classes.submitButton}
                                variant="contained"
                                color="primary"
                                onClick={this.updateParam}
                            >
                                    Load Last Saved Parameters
                            </Button>
                        </Grid>

                        {/* <Grid item>
                            <Button
                                color="primary"
                                onClick={showDes ? this.hideDescription : this.showDescription}
                            >
                                {showDes ? 'Hide Description' : 'Show Description'}
                            </Button>
                        </Grid>

                        {showDes && (
                            <Grid>
                                <Typography>
                                    Freping, portmanteau for frequency warping, is basically shifting audio
                                    content from their original frequency to another one. Freping is an intervention approach
                                    used in HA hearbables that can help with reducing acoustic feedback between the
                                    microphones and loudspeakers.
                                    It also provides audiologists with a tool to handle less common types of
                                    hearing loss, like "cookie bite", where HA users have difficulties perceiving
                                    mid-frequency contents, compared to low- and high-frequency components.
                                </Typography>
                            </Grid>
                        )} */}

                    </Grid>

                </Paper>
            </div>
        );
    }
}

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(FreppingDemo);