exports.socketServer = function (app, server) {
  const io = require('socket.io')(server);

  io.sockets.on('connection', function (socket) {
    console.log("conn")
  });
};