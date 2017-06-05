<?php

add_action( 'admin_head', 'hide_editor' );
function hide_editor() {
	global $pagenow;

	if ( 'post.php' != $pagenow ) {
		return;
	}
	if ( ! isset( $_GET['post'] ) ) {
		return;
	}
	if ( 'sections.php' == get_page_template_slug( absint( $_GET['post'] ) ) ) {
		remove_post_type_support('page', 'editor');
	}
}

add_action( 'cmb2_admin_init', 'mai_do_sections_metabox' );
function mai_do_sections_metabox() {

	// Posts/Pages/CPTs
	$sections = new_cmb2_box( array(
		'id'			=> 'mai_sections',
		'title'			=> __( 'Sections', 'mai-pro' ),
		'object_types'	=> array( 'page' ),
		'context'		=> 'normal',
		'priority'		=> 'high',
		'classes' 		=> 'mai-metabox',
		'show_on' 		=> array( 'key' => 'page-template', 'value' => 'sections.php' ),
	) );

	$section = $sections->add_field( array(
	    'id'          => 'mai_sections',
	    'type'        => 'group',
	    'repeatable'  => true,
	    'options'     => array(
			'group_title'	=> __( 'Section #{#}', 'mai-pro' ),
			'add_button'	=> __( 'Add Section', 'mai-pro' ),
			'remove_button'	=> __( 'Remove Section', 'mai-pro' ),
			'sortable'		=> true,
	    ),
	) );

	// Background Color
	$sections->add_group_field( $section, array(
		'name'		 => __( 'Background Color', 'mai-pro' ),
		'id'		 => 'bg',
		'before_row' => '<div class="cmb-section-settings"><div class="mai-row"><div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		// 'after_row'	 => '</div>',
		'type'		 => 'colorpicker',
		'default'	 => '#fff',
	) );

	// Background Image
	$sections->add_group_field( $section, array(
		'name'			=> __( 'Background Image', 'mai-pro' ),
		'id'			=> 'image',
		// 'before_row'	=> '<div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		'after_row'		=> '</div>',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array( 'url' => false ),
		'text'			=> array(
	        'add_upload_file_text' => __( 'Add Image', 'mai-pro' ),
	    ),
	) );

	// Overlay
	$sections->add_group_field( $section, array(
		'name'				=> __( 'Overlay Style', 'mai-pro' ),
		'id'				=> 'overlay',
		'before_row'		=> '<div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		// 'after_row'			=> '</div>',
		'type'				=> 'select',
		'select_all_button'	=> false,
		'options'			=> array(
			''			=> __( '- None -', 'mai-pro' ),
			'gradient'	=> __( 'Gradient overlay', 'mai-pro' ),
			'light'		=> __( 'Light overlay', 'mai-pro' ),
			'dark'		=> __( 'Dark overlay', 'mai-pro' ),
			// 'inner'				=> __( 'Add content inner styling', 'mai-pro' ),
		),
	) );

	// Inner
	$sections->add_group_field( $section, array(
		'name'				=> __( 'Inner Style', 'mai-pro' ),
		'id'				=> 'inner',
		// 'before_row'		=> '<div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		'after_row'			=> '</div>',
		'type'				=> 'select',
		'select_all_button'	=> false,
		'options'			=> array(
			''		=> __( '- None -', 'mai-pro' ),
			'light' => __( 'Light box', 'mai-pro' ),
			'dark'  => __( 'Dark box', 'mai-pro' ),
		),
	) );

	// Height
	$sections->add_group_field( $section, array(
		'name'			=> __( 'Height', 'mai-pro' ),
		'id'			=> 'height',
		'before_row'	=> '<div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		// 'after_row'		=> '</div>',
		'type'			=> 'select',
		'default'		=> 'md',
		'options'		=> array(
			'auto'	=> __( 'Auto (Use height of content)', 'mai-pro' ),
			'sm'	=> __( 'Small', 'mai-pro' ),
			'md'	=> __( 'Medium', 'mai-pro' ),
			'lg'	=> __( 'Large', 'mai-pro' ),
		),
	) );

	// Content Width
	$sections->add_group_field( $section, array(
		'name'				=> __( 'Content Width', 'mai-pro' ),
		'id'				=> 'content_width',
		// 'before_row'		=> '<div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		'after_row'			=> '</div></div></div>',
		'type'				=> 'select',
		'show_option_none'	=> __( 'Default (Use Layout Width)', 'mai-pro' ),
		'options'			=> array(
			'xs'	=> __( 'Extra Small', 'mai-pro' ),
			'sm'	=> __( 'Small', 'mai-pro' ),
			'md'	=> __( 'Medium', 'mai-pro' ),
			'lg'	=> __( 'Large', 'mai-pro' ),
			'xl'	=> __( 'Extra Large', 'mai-pro' ),
			'full'	=> __( 'Full Width', 'mai-pro' ),
		),
	) );

	// Title
	$sections->add_group_field( $section, array(
		'name'	=> 'Title',
		'id'	=> 'title',
		'type'	=> 'text',
		'attributes'  => array(
			'placeholder' => __( 'Enter section title here', 'mai-pro' ),
			'class'        => 'widefat',
		),
	) );

	// Content
	$sections->add_group_field( $section, array(
		'name'	=> 'Content',
		'id'	=> 'content',
		'type'	=> 'wysiwyg',
	) );

}

/**
 * Only return default value if we don't have a post ID (in the 'post' query variable)
 *
 * @param  bool  $default On/Off (true/false)
 * @return mixed          Returns true or '', the blank default
 */
function mai_set_checkbox_default_for_new_post( $default ) {
	return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
}
