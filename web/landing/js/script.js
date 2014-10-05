$(document).ready(function(){

	parallax();

	$('#horizontalmenu').ddscrollSpy();

    $(".submitBtn").on('click', function(e){

        var $myForm = $(".submitBtn").parents('form');
        if (!$myForm[0].checkValidity()) {

        }else{
            e.preventDefault();
            $.ajax({
                data: {'email': $(".inputField").val()},
                type: 'POST',
                url: baseUrl + '/subscription/add',
                success: function(result){
                    console.log(result);
                    if (result.status == 'success'){
                        $(".subscribeBox >").fadeOut('slow', function(){
                            $(".subscribeBox").html('<h4>Thank you for your subscription.</h4>');
                        });
                    }else if(result.status == 'error'){
                        $('.formError').remove();
                        $(".subscribeBox").append('<p class="formError">'+result.message+'</p>');
                    }
                }
            });
        }
    });

    setTimeout(function(){
        $('.infoBoxSmall.teacher').fadeIn();
    },2000);

    $('.icon.guitar, .icon.drumms').on('mouseover', function(){
        $('.infoBoxSmall.teacher').hide();
    });

    $('.icon.info').on('mouseover', function(){
        $(this).find('.infoBox').fadeIn();
    });

    $('body').on('mouseleave', '.icon.info',function(e){
        $('.infoBoxSmall').hide();
    });

    $('.fast-subscribe').on('click', function(){
        $('.inputField').focus();
    });

}); // document ready

function parallax() {
	$window = $(window);
	
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

			}; // in view

			$(window).scroll(function() {

				if ( ($window.scrollTop() + $window.height()) > (topOffset) &&
					( (topOffset + $self.height()) > $window.scrollTop() ) ) {

					var yPos = -( ($window.scrollTop() - $self.offset().top) / $self.data('speed'));  
				
				if ($self.data('offsetY')) {
					yPos += $self.data('offsetY');
				}
				
				var coords = '50% '+ yPos + 'px';

				$self.css({ backgroundPosition: coords }); 

			}; // in view			

		}); // window scroll
			
	});	// each data-type
}

function backToTop() {
	var offset = 220;
	var duration = 500;

	if ($(this).scrollTop() > offset) {
		$('.back-to-top').fadeIn(duration);
	} else {
		$('.back-to-top').fadeOut(duration);
	}

	$('.back-to-top').click(function(event) {
		event.preventDefault();
		$('html, body').animate({scrollTop: 0}, duration);
		return false;
	});
}


