# Embedded Web Server - Backend
## Application Overview
This project is a Node.js server using [Express](https://expressjs.com/). It defines the API implmentations and database connection.

## Application Installation
1. You need to install [Node.js](https://nodejs.org/en/) and [NPM](https://www.npmjs.com/) on your system to run this app. Please refer to their official websites for detailed installation steps on different operating systems
2. Clone or download this project
3. ```cd``` into the project folder and run ```npm install```
4. (optional) Run ```sudo npm install -g sequelize-cli``` to install the command-line interface to sqlite connection.
5. Run ```mkdir database```
6. (optional) Run ```sequelize db:migrate``` to migrate the database table definitions
7. Run ```npm run dev``` to start the server
8. (Optional) Run ```npm install -g nodemon``` to install [nodemon](https://nodemon.io/) for hot-reloading during developement. You can now run ```npm run watch``` to enable nodemon.
9. Open your browser (Chrome preferred) and navigate to [http://localhost:5000/](http://localhost:5000/) for using the production build of the React frontend asset under this repository. If you are working on the frontend at the same time, run EWSNodeJS-client and EWSNodeJS-server at the same time and navigate to [http://localhost:3000/](http://localhost:3000/)

## Application Development
* ```app.js``` is the main file that defines the server.
* ```models``` folder contains the definations of database access objects.
* ```src/controllers``` folder contains the handlers of the APIs.
* ```src/services``` folder contains the business logic for APIs.  
Please refer to [this article](https://dev.to/santypk4/bulletproof-node-js-project-architecture-4epf) if you are not familar with the 3-layer architecture of server developemnt

## Major Dependencies
#### Please refer to the following documentations for the major packages that are used in this project:
* [Express](https://expressjs.com/) for server bootstrapping
* [Sequelize](https://sequelize.org/) for database ORM
* [bcrypt](https://github.com/kelektiv/node.bcrypt.js/) for password encryption