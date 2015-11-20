var counter;
function checkCanShout(){

    if ($('#shoutForm').length > 0) {
        $.ajax({
            url: Routing.generate('can_shout'),
            success: function (seconds) {

                if (seconds > 0) {
                    var count = seconds;

                    function timer() {
                        count = count - 1;
                        if (count == -1) {
                            $('#shoutCountdown').text('');
                            clearInterval(counter);
                            checkCanShout();
                        }

                        var seconds = count % 60;
                        var minutes = Math.floor(count / 60);
                        var hours = Math.floor(minutes / 60);
                        minutes %= 60;
                        hours %= 60;

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
