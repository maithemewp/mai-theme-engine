// Get it started.
document.addEventListener( 'DOMContentLoaded', function() {

	// Set consts.
	var html            = document.querySelector( 'html' );
	var body            = document.querySelector( 'body' );
	var header          = document.querySelector( '.site-header' );
	var htmlStyles      = window.getComputedStyle( html );
	var headerStyles    = window.getComputedStyle( html );
	var logoWidth       = maiScroll.logoWidth;
	var shrunkLogoWidth = Math.round( logoWidth * .7 );
	var hasShrinkHeader = body.classList.contains( 'has-shrink-header' );
	var hasRevealHeader = body.classList.contains( 'has-reveal-header' );

	// Set vars.
	var scrollClassAdded   = false,
		scrollingDown      = true,
		scrollingUp        = false,
		headerConcealed    = false,
		startDistance      = false,
		percentage         = 0,
		previousPercentage = 0,
		scrollDowns        = [],
		shrinkClassAdded   = false,
		afterHeader        = false;

	/**
	 * Handle body scroll tracking.
	 *
	 * @version  1.0.0
	 */
	var bodyScroll = basicScroll.create({
		elem: html,
		from: 'top-top',
		to: 'bottom-bottom',
		props: {},
		inside: (instance, percentage, props) => {

			// Scroll class.
			if ( ( percentage > 0 ) && ! scrollClassAdded ) {
				addScrollClass();
			} else if ( ( percentage <= 0 ) && scrollClassAdded ) {
				removeScrollClass();
			}

			// Bail if no reveal header.
			if ( ! hasRevealHeader ) {
				return;
			}

			var scrollTop = false;

			// Maybe set the new start distance.
			if ( false === startDistance ) {
				scrollTop     = document.documentElement.scrollTop;
				startDistance = scrollTop;
			}

			// Store items.
			scrollingDown      = ( percentage > previousPercentage );
			scrollingUp        = ! scrollingDown;
			previousPercentage = percentage;
			scrollDowns        = storeItem( scrollDowns, scrollingDown, 4 );

			// Bail if not scrolling the same direction.
			if ( ! sameItems( scrollDowns ) ) {
				// Reset the starting distance.
				startDistance = false;
				return;
			}

			// Maybe get scrollTop if we haven't yet.
			if ( false === scrollTop ) {
				scrollTop = document.documentElement.scrollTop;
			}

			// Check if scrolled enough.
			hasScrolledEnough = scrolledEnough( startDistance, scrollTop, scrollingDown );

			// Bail if haven't scrolled enough.
			if ( ! hasScrolledEnough ) {
				// If scrolling up and not after the header.
				if ( headerConcealed && scrollingUp && ! afterHeader ) {
					// Reveal the header.
					revealHeader();
				}
				return;
			}

			// Not concealed and scrolling down.
			if ( ! headerConcealed && scrollingDown ) {
				concealHeader();
			}
			// Concealed and scrolling up.
			else if ( headerConcealed && scrollingUp ) {
				revealHeader();
			}
		},
		outside: (instance, percentage, props) => {
			if ( percentage <= 0 ) {
				if ( scrollClassAdded ) {
					removeScrollClass();
				}
				if ( headerConcealed ) {
					revealHeader();
				}
			}
		}
	});
	bodyScroll.start();


	/**
	 * Handle header scroll tracking.
	 * This is always running, just not always outputting CSS vars.
	 *
	 * @version  1.0.0
	 */
	var headerFrom  = parseInt( headerStyles.marginTop ) - parseInt( htmlStyles.marginTop ),
		headerTo    = headerFrom + 200,
		headerProps = hasShrinkHeader ? {
			'--logo-width': {
				from: logoWidth + 'px',
				to: shrunkLogoWidth + 'px'
			},
			'--logo-margin': {
				from: '24px',
				to: '4px'
			}
		} : [];

	var headerScroll = basicScroll.create({
		elem: header,
		from: headerFrom,
		to: headerTo,
		props: headerProps,
		inside: (instance, percentage, props) => {
			if ( ( percentage > 0 ) && ! shrinkClassAdded ) {
				addShrinkClass();
			} else if ( ( percentage <= 0 ) && shrinkClassAdded ) {
				removeShrinkClass();
			}
			if ( afterHeader ) {
				afterHeader = false;
			}
		},
		outside: (instance, percentage, props) => {
			if ( percentage <= 0 ) {
				removeShrinkClass();
				if ( afterHeader ) {
					afterHeader = false;
				}
			} else {
				if ( ! afterHeader ) {
					afterHeader = true;
				}
			}
		}
	});
	headerScroll.start();

	// Add scroll.
	function addScrollClass() {
		body.classList.add( 'scroll' );
		scrollClassAdded = true;
	}

	// Remove scroll.
	function removeScrollClass() {
		body.classList.remove( 'scroll' );
		scrollClassAdded = false;
	}

	// Add shrink.
	function addShrinkClass() {
		if ( ! header ) {
			return;
		}
		header.classList.add( 'shrink' );
		shrinkClassAdded = true;
	}

	// Remove shrink.
	function removeShrinkClass() {
		if ( ! header ) {
			return;
		}
		header.classList.remove( 'shrink' );
		shrinkClassAdded = false;
	}

	// Conceal the header.
	function concealHeader() {
		if ( ! header ) {
			return;
		}
		header.classList.remove( 'reveal-header' );
		header.classList.add( 'conceal-header' );
		headerConcealed = true;
	}

	// Reveal the header.
	function revealHeader() {
		if ( ! header ) {
			return;
		}
		header.classList.remove( 'conceal-header' );
		header.classList.add( 'reveal-header' );
		headerConcealed = false;
	}

	// Store items in an array.
	function storeItem( existingItems, newItem, total ) {
		// If we have more than the total.
		if ( existingItems.length > total ) {
			// Set the tempTotal items to 1 less than the actual total.
			var tempTotal = (total - 1);
			existingItems = existingItems.slice(-tempTotal);
		}
		// Add new item to this array.
		existingItems.push( newItem );
		return existingItems;
	}

	// Check if progress has incremented in the same direction.
	function sameItems( array ) {
		return array.every( function( v, i, a ) {
			return i === 0 || v === a[i - 1];
		});
	}

	// If scrolled far enough.
	function scrolledEnough( firstItem, lastItem, scrollingDown ) {
		if ( ! ( firstItem && lastItem ) ) {
			return false;
		}
		return scrollingDown ? scrolledDownEnough( firstItem, lastItem ) : scrolledUpEnough( firstItem, lastItem );
	}

	// If scrolled down enough.
	function scrolledDownEnough( firstItem, lastItem ) {
		return ( lastItem - firstItem ) > 320;
	}

	// If scrolled up enough.
	function scrolledUpEnough( firstItem, lastItem ) {
		return ( firstItem - lastItem ) > 160;
	}

});
