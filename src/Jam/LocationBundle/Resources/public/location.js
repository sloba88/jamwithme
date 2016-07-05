'use strict';

/* global _user */
/* global L */

var myLocation = [_user.lat, _user.lng],
    map,
    mapInitialized = false,
    waitAutocomplete,
    myLocationMarker,
    circle = false;
    L.Icon.Default.imagePath = '/vendor/leaflet-dist/images';

function setMyMarker() {
    myLocationMarker = L.marker(myLocation, {
        draggable: true
    }).addTo(map);

    myLocationMarker.on('dragend', markerDragEnd);

    map.setView(myLocationMarker.getLatLng(), 17, { animate: true });
}

//TODO: this is duplicate
function resizeIcons() {
    var iconSize = map.getZoom() * 2;
    $('.mapIcon >').css({'width': iconSize, 'height': iconSize});
}

//TODO: this is duplicate
function drawRadius() {

    if (circle){
        map.removeLayer(circle);
    }

    circle = L.circle(myLocation, 30 * 1000, {
        color: 'silver',
        fillColor: 'lightblue',
        fillOpacity: 0.3
    }).addTo(map);
}

var markerDragEnd = function(e) {
    var self = e.target;
    self.update();

    $.ajax({
        url: location.protocol + '//nominatim.openstreetmap.org/reverse?format=json&lat='+self.getLatLng().lat+'&lon='+self.getLatLng().lng+'&zoom=18&addressdetails=1&accept-language=en',
        success: function(data){
            console.log(data);
            $('[id$="_location_locality"]').val(data.address.city);
            $('[id$="_location_neighborhood"]').val(data.address.suburb);
            $('[id$="_administrative_area_level_3"]').val(data.address.city);
            $('[id$="_location_country"]').val(data.address.country);
            $('[id$="_location_route"]').val(data.address.road);
            $('[id$="_location_zip"]').val(data.address.postcode);
            $('[id$="_location_lat"]').val(data.lat);
            $('[id$="_location_lng"]').val(data.lon);
            $('[id$="_location_isTemporary"]').val(false);


            var displayAddress = '';

            displayAddress += data.address.road ? data.address.road + ', ' : '';
            displayAddress += data.address.suburb ? data.address.suburb + ', ' : '';
            displayAddress += data.address.neighbourhood ? data.address.neighbourhood + ', ' : '';
            displayAddress += data.address.city ? data.address.city + ', ' : '';
            displayAddress += data.address.town ? data.address.town + ', ' : '';
            displayAddress += data.address.village ? data.address.village + ', ' : '';
            displayAddress += data.address.island ? data.address.island + ', ' : '';
            displayAddress += data.address.country ? data.address.country + ', ' : '';

            displayAddress = displayAddress.replace(/,\s*$/, '');

            $('.location_widget .location-input').val(displayAddress);
        }
    });
};

function initMap(){
    if (!mapInitialized){
        console.log('map initialized');
        if ($('body.page-settings').length > 0) {
            $('.location_widget').find('.dropdown-menu').css({'display': 'block'});
        }

        map = L.map('_location_map');

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

        $('[id^="ad_location_address"]').on('change', function() {
            if (!$(this).val()) {
                $('[id^="ad_location"]').val('');
            }
        });

        mapInitialized = true;
    }
}

$(function() {

    if ($('.location_widget:visible').length > 0) {
        initMap();
        setMyMarker();
    }

    $('#location-tab').on('click', function(){
        setTimeout(function() {
            initMap();
            setMyMarker();
        }, 400 );
    });

    $('body').on('keyup', '.location_widget .location-input', function(){
        var value = $(this).val();

        if (waitAutocomplete) {
            clearTimeout(waitAutocomplete);
        }

        if (value.length > 2){
            waitAutocomplete = setTimeout(function() {
                $.ajax({
                    url: location.protocol + '//nominatim.openstreetmap.org/search?q='+value+'&format=json&addressdetails=1&accept-language=en&namedetails=0&polygon=0&bounded=0&',
                    cache: true,
                    success: function(data){
                        console.log(data);
                        $('.location-results').html('').hide();
                        if (data.length > 0){
                            $.each(data, function(k, v){

                                var displayAddress = '';

                                displayAddress += v.address.road ? v.address.road + ', ' : '';
                                displayAddress += v.address.suburb ? v.address.suburb + ', ' : '';
                                displayAddress += v.address.neighbourhood ? v.address.neighbourhood + ', ' : '';
                                displayAddress += v.address.city ? v.address.city + ', ' : '';
                                displayAddress += v.address.town ? v.address.town + ', ' : '';
                                displayAddress += v.address.village ? v.address.village + ', ' : '';
                                displayAddress += v.address.island ? v.address.island + ', ' : '';
                                displayAddress += v.address.country ? v.address.country + ', ' : '';

                                displayAddress = displayAddress.replace(/,\s*$/, '');

                                var element = $('<li />').append($('<a/>', {
                                    href: '#',
                                    'data-lat': v.lat,
                                    'data-lng': v.lon,
                                    text: displayAddress,
                                    'data-all': JSON.stringify(v)
                                }));

                                $('.location-results').append(element).show();
                            });
                        }
                    }
                });
            }, 500 );
        }else {
            $('.location-results').html('').hide();
        }
    });

    $('body').on('click', '.location-results a', function(e){
        e.preventDefault();
        myLocationMarker.setLatLng(new L.LatLng(Number($(this).data('lat')), Number($(this).data('lng'))));
        map.setView(myLocationMarker.getLatLng(), 17, { animate: true });
        $('.location-results').css({'display': 'none'});
        $('.location_widget .location-input').val($(this).text());

        var data = $(this).data('all');

        $('[id$="_location_locality"]').val(data.address.city);
        $('[id$="_location_neighborhood"]').val(data.address.suburb);
        $('[id$="_administrative_area_level_3"]').val(data.address.city);
        $('[id$="_location_country"]').val(data.address.country);
        $('[id$="_location_route"]').val(data.address.road);
        $('[id$="_location_zip"]').val(data.address.postcode);
        $('[id$="_location_lat"]').val(data.lat);
        $('[id$="_location_lng"]').val(data.lon);
        $('[id$="_location_isTemporary"]').val(false);

    });


    $('.location_widget').find('*').on('focus', function() {
        $('.location_widget').find('.dropdown-menu').css({'display': 'block'});
    });

    $('body').bind('click', function(e) {
        if (!$(e.target).closest('.location_widget').length) {
            $('.location_widget').find('.dropdown-menu').css({'display': 'none'});
        }
    });

});
