'use strict';

module.exports = {
  up: (queryInterface, Sequelize) => {
    /*
      Add altering commands here.
      Return a promise to correctly handle asynchronicity.

      Example:
      return queryInterface.createTable('users', { id: Sequelize.INTEGER });
    */
    return queryInterface.createTable('listeners', {
      id: {
        type: Sequelize.INTEGER,
        primaryKey: true,
        autoIncrement: true
      },
      createdAt: {
        type: Sequelize.DATE
      },
      updatedAt: {
        type: Sequelize.DATE
      },
      listenerID: {
        type:Sequelize.STRING,
        allowNull: false,
        unique: true,
      },
      researcher: {
          type: Sequelize.STRING,
          allowNull: false,
      },
      password: {
          type: Sequelize.STRING,
          allowNull: false,
      },
      userLog: {
          type: Sequelize.TEXT,
          allowNull: false,
          defaultValue: '{"history":"","AFC":"","PTA":"","assessment":""}',
          get: function() {
            return JSON.parse(this.getDataValue("userLog"));
          },
          set: function(value) {
            return this.setDataValue("userLog", JSON.stringify(value));
          }
      },
      historyDone: {
          type: Sequelize.BOOLEAN,
          defaultValue: false,
      },
      PTADone: {
          type: Sequelize.BOOLEAN,
          defaultValue: false,
      },
      assessmentDone: {
          type: Sequelize.BOOLEAN,
          defaultValue: false,
      },
      AFCDone: {
          type: Sequelize.BOOLEAN,
          defaultValue: false,
      },
      leftEarIsWorse: {
          type: Sequelize.BOOLEAN,
          defaultValue: true,
      },
      preferredParameters: {
        type: Sequelize.STRING,
    },
    });
  },

  down: (queryInterface, Sequelize) => {
    /*
      Add reverting commands here.
      Return a promise to correctly handle asynchronicity.

      Example:
      return queryInterface.dropTable('users');
    */
    return queryInterface.dropTable('listeners');
  }
};
