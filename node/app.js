var http = require('http'),
    mongoose = require('mongoose'),
    express = require('express'),
    redis = require('redis'),
    redisClient = redis.createClient('6379', 'redis_1'),
    Message = require('./schema'),
    app = express(),
    server = app.listen(3000),
    io = require('socket.io').listen(server),
    PHPUnserialize = require('php-unserialize'),
    activeUsers = {};

//io.set('origins', '*178.62.189.52:*');

mongoose.connect('mongodb://mongo_1:27017/jamwithme');

//TODO: authenticate user

var db = mongoose.connection;
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback () {
    // yay!
    console.log('connected to MONGO');

    io.on('connection', function (socket) {
        socket.emit('registerConnectedUser', { hello: 'world' });

        socket.on('registerUserData', function (data) {

            authenticateUser(data.sessionId, function(data){

                if (data == null) {
                    return false;
                }

                socket.userID   = data.userId;
                socket.username = data.username;
                activeUsers[data.userID] = socket;

                setTimeout(function(){
                    //get unread messages count
                    Message.find({ 'isRead' :  false, 'owner.id' :  socket.userID }, function (err, messages) {
                        if (err) return console.error(err);
                        socket.unreadMessages = messages.length;
                        socket.emit('myUnreadMessagesCount', messages.length);
                    });
                }, 1000);

            });
        });

        socket.on('newMessage', function (data) {

            console.log(data);

            var m = {
                from: {
                    id: socket.userID,
                    username: socket.username
                },
                to: {
                    id: data.to.id,
                    username:data.to.username
                },
                owner: {
                    id: socket.userID,
                    username: socket.username
                },
                message: data.message
            };

            var message = new Message(m);

            message.owner = {
                id: socket.userID,
                username: socket.username
            };

            message.isRead = true;

            var messageTo = new Message(m);

            messageTo.owner = {
                id: data.to.id,
                username:data.to.username
            };

            //my inbox
            message.save(function (err, m) {
                if (err) return console.error(err);
                socket.emit('messageSaved', message);
            });

            //other inbox
            messageTo.save(function (err, m) {
                if (err) return console.error(err);
                //socket.emit('messageReceived', messageTo);
                var socketTo = activeUsers[data.to.id];
                if (socketTo){
                    socketTo.emit('messageReceived', messageTo);
                }
            });
        });

        socket.on('getMyMessages', function (data) {
            Message.find({ 'owner.id' :  socket.userID }).sort( { createdAt: 1 }).exec(function (err, messages) {
                if (err) return console.error(err);
                socket.emit('myMessages', messages);
            });
        });

        socket.on('getOurConversation', function (data) {
            Message.find({ 'owner.id' :  socket.userID, $or: [{ 'messages.to.id' : data.userID }, { 'messages.from.id' : data.userID }] }, function (err, messages) {
                if (err) return console.error(err);
                socket.emit('ourConversation', messages);
            });
        });

        socket.on('conversationIsRead', function (data) {
            console.log(data);
            console.log('read');
            Message.update({ 'owner.id' : socket.userID, isRead: false, 'from.id' : data.userID }, {
                isRead: true
            }, function(err, numberAffected, rawResponse) {
                //handle it
                console.log(numberAffected);
                socket.unreadMessages -= numberAffected;
                socket.emit('myUnreadMessagesCount', socket.unreadMessages);
            })
        });

    });
});

function authenticateUser(sessionId, callback) {
    redisClient.get('session:'+sessionId, function (err, user) {
        if (err) {
            console.log('error authenticating');
            return false;
        } else {
            console.log('authenticated');
        }
        if (user === null) {
            callback(null);
        } else {
            var sess = PHPUnserialize.unserializeSession(user);
            var data = [];
            data['userId'] = sess._sf2_attributes.__userId;
            data['username'] = sess._sf2_attributes.__username;
            callback(data);
        }
    });
}

