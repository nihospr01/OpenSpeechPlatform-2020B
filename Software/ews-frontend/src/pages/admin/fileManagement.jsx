import React, { Component } from "react";
import { axios } from "utils/utils";
import { withStyles } from "@material-ui/styles";
import Paper from "@material-ui/core/Paper";
import Toolbar from "@material-ui/core/Toolbar";
import Typography from "@material-ui/core/Typography";
import Button from "@material-ui/core/Button";
import Table from "@material-ui/core/Table";
import TableHead from "@material-ui/core/TableHead";
import TableRow from "@material-ui/core/TableRow";
import TableBody from "@material-ui/core/TableBody";
import TableCell from "@material-ui/core/TableCell";
import Dialog from "@material-ui/core/Dialog";
import DialogTitle from "@material-ui/core/DialogTitle";
import DialogActions from "@material-ui/core/DialogActions";
import DialogContent from "@material-ui/core/DialogContent";
import TableSortLabel from "@material-ui/core/TableSortLabel";
import { withRouter } from "react-router-dom";
import { compose } from "recompose";
import withUserAuth from "context/withUserAuth";
import FileListRow from "components/fileListRow";

const styles = theme => ({
    root: {
        flexGrow: 1
    },
    content: {
        padding: theme.spacing(3)
    },
    listenerSection: {
        marginTop: 30,
        width: "90%",
        maxWidth: 1000
    },
    listenerSectionContent: {
        display: "flex",
        flexDirection: "column",
        alignItems: "center"
    },
    topBarButton: {
        marginLeft: 30
    },
    textField: {
        width: "90%",
        maxWidth: 400,
        marginBottom: 20
    }
});

class FileManagement extends Component {
    state = {
        fileList: [],
        fileOrder: "asc",
        uploadDialogOpen: false,
        selectedFiles: []
    };

    componentDidMount = async () => {
        this.getFileNames();
    };

    getFileNames = async () => {
        try {
            const response = await axios.get("/api/researcher/fileNames");
            this.setState({ fileList: response.data });
        } catch (error) {
            alert(error.response.data);
        }
    };

    handleSortChange = () => {
        const { fileOrder } = this.state;
        const newOrder = fileOrder === "asc" ? "desc" : "asc";
        this.setState({
            fileOrder: newOrder
        });
    };

    handleUploadDialogOpen = () => {
        this.setState({ uploadDialogOpen: true });
    };

    handleUploadDialogClose = () => {
        this.setState({ uploadDialogOpen: false });
    };

    handleDelete = async filename => {
        try {
            await axios.post("/api/researcher/delete", {
                filename: filename
            });
        } catch (err) {
            throw err;
        }
        this.getFileNames();
    };

    uploadFiles = async () => {
        console.log(this.state.selectedFiles);
        const data = new FormData();
        for (var x = 0; x < this.state.selectedFiles.length; x++) {
            data.append("file", this.state.selectedFiles[x]);
        }
        try {
            const response = await axios.post(
                "/api/researcher/upload",
                data,
                {}
            );
            console.log(response.data);
        } catch (error) {
            alert(error);
        }
        this.getFileNames();
        this.handleUploadDialogClose();
    };

    onChangeHandler = event => {
        this.setState({
            selectedFiles: event.target.files
        });
    };

    render() {
        const { classes } = this.props;
        const { fileOrder, fileList, uploadDialogOpen } = this.state;
        console.log(fileList);
        const sortedFileList = [...fileList].sort((a, b) => {
            if (a === "4AFCConfig.json" || a === "UserAssessmentConfig.json") {
                return -1;
            }
            if (b === "4AFCConfig.json" || b === "UserAssessmentConfig.json") {
                return 1;
            }
            if (fileOrder === "asc") {
                if (a > b) {
                    return 1;
                } else {
                    return -1;
                }
            } else {
                if (a < b) {
                    return 1;
                } else {
                    return -1;
                }
            }
        });

        return (
            <div className={classes.content}>
                <Paper className={classes.listenerSection}>
                    <Toolbar>
                        <Typography color="textSecondary" gutterBottom>
                            My Files
                        </Typography>
                        <Button
                            color="primary"
                            variant="contained"
                            className={classes.topBarButton}
                            onClick={this.handleUploadDialogOpen}
                        >
                            Upload Files
                        </Button>
                    </Toolbar>
                    <div className={classes.listenerSectionContent}>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>
                                        <TableSortLabel
                                            active={true}
                                            direction={fileOrder}
                                            onClick={this.handleSortChange}
                                        >
                                            <Typography
                                                variant="subtitle1"
                                                color="textSecondary"
                                            >
                                                File Name
                                            </Typography>
                                        </TableSortLabel>
                                    </TableCell>
                                    <TableCell>
                                        <Typography
                                            variant="subtitle1"
                                            color="textSecondary"
                                        >
                                            Actions
                                        </Typography>
                                    </TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {sortedFileList.map((filename, index) => (
                                    <FileListRow
                                        key={`file_${index}`}
                                        filename={filename}
                                        handleDelete={this.handleDelete}
                                    />
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                    <Dialog
                        open={uploadDialogOpen}
                        onClose={this.handleUploadDialogClose}
                    >
                        <DialogTitle>Upload File</DialogTitle>
                        <DialogContent>
                            <input
                                type="file"
                                multiple
                                onChange={this.onChangeHandler}
                            />
                        </DialogContent>
                        <DialogActions>
                            <Button color="primary" onClick={this.uploadFiles}>
                                Submit
                            </Button>
                        </DialogActions>
                    </Dialog>
                </Paper>
            </div>
        );
    }
}

export default compose(
    withUserAuth,
    withRouter,
    withStyles(styles)
)(FileManagement);
