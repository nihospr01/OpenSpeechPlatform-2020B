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
        const { loginMode, assessmentDone, historyDone, AFCDone, PTAdone } = this.props.context;

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
                    { loginMode === 'researcher' && 
                        [
                            <ListItem button key = "Admin" onClick = {this.handleAdminOpen}>
                                <ListItemText primary = "Admin"/>
                                {this.state.adminOpen ? <ExpandLess/> : <ExpandMore/>}
                            </ListItem>,
                            <Collapse key = "AdminTab" in = {this.state.adminOpen} timeout = "auto" unmountOnExit>
                                <List component = "div" disablePadding>
                                    <ListItem className = {classes.nested} button key="ListenerManagement" component={Link} to="/listenermanagement" >
                                        <ListItemText primary="Listener Management"  />
                                    </ListItem>
                                    <ListItem className = {classes.nested} button key="FileManagement" component={Link} to="/filemanagement" >
                                        <ListItemText primary="File Management"  />
                                    </ListItem>
                                </List>
                            </Collapse>
                        ]
                    }
                    <ListItem button key="Stimulus" component={Link} to="/stimulus">
                        <ListItemText primary="Stimulus"/>
                    </ListItem>
                    <ListItem button key="Researcher Page" component={Link} to="/researcherpage">
                        <ListItemText primary="Researcher Page"/>
                    </ListItem>
                    {
                        historyDone !== true &&
                            <ListItem button key="Survey Page" component={Link} to="/surveypage">
                                <ListItemText primary="Patient History"/>
                            </ListItem>
                    }
                    {
                        PTAdone !== true && 
                            <ListItem button key="Pure Tone Audiogram" component={Link} to="/PTApage">
                                <ListItemText primary="Pure Tone Audiogram"/>
                            </ListItem>
                    }
                    <ListItem button key = "Demos" onClick = {this.handleDemos}>
                        <ListItemText primary = "Demos"/>
                        {this.state.demosOpen? <ExpandLess/> : <ExpandMore/>}
                    </ListItem>
                    <Collapse in = {this.state.demosOpen} timeout = "auto" unmountOnExit>
                        <List component = "div" disablePadding>
                            <ListItem className = {classes.nested} button key="Hearing Loss Demo" component={Link} to="/hearingLossDemo">
                                <ListItemText primary="Hearing Loss Demo"/>
                            </ListItem>  
                            <ListItem className = {classes.nested} button key="Quality Assessment Demo" component={Link} to="/qualityAssessmentDemo">
                                <ListItemText primary="Quality Assessment Demo"/>
                            </ListItem>   
                        </List>
                    </Collapse>
                    <ListItem button key = "BST Apps" onClick = {this.handleFittingApps}>
                        <ListItemText primary = "BST Apps"/>
                        {this.state.fittingAppsOpen? <ExpandLess/> : <ExpandMore/>}
                    </ListItem>
                    <Collapse in = {this.state.fittingAppsOpen} timeout = "auto" unmountOnExit>
                        <List component = "div" disablePadding>
                            {
                                assessmentDone !== true &&
                                    <ListItem className = {classes.nested} button key="User Assessment Page" component={Link} to="/assessmentpageSetup/first">
                                        <ListItemText primary="User Assessment"/>
                                    </ListItem>
                            }
                            {
                                AFCDone !== true && 
                                    <ListItem className = {classes.nested} button key="4AFC Page" component={Link} to="/4AFCpage">
                                        <ListItemText primary="4AFC"/>
                                    </ListItem>
                            }
                            {/* <ListItem className = {classes.nested} button key = "AB Tasks" onClick = {this.handleABOpen}>
                                <ListItemText primary = "AB Tasks"/>
                                {this.state.ABOpen? <ExpandLess /> : <ExpandMore />}
                            </ListItem>
                            <Collapse in={this.state.ABOpen} timeout="auto" unmountOnExit>
                                <List component="div" disablePadding>
                                    {['AB','SearchEx'].map((text,index)=>(
                                    <ListItem className = {classes.doublyNested} button key = {text}>
                                        <ListItemText primary = {text}/>
                                    </ListItem>
                                ))}
                                </List>
                            </Collapse>       */}
                        </List>
                    </Collapse>
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