<?php

/**
 * This filter makes sure our custom settings are not wiped out when updating via Genesis > Theme Settings.
 * In 1.1.2 we were made aware of a critical bug where our custom settings were cleared anytime
 * a user would hit "Save" in Genesis > Theme Settings.
 *
 * This also prevents custom Mai settings from getting lost anytime 'genesis-settings' option is updated elsewhere.
 *
 * @since   1.1.3
 *
 * @return  array
 */
add_filter( 'pre_update_option_genesis-settings', 'mai_enforce_custom_genesis_settings', 10, 2 );
function mai_enforce_custom_genesis_settings( $new_value, $old_value ) {

	// If this is happening from a form submission page.
	if ( isset( $_POST ) || ! empty( $_POST ) ) {
		// If this is happening on a page that's submitting a 'genesis-settings' form.
		if ( isset( $_POST[ 'genesis-settings' ] ) || ! empty( $_POST[ 'genesis-settings' ] ) ) {
			// New value is the only genesis settings left in the form.
			$new_value = $_POST[ 'genesis-settings' ];
			// Fix slashes getting added to scripts. Argh.
			if ( isset( $new_value['header_scripts'] ) ) {
				$new_value['header_scripts'] = stripslashes( $new_value['header_scripts'] );
			}
			if ( isset( $new_value['footer_scripts'] ) ) {
				$new_value['footer_scripts'] = stripslashes( $new_value['footer_scripts'] );
			}
		}
	}
	// Make sure we don't lose old settings that don't exist in the $new_value array.
	$new_value = wp_parse_args( $new_value, $old_value );

	return $new_value;
}

/**
 * Filter the default options, adding our custom settings.
 * CPT settings defaults are filtered in /customizer/custom-post-types.php
 *
 * @param   array   $options  The genesis options.
 * @param   string  $setting  The setting key/name.
 *
 * @return  array   The modified options.
 */
add_filter( 'genesis_options', 'mai_genesis_options_defaults', 10, 2 );
function mai_genesis_options_defaults( $options, $setting ) {

	if ( GENESIS_SETTINGS_FIELD !== $setting ) {
		return $options;
	}

	// Default options.
	$all_options = mai_get_default_options();
	foreach ( $all_options as $key => $value ) {
		if ( ! isset( $options[$key] ) ) {
			$options[$key] = $value;
		}
	}

	// Return the modified options.
	return $options;
}

/**
 * Get a default option by name.
 *
 * @param  string  $key  The option name.
 *
 * @return string  The option value.
 */
function mai_get_default_option( $key ) {
	$options = mai_get_default_options();
	return $options[$key];
}

/**
 * Get all of the default options.
 *
 * @return  array  The options.
 */
function mai_get_default_options() {

	$defaults = array(
		// Genesis (G defaults are used for everything but Mai CPT settings).
		'content_archive'           => 'full',
		'content_archive_limit'     => 120,
		'content_archive_thumbnail' => 1,
		'image_size'                => 'one-third',
		'image_alignment'           => '',
		'posts_nav'                 => 'numeric',
		// Header & Footer Settings.
		'header_style'              => 'standard',
		'footer_widget_count'       => 2,
		'mobile_menu_style'         => 'standard',
		// Mai Banner.
		'enable_banner_area'        => 1,
		'banner_background_color'   => '#f1f1f1',
		'banner_id'                 => '',
		'banner_featured_image'     => 0,
		'banner_overlay'            => '',
		'banner_inner'              => '',
		'banner_height'             => 'md',
		'banner_content_width'      => 'auto',
		'banner_align_content'      => 'center',
		'banner_align_text'         => 'center',
		'banner_disable_post_types' => array(),
		'banner_disable_taxonomies' => array(),
		// Mai Content Types.
		'columns'                   => 1,
		'image_location'            => 'before_title',
		'more_link'                 => 0,
		'more_link_text'            => '',
		'remove_meta'               => array(),
		'posts_per_page'            => get_option( 'posts_per_page' ),
		// Mai Singular.
		'singular_image_page'       => 1,
		'singular_image_post'       => 1,
		'remove_meta_post'          => array(),
		// Mai Site Layout.
		'layout_archive'            => 'full-width-content',
		'layout_page'               => '',
		'layout_post'               => '',
		'boxed_elements'            => array( 'entry_singular', 'entry_archive', 'sidebar_widgets', 'sidebar_alt_widgets', 'author_box', 'after_entry_widgets', 'adjacent_entry_nav', 'comment_wrap', 'comment', 'comment_respond', 'pings' ),
		// Mai Utility.
		'mai_db_version'            => MAI_THEME_ENGINE_DB_VERSION,
	);

	/**
	 * Get post types.
	 *
	 * @return  array  Post types  array( 'name' => object )
	 */
	$post_types = mai_get_cpt_settings_post_types();

	if ( $post_types ) {
		// Loop through em.
		foreach ( $post_types as $post_type => $object ) {
			$defaults[ sprintf( 'banner_featured_image_%s', $post_type ) ]     = 0;
			$defaults[ sprintf( 'banner_disable_%s', $post_type ) ]            = 0;
			$defaults[ sprintf( 'banner_disable_taxonomies_%s', $post_type ) ] = array();
			$defaults[ sprintf( 'singular_image_%s', $post_type ) ]            = 1;
			$defaults[ sprintf( 'remove_meta_%s', $post_type ) ]               = array();
			$defaults[ sprintf( 'layout_%s', $post_type ) ]                    = '';
		}
	}
	return apply_filters( 'genesis_theme_settings_defaults', $defaults );
}

/**
 * Get a default CPT option by name.
 *
 * @param  string  $key  The option name.
 *
 * @return string  The option value.
 */
function mai_get_default_cpt_option( $key, $post_type = 'post' ) {
	$options = mai_get_default_cpt_options( $post_type );
	return $options[$key];
}

/**
 * Get all of the default CPT options.
 *
 * @return  array  The options.
 */
function mai_get_default_cpt_options( $post_type ) {
	// Defaults.
	$defaults = array(
		'banner_id'                       => '',
		'hide_banner'                     => 0,
		'layout'                          => mai_get_default_option( 'layout_archive' ),
		'enable_content_archive_settings' => 0,
		'columns'                         => mai_get_default_option( 'columns' ),
		'content_archive'                 => mai_get_default_option( 'content_archive' ),
		'content_archive_limit'           => mai_get_default_option( 'content_archive_limit' ),
		'content_archive_thumbnail'       => mai_get_default_option( 'content_archive_thumbnail' ),
		'image_location'                  => mai_get_default_option( 'image_location' ),
		'image_size'                      => mai_get_default_option( 'image_size' ),
		'image_alignment'                 => mai_get_default_option( 'image_alignment' ),
		'more_link'                       => mai_get_default_option( 'more_link' ),
		'more_link_text'                  => mai_get_default_option( 'more_link_text' ),
		'remove_meta'                     => mai_get_default_option( 'remove_meta' ),
		'posts_per_page'                  => mai_get_default_option( 'posts_per_page' ),
		'posts_nav'                       => mai_get_default_option( 'posts_nav' ),
	);
	return apply_filters( 'genesis_cpt_archive_settings_defaults', $defaults, $post_type );
}
