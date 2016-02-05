'use strict';

/* global socket */
/* global scrollToBottom */
/* global addMessage */
/* global scrollbarPlugin */
/* global isMobile */

var openedConversation = {};

socket.on('ourConversation', function(data) {
    console.log(data);
    openedConversation.id = data[0]._conversation;

    if(typeof(Storage) !== 'undefined') {
        localStorage.setItem('openedConversationId', openedConversation.id);
    }

    $('.conversation-message-box').html('');
    $('.conversation-message-box .conversation-single').show();
    $('.conversation').removeClass('is-opened is-opened-compose').addClass('is-opened');

    $.each(data, function(index, value) {
        $('.conversation-message-box').append(window.JST.messageTemplate(value));
    });
    setTimeout(function() {
        scrollToBottom();
        scrollbarPlugin();
    }, 300);
});

socket.on('beginConversation', function(data) {

    if(typeof(Storage) !== 'undefined') {
        localStorage.removeItem('openedConversationId');
    }

    if (data === true) {
        $('.conversation-message-box').html('<div class="alert alert-info" role="alert">No conversation history. Introduce yourself.</div>');
        $('.conversation-message-box .conversation-single').show();
        $('.conversation').removeClass('is-opened is-opened-compose').addClass('is-opened');
    }
});

socket.on('messageSaved', function(value) {
    $('.conversation-message-box').append(window.JST.messageTemplate(value));
    scrollToBottom();

    if ($('.conversations-box').length > 0) {
        socket.emit('getMyConversations', {userID: '_user.id'});
    }

    if (openedConversation.id === '' || typeof openedConversation.id === 'undefined') {
        openedConversation.id = value._conversation;

        if(typeof(Storage) !== 'undefined') {
            localStorage.setItem('openedConversationId', openedConversation.id);
        }

        socket.emit('getConversation', {
            conversationId: openedConversation.id
        });
    }
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
        var mess = $(window.JST.messageTemplate(data)).show();
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
    //addMessage('danger', 'There are some problems with messaging application, please try again later');
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
            $('.conversations-box').html('');
            $.each(data, function (index, val) {
                val._lastMessage.createdAt = new Date(val._lastMessage.createdAt);
                val.index = index;
                $('.conversations-box').append(window.JST.conversationTemplate(val));
            });
        });
    }

    $('.send-message').on('keyup', function(e) {
        if (e.which == 13 && !e.shiftKey) {
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


    $(document).on('click', '.open-conversation', function() {
        if (!socket.connected) {
            addMessage('danger', 'There are some problems with messaging application, please try again later');
            return false;
        }

        var userId = $(this).data('user-id');
        var conversationId = $(this).data('id');

        $('.open-conversation.active').removeClass('active');
        $(this).addClass('active');

        $(this).removeClass('unread');

        if (conversationId) {
            if (openedConversation.id === conversationId) {
                //this conversation is already opened
            } else {
                $('.conversation-message-box .conversation-single').hide();

                socket.emit('getConversation', {
                    conversationId: conversationId,
                    to: userId
                });
            }
        } else {
            socket.emit('getConversation', {
                conversationId: conversationId,
                to: userId
            });
        }

        $conversation.removeClass('is-opened-compose');
        $conversation.addClass('is-opened');
        $overlay.removeClass('hide');

        openedConversation.id = conversationId;
        openedConversation.userId = userId;

        scrollToBottom();
        $('textarea.send-message').focus();
        if (isMobile) {
            window.scrollTo(0, 1);

            if (isMobile) {
                if ($(window).height() <= 568){
                    $('body').css({ 'overflow': 'hidden' });
                }
            }
        }

        //todo: this can also be better done in backend
        setTimeout(function() {
            socket.emit('conversationIsRead', {
                conversationId: conversationId
            });
        }, 500 );
    });

    if (localStorage.openedConversationId || localStorage.openedConversationUserId) {
        if (!socket.connected) {
            addMessage('danger', 'There are some problems with messaging application, please try again later');
            return false;
        }

        var userId = false;
        var conversationId = localStorage.openedConversationId;

        //TODO: bellow all the same as previous function -->
        $('.open-conversation.active').removeClass('active');
        $(this).addClass('active');

        $(this).removeClass('unread');

        if (conversationId) {
            if (openedConversation.id === conversationId) {
                //this conversation is already opened
            } else {
                $('.conversation-message-box .conversation-single').hide();

                socket.emit('getConversation', {
                    conversationId: conversationId,
                    to: userId
                });
            }
        } else {
            if ($conversation.hasClass('is-opened')) {

            } else {
                socket.emit('getConversation', {
                    conversationId: conversationId,
                    to: userId
                });
            }
        }

        $conversation.removeClass('is-opened-compose');
        $conversation.addClass('is-opened');
        $overlay.removeClass('hide');

        openedConversation.id = conversationId;
        openedConversation.userId = userId;

        scrollToBottom();
        $('textarea.send-message').focus();
        if (isMobile) {
            window.scrollTo(0, 1);

            if (isMobile) {
                if ($(window).height() <= 568){
                    $('body').css({ 'overflow': 'hidden' });
                }
            }
        }

        //todo: this can also be better done in backend
        setTimeout(function() {
            socket.emit('conversationIsRead', {
                conversationId: conversationId
            });
        }, 500 );
    }

    $('textarea.send-message').on('focus', function(){
        if (isMobile) {
            if ($(window).height() <= 568){
                //$('.conversation.is-opened').css({ 'bottom': '300px' });
                //$('.conversation-container').css({ 'height': '130px' });
                //$('body').css({ 'overflow': 'hidden' });
            }
        }
    });

    $('textarea.send-message').on('blur', function(){
        if (isMobile) {
            if ($(window).height() <= 568){
                //$('.conversation.is-opened').css({ 'bottom': '0' });
                //$('.conversation-container').css({ 'height': $(window).height() - 100 });
                //$('body').css({ 'overflow': 'auto' });
            }
        }
    });

    //compose
    $compose.on('click', function() {
        $conversation.addClass('is-opened is-opened-compose');
        $('.conversation-message-box .conversation-single').hide();
        $overlay.removeClass('hide');
        openedConversation.id = '';
        openedConversation.userId = '';
    });

    //close
    $('.conversation-close').on('click', '.close-link', function(e) {
        e.preventDefault();

        $('.open-conversation.active').removeClass('active');
        $conversation.removeClass('is-opened');
        $overlay.addClass('hide');
        openedConversation.id = '';
        openedConversation.userId = '';

        if (isMobile) {
            if ($(window).height() <= 568){
                $('body').css({ 'overflow': 'auto' });
            }
        }

        if(typeof(Storage) !== 'undefined') {
            // Code for localStorage/sessionStorage.
            localStorage.removeItem('openedConversationId');
        }

        $('.autocomplete').val('');
    });

});
