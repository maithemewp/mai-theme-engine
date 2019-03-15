<?php

/**
 * Remove the admin page editor on Sections template pages.
 *
 * @access  private
 *
 * @return  void.
 */
add_action( 'admin_head', 'mai_sections_hide_editor' );
function mai_sections_hide_editor() {

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
 * @access  private
 *
 * @return  void
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
				''         => __( 'None', 'mai-theme-engine' ),
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
				''      => __( 'None', 'mai-theme-engine' ),
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
			'id'               => 'align_content',
			'type'             => 'select',
			'show_option_none' => __( 'None', 'mai-theme-engine' ),
			'options'          => array(
				'left'         => __( 'Left', 'mai-theme-engine' ),
				'lefttop'      => __( 'Left Top', 'mai-theme-engine' ),
				'leftbottom'   => __( 'Left Bottom', 'mai-theme-engine' ),
				'center'       => __( 'Center', 'mai-theme-engine' ),
				'centertop'    => __( 'Center Top', 'mai-theme-engine' ),
				'centerbottom' => __( 'Center Bottom', 'mai-theme-engine' ),
				'right'        => __( 'Right', 'mai-theme-engine' ),
				'righttop'     => __( 'Right Top', 'mai-theme-engine' ),
				'rightbottom'  => __( 'Right Bottom', 'mai-theme-engine' ),
			),
		) );

		// Text Alignment.
		$sections->add_group_field( $section, array(
			'name'             => __( 'Text Alignment', 'mai-theme-engine' ),
			'id'               => 'align',
			'type'             => 'select',
			'show_option_none' => __( 'None', 'mai-theme-engine' ),
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
			'show_option_none' => __( 'None', 'mai-theme-engine' ),
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
		'default'         => '',
		'after_row'       => '</div></div></div>',
		'sanitization_cb' => 'mai_sanitize_post_content',
	) );

}

/**
 * Add the CMB2 Sections repeater group.
 *
 * @access  private
 *
 * @since   1.3.0
 *
 * @return  void
 */
add_action( 'cmb2_admin_init', 'mai_do_sections_import_export_metabox' );
function mai_do_sections_import_export_metabox() {

	// Pages with Sections template.
	$importexport = new_cmb2_box( array(
		'id'           => 'mai_sections_importexport',
		'title'        => __( 'Sections Import/Export', 'mai-theme-engine' ),
		'object_types' => array( 'page' ),
		'context'      => 'normal', //  'normal', 'advanced', or 'side'
		'priority'     => 'low',
		'classes'      => 'mai-metabox',
		'show_on'      => array( 'key' => 'page-template', 'value' => 'sections.php' ),
	) );

	// Import JSON.
	$importexport->add_field( array(
		'name'       => __( 'Import (JSON)', 'mai-theme-engine' ),
		'desc'       => __( 'Paste JSON code and update the page/post to import.', 'mai-theme-engine' ),
		'default'    => '',
		'id'         => 'mai_sections_json_import',
		'type'       => 'textarea_small',
		'save_field' => false, // Otherwise CMB2 will end up removing the value.
		'before_row' => '<ul id="mai-sections-importexport-toggles"><li style="display:inline-block;"><a class="mai-importexport-toggle active" href="#mai-sections-importexport-1">' . __( 'Import', 'mai-theme-engine' ) . '</li><li style="display:inline-block;"><a class="mai-importexport-toggle" href="#mai-sections-importexport-2">' . __( 'Export', 'mai-theme-engine' ) . '</a></li></ul><div id="mai-sections-importexport-1" class="mai-sections-importexport-content">',
	) );

	// Import images.
	$importexport->add_field( array(
		'name'       => __( '&nbsp;', 'mai-theme-engine' ),
		'desc'       => __( '(Experimental) Import Section background images. Images must be on a publicly accessible URL.', 'mai-theme-engine' ),
		'id'         => 'mai_sections_json_import_images',
		'type'       => 'checkbox',
		'save_field' => false, // Otherwise CMB2 will end up removing the value.
		'after_row'  => '</div>',
	) );

	// Export.
	$importexport->add_field( array(
		'name'       => __( 'Export (JSON)', 'mai-theme-engine' ),
		'desc'       => __( 'Copy and paste this code into the "Import" field of another Sections template.', 'mai-theme-engine' ),
		'default_cb' => '_mai_cmb_get_sections_json',
		'id'         => 'mai_sections_json_export',
		'type'       => 'textarea_small',
		'save_field' => false, // Otherwise CMB2 will end up removing the value.
		'before_row' => '<div id="mai-sections-importexport-2" class="mai-sections-importexport-content" style="display:none;">',
		'after_row'  => '</div>',
		'attributes' => array(
			'readonly' => 'readonly',
		),
	) );

}

/**
 * Fires after all fields have been saved.
 * Runs via save_post in CMB2.
 *
 * @since   1.3.0
 *
 * @param   int     $object_id  The ID of the current object.
 * @param   string  $updated    Array of field ids that were updated.
 *                              Will only include field ids that had values change.
 * @param   array   $cmb        This CMB2 object.
 *
 * @return  void
 */
add_action( 'cmb2_save_post_fields_mai_sections', 'mai_import_section_data', 8, 3 );
function mai_import_section_data( $object_id, $updated, $cmb ) {

	// Check required $_POST variables and security nonce.
	if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return;
	}

	// If not importing section data.
	if ( empty( $_POST ) || ! isset( $_POST['mai_sections_json_import'] ) || empty( $_POST['mai_sections_json_import'] ) ) {
		return;
	}

	$submission   = trim( $_POST['mai_sections_json_import'] );
	$section_data = json_decode( stripslashes( $submission ), true );

	if ( ! $section_data ) {
		return;
	}

	// Whether to import images.
	$import_images = filter_var( $_POST['mai_sections_json_import_images'], FILTER_VALIDATE_BOOLEAN );

	// Remove these functions so it doesn't cause infinite loop error.
	remove_action( 'cmb2_save_post_fields_mai_sections', 'mai_import_section_data', 8, 3 );
	remove_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );

	// Update.
	mai_update_sections_template( $section_data, $object_id, $import_images );

	// Add these functions back.
	add_action( 'cmb2_save_post_fields_mai_sections', 'mai_import_section_data', 8, 3 );
	add_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );
}

/**
 * Save section meta content to the_content for search indexing and SEO content analysis.
 * Runs via save_post in CMB2.
 *
 * @since   1.3.0
 *
 * @param   int     $post_id  The ID of the current object.
 * @param   string  $updated  Array of field ids that were updated.
 *                            Will only include field ids that had values change.
 * @param   array   $cmb      This CMB2 object.
 *
 * @return  void
 */
add_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );
function mai_save_sections_to_the_content( $post_id, $updated, $cmb ) {

	// Check required $_POST variables and security nonce.
	if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return;
	}

	// Get the sections.
	$sections = get_post_meta( $post_id, 'mai_sections', true );

	// Bail if no sections.
	if ( ! $sections || ! is_array( $sections ) ) {
		return;
	}

	// Get the page template.
	$template = get_post_meta( $post_id, '_wp_page_template', true );

	// Bail if switching away from Sections template.
	if ( 'sections.php' !== $template ) {
		return;
	}

	// Setup basic HTML.
	$content = '';
	foreach ( $sections as $section ) {
		$content .= ! empty( $section['title'] ) ? sprintf( '<h2>%s</h2>', $section['title'] ) . "\r\n" : '';
		$content .= $section['content'] . "\r\n";
	}

	// Remove these functions so it doesn't cause infinite loop error.
	remove_action( 'cmb2_save_post_fields_mai_sections', 'mai_import_section_data', 8, 3 );
	remove_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );

	// Update the post content in the DB.
	$updated = wp_update_post( array(
		'ID'           => $post_id,
		'post_content' => $content,
	) );

	// Add these functions back.
	add_action( 'cmb2_save_post_fields_mai_sections', 'mai_import_section_data', 8, 3 );
	add_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );
}

/**
 * When change from another template to the Sections template, update the first section with the post content.
 * When changing from the Sections template to another template, delete the section meta.
 *
 * @since   1.3.0
 *
 * @param   null|bool  $check       Whether to allow updating metadata for the given type.
 * @param   int        $object_id   Object ID.
 * @param   string     $meta_key    Meta key.
 * @param   mixed      $meta_value  Meta value. Must be serializable if non-scalar.
 * @param   mixed      $prev_value  Optional. If specified, only update existing
 *                                  metadata entries with the specified value.
 *                                  Otherwise, update all entries.
 *
 * @return  mixed
 */
add_filter( 'update_post_metadata', 'mai_update_to_or_from_sections_template', 10, 5 );
function mai_update_to_or_from_sections_template( $check, $object_id, $meta_key, $meta_value, $prev_value ) {

	// Bail if doing ajax (for onboarding and programmatically updating this meta via onboarding).
	if ( wp_doing_ajax() ) {
		return $check;
	}

	// Bail if no value change or not updating page template.
	if ( $meta_value === $prev_value || '_wp_page_template' !== $meta_key ) {
		return $check;
	}

	/**
	 * We must get the actual existing template value
	 * because $prev_value is not passed for _wp_page_template.
	 */
	$previous_value = get_post_meta( $object_id, '_wp_page_template', true );

	// Bail if the existing template is the same as the updated one.
	if ( $previous_value === $meta_value ) {
		return $check;
	}

	// If changing TO Sections template.
	if ( 'sections.php' === $meta_value ) {

		// Update the first section content with the post content.
		$sections = array( array( 'content' => get_post_field( 'post_content', $object_id ) ) );
		update_post_meta( $object_id, 'mai_sections', $sections );

	}
	// If changing FROM Sections template.
	elseif ( 'sections.php' === $previous_value ) {

		// Delete the section meta.
		delete_post_meta( $object_id, 'mai_sections' );

	}

	return $check;
}

/**
 * Inline script to display a warning when toggling to another page template than Sections.
 * This is only loaded on existing Sections template admin pages.
 *
 * @since   1.3.0
 *
 * @access  private
 *
 * @return  void
 */
add_action( 'cmb2_after_post_form_mai_sections', 'mai_change_from_sections_template_warning', 20, 2 );
function mai_change_from_sections_template_warning( $object_id, $cmb ) {

	$alert = __( 'Warning! Changing to another page template will delete Sections template settings and data. Your content will be moved to the regular editor, but there is no going back!', 'mai-theme-engine' );

	printf( "<script>
		jQuery(window).load( function() {
			$( '#pageparentdiv' ).on( 'change', 'select#page_template', function() {
				if ( 'sections.php' !== $(this).val() ) {
					alert( '%s' );
				}
			});
		});
	</script>", $alert );
}

/**
 * Update sections data from array of import data.
 *
 * @since   1.3.0
 *
 * @access  private
 *
 * @param   array  $section_data   Array of sections and parameter keys/values.
 * @param   int    $post_id        The post ID to attach the image to.
 * @param   bool   $import_images  Whether to attempt to import background images.
 *
 * @return  void
 */
function mai_update_sections_template( $section_data, $post_id, $import_images = false ) {

	if ( empty( $section_data ) || ! is_array( $section_data ) ) {
		return;
	}

	// Separate our data.
	$sections         = isset( $section_data['sections'] ) ? $section_data['sections'] : false;
	$home_url         = isset( $section_data['home_url'] ) ? esc_url( $section_data['home_url'] ) : null;
	$excerpt          = isset( $section_data['excerpt'] ) ? wp_kses_post( $section_data['excerpt'] ) : null;
	$layout           = isset( $section_data['layout'] ) ? sanitize_key( $section_data['layout'] ) : null;
	$banner_id        = isset( $section_data['banner_id'] ) ? $section_data['banner_id'] : null;
	$hide_banner      = isset( $section_data['hide_banner'] ) ? sanitize_key( $section_data['hide_banner'] ) : null;
	$hide_breadcrumbs = isset( $section_data['hide_breadcrumbs'] ) ? sanitize_key( $section_data['hide_breadcrumbs'] ) : null;
	$hide_featured    = isset( $section_data['hide_featured'] ) ? sanitize_key( $section_data['hide_featured'] ) : null;
	$images           = isset( $section_data['images'] ) ? $section_data['images'] : null;
	$imported_images  = array();

	// If we have images, and are importing them.
	if ( $images && is_array( $images ) && $import_images ) {

		// This is only for frontend, but just incase.
		if ( ! function_exists( 'media_sideload_image' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
		}

		// Loop through our images.
		foreach ( $images as $old_id => $old_url ) {

			// Get image data, for filename.
			$path_parts = pathinfo( $old_url );

			// Create attachment.
			$new_id = media_sideload_image( $old_url, $post_id, $path_parts['filename'], 'id' );

			// If valid attachment.
			if ( $new_id && ! is_wp_error( $new_id ) ) {

				/**
				 * Build array of imported image data.
				 * Keyed by old image ID, and value is array of new id and new url.
				 *
				 * $imported_images = array(
				 *     123 => array(
				 *         'id'  => '1001,
				 *         'url' => 'https://example.com/wp-content/uploads/my-image.jpg',
				 *     ),
				 *     345 => array(
				 *         'id'  => '1001,
				 *         'url' => 'https://example.com/wp-content/uploads/my-image.jpg',
				 *     ),
				 * );
				 */
				$imported_images[ $old_id ] = array(
					'id'  => $new_id,
					'url' => wp_get_attachment_url( $new_id ),
				);
			}
		}
	}

	/**
	 * Maybe update the excerpt.
	 * This needs to happen before updating meta or it wipes out our values.
	 */
	if ( $excerpt ) {
		$updated = wp_update_post( array(
			'ID'           => $post_id,
			'post_excerpt' => $excerpt,
		) );
	}

	// If we have section data.
	if ( $sections ) {

		// Whitelist section args/keys.
		$args = array(
			'bg'            => '',
			'image_id'      => '',
			'image'         => false,
			'overlay'       => '',
			'inner'         => '',
			'height'        => '',
			'content_width' => '',
			'align_content' => '',
			'align'         => '',
			'text_size'     => '',
			'id'            => '',
			'class'         => '',
			'context'       => '',
			'title'         => '',
			'content'       => '',
		);

		// Loop through each section.
		foreach ( $sections as $index => $section ) {

			// Parse attributes.
			$section = shortcode_atts( $args, $section );

			// Loop through and sanitize each section parameter individually.
			foreach ( $section as $key => $value ) {

				// Sanitize.
				if ( 'content' === $key ) {

					// Sanitize content.
					$section[ $key ] = wp_kses_post( $value );

				} else {

					// Sanitize everything else.
					$section[ $key ] = sanitize_text_field( $value );
				}

			}

			// If we have content.
			if ( ! empty( $section['content'] ) ) {

				// Search/Replace URLs.
				$section['content'] = $home_url ? str_replace( $home_url, untrailingslashit( home_url() ), $section['content'] ) : $section['content'];

				// If importing images and have some imported images.
				if ( $import_images && $imported_images ) {

					// Loop through imported images.
					foreach ( $imported_images as $old_id => $imported_image ) {

						// Replace old image IDs (hopefully from shortcodes) with new imported IDs.
						$section['content'] = str_replace( $old_id, $imported_image['id'], $section['content'] );
					}
				}
			}

			// If we have a background image.
			if ( ! empty( $section['image_id'] ) ) {

				$section_image_id = (int) $section['image_id'];

				// Set section image params.
				if ( isset( $imported_images[ $section_image_id ], $imported_images[ $section_image_id ]['id'] ) ) {
					$section['image_id'] = $imported_images[ $section_image_id ]['id'];
				}
				if ( isset( $imported_images[ $section_image_id ], $imported_images[ $section_image_id ]['url'] ) ) {
					$section['image'] = $imported_images[ $section_image_id ]['url'];
				}
			}

			// Rebuild the updated $sections.
			$sections[ $index ] = $section;
		}

	}

	// Update with our new section data.
	update_post_meta( $post_id, 'mai_sections', $sections );

	// If our layout exists.
	$layouts = genesis_get_layouts();
	if ( isset( $layouts[ $layout ] ) ) {
		// Update the layout.
		update_post_meta( $post_id, '_genesis_layout', $layout );
	}

	// Maybe update banner image.
	if ( ( null !== $banner_id ) && $import_images && $imported_images ) {
		if ( isset( $imported_images[ $banner_id ], $imported_images[ $banner_id ]['id'], $imported_images[ $banner_id ]['url'] ) ) {
			update_post_meta( $post_id, 'banner_id', (int) $imported_images[ $banner_id ]['id'] );
			update_post_meta( $post_id, 'banner', esc_url( $imported_images[ $banner_id ]['url'] ) );
		}
	}

	// Update visibility settings.
	if ( null !== $hide_banner ) {
		update_post_meta( $post_id, 'hide_banner', $hide_banner );
	}
	if ( null !== $hide_breadcrumbs ) {
		update_post_meta( $post_id, 'mai_hide_breadcrumbs', $hide_banner );
	}
	if ( null !== $hide_featured ) {
		update_post_meta( $post_id, 'mai_hide_featured_image', $hide_banner );
	}

}

/**
 * Get all image IDs from content.
 *
 * @access  private
 *
 * @link    https://stackoverflow.com/questions/32523265/extract-shortcode-parameters-in-content-wordpress
 *
 * @since   1.3.0
 *
 * @param   string  The content to get shortcodes from.
 *
 * @return  array   The image IDs.
 */
function mai_get_shortcode_image_ids( $content ) {

	$image_ids = array();

	$shortcodes = array(
		'col',
		'col_auto',
		'col_one_twelfth',
		'col_one_sixth',
		'col_one_fourth',
		'col_one_third',
		'col_five_twelfths',
		'col_one_half',
		'col_seven_twelfths',
		'col_two_thirds',
		'col_three_fourths',
		'col_five_sixths',
		'col_eleven_twelfths',
		'col_one_whole',
	);

	$pattern = get_shortcode_regex( $shortcodes );

	// Bail if no shortcodes.
	if ( ! preg_match_all( '/' . $pattern . '/s', $content, $matches ) ) {
		return false;
	}

	// Bail if no attributes.
	if ( empty( $matches[0] ) ) {
		return false;
	}

	// Loop through our attribute matches.
	foreach ( $matches[0] as $key => $value ) {

		/**
		 * Replace space with '&' for parse_str() function.
		 * $matches[3] returns the shortcode attributes as strings.
		 */
		$get = str_replace( ' ', '&', $matches[3][ $key ] );

		// Parse as if a query string.
		parse_str( $get, $output );

		// If we have and image value.
		if ( $output && isset( $output['image'] ) && ! empty( $output['image'] ) ) {

			// Remove extra quotes. Shorcodes can do image=123, image='123', or image="123".
			$image_id = $output['image'];
			$image_id = str_replace( '"', '', $image_id );
			$image_id = str_replace( "'", '', $image_id );

			// Add our image to the array.
			$image_ids[] = (int) $image_id;
		}
	}

	// If we have image IDs, return them.
	if ( ! empty( $image_ids ) ) {
		return $image_ids;
	}

	// None.
	return false;
}

/**
 * Callback function to get the sections default data for the export field.
 * This is from the CMB 'default_cb'
 *
 * @access  private
 *
 * @since   1.3.0
 *
 * @return  string  The sections JSON.
 */
function _mai_cmb_get_sections_json( $args, $field ) {
	$site_layout = genesis_get_custom_field( '_genesis_layout', $field->object_id );
	$site_layout = $site_layout ? $site_layout : genesis_get_option( sprintf( 'layout_%s', get_post_type( $field->object_id ) ) );
	$data = array(
		'home_url'         => untrailingslashit( home_url() ),
		'layout'           => $site_layout,
		'banner_id'        => get_post_meta( 'banner_id', $field->object_id, true ),
		'hide_banner'      => get_post_meta( $field->object_id, 'hide_banner', true ),
		'hide_breadcrumbs' => get_post_meta( $field->object_id, 'mai_hide_breadcrumbs', true ),
		'hide_featured'    => get_post_meta( $field->object_id, 'mai_hide_featured_image', true ),
		'excerpt'          => get_post_field( 'post_excerpt', $field->object_id ),
		'images'           => mai_get_sections_template_images( $field->object_id ),
		'sections'         => (array) get_post_meta( $field->object_id, 'mai_sections', true ),
	);
	return json_encode( $data );
}

/**
 * Get an array of section images.
 *
 * @access  private
 *
 * @since   1.3.0
 *
 * @param   int  The post ID to get section images from.
 *
 * @return  array  Associative array of images. image ID => URL.
 */
function mai_get_sections_template_images( $post_id ) {
	$images    = array();
	$sections  = get_post_meta( $post_id, 'mai_sections', true );
	$banner_id = get_post_meta( $post_id, 'banner_id', true );
	if ( $sections ) {
		foreach ( $sections as $section ) {
			if ( isset( $section['image'] ) && ! empty( $section['image'] ) ) {
				if ( isset( $section['image_id'] ) && ! empty( $section['image_id'] ) ) {
					$image_id            = (int) $section['image_id'];
					$images[ $image_id ] = wp_get_attachment_url( $image_id );
				}
			}
			if ( isset( $section['content'] ) && ! empty( $section['content'] ) ) {
				$image_ids = mai_get_shortcode_image_ids( $section['content'] );
				if ( $image_ids ) {
					foreach ( $image_ids as $image_id ) {
						$images[ $image_id ] = wp_get_attachment_url( $image_id );
					}
				}
			}
		}
	}
	if ( $banner_id ) {
		$banner_id            = (int) $banner_id;
		$images[ $banner_id ] = wp_get_attachment_url( $banner_id );
	}
	return $images;
}
