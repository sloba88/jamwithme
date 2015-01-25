$(function () {

    $(".instrument-select").select2({
        placeholder: 'What do you play?'
    });

    $('select').select2();

    //scrollbar plugin
    if ($('.view-tab-container').length && $('.shouts-listing').length) {
        $('.view-tab-container, .shouts-listing').perfectScrollbar({
            suppressScrollX: true
        });
    }

    $('input[type=checkbox]').next('label').prepend('<span></span>');

    //activates tooltip
    $("[data-toggle=tooltip]").tooltip();

    //activates slider
    $("#filter-by-distance-slider").slider({
        range: "min",
        value: $("#search_form_distance").val(),
        min: 0,
        max: 20,
        step: 2,
        create: function(event, ui) {
            var selection = $("#filter-by-distance-slider").slider("value");
            $('.slide-max').text(selection + 'km');
        },
        slide: function(event, ui) {
            $('.slide-max').text(ui.value + 'km');
            $("#search_form_distance").val(ui.value).trigger('change');
        }
    });

    profileCompletion();

    filtersToggle();

    conversations();

    conversationHeight();

    sidebarHeight();

    peopleGrid();

    $(window).resize(function() {
        conversationHeight();
        sidebarHeight();
    });

    //activate tabs
    tabsToggle($('.tabs-activate'));

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

    var $container = $('.profile-media-wall').isotope({
        // main isotope options
        itemSelector: '.profile-media-wall-item',
        layoutMode: 'masonry',
        // options for cellsByRow layout mode
        // options for masonry layout mode
        masonry: {
            columnWidth: 140,
            gutter: 10
        }
    });

    $container.imagesLoaded( function() {
        $container.isotope('layout');
    });

    $("a.my-stuff").on("shown.tab", function (e) {
        $('.profile-media-wall').isotope( 'reloadItems' ).isotope();
    });

    var jamMembersCollectionHolder = $("#jam_members");
    var musicianInstrumentsCollectionHolder = $("#musician_instruments");
    var videosCollectionHolder = $("#musician_videos");

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

    if (jQuery().fancybox) {
        $(".fancybox").fancybox();
    }

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
        $('.filter-container').prependTo("#list");
        $('.tabs .active').removeClass('active');
        $(this).addClass('active');
        $('.filter-container').parents('.tab-content').removeClass('full-screen');
        localStorage.setItem("view", "list");
    });

    $("#map-tab-btn").on('shown.bs.tab', function (e) {
        $(".filter-container").appendTo(".map-view-container");
        $('.tabs .active').removeClass('active');
        $(this).addClass('active');
        $(".filter-container").parents('.tab-content').addClass('full-screen');
        localStorage.setItem("view", "map");
    });

    // store the currently selected tab in the hash value
    $("ul.tabs-activate > li > a").on("shown.tab", function (e) {
        var id = $(e.target).attr("href").substr(1);
        window.location.hash = id;
        if (id=='media'){
            $('.profile-media-wall').isotope( 'reloadItems' ).isotope();
        }
    });

    _.templateSettings.variable = "rc";
    var messageTemplate      = _.template($( "#messageTemplate" ).html());

    socket.on('ourConversation', function (data) {
        $(".conversation-message-box").html('');
        $.each(data, function( index, value ) {
            $(".conversation-message-box").append(messageTemplate(value));
        });
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
    $('ul.tabs-activate a[href="' + hash + '"]').trigger('click');

    setTimeout(function(){
        $(".flash-message.alert, .flash-message.success").fadeOut();
    },3000);

    $("#addUserRecommendationToggle").on('click', function(){
        $(this).toggleClass('active');
        $(".profile-write-recommendation").fadeToggle();
        $(".profile-add-photos").hide();
        $("#addPhotosToggle").removeClass('active');
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

    $('.ytvideo').each(function(){
        var src = $(this).attr('href');
        var ytId = youtubeParser(src);
        var ytImg = 'http://img.youtube.com/vi/'+ytId+'/0.jpg';
        $(this).find('img').attr('src', ytImg);
    });

    $('.ytvideo').on('click', function() {
        $.fancybox({
            'padding'		: 0,
            'autoScale'		: false,
            'transitionIn'	: 'none',
            'transitionOut'	: 'none',
            'width'			: 640,
            'height'		: 385,
            'href'			: this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
            'type'			: 'swf',
            'swf'			: {
                'wmode'				: 'transparent',
                'allowfullscreen'	: 'true'
            }
        });

        return false;
    });
});

socket.on('myUnreadMessagesCount', function(data){
    if (data!=0){
        $(".inbox .badge").text(data);
    }else{
        $(".inbox .badge").text('');
    }
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

function profileCompletion() {
    var numberText = $('.profile-completion-text span').text(),
        number = numberText.slice(0, -1);

    var $pickInner = $('.pick-inner');

    $pickInner.height(number * 0.5);
}

function filtersToggle() {
    var $filtersBy = $('.filters-by'),
        $filtersHide = $('.filters-hide'),
        $filtersHideText = $filtersHide.children('.text');

    $filtersBy.on('hidden.bs.collapse', function() {
        $filtersHide.removeClass('dropup');
        $filtersHideText.text('Show filters');
    });

    $filtersBy.on('shown.bs.collapse', function() {
        $filtersHide.addClass('dropup');
        $filtersHideText.text('Hide filters');
    });
}

function peopleGrid() {
    var $peopleGrid = $('.people-listing-grid');

    $peopleGrid.on('mouseenter', '.people-grid', function() {
        var $this = $(this),
            $tags = $this.find('.tags'),
            $compatibilityBox = $this.find('.compatibility-box');

        $this.addClass('hovered');

        setTimeout(function() {
            if ($this.hasClass('hovered')) {
                $tags.fadeIn(300);
                $compatibilityBox.removeClass('hide').fadeIn(100);
            }
        }, 200)

    });

    $peopleGrid.on('mouseleave', '.people-grid', function() {
        var $this = $(this),
            $tags = $this.find('.tags'),
            $compatibilityBox = $this.find('.compatibility-box');

        $this.removeClass('hovered');

        $tags.hide();
        $compatibilityBox.addClass('hide').hide();
    });
}

function tabsToggle(object) {
    var $btns = object.find('a'); //all buttons
    $viewTabContainer = $('.view-tab-container');//tabs container
    $viewTab = $viewTabContainer.find('.view-tab'); //all tabs

    var speed = 100;

    //add active classes on load
    // $btns.eq(0).addClass('is-active');
    // $viewTabContainer.children(':first-child()').addClass('is-active');

    $btns.on('click', function(e) {
        e.preventDefault();

        var $thisBtn = $(this), //this button
            btnDataTab = $thisBtn.data('tab'); //this button data

        if (!$thisBtn.hasClass('is-active')) {
            $btns.removeClass('is-active');
            $thisBtn.addClass('is-active');

            $viewTab.each(function() {
                var $thisTab = $(this); //this tab
                if ($thisTab.data('tab') == btnDataTab) {
                    $viewTab.fadeOut(speed).removeClass('is-active');
                    setTimeout(function(){
                        $thisTab.fadeIn(speed).addClass('is-active');
                        $thisBtn.trigger('shown.tab');
                    }, speed)
                }
            });

        } else {
            return false;
        }
    });
}

function sidebarHeight() {
    var windowHeight = $(window).height(),
        $mainContentInner = $('.main-content-inner'),
        $viewTabContainer = $mainContentInner.children('.view-tab-container'),
        eventDivHeight = $mainContentInner.children('.event').height(),
        filtersAreaHeight = $mainContentInner.children('.filters-area').height();

    $('.sidebar-inner').height(windowHeight - 70);

    $mainContentInner.height(windowHeight - 100);

    $('.shouts-listing').height($('.sidebar-inner').height() - 374);

    $viewTabContainer.height($mainContentInner.height() - eventDivHeight - filtersAreaHeight - 39);
}

function conversations() {
    var $conversation = $('.conversation'),
        $conversationContainer = $conversation.find('.conversation-container'),
        $compose = $('.messages-header').find('.btn-compose'),
        $overlay = $('.overlay');

    //open
    $('.messages-container').on('click', '.message-single', function() {
        $conversation.removeClass('is-opened-compose');
        $conversation.addClass('is-opened');
        $overlay.removeClass('hide');

        $(".conversation-message-box .conversation-single").hide();
        var user = $(this).data('user');
        var userID = $(this).data('id');
        $('*[data-user="'+user+'"]').show();
        $('*[data-user2="'+user+'"]').show();
        $(".send-message").data('toid', userID);
        $(".send-message").data('tousername', user);

        scrollToBottom();

        setTimeout(function(){
            socket.emit('conversationIsRead', { userID: userID });
        },500);

    });

    $('.open-conversation').on('click', function(e){
        e.preventDefault();

        socket.emit('getOurConversation', { userID:  $(this).data('id')});

        $conversation.removeClass('is-opened-compose');
        $conversation.addClass('is-opened');
        $overlay.removeClass('hide');

        $(".conversation-message-box .conversation-single").hide();
        var user = $(this).data('user');
        var userID = $(this).data('id');
        $('*[data-user="'+user+'"]').show();
        $('*[data-user2="'+user+'"]').show();
        $(".send-message").data('toid', userID);
        $(".send-message").data('tousername', user);

        scrollToBottom();

        setTimeout(function(){
            socket.emit('conversationIsRead', { userID: userID });
        },500);

    });

    //compose
    $compose.on('click', function(e){
        $conversation.addClass('is-opened is-opened-compose');
        $overlay.removeClass('hide');
    });

    //close
    $('.conversation-close').on('click', '.close-link', function(e) {
        e.preventDefault();

        $conversation.removeClass('is-opened');
        $overlay.addClass('hide');
    });

}

function conversationHeight() {
    var $conversation = $('.conversation'),
        $conversationContainer = $conversation.find('.conversation-container'),
        conversationHeight = $conversation.height(),
        coneversationCloseHeight = $('.conversation-close').height(),
        coneversationSendHeight = $('.conversation-send').height();

    $conversationContainer.height(conversationHeight - coneversationCloseHeight - coneversationSendHeight - 30);
}

function youtubeParser(url){
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match = url.match(regExp);
    if (match&&match[7].length==11){
        return match[7];
    }else{

    }
}