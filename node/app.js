'use strict';

var readYaml = require('read-yaml');
var fs = require('fs');
var symfonyParameters = readYaml.sync('../app/config/parameters.yml');

var ssl = {
    key: fs.readFileSync('/etc/letsencrypt/live/jamifind.com/privkey.pem'),
    cert: fs.readFileSync('/etc/letsencrypt/live/jamifind.com/fullchain.pem'),
    ca: fs.readFileSync('/etc/letsencrypt/live/jamifind.com/chain.pem')
};

var mongoose = require('mongoose'),
    express = require('express'),
    redis = require('redis'),
    redisClient = redis.createClient(),
    collections = require('./schema'),
    Message = collections.Message,
    Conversation = collections.Conversation,
    app = express(),
    https = require('https'),
    server = https.createServer(ssl, app).listen(3000),
    io = require('socket.io').listen(server),
    PHPUnserialize = require('php-unserialize'),
    mysql      = require('mysql'),
    promise    = require('promise'),
    Autolinker = require('autolinker'),
    striptags = require('striptags'),
    mysqlConnection,
    request = require('request'),
    activeUsers = {};

//io.set('origins', '*178.62.189.52:*');
redisClient.select(1);


/*jshint camelcase: false */
mysqlConnection = mysql.createConnection({
    host     : symfonyParameters.parameters.database_host,
    user     : symfonyParameters.parameters.database_user,
    password : symfonyParameters.parameters.database_password,
    database : symfonyParameters.parameters.database_name
});
/*jshint camelcase: true */

function getUsernames(mysqlConnection, ids) {
    return new promise(function (fulfill, reject){
        var query = 'SELECT id, username from users WHERE id IN (' + ids.toString() +') ;';
        mysqlConnection.query(query, function(err, rows) {
            if (err) {
                reject(err);
            }
            fulfill(rows);
        });
    });
}

function tagLinks(message) {
    return Autolinker.link(message, {
        truncate: {
            length: 24, location: 'middle'
        }
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

        conversation._lastMessage = message;
        conversation.save();

        message.message = tagLinks(message.message);
        socket.emit('messageSaved', message);
    });
}

function saveMessageTo(socket, data, conversation, notify) {
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

            messageTo.message = tagLinks(messageTo.message);
            socketTo.emit('messageReceived', messageTo);
        } else {
            if (notify) {
                //send email about the message
                var mess = tagLinks(messageTo.message).substring(0, 60) + ' ...';
                var createdAt = Math.round(messageTo.createdAt.getTime() / 1000);
                notifyUserByEmail(to, mess, createdAt, messageTo.from);
            }
        }

        conversation._lastMessage = messageTo;
        conversation.save();
    });
}

function authenticateUser(sessionId) {
    return new promise(function (fulfill, reject) {

        redisClient.get('session'+sessionId, function (err, user) {
            if (err) {
                console.log('error authenticating');
                reject(err);
            }
            if (user === null) {
                reject(null);
            } else {
                var sess = PHPUnserialize.unserializeSession(user);
                var data = [];
                /*jshint camelcase: false */
                data.userId = sess._sf2_attributes.__userId;
                data.username = sess._sf2_attributes.__username;
                /*jshint camelcase: true */
                console.log('User ' + data.username + ' authenticated.');
                fulfill(data);
            }
        });
    });
}

function getUnreadConversations(socket) {
    Conversation.find({ 'isRead' :  false, 'owner' :  socket.userID }, function (err, messages) {
        if (err) {
            return console.error(err);
        }
        socket.unreadMessages = messages.length;
        socket.emit('myUnreadMessagesCount', messages.length);
    });
}

function notifyUserByEmail(to, message, time, from) {

    request.post(
        symfonyParameters.parameters['router.request_context.scheme'] + '://' + symfonyParameters.parameters['router.request_context.host']+'/api/send-message-email',
        { form : {
            'to': to,
            'type': 'messageNotification',
            'text': message,
            'time': time,
            'from': from
        }},
        function (error, response, body) {
            if (!error && response.statusCode == 200) {
                console.log('success email sent');
                return true;
            } else {
                console.log(error);
                console.log(response.statusCode);
                return false;
            }
        }
    );
}

mongoose.connect('mongodb://localhost:27017/jamifind');

mysqlConnection.connect(function(err) {
    if (err) {
        console.error('error connecting: ' + err.stack);
        return;
    }

    console.log('connected to MYSQL as id ' + mysqlConnection.threadId);

    var db = mongoose.connection;
    db.on('error', console.error.bind(console, 'connection error:'));
    db.once('open', function callback () {

        console.log('connected to MONGO');

        io.on('connection', function (socket) {
            socket.emit('registerConnectedUser', { hello: 'world' });

            socket.on('registerUserData', function (data) {

                authenticateUser(data.sessionId).then(function(data) {

                    if (data === null) {
                        return false;
                    }

                    socket.userID   = data.userId;
                    socket.username = data.username;
                    activeUsers[socket.userID] = socket;

                    setTimeout(function(){
                        //get unread messages count
                        getUnreadConversations(socket);
                    }, 1000);
                    socket.emit('userAuthenticated', true );
                });
            });

            socket.on('getUnreadMessagesCount', function() {
                getUnreadConversations(socket);
            });

            /*
            data.to = user id
            data.conversationId = conversation id
            data.message = message text
             */
            socket.on('newMessage', function (data) {

                if (data.conversationId === '' && (typeof data.to == 'undefined' || data.to === '')) {
                    return false;
                }

                if (data.conversationId === '') {
                    data.conversationId = null;
                }

                if (!data.to) {
                    data.to = '-1';
                }

                //striptags
                data.message = striptags(data.message);

                //check for my conversation
                Conversation.findOne({ 'owner': socket.userID, $or: [{ '_id' :  new mongoose.Types.ObjectId(data.conversationId) }, { 'participants' :  { $all: [socket.userID, data.to ]} } ] }, function(err, conversation1) {
                    if (err) {
                        console.log(err);
                    }

                    if (!conversation1) {
                        conversation1 = new Conversation({
                            owner: socket.userID,
                            participants: [parseInt(data.to), parseInt(socket.userID)],
                            isRead: true
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
                                participants: [parseInt(data.to), parseInt(socket.userID)],
                                mirroredConversations: [conversation1._id]
                            });

                            conversation2.save(function (err, conversation2) {
                                if (err) {
                                    return console.error(err);
                                }

                                saveMessageTo(socket, data, conversation2, true);

                                //save reference back to conversation 1
                                conversation1.mirroredConversations.push(conversation2._id);
                                conversation1.save(function (err) {
                                    if (err) {
                                        return console.error(err);
                                    }
                                });
                            });
                        } else {
                            var notify = false;
                            if (conversation2.isRead) {
                                conversation2.isRead = false;

                                conversation2.save(function (err) {
                                    if (err) {
                                        return console.error(err);
                                    }
                                });
                            } else {
                                //notify guy
                                notify = true;
                            }

                            saveMessageTo(socket, data, conversation2, notify);
                        }
                    });
                });
            });

            socket.on('getMyConversations', function () {

                Conversation.find({ 'owner' :  socket.userID }).sort( { createdAt: -1 }).lean().populate('_lastMessage').exec(function (err, conversations) {
                    if (err) {
                        return console.error(err);
                    }

                    if (conversations.length !== 0){

                        var users = [];
                        for( var i=0; i<conversations.length; i++) {
                            //show the last message in the conversation in the frontend

                            for( var g=0; g<conversations[i].participants.length; g++) {
                                if (conversations[i].participants[g] != socket.userID) {
                                    users.push(conversations[i].participants[g]);
                                }
                            }

                            if (conversations[i]._lastMessage.message.length > 60) {
                                conversations[i]._lastMessage.message = conversations[i]._lastMessage.message.substring(0, 60) + ' ...';
                            }
                        }

                        getUsernames(mysqlConnection, users).then(function(results){

                            for( var z=0; z<conversations.length; z++) {

                                for( var t=0; t<results.length; t++) {
                                    if (users[z] == results[t].id) {
                                        conversations[z].fromData = results[t];
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
                if (data.conversationId === '') {
                    data.conversationId = null;
                }

                if (!data.to) {
                    data.to = '-1';
                }

                Conversation.findOne({ 'owner': socket.userID, $or: [{ '_id' :  new mongoose.Types.ObjectId(data.conversationId) }, { 'participants' :  { $all: [data.to, socket.userID ]} } ] }).lean().exec(function (err, conversation) {
                    if (err) {
                        return console.error(err);
                    }

                    if (conversation) {

                        var myIndex = conversation.participants.indexOf(socket.userID);
                        if (myIndex > -1) {
                            conversation.participants.splice(myIndex, 1);
                        }

                        getUsernames(mysqlConnection, conversation.participants).then(function(results){
                            //add usernames to participants from mysql table
                            conversation.participants = results;

                            Message.find({ 'owner': socket.userID, '_conversation' :  conversation._id }).lean().exec(function (err, messages) {
                                if (err) {
                                    return console.error(err);
                                }

                                var users = [];
                                for( var i=0; i<messages.length; i++) {
                                    users.push(messages[i].from);
                                    messages[i].message = tagLinks(messages[i].message);
                                }

                                getUsernames(mysqlConnection, users).then(function(results){
                                    for( var z=0; z<messages.length; z++) {
                                        for( var b=0; b< results.length; b++) {
                                            if (results[b].id == messages[z].from) {
                                                messages[z].fromData = results[b];
                                            }
                                        }
                                    }

                                    socket.emit('ourConversation', {
                                        'messages': messages,
                                        'conversation': conversation
                                    });
                                });
                            });
                        });


                    } else {
                        console.log('no conversation found');
                        //new conversation?
                        socket.emit('beginConversation', true);
                    }
                });
            });

            socket.on('conversationIsRead', function (data) {

                Conversation.update({ 'owner' : socket.userID, '_id': new mongoose.Types.ObjectId(data.conversationId), isRead: false }, {
                    isRead: true
                }, function(err, numberAffected) {
                    //handle it
                    if (socket.unreadMessages) {
                        socket.unreadMessages -= numberAffected.n;
                        socket.emit('myUnreadMessagesCount', socket.unreadMessages);
                    }
                });
            });

            socket.on('checkIsOnline', function(id) {
                if (activeUsers[id]) {
                    socket.emit('isOnline', true);
                } else {
                    socket.emit('isOnline', false);
                }
            });

            socket.on('disconnect', function() {
                console.log('User ' + socket.username + ' disconnected.');
                //remove him from the active users
                delete activeUsers[socket.userID];
            });

        });
    });
});