'use strict';

/* global actionConfirmModalTemplate */
/* global Routing */

function showError(error) {
    switch(error.code)
    {
        //TODO: log these server side also
        case error.PERMISSION_DENIED:
            console.log('User denied the request for Geolocation.');
            break;
        case error.POSITION_UNAVAILABLE:
            console.log('Location information is unavailable.');
            break;
        case error.TIMEOUT:
            console.log('The request to get user location timed out.');
            break;
        case error.UNKNOWN_ERROR:
            console.log('An unknown error occurred.');
            break;
    }
}

function getLocation(callback){
    if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(position) {
            return callback([position.coords.latitude, position.coords.longitude]);
        }, showError);
        return callback(false);
    }else{
        alert('Geolocation is not supported by this browser.');
        return callback(false);
    }
}

window.onerror = function(message, url, lineNumber) {
    //save error and send to server
    $.ajax({
        url: Routing.generate('api_js_error_report'),
        type: 'POST',
        data: { 'message': message, 'url': url, 'lineNumber': lineNumber }
    }).done(function( result ) {
        console.log(result);
    });
};

function addMessage(type, message, temp) {
    if (typeof temp == 'undefined') {
        temp = 'temp';
    } else {
        temp = '';
    }

    if (type === false) {
        type = 'danger';
    }

    $('.fixed-alerts-container').append(_templates.notificationTemplate({
        type: type,
        message: message,
        temp: temp
    }));
    setTimeout(function() {
        $('.fixed-alerts-container').children('.temp:last').alert('close');
    }, 4000);

    return true;
}

$(function() {
    $(document).on('click', '.action-confirm', function(e){
        e.stopImmediatePropagation();
        e.stopPropagation();
        e.preventDefault();
        var self = $(this);

        //programatically create a modal
        $('body').append(actionConfirmModalTemplate({
            message: 'Are you sure that you want to remove this?'
        }));

        $('#actionConfirmModal').modal();

        $('#actionConfirmModal').on('click', '.action-confirm-ok', function() {
            self.removeClass('action-confirm').trigger('click');
            $('#actionConfirmModal').modal('hide');
        });

        $('#actionConfirmModal').on('hidden.bs.modal', function () {
            $('#actionConfirmModal').remove();
        });
    });
});