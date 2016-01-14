'use strict';

/* global Routing */

var _counter;
var _count;

function timer() {
    _count = _count - 1;
    if (_count == -1) {
        $('#shoutCountdown').text('');
        clearInterval(_counter);
        checkCanShout();
    }

    var seconds = _count % 60;

    if (seconds < 10) {
        seconds = '0' + seconds;
    }

    var minutes = Math.floor(_count / 60);
    var hours = Math.floor(minutes / 60);
    minutes %= 60;
    hours %= 60;

    if (hours < 10) {
        hours = '0' + hours;
    }

    if (minutes < 10) {
        minutes = '0' + minutes;
    }

    document.getElementById('shoutCountdown').innerHTML = hours + ':' + minutes + ':' + seconds;
    $('.shouts-countdown-container').removeClass('hidden');
    $('#shoutForm').addClass('hidden');

    return _count;
}

function checkCanShout(){

    if ($('#shoutForm').length > 0) {
        $.ajax({
            url: Routing.generate('can_shout'),
            success: function (data) {

                if (data.data > 0) {
                    _count = data.data;
                    _counter = setInterval(timer, 1000); //1000 will  run it every 1 second
                } else {
                    clearInterval(_counter);
                    $('#shoutForm').removeClass('hidden');
                    $('.shouts-countdown-container').addClass('hidden');
                }
            }
        });
    }
}

$(function() {
    checkCanShout();
});
