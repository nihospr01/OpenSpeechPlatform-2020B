import React, { Component } from 'react';
import { axios } from 'utils/utils';
import { withRouter, Link } from 'react-router-dom';
import withUserAuth from 'context/withUserAuth';
import { compose } from 'recompose';
import * as d3 from 'd3';
import { withStyles } from '@material-ui/styles';
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import Button from '@material-ui/core/Button';
import Slider from '@material-ui/core/Slider';
import VolumeDownIcon from '@material-ui/icons/VolumeDown';
import VolumeUpIcon from '@material-ui/icons/VolumeUp';
import treeNodes from 'utils/BST/treeNodes.csv'
import rootNodes from 'utils/BST/rootNodes.csv'
import Demo1 from 'utils/images/hearingLossDemo/demo1.png';
import Demo2 from 'utils/images/hearingLossDemo/demo2.png';
import Demo3 from 'utils/images/hearingLossDemo/demo3.png';
import Demo4 from 'utils/images/hearingLossDemo/demo4.png';
import Demo5 from 'utils/images/hearingLossDemo/demo5.png';
import { parseTreeNode } from 'utils/BST/BSTUtil';

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
});

class HearingLossDemo extends Component {
    state = {
        nodes: {},
        options: {},
        currentNode: 1,
    };

    componentDidMount = async () => {
        this._isMounted = true;
        const { options } = this.state;
        if ( Object.keys(options).length === 0 ) {
            await this.constructGraph();
        }
    }

    componentWillUnmount(){
        this._isMounted= false
    }

    constructGraph = async () => {
        const nodes = {};
        const options = {};

        await d3.csv(rootNodes, (rootNode) => {
            options[rootNode['NodeNo']] = rootNode['HearingLoss'];
        });

        await d3.csv(treeNodes, (treeNode) => {
            nodes[treeNode['NodeNo']] = parseTreeNode(treeNode);
        });
        
        if (this._isMounted) {
            this.setState({ options, nodes });
        }
        
    }

    getMarks = () => {
        const marks = [];
        const { options } = this.state;
        Object.keys(options).forEach( node => {
            marks.push( {
                value: 25 * (node - 1),
                label: options[node],
            })
        });
        return marks;
    }

    getImage = () => {
        switch (this.state.currentNode) {
            case 1: return Demo1;
            case 2: return Demo2;
            case 3: return Demo3;
            case 4: return Demo4;
            case 5: return Demo5;
            default: return;
        }
    }

    handleApply = async () => {
        const { currentNode, nodes } = this.state;
        try {
            const response = await axios.post("/api/param/setparam", {
                method: 'set',
                data: nodes[currentNode],
            });
            const data = response.data;
            console.log(data);
        }
        catch(error) {
            alert(error.response.data);
        }
    }

    handleSliderChange = (event, value) => {
        this.setState({ currentNode: value / 25 + 1 });
        sessionStorage.setItem('rootNode', parseInt(value / 25 + 1));
    }

    render() {
        const { classes } = this.props;
        const { 
            currentNode,
            options,
        } = this.state;
        return (
            <Paper className={classes.paper}>
                <Grid container spacing={1}>
                    <Grid item xs={12} className={classes.title}>
                        Please adjust the hearing loss:
                    </Grid>
                    <Grid 
                        container
                        direction="row"
                        justify="center"
                        alignItems="center"
                    >
                        <VolumeDownIcon className={classes.icon}/>
                        {
                            options ? 
                                <Slider
                                    value={(currentNode - 1) * 25}
                                    aria-labelledby="discrete-slider-always"
                                    step={25}
                                    onChangeCommitted={this.handleSliderChange}
                                    marks={this.getMarks()}
                                    track={false}
                                    className={classes.slider}
                                /> : null
                        }
                        <VolumeUpIcon className={classes.icon}/>
                    </Grid>
                    <Grid 
                        container
                        direction="row"
                        justify="center"
                        alignItems="center"
                    >
                        <img
                            src={this.getImage()}
                            alt={`Demo_${currentNode}`}
                            className={classes.img}
                        />
                    </Grid>
                    <Grid 
                        container
                        direction="row"
                        justify="flex-end"
                        alignItems="center"
                    >
                        <Button
                            className={classes.button}
                            variant="outlined" 
                            color="primary"
                            onClick={this.handleApply}
                        >
                            Transmit
                        </Button>
                        <Button
                            className={classes.button}
                            variant="outlined" 
                            color="primary"
                            onClick={null}
                            component={Link}
                            to={'/researcherpage'}
                        >
                            Continue
                        </Button>
                    </Grid>
                </Grid>
            </Paper>
        );
    }
}

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(HearingLossDemo);