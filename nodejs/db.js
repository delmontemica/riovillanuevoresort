const fs = require('fs-extra');
const moment = require('moment');
const mysql = require('mysql');
const mysqldump = require('mysqldump');

const dir = __dirname + '/../backup/';

class Database {
  constructor(config) {
    this.config = config;
    this.connection = mysql.createConnection(config);
    this.connection.connect(err => {
      if (err) throw err;
      console.log('Database Connected!\n');
    });
  }
  query(sql, args) {
    return new Promise((resolve, reject) => {
      this.connection.query(sql, args, (err, rows) => {
        if (err) return reject(err);
        resolve(rows);
      });
    });
  }
  async backup() {
    var filename = dir + moment().format('YYYY_MM_DD_hh_mm_ss_A') + '.sql';
    await fs.ensureFile(filename);
    var files = await fs.readdir(dir);

    if (files.length >= 20) {
      for (var i = 0; i < (files.length % 19) - 1; i++) {
        await fs.remove(dir + files[i]);
      }
    }
    mysqldump({
      connection: this.config,
      dumpToFile: filename
    });

    console.log('Backup database: ' + filename);
  }
}

module.exports = Database;
