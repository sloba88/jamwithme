'use strict';

//plugin for dinamic window height, written by Vladislav Stanic
(function($) {
    $.heightsPlugin = function(element, options) {
        var settings = {};
        element.data('vladislav', this);

        var windowHeight,
            isMobile;

        var obj = this;

        this.init = function(element, options) {
            settings = $.extend({}, $.heightsPlugin.defaultOptions, options);

            this.setup();
            this.sidebarHeight();
            this.mainHeight();
            this.tabHeight();
            this.shoutsHeight();

            this.composeOnClick();
            this.filtersOnChange();

            //window resize event
            $(window).resize(function(){

                obj.setup();
                obj.sidebarHeight();
                obj.mainHeight();
                obj.tabHeight();
                obj.shoutsHeight();
                obj.conversationHeight();
            });
        };

        this.setup = function() {
            windowHeight = $(window).height();
            isMobile = $(window).width() < 768;
        };

        this.sidebarHeight = function() {
            var $sidebarInner = $('.sidebar-inner');

            if (isMobile === false) {

                $sidebarInner.each(function(){
                    var $this = $(this);


                    $this.height(windowHeight - $this.offset().top);
                });
            } else {
                $sidebarInner.height('');
            }

        };

        this.mainHeight = function() {
            var $mainContentInner = $('.main-content-inner');

            if (isMobile === false) {

                $mainContentInner.each(function(){
                    var $this = $(this);

                    $this.height(windowHeight - $this.offset().top);
                });
            } else {
                $mainContentInner.height('');
            }
        };

        this.tabHeight = function() {
            var $viewTab = $('.main-content-inner .view-tab-container');

            if (isMobile === false) {

                $viewTab.each(function(){
                    var $this = $(this);

                    $this.height(windowHeight - $this.offset().top - 10);

                    $viewTab.on('shown.tab', function() {
                        console.log($this);
                        $this.height(windowHeight - $this.offset().top - 10);
                    });
                });
            } else {
                $viewTab.height('');
            }
        };

        this.shoutsHeight = function() {
            var $shoutsListing = $('.shouts-listing');

            if (isMobile === false || $('.page-shouts').length) {

                $shoutsListing.each(function(){
                    var $this = $(this);

                    $this.height(windowHeight - $this.offset().top);
                });
            } else {
                $shoutsListing.height('');
            }
        };

        this.conversationHeight = function() {
            var $conversation = $('.conversation');

            if ($conversation.length > 0) {
                var $conversationContainer = $conversation.find('.conversation-container'),
                    coneversationSendHeight = $('.conversation-send').outerHeight(),
                    containerOffsetTop = $conversationContainer.offset().top;

                $conversationContainer.height($conversation.outerHeight() - containerOffsetTop - coneversationSendHeight - 30);
            }
        };

        this.composeOnClick = function() {
            $('.btn-compose').on('click', function() {
                setTimeout(function(){

                    obj.conversationHeight();

                }, 100);
            });
        };

        this.filtersOnChange = function() {

            //select plugin on dashboard updates height of main container on change
            $('#instruments, #genres').on('change', obj.tabHeight);
        };

        this.init(element, options);

    }; // heightsPlugin

    // create object
    $.fn.heightsPlugin = function(options) {
        return this.each(function() {
            (new $.heightsPlugin($(this), options));
        });
    };

    // default options
    $.heightsPlugin.defaultOptions = {
        // idCarrier: '#selected-nid'
    };

    $(document).ready(function() {
        $(this).heightsPlugin();
    });

})(jQuery);