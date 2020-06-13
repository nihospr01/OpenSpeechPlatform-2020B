var express = require('express');
var router = express.Router();
var net = require('net');

import AmplificationServices from '../services/amplificationServices';


var amplificationServices = new AmplificationServices();

router.post("/setparam", (req, res, next) => {
    const data = req.body;
    var client = new net.Socket();
    client.connect({
        port: 8001,
        host: '127.0.0.1'
    });

    client.on('error', function(err) {
        res.status(500).send("Setting Params Failed. Please make sure MHA is running");
        client.end();
        return;
    });

    client.on('connect', function() {
        client.write(JSON.stringify(data));
        client.end();
        res.status(200).send("Parameters Successfully Transmited to MHA");
    });
})

router.post("/getparam", (req, res, next) => {
    const testData = {
        "user_id": 10,
        "method": "get",
        "request_action": 1,
        "data": {},
    };

    var client = new net.Socket();
    client.connect({
        port: 8001,
        host: '127.0.0.1'
    });

    client.on('connect', function() {
        client.write(JSON.stringify(testData));
    });

    client.on('data', function(data) {
        res.status(200).send(data);
        client.end();
    });

    client.on('error', function(err) {
        res.status(500).send("Setting Params Failed. Please make sure MHA is running");
        client.end();
        return;
    });
})

// router.post("/startPlay", async (req, res, next) => {
//     let socket = req.app.socket;
//     socket.connect({
//         port: 8001,
//         host: '127.0.0.1'
//     });
//     // socket.setKeepAlive(true, 60000);
    
//     socket.on('connect', () => {
//         console.log(socket);
//         res.status(200).send('Start');
//     })

//     socket.on('error', (err) => {
//         res.status(500).send('Start fails');
//         socket.end();
//         return;
//     })
//     req.app.socket = socket;
//     console.log( req.app.socket);
   
// })

router.post("/getTime", async (req, res, next) => {
    const data = {
        "method": "get",
        "data": 0,
    };
    let socket = new net.Socket();
    socket.connect({
        port: 6000,
        host: '127.0.0.1'
    });

    socket.on('connect', function() {
        socket.write(JSON.stringify(data));
    });
    // {data : 0 } json format ( number in ms ) 
    socket.on('data', function(data) {

        data= JSON.parse(data)
        socket.end();
        return res.status(200).send(data);
    });

    socket.on('error', function(err) {
        socket.end();
        return res.status(500).send("Getting time fails");
    });
})


router.post("/amplification", async (req, res, next) => {
    const { profileName, bandNumber, parameters } = req.body;
    try {
        const savedParameters = await amplificationServices.save(profileName, bandNumber, parameters);
        res.status(200).send(savedParameters);
    }
    catch (err) {
        console.log(err);
        res.status(500).send(err);
    }
})

router.get("/amplification", async (req, res, next) => {
    try {
        const profiles = await amplificationServices.getAllProfiles();
        res.status(200).send(profiles);
    }
    catch (err) {
        res.status(500).send(err);
    }
})


module.exports = router;