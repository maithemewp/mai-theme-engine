<?php
/**
 * Mai Pro Engine.
 *
 * @author   Mike Hemberger
 *
 * @version  1.1.0
 */

/**
 * Main Mai_Shortcodes Class.
 *
 * @since 1.0.0
 */
final class Mai_Shortcodes {

	/**
	 * Singleton
	 * @var   Mai_Shortcodes The one true Mai_Shortcodes
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Main Mai_Shortcodes Instance.
	 *
	 * Insures that only one instance of Mai_Shortcodes exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   1.0.0
	 * @static  var array $instance
	 * @return  object | Mai_Shortcodes The one true Mai_Shortcodes
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Mai_Shortcodes;
            // Initialize
            self::$instance->init();
		}
		return self::$instance;
	}

	function init() {

		// Enable shortcodes in widgets
		add_filter( 'widget_text', 'do_shortcode' );

		// Custom Post Type Archive Intro Text
		add_filter( 'genesis_cpt_archive_intro_text_output', 'do_shortcode' );

		// Author Archive Intro Text
		add_filter( 'genesis_author_intro_text_output', 'do_shortcode' );

		// Term Archive Intro Text
		add_filter( 'genesis_term_intro_text_output', 'do_shortcode' );

		// Remove empty <p> tags from shortcodes
		add_filter( 'the_content', array( $this, 'content_filter' ) );

		// Create shortcodes
		add_shortcode( 'callout', 				array( $this, 'get_callout' ) );
		add_shortcode( 'section', 				array( $this, 'get_section' ) );
		add_shortcode( 'columns', 				array( $this, 'get_columns' ) );
		add_shortcode( 'col', 					array( $this, 'get_col' ) );
		add_shortcode( 'col_auto', 				array( $this, 'get_col_auto' ) );
		add_shortcode( 'col_one_twelfth', 		array( $this, 'get_col_one_twelfth' ) );
		add_shortcode( 'col_one_sixth', 		array( $this, 'get_col_one_sixth' ) );
		add_shortcode( 'col_one_fourth', 		array( $this, 'get_col_one_fourth' ) );
		add_shortcode( 'col_one_third', 		array( $this, 'get_col_one_third' ) );
		add_shortcode( 'col_five_twelfths', 	array( $this, 'get_col_five_twelfths' ) );
		add_shortcode( 'col_one_half', 			array( $this, 'get_col_one_half' ) );
		add_shortcode( 'col_seven_twelfths', 	array( $this, 'get_col_seven_twelfths' ) );
		add_shortcode( 'col_two_thirds', 		array( $this, 'get_col_two_thirds' ) );
		add_shortcode( 'col_three_fourths', 	array( $this, 'get_col_three_fourths' ) );
		add_shortcode( 'col_five_sixths', 		array( $this, 'get_col_five_sixths' ) );
		add_shortcode( 'col_eleven_twelfths', 	array( $this, 'get_col_eleven_twelfths' ) );
		add_shortcode( 'col_one_whole', 		array( $this, 'get_col_one_whole' ) );
		add_shortcode( 'grid', 					array( $this, 'get_grid' ) );
	}

	/**
	 * Filter the content to remove empty <p></p> tags from shortcodes
	 *
	 * @link https://gist.github.com/bitfade/4555047
	 *
	 * @return  mixed  Fixed shortcode content
	 */
	function content_filter( $content ) {

	    $shortcodes = array(
	        'callout',
	        'section',
	        'columns',
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
	        'grid',
	    );

	    // Array of custom shortcodes requiring the fix
	    $shortcodes = join( '|', $shortcodes );

	    // Opening tag
	    $rep = preg_replace( "/(<p>)?\[($shortcodes)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );

	    // Closing tag
	    $rep = preg_replace( "/(<p>)?\[\/($shortcodes)](<\/p>|<br \/>)?/", "[/$2]", $rep );

	    // Return fixed shortcodes
	    return $rep;
	}

	function get_callout( $atts, $content = null ) {

	    // Bail if no content
	    if ( null == $content ) {
	        return;
	    }

	    $defaults = array(
	        'color' => '',
	    );

	    /**
	     * Shortcode callout attributes
	     */
	    $atts = shortcode_atts( $defaults, $atts, 'callout' );

	    $attributes['class'] = 'callout';

	    if ( $atts['color'] ) {
	    	$attributes['class'] .= mai_sanitized_html_classes( $atts['color'] );
	    }

	    $output = sprintf( '<div %s>%s</div>', genesis_attr( 'mai-callout', $attributes ), do_shortcode( wpautop( trim($content) ) ) );

	    return $output;
	}

	/**
	 * Add new section shortcode
	 * On layouts with no sidebar it will be a full browser/window width section
	 *
	 * Add parameter of 'image=246' with an image ID from the media library to use a full width background image
	 */
	function get_section( $atts, $content = null ) {
	    // Bail if no content
	    if ( null == $content ) {
	        return;
	    }

	    // Add shortcode class
	    $atts['class'] = isset( $atts['class'] ) ? 'section-shortcode ' . $atts['class'] : 'section-shortcode';

	    $output = '';

	    $output .= $this->get_section_open( $atts );
	    $output .= do_shortcode( trim($content) );
	    $output .= $this->get_section_close( $atts );

	    return $output;
	}

	/**
	 * Get opening section wrap
	 * To be used in front-page.php and [section] shortcode
	 *
	 * @version  1.0.1
	 *
	 * @param    array  $args  Options for the wrapping markup
	 *
	 * @return   string|HTML
	 */
	function get_section_open( $args ) {

	    // Shortcode section atts
	    $args = shortcode_atts( $this->get_section_defaults(), $args, 'section' );

	    // Add filter to change args. There is also a mai_section_defaults filter to manipulate earlier.
	    $args = apply_filters( 'mai_section_args', $args );

	    // Sanitized args
	    $args = array(
	        'wrapper'       => sanitize_key( $args['wrapper'] ),
	        'id'            => sanitize_html_class( $args['id'] ),
	        'class'         => mai_sanitize_html_classes( $args['class'] ),
	        'align'         => mai_sanitize_keys( $args['align'] ), // left, center, right
	        'image'         => absint( $args['image'] ),
	        'overlay'       => filter_var( $args['overlay'], FILTER_VALIDATE_BOOLEAN ),
	        'title'         => sanitize_text_field( $args['title'] ),
	        'title_wrap'    => sanitize_key( $args['title_wrap'] ),
	        'wrap'          => filter_var( $args['wrap'], FILTER_VALIDATE_BOOLEAN ),
	        'inner'         => filter_var( $args['inner'], FILTER_VALIDATE_BOOLEAN ),
	        'content_width' => sanitize_key( $args['content_width'] ),
	        'height'        => sanitize_key( $args['height'] ),
	    );

	    // Start all element variables as empty string
	    $title = $wrap = $inner = '';

	    // Start all attributes as empty array
	    $section_atts = $wrap_atts = $inner_atts = array();

	    // Maybe add section id
	    if ( $args['id'] ) {
	        $section_atts['id'] = $args['id'];
	    }

	    // Default section class
	    $section_atts['class'] = 'section row middle-xs center-xs';

	    // Maybe add additional section classes
	    if ( $args['class'] ) {
	        $section_atts['class'] .= ' ' . $args['class'];
	    }

	    // Align text
	    if ( ! empty( $args['align'] ) ) {

	        // Left
	        if ( in_array( 'left', $args['align']) ) {
	            $section_atts['class'] .= ' text-xs-left';
	        }

	        // Center
	        if ( in_array( 'center', $args['align'] ) ) {
	            $section_atts['class'] .= ' text-xs-center';
	        }

	        // Right
	        if ( in_array( 'right', $args['align'] ) ) {
	            $section_atts['class'] .= ' text-xs-right';
	        }

	    }

	    // If we have an image ID
	    if ( $args['image'] ) {

	        // If no inner, add light-content class
	        if ( ! $args['inner'] ) {
	            $section_atts['class'] .= ' light-content';
	        }

	        // Add the aspect ratio attributes
	        $section_atts = mai_add_image_background_attributes( $section_atts, $args['image'], 'banner' );
	    }

	    // Maybe add an overlay, typically for image tint/style
	    if ( $args['overlay'] ) {
	        $section_atts['class'] .= ' overlay';
	    }

	    // Maybe add a wrap, typically to contain content over the image
	    if ( $args['wrap'] ) {

	        $wrap_atts['class'] = 'wrap';

	        // Wrap height
	        if ( $args['height'] ) {

	            switch ( $args['height'] ) {
	                case 'auto';
	                    $wrap_atts['class'] .= ' height-auto';
	                    break;
	                case 'sm':
	                case 'small';
	                    $wrap_atts['class'] .= ' height-sm';
	                    break;
	                case 'md':
	                case 'medium':
	                    $wrap_atts['class'] .= ' height-md';
	                    break;
	                case 'lg':
	                case 'large':
	                    $wrap_atts['class'] .= ' height-lg';
	                    break;
	            }

	        }

	        // Wrap content width
	        if ( $args['content_width'] ) {

	            switch ( $args['content_width'] ) {
	                case 'xs':
	                case 'extra-small':
	                    $wrap_atts['class'] .= ' width-xs';
	                    break;
	                case 'sm':
	                case 'small';
	                    $wrap_atts['class'] .= ' width-sm';
	                    break;
	                case 'md':
	                case 'medium':
	                    $wrap_atts['class'] .= ' width-md';
	                    break;
	                case 'lg':
	                case 'large':
	                    $wrap_atts['class'] .= ' width-lg';
	                    break;
	                case 'xl':
	                case 'extra-large':
	                    $wrap_atts['class'] .= ' width-xl';
	                    break;
	                case 'full':
	                    $wrap_atts['class'] .= ' width-full';
	                    break;
	            }

	        } else {

	            // Add width classes based on layout
	            switch ( genesis_site_layout() ) {
	                case 'xs-content':
	                    $wrap_atts['class'] .= ' width-xs';
	                    break;
	                case 'sm-content':
	                    $wrap_atts['class'] .= ' width-sm';
	                    break;
	                case 'md-content':
	                    $wrap_atts['class'] .= ' width-md';
	                    break;
	                case 'lg-content':
	                    $wrap_atts['class'] .= ' width-lg';
	                    break;
	            }

	        }

	        $wrap = sprintf( '<div %s>', genesis_attr( 'mai-wrap', $wrap_atts ) );
	    }

	    // Maybe add a section title
	    if ( $args['title'] ) {
	        $title = sprintf( '<%s class="heading">%s</%s>', $args['title_wrap'], $args['title'], $args['title_wrap'] );
	    }

	    // Maybe add an inner wrap, typically for content width/style
	    if ( $args['inner'] ) {
	        $inner_atts['class'] = 'inner';
	        $inner               = sprintf( '<div %s>', genesis_attr( 'mai-inner', $inner_atts ) );
	    }

	    // Build the opening markup
	    return sprintf( '<%s %s>%s%s%s',
	        $args['wrapper'],
	        genesis_attr( 'mai-section', $section_atts ),
	        $wrap,
	        $title,
	        $inner
	    );

	}

	/**
	 * Get closing section wrap
	 * To be used in front-page.php and [section] shortcode
	 *
	 * This should share the same $args variable as opening function
	 *
	 * @version  1.0.1
	 *
	 * @param    array  $args  Options for the wrapping markup
	 *
	 * @return   string|HTML
	 */
	function get_section_close( $args ) {

	    // Get the args
	    $args = wp_parse_args( $args, $this->get_section_defaults() );

	    // Start all element variables as empty string
	    $title = $wrap = $inner = '';

	    // Maybe close wrap
	    if ( filter_var( $args['wrap'], FILTER_VALIDATE_BOOLEAN ) ) {
	        $wrap = '</div>';
	    }

	    // Maybe close inner wrap
	    if ( filter_var( $args['inner'], FILTER_VALIDATE_BOOLEAN ) ) {
	        $outer = '</div>';
	    }

	    // Build the closing markup, in reverse order so the close appropriately
	    return sprintf( '%s%s</%s>',
	        $inner,
	        $wrap,
	        sanitize_text_field( $args['wrapper'] )
	    );

	}

	function get_section_defaults() {
	    $defaults = array(
	        'wrapper'       => 'section',
	        'id'            => '',
	        'class'         => '',
	        'align'         => '',
	        'image'         => '',
	        'overlay'       => false,
	        'title'         => '',
	        'title_wrap'    => 'h2',
	        'wrap'          => true,
	        'inner'         => false,
	        'content_width' => '',
	        'height'        => 'md',
	    );
	    // Add filter to the defaults. Maybe different themes want to have the default something unique.
	    return apply_filters( 'mai_section_defaults', $defaults );
	}

	function get_columns( $atts, $content = null ) {

		// Bail if no content
		if ( null == $content ) {
			return;
		}

		// Pull in shortcode attributes and set defaults
		$atts = shortcode_atts( array(
			'align'				=> '',
			'align_cols'		=> '',
			'align_text'		=> '',
			'class'				=> '',	 // HTML classes (space separated)
			'gutter'			=> '30', // Space between columns (5, 10, 20, 30, 40, 50) only
			'id'				=> '',   // Add HTML id
			'style'				=> '',   // Inline styles
		), $atts, 'columns' );

		// Sanitize atts
		$atts = array(
			'align'				=> mai_sanitize_keys( $atts['align'] ),
			'align_cols'		=> mai_sanitize_keys( $atts['align_cols'] ),
			'align_text'		=> mai_sanitize_keys( $atts['align_text'] ),
			'class'				=> mai_sanitize_html_classes( $atts['class'] ),
			'gutter'			=> absint( $atts['gutter'] ),
			'id'				=> sanitize_html_class( $atts['id'] ),
			'style'				=> esc_attr( $atts['style'] ),
		);

		$html = '';

		$html .= $this->get_row_wrap_open( $atts, 'columns' );
		$html .= do_shortcode(trim($content));
		$html .= $this->get_row_wrap_close( $atts );

		return $html;
	}

	function get_col( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'col', $atts, $content );
	}

	function get_col_auto( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'col-auto', $atts, $content );
	}

	function get_col_one_twelfth( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'one-twelfth', $atts, $content );
	}

	function get_col_one_sixth( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'one-sixth', $atts, $content );
	}

	function get_col_one_fourth( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'one-fourth', $atts, $content );
	}

	function get_col_one_third( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'one-third', $atts, $content );
	}

	function get_col_five_twelfths( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'five-twelfths', $atts, $content );
	}

	function get_col_one_half( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'one-half', $atts, $content );
	}

	function get_col_seven_twelfths( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'seven-twelfths', $atts, $content );
	}

	function get_col_two_thirds( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'two-thirds', $atts, $content );
	}

	function get_col_three_fourths( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'three-fourths', $atts, $content );
	}

	function get_col_five_sixths( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'five-sixths', $atts, $content );
	}

	function get_col_eleven_twelfths( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'eleven-twelfths', $atts, $content );
	}

	function get_col_one_whole( $atts, $content = null ) {
		return $this->get_col_by_fraction( 'one-whole', $atts, $content );
	}

	function get_col_by_fraction( $fraction, $atts, $content ) {

		// Bail if no content
		if ( null == $content ) {
			return;
		}

		// Pull in shortcode attributes and set defaults
		$atts = shortcode_atts( array(
			'align'	=> '',
			'class'	=> '',
			'id'	=> '',
			'style'	=> '',
		), $atts, 'col' );

		$atts = apply_filters( 'mai_col_args', $atts );

		// Sanitize atts
		$atts = array(
			'align'	=> mai_sanitize_keys( $atts['align'] ),
			'class'	=> mai_sanitize_html_classes( $atts['class'] ),
			'id'	=> sanitize_html_class( $atts['id'] ),
			'style'	=> esc_attr( $atts['style'] ),
		);

		$flex_col = array( 'class' => mai_get_flex_entry_classes_by_fraction( $fraction ) );

		// ID
		if ( ! empty( $atts['wrapper_id'] ) ) {
			$flex_col['id'] = $atts['wrapper_id'];
		}

		// Classes
		if ( ! empty( $atts['class'] ) ) {
			$flex_col['class'] .= ' ' . $atts['class'];
		}

	    // Align text
	    if ( ! empty( $atts['align'] ) ) {

	    	// Left
		    if ( in_array( 'left', $atts['align']) ) {
		    	$flex_col['class'] .= ' text-xs-left';
		    }

		    // Center
		    if ( in_array( 'center', $atts['align'] ) ) {
		    	$flex_col['class'] .= ' text-xs-center';
		    }

		    // Right
		    if ( in_array( 'right', $atts['align'] ) ) {
		    	$flex_col['class'] .= ' text-xs-right';
		    }

	    }

	    /**
	     * Return the content with col wrap.
	     * With flex-col attr so devs can filter elsewhere.
	     */
	    return sprintf( '<div %s>%s</div>', genesis_attr( 'flex-col', $flex_col ), wpautop( do_shortcode( trim($content) ) ) );

	}

	function get_grid( $atts ) {

		// Save original atts in a variable for filtering later
		$original_atts = $atts;

		// Pull in shortcode attributes and set defaults
		$atts = shortcode_atts( array(
			'align'					=> '', // "top, left" Comma separted. overrides align_cols and align_text for most times one setting makes sense
			'align_cols'			=> '', // "top, left" Comma separted
			'align_text'			=> '', // "center" Comma separted
			'authors'				=> '', // Comma separated author/user IDs
			'categories'			=> '', // Comma separated category IDs
			'columns'				=> '3',
			'content'				=> 'post', // post_type name (comma separated if multiple), or taxonomy name
			'content_limit'			=> '', 	// Limit number of words
			'content_type'			=> '',
			'date_after'			=> '',
			'date_before'			=> '',
			'date_format'			=> '',
			'entry_class'			=> '',
			'exclude'				=> '',
			'exclude_current'		=> false,
			'grid_title'			=> '',
			'grid_title_class'		=> '',
			'grid_title_wrap'		=> 'h2',
			'gutter'				=> '30',
			'hide_empty'			=> true,
			'ids'					=> '',
			'ignore_sticky_posts'	=> false,
			'image_location'		=> 'before_entry',
			'image_size'			=> 'one-third',
			'link'					=> true,
			'meta_key'				=> '',
			'meta_value'			=> '',
			'more_link_text'		=> apply_filters( 'mai_more_link_text', __( 'Read More', 'mai-pro' ) ),
			'no_content_message'	=> '',
			'number'				=> '12',
			'offset'				=> '0',
			'order'					=> '',
			'order_by'				=> '',
			'parent'				=> '',
			'row_class'				=> '',
			'show'					=> 'image, title', // image, title, add_to_cart, author, content, date, excerpt, image, more_link, price, meta, title
			// 'show_add_to_cart'		=> false, // Woo only
			// 'show_author'			=> false,
			// 'show_content'			=> false,
			// 'show_date'				=> false,
			// 'show_excerpt'			=> false,
			// 'show_image'			=> true,
			// 'show_more_link'		=> false,
			// 'show_price'			=> false, // Woo only
			// 'show_taxonomies'		=> false,
			// 'show_title'			=> true,
			'status'				=> '', // Comma separated for multiple
			'tags'					=> '', // Comma separated tag IDs
			'tax_include_children'	=> true,
			'tax_operator'			=> 'IN',
			'tax_field'				=> 'term_id',
			'taxonomy'				=> '',
			'terms'					=> '',
			'title_wrap'			=> 'h3',
			'class'					=> '',
			'id'					=> '',
			'slider'				=> false, // (slider only) Make the columns a slider
			'arrows'				=> true,  // (slider only) Whether to display arrows
			'center_mode'			=> false, // (slider only) Mobile 'peek'
			'dots'					=> false, // (slider only) Whether to display dots
			'fade'					=> false, // (slider only) Fade instead of left/right scroll (works requires slidestoshow 1)
			'infinite'				=> true,  // (slider only) Loop slider
			'slidestoscroll' 		=> '1',   // (slider only) The amount of posts to scroll
		), $atts, 'grid' );

		$atts = apply_filters( 'mai_grid_defaults', $atts );

		$atts = array(
			'align'					=> mai_sanitize_keys( $atts['align'] ),
			'align_cols'			=> mai_sanitize_keys( $atts['align_cols'] ),
			'align_text'			=> mai_sanitize_keys( $atts['align_text'] ),
			'authors'				=> $atts['authors'], // Validated later
			'categories'			=> array_filter( explode( ',', sanitize_text_field( $atts['categories'] ) ) ),
			'columns'				=> absint( $atts['columns'] ),
			'content'				=> array_filter( explode( ',', sanitize_text_field( $atts['content'] ) ) ),
			'content_limit'			=> absint( $atts['content_limit'] ),
			'content_type'			=> sanitize_text_field( $atts['content_type'] ),
			'date_after'			=> sanitize_text_field( $atts['date_after'] ),
			'date_before'			=> sanitize_text_field( $atts['date_before'] ),
			'date_format'			=> sanitize_text_field( $atts['date_format'] ),
			'entry_class'			=> sanitize_text_field( $atts['entry_class'] ),
			'exclude'				=> array_filter( explode( ',', sanitize_text_field( $atts['exclude'] ) ) ),
			'exclude_current'		=> filter_var( $atts['exclude_current'], FILTER_VALIDATE_BOOLEAN ),
			'grid_title'			=> sanitize_text_field( $atts['grid_title'] ),
			'grid_title_class'		=> sanitize_text_field( $atts['grid_title_class'] ),
			'grid_title_wrap'		=> sanitize_key( $atts['grid_title_wrap'] ),
			'gutter'				=> absint( $atts['gutter'] ),
			'hide_empty'			=> filter_var( $atts['hide_empty'], FILTER_VALIDATE_BOOLEAN ),
			'ids'					=> array_filter( explode( ',', sanitize_text_field( $atts['ids'] ) ) ),
			'ignore_sticky_posts'	=> filter_var( $atts['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN ),
			'image_location'		=> sanitize_key( $atts['image_location'] ),
			'image_size'			=> sanitize_key( $atts['image_size'] ),
			'link'					=> filter_var( $atts['link'], FILTER_VALIDATE_BOOLEAN ),
			'meta_key'				=> sanitize_text_field( $atts['meta_key'] ),
			'meta_value'			=> sanitize_text_field( $atts['meta_value'] ),
			'more_link_text'		=> sanitize_text_field( $atts['more_link_text'] ),
			'no_content_message'	=> sanitize_text_field( $atts['no_content_message'] ),
			'number'				=> $atts['number'], // Validated later, after check for 'all'
			'offset'				=> absint( $atts['offset'] ),
			'order'					=> sanitize_key( $atts['order'] ),
			'order_by'				=> sanitize_key( $atts['order_by'] ),
			'parent'				=> $atts['parent'], // Validated later, after check for 'current'
			'row_class'				=> mai_sanitize_html_classes( $atts['row_class'] ),
			'show'					=> mai_sanitize_keys( $atts['show'] ),
			// 'show_add_to_cart'		=> filter_var( $atts['show_add_to_cart'], FILTER_VALIDATE_BOOLEAN ),
			// 'show_author'			=> filter_var( $atts['show_author'], FILTER_VALIDATE_BOOLEAN ),
			// 'show_content'			=> filter_var( $atts['show_content'], FILTER_VALIDATE_BOOLEAN ),
			// 'show_date'				=> filter_var( $atts['show_date'], FILTER_VALIDATE_BOOLEAN ),
			// 'show_excerpt'			=> filter_var( $atts['show_excerpt'], FILTER_VALIDATE_BOOLEAN ),
			// 'show_image'			=> filter_var( $atts['show_image'], FILTER_VALIDATE_BOOLEAN ),
			// 'show_more_link'		=> filter_var( $atts['show_more_link'], FILTER_VALIDATE_BOOLEAN ),
			// 'show_price'			=> filter_var( $atts['show_price'], FILTER_VALIDATE_BOOLEAN ),
			// 'show_taxonomies'		=> filter_var( $atts['show_taxonomies'], FILTER_VALIDATE_BOOLEAN ),
			// 'show_title'			=> filter_var( $atts['show_title'], FILTER_VALIDATE_BOOLEAN ),
			'status'				=> array_filter( explode( ',', $atts['status'] ) ),
			'tags'					=> array_filter( explode( ',', sanitize_text_field( $atts['tags'] ) ) ),
			'tax_include_children'	=> filter_var( $atts['tax_include_children'], FILTER_VALIDATE_BOOLEAN ),
			'tax_operator'			=> $atts['tax_operator'], // Validated later as one of a few values
			'tax_field'				=> sanitize_key( $atts['tax_field'] ),
			'taxonomy'				=> sanitize_key( $atts['taxonomy'] ),
			'terms'					=> $atts['terms'], // Validated later, after check for 'current'
			'title_wrap'			=> sanitize_key( $atts['title_wrap'] ),
			'class'					=> mai_sanitize_html_classes( $atts['class'] ),
			'id'					=> sanitize_html_class( $atts['id'] ),
			'slider'				=> filter_var( $atts['slider'], FILTER_VALIDATE_BOOLEAN ),
			'arrows'				=> filter_var( $atts['arrows'], FILTER_VALIDATE_BOOLEAN ),
			'center_mode'			=> filter_var( $atts['center_mode'], FILTER_VALIDATE_BOOLEAN ),
			'dots'					=> filter_var( $atts['dots'], FILTER_VALIDATE_BOOLEAN ),
			'fade'					=> filter_var( $atts['fade'], FILTER_VALIDATE_BOOLEAN ),
			'infinite'				=> filter_var( $atts['infinite'], FILTER_VALIDATE_BOOLEAN ),
			'slidestoscroll'		=> absint( $atts['slidestoscroll'] ),
		);

		$html = '';

		// Get the content type
		if ( empty( $atts['content_type'] ) ) {
			$atts['content_type'] = $this->get_content_type( $atts['content'] );
		}

		// Bail if we don't have a valid content type
		if ( empty( $atts['content_type'] ) ) {
			return;
		}

		// Add default/base class
		$flex_grid = array( 'class' => 'flex-grid' );

		// If we have an id, add it
		if ( ! empty($atts['id']) ) {
			$flex_grid['id'] = $atts['id'];
		}

		// If we have classes, add them
		if ( ! empty($atts['class']) ) {
			$flex_grid['class'] .= ' ' . $atts['class'];
		}

	    /**
	     * Main content row wrap.
	     * With flex-row attr so devs can filter elsewhere.
	     */
	    $html .= sprintf( '<div %s>', genesis_attr( 'flex-grid', $flex_grid ) );

	        switch ( $atts['content_type'] ) {
	            case 'post':
	                $html .= $this->get_posts( $atts, $original_atts );
	                break;
	            case 'term':
	                $html .= $this->get_terms( $atts, $original_atts );
	                break;
	            // TODO: $this->get_users( $atts );
	            default:
	                $html .= '';
	                break;
	        }

        $html .= '</div>';

		return $html;

	}


	function get_content_type( $content_types ) {

		/**
		 * If types are all post types.
		 * get_post_type() on its own gets all built in and custom post types.
		 */
		if ( array_intersect( $content_types, get_post_types() ) == $content_types ) {
			return 'post'; // Means any post_type
		} else {

			$taxos = get_taxonomies( array(
			   'public' => true,
			), 'names' );

			if ( array_intersect( $content_types, $taxos ) == $content_types ) {
				return 'term';
			}

		}

		return false;

	}

	function get_row_wrap_open( $atts, $context = 'grid' ) {

		$flex_row = array();

		// Main row class
	    $flex_row['class'] = 'row';

	    $is_valid_gutter = false;
	    // Gutter
	    if ( $atts['gutter'] ) {
	    	// If gutter is a valid Flexington size
			if ( in_array( $atts['gutter'], array( 5, 10, 20, 30, 40, 50 ) ) ) {
				$is_valid_gutter = true;
			}
	    }

	    // Row classes
	    if ( ! empty( $atts['row_class'] ) ) {
	    	$flex_row['class'] .= ' ' . $atts['row_class'];
	    }

	    // If posts are a slider. 'slider' may not be set if coming from [columns] shortcode.
		if ( isset( $atts['slider'] ) && $atts['slider'] ) {

			// Enqueue Slick Carousel
			wp_enqueue_script( 'mai-slick' );
			wp_enqueue_script( 'mai-slick-init' );

			// Slider wrapper class
			$flex_row['class'] .= ' mai-slider';

			// Slider HTML data attributes
			$flex_row['data-arrows']		 = $atts['arrows'] ? 'true' : 'false';
			$flex_row['data-center']		 = in_array( 'center', $atts['align'] ) ? 'true' : 'false';
			$flex_row['data-centermode']	 = $atts['center_mode'] ? 'true' : 'false';
			$flex_row['data-dots']			 = $atts['dots'] ? 'true' : 'false';
			$flex_row['data-fade']			 = $atts['fade'] ? 'true' : 'false';
			$flex_row['data-infinite']		 = $atts['infinite'] ? 'true' : 'false';
			$flex_row['data-middle']		 = in_array( 'middle', $atts['align'] ) ? 'true' : 'false';
			$flex_row['data-slidestoscroll'] = $atts['slidestoscroll'];
			$flex_row['data-slidestoshow']	 = $atts['columns'];
			$flex_row['data-gutter']		 = $is_valid_gutter ? $atts['gutter'] : 0;

		}
		// Flex row classes are not on slider
		else {

			// Add gutter
	    	if ( $is_valid_gutter ) {
				$flex_row['class'] .= sprintf( ' gutter-%s', $atts['gutter'] );
		    }

		    $flex_row['class'] = $this->add_row_align_classes( $flex_row['class'], $atts, $context );

		}

		// WooCommerce. 'content' may not be set if coming from [columns] shortcode
		if ( isset( $atts['content'] ) && class_exists( 'WooCommerce' ) && in_array( 'product', $atts['content'] ) ) {
			$flex_row['class'] .= ' woocommerce';
		}

	    /**
	     * Main content row wrap.
	     * With flex-row attr so devs can filter elsewhere.
	     */
	    return sprintf( '<div %s>', genesis_attr( 'flex-row', $flex_row ) );

	}

	function get_row_wrap_close( $atts ) {
		return '</div>';
	}

	function get_entry_wrap_open( $atts, $object, $has_image_bg ) {

		$flex_entry = array();

		// Add href if linking element
		if ( $this->is_linking_element( $atts, $has_image_bg ) ) {
			$flex_entry['href'] = $this->get_entry_link( $atts, $object );
		}

		// Set the entry classes
		$flex_entry['class'] = $this->get_entry_classes( $atts );

		// Add the align classes
	    $flex_entry['class'] = $this->add_entry_align_classes( $flex_entry['class'], $atts );

		if ( $this->is_image_bg( $atts ) && $has_image_bg ) {
			// Get the object ID
			$object_id = $this->get_object_id( $atts, $object );
			if ( $object_id ) {
				$flex_entry = $this->add_image_bg( $flex_entry, $atts, $object_id );
			}
		}

		/**
		 * Main entry col wrap.
		 * If we use genesis_attr( 'entry' ) then it resets the classes.
		 */
		return sprintf( '<%s %s>', $this->get_entry_wrap_element( $atts, $has_image_bg ), genesis_attr( 'flex-entry', $flex_entry ) );
	}

	function get_entry_wrap_close( $atts, $has_image_bg ) {
		return sprintf( '</%s>', $this->get_entry_wrap_element( $atts, $has_image_bg ) );
	}

	/**
	 * Add align classes to the row.
	 *
	 * @param   string   $classes   The existing classes.
	 * @param   array    $atts      The attributes from the shortcode or helper function.
	 * @param   string   $context   The shortcode context (grid or columns).
	 *
	 * @return  string   The modified classes
	 */
	function add_row_align_classes( $classes, $atts, $context = 'grid' ) {

	    /**
	     * "align" takes precendence over "align_cols" and "align_text".
	     * "align" forces the text to align along with the cols.
	     */
	    if ( ! empty( $atts['align'] ) ) {
	    	// Left
		    if ( in_array( 'left', $atts['align'] ) ) {
		    	$classes .= ' start-xs text-xs-left';
		    }

		    // Center
		    if ( in_array( 'center', $atts['align'] ) ) {
		    	$classes .= ' center-xs text-xs-center';
		    }

		    // Right
		    if ( in_array( 'right', $atts['align'] ) ) {
		    	$classes .= ' end-xs text-xs-right';
		    }

		    // These are added to the entries when context is 'grid'
			if ( 'columns' == $context ) {

			    // Top
			    if ( in_array( 'top', $atts['align'] ) ) {
			    	$classes .= ' top-xs';
			    }

			    // Middle
			    if ( in_array( 'middle', $atts['align'] ) ) {
			    	$classes .= ' middle-xs';
			    }

			    // Bottom
			    if ( in_array( 'bottom', $atts['align'] ) ) {
			    	$classes .= ' bottom-xs';
			    }

			}

	    } else {

		    // Align columns
		    if ( ! empty( $atts['align_cols'] ) ) {

		    	// Left
			    if ( in_array( 'left', $atts['align_cols'] ) ) {
			    	$classes .= ' start-xs';
			    }

			    // Center
			    if ( in_array( 'center', $atts['align_cols'] ) ) {
			    	$classes .= ' center-xs';
			    }

			    // Right
			    if ( in_array( 'right', $atts['align_cols'] ) ) {
			    	$classes .= ' end-xs';
			    }

			    // Top
			    if ( in_array( 'top', $atts['align_cols'] ) ) {
			    	$classes .= ' top-xs';
			    }

			    // Middle
			    if ( in_array( 'middle', $atts['align_cols'] ) ) {
			    	$classes .= ' middle-xs';
			    }

			    // Bottom
			    if ( in_array( 'bottom', $atts['align_cols'] ) ) {
			    	$classes .= ' bottom-xs';
			    }

		    }

		}

		return $classes;
	}

	function add_entry_align_classes( $classes, $atts, $content = 'grid' ) {

	    /**
	     * "align" takes precendence over "align_cols" and "align_text".
	     * "align" forces the text to align along with the cols.
	     */
	    if ( ! empty( $atts['align'] ) ) {
	    	// Left
		    if ( in_array( 'left', $atts['align'] ) ) {
		    	// $classes .= ' start-xs text-xs-left';
		    	$classes .= ' top-xs text-xs-left';
		    }

		    // Center
		    if ( in_array( 'center', $atts['align'] ) ) {
		    	// $classes .= ' center-xs text-xs-center';
		    	$classes .= ' middle-xs text-xs-center';
		    }

		    // Right
		    if ( in_array( 'right', $atts['align'] ) ) {
		    	// $classes .= ' end-xs text-xs-right';
		    	$classes .= ' bottom-xs text-xs-right';
		    }

		    // Top
		    if ( in_array( 'top', $atts['align'] ) ) {
		    	// $classes .= ' top-xs';
		    	$classes .= ' start-xs';
		    }

		    // Middle
		    if ( in_array( 'middle', $atts['align'] ) ) {
		    	// $classes .= ' middle-xs';
		    	$classes .= ' center-xs';
		    }

		    // Bottom
		    if ( in_array( 'bottom', $atts['align'] ) ) {
		    	// $classes .= ' bottom-xs';
		    	$classes .= ' end-xs';
		    }

	    } else {

		    // Align text
		    if ( ! empty( $atts['align_text'] ) ) {

		    	// Left
			    if ( in_array( 'left', $atts['align_text']) ) {
			    	$classes .= ' text-xs-left';
			    }

			    // Center
			    if ( in_array( 'center', $atts['align_text'] ) ) {
			    	$classes .= ' text-xs-center';
			    }

			    // Right
			    if ( in_array( 'right', $atts['align_text'] ) ) {
			    	$classes .= ' text-xs-right';
			    }

			    // Top
			    if ( in_array( 'top', $atts['align_text'] ) ) {
			    	// $classes .= ' top-xs';
			    	$classes .= ' start-xs';
			    }

			    // Middle
			    if ( in_array( 'middle', $atts['align_text'] ) ) {
			    	// $classes .= ' middle-xs';
			    	$classes .= ' center-xs';
			    }

			    // Bottom
			    if ( in_array( 'bottom', $atts['align_text'] ) ) {
			    	// $classes .= ' bottom-xs';
			    	$classes .= ' end-xs';
			    }

		    }

		}

		return $classes;
	}

	function get_posts( $atts, $original_atts ) {

		$number = $this->get_number( $atts );

		// Set up initial query for posts
		$args = array(
			'post_type'		 => $atts['content'],
			'posts_per_page' => $number,
		);

		// Authors
		if ( ! empty($atts['authors']) ) {
			if ( 'current' == $atts['authors'] && is_user_logged_in() ) {
				$args['author__in'] = get_current_user_id();
			} elseif( 'current' == $atts['authors'] ) {
				// Force an unused meta key so no results are found
				$args['meta_key'] = 'mai_no_results_abcdefg';
			} else {
				$args['author__in'] = explode( ',', sanitize_text_field( $atts['author'] ) );
			}
		}

		// Categories
		if ( ! empty($atts['categories']) ) {
			$args['category__in'] = $atts['categories'];
		}

		// Exclude
		if ( ! empty($atts['exclude']) ) {
			$args['post__not_in'] = $atts['exclude'];
		}

		// If Exclude Current
		if ( is_singular() && $atts['exclude_current'] ) {
			// If this args is already set (probably from 'exclude')
			if ( isset( $args['post__not_in'] ) ) {
				$args['post__not_in'] = array_push( $args['post__not_in'], get_the_ID() );
			} else {
				$args['post__not_in'] = array( get_the_ID() );
			}
		}

		// Post IDs
		if ( ! empty($atts['ids']) ) {
			$args['post__in'] = $atts['ids'];
		}

		// Ignore Sticky Posts
		if ( $atts['ignore_sticky_posts'] ) {
			$args['ignore_sticky_posts'] = true;
		}

		// Order
		if ( ! empty($atts['order']) ) {
			$args['order'] = $atts['order'];
		}

		// Orderby
		if ( ! empty($atts['order_by']) ) {
			$args['orderby'] = $atts['order_by'];
		}

		// Meta key (for ordering)
		if ( ! empty( $atts['meta_key'] ) ) {
			$args['meta_key'] = $atts['meta_key'];
		}

		// Meta value (for simple meta queries)
		if ( ! empty( $atts['meta_value'] ) ) {
			$args['meta_value'] = $atts['meta_value'];
		}

		// Offset. Empty string is sanitized to zero.
		if ( $atts['offset'] > 0 ) {
			$args['offset'] = $atts['offset'];
		}

		// If post parent attribute, set up parent
		if ( ! empty($atts['parent']) ) {
			if ( is_singular() && 'current' == $atts['parent'] ) {
				$args['post_parent'] = get_the_ID();
			} else {
				$args['post_parent'] = intval( $atts['parent'] );
			}
		}

		// Status
		if ( ! empty($atts['status']) ) {
			$args['post_status'] = $atts['status'];
		}

		// Tags
		if ( ! empty($atts['tags']) ) {
			$args['tag__in'] = $atts['tags'];
		}

		// Tax query
		if ( ! empty($atts['taxonomy']) && ! empty($atts['terms']) ) {
			if ( 'current' == $atts['terms'] ) {
				$terms		= array();
				$post_terms	= wp_get_post_terms( get_the_ID(), $atts['taxonomy'] );
				if ( is_wp_error( $post_terms ) ) {
					foreach ( $post_terms as $term ) {
						$terms[] = $term->slug;
					}
				}
			} else {
				// Term string to array
				$terms = explode( ',', $atts['terms'] );
			}

			// Validate operator
			if ( ! in_array( $atts['tax_operator'], array( 'IN', 'NOT IN', 'AND' ) ) ) {
				$atts['tax_operator'] = 'IN';
			}

			$args['tax_query'] = array(
				array(
					'taxonomy'         => $atts['taxonomy'],
					'field'            => $atts['tax_field'],
					'terms'            => $terms,
					'operator'         => $atts['tax_operator'],
					'include_children' => $atts['tax_include_children'],
				)
			);
		}


		/**
		 * Temporarily disabled cause this is coming from [grid] and [columns] now
		 *
		 * Filter the arguments passed to WP_Query.
		 *
		 * @param array $args          Parsed arguments to pass to WP_Query.
		 * @param array $original_atts Original attributes passed to the shortcode.
		 */
		// $args = apply_filters( 'mai_grid_args', $args, $original_atts );

		// Get our query
		$query = new WP_Query( $args );

		// If no posts
		if ( ! $query->have_posts() ) {
			/**
			 * Filter content to display if no posts match the current query.
			 *
			 * @param string $no_posts_message Content to display, returned via {@see wpautop()}.
			 */
			return apply_filters( 'mai_grid_no_results', wpautop( $atts['no_content_message'] ) );
		}

		// Get it started
		$html = '';

		$html .= $this->get_grid_title( $atts );

		$html .= $this->get_row_wrap_open( $atts );

			// Loop through posts
			while ( $query->have_posts() ) : $query->the_post();

				global $post;

				$image_html = $entry_header = $date = $author = $entry_meta = $entry_content = $entry_footer = $image_id = '';

				// Get image vars
				$do_image_bg = $has_image_bg = false;

				// If showing image
				if ( in_array( 'image', $atts['show'] ) ) {
					$image_id = $this->get_image_id( $atts, get_the_ID() );
					if ( $image_id ) {
						$do_image_bg = true;
						if ( $this->is_image_bg( $atts ) ) {
							$has_image_bg = true;
						}
					}
				}

				// Opening wrap
				$html .= $this->get_entry_wrap_open( $atts, $post, $has_image_bg );

					// Set url as a variable
					$url = $this->get_entry_link( $atts, $post );

					// Image
					if ( $do_image_bg && ! $this->is_image_bg( $atts ) ) {
						if ( $image_id ) {
							$image = wp_get_attachment_image( $image_id, $atts['image_size'], false, array( 'class' => 'wp-post-image' ) );
							if ( $image ) {
								if ( $atts['link'] ) {
									$image_html = sprintf( '<a href="%s" class="entry-image-link" title="%s">%s</a>', $url, the_title_attribute( 'echo=0' ), $image );
								} else {
									$image_html = $image;
								}
							}
						}
					}

					// Image
					if ( 'before_entry' == $atts['image_location'] ) {
						$html .= $image_html;
					}

					// Date
					if ( in_array( 'date', $atts['show'] ) ) {
						/**
						 * If date formate is set in shortcode, use that format instead of default Genesis.
						 * Since using G post_date shortcode you can also use 'relative' for '3 days ago'.
						 */
						$date_before	= $atts['date_before'] ? ' before="' . $atts['date_before'] . '"' : '';
						$date_after		= $atts['date_after'] ? ' after="' . $atts['date_after'] . '"' : '';
						$date_format	= $atts['date_format'] ? ' format="' . $atts['date_format'] . '"' : '';
						$date_shortcode	= sprintf( '[post_date%s%s%s]', $date_before, $date_after, $date_format );
						// Use Genesis output for post date
						$date = do_shortcode( $date_shortcode );
					}

					// Author
					if ( in_array( 'author', $atts['show'] ) ) {
						/**
						 * If author has no link this shortcode defaults to genesis_post_author_shortcode() [post_author]
						 */
						$author_before	  = $atts['author_before'] ? ' before="' . $atts['author_before'] . '"' : '';
						$author_after	  = $atts['author_after'] ? ' after="' . $atts['author_after'] . '"' : '';
						// Can't have a nested link if we have a background image
						if ( $has_image_bg ) {
							$author_shortcode_name = 'post_author';
						} else {
							$author_shortcode_name = 'post_author_link';
						}
						$author_shortcode = sprintf( '[%s%s%s]', $author_shortcode_name, $author_before, $author_after );
						// Use Genesis output for author, including link
						$author = do_shortcode( $author_shortcode );
					}

					// Build entry meta
					if ( $date || $author ) {
						$entry_meta .= sprintf( '<p %s>%s%s</p>', genesis_attr( 'entry-meta-before-content' ), $date, $author );
					}

					// Build entry header
					if ( $this->is_entry_header_image( $atts ) || in_array( 'title', $atts['show'] ) || $entry_meta ) {

						// Entry header open
						$html .= sprintf( '<header %s>', genesis_attr( 'entry-header' ) );

							// Image
							if ( 'before_title' == $atts['image_location'] ) {
								$html .= $image_html;
							}

							// Title
							if ( in_array( 'title', $atts['show'] ) ) {
								if ( $atts['link'] && ! $has_image_bg ) {
									$title = sprintf( '<a href="%s" title="%s">%s</a>', $url, esc_attr( get_the_title() ), get_the_title() );
								} else {
									$title = get_the_title();
								}
								$html .= sprintf( '<%s %s>%s</%s>', $atts['title_wrap'], genesis_attr( 'entry-title' ), $title, $atts['title_wrap'] );
							}

							// Image
							if ( 'after_title' == $atts['image_location'] ) {
								$html .= $image_html;
							}

							// Entry Meta
							if ( $entry_meta ) {
								$html .= $entry_meta;
							}

						// Entry header close
						$html .= '</header>';

					}

					// Image
					if ( 'before_content' == $atts['image_location'] ) {
						$html .= $image_html;
					}

					// Excerpt
					if ( in_array( 'excerpt', $atts['show'] ) ) {
						// Strip tags and shortcodes cause things go nuts, especially if showing image as background
						$entry_content .= wpautop( wp_strip_all_tags( strip_shortcodes( get_the_excerpt() ) ) );
					}

					// Content
					if ( in_array( 'content', $atts['show'] ) ) {
						$entry_content .= wp_strip_all_tags( strip_shortcodes( get_the_content() ) );
					}

					// Limit content. Empty string is sanitized to zero.
					if ( $atts['content_limit'] > 0 ) {
						// Reset the variable while trimming the content
						$entry_content = wpautop( wp_trim_words( $entry_content, $atts['content_limit'], '&hellip;' ) );
					}

					if ( in_array( 'price', $atts['show'] ) ) {
						ob_start();
						woocommerce_template_loop_price();
						$entry_content .= ob_get_clean();
					}

					// More link
					if ( $atts['link'] && in_array( 'more_link', $atts['show'] ) ) {
						$entry_content .= $this->get_more_link( $atts, $url, $has_image_bg );
					}

					// Add to cart link
					if ( $atts['link'] && in_array( 'add_to_cart', $atts['show'] ) ) {
						$entry_content .= $this->get_add_to_cart_link( $atts, $url, $has_image_bg );
					}

					// Add entry content wrap if we have content
					if ( $entry_content ) {
						$html .= sprintf( '<div %s>%s</div>', genesis_attr( 'entry-content' ), $entry_content );
					}

					// Meta
					if ( in_array( 'meta', $atts['show'] ) ) {
						$entry_footer = mai_get_post_meta( get_the_ID() );
					}

					// Entry footer
					if ( $entry_footer ) {
						$html .= sprintf( '<footer %s>%s</footer>', genesis_attr( 'entry-footer' ), $entry_footer );
					}

				$html .= $this->get_entry_wrap_close( $atts, $has_image_bg );

			endwhile;
			wp_reset_postdata();

		$html .= $this->get_row_wrap_close( $atts );

		return $html;

	}


	function get_terms( $atts ) {

		$number = $this->get_number( $atts );

		// Set up initial query for terms
		$args = array(
			'hide_empty' => $atts['hide_empty'],
			'number'	 => $number,
			'taxonomy'	 => $atts['content'],
		);

		// Exclude
		if ( ! empty($atts['exclude']) ) {
			$args['exclude_tree'] = $atts['exclude'];
		}

		// Terms IDs
		if ( ! empty($atts['ids']) ) {
			$args['include'] = $atts['ids'];
		}

		// Order
		if ( ! empty($atts['order']) ) {
			$args['order'] = $atts['order'];
		}

		// Orderby
		if ( ! empty($atts['order_by']) ) {
			$args['orderby'] = $atts['order_by'];
		}

		// Meta key (for ordering)
		if ( ! empty( $atts['meta_key'] ) ) {
			$args['meta_key'] = $atts['meta_key'];
		}

		// Meta value (for simple meta queries)
		if ( ! empty( $atts['meta_value'] ) ) {
			$args['meta_value'] = $atts['meta_value'];
		}

		// Offset. Empty string is sanitized to zero.
		if ( $atts['offset'] > 0 ) {
			$args['offset'] = $atts['offset'];
		}

		// If post parent attribute, set up parent
		if ( ! empty($atts['parent']) ) {
			if ( ( is_category() || is_tag() || is_tax() ) && 'current' == $atts['parent'] ) {
				$args['parent'] = get_queried_object_id();
			} else {
				$args['parent'] = intval( $atts['parent'] );
			}
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) ) {
			/**
			 * Filter content to display if no posts match the current query.
			 *
			 * @param string $no_posts_message Content to display, returned via {@see wpautop()}.
			 */
			return apply_filters( 'grid_shortcode_no_results', wpautop( $atts['no_content_message'] ) );
		}

		$html = '';

		$html .= $this->get_grid_title( $atts );

		$html .= $this->get_row_wrap_open( $atts );

			foreach ( $terms as $term ) {

				$image_html = $entry_header = $date = $author = $entry_meta = $entry_content = $image_id = '';

				// Get image vars
				$do_image_bg = $has_image_bg = false;

				// If showing image
				if ( in_array( 'image', $atts['show'] ) ) {
					$image_id = $this->get_image_id( $atts, $term->term_id );
					if ( $image_id ) {

						$do_image_bg = true;
						if ( $this->is_image_bg( $atts ) ) {
							$has_image_bg = true;
						}
					}
				}

				// Opening wrap
				$html .= $this->get_entry_wrap_open( $atts, $term, $has_image_bg );

					// Set url as a variable
					$url = $this->get_entry_link( $atts, $term );

					// Image
					if ( $do_image_bg && ! $this->is_image_bg( $atts ) ) {
						if ( $image_id ) {
							$image = wp_get_attachment_image( $image_id, $atts['image_size'], false, array( 'class' => 'wp-post-image' ) );
							if ( $image ) {
								if ( $atts['link'] ) {
									$image_html = sprintf( '<a href="%s" class="entry-image-link" title="%s">%s</a>', $url, esc_attr( $term->name ), $image );
								} else {
									$image_html = $image;
								}
							}
						}
					}

					// Image
					if ( 'before_entry' == $atts['image_location'] ) {
						$html .= $image_html;
					}

					// Build entry header
					if ( $this->is_entry_header_image( $atts ) || in_array( 'title', $atts['show'] ) ) {

						// Entry header open
						$html .= sprintf( '<header %s>', genesis_attr( 'entry-header' ) );

							// Image
							if ( 'before_title' == $atts['image_location'] ) {
								$html .= $image_html;
							}

							// Title
							if ( $atts['link'] && ! $has_image_bg ) {
								$title = sprintf( '<a href="%s" title="%s">%s</a>', $url, esc_attr( $term->name ), $term->name );
							} else {
								$title = $term->name;
							}
							$html .= sprintf( '<%s %s>%s</%s>', $atts['title_wrap'], genesis_attr( 'entry-title' ), $title, $atts['title_wrap'] );

							// Image
							if ( 'after_title' == $atts['image_location'] ) {
								$html .= $image_html;
							}

						// Entry header close
						$html .= '</header>';

					}

					// Image
					if ( 'before_content' == $atts['image_location'] ) {
						$html .= $image_html;
					}

					// Excerpt/Content
					if ( in_array( 'excerpt', $atts['show'] ) || in_array( 'content', $atts['show'] ) ) {
						$entry_content .= wpautop( wp_strip_all_tags( strip_shortcodes( term_description( $term->term_id, $term->taxonomy ) ) ) );
					}

					// Limit content. Empty string is sanitized to zero.
					if ( $atts['content_limit'] > 0 ) {
						// Reset the variable while trimming the content
						$entry_content = wpautop( wp_trim_words( $entry_content, $atts['content_limit'], '&hellip;' ) );
					}

					// More link
					if ( $atts['link'] && in_array( 'more_link', $atts['show'] ) ) {
						$entry_content .= $this->get_more_link( $atts, $url, $has_image_bg );
					}

					// Add entry content wrap if we have content
					if ( $entry_content ) {
						$html .= sprintf( '<div %s>%s</div>', genesis_attr( 'entry-content' ), $entry_content );
					}

				$html .= $this->get_entry_wrap_close( $atts, $has_image_bg );

			}

		$html .= $this->get_row_wrap_close( $atts );

		return $html;

	}

	function get_grid_title( $atts ) {

		// Bail if no title
		if ( empty( $atts['grid_title'] ) ) {
			return;
		}
		$classes = 'heading ' . $atts['grid_title_class'];
		return sprintf( '<%s class="%s">%s</%s>', $atts['grid_title_wrap'], trim($classes), $atts['grid_title'], $atts['grid_title_wrap'] );
	}

	function get_entry_wrap_element( $atts, $has_image_bg ) {
		return $this->is_linking_element( $atts, $has_image_bg ) ? 'a' : 'div';
	}

	/**
	 * Whether the main entry element should be a link or not.
	 *
	 * @param   array  $atts  The shortcode atts
	 *
	 * @return  bool
	 */
	function is_linking_element( $atts, $has_image_bg ) {
		if ( $this->is_image_bg( $atts ) && $atts['link'] && $has_image_bg ) {
			return true;
		}
		return false;
	}

	function is_image_bg( $atts ) {
	    switch ( $atts['image_location'] ) {
	        case 'bg':
	        case 'background':
	            $return = true;
	            break;
	        default:
	            $return = false;
	            break;
	    }
	    return $return;
	}

	function is_entry_header_image( $atts ) {
	    switch ( $atts['image_location'] ) {
	        case 'before_title':
	        case 'after_title':
	            $return = true;
	            break;
	        default:
	            $return = false;
	            break;
	    }
	    return $return;
	}

	function get_entry_link( $atts, $object_or_id ) {
	    switch ( $atts['content_type'] ) {
	        case 'post':
	        	$link = '';
				if ( in_array( 'add_to_cart', $atts['show'] ) ) {
					if ( class_exists( 'WooCommerce' ) ) {
						$product = wc_get_product( $object_or_id );
						$link 	 = $product->add_to_cart_url();
					}
				}
				if ( ! $link ) {
		            $link = get_permalink( $object_or_id );
				}
	            break;
	        case 'term':
	            $link = get_term_link( $object_or_id );
	            break;
	        default:
	            $link = '';
	            break;
	    }
	    return $link;
	}

	function get_entry_classes( $atts ) {

		// We need classes to be an array so we can use them in get_post_class()
		$classes = array( 'flex-entry', 'entry' );

		// Add any custom classes
		if ( $atts['entry_class'] ) {
			$classes = array_merge( $classes, explode( ' ', $atts['entry_class'] ) );
		}

		// If not a slider
		if ( ! $atts['slider'] ) {
			// Add Flexington columns
			$classes = array_merge( $classes, explode( ' ', mai_get_flex_entry_classes_by_columns( $atts['columns'] ) ) );
		} else {
			// Add slide class
			$classes[] = 'mai-slide';
		}

		// If dealing with a post object
		if ( 'post' == $atts['content_type'] ) {

		    /**
		     * Merge our new classes with the default WP generated classes.
		     * Also removes potential duplicate flex-entry since we need it even if slider.
		     */
			$classes = array_map( 'sanitize_html_class', get_post_class( array_unique( $classes ), get_the_ID() ) );

		}

		// Turn array into a string of space separated classes
		return implode( ' ', $classes );
	}

	function get_object_id( $atts, $object ) {
	    switch ( $atts['content_type'] ) {
	        case 'post':
	            $id = $object->ID;
	            break;
	        case 'term':
	            $id = $object->term_id;
	            break;
	        case 'user':
	            $id = $object->ID;
	            break;
	        default:
	            $id = '';
	            break;
	    }
	    return $id;
	}

	/**
	 * Maybe add the featured image as inline style.
	 *
	 * @param   array  $attributes  The genesis_attr attributes
	 * @param   array  $atts        The shortcode atts
	 *
	 * @return  array              [description]
	 */
	function add_image_bg( $attributes, $atts, $object_id ) {
		// Get the image ID
		$image_id = $this->get_image_id( $atts, $object_id );
	    if ( ! $image_id ) {
	    	return $attributes;
	    }

	    // $image = wp_get_attachment_image_src( $image_id, $atts['image_size'], true );
		// $attributes['class'] .= ' image-bg aspect-ratio overlay light-content';
		// $attributes['style']  = 'background-image: url(' . $image[0] . ');';

		$attributes['class'] .= ' overlay light-content';

		// Add the image background attributes
		$attributes = mai_add_image_background_attributes( $attributes, $image_id, $atts['image_size'] );

	    return $attributes;
	}

	/**
	 * Get a type of content main image ID.
	 * Needs to be used in the loop, so it can get the correct content type ID.
	 *
	 * @param  string  $type        The type of content, either 'post', 'term', or 'user'
	 * @param  int     $object_id   The object ID, either $post_id, $term_id, or $user_id
	 *
	 * @return int     The image ID
	 */
	function get_image_id( $atts, $object_id ) {
	    switch ( $atts['content_type'] ) {
	        case 'post':
	            $image_id = get_post_thumbnail_id( $object_id );
	            break;
	        case 'term':
	            $key = 'banner_id';
	            // If the term is a WooCommerce Product Category, change the key
	            if ( class_exists( 'WooCommerce' ) && ( 'product_cat' == $atts['taxonomy'] ) ) {
                    $key = 'thumbnail_id';
	            }
	            $image_id = get_term_meta( $object_id, $key, true );
	            break;
	        case 'user':
	            $image_id = get_user_meta( $object_id, 'banner_id', true );
	            break;
	        default:
	            $image_id = '';
	            break;
	    }
	    return $image_id;
	}

	function get_more_link( $atts, $url, $has_image_bg ) {
		if ( $this->is_image_bg( $atts ) && $has_image_bg ) {
			$link = sprintf( '<span class="more-link">%s</span>', $atts['more_link_text'] );
		} else {
			$link = sprintf( '<a class="more-link" href="%s">%s</a>', $url, $atts['more_link_text'] );
		}
	    return sprintf( '<p class="more-link-wrap">%s</p>', $link );
	}

	function get_add_to_cart_link( $atts, $url, $has_image_bg ) {
		$link = '';
		if ( class_exists( 'WooCommerce' ) ) {
			$product = wc_get_product( get_the_ID() );
			if ( $this->is_image_bg( $atts ) && $has_image_bg ) {
				$link = sprintf( '<span class="more-link">%s</span>', $product->add_to_cart_text() );
			} else {
				ob_start();
				woocommerce_template_loop_add_to_cart();
				$link = ob_get_clean();
			}
		}
		return $link ? sprintf( '<p class="more-link-wrap">%s</p>', $link ) : '';
	}

	/**
	 * Get the number of items to show.
	 * If all, return the appropriate value depending on content type.
	 *
	 * @param  array  $atts        The shortcode atts
	 *
	 * @return int 	  The number of items
	 */
	function get_number( $atts ) {
		if ( 'all' === $atts['number'] ) {
		    switch ( $atts['content_type'] ) {
		        case 'post':
		            $number = -1; // wp_query uses -1 for all
		            break;
		        case 'term':
		            $number = 0;  // get_terms() uses 0 for all
		            break;
		        default:
		            $number = 100; // Just to be safe, cause we may add user later
		            break;
		    }
		} else {
			$number = $atts['number'];
		}
		return intval( $number );
	}

}

/**
 * The main function for that returns Mai_Shortcodes
 *
 * The main function responsible for returning the one true Mai_Shortcodes
 * Instance to functions everywhere.
 *
 * @since 1.0.0
 *
 * @return object|Mai_Shortcodes The one true Mai_Shortcodes Instance.
 */
function Mai_Shortcodes() {
	return Mai_Shortcodes::instance();
}

// Get Mai_Shortcodes Running.
Mai_Shortcodes();
