'use strict';

/* global Routing */
/* global delay */
/* global initMap */
/* global setMyFilterMarker */
/* global _user */
/* global getLocation */
/* global tabsToggle */
/* global placeMarkers */
/* global drawRadius */
/* global addMessage */
/* global myLocation */
/* global scrollbarPlugin */
/* global ga */
/* global SVGInjector */
/* global matchStart */

//TODO: globals are bad, don't use globals
var filterResults = [];
var initializedMap = false;
var loadMoreResults = true;
var loadMoreShoutsResults = true;
var page = 1;
var shoutsPage = 1;
var filterRunning = false;

function renderGridView(data) {

    $.each(data, function (k, v) {
        $('.people-listing-grid').append(window.JST.musicianBoxTemplate(v));
    });

    if (loadMoreResults === false && ($('select.filter-genres').val() !== '' || $('select.filter-instruments').val() !== '') ){
        $('.people-listing-grid').append('<div class="subscribe-info-search"><div class="alert alert-info" role="alert">Didn\'t find what you searched for? We can let you know when people with this profile join. </div><a href="#" class="btn btn-primary" id="subscribeToSearch"><i class="fa fa-envelope"></i> Subscribe for this search criteria</a></div>');
    }

    if ($('.people-listing-grid').width() > 1000){
        $('.musician-box-container').removeClass('col-lg-3').addClass('col-lg-2');
    }

    $('.people-listing-grid').removeClass('loading-content');

    scrollbarPlugin();

    //replace all svg
    SVGInjector(document.querySelectorAll('img.inject-me'));
}

function getFilterData() {

    if ($('#main-filter-form').length === 0) {
        //no filters form
        return '';
    }

    var data='';

    if ( $('select.filter-genres').val() !== null ){
        //data += $('select.filter-genres').serialize();

        var result = $('select.filter-genres option:selected').map(function(i, opt) {
            return $(opt).val();
        }).toArray().join(',');

        data += 'genres=' + result;
        data += '&';

        $.each($('select.filter-genres').select2('data'), function(k, v) {
            ga('send', {
                hitType: 'event',
                eventCategory: 'search',
                eventAction: 'genres',
                eventLabel: v.text
            });
        });
    }

    if ( $('select.filter-instruments').val() !== null ){
        //data += $('select.filter-genres').serialize();

        var result2 = $('select.filter-instruments option:selected').map(function(i, opt) {
            return $(opt).val();
        }).toArray().join(',');

        data += 'instruments=' + result2;
        data += '&';

        $.each($('select.filter-instruments').select2('data'), function(k, v) {
            ga('send', {
                hitType: 'event',
                eventCategory: 'search',
                eventAction: 'instruments',
                eventLabel: v.text
            });
        });
    }

    data += 'isTeacher='+$('body.page-teachers').length;

    if ( $('#search_form_distance').val() !== 0 ){
        data += '&'+ $('#search_form_distance').serialize();
    }

    if ($('.page-shouts ').length !== 0) {
        data += '&distance=50';
        $('.shouts-listing-container h4 a').text('Shouts 50km around you');
    }

    //save to local storage
    if(typeof(Storage) !== 'undefined') {
        localStorage.setItem('lastSearch', data);
    }

    return data;
}

function filterMusicians(){

    if ($('#main-filter-form').length === 0) {
        //no filters form
        return '';
    }

    if (page === 1) {
        $('.people-listing-grid').html('').addClass('loading-content');
    }

    if (filterRunning) {
        return false;
    }

    if (_user.temporaryLocation === '1'){
        return false;
    }

    filterRunning = $.ajax({
        url: Routing.generate('musicians_find'),
        data: getFilterData() + '&page='+page
    }).done(function( result ) {
        filterRunning = false;
        if (result.status == 'success') {
            filterResults = filterResults.concat(result.data);
            if (result.data.length !== 0) {

                if (result.finalResults === false) {
                    //load another page if the results are
                    page ++;
                    loadMoreResults = true;

                    if (page == 2) {
                        //load second page just in case
                        filterMusicians();
                    }
                }else {
                    loadMoreResults = false;
                }

            } else {
                loadMoreResults = false;
            }
            renderGridView(result.data);
        }

        if (result.alreadySubscribed === true && page === 1) {
            $('.people-listing-grid').html('<br /><p>You have already subscribed to this search criteria and will be notified when someone join. </p>');
        }
    });
}

function mapFilterMusicians() {
    var data = getFilterData();
    data += '&limit=0';

    $.ajax({
        url: Routing.generate('musicians_find'),
        data: data
    }).done(function( result ) {
        if (result.status == 'success') {
            filterResults = result.data;
            loadMoreResults = false;
            if (initializedMap !== false ){
                placeMarkers();
                drawRadius();
            }
        }
    });
}

function filterShouts() {
    if (shoutsPage === 1) {
        $('.shouts-listing').html('');
    }

    $.ajax({
        url: Routing.generate('shouts_find'),
        data: getFilterData() + '&page='+shoutsPage
    }).done(function( result ) {
        if (result.status == 'success'){
            if (result.data.length !== 0) {
                shoutsPage ++;
                loadMoreShoutsResults = true;
            } else {
                loadMoreShoutsResults = false;
            }
            $.each(result.data, function(k, v){
                $( '.shouts-listing' ).append(window.JST.shoutBoxTemplate( v ) );
            });
        }
    });
}

$(function() {

    if(typeof(Storage) !== 'undefined') {
        // Code for localStorage/sessionStorage.
        if (localStorage.distance) {
            $('#search_form_distance').val(localStorage.distance);
        }
    }

    $('#map-view').on('click', function(){
        delay(function(){
            if (initializedMap === false ){
                $('#map').height($('.view-tab-container').height() - 10);
                initializedMap = initMap();
                setMyFilterMarker();
                mapFilterMusicians();
            } else {
                $('.view-tab-container').scrollTop(0);
            }
        }, 500);
    });

    $('#grid-view').on('click', function(){
        $('.people-listing-grid').html('').addClass('loading-content');
        renderGridView(filterResults);
    });

    $('#main-filter-form').on('change', function() {
        //reset page to 1st page
        page = 1;
        filterResults = [];
        loadMoreResults = true;
        delay(function(){
            if ($('#map').is(':visible')) {
                mapFilterMusicians();
            } else {
                filterMusicians();
                filterShouts();
                $('.view-tab-container').scrollTop(0).perfectScrollbar('update');
                $('.shouts-listing').scrollTop(0).perfectScrollbar('update');
            }

        }, 500);
    });

    $.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
        //parse genres


        $.when(
            $.ajax({
                url: Routing.generate('api_genres')
            }).done(function( result ) {
                $('.filter-genres').select2({
                    data: result,
                    placeholder: $('.filter-genres').attr('placeholder'),
                    multiple: true,
                    matcher: oldMatcher(matchStart)
                });

                var genres = getParameterByName('genres');

                if (genres) {
                    $('.filter-genres').select2('trigger', 'select', {
                        data: { id: genres }
                    });
                }
            }),

            //parse instruments
            $.ajax({
                url: Routing.generate('api_instruments')
            }).done(function( result ) {
                $('.filter-instruments').select2({
                    data: result,
                    placeholder: $('.filter-instruments').attr('placeholder'),
                    multiple: true,
                    matcher: oldMatcher(matchStart)
                });

                var instruments = getParameterByName('instruments');

                if (instruments) {
                    $('.filter-instruments').select2('trigger', 'select', {
                        data: { id: instruments }
                    });
                }
            })

        ).then(function() {
            if (_user.lat === '' || _user.lng === '' || _user.temporaryLocation == '1') {
                //if there are no coordinates set, try browser get position
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
                            window.location.reload();
                        });
                    }
                });
            }else {
                if ($('.page-shouts ').length === 0) {
                    filterMusicians();
                }
                filterShouts();
            }
        });
    });

    //activate tabs
    tabsToggle($('.tabs-activate'));

    //activates tooltip
    $('[data-toggle=tooltip]').tooltip();

    $('body').on('click', '#subscribeToSearch', function(e){
        e.preventDefault();

        $.ajax({
            url: Routing.generate('subscribe_search_add')
        }).done(function( result ) {
            if (result.success === true) {
                $('.people-listing-grid .subscribe-info-search').html(window.JST.notificationTemplate({ type : 'success', message : 'Subscription to this search made successfully.', temp : 'temp' }));

                ga('send', {
                    hitType: 'event',
                    eventCategory: 'search',
                    eventAction: 'subscribed',
                    eventLabel: 'search'
                });
            }
        });
    });

    //activates slider
    $('#filter-by-distance-slider').slider({
        range: 'min',
        value: $('#search_form_distance').val(),
        min: 5,
        max: 100,
        step: 5,
        create: function() {
            var selection = $('#filter-by-distance-slider').slider('value');
            $('.slide-max').text(selection + 'km');
        },
        slide: function(event, ui) {
            $('.slide-max').text(ui.value + 'km');
            $('#search_form_distance').val(ui.value).trigger('change');
            $('#filter-by-distance-btn span').text(ui.value + 'km around you');

            if(typeof(Storage) !== 'undefined') {
                // Code for localStorage/sessionStorage.
                localStorage.setItem('distance', ui.value);
            }
        }
    });

    $('#filter-by-distance-btn span').text($('#search_form_distance').val() + $('#search_form_distance').data('text'));

    $('.view-tab-container').on('ps-y-reach-end', function () {
        console.log('end reached');
        if (loadMoreResults === true) {
            loadMoreResults = false;
            filterMusicians();
        }
    });

    $('.shouts-listing-filter').on('ps-y-reach-end', function () {
        console.log('shouts end reached');
        if (loadMoreShoutsResults === true) {
            loadMoreShoutsResults = false;
            filterShouts();
        }
    });


});