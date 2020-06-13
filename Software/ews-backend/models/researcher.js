const bcrypt = require('bcryptjs');
const utils = require('../src/utils/utils');

module.exports = function (sequelize, DataTypes) {
    var Researcher = sequelize.define("researcher", {
        researcherID: {
            type:DataTypes.STRING,
            allowNull: false,
            unique: true,
        },
        institution: {
            type: DataTypes.STRING,
            allowNull: false,
        },
        password: {
            type: DataTypes.STRING,
            allowNull: false,
        },
    }, {
        hooks: {
            beforeCreate: (researcher) => {
                researcher.password = bcrypt.hashSync(researcher.password, utils.saltRounds);
            }
        },
    });

    Researcher.prototype.isCorrectPassword = function(password) {
        return bcrypt.compare(password, this.password);
    }

    return Researcher;
};