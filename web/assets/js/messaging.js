socket.on('ourConversation', function(data) {
    console.log(data);
    $(".conversation-message-box").html('');
    $.each(data, function(index, value) {
        $(".conversation-message-box").append(messageTemplate(value));
    });
    setTimeout(function() {
        scrollToBottom()
    }, 300);
});

socket.on('messageSaved', function(data) {
    var mess = $(messageTemplate(data)).show();
    $(".conversation-message-box").append(mess);
    scrollToBottom();
});

socket.on('messageReceived', function(data) {
    var mess = $(messageTemplate(data)).show();
    $(".conversation-message-box").append(mess);
    scrollToBottom();
});

socket.on('myUnreadMessagesCount', function(data) {
    if (data != 0) {
        $(".inbox .badge").text(data);
    } else {
        $(".inbox .badge").text('');
    }
});

function conversations() {
    var $conversation = $('.conversation'),
        $conversationContainer = $conversation.find('.conversation-container'),
        $compose = $('.messages-header').find('.btn-compose'),
        $overlay = $('.overlay');

    //open
    $('.messages-container').on('click', '.message-single', function() {
        $conversation.removeClass('is-opened-compose');
        $conversation.addClass('is-opened');
        $overlay.removeClass('hide');
        $(this).removeClass('unread');

        $(".conversation-message-box .conversation-single").hide();
        var user = $(this).data('user');
        var userID = $(this).data('id');
        $('*[data-user="' + user + '"]').show();
        $('*[data-user2="' + user + '"]').show();
        $(".send-message").data('toid', userID);
        $(".send-message").data('tousername', user);

        scrollToBottom();

        setTimeout(function() {
            socket.emit('conversationIsRead', {
                userID: userID
            });
        }, 500);

    });

    $('.open-conversation').on('click', function(e) {
        e.preventDefault();

        socket.emit('getOurConversation', {
            userID: $(this).data('id')
        });

        $conversation.removeClass('is-opened-compose');
        $conversation.addClass('is-opened');
        $overlay.removeClass('hide');

        $(".conversation-message-box .conversation-single").hide();
        var user = $(this).data('user');
        var userID = $(this).data('id');
        $('*[data-user="' + user + '"]').show();
        $('*[data-user2="' + user + '"]').show();
        $('.send-message').data('toid', userID);
        $('.send-message').data('tousername', user);

        scrollToBottom();

        setTimeout(function() {
            socket.emit('conversationIsRead', {
                userID: userID
            });
        }, 500);

    });

    //compose
    $compose.on('click', function(e) {
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

}

function sendMessage(self) {
    var value = self.val();
    var toID = self.data('toid');
    var toUsername = self.data('tousername');

    if ($.trim(value) == '') return false;

    socket.emit('newMessage', {
        message: value,
        to: {
            id: toID,
            username: toUsername
        }
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
});
