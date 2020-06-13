import React from 'react';
import MainPage from 'pages/general/mainPage';
import { createMuiTheme, MuiThemeProvider } from '@material-ui/core/styles';
import blue from '@material-ui/core/colors/blue';
import pink from '@material-ui/core/colors/pink';
import { CssBaseline } from '@material-ui/core';
import { BrowserRouter, Switch, Route } from 'react-router-dom'; 
import SignUp from 'pages/general/signup';
import Login from 'pages/general/login';
import UserAuthProvider from 'context/userContext';

import './styles/App.css';

const theme = createMuiTheme({
  palette:{
    primary: {
      main:blue['A400'],
    },
    secondary: pink,
  },
  typography:{
    fontFamily: '"Lato", sans-serif',
  },
});

function App() {
  return (
    <UserAuthProvider>
      <BrowserRouter>
        <MuiThemeProvider theme={theme} >
          <CssBaseline/>
          <Switch>
            <Route exact path="/login">
              <Login />
            </Route>
            <Route path="/signup">
              <SignUp />
            </Route>
            <Route path="/">
              <MainPage />
            </Route>
          </Switch>
        </MuiThemeProvider>
      </BrowserRouter>
    </UserAuthProvider>
  );
}

export default App;
