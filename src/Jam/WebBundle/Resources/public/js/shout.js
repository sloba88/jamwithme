if (seconds > 0) {
    var count = seconds;

    function timer() {
        count = count - 1;
        if (count == -1) {
            $('#shoutCountdown').text('');
            clearInterval(counter);

            location.reload();
        }

        var seconds = count % 60;
        var minutes = Math.floor(count / 60);
        var hours = Math.floor(minutes / 60);
        minutes %= 60;
        hours %= 60;

        document.getElementById("shoutCountdown").innerHTML = hours + ":" + minutes + ":" + seconds;
    }

    var counter = setInterval(timer, 1000); //1000 will  run it every 1 second
}