module.exports = function (sequelize, DataTypes) {
    var Listener =  sequelize.define("listener", {
        listenerID: {
            type:DataTypes.STRING,
            allowNull: false,
            unique: true,
        },
        researcher: {
            type: DataTypes.STRING,
            allowNull: false,
        },
        password: {
            type: DataTypes.STRING,
            allowNull: false,
        },
        userLog: {
            type: DataTypes.TEXT,
        },
        historyDone: {
            type: DataTypes.BOOLEAN,
            defaultValue: false,
        },
        PTADone: {
            type: DataTypes.BOOLEAN,
            defaultValue: false,
        },
        assessmentDone: {
            type: DataTypes.BOOLEAN,
            defaultValue: false,
        },
        AFCDone: {
            type: DataTypes.BOOLEAN,
            defaultValue: false,
        },
        leftEarIsWorse: {
            type: DataTypes.BOOLEAN,
            defaultValue: true,
        },
        preferredParameters: {
            type: DataTypes.STRING,
        },
    });

    Listener.prototype.isCorrectPassword = function(password) {
        return password === this.password;
    }

    return Listener;
};