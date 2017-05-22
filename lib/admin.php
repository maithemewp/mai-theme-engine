<?php

/**
 * Enqueue an admin script, for custom editor styles and other stuff
 *
 * @return void
 */
add_action( 'admin_enqueue_scripts', 'mai_admin_enqueue_scripts' );
function mai_admin_enqueue_scripts() {

	// Add an editor stylesheet
	add_editor_style( '/assets/css/editor-style.css' );

	// Use minified files if script debug is not being used
	$suffix = mai_get_suffix();

	// Register for later
	wp_enqueue_style( 'mai-cmb2', MAI_PRO_ENGINE_PLUGIN_URL . "/assets/css/mai-cmb2{$suffix}.css", array(), CHILD_THEME_VERSION );
	wp_enqueue_script( 'mai-cmb2', MAI_PRO_ENGINE_PLUGIN_URL . "/assets/js/mai-cmb2{$suffix}.js", array( 'jquery' ), CHILD_THEME_VERSION, true );
}

/**
 * Adds a new select bar to the WP editor
 * Insert 'styleselect' into the $buttons array
 * _2 places the new button on the second line
 *
 * @return  array
 */
add_filter( 'mce_buttons_2', 'mai_add_styleselect_dropdown' );
function mai_add_styleselect_dropdown( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}

/**
 * Add a button option to the editor.
 *
 * @param   array  $init_array
 *
 * @return  array
 */
add_filter( 'tiny_mce_before_init', 'mai_add_style_format_options_to_editor' );
function mai_add_style_format_options_to_editor( $init_array ) {
	// Define the style_formats array
	$style_formats = array(
		// Each array child is a format with it's own settings
		array(
			'title' 	=> 'Section Title',
			// 'selector'  => 'h2',
			'block' 	=> 'h2',
			'classes' 	=> 'heading',
		),
		array(
			'title'		=> 'Button',
			'selector'	=> 'a',
			'classes'	=> 'button',
		),
	);
	// Insert the array, JSON ENCODED, into 'style_formats'
	$init_array['style_formats'] = json_encode( $style_formats );
	return $init_array;
}

// Show the editor on the page set for is_home()
add_action( 'edit_form_after_title', 'mai_posts_page_edit_form' );
function mai_posts_page_edit_form() {
    global $post, $post_type, $post_ID;
    if ( $post_ID == get_option( 'page_for_posts' ) && empty( $post->post_content ) ) {
        add_post_type_support( $post_type, 'editor' );
    }
}

// Change login logo
add_action( 'login_head',  'mai_login_logo' );
function mai_login_logo() {

	$logo_id  = get_theme_mod( 'custom_logo' );

	// Bail if we don't have a custom logo
	if ( ! $logo_id ) {
		return;
	}

	$logo_src = wp_get_attachment_image_src( $logo_id, 'medium' );
	$logo_url = $logo_src[0];

	echo '<style  type="text/css">
		.login h1 a {
			background-image:url(' . $logo_url . ') !important;
			background-size: contain !important;
			width: 100% !important;
			max-width: 300px !important;
			min-height: 100px !important;
		}
	</style>';
}

// Change login link
add_filter( 'login_headerurl', 'mai_login_link' );
function mai_login_link() {
	return get_site_url();
}
