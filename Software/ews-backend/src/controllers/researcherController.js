var express = require('express');
var router = express.Router();
const utils = require('../utils/utils');

import ResearcherServices from '../services/researcherServices';

var researcherServices = new ResearcherServices();

router.get("/", async(req, res, next) => {
    try {
        const researcherList = await researcherServices.getAllResearchers();
        res.status(200).send(researcherList);
    }
    catch (err) {
        res.status(500).send(err);
    }

})

router.post("/signup", async (req, res, next) => {
    const { researcherID, institution, password } = req.body;
    try {
        const savedResearcher = await researcherServices.signUp(researcherID, institution, password);
        res.status(200).send(savedResearcher);
    }
    catch (err) {
        res.status(500).send(err.message);
    }
})

router.post("/login", async (req, res, next) => {
    const { researcherID, password } = req.body;
    try {
        const researcher = await researcherServices.findUserByID(researcherID);
        if (!researcher) {
            res.status(404).json({
                error: 'User not found'
            });
        }
        else {
            const same =  await researcherServices.validatePassword(researcher, password);

            if (!same) {
                res.status(401).json({
                    error: "Password Incorrect",
                });
            }
            else {
                res.status(200).send(researcher);
            }
        }
    }
    catch (err) {
        res.status(500).json({
            error: 'Server error please try again',
        });
    }
})

router.get("/fileNames", async (req, res, next) => {
    try {
        const fileNames = await utils.getFileNames();
        res.status(200).send(fileNames.filter( filename => {
            return filename.endsWith('.wav') || filename.endsWith('.txt') || filename.endsWith('.json');
        }));
    }
    catch (err) {
        res.status(500).send(err);
    }
})

router.get("/audioPath", async (req, res, next) => {
    try {
        const currentPath = utils.audioPath;
        res.status(200).send(currentPath);
    }
    catch (err) {
        res.status(500).send(err);
    }
})


router.post("/upload", async (req, res, next) => {
    try {
        utils.uploadFiles(req, res);
    }
    catch (err) {
        res.status(500).send(err);
    }
})

router.post("/delete", async (req, res, next) => {
    const { filename } = req.body;
    try {
        utils.deleteFile(filename);
        res.status(200).send("success");
    }
    catch (err) {
        res.status(500).send(err);
    }
})

router.get("/userAssessmentConfig", async (req, res, next) => {
    const { filename } = req.body;
    try {
        const data = utils.getUserAssessmentConfig();
        res.status(200).send(data);
    }
    catch (err) {
        console.log(err)
        res.status(500).send(err);
    }
})

router.get("/4AFCConfig", async (req, res, next) => {
    const { filename } = req.body;
    try {
        const data = utils.get4AFCConfig();
        res.status(200).send(data);
    }
    catch (err) {
        console.log(err)
        res.status(500).send(err);
    }
})

module.exports = router;