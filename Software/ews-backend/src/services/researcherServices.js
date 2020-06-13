// var Researcher = require('../models/researcher');
const db = require('../../models/index');

const Researcher = db.researcher;

export default class ResearcherService {
    async signUp (researcherID, institution, password) {
        try {
            const savedResearcher = await Researcher.create({researcherID, institution, password});
            console.log(savedResearcher);
            return savedResearcher;
        }
        catch (err) {
            console.log("Error writing to db");
            throw err;
        }
    }

    async findUserByID(researcherID) {
        try {
            const researcher = await Researcher.findOne({where: {researcherID: researcherID}});
            return researcher;
        }
        catch (err) {
            console.log(err);
            throw err;
        }
    }

    async validatePassword(researcher, password) {
        try {
            const same = await researcher.isCorrectPassword(password);
            console.log(same);
            return same;
        }
        catch (err) {
            throw err;
        }
    }

    async getAllResearchers() {
        try {
            const researchers = await Researcher.findAll();
            var researcherIDs = [];
            researchers.forEach((researcher) => {
                researcherIDs.push(researcher.researcherID);
            });
            return researcherIDs;
        }
        catch (err) {
            console.log("Error retrieving researchers");
            throw err;
        }
    }
}