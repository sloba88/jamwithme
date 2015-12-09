var counter;
function checkCanShout(){

    if ($('#shoutForm').length > 0) {
        $.ajax({
            url: Routing.generate('can_shout'),
            success: function (data) {

                if (data.data > 0) {
                    var count = data.data;

                    function timer() {
                        count = count - 1;
                        if (count == -1) {
                            $('#shoutCountdown').text('');
                            clearInterval(counter);
                            checkCanShout();
                        }

                        var seconds = count % 60;

                        if (seconds < 10) {
                            seconds = '0' + seconds;
                        }

                        var minutes = Math.floor(count / 60);
                        var hours = Math.floor(minutes / 60);
                        minutes %= 60;
                        hours %= 60;

                        if (hours < 10) {
                            hours = '0' + hours;
                        }

                        if (minutes < 10) {
                            minutes = '0' + minutes;
                        }

                        document.getElementById("shoutCountdown").innerHTML = hours + ":" + minutes + ":" + seconds;
                        $('.shouts-countdown-container').removeClass('hidden');
                        $('#shoutForm').addClass('hidden');
                    }

                    counter = setInterval(timer, 1000); //1000 will  run it every 1 second
                } else {
                    clearInterval(counter);
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
