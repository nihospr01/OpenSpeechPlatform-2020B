var fs = require('fs');
var path = require('path');
var multer = require('multer');
const { resolve } = require('path');
var storage = multer.diskStorage({
    destination: function (req, file, cb) {
    cb(null, audioPath)
  },
  filename: function (req, file, cb) {
    cb(null, file.originalname )
  }
})

var upload = multer({ storage: storage }).array('file')

const videoFilePath = process.cwd() + '/src/utils/video/';
const videoExtension = '.mp4';
const audioFilePath = process.cwd() + '/src/utils/audio/';
const audioExtension = '.wav';

const audioPath = process.env.OSP_MEDIA + '/';
const saltRounds = 10;

const getFileNames = () => new Promise((resolve, reject) => {
    if (audioPath==undefined) {
        throw "OSP_MEDIA is not set";
    }
    fs.readdir(audioPath, (err, filenames) => {
        if (err) {
            throw err;
        }
        resolve(filenames);
    });
});

const readJSONFile = (path) => new Promise((resolve, reject) => {
    console.log(path);
    fs.readFile(path, (err, data) => {
        if (err) {
            throw "Cannot read path";
        }
        let jsonData = JSON.parse(data);
        console.log(jsonData);
        resolve(jsonData);
    });
});

const uploadFiles = (req, res) => {
    upload(req, res, function (err) {
        if (err instanceof multer.MulterError) {
            res.status(500).json(err);
        } else if (err) {
            res.status(500).json(err);
        }
        res.status(200).send(req.file);
    });
}

const deleteFile = (filename) => {
    try {
        fs.unlinkSync(audioPath + filename);
    } catch(err) {
        console.error(err)
        throw err;
    }
}

const getUserAssessmentConfig = () => {
    try {
        var data=fs.readFileSync(audioPath + 'UserAssessmentConfig.json', 'utf8');
        return data;
    } catch(err) {
        console.log(err);
        throw err;
    }
}

const get4AFCConfig = () => {
    try {
        var data=fs.readFileSync(audioPath + 'BoothroydCCT.json', 'utf8');
        return data;
    } catch(err) {
        console.log(err);
        throw err;
    }
}

const streamFile = (path, req, contentType, res) => {
    const fileSize = fs.statSync(path).size;
    const range = req.headers.range;
    if (range != null) {
        const boundary = range.replace(/bytes=/, "").split("-");
        const start = parseInt(boundary[0], 10);
        let end = boundary[1] ? parseInt(boundary[1], 10) : start + 999999;
        end = end > fileSize - 1 ? fileSize - 1 : end;
        const chunkSize = (end - start) + 1;
        const file = fs.createReadStream(path, {start, end});
        const head = {
            'Content-Range' : `bytes ${start}-${end}/${fileSize}`,
            'Accept-Ranges' : 'bytes',
            'Content-Length' : chunkSize,
            'Content-Type' : contentType,
        };
        res.writeHead(206, head);
        file.pipe(res);
    }
    else
    {
        let head = { 'Content-Type': contentType };
        res.writeHead(200, head);
        fs.createReadStream(path).pipe(res);
    } 
}

const handleError = (err, res) => {
    if (err.errno == -2) {
        res.status(404).send(err);
    }
    else {
        res.status(500).send(err);
    }
}

module.exports = {
    saltRounds: saltRounds,
    audioPath: audioPath,
    getFileNames: getFileNames,
    uploadFiles: uploadFiles,
    deleteFile: deleteFile,
    getUserAssessmentConfig: getUserAssessmentConfig,
    get4AFCConfig: get4AFCConfig,
    readJSONFile: readJSONFile,
    streamFile,
    handleError,
    videoFilePath,
    videoExtension,
    audioFilePath,
    audioExtension,
}