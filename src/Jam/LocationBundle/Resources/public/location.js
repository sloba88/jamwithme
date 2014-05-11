$(function() {

    $('.location_widget').each(function() {
        var widget = $(this),
            widget_location = widget.find('[id$="_location"]')

        var addresspickerMap = widget_location.find('[id$="_location_address"]').addresspicker({
            regionBias: widget_location.attr('data-country-short'),
            reverseGeocode: true,
            elements: {
                map: widget.find('[id$="_location_map"]'),
                neighborhood: widget.find('[id$="_location_neighborhood"]'),
                locality: widget.find('[id$="_location_locality"]'),
                country: widget.find('[id$="_location_country"]'),
                lat: widget.find('[id$="_location_lat"]'),
                lng: widget.find('[id$="_location_lng"]')
            },
            mapOptions: {
                center: new google.maps.LatLng(60.1605645, 24.8696196),
                zoom: widget_location.attr('data-marker') ? 9 : 6
            },
            updateCallback: function() {
                showMap(false);
            }
        })

        var gmarker = addresspickerMap.addresspicker("marker");
        gmarker.setVisible(widget_location.attr('data-marker') ? true : false);
        widget.find('.location_map').on('click', function() {
            if (!gmarker.getVisible()) {
                gmarker.setVisible(true);
                addresspickerMap.addresspicker("updatePosition");
            }
        });

        $('[id^="ad_location_address"]').on('change', function() {
            if (!$(this).val()) {
                $('[id^="ad_location"]').val('');
                gmarker.setVisible(false);
            }
        });

        var showMap = function(boolean) {
            widget.find('.dropdown-menu').css({'visibility': boolean ? 'visible' : 'hidden'});
        }

        widget.find('*').on('focus', function(e) {
            showMap(true);
        });
        
        $('body').bind('click', function(e) {
            if (!$(e.target).closest('.location_widget').length) {
                showMap(false);
            }
        });

    })

});