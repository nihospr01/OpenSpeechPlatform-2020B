import React, { Component } from 'react';
import { withStyles } from '@material-ui/styles';
import Typography from '@material-ui/core/Typography';
import Button from '@material-ui/core/Button';
import TableRow from '@material-ui/core/TableRow';
import TableCell from '@material-ui/core/TableCell';
import { withRouter } from 'react-router-dom';
import { compose } from 'recompose';
import withUserAuth from 'context/withUserAuth';
import PropTypes from 'prop-types';

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
            handleDelete,
        } = this.props;
        
        return (
            <TableRow hover>
                <TableCell>
                    <Typography variant="subtitle1" color="textSecondary">{filename}</Typography>
                </TableCell>
                <TableCell>
                    <Button
                        onClick={() => handleDelete(filename)}
                        color="secondary"
                    >
                        Delete
                    </Button>
                    {/* <Button
                        onClick={null}
                        color="secondary"
                    >
                        Replace
                    </Button> */}
                    {/* <Button
                        onClick={this.handleEditPasswordClose}
                        color="secondary"
                    >
                        Cancel
                    </Button> */}
                </TableCell>
                {/* <Dialog open={showLogOpen} fullWidth={true} maxWidth='xl' onClose={this.handleShowLogClose}>
                    <DialogTitle>Log</DialogTitle>
                    <DialogContent>
                        <Typography
                            variant="body1"
                            style={{
                                whiteSpace: 'pre-line'
                            }}
                        >
                            {listener.userLog}
                        </Typography>
                    </DialogContent>
                    <DialogActions>
                        <Button
                            color="primary"
                            onClick={this.exportTXT}
                        >
                            Export
                        </Button>
                        <Button
                            color="primary"
                            onClick={this.handleShowLogClose}
                        >
                            OK
                        </Button>
                    </DialogActions>
                </Dialog>
                <Dialog open={editPasswordOpen} onClose={this.handleEditPasswordClose}>
                    <DialogTitle>Edit Password</DialogTitle>
                    <DialogContent>
                        <TextField
                            variant="outlined"
                            label="Current Password"
                            disabled
                            value={listener.password}
                            style={{
                                width: '90%',
                                maxWidth: 400,
                                marginBottom: 20
                            }}
                        />
                        <TextField
                            variant="outlined"
                            label="New Password"
                            style={{
                                width: '90%',
                                maxWidth: 400,
                                marginBottom: 20
                            }}
                            onChange={this.handlePasswordChange}
                        />
                    </DialogContent>
                    <DialogActions>
                        <Button
                            onClick={this.handleEditPasswordClose}
                            color="secondary"
                        >
                            Cancel
                        </Button>
                        <Button
                            color="primary"
                            disabled={submitDisabled}
                            onClick={this.handleEditPassword}
                        >
                            Confirm
                        </Button>
                    </DialogActions>
                </Dialog>
                <Dialog open={deleteDialogOpen} onClose={this.handleClose}>
                    <DialogTitle>Delete Listener</DialogTitle>
                    <DialogContent>
                        <DialogContentText>
                            Warning! This action is NOT reversable.
                        </DialogContentText>
                    </DialogContent>
                    <DialogActions>
                        <Button
                            onClick={this.handleClose}
                            color="secondary"
                        >
                            Cancel
                        </Button>
                        <Button
                            color="primary"
                            onClick={this.handleDeleteListener}
                        >
                            Delete
                        </Button>
                    </DialogActions>
                </Dialog> */}
            </TableRow>
        );
    }
}

FileListRow.propTypes = {
    handleDelete: PropTypes.func.isRequired,
};

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(FileListRow);