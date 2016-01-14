'use strict';

/* global baseURL */
/* global _ */
/* global Routing */
/* global checkCanShout */
/* global imagesCollectionHolder */

_.templateSettings.variable = 'rc';
var shoutBoxTemplate = _.template($('#shoutBoxTemplate').html());
var notificationTemplate = _.template($('#notificationTemplate').html());
var musicianBoxTemplate = _.template($('#musicianBoxTemplate').html());
var musicianMapBoxTemplate = _.template($('#musicianMapBoxTemplate').html());
var messageTemplate = _.template($('#messageTemplate').html());
var actionConfirmModalTemplate = _.template($('#actionConfirmModalTemplate').html());

var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
    isMobile = true;
}

function checkTouchDevice() {
    if ('ontouchstart' in document.documentElement) {
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

                var imageHolder = $(input).parents('.image-holder');
                imageHolder.append('<img src="" width="200" />');
                imageHolder.find('img').attr('src', e.target.result);
                imageHolder.find('.make-primary-image').parent().show();
                imageHolder.find('.remove-image').show();
                imageHolder.find('.upload').hide();
                if ($('.image-preview').length == 1) {
                    imageHolder.find('.make-primary-image').prop('checked', true).attr('checked', true);
                }
            };
        };
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
        }, 200 );

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
    var $viewTabContainer = $('.view-tab-container'); //tabs container
    var $viewTab = $viewTabContainer.find('.view-tab'); //all tabs

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

    //if main-content-inner exists and it's not mobile
    if ($mainContentInner.length && $('.hidden-xs').is(':visible')) {
        // sidebar height
        $sidebarInner.each(function() {
            var offsetTop = $(this).offset().top;
            $(this).height(windowHeight - offsetTop);
        });

        if ($('.page-settings').length || $('.page-shouts ').length) {
            paddings = 0;
        } else {
            paddings = 30;
        }

        //main content inner block
        $mainContentInner.height(windowHeight - $mainContentInner.offset().top - paddings); //30px is for padding top and bottom

        if ($('.shouts-listing.shouts-listing-filter').length) {
            //shouts listing in sidebar
            var shoutsListSiblHeight = $('.shouts-listing.shouts-listing-filter').offset().top;
            $('.shouts-listing.shouts-listing-filter').height($sidebarInner.height() - shoutsListSiblHeight + 70);
        }

        if ($('.shouts-listing.shouts-listing-feed').length) {
            $('.shouts-listing.shouts-listing-feed').height($('.shouts-listing').closest('.sidebar-inner').height() - 73);
        }

        // $viewTabContainer.height($mainContentInner.height() - eventDivHeight - filtersAreaHeight - 39);
        if ($viewTabContainer.length) {
            setTimeout(function() {
                $viewTabContainer.height(windowHeight - $viewTabContainer.offset().top);
            }, 100);
        }
    } 
    else if ($('.page-shouts').length && !$('.hidden-xs').is(':visible')) {
        setTimeout(function() {
            //main content inner block
            $mainContentInner.height(windowHeight - $mainContentInner.offset().top - 0); //30px is for padding top and bottom
            //shouts listing in sidebar
            var shoutsListSiblHeight = $('.shouts-listing-container').offset().top;
            $('.shouts-listing.shouts-listing-filter').height($mainContentInner.height() - shoutsListSiblHeight + 26);
        }, 100);
    }
    else if (!$('.hidden-xs').is(':visible')) {
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
    }
}

function conversationHeight() {
    var $conversation = $('.conversation'),
        $conversationContainer = $conversation.find('.conversation-container'),
        conversationHeight = $conversation.height(),
        coneversationCloseHeight = $('.conversation-close').height(),
        coneversationSendHeight = $('.conversation-send').height();

    $conversationContainer.height(conversationHeight - coneversationCloseHeight - coneversationSendHeight - 30);
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
            return $('<li />')
                .data('item.autocomplete', item)
                .append('<a class="user-suggest-messages" data-user="' + item.username + '" data-id="' + item.id + '"><img src="' + item.avatar + '" />' + '<span class="search-text">' + item.username + '<span class="search-location">' + item.username + '</span></span></a>')
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

                //create same type of event when the bottom of page is reached, for infinite scroll
                $(window).scroll(function() {
                    if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                        $withScrollbar.trigger('ps-y-reach-end');
                    }
                });
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

            if (result.data.length === 0) {
                $( '.shouts-listing' ).html('<h5>No shouts yet.</h5>');
            }
        }
    });
}

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

    $('.profile-tabs a').on('shown.tab', function() {
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
        var val = $('#shoutForm #shout_text').val();

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
                    $('#shout_text').val('');
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