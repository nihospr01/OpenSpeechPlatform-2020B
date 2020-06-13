import React, { Component } from "react";
import { axios } from "utils/utils";
import { withStyles } from "@material-ui/styles";
import Typography from "@material-ui/core/Typography";
import Button from "@material-ui/core/Button";
import DialogContentText from "@material-ui/core/DialogContentText";
import TextField from "@material-ui/core/TextField";
import TableRow from "@material-ui/core/TableRow";
import TableCell from "@material-ui/core/TableCell";
import Dialog from "@material-ui/core/Dialog";
import DialogTitle from "@material-ui/core/DialogTitle";
import DialogActions from "@material-ui/core/DialogActions";
import DialogContent from "@material-ui/core/DialogContent";
import IconButton from "@material-ui/core/IconButton";
import DeleteIcon from "@material-ui/icons/Delete";
import EditIcon from "@material-ui/icons/Edit";
import InfoOutlinedIcon from "@material-ui/icons/InfoOutlined";
import { withRouter } from "react-router-dom";
import { compose } from "recompose";
import withUserAuth from "context/withUserAuth";

import Checkbox from "@material-ui/core/Checkbox";
import ListenerLog from "./listenerLog";

const styles = (theme) => ({
  root: {
    flexGrow: 1,
  },
  content: {
    padding: theme.spacing(3),
  },
  listenerSection: {
    marginTop: 30,
    width: "90%",
    maxWidth: 1000,
  },
  listenerSectionContent: {
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
  },
  topBarButton: {
    marginLeft: 30,
  },
  textField: {
    width: "90%",
    maxWidth: 400,
    marginBottom: 20,
  },
});

class ListenerListRow extends Component {
  state = {
    listener: this.props.listener,
    showLogOpen: false,
    deleteDialogOpen: false,
    editPasswordOpen: false,
    newPassword: "",
    currTabIdx: 0,
    listenerLog: JSON.parse(this.props.listener.userLog),
  };


  handleTabChange = (event, currTabIdx) => {
    this.setState({
      currTabIdx,
    });
  };
  handleOpen = () => {
    this.setState({
      deleteDialogOpen: true,
    });
  };

  handleClose = () => {
    this.setState({
      deleteDialogOpen: false,
    });
  };

  handleShowLogOpen = () => {
    this.setState({
      showLogOpen: true,
    });
  };

  handleShowLogClose = () => {
    this.setState({
      showLogOpen: false,
    });
  };

  handleEditPasswordOpen = () => {
    this.setState({
      editPasswordOpen: true,
    });
  };

  handleEditPasswordClose = () => {
    this.setState({
      editPasswordOpen: false,
    });
  };

  handlePasswordChange = (event) => {
    this.setState({
      newPassword: event.target.value,
    });
  };

  handleDeleteListener = async () => {
    try {
      const response = await axios.post("/api/listener/delete", {
        listenerID: this.state.listener.listenerID,
      });
      console.log(response.data);
      window.location.reload();
    } catch (error) {
      alert(error);
    }
  };

  handleEditPassword = async () => {
    try {
      const response = await axios.post("/api/listener/password", {
        listenerID: this.state.listener.listenerID,
        newPassword: this.state.newPassword,
      });
      console.log(response.data);
      window.location.reload();
    } catch (error) {
      alert(error);
    }
  };


  render() {
    const {
      listener,
      deleteDialogOpen,
      editPasswordOpen,
      newPassword,
      listenerLog,
      showLogOpen,
    } = this.state;
    const submitDisabled =
      newPassword === "" || newPassword === listener.password;

    return (
      <TableRow hover>
        <TableCell padding="checkbox">
          <Checkbox
            checked={this.props.selected}
            onChange={this.props.onClick}
          />
        </TableCell>
        <TableCell>
          <Typography variant="subtitle1" color="textSecondary">
            {listener.listenerID}
          </Typography>
        </TableCell>
        <TableCell>
          <Typography variant="subtitle1" color="textPrimary">
            {listener.createdAt}
          </Typography>
        </TableCell>
        <TableCell>
          <Typography variant="subtitle1" color="textPrimary">
            {listener.updatedAt}
          </Typography>
        </TableCell>
        <TableCell>
          <IconButton color="primary" onClick={this.handleShowLogOpen}>
            <InfoOutlinedIcon />
          </IconButton>
          <IconButton color="primary" onClick={this.handleEditPasswordOpen}>
            <EditIcon />
          </IconButton>
          <IconButton color="secondary" onClick={this.handleOpen}>
            <DeleteIcon />
          </IconButton>
        </TableCell>
        <Dialog
          open={showLogOpen}
          fullWidth={true}
          maxWidth="lg"
          onClose={this.handleShowLogClose}
        >
          <ListenerLog log={listenerLog} open={showLogOpen} />
          <DialogActions>
            <Button color="primary" onClick={this.handleShowLogClose}>
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
                width: "90%",
                maxWidth: 400,
                marginBottom: 20,
              }}
            />
            <TextField
              variant="outlined"
              label="New Password"
              style={{
                width: "90%",
                maxWidth: 400,
                marginBottom: 20,
              }}
              onChange={this.handlePasswordChange}
            />
          </DialogContent>
          <DialogActions>
            <Button onClick={this.handleEditPasswordClose} color="secondary">
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
            <Button onClick={this.handleClose} color="secondary">
              Cancel
            </Button>
            <Button color="primary" onClick={this.handleDeleteListener}>
              Delete
            </Button>
          </DialogActions>
        </Dialog>
      </TableRow>
    );
  }
}

export default compose(
  withUserAuth,
  withRouter,
  withStyles(styles)
)(ListenerListRow);
