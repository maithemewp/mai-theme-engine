// Get it started.
document.addEventListener( 'DOMContentLoaded', function() {

	// Set consts.
	var body             = document.querySelector( 'body' );
	var header           = document.querySelector( '.site-header' );
	var logoWidth        = maiScroll.logoWidth;
	var logoTop          = maiScroll.logoTop;
	var logoBottom       = maiScroll.logoBottom;
	var logoShrinkWidth  = maiScroll.logoShrinkWidth;
	var logoShrinkTop    = maiScroll.logoShrinkTop;
	var logoShrinkBottom = maiScroll.logoShrinkBottom;
	var hasShrinkHeader  = body.classList.contains( 'has-shrink-header' );
	var hasRevealHeader  = body.classList.contains( 'has-reveal-header' );
	var hasStickyHeader  = ( hasRevealHeader || body.classList.contains( 'has-shrink-header' ) );

	// Set vars.
	var scrollClassAdded   = false,
		scrollingDown      = true,
		scrollingUp        = false,
		headerConcealed    = false,
		startDistance      = false,
		previousPercentage = 0,
		scrollDowns        = [],
		stuckClassAdded    = false,
		afterHeader        = false;

	/**
	 * Handle body scroll tracking.
	 *
	 * @version  1.0.0
	 */
	var bodyScroll = basicScroll.create({
		elem: body,
		from: 0,
		to: 100,
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

	// Make sure we have a header.
	if ( header ) {

		/**
		 * Handle header scroll tracking.
		 * This is always running, just not always outputting CSS vars.
		 *
		 * @version  1.0.0
		 */
		var root         = document.documentElement;
		var headerHeight = 0;

		var headerScroll = basicScroll.create({
			elem: document.querySelector( '#header-trigger' ),
			from: 'top-top',
			to: 'bottom-top',
			props: hasShrinkHeader ? {
				'--text-title': {
					from: '100%',
					to: '70%',
				},
				'--logo-width': {
					from: logoWidth + 'px',
					to: logoShrinkWidth + 'px',
				},
				'--logo-margin-top': {
					from: logoTop + 'px',
					to: logoShrinkTop + 'px',
				},
				'--logo-margin-bottom': {
					from: logoBottom + 'px',
					to: logoShrinkBottom + 'px',
				},
			} : [],
			inside: (instance, percentage, props) => {
				if ( afterHeader ) {
					afterHeader = false;
				}
				if ( ! hasStickyHeader ) {
					return;
				}
				// Shrink only happens > 768 so CSS position: sticky; is fine, bail out here.
				if ( window.innerWidth <= 768 ) {
					return;
				}
				// Slight tolerance since we have position:sticky; fallback. Less jarring.
				if ( percentage > 5 ) {
					headerHeight = header.clientHeight;
					stick();
				} else {
					unstick();
				}
			},
			outside: (instance, percentage, props) => {
				// Negative percentage is space above the header. Slight tolerance since we have position:sticky; fallback. Less jarring.
				if ( percentage <= 5 ) {
					if ( afterHeader ) {
						afterHeader = false;
					}
					if ( ! hasStickyHeader ) {
						return;
					}
					// Shrink only happens > 768 so CSS position: sticky; is fine, bail out here.
					if ( window.innerWidth <= 768 ) {
						return;
					}
					unstick();
				}
				// Below the header.
				else {
					if ( ! afterHeader ) {
						afterHeader = true;
					}
				}
			}
		});
		headerScroll.start();

	}

	// Add scroll class.
	function addScrollClass() {
		body.classList.add( 'scroll' );
		scrollClassAdded = true;
	}

	// Remove scroll class.
	function removeScrollClass() {
		body.classList.remove( 'scroll' );
		scrollClassAdded = false;
	}

	// Do sticky things.
	function stick() {
		if ( stuckClassAdded ) {
			return;
		}
		body.classList.add( 'header-stuck' );
		header.classList.add( 'stuck' );
		stuckClassAdded = true;
		if ( ! hasShrinkHeader ) {
			return;
		}
		header.style.position = 'fixed';
		root.style.setProperty( '--header-spacer', headerHeight + 'px' );
	}

	// Remove sticky things.
	function unstick() {
		if ( ! stuckClassAdded ) {
			return;
		}
		body.classList.remove( 'header-stuck' );
		header.classList.remove( 'stuck' );
		stuckClassAdded = false;
		if ( ! hasShrinkHeader ) {
			return;
		}
		header.style.position = '';
		root.style.setProperty( '--header-spacer', '0' );
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
