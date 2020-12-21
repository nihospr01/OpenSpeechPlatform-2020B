import React, { Component } from 'react';
import { axios } from 'utils/utils';
import { withStyles } from '@material-ui/styles';
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import Button from '@material-ui/core/Button';
import IconButton from '@material-ui/core/IconButton';
import Slider from '@material-ui/core/Slider';
import VolumeDownIcon from '@material-ui/icons/VolumeDown';
import VolumeUpIcon from '@material-ui/icons/VolumeUp';
import AddCircleOutlineIcon from '@material-ui/icons/AddCircleOutline';
import RemoveCircleOutlineIcon from '@material-ui/icons/RemoveCircleOutline';
import PlayCircleOutlineIcon from '@material-ui/icons/PlayCircleOutline';
import * as d3 from 'd3';
import treeNodes from 'utils/BST/treeNodes.csv'
import rootNodes from 'utils/BST/rootNodes.csv'
import { parseTreeNode } from 'utils/BST/BSTUtil';
import withUserAuth from 'context/withUserAuth';
import { withRouter, Link } from 'react-router-dom';
import { compose } from 'recompose';
import FileListRow from 'components/fileTable'

import Table from '@material-ui/core/Table';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableSortLabel from '@material-ui/core/TableSortLabel';
import Typography from '@material-ui/core/Typography';
import { display } from '@material-ui/system';



const styles = (theme) => ({
    slider: {
        width: '60vw',
        marginLeft: 30,
        marginRight: 30,
    },
    icon: {
        maxWidth: '10vw',
        marginBottom: 20,
    },
    title: {
        margin: 10,
    },
    img: {
        height: 'auto',
        width: '60vw',
    },
    button: {
        margin: 10,
    },
    iconButton: {
        height: 50,
        width: 50,
    },
    iconButtonMarginLeft: {
        // marginRight: '28vw',
    },
    iconButtonMarginRight: {
        // marginLeft: '28vw',
    },
    buttonGroup: {
        width: 180,
        height: 180,
    },
});



class AssessmentSetup extends Component {
    state = {
        min: 0,
        max: 0
    };

    decreaseSound = () => {
        this.setState({ currentNode: this.state.currentNode > 1 ? this.state.currentNode - 1 : 1 }, 
            this.handleApply);
    }

    increaseSound = () => {
        this.setState({ currentNode: this.state.currentNode < 5 ? this.state.currentNode + 1 : 5 },
            this.handleApply);
    }

    playFile = () => {}
    // setupDone = () => {
    //     this.setState({
    //         min: newMin,
    //         max: newMax
    //     })
    // }

    render() {
        const { classes } = this.props;

        return (
            <Paper className={classes.paper}>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                }}>
                    <h1 class="display-5">
                            Search Example
                    </h1>
                </div>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                }}>
                    <p class="text-secondary" id="description">
                            Choose between two set of changing parameters to find your optimal setting.
                    </p>
                </div>
                    {/* <div>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>
                                        <TableSortLabel>
                                            <Typography variant="subtitle1" color="textSecondary">File Name</Typography>
                                        </TableSortLabel>
                                        </TableCell>
                                    <TableCell>
                                        <Typography variant="subtitle1" color="textSecondary">Actions</Typography>
                                    </TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {
                                    file.map((filename, index) => (
                                        <FileListRow
                                            key={`file_${index}`}
                                            filename={filename}
                                            handlePlay={this.handlePlay}
                                            handleVolumeUp={this.increaseSound}
                                            handleVolumeDown={this.decreaseSound}
                                        />
                                    ))
                                }
                            </TableBody>
                        </Table>
                    </div> */}
                    
                <Grid 
                    container
                    direction="column"
                    justify="flex-end"
                    alignItems="center"
                    key="options"
                >
                    <Button
                        className={classes.button}
                        variant="contained" 
                        color="primary"
                        onClick={this.playFile}
                        component={Link}
                        to={`/qualityAssessmentDemo/`}
                    >
                        start
                    </Button>
                    <Button
                        className={classes.button}
                        variant="outlined" 
                        color="primary"
                            //onClick={this.setupDone}
                        component={Link}
                        to={`/qualityAssessmentDemo/`}
                    >
                        exit
                    </Button>
                </Grid>
            </Paper>
        );
    }
}

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(AssessmentSetup);