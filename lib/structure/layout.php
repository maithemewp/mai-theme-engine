<?php
/**
 * Mai Theme Engine.
 *
 * @author   Mike Hemberger
 *
 * @version  1.1.0
 */


/**
 * Helper function to force a layout in a template
 *
 * Used as shortcut second parameter for `add_filter()`.
 *
 * add_filter( 'genesis_pre_get_option_site_layout', '__mai_return_md_content' );
 */

function __mai_return_md_content() {
	return 'md-content';
}

function __mai_return_sm_content() {
	return 'sm-content';
}

function __mai_return_xs_content() {
	return 'xs-content';
}

/**
 * Add new sitewide layout options.
 *
 * @return  void
 */
add_action( 'init', 'mai_register_layouts' );
function mai_register_layouts() {

	// Layout image directory
	$dir = MAI_THEME_ENGINE_PLUGIN_URL . 'assets/images/layouts/';

	// Medium Content
	genesis_register_layout( 'md-content', array(
		'label' => __( 'Medium Content', 'mai-theme-engine' ),
		'img'   => $dir . 'mdc.gif',
	) );
	// Small Content
	genesis_register_layout( 'sm-content', array(
		'label' => __( 'Small Content', 'mai-theme-engine' ),
		'img'   => $dir . 'smc.gif',
	) );
	// Extra Small Content
	genesis_register_layout( 'xs-content', array(
		'label' => __( 'Extra Small Content', 'mai-theme-engine' ),
		'img'   => $dir . 'xsc.gif',
	) );
}

/**
 * Maybe set fallbacks for archive layouts.
 *
 * @return  array  The layouts.
 */
add_filter( 'genesis_site_layout', 'mai_genesis_site_layout' );
function mai_genesis_site_layout( $layout ) {

	/**
	 * Remove layout filter from Genesis Connect for WooCommerce.
	 * Mai Theme Engine handles this instead.
	 */
	remove_filter( 'genesis_pre_get_option_site_layout', 'genesiswooc_archive_layout' );

	return mai_get_layout();
}

/**
 * Maybe add no-sidebars body class to the head.
 *
 * @access  private
 *
 * @param   array  $classes  The body classes.
 *
 * @return  array  The modified body classes.
 */
add_filter( 'body_class', 'mai_sidebars_body_class' );
function mai_sidebars_body_class( $classes ) {

	$layout = genesis_site_layout();

	$no_sidebars = array(
		'full-width-content',
		'md-content',
		'sm-content',
		'xs-content',
	);
	$has_sidebar = array(
		'sidebar-content',
		'content-sidebar',
	);
	$has_sidebars = array(
		'sidebar-content-sidebar',
		'content-sidebar-sidebar',
		'sidebar-sidebar-content',
	);
	// Add .no-sidebar body class if don't have any sidebars
	if ( in_array( $layout, $no_sidebars ) ) {
		$classes[] = 'no-sidebars';
	} elseif ( in_array( $layout, $has_sidebar ) ) {
		$classes[] = 'has-sidebar';
	} elseif ( in_array( $layout, $has_sidebars ) ) {
		$classes[] = 'has-sidebar has-sidebars';
	}
	return $classes;
}

/**
 * Use Flexington for the main content and sidebar layout.
 *
 * @access  private
 *
 * @return  void
 */
add_action( 'genesis_before', 'mai_do_layout' );
function mai_do_layout() {

	$layout = genesis_site_layout();

	// No sidebars.
	$no_sidebars = array(
		'full-width-content',
		'md-content',
		'sm-content',
		'xs-content',
	);

	$one_sidebar = array(
		'sidebar-content',
		'content-sidebar',
	);
	$two_sidebars = array(
		'sidebar-content-sidebar',
		'content-sidebar-sidebar',
		'sidebar-sidebar-content',
	);

	// Remove primary sidebar.
	if ( in_array( $layout, $no_sidebars ) ) {
		remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
	}

	// Reposition secondary sidebar, we'll add it back later where we need it.
	remove_action( 'genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt' );

	// Add back the secondary sidebary where it really belongs.
	if ( in_array( $layout, $two_sidebars ) ) {
		add_action( 'genesis_after_content', function() {
			get_sidebar( 'alt' );
		}, 11 );
	}

	// Add content-no-sidebars class to the content.
	add_filter( 'genesis_attr_content', function( $attributes ) use ( $layout, $no_sidebars ) {
		$classes = '';
		// Add .content-no-sidebar class if don't have any sidebars
		if ( in_array( $layout, $no_sidebars ) ) {
			$classes .= ' content-no-sidebars';
		}
		$attributes['class'] .= $classes;
		return $attributes;
	});
}

/**
 * Filter the footer-widgets context of the genesis_structural_wrap to add a div before the closing wrap div.
 *
 * @param   string  $output             The markup to be returned.
 * @param   string  $original_output    Set to either 'open' or 'close'.
 *
 * @return  string  The footer markup
 */
add_filter( 'genesis_structural_wrap-footer-widgets', 'mai_footer_widgets_flex_row', 10, 2 );
function mai_footer_widgets_flex_row( $output, $original_output ) {
	if ( 'open' == $original_output ) {
		$output = $output . '<div class="row gutter-30">';
	}
	elseif ( 'close' == $original_output ) {
		$output = '</div>' . $output;
	}
	return $output;
}

/**
 * Filter the footer-widget markup to add flexington column classes.
 *
 * @param   array  $attributes  The array of attributes to be added to the footer widget wrap.
 *
 * @return  array  The attributes.
 */
add_filter( 'genesis_attr_footer-widget-area', 'mai_footer_widgets_flex_classes' );
function mai_footer_widgets_flex_classes( $attributes ) {
	switch ( mai_get_footer_widgets_count() ) {
		case '1':
			$classes = ' col col-xs-12 center-xs';
		break;
		case '2':
			$classes = ' col col-xs-12 col-sm-6';
		break;
		case '3':
			$classes = ' col col-xs-12 col-sm-6 col-md-4';
		break;
		case '4':
			$classes = ' col col-xs-12 col-sm-6 col-md-3';
		break;
		case '6':
			$classes = ' col col-xs-6 col-sm-4 col-md-2';
		break;
		default:
			$classes = ' col col-xs';
	}
	$attributes['class'] .= $classes;
	return $attributes;
}
