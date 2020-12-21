'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    /**
     * Add altering commands here.
     *
     * Example:
     * await queryInterface.createTable('users', { id: Sequelize.INTEGER });
     */
      // logic for transforming into the new state
      return queryInterface.removeColumn('researchers',
        'institution'
      );
  },

  down: async (queryInterface, Sequelize) => {
    /**
     * Add reverting commands here.
     *
     * Example:
     * await queryInterface.dropTable('users');
     */
    return function(queryInterface, Sequelize) {
      // logic for reverting the changes
      return queryInterface.addColumn('researchers',{
        institution: {
          type: Sequelize.STRING,
          allowNull: false,
        },
      });
    }
  }
};
