module.exports = function (sequelize, DataTypes) {
    var kv_table = sequelize.define('$TABLE_NAME', {
        key: {
            type: DataTypes.STRING,
            allowNull: false,
            unique: true,
        },
        value: {
            type: DataTypes.BLOB,
        },
    });

    return kv_table;
};