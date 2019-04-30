<?php

/**
 * Add header trigger element.
 * For basicScroll.
 *
 * @access  private
 * @since   1.8.0
 *
 * @return  void
 */
add_action( 'genesis_header', 'mai_header_trigger', 3 );
function mai_header_trigger() {
	echo '<span id="header-trigger-wrap"><span id="header-trigger"></span></span>';
}

/**
 * Add an image inline in the site title element for the main logo.
 * The custom logo is added via the Customiser.
 *
 * @since   1.3.0
 *
 * @param   string  $content  The existing site title content.
 *
 * @return  string|HTML       The logo markup
 */
add_filter( 'genesis_markup_site-title_content', 'mai_custom_logo' );
function mai_custom_logo( $content ) {
	// If no custom logo, return the original content.
	if ( ! ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) ) {
		return $content;
	}
	return get_custom_logo();
}

/**
 * Fix Google Schema error, "The property logo is not recognised by Google for an object of type WPHeader".
 *
 * @param   string  $html  The logo HTML.
 *
 * @return  string  The modified HTML.
 */
add_filter( 'get_custom_logo', function( $html ) {
	return str_replace( 'itemprop="logo"', 'itemprop="image"', $html );
});

/**
 * Add logo vs text class to site title.
 *
 * @since   1.10.0
 *
 * @param   array  $attributes  Current attributes.
 *
 * @return  array  The modified attributes.
 */
add_filter( 'genesis_attr_site-title', 'mai_site_title_type' );
function mai_site_title_type( $attributes ) {
	// Bail if has a logo.
	if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
		return $attributes;
	}
	$attributes['class'] .= ' has-text-title';
	return $attributes;
}

/**
 * Use h1 on site title when banner area isn't enabled,
 * and using sections template without h1 in content and without any section titles.
 *
 * @param  string  $wrap
 *
 * @return string
 */
add_filter( 'genesis_site_title_wrap', 'mai_site_title_wrap' );
function mai_site_title_wrap( $wrap ) {

	// Bail if not a singular Sections template.
	if ( ! ( is_singular() && is_page_template( 'sections.php' ) ) ) {
		return $wrap;
	}

	// Bail if banner area is enabled since this should have the h1.
	if ( mai_is_banner_area_enabled() ) {
		return $wrap;
	}

	// If section content has an h1.
	$has_h1 = mai_sections_has_h1( get_the_ID() );

	if ( ! $has_h1 ) {

		// If any section has a title.
		$has_title = mai_sections_has_title( get_the_ID() );

		// If no title, use h1 on title.
		if ( ! $has_title ) {
			$wrap = 'h1';
		}

	}

	return $wrap;
}

/**
 * Add class for screen readers to site description.
 * This will keep the site description markup but will not have any visual presence on the page.
 *
 * @param   array  $attributes  Current attributes.
 *
 * @return  array  The modified attributes.
 */
add_filter( 'genesis_attr_site-description', 'mai_hide_site_description' );
function mai_hide_site_description( $attributes ) {
	$attributes['class'] .= ' screen-reader-text';
	return $attributes;
}

/**
 * Add header before markup if there is content.
 *
 * @return  void
 */
add_action( 'genesis_header', 'mai_header_before', 2 );
function mai_header_before() {
	$before = mai_get_do_action( 'mai_header_before' );
	if ( ! $before ) {
		return;
	}
	printf( '<div %s><div class="wrap">%s</div></div>',
		genesis_attr( 'header-before', array( 'class' => 'header-before text-sm' ) ),
		$before
	);
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
	$left       = mai_get_do_action( 'mai_header_left' );
	$right      = mai_get_do_action( 'mai_header_right' );
	$mobile     = ! mai_is_side_menu_enabled() ? mai_get_mobile_menu() : '';
	$has_mobile = apply_filters( '_mai_mobile_menu', true );

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
	add_filter( 'genesis_structural_wrap-header', function( $output, $original_output ) use ( $left, $right, $has_mobile, $mobile ) {

		if ( 'open' == $original_output ) {

			// Default classes.
			$row['class'] = 'site-header-row row middle-xs';

			// Alignment.
			$row['class'] .= $has_mobile ? ' between-xs' : ' around-xs';

			// Justification. If no left or right, and we have mobile. If no mobile we already have around-xs.
			$row['class'] .= ( ! ( $left || $right ) && $has_mobile ) ? ' around-md' : '';

			// Output with row open.
			$output = $output . sprintf( '<div %s>', genesis_attr( 'site-header-row', $row ) );

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

			// Output with row close.
			$output = $left . $right . $output . $mobile . '</div>';

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
 * Add header after markup if there is content.
 *
 * @return  void
 */
add_action( 'genesis_header', 'mai_header_after' );
function mai_header_after() {
	$after = mai_get_do_action( 'mai_header_after' );
	if ( ! $after ) {
		return;
	}
	printf( '<div %s><div class="wrap">%s</div></div>',
		genesis_attr( 'header-after', array( 'class' => 'header-after' ) ),
		$after
	);
}

/**
 * Add the header before content.
 *
 * @return  void
 */
add_action( 'mai_header_before', 'mai_do_header_before' );
function mai_do_header_before() {

	// Bail if no content
	if ( ! is_active_sidebar( 'header_before' ) ) {
		return;
	}

	// Variable function.
	$header_before = function( $attributes ) {
		$attributes['class'] = 'nav-header-before';
		return $attributes;
	};

	// Change the header before menu class.
	add_filter( 'genesis_attr_nav-header', $header_before );

	// Before Header widget area
	_mai_add_widget_header_menu_args();
	genesis_widget_area( 'header_before' );
	_mai_remove_widget_header_menu_args();

	// Remove the filter.
	remove_filter( 'genesis_attr_nav-header', $header_before );
}

/**
 * Add the header left content.
 *
 * @return  void
 */
add_action( 'mai_header_left', 'mai_do_header_left' );
function mai_do_header_left() {

	// Bail if no content
	if ( ! ( is_active_sidebar( 'header_left' ) || has_nav_menu( 'header_left' ) ) ) {
		return;
	}

	// Header Left widget area
	if ( is_active_sidebar( 'header_left' ) ) {
		_mai_add_widget_header_menu_args();
		genesis_widget_area( 'header_left' );
		_mai_remove_widget_header_menu_args();
	}

	// Header Left menu
	if ( has_nav_menu( 'header_left' ) ) {
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
	if ( ! ( is_active_sidebar( 'header_right' ) || has_nav_menu( 'header_right' ) ) ) {
		return;
	}

	// Header Right widget area
	if ( is_active_sidebar( 'header_right' ) ) {
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
