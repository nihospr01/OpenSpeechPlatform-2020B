const db = require('../../models/index');
const Listener = db.listener;

export default class ListenerServices {
    async signUp(listenerID, researcher, password) {
        try {
            const savedListener = await Listener.create({listenerID, researcher, password});
            return savedListener;
        }
        catch (err) {
            console.log("Error writing to db");
            throw err;
        }
    }

    async findUserByID(listenerID) {
        try {
            const listener = await Listener.findOne({ where: {listenerID: listenerID}});
            return listener;
        }
        catch (err) {
            console.log(err);
            throw err;
        }
    }

    async validatePassword(listener, password) {
     
        try {
            const same = await listener.isCorrectPassword(password);
           
            return same;
        }
        catch (err) {
            throw err;
        }
    }

    async getAllListeners(researcherID) {
        try {
            const listeners = await Listener.findAll({
                where: {
                    researcher: researcherID
                }
            });
            return listeners;
        }
        catch (err) {
            console.log("Error retrieving listeners");
            throw err;
        }
    }

    async deleteListener(listenerID) {
        try {
            const response = await Listener.destroy({
                where: {
                  listenerID: listenerID
                }
            });
        }
        catch (err) {
            console.log("Error deleting listener");
            throw err;
        }
    }
    
    async editPassword(listenerID, newPassword) {
        try {
            const response = await Listener.update({
                password: newPassword,
            }, {
                where: {
                    listenerID: listenerID
                }
            })
        }
        catch (err) {
            console.log("Error editing password");
            throw err;
        }
    }

    async getLog(listenerID) {
        try {
            const listener = await Listener.findOne({
                where: {
                    listenerID: listenerID
                }
            });
            return JSON.parse(listener.userLog);
        }
        catch (err) {
            console.log("Error getting log");
            throw err;
        }
    }

    async editLog(listenerID, newLog, flag, flagValue) {
        try {
            const oldLog = await this.getLog(listenerID);
            let userLog = {
                ...oldLog,
                ...newLog
            }
            console.log(userLog)
            const response = await Listener.update({
                userLog: JSON.stringify(userLog),
                [flag]: flagValue,
            }, {
                where: {
                    listenerID: listenerID
                }
            })
        }
        catch (err) {
            console.log("Error editing log");
            throw err;
        }
    }
    
    async getParameters(listenerID) {
        try {
            const listener = await Listener.findOne({
                where: {
                    listenerID: listenerID
                }
            });
            return listener.preferredParameters;
        }
        catch (err) {
            console.log("Error getting parameters");
            throw err;
        }
    }

    async updateParameters(listenerID, parameters) {
        try {
            const response = await Listener.update({
                preferredParameters: parameters,
            }, {
                where: {
                    listenerID: listenerID
                }
            })
        }
        catch (err) {
            console.log("Error updating parameters");
            throw err;
        }
    }
}