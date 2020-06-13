import React, { Component } from 'react';
import { axios } from 'utils/utils';
import { withStyles } from '@material-ui/core/styles';
import Typography from '@material-ui/core/Typography';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import Paper from '@material-ui/core/Paper';
import Hidden from '@material-ui/core/Hidden';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab'
import CircularProgress from '@material-ui/core/CircularProgress';
import { Link, withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import withUserAuth from 'context/withUserAuth';

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
        marginTop: 10
    },
})

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
}

class ListenerLogin extends Component {
    state = {
        listenerID: "",
        password: "",
    };

    handleInputChange = (event) => {
        this.setState({
            [event.target.id]: event.target.value,
        });
    }

    handleSubmit = async event => {
        const { history } = this.props;
        event.preventDefault();
        this.setState({
            isLoading: true,
        });
        try {
            const response = await axios.post("/api/listener/login", {
                listenerID: this.state.listenerID,
                password: this.state.password
            });
        
            const { listenerID } = response.data;
            sessionStorage.setItem('user', listenerID);
            sessionStorage.setItem('loginMode', 1);
            sessionStorage.setItem('historyDone', response.data.historyDone);
            sessionStorage.setItem('PTADone', response.data.PTADone);
            sessionStorage.setItem('assessmentDone', response.data.assessmentDone);
            sessionStorage.setItem('AFCDone', response.data.AFCDone);
            sessionStorage.setItem('leftEarIsWorse', response.data.leftEarIsWorse);
            await this.props.context.updateUser();
            history.push('/');
        }
        catch (error) {
            alert(error);
            this.setState({
                isLoading: false,
            });
            console.log(error);
        }
    }

    render() {
        const { classes } = this.props;
        const { listenerID, password, isLoading } = this.state;
        const submitDisabled = (listenerID === "" || password === "" || isLoading);
        return (
            <form autoComplete="off" noValidate className={classes.formContent}>
                <TextField
                    variant="outlined"
                    required
                    label="Listener ID"
                    id="listenerID"
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

const ListenerLoginForm = compose(
    withUserAuth,
    withRouter,
    withStyles(formStyles),
)(ListenerLogin);

class ResearcherLogin extends Component {
    state = {
        researcherID: "",
        password: "",
    };

    handleInputChange = (event) => {
        this.setState({
            [event.target.id]: event.target.value,
        });
    }

    handleSubmit = async event => {
        const { history } = this.props;
        event.preventDefault();
        this.setState({
            isLoading: true,
        });
        try {
            const response = await axios.post("/api/researcher/login", {
                researcherID: this.state.researcherID,
                password: this.state.password
            });
            const { researcherID } = response.data;
            sessionStorage.setItem('user', researcherID);
            sessionStorage.setItem('loginMode', 'researcher');
            sessionStorage.setItem('historyDone', false);
            sessionStorage.setItem('PTADone', false);
            sessionStorage.setItem('assessmentDone', false);
            sessionStorage.setItem('AFCDone', false);
            sessionStorage.setItem('leftEarIsWorse', false);
            await this.props.context.updateUser();
            history.push('/');
        }
        catch (error) {
            alert(error);
            this.setState({
                isLoading: false,
            });
            alert(error);
            console.log(error);
        }
    }

    render() {
        const { classes } = this.props;
        const { listenerID, password, isLoading } = this.state;
        const submitDisabled = (listenerID === "" || password === "" || isLoading);
        return (
            <form autoComplete="off" noValidate className={classes.formContent}>
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

const ResearcherLoginForm = compose(
    withUserAuth,
    withRouter,
    withStyles(formStyles),
)(ResearcherLogin);


class Login extends Component {
    state = {
        loginMode: 0,
        listenerID: "",
        password: "",
        isLoading: false,
    };

    handleInputChange = (event) => {
        this.setState({
            [event.target.id]: event.target.value,
        });
    }

    handleTabChange = (event, value) => {
        this.setState({
            loginMode: value,
        });
    }

    topBar = () => {
        const { classes } = this.props;

        return (
            <div className={classes.topBar}>
                <Hidden xsDown>
                    <Typography variant="subtitle1" color="textSecondary" className={classes.topBarTitle}>
                        Researcher and Listener Login
                    </Typography>
                </Hidden>
                <Button 
                    variant="outlined" 
                    color="secondary" 
                    className={classes.topBarButton}
                    component={Link}
                    to="/signup"
                >
                    Sign up
                </Button>
            </div>
        )
    }

    render() {
        const { classes } = this.props;
        const { loginMode } = this.state;
        return (
            <div className={classes.root}>
                {this.topBar()}
                <div className={classes.title}>
                        <Typography variant="h4" gutterBottom>Welcom to Open Speech Platform</Typography>
                        <Typography variant="h6" gutterBottom color="textSecondary">WebApps for hearing-aid research</Typography>
                    </div>
                <div className={classes.content}>
                    <Paper className={classes.formPaper}>
                    <Tabs
                        value={loginMode}
                        onChange={this.handleTabChange}
                        indicatorColor="primary"
                        textColor="primary"
                        variant="fullWidth"
                        id="loginMode"
                    >
                        <Tab label="Researcher Login" value={0}/>
                        <Tab label="Listener Login" value={1} />
                    </Tabs>
                        {loginMode === 0 && <ResearcherLoginForm/>}
                        {loginMode === 1 && <ListenerLoginForm/>}
                    </Paper>
                </div>
            </div>
        );
    }
}

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(Login);
