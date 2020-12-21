module.exports = function (sequelize, DataTypes) {
    return sequelize.define("amplification", {
        profileName: {
            type: DataTypes.STRING,
            allowNull: false,
            unique: true,
        },
        bandNumber: {
            type: DataTypes.INTEGER,
            allowNull: false,
        },
        parameters: {
            type: DataTypes.STRING,
            allowNull: false,
        },
    });
};