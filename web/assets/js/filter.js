var qd = {};
decodeURIComponent(location.search).substr(1).split("&").forEach(function(item) {(item.split("=")[0] in qd) ? qd[item.split("=")[0]].push(item.split("=")[1]) : qd[item.split("=")[0]] = [item.split("=")[1],]});

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
        $.each(result, function(k, v){
            $('#filter-genres').append('<option value="'+v.id+'">'+ v.text + '</option>');
        });
        //parse instruments
        $.ajax({
            url: Routing.generate('api_instruments')
        }).done(function( result ) {
            $.each(result, function(k, v){
                $('#filter-instruments').append('<option value="'+v.id+'">'+ v.text + '</option>');
            });

            $('select').select2();

            if (qd['s[genres][]']){
                $("#filter-genres").select2('val', qd['s[genres][]']);
                $("#filter-genres").trigger('change');
            }

            if (qd['s[instruments][]']){
                $("#filter-instruments").select2('val', qd['s[instruments][]']);
                $("#filter-instruments").trigger('change');
            }

            filterMusicians();
        });
    });

    //activate tabs
    tabsToggle($('.tabs-activate'));

    //activates tooltip
    $("[data-toggle=tooltip]").tooltip();

    $('body').on('click', '#subscribeToSearch', function(e){
        e.preventDefault();
        var data = {};
        data.genres = [];
        data.instruments = [];

        $('#filter-genres option:selected').each(function () {
            if ($(this).length) {
                data.genres.push($(this).text())
            }
        });

        $('#filter-instruments option:selected').each(function () {
            if ($(this).length) {
                data.instruments.push($(this).text())
            }
        });

        data.distance = $('#search_form_distance').val();
        data.isTeacher = $('#lessons-checkbox').val();

        $.ajax({
            url: Routing.generate('subscribe_search_add'),
            data: $.param(data)
        }).done(function( result ) {
            if (result.status == 'success') {
                $('.people-listing-grid').html(notificationTemplate({ type : 'success', message : 'Subscription to this search made successfully', temp : 'temp' }));
            }
        });
    });

});

function renderGridView() {
    $(".people-listing-grid").html('');
    $.each(filterResults.data, function (k, v) {
        $(".people-listing-grid").append(musicianBoxTemplate(v));
    });
}

function filterMusicians(){
    var data='';

    if ( $("#filter-genres").val() != 0 ){
        data += $("#filter-genres").serialize();
    }

    if ( $("#filter-instruments").val() != 0 ){
        data += $("#filter-instruments").serialize();
    }

    if ($("#lessons-checkbox").is(':checked')) {
        data += '&isTeacher=1';
    }

    if ( $("#search_form_distance").val() != 0 ){
        data += '&'+ $("#search_form_distance").serialize();
    }

    var url = $map.data('url');

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