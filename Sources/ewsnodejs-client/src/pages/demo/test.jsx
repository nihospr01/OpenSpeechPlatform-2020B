import React, { Component } from 'react';
import { makeStyles } from '@material-ui/styles';
import { withStyles } from '@material-ui/styles';
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import Button from '@material-ui/core/Button';
import Typography from '@material-ui/core/Typography';
import Box from '@material-ui/core/Box';
import { createMuiTheme, MuiThemeProvider } from '@material-ui/core/styles';
import teal from '@material-ui/core/colors/teal';
import {Link} from 'react-router-dom';
import { compose } from 'recompose';
import {axios} from 'utils/utils';

const tealTheme = createMuiTheme({ palette: {primary: teal}});

const styles = (theme) => ({
    root: {
      minWidth: 275,
    },
    bullet: {
      display: 'inline-block',
      margin: '0 2px',
      transform: 'scale(0.8)',
    },
    title: {
      fontSize: 14,
    },
    pos: {
      marginBottom: 12,
    },
    gridContainer: {
        paddingLeft: '20px',
        paddingRight: '20px',
    }
  });

class Testing extends Component {

    reset = async() => {
        const dataInput ={
            left:{
                alpha: 0.0,
                en_ha:1,
                afc:1,
                freping:1,
                rear_mics:0,
                g50:[0,0,0,0,0,0],
                g80:[0,0,0,0,0,0],
                knee_low:[45,45,45,45,45,45],
                mpo_band:[120,120,120,120,120,120],
                attack:[5,5,5,5,5,5],
                release:[20,20,20,20,20,20],
                global_mpo:120,
                freping_alpha:[0,0,0,0,0.019999999552965164,0]
            },
            right:{
                en_ha:1,
                afc:1,
                freping:1,
                rear_mics:0,
                g50:[0,0,0,0,0,0],
                g80:[0,0,0,0,0,0],
                knee_low:[45,45,45,45,45,45],
                mpo_band:[120,120,120,120,120,120],
                attack:[5,5,5,5,5,5],
                release:[20,20,20,20,20,20],
                global_mpo:120,
                freping_alpha:[0,0,0,0,0.019999999552965164,0]
            }
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

    render() {
        const {classes} = this.props;
        var buttonStyle = {
            display: 'block',
            width: '100%',
            height: '150%',
            fontSize: '15px',
            textTransform: 'none',
        };
        return (
            <div className={classes.root}>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    alignContent: "center",
                }}
                >
                    <h1 className="display-5"> The Open Speech Platform </h1>
                </div>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    alignContent: "center",
                }}
                >
                    <h3 className="display-5"> A Real-time, Open, Portable, Extensible Speech Lab. 
                        Visit our <a href="http://openspeechplatform.ucsd.edu/">website</a> to learn more.</h3>
                </div>

                <Grid container spacing={10} direction='column'>
                    <Grid item xs={12}>
                        <Grid container spacing={10} className={classes.gridContainer} >
                            <Grid item xs={12} md={6} lg={4}>
                                <MuiThemeProvider theme={tealTheme}>
                                    <Link to={'/main/researcherpage'}>
                                        <Button variant='outlined' color='primary' style={buttonStyle} variant='contained'>
                                            <Typography component="div">
                                                <Box textAlign="center" fontWeight="fontWeightBold" fontSize={20}>
                                                    Researcher Page
                                                </Box>
                                                <Box textAlign="center" m={1}>
                                                    Includes amplification, noise and feedback parameters.
                                                </Box>
                                            </Typography>
                                        </Button>
                                    </Link>
                                </MuiThemeProvider>
                            </Grid>
                            <Grid item item xs={12} md={6} lg={4}>
                                <MuiThemeProvider theme={tealTheme}>
                                    <Link to={'/main/freppingDemo'}>
                                        <Button variant='outlined' color='primary' style={buttonStyle} variant='contained'>
                                            <Typography component="div">
                                                <Box textAlign="center" fontWeight="fontWeightBold" fontSize={20}>
                                                    Freping
                                                </Box>
                                                <Box textAlign="center" m={1}>
                                                    Includes amplification, noise and feedback parameters.
                                                </Box>
                                            </Typography>
                                        </Button>
                                    </Link>
                                </MuiThemeProvider>
                            </Grid>
                            <Grid item item item xs={12} md={6} lg={4}>
                                <MuiThemeProvider theme={tealTheme}>
                                    <Link to={'/main/beamformingDemo'}>
                                        <Button variant='outlined' color='primary' style={buttonStyle} variant='contained'>
                                            <Typography component="div">
                                                <Box textAlign="center" fontWeight="fontWeightBold" fontSize={20}>
                                                    Beamforming Task
                                                </Box>
                                                <Box textAlign="center">
                                                    Manipulation of the parameters associated with OSP’s adaptive beamformer
                                                </Box>
                                            </Typography>
                                        </Button>
                                    </Link>
                                </MuiThemeProvider>
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid item xs={12}>
                        <Grid container spacing={10} className={classes.gridContainer} >
                                <Grid item item item xs={12} md={6} lg={4}>
                                    <MuiThemeProvider theme={tealTheme}>
                                        <Link to={'/main/FourAFCDemo'}>
                                            <Button variant='outlined' color='primary' style={buttonStyle} variant='contained'>
                                                <Typography component="div">
                                                    <Box textAlign="center" fontWeight="fontWeightBold" fontSize={20}>
                                                        4 Alternative Forced Choice(4 AFC) Task
                                                    </Box>
                                                    <Box textAlign="center" >
                                                        Mobile-assisted word recognition tests using minimal contrast sets.
                                                    </Box>
                                                </Typography>
                                            </Button>
                                        </Link>
                                    </MuiThemeProvider>
                                </Grid>
                                <Grid item item item xs={12} md={6} lg={4}>
                                    <MuiThemeProvider theme={tealTheme}>
                                        <Link to={'/main/coarseFitDemo'}>
                                            <Button variant='outlined' color='primary' style={buttonStyle} variant='contained'>
                                                <Typography component="div">
                                                    <Box textAlign="center" fontWeight="fontWeightBold" fontSize={20}>
                                                        CoarseFit Task
                                                    </Box>
                                                    <Box textAlign="center" >
                                                        Includes amplification, noise and feedback parameters.
                                                    </Box>
                                                </Typography>
                                            </Button>
                                        </Link>
                                    </MuiThemeProvider>
                                </Grid>
                                <Grid item item item xs={12} md={6} lg={4}>
                                    <MuiThemeProvider theme={tealTheme}>
                                        <Link to={'/main/reset'}>
                                            <Button onClick={this.reset} variant='outlined' color='primary' style={buttonStyle} variant='contained'>
                                                <Typography component="div">
                                                    <Box textAlign="center" fontWeight="fontWeightBold" fontSize={20}>
                                                        Reset
                                                    </Box>
                                                    <Box textAlign="center" >
                                                        Reset to Original Prescription
                                                    </Box>
                                                </Typography>
                                            </Button>
                                        </Link>
                                    </MuiThemeProvider>
                                </Grid>
                        </Grid>
                    </Grid>
                </Grid>
            </div>
        )
    }
}

export default compose(
    withStyles(styles)
)
(Testing);