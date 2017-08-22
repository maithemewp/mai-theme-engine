<?php

// Add singular body class to the head
add_filter( 'body_class', 'mai_singular_body_class' );
function mai_singular_body_class( $classes ) {
	if ( ! is_singular() ) {
		return $classes;
	}
	$classes[] = 'singular';
	return $classes;
}

/**
 * Add no-js body class to the head.
 * This get's changed to "js" if JS is enabled.
 *
 * @link https://www.paulirish.com/2009/avoiding-the-fouc-v3/
 */
add_filter( 'body_class', 'mai_js_detection_body_class' );
function mai_js_detection_body_class($classes) {
	$classes[] = 'no-js';
	return $classes;
}

/**
 * Remove the .no-js class from the html element via JS.
 * This allows styles targetting browsers without JS.
 *
 * Props Gary Jones for the initial push to start doing this
 * Props Sal Ferrarello for introducing me to this solution
 *
 * @link https://github.com/GaryJones/genesis-js-no-js/
 * @link https://www.paulirish.com/2009/avoiding-the-fouc-v3/
 */
add_action( 'genesis_before', 'mai_js_detection_script', 1 );
function mai_js_detection_script() {
	?>
	<script type="text/javascript">
		//<![CDATA[
		(function(){
			var c = document.body.className;
			c = c.replace(/no-js/, 'js');
			document.body.className = c;
		})();
		jQuery(function( $ ) {
			'use strict';
			jQuery( 'p:empty' ).remove();
		});
		//]]>
	</script>
	<?php
}

/**
 * Remove empty <p></p> tags from content.
 * We have a bunch of cleanup in the shortcodes,
 * but this seems much easier, though a bit of a hack.
 */
add_action( 'genesis_before', 'mai_html_cleanup_script', 1 );
function mai_html_cleanup_script() {
	?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(function( $ ) { 'use strict'; jQuery( 'p:empty' ).remove(); });
		//]]>
	</script>
	<?php
}

/**
 * Add body class to enabled specific settings.
 *
 * @param   array  $settings  The theme setting defaults.
 *
 * @return  array  The modified theme setting defaults.
 */
add_filter( 'genesis_theme_settings_defaults', 'mai_theme_settings_defaults' );
function mai_theme_settings_defaults( $settings ) {

	// General
	$settings['enable_sticky_header']      = 0;
	$settings['enable_shrink_header']      = 0;
	$settings['singular_image_post_types'] = array( 'post', 'page' );
	$settings['footer_widget_count']       = '2';
	$settings['mobile_menu_style']         = 'standard';

	// Banner
	$settings['enable_banner_area']        = 1;
	$settings['banner_background_color']   = '#f1f1f1';
	$settings['banner_id']                 = '';
	$settings['banner_overlay']            = '';
	$settings['banner_inner']              = '';
	$settings['banner_content_width']      = 'auto';
	$settings['banner_align_text']         = '';
	$settings['banner_featured_image']     = 0;
	$settings['banner_disable_post_types'] = array();
	$settings['banner_disable_taxonomies'] = array();

	// Archives
	$settings['columns']                   = 3;
	$settings['more_link']                 = 1;
	$settings['image_location']            = 'before_entry';
	$settings['image_alignment']           = '';
	$settings['image_size']                = 'one-third';
	$settings['remove_meta']               = array();

	// Layouts
	$settings['layout_page']               = '';
	$settings['layout_post']               = '';
	$settings['layout_archive']            = '';

	return $settings;
}

/**
 * Filter CPT defaults.
 *
 * @param   array  $settings  The theme setting defaults.
 *
 * @return  array  The modified theme setting defaults.
 */
// add_filter( 'genesis_cpt_archive_settings_defaults', 'mai_cpt_archive_settings_defaults' );
function mai_cpt_archive_settings_defaults( $settings ) {
	$settings['layout']                    = genesis_get_default_layout();
	$settings['columns']                   = 3;
	$settings['content_archive']           = 'full';
	$settings['content_archive_limit']     = 0;
	$settings['more_link']                 = 1;
	$settings['more_link_text']            = '';
	$settings['content_archive_thumbnail'] = '';
	$settings['image_location']            = '';
	$settings['image_size']                = '';
	$settings['image_alignment']           = '';
	$settings['remove_meta']               = '';
	$settings['posts_per_page']            = '';
	$settings['posts_nav']                 = '';
	return $settings;
}
