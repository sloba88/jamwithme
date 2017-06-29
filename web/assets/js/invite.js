'use strict';

/* global gapi */
/* global scrollbarPlugin */
/* global $ */
/* global Routing */
/* global addMessage */

$('body').on('click', '#importContactsFromGmail', function(e) {
    e.preventDefault();
    var config = {
        'client_id': '429745829616-l08gihug3r9o9fvj76oh0jdts3sq4g6j.apps.googleusercontent.com',
        'scope': 'https://www.google.com/m8/feeds',
        'immediate': false
    };

    gapi.auth.authorize(config, function() {
        fetch(gapi.auth.getToken());
    });
});

$('body').on('click', '#inviteByEmailBtn', function(e) {
    e.preventDefault();
    var email = $('#inviteByEmailEmail').val();
    $.ajax({
        url: Routing.generate('send_invite_email'),
        type: 'POST',
        data: { 'email': email },
        success: function(result) {
            if (result.status === 'success') {
                addMessage(result.status, result.message);
                $('#inviteByEmailEmail').val('');
            }
        }
    });
});

function fetch(token) {
    $.ajax({
        url: 'https://www.google.com/m8/feeds/contacts/default/full?access_token=' + token.access_token + '&alt=json&max-results=1000',
        dataType: 'jsonp',
        success:function(data) {
            $('.people-listing-grid').removeClass('loading-content').html('');
            $.each(data.feed.entry, function(k, v){
                $('.people-listing-grid').append(window.JST.inviteGmailTemplate(v));
            });

            $('.invite-commands').removeClass('hidden');
            $('.importContactsFromGmailHolder').addClass('hidden');

            scrollbarPlugin();
        }
    });
}

(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s); js.id = id;
    js.src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=579439738844251';
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));