import React, { Component } from 'react';
import Tab from '@material-ui/core/Tab';
import Tabs from '@material-ui/core/Tabs';
import { withStyles } from '@material-ui/styles';
import Amplification from 'components/amplification';
import Paper from '@material-ui/core/Paper';
import { Hidden } from '@material-ui/core';
import NoiseManagement from 'components/noiseManagement';
import FeedbackManagement from 'components/feedbackManagement';

const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    content: {
        padding: theme.spacing(3)
    },
    mobileTabs: {
        width: '100vw',
    },
});

class ResearcherPage extends Component {
    state = {
        value: 0
    };

    handleTabChange = (event, value) => {
        this.setState({
            value: value,
        })
    }

    render() {
        const { classes } = this.props;
        return (
            <div>
                <Paper className={classes.root}>
                    <Hidden smDown implementation="css">
                        <Tabs
                            value={this.state.value}
                            centered
                            onChange={this.handleTabChange}
                        >
                            <Tab label="Amplification" />
                            <Tab label="Noise"/>
                            <Tab label="Feedback"/>
                            {/* <Tab label="Stimulus"/> */}
                        </Tabs>
                    </Hidden>
                    <Hidden mdUp implementation="css">
                        <Tabs
                            value={this.state.value}
                            onChange={this.handleTabChange}
                            variant="scrollable"
                            scrollButtons="on"
                            className={classes.mobileTabs}
                        >
                            <Tab label="Amplification"/>
                            <Tab label="Noise"/>
                            <Tab label="Feedback"/>
                            {/* <Tab label="Stimulus"/> */}
                        </Tabs>
                    </Hidden>
                    {
                        this.state.value === 0 && <Amplification />
                    }
                    {
                        this.state.value === 1 && <NoiseManagement />
                    }
                    {
                        this.state.value === 2 && <FeedbackManagement />
                    }
                    {/* {
                        this.state.value === 3 && <Stimulus />
                    } */}
                </Paper>
                
            </div>
        );
    }
}

export default withStyles(styles)(ResearcherPage);
