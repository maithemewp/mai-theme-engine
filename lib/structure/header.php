<?php

/**
 * Add an image inline in the site title element for the main logo
 *
 * The custom logo is then added via the Customiser
 *
* @param   string  $title   All the mark up title.
* @param   string  $inside  Mark up inside the title.
* @param   string  $wrap    Mark up on the title.
*
* @return  string|HTML  The title markup
 */
add_filter( 'genesis_seo_title','mai_custom_logo', 10, 3 );
function mai_custom_logo( $title, $inside, $wrap ) {

	// If no custom logo, return the original title.
	if ( ! ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) ) {
		return $title;
	}

	// Build the title with logo.
	return genesis_markup( array(
		'open'    => sprintf( "<{$wrap} %s>", genesis_attr( 'site-title' ) ),
		'close'   => "</{$wrap}>",
		'content' => get_custom_logo(),
		'context' => 'site-title',
		'echo'    => false,
		'params'  => array(
			'wrap' => $wrap,
		),
	) );

}

/**
 * If the front page is a static page, wrap the site title in an <h1>.
 *
 * @param $wrap
 *
 * @return string
 */
add_filter( 'genesis_site_title_wrap', 'mai_site_title_wrap' );
function mai_site_title_wrap( $wrap ) {

	// Bail if not a singular Sections template without banner enabled.
	if ( ! is_singular() && ! is_page_template( 'sections.php' ) && ! mai_is_banner_area_enabled() ) {
		return $wrap;
	}

	$has_title = mai_sections_has_title( get_the_ID() );

	// If no title, use h1 on title.
	if ( ! $has_title ) {
		$wrap = 'h1';
	}

	return $wrap;
}

/**
 * Add class for screen readers to site description.
 * This will keep the site description markup but will not have any visual presence on the page.
 *
 * @param   array  $attributes Current attributes.
 *
 * @return  array  The attributes.
 */
add_filter( 'genesis_attr_site-description', 'mai_hide_site_description' );
function mai_hide_site_description( $attributes ) {
	$attributes['class'] .= ' screen-reader-text';
	return $attributes;
}

/**
 * Add header action hooks.
 * Filter header elements to modify attributes accordingly.
 *
 * @return  void
 */
add_action( 'genesis_header', 'mai_do_header', 4 );
function mai_do_header() {

	// These are basically do_action() hooks you can use, with filters (via mai_get_do_action helper) so we can easily remove elements from templates
	$before = mai_get_do_action( 'mai_header_before' );
	$left   = mai_get_do_action( 'mai_header_left' );
	$right  = mai_get_do_action( 'mai_header_right' );
	$after  = mai_get_do_action( 'mai_header_after' );
	$mobile = apply_filters( '_mai_mobile_menu', mai_get_mobile_menu() );

	/**
	 * Add classes to know when the header has left or right header content.
	 *
	 * @param   array  $attributes  The header attributes.
	 *
	 * @return  array  The modified attributes.
	 */
	add_filter( 'genesis_attr_site-header', function( $attributes ) use ( $left, $right ) {

		if ( ! ( $left || $right ) ) {
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
	add_filter( 'genesis_structural_wrap-header', function( $output, $original_output ) use ( $before, $left, $right, $after, $mobile ) {

		if ( 'open' == $original_output ) {

			// Build header before markup.
			if ( $before ) {

				$before_atts['class'] = 'header-before text-sm';

				$before = sprintf( '<div %s><div class="wrap">%s</div></div>', genesis_attr( 'header-before', $before_atts ), $before );

			}

			// Default classes.
			$row['class'] = 'site-header-row row middle-xs between-xs';

			// Justification.
			$row['class'] .= ( ! $left || $right ) ? ' around-md' : '';

			// Output with row open.
			$output = $before . $output . sprintf( '<div %s>', genesis_attr( 'site-header-row', $row ) );

		} elseif ( 'close' == $original_output ) {

			// Build header left markup.
			if ( $left ) {

				$left_atts['class'] = 'header-left col col-xs';

				if ( $right ) {
					$left_atts['class'] .= ' col-md-6 col-lg first-lg text-xs-right';
				} else {
					$left_atts['class'] .= ' first-xs';
				}

				$left = sprintf( '<div %s>%s</div>', genesis_attr( 'header-left', $left_atts ), $left );

			}

			// Build header right markup.
			if ( $right ) {

				$right_atts['class'] = 'header-right col col-xs';

				if ( $left ) {
					$right_atts['class'] .= ' col-md-6 col-lg text-xs-left';
				} else {
					$right_atts['class'] .= ' text-xs-right';
				}

				$right = sprintf( '<div %s>%s</div>', genesis_attr( 'header-right', $right_atts ), $right );

			}

			// Build header after markup.
			if ( $after ) {

				$after_atts['class'] = 'header-after';

				$after = sprintf( '<div %s><div class="wrap">%s</div></div>', genesis_attr( 'header-after', $after_atts ), $after );

			}

			// Output with row close.
			$output = $left . $right . $output . $after . $mobile . '</div>';

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
