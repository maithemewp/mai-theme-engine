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

	/**
	 * Set initial inline width.
	 * This seems to help with jitters on first scroll.
	 */
	reSize();

	/**
	 * Resize logo/title when resizing the browser window.
	 */
	$window.on( 'resize', function() {
		reSize();
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
					// console.log( 'shrink' );
					shrinkHeader();
					shrinkFired   = true;
					unshrinkFired = false;
				} else {
					if ( false !== unshrinkFired ) {
						return;
					}
					// console.log( 'unshrink' );
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

		// Bail if not shrinking.
		if ( ! hasRevealShrink ) {
			return;
		}

		var lastScrollTop   = $body.scrollTop(),
			shouldNotScroll = false,
			threshold       = 5;

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
			// If mobile menu not activated and headroom is not initiatilized.
			else if ( ! $header.hasClass( 'headroom' ) ) {
				// Initialize.
				setTimeout( function() {
					headroom.init();
				}, 300 );
			}
		});

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
			if ( 0 === scrollTop ) {
				unshrinkHeader();
			}
			// Scrolling either direction and header is not shrunk.
			else if ( ! $header.hasClass( 'shrink' ) ) {
				shrinkHeader();
			}

			// Current scroll saved as the last scroll position.
			lastScrollTop = scrollTop;

		});

	}

	/**
	 * Resize logo and title.
	 */
	function reSize() {
		// Bigger windows.
		if ( $window.width() > 768 ) {
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
 * Set an elements min-height
 * according to the aspect ratio of its' background image.
 *
 * @version  2.0.0
 */
( function( window, document, $, undefined ) {

	// Resize after the window is ready. WP Rocket critical CSS needs this to wait, among other things.
	window.addEventListener( 'load', aspectRatio );
	window.addEventListener( 'resize', aspectRatio );

	// After FacetWP is loaded/refreshed. We needed to get the elements again because of the way FWP re-displays them.
	$( document ).on( 'facetwp-loaded', function() {
		aspectRatio();
	});

	function aspectRatio() {
		return document.querySelectorAll( '.aspect-ratio' ).forEach( function( el ) {
			return el.style.minHeight = el.offsetWidth / ( el.getAttribute( 'data-aspect-width' ) / el.getAttribute('data-aspect-height') ) + 'px';
		});
	}

})( window, document, jQuery );


/**
 * Convert menu items with .search class to a search icon with a fade in search box.
 * Show/hide search box on click, and allow closing by clicking outside of search box.
 *
 * @version  1.0.0
 */
( function( document, $, undefined ) {

	var $searchItems = $( '.genesis-nav-menu .search' );

	if ( 0 === $searchItems.length ) {
		return;
	}

	$.each( $searchItems, function(){

		var $this = $(this);

		$this.html( '<button class="nav-search"><span class="screen-reader-text">' + $this.text() + '</span></button>' ).show();

		var $searchButton = $this.find( 'button' );

		toggleAria( $searchButton, 'aria-pressed' );
		toggleAria( $searchButton, 'aria-expanded' );

		// Add the search box after the link.
		$this.append( maiVars.search_box );

		// On click of the search button.
		$this.on( 'click', 'button', function(e){

			e.preventDefault();

			toggleAria( $(this), 'aria-pressed' );
			toggleAria( $(this), 'aria-expanded' );

			// Close if the button has open class, otherwise open.
			if ( $this.hasClass( 'activated' ) ) {

				_searchClose( $this );

			} else {

				_searchOpen( $this );

				// Close search listener
				$( 'body' ).mouseup(function(e){
					/**
					 * Bail if:
					 * If click is on our search box container.
					 * If click is on a child of our search box container.
					 */
					if ( $(this).hasClass( 'search-box' ) || ( $this.has(e.target).length ) ) {
						return;
					}
					_searchClose( $this );
				});

				// Close search if esc key pressed.
				$(document).keydown(function(e) {
					// Use switch to easily add new keystrokes.
					switch(e.which) {
						case 27: // esc.
						// Close search box with esc key.
						_searchClose( $this );
						break;

						default: return; // exit this handler for other keys.
					}
				});

			}
		});

	});

	// Helper function to open search form and add class to search button.
	function _searchOpen( $this ) {
		$this.addClass( 'activated' ).find( '.search-box' ).fadeIn( 'fast' ).find( 'input[type="search"]' ).focus();
	}

	// Helper function to close search form and remove class to search button.
	function _searchClose( $this ) {
		$this.removeClass( 'activated' ).find( '.search-box' ).fadeOut( 'fast' );
	}

})( document, jQuery );


/**
 * This script adds the accessibility-ready responsive menu.
 * Based off https://github.com/copyblogger/responsive-menus.
 *
 * @version  1.0.0
 */

var maiMenuParams = typeof maiVars === 'undefined' ? '' : maiVars;

( function( document, $, undefined ) {
	'use strict';

	var maiMenu            = {},
		maiMenuClass       = 'mai-menu',
		maiButtonClass     = 'mai-toggle',
		subMenuButtonClass = 'sub-menu-toggle',
		menuClass          = 'mobile-menu';

	var $body        = $( 'body' ),
		$maiMenu     = $( '.' + maiMenuClass ),
		$mobileMenus = $maiMenu.find( '.menu' );

	// Initialize.
	maiMenu.init = function() {

		var toggleButtons = {
			menu : $( '<button />', {
				'id' : maiButtonClass,
				'class' : maiButtonClass,
				'aria-expanded' : false,
				'aria-pressed' : false,
				'role' : 'button'
			} )
			.append( '<span class="screen-reader-text">' + maiMenuParams.mainMenu + '</span><span class="mai-bars"></span></span>' ),
			submenu : $( '<button />', {
				'class' : subMenuButtonClass,
				'aria-expanded' : false,
				'aria-pressed' : false,
				'role' : 'button'
			} )
			.append( '<span class="screen-reader-text">' + maiMenuParams.subMenu + '</span>' ),
		};

		// Add the main nav and sub-menu toggle button.
		_addMenuButtons( toggleButtons );

		// Add the responsive menu class to the menus.
		_addResponsiveMenuClass();

		// Action triggers.
		$( '.' + maiButtonClass ).on( 'click.maiMenu-mainbutton', _maiMenuToggle );
		$( '.' + subMenuButtonClass ).on( 'click.maiMenu-subbutton', _submenuToggle );
		$( window ).on( 'resize.maiMenu', _doResize ).triggerHandler( 'resize.maiMenu' );

	};

	/**
	 * Add toggle buttons.
	 * @param {toggleButtons} Object of menu buttons to use for toggles.
	 */
	function _addMenuButtons( toggleButtons ) {

		$( '.site-header-row' ).append( toggleButtons.menu ); // add the main nav button.

		if ( $mobileMenus.length > 0 ) {
			$( '.' + maiMenuClass ).find( '.sub-menu' ).before( toggleButtons.submenu ); // add the submenu nav buttons.
		}

	}

	/**
	 * Add the responsive menu class.
	 */
	function _addResponsiveMenuClass() {
		$.each( $mobileMenus, function() {
			$(this).addClass( menuClass );
		});
	}

	/**
	 * Execute our responsive menu functions on window resizing.
	 */
	function _doResize() {

		if ( typeof maiButtonClass === 'undefined' ) {
			return;
		}
		_maybeClose();
		_changeSkipLink();
	}

	/**
	 * Action to happen when the main menu button is clicked.
	 */
	function _maiMenuToggle() {

		var $this = $( this );

		toggleAria( $this, 'aria-pressed' );
		toggleAria( $this, 'aria-expanded' );
		$this.toggleClass( 'activated' );
		$body.toggleClass( 'mai-menu-activated' );

		if ( $body.hasClass( 'has-side-menu' ) ) {
			// Side menu activated class.
			$body.toggleClass( 'mai-side-menu-activated' );
		} else {
			// Standard menu activated class.
			$body.toggleClass( 'mai-standard-menu-activated' );
			/**
			 * Standard menu, instant toggle open/closed.
			 * Reveal header animation was throwing this off,
			 * plus it's nicer when it's instant anyway.
			 */
			$maiMenu.slideToggle(0);
		}

		// If opening the menu.
		if ( $body.hasClass( 'mai-menu-activated' ) ) {

			// Allow additional keyboard nav.
			$(document).keydown( function(e) {
				// Use switch to easily add new keystrokes.
				switch(e.which) {
					case 27: // esc.
					// Close popup with esc key.
					_closeAll();
					break;

					default: return; // exit this handler for other keys.
				}
				e.preventDefault(); // prevent the default action (scroll / move caret).
			});

		}

		// On click of close button, close all.
		$(document).on( 'click', '.menu-close', function(e){
			_closeAll();
		});

	}

	/**
	 * Action for submenu toggles.
	 */
	function _submenuToggle() {

		var $this  = $( this ),
			others = $this.closest( '.menu-item' ).siblings();

		toggleAria( $this, 'aria-pressed' );
		toggleAria( $this, 'aria-expanded' );
		$this.toggleClass( 'activated' );
		$this.next( '.sub-menu' ).slideToggle( 'fast' );

		others.find( '.' + subMenuButtonClass ).removeClass( 'activated' ).attr( 'aria-pressed', false );
		others.find( '.sub-menu' ).slideUp( 'fast' );

	}

	/**
	 * Modify skip link to match mobile buttons.
	 */
	function _changeSkipLink() {

		var $skipLinksUL    = $( '.genesis-skip-link' ),
			$mobileSkipLink = $( '.genesis-skip-link a[href="#mai-toggle"]' ),
			$menuSkipLinks  = $( '.genesis-skip-link a[href*="#genesis-nav"]' );

		var buttonDisplay = _getDisplayValue( maiButtonClass );

		if ( $mobileSkipLink.length == 0 ) {
			$skipLinksUL.prepend( '<li><a href="#' + maiButtonClass + '" class="screen-reader-shortcut"> ' + maiMenuParams.mainMenu + '</a></li>' );
			$mobileSkipLink = $( '.genesis-skip-link a[href="#mobile-nav"]' );
		}

		if ( 'none' == buttonDisplay ) {
			$mobileSkipLink.addClass( 'skip-link-hidden' );
		} else {
			$mobileSkipLink.removeClass( 'skip-link-hidden' );
		}

		$.each( $menuSkipLinks, function () {

			if ( 'none' == buttonDisplay ) {
				$(this).removeClass( 'skip-link-hidden' );
			} else {
				$(this).addClass( 'skip-link-hidden' );
			}

		});
	}

	/**
	 * Maybe close all the things.
	 */
	function _maybeClose() {

		if ( 'none' !== _getDisplayValue( maiButtonClass ) ) {
			return true;
		}

		_closeAll();
	}

	/**
	 * Close all the things.
	 */
	function _closeAll() {

		$body.removeClass( 'mai-menu-activated' )
		if ( $body.hasClass('has-side-menu') ) {
			$body.removeClass( 'side-menu-activated' );
		} else {
			$maiMenu.slideUp( 'fast' );
		}

		$( '.' + maiButtonClass + ', .' + menuClass + ' .sub-menu-toggle' )
			.removeClass( 'activated' )
			.attr( 'aria-expanded', false )
			.attr( 'aria-pressed', false );

		$( '.' + menuClass + ', ' + menuClass + ' .sub-menu' )
			.removeClass( 'activated' )
			.attr( 'style', '' )
			.attr( 'aria-pressed', false );

		// Hide any open sub-menus.
		$( '.' + menuClass + ' .sub-menu' ).hide();
	}

	/**
	 * Generic function to get the display value of an element.
	 * @param  {id} $id ID to check.
	 * @return {string} CSS value of display property.
	 */
	function _getDisplayValue( $id ) {
		var element = document.getElementById( $id ),
			style   = window.getComputedStyle( element );
		return style.getPropertyValue( 'display' );
	}

	/**
	 * Helper function to return a group array of all the mobile menus.
	 * @return {array} Array of all menu items as class selectors.
	 */
	function _getAllMenusArray() {

		// Start with an empty array.
		var menuList = [];

		// If there are menus in the '$mobileMenus' array, add them to 'menuList'.
		if ( $mobileMenus.length != 0 ) {

			$.each( $mobileMenus, function( key, value ) {
				menuList.push( value.valueOf() );
			});

		}

		if ( menuList.length > 0 ) {
			return menuList;
		} else {
			return null;
		}

	}

	// Make it happen.
	$(document).ready(function () {

		// Initiate if there is menu content.
		if ( $maiMenu.length > 0 ) {
			maiMenu.init();
		}
	});

})( document, jQuery );


/**
 * Toggle aria attributes.
 * @param  {button} $this   passed through.
 * @param  {aria-xx}        attribute aria attribute to toggle.
 * @return {bool}           from _ariaReturn.
 */
function toggleAria( $this, attribute ) {
	$this.attr( attribute, function( index, value ) {
		return 'false' === value;
	});
}


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

	$('.js-superfish').superfish({
		'delay': 100,
		'speed': 'fast',
		'speedOut': 'slow',
		'disableHI': true,
	});

})( document, jQuery );
