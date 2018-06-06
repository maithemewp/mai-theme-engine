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
			'id'               => 'align_content',
			'type'             => 'select',
			'show_option_none' => __( '- None -', 'genesis' ),
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
		'default'         => '',
		'after_row'       => '</div></div></div>',
		'sanitization_cb' => 'mai_sanitize_post_content',
	) );

	// Import JSON.
	$sections->add_field( array(
		'name'       => __( 'Import (JSON)', 'mai-theme-engine' ),
		'desc'       => __( 'Paste JSON code and update the page/post to import.', 'mai-theme-engine' ),
		'default'    => '',
		'id'         => 'mai_sections_json_import',
		'type'       => 'textarea_small',
		'save_field' => false, // Otherwise CMB2 will end up removing the value.
		'before_row' => '<div id="mai-sections-import-export"><ul style="text-align:right;"><li style="display:inline-block;"><a href="#mai-sections-import-export-1">Import</a>&nbsp;|&nbsp;</li><li style="display:inline-block;"><a href="#mai-sections-import-export-2">Export</a></li></ul><div id="mai-sections-import-export-1">',
	) );

	// Import images.
	$sections->add_field( array(
		'name'       => __( '&nbsp;', 'mai-theme-engine' ),
		'desc'       => __( '(Experimental) Import Section background images. Images must be on a publicly accessible URL.', 'mai-theme-engine' ),
		'id'         => 'mai_sections_json_import_images',
		'type'       => 'checkbox',
		'save_field' => false, // Otherwise CMB2 will end up removing the value.
		'after_row'  => '</div>',
	) );

	// Export.
	$sections->add_field( array(
		'name'       => __( 'Export (JSON)', 'mai-theme-engine' ),
		'desc'       => __( 'Copy and paste this code into the "Import" field on another Sections template.', 'mai-theme-engine' ),
		'default_cb' => '_mai_cmb_get_sections_json',
		'id'         => 'mai_sections_json_export',
		'type'       => 'textarea_small',
		'save_field' => false, // Otherwise CMB2 will end up removing the value.
		'before_row' => '<div id="mai-sections-import-export-2">',
		'after_row'  => '</div></div>',
		'attributes' => array(
			'readonly' => 'readonly',
		),
	) );

}

/**
 * Save section meta content to the_content for search indexing and SEO content analysis.
 *
 * @since   1.3.0
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

	$alert = __( 'Warning! Changing to another page template will lose delete Sections template settings and data. Your content will be moved to the regular editor, but there is no going back!', 'mai-theme-engine' );

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
 * Fires after all fields have been saved.
 *
 * @since   1.3.0
 *
 * @param   int     $object_id  The ID of the current object
 * @param   string  $updated    Array of field ids that were updated.
 *                             Will only include field ids that had values change.
 * @param   array   $cmb        This CMB2 object
 *
 * @return  void
 */
add_action( 'cmb2_save_post_fields_mai_sections', 'mai_import_section_data_og', 10, 3 );
function mai_import_section_data_og( $object_id, $updated, $cmb ) {

	// Check required $_POST variables and security nonce
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

	mai_update_sections( $section_data, $object_id, $import_images );
}

/**
 * Callback function to get the sections default data for the export field.
 *
 * @access  private
 *
 * @since   1.3.0
 *
 * @return  string  The sections JSON.
 */
function _mai_cmb_get_sections_json( $args, $field ) {
	global $post;
	$site_layout = genesis_get_custom_field( '_genesis_layout', $post->ID );
	if ( ! $site_layout ) {
		$site_layout = genesis_get_option( sprintf( 'layout_%s', get_post_type( $post->ID ) ) );
	}
	$data = array(
		'home_url' => untrailingslashit( home_url() ),
		'layout'   => $site_layout,
		'sections' => (array) get_post_meta( $field->object_id, 'mai_sections', true ),
	);
	return json_encode( $data );
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
function mai_update_sections( $section_data, $post_id, $import_images = false ) {

	if ( empty( $section_data ) || ! is_array( $section_data ) ) {
		return;
	}

	if ( $import_images && ! function_exists( 'media_sideload_image' ) ) {
		require_once(ABSPATH . 'wp-admin/includes/media.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
	}

	// Whitelist args/keys.
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

	// Separate our data.
	$home_url = isset( $section_data['home_url']  ) ? esc_url( $section_data['home_url'] ) : false;
	$layout   = isset( $section_data['layout']  ) ? sanitize_key( $section_data['layout'] ) : false;
	$sections = isset( $section_data['sections']  ) ? $section_data['sections'] : false;

	// Bail if no sections, that's the whole point right?
	if ( ! $sections ) {
		return;
	}

	$imported_images = array();

	// Loop through each section.
	foreach ( $sections as $index => $section ) {

		// Parse attributes.
		$section = shortcode_atts( $args, $section );

		// Loop through and sanitize each section parameter individually.
		foreach ( $section as $key => $value ) {

			// Sanitize.
			if ( 'content' === $key ) {
				$section[ $key ] = wp_kses_post( $value );
				// Search/Replace URLs.
				$section[ $key ] = $home_url ? str_replace( $home_url, untrailingslashit( home_url() ), $value ) : $value;
			} else {
				$section[ $key ] = sanitize_text_field( $value );
			}

		}

		// If importing images, do it now.
		if ( $import_images ) {

			// If already uploaded.
			if ( isset( $imported_images[ $section['image_id'] ] ) && $imported_images[ $section['image_id'] ] ) {

				// Swap our imported image ID and URL.
				$section['image_id'] = isset( $imported_images[ $section['image_id'] ]['id'] ) ? $imported_images[ $section['image_id'] ]['id'] : '';
				$section['image']    = isset( $imported_images[ $section['image_id'] ]['url'] ) ? $imported_images[ $section['image_id'] ]['url'] : '';
			}
			// We need to upload now.
			else {

				// Create attachment.
				$attachment_id = media_sideload_image( $section['image'], $post_id, $desc = '', 'id' );

				// If valid attachment.
				if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {

					$attachment_url = wp_get_attachment_url( $attachment_id );

					// Build array of imported image data.
					$imported_images[ $section['image_id'] ] = array(
						'id'  => $attachment_id,
						'url' => $attachment_url,
					);

					// Swap our imported image ID and URL.
					$section['image_id'] = $attachment_id;
					$section['image']    = $attachment_url;

				} else {

					// We tried, but didn't work so flag it so we don't try again for this image.
					$imported_images[ $section['image_id'] ] = false;

					$section['image_id'] = '';
					$section['image']    = '';
				}

			}
		}

		// Rebuild the updated $sections.
		$sections[ $index ] = $section;
	}

	// Update with our new section data.
	update_post_meta( $post_id, 'mai_sections', $sections );

	// If our layout exists.
	$layouts = genesis_get_layouts();
	if ( isset( $layouts[ $layout ] ) ) {
		// Update the layout.
		update_post_meta( $post_id, '_genesis_layout', $layout );
	}
}
