'use strict';

/* global Routing */

function getParameterByName(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)'),
        results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

function parallax() {
    var $window = $(window);

    $('[data-type]').each(function() {
        $(this).data('offsetY', parseInt($(this).attr('data-offsetY')));
        $(this).data('Xposition', $(this).attr('data-Xposition'));
        $(this).data('speed', $(this).attr('data-speed'));
    });

    $('section[data-type="background"]').each(function(){

        var $self = $(this),
            offsetCoords = $self.offset(),
            topOffset = offsetCoords.top;

        if ( ($window.scrollTop() + $window.height()) > (topOffset) &&
            ( (topOffset + $self.height()) > $window.scrollTop() ) ) {

            var yPos = -( ($window.scrollTop() - $self.offset().top) / $self.data('speed'));

            if ($self.data('offsetY')) {
                yPos += $self.data('offsetY');
            }

            var coords = '50% '+ yPos + 'px';

            $self.css({ backgroundPosition: coords });

        } // in view

        $(window).scroll(function() {

            if ( ($window.scrollTop() + $window.height()) > (topOffset) &&
                ( (topOffset + $self.height()) > $window.scrollTop() ) ) {

                var yPos = -( ($window.scrollTop() - $self.offset().top) / $self.data('speed'));

                if ($self.data('offsetY')) {
                    yPos += $self.data('offsetY');
                }

                var coords = '50% '+ yPos + 'px';

                $self.css({ backgroundPosition: coords });

            } // in view

        }); // window scroll

    });	// each data-type
}

$(document).ready(function(){

    $('[data-toggle="tooltip"]').tooltip();

	parallax();

	$('#horizontalmenu').ddscrollSpy();

    $('input[type=checkbox]').next('label').prepend('<span></span>');
    $('.info-popover').popover();

    $('#fos_user_registration_form_plainPassword_second').bind('cut copy paste',function(e) {
        e.preventDefault();
    });

    $('#fos_user_profile_form_username').alphanum({
        allowSpace: false,
        allowNewline: false
    });

    if (getParameterByName('email')) {
        $('#fos_user_registration_form_email').val(getParameterByName('email'));
        $('#fos_user_resetting_request_username').val(getParameterByName('email'));
    }

    $('#acceptTerms').on('click', function() {
        $('#fos_user_registration_form_acceptedTerms').prop('checked', true).attr('checked', true);
        $('.fos_user_registration_form_acceptedTerms_label span').popover('hide');
    });

    $('.fos_user_registration_form_acceptedTerms_label span').popover({
        'trigger': 'manual',
        'content': 'You must accept terms and conditions in order to continue.'
    });

    $('#fos_user_registration_form_acceptedTerms').on('click', function() {
        $('.fos_user_registration_form_acceptedTerms_label span').popover('hide');
    });

    $('.fos_user_registration_register :submit').on('click', function() {
        if ($('#fos_user_registration_form_acceptedTerms').prop('checked') === false) {
            $('.fos_user_registration_form_acceptedTerms_label span').popover('show');
        }
    });

    setTimeout(function(){
        $('.infoBoxSmall.teacher').fadeIn();
    }, 2000);

    $('.icon.guitar, .icon.drumms').on('mouseover', function(){
        $('.infoBoxSmall.teacher').hide();
    });

    $('.icon.info').on('mouseover', function(){
        $(this).find('.infoBox').fadeIn();
    });

    $('body').on('mouseleave', '.icon.info',function(e){
        $('.infoBoxSmall').hide();
    });

    $('#termsModal').on('show.bs.modal', function() {
        $.get(
            Routing.generate('terms_raw'),
            function(data){
                $('#termsModal .modal-body').html(data);
            });
    });

    if ($(window).width() < 380) {
        $('#ytPromoVideo').height('180');
    }

}); // document ready




