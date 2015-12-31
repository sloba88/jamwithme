var mongoose = require('mongoose');

var collections = [];

collections.Message = mongoose.model('Messages',
    new mongoose.Schema(
        {
            from: Number,
            owner : Number,
            _conversation: { type: mongoose.Schema.Types.ObjectId, ref: 'Conversations'},
            message : String,
            createdAt : { type: Date, default: Date.now }
        }
    ),
    'Messages'
);

collections.Conversation = mongoose.model('Conversations',
    new mongoose.Schema(
        {
            owner : Number,
            participants: [],
            mirroredConversations: [],
            _lastMessage: { type: mongoose.Schema.Types.ObjectId, ref: 'Messages'},
            createdAt : { type: Date, default: Date.now },
            isRead: { type: Boolean, default: false }
        }
    ),
    'Conversations'
);
module.exports = collections;