'use strict';

_.templateSettings.variable = 'rc';
var mapContainer = $('#map'),
    myLocation = [_user.lat, _user.lng],
    myIcon = L.divIcon({
        html: '<img src="'+_user.avatar+'" />',
        iconSize:     [40, 40],
        className: 'mapIcon'
    }),
    map,
    circle = false,
    markers = new L.FeatureGroup(),
    musicianMapTemplate = _.template($('#musicianMapTemplate').html());

function initMap(){
    map = L.map('map').setView(myLocation, 14);

    L.tileLayer('http://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a>',
        maxZoom: 16,
        minZoom: 2,
        noWrap: true
    }).addTo(map);

    var myLocationMarker = L.marker(myLocation, {icon: myIcon}).addTo(map);

    map.on('zoomend', function(){
        resizeIcons();
    });

    drawRadius();
}

function resizeIcons(){
    var iconSize = map.getZoom() * 2;
    $('.mapIcon >').css({'width': iconSize, 'height': iconSize});
}

function drawRadius(){

    if (circle){
        map.removeLayer(circle);
    }

    circle = L.circle(myLocation, $('#search_form_distance').val() * 1000, {
        color: 'silver',
        fillColor: 'lightblue',
        fillOpacity: 0.3
    }).addTo(map);
}

function placeMarkers(){

    map.removeLayer(markers);
    markers = new L.FeatureGroup();

    $.each(filterResults, function(k, v){

        if (v.icon == "") {
            var iconSource = '<div class="no-icon"></div>';
        } else {
            var iconSource = v.icon;
        }

        var i = L.divIcon({
            html: iconSource,
            iconSize:     [40, 40],
            className: 'mapIcon'
        });

        var marker = L.marker([v.lat, v.lng], {icon: i});

        var popup = L.popup({
            'minWidth': 200
        }).setContent(musicianMapTemplate(v));

        marker.data = v;
        marker.bindPopup(popup);

        markers.addLayer(marker);
    });

    map.addLayer(markers);
    resizeIcons();
}

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

/*
function getLocation(){
    if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    }else{
        alert("Geolocation is not supported by this browser.");
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
*/