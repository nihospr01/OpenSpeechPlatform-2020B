import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';
import Hidden from '@material-ui/core/Hidden';
import IconButton from '@material-ui/core/IconButton';
import Button from '@material-ui/core/Button';
import Drawer from '@material-ui/core/Drawer'; 
import MenuIcon from '@material-ui/icons/Menu'
import SideBar from 'components/sideBar';
import { compose } from 'recompose';
import Stimulus from 'pages/general/stimulus'
import ResearcherPage from 'pages/general/researcherPage';
import SurveyPage from 'pages/BST/surveyPage';
import PureTonePage from 'pages/BST/pureTonePage';
import AssessmentPage from 'pages/BST/assessment/assessmentPage';
import AssessmentSetup from 'pages/BST/assessment/assessmentSetup';
import QualityAssessmentDemo from 'pages/demo/qualityAssessmentDemo';
import QualityAssessmentSetup from 'pages/demo/qualityAssessmentSetup';
import FourAFCPage from 'pages/BST/fourAFCPage';
import CoarseFitDemo from 'pages/demo/coarseFitDemo';
import FreppingDemo from 'pages/demo/freppingDemo';
import ListenerManagement from 'pages/admin/listenerManagement';
import FileManagement from 'pages/admin/fileManagement';
import withUserAuth from 'context/withUserAuth';
import BeamformingDemo from 'pages/demo/beamformingDemo';
import FourAFCDemo from 'pages/demo/FourAFCDemo';
import VideoDemo from 'pages/demo/videoDemo';
import Testing from 'pages/demo/test'
import Reset from 'pages/demo/reset'
import { withRouter, Switch, Route } from 'react-router-dom';
const sidebarWidth = 270;

const styles = theme => ({
    root: {
        display: 'flex',
    },
    appBar: {
        [theme.breakpoints.up('md')]: {
            width: `calc(100% - ${sidebarWidth}px)`,
            marginLeft: sidebarWidth,
        },
    },
    menuButton:{
        marginRight: 20,
        [theme.breakpoints.up('md')]:{
          display: 'none',
        },
    },
    toolbar: theme.mixins.toolbar,
    drawer:{
        [theme.breakpoints.up('md')]: {
          width: sidebarWidth,
          flexShrink: 0,
        },
    },
    drawerPaper:{
        width: sidebarWidth
    },
    content: {
        flexGrow: 1,
        marginTop: 70
    }
});

const routes = [
    {
        path: "/listenermanagement",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                            Listener Management
                            </Typography>,
        main: ()=><ListenerManagement />
    },
    {
        path: "/filemanagement",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                              File Management
                            </Typography>,
        main: ()=><FileManagement />
    },
    {
        path: "/stimulus",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                            Stimulus
                        </Typography>,
        main: ()=><Stimulus />
    },
    {
        path: "/main/researcherpage",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                            Researcher Page
                        </Typography>,
        main: ()=><ResearcherPage />
    },  
    {
        path: "/surveypage",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                            Patient History
                        </Typography>,
        main: ()=><SurveyPage />
    }, 
    {
        path: "/PTApage",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                              Pure Tone Audiogram
                              <div>(Researcher Only)</div>
                        </Typography>,
        main: ()=><PureTonePage />
    },
    {
        path: "/assessmentpageSetup/:time",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                              User Assessment
                        </Typography>,
        main: ()=><AssessmentSetup />
    },
    {
        path: "/assessmentpage/:currentIsLeft",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                              User Assessment
                        </Typography>,
        main: ()=><AssessmentPage showNodeNumber={false}/>
    },
    {
        path: "/qualityAssessmentSetup/:time",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                              Quality Assessment Demo    
                        </Typography>,
        main: ()=><QualityAssessmentSetup />
    },
    {
        path: "/qualityAssessmentDemo/",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                              Quality Assessment Demo
                        </Typography>,
        main: ()=><QualityAssessmentDemo showNodeNumber={false}/>
    },
    {
        path: "/4AFCpage",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                              User Assessment
                        </Typography>,
        main: ()=><FourAFCPage />
    },
    {
        path: "/main/coarseFitDemo",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                              CoarseFit Demo
                        </Typography>,
        main: ()=><CoarseFitDemo />
    },
    {
        path: "/main/freppingDemo",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                              Freping Demo
                        </Typography>,
        main: ()=><FreppingDemo />
    },
    {
        path: "/main/beamformingDemo",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                                Beamforming Demo
                        </Typography>,
        main: ()=><BeamformingDemo />
    },
    {
        path: "/main/FourAFCDemo",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                                4AFC Demo
                        </Typography>,
        main: ()=><FourAFCDemo />
    },
    {
        path: "/videoDemo",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                                Video Playing Demo
                        </Typography>,
        main: ()=><VideoDemo />
        
    },
    {
        path: "/test",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                                Testing
                        </Typography>,
        main: ()=><Testing />
        
    },
    {
        path: "/main/reset",
        appBarTitle: () =>  <Typography variant="h6" color="inherit" style={{flexGrow:1}}>
                                
                        </Typography>,
        main: ()=><Reset />
        
    },
  ];

class MainPage extends Component {
    state = {
        drawerOpen: false,
    };

    // componentDidMount = async () => {
    //     const { updateUser } = this.props.context;
    //     await updateUser();
    //     const { user, loginMode } = this.props.context;
        
    //     console.log(user, loginMode);
    //     if (true){
    //     //if (user && loginMode) {
    //         if (loginMode === 'researcher' ) {
    //             this.props.history.push('/listenermanagement');
    //         }
    //         else {
    //             this.props.history.push('/researcherpage');
    //         }
    //     }
    //     else {
    //         this.props.history.push('/login');
    //     }
    // }

    handleDrawerToggle = () => {
        this.setState(state => ({drawerOpen: !state.drawerOpen}));
    }

    handleLogOut = () => {
        sessionStorage.clear();
        window.location.reload();
    }

    render() {
        const { classes } = this.props;
        //const { loginMode, assessmentDone } = this.props.context;
        const loginMode = 'researcher';
        const assessmentDone = false;
        return (
            <div className={classes.root}>
                <AppBar position="fixed"  className={classes.appBar}>
                    <Toolbar>
                        <IconButton
                            color="inherit"
                            edge="start"
                            className={classes.menuButton}
                            onClick={this.handleDrawerToggle}
                        >
                            <MenuIcon />
                        </IconButton>
                        <Switch>
                            {routes.map((route,index)=>(
                                <Route
                                    key = {index}
                                    path = {route.path}
                                    component = {route.appBarTitle}
                                />
                            ))}
                        </Switch>
                    </Toolbar>
                </AppBar>
                <nav className ={classes.drawer}>
                    <Hidden mdUp im mentation = "css">
                        <Drawer
                            classes = {{
                                paper: classes.drawerPaper,
                            }}
                            anchor = 'left'
                            variant = "temporary"
                            open = {this.state.drawerOpen}
                            onClose = {this.handleDrawerToggle}
                        >
                            <SideBar 
                                loginMode={loginMode}
                                assessmentDone={assessmentDone}
                            />
                        </Drawer>
                    </Hidden>
                    <Hidden smDown implementation = "css">
                        <Drawer
                            classes = {{
                                paper: classes.drawerPaper,
                            }}
                            variant = "permanent"
                            open
                        >
                            <SideBar 
                                loginMode={loginMode}
                                assessmentDone={assessmentDone}
                            />
                        </Drawer>
                    </Hidden>
                </nav>
                <main className={classes.content}>
                <Switch>
                    {routes.map((route,index)=>(
                        <Route 
                        key = {index}
                        path = {route.path}
                        component = {route.main}
                        />
                    ))}
                </Switch>
                </main>
            </div>
        );
    }
}

MainPage.protoTypes = {
    classes: PropTypes.object.isRequired,
};

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(MainPage);