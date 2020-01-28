/**
 * Body scroll lock.
 *
 * @link https://github.com/willmcpo/body-scroll-lock
 *
 * @version 2.6.3
 */
!function(e,t){if("function"==typeof define&&define.amd)define(["exports"],t);else if("undefined"!=typeof exports)t(exports);else{var o={};t(o),e.bodyScrollLock=o}}(this,function(exports){"use strict";function r(e){if(Array.isArray(e)){for(var t=0,o=Array(e.length);t<e.length;t++)o[t]=e[t];return o}return Array.from(e)}Object.defineProperty(exports,"__esModule",{value:!0});var l=!1;if("undefined"!=typeof window){var e={get passive(){l=!0}};window.addEventListener("testPassive",null,e),window.removeEventListener("testPassive",null,e)}var d="undefined"!=typeof window&&window.navigator&&window.navigator.platform&&/iP(ad|hone|od)/.test(window.navigator.platform),c=[],u=!1,a=-1,s=void 0,v=void 0,f=function(t){return c.some(function(e){return!(!e.options.allowTouchMove||!e.options.allowTouchMove(t))})},m=function(e){var t=e||window.event;return!!f(t.target)||(1<t.touches.length||(t.preventDefault&&t.preventDefault(),!1))},o=function(){setTimeout(function(){void 0!==v&&(document.body.style.paddingRight=v,v=void 0),void 0!==s&&(document.body.style.overflow=s,s=void 0)})};exports.disableBodyScroll=function(i,e){if(d){if(!i)return void console.error("disableBodyScroll unsuccessful - targetElement must be provided when calling disableBodyScroll on IOS devices.");if(i&&!c.some(function(e){return e.targetElement===i})){var t={targetElement:i,options:e||{}};c=[].concat(r(c),[t]),i.ontouchstart=function(e){1===e.targetTouches.length&&(a=e.targetTouches[0].clientY)},i.ontouchmove=function(e){var t,o,n,r;1===e.targetTouches.length&&(o=i,r=(t=e).targetTouches[0].clientY-a,!f(t.target)&&(o&&0===o.scrollTop&&0<r?m(t):(n=o)&&n.scrollHeight-n.scrollTop<=n.clientHeight&&r<0?m(t):t.stopPropagation()))},u||(document.addEventListener("touchmove",m,l?{passive:!1}:void 0),u=!0)}}else{n=e,setTimeout(function(){if(void 0===v){var e=!!n&&!0===n.reserveScrollBarGap,t=window.innerWidth-document.documentElement.clientWidth;e&&0<t&&(v=document.body.style.paddingRight,document.body.style.paddingRight=t+"px")}void 0===s&&(s=document.body.style.overflow,document.body.style.overflow="hidden")});var o={targetElement:i,options:e||{}};c=[].concat(r(c),[o])}var n},exports.clearAllBodyScrollLocks=function(){d?(c.forEach(function(e){e.targetElement.ontouchstart=null,e.targetElement.ontouchmove=null}),u&&(document.removeEventListener("touchmove",m,l?{passive:!1}:void 0),u=!1),c=[],a=-1):(o(),c=[])},exports.enableBodyScroll=function(t){if(d){if(!t)return void console.error("enableBodyScroll unsuccessful - targetElement must be provided when calling enableBodyScroll on IOS devices.");t.ontouchstart=null,t.ontouchmove=null,c=c.filter(function(e){return e.targetElement!==t}),u&&0===c.length&&(document.removeEventListener("touchmove",m,l?{passive:!1}:void 0),u=!1)}else(c=c.filter(function(e){return e.targetElement!==t})).length||o()}});

/**
 * This script adds the accessibility-ready responsive menu.
 * Loosely off https://github.com/copyblogger/responsive-menus.
 *
 * Props @robincornett for some help/code.
 *
 * @version  2.0.0
 */
( function( window, document, $, undefined ) {

	var $maiMenu = $( '.mai-menu' );

	// Bail if no menu.
	if ( ! $maiMenu.length ) {
		return;
	}

	_maiGlobalFunctions();

	// Build toggle buttons.
	var $maiToggle = $( '<button />', {
			'id' : 'mai-toggle',
			'class' : 'mai-toggle',
			'aria-expanded' : false,
			'aria-pressed' : false,
			'role' : 'button'
		}).append( '<span class="screen-reader-text">' + maiVars.mainMenu + '</span><span class="mai-bars"></span></span>' );

	var $maiSubToggle = $( '<button />', {
			'class' : 'sub-menu-toggle',
			'aria-expanded' : false,
			'aria-pressed' : false,
			'role' : 'button'
		}).append( '<span class="screen-reader-text">' + maiVars.subMenu + '</span>' );

	// Set vars.
	var $window        = $(window),
		$body          = $( 'body' ),
		$header        = $( '.site-header' ),
		$headerRow     = $( '.site-header-row' ),
		$maiMenus      = $( '.mai-menu .menu' ),
		$maiSubToggles = $( '.mai-menu .sub-menu-toggle' ),
		$maiSubMenus   = $( '.mai-menu .sub-menu' );

	// Get a target element that you want to persist scrolling for (such as a modal/lightbox/flyout/nav).
	var bodyLockElement = document.querySelector( '#mai-menu' );

	// Add the main nav and sub-menu toggle button.
	_addMenuButtons();

	// Remove classes that may unintentially inherit styling.
	$maiMenus.removeClass( 'nav-header nav-primary nav-secondary' );

	// Toggle triggers.
	$header.on( 'click', '.mai-toggle', _doToggleMenu );
	$maiMenu.on( 'click', '.sub-menu-toggle:not(.sub-sub-menu-toggle)', _doToggleSubMenu );
	$maiMenu.on( 'click', '.sub-menu-toggle.sub-sub-menu-toggle', _doToggleSubSubMenu );
	$maiMenu.on( 'click', 'a[href]', _doAnchorLinkClicked );

	// Resize.
	$window.on( 'load resize orientationchange', function(e) {
		_maybeCloseAll();
		_changeSkipLink();
	});

	/**
	 * Add toggle buttons.
	 */
	function _addMenuButtons() {

		// Add the main mobile nav toggle.
		$headerRow.append( $maiToggle );

		// Bail if no menus in the mobile menu. It could just be widget content.
		if ( 0 == $maiMenus.length ) {
			return;
		}

		// Add the responsive menu class to the menus.
		$.each( $maiMenus, function(e) {
			$(this).addClass( 'mobile-menu' );
		});

		// Add the submenu toggles.
		$maiSubMenus.before( $maiSubToggle );

		$( '.sub-menu .sub-menu-toggle' ).addClass( 'sub-sub-menu-toggle' );
	}

	/**
	 * Action to happen when the main menu button is clicked.
	 */
	function _doToggleMenu() {

		var $this       = $(this),
			hasSideMenu = $body.hasClass( 'has-side-menu' );

		// Remove reveal/conceal classes cause they cause animation issues.
		$header.removeClass( 'conceal-header reveal-header' );

		// Toggle the mobile menu activated.
		$this._toggleActive();

		// Activated body class.
		$body.toggleClass( 'mai-menu-activated' );

		// If we have a side menu.
		if ( hasSideMenu ) {
			// Side menu activated class.
			$body.toggleClass( 'mai-side-menu-activated' );
		}
		// Standard menu.
		else {
			// Standard menu activated class.
			$body.toggleClass( 'mai-standard-menu-activated' );
		}

		// If opening the menu.
		if ( $body.hasClass( 'mai-menu-activated' ) ) {

			// Disable body scroll (stupid iOS) while allowing the menu to scroll.
			bodyScrollLock.disableBodyScroll( bodyLockElement );

			if ( ! hasSideMenu ) {

				// Set max-height as window height minus header height.
				$maiMenu.css( 'max-height', $window.height() - $header.height() + 'px' );

				// Set max-height if window is resized.
				$window.on( 'resize orientationchange', function(e) {
					$maiMenu.css( 'max-height', $window.height() - $header.height() + 'px' );
				});
			}

			// Allow additional keyboard nav.
			$(document).keydown( function(e) {

				// Use switch to easily add new keystrokes.
				switch(e.which) {
					case 27: // esc.
						// Close popup with esc key.
						_closeAll();
						break;
					default: return; // Exit this handler for other keys.
				}

				// Prevent the default action (scroll/move caret).
				e.preventDefault();
			});

		}
		// Closing the menu.
		else {

			if ( ! hasSideMenu ) {
				// Remove inline styles.
				$maiMenu.css( 'max-height', '' );
			}

			_closeAll();
		}

		// On click of close button inside the side menu, close all.
		$body.on( 'click', '.menu-close', function(e){
			_closeAll();
		});

	}

	/**
	 * Action for sub-menu toggles.
	 */
	function _doToggleSubMenu() {
		$(this)._toggleSubMenu();
		$( '.sub-menu-toggle.activated' ).not( $(this) )._closeSubMenu();
	}

	/**
	 * Action for nested sub-menu toggles.
	 */
	function _doToggleSubSubMenu() {
		$(this)._toggleSubMenu();
	}

	/**
	 * Handle scroll-to anchor links in mobile menu.
	 */
	function _doAnchorLinkClicked() {
		var $target = _maiGetHashElement( $(this) );
		if ( ! $target ) {
			return;
		}
		_closeAll();
	}

	/**
	 * Modify skip link to match mobile buttons.
	 */
	function _changeSkipLink() {

		// Get vars.
		var $skipLinksUL    = $( '.genesis-skip-link' ),
			$mobileSkipLink = $( '.genesis-skip-link a[href="#mai-toggle"]' ),
			$menuSkipLinks  = $( '.genesis-skip-link a[href*="#genesis-nav"]' );

		// Whether mobile menu toggle is visible.
		var toggleDisplay = _getDisplayValue( $maiToggle );

		// If mai-toggle skip link is not created yet.
		if ( 0 == $mobileSkipLink.length ) {
			$skipLinksUL.prepend( '<li><a href="#mai-toggle" class="screen-reader-shortcut"> ' + maiVars.mainMenu + '</a></li>' );
			$mobileSkipLink = $( '.genesis-skip-link a[href="#mobile-nav"]' );
		}

		// If mai-toggle is not visible.
		if ( 'none' == toggleDisplay ) {
			$mobileSkipLink.addClass( 'skip-link-hidden' );
		}
		// Visible.
		else {
			$mobileSkipLink.removeClass( 'skip-link-hidden' );
		}

		// Manage skip link visibility.
		$.each( $menuSkipLinks, function () {

			var $this = $(this);

			if ( 'none' == toggleDisplay ) {
				$this.removeClass( 'skip-link-hidden' );
			} else {
				$this.addClass( 'skip-link-hidden' );
			}

		});
	}

	/**
	 * Maybe close all the things.
	 */
	function _maybeCloseAll() {

		if ( 'none' !== _getDisplayValue( $maiToggle ) ) {
			return true;
		}

		_closeAll();
	}

	/**
	 * Close all the things.
	 */
	function _closeAll() {

		$body.removeClass( 'mai-menu-activated mai-standard-menu-activated mai-side-menu-activated' )

		$maiToggle._closeElement();
		$( '.sub-menu-toggle.activated' )._closeSubMenu();

		// Re-enable body scroll.
		bodyScrollLock.enableBodyScroll( bodyLockElement );
	}

	/**
	 * Get the display value of an element.
	 */
	function _getDisplayValue( $element ) {
		return $element.css( 'display' );
	}

	$.fn._toggleSubMenu = function(){
		$(this)._toggleActive().next( '.sub-menu' ).slideToggle( 'fast' );
		return $(this);
	};

	$.fn._closeSubMenu = function(){
		$(this)._closeElement().next( '.sub-menu' ).slideUp( 'fast' );
		return $(this);
	};

})( window, document, jQuery );


/**
 * Convert menu items with .search class to a search icon with a fade in search box.
 * Show/hide search box on click, and allow closing by clicking outside of search box.
 *
 * @version  2.0.0
 */
( function( document, $, undefined ) {

	var $navMenu    = $( '.genesis-nav-menu' ),
		$searchItem = $navMenu.children( '.search' );

	// Bail if no search items.
	if ( 0 === $searchItem.length ) {
		return;
	}

	_maiGlobalFunctions();

	$searchItem.html( '<button class="nav-search" aria-expanded="false" aria-pressed="false"><span class="search-icon"></span><span class="screen-reader-text">' + $searchItem.text() + '</span></button>' ).show();

	// Add the search box after the link.
	$searchItem.append( maiVars.searchBox );

	$navMenu.on( 'click', '.nav-search', function(e){

		$searchButton = $(this);

		// If already opened.
		if ( $searchButton.hasClass( 'activated' ) ) {

			$searchButton._searchClose();

		}
		// Closing.
		else {

			// Close other search boxes.
			$( '.nav-search' ).not( $searchButton )._searchClose();

			$searchButton._searchOpen();

			// Close search listener.
			$( 'body' ).mouseup( function(e){
				/**
				 * Bail if:
				 * If click is on our search box container.
				 * If click is on a child of our search box container.
				 */
				if ( $(this).hasClass( 'search-box' ) || ( $searchItem.has( e.target ).length ) ) {
					return;
				}
				$searchButton._searchClose();
			});

			// Close search if esc key pressed.
			$(document).keydown( function(e){

				// Use switch to easily add new keystrokes.
				switch(e.which) {
					case 27: // esc.
					$searchButton._searchClose();
					break;
					// Exit this handler for other keys.
					default: return;
				}
			});

		}

	});

	$.fn._searchOpen = function(){
		var $this = $(this);
		$this._openElement().next( '.search-box' ).fadeIn( 'fast' ).find( 'input[type="search"]' ).focus();
		return $this;
	};

	$.fn._searchClose = function(){
		var $this = $(this);
		$this._closeElement().removeClass( 'activated' ).next( '.search-box' ).fadeOut( 'fast' );
		return $this;
	};

})( document, jQuery );


/**
 * Scroll to a div id.
 *
 * Link
 * <a class="scroll-to" href="#element-id">Text</a>
 *
 * Or Custom Link in menu, just add scroll-to class.
 *
 * Target
 * <div id="element-id">Content</div>
 */
( function ( document, $, undefined ) {

	var $html       = $( 'html' );
	var $body       = $( 'body' );
	var hasSticky   = $body.hasClass( 'has-sticky-header' );
	var hasReveal   = $body.hasClass( 'has-reveal-header' );
	var needsOffset = false;

	// _maiGlobalFunctions();

	// On click of scroll-to elements.
	$body.on( 'click', maiVars.maiScrollTo, function(e) {

		// Get the link element.
		var $link = $(this);
		if ( $link.hasClass( 'menu-item' ) ) {
			$link = $link.find( 'a' );
		}

		// Bail if no link.
		if ( ! $link.length ) {
			return;
		}

		// Get the target element.
		var $target = _maiGetHashElement( $link );

		// Bail if no target element.
		if ( ! $target ) {
			return;
		}

		e.preventDefault();

		// Build initial offset.
		var offset = $target.offset().top - parseInt( $html.css( 'marginTop' ) ) - 16;

		// Handle header offset if sticky or reveal.
		if ( hasSticky ) {
			// Offset includes header height if we have a header.
			var $header = $( '.site-header' );
			var $height = $header.length ? $header.outerHeight() : 0;
			offset = offset - $height;
		} else if ( hasReveal ) {
			var $header = $( '.site-header' );
			var $height = $header.length ? $header.outerHeight() : 0;
			// Scrolling down.
			if ( $(this).offset().top < $target.offset().top ) {
				var distance = $target.offset().top - $(this).offset().top;
				// If scrolling enough. This is the same distance in scrolledDownEnough() in mai-scroll.js.
				if ( distance < 320 ) {
					offset = offset - $height;
				}
			}
			// Srolling up.
			else {
				var distance = $(this).offset().top - $target.offset().top;
				// If scrolling enough. This is the same distance in scrolledUpEnough() in mai-scroll.js.
				if ( distance > 160 ) {
					offset = offset - $height;
				}
			}
		}

		// Animate scroll to offset.
		$( 'html, body' ).stop().animate({ scrollTop: offset }, 500 );

	});

})( document, jQuery );


/**
 * Get the element from a link's hash.
 *
 * @return  false|Object  false or the target element.
 */
function _maiGetHashElement( $link ) {

	var $ = jQuery;

	// Get the href.
	var href = $link.attr( 'href' );

	// Bail if no href.
	if ( 'undefined' == typeof href || href.length <= 1 ) {
		return false;
	}

	// Get the hash.
	var hash = href.substring( href.indexOf('#') );

	// Bail if no hash.
	if ( ! hash.length ) {
		return false;
	}

	/**
	 * Bail if the link is external.
	 *
	 * @link  https://stackoverflow.com/questions/2910946/test-if-links-are-external-with-jquery-javascript/18660968#18660968
	 */
	if ( $link.prop( 'host' ) !== window.location.host ) {
		return false;
	}

	/**
	 * Bail if the link is not on the same page.
	 *
	 * @link  https://stackoverflow.com/questions/33818702/jquery-how-to-test-if-link-goes-to-anchor-on-same-page
	 */
	if ( href.indexOf( window.location.href ) > -1 ) {
		return false;
	}

	// Get target element.
	var $target = $( hash );

	// Bail if no target element.
	if ( ! $target.length ) {
		return false;
	}

	return $target;
}

/**
 * Initialise Superfish with custom arguments.
 *
 * @package Genesis\JS
 * @author StudioPress
 * @license GPL-2.0+
 */
( function( document, $, undefined ) {

	var $superfish = $( '.js-superfish' );

	// Bail if no object.
	if ( ! $superfish.length ) {
		return;
	}

	// Bail if superfish function does not exist.
	if ( 'function' !== typeof $superfish.superfish ) {
		return;
	}

	$superfish.superfish({
		'delay': 1000,
		'speed': 'fast',
		'speedOut': 'slow',
		'disableHI': true,
	});

})( document, jQuery );

/**
 * Build some helper functions.
 *
 * @access  private.
 */
function _maiGlobalFunctions() {

	var $ = jQuery;

	$.fn._toggleActive = function(){
		var $this = $(this);
		$this._toggleArias().toggleClass( 'activated' );
		return $this;
	};

	$.fn._toggleArias = function(){
		var $this = $(this);
		$this.attr({
			'aria-expanded': 'false' === $this.attr( 'aria-expanded' ),
			'aria-pressed': 'false' === $this.attr( 'aria-pressed' ),
		});
		return $this;
	};

	$.fn._openElement = function(){
		var $this = $(this);
		$this.addClass( 'activated' ).attr({
			'aria-expanded': true,
			'aria-pressed': true,
		});
		return $this;
	};

	$.fn._closeElement = function(){
		var $this = $(this);
		$this.removeClass( 'activated' ).attr({
			'aria-expanded': false,
			'aria-pressed': false,
		});
		return $this;
	};

	$.fn._linkContainsValidScrollToHash = function(){
		var $this = $(this);
		return $this;
	};

}
