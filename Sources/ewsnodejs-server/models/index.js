'use strict';

const fs = require('fs');
const path = require('path');
const Sequelize = require('sequelize');
const basename = path.basename(__filename);
const env = process.env.NODE_ENV || 'development';
const config = require(__dirname + '/../config/config.json')[env];
const db = {};

const sequelize = new Sequelize({
  dialect: config.dialect,
  storage: __dirname + '/../database/database.sqlite',
});

// define the models for the KV tables
sequelize.getQueryInterface().showAllSchemas().then((schemaList) => {
  schemaList.forEach(function (item, index) {
    if (item.name.startsWith("kv_")) {
      console.log("Adding " + item.name);
      db[item.name] = db.sequelize.define(item.name, {
        key: {
          type: Sequelize.STRING,
          allowNull: false,
          unique: true
        },
        value: {
          type: Sequelize.BLOB,
        }
      },
        {
          freezeTableName: true
        });
    };
  });
}).catch((err) => {
  console.log('showAllSchemas ERROR', err);
});

// read the models directory for the non-KV models
fs
  .readdirSync(__dirname)
  .filter(file => {
    return (file.indexOf('.') !== 0) && (file !== basename) && (file.slice(-3) === '.js');
  })
  .forEach(file => {
    const model = sequelize['import'](path.join(__dirname, file));
    db[model.name] = model;
  });

Object.keys(db).forEach(modelName => {
  console.log("Adding Table " + modelName);
  // console.log(db);
  if (db[modelName].associate) {
    db[modelName].associate(db);
  }
});

db.sequelize = sequelize;
db.Sequelize = Sequelize;

module.exports = db;
