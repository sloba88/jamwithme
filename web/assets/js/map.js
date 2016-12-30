'use strict';

/* global _user */
/* global L */
/* global filterResults */
/* global SVGInjector */

var myLocation = [_user.lat, _user.lng],
    myIcon = L.divIcon({
        html: '<img src="'+_user.avatar+'" />',
        iconSize: [50, 50],
        className: 'mapIcon myIconAvatar'
    }),
    map,
    circle = false,
    outterCircle = false,
    innerCircle = false,
    myLocationMarker,
    markers = new L.FeatureGroup(),
    services = new L.FeatureGroup();

//http://wiki.openstreetmap.org/wiki/Zoom_levels
var zoomToMeters = {
    16 : 2.387,
    15 : 4.773,
    14 : 9.547,
    13 : 19.093,
    12 : 38.187,
    11 : 76.373,
    10 : 152.746,
    9  : 305.492,
    8  : 610.984
};

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
        minZoom: 8
    }).addTo(map);

    map.on('zoomend', function(){
        resizeIcons();
        drawRadius();
    });

    map.on('popupopen', function(){
        SVGInjector(document.querySelectorAll('img.inject-me'));
    });

    console.log('map initialized');
    return true;
}

function resizeIcons(){
    var iconSize = map.getZoom() * 2.5;
    $('.mapIcon >, .mapIcon').css({'width': iconSize, 'height': iconSize});
}

function drawRadius(){

    if (circle){
        map.removeLayer(circle);
        map.removeLayer(outterCircle);
        map.removeLayer(innerCircle);
    }

    var m = $('#search_form_distance').val() * 1000;
    var weight = (m / zoomToMeters[map.getZoom()]) * 2;

    circle = L.circle(myLocation, (m) * 1.5 , {
        color: '#CCCCCC',
        weight: weight,
        fillOpacity: 0

    }).addTo(map);

    innerCircle = L.circle(myLocation, (m) , {
        color: 'white',
        weight: 5,
        fillOpacity: 0

    }).addTo(map);


    outterCircle = L.circle(myLocation, (m) * 5 , {
        color: 'silver',
        weight: weight * 6.05,
        fillOpacity: 0

    }).addTo(map);

}

function placeMarkers() {

    map.removeLayer(markers);
    markers = new L.markerClusterGroup({
        maxClusterRadius: 25
    });

    markers.on('animationend', function () {
        resizeIcons();
    });

    markers.on('spiderfied', function () {
        resizeIcons();
    });

    $.each(filterResults, function(k, v) {
        var iconSource;
        if (v.instrument === '' || v.instrument === 'Other Skills') {
            iconSource = '<div class="no-icon"></div>';
            createIcon(iconSource, v);
        } else {
            var fragment = document.createDocumentFragment();
            var img = new Image();
            img.src = '/assets/images/icons-svg/' + v.instrument + '.svg';
            fragment.appendChild(img);
            SVGInjector(fragment.childNodes, {}, function(){
                iconSource = fragment.childNodes[0].outerHTML;
                createIcon(iconSource, v);
            });
        }
    });

    function createIcon(iconSource, v) {
        var i = L.divIcon({
            iconSize:     [50, 50],
            className: 'mapIcon map-icon-' + v.instrument,
            html: iconSource
        });

        var marker = L.marker([v.lat, v.lng], {icon: i});

        var popup = L.popup({
            'minWidth': 200
        }).setContent(window.JST.musicianMapTemplate(v));

        marker.data = v;
        marker.bindPopup(popup);

        markers.addLayer(marker);
    }

    map.addLayer(markers);
    resizeIcons();

    SVGInjector(document.querySelectorAll('img.inject-me'));
}

function placeServiceMarkers(data) {

    map.removeLayer(services);
    services = new L.markerClusterGroup({
        maxClusterRadius: 5
    });

    services.on('animationend', function () {
        resizeIcons();
    });

    services.on('spiderfied', function () {
        resizeIcons();
    });

    $.each(data, function(k, v) {
        var iconSource;
        iconSource = '<div class="no-icon"></div>';
        createIcon(iconSource, v);
    });

    function createIcon(iconSource, v) {
        var i = L.divIcon({
            iconSize:     [50, 50],
            className: 'mapIcon map-icon-' + v.instrument,
            html: iconSource
        });

        var marker = L.marker([v.lat, v.lng], {icon: i});

        var popup = L.popup({
            'minWidth': 200
        }).setContent(window.JST.serviceMapTemplate(v));

        marker.data = v;
        marker.bindPopup(popup);

        services.addLayer(marker);
    }

    map.addLayer(services);
    resizeIcons();

    SVGInjector(document.querySelectorAll('img.inject-me'));
}

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();
