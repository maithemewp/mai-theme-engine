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
		return str_replace( '/Users/JiveDig/Plugins/mai-theme-engine/', MAI_THEME_ENGINE_PLUGIN_URL, $url );
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

function _mai_cmb_banner_show_on_cb( $field ) {

	global $pagenow, $typenow;

	$banner_enabled = mai_is_banner_area_enabled_globally();

	// Posts.
	if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {

		// Don't show field if banner area is globally disabled.
		if ( ! $banner_enabled ) {
			return false;
		}

		// Post/Page.
		if ( in_array( $typenow, array( 'post', 'page' ) ) ) {
			if ( in_array( $typenow, (array) genesis_get_option( 'banner_disable_post_types' ) ) ) {
				return false;
			}
			if ( class_exists( 'WooCommerce' ) && ( get_the_ID() === (int) get_option( 'woocommerce_shop_page_id' ) ) ) {
				return false;
			}
		}
		// CPT.
		else {
			$disable_post_type_key = sprintf( 'banner_disable_%s', $typenow );
			if ( (bool) genesis_get_option( $disable_post_type_key ) ) {
				return false;
			}
		}
	}
	// Terms.
	elseif ( 'term.php' === $pagenow ) {

		// Get taxonomy.
		$taxonomy = filter_input( INPUT_GET, 'taxonomy', FILTER_SANITIZE_STRING );

		// If Woo default taxo.
		if ( class_exists( 'WooCommerce' ) && in_array( $taxonomy, array( 'product_cat', 'product_tag' ) ) ) {
			// If checking for the banner image.
			if ( 'banner' === $field->args['id'] ) {
				// Hide the banner image field if Product Category, Woo has their own category image field.
				if ( 'product_cat' === $taxonomy ) {
					return false;
				}
			}
			// Not checking banner image field.
			else {
				// Don't show field if banner area is globally disabled.
				if ( ! $banner_enabled ) {
					return false;
				}
				return true;
			}
		}
		// Not a Woo default taxo.
		else {

			// Banner image field should always be visible on terms, since they are used by [grid].
			if ( 'banner' === $field->args['id'] ) {
				return true;
			}

			// Don't show ohter fields if banner area is globally disabled.
			if ( ! $banner_enabled ) {
				return false;
			}

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
					return false;
				}
			}
		}

	}

	return true;
}

function _mai_cmb_hide_breacrumbs_show_on_cb() {
	// Hide on landing page template.
	if ( 'landing.php' === get_post_meta( get_the_ID(), '_wp_page_template', true ) ) {
		return false;
	}
	// Hide on static front page. This is handled in Genesis Theme Settings.
	if ( get_the_ID() === (int) get_option( 'page_on_front' ) ) {
		return false;
	}
	return true;
}

function _mai_cmb_hide_featured_image_show_on_cb() {
	// Bail if post type doesn't support featured image.
	if ( ! post_type_supports( get_post_type(), 'thumbnail' ) ) {
		return false;
	}
	global $typenow;
	// Check if auto-displaying the featured image.
	$key     = sprintf( 'singular_image_%s', $typenow );
	$display = genesis_get_option( $key );
	// Bail if not displaying.
	if ( ! $display ) {
		return false;
	}
	// Bail if editing the WooCommerce Shop page.
	if ( class_exists( 'WooCommerce' ) && get_the_ID() === (int) get_option( 'woocommerce_shop_page_id' ) ) {
		return false;
	}
	return true;
}

function _mai_cmb_show_if_genesis_title_toggle_not_active() {
	if ( class_exists( 'BE_Title_Toggle' ) ) {
		return false;
	}
	return true;
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
		'name'              => __( 'Disable Banner Area on<br />(Post Types)', 'mai-theme-engine' ),
		'desc'              => __( 'Disable the banner area for single post posts.', 'mai-theme-engine' ),
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
		'name'              => __( 'Disable Banner Area on<br />(Taxonomies)', 'mai-theme-engine' ),
		'desc'              => __( 'Disable the banner area for taxonomy archives.', 'mai-theme-engine' ),
		'id'                => 'banner_disable_taxonomies',
		'type'              => 'multicheck',
		'select_all_button' => false,
		'options'           => $options,
		'show_on_cb'        => 'mai_is_banner_area_enabled_globally',
	);
}

function _mai_cmb_banner_image_config() {
	return array(
		'name'         => __( 'Banner/Featured Image', 'mai-theme-engine' ),
		'id'           => 'banner',
		'type'         => 'file',
		'preview_size' => 'one-third',
		'options'      => array( 'url' => false ),
		'text'         => array(
			'add_upload_file_text' => __( 'Add Image', 'mai-theme-engine' ),
		),
		'show_on_cb'   => '_mai_cmb_banner_show_on_cb',
	);
}

function _mai_cmb_banner_visibility_config() {
	return array(
		'name'            => __( 'Banner Visibility', 'mai-theme-engine' ),
		'desc'            => __( 'Hide the banner area', 'mai-theme-engine' ),
		'id'              => 'hide_banner',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
		'show_on_cb'      => '_mai_cmb_banner_show_on_cb',
	);
}

function _mai_cmb_breadcrumb_visibility_config() {
	return array(
		'desc'            => __( 'Hide the breadcrumbs', 'mai-theme-engine' ),
		'id'              => 'mai_hide_breadcrumbs',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
		'show_on_cb'      => '_mai_cmb_hide_breacrumbs_show_on_cb',
	);
}

function _mai_cmb_featured_image_visibility_config() {
	return array(
		'desc'            => __( 'Hide the featured image', 'mai-theme-engine' ),
		'id'              => 'mai_hide_featured_image',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
		'show_on_cb'      => '_mai_cmb_hide_featured_image_show_on_cb',
	);
}

function _mai_cmb_title_visibility_config() {
	return array(
		'desc'            => __( 'Hide the title', 'mai-theme-engine' ),
		'id'              => 'be_title_toggle_hide',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
		'show_on_cb'      => '_mai_cmb_show_if_genesis_title_toggle_not_active',
	);
}

function _mai_cmb_content_archive_settings_title_config() {
	return array(
		'name' => '',
		'desc' => __( 'If enabled, these will override the default content archive settings', 'mai-theme-engine' ),
		'type' => 'title',
		'id'   => 'mai_content_archives_title',
	);
}

function _mai_cmb_content_enable_archive_settings_config() {
	return array(
		'before_row'      => '<div class="mai-archive-setting-wrap">',
		'name'            => __( 'Archive Settings', 'mai-theme-engine' ),
		'desc'            => __( 'Enable custom archive settings', 'mai-theme-engine' ),
		'id'              => 'enable_content_archive_settings',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
	);
}

function _mai_cmb_remove_loop_config() {
	return array(
		// 'after_row'       => '</div>',
		'name'            => __( 'Hide Entries', 'mai-theme-engine' ),
		'desc'            => __( 'Hide entries from this archive', 'mai-theme-engine' ),
		'id'              => 'remove_loop',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
	);
}

function _mai_cmb_columns_config() {
	return array(
		'before_row' => '<div class="mai-archive-settings-wrap">',
		'name'       => __( 'Content Columns', 'mai-theme-engine' ),
		'desc'       => __( 'Display content in multiple columns.', 'mai-theme-engine' ),
		'id'         => 'columns',
		'type'       => 'select',
		'default'    => 1,
		'options'    => array(
			1 => __( 'None', 'mai-theme-engine' ),
			2 => __( '2 Columns', 'mai-theme-engine' ),
			3 => __( '3 Columns', 'mai-theme-engine' ),
			4 => __( '4 Columns', 'mai-theme-engine' ),
			6 => __( '6 Columns', 'mai-theme-engine' ),
		),
	);
}

function _mai_cmb_content_archive_config() {
	return array(
		'name'    => __( 'Content', 'mai-theme-engine' ),
		'id'      => 'content_archive',
		'type'    => 'select',
		'default' => 'excerpts',
		'options' => array(
			'none'     => __( 'No content', 'mai-theme-engine' ),
			'full'     => __( 'Entry content', 'mai-theme-engine' ),
			'excerpts' => __( 'Entry excerpts', 'mai-theme-engine' ),
		),
	);
}

function _mai_cmb_content_archive_thumbnail_config() {
	return array(
		'name'            => __( 'Featured Image', 'mai-theme-engine' ),
		'desc'            => __( 'Include the Featured Image', 'mai-theme-engine' ),
		'id'              => 'content_archive_thumbnail',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
	);
}

function _mai_cmb_image_location_config() {
	return array(
		'name'         => __( 'Image Location:', 'mai-theme-engine' ),
		'id'           => 'image_location',
		'before_field' => __( 'Image Location:', 'mai-theme-engine' ) . ' ',
		'type'         => 'select',
		'default'      => 'before_entry',
		'options'      => array(
			'background'     => __( 'Background Image', 'mai-theme-engine' ),
			'before_entry'   => __( 'Before Entry', 'mai-theme-engine' ),
			'before_title'   => __( 'Before Title', 'mai-theme-engine' ),
			'after_title'    => __( 'After Title', 'mai-theme-engine' ),
			'before_content' => __( 'Before Content', 'mai-theme-engine' ),
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
		'name'         => __( 'Image Size:', 'mai-theme-engine' ),
		'id'           => 'image_size',
		'type'         => 'select',
		'before_field' => __( 'Image Size:', 'mai-theme-engine' ) . ' ',
		'default'      => 'one-third',
		'options'      => $size_options,
	);
}

function _mai_cmb_image_alignment_config() {
	return array(
		'name'             => __( 'Image Alignment:', 'mai-theme-engine' ),
		'id'               => 'image_alignment',
		'type'             => 'select',
		'before_field'     => __( 'Image Alignment:', 'mai-theme-engine' ) . ' ',
		'show_option_none' => __( 'None', 'mai-theme-engine' ),
		'options'          => array(
			'aligncenter' => __( 'Center', 'mai-theme-engine' ),
			'alignleft'   => __( 'Left', 'mai-theme-engine' ),
			'alignright'  => __( 'Right', 'mai-theme-engine' ),
		),
	);
}

function _mai_cmb_content_archive_limit_config() {
	return array(
		'name'         => __( 'Limit content to', 'mai-theme-engine' ),
		'id'           => 'content_archive_limit',
		'type'         => 'text_small',
		'before_field' => __( 'Limit content to', 'mai-theme-engine' ) . ' ',
		'after_field'  => ' ' . __( 'characters', 'mai-theme-engine' ),
		'attributes'   => array(
			'type'    => 'number',
			'pattern' => '\d*',
		),
	);
}

function _mai_cmb_more_link_config() {
	return array(
		'name'            => __( 'More Link', 'mai-theme-engine' ),
		'desc'            => __( 'Include the Read More link', 'mai-theme-engine' ),
		'id'              => 'more_link',
		'type'            => 'checkbox',
		'sanitization_cb' => '_mai_cmb_sanitize_one_zero',
		'escape_cb'       => '_mai_cmb_escape_one_zero',
	);
}

function _mai_cmb_meta_config() {
	return array(
		'name'    => __( 'Entry Meta', 'mai-theme-engine' ),
		'id'      => 'remove_meta',
		'type'    => 'multicheck',
		'options' => array(
			'post_info' => __( 'Remove Post Info', 'mai-theme-engine' ),
			'post_meta' => __( 'Remove Post Meta', 'mai-theme-engine' ),
		),
		'select_all_button' => false,
	);
}

function _mai_cmb_posts_per_page_config() {
	return array(
		'name'       => __( 'Entries Per Page', 'mai-theme-engine' ),
		'desc'       => __( 'The max number of posts to show, per page.', 'mai-theme-engine' ),
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
		'name'      => __( 'Entry Pagination', 'mai-theme-engine' ),
		'id'        => 'posts_nav',
		'type'      => 'select',
		'default'   => 'numeric',
		'options'   => array(
			'prev-next' => __( 'Previous / Next', 'mai-theme-engine' ),
			'numeric'   => __( 'Numeric', 'mai-theme-engine' ),
		),
	);
}

function _mai_cmb_sanitize_one_zero( $value ) {
	return absint( filter_var( $value, FILTER_VALIDATE_BOOLEAN ) );
}

function _mai_cmb_escape_one_zero( $value ) {
	return absint( filter_var( $value, FILTER_VALIDATE_BOOLEAN ) );
}
