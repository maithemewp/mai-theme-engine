/**
 * Initiate Slick on [grid slider="true"] posts when document ready.
 *
 * @version 1.0.2
 */
jQuery(document).ready(function($) {

	var $sliders = $('.mai-slider');

	if ( ! $sliders.length ) {
		return;
	}

	$.each( $sliders, function() {

		var arrows         = Boolean( $(this).data('arrows') ),
			autoPlay       = Boolean( $(this).data('autoplay') ),
			centerMode     = Boolean( $(this).data('centermode') ),
			dots           = Boolean( $(this).data('dots') ),
			fade           = Boolean( $(this).data('fade') ),
			infinite       = Boolean( $(this).data('infinite') ),
			slidesToScroll = parseInt( $(this).data('slidestoscroll') ),
			slidesToShow   = parseInt( $(this).data('slidestoshow') );
			speed          = parseInt( $(this).data('speed') );

		var desktopToShow,
			desktopToScroll,
			tabletToShow,
			tabletToScroll,
			mobileToShow,
			mobileToScroll;

		/**
		 * Default show/scroll is over 1200px.
		 * Note: Seems to be a bug with slidesToScroll when centermode is true.
		 * @link https://github.com/kenwheeler/slick/issues/2328
		 */

		// Get desktoToShow (1200 breakpoint).
		if ( slidesToShow > 3 ) {
			desktopToShow = slidesToShow - 1;
		} else {
			desktopToShow = slidesToShow;
		}
		// Get desktopToScroll (1200 breakpoint).
		if ( slidesToScroll > desktopToShow ) {
			desktopToScroll = desktopToShow;
		} else {
			desktopToScroll = slidesToScroll;
		}

		// Get tabletToShow (992 breakpoint).
		if ( desktopToShow > 2 ) {
			tabletToShow = desktopToShow - 1;
		} else {
			tabletToShow = desktopToShow;
		}
		// Get tabletToScroll (992 breakpoint).
		if ( desktopToScroll > tabletToShow ) {
			tabletToScroll = tabletToShow;
		} else {
			tabletToScroll = desktopToScroll;
		}

		// Get mobileToShow (768 breakpoint).
		if ( tabletToShow > 1 ) {
			mobileToShow = tabletToShow - 1;
		} else {
			mobileToShow = tabletToShow;
		}
		// Get mobileToScroll (768 breakpoint).
		if ( tabletToScroll > mobileToShow ) {
			mobileToScroll = mobileToShow;
		} else {
			mobileToScroll = tabletToScroll;
		}

		$(this).slick({
			adaptiveHeight: false, // true breaks things on image-bg aspect-ratio resize.
			arrows: arrows,
			autoplay: autoPlay,
			dots: dots,
			fade: fade,           // Things seem to blow up if columns is > 1.
			focusOnChange: false, // This is Slick default as of 1.8.0, but i want to make sure, cause if true it makes things really jumpy.
			infinite: infinite,
			slidesToShow: slidesToShow,
			slidesToScroll: slidesToScroll,
			autoplaySpeed: speed,
			cssEase: fade ? 'linear' : 'ease', // Use linear if fade is true, otherwise default is ease.
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
