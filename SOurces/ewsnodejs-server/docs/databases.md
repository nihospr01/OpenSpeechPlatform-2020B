# Databases

## Location

All the tables are in `database/database.sqlite`.

The webserver accesses the database through the [sequelize ORM](https://sequelize.org/).

In normal operation, web apps access the database only through the
[Open Speech Platform Server API](api.md).  This document describes
the structure of the database which may be useful for EWS developers.


## Creation

The database and tables are defined in 
`models/*.js`

`app.js` loads the database and creates any missing tables.

``` javascript
const db = require('./models/index');

const researcherController = require('./src/controllers/researcherController');
const listenerController = require('./src/controllers/listenerController');
const paramController = require('./src/controllers/paramController');

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
```

## Key-Value Tables (NEW)

The key-value tables are internally named starting with "kv_".  The key is a string and the
value is a binary blob.

KV tables are fast and efficient.  They have a simple API and schema which applies to all tables.  Web Apps can create and delete tables as needed without requiring web server, database, and API changes.

Tables can be created to group k-v pairs, which will improve performance in cases where we need to search through keys.  However it is possible to put all the k-v pairs in a single table.

To convert a SQL database to a K-V database, you can simply format the key so the SQL 
column is in the name.  For example,

Key | Value 
--- | --- 
martin-password | XXXXXX
martin-log| {text}
martin-history-done | 0
hari-password | YYYYYY
... | ...

So to get the log for a user, you simply get the value for key {user}-log.

If this is the only table and you need a list of users, it is currently necessary
to get all the keys and extract the user list from those.  Or add a special
"userlist" key that contains a json list of users.

It might be a good idea
in the future to allow finding keys with wildcards, so we can, for example, get all "*-password" keys which would be a simpler way to get the user list.

If you combine multiple SQL tables into one KV table, you can simply prefix the keys
to make it clearer what the data is.  For example, "listener-{name}" and "researcher-{name}".

See the [REST API](api.md) for more details.

## SQL Tables

THESE SHOULD ALL BE REPLACED BY K-V TABLES!


I'll show the commands used to examine the database so others can update this doc.

```
/opt2/OSP/ewsnodejs-server (develop)> sqlite3 database/database.sqlite 
SQLite version 3.33.0 2020-08-14 13:23:32
Enter ".help" for usage hints.
sqlite> .tables
amplifications  listeners       researchers   
sqlite> 
```

### AMPLIFICATIONS TABLE

``` sql
sqlite> .schema amplifications
CREATE TABLE `amplifications` (`id` INTEGER PRIMARY KEY AUTOINCREMENT, `profileName` VARCHAR(255) NOT NULL UNIQUE, `bandNumber` INTEGER NOT NULL, `parameters` VARCHAR(255) NOT NULL, `createdAt` DATETIME NOT NULL, `updatedAt` DATETIME NOT NULL);

sqlite> .header on
sqlite> .mode markdown
sqlite> select * from amplifications;
```

| id |   profileName   | bandNumber |                                                                                                                                                                                                                                                                                                                                                                                                           parameters                                                                                                                                                                                                                                                                                                                                                                                                           |           createdAt            |           updatedAt            |
|----|-----------------|------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------|--------------------------------|
| 1  | Martin_profile1 | 6          | {"left":{"afc":1,"freping":1,"cr":[1,1,1,1,1,1],"g50":[-5,-5,-5,-5,-5,-5],"g65":[-5,-5,-5,-5,-5,-5],"g80":[-5,-5,-5,-5,-5,-5],"knee_low":[52,52,52,52,52,52],"mpo_band":[87.7645034790039,93.39839935302734,95.56849670410156,101.63400268554688,108.70700073242188,120.01000213623047],"attack":[5,5,5,5,5,5],"release":[20,20,20,20,20,20], | 2020-09-10 19:44:22.498 +00:00 | 2020-09-10 19:44:22.498 +00:00 |

### LISTENERS TABLE

``` sql
sqlite> .schema listeners
CREATE TABLE `listeners` (`id` INTEGER PRIMARY KEY AUTOINCREMENT, `listenerID` VARCHAR(255) NOT NULL UNIQUE, `researcher` VARCHAR(255) NOT NULL, `password` VARCHAR(255) NOT NULL, `userLog` TEXT, `historyDone` TINYINT(1) DEFAULT 0, `PTADone` TINYINT(1) DEFAULT 0, `assessmentDone` TINYINT(1) DEFAULT 0, `AFCDone` TINYINT(1) DEFAULT 0, `leftEarIsWorse` TINYINT(1) DEFAULT 1, `preferredParameters` VARCHAR(255), `createdAt` DATETIME NOT NULL, `updatedAt` DATETIME NOT NULL);

sqlite> select * from listeners;
```
| id | listenerID | researcher | password |                                                                                                                    userLog                                                                                                                    | historyDone | PTADone | assessmentDone | AFCDone | leftEarIsWorse | preferredParameters |           createdAt            |           updatedAt            |
|----|------------|------------|----------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-------------|---------|----------------|---------|----------------|---------------------|--------------------------------|--------------------------------|
| 1  | martin     | martin     | foo      | {"history":{"logOn":"9/10/2020, 3:43:54 PM","medicalHistory":{"leftEar":"Normal Hearing","rightEar":"Normal Hearing"},"illnesses":{},"ageSpeak":"0 - 2 years old.","highestEdu":"Post-Graduate/Master’s level education.","illnessesFam":{}}} | 1           | 0       | 0              | 0       | 1              |                     | 2020-09-10 19:33:39.957 +00:00 | 2020-09-10 19:43:54.603 +00:00 |

### RESEARCHERS TABLE

``` sql
sqlite> .schema researchers
CREATE TABLE `researchers` (`id` INTEGER PRIMARY KEY AUTOINCREMENT, `researcherID` VARCHAR(255) NOT NULL UNIQUE, `password` VARCHAR(255) NOT NULL, `createdAt` DATETIME NOT NULL, `updatedAt` DATETIME NOT NULL);
sqlite> select * from researchers;
```

| id | researcherID |                           password                           |           createdAt            |           updatedAt            |
|----|--------------|--------------------------------------------------------------|--------------------------------|--------------------------------|
| 1  | martin       | $2a$10$YJhHHzaBwC8EL9tbvU7tBefjb.6Stv0mWMHq15YalUkrjJdEk.MAu | 2020-09-10 19:33:33.179 +00:00 | 2020-09-10 19:33:33.179 +00:00 |
