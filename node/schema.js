var mongoose = require('mongoose');

var Message = mongoose.model('Inbox',
    new mongoose.Schema(
        {
            from: {
                id: Number,
                username: String
            },
            to : {
                id: Number,
                username: String
            },
            owner : {
                id: Number,
                username: String
            },
            message : String,
            createdAt : { type: Date, default: Date.now },
            isRead: { type: Boolean, default: false }
        }
    ),
    'Inbox'
);

module.exports = Message;