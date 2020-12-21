import React, { Component } from 'react';
import { axios } from 'utils/utils';
import { withStyles } from '@material-ui/core/styles';
import Typography from '@material-ui/core/Typography';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import Paper from '@material-ui/core/Paper';
import Hidden from '@material-ui/core/Hidden';
import Select from '@material-ui/core/Select';
import CircularProgress from '@material-ui/core/CircularProgress';
import { MenuItem, InputLabel, FormControl } from '@material-ui/core';
import { Link, withRouter } from 'react-router-dom';
import { compose } from 'recompose';

const styles = (theme) => ({
    root: {
        padding: 20
    },
    content: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
    },
    topBar: {
        display: 'flex',
        justifyContent: 'space-between',
        marginBottom: 30
    },
    topBarTitle: {
        marginLeft: 50,
    },
    topBarButton: {
        marginRight: 50,
        justifySelf: 'flex-end',
    },
    title: {
        marginBottom: 30,
        textAlign: 'center'
    },
    formPaper: {
        width: '90%',
        maxWidth: 500,
    },
});

const formStyles = {
    formContent: {
        paddingTop: 30,
        paddingBottom: 20,
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
    },
    textField: {
        width: '90%',
        maxWidth: 400,
        marginBottom: 20
    },
    button: {
        width: '90%',
        maxWidth: 300,
        marginTop: 10,
        marginBottom: 10,
    },
};

class ResearcherSignup extends Component {
    state = {
        researcherID: "",
        password: "",
        isLoading: false,
    };

    handleInputChange = (event) => {
        this.setState({
            [event.target.id]: event.target.value,
        });
    }

    handleSubmit = async event => {
        event.preventDefault();
        this.setState({
            isLoading: true,
        });
        const { history } = this.props;

        try {
            await axios.post("/api/researcher/signup", {
                researcherID: this.state.researcherID,
                institution: this.state.institution,
                password: this.state.password
            });
            history.push('/login');
            this.setState({
                isLoading: false,
            });
        }
        catch (error) {
            this.setState({
                isLoading: false,
            });
            alert(error.message);
            console.log(error);
        }
    }

    render() {
        const { classes } = this.props;
        const { researcherID, password, isLoading } = this.state;
        const submitDisabled = researcherID === ""  || password === "" || isLoading;
        return (
            <form autoComplete="off" noValidate className={classes.formContent} >
                <TextField
                    variant="outlined"
                    required
                    label="Researcher ID"
                    id="researcherID"
                    onChange={this.handleInputChange}
                    className={classes.textField}
                />
                <TextField
                    variant="outlined"
                    required
                    type="password"
                    label="Password"
                    id="password"
                    onChange={this.handleInputChange}
                    className={classes.textField}
                /> 
                <Button
                    variant="contained"
                    className={classes.button}
                    color="primary"
                    disabled={submitDisabled}
                    onClick={this.handleSubmit}
                >
                    Submit
                </Button>
                {isLoading && <CircularProgress />}
            </form>
        );
    }
}

const ResearcherSignupForm = compose(
    withRouter,
    withStyles(formStyles),
)(ResearcherSignup);

class SignUp extends Component {

    handleChangeSignupMode = (event, value) => {
        this.setState({
            signupMode: value
        })
    }

    topBar = () => {
        const { classes } = this.props;

        return (
            <div className={classes.topBar}>
                <Hidden xsDown>
                    <Typography variant="subtitle1" color="textSecondary" className={classes.topBarTitle}>
                        Researcher Sign Up
                    </Typography>
                </Hidden>
                <Button 
                    variant="outlined"
                    color="secondary" 
                    className={classes.topBarButton} 
                    component={Link}
                    to="/login"
                >
                    Log in
                </Button>
            </div>
        );
    }

    render() {
        const { classes } = this.props; 
        return (
            <div className={classes.root}>
                {this.topBar()}
                <div className={classes.content}>
                    <div className={classes.title}>
                        <Typography variant="h4" gutterBottom>Welcom to Open Speech Platform</Typography>
                        <Typography variant="h6" gutterBottom color="textSecondary">WebApps for hearing-aid research</Typography>
                    </div>
                    <Paper className={classes.formPaper}>
                        <ResearcherSignupForm/>
                    </Paper>
                </div>
            </div>
        );
    }
}

export default withStyles(styles)(SignUp);
