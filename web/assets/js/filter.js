$(function() {

    $.ajax({
        url: baseURL+'/shouts/find'
        //data: data
    }).done(function( result ) {
        if (result.status == 'success'){
            $.each(result.data, function(k, v){
                $( ".shouts-listing" ).prepend(shoutBoxTemplate( v ) );
            });
        }
    });

    $('#map-view').on('click', function(){
        delay(function(){
            initializeMap();
        }, 500);
    });

    if (localStorage.view=="list") {
        $("#list-tab-btn").click();
    }

    $("#main-filter-form").on('change', function(){
        delay(function(){
            if ($('#map-canvas').is(':visible')){
                fetchMapData();
            }else{
                filterMusicians();
            }
        }, 500);
    });

    //parse generes
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

    filterMusicians();

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
        data.isTeacher = $('#lessons-checkbox').is(':checked');

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

});

function renderGridView() {
    $.each(filterResults.data, function (k, v) {
        $(".people-listing-grid").append(musicianBoxTemplate(v));
    });

    if ($('.people-listing-grid').width() > 1000){
        $('.musician-box-container').removeClass('col-lg-3').addClass('col-lg-2');
    }

    $('.people-listing-grid').removeClass('loading-content');
}

function filterMusicians(){
    var data='';

    if ( $("input.filter-genres").val() != "" ){
        data += $(".filter-genres").serialize();
        data += '&';
    }

    if ( $("input.filter-instruments").val() != "" ){
        data += $(".filter-instruments").serialize();
        data += '&';
    }

    if ($("#lessons-checkbox").is(':checked')) {
        data += 'isTeacher=1';
    }

    if ( $("#search_form_distance").val() != 0 ){
        data += '&'+ $("#search_form_distance").serialize();
    }

    var url = $map.data('url');

    $('.people-listing-grid').html('').addClass('loading-content');

    $.ajax({
        url: url,
        data: data
    }).done(function( result ) {
        if (result.status == 'success') {
            filterResults = result;
            renderGridView();
            if (result.data.length == 0){
                $('.people-listing-grid').html('<br /><p>Didn\'t find what you searched for? We can let you know when people with this profile join. <br /><a href="#" id="subscribeToSearch">Subscribe for this search criteria.</a></p>')
            }
        }
    });
}