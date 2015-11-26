function getLocation(callback){
    if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(position) {
            return callback([position.coords.latitude, position.coords.longitude])
        }, showError);
        return callback(false);
    }else{
        alert('Geolocation is not supported by this browser.');
        return callback(false);
    }
}

function showError(error) {
    switch(error.code)
    {
        case error.PERMISSION_DENIED:
            console.log("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            console.log("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            console.log("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            console.log("An unknown error occurred.");
            break;
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