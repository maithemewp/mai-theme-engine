<?php

// If debug mode
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	/**
	 * Update CMB2 URL cause JS/CSS files 404
	 * when using the plugin via symlink
	 * in my local dev environment.
	 *
	 * This shouldn't affect anyone else, sorry for extra code just for me :P
	 */
	add_filter( 'cmb2_meta_box_url', 'mai_update_cmb2_meta_box_url' );
	function mai_update_cmb2_meta_box_url( $url ) {
		return str_replace( '/Users/JiveDig/Plugins/mai-pro-engine/', MAI_PRO_ENGINE_PLUGIN_URL, $url );
	}
}

/**
 * Add some inline styles to make the banner metabox a little more streamlined.
 *
 * @return  void
 */
add_action( 'cmb2_before_form', 'mai_before_mai_metabox', 10, 4 );
function mai_before_mai_metabox( $cmb_id, $object_id, $object_type, $cmb ) {
	// Bail if not the form(s) we want
	if ( ! in_array( $cmb_id, array( 'mai_content_archive', 'mai_post_banner', 'mai_term_settings', 'mai_user_settings' ) )
		&& ( strpos( $cmb_id, 'mai-cpt-archive-settings-' ) === false ) ) {
		return;
	}

	// Enqueue
	wp_enqueue_style( 'mai-cmb2' );
	wp_enqueue_script( 'mai-cmb2' );
}

function _mai_cmb_show_banner_visibility_field_nooooo_mooorrreeeee() {

	// Bail if not enabled at all
	if ( ! mai_is_banner_area_enabled_globally() ) {
		return false;
	}

	$show = true;

	global $pagenow, $typenow;

	// Get 'disabled' content, typecasted as array because it may return empty string if none
	$disable_post_types = (array) genesis_get_option( 'banner_disable_post_types' );

	// Posts
	if ( ( 'post.php' || 'post-new.php' ) == $pagenow ) {

		if ( in_array( $typenow, $disable_post_types ) ) {
			$show = false;
		}

	}
	// Terms
	elseif ( 'term.php' == $pagenow ) {

		// Get taxonomy
		$taxonomy = filter_input( INPUT_GET, 'taxonomy', FILTER_SANITIZE_STRING );

		if ( array_intersect( get_taxonomy( $taxonomy )->object_type, $disable_post_types ) ) {
			$show = false;
		}

	}

	return $show;
}

function _mai_cmb_show_banner_fields() {

	// Don't show field if banner area is globally disabled.
	if ( ! mai_is_banner_area_enabled_globally() ) {
		return false;
	}

	$show = true;

	global $pagenow, $typenow;

	// Posts.
	if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {

		// Post/Page.
		if ( in_array( $typenow, array( 'post', 'page' ) ) ) {
			if ( in_array( $typenow, (array) genesis_get_option( 'banner_disable_post_types' ) ) ) {
				$show = false;
			}
		}
		// CPT.
		else {
			$disable_post_type_key = sprintf( 'banner_disable_%s', $typenow );
			if ( (bool) genesis_get_option( $disable_post_type_key ) ) {
				$show = false;
			}
		}

	}
	// Terms.
	elseif ( 'term.php' === $pagenow ) {

		// Get taxonomy.
		$taxonomy = filter_input( INPUT_GET, 'taxonomy', FILTER_SANITIZE_STRING );

		// Don't show field on Woo product categories/tags, they have their own image field.
		if ( class_exists('WooCommerce') && in_array( $taxonomy, array( 'product_cat', 'product_tag' ) ) ) {
			$show = false;
		}
		// Not a Woo tax.
		else {
			$taxo_object = get_taxonomy( $taxonomy );
			// If taxo is registered to only one object. (taxo's registered to multiple objects are skipped for now).
			if ( $taxonomy && ( 1 === count( (array) $taxo_object->object_type ) ) ) {
				$disable_taxonomies = array();
				// If a post taxonomy.
				if ( in_array( 'post' , $taxo_object->object_type ) ) {
					$disable_taxonomies = (array) genesis_get_option( 'banner_disable_taxonomies' );
				}
				// CPT custom taxo.
				else {
					$disable_taxonomies = (array) genesis_get_option( sprintf( 'banner_disable_taxonomies_%s', $taxo_object->object_type[0] ) );
				}

				// If disabling.
				if ( in_array( $taxonomy, $disable_taxonomies ) ) {
					$show = false;
				}
			}
		}

	}

	return $show;
}

/**
 * Post metabox callback function to check if the
 * archive metabox should show for a post.
 *
 * Returns true if viewing the static blog page or WooCommerce shop page in the admin.
 *
 * @return bool
 */
function _mai_cmb_show_if_static_archive() {
	// Bail if not editing a post
	global $pagenow;
	if ( 'post.php' != $pagenow ) {
		return false;
	}

	$post_id       = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
	$posts_page_id = get_option('page_for_posts');

	// If static blog page.
	if ( ( $post_id == $posts_page_id ) ) {
		return true;
	}
	return false;
}

/**
 * User metabox callback function to check if the
 * banner metabox should show for a user.
 *
 * Returns true if the viewed profile's user can publish posts.
 *
 * @return bool
 */
function _mai_cmb_show_if_user_is_author_or_above() {
	global $user_id;
	if ( user_can( $user_id, 'publish_posts' ) ) {
		return true;
	}
	return false;
}

function _mai_cmb_banner_disable_post_types_config() {
	$options    = array();
	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	if ( $post_types ) {
		foreach ( $post_types as $post_type ) {
			$options[$post_type->name] = $post_type->label;
		}
	}
	return array(
		'name'              => __( 'Disable Banner Area on<br />(Post Types)', 'mai-pro-engine' ),
		'desc'              => __( 'Disable the banner area for single post posts.', 'mai-pro-engine' ),
		'id'                => 'banner_disable_post_types',
		'type'              => 'multicheck',
		'select_all_button' => false,
		'options'           => $options,
		'show_on_cb'        => 'mai_is_banner_area_enabled_globally',
	);
}

function _mai_cmb_banner_disable_taxonomies_config() {
	$options    = array();
	$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxo ) {
			$options[$taxo->name] = $taxo->label;
		}
	}
	return array(
		'name'              => __( 'Disable Banner Area on<br />(Taxonomies)', 'mai-pro-engine' ),
		'desc'              => __( 'Disable the banner area for taxonomy archives.', 'mai-pro-engine' ),
		'id'                => 'banner_disable_taxonomies',
		'type'              => 'multicheck',
		'select_all_button' => false,
		'options'           => $options,
		'show_on_cb'        => 'mai_is_banner_area_enabled_globally',
	);
}

function _mai_cmb_banner_visibility_config() {
	return array(
		'name'            => __( 'Banner Visibility', 'mai-pro-engine' ),
		'desc'            => __( 'Hide the banner area', 'mai-pro-engine' ),
		'id'              => 'hide_banner',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
		'show_on_cb'      => '_mai_cmb_show_banner_fields',
	);
}

function _mai_cmb_banner_image_config() {
	return array(
		'name'         => __( 'Banner Image', 'mai-pro-engine' ),
		'id'           => 'banner',
		'type'         => 'file',
		'preview_size' => 'one-third',
		'options'      => array( 'url' => false ),
		'text'         => array(
			'add_upload_file_text' => __( 'Add Image', 'mai-pro-engine' ),
		),
		'show_on_cb' => '_mai_cmb_show_banner_fields',
	);
}

function _mai_cmb_content_archive_settings_title_config() {
	return array(
		'name' => '',
		'desc' => __( 'If enabled, these will override the default content archive settings', 'mai-pro-engine' ),
		'type' => 'title',
		'id'   => 'mai_content_archives_title',
	);
}

function _mai_cmb_content_enable_archive_settings_config() {
	return array(
		'before_row'      => '<div class="mai-archive-setting-wrap">',
		'name'            => __( 'Archive Settings', 'mai-pro-engine' ),
		'desc'            => __( 'Enable custom archive settings', 'mai-pro-engine' ),
		'id'              => 'enable_content_archive_settings',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
	);
}

function _mai_cmb_remove_loop_config() {
	return array(
		// 'after_row'       => '</div>',
		'name'            => __( 'Hide Entries', 'mai-pro-engine' ),
		'desc'            => __( 'Hide entries from this archive', 'mai-pro-engine' ),
		'id'              => 'remove_loop',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
	);
}

function _mai_cmb_columns_config() {
	return array(
		'before_row' => '<div class="mai-archive-settings-wrap">',
		'name'       => __( 'Content Columns', 'mai-pro-engine' ),
		'desc'       => __( 'Display content in multiple columns.', 'mai-pro-engine' ),
		'id'         => 'columns',
		'type'       => 'select',
		'default'    => 1,
		'options'    => array(
			1 => __( '- None -', 'genesis' ),
			2 => __( '2 Columns', 'mai-pro-engine' ),
			3 => __( '3 Columns', 'mai-pro-engine' ),
			4 => __( '4 Columns', 'mai-pro-engine' ),
			6 => __( '6 Columns', 'mai-pro-engine' ),
		),
	);
}

function _mai_cmb_content_archive_config() {
	return array(
		'name'    => __( 'Content', 'genesis' ),
		'id'      => 'content_archive',
		'type'    => 'select',
		'default' => 'excerpts',
		'options' => array(
			'none'     => __( 'No content', 'mai-pro-engine' ),
			'full'     => __( 'Entry content', 'genesis' ),
			'excerpts' => __( 'Entry excerpts', 'genesis' ),
		),
	);
}

function _mai_cmb_content_archive_thumbnail_config() {
	return array(
		'name'            => __( 'Featured Image', 'genesis' ),
		'desc'            => __( 'Include the Featured Image', 'mai-pro-engine' ),
		'id'              => 'content_archive_thumbnail',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
	);
}

function _mai_cmb_image_location_config() {
	return array(
		'name'         => __( 'Image Location:', 'mai-pro-engine' ),
		'id'           => 'image_location',
		'before_field' => __( 'Image Location:', 'mai-pro-engine' ) . ' ',
		'type'         => 'select',
		'default'      => 'before_entry',
		'options'      => array(
			'background'     => __( 'Background Image', 'mai-pro-engine' ),
			'before_entry'   => __( 'Before Entry', 'mai-pro-engine' ),
			'before_title'   => __( 'Before Title', 'mai-pro-engine' ),
			'after_title'    => __( 'After Title', 'mai-pro-engine' ),
			'before_content' => __( 'Before Content', 'mai-pro-engine' ),
		),
	);
}

function _mai_cmb_image_size_config() {
	// Get our image size options
	$sizes = genesis_get_image_sizes();
	$size_options = array();
	foreach ( $sizes as $index => $value ) {
		$size_options[$index] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
	}
	return array(
		'name'         => __( 'Image Size:', 'genesis' ),
		'id'           => 'image_size',
		'type'         => 'select',
		'before_field' => __( 'Image Size:', 'genesis' ) . ' ',
		'default'      => 'one-third',
		'options'      => $size_options,
	);
}

function _mai_cmb_image_alignment_config() {
	return array(
		'name'             => __( 'Image Alignment:', 'genesis' ),
		'id'               => 'image_alignment',
		'type'             => 'select',
		'before_field'     => __( 'Image Alignment:', 'genesis' ) . ' ',
		'show_option_none' => __( '- None -', 'genesis' ),
		'options'          => array(
			'aligncenter' => __( 'Center', 'genesis' ),
			'alignleft'   => __( 'Left', 'genesis' ),
			'alignright'  => __( 'Right', 'genesis' ),
		),
	);
}

function _mai_cmb_content_archive_limit_config() {
	return array(
		'name'         => __( 'Limit content to', 'genesis' ),
		'id'           => 'content_archive_limit',
		'type'         => 'text_small',
		'before_field' => __( 'Limit content to', 'genesis' ) . ' ',
		'after_field'  => ' ' . __( 'characters', 'genesis' ),
		'attributes'   => array(
			'type'    => 'number',
			'pattern' => '\d*',
		),
	);
}

function _mai_cmb_more_link_config() {
	return array(
		'name'            => __( 'More Link', 'mai-pro-engine' ),
		'desc'            => __( 'Include the Read More link', 'mai-pro-engine' ),
		'id'              => 'more_link',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
	);
}

function _mai_cmb_meta_config() {
	return array(
		'name'    => __( 'Entry Meta', 'mai-pro-engine' ),
		'id'      => 'remove_meta',
		'type'    => 'multicheck',
		'options' => array(
			'post_info' => __( 'Remove Post Info', 'mai-pro-engine' ),
			'post_meta' => __( 'Remove Post Meta', 'mai-pro-engine' ),
		),
		'select_all_button' => false,
	);
}

function _mai_cmb_posts_per_page_config() {
	return array(
		'name'       => __( 'Entries Per Page', 'mai-pro-engine' ),
		'desc'       => __( 'The max number of posts to show, per page.', 'mai-pro-engine' ),
		'id'         => 'posts_per_page',
		'type'       => 'text_small',
		'default'    => 12,
		'attributes' => array(
			'type'    => 'number',
			'pattern' => '\d*',
		),
	);
}

function _mai_cmb_posts_nav_config() {
	return array(
		'after_row' => '</div>', // close .mai-archive-settings-wrap
		'name'      => __( 'Entry Pagination', 'genesis' ),
		'id'        => 'posts_nav',
		'type'      => 'select',
		'default'   => 'numeric',
		'options'   => array(
			'prev-next' => __( 'Previous / Next', 'genesis' ),
			'numeric'   => __( 'Numeric', 'genesis' ),
		),
	);
}

function _mai_cmb_sanitize_one_zero( $value ) {
	return absint( filter_var( $value, FILTER_VALIDATE_BOOLEAN ) );
}

function _mai_cmb_escape_one_zero( $value ) {
	return absint( filter_var( $value, FILTER_VALIDATE_BOOLEAN ) );
}
