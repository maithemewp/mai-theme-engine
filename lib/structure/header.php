<?php
/**
 * Mai Pro Engine.
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.0
 */


/**
 * Add an image inline in the site title element for the main logo
 *
 * The custom logo is then added via the Customiser
 *
 * @param  	string  $title 	 All the mark up title.
 * @param  	string  $inside  Mark up inside the title.
 * @param  	string  $wrap 	 Mark up on the title.
 *
 * @return  string|HTML  The title markup
 */
add_filter( 'genesis_seo_title','mai_do_custom_logo', 10, 3 );
function mai_do_custom_logo( $title, $inside, $wrap ) {

	$site_title	= get_bloginfo( 'name' );

	// If the custom logo function and custom logo exist, set the logo image element inside the wrapping tags.
	if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
		$inside = sprintf( '<span class="screen-reader-text">%s</span>%s' , esc_html( $site_title ), get_custom_logo() );
	} else {
		// If no custom logo, wrap around the site name.
		$inside	= sprintf( '<a href="%s" title="%s">%s</a>', trailingslashit( home_url() ), esc_attr( $site_title ), esc_html( $site_title ) );
	}

	// Determine which wrapping tags to use.
	$wrap = genesis_is_root_page() && 'title' === genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : 'p';

	// A little fallback, in case a SEO plugin is active.
	$wrap = genesis_is_root_page() && ! genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : $wrap;

	// Wrap homepage site title in p tags if static front page.
	$wrap = is_front_page() && ! is_home() ? 'p' : $wrap;

	// And finally, $wrap in h1 if HTML5 & semantic headings enabled.
	$wrap = genesis_html5() && genesis_get_seo_option( 'semantic_headings' ) ? 'h1' : $wrap;

	// Rebuild the markup
	$title = sprintf( '<%s %s>%s</%s>', $wrap, genesis_attr( 'site-title' ), $inside, $wrap );

	return $title;
}

/**
 * Add class for screen readers to site description.
 * This will keep the site description markup but will not have any visual presence on the page.
 *
 * @param 	array  $attributes Current attributes.
 *
 * @return  array  The attributes.
 */
add_filter( 'genesis_attr_site-description', 'mai_hide_site_description' );
function mai_hide_site_description( $attributes ) {
	$attributes['class'] .= ' screen-reader-text';
	return $attributes;
}


add_action( 'genesis_header', 'mai_do_header', 4 );
function mai_do_header() {

	// These are basically do_action() hooks you can use
	$before	= mai_get_do_action( 'mai_header_before' );
	$left	= mai_get_do_action( 'mai_header_left' );
	$right	= mai_get_do_action( 'mai_header_right' );
	$after	= mai_get_do_action( 'mai_header_after' );

	/**
	 * Add classes to know when the header has left or right header content.
	 *
	 * @param   array  $attributes  The header attributes.
	 *
	 * @return  array  The modified attributes.
	 */
	add_filter( 'genesis_attr_site-header', function( $attributes ) use ( $left, $right ) {

		if ( ! ( $left && $right ) ) {
			$attributes['class'] .= ' no-header-content';
		}

		if ( $left ) {
			$attributes['class'] .= ' has-header-left';
		}

		if ( $right ) {
			$attributes['class'] .= ' has-header-right';
		}

		return $attributes;

	});

	/**
	 * Filter the (site) header context of the genesis_structural_wrap.
	 * Add new before/after header content hooks.
	 *
	 * @return  string|HTML  The content
	 */
	add_filter( 'genesis_structural_wrap-header', function( $output, $original_output ) use ( $before, $left, $right, $after ) {

	    if ( 'open' == $original_output ) {

			// Build header before markup
			if ( $before ) {

				$before_atts['class'] = 'header-before';

				$before = sprintf( '<div %s>%s</div>', genesis_attr( 'header-before', $before_atts ), $before );

			}

			// Default classes
			$row['class'] = 'row middle-xs';

			// Justification
			$row['class'] .= ( $left || $right ) ? ' between-xs' : ' around-xs';

			// Output with row open
			$output = $before . $output . sprintf( '<div %s>', genesis_attr( 'site-header-row', $row ) );

	    } elseif ( 'close' == $original_output ) {

			// Build header left markup
			if ( $left ) {

				$left_atts['class'] = 'header-left col col-xs';

				if ( $right ) {
					$left_atts['class'] .= ' col-md-6 col-lg first-lg text-xs-right';
				} else {
					$left_atts['class'] .= ' first-xs';
				}

				$left = sprintf( '<div %s>%s</div>', genesis_attr( 'header-left', $left_atts ), $left );

			}

			// Build header right markup
			if ( $right ) {

				$right_atts['class'] = 'header-right col col-xs';

				if ( $left ) {
					$right_atts['class'] .= ' col-md-6 col-lg text-xs-left';
				} else {
					$right_atts['class'] .= ' text-xs-right';
				}

				$right = sprintf( '<div %s>%s</div>', genesis_attr( 'header-right', $right_atts ), $right );

			}

			// Build header after markup
			if ( $after ) {

				$after_atts['class'] = 'header-after';

				$after = sprintf( '<div %s>%s</div>', genesis_attr( 'header-after', $after_atts ), $after );

			}


			$output = $left . $right . $output . $after;

	    }

	    return $output;

	}, 10, 2 );

	// Add Flexington classes to the title area
	add_filter( 'genesis_attr_title-area', function( $attributes ) use ( $left, $right ) {

		// Default classes
		$attributes['class'] .= ' col col-xs-auto';

		// If left and right content, or logo
		if ( $left && $right ) {

			$attributes['class'] .= ' col-md-12 col-lg-auto';

			if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
				$attributes['class'] .= ' text-xs-center';
			}
		}

		// If left or right content
		if ( $left || $right ) {

			$attributes['class'] .= ' start-xs';
			if ( $left && ! $right ) {
				$attributes['class'] .= ' last-xs';
			}


		} else {
			$attributes['class'] .= ' center-xs';
		}

	    return $attributes;

	});

}

/**
 * Add the header before content.
 *
 * @return  void
 */
add_action( 'mai_header_before', 'mai_do_header_before' );
function mai_do_header_before() {

	// Bail if no content
	if ( ! is_active_sidebar('header_before') ) {
		return;
	}

	// Before Header widget area
	_mai_add_widget_header_menu_args();
	genesis_widget_area( 'header_before' );
	_mai_remove_widget_header_menu_args();
}

/**
 * Add the header left content.
 *
 * @return  void
 */
add_action( 'mai_header_left', 'mai_do_header_left' );
function mai_do_header_left() {

	// Bail if no content
	if ( ! ( is_active_sidebar('header_left') || has_nav_menu('header_left') ) ) {
		return;
	}

	// Header Left widget area
	if ( is_active_sidebar('header_left') ) {
		_mai_add_widget_header_menu_args();
		genesis_widget_area( 'header_left' );
		_mai_remove_widget_header_menu_args();
	}

	// Header Left menu
	if ( has_nav_menu('header_left') ) {
		echo genesis_get_nav_menu( array( 'theme_location' => 'header_left' ) );
	}
}

/**
 * Add the header right content.
 *
 * @return  void
 */
add_action( 'mai_header_right', 'mai_do_header_right' );
function mai_do_header_right() {

	// d( did_action( 'mai_header_right' ) );

	// Bail if no content
	if ( ! ( is_active_sidebar('header_right') || has_nav_menu('header_right') ) ) {
		return;
	}

	// Header Right widget area
	if ( is_active_sidebar('header_right') ) {
		_mai_add_widget_header_menu_args();
		genesis_widget_area( 'header_right' );
		_mai_remove_widget_header_menu_args();
	}
	// Header Right menu
	if ( has_nav_menu('header_right') ) {
		echo genesis_get_nav_menu( array( 'theme_location' => 'header_right' ) );
	}
}

// Use Genesis header menu filter (taken from G)
function _mai_add_widget_header_menu_args() {
    add_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
    add_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
}

// Remove Genesis header menu filter (taken from G)
function _mai_remove_widget_header_menu_args() {
    remove_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
    remove_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
}
