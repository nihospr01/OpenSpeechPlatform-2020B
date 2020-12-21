module.exports = function (sequelize, DataTypes) {
    var Keyval = sequelize.define("keyval", {
        key: {
            type: DataTypes.STRING,
            allowNull: false,
            unique: true,
        },
        value: {
            type: DataTypes.BLOB,
        },
    });

    return Keyval;
};