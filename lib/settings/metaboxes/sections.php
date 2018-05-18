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
	// Currently this will not work because scripts won't be loaded and [gallery] won't be parsed.
	// See https://github.com/CMB2/CMB2/issues/1083
	// if ( 'sections.php' === get_page_template_slug( filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT ) ) ) {
	// 	remove_post_type_support( 'page', 'editor' );
	// }

	// Bail if not on a sections template.
	if ( 'sections.php' !== get_page_template_slug( filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT ) ) ) {
		return;
	}
	// Instead we're forced to hide via CSS for now.
	echo '<style type="text/css">#postdivrich{display:none!important;}</style>';
}

/**
 * Add the CMB2 Sections repeater group.
 *
 * @return  void.
 */
add_action( 'cmb2_admin_init', 'mai_do_sections_metabox' );
function mai_do_sections_metabox() {

	// Pages with Sections template.
	$sections = new_cmb2_box( array(
		'id'           => 'mai_sections',
		'title'        => __( 'Sections', 'mai-theme-engine' ),
		'object_types' => array( 'page' ),
		'context'      => 'after_editor',
		'classes'      => 'mai-metabox',
		'show_on'      => array( 'key' => 'page-template', 'value' => 'sections.php' ),
	) );

	// Sections.
	$section = $sections->add_field( array(
		'id'          => 'mai_sections',
		'type'        => 'group',
		'repeatable'  => true,
		'options'     => array(
			'group_title'   => __( 'Section #{#}', 'mai-theme-engine' ),
			'add_button'    => __( 'Add Section', 'mai-theme-engine' ),
			'remove_button' => __( 'Remove Section', 'mai-theme-engine' ),
			'sortable'      => true,
		),
	) );

	// Settings.
	$sections->add_group_field( $section, array(
		'name'       => '<button class="button mai-section-settings-toggle"><span class="dashicons dashicons-admin-generic"></span>' . __( 'Settings', 'mai-theme-engine' ) . '</button>',
		'id'         => 'settings_title',
		'before_row' => '<div class="mai-section"><div class="mai-section-wrap"><div class="mai-section-settings"><div class="mai-section-settings-inner"><button class="button mai-section-settings-close">' . __( 'Close', 'mai-theme-engine' ) . '</button>',
		'type'       => 'title',
		'classes'    => 'mai-section-settings-toggle-wrap',
	) );

		// Background Color.
		$sections->add_group_field( $section, array(
			'name'    => __( 'Background Color', 'mai-theme-engine' ),
			'id'      => 'bg',
			'type'    => 'colorpicker',
			'default' => '', // Keep this empty, so CSS can control the defaults if left untouched
		) );

		// Background Image.
		$sections->add_group_field( $section, array(
			'name'         => __( 'Background Image', 'mai-theme-engine' ),
			'id'           => 'image',
			'type'         => 'file',
			'preview_size' => 'one-third',
			'options'      => array( 'url' => false ),
			'text'         => array(
				'add_upload_file_text' => __( 'Add Image', 'mai-theme-engine' ),
			),
		) );

		// Overlay.
		$sections->add_group_field( $section, array(
			'name'              => __( 'Overlay Style', 'mai-theme-engine' ),
			'id'                => 'overlay',
			'type'              => 'select',
			'select_all_button' => false,
			'options'           => array(
				''         => __( '- None -', 'genesis' ),
				'gradient' => __( 'Gradient', 'mai-theme-engine' ),
				'light'    => __( 'Light', 'mai-theme-engine' ),
				'dark'     => __( 'Dark', 'mai-theme-engine' ),
			),
		) );

		// Inner.
		$sections->add_group_field( $section, array(
			'name'              => __( 'Inner Style', 'mai-theme-engine' ),
			'id'                => 'inner',
			'type'              => 'select',
			'select_all_button' => false,
			'options'           => array(
				''      => __( '- None -', 'genesis' ),
				'light' => __( 'Light Box', 'mai-theme-engine' ),
				'dark'  => __( 'Dark Box', 'mai-theme-engine' ),
			),
		) );

		// Height.
		$sections->add_group_field( $section, array(
			'name'    => __( 'Height', 'mai-theme-engine' ),
			'id'      => 'height',
			'type'    => 'select',
			'default' => 'md',
			'options' => array(
				'auto' => __( 'Auto (Use height of content)', 'mai-theme-engine' ),
				'xs'   => __( 'Extra Small', 'mai-theme-engine' ),
				'sm'   => __( 'Small', 'mai-theme-engine' ),
				'md'   => __( 'Medium (Default)', 'mai-theme-engine' ),
				'lg'   => __( 'Large', 'mai-theme-engine' ),
				'xl'   => __( 'Extra Large', 'mai-theme-engine' ),
			),
		) );

		// Content Width.
		$sections->add_group_field( $section, array(
			'name'             => __( 'Content Width', 'mai-theme-engine' ),
			'id'               => 'content_width',
			'type'             => 'select',
			'show_option_none' => __( 'Default (Use Layout Width)', 'mai-theme-engine' ),
			'options'          => array(
				'xs'   => __( 'Extra Small', 'mai-theme-engine' ),
				'sm'   => __( 'Small', 'mai-theme-engine' ),
				'md'   => __( 'Medium', 'mai-theme-engine' ),
				'lg'   => __( 'Large', 'mai-theme-engine' ),
				'xl'   => __( 'Extra Large', 'mai-theme-engine' ),
				'full' => __( 'Full Width', 'mai-theme-engine' ),
			),
		) );

		// Content Alignment.
		$sections->add_group_field( $section, array(
			'name'             => __( 'Content Alignment', 'mai-theme-engine' ),
			'id'               => 'align',
			'type'             => 'select',
			'show_option_none' => __( '- None -', 'genesis' ),
			'options'          => array(
				'left'   => __( 'Left', 'mai-theme-engine' ),
				'center' => __( 'Center', 'mai-theme-engine' ),
				'right'  => __( 'Right', 'mai-theme-engine' ),
			),
		) );

		// Text Size.
		$sections->add_group_field( $section, array(
			'name'             => __( 'Text Size', 'mai-theme-engine' ),
			'id'               => 'text_size',
			'type'             => 'select',
			'show_option_none' => __( '- None -', 'genesis' ),
			'options' => array(
				'xs' => __( 'Extra Small', 'mai-theme-engine' ),
				'sm' => __( 'Small', 'mai-theme-engine' ),
				'md' => __( 'Medium (Default)', 'mai-theme-engine' ),
				'lg' => __( 'Large', 'mai-theme-engine' ),
				'xl' => __( 'Extra Large', 'mai-theme-engine' ),
			),
		) );

		// Advanced Settings.
		$sections->add_group_field( $section, array(
			'name'       => __( 'Advanced Settings', 'mai-theme-engine' ),
			'id'         => 'advanced_settings_title',
			'type'       => 'title',
			'before_row' => '<div class="mai-section-advanced-settings">',
			'classes'    => 'mai-section-advanced-settings-title',
		) );

		// ID.
		$sections->add_group_field( $section, array(
			'name'            => 'HTML id',
			'id'              => 'id',
			'type'            => 'text',
			'sanitization_cb' => 'sanitize_key',
		) );

		// Class.
		$sections->add_group_field( $section, array(
			'name'            => 'HTML additional classes',
			'id'              => 'class',
			'type'            => 'text',
			'sanitization_cb' => 'mai_sanitize_html_classes',
		) );

		// Context.
		$sections->add_group_field( $section, array(
			'name'            => 'Context',
			'id'              => 'context',
			'type'            => 'text',
			'after_row'       => '</div>',
			'sanitization_cb' => 'sanitize_title_with_dashes',
		) );

	// Title.
	$sections->add_group_field( $section, array(
		'name'       => 'Title',
		'id'         => 'title',
		'type'       => 'text',
		'before_row' => '</div></div><div class="mai-section-content">',
		'attributes' => array(
			'placeholder' => __( 'Enter section title here', 'mai-theme-engine' ),
			'class'       => 'widefat',
		),
	) );

	// Content.
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

	$content = mai_get_sections_html( $sections );

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
