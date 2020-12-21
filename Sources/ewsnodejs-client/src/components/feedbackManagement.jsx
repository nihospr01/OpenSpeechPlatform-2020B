import React, { Component } from 'react';
import { axios, roundOff4 } from 'utils/utils';
import { withStyles } from '@material-ui/styles';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import Button from '@material-ui/core/Button';
import Input from '@material-ui/core/Input';
import Slider from '@material-ui/core/Slider';
import ToggleButton from '@material-ui/lab/ToggleButton';
import Box from '@material-ui/core/Box';
import Tooltip from '@material-ui/core/Tooltip';
import IconButton from '@material-ui/core/IconButton';
import HelpIcon from '@material-ui/icons/Help';
import block4afc from 'utils/images/4afcBlock.png';

const styles = (theme) => ({
    content: {
        margin: 10,
        maxWidth: '90%',
        margin: 30,
    },
    setting: {
        width: '60%',
        margin: 2,
    },
    button: {
        width: 100,
        margin: 10,
    },
    groupButton: {
        width: '60%',
        color: 'gray',
    },
    input: { 
        width: 100,
    },
    helperIcon: {
        display: 'inline-flex',
        verticalAlign: 'middle',
    },
    explanation: {
        width: '80%',
        margin: 8,
    },
    img: {
        width: '80%',
        height: 'auto',
    },
})

const afc_options = [
    'No Adaptation',
    'FxLMS',
    'IPNLMS',
    'SLMS',
];

class FeedbackManagement extends Component {
    state = {
        afc_delay: 0,
        afc_mu: 0,
        afc_rho: 0,
        afc_type: 0,
    };

    componentDidMount = async () => {
        try {
            await this.loadParameter();
        }
        catch (error) {
            alert(error);
        }
    }


    loadParameter = async () => {
        try {
            const response = await axios.post("/api/param/getparam");
            const data = response.data;
            console.log(data)
            this.setState({
                afc_delay: parseInt(data['left']['afc_delay']),
                afc_mu: parseInt(data['left']['afc_mu']),
                afc_rho: parseInt(data['left']['afc_rho']),
                afc_type: parseInt(data['left']['afc_type']),
            });
            
        }
        catch (error) {
            throw error;
        }
    }


    handleAllInputChange = (type, newValue)=> {

        //Blur the invalid input value based on the type
        if(type === 'afc_mu' || type === 'afc_rho'){
            newValue = newValue <= 0 ? 0: (newValue >= 1 ? 1 : newValue);
        }
        if(type === 'afc_delay'){
            newValue = newValue <= 0 ? 0: (newValue >= 8 ? 8 : newValue);
        }
        this.setState({
            [type]: newValue
        })
    }
  
    //Transmit current values when use hit enter key on input
    handleTransmit = async() => {
        const {
            afc_mu,
            afc_rho,
            afc_type,
            afc_delay,
        } = this.state;
        
        let transmitData = {
            afc_mu,
            afc_rho,
            afc_type,
            afc_delay,
            afc_reset:0,
        }
        //console.log(transmitData)
        try {
            await axios.post("/api/param/setparam", {
                method: 'set',
                data: {
                    'left': transmitData,
                    'right': transmitData
                },
                
            });
            await this.loadParameter();
        }
        catch(error) {
            alert(error.response.data);
        }

    }

    setDelay = (value) => {
        this.setState({ afc_delay: roundOff4(value) });
    }

    setMu = (value) => {
        this.setState({ afc_mu: roundOff4(value) });
    }

    setRho = (value) => {
        this.setState({ afc_rho: roundOff4(value) });
    }

    setType = (value) => {
        this.setState({ afc_type: value});
    }

    handleReset = async () => {
        const {
            afc_mu,
            afc_rho,
            afc_type,
            afc_delay,
        } = this.state;
        try {
            await axios.post("/api/param/setparam", {
                method: 'set',
                data: {
                    'left': {
                        'afc_mu': 0.005,
                        'afc_rho': 0.9,
                        'afc_type': 1,
                        'afc_delay': 4.6875,
                        'afc_reset': 1,
                    },
                    'right': {
                        'afc_mu': 0.005,
                        'afc_rho': 0.9,
                        'afc_type': 1,
                        'afc_delay': 4.6875,
                        'afc_reset': 1,
                    }
                },
            });
            await this.loadParameter();
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    render() {
        const { classes } = this.props; 
        const { 
            afc_type,
            afc_delay,
            afc_mu,
            afc_rho,
        } = this.state;
        return (
            <div className={classes.content}>
                <Grid container 
                    direction="column" 
                    spacing={5} 
                    alignItems="center" 
                    justify="center">
                    <Grid
                        container
                        direction="column"
                        justify="center"
                        alignItems="center"
                    >
                        {
                            afc_options.map((value, index) => (
                                <ToggleButton 
                                    color="primary"
                                    className={classes.groupButton} 
                                    key={`noise_${index}`}
                                    value={index} 
                                    selected={index === afc_type}
                                    onClick={() => this.handleAllInputChange('afc_type', index)}
                                >
                                    {value}
                                </ToggleButton>
                            ))
                        }
                    </Grid>
                    <Grid
                        container
                        alignItems="center" 
                        direction='row'
                        spacing={3}
                        justify="space-between"
                        className={classes.setting}
                        >
                        <Grid item>
                            <Typography component="div">
                                <Box lineHeight={2} >  
                                    Delay (ms):
                                </Box>
                            </Typography>
                        </Grid>
                        <Grid container item xs={6}>
                            <Slider
                                value = {afc_delay}
                                step = {0.01}
                                min = {0}
                                max = {8}
                                onChange = {(event,value) => this.handleAllInputChange('afc_delay', value)}
                            />
                        </Grid>
                        <Grid item>
                            <Input
                                className={classes.input}
                                value={afc_delay}
                                margin="dense"
                                // onChange={this.handleDelayInputChange}
                                // onBlur={this.handleBlurDelay}
                                onChange={(event) => this.handleAllInputChange('afc_delay', Number(event.target.value))}
                                // onBlur={()=>this.handleTransmitParamChange('afc_delay', afc_delay)}
                                inputProps={{
                                    step: 0.0001,
                                    min: 0,
                                    max: 8.0,
                                    type: 'number',
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Grid 
                        container
                        alignItems="center" 
                        direction='row'
                        spacing={3}
                        justify="space-between"
                        className={classes.setting}
                    >
                        <Grid item>
                            <Typography component="div">
                                <Box >  
                                    Mu 
                                    <Tooltip disableFocusListener title={
                                         'The step size parameter, which has to be set to '.concat(
                                            'a positive value. It controls the adaptation rate of the AFC filter,',
                                            ' in the sense that a larger mu is more suitable for a highlty changin ',
                                            'environment. Note, however, a larger mu also leads to a higher possibility ',
                                            'of instability and more artifacts.',
                                        )
                                    }>
                                        <IconButton size="small" className={classes.helperIcon}>
                                            <HelpIcon />
                                        </IconButton>
                                    </Tooltip>  :
                                </Box>
                            </Typography>
                        </Grid>
                        <Grid container item xs={6}>
                            <Slider
                                value = {afc_mu}
                                step = {0.001}
                                min = {0}
                                max = {1}
                                onChange = {(event,value) => this.handleAllInputChange('afc_mu', value)}
                            />
                        </Grid>
                        <Grid item>
                            <Input
                                className={classes.input}
                                value={afc_mu}
                                margin="dense"
                                onChange={(event) => this.handleAllInputChange('afc_mu',Number(event.target.value))}
                                inputProps={{
                                    step: 0.0001,
                                    min: 0,
                                    max: 1,
                                    type: 'number',
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Grid 
                        container
                        alignItems="center" 
                        direction='row'
                        spacing={2}
                        justify="space-between"
                        className={classes.setting}
                    >
                        <Grid item>
                            <Typography component="div">
                                <Box >  
                                    Rho
                                    <Tooltip disableFocusListener title={
                                         'A forgetting factor for controlling the update of the signal power estimate'.concat(
                                             '. It ranges from 0-1. For speech signals, typical values of rho are around 0.9. ',
                                             'If it is too large, the update of the power estimate would fail to catch up with ',
                                             'speech variatios. If it is set too small, the variance of the estimate will be high, ',
                                             'leading to more artifacts.' 
                                        )
                                    }>
                                        <IconButton size="small" className={classes.helperIcon}>
                                            <HelpIcon />
                                        </IconButton>
                                    </Tooltip>  :
                                </Box>
                            </Typography>
                        </Grid>
                        <Grid container item xs={6}>
                            <Slider
                                value = {afc_rho}
                                step = {0.001}
                                min = {0}
                                max = {1}
                                onChange = {(event,value) => this.handleAllInputChange('afc_rho', value)}
                            />
                        </Grid>
                        <Grid item>
                            <Input
                                className={classes.input}
                                value={afc_rho}
                                margin="dense"
                                onChange={(event) => this.handleAllInputChange('afc_rho',Number(event.target.value))}
                                inputProps={{
                                    step: 0.0001,
                                    min: 0,
                                    max: 1,
                                    type: 'number',
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Grid container justify="center" direction='row'>
                        <Button 
                            variant="contained"
                            color="primary"
                            onClick={() => this.handleTransmit()}
                            className={classes.button}
                        >
                            Update
                        </Button>
                        <Button 
                            variant="contained"
                            color="default"
                            onClick={(event) => this.handleReset()}
                            className={classes.button}
                        >
                            Reset
                        </Button>
                    </Grid>
                    <Typography component="div" >
                        <Box fontSize={24}>  
                            Troubleshooting AFC
                        </Box>
                    </Typography>
                    <Typography component="div" className={classes.explanation}>
                        <Box>  
                        {
                            'When instability is detected by the user, he/she should press '.concat(
                                'the "Reset” button above. This will (i) initialize AFC filter to its',
                                ' initial coefficients, obtained from average of multiple feedback path',
                                ' measurements; and (ii) reduce amplification gain in all bands by x dB. ',
                                '(Arthur to experiment and suggest value of x. Initially, x=10).',
                            )
                        }
                        </Box>
                    </Typography>
                    
                    <img
                        src={block4afc}
                        alt={`4AFC Block`}
                        className={classes.img}
                    />
                </Grid>
            </div>
        );
    }
}

export default withStyles(styles)(FeedbackManagement);