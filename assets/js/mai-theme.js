/*! fluidvids.js v2.4.1 | (c) 2014 @toddmotto | https://github.com/toddmotto/fluidvids */
!function(e,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t:e.fluidvids=t()}(this,function(){"use strict";function e(e){return new RegExp("^(https?:)?//(?:"+d.players.join("|")+").*$","i").test(e)}function t(e,t){return parseInt(e,10)/parseInt(t,10)*100+"%"}function i(i){if((e(i.src)||e(i.data))&&!i.getAttribute("data-fluidvids")){var n=document.createElement("div");i.parentNode.insertBefore(n,i),i.className+=(i.className?" ":"")+"fluidvids-item",i.setAttribute("data-fluidvids","loaded"),n.className+="fluidvids",n.style.paddingTop=t(i.height,i.width),n.appendChild(i)}}function n(){var e=document.createElement("div");e.innerHTML="<p>x</p><style>"+o+"</style>",r.appendChild(e.childNodes[1])}var d={selector:["iframe","object"],players:["www.youtube.com","player.vimeo.com"]},o=[".fluidvids {","width: 100%; max-width: 100%; position: relative;","}",".fluidvids-item {","position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;","}"].join(""),r=document.head||document.getElementsByTagName("head")[0];return d.render=function(){for(var e=document.querySelectorAll(d.selector.join()),t=e.length;t--;)i(e[t])},d.init=function(e){for(var t in e)d[t]=e[t];d.render(),n()},d});

/**
 * Instantiate FluidVids on YouTube and Vimeo embeds
 */
( function( document, $, undefined ) {

	fluidvids.init({
		selector: ['iframe', 'object'], // runs querySelectorAll()
		players: ['www.youtube.com', 'player.vimeo.com'] // players to support
	});

})( document, jQuery );

/**
 * Handle sticky-header, shrink-header, and scroll logic.
 *
 * @version  1.0.0
 */
( function( document, $, undefined ) {

	var $body      = $( 'body' ),
		$siteTitle = $( '.site-title a' ),
		titleWidth = $siteTitle.outerWidth();

	// Set inline width. This seems to help with jitters on first scroll.
	if ( $(this).width() > 768 ) {
		$siteTitle.css({ maxWidth: titleWidth });
	}
	// Load shrunk header on mobile.
	else {
		$siteTitle.css({ maxWidth: titleWidth * .7 });
	}

	// If doing a shrink header.
	if ( $body.hasClass( 'shrink-header' ) ) {

		var $siteHeader   = $( '.site-header' ),
			shrinkFired   = false,
			unshrinkFired = false;

		// On resize and/or scroll.
		$( window ).on( 'resize scroll', function() {

			if ( $(this).width() > 768 ) {

				// Shrink/Unshrink triggers.
				if ( $(this).scrollTop() > 1 ) {
					if ( false === shrinkFired ) {
						$siteHeader.trigger( 'mai-shrink-header' );
						shrinkFired   = true;
						unshrinkFired = false;
					}
				} else {
					if ( false === unshrinkFired ) {
						$siteHeader.trigger( 'mai-unshrink-header' );
						unshrinkFired = true;
						shrinkFired   = false;
					}
				}

			} else {

				// Force shrink on mobile.
				$siteTitle.css({ maxWidth: titleWidth * .7 });
			}

		});

		// Shrink.
		$siteHeader.on( 'mai-shrink-header', function() {
			$(this).addClass( 'shrink' );
			$siteTitle.css({ maxWidth: titleWidth * .7 });
		});

		// Unshrink.
		$siteHeader.on( 'mai-unshrink-header', function() {
			$(this).removeClass( 'shrink' );
			$siteTitle.css({ maxWidth: titleWidth });
		});

	}
	// Not shrinking header.
	else {

		// When resizing or scrolling, typically from changing device orientation.
		$( window ).on( 'resize scroll', function() {

			// Show normal size on desktop.
			if ( $(this).width() > 768 ) {
				$siteTitle.css({ maxWidth: titleWidth });
			}
			// Show shrunk on mobile.
			else {
				$siteTitle.css({ maxWidth: titleWidth * .7 });
			}

		});

	}

	// On scroll add .scroll class.
	$( window ).scroll( function() {
		// Shrink the header on scroll.
		if ( $( window ).scrollTop() > 1 ) {
			$body.addClass( 'scroll' );
		} else {
			$body.removeClass( 'scroll' );
		}
	});

})( document, jQuery );


/**
 * Set an elements min-height
 * according to the aspect ratio of its' background image.
 *
 * @version  1.0.0
 */
( function( document, $, undefined ) {

	// Aspect ratio elements
	var $aspectElement = $( '.aspect-ratio' );

	// If we have any elements
	if ( $aspectElement.length > 0 ) {

		// If the element is part of a slider
		if ( $aspectElement.hasClass( 'mai-slide' ) ) {

			// Get the slider element
			var $slider = $aspectElement.parents( '.flex-grid' ).find( '.mai-slider' );

			/**
			 * Setup resize after slider initialization
			 * since additional elements are often created during init
			 */
			$slider.on( 'init', function( event, slick, direction ) {
				var $additionalAspectElements = $( '.aspect-ratio' );
				_setupResize( $additionalAspectElements );
			});

		} else {
			_setupResize( $aspectElement );
		}

	}

	function _setupResize( $aspectElement ) {

		$.each( $aspectElement, function(){

			var $element = $(this);

			if ( $element.hasClass( 'mai-slide' ) ) {

				var $slider = $element.parents( '.flex-grid' ).find( '.mai-slider' );

				/**
				 * Wait till slider events before initial resize,
				 * otherwise we were getting element width too early and calculations were wrong.
				 */
				$slider.on( 'init reInit breakpoint setPosition', function( event, slick ) {
					_resizeToMatch( $element );
				});

			} else {

				_resizeToMatch( $element );

			}

			// Resize the window resize.
			$( window ).on( 'resize', function() {
				_resizeToMatch( $element );
			});

		});

	}

	function _resizeToMatch( $element ) {

		// Get the image size from attributes.
		var width  = $element.data( 'aspect-width' ),
			height = $element.data( 'aspect-height' );

		if ( width && height ) {
			$element.css( 'min-height', Math.round( $element.outerWidth() * height / width ) + 'px' );
		}

	}

})( document, jQuery );


/**
 * Convert menu items with .search class to a search icon with a fade in search box.
 * Show/hide search box on click, and allow closing by clicking outside of search box.
 *
 * @version  1.0.0
 */
( function( document, $, undefined ) {

	var $searchItems = $( '.genesis-nav-menu .search' );

	if ( $searchItems.length == 0 ) {
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

		var toggleButtons     = {
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

		if ( $body.hasClass( 'side-menu' ) ) {
			// Side menu activated class.
			$body.toggleClass( 'mai-side-menu-activated' );
		} else {
			// Standard menu activated class.
			$body.toggleClass( 'mai-standard-menu-activated' );
			// Standard menu, toggle it down/up.
			$maiMenu.slideToggle( 'fast' );
		}

		// Allow additional keyboard nav.
		if ( $body.hasClass( 'mai-menu-activated' ) ) {

			$(document).keydown(function(e) {
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
			$mobileSkipLink = $( '.genesis-skip-link a[href="#mobile-nav"]' ),
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
		if ( $body.hasClass('side-menu') ) {
			$body.removeClass( 'side-menu-activated' )
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
