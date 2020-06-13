import React, { Component, Fragment } from 'react';
import { axios } from 'utils/utils';
import List from '@material-ui/core/List';
import ListSubheader from '@material-ui/core/ListSubheader';
import ListItem from '@material-ui/core/ListItem';
import DialogContentText from '@material-ui/core/DialogContentText';
import TextField from '@material-ui/core/TextField';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import Dialog from '@material-ui/core/Dialog';
import DialogTitle from '@material-ui/core/DialogTitle';
import Button from '@material-ui/core/Button';
import Paper from '@material-ui/core/Paper';
import Select from 'react-select';

class ProfileSetting extends Component {
    state = {
        loadDialogOpen: false,
        saveDialogOpen: false,
        profileName: "",
        profiles: [],
        profileOptions: [],
        selectedProfile: "",
    };

    // CurrProfileBand could not be ready for initial render in componentDidMount
    // Re-filer in componentDidUpdate
    componentDidMount = async () => {
        const { currProfileBand } = this.props;
        try {
            const response = await axios.get("/api/param/amplification");
            let profiles = response.data;

            let profileOptions = [];
            profiles.forEach(element => {
                if(element.bandNumber ===  currProfileBand){
                    profileOptions.push({
                        value: element.parameters,
                        label: element.profileName,
                    });
                }
            })
            this.setState({
                profileOptions,
                profiles,
            });
        }
        catch (error) {
            alert("Error loading profiles");
        }
    }

    // Props are ready now, filter available profiles based on bandNumber
    componentDidUpdate  = (prevProps) => {
        const { currProfileBand } = this.props;
        const { profiles } = this.state;

        if(prevProps.currProfileBand !== currProfileBand){
            let profileOptions = [];
            profiles.forEach(element => {
                if(element.bandNumber ===  currProfileBand){
                    profileOptions.push({
                        value: element.parameters,
                        label: element.profileName,
                    });
                }
            })
            this.setState({
                profileOptions,
            });
        }

    }

    handleLoadDialogOpen = () => {
        this.setState({
            loadDialogOpen: true,
        });
    }

    handleLoadDialogClose = () => {
        this.setState({
            loadDialogOpen: false,
        });
    }

    handleSaveDialogOpen = () => {
        this.setState({
            saveDialogOpen: true,
        });
    }

    handleSaveDialogClose = () => {
        this.setState({
            saveDialogOpen: false,
        });
    }

    handleChangeProfileName = (event) => {
        this.setState({
            profileName: event.target.value,
        });
    }

    handleSaveParams = async () => {
        const { save } = this.props;
        const { profileName } = this.state;
        if (save != null) {
            try {
                await save(profileName);
                this.setState({
                    saveDialogOpen: false,
                });
            }
            catch (error) {
                console.log(error);
            }
        }
    }

    handleLoadProfile = async () => {
        const { load } = this.props;
        const { selectedProfile } = this.state;
        console.log(selectedProfile);
        if (load != null) {
            try {
                await load(selectedProfile);
            }
            catch (error) {
                console.log(error);
            } 
            this.setState({
                loadDialogOpen: false,
            });
        }
        else {
            this.setState({
                loadDialogOpen: false,
            });
        }
    }

    handleProfileChange = (profile) => {
        this.setState({
            selectedProfile: profile.value,
        });
    }
    
    // Disable loading profile for different bands number
    handleDisableSelect = () => {

    }
    render() {
        const { loadDialogOpen, saveDialogOpen } = this.state;
        return (
            <Fragment>
                <Paper style={{maxWidth: 350}}>
                    <List subheader={<ListSubheader>Parameter Settings</ListSubheader>} >
                        <ListItem>
                            <Button 
                            color="primary" 
                            variant="contained" 
                            fullWidth 
                            onClick={this.handleLoadDialogOpen}
                            >
                                Load Profile
                            </Button>
                        </ListItem>
                        <ListItem>
                            <Button 
                            color="primary" 
                            variant="contained" 
                            fullWidth 
                            onClick={this.handleSaveDialogOpen}
                            >
                                Save Profile
                            </Button>
                        </ListItem>
                    </List>
                </Paper>            
                <Dialog open={loadDialogOpen} scroll="body" onClose={this.handleLoadDialogClose}
                    fullWidth maxWidth="xs"
                >
                    <DialogTitle>Load Parameter Profile</DialogTitle>
                    <DialogContent>
                        <Select
                            onChange={this.handleProfileChange}
                            options={this.state.profileOptions}
                            styles={{ menuPortal: base => ({ ...base, zIndex: 9999 }) }}
                            menuPortalTarget={document.body}
                        />
                    </DialogContent>
                    <DialogActions>
                        <Button
                            onClick={this.handleLoadDialogClose}
                            color="secondary"
                        >
                            Cancel
                        </Button>
                        <Button
                            color="primary"
                            onClick={this.handleLoadProfile}
                        >
                            Load
                        </Button>
                    </DialogActions>
                </Dialog>
                <Dialog open={saveDialogOpen} onClose={this.handleSaveDialogClose}
                    fullWidth maxWidth="xs"
                >
                    <DialogTitle>Save Parameters</DialogTitle>
                    <DialogContent>
                        <DialogContentText>Please enter your profile name</DialogContentText>
                        <TextField
                            variant="outlined"
                            label="Profile Name"
                            fullWidth
                            onChange={this.handleChangeProfileName}
                        />
                    </DialogContent>
                    <DialogActions>
                        <Button
                            onClick={this.handleSaveDialogClose}
                            color="secondary"
                        >
                            Cancel
                        </Button>
                        <Button
                            color="primary"
                            onClick={this.handleSaveParams}
                        >
                            Save
                        </Button>
                    </DialogActions>
                </Dialog>
            </Fragment>
        );
    }
}

export default ProfileSetting;