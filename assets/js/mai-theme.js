/**
 * Body scroll lock.
 *
 *  @link    https://github.com/willmcpo/body-scroll-lock
 *
 * @version  2.6.1
 */
!function(e,t){if("function"==typeof define&&define.amd)define(["exports"],t);else if("undefined"!=typeof exports)t(exports);else{var o={};t(o),e.bodyScrollLock=o}}(this,function(exports){"use strict";function r(e){if(Array.isArray(e)){for(var t=0,o=Array(e.length);t<e.length;t++)o[t]=e[t];return o}return Array.from(e)}Object.defineProperty(exports,"__esModule",{value:!0});var l=!1;if("undefined"!=typeof window){var e={get passive(){l=!0}};window.addEventListener("testPassive",null,e),window.removeEventListener("testPassive",null,e)}var d="undefined"!=typeof window&&window.navigator&&window.navigator.platform&&/iP(ad|hone|od)/.test(window.navigator.platform),c=[],u=!1,a=-1,s=void 0,v=void 0,f=function(t){return c.some(function(e){return!(!e.options.allowTouchMove||!e.options.allowTouchMove(t))})},m=function(e){var t=e||window.event;return!!f(t.target)||(1<t.touches.length||(t.preventDefault&&t.preventDefault(),!1))},o=function(){setTimeout(function(){void 0!==v&&(document.body.style.paddingRight=v,v=void 0),void 0!==s&&(document.body.style.overflow=s,s=void 0)})};exports.disableBodyScroll=function(i,e){if(d){if(!i)return void console.error("disableBodyScroll unsuccessful - targetElement must be provided when calling disableBodyScroll on IOS devices.");if(i&&!c.some(function(e){return e.targetElement===i})){var t={targetElement:i,options:e||{}};c=[].concat(r(c),[t]),i.ontouchstart=function(e){1===e.targetTouches.length&&(a=e.targetTouches[0].clientY)},i.ontouchmove=function(e){var t,o,n,r;1===e.targetTouches.length&&(o=i,r=(t=e).targetTouches[0].clientY-a,!f(t.target)&&(o&&0===o.scrollTop&&0<r?m(t):(n=o)&&n.scrollHeight-n.scrollTop<=n.clientHeight&&r<0?m(t):t.stopPropagation()))},u||(document.addEventListener("touchmove",m,l?{passive:!1}:void 0),u=!0)}}else{n=e,setTimeout(function(){if(void 0===v){var e=!!n&&!0===n.reserveScrollBarGap,t=window.innerWidth-document.documentElement.clientWidth;e&&0<t&&(v=document.body.style.paddingRight,document.body.style.paddingRight=t+"px")}void 0===s&&(s=document.body.style.overflow,document.body.style.overflow="hidden")});var o={targetElement:i,options:e||{}};c=[].concat(r(c),[o])}var n},exports.clearAllBodyScrollLocks=function(){d?(c.forEach(function(e){e.targetElement.ontouchstart=null,e.targetElement.ontouchmove=null}),u&&(document.removeEventListener("touchmove",m,l?{passive:!1}:void 0),u=!1),c=[],a=-1):(o(),c=[])},exports.enableBodyScroll=function(t){if(d){if(!t)return void console.error("enableBodyScroll unsuccessful - targetElement must be provided when calling enableBodyScroll on IOS devices.");t.ontouchstart=null,t.ontouchmove=null,c=c.filter(function(e){return e.targetElement!==t}),u&&0===c.length&&(document.removeEventListener("touchmove",m,l?{passive:!1}:void 0),u=!1)}else 1===c.length&&c[0].targetElement===t?(o(),c=[]):c=c.filter(function(e){return e.targetElement!==t})}});

/**
 * Header shrink helper functions.
 * Everything here rebuilt for v1.4.0.
 *
 * @version  1.0.0
 */
( function( document, $, undefined ) {

	var $body       = $( 'body' );
		$window     = $(window),
		$header     = $( '.site-header' ),
		$customLogo = $header.find( '.custom-logo-link' ),
		$titleText  = $header.find( '.site-title a' ).not( '.custom-logo-link' );

	var hasStickyShrink = $body.hasClass( 'has-sticky-shrink-header' ),
		hasReveal       = $body.hasClass( 'has-reveal-header' ),
		hasRevealShrink = $body.hasClass( 'has-reveal-shrink-header' ),
		fontSize        = parseInt( $titleText.css( 'font-size' ) ),
		logoWidth       = $customLogo.outerWidth();

	// Add scroll class.
	$window.on( 'resize scroll', function() {

		var scrollClassAdded = false;

		// Shrink the header on scroll.
		if ( $window.scrollTop() > 1 ) {

			// Bail if scroll class added.
			if ( scrollClassAdded ) {
				return;
			}

			$body.addClass( 'scroll' );

			scrollClassAdded = true;

		} else {

			$body.removeClass( 'scroll' );
		}
	});

	// If doing a sticky shrink header.
	if ( hasStickyShrink ) {

		var	shrinkFired      = false,
			unshrinkFired    = false,
			titleShrinkFired = false;

		// On resize and/or scroll.
		$( window ).on( 'resize scroll', function() {

			var windowWidth = $window.width();

			// Larger browser windows.
			if ( $window.width() > 768 ) {

				// Shrink/Unshrink triggers.
				if ( $window.scrollTop() > 1 ) {
					if ( false !== shrinkFired ) {
						return;
					}
					shrinkHeader();
					shrinkFired   = true;
					unshrinkFired = false;
				} else {
					if ( false !== unshrinkFired ) {
						return;
					}
					unshrinkHeader();
					unshrinkFired = true;
					shrinkFired   = false;
				}

				// Unset this incase browser resized small to large.
				titleShrinkFired = false;

			}
			// Smaller browser windows.
			else {

				if ( ! titleShrinkFired ) {

					// Force shrink text on wall windows.
					shrinkTitle();
					shrinkLogo();
				}

				titleShrinkFired = true;
			}

		});

	}
	// If doing reveal header.
	else if ( hasReveal ) {

		// Get the header.
		var $siteHeader = document.querySelector( 'header' );

		/**
 		 * headroom.js v0.9.4 - Give your page some headroom. Hide your header until you need it.
 		 * Copyright (c) 2017 Nick Williams - http://wicky.nillia.ms/headroom.js
		 * License: MIT
		 */
		!function(a,b){"use strict";"function"==typeof define&&define.amd?define([],b):"object"==typeof exports?module.exports=b():a.Headroom=b()}(this,function(){"use strict";function a(a){this.callback=a,this.ticking=!1}function b(a){return a&&"undefined"!=typeof window&&(a===window||a.nodeType)}function c(a){if(arguments.length<=0)throw new Error("Missing arguments in extend function");var d,e,f=a||{};for(e=1;e<arguments.length;e++){var g=arguments[e]||{};for(d in g)"object"!=typeof f[d]||b(f[d])?f[d]=f[d]||g[d]:f[d]=c(f[d],g[d])}return f}function d(a){return a===Object(a)?a:{down:a,up:a}}function e(a,b){b=c(b,e.options),this.lastKnownScrollY=0,this.elem=a,this.tolerance=d(b.tolerance),this.classes=b.classes,this.offset=b.offset,this.scroller=b.scroller,this.initialised=!1,this.onPin=b.onPin,this.onUnpin=b.onUnpin,this.onTop=b.onTop,this.onNotTop=b.onNotTop,this.onBottom=b.onBottom,this.onNotBottom=b.onNotBottom}var f={bind:!!function(){}.bind,classList:"classList"in document.documentElement,rAF:!!(window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame)};return window.requestAnimationFrame=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame,a.prototype={constructor:a,update:function(){this.callback&&this.callback(),this.ticking=!1},requestTick:function(){this.ticking||(requestAnimationFrame(this.rafCallback||(this.rafCallback=this.update.bind(this))),this.ticking=!0)},handleEvent:function(){this.requestTick()}},e.prototype={constructor:e,init:function(){if(e.cutsTheMustard)return this.debouncer=new a(this.update.bind(this)),this.elem.classList.add(this.classes.initial),setTimeout(this.attachEvent.bind(this),100),this},destroy:function(){var a=this.classes;this.initialised=!1;for(var b in a)a.hasOwnProperty(b)&&this.elem.classList.remove(a[b]);this.scroller.removeEventListener("scroll",this.debouncer,!1)},attachEvent:function(){this.initialised||(this.lastKnownScrollY=this.getScrollY(),this.initialised=!0,this.scroller.addEventListener("scroll",this.debouncer,!1),this.debouncer.handleEvent())},unpin:function(){var a=this.elem.classList,b=this.classes;!a.contains(b.pinned)&&a.contains(b.unpinned)||(a.add(b.unpinned),a.remove(b.pinned),this.onUnpin&&this.onUnpin.call(this))},pin:function(){var a=this.elem.classList,b=this.classes;a.contains(b.unpinned)&&(a.remove(b.unpinned),a.add(b.pinned),this.onPin&&this.onPin.call(this))},top:function(){var a=this.elem.classList,b=this.classes;a.contains(b.top)||(a.add(b.top),a.remove(b.notTop),this.onTop&&this.onTop.call(this))},notTop:function(){var a=this.elem.classList,b=this.classes;a.contains(b.notTop)||(a.add(b.notTop),a.remove(b.top),this.onNotTop&&this.onNotTop.call(this))},bottom:function(){var a=this.elem.classList,b=this.classes;a.contains(b.bottom)||(a.add(b.bottom),a.remove(b.notBottom),this.onBottom&&this.onBottom.call(this))},notBottom:function(){var a=this.elem.classList,b=this.classes;a.contains(b.notBottom)||(a.add(b.notBottom),a.remove(b.bottom),this.onNotBottom&&this.onNotBottom.call(this))},getScrollY:function(){return void 0!==this.scroller.pageYOffset?this.scroller.pageYOffset:void 0!==this.scroller.scrollTop?this.scroller.scrollTop:(document.documentElement||document.body.parentNode||document.body).scrollTop},getViewportHeight:function(){return window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight},getElementPhysicalHeight:function(a){return Math.max(a.offsetHeight,a.clientHeight)},getScrollerPhysicalHeight:function(){return this.scroller===window||this.scroller===document.body?this.getViewportHeight():this.getElementPhysicalHeight(this.scroller)},getDocumentHeight:function(){var a=document.body,b=document.documentElement;return Math.max(a.scrollHeight,b.scrollHeight,a.offsetHeight,b.offsetHeight,a.clientHeight,b.clientHeight)},getElementHeight:function(a){return Math.max(a.scrollHeight,a.offsetHeight,a.clientHeight)},getScrollerHeight:function(){return this.scroller===window||this.scroller===document.body?this.getDocumentHeight():this.getElementHeight(this.scroller)},isOutOfBounds:function(a){var b=a<0,c=a+this.getScrollerPhysicalHeight()>this.getScrollerHeight();return b||c},toleranceExceeded:function(a,b){return Math.abs(a-this.lastKnownScrollY)>=this.tolerance[b]},shouldUnpin:function(a,b){var c=a>this.lastKnownScrollY,d=a>=this.offset;return c&&d&&b},shouldPin:function(a,b){var c=a<this.lastKnownScrollY,d=a<=this.offset;return c&&b||d},update:function(){var a=this.getScrollY(),b=a>this.lastKnownScrollY?"down":"up",c=this.toleranceExceeded(a,b);this.isOutOfBounds(a)||(a<=this.offset?this.top():this.notTop(),a+this.getViewportHeight()>=this.getScrollerHeight()?this.bottom():this.notBottom(),this.shouldUnpin(a,c)?this.unpin():this.shouldPin(a,c)&&this.pin(),this.lastKnownScrollY=a)}},e.options={tolerance:{up:0,down:0},offset:0,scroller:window,classes:{pinned:"headroom--pinned",unpinned:"headroom--unpinned",top:"headroom--top",notTop:"headroom--not-top",bottom:"headroom--bottom",notBottom:"headroom--not-bottom",initial:"headroom"}},e.cutsTheMustard="undefined"!=typeof f&&f.rAF&&f.bind&&f.classList,e});

		var options = {
			offset: 256,
			tolerance: {
				down: 5,
				up:   15
			},
			classes: {
				initial:  "",
				pinned:   "reveal-header",
				unpinned: "conceal-header",
				top:      "top",
				notTop:   "not-top",
			}
		}

		// Construct an instance of Headroom, passing the element.
		var headroom = new Headroom( $siteHeader, options );

		// Initialise.
		headroom.init();

		var shouldNotScroll = false;

		/**
 		 * Temporarily disable the scroll function
		 * when clicking on anything in the header.
		 *
		 * If the mobile menu is activated, destroy headroom.
		 */
		$header.on( 'click', function() {

			shouldNotScroll = true;
			setTimeout( function() {
				shouldNotScroll = false;
			}, 300 );

			// If mobile menu is activated.
			if ( $body.hasClass( 'mai-menu-activated' ) ) {

				// Deactivate headroom.
				headroom.destroy();
			}
			// If mobile menu not activated and headroom is not initialized.
			else if ( ! $header.hasClass( 'headroom' ) ) {

				// Initialize.
				setTimeout( function() {
					headroom.init();
				}, 300 );
			}
		});

		// Bail if not shrinking.
		if ( ! hasRevealShrink ) {
			return;
		}

		$window.on( 'resize scroll', function() {

			// Bail if not monitoring scroll.
			if ( shouldNotScroll ) {
				return;
			}

			// Bail if the mobile menu is open. Typically when scrolling with mobile menu open.
			if ( $body.hasClass( 'mai-menu-activated' ) ) {
				return;
			}

			// Bail if smaller window, since everything will always be shrunk.
			if ( $window.width() <= 768 ) {
				return;
			}

			// Current scroll position and window width.
			var scrollTop = $window.scrollTop();

			// Scrolled to the top.
			if ( scrollTop <= 1 ) {
				unshrinkHeader();
			}
			// Scrolling either direction and header is not shrunk.
			else if ( ! $header.hasClass( 'shrink' ) ) {
				shrinkHeader();
			}

		});

	}

	/**
	 * Resize logo and title.
	 */
	function reSize() {
		// Bigger windows and not shrunk.
		if ( $window.width() > 768 && ! $header.hasClass( 'shrink' )  ) {
			// Show normal size.
			unshrinkLogo();
			unshrinkTitle();
		}
		// Smaller windows.
		else {
			// Show smaller size.
			shrinkLogo();
			shrinkTitle();
		}
	}

	/* ****** *
	 * Shrink *
	 * ****** */

	function shrinkHeader() {
		$header.addClass( 'shrink' );
		shrinkLogo();
		shrinkTitle();
	}

	function shrinkLogo() {
		if ( ! $customLogo.length ) {
			return;
		}
		$customLogo.css({ maxWidth: logoWidth * .7 });
	}

	function shrinkTitle() {
		if ( ! $titleText.length ) {
			return;
		}
		$titleText.css({ fontSize: fontSize * .8 });
	}

	/* ******** *
	 * Unshrink *
	 * ******** */

	function unshrinkHeader() {
		$header.removeClass( 'shrink' );
		unshrinkLogo();
		unshrinkTitle();
	}

	function unshrinkLogo() {
		if ( ! $customLogo.length ) {
			return;
		}
		$customLogo.css({ maxWidth: logoWidth });
	}

	function unshrinkTitle() {
		if ( ! $titleText.length ) {
			return;
		}
		$titleText.css({ fontSize: fontSize });
	}

})( document, jQuery );


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
		$siteHeader    = $( '.site-header' ),
		$siteHeaderRow = $( '.site-header-row' ),
		$maiMenus      = $( '.mai-menu .menu' ),
		$maiSubToggles = $( '.mai-menu .sub-menu-toggle' ),
		$maiSubMenus   = $( '.mai-menu .sub-menu' );

	// Add the main nav and sub-menu toggle button.
	_addMenuButtons();

	// Remove classes that may unintentially inherit styling.
	$maiMenus.removeClass( 'nav-header nav-primary nav-secondary' );

	// Toggle triggers.
	$siteHeader.on( 'click', '.mai-toggle', _doToggleMenu );
	$maiMenu.on( 'click', '.sub-menu-toggle:not(.sub-sub-menu-toggle)', _doToggleSubMenu );
	$maiMenu.on( 'click', '.sub-menu-toggle.sub-sub-menu-toggle', _doToggleSubSubMenu );

	// Resize.
	$window.on( 'load resize', function(e) {
		_maybeCloseAll();
		_changeSkipLink();
	});

	/**
	 * Add toggle buttons.
	 */
	function _addMenuButtons() {

		// Add the main mobile nav toggle.
		$siteHeaderRow.append( $maiToggle );

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

		// Get a target element that you want to persist scrolling for (such as a modal/lightbox/flyout/nav).
		var targetElement = document.querySelector( '#mai-menu' );

		// If opening the menu.
		if ( $body.hasClass( 'mai-menu-activated' ) ) {

			// Disable body scroll (stupid iOS) while allowing the menu to scroll.
			bodyScrollLock.disableBodyScroll( targetElement );

			if ( ! hasSideMenu ) {

				// Set max-height as window height minus header height.
				$maiMenu.css( 'max-height', $window.height() - $siteHeader.height() + 'px' );

				// Set max-height if window is resized.
				$window.on( 'resize', function(e) {
					$maiMenu.css( 'max-height', $window.height() - $siteHeader.height() + 'px' );
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

			// Re-enable body scroll.
			bodyScrollLock.enableBodyScroll( targetElement );

			if ( ! hasSideMenu ) {

				// Remove inline styles.
				$maiMenu.css( 'max-height', '' );
			}

			_closeAll();
		}

		// On click of close button inside the side menu, close all.
		$siteHeader.on( 'click', '.menu-close', function(e){

			// Re-enable body scroll.
			bodyScrollLock.enableBodyScroll( targetElement );

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
 * Set an elements min-height
 * according to the aspect ratio of its' background image.
 *
 * @version  2.1.0
 */
( function( window, document, $, undefined ) {

	// Get all our elements.
	var elements = document.querySelectorAll( '.aspect-ratio' );

	// Bail if no elements.
	if ( 0 === elements.length ) {
		return;
	}

	// Resize after the window is ready. WP Rocket critical CSS needs this to wait, among other things.
	window.addEventListener( 'load', aspectRatio );
	window.addEventListener( 'resize', aspectRatio );

	// After FacetWP is loaded/refreshed. We needed to get the elements again because of the way FWP re-displays them.
	$( document ).on( 'facetwp-loaded', function() {
		aspectRatio();
	});

	// Helper function to loop through the elements and set the aspect ratio.
	function aspectRatio() {
		forEach( elements, function( index, value ) {
			return value.style.minHeight = Math.round( value.offsetWidth / ( value.getAttribute( 'data-aspect-width' ) / value.getAttribute('data-aspect-height') ) ) + 'px';
		});
	}

	// Thanks Todd! @link https://toddmotto.com/ditch-the-array-foreach-call-nodelist-hack/
	var forEach = function( array, callback, scope ) {
		for ( var i = 0; i < array.length; i++ ) {
			// Passes back stuff we need.
			callback.call( scope, i, array[i] );
		}
	};

})( window, document, jQuery );


/**
 * Scroll to a div id.
 *
 * Link
 * <a class="scroll-to" href="#element-id">Text</a>
 *
 * Target
 * <div id="element-id">Content</div>
 */
( function ( document, $, undefined ) {

	$( 'body' ).on( 'click', '.scroll-to', function(event) {
		var target = $(this.getAttribute('href'));
		if( target.length ) {
			event.preventDefault();
			$('html, body').stop().animate({
				scrollTop: target.offset().top - 120
			}, 1000 );
		}
	});

})( document, jQuery );


/**
 * Initialise Superfish with custom arguments.
 *
 * @package Genesis\JS
 * @author StudioPress
 * @license GPL-2.0+
 */
( function( document, $, undefined ) {

	$( '.js-superfish' ).superfish({
		'delay': 100,
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
function _maiGlobalFunctions(){

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

}
