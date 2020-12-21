import React, { Component } from 'react';

const userContext = React.createContext();

class UserAuthProvider extends Component {
    state = {
        user: '1',
        loginMode: 'researcher',
        historyDone: null,
        PTAdone: null,
        assessmentDone: null,
        AFCDone: null,
        leftEarIsWorse: null,
    };

    updateUser = () => {
        const user = sessionStorage.getItem('user');
        const loginMode = sessionStorage.getItem('loginMode');
        const historyDone = JSON.parse(sessionStorage.getItem('historyDone'));
        const PTAdone = JSON.parse(sessionStorage.getItem('PTAdone'));
        const assessmentDone = JSON.parse(sessionStorage.getItem('assessmentDone'));
        const AFCDone = JSON.parse(sessionStorage.getItem('AFCDone'));
        const leftEarIsWorse = JSON.parse(sessionStorage.getItem('leftEarIsWorse'));
        
        this.setState({
            user,
            loginMode,
            historyDone,
            PTAdone,
            assessmentDone,
            AFCDone,
            leftEarIsWorse,
        });
    }

    render() {
        return (
            <userContext.Provider
                value={{
                    user: this.state.user,
                    loginMode: this.state.loginMode,
                    historyDone: this.state.historyDone,
                    PTAdone: this.state.PTAdone,
                    assessmentDone: this.state.assessmentDone,
                    AFCDone: this.state.AFCDone,
                    leftEarIsWorse: this.state.leftEarIsWorse,
                    updateUser: this.updateUser
                }}
            >
                {this.props.children}
            </userContext.Provider>
        );
    }
}

export const UserConsumer = userContext.Consumer;

export default UserAuthProvider;