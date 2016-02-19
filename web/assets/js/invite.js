'use strict';

/* global gapi */

function gapiLoad() {
    var config = {
        'client_id': '429745829616-l08gihug3r9o9fvj76oh0jdts3sq4g6j.apps.googleusercontent.com',
        'scope': 'https://www.google.com/m8/feeds'
    };

    gapi.auth.authorize(config, function() {
        fetch(gapi.auth.getToken());
    });
}

function fetch(token) {
    $.ajax({
        url: 'https://www.google.com/m8/feeds/contacts/default/full?access_token=' + token.access_token + '&alt=json',
        dataType: 'jsonp',
        success:function(data) {
            $('.people-listing-grid').html('');
            $.each(data.feed.entry, function(k, v){
                $('.people-listing-grid').append(window.JST.inviteGmailTemplate(v));
            });
        }
    });
}