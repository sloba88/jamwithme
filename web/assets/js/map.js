
var map;
var $map = $('#map-canvas');
var distanceCircle;
var bounds = null;
var donut;
var iconBase = 'https://maps.google.com/mapfiles/kml/';

var markers = [];
var filterResults;

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

function initializeMap() {

    var mapOptions = {
        zoom: 12,
        maxZoom: 13,
        scrollwheel: false,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL,
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        }
    };
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

    bounds = new google.maps.LatLngBounds();

    if ($map.data('lat') == ''){
        getLocation();
    }else{
        map.setCenter(new google.maps.LatLng(parseFloat($map.data('lat')), parseFloat($map.data('lng'))));
    }

    drawRadius($("#search_form_distance").val());

    renderMapView();
    scrollbarPlugin();
}

function getLocation(){
    if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    }else{
        alert("Geolocation is not supported by this browser.");
    }
}
function showPosition(position){
    map.setCenter(new google.maps.LatLng(parseFloat(position.coords.latitude), position.coords.longitude));
    $map.data('lat', position.coords.latitude);
    $map.data('lng', position.coords.longitude);
    addMyselfOnMap(map);
    //save to database also
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

function drawRadius(radius){
    if (typeof donut != 'undefined') donut.setMap(null);

    donut = new google.maps.Polygon({
        paths: [drawCircle(new google.maps.LatLng(parseFloat($map.data('lat')), parseFloat($map.data('lng'))), 1000, 1),
            drawCircle(new google.maps.LatLng(parseFloat($map.data('lat')), parseFloat($map.data('lng'))), radius, -1)],
        strokeColor: "white",
        strokeOpacity: 0.8,
        strokeWeight: 5,
        map: map,
        fillColor: "blue",
        fillOpacity: 0.10
    });
}

function addMyselfOnMap(map){
    icon = iconBase + 'pal2/icon2.png';
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(parseFloat($map.data('lat')), parseFloat($map.data('lng'))),
        map: map,
        title: _username,
        icon: icon
    });
    markers.push(marker);
}

function renderMapView() {
    var infowindow = null;
    deleteMarkers();
    drawRadius($("#search_form_distance").val());
    $.each(filterResults.data, function (k, v) {
        icon = iconBase + 'pal4/icon39.png';

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(v.lat, v.lng),
            map: map,
            title: v.username,
            icon: icon
        });
        markers.push(marker);

        google.maps.event.addListener(marker, 'mouseover', function() {
            if (infowindow) {
                infowindow.close();
            }

            infowindow = new google.maps.InfoWindow({
                content: musicianMapBoxTemplate( v )
            });

            infowindow.open(map,marker);
        });
    });

    addMyselfOnMap(map);

    // Sets the map on all markers in the array.
    function setAllMap(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }

    // Removes the markers from the map, but keeps them in the array.
    function clearMarkers() {
        setAllMap(null);
    }

    // Deletes all markers in the array by removing references to them.
    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }
}

function fetchMapData(){
    var data='';

    if ( $("input.filter-genres").val() != "" ){
        data += $(".filter-genres").serialize();
    }

    if ( $("input.filter-instruments").val() != "" ){
        data += $(".filter-instruments").serialize();
    }

    if ($("#lessons-checkbox").is(':checked')) {
        data += '&isTeacher=1';
    }

    if ( $("#search_form_distance").val() != 0 ){
        data += '&'+ $("#search_form_distance").serialize();
    }

    var url = $map.data('url');
    var infowindow = null;
    $.ajax({
        url: url,
        data: data
    }).done(function( result ) {
        if (result.status == 'success'){
            deleteMarkers();
            drawRadius($("#search_form_distance").val());
            $.each(result.data, function(k, v){

                icon = iconBase + 'pal4/icon39.png';

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(v.lat, v.lng),
                    map: map,
                    title: v.username,
                    icon: icon
                });
                markers.push(marker);

                google.maps.event.addListener(marker, 'mouseover', function() {
                    if (infowindow) {
                        infowindow.close();
                    }

                    infowindow = new google.maps.InfoWindow({
                        content: musicianMapBoxTemplate( v )
                    });

                    infowindow.open(map,marker);
                });

            });

            filterResults = result;
            renderGridView();

            //add myself on map
            addMyselfOnMap(map);
        }else{
            alert('Please set your location to be able to see people around you');
        }
    });

    // Sets the map on all markers in the array.
    function setAllMap(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }

    // Removes the markers from the map, but keeps them in the array.
    function clearMarkers() {
        setAllMap(null);
    }

    // Deletes all markers in the array by removing references to them.
    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }
}

function drawCircle(point, radius, dir) {
    var d2r = Math.PI / 180;   // degrees to radians
    var r2d = 180 / Math.PI;   // radians to degrees
    var earthsradius = 6371; // 3963 is the radius of the earth in miles

    var points = 128;

    // find the raidus in lat/lon
    var rlat = (radius / earthsradius) * r2d;
    var rlng = rlat / Math.cos(point.lat() * d2r);


    var extp = new Array();
    if (dir==1)	{var start=0;var end=points+1} // one extra here makes sure we connect the
    else		{var start=points+1;var end=0}
    for (var i=start; (dir==1 ? i < end : i > end); i=i+dir)
    {
        var theta = Math.PI * (i / (points/2));
        ey = point.lng() + (rlng * Math.cos(theta)); // center a + radius x * cos(theta)
        ex = point.lat() + (rlat * Math.sin(theta)); // center b + radius y * sin(theta)
        extp.push(new google.maps.LatLng(ex, ey));
        bounds.extend(extp[extp.length-1]);
    }
    // alert(extp.length);
    return extp;
}