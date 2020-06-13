import React, { Component } from 'react';
import { withStyles } from '@material-ui/styles';
import Typography from '@material-ui/core/Typography';
import IconButton from '@material-ui/core/IconButton';
import Button from '@material-ui/core/Button';
import TableRow from '@material-ui/core/TableRow';
import TableCell from '@material-ui/core/TableCell';
import { withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import withUserAuth from 'context/withUserAuth';
import PropTypes from 'prop-types';
import AddCircleOutlineIcon from '@material-ui/icons/AddCircleOutline';
import RemoveCircleOutlineIcon from '@material-ui/icons/RemoveCircleOutline';
import PlayCircleOutlineIcon from '@material-ui/icons/PlayCircleOutline';
const styles = (theme) => ({
    root: {
        flexGrow: 1,
    },
    content: {
        padding: theme.spacing(3),
    },
    listenerSection: {
        marginTop: 30,
        width: '90%',
        maxWidth: 1000,
    },
    listenerSectionContent: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
    },
    topBarButton: {
        marginLeft: 30,
    },
    textField: {
        width: '90%',
        maxWidth: 400,
        marginBottom: 20,
    },
});

class FileListRow extends Component {
    state = {

    };
    
    render() {
        const { 
            filename,
            handlePlay,
            handleVolumeUp,
            handleVolumeDown
        } = this.props;
        
        return (
            <TableRow hover>
                <TableCell>
                    <Typography variant="subtitle1" color="textSecondary">{filename}</Typography>
                </TableCell>
                <TableCell>
                    <IconButton
                        variant={"contained"} 
                        onClick={() => handlePlay(filename)}
                    >
                        <PlayCircleOutlineIcon />
                    </IconButton>
                    <IconButton
                        aria-label="add"
                        onClick={() => handleVolumeUp(filename)}
                    >
                        <AddCircleOutlineIcon />
                    </IconButton>
                    <IconButton
                        aria-label="remove"
                        onClick={() => handleVolumeDown(filename)}
                    >
                        <RemoveCircleOutlineIcon />
                    </IconButton>
                </TableCell>
                
            </TableRow>
        );
    }
}

FileListRow.propTypes = {
    handlePlay: PropTypes.func.isRequired,
    handleVolumeUp: PropTypes.func.isRequired,
    handleVolumeDown: PropTypes.func.isRequired
};

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(FileListRow);