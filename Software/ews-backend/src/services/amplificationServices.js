const db = require('../../models/index');
const Amplification = db.amplification;

export default class AmplificationServices {
    async save(profileName, bandNumber, parameters) {
        try {
            const savedParameters = await Amplification.create({
                profileName,
                parameters,
                bandNumber
            });
            
            return savedParameters;
        }
        catch (err) {
            console.log("Error writing to db");
            throw err;
        }
    }

    async getAllProfiles() {
        try {
            return await Amplification.findAll();
        }
        catch (err) {
            console.log("Error retrieving amplification profiles");
            throw err;
        }
    }

}