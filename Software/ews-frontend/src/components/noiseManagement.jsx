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

const styles = (theme) => ({
    content: {
        padding: theme.spacing(3),
        maxWidth: 500,
        minWidth: 200,
        margin: 'auto'
    },
    button: {
        width: 320,
        margin: 'auto',
        marginTop: 8,
    },
    groupButton: {
        width: 320,
        color: 'gray',
    },
    input: {
        width: 60,
    },
})

const noiseOptions = [
    'None',
    'Arslan power averaging procedure',
    'Hirsch and Ehrlicher weighted noise averaging procedure',
    'Cohen and Berdugo MCRA Procedure',
];


class NoiseManagement extends Component {
    state = {
        currentNoiseOption: 0,
        noiseSubtraction: 0,
        speechEnhancementEnabled: false,
        normConstraint: 0,
        adaptionModeControl: 0,
        beamformingEnabled: false,
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
                currentNoiseOption: parseInt(data['left']['noise_estimation_type']),
                noiseSubtraction: parseFloat(data['left']['spectral_subtraction']),
                speechEnhancementEnabled: data['left']['spectral_type'] === 1,
                normConstraint: parseInt(data['left']['nc_thr']),
                adaptionModeControl: parseInt(data['left']['amc_thr']),
                beamformingEnabled: data['left']['bf'] === 1,
            });
        }
        catch (error) {
            throw error;
        }
    }

    // Transmit data immediately when option change
    handleToggleNoiseOption = async (value) => {
        const { 
            speechEnhancementEnabled, 
            noiseSubtraction,
        } = this.state;
        await this.handleTransmitSpeechData(speechEnhancementEnabled, value, noiseSubtraction );
        this.setState({
            currentNoiseOption: value,
        });
    }

    // Transmit data immediately when slider change
    handleNoiseSubtractionSliderChange = async (event, newValue) => {
        await this.setNoiseSubtraction(newValue);
    }

    handleNoiseSubtractionInputChange = async event => {
        await this.setNoiseSubtraction(event.target.value === '' ? 0 : Number(event.target.value));
    }

    handleBlurNoiseSubtraction = async () => {
        const { noiseSubtraction } = this.state;

        if (noiseSubtraction < 0) {
            await this.setNoiseSubtraction(0);
        } else if (noiseSubtraction > 1) {
            await this.setNoiseSubtraction(1);
        }
    }


    handleNormConstraintSliderChange = async (event, newValue) => {
        await this.setNormConstraint(newValue);
    }

    handleNormConstraintInputChange = async (event) => {
        await this.setNormConstraint(event.target.value === '' ? 0 : Number(event.target.value));
    }

    handleBlurNormConstraint = async () => {
        const { normConstraint } = this.state;

        if (normConstraint < 0) {
            await this.setNormConstraint(0);
        } else if (normConstraint > 10) {
            await this.setNormConstraint(10);
        }
    }

    handleAdaptionSliderChange = async (event, newValue) => {
        await this.setAdaption(newValue);
    }

    handleAdaptionInputChange = async (event) => {
        await this.setAdaption(event.target.value === '' ? 0 : Number(event.target.value));
    }

    handleBlurAdaption = async () => {
        const { adaptionModeControl } = this.state;

        if (adaptionModeControl < 0) {
            await this.setAdaption(0);
        } else if (adaptionModeControl > 1) {
            await this.setAdaption(1);
        }
    }

    setNoiseSubtraction = async (value) => {
        const { 
            speechEnhancementEnabled, 
            currentNoiseOption,
        } = this.state;
        await this.handleTransmitSpeechData(speechEnhancementEnabled, currentNoiseOption, value );
        this.setState({ noiseSubtraction: value });
    }

    setNormConstraint = async (value) => {
        const { 
            beamformingEnabled, 
            adaptionModeControl,
        } = this.state;
        await this.handleTransmiteBeamformingData(beamformingEnabled, value, adaptionModeControl );
        this.setState({ normConstraint: value });
    }

    setAdaption = async (value) => {
        const { 
            beamformingEnabled, 
            normConstraint,
        } = this.state;
        await this.handleTransmiteBeamformingData(beamformingEnabled, normConstraint, value );
        this.setState({ adaptionModeControl: value });
    }

    handleSpeechEnhancementChange = async () => {
        const { 
            speechEnhancementEnabled, 
            currentNoiseOption, 
            noiseSubtraction,
        } = this.state;
        try {
            await this.handleTransmitSpeechData(!speechEnhancementEnabled,currentNoiseOption, noiseSubtraction);
            this.setState({ 
                speechEnhancementEnabled: !speechEnhancementEnabled,
            });
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    handleBeamformingChange = async () => {
        const { 
            beamformingEnabled, 
            normConstraint, 
            adaptionModeControl,
        } = this.state;
        try {
            await this.handleTransmiteBeamformingData(!beamformingEnabled, normConstraint,adaptionModeControl);
            this.setState({ 
                beamformingEnabled: !beamformingEnabled,
            });
        }
        catch(error) {
            alert(error.response.data);
        }
    }


    handleTransmiteBeamformingData  = async (beamformingEnabled,normConstraint, adaptionModeControl ) => {
        let beamformingData = {
            'bf':beamformingEnabled === false ? 0 : 1,
            'nc_thr': normConstraint,
            'amc_thr': adaptionModeControl
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

    handleTransmitSpeechData = async(speechEnhancementEnabled,currentNoiseOption, noiseSubtraction) => {
        let speechData = {
            'noise_estimation_type': currentNoiseOption,
            'spectral_type': speechEnhancementEnabled === false ? 0 : 1,
            'spectral_subtraction': noiseSubtraction,
        };
        return await axios.post("/api/param/setparam", {
            method: 'set',
            data: 
                {
                    'left': speechData,
                    'right':speechData
                } 
        });
    }

    render() {
        const { classes } = this.props; 
        const { 
            currentNoiseOption,
            noiseSubtraction,
            speechEnhancementEnabled,
            normConstraint,
            adaptionModeControl,
            beamformingEnabled,
        } = this.state;
        return (
            <div className={classes.content}>
                <Grid container direction="column">
                    <Typography component="div">
                        <Box lineHeight={2} >  
                            Noise Estimation Options:
                        </Box>
                    </Typography>
                    <Grid
                        container
                        direction="column"
                        justify="center"
                        alignItems="center"
                    >
                        {
                            noiseOptions.map((value, index) => (
                                <ToggleButton 
                                    color="primary"
                                    className={classes.groupButton} 
                                    key={`noise_${index}`}
                                    value={index} 
                                    selected={index === currentNoiseOption}
                                    onClick={() => this.handleToggleNoiseOption(index)}
                                >
                                    {value}
                                </ToggleButton>
                            ))
                        }
                    </Grid>
                    <Typography component="div">
                        <Box lineHeight={2} >  
                            Noise Subtraction Parameter:
                        </Box>
                    </Typography>
                    <Grid container spacing={3} alignItems="center">
                        <Grid item>
                            None
                        </Grid>
                        <Grid item xs>
                            <Slider
                                value={noiseSubtraction}
                                onChange={this.handleNoiseSubtractionSliderChange}
                                step={0.001}
                                min={0}
                                max={1}
                            />
                        </Grid>
                        <Grid item>
                            Aggressive
                        </Grid>
                        <Grid item>
                            <Input
                                className={classes.input}
                                value={noiseSubtraction}
                                margin="dense"
                                onChange={this.handleNoiseSubtractionInputChange}
                                onBlur={this.handleBlurNoiseSubtraction}
                                inputProps={{
                                    step: 0.001,
                                    min: 0,
                                    max: 1,
                                    type: 'number',
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Grid container alignItems="center">
                        <Button 
                            variant="contained"
                            color="primary"
                            onClick={this.handleSpeechEnhancementChange}
                            className={classes.button}
                        >
                        {
                            speechEnhancementEnabled ? 
                                "Disable Speech Enhancement" :
                                "Enable Speech Enhancement"
                        }
                        </Button>
                    </Grid>
                    <Typography component="div">
                        <Box lineHeight={2} fontSize={24} >  
                            Beamforming
                        </Box>
                    </Typography>
                    <Typography component="div">
                        <Box lineHeight={2} >  
                            Norm Constraint:
                        </Box>
                    </Typography>
                    <Grid 
                        container
                        spacing={3}
                        alignItems="center" 
                        justify="center"
                    >
                        <Grid container item xs={8} >
                            <Slider
                                value={normConstraint}
                                onChange={this.handleNormConstraintSliderChange}
                                step={1}
                                min={0}
                                max={10}
                            />
                        </Grid>
                        <Grid item>
                            <Input
                                className={classes.input}
                                value={normConstraint}
                                margin="dense"
                                onChange={this.handleNormConstraintInputChange}
                                onBlur={this.handleBlurNormConstraint}
                                inputProps={{
                                    step: 1,
                                    min: 0,
                                    max: 10,
                                    type: 'number',
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Typography component="div">
                        <Box lineHeight={2} >  
                            Adaptation mode control:
                        </Box>
                    </Typography>
                    <Grid 
                        container
                        spacing={3}
                        alignItems="center" 
                        justify="center"
                    >
                        <Grid container item xs={8} >
                            <Slider
                                value={adaptionModeControl}
                                onChange={this.handleAdaptionSliderChange}
                                step={1}
                                min={0}
                                max={10}
                            />
                        </Grid>
                        <Grid item>
                            <Input
                                className={classes.input}
                                value={adaptionModeControl}
                                margin="dense"
                                onChange={this.handleAdaptionInputChange}
                                onBlur={this.handleBlurAdaption}
                                inputProps={{
                                    step: 1,
                                    min: 0,
                                    max: 10,
                                    type: 'number',
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Grid container alignItems="center">
                        <Button 
                            variant="contained"
                            color="primary"
                            onClick={this.handleBeamformingChange}
                            className={classes.button}
                        >
                        {
                            beamformingEnabled ? 
                                "Disable Beamforming" :
                                "Enable Beamforming"
                        }
                        </Button>
                    </Grid>
                </Grid>
            </div>
        );
    }
}

export default withStyles(styles)(NoiseManagement);