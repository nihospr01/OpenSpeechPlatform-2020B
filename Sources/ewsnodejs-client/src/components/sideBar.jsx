import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Typography from '@material-ui/core/Typography';
import List from '@material-ui/core/List';
import Divider from '@material-ui/core/Divider';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';
import ExpandLess from '@material-ui/icons/ExpandLess';
import ExpandMore from '@material-ui/icons/ExpandMore';
import Collapse from '@material-ui/core/Collapse';
import { Link, withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import withUserAuth from 'context/withUserAuth';

const styles = theme =>( {
    list:{
        width: 300,
    },
    fullList:{
        width: 'auto',
    },
    drawerPaper:{
        width: 300,
    },
    nested:{
        paddingLeft: theme.spacing(4),
    },
    doublyNested:{
        paddingLeft: theme.spacing(8),
    },
    header: {
        paddingTop: 20,
        paddingLeft: 20,
        paddingBottom: 10
    }
});

class Sidebar extends Component{
    state = {
        fittingAppsOpen: false,
        demosOpen: false,
        adminOpen: true,
        ABOpen: false,
    };

    handleAdminOpen = () => {
        this.setState( prevState => (
            {
                adminOpen: !prevState.adminOpen,
            }   
          ));
    }

    handleDemos = () => {
        this.setState( prevState => (
            {
                demosOpen: !prevState.demosOpen,
            }   
          ));
    }

    handleFittingApps = () =>{
        this.setState( prevState => (
            {
                fittingAppsOpen: !prevState.fittingAppsOpen,
            }   
        ));
    }
       
    handleABOpen = ()=>{
        this.setState(prevState=>(
          {
              ABOpen: !prevState.ABOpen,
          }   
        ));
    }

    render(){
        const { classes } = this.props;
        //const { loginMode, assessmentDone, historyDone, AFCDone, PTAdone } = this.props.context;
        const loginMode = 'researcher';
        const assessmentDone = false;
        const { historyDone, AFCDone, PTAdone } = false;
        return(
            <div>
                <div className={classes.header}>
                    <Typography variant="h5" > 
                        Open Speech Platform
                    </Typography>
                    <Typography variant="subtitle1" color="textSecondary">
                        WebApps for hearing-aid research
                    </Typography>
                </div>
                <Divider />
                <List>
                    <ListItem button key="Researcher Page" component={Link} to="/main/researcherpage">
                        <ListItemText primary="Researcher Page"/>
                    </ListItem>
                    <ListItem button key="Coarse Fit Demo" component={Link} to="/main/coarseFitDemo">
                        <ListItemText primary="CoarseFit Demo"/>
                    </ListItem>
                    <ListItem button key="Freping Demo" component={Link} to="/main/freppingDemo">
                        <ListItemText primary="Freping Demo"/>
                    </ListItem>
                    <ListItem button key="Beamforming Demo" component={Link} to="/main/beamformingDemo">
                        <ListItemText primary="Beamforming Demo"/>
                    </ListItem> 
                    <ListItem button key="4AFC Demo" component={Link} to="/main/FourAFCDemo">
                        <ListItemText primary="4AFC Demo"/>
                    </ListItem>
                    <ListItem button key="Reset Page" component={Link} to="/main/reset">
                        <ListItemText primary="Reset"/>
                    </ListItem>
                </List>
            </div>      
        );
    }
}

Sidebar.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(Sidebar);