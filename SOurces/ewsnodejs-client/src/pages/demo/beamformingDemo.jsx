import React, { Component } from 'react';
import { withStyles } from '@material-ui/styles';
import { axios } from 'utils/utils';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import Button from '@material-ui/core/Button';
import Input from '@material-ui/core/Input';
import Slider from '@material-ui/core/Slider';
import Box from '@material-ui/core/Box';
import SettingsIcon from '@material-ui/icons/Settings';
import bfDemo from 'utils/images/bfDemo.png';

import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import useMediaQuery from '@material-ui/core/useMediaQuery';
import { useTheme } from '@material-ui/core/styles';

const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    content: {
        padding: theme.spacing(3),
        maxWidth: 500,
        minWidth: 200,
        margin: 'auto'
    },
    bigbutton: {
        width: 480,
        height: 100,
        fontSize: 30,
        margin: 'auto',
    },
    button: {
        width: 40,
        height: 30,
        fontSize: 10,
        margin: 'auto',
    },
    input: {
        width: 100,
    },
    iconHover: {
        float: 'right',
        '&:hover': {
          color: "grey",
        },
        alignItems: "right",
        color: "inherit",
      },
    img: {
        width: 300,
        height: 'auto',
    },
});


class BeamformingDemo extends Component {
    state = {
        beamformingEnabled: false,
        mu: 0,
        ncEnabled: false,
        amcEnabled: false,
        open: false,
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
            this.setState({
                beamformingEnabled: data['left']['bf'] === 1,
                mu: parseFloat((data['left']['bf_mu']).toFixed(4)),
                ncEnabled: data['left']['bf_nc_on_off'] === 1,
                amcEnabled: data['left']['bf_amc_on_off'] === 1,
            });
        }
        catch (error) {
            throw error;
        }
    }

    ResponsiveDialog = async () => {
        //const [open, setOpen] = React.useState(false);
        const theme = useTheme();
        const fullScreen = useMediaQuery(theme.breakpoints.down('sm'));
    }

    handleClickOpen = async () => {
        this.setState({
            open: true,
        });
    };

    handleClickClose = async () => {
        this.setState({
            open: false,
        });
    };

    handleMuSliderChange = async (event, newValue) => {
        await this.setMu(newValue);
    }

    handleMuInputChange = async (event) => {
        await this.setMu(event.target.value === '' ? 0 : Number(event.target.value));
    }

    handleBlurMu = async () => {
        const { mu } = this.state;

        if (mu < 0.0001) {
            await this.setMu(0.0001);
        } else if (mu > 0.02) {
            await this.setMu(0.02);
        }
    }

    setMu = async (value) => {
        const { 
            beamformingEnabled,
            ncEnabled,
            amcEnabled,
        } = this.state;
        await this.handleTransmiteBeamformingData(beamformingEnabled, value, ncEnabled, amcEnabled);
        this.setState({ mu: value });
    }

    
    handleNcChange = (event) => {
        this.setState({
            ncEnabled: event.target.value,
        });
    };

    handleAmcChange = (event) => {
        this.setState({
            amcEnabled: event.target.value,
        });
    };

    handleNcChange = async () => {
        const { 
            beamformingEnabled,
            mu,
            ncEnabled,
            amcEnabled,
        } = this.state;
        try {
            await this.handleTransmiteBeamformingData(beamformingEnabled, mu, !ncEnabled, amcEnabled);
            this.setState({ 
                ncEnabled: !ncEnabled,
            });
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    handleAmcChange = async () => {
        const { 
            beamformingEnabled,
            mu,
            ncEnabled,
            amcEnabled,
        } = this.state;
        try {
            await this.handleTransmiteBeamformingData(beamformingEnabled, mu, ncEnabled, !amcEnabled);
            this.setState({ 
                amcEnabled: !amcEnabled,
            });
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    handleBeamformingChange = async () => {
        const { 
            beamformingEnabled,
            mu,
            ncEnabled,
            amcEnabled,
        } = this.state;
        try {
            await this.handleTransmiteBeamformingData(!beamformingEnabled, mu, ncEnabled, amcEnabled);
            this.setState({ 
                beamformingEnabled: !beamformingEnabled,
            });
        }
        catch(error) {
            alert(error.response.data);
        }
    }


    handleTransmiteBeamformingData  = async (beamformingEnabled, mu, ncEnabled, amcEnabled ) => {
        let beamformingData = {
            'bf':beamformingEnabled === false ? 0 : 1,
            'bf_mu': mu,
            'bf_nc_on_off': ncEnabled === false ? 0 : 1,
            'bf_amc_on_off': amcEnabled === false ? 0 : 1
        }
        return await axios.post("/api/param/setparam", {
            method: 'set',
            data: 
                {
                    'left': beamformingData,
                    'right': beamformingData
                } 
        });
    }

    render() {
        const { classes } = this.props; 
        const {
            beamformingEnabled,
            mu,
            ncEnabled,
            amcEnabled,
            open,
        } = this.state;
        return (
            <div className={classes.root}>
                <Grid container direction="column" justify="center">
                    <div>
                        <SettingsIcon className={classes.iconHover} fontSize="large" onClick={this.handleClickOpen}></SettingsIcon>
                        <Dialog
                            fullScreen={this.fullScreen}
                            open={open}
                            onClose={this.handleClickClose}
                            aria-labelledby="responsive-dialog-title"
                        >
                        <DialogTitle id="responsive-dialog-title">{"Adjust Settings for Beamforming Demo"}</DialogTitle>
                        <DialogContent>
                        <Box mt={2} /*lineHeight={2}*/ fontSize={18} >  
                            Basic Settings:
                        </Box>
                        <Box lineHeight={2} >  
                            mu:
                        </Box>
                        <Box lineHeight={2} fontSize={12} >  
                            Learning rate- controls speed for convergence
                        </Box>
                        <Grid 
                            container
                            spacing={3}
                            alignItems="center" 
                            justify="center"
                        >
                            <Grid container item xs={8} >
                                <Slider
                                    value={mu} 
                                    onChange={this.handleMuSliderChange}
                                    step={0.0001}
                                    min={0.0001}
                                    max={0.02}
                                />
                            </Grid>
                            <Grid item>
                                <Input
                                    className={classes.input}
                                    value={mu}
                                    margin="dense"
                                    onChange={this.handleMuInputChange}
                                    onBlur={this.handleBlurMu}
                                    inputProps={{
                                        step: 0.0002,
                                        min: 0.0001,
                                        max: 0.02,
                                        type: 'number',
                                    }}
                                />
                            </Grid>
                        </Grid>
                        <Box mt={2} lineHeight={2} fontSize={18} >  
                            Advanced Settings:
                        </Box>
                        <Box lineHeight={2} fontSize={12} >  
                            Mechanisms for Robustness in Beamforming
                        </Box>
                        <Box mt={1} lineHeight={2}>  
                            Norm Constraint:
                        </Box>
                        <Grid item>
                            <Button 
                                variant="contained"
                                color="primary"
                                onClick={this.handleNcChange}
                                className={classes.button}
                            >
                            {
                                ncEnabled ? 
                                    "ON" :
                                    "OFF"
                            }
                            </Button>
                            </Grid>
                            <Box lineHeight={2}>  
                                Adaptation Mode Control:
                            </Box>
                            <Grid item>
                                <Button 
                                    variant="contained"
                                    color="primary"
                                    onClick={this.handleAmcChange}
                                    className={classes.button}
                                >
                                {
                                    amcEnabled ? 
                                        "ON" :
                                        "OFF"
                                }
                                </Button>
                            </Grid>
                        </DialogContent>
                        <DialogActions>
                            <Button autoFocus onClick={this.handleClickClose} color="primary">
                                Exit Settings
                            </Button>
                        </DialogActions>
                        </Dialog>
                    </div>
                    <Grid container alignItems="center">
                        <Button 
                            variant="contained"
                            color="primary"
                            onClick={this.handleBeamformingChange}
                            className={classes.bigbutton}
                        >
                        {
                            beamformingEnabled ? 
                                "Disable Beamforming" :
                                "Enable Beamforming"
                        }
                        </Button>
                    </Grid>
                    <Grid>
                        <Box mt={1} lineHeight={2} fontSize={16} justify="center" align="center">  
                            Click the settings icon at the top right corner to adjust beamforming settings.
                        </Box>
                    </Grid>
                    <Grid className={classes.content}>
                        <Typography component="div">
                            <Box mt={2} lineHeight={2} fontSize={20} >  
                                Description:
                            </Box>
                        </Typography>
                        <Typography component="div" >
                            <Box justify="center" align="center" >
                                <img
                                    src={bfDemo}
                                    alt={`Beamforming Demo`}
                                    className={classes.img}
                                />
                                <Box fontSize={13}>
                                    One interference - Two microphone setup
                                </Box>  
                            </Box>
                            <Box mt={2} justify="center" align="center">  
                            {
                                'The current system uses two microphones (R/L). The signals from '.concat(
                                'these microphones are shared across the right and left channels and ',
                                'consist of the intended signal plus any interference. One channel ',
                                'is kept intact while the other is processed to obtain the best ',
                                'estimate of the interference (as little of the intended signal as ',
                                'possible). This signal is then subtracted from that in the first ',
                                'channel to leave "only" the target. The "corrected" combination in ',
                                'the first channel is evaluated and the processing of the second ',
                                'channel modified adaptively to produce one output that is then ',
                                'shared by the right and left ears.')
                            }
                            </Box>
                        </Typography>
                    </Grid>
                </Grid>
            </div>
        );
    }
}
export default withStyles(styles)(BeamformingDemo);