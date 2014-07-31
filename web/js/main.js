$(function () {

    $(".instrument-select").select2({
        placeholder: 'What do you play?'
    });

    $("#fos_user_profile_form_genres, #form_genres, #fos_user_registration_form_genres, #jam_genres").select2({
        placeholder: 'Whats music do you play?'
    });

    $("#fos_user_registration_form_brands, #fos_user_profile_form_brands").select2({
        placeholder: 'Favorite brands?'
    });

    $("#search_form_genres").select2({
        placeholder: 'Filter by genres'
    });

    $("#search_form_instruments").select2({
        placeholder: 'Filter by instruments'
    });

    $("#distance-slider").slider({
        range: "min",
        value: $("#search_form_distance").val(),
        step: 1,
        min: 1,
        max: 20,
        slide: function(event, ui) {
            $("#search_form_distance").val(ui.value).trigger('change');
        }
    });

    var $container = $('.profile-media-wall').isotope({
        // main isotope options
        itemSelector: '.profile-media-wall-item',
        layoutMode: 'fitRows',
        // options for cellsByRow layout mode
        // options for masonry layout mode
        masonry: {
            columnWidth: 150
        }
    });

    var jamMembersCollectionHolder = $("#jam_members")
    var musicianInstrumentsCollectionHolder = $("#musician_instruments")
    var videosCollectionHolder = $("#musician_videos")

    $("#add_another_image").click(function(e){
        e.preventDefault();
        addCollectionForm(imagesCollectionHolder, 'images');
    });

    $("#add_another_member").on('click', function(e){
        e.preventDefault();
        addCollectionForm(jamMembersCollectionHolder, 'members');
    });

    $("#add_another_instrument").on('click', function(e){
        e.preventDefault();
        addCollectionForm(musicianInstrumentsCollectionHolder, 'instruments');
    });

    $("#add_another_video").on('click', function(e){
        e.preventDefault();
        addCollectionForm(videosCollectionHolder, 'instruments');
    });

    $(".price-type").click(function(){
        $('.price-type').attr('checked', false);
        $(this).prop('checked', true).attr('checked', true);
        $("#ad_price").val('');
    });

    $("body").on('click', '.set-profile-photo', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        $.ajax({
            url: url
        }).done(function( result ) {
            if (result.status == 'success'){
                window.location.reload();
            }
        });

    });

    $("#addPhotosToggle").on('click', function(e){
        e.preventDefault();
        $(".profile-add-photos").fadeToggle();
        $(this).toggleClass('active');
        $(".profile-write-recommendation").hide();
        $("#addUserRecommendationToggle").removeClass('active');
    });

    $("#fos_user_profile_form_artists2" ).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: "http://developer.echonest.com/api/v4/artist/suggest",
                dataType: "jsonp",
                data: {
                    results: 12,
                    api_key: "AVZ7NYSNWRRUQVWXS",
                    format:"jsonp",
                    name:request.term
                },
                success: function( data ) {
                    response( $.map( data.response.artists, function(item) {
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
        select: function( event, ui ) {
            $("#log").empty();
            $("#log").append(ui.item ? ui.item.id + ' ' + ui.item.label : '(nothing)');
        }
    });

    $("#fos_user_profile_form_artists").select2({
        placeholder: "Favourite Artists?",
        minimumInputLength: 2,
        multiple: true,
        initSelection : function (element, callback) {
            var data = [];
            $(element.val().split(",")).each(function () {
                data.push({id: this, text: this});
            });
            callback(data);
        },
        ajax: {
            url: "http://developer.echonest.com/api/v4/artist/suggest",
            dataType: "jsonp",
            results: function (data) {
                //console.log(data);
                return {results: $.map( data.response.artists, function(item) {
                    return {
                        text: item.name,
                        value: item.name,
                        id: item.name
                    }
                })}
            },
            data: function (term, page) {
                return {
                    results: 12,
                    api_key: "AVZ7NYSNWRRUQVWXS",
                    format:"jsonp",
                    name: term
                };
            }
        }
    });

    $('#list-tab-btn').on('shown.bs.tab', function (e) {
        $(".filter-container").prependTo("#list");
        $('.tabs .active').removeClass('active');
        $(this).addClass('active');
        $(".filter-container").parents('.tab-content').removeClass('full-screen');
    });

    $("#map-tab-btn").on('shown.bs.tab', function (e) {
        $(".filter-container").appendTo(".map-view-container");
        $('.tabs .active').removeClass('active');
        $(this).addClass('active');
        $(".filter-container").parents('.tab-content').addClass('full-screen');
    });

    // store the currently selected tab in the hash value
    $("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
        var id = $(e.target).attr("href").substr(1);
        window.location.hash = id;
        if (id=='media'){
            $('.profile-media-wall').isotope( 'reloadItems' ).isotope();
        }
    });

    $('#messageModal').on('show.bs.modal', function (e) {
        socket.emit('getOurConversation', { userID:  $("#messageModal").data('id')});
    });

    _.templateSettings.variable = "rc";
    var messageTemplate      = _.template($( "#messageTemplate" ).html());

    socket.on('ourConversation', function (data) {
        $( "#messageModal .conversation-message-box").html('');
        $.each(data, function( index, value ) {
            $( "#messageModal .conversation-message-box" ).append(messageTemplate( value ) );
        });

        $( "#messageModal .conversation-message-box p").fadeIn();
        setTimeout(function(){scrollToBottom()},300);
    });

    socket.on('messageSaved', function (data) {
        var mess = $(messageTemplate( data )).show();
        $( ".conversation-message-box" ).append(mess);
        scrollToBottom();
    });

    socket.on('messageReceived', function (data) {
        var mess = $(messageTemplate( data )).show();
        $( ".conversation-message-box" ).append(mess);
        scrollToBottom();
    });

    // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    $('ul.nav-tabs a[href="' + hash + '"]').tab('show');

    setTimeout(function(){
        $(".flash-message.alert, .flash-message.success").fadeOut();
    },3000);

    $("#addUserRecommendationToggle").on('click', function(){
        $(this).toggleClass('active');
        $(".profile-write-recommendation").fadeToggle();
        $(".profile-add-photos").hide();
        $("#addPhotosToggle").removeClass('active');
    });

    socket.on('myUnreadMessagesCount', function(data){
        if (data!=0){
            $("#messages-nav .badge").text(data);
        }else{
            $("#messages-nav .badge").text('');
        }
    });

    $(".send-message").on('keyup', function(e){
        if(e.which == 13) {
            var self = $(this);
            var value = $(this).val();
            var toID = $(this).data('toid');
            var toUsername = $(this).data('tousername');

            if ($.trim(value)=='') return false;

            socket.emit('newMessage', {
                message: value,
                to: {
                    id: toID,
                    username: toUsername
                }
            });

            self.val('');
        }
    });

});

function addCollectionForm(collectionHolder, type) {
    var prototype = collectionHolder.data('prototype');
    var index = collectionHolder.data('index');
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    collectionHolder.data('index', index + 1);
    collectionHolder.append(newForm);
}

function readURL(input) {
    if (input.files && input.files[0]) {
        if(input.files[0].size>3000000){
            alert('Photo is too big. Please upload a file that is less than 3MB');
            $(input).val(null);
            return false;
        }
        var image  = new Image();
        var reader = new FileReader();
        reader.readAsDataURL(input.files[0]);
        reader.onload = function (e) {
            //parent parent is stupid

            image.src    = e.target.result;
            image.onload = function() {
                var w = this.width,
                    h = this.height;

                if(w<320 || h<190){
                    alert('Please choose a picture larger than 320x190');
                    $(input).val('');
                    return false;
                }

                var image_holder = $(input).parents('.image-holder');
                image_holder.append('<img src="" width="200" />');
                image_holder.find('img').attr('src', e.target.result);
                image_holder.find('.make-primary-image').parent().show();
                image_holder.find('.remove-image').show();
                image_holder.find('.upload').hide();
                if($('.image-preview').length==1){
                    image_holder.find('.make-primary-image').prop('checked', true).attr('checked', true);
                }
            };
        }
    }
}

function scrollToBottom(){
    var wtf    = $('.conversation-message-box');
    var height = wtf[0].scrollHeight;
    wtf.scrollTop(height);
}