$(function() {

    if ($('#musician_instruments').length > 0){
        var instrumentTemplate = _.template($('#instrumentBoxTemplate').html());

        if ($('#musician_instruments .row').length == 0){
            //there are no instruments in settings, add some
            $('#musician_instruments').append(instrumentTemplate({'num': 0}));
        }

        initInstrumentSelection();
    }

    $('#add_another_instrument').on('click', function(e) {
        e.preventDefault();
        var length = $('#musician_instruments .row').length;
        $('#musician_instruments').append(instrumentTemplate({'num': length}));

        initInstrumentSelection();
    });

    $('#musician_instruments').sortable({
        handle: '.handle',
        update: function( event, ui ) {
            renameCollectionNames($('#musician_instruments .row'));
        }
    });

    $('#musician_instruments').on('click', '.remove-instrument', function(e){
        e.preventDefault();
        $(this).closest('.row').remove();
        renameCollectionNames($('#musician_instruments .row'));
    });

    $('#musician_videos').on('click', '.save-video', function(){
        var url = $(this).closest('li').find('.youtube-url').val();
        var self = $(this);
        $.ajax({
            method: "POST",
            url: Routing.generate('video_create'),
            data: { 'url' : url }
        }).done(function(data) {
            if (data.status == 'success') {
                self.closest('li').remove();

                var videoTemplate = _.template($('#videoBoxTemplate').html());
                $('#musician_videos').append(videoTemplate({'id' : data.id, 'url': data.url }));
                parseYTVideoImages();

                addMessage(data.status, data.message);
            }
        });
    });

    $('#musician_videos').on('click', '.remove-video', function(e){
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

    $('#fos_user_profile_form_artists2').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: 'http://developer.echonest.com/api/v4/artist/suggest',
                dataType: 'jsonp',
                data: {
                    results: 12,
                    api_key: 'AVZ7NYSNWRRUQVWXS',
                    format: 'jsonp',
                    name: request.term
                },
                success: function(data) {
                    response($.map(data.response.artists, function(item) {
                        return {
                            label: item.name,
                            value: item.name,
                            id: item.id
                        }
                    }));
                }
            });
        },
        minLength: 3,
        select: function(event, ui) {
            $('#log').empty();
            $('#log').append(ui.item ? ui.item.id + ' ' + ui.item.label : '(nothing)');
        }
    });

    $('#fos_user_profile_form_brands').select2({
        placeholder: 'Favourite Brands?',
        minimumInputLength: 2,
        multiple: true,
        quietMillis: 250,
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
            url: Routing.generate('api_brands'),
            data: function (term, page) {
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
                        }
                    })
                }

            },
            cache: true
        }
    });

    $('#fos_user_profile_form_brands').select2('container').find('ul.select2-choices').sortable({
        containment: 'parent',
        start: function() { $('#fos_user_profile_form_brands').select2('onSortStart'); },
        update: function() { $('#fos_user_profile_form_brands').select2('onSortEnd'); }
    });

    if ($('#fos_user_profile_form_genres').length > 0){
        $('#fos_user_profile_form_genres').select2({
            placeholder: 'Favourite Genres?',
            multiple: true,
            data: genresNames
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
                        }
                    })
                }
            },
            data: function(term, page) {
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
            var videoTemplate = _.template($('#videoAddBoxTemplate').html());
            $('#musician_videos').append(videoTemplate());
        });
    }

});

function initInstrumentSelection(){
    $('.instrument-select').select2({
        placeholder: 'What do you play?',
        data: instrumentNames
    });

    $('.skill-select').select2({
        placeholder: 'How good are you?',
        data: instrumentSkills
    });
}

function renameCollectionNames($selection) {
    $selection.each(function(k, v){
        $(this).find('input[type=hidden]').each(function(){
            var name = $(this).attr('name');
            name = name.replace(/(\d+)/g, k);
            $(this).attr('name', name);
        });
    });
}