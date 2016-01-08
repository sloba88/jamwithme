'use strict';

/* global _ */
/* global _user */
/* global L */
/* global filterResults */

var myLocation = [_user.lat, _user.lng],
    myIcon = L.divIcon({
        html: '<img src="'+_user.avatar+'" />',
        iconSize: [40, 40],
        className: 'mapIcon'
    }),
    map,
    circle = false,
    myLocationMarker,
    markers = new L.FeatureGroup();

function setMyFilterMarker() {
    myLocationMarker = L.marker(myLocation, {
        icon: myIcon
    }).addTo(map);

    map.setView(myLocationMarker.getLatLng(), 17, { animate: true });
    resizeIcons();
}

function initMap(){
    map = L.map('map');

    L.tileLayer('http://{s}.{base}.maps.cit.api.here.com/maptile/2.1/maptile/{mapID}/normal.day/{z}/{x}/{y}/256/png8?app_id={app_id}&app_code={app_code}', {
        attribution: 'Map &copy; 1987-2014 <a href="http://developer.here.com">HERE</a>',
        subdomains: '1234',
        mapID: 'newest',
        app_id: 'll2Cde8wIa5h5YgDoW9x',
        app_code: '1LBq52rA1_q-8pj6_67OMg',
        base: 'base',
        maxZoom: 15,
        minZoom: 9
    }).addTo(map);

    map.on('zoomend', function(){
        resizeIcons();
    });

    console.log('map initialized');
    return true;
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
    markers = new L.markerClusterGroup({
        maxClusterRadius: 30
    });

    markers.on('animationend', function () {
        resizeIcons();
    });

    markers.on('spiderfied', function () {
        resizeIcons();
    });

    $.each(filterResults, function(k, v){

        var iconSource;
        if (v.icon === '') {
            iconSource = '<div class="no-icon"></div>';
        } else {
            iconSource = v.icon;
        }

        var i = L.divIcon({
            html: iconSource,
            iconSize:     [40, 40],
            className: 'mapIcon'
        });

        var marker = L.marker([v.lat, v.lng], {icon: i});

        var popup = L.popup({
            'minWidth': 200
        }).setContent(window.JST['musicianMapTemplate'](v));

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
