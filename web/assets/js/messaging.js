'use strict';

/* global socket */
/* global scrollToBottom */
/* global messageTemplate */

var openedConversation = {};

socket.on('ourConversation', function(data) {
    console.log(data);
    openedConversation.id = data[0]._conversation;

    $('.conversation-message-box').html('');

    $.each(data, function(index, value) {
        $('.conversation-message-box').append(messageTemplate(value));
    });
    setTimeout(function() {
        scrollToBottom();
    }, 300);
});

socket.on('messageSaved', function(value) {
    $('.conversation-message-box').append(messageTemplate(value));
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
    //todo: append to right message container, not anyone!!!!!
    if (data._conversation == openedConversation.id) {
        var mess = $(messageTemplate(data)).show();
        $('.conversation-message-box').append(mess);
        scrollToBottom();
    } else {
        socket.emit('getUnreadMessagesCount');
    }
});

socket.on('myUnreadMessagesCount', function(data) {
    console.log('unread count ' + data);
    if (data !== 0) {
        $('.inbox .badge').text(data);
    } else {
        $('.inbox .badge').text('');
    }
});

    var $conversation = $('.conversation'),
        $conversationContainer = $conversation.find('.conversation-container'),
        $compose = $('.messages-header').find('.btn-compose'),
        $overlay = $('.overlay');

    //open
/*
    $('.messages-container').on('click', '.message-single', function() {
        $conversation.removeClass('is-opened-compose');
        $conversation.addClass('is-opened');
        $overlay.removeClass('hide');
        $(this).removeClass('unread');

        $('.conversation-message-box .conversation-single').hide();
        var user = $(this).data('user');
        var userID = $(this).data('id');
        $('*[data-user="' + user + '"]').show();
        $('*[data-user2="' + user + '"]').show();
        $('.send-message').data('toid', userID);

        scrollToBottom();

        setTimeout(function() {
            socket.emit('conversationIsRead', {
                userID: userID
            });
        }, 500);

    });
    */

    $(document).on('click', '.open-conversation', function(e) {
        e.preventDefault();
        var user = $(this).data('user');
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

});
