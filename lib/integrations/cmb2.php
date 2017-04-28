<?php

// If debug mode
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	/**
	 * Update CMB2 URL cause JS/CSS files 404
	 * when using the plugin via symlink
	 * in local dev environments.
	 */
	add_filter( 'cmb2_meta_box_url', 'mai_update_cmb2_meta_box_url' );
	function mai_update_cmb2_meta_box_url( $url ) {
	    return MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . 'includes/vendor/CMB2';
	}
}

/**
 * Add some inline styles to make the banner metabox a little more streamlined.
 *
 * @return  void
 */
add_action( 'cmb2_before_form', 'mai_before_banner_image_metabox', 10, 4 );
function mai_before_banner_image_metabox( $cmb_id, $object_id, $object_type, $cmb ) {

	if ( ! in_array( $cmb_id, array( 'mai_post_banner', 'mai_term_banner', 'mai_user_banner' ) )
		&& ( strpos( $cmb_id, 'mai-cpt-archive-settings-' ) === false ) ) {
		return;
	}

    echo '<style type="text/css">
        .mai-banner-metabox .cmb-row {
	        padding: 10px 0 0 !important;
		    border: none !important;
		    margin-bottom: 0 !important;
		}
		.mai-banner-metabox span.cmb2-metabox-description {
		    color: inherit !important;
		    font-style: inherit !important;
		}
		.mai-banner-metabox .cmb-th {
			margin-top: -5px !important;
		}
		.mai-banner-metabox .cmb-type-checkbox .cmb-th {
			margin-top: -10px !important;
		}
        </style>';

}

/**
 * Add banner image field
 *
 * $post_banner_image = wp_get_attachment_image( get_post_meta( $post_id, 'banner_id', true ), 'banner' );
 * $term_banner_image = wp_get_attachment_image( get_term_meta( $term_id, 'banner_id', true ), 'banner' );
 * $user_banner_image = wp_get_attachment_image( get_user_meta( $user_id, 'banner_id', true ), 'banner' );
 *
 * @return  void
 */
add_action( 'cmb2_admin_init', 'mai_banner_image_metabox' );
function mai_banner_image_metabox() {

	// Bail if banner area is not enabled
	if ( ! mai_is_banner_area_enabled() ) {
		return;
	}

	$post_types = get_post_types( array('public' => true ), 'names' );
	// Remove attachments
	unset( $post_types['attachment'] );
	// Filter post_types so devs can change where this shows up
	$post_types = apply_filters( 'mai_banner_post_types', $post_types );

	$taxonomies = get_taxonomies( array( 'public' => true ) );
	// Remove Woo Product Cat since it has its own image field
	unset( $taxonomies['product_cat'] );
	// Filter taxonomies so devs can change where this shows up
	$taxonomies = apply_filters( 'mai_banner_taxonomies', $taxonomies );

	$metabox_title = __( 'Banner Area', 'maitheme' );
	$upload_label  = __( 'Banner Image', 'maitheme' ); // Hidden on posts since show_names is false
	$button_text   = __( 'Add Banner Image', 'maitheme' );

	// Posts/Pages/CPTs
    $post = new_cmb2_box( array(
		'id'			=> 'mai_post_banner',
		'title'			=> $metabox_title,
		'object_types'	=> $post_types,
		'context'		=> 'side',
		'priority'		=> 'low',
		'classes' 		=> 'mai-banner-metabox',
    ) );
    $post->add_field( array(
		'name'			=> __( 'Banner Visibility', 'maitheme' ),
		'show_names'	=> false,
		'desc'			=> __( 'Hide banner area', 'maitheme' ),
		'id'			=> 'mai_hide_banner',
		'type'			=> 'checkbox',
    ) );
    $post->add_field( array(
		'name'			=> $upload_label,
		'show_names'	=> false,
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false, // Hide the text input for the url
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => $button_text,
	    ),
    ) );

    // Taxonomy Terms
    $term = new_cmb2_box( array(
        'id'               => 'mai_term_banner',
        'title'            => $metabox_title,
        'object_types'     => array( 'term' ),
        'taxonomies'       => $taxonomies,
        'new_term_section' => true,
        'context' 		   => 'normal',
        'classes' 		   => 'mai-banner-metabox',
    ) );
    $term->add_field( array(
    	'name'			=> __( 'Banner Visibility', 'maitheme' ),
		'desc'			=> __( 'Hide banner area', 'maitheme' ),
		'id'			=> 'mai_hide_banner',
		'type'			=> 'checkbox',
    ) );
    $term->add_field( array(
		'name'			=> $upload_label,
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false,
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => $button_text,
	    ),
    ) );

    // User Profiles
    $user = new_cmb2_box( array(
		'id'			=> 'mai_user_banner',
		'title'			=> $metabox_title,
		'object_types'	=> array( 'user' ),
		'context'		=> 'normal',
		'show_on_cb' 	=> 'mai_cmb_show_if_user_is_author_or_above',
		'classes' 		=> 'mai-banner-metabox',
    ) );
    $user->add_field( array(
		'name'	=> __( 'Banner Visibility', 'maitheme' ),
		'desc'	=> __( 'Hide banner area', 'maitheme' ),
		'id'	=> 'mai_hide_banner',
		'type'	=> 'checkbox',
    ) );
    $user->add_field( array(
		'name'			=> $upload_label,
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false,
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => $button_text,
	    ),
    ) );

}

/**
 * User metabox callback function to check if the
 * banner metabox should show for a user.
 *
 * @return bool
 */
function mai_cmb_show_if_user_is_author_or_above() {
	global $user_id;
	if ( user_can( $user_id, 'publish_posts' ) ) {
		return true;
	}
	return false;
}
