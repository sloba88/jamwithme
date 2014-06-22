var express = require('express')
    , http = require('http');

var mongoose = require('mongoose');
mongoose.connect('mongodb://localhost:27017/jamwithme');

var app = express();
var server = app.listen(3000);
var io = require('socket.io').listen(server);

var activeUsers = {};


var db = mongoose.connection;
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback () {
    // yay!
    console.log('connected to MONGO');

    var Message = mongoose.model('Inbox',
        new mongoose.Schema(
            {
                user: {
                  id: Number,
                  username: String
                },
                messages: [{
                    from : {
                        id: Number,
                        username: String
                    },
                    to : {
                        id: Number,
                        username: String
                    },
                    message : String,
                    createdAt : Date
                }]
            }
        ),
        'Inbox');

    io.on('connection', function (socket) {
        socket.emit('registerConnectedUser', { hello: 'world' });

        socket.on('registerUserData', function (data) {

            socket.userID   = data.userID;
            socket.username = data.username;
            activeUsers[data.userID] = socket;

            Message.find({ user :  data.userID }, function (err, messages) {
                if (err) return console.error(err);
                console.log(messages)
            })
        });

        socket.on('newMessage', function (data) {

            var m = {
                from: {
                    id: socket.userID,
                    username: socket.username
                },
                to: {
                    id: data.to.id,
                    username:data.to.username
                },
                message: data.message,
                createdAt: new Date().getTime()
            }

            var message = new Message({
                user: {
                    id: socket.userID,
                    username: socket.username
                },
                otherUser: data.to,
                messages: [m]
            });

            var messageTo = new Message({
                user: data.to,
                otherUser: m.from,
                messages: [m]
            });

            Message.findOne({'user.id': socket.userID}, function(e, r){
                if (r == null){
                    message.save(function (err, m) {
                        if (err) return console.error(err);
                        socket.emit('messageSaved', message);
                    });
                }else{
                    r.messages.push(m);
                    r.save(function(err, res){
                        socket.emit('messageSaved', message);
                    });
                }
            });

            Message.findOne({'user.id': data.to.id}, function(e, r){
                if (r == null){
                    messageTo.save(function (err, m) {
                        if (err) return console.error(err);
                        //socket.emit('messageReceived', messageTo);
                        var socketTo = activeUsers[data.to.id];
                        socketTo.emit('messageReceived', messageTo);
                    });
                }else{
                    r.messages.push(m);
                    r.save(function(err, res){
                        //socket.emit('messageReceived', messageTo);
                        var socketTo = activeUsers[data.to.id];
                        socketTo.emit('messageReceived', messageTo);
                    });
                }
            });
        });

        socket.on('getMyMessages', function (data) {
            Message.find({ 'user.id' :  socket.userID }, function (err, messages) {
                if (err) return console.error(err);
                socket.emit('myMessages', messages);
            });
        });


    });

});


