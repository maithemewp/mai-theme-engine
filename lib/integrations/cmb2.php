<?php

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
        'id'               => 'post_banner_edit',
        'title'            => __( 'Banner Image', 'maitheme' ),
        'object_types'     => $post_types,
        'context' 		   => 'side',
        'priority' 		   => 'low',
    ) );
    $post->add_field( array(
    	'name'			=> __( 'Hide', 'maitheme' ),
		'desc'			=> __( 'Hide banner on this post', 'maitheme' ),
		'id'			=> 'mai_hide_banner',
		'type'			=> 'checkbox',
    ) );
    $post->add_field( array(
		'name'			=> __( 'Banner Image', 'maitheme' ),
		'desc'			=> __( 'Leave empty to use Featured Image', 'maitheme' ),
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false, // Hide the text input for the url
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => __( 'Add Image', 'maitheme' ),
	    ),
    ) );

    // Taxonomy Terms
    $term = new_cmb2_box( array(
        'id'               => 'term_banner_edit',
        'title'            => __( 'Banner Image', 'maitheme' ),
        'object_types'     => array( 'term' ),
        'taxonomies'       => $taxonomies,
        'new_term_section' => true,
        'context' 		   => 'normal',
    ) );
    $term->add_field( array(
		'name'			=> __( 'Banner Image', 'maitheme' ),
		'desc'			=> __( 'Add banner image', 'maitheme' ),
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false,
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => __( 'Add Image', 'maitheme' ),
	    ),
    ) );

    // User Profiles
    $user = new_cmb2_box( array(
        'id'               => 'user_banner_edit',
        'title'            => __( 'Banner Image', 'maitheme' ),
        'object_types'     => array( 'user' ),
        'context' 		   => 'normal',
    ) );
    $user->add_field( array(
		'name'			=> __( 'Banner Image', 'maitheme' ),
		'desc'			=> __( 'Add banner image', 'maitheme' ),
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false,
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => __( 'Add Image', 'maitheme' ),
	    ),
    ) );

}
