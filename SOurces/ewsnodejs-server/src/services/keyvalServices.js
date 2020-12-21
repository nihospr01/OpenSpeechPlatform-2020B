const db = require('../../models/index');
const { spawnSync } = require('child_process');
const path = require('path');
const fs = require('fs');

const Sequelize = require('sequelize');

export default class KeyvalServices {
	async getAll(name) {
		name = "kv_" + name;
		try {
			if (!(name in db)) {
				throw "table not found";
			}
			const response = await db[name].findAll();
			var result = [];
			for (var item, i = 0; item = response[i++];) {
				result.push(item.dataValues.key);
			}
			return result;
		} catch (err) {
			throw err;
		}
	}

	async set(key, value, name) {
		name = "kv_" + name;
		try {
			if (!(name in db)) {
				throw "table not found";
			}

			var response = await db[name].update({
				value
			}, {
				where: {
					key
				}
			});

			if (!response[0]) {
				await db[name].create({
					key,
					value
				});
			}
		} catch (err) {
			throw err;
		}
	}


	async get(key, name) {
		name = "kv_" + name;
		// console.log("GET " + name + ":" + key);
		try {
			if (!(name in db)) {
				throw "table not found";
			}
			// console.log("found table");
			const response = await db[name].findOne({
				where: {
					key
				}
			});
			// console.log("GET response=" + response );
			if (!response) {
				console.log("key not found");
				throw "key not found";
			}
			// console.log(response.dataValues.value);
			return response.dataValues.value;
		} catch (err) {
			throw err;
		}
	}

	async delete(key, name) {
		name = "kv_" + name;
		try {
			if (!(name in db)) {
				throw "table not found";
			}

			const response = await db[name].destroy({
				where: {
					key
				}
			});

			if (!response) {
				throw "key not found";
			}
		} catch (err) {
			throw err;
		}
	}

	async createtable(name) {
		name = "kv_" + name;

		try {
			if (name in db) {
				throw "table with that name already exists"
			}

			db[name] = db.sequelize.define(name, {
				key: {
					type: Sequelize.STRING,
					allowNull: false,
					unique: true
				},
				value: {
					type: Sequelize.BLOB,
				}
			},
				{
					freezeTableName: true
				});
			await db[name].sync({ force: true });
			return "created successfully";
		} catch (err) {
			throw err;
		}
	}

	async deletetable(name) {
		name = "kv_" + name;
		try {
			if (!(name in db)) {
				throw "table with that name does not exist"
			}
			await db[name].drop();
			delete db[name];
			return "deleted successfully"
		}
		catch (err) {
			throw err;
		}
	}
}