<?php

add_action( 'cmb2_before_form', 'mai_before_banner_image_metabox', 10, 4 );
function mai_before_banner_image_metabox( $cmb_id, $object_id, $object_type, $cmb ) {

	if ( ! in_array( $cmb_id, array( 'mai_post_banner', 'mai_term_banner', 'mai_user_banner' ) ) ) {
		return;
	}
    echo '<style type="text/css">
        #cmb2-metabox-mai_post_banner .cmb-row,
        #cmb2-metabox-mai_term_banner .cmb-row,
        #cmb2-metabox-mai_user_banner .cmb-row {
	        padding: 10px 0 0 !important;
		    border: none !important;
		    margin-bottom: 0 !important;
		}
		#cmb2-metabox-mai_post_banner span.cmb2-metabox-description,
		#cmb2-metabox-mai_term_banner span.cmb2-metabox-description,
		#cmb2-metabox-mai_user_banner span.cmb2-metabox-description {
		    color: inherit !important;
		    font-style: inherit !important;
		}
        </style>';

}

/**
 * Add banner image field
 *
 * $post_banner_image = wp_get_attachment_image( get_post_meta( $post_id, 'banner_id', true ), 'banner' );
 * $term_banner_image = wp_get_attachment_image( get_term_meta( $term_id, 'banner_id', true ), 'banner' );
 * $user_banner_image = wp_get_attachment_image( get_user_meta( $user_id, 'banner_id', true ), 'banner' );
 */
add_action( 'cmb2_admin_init', 'mai_banner_image_metabox' );
function mai_banner_image_metabox() {

	// Bail if banner area is not enabled
	if ( ! mai_is_banner_area_enabled() ) {
		return;
	}

	$post_types = array_keys( get_post_types( array('public' => true, '_builtin' => true ) ) );
	unset($post_types['attachment']);
	$post_types = apply_filters( 'mai_banner_post_types', $post_types );

	$taxonomies = get_taxonomies( array( 'public' => true ) );
	// Remove Woo Product Cat since it has its own image field
	unset( $taxonomies['product_cat'] );
	// Filter taxonomies so devs can change where this shows up
	$taxonomies = apply_filters( 'mai_banner_taxonomies', $taxonomies );

	$metabox_id = 'banner_edit';
	$title 		= __( 'Banner Image', 'maitheme' );

	// Posts/Pages/CPTs
    $post = new_cmb2_box( array(
        'id'               => 'mai_post_banner',
        'title'            => __( 'Banner Image', 'maitheme' ),
        'object_types'     => $post_types,
        'context' 		   => 'side',
        'priority' 		   => 'low',
    ) );
    $post->add_field( array(
		'desc'			=> __( 'Hide banner on this post', 'maitheme' ),
		'id'			=> 'mai_hide_banner',
		'type'			=> 'checkbox',
    ) );
    $post->add_field( array(
		'name'			=> __( 'Banner Image', 'maitheme' ),
		'show_names' 	=> false,
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false, // Hide the text input for the url
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => __( 'Add Banner Image', 'maitheme' ),
	    ),
    ) );

    // Taxonomy Terms
    $term = new_cmb2_box( array(
        'id'               => 'mai_term_banner',
        'title'            => __( 'Banner Image', 'maitheme' ),
        'object_types'     => array( 'term' ),
        'taxonomies'       => $taxonomies,
        'new_term_section' => true,
        'context' 		   => 'normal',
    ) );
    $term->add_field( array(
		'name'			=> __( 'Banner Image', 'maitheme' ),
		'show_names' 	=> false,
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false,
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => __( 'Add Banner Image', 'maitheme' ),
	    ),
    ) );

    // User Profiles
    $user = new_cmb2_box( array(
        'id'               => 'mai_user_banner',
        'title'            => __( 'Banner Image', 'maitheme' ),
        'object_types'     => array( 'user' ),
        'context' 		   => 'normal',
    ) );
    $user->add_field( array(
		'name'			=> __( 'Banner Image', 'maitheme' ),
		'show_names' 	=> false,
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false,
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => __( 'Add Banner Image', 'maitheme' ),
	    ),
    ) );

}
