function getLocation(callback){
    if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(position) {
            return callback([position.coords.latitude, position.coords.longitude])
        }, showError);
    }else{
        alert('Geolocation is not supported by this browser.');
        return callback(false);
    }
}

function showError(error) {
    switch(error.code)
    {
        case error.PERMISSION_DENIED:
            alert("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            alert("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            alert("An unknown error occurred.");
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