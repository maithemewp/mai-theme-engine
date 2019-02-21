// Set consts.
const html            = document.querySelector( 'html' );
const body            = document.querySelector( 'body' );
const siteHeader      = document.querySelector( '.site-header' );
const logoWidth       = maiScroll.logoWidth;
const shrunkLogoWidth = Math.round( logoWidth * .7 );
const hasShrinkHeader = body.classList.contains( 'has-shrink-header' );
const hasRevealHeader = body.classList.contains( 'has-reveal-header' );

// Set vars.
var scrollClassAdded   = false,
	scrollingDown      = true,
	scrollingUp        = false,
	headerConcealed    = false,
	startDistance      = false,
	percentage         = 0,
	previousPercentage = 0,
	scrollDowns        = [];
	shrinkClassAdded   = false,
	afterHeader        = false;

/**
 * Handle body scroll tracking.
 *
 * @version  1.0.0
 */
const bodyScroll = basicScroll.create({
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

		// Maybe set the new start distance.
		if ( false === startDistance ) {
			startDistance = document.documentElement.scrollTop;
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

		// Check if scrolled enough.
		hasScrolledEnough = scrolledEnough( startDistance, document.documentElement.scrollTop, scrollingDown );

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
		if ( ( percentage <= 0 ) && scrollClassAdded ) {
			removeScrollClass();
		}
		if ( headerConcealed ) {
			revealHeader();
		}
	}
})
.start();

/**
 * Handle header scroll tracking.
 * This is always running, just not always outputting CSS vars.
 *
 * @version  1.0.0
 */
const headerScroll = basicScroll.create({
	elem: siteHeader,
	from: parseInt( window.getComputedStyle( html ).marginTop ),
	to: parseInt( window.getComputedStyle( html ).marginTop ) + 200,
	props: hasShrinkHeader ? {
		'--logo-width': {
			from: logoWidth + 'px',
			to: shrunkLogoWidth + 'px',
		},
		'--logo-margin-top': {
			from: hasShrinkHeader ? '24px' : '4px',
			to: '4px',
		},
		'--logo-margin-bottom': {
			from: hasShrinkHeader ? '24px' : '4px',
			to: '4px',
		}
	} : [],
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
})
.start();

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
	siteHeader.classList.add( 'shrink' );
	shrinkClassAdded = true;
}

// Remove shrink.
function removeShrinkClass() {
	siteHeader.classList.remove( 'shrink' );
	shrinkClassAdded = false;
}

// Conceal the header.
function concealHeader() {
	siteHeader.classList.remove( 'reveal-header' );
	siteHeader.classList.add( 'conceal-header' );
	headerConcealed = true;
}

// Reveal the header.
function revealHeader() {
	siteHeader.classList.remove( 'conceal-header' );
	siteHeader.classList.add( 'reveal-header' );
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
