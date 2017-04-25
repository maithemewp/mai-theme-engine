/**
 * Initiate Slick on [display-posts slider=true] posts when document ready
 *
 * @version 1.0.2
 */
jQuery(document).ready(function($) {

    var $sliders = $('.mai-slider');

    if ( ! $sliders.length > 0 ) {
        return;
    }

    $.each( $sliders, function() {

	    var arrows 			= Boolean( $(this).data('arrows') ),
	        centerMode  	= Boolean( $(this).data('centermode') ),
	    	dots 			= Boolean( $(this).data('dots') ),
	    	fade 			= Boolean( $(this).data('fade') ),
	        infinite  		= Boolean( $(this).data('infinite') ),
	        slidesToScroll  = parseInt( $(this).data('slidestoscroll') ),
	    	slidesToShow    = parseInt( $(this).data('slidestoshow') );

	    var desktopToShow,
		    desktopToScroll,
	    	tabletToShow,
	    	tabletToScroll,
	    	mobileToShow,
	    	mobileToScroll;

	    // Get desktoToShow
	    if ( slidesToShow > 3 ) {
	        desktopToShow = slidesToShow - 1;
	    } else {
	    	desktopToShow = slidesToShow;
	    }
	    // Get desktopToScroll
        if ( slidesToScroll > desktopToShow ) {
	        desktopToScroll = desktopToShow;
        } else {
        	desktopToScroll = slidesToScroll;
        }

        // Get tabletToShow
	    if ( desktopToShow > 2 ) {
	        tabletToShow = desktopToShow - 1;
	    } else {
	    	tabletToShow = desktopToShow;
	    }
	    // Get tabletToScroll
        if ( desktopToScroll > tabletToShow ) {
	        tabletToScroll = tabletToShow;
        } else {
        	tabletToScroll = desktopToScroll;
        }

        // Get mobileToShow
	    if ( tabletToShow > 1 ) {
	        mobileToShow = tabletToShow - 1;
	    } else {
	    	mobileToShow = tabletToShow;
	    }
	    // Get mobileToScroll
        if ( tabletToScroll > mobileToShow ) {
	        mobileToScroll = mobileToShow;
        } else {
        	mobileToScroll = tabletToScroll;
        }

		$(this).slick({
			adaptiveHeight: false,
			arrows: arrows,
			dots: dots,
			fade: fade,
			infinite: infinite,
			slidesToShow: slidesToShow,
			slidesToScroll: slidesToScroll,
			responsive: [{
				breakpoint: 1200,
				settings: {
					slidesToShow: desktopToShow,
					slidesToScroll: desktopToScroll,
					centerMode: centerMode,
				}
			}, {
				breakpoint: 992,
				settings: {
					slidesToShow: tabletToShow,
					slidesToScroll: tabletToScroll,
					centerMode: centerMode,
				}
			}, {
				breakpoint: 768,
				settings: {
					slidesToShow: mobileToShow,
					slidesToScroll: mobileToScroll,
					centerMode: centerMode,
				}
			}],
		});

		var $slickTrack = $(this).find('.slick-track');

		var center = Boolean( $(this).data('center') ),
			middle = Boolean( $(this).data('middle') );

	    if ( center ) {
		    $slickTrack.addClass('center-xs');
		}

	    if ( middle ) {
		    $slickTrack.addClass('middle-xs');
		}

    });

	function _isEven(value) {
		if ( value%2 == 0 ) {
			return true;
		} else {
			return false;
		}
	}

});
