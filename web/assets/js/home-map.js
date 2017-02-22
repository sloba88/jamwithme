'use strict';

/* global L */
/* global Routing */
/* global SVGInjector */
/* global getLocation */

var myLocation,
    map,
    filterResults = [],
    mapInitialized = false,
    myLocationMarker,
    circle = false,
    outterCircle = false,
    innerCircle = false,
    markers = new L.FeatureGroup(),
    zoomToMeters = {
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

L.Icon.Default.imagePath = '/vendor/leaflet-dist/images';

function resizeIcons(){
    var iconSize = map.getZoom() * 2.1;
    $('.mapIcon >, .mapIcon').css({'width': iconSize, 'height': iconSize});
}

function setMyMarker() {
    myLocationMarker = L.marker(myLocation, {
        draggable: false
    }).addTo(map);
}

function placeMarkers() {

    map.removeLayer(markers);
    markers = new L.markerClusterGroup({
        maxClusterRadius: 15,
        zoomToBoundsOnClick: false
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
            SVGInjector(fragment.childNodes, {}, function() {
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
        markers.addLayer(marker);
    }

    map.addLayer(markers);
    resizeIcons();

    SVGInjector(document.querySelectorAll('img.inject-me'));
}

//TODO: this is duplicate
function drawRadius(myLocation){

    if (circle){
        map.removeLayer(circle);
        map.removeLayer(outterCircle);
        map.removeLayer(innerCircle);
    }

    var m = 30 * 1000;
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

function initMap(){
    if (!mapInitialized){

        map = L.map('map', { scrollWheelZoom: false, zoomControl: true, doubleClickZoom: false });
        map.zoomControl.setPosition('bottomleft');

        L.tileLayer('https://{s}.{base}.maps.cit.api.here.com/maptile/2.1/maptile/{mapID}/normal.day/{z}/{x}/{y}/256/png8?app_id={app_id}&app_code={app_code}', {
            attribution: 'Map &copy; 1987-2014 <a href="http://developer.here.com">HERE</a>',
            subdomains: '1234',
            mapID: 'newest',
            app_id: 'll2Cde8wIa5h5YgDoW9x',
            app_code: '1LBq52rA1_q-8pj6_67OMg',
            base: 'base',
            maxZoom: 15,
            minZoom: 8
        }).addTo(map);

        map.on('moveend', function() {
            resizeIcons();
        });

        console.log('map initialized');
        mapInitialized = true;
    }
}

function fetchMapData(myLocation, city, callback){
    $.ajax({
        url: Routing.generate('musicians_find_map') + '?city=' + city + '&lat=' + myLocation[0] + '&lng=' + myLocation[1]
    }).done(function( result ) {
        if (result.status === 'success') {
            filterResults = result.data;
            if (mapInitialized !== false ){
                callback(result.location);

                placeMarkers();
                drawRadius(result.location);

                resizeIcons();

                setTimeout(function() {
                    resizeIcons();
                }, 400);
            }
        }
    });
}

$(function() {

    getLocation(function(myBrowserLocation) {
        myLocation = myBrowserLocation;
        if (!myLocation) {
            initMap();
            setTimeout(function() {
                $('#top-locations').trigger('change');
            }, 500);
        } else {
            initMap();
            fetchMapData(myLocation, false, function() {
                setMyMarker();
                map.setView(L.latLng(myLocation[0], myLocation[1]), 11, { animate: true });
            });
        }
    });

    $('body').on('change', '#top-locations', function() {
        fetchMapData([0, 0], $(this).val(), function(myLocation) {
            myLocation = [myLocation.lat, myLocation.lng];
            map.setView(L.latLng(myLocation), 12, { animate: true });
        });
    });
});
