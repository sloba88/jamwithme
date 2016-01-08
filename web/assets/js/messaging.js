'use strict';

/* global socket */
/* global scrollToBottom */
/* global addMessage */

var openedConversation = {};

socket.on('ourConversation', function(data) {
    console.log(data);
    openedConversation.id = data[0]._conversation;

    $('.conversation-message-box').html('');

    $.each(data, function(index, value) {
        $('.conversation-message-box').append(window.JST['messageTemplate'](value));
    });
    setTimeout(function() {
        scrollToBottom();
    }, 300);
});

socket.on('messageSaved', function(value) {
    $('.conversation-message-box').append(window.JST['messageTemplate'](value));
    scrollToBottom();
});

socket.on('isOnline', function(is){
    if (is) {
        console.log('online');
    } else {
        console.log('not online');
    }
});

socket.on('messageReceived', function(data) {
    if (data._conversation == openedConversation.id) {
        var mess = $(window.JST['messageTemplate'](data)).show();
        $('.conversation-message-box').append(mess);
        scrollToBottom();
    } else {
        socket.emit('getUnreadMessagesCount');
    }
});

socket.on('myUnreadMessagesCount', function(data) {
    if (data !== 0) {
        $('.inbox .badge').text(data);
    } else {
        $('.inbox .badge').text('');
    }
});

socket.on('connect_error', function(){
    socket.io.disconnect();
    addMessage('danger', 'There are some problems with messaging application, please try again later');
});

function sendMessage(self) {
    var value = self.val();

    if ($.trim(value) === '') {
        return false;
    }

    socket.emit('newMessage', {
        message: value,
        conversationId: openedConversation.id,
        to: openedConversation.userId
    });

    self.val('');
}

$(function() {

    //only on messages page
    if ($('.conversations-box').length > 0 ) {
        if (typeof socket.emit != 'undefined'){
            setTimeout(function () {
                socket.emit('getMyConversations', {userID: '_user.id'});
            }, 500);
        }

        socket.on('myConversations', function (data) {
            $.each(data, function (index, val) {
                val._lastMessage.createdAt = new Date(val._lastMessage.createdAt);
                val.index = index;
                $('.conversations-box').append(window.JST['conversationTemplate'](val));
            });

            scrollToBottom();
        });
    }

    $('.send-message').on('keyup', function(e) {
        if (e.which == 13) {
            sendMessage($(this));
        }
    });

    $('.send-message-btn').on('click', function(e) {
        e.preventDefault();
        sendMessage($('.send-message'));
    });

    setTimeout(function() {
        socket.emit('checkIsOnline', $('.open-conversation').data('user-id'));
    }, 1000);

    var $conversation = $('.conversation'),
        $compose = $('.messages-header').find('.btn-compose'),
        $overlay = $('.overlay');


    $(document).on('click', '.open-conversation', function(e) {
        e.preventDefault();

        if (!socket.connected) {
            addMessage('danger', 'There are some problems with messaging application, please try again later');
            return false;
        }

        var userId = $(this).data('user-id');
        var conversationId = $(this).data('id');
        socket.emit('getConversation', {
            conversationId: conversationId
        });

        $conversation.removeClass('is-opened-compose');
        $conversation.addClass('is-opened');
        $overlay.removeClass('hide');

        $('.conversation-message-box .conversation-single').hide();

        openedConversation.id = conversationId;
        openedConversation.userId = userId;

        scrollToBottom();

        //todo: this can also be better done in backend
        setTimeout(function() {
            socket.emit('conversationIsRead', {
                conversationId: conversationId
            });
        }, 500 );
    });

    //compose
    $compose.on('click', function() {
        $conversation.addClass('is-opened is-opened-compose');
        $('.conversation-message-box .conversation-single').hide();
        $overlay.removeClass('hide');
    });

    //close
    $('.conversation-close').on('click', '.close-link', function(e) {
        e.preventDefault();

        $conversation.removeClass('is-opened');
        $overlay.addClass('hide');
    });

});
