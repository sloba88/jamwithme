'use strict';

/* global Routing */
/* global parseYTVideoImages */
/* global addMessage */
/* global scrollbarPlugin */
/* global _user */
/* global matchStart */
/* global oldMatcher */
/* global spotifyToken */

var allInstruments = false;
var allSkills = false;

function formatResultData (data) {
    if (!data.id) {
        return data.text;
    }
    if (data.element.selected) {
        return;
    }
    return data.text;
}

function setSelect2() {

    $('input.instrument-select').each(function(){
        var self = $(this);
        if (self.val()){
            var selected = $.grep(allInstruments, function(e){ return e.id == self.val(); });
            selected[0].disabled = true;
        }
    });

    $('.instrument-select').select2({
        data: allInstruments,
        matcher: oldMatcher(matchStart)
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
            data: allInstruments,
            matcher: oldMatcher(matchStart)
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
            data: allSkills
        });
    } else {
        $.ajax({
            url: Routing.generate('api_instruments_skills')
        }).done(function(data) {
            allSkills = data;

            $('.skill-select').select2({
                data: allSkills
            });
        });
    }
}

function initiateMemberInstrumentSelection() {
    if (allInstruments) {
        $('.member-instrument').select2({
            data: allInstruments
        });
    } else {
        $.ajax({
            url: Routing.generate('api_instruments')
        }).done(function(data) {
            allInstruments = data;
            $('.member-instrument').select2({
                data: allInstruments
            });
        });
    }
}

function renameCollectionNames($selection) {
    $selection.each(function(k){
        $(this).find('input[type=hidden], input[type=checkbox]').each(function(){
            var name = $(this).attr('name');
            name = name.replace(/(\d+)/g, k);
            $(this).attr('name', name);
        });
    });
}

function showLearnOptions() {
    //if the form element is changed or the form element doesnt exist as on setup page
    if ($('#fos_user_profile_form_isVisitor').prop('checked') || ($('#fos_user_profile_form_isVisitor').length === 0 && _user.isVisitor)) {
        $('.learn-options').removeClass('hidden');
    } else {
        $('.learn-options').addClass('hidden');
        $('.learn-options input:checked').each(function() {
            $(this).prop('checked', false);
        });
    }

    if ($('.learn-options').hasClass('hidden')) {
        $('.skill-level-select-form').removeClass('col-md-4 col-sm-4').addClass('col-md-5 col-sm-5');
        $('.instrument-select-form').removeClass('col-md-5 col-sm-5').addClass('col-md-6 col-sm-6');
    } else {
        $('.skill-level-select-form').removeClass('col-md-5 col-sm-5').addClass('col-md-4 col-sm-4');
        $('.instrument-select-form').removeClass('col-md-6 col-sm-6').addClass('col-md-5 col-sm-5');
    }
}

$(function() {

    var $musiciansInstruments = $('#musician_instruments');
    var $musiciansVideos = $('#musician_videos');
    var $soundcloudTracks = $('#soundcloud_tracks');

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
        showLearnOptions();
        scrollbarPlugin();
    });

    $musiciansInstruments.on('click', '.remove-instrument', function(e){
        e.preventDefault();
        $(this).closest('.row').remove();
        scrollbarPlugin();
        renameCollectionNames($musiciansInstruments.find('.row'));
    });

    var $jamMusicians = $('#jam_musician_instruments');

    $jamMusicians.on('click', '.remove-member', function(e){
        e.preventDefault();
        $(this).closest('.row').remove();
        scrollbarPlugin();
        renameCollectionNames($jamMusicians.find('.row'));
    });

    $('#add_jam_member').on('click', function(e) {
        e.preventDefault();
        var length = $jamMusicians.find('.row').length;
        $jamMusicians.append(window.JST.jamMusicianBoxTemplate({'num': length}));

        initiateMemberInstrumentSelection();

        var memberUserPlaceholder = $('#jam_members_0_musician').data('placeholder');

        $('.member-user').select2({
            placeholder: memberUserPlaceholder,
            minimumInputLength: 2,
            multiple: false,
            allowClear: true,
            createSearchChoice: function(term, data) { if ($(data).filter(function() { return this.text.localeCompare(term)===0; }).length===0) {return {id:term, text:term};} },
            ajax: {
                delay: 300,
                url: Routing.generate('users_find'),
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.username,
                                value: item.username,
                                id: item.id
                            };
                        })
                    };

                },
                cache: true
            }
        });

        scrollbarPlugin();
    });

    $('#invite_jam_member').on('click', function(e) {
        e.preventDefault();
        var $jamMusicians = $('#jam_musician_instruments');

        var length = $jamMusicians.find('.row').length;
        $jamMusicians.append(window.JST.jamMusicianInviteBoxTemplate({'num': length}));

        initiateMemberInstrumentSelection();
    });

    $musiciansVideos.on('click', '.save-video', function(){
        var url = $(this).closest('li').find('.youtube-url').val();
        var self = $(this);
        var jam = $('#add_another_video').data('jam');
        $.ajax({
            method: 'POST',
            url: Routing.generate('video_create'),
            data: { 'url' : url, 'jam' :  jam}
        }).done(function(data) {
            if (data.status == 'success') {
                self.closest('li').remove();

                $musiciansVideos.append(window.JST.videoBoxTemplate({'id' : data.id, 'url': data.url }));
                parseYTVideoImages();
                addMessage(data.status, data.message);
                scrollbarPlugin();
            }
        });
    });

    $soundcloudTracks.on('click', '.save-track', function(){
        var url = $(this).closest('li').find('.soundcloud-track-url').val();
        var self = $(this);
        $.ajax({
            method: 'POST',
            url: Routing.generate('soundcloud_track_create'),
            data: { 'url' : url}
        }).done(function(data) {
            if (data.status == 'success') {
                self.closest('li').remove();

                $soundcloudTracks.append(window.JST.soundcloudTrackBoxTemplate({'id' : data.id, 'url': data.url }));

                //SC.Widget('sc_track_'+data.id);

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

    $(document).on('click', '.remove-soundcloud-track', function(e){
        e.preventDefault();
        var self = $(this);
        var id = $(this).closest('li').data('id');
        $.ajax({
            url: Routing.generate('soundcloud_track_remove', {'id': id})
        }).done(function(data){
            if (data.status == 'success') {
                self.closest('li').remove();
                addMessage(data.status, data.message);
            }
        });
    });

    if ($('#fos_user_profile_form_gear').length > 0) {

        $('#fos_user_profile_form_gear').select2({
            minimumInputLength: 2,
            multiple: true,
            tags: true,
            createSearchChoice: function(term, data) { if ($(data).filter(function() { return this.text.localeCompare(term)===0; }).length===0) {return {id:term, text:term};} },
            ajax: {
                delay: 300,
                url: Routing.generate('api_gear'),
                data: function (term) {
                    return {
                        q: term.term // search term
                    };
                },
                processResults: function(data) {
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

        $('#fos_user_profile_form_gear ul.select2-choices').sortable({
            containment: 'parent',
            start: function() { $('#fos_user_profile_form_gear').select2('onSortStart'); },
            update: function() { $('#fos_user_profile_form_gear').select2('onSortEnd'); }
        });
    }

    if ($('#fos_user_profile_form_genres').length > 0 ) {
        $('#fos_user_profile_form_genres ul.select2-choices').sortable({
            containment: 'parent',
            start: function() { $('#fos_user_profile_form_genres').select2('onSortStart'); },
            update: function() { $('#fos_user_profile_form_genres').select2('onSortEnd'); }
        });
    }

    if ($('#fos_user_profile_form_artists').length > 0 ) {

        $('#fos_user_profile_form_artists').select2({
            minimumInputLength: 2,
            multiple: true,
            placeholder: $('#fos_user_profile_form_artists').attr('placeholder'),
            ajax: {
                delay: 200,
                url: 'https://api.spotify.com/v1/search',
                headers: {
                    'Authorization': 'Bearer ' + spotifyToken
                },
                data: function(term) {
                    return {
                        results: 12,
                        api_key: '821adc24f1684cf89b7ef538d8808b8a',
                        q: term.term,
                        type: 'artist'
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.artists.items, function(item) {
                            return {
                                text: item.name,
                                value: item.name,
                                id: item.name
                            };
                        })
                    };
                }
            }
        });

        $('#fos_user_profile_form_artists ul.select2-choices').sortable({
            containment: 'parent',
            start: function() { $('#fos_user_profile_form_artists').select2('onSortStart'); },
            update: function() { $('#fos_user_profile_form_artists').select2('onSortEnd'); }
        });
    }

    if ($('#musician_videos').length > 0){
        $('#add_another_video').on('click', function(e) {
            e.preventDefault();
            if ($('.add-video-box').length === 0) {
                $('#musician_videos').prepend(window.JST.videoAddBoxTemplate());
            } else {
                $('.youtube-url').focus();
            }
        });
    }

    if ($('#soundcloud_tracks').length > 0){
        $('#add_another_soundcloud_track').on('click', function(e) {
            e.preventDefault();
            if ($('.add-sound-box').length === 0) {
                $('#soundcloud_tracks').prepend(window.JST.soundcloudTrackAddBoxTemplate());
            } else {
                $('.soundcloud-track-url').focus();
            }
        });
    }

    $('body').on('change', '#fos_user_profile_form_isTeacher', function(){
        if ($(this).prop('checked')){
            $('.teacherSpecific').removeClass('hidden');
        } else {
            $('.teacherSpecific').addClass('hidden');
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

    $('#fos_user_profile_form_username').alphanum({
        allowSpace: false,
        allowNewline: false
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

    $('.user-type input').on('change', function() {
        var id = $(this).attr('id');

        if ($(this).prop('checked')) {
            $('span.'+id).removeClass('hidden');
        } else {
            $('span.'+id).addClass('hidden');
        }

        $('.what-do-you-label .separator').remove();

        $('.what-do-you-label span.what-label-option:visible').each(function(k) {
            if (k > 0) {
                $(this).prepend('<span class="separator">/ </span>');
            }
        });

        if ($('.user-type input:checked').length === 0) {
            $('.what-do-you-play-default').removeClass('hidden');
        } else {
            $('.what-do-you-play-default').addClass('hidden');
        }

        showLearnOptions();
    });

    if ($('#fos_user_profile_form_genres').length > 0){
        $.ajax({
            url: Routing.generate('api_genres')
        }).done(function(data) {
            $('#fos_user_profile_form_genres').select2({
                multiple: true,
                data: data,
                matcher: oldMatcher(matchStart)
            });
        });
    }

    if ($('#jam_instruments').length > 0){
        $('#jam_instruments').select2({
            multiple: true,
            templateResult: formatResultData,
            matcher: oldMatcher(matchStart),
            placeholder: $('#jam_instruments').attr('placeholder')
        }).on('select2:select', function (e) {
            $(this).prepend('<option value="'+e.params.data.text+'">' +e.params.data.text + '</option>');
        }).on('select2:unselect', function (e) {
            e.params.data.element.remove();
        });
    }

    if (typeof _user != 'undefined') {
        //todo: fix this poor hack
        showLearnOptions();
    }

});