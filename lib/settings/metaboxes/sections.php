<?php

/**
 * Remove the admin page editor on Sections template pages.
 *
 * @return  void.
 */
add_action( 'admin_head', 'hide_editor' );
function hide_editor() {
	global $pagenow;

	if ( 'post.php' !== $pagenow ) {
		return;
	}
	if ( ! isset( $_GET['post'] ) ) {
		return;
	}
	if ( 'sections.php' === get_page_template_slug( absint( $_GET['post'] ) ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
}

/**
 * Add the CMB2 Sections repeater group.
 *
 * @return  void.
 */
add_action( 'cmb2_admin_init', 'mai_do_sections_metabox' );
function mai_do_sections_metabox() {

	// Posts/Pages/CPTs
	$sections = new_cmb2_box( array(
		'id'           => 'mai_sections',
		'title'        => __( 'Sections', 'mai-pro-engine' ),
		'object_types' => array( 'page' ),
		'context'      => 'after_editor',
		'classes'      => 'mai-metabox',
		'show_on'      => array( 'key' => 'page-template', 'value' => 'sections.php' ),
	) );

	$section = $sections->add_field( array(
		'id'          => 'mai_sections',
		'type'        => 'group',
		'repeatable'  => true,
		'options'     => array(
			'group_title'   => __( 'Section #{#}', 'mai-pro-engine' ),
			'add_button'    => __( 'Add Section', 'mai-pro-engine' ),
			'remove_button' => __( 'Remove Section', 'mai-pro-engine' ),
			'sortable'      => true,
		),
	) );

	// Settings
	$sections->add_group_field( $section, array(
		'name'       => '<button class="button mai-section-settings-toggle"><span class="dashicons dashicons-admin-generic"></span>' . __( 'Settings', 'mai-pro-engine' ) . '</button>',
		'id'         => 'settings_title',
		'before_row' => '<div class="mai-section"><div class="mai-section-wrap"><div class="mai-section-settings"><div class="mai-section-settings-inner"><button class="button mai-section-settings-close">' . __( 'Close', 'mai-pro-engine' ) . '</button>',
		'type'       => 'title',
		'classes'    => 'mai-section-settings-toggle-wrap',
	) );

	// Background Color
	$sections->add_group_field( $section, array(
		'name'    => __( 'Background Color', 'mai-pro-engine' ),
		'id'      => 'bg',
		'type'    => 'colorpicker',
		'default' => '', // Keep this empty, so CSS can control the defaults if left untouched
	) );

	// Background Image
	$sections->add_group_field( $section, array(
		'name'         => __( 'Background Image', 'mai-pro-engine' ),
		'id'           => 'image',
		'type'         => 'file',
		'preview_size' => 'one-third',
		'options'      => array( 'url' => false ),
		'text'         => array(
			'add_upload_file_text' => __( 'Add Image', 'mai-pro-engine' ),
		),
	) );

	// Overlay
	$sections->add_group_field( $section, array(
		'name'              => __( 'Overlay Style', 'mai-pro-engine' ),
		'id'                => 'overlay',
		'type'              => 'select',
		'select_all_button' => false,
		'options'           => array(
			''         => __( '- None -', 'genesis' ),
			'gradient' => __( 'Gradient', 'mai-pro-engine' ),
			'light'    => __( 'Light', 'mai-pro-engine' ),
			'dark'     => __( 'Dark', 'mai-pro-engine' ),
		),
	) );

	// Inner
	$sections->add_group_field( $section, array(
		'name'              => __( 'Inner Style', 'mai-pro-engine' ),
		'id'                => 'inner',
		'type'              => 'select',
		'select_all_button' => false,
		'options'           => array(
			''      => __( '- None -', 'genesis' ),
			'light' => __( 'Light Box', 'mai-pro-engine' ),
			'dark'  => __( 'Dark Box', 'mai-pro-engine' ),
		),
	) );

	// Height
	$sections->add_group_field( $section, array(
		'name'    => __( 'Height', 'mai-pro-engine' ),
		'id'      => 'height',
		'type'    => 'select',
		'default' => 'md',
		'options' => array(
			'auto'  => __( 'Auto (Use height of content)', 'mai-pro-engine' ),
			'sm'    => __( 'Small', 'mai-pro-engine' ),
			'md'    => __( 'Medium', 'mai-pro-engine' ),
			'lg'    => __( 'Large', 'mai-pro-engine' ),
		),
	) );

	// Content Width
	$sections->add_group_field( $section, array(
		'name'             => __( 'Content Width', 'mai-pro-engine' ),
		'id'               => 'content_width',
		'type'             => 'select',
		'show_option_none' => __( 'Default (Use Layout Width)', 'mai-pro-engine' ),
		'options'          => array(
			'xs'   => __( 'Extra Small', 'mai-pro-engine' ),
			'sm'   => __( 'Small', 'mai-pro-engine' ),
			'md'   => __( 'Medium', 'mai-pro-engine' ),
			'lg'   => __( 'Large', 'mai-pro-engine' ),
			'xl'   => __( 'Extra Large', 'mai-pro-engine' ),
			'full' => __( 'Full Width', 'mai-pro-engine' ),
		),
	) );

	// Content Alignment
	$sections->add_group_field( $section, array(
		'name'             => __( 'Content Alignment', 'mai-pro-engine' ),
		'id'               => 'align',
		'type'             => 'select',
		'show_option_none' => __( '- None -', 'genesis' ),
		'options'          => array(
			'left'   => __( 'Left', 'mai-pro-engine' ),
			'center' => __( 'Center', 'mai-pro-engine' ),
			'right'  => __( 'Right', 'mai-pro-engine' ),
		),
	) );

	// Text Size
	$sections->add_group_field( $section, array(
		'name'             => __( 'Text Size', 'mai-pro-engine' ),
		'id'               => 'text_size',
		'type'             => 'select',
		'show_option_none' => __( '- None -', 'genesis' ),
		'options' => array(
			'xs' => __( 'Extra Small', 'mai-pro-engine' ),
			'sm' => __( 'Small', 'mai-pro-engine' ),
			'md' => __( 'Medium (Default)', 'mai-pro-engine' ),
			'lg' => __( 'Large', 'mai-pro-engine' ),
			'xl' => __( 'Extra Large', 'mai-pro-engine' ),
		),
	) );

	// Advanced Settings
	$sections->add_group_field( $section, array(
		'name'       => __( 'Advanced Settings', 'mai-pro-engine' ),
		'id'         => 'advanced_settings_title',
		'type'       => 'title',
		'before_row' => '<div class="mai-section-advanced-settings">',
		'classes'    => 'mai-section-advanced-settings-title',
	) );

	// ID
	$sections->add_group_field( $section, array(
		'name'            => 'HTML id',
		'id'              => 'id',
		'type'            => 'text',
		'sanitization_cb' => 'sanitize_key',
	) );

	// Class
	$sections->add_group_field( $section, array(
		'name'            => 'HTML additional classes',
		'id'              => 'class',
		'type'            => 'text',
		'after_row'       => '</div>',
		'sanitization_cb' => 'mai_sanitize_html_classes',
	) );

	// Title
	$sections->add_group_field( $section, array(
		'name'       => 'Title',
		'id'         => 'title',
		'type'       => 'text',
		'before_row' => '</div></div><div class="mai-section-content">',
		'attributes' => array(
			'placeholder' => __( 'Enter section title here', 'mai-pro-engine' ),
			'class'       => 'widefat',
		),
	) );

	// Content
	$sections->add_group_field( $section, array(
		'name'            => 'Content',
		'id'              => 'content',
		'type'            => 'wysiwyg',
		'after_row'       => '</div></div></div>',
		'sanitization_cb' => 'mai_sanitize_post_content',
	) );

}

/**
 * Save section meta content to the_content for search indexing and SEO content analysis.
 *
 * @param   int     $post_id  The ID of the current object
 * @param   string  $updated  Array of field ids that were updated.
 *                            Will only include field ids that had values change.
 * @param   array   $cmb      This CMB2 object
 *
 * @return  void.
 */
add_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );
function mai_save_sections_to_the_content( $post_id, $updated, $cmb ) {

	// Get the sections
	$sections = get_post_meta( $post_id, 'mai_sections', true );

	// Bail if no sections
	if ( ! $sections ) {
		return;
	}

	$content = '';

	// Loop through each section
	foreach ( $sections as $section ) {

		// Add h2 titles to the_content.
		$content .= ! empty( $section['title'] ) ? sprintf( '<h2>%s</h2>', sanitize_text_field( $section['title'] ) ) : '';

		// Add section content to the_content.
		$content .= ! empty( $section['content'] ) ? mai_get_processed_content( $section['content'] ) : '';

	}

	// Remove this function so it doesn't cause infinite loop error.
	remove_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );

	// Update the post content in the DB.
	$updated = wp_update_post( array(
		'ID'           => $post_id,
		'post_content' => $content,
	) );

	// Add this function back.
	add_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );
}
