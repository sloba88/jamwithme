_.templateSettings.variable = 'rc';
var shoutBoxTemplate = _.template($('#shoutBoxTemplate').html());
var notificationTemplate = _.template($('#notificationTemplate').html());
var musicianBoxTemplate = _.template($('#musicianBoxTemplate').html());
var musicianMapBoxTemplate = _.template($('#musicianMapBoxTemplate').html());
var messageTemplate = _.template($('#messageTemplate').html());

$(function() {

    //checks if touch device
    checkTouchDevice();

    $('select').select2();

    $('.info-popover').popover();

    //select plugin on dashboard updates height of main container on change
    $('select').on('change', sidebarHeight);

    $('#form_genres, #jam_genres').select2({
        placeholder: 'Whats music do you play?'
    });

    $('#search_form_genres').select2({
        placeholder: 'Filter by genres'
    });

    $('#search_form_instruments').select2({
        placeholder: 'Filter by instruments'
    });

    $('input[type=checkbox]').next('label').prepend('<span></span>');

    //activates tooltip
    $('[data-toggle=tooltip]').tooltip();

    profileCompletion();

    filtersToggle();

    conversations();

    conversationHeight();

    sidebarHeight();

    peopleGrid();

    //menu for mobile devices
    menu();

    if ($('.shouts-listing').length) {
        $('.shouts-listing').perfectScrollbar({
            suppressScrollX: true
        });
    }

    //scrollbar on settings page - photos
    $('.page-profile .profile-media-wall').perfectScrollbar({
        suppressScrollX: true
    });

    //window resize after delay
    var resizeTimer;
    $(window).resize(function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            conversationHeight();
            sidebarHeight();
            peopleGrid();

            //scrollbar plugin
            scrollbarPlugin();
        }, 50);
    });

    //search
    autocomplete();

    autocompleteMessageUser();

    $('.show-all-tags').on('click', showAllTags);

    $(window).resize(function() {
        conversationHeight();
        sidebarHeight();
    });

    var $container = $('.profile-media-wall').isotope({
        // main isotope options
        itemSelector: '.profile-media-wall-item',
        layoutMode: 'masonry',
        // options for cellsByRow layout mode
        // options for masonry layout mode
        masonry: {
            columnWidth: 120,
            gutter: 2
        }
    });

    //activate tabs
    tabsToggle($('.tabs-activate'));

    $container.imagesLoaded(function() {
        $container.isotope('layout');
        scrollbarPlugin();
    });

    $('.profile-tabs a').on('shown.tab', function(e) {
        var tab = $(this).data('tab');
        if (['media', 'photos'].indexOf(tab != -1 )){
            $('.profile-media-wall').isotope('layout');
        }
    });

    $('#add_another_image').click(function(e) {
        e.preventDefault();
        addCollectionForm(imagesCollectionHolder, 'images');
    });

    $('.price-type').click(function() {
        $('.price-type').attr('checked', false);
        $(this).prop('checked', true).attr('checked', true);
        $('#ad_price').val('');
    });

    if (jQuery().fancybox) {
        $('.fancybox').fancybox({
                'closeEffect': 'none',
                helpers : {
                    title: {
                        type: 'inside'
                    }
                },
                afterLoad: function() {
                    this.title = $(this.element).parent().find('.profile-media-image-commands').html();
                }
        });
    }

    $('#addPhotosToggle').on('click', function(e) {
        e.preventDefault();
        $('.profile-add-photos').fadeToggle();
        $(this).toggleClass('active');
        $('.profile-write-recommendation').hide();
        $('#addUserRecommendationToggle').removeClass('active');
    });

    setTimeout(function() {
        $('.flash-message.alert, .flash-message.success').fadeOut();
    }, 3000);

    $('#addUserRecommendationToggle').on('click', function() {
        $(this).toggleClass('active');
        $('.profile-write-recommendation').fadeToggle();
        $('.profile-add-photos').hide();
        $('#addPhotosToggle').removeClass('active');
    });

    parseYTVideoImages();

    $('body').on('click', '.ytvideo', function() {
        $.fancybox({
            'padding': 0,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'width': 640,
            'height': 385,
            'href': this.href.replace(new RegExp('watch\\?v=', 'i'), 'v/'),
            'type': 'swf',
            'swf': {
                'wmode': 'transparent',
                'allowfullscreen': 'true'
            }
        });

        return false;
    });

    $(document).on('click', '#shoutSend', function(e){
        e.preventDefault();
        var self = $(this);
        var val = $('#shoutForm #form_text').val();

        if (val.trim() === ''){
            addMessage(false, 'Shout can\'t be empty, don\'t be shy.');
            return false;
        }

        $.ajax({
            url: Routing.generate('create_shout'),
            data: $('#shoutForm').serialize(),
            type: 'POST',
            success: function(result) {
                addMessage(result.status, result.message);
                if (result.status === 'success'){
                    $.each(result.data, function(k, v){
                        $( '.shouts-listing' ).prepend(shoutBoxTemplate( v ) );
                    });
                    checkCanShout();
                    $('#form_text').val('');
                }
            },
            error: function(result) {
                addMessage(result.status, result.message);
            }
        });
    });

    $(document).on('click', '.remove-shout', function(e){
        var element = $(e.currentTarget);
        $.ajax({
            url: Routing.generate('remove_shout', {id: $(e.currentTarget).attr('id')}),
            type: 'DELETE',
            success: function(result) {
                if (result.status === 'success') {
                    element.parents('.shout-box').remove();
                    checkCanShout();
                    addMessage(result.status, result.message);
                }
            }
        });
    });

    //scrollbar plugin
    scrollbarPlugin();
});

function checkTouchDevice() {
    var is_touch_device = 'ontouchstart' in document.documentElement;
    if (is_touch_device) {
        $('html').addClass('touch-device');
    } else {
        $('html').addClass('no-touch-device');
    }
}

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
        if (input.files[0].size > 3000000) {
            alert('Photo is too big. Please upload a file that is less than 3MB');
            $(input).val(null);
            return false;
        }
        var image = new Image();
        var reader = new FileReader();
        reader.readAsDataURL(input.files[0]);
        reader.onload = function(e) {
            //parent parent is stupid

            image.src = e.target.result;
            image.onload = function() {
                var w = this.width,
                h = this.height;

                if (w < 320 || h < 190) {
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
                if ($('.image-preview').length == 1) {
                    image_holder.find('.make-primary-image').prop('checked', true).attr('checked', true);
                }
            };
        }
    }
}

function scrollToBottom() {
    var wtf = $('.conversation-message-box');
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
    $viewTabContainer = $('.view-tab-container'); //tabs container
    $viewTab = $viewTabContainer.find('.view-tab'); //all tabs

    var speed = 0;

    //add active classes on load
    // $btns.eq(0).addClass('is-active');
    // $viewTabContainer.children(':first-child()').addClass('is-active');

    $btns.on('click', function(e) {
        e.preventDefault();

        var $thisBtn = $(this), //this button
            btnDataTab = $thisBtn.data('tab'), //this button data
            hash = $thisBtn.data('tab'); //this data tab


            if (!$thisBtn.hasClass('is-active')) {
                $btns.removeClass('is-active');
                $thisBtn.addClass('is-active');

            //add hash
            window.location.hash = hash;

            $viewTab.each(function() {
                var $thisTab = $(this); //this tab
                if ($thisTab.data('tab') == btnDataTab) {
                    $viewTab.fadeOut(speed).removeClass('is-active');
                    // setTimeout(function() {
                        $thisTab.fadeIn(speed).addClass('is-active');
                        $thisBtn.trigger('shown.tab');
                    // }, speed)
        }
    });
        }
    });

    //read hash
    var currentHash = window.location.hash.substr(1);

    if (currentHash.length) {
        object.find('a').each(function() {
            if (currentHash == $(this).data('tab')) {
                $(this).trigger('click');
            }
        });
    }
}

function sidebarHeight() {
    var windowHeight = $(window).height(),
    $mainContentInner = $('.main-content-inner'),
    $sidebarInner = $('.sidebar-inner'),
    $viewTabContainer = $mainContentInner.children('.view-tab-container'),
    eventDivHeight = $mainContentInner.children('.event').height(),
    filtersAreaHeight = $mainContentInner.children('.filters-area').height(),
    paddings;

    if ($mainContentInner.length && $('.hidden-xs').is(':visible')) {
        $sidebarInner.each(function() {
            var offsetTop = $(this).offset().top;
            $(this).height(windowHeight - offsetTop);
        });

        if ($('.page-settings').length) {
            paddings = 0;
        } else {
            paddings = 30;
        }

        $mainContentInner.height(windowHeight - $mainContentInner.offset().top - paddings); //30px is for padding top and bottom

        $('.shouts-listing.shouts-listing-filter').height($sidebarInner.height() - 366);

        $('.shouts-listing.shouts-listing-feed').height($('.shouts-listing').closest('.sidebar-inner').height() - 73);

        // $viewTabContainer.height($mainContentInner.height() - eventDivHeight - filtersAreaHeight - 39);
        if ($viewTabContainer.length) {
            setTimeout(function() {
                $viewTabContainer.height(windowHeight - $viewTabContainer.offset().top);
            }, 100);
        }
    } else if (!$('.hidden-xs').is(':visible')) {
        $mainContentInner.height('');
        $('.shouts-listing.shouts-listing-filter').height('');
        $('.shouts-listing.shouts-listing-feed').height('');
        if ($viewTabContainer.length) {
            setTimeout(function() {
                $viewTabContainer.height('');
            }, 100);
        }
    }
}

function youtubeParser(url) {
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match = url.match(regExp);
    if (match && match[7].length == 11) {
        return match[7];
    } else {

    }
}

function autocomplete() {

    var $searchContainer = $('.search-block-container'),
    $autocompleteInput = $('#autocomplete'),
    $searchBlock = $autocompleteInput.parent();

    if ($autocompleteInput.length) {

        //jquery-ui autocomplete
        $autocompleteInput.autocomplete({
            delay: 10,
            minLength: 2,
            source: function(request, response) {
                $.ajax({
                    url: Routing.generate('users_find'),
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            messages: {
                noResults: ''
            }
        }).data('uiAutocomplete')._renderItem = function(ul, item) {
            return $('<li />')
            .data('item.autocomplete', item)
            .append("<a href='" + baseURL + "/m/" + item.username + "'><img src='" + baseURL + "/m/" + item.username + "/avatar/my_thumb' />" + "<span class='search-text'>" + item.username + "<span class='search-location'>" + item.fullName + "</span></span></a>")
            .appendTo(ul);
        };

        $searchBlock.addClass('effects-ready');

        $autocompleteInput.focus(function() {
            $searchBlock.addClass('is-opened');
        });

        $autocompleteInput.blur(function() {
            $autocompleteInput.val().length || $searchBlock.removeClass('is-opened');
        });

        //this is for responsive
        $('.search-toggle').on('click', function() {
            $(this).toggleClass('is-active');
            $searchContainer.toggleClass('is-opened');
        });
    }
}

function autocompleteMessageUser() {

    var $autocompleteInput = $('.autocomplete');

    if ($autocompleteInput.length) {

        //jquery-ui autocomplete
        $autocompleteInput.autocomplete({
            delay: 10,
            minLength: 2,
            source: function(request, response) {
                $.ajax({
                    url: Routing.generate('users_find'),
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            messages: {
                noResults: ''
            },
            select: function(event, ui) {
                $autocompleteInput.val(ui.item.username);
                $('.conversation-send .send-message').data('tousername', ui.item.username).data('toid', ui.item.id);

                return false;
            }
        }).data('uiAutocomplete')._renderItem = function(ul, item) {
            return $("<li />")
            .data("item.autocomplete", item)
            .append("<a class='user-suggest-messages' data-user='" + item.username + "' data-id='" + item.id + "'><img src='" + item.avatar + "' />" + "<span class='search-text'>" + item.username + "<span class='search-location'>" + item.username + "</span></span></a>")
            .appendTo(ul);
        };
    }
}

function showAllTags(e) {
    e.preventDefault();

    var $this = $(this),
    $tagsContainer = $this.parent('.tags-container'),
    $icon = $this.children('.fa');

    if ($tagsContainer.hasClass('opened')) {
        $tagsContainer.removeClass('opened');
        $icon.removeClass('fa-angle-up').addClass('fa-angle-down');
    } else {
        $tagsContainer.addClass('opened');
        $icon.removeClass('fa-angle-down').addClass('fa-angle-up');
    }
}

function menu() {
    var $header = $('header'),
    $mainFluid = $('.main-fluid'),
    $sidebar = $('.sidebar'),
    $navbarToggle = $('.navbar-toggle');

    $navbarToggle.on('click', function() {
        $sidebar.toggleClass('is-active');
        $header.toggleClass('is-active');
        $mainFluid.toggleClass('is-active');
        $('body').toggleClass('modal-open');
    });

    var resizeTimerMenu;
    $(window).resize(function() {
        clearTimeout(resizeTimerMenu);
        resizeTimerMenu = setTimeout(function() {
            $sidebar.removeClass('is-active');
            $header.removeClass('is-active');
            $mainFluid.removeClass('is-active');
        }, 50);
    });
}

function scrollbarPlugin() {
    var $withScrollbar = $('.with-scrollbar');

    $withScrollbar.each(function() {
        var $this = $(this),
        thisHeight = $(this).height(),
        thisChildrenHeight = $this.children().height();

        if (thisChildrenHeight > thisHeight) {
            if ($('.hidden-xs').is(':visible')) {
                $withScrollbar.perfectScrollbar({
                    suppressScrollX: true,
                    wheelPropagation: true
                });
            } else {
                if ($('.with-scrollbar.ps-container').length) {
                    $(this).perfectScrollbar('destroy');
                }
            }
        }
    });
}

function addMessage(type, message, temp) {
    if (typeof temp == 'undefined') {
        temp = 'temp';
    } else {
        temp = '';
    }

    if (type === false) {
        type = 'danger';
    }

    $('.fixed-alerts-container').append(notificationTemplate({
        type: type,
        message: message,
        temp: temp
    }));
    setTimeout(function() {
        $('.fixed-alerts-container').children('.temp:last').alert('close');
    }, 4000);

    return true;
}

function parseYTVideoImages() {
    $('.ytvideo').each(function() {
        var src = $(this).attr('href');
        var ytId = youtubeParser(src);
        var ytImg = 'http://img.youtube.com/vi/' + ytId + '/0.jpg';
        $(this).find('img').attr('src', ytImg);
    });
}

function imgError(image, size) {
    image.onerror = '';
    image.src = Routing.generate('default_avatar', {'size': size});
    return true;
}

function getUserShouts() {
    $('.shouts-listing').html('');
    var username = $('.shouts-sidebar').data('username');

    $.ajax({
        url: Routing.generate('user_shouts', {'username': username})
    }).done(function( result ) {
        if (result.status == 'success'){
            $.each(result.data, function(k, v){
                $( '.shouts-listing' ).prepend(shoutBoxTemplate( v ) );
            });

            if (result.data.length == 0) {
                $( '.shouts-listing' ).html('<h5>No shouts yet.</h5>');
            }
        }
    });
}

function resetPhotosCountIndicator() {
    $('.panel-photos-header .badge span').text($('.profile-media-wall-item').length);
}