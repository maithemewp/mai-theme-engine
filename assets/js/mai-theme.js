/**
 * Swap out no-js for js body class
 */
( function() {
    var c = document.body.className;
    c = c.replace( /no-js/, 'js' );
    document.body.className = c;
})();


/**
 * Handle spacing for sticky-header and shrink-header.
 *
 * @version  1.0.0
 */
( function ( document, $, undefined ) {

    var $body = $('body');

    // If doing a sticky header
    if ( $body.hasClass('sticky-header') ) {
        // Trigger the resizing
        $( window ).on( 'resize.maiHeader', _doFixedHeader ).triggerHandler( 'resize.maiHeader' );
    }

    // If doing a shrink header
    if ( $body.hasClass('shrink-header') ) {

        // On scroll
        $( window ).scroll(function () {
            // Shrink the header on scroll.
            if ( $( document ).scrollTop() > 1 ) {
                $( '.site-header' ).addClass( 'shrink' );
            } else {
                $( '.site-header' ).removeClass( 'shrink' );
            }
        });

    }

    // On scroll add .scroll class
    $( window ).scroll(function () {
        // Shrink the header on scroll.
        if ( $( document ).scrollTop() > 1 ) {
            $body.addClass( 'scroll' );
        } else {
            $body.removeClass( 'scroll' );
        }
    });

    // Dynamically add top margin to site inner at the same height as the site header
    function _doFixedHeader() {

        var $window      = $(window),
            $siteHeader  = $('.site-header'),
            $siteInner   = $('.site-inner'),
            $navPrimary  = $('.nav-primary'),
            $pushElement;

        var headerHeight = $siteHeader.height();

        // If we have a visible primary nav bar
        if ( $navPrimary.is(':visible') ) {
            // Push the primary nav
            $pushElement = $navPrimary;
        } else {
            // Push site inner
            $pushElement = $siteInner;
        }

        /**
         * Clear all margin-top inline styles
         * This helps when resizing larger
         */
        $siteInner.css( 'margin-top', '' );
        $navPrimary.css( 'margin-top', '' );

        /**
         * If bigger than mobile (xs) screen size
         * Make header fixed
         * Add spacing above our push element
         */
        if ( $window.width() > 544 ) {
            $siteHeader.css( 'position', 'fixed' );
            $pushElement.css( 'margin-top', headerHeight + 'px' );
        } else {
            // Set header back to static
            $siteHeader.css( 'position', 'static' );
        }

    }

})( document, jQuery );


/**
 * Set an elements min-height
 * according to the aspect ratio of its' background image.
 *
 * @version  1.0.0
 */
( function ( document, $, undefined ) {

    // Image aspect ratio elements
    var $imageBG = $( '.aspect-ratio' );

    if ( $imageBG.length > 0 ) {

        $.each( $imageBG, function(){
            var $element = $(this);
            // Initial sizing
            _resizeToMatch( $element );
            // Resize the banner
            $( window ).resize( function(){
                 _resizeToMatch( $element );
            });
        });
    }

    function _resizeToMatch( $element ) {

        // Get the image size from attributes
        var width  = $element.data( 'aspect-width' ),
            height = $element.data( 'aspect-height' );

        if ( width && height ) {
            $element.css( 'min-height', Math.round( $element.outerWidth() * height / width ) + 'px' );
        }

    }

})( document, jQuery );


/**
 * Convert menu items with .search class to a search icon with a fade in search box
 * Show/hide search box on click, and allow closing by clicking outside of search box
 *
 * @version  1.0.0
 */
( function ( document, $, undefined ) {

    var $searchItems = $( '.genesis-nav-menu .search' );

    if ( $searchItems.length == 0 ) {
        return;
    }

    $.each( $searchItems, function(){

        var $this = $(this);

        // Show search menu item and hide the menu text
        $this.show().find( 'span[itemprop=name]' ).addClass( 'screen-reader-text' );

        var $searchLink = $this.find( 'a' );

        toggleAria( $searchLink, 'aria-pressed' );
        toggleAria( $searchLink, 'aria-expanded' );

        // Add the search box after the link
        $this.append( maiVars.search_box );

        // On click of the search button
        $this.on( 'click', 'a', function(e){

            e.preventDefault()

            toggleAria( $(this), 'aria-pressed' );
            toggleAria( $(this), 'aria-expanded' );

            // Close if the button has open class, otherwise open
            if ( $this.hasClass( 'activated' ) ) {

                _searchClose($this);

            } else {

                _searchOpen($this);

                // Close search listener
                $('body').mouseup(function(e){
                    /**
                     * If click is not on our search box container
                     * If click is not on a child of our search box container
                     */
                    if( ! $(this).hasClass( 'search-box' ) && ! $this.has(e.target).length ) {
                        _searchClose($this);
                    }
                });
            }
        });

    });

    // Helper function to open search form and add class to search button
    function _searchOpen($this) {
        $this.addClass('activated').find('.search-box').fadeIn('fast');
    }

    // Helper function to close search form and remove class to search button
    function _searchClose($this) {
        $this.removeClass('activated').find('.search-box').fadeOut('fast');
    }

})( document, jQuery );


/**
 * This script adds the accessibility-ready responsive menu
 * Based off https://github.com/copyblogger/responsive-menus
 *
 * @version  1.0.0
 */

var maiMenuParams = typeof maiVars === 'undefined' ? '' : maiVars;

( function ( document, $, undefined ) {
    'use strict';

    var maiMenu             = {},
        maiMenuClass        = 'mai-menu',
        maiButtonClass      = 'mai-toggle',
        subMenuButtonClass  = 'sub-menu-toggle',
        menuClass           = 'mobile-menu';

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
            .append( '<span class="screen-reader-text">' + maiMenuParams.mainMenu + '</span><span class="mai-bars"><span class="mai-bar"></span><span class="mai-bar"></span><span class="mai-bar"></span></span>' ),
            submenu : $( '<button />', {
                'class' : subMenuButtonClass,
                'aria-expanded' : false,
                'aria-pressed' : false,
                'role' : 'button'
            } )
            .append( '<span class="screen-reader-text">' + maiMenuParams.subMenu + '</span>' ),
        };

        // Add the main nav and sub-menu toggle button
        _addMenuButtons( toggleButtons );

        // Add the responsive menu class to the menus
        _addResponsiveMenuClass();

        // Action triggers
        $( '.' + maiButtonClass ).on( 'click.maiMenu-mainbutton', _maiMenuToggle );
        $( '.' + subMenuButtonClass ).on( 'click.maiMenu-subbutton', _submenuToggle );
        $( window ).on( 'resize.maiMenu', _doResize ).triggerHandler( 'resize.maiMenu' );

    };

    /**
     * Add toggle buttons
     * @param {toggleButtons} Object of menu buttons to use for toggles.
     */
    function _addMenuButtons( toggleButtons ) {

        $('.site-header').find('.wrap > .row').append( toggleButtons.menu ); // add the main nav button

        if ( $mobileMenus.length > 0 ) {
            $( '.' + maiMenuClass ).find( '.sub-menu' ).before( toggleButtons.submenu ); // add the submenu nav buttons
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

        if ( $body.hasClass('side-menu') ) {

            // First, show the mobile menu behind the main container
            $maiMenu.show();

            if ( $body.hasClass( 'side-menu-activated' ) ) {
                $body.removeClass( 'side-menu-activated' );
            } else {
                /**
                 * Allow the menu to slide in before adding the activated class
                 * This allows us to change z-index after opening (and other usability changes)
                 */
                setTimeout( function() {
                    if ( ! $body.hasClass( 'side-menu-activated' ) ) {
                        $body.addClass( 'side-menu-activated' );
                    }
                }, 300 );
            }

        } else {
            // Standard menu, toggle it down/up
            $maiMenu.slideToggle( 'fast' );
        }

        // Allow additional keyboard nav
        if ( $body.hasClass( 'mai-menu-activated' ) ) {

            $(document).keydown(function(e) {
                // Use switch to easily add new keystrokes
                switch(e.which) {
                    case 27: // esc
                    // Close popup with esc key
                    _closeAll();
                    break;

                    default: return; // exit this handler for other keys
                }
                e.preventDefault(); // prevent the default action (scroll / move caret)
            });

        }

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
     * Maybe close all the things
     */
    function _maybeClose() {

        if ( 'none' !== _getDisplayValue( maiButtonClass ) ) {
            return true;
        }

        _closeAll();

    }

    /**
     * Close all the things
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

        // Hide any open sub-menus
        $( '.' + menuClass + ' .sub-menu' ).hide();

    }

    /**
     * Generic function to get the display value of an element.
     * @param  {id} $id ID to check
     * @return {string} CSS value of display property
     */
    function _getDisplayValue( $id ) {
        var element = document.getElementById( $id ),
            style   = window.getComputedStyle( element );
        return style.getPropertyValue( 'display' );
    }

    /**
     * Helper function to return a group array of all the mobile menus
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

    // Make it happen
    $(document).ready(function () {
        // Initiate if there is menu content
        if ( $maiMenu.length > 0 ) {
            maiMenu.init();
        }
    });

})( document, jQuery );


/**
 * Toggle aria attributes.
 * @param  {button} $this   passed through
 * @param  {aria-xx}        attribute aria attribute to toggle
 * @return {bool}           from _ariaReturn
 */
function toggleAria( $this, attribute ) {
    $this.attr( attribute, function( index, value ) {
        return 'false' === value;
    });
}


/**
 * Scroll to a div id
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
( function ( document, $, undefined ) {

    $('.js-superfish').superfish({
        'delay': 100,
        'speed': 'fast',
        'speedOut': 'slow',
        'disableHI': true,
    });

})( document, jQuery );
