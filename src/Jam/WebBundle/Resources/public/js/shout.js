$(function(){
    if (shoutedToday) {

        var tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(0,0,0,0);

        $('#shoutCountdown').countdown(tomorrow, function(event){
            $(event.currentTarget).text(event.strftime('%H:%M:%S'));
        })
        .on('finish.countdown', function(){
            location.reload();
        });
    }
});
