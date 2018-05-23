<?php

/**
 * Only enqueue our admin CSS/JS when showing our CMB2 forms.
 *
 * @param  array   $cmb_id       The current box ID.
 * @param  int     $object_id    The ID of the current object.
 * @param  string  $object_type  The type of object you are working with.
 *                               Usually `post` (this applies to all post-types).
 *                               Could also be `comment`, `user` or `options-page`.
 * @param  array  $cmb           This CMB2 object.
 *
 * @return void.
 */
add_action( 'cmb2_before_form', 'cmb2_test_before_form', 10, 4 );
function cmb2_test_before_form( $cmb_id, $object_id, $object_type, $cmb ) {
	/**
	 * Default CMB2 forms.
	 * Note: CPT Archive Settings metaboxes handled in Mai_Genesis_CPT_Settings_Metabox class.
	 */
	$mai_cmb = array(
		'mai_sections',
		'mai_post_banner',
		'mai_term_settings',
		'mai_user_settings',
	);
	// Bail if not our CMB2 metabox.
	if ( ! in_array( $cmb_id, $mai_cmb ) ) {
		return;
	}
	// Enqueue scripts and styles, previously registered.
	wp_enqueue_style( 'mai-admin' );
	wp_enqueue_script( 'mai-admin' );
}

/**
 * Add metaboxes for banner image and archive settings.
 *
 * To get banner image:
 *
 * $post_banner_image = wp_get_attachment_image( get_post_meta( $post_id, 'banner_id', true ), 'banner' );
 * $term_banner_image = wp_get_attachment_image( get_term_meta( $term_id, 'banner_id', true ), 'banner' );
 * $user_banner_image = wp_get_attachment_image( get_user_meta( $user_id, 'banner_id', true ), 'banner' );
 *
 * @return  void.
 */
add_action( 'cmb2_admin_init', 'mai_cmb2_add_metaboxes' );
function mai_cmb2_add_metaboxes() {

	$metabox_title = __( 'Mai Content Archives', 'mai-theme-engine' );
	$upload_label  = __( 'Banner Image', 'mai-theme-engine' ); // Hidden on posts since show_names is false
	$button_text   = __( 'Add Banner Image', 'mai-theme-engine' );

	// Single Entries.
	$post_banner = new_cmb2_box( array(
		'id'           => 'mai_post_banner',
		'title'        => __( 'Banner Image', 'mai-theme-engine' ),
		'object_types' => get_post_types( array('public' => true ), 'names' ),
		'context'      => 'side',
		'priority'     => 'low',
		'classes'      => 'mai-metabox',
		'show_on_cb'   => '_mai_cmb_show_banner_fields',
	) );
	$post_banner->add_field( _mai_cmb_banner_image_config() );

	// Single Entries.
	$post_settings = new_cmb2_box( array(
		'id'           => 'mai_post_settings',
		'title'        => __( 'Visibility Settings', 'mai-theme-engine' ),
		'object_types' => get_post_types( array('public' => true ), 'names' ),
		'context'      => 'side',
		'priority'     => 'low',
		'classes'      => 'mai-metabox',
	) );
	$post_settings->add_field( _mai_cmb_banner_visibility_config() );
	$post_settings->add_field( _mai_cmb_breadcrumb_visibility_config() );
	$post_settings->add_field( _mai_cmb_featured_image_visibility_config() );
	$post_settings->add_field( _mai_cmb_title_visibility_config() );

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
	$term->add_field( _mai_cmb_content_archive_settings_title_config() );
	$term->add_field( _mai_cmb_content_enable_archive_settings_config() );
	$term->add_field( _mai_cmb_columns_config() );
	$term->add_field( _mai_cmb_content_archive_thumbnail_config() );
	$term->add_field( _mai_cmb_image_location_config() );
	$term->add_field( _mai_cmb_image_size_config() );
	$term->add_field( _mai_cmb_image_alignment_config() );
	$term->add_field( _mai_cmb_content_archive_config() );
	$term->add_field( _mai_cmb_content_archive_limit_config() );
	$term->add_field( _mai_cmb_more_link_config() );
	$term->add_field( _mai_cmb_meta_config() );
	$term->add_field( _mai_cmb_posts_per_page_config() );
	$term->add_field( _mai_cmb_posts_nav_config() );

	// User Profiles
	$user = new_cmb2_box( array(
		'id'           => 'mai_user_settings',
		'title'        => $metabox_title,
		'object_types' => array( 'user' ),
		'context'      => 'normal',
		'show_on_cb'   => 'mai_cmb_show_if_user_is_author_or_above',
		'classes'      => 'mai-metabox mai-content-archive-metabox',
	) );
	$user->add_field( _mai_cmb_banner_visibility_config() );
	$user->add_field( _mai_cmb_banner_image_config() );
	$user->add_field( _mai_cmb_remove_loop_config() );
	$user->add_field( _mai_cmb_content_archive_settings_title_config() );
	$user->add_field( _mai_cmb_content_enable_archive_settings_config() );
	$user->add_field( _mai_cmb_columns_config() );
	$user->add_field( _mai_cmb_content_archive_thumbnail_config() );
	$user->add_field( _mai_cmb_image_location_config() );
	$user->add_field( _mai_cmb_image_size_config() );
	$user->add_field( _mai_cmb_image_alignment_config() );
	$user->add_field( _mai_cmb_content_archive_config() );
	$user->add_field( _mai_cmb_content_archive_limit_config() );
	$user->add_field( _mai_cmb_more_link_config() );
	$user->add_field( _mai_cmb_meta_config() );
	$user->add_field( _mai_cmb_posts_per_page_config() );
	$user->add_field( _mai_cmb_posts_nav_config() );

}

/**
 * Add custom meta box(es) to Genesis Theme Settings page
 *
 * @param   string $_genesis_theme_settings_pagehook
 *
 * @return  void.
 */
add_action( 'genesis_theme_settings_metaboxes', 'mai_theme_settings_customizer_link' );
function mai_theme_settings_customizer_link( $pagehook ) {
	// Add metabox shop notice.
	add_meta_box( 'mai_theme_settings_customizer_links', __( 'Mai Theme Settings', 'mai-theme-engine' ), 'mai_do_theme_settings_customizer_links', $pagehook, 'main', 'high' );
}

/**
 * Outputs the content of the meta box.
 *
 * @link    https://www.slushman.com/how-to-link-to-the-customizer/
 *
 * @return  void.
 */
function mai_do_theme_settings_customizer_links() {
	// Mai Settings.
	printf( '<p><strong>%s</strong></p>', __( 'Edit theme settings in the customizer:', 'mai-theme-engine' ) );
	echo '<p>';
		printf( '<a class="button" href="%s">%s</a>&nbsp;&nbsp;', mai_get_customizer_section_link( 'mai_settings' ), __( 'Mai Settings', 'mai-theme-engine' ) );
		printf( '<a class="button" href="%s">%s</a>&nbsp;&nbsp;', mai_get_customizer_section_link( 'mai_banner_area' ), __( 'Mai Banner Area', 'mai-theme-engine' ) );
		printf( '<a class="button" href="%s">%s</a>&nbsp;&nbsp;', mai_get_customizer_section_link( 'mai_content_archives' ), __( 'Mai Content Archives', 'mai-theme-engine' ) );
		printf( '<a class="button" href="%s">%s</a>&nbsp;&nbsp;', mai_get_customizer_section_link( 'mai_content_singular' ), __( 'Mai Content Singular', 'mai-theme-engine' ) );
		printf( '<a class="button" href="%s">%s</a>&nbsp;&nbsp;', mai_get_customizer_section_link( 'mai_site_layouts' ), __( 'Mai Site Layouts', 'mai-theme-engine' ) );
		printf( '<a class="button" href="%s">%s</a>&nbsp;&nbsp;', mai_get_customizer_section_link( 'genesis_breadcrumbs' ), __( 'Breadcrumbs', 'mai-theme-engine' ) );
		printf( '<a class="button" href="%s">%s</a>&nbsp;&nbsp;', mai_get_customizer_section_link( 'genesis_comments' ), __( 'Comments & Trackbacks', 'mai-theme-engine' ) );
	echo '</p>';
}
