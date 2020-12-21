var express = require('express');
var router = express.Router();

import KeyvalServices from '../services/keyvalServices';

var keyvalServices = new KeyvalServices();


router.get('/:table/:key', async (req, res, next) => {
	try {
		const key = req.params.key;
		res.status(200).send(await keyvalServices.get(key, req.params.table));
	} catch (err) {
		console.log(err);
		if (err == "table not found") {
			res.status(404).send(err);
		} else {
			res.status(500).send(err);
		}
	}
});

router.get('/:table', async (req, res, next) => {
	try {
		res.status(200).send(await keyvalServices.getAll(req.params.table));
	} catch (err) {
		console.log(err);
		if (err == "table not found") {
			res.status(404).send(err);
		} else {
			res.status(500).send(err);
		}
	}
});

router.post('/:table', async (req, res, next) => {
	try {
		const { key, value } = req.body;
		await keyvalServices.set(key, value, req.params.table);
		res.status(200).end();
	} catch (err) {
		console.log(err);
		if (err == "table not found") {
			res.status(404).send(err);
		} else {
			res.status(500).send(err);
		}
	}
});

router.delete('/:table', async (req, res, next) => {
	try {
		const { key } = req.body;
		await keyvalServices.delete(key, req.params.table);
		res.status(200).end();
	}
	catch (err) {
		console.log(err);
		if (err == "table not found" || err == "key not found") {
			res.status(404).send(err);
		} else {
			res.status(500).send(err);
		}
	}
});

router.post('/table/create/:name', async (req, res, next) => {
	try {
		await keyvalServices.createtable(req.params.name);
		res.status(200).end();
	}
	catch (err) {
		console.log(err);
		if (err == "table with that name already exists") {
			res.status(404).send(err);
		} else {
			res.status(500).send(err);
		}
	}
});

router.delete('/table/delete/:name', async (req, res, next) => {
	try {
		await keyvalServices.deletetable(req.params.name);
		res.status(200).end();
	}
	catch (err) {
		console.log(err);
		res.status(500).send(err);
	}
});


module.exports = router;