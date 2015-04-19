var mongoose = require('mongoose');

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
            }],
            isRead: Boolean
        }
    ),
    'Inbox'
);

module.exports = Message;