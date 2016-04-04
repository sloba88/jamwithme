'use strict';

/* global Routing */
/* global parseYTVideoImages */
/* global addMessage */
/* global scrollbarPlugin */

var allInstruments = false;
var allSkills = false;

function setSelect2() {

    $('input.instrument-select').each(function(){
        var self = $(this);
        if (self.val()){
            var selected = $.grep(allInstruments, function(e){ return e.id == self.val(); });
            selected[0].disabled = true;
        }
    });

    $('.instrument-select').select2({
        placeholder: 'What do you play?',
        data: allInstruments
    }).on('change', function(t) {
        var selected = $.grep(allInstruments, function(e){ return e.id == t.val; });
        selected[0].disabled = true;

        if (t.removed) {
            var removed = $.grep(allInstruments, function (e) {
                return e.id == t.removed.id;
            });
            removed[0].disabled = false;
        }

        $('.instrument-select').select2({
            placeholder: 'What do you play?',
            data: allInstruments
        });
    });
}

function initInstrumentSelection() {

    if (allInstruments) {
        setSelect2();
    } else {
        $.ajax({
            url: Routing.generate('api_instruments')
        }).done(function(data) {
            allInstruments = data;
            setSelect2();
        });
    }

    if (allSkills) {
        $('.skill-select').select2({
            placeholder: 'How good are you?',
            data: allSkills
        });
    } else {
        $.ajax({
            url: Routing.generate('api_instruments_skills')
        }).done(function(data) {
            allSkills = data;

            $('.skill-select').select2({
                placeholder: 'How good are you?',
                data: allSkills
            });
        });
    }
}

function renameCollectionNames($selection) {
    $selection.each(function(k){
        $(this).find('input[type=hidden]').each(function(){
            var name = $(this).attr('name');
            name = name.replace(/(\d+)/g, k);
            $(this).attr('name', name);
        });
    });
}

function setTabsHeight() {
    if ($(window).width() > 992) {
        $('.page-settings .view-tab.with-scrollbar').height($(window).height() - 250);
    }
    return true;
}

$(function() {

    var $musiciansInstruments = $('#musician_instruments');
    var $musiciansVideos = $('#musician_videos');

    if ($musiciansInstruments.length > 0){
        if ($musiciansInstruments.find('.row').length === 0){
            //there are no instruments in settings, add some
            $musiciansInstruments.append(window.JST.instrumentBoxTemplate({'num': 0}));
        }

        initInstrumentSelection();
    }

    $('#add_another_instrument').on('click', function(e) {
        e.preventDefault();
        var length = $musiciansInstruments.find('.row').length;
        $musiciansInstruments.append(window.JST.instrumentBoxTemplate({'num': length}));

        initInstrumentSelection();
        scrollbarPlugin();
    });

    $musiciansInstruments.sortable({
        handle: '.handle',
        update: function() {
            renameCollectionNames($musiciansInstruments.find('.row'));
        }
    });

    $musiciansInstruments.on('click', '.remove-instrument', function(e){
        e.preventDefault();
        $(this).closest('.row').remove();
        scrollbarPlugin();
        renameCollectionNames($musiciansInstruments.find('.row'));
    });

    $musiciansVideos.on('click', '.save-video', function(){
        var url = $(this).closest('li').find('.youtube-url').val();
        var self = $(this);
        $.ajax({
            method: 'POST',
            url: Routing.generate('video_create'),
            data: { 'url' : url }
        }).done(function(data) {
            if (data.status == 'success') {
                self.closest('li').remove();

                $('#musician_videos').append(window.JST.videoBoxTemplate({'id' : data.id, 'url': data.url }));
                parseYTVideoImages();
                addMessage(data.status, data.message);
                scrollbarPlugin();
            }
        });
    });

    $(document).on('click', '.remove-video', function(e){
        e.preventDefault();
        var self = $(this);
        var id = $(this).closest('li').data('id');
        $.ajax({
           url: Routing.generate('video_remove', {'id': id})
        }).done(function(data){
            if (data.status == 'success') {
                self.closest('li').remove();
                addMessage(data.status, data.message);
            }
        });
    });

    $('#fos_user_profile_form_gear').select2({
        placeholder: 'Enter model',
        minimumInputLength: 2,
        multiple: true,
        quietMillis: 250,
        initSelection : function (element, callback) {
            var data = [];
            $(element.val().split(',')).each(function () {
                data.push({id: this, text: this});
            });
            callback(data);
        },
        createSearchChoice: function(term, data) { if ($(data).filter(function() { return this.text.localeCompare(term)===0; }).length===0) {return {id:term, text:term};} },
        ajax: {
            url: Routing.generate('api_gear'),
            data: function (term) {
                return {
                    q: term // search term
                };
            },
            results: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.name,
                            value: item.name,
                            id: item.name
                        };
                    })
                };

            },
            cache: true
        }
    });

    $('#fos_user_profile_form_gear').select2('container').find('ul.select2-choices').sortable({
        containment: 'parent',
        start: function() { $('#fos_user_profile_form_gear').select2('onSortStart'); },
        update: function() { $('#fos_user_profile_form_gear').select2('onSortEnd'); }
    });

    if ($('#fos_user_profile_form_genres').length > 0){

        $.ajax({
            url: Routing.generate('api_genres')
        }).done(function(data) {
            $('#fos_user_profile_form_genres').select2({
                placeholder: 'Favourite Genres?',
                multiple: true,
                data: data,
                matcher: function(term, text) { return text.toUpperCase().indexOf(term.toUpperCase())===0; }
            });
        });
    }

    $('#fos_user_profile_form_genres').select2('container').find('ul.select2-choices').sortable({
        containment: 'parent',
        start: function() { $('#fos_user_profile_form_genres').select2('onSortStart'); },
        update: function() { $('#fos_user_profile_form_genres').select2('onSortEnd'); }
    });

    $('#fos_user_profile_form_artists').select2({
        placeholder: 'Favourite Artists?',
        minimumInputLength: 2,
        multiple: true,
        initSelection: function(element, callback) {
            var data = [];
            $(element.val().split(',')).each(function() {
                data.push({
                    id: this,
                    text: this
                });
            });
            callback(data);
        },
        ajax: {
            url: 'http://developer.echonest.com/api/v4/artist/suggest',
            dataType: 'jsonp',
            results: function(data) {
                return {
                    results: $.map(data.response.artists, function(item) {
                        return {
                            text: item.name,
                            value: item.name,
                            id: item.name
                        };
                    })
                };
            },
            data: function(term) {
                return {
                    results: 12,
                    api_key: 'AVZ7NYSNWRRUQVWXS',
                    format: 'jsonp',
                    name: term
                };
            }
        }
    });

    $('#fos_user_profile_form_artists').select2('container').find('ul.select2-choices').sortable({
        containment: 'parent',
        start: function() { $('#fos_user_profile_form_artists').select2('onSortStart'); },
        update: function() { $('#fos_user_profile_form_artists').select2('onSortEnd'); }
    });

    if ($('#musician_videos').length > 0){
        $('#add_another_video').on('click', function(e) {
            e.preventDefault();
            if ($('.add-video-box').length === 0) {
                $('#musician_videos').prepend(window.JST.videoAddBoxTemplate());
            }
        });
    }

    $('body').on('change', '#fos_user_profile_form_isTeacher', function(){
        if ($(this).is(':checked')){
            $('.teacherSpecific').fadeIn();
        } else {
            $('.teacherSpecific').fadeOut();
        }
    });

    $('#fos_user_profile_form_firstName, #fos_user_profile_form_lastName').on('keyup', function(){
        var start = this.selectionStart,
            end = this.selectionEnd,
            str = $(this).val();
        str = str.replace(/ +(?= )/g,'').replace(/[0-9]/g, '');
        $(this).val(str);

        this.setSelectionRange(start, end);
    });

    $('#fos_user_profile_form_username').on('keyup', function(){
        var start = this.selectionStart,
            end = this.selectionEnd,
            str = $(this).val();
        str = str.replace(/[^A-Za-z0-9 ]/g, '');
        $(this).val(str);

        this.setSelectionRange(start, end);
    });

    $(window).on('hashchange', function() {
        $('#settings-current-hash').val(window.location.hash);
    });

    $('#settings-current-hash').val(window.location.hash);

    $('#next-1').on('click', function(e){
        e.preventDefault();
        $('#location-tab').click();
    });

    $('#location-tab').on('click', function(){
        $('#next-1').addClass('hidden');
        $('#finish-1').removeClass('hidden');
    });

    $('#musician-info-tab').on('click', function(){
        $('#next-1').removeClass('hidden');
        $('#finish-1').addClass('hidden');
    });

    setTabsHeight();
});