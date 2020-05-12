const express = require('express')
const app = express()
const server = require('http').Server(app)
const io = require('socket.io')(server)

const rooms = { }
const PORT = process.env.PORT || 3000;

/*
if (YOUR_LIVE_CHECK) { // For example: (PORT !== 3000)
  io.origins(['YOUR_DRUPAL_DOMAIN']); // For example: https://www.domain.com:443
}
*/

server.listen(PORT, ()=>{
  console.log("Connected to port:" + PORT)
})

io.on('connection', socket => {
  socket.on('new-user', (room, name, userPicture) => {
    if(rooms[room] == null){
      rooms[room] = { users: {} }
    }
    socket.join(room)
    rooms[room].users[socket.id] = name
  })
  socket.on('send-stream-message', (room, message, userPicture, created, timestamp) => {
    socket.to(room).broadcast.emit('stream-message', { message: message, name: rooms[room].users[socket.id], userpicture:userPicture, created:created, timestamp:timestamp })
  })
  socket.on('disconnect', () => {
    getUserRooms(socket).forEach(room => {
      delete rooms[room].users[socket.id]
    })
  })
})

function getUserRooms(socket) {
  return Object.entries(rooms).reduce((names, [name, room]) => {
    if (room.users[socket.id] != null) names.push(name)
    return names
  }, [])
}
