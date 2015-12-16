var filterResults = [];
var initializedMap = false;
var loadMoreResults = true;
var page = 1;

$(function() {

    $('#map-view').on('click', function(){
        delay(function(){
            if (initializedMap == false ){
                $('#map').height($('.view-tab-container').height() - 10);
                initializedMap = initMap();
                placeMarkers();
                setMyFilterMarker();
                drawRadius();
            }
        }, 500);
    });

    if (localStorage.view=='list') {
        $('#list-tab-btn').click();
    }

    $('#main-filter-form').on('change', function() {
        //reset page to 1st page
        page = 1;
        delay(function(){
            filterMusicians();
            filterShouts();
        }, 500);
    });

    //parse genres
    $.ajax({
        url: Routing.generate('api_genres')
    }).done(function( result ) {
        $('.filter-genres').select2({
            data: result,
            multiple: true
        });
    });

    //parse instruments
    $.ajax({
        url: Routing.generate('api_instruments')
    }).done(function( result ) {
        $('.filter-instruments').select2({
            data: result,
            multiple: true
        });
    });

    if (_user.lat == '' || _user.lng == '' || _user.temporaryLocation == '1') {
        //if there are no coordinates set try browser get position
        getLocation(function(myBrowserLocation) {
            myLocation = myBrowserLocation;
            if (!myLocation) {
                myLocation = [_user.lat, _user.lng];
                filterMusicians();
                filterShouts();
            } else {
                //save this to db and then do filters
                $.ajax({
                    url: Routing.generate('api_set_musician_location'),
                    type: 'POST',
                    data: { 'coords': myBrowserLocation.join()}
                }).done(function( result ) {

                    $('.no-location-message').fadeOut(function (){
                        $('.no-location-message').remove();
                    });

                    addMessage(result.status, result.message);

                    filterMusicians();
                    filterShouts();
                });
            }
        });
    }else {
        filterMusicians();
        filterShouts();
    }

    //activate tabs
    tabsToggle($('.tabs-activate'));

    //activates tooltip
    $('[data-toggle=tooltip]').tooltip();

    $('body').on('click', '#subscribeToSearch', function(e){
        e.preventDefault();
        var data = {};
        data.genres = [];
        data.instruments = [];

        if ($('#genres').val() !== '') {
            data.genres = $('#genres').val().split(',');
        }

        if ($('#instruments').val() !== '') {
            data.instruments = $('#instruments').val().split(',');
        }

        data.distance = $('#search_form_distance').val();
        data.isTeacher = $('body.page-teachers').length > 0;

        $.ajax({
            url: Routing.generate('subscribe_search_add'),
            data: $.param(data)
        }).done(function( result ) {
            if (result.status == 'success') {
                $('.people-listing-grid').html(notificationTemplate({ type : 'success', message : 'Subscription to this search made successfully', temp : 'temp' }));
            }
        });
    });

    //activates slider
    $('#filter-by-distance-slider').slider({
        range: "min",
        value: $('#search_form_distance').val(),
        min: 2,
        max: 20,
        step: 2,
        create: function(event, ui) {
            var selection = $('#filter-by-distance-slider').slider('value');
            $('.slide-max').text(selection + 'km');
        },
        slide: function(event, ui) {
            $('.slide-max').text(ui.value + 'km');
            $('#search_form_distance').val(ui.value).trigger('change');
            $('#filter-by-distance-btn span').text(ui.value + 'km around you');
        }
    });

    $('#filter-by-distance-btn span').text($('#search_form_distance').val() + 'km around you');

    $('.view-tab-container').on('ps-y-reach-end', function () {

        if (loadMoreResults === true) {
            loadMoreResults = false;
            filterMusicians();
        }
    })

});

function renderGridView(data) {
    $.each(data, function (k, v) {
        $(".people-listing-grid").append(musicianBoxTemplate(v));
    });

    if ($('.people-listing-grid').width() > 1000){
        $('.musician-box-container').removeClass('col-lg-3').addClass('col-lg-2');
    }

    $('.people-listing-grid').removeClass('loading-content');

    scrollbarPlugin();
}

function getFilterData() {
    var data='';

    if ( $("input.filter-genres").val() != "" ){
        data += $(".filter-genres").serialize();
        data += '&';
    }

    if ( $("input.filter-instruments").val() != "" ){
        data += $(".filter-instruments").serialize();
        data += '&';
    }

    data += 'isTeacher='+$('body.page-teachers').length;

    if ( $('#search_form_distance').val() != 0 ){
        data += '&'+ $("#search_form_distance").serialize();
    }

    data += '&page='+page;

    return data;
}

function filterMusicians(){
    if (page === 1) {
        $('.people-listing-grid').html('').addClass('loading-content');
    }

    $.ajax({
        url: Routing.generate('musicians_find'),
        data: getFilterData()
    }).done(function( result ) {
        if (result.status == 'success') {
            filterResults = filterResults.concat(result.data);
            renderGridView(result.data);
            if (filterResults.length == 0){
                $('.people-listing-grid').html('<br /><p>Didn\'t find what you searched for? We can let you know when people with this profile join. <br /><a href="#" id="subscribeToSearch">Subscribe for this search criteria.</a></p>')
            }

            if (result.data.length !== 0) {
                page ++;
                loadMoreResults = true;
            }

            if (initializedMap != false ){
                placeMarkers();
                drawRadius();
            }

        }
    });
}

function filterShouts() {
    $('.shouts-listing').html('');

    $.ajax({
        url: Routing.generate('shouts_find'),
        data: getFilterData()
    }).done(function( result ) {
        if (result.status == 'success'){
            $.each(result.data, function(k, v){
                $( '.shouts-listing' ).prepend(shoutBoxTemplate( v ) );
            });
        }
    });
}