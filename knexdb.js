require('dotenv').config()

// require('db-migrate-shared').log.setLogLevel(5);

const {
    DB_HOST, DB_USER, DB_PASSWORD, DB_NAME
} = process.env

var knex = require('knex')({
    client: 'pg',
    connection: {
      host : DB_HOST,
      user : DB_USER,
      password : DB_PASSWORD,
      database : DB_NAME
    }
});

module.exports = knex

