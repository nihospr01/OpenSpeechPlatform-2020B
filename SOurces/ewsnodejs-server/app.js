var createError = require('http-errors');
var express = require('express');
var path = require('path');
var cookieParser = require('cookie-parser');
var logger = require('morgan');
var app = express();
var cors = require('cors');
var net = require('net');

const db = require('./models/index');

const researcherController = require('./src/controllers/researcherController');
const listenerController = require('./src/controllers/listenerController');
const paramController = require('./src/controllers/paramController');
const keyvalController = require('./src/controllers/keyvalController');

db.sequelize
  .authenticate()
  .then(() => {
    console.log('Connection has been established successfully.');
  })
  .catch(err => {
    console.error('Unable to connect to the database:', err);
  });

// create any missing database tables
db.sequelize.sync()  

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(cors());

app.use('/api/researcher', researcherController);
app.use('/api/listener', listenerController);
app.use('/api/param', paramController);
app.use('/api/db', keyvalController);

app.use(express.static(path.join(__dirname, 'build')));

app.use('/*', (req, res) => {
  res.sendFile(path.join(__dirname, 'build', 'index.html'))
});

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  next(createError(404));
});

// app.socket = new net.Socket();

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.render('error');
});

module.exports = app;
