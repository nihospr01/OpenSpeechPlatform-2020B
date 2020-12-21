var express = require('express');
var router = express.Router();
import ListenerServices from '../services/listenerServices';

var listenerServices = new ListenerServices();

router.get("/:researcherID", async (req, res, next) => {
    try {
        const researcherID = req.params.researcherID;
        const listenerList = await listenerServices.getAllListeners(researcherID);
        res.status(200).send(listenerList ? listenerList : []);
    }
    catch (err) {
        console.log(err);
        res.status(500).send(err);
    }
})

router.post("/delete", async (req, res, next) => {
    try {
        const { listenerID } = req.body;
        const response = await listenerServices.deleteListener(listenerID);
        res.status(200).send(response);
    }
    catch (err) {
        console.log(err);
        res.status(500).send(err);
    }
})

router.post("/password", async(req, res, next) => {
    try {
        const { listenerID, newPassword } = req.body;
        console.log(newPassword, listenerID);
        const response = await listenerServices.editPassword(listenerID, newPassword);
        res.status(200).send(response);
    }
    catch (err) {
        console.log(err);
        res.status(500).send(err);
    }
})

router.post("/signup", async (req, res, next) => {
    const { listenerID, researcher, password } = req.body;
    try {
        const savedListener = await listenerServices.signUp(listenerID, researcher, password);
        res.status(200).send(savedListener);
    }
    catch (err) {
        console.log(err);
        res.status(500).send(err);
    }
})

router.post("/login", async (req, res, next) => {
    const { listenerID, password } = req.body;

    try {
        const listener = await listenerServices.findUserByID(listenerID);
       
        if (!listener) {
            res.status(404).json({
                error: 'User not found'
            });
        }
        else {
            const same = await listenerServices.validatePassword(listener, password);
            if (!same) {
                res.status(401).json({
                    error: 'Password incorrect',
                });
            }
            else {
                res.status(200).send(listener);
            }
        }
    }
    catch (err) {
        console.log(err);
        res.status(500).json({
            error: 'Server error please try again',
        });
    }
})

router.post("/addLog", async (req, res, next) => {
    try {
        const { listenerID, newLog, flag, flagValue } = req.body;
        const response = await listenerServices.editLog(
            listenerID, 
            newLog,
            flag,
            flagValue,
        );
        res.status(200).send(response);
    }
    catch (err) {
        console.log(err);
        res.status(500).send(err);
    }
})

router.post("/updateParameters", async (req, res, next) => {
    try {
        const { listenerID, parameters } = req.body;
        const response = await listenerServices.updateParameters(
            listenerID, 
            parameters,
        );
        res.status(200).send(response);
    }
    catch (err) {
        console.log(err);
        res.status(500).send(err);
    }
})

router.get("/getParameters/:listenerID", async (req, res, next) => {
    try {
        const listenerID = req.params.listenerID;
        const parameters = await listenerServices.getParameters(listenerID);
        res.status(200).send(parameters);
    }
    catch (err) {
        console.log(err);
        res.status(500).send(err);
    }
})


module.exports = router;