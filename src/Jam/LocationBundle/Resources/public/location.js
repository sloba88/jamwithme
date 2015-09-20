
var myLocation = [_user.lat, _user.lng],
    map,
    showMap = false,
    myLocationMarker,
    circle = false;
    L.Icon.Default.imagePath = '/vendor/leaflet-dist/images';


if (_user.lat == '') {
    myLocation = [60.1576083, 24.8740487];
}

//TODO: this is duplicate
function resizeIcons(){
    var iconSize = map.getZoom() * 2;
    $('.mapIcon >').css({'width': iconSize, 'height': iconSize});
}

//TODO: this is duplicate
function drawRadius(){

    if (circle){
        map.removeLayer(circle);
    }

    circle = L.circle(myLocation, 30 * 1000, {
        color: 'silver',
        fillColor: 'lightblue',
        fillOpacity: 0.3
    }).addTo(map);
}

var markerDragEnd = function(e){
    var self = e.target;
    markerIsDragged = true;
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


            var displayAddress = '';

            displayAddress += data.address.road ? data.address.road + ', ' : '';
            displayAddress += data.address.suburb ? data.address.suburb + ', ' : '';
            displayAddress += data.address.neighbourhood ? data.address.neighbourhood + ', ' : '';
            displayAddress += data.address.city ? data.address.city + ', ' : '';
            displayAddress += data.address.town ? data.address.town + ', ' : '';
            displayAddress += data.address.village ? data.address.village + ', ' : '';
            displayAddress += data.address.island ? data.address.island + ', ' : '';
            displayAddress += data.address.country ? data.address.country + ', ' : '';

            displayAddress = displayAddress.replace(/,\s*$/, "");

            $('#fos_user_profile_form_location_address').val(displayAddress);
        }
    });
};

$(function() {

    $('.location_widget').each(function() {
        var widget = $(this),
            widget_location = widget.find('[id$="_location"]')

        if (!showMap){

            $('.location_widget').find('.dropdown-menu').css({'display': 'block'});

            map = L.map('_location_map').setView(myLocation, 14);

            L.tileLayer('http://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a>',
                maxZoom: 16,
                minZoom: 2,
                noWrap: true
            }).addTo(map);

            myLocationMarker = L.marker(myLocation, {
                draggable: true
            }).addTo(map);

            myLocationMarker.on('dragend', markerDragEnd);

            map.on('zoomend', function(){
                resizeIcons();
            });

            $('[id^="ad_location_address"]').on('change', function() {
                if (!$(this).val()) {
                    $('[id^="ad_location"]').val('');
                    //gmarker.setVisible(false);
                }
            });

            showMap = true;
        }

        $('body').on('keyup', '#fos_user_profile_form_location_address', function(e){
            var value = $(this).val();
            var self = $(this);

            if (value.length > 2){
                $.ajax({
                    url: location.protocol + '//nominatim.openstreetmap.org/search?q='+value+'&format=json&polygon=1&addressdetails=1&accept-language=en&countrycodes=fi',
                    cache: true,
                    success: function(data){
                        console.log(data);
                        $('.location-results').html('').hide();
                        if (data.length > 1){
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

                                displayAddress = displayAddress.replace(/,\s*$/, "");

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
            }else {
                $('.location-results').html('').hide();
            }
        });

        $('body').on('click', '.location-results a', function(e){
            e.preventDefault();
            myLocationMarker.setLatLng(new L.LatLng(Number($(this).data('lat')), Number($(this).data('lng'))));
            map.setView(myLocationMarker.getLatLng(), 17, { animate: true });
            $('.location-results').css({'display': 'none'});
            $('#fos_user_profile_form_location_address').val($(this).text());

            var data = $(this).data('all');

            $('[id$="_location_locality"]').val(data.address.city);
            $('[id$="_location_neighborhood"]').val(data.address.suburb);
            $('[id$="_administrative_area_level_3"]').val(data.address.city);
            $('[id$="_location_country"]').val(data.address.country);
            $('[id$="_location_route"]').val(data.address.road);
            $('[id$="_location_zip"]').val(data.address.postcode);
            $('[id$="_location_lat"]').val(data.lat);
            $('[id$="_location_lng"]').val(data.lon);

        });


        $('.location_widget').find('*').on('focus', function(e) {
            $('.location_widget').find('.dropdown-menu').css({'display': 'block'});
        });
        
        $('body').bind('click', function(e) {
            if (!$(e.target).closest('.location_widget').length) {
                $('.location_widget').find('.dropdown-menu').css({'display': 'none'});
            }
        });

    })

});
