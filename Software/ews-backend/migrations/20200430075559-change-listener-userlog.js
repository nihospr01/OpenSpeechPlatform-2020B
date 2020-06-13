"use strict";

module.exports = {
  up: (queryInterface, Sequelize) => {
    /*
      Add altering commands here.
      Return a promise to correctly handle asynchronicity.

      Example:
      return queryInterface.createTable('users', { id: Sequelize.INTEGER });
    */
    return queryInterface.changeColumn("listeners", "userLog", {
      type: Sequelize.TEXT,
      allowNull: false,
      defaultValue:'{"history":"","AFC":"","PTA":"","assessment":""}'
    });
  },

  down: (queryInterface, Sequelize) => {
    /*
      Add reverting commands here.
      Return a promise to correctly handle asynchronicity.

      Example:
      return queryInterface.dropTable('users');
    */
    return queryInterface.changeColumn("listeners", "userLog", {
      type: Sequelize.STRING,
      allowNull: false,
      defaultValue: "",
    });
  },
};
