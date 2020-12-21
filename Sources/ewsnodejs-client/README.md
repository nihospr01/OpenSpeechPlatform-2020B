# Embedded Web Server - Frontend
## Application Overview
This project is bootstrapped by [Create-React-App](https://github.com/facebook/create-react-app). It contains the frontend React assets for the Embedded Web Server. This project provides a developement server which only serves the user interfaces at localhost:3000. To enable the backend APIs, you need to run EWSNodeJS-server separately at localhost:5000. 

## Application Installation
1. You need to install [Node.js](https://nodejs.org/en/) and [NPM](https://www.npmjs.com/) on your system to run this app. Please refer to their official websites for detailed installation steps on different operating systems
2. Clone or download this project
3. ```cd``` into the project folder and run ```npm install```
4. Run ```npm run start``` to start the React development server
5. Open your browser (Chrome preferred) and navigate to [http://localhost:3000/](http://localhost:3000/)

## Build this application
Notice that running this application using ```npm run start``` is only for development. To create an optimized production build, run ```npm run build```, which will generate a production build in the ```build``` folder. Copy the ```build``` folder into EWSNodeJS-server.

## Major Dependencies
#### Please refer to the following documentations for the major packages that are used in this project:  
*  [React](https://reactjs.org/)  
*  [material-ui](https://material-ui.com/) for UI styles  
*  [react-router](https://github.com/ReactTraining/react-router) for broswer routing  
*  [axios](https://github.com/axios/axios) for API calls  
