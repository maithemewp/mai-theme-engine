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

/**
 * Do the Flexington header.
 * This is all wrapped in one function so we can pass $left and $right variables easier.
 *
 * @version  1.1.0
 *
 * @return   void
 */
add_action( 'genesis_meta', 'mai_do_header' );
function mai_do_header() {

	/**
	 * Allow templates to hijack and remove the header content via a filter
	 *
	 * add_filter( 'mai_utility_nav', '__return_false' );
	 * add_filter( 'mai_header_left_content', '__return_false' );
	 * add_filter( 'mai_header_right_content', '__return_false' );
	 * add_filter( 'mai_mobile_menu', '__return_false' );
	 *
	 * @return  bool
	 */
	// $utility = apply_filters( 'mai_utility_nav', genesis_get_nav_menu( array( 'theme_location' => 'utility' ) ) );
	$before  = apply_filters( 'mai_header_before_content', '' );
	$left 	 = apply_filters( 'mai_header_left_content', '' );
	$right 	 = apply_filters( 'mai_header_right_content', '' );
	$mobile  = apply_filters( 'mai_mobile_menu', mai_get_mobile_menu() );

	/**
	 * Add classes to know when the header has left or right header content.
	 *
	 * @param   array  $attributes  The header attributes.
	 *
	 * @return  array  The modified attributes.
	 */
	add_filter( 'genesis_attr_site-header', function( $attributes ) use ( $left, $right ) {

		if ( $left ) {
			$attributes['class'] .= ' has-header-left';
		}

		if ( $right ) {
			$attributes['class'] .= ' has-header-right';
		}

		return $attributes;

	});

	/**
	 * Filter the (site) header context of the genesis_structural_wrap
	 * Add utility nav before the wrap
	 * Open Flexington row after the wrap
	 *
	 * @param  string  $output 			 The markup to be returned
	 * @param  string  $original_output  Set to either 'open' or 'close'
	 */
	add_filter( 'genesis_structural_wrap-header', function( $output, $original_output ) use ( $before, $left, $right, $mobile ) {

		$content_before = $content_after = '';

	    if ( 'open' == $original_output ) {

	    	if ( $before ) {
	    		$content_before .= sprintf( '<div class="header-before"><div class="wrap">%s</div></div>', $before );
	    	}

			$row['class'] = 'row middle-xs';

			// Justification
			$justify = ' around-xs';
			if ( $left || $right ) {
				$justify = ' between-xs';
			}
			$row['class'] .= $justify;

			$content_after .= sprintf( '<div %s>', genesis_attr( 'site-header-row', $row ) );

	    } elseif ( 'close' == $original_output ) {

	    	$content_before .= '</div>';

	    	if ( $mobile ) {
		    	$content_before .= $mobile;
	    	}

	    }

	    return $content_before . $output . $content_after;

	}, 10, 2 );

	// Add Flexington classes to the title area
	add_filter( 'genesis_attr_title-area', function( $attributes ) use ( $left, $right ) {
		$classes = 'col col-xs-auto';
		$distribution = '';
		if ( ( $left && $right ) || ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) ) {
			$distribution = 'text-xs-center';
		}
		if ( $left || $right ) {
			$classes .= ' start-xs';
			if ( $left && $right ) {
				$classes .= ' col-md-12 col-lg-auto';
			} elseif ( $left && ! $right ) {
				$classes .= ' last-xs';
			}
		} else {
			$classes .= ' center-xs';
		}
	    $attributes['class'] = $attributes['class'] . ' ' . $classes . ' ' . $distribution;
	    return $attributes;
	});

	// Check if we have header content
	if ( $left || $right ) {

		/**
		 * Add header left and right widget areas and menus
		 * with Flexington classes
		 */
		add_action( 'genesis_header', function() use ( $left, $right ) {

			if ( $left ) {

				$left_atts['class'] = 'header-left col col-xs';

				if ( $right ) {
					$left_atts['class'] .= ' col-md-6 col-lg first-lg text-xs-right';
				} else {
					$left_atts['class'] .= ' first-xs';
				}

				printf( '<div %s>%s</div>', genesis_attr( 'header-left', $left_atts ), $left );

			}

			if ( $right ) {

				$right_atts['class'] = 'header-right col col-xs';

				if ( $left ) {
					$right_atts['class'] .= ' col-md-6 col-lg text-xs-left';
				} else {
					$right_atts['class'] .= ' text-xs-right';
				}

				printf( '<div %s>%s</div>', genesis_attr( 'header-right', $right_atts ), $right );

			}

		});

	}

}

/**
 * Run the filter to get the header before content.
 *
 * @return  string|HTML  The content
 */
add_filter( 'mai_header_before_content', 'mai_get_header_before_content' );
function mai_get_header_before_content( $content ) {
	// Header Before widget area
	if ( is_active_sidebar('header_before') ) {
		ob_start();
		_mai_add_header_menu_args();
		genesis_widget_area( 'header_before' );
		_mai_remove_header_menu_args();
		$content .= ob_get_clean();
	}
	// Header Before menu
	if ( has_nav_menu('header_left') ) {
		$content .= genesis_get_nav_menu( array( 'theme_location' => 'utility' ) );
	}

	return $content;
}

/**
 * Run the filter to get the header left content.
 *
 * @return  string|HTML  The content
 */
add_filter( 'mai_header_left_content', 'mai_get_header_left_content' );
function mai_get_header_left_content( $content ) {
	// Header Left widget area
	if ( is_active_sidebar('header_left') ) {
		ob_start();
		_mai_add_header_menu_args();
		genesis_widget_area( 'header_left' );
		_mai_remove_header_menu_args();
		$content .= ob_get_clean();
	}
	// Header Left menu
	if ( has_nav_menu('header_left') ) {
		$content .= genesis_get_nav_menu( array( 'theme_location' => 'header_left' ) );
	}

	return $content;
}

/**
 * Run the filter to get the header right content.
 *
 * @return  string|HTML  The content
 */
add_filter( 'mai_header_right_content', 'mai_get_header_right_content' );
function mai_get_header_right_content( $content ) {
	// Header Right widget area
	if ( is_active_sidebar('header_right') ) {
		ob_start();
		_mai_add_header_menu_args();
		genesis_widget_area( 'header_right' );
		_mai_remove_header_menu_args();
		$content .= ob_get_clean();
	}
	// Header Right menu
	if ( has_nav_menu('header_right') ) {
		$content .= genesis_get_nav_menu( array( 'theme_location' => 'header_right' ) );
	}

	return $content;
}

// Use Genesis header menu filter (taken from G)
function _mai_add_header_menu_args() {
	add_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
	add_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
}

// Remove Genesis header menu filter (taken from G)
function _mai_remove_header_menu_args() {
	remove_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
	remove_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
}
