'use strict';

var mongoose = require('mongoose'),
    express = require('express'),
    redis = require('redis'),
    redisClient = redis.createClient(),
    collections = require('./schema'),
    Message = collections.Message,
    Conversation = collections.Conversation,
    app = express(),
    server = app.listen(3000),
    io = require('socket.io').listen(server),
    PHPUnserialize = require('php-unserialize'),
    mysql      = require('mysql'),
    //todo : parametrize this
    mysqlConnection = mysql.createConnection({
        host     : 'localhost',
        user     : 'root',
        password : 'root',
        database : 'jamifind'
    }),
    activeUsers = {};

//io.set('origins', '*178.62.189.52:*');

function getUsernames(mysqlConnection, ids, callback) {
    var query = 'SELECT id, username from users WHERE id IN (' + ids.toString() +') ;';
    console.log(query);
    mysqlConnection.query(query, function(err, rows) {
        if (err) {
            throw err;
        }
        return callback(rows);
    });
}

function saveMessageFrom(socket, data, conversation) {

    var message = new Message({
        from: socket.userID,
        owner: socket.userID,
        message: data.message,
        _conversation: conversation._id
    });

    //my inbox
    message.save(function (err, message) {
        if (err) {
            return console.error(err);
        }
        message = message.toJSON();

        message.fromData = {
            id : socket.userID,
            username: socket.username
        };

        console.log(message);

        socket.emit('messageSaved', message);

        conversation._lastMessage = message;
        conversation.save();
    });
}

function saveMessageTo(socket, data, conversation) {
    //TODO: message should be created for each participant

    var to;
    conversation.participants.forEach(function(item) {
        if (item !== socket.userID){
            to = item;
        }
    });

    var messageTo = new Message({
        from: socket.userID,
        owner: to,
        message: data.message,
        _conversation: conversation._id
    });

    //other inbox
    messageTo.save(function (err) {
        if (err) {
            return console.error(err);
        }

        var socketTo = activeUsers[to];
        if (socketTo){
            messageTo = messageTo.toJSON();

            messageTo.fromData = {
                id : socket.userID,
                username: socket.username
            };

            socketTo.emit('messageReceived', messageTo);
        }

        conversation._lastMessage = messageTo;
        conversation.save();
    });
}

mongoose.connect('mongodb://localhost:27017/jamwithme');

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
            data.userId = sess._sf2_attributes.__userId;
            data.username = sess._sf2_attributes.__username;
            callback(data);
        }
    });
}

mysqlConnection.connect(function(err) {
    if (err) {
        console.error('error connecting: ' + err.stack);
        return;
    }

    console.log('connected to MYSQL as id ' + mysqlConnection.threadId);

    var db = mongoose.connection;
    db.on('error', console.error.bind(console, 'connection error:'));
    db.once('open', function callback () {
        // yay!
        console.log('connected to MONGO');

        io.on('connection', function (socket) {
            socket.emit('registerConnectedUser', { hello: 'world' });

            socket.on('registerUserData', function (data) {

                authenticateUser(data.sessionId, function(data){

                    if (data === null) {
                        return false;
                    }

                    socket.userID   = data.userId;
                    socket.username = data.username;
                    activeUsers[socket.userID] = socket;

                    setTimeout(function(){
                        //get unread messages count
                        getUnreadConversations();
                    }, 1000);

                });
            });

            socket.on('getUnreadMessagesCount', function() {
                getUnreadConversations();
            });

            function getUnreadConversations() {
                Conversation.find({ 'isRead' :  false, 'owner' :  socket.userID }, function (err, messages) {
                    if (err) {
                        return console.error(err);
                    }
                    socket.unreadMessages = messages.length;
                    socket.emit('myUnreadMessagesCount', messages.length);
                });
            }

            /*
            data.to = user id
            data.conversationId = conversation id
            data.message = message text
             */
            socket.on('newMessage', function (data) {

                console.log(data);

                if (data.conversationId === '') {
                    //there is no conversation so you have to create one
                    //todo: this is bad
                    //todo: it doesnt mean that there is no conversation, you should check that
                    data.conversationId = -1;
                } else {
                    data.conversationId = new mongoose.Types.ObjectId(data.conversationId);
                }

                //check for my conversation
                Conversation.findOne({ 'owner': socket.userID, $or: [{ '_id' :  data.conversationId }, { 'participants' :  { $in: [socket.userID, data.to ]} } ] }, function(err, conversation1) {
                    if (err) {
                        console.log(err);
                    }
                    if (!conversation1) {
                        conversation1 = new Conversation({
                            owner: socket.userID,
                            participants: [data.to, socket.userID]
                        });

                        conversation1.save(function (err, conversation1) {
                            if (err) {
                                return console.error(err);
                            }

                            saveMessageFrom(socket, data, conversation1);
                        });
                    } else {
                        saveMessageFrom(socket, data, conversation1);
                    }

                    //check for other guy conversation
                    Conversation.findOne({ '_id' :  new mongoose.Types.ObjectId(conversation1.mirroredConversations[0]) }, function(err, conversation2) {
                        if (err) {
                            console.log(err);
                        }
                        if (!conversation2) {
                            conversation2 = new Conversation({
                                owner: data.to,
                                participants: [data.to, socket.userID],
                                mirroredConversations: [conversation1._id]
                            });

                            conversation2.save(function (err, conversation2) {
                                if (err) {
                                    return console.error(err);
                                }

                                saveMessageTo(socket, data, conversation2);

                                //save reference back to conversation 1
                                conversation1.mirroredConversations.push(conversation2._id);
                                conversation1.save(function (err) {
                                    if (err) {
                                        return console.error(err);
                                    }
                                });
                            });
                        } else {

                            conversation2.isRead = false;

                            conversation2.save(function (err) {
                                if (err) {
                                    return console.error(err);
                                }
                            });

                            saveMessageTo(socket, data, conversation2);
                        }
                    });
                });
            });

            socket.on('getMyConversations', function () {

                Conversation.find({ 'owner' :  socket.userID }).sort( { createdAt: 1 }).lean().populate('_lastMessage').exec(function (err, conversations) {
                    if (err) {
                        return console.error(err);
                    }

                    if (conversations.length !== 0){

                        var users = [];
                        //TODO: don't query from loop
                        for( var i=0; i< conversations.length; i++) {
                            //show the last message in the conversation in the frontend
                            users.push(conversations[i]._lastMessage.from);
                        }

                        getUsernames(mysqlConnection, users, function(results){
                            console.log(conversations.length);
                            for( var z=0; z< conversations.length; z++) {
                                conversations[z].fromData = results[z];

                                for( var b=0; b< results.length; b++) {
                                    if (b[0] == conversations[z].from) {
                                        conversations[z].fromData = results[z];
                                    }
                                }
                            }

                            console.log(conversations);
                            socket.emit('myConversations', conversations);
                        });

                    } else {
                        socket.emit('myConversations', conversations);
                    }
                });

            });

            socket.on('getConversation', function (data) {
                console.log(data);
                if (data.conversationId === '') {
                    data.conversationId = -1;
                } else {
                    data.conversationId =  new mongoose.Types.ObjectId(data.conversationId);
                }

                Message.find({ 'owner': socket.userID, $or: [{ '_conversation' :  data.conversationId }, { '_conversation.participants' :  { $in: [data.to, socket.userID ]} } ] }).lean().exec(function (err, messages) {
                    if (err) {
                        return console.error(err);
                    }

                    var users = [];
                    for( var i=0; i<messages.length; i++) {
                        users.push(messages[i].from);
                    }

                    getUsernames(mysqlConnection, users, function(results){
                        for( var z=0; z<messages.length; z++) {

                            for( var b=0; b< results.length; b++) {
                                if (results[b].id == messages[z].from) {
                                    messages[z].fromData = results[b];
                                }
                            }
                        }

                        console.log(messages);
                        socket.emit('ourConversation', messages);
                    });
                });
            });

            socket.on('conversationIsRead', function (data) {

                data.conversationId = new mongoose.Types.ObjectId(data.conversationId);

                Conversation.update({ 'owner' : socket.userID, '_id': data.conversationId, isRead: false }, {
                    isRead: true
                }, function(err, numberAffected) {
                    //handle it
                    console.log(numberAffected);
                    socket.unreadMessages -= numberAffected;
                    socket.emit('myUnreadMessagesCount', socket.unreadMessages);
                });
            });

        });
    });
});