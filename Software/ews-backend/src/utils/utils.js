var fs = require('fs');
var path = require('path');
var multer = require('multer')
var storage = multer.diskStorage({
    destination: function (req, file, cb) {
    cb(null, audioPath)
  },
  filename: function (req, file, cb) {
    cb(null, file.originalname )
  }
})

var upload = multer({ storage: storage }).array('file')

const audioPath = process.cwd() + '/src/utils/audio/';

const saltRounds = 10;

const getFileNames = () => new Promise((resolve, reject) => {
    fs.readdir(audioPath, (err, filenames) => {
        if (err) {
            throw err;      
        }
        resolve(filenames);
    });
});

const uploadFiles = (req, res) => {
    upload(req, res, function (err) {
        if (err instanceof multer.MulterError) {
            res.status(500).json(err)
        } else if (err) {
            res.status(500).json(err)
        }
        res.status(200).send(req.file)
    })
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
    }
}

const get4AFCConfig = () => {
    try {
        var data=fs.readFileSync(audioPath + '4AFCConfig.json', 'utf8');
        return data;
    } catch(err) {
        console.log(err);
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
}