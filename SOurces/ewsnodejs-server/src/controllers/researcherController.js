var express = require('express');
var router = express.Router();
const utils = require('../utils/utils');
const util = require('util');
const exec = util.promisify(require('child_process').exec);
const fs = require('fs');
var net = require('net');

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
    const { researcherID, password } = req.body;
    try {
        const savedResearcher = await researcherServices.signUp(researcherID, password);
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
        console.log("fileNames="+fileNames);
        // Here we want to return either .wav files or the folder which contains audio files
        // and their transcription. For now we just test suffix for .wav and hard-coded prefix
        // for folders.
        // TODO: the standard way might be checking the type of files
        let audioFileList = [];
        fileNames.forEach(filename => {
                if (filename.endsWith('.wav')) {
                    audioFileList.push({value: filename, transcript: null});
                } else if (filename.startsWith('stims')) {
                    let stimIndex = filename.substring(5);
                    audioFileList.push(
                        {
                            value: filename + '/stim' + stimIndex + '.wav',
                            // value: filename + '/substim0.wav',
                            transcript: filename + '/stim' + stimIndex + '.json'
                        }
                    );
                }
            }
        );
        console.log("audiofilelist:");
        console.log(audioFileList);
        res.status(200).send(audioFileList);
    }
    catch (err) {
        res.status(500).send(err);
    }
})

router.post("/jsonFile", async (req, res) => {
    const filePath = req.body.data;
    try {
        const jsonData = await utils.readJSONFile(filePath);
        res.status(200).send(jsonData);
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
    try {
        const data = utils.getUserAssessmentConfig();
        res.status(200).send(data);
    }
    catch (err) {
        console.log(err)
        res.status(404).send(err);
    }
})

router.get("/4AFCConfig", async (req, res, next) => {
    try {
        const data = utils.get4AFCConfig();
        res.status(200).send(data);
    }
    catch (err) {
        console.log(err)
        res.status(404).send(err);
    }
})
process.on('uncaughtException', error => console.error('Uncaught exception: ', error));
process.on('unhandledRejection', error => console.error('Unhandled rejection: ', error));

// for backwards compatibility.  Deprecated.
router.post("/restartRTMHA", async (req, res, next) => {
    const isTenBand = req.body.data;

    var num_bands = 6;
    if (isTenBand) num_bands = 10;

    var client = new net.Socket();
    client.connect({
        port: 8001,
        host: '127.0.0.1'
    });

    client.on('error', function (err) {
        res.status(500).send("Setting Params Failed. Please make sure MHA is running");
        client.end();
        return;
    });

    client.on('connect', function () {
        client.write(JSON.stringify({ "method": "set", "data": { "num_bands": num_bands } }));
        client.end();
        res.status(200).send("Bands Switched");
    });
})

router.get('/video', async (req, res, next) => {
    const videoPath = utils.videoFilePath + req.query.name;
    try {
        utils.streamFile(videoPath, req, 'video/mp4', res)
    }
    catch (err) {
        console.log(err);
        utils.handleError(err, res);
    }
    
})


router.get('/audio', async (req, res, next) => {
    const audioPath = utils.audioFilePath + req.query.name;
    try {
        utils.streamFile(audioPath, req, 'audio/wav', res)
    }
    catch (err) {
        console.log(err);
        utils.handleError(err, res);
    }
    
})

module.exports = router;