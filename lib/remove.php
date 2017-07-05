<?php
/**
 * Mai Theme.
 *
 * WARNING: This file is part of the core Mai Theme framework.
 * The goal is to keep all files in /lib/ untouched.
 * That way we can easily update the core structure of the theme on existing sites without breaking things
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.0
 */

// Remove the page title from the front page
add_action( 'genesis_before_content_sidebar_wrap', 'mai_remove_front_page_post_title' );
function mai_remove_front_page_post_title() {
	// Bail if not front page
	if ( ! is_front_page() ) {
		return;
	}
	// Remove post title
	remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
}

// Unregister header right widget area
unregister_sidebar( 'header-right' );

// Remove Blog & Archive Template From Genesis
add_filter( 'theme_page_templates', 'mai_remove_page_templates' );
function mai_remove_page_templates( $templates ) {
	unset( $templates['page_blog.php'] );
	unset( $templates['page_archive.php'] );
	return $templates;
}

// Remove edit post link
add_filter ( 'genesis_edit_post_link' , '__return_false' );

// Turn off gallery CSS
add_filter( 'use_default_gallery_style', '__return_false' );

// Disable the Genesis Favicon
remove_action( 'wp_head', 'genesis_load_favicon' );

/**
 * Remove default Superfish arguments
 * They are now added in global.js
 *
 * @author Gary Jones
 * @link   http://code.garyjones.co.uk/change-superfish-arguments
 *
 * @param string $url Existing URL.
 *
 * @return string Amended URL.
 */
add_filter( 'genesis_superfish_args_url', '__return_false' );

/**
 * Remove custom title/logo and blog page template metaboxes from Genesis theme options page
 *
 * @link http://www.billerickson.net/code/remove-metaboxes-from-genesis-theme-settings/
 */
add_action( 'genesis_theme_settings_metaboxes', 'mai_remove_genesis_theme_settings_metaboxes' );
function mai_remove_genesis_theme_settings_metaboxes( $_genesis_theme_settings_pagehook ) {
	remove_meta_box( 'genesis-theme-settings-header', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-posts', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-blogpage', $_genesis_theme_settings_pagehook, 'main' );
}

/**
 * Remove custom title/logo metabox from customizer
 * Priority had to be late for this to work
 *
 * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/
 */
add_action( 'customize_register', 'mai_remove_genesis_customizer_controls', 99 );
function mai_remove_genesis_customizer_controls( $wp_customize ) {
	$wp_customize->remove_control('blog_title');
}
