import React, {Component} from 'react';
import { axios } from 'utils/utils';
import { withStyles } from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import Dialog from '@material-ui/core/Dialog';
import DialogTitle from '@material-ui/core/DialogTitle';
import CircularProgress from '@material-ui/core/CircularProgress';
import { compose } from 'recompose';
import withUserAuth from 'context/withUserAuth';

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
}

class ListenerSignup extends Component {
    state = {
        listenerID: "",
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
        const { handleClose } = this.props;
        const { user } = this.props.context;
        try {
            const response = await axios.post("http://localhost:5000/api/listener/signup", {
                listenerID: this.state.listenerID,
                researcher: user,
                password: this.state.password
            });
            if (handleClose != null) {
                handleClose();
            }
            console.log(response);
            window.location.reload();
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
        const { classes, open, handleClose } = this.props;
        const { listenerID, password, isLoading } = this.state;
        const submidDisabled = (listenerID === "" || password === "" || isLoading);
        return (
            <div>
                <Dialog open={open} onClose={handleClose}>
                    <DialogTitle id="form-dialog-title">Create Listener</DialogTitle>
                    <DialogContent>
                        <TextField
                            variant="outlined"
                            required
                            label="listener ID"
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
                        {isLoading && <CircularProgress />}
                    </DialogContent>
                    <DialogActions>
                        <Button 
                            onClick={handleClose} 
                            color="secondary"                       
                        >
                            Cancel
                        </Button>
                        <Button 
                            onClick={this.handleSubmit} 
                            color="primary"
                            disabled={submidDisabled}
                        >
                            Create
                        </Button>
                    </DialogActions>
                </Dialog>
            </div>
        );
    }
}

export default compose(
    withUserAuth,
    withStyles(formStyles),
)(ListenerSignup);
