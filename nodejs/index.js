module.exports.localhost = true;

const port = Number.isInteger(process.argv[0]) ? process.argv[0] : 3556;
const fs = require('fs');
const $ = require('./functions');

const { localhost } = module.exports;

const http = localhost
  ? require('http').createServer()
  : require('https').createServer({
      ca: fs.readFileSync('../sslkeys/ca_bundle.crt'),
      key: fs.readFileSync('../sslkeys/private.key'),
      cert: fs.readFileSync('../sslkeys/certificate.crt')
    });

const io = require('socket.io')(http);
const { dbBackup, checkExpiredBooking } = require('./updater');

var intervalTimer = null;

io.on('connection', function(socket) {
  socket.on('notification', function(data) {
    $.log(data);
    // io.emit('notification', data);
    socket.broadcast.emit('notification', data);
  });
  socket.on('updateTable', async function(data) {
    $.log(data);
    dbBackup();
    socket.broadcast.emit('updateTable', data);
  });
  socket.on('uploadBankImage', function(data) {
    $.log(data);
    socket.broadcast.emit('uploadBankImage', data);
  });
  socket.on('reload', function(data) {
    $.log(data);
    socket.broadcast.emit('reload', data);
  });
});

setInterval(() => {
  checkExpiredBooking(io);
}, 5000);

http.listen(port, function() {
  console.log(`HTTP Server connected to port ${port}`);
});

module.exports.io = io;
