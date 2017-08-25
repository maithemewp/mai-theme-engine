<?php

/**
 * Add metaboxes for banner image and archive settings.
 *
 * To get banner image:
 *
 * $post_banner_image = wp_get_attachment_image( get_post_meta( $post_id, 'banner_id', true ), 'banner' );
 * $term_banner_image = wp_get_attachment_image( get_term_meta( $term_id, 'banner_id', true ), 'banner' );
 * $user_banner_image = wp_get_attachment_image( get_user_meta( $user_id, 'banner_id', true ), 'banner' );
 *
 * @return  void
 */
add_action( 'cmb2_admin_init', 'mai_cmb2_add_metaboxes' );
function mai_cmb2_add_metaboxes() {

	$metabox_title = __( 'Mai Content Archives', 'mai-pro-engine' );
	$upload_label  = __( 'Banner Image', 'mai-pro-engine' ); // Hidden on posts since show_names is false
	$button_text   = __( 'Add Banner Image', 'mai-pro-engine' );

	// Single Posts/Pages/CPTs
	$post = new_cmb2_box( array(
		'id'			=> 'mai_post_banner',
		'title'			=> __( 'Banner Area', 'mai-pro-engine' ),
		'object_types'	=> get_post_types( array('public' => true ), 'names' ),
		'context'		=> 'side',
		'priority'		=> 'low',
		'classes' 		=> 'mai-metabox',
		'show_on_cb'	=> '_mai_cmb_show_banner_visibility_field',
	) );
	$post->add_field( _mai_cmb_banner_visibility_config() );
	$post->add_field( _mai_cmb_banner_image_config() );

	// Taxonomy Terms
	$term = new_cmb2_box( array(
		'id'               => 'mai_term_settings',
		'title'            => $metabox_title,
		'object_types'     => array( 'term' ),
		'taxonomies'       => get_taxonomies( array( 'public' => true ), 'names' ),
		'new_term_section' => false,
		'context'          => 'normal',
		'priority'         => 'low',
		'classes'          => 'mai-metabox mai-content-archive-metabox',
	) );
	$term->add_field( _mai_cmb_banner_image_config() );
	$term->add_field( _mai_cmb_banner_visibility_config() );
	$term->add_field( _mai_cmb_remove_loop_config() );

	// User Profiles
	$user = new_cmb2_box( array(
		'id'           => 'mai_user_settings',
		'title'        => $metabox_title,
		'object_types' => array( 'user' ),
		'context'      => 'normal',
		'show_on_cb'   => 'mai_cmb_show_if_user_is_author_or_above',
		'classes'      => 'mai-metabox mai-content-archive-metabox',
	) );
	$user->add_field( _mai_cmb_banner_image_config() );
	$user->add_field( _mai_cmb_banner_visibility_config() );
	$user->add_field( _mai_cmb_remove_loop_config() );

}
