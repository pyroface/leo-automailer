
const express = require('express')
const app = express()
const mailer = require('./mailer.js');

app.use(function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "http://localhost:8888");
  res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
  next();
})

app.get('/', async (req, res) => {
  console.log('Hej från PHP', req.query)
  try{
    //contactResultCompID körs från mailer.js
    const peopleToRecieveEmail = await mailer.contactResultCompID(req.query.id)
  }
  catch(error) {
    console.error(error);
  }
  res.send({
    msg: 'Hej från node'
  })
})

app.listen(3333, () => {
  console.log('Server running on http://localhost:3333')
})