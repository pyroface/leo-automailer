
//SERVER.JS ÄR TILL af-test.html
//om du vill se AF datan snyggare presenterad i consolen

let http = require('http');
let fs = require("fs");

let hostname = '127.0.0.1';
let port = 6700;

fs.readFile('af-test.html', (err,html) => {
  if(err){
    throw err;
  }
  const server = http.createServer((req, res) => {
    res.statusCode = 200;
    res.setHeader('Content-type', 'text/html');
    res.write(html);
    res.end();
  });

  server.listen(port, hostname, () => {
    console.log('Server is live at 6700');
  });
});