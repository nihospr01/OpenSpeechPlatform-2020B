import React, { Component } from 'react';
import { withStyles } from '@material-ui/styles';
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import Button from '@material-ui/core/Button';
import ButtonGroup from '@material-ui/core/ButtonGroup';
import PlayCircleOutlineIcon from '@material-ui/icons/PlayCircleOutline';
import * as d3 from 'd3';
import treeGraph from 'utils/BST/treeGraph.csv'
import treeNodes from 'utils/BST/treeNodes.csv'
import { parseTreeNodeSingleEar } from 'utils/BST/BSTUtil';
import withUserAuth from 'context/withUserAuth';
import { withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import { Link } from 'react-router-dom';
import { logBoarder, axios } from 'utils/utils';
import PropTypes from 'prop-types';
import clsx from 'clsx';
import { makeStyles } from '@material-ui/core/styles';
import IconButton from '@material-ui/core/IconButton';
import Input from '@material-ui/core/Input';
import FilledInput from '@material-ui/core/FilledInput';
import OutlinedInput from '@material-ui/core/OutlinedInput';
import InputLabel from '@material-ui/core/InputLabel';
import InputAdornment from '@material-ui/core/InputAdornment';
import FormHelperText from '@material-ui/core/FormHelperText';
import FormControl from '@material-ui/core/FormControl';
import TextField from '@material-ui/core/TextField';
import Card from '@material-ui/core/Card';
import Typography from '@material-ui/core/Typography';
import CardActions from '@material-ui/core/CardActions';
import CardContent from '@material-ui/core/CardContent';

const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    cardRoot: {
        minWidth: 100
    },
    bullet: {
        display: 'inline-block',
        margin: '0 2px',
        transform: 'scale(0.8)',
    },
    pos: {
        marginBottom: 12
    },
    paper: {
        minHeight: '90vh',
    },
    title: {
        fontSize: 14,
    },
    button: {
        minWidth: 250,
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
        width: '40vw',
        height: 100,
        marginTop: 30,
        marginBottom: 30,
    },
});

class AssessmentPage extends Component {
    state = {
        numIteration: 1,
        low: -20,
        high: 0,
        directoin: 0,
        diff: 0,
        alpha: 0,
        New: 0,
        Now: 0,
        switchingTime: 2.5,
        currPlaying: 0,
        switchingParamIntervalId: null,
        controlMode: 0,
        playCurrent: true,
        completed: false,
        isSetup: false,
        controlMode: null,
        inputMode: null,
    };

    handleChangeMin (event) {
        const low = this.state.low;
        this.setState({
            low: event.target.value
        });
    }
    handleChangeMax (event) {
        const high = this.state.high;
        this.setState({
            high: event.target.value
        });
    }
    handleChangeInputMode (event) {
        const inputMode = this.state.inputMode;
        this.setState({
            inputMode: event.target.value
        });
    }
    handleChangeControlMode (event){
        const controlMode = this.state.controlMode;
        this.setState({
            controlMode: event.target.value
        });
    }
    reset = async (backToMenu) => {
        const dataInput ={
            left: {
                alpha: 0.0,
            },
            right: {
                alpha: 0.0,
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
        this.setState({
            low: -20,
            high: 0
        });
        if (backToMenu){
            this.setState({ isSetup: false});
        }
    }

    playFile = async () => {
        const {high, low, inputMode, controlMode, switchingTime} = this.state;
        console.log('high: ' + high);
        console.log('low: ' + low);
        console.log(controlMode);
        console.log('inputMode: ' + inputMode === '0' ? 'live audio' : 'file stimulus');
        console.log('control: ' + controlMode === '0' ? 'auto': 'manual');
        let path = "";
        try {
            const response = await axios.get("/api/researcher/audioPath");
            path = path.concat(response.data);
        }
        catch(error) {
            alert(error.response.data);
        }
        path = path.concat('test.wav')
        const dataInput ={
            left: {
                audio_filename: path,
                audio_reset: 1,
                audio_repeat: 1,
                audio_play: 1,
                alpha: 1.0,
                afc: 0,
            },
            right: {
                audio_filename: path,
                audio_reset: 1,
                audio_repeat: 1,
                audio_play: 1,
                alpha: 1.0,
                afc: 0,
            },
        };
        this.setState({
            isSetup: true
        })
        
        let mid = (Number(low) + Number(high)) * 0.5;
        let newDiff = Math.abs(high - low) * 0.25;
        let newNew = Number(mid) + Number(newDiff);
        let newNow = Number(mid) - Number(newDiff);
        // this.setState({
        //     low: newNow,
        //     high: newNew,
        //     diff: newDiff,
        //     New: newNew,
        //     Now: newNow,
        // })
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

        if (controlMode == 0){
            let param = this.getParam(newNow);
            this.transmitParams(param);
            let Id = null;
            Id = setInterval(() => this.switchParam(newNow), switchingTime*1000);
            this.setState({ switchingParamIntervalId: Id});
        }

        this.setState({
            low: newNow,
            high: newNew,
            diff: newDiff,
            New: newNew,
            Now: newNow,
        })

        console.log("Current: " + this.state.Now);
        console.log("New: " + this.state.New);        
    }

    switchParam = (value) => {
        const {playCurrent} = this.state;
        if (playCurrent){
            let param = this.getParam(value);
            this.transmitParams(param);
            this.setState({ playCurrent: false});
        }
        else {
            let param = this.getParam(value);
            this.transmitParams(param);
            this.setState({ playCurrent: true});
        }
    }

    getParam = (value) =>{
        return {
            'left':{
                'g50': [value, value, value, value, value, value],
                'g80': [value, value, value, value, value, value]
            },
            'right':{
                'g50': [value, value, value, value, value, value],
                'g80': [value, value, value, value, value, value]
            }  
        }
    }

    transmitParams = async(param) =>{
        try {
            const response = await axios.post("/api/param/setparam", {
                method: 'set',
                data: param,
            });
            const data = response.data;
            console.log(data);
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    handlePlay = (isCurrent) => {       
        const { New, Now } = this.state;      
        let param = isCurrent ? this.getParam(Now) : this.getParam(New);
        this.transmitParams(param);
        this.setState({ playCurrent: isCurrent });
        console.log(isCurrent ? "Current: " + param['left']['g50'][0]: "New: " + param['left']['g50'][0])
    }

    calculateNew = (choice) => {
        const {high, low, diff, New} = this.state;
        let newLow = 0;
        let newDiff = 0;
        let newNew = 0;
        let newHigh = 0;

        if (Math.abs(choice - high) < Math.abs(choice - low)) {
            newLow = Number(low) + Number(diff);
            newDiff = Math.abs(high - low)*0.25;
            newNew = Number(high) - Number(newDiff);
            return [newNew, [newLow, 'l'], newDiff]
            // this.setState({
            //     low: newLow,
            //     diff: newDiff,
            //     New: newNew,
            // }, () => {
            //     console.log(this.state.New);
            // });
        }
        else {
            newHigh = Number(high) - Number(diff);
            newDiff = Math.abs(high - low)*0.25;
            newNew = Number(low) + Number(newDiff);
            return [newNew,  [newHigh, 'h'], newDiff]
        }

    }

    handleNewBetter = () => {
        const {New, numIteration} = this.state;
        this.setState({Now: New});
        let param = this.getParam(New);
        this.transmitParams(param);
        let tmp = this.calculateNew(New);
        if (tmp[1][1] === 'h'){
            this.setState({
                New: tmp[0],
                diff: tmp[2],
                high: tmp[1][0],
                numIteration: numIteration + 1,
            })
        }
        else{
            this.setState({
                New: tmp[0],
                diff: tmp[2],
                low: tmp[1][0],
                numIteration: numIteration + 1,
            })
        }
        // console.log("Current: " + this.state.Now);
        // console.log("New: " + this.state.New);
    }

    handleEqual = () =>{
        const {switchingParamIntervalId} = this.state;
        if (switchingParamIntervalId != null){
            clearInterval(switchingParamIntervalId);
        }
        this.reset(false);
        this.setState({
            completed: true
        });
    }

    handleCurrBetter = () =>{
        const {Now, numIteration} = this.state;
        let tmp = this.calculateNew(Now);
        let param = this.getParam(Now);
        this.transmitParams(param);
        if (tmp[1][1] === 'h'){
            this.setState({
                New: tmp[0],
                diff: tmp[2],
                high: tmp[1][0],
                numIteration: numIteration + 1,
            })
        }
        else{
            this.setState({
                New: tmp[0],
                diff: tmp[2],
                low: tmp[1][0],
                numIteration: numIteration + 1,
            })
        }
    }

//TODO  
    render() {
        const { classes } = this.props;
        const { completed, Now, New, numIteration, playCurrent, isSetup } = this.state;

        return (
            <Paper className={classes.paper}>
                { isSetup ?
                [
                <Grid container spacing={1}>
                    <Grid item xs={12} className={classes.grid}>
                        Please click the one you think is better:
                    </Grid>
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
                                startIcon={<PlayCircleOutlineIcon/>}
                                onClick={() => this.handlePlay(true)}
                            > 
                                Current
                            </Button>
                            <Button 
                                className={classes.buttonGroup}
                                variant={playCurrent ? "outlined" : "contained"}
                                startIcon={<PlayCircleOutlineIcon/>}
                                onClick={() => this.handlePlay(false)}
                            >
                                New
                            </Button>
                        </ButtonGroup>
                    </Grid>
                    {
                        !completed ? 
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
                                    onClick={() => this.handleNewBetter()}
                                >
                                    New is Better than Current
                                </Button>
                                <Button
                                    className={classes.button} 
                                    variant="outlined" 
                                    color="primary"
                                    onClick={() => this.handleEqual()}
                                >
                                    New is the Same as Current 
                                </Button>
                                <Button
                                    className={classes.button} 
                                    variant="outlined" 
                                    color="primary"
                                    onClick={() => this.handleCurrBetter()}
                                >
                                    New is Worse than Current
                                </Button>
                            </Grid>,
                        ] :
                        [
                        <Grid 
                            container
                            direction="column"
                            justify="flex-end"
                            alignItems="center"
                        >
                            <div>
                                <p>
                                Congrats!! You've finished the user assessment!"<br />
                                The overall gain is:   {Now}<br />
                                The difference between two stimulus:  {Math.abs(New - Now)}.<br />
                                The number of iterations is:  {numIteration}.
                                </p>
                            </div>
                            <Button
                                className={classes.button}
                                variant="outlined" 
                                color="primary"
                                onClick={()=>this.reset(true)}
                                // component={Link}
                                // to={`/qualityAssessmentDemo/`}
                            >
                                exit
                            </Button>
                        </Grid> 
                        ]
                    }
                </Grid>
                ] : 
                [
                    <>
                    <div style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}>
                        <h1 class="display-5">
                                Search Example
                        </h1>
                    </div>
                    <div style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}>
                        <p class="text-secondary" id="description">
                                Choose between two set of changing parameters to find your optimal setting.
                        </p>
                    </div>

                    <div style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}>
                        <input 
                            type="radio"
                            value="0" 
                            name="gender"
                            onChange={this.handleChangeInputMode.bind(this)}
                        />
                            Use Live Audio
                    </div>

                    <div style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}>
                        <input 
                            type="radio"
                            value="1" 
                            name="gender"
                            onChange={this.handleChangeInputMode.bind(this)}
                        />
                            Use File Stimulus
                    </div>                    
                    <div style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}>

                        <FormControl className={clsx(classes.margin, classes.withoutLabel, classes.textField)}>
                            <Input
                                id="minVolume"
                                value= {this.state.low}
                                onChange={this.handleChangeMin.bind(this)}
                                endAdornment={<InputAdornment position="end">dB</InputAdornment>}
                                inputProps={{
                                'aria-label': 'Min',
                                }}
                            />
                            <FormHelperText id="minVolume">Min</FormHelperText>
                        </FormControl>
                    </div>
                    <div style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}>

                        <FormControl className={clsx(classes.margin, classes.withoutLabel, classes.textField)}>
                            <Input
                                id="maxVolume"
                                value= {this.state.high}
                                onChange={this.handleChangeMax.bind(this)}
                                endAdornment={<InputAdornment position="end">dB</InputAdornment>}
                                //aria-describedby="standard-weight-helper-text"
                                inputProps={{
                                'aria-label': 'Max',
                                }}
                            />
                            <FormHelperText id="maxVolume">Max</FormHelperText>
                        </FormControl>
                    </div>
                    <div style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}>
                        <input 
                            type="radio"
                            value="0" 
                            name="inputType"
                            onChange={this.handleChangeControlMode.bind(this)}
                        />
                            Auto
                    </div>

                    <div style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}>
                        <input 
                            type="radio"
                            value="1" 
                            name="inputType"
                            onChange={this.handleChangeControlMode.bind(this)}
                        />
                            Manual
                    </div>
                    <Grid 
                        container
                        direction="column"
                        justify="flex-end"
                        alignItems="center"
                        key="options"
                    >
                        <Button
                            className={classes.button}
                            variant="contained" 
                            color="primary"
                            onClick={this.playFile}
                        >
                            start
                        </Button>
                        <Button
                            className={classes.button}
                            variant="outlined" 
                            color="primary"
                            onClick={() => this.reset(false)}
                            component={Link}
                            to={`/listenermanagement/`}
                        >
                            exit
                        </Button>
                    </Grid>
                    </>
                    ]
            }
            </Paper>
        );
    }
}


AssessmentPage.propTypes = {
    showNodeNumber: PropTypes.bool.isRequired,
};

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(AssessmentPage);