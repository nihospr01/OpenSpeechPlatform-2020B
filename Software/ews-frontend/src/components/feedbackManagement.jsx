import React, { Component } from 'react';
import { axios } from 'utils/utils';
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
        padding: theme.spacing(3),
        maxWidth: 480,
        minWidth: 200,
        margin: 'auto'
    },
    button: {
        width: 100,
        margin: 10,
    },
    groupButton: {
        width: 320,
        color: 'gray',
    },
    input: {
        width: 72,
    },
    helperIcon: {
        // width: 20,
        // height: 20,
        // margin: 'auto',
        display: 'inline-flex',
        verticalAlign: 'middle',
    },
    img: {
        width: 440,
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
                afc_delay: parseInt(data['left']['afc_delay']*100000)/100000,
                afc_mu: parseInt(data['left']['afc_mu']*100000)/100000,
                afc_rho: parseInt(data['left']['afc_rho']*100000)/100000,
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
            newValue = newValue === "" || newValue <= 0 ? 0: (newValue >= 1 ? 1 : newValue);
        }
        if(type === 'afc_delay'){
            newValue = newValue === "" || newValue <= 0 ? 0: (newValue >= 8 ? 8 : newValue);
        }
        this.setState({
            [type]: newValue
        })
    }
  
    //Transmit current values when use hit enter key on input
    handleTransmit = async(e, reset) => {
        if(e.key === 'Enter'){
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
                afc_reset:reset?1:0,
            }

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
    }
    
    handleRhoSliderChange = (event, newValue) => {
        this.setRho(newValue);
    }

    handleDelayInputChange = event => {
        this.setDelay(event.target.value === '' ? 0 : Number(event.target.value));
    }

    handleMuInputChange = (event) => {
        this.setMu(event.target.value === '' ? 0 : Number(event.target.value));
    }

    handleRhoInputChange = (event) => {
        this.setRho(event.target.value === '' ? 0 : Number(event.target.value));
    }

    handleBlurDelay = () => {
        const { afc_delay } = this.state;

        if (afc_delay < 0) {
            this.setDelay(0);
        } else if (afc_delay > 8) {
            this.setDelay(8);
        }
    }

    handleBlurMu = () => {
        const { afc_mu } = this.state;

        if (afc_mu < 0) {
            this.setMu(0);
        } else if (afc_mu > 1) {
            this.setMu(1);
        }
    }

    handleBlurRho = () => {
        const { afc_rho } = this.state;

        if (afc_rho < 0) {
            this.setRho(0);
        } else if (afc_rho > 1) {
            this.setRho(1);
        }
    }

    setDelay = (value) => {
        this.setState({ afc_delay: value });
    }

    setMu = (value) => {
        this.setState({ afc_mu: value });
    }

    setRho = (value) => {
        this.setState({ afc_rho: value });
    }

    handleReset = async () => {
        const {
            afc_mu,
            afc_rho,
            afc_type,
            afc_delay,
        } = this.state;
        try {
            const response = await axios.post("/api/param/setparam", {
                method: 'set',
                data: {
                    'left': {
                        'afc_mu': afc_mu,
                        'afc_rho': afc_rho,
                        'afc_type': afc_type,
                        'afc_delay': afc_delay,
                        'afc_reset': 1,
                    },
                    'right': {
                        'afc_mu': afc_mu,
                        'afc_rho': afc_rho,
                        'afc_type': afc_type,
                        'afc_delay': afc_delay,
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

    handleTransmitParamChange = async (type, newValue) => {
        console.log(type, newValue);
        const {
            afc_mu,
            afc_rho,
            afc_type,
            afc_delay,
        } = this.state;
        if(type === 'afc_mu' || type === 'afc_rho'){
            newValue = newValue === "" || newValue <= 0 ? 0: (newValue >= 1 ? 1 : newValue);
        }
        if(type === 'afc_delay'){
            newValue = newValue === "" || newValue <= 0 ? 0: (newValue >= 8 ? 8 : newValue);
        }
        let transmitData = {
            afc_mu,
            afc_rho,
            afc_type,
            afc_delay,
            afc_reset:0,
            [type]:newValue,
        }
        console.log(transmitData)
        try {
            const response = await axios.post("/api/param/setparam", {
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
                <Grid container direction="column" spacing={2}>
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
                    <Grid container spacing={3} justify="space-between" alignItems="center">
                        <Grid item>
                            <Typography component="div">
                                <Box lineHeight={2} >  
                                    Delay (ms):
                                </Box>
                            </Typography>
                        </Grid>
                        {/* <Grid item xs={7} >
                            <Slider
                                value={afc_delay}
                                onChange={this.handleDelaySliderChange}
                                step={0.00001}
                                min={0}
                                max={8}
                            />
                        </Grid> */}
                        <Grid item>
                            <Input
                                className={classes.input}
                                value={afc_delay}
                                margin="dense"
                                // onChange={this.handleDelayInputChange}
                                // onBlur={this.handleBlurDelay}
                                onKeyDown={this.handleTransmit}
                                onChange={(event) => this.handleAllInputChange('afc_delay', Number(event.target.value))}
                                // onBlur={()=>this.handleTransmitParamChange('afc_delay', afc_delay)}
                                inputProps={{
                                    step: 0.00001,
                                    min: 0,
                                    max: 8.0,
                                    type: 'number',
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Grid 
                        container
                        spacing={3}
                        alignItems="center" 
                        justify="space-between"
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
                                    </Tooltip>:
                                </Box>
                            </Typography>
                        </Grid>
                        <Grid item>
                            <Input
                                className={classes.input}
                                value={afc_mu}
                                margin="dense"
                                onKeyDown={this.handleTransmit}
                                onChange={(event) => this.handleAllInputChange('afc_mu',Number(event.target.value))}
                                inputProps={{
                                    step: 0.00001,
                                    min: 0,
                                    max: 1,
                                    type: 'number',
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Grid 
                        container
                        spacing={3}
                        alignItems="center" 
                        justify="space-between"
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
                                    </Tooltip>:
                                </Box>
                            </Typography>
                        </Grid>
                        <Grid container item xs={6} >
                            <Slider
                                value={afc_rho}
                                onChange={(event, newValue)=>this.handleAllInputChange('afc_rho', newValue)}
                                step={0.00001}
                                min={0}
                                max={1}
                            />
                        </Grid>
                        <Grid item>
                            <Input
                                className={classes.input}
                                value={afc_rho}
                                margin="dense"
                                onKeyDown={this.handleTransmit}
                                onChange={(event) => this.handleAllInputChange('afc_rho',Number(event.target.value))}
                                inputProps={{
                                    step: 0.00001,
                                    min: 0,
                                    max: 1,
                                    type: 'number',
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Typography component="div">
                        <Box fontSize={24}>  
                            Troubleshooting 4AFC
                        </Box>
                    </Typography>
                    <Typography component="div">
                        <Box>  
                        {
                            'When instability is detected by the user, he/she should press '.concat(
                                'the "Undo” button below. This will (i) initialize AFC filter to its',
                                ' initial coefficients, obtained from average of multiple feedback path',
                                ' measurements; and (ii) reduce amplification gain in all bands by x dB. ',
                                '(Arthur to experiment and suggest value of x. Initially, x=10).',
                            )
                        }
                        </Box>
                    </Typography>
                    <Grid container justify="flex-end">
                        <Button 
                            variant="contained"
                            color="primary"
                            onClick={(event) => this.handleAllInputChange('afc_reset',1)}
                            className={classes.button}
                        >
                            Reset
                        </Button>
                        {/* <Button 
                            variant="contained"
                            color="primary"
                            onClick={this.handleTransmit}
                            className={classes.button}
                        >
                            Transmit
                        </Button> */}
                    </Grid>
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