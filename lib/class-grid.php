<?php

class Mai_Grid extends Mai_Content {

	private $args;

	private $original_args;

	private $content_type;

	// private $ids; // $ids to store incase exclude_existing is true.

	private $facetwp = false;

	function __contruct( $args ) {

		// Save original args in a variable for filtering later.
		$this->original_args = $args;

		// Pull in shortcode attributes and set defaults.
		$args = shortcode_atts( array(
			'align'                => '',  // "top, left" Comma separted. overrides align_cols and align_text for most times one setting makes sense
			'align_cols'           => '',  // "top, left" Comma separted
			'align_text'           => '',  // "center" Comma separted
			'author_after'         => '',
			'author_before'        => '',
			'authors'              => '',  // Comma separated author/user IDs
			'bottom'               => '',  // Bottom margin. 0, 5, 10, 20, 30, 40, 50, 60.
			'categories'           => '',  // Comma separated category IDs
			'columns'              => 3,
			'content'              => 'post',  // post_type name (comma separated if multiple), or taxonomy name
			'content_limit'        => '',  // Limit number of words
			'content_type'         => '',
			'date_after'           => '',
			'date_before'          => '',
			'date_format'          => '',
			'date_query_after'     => '',
			'date_query_before'    => '',
			'entry_class'          => '',
			'exclude'              => '',
			'exclude_categories'   => '',  // Comma separated category IDs
			'exclude_current'      => false,
			'facetwp'              => false,
			'grid_title'           => '',
			'grid_title_class'     => '',
			'grid_title_wrap'      => 'h2',
			'gutter'               => '30',
			'hide_empty'           => true,
			'ids'                  => '',
			'ignore_sticky_posts'  => true,  // normal WP_Query is false
			'image_align'          => '',
			'image_location'       => 'before_entry',
			'image_size'           => 'one-third',
			'link'                 => true,
			'meta_key'             => '',
			'meta_value'           => '',
			'more_link_text'       => __( 'Read More', 'mai-theme-engine' ),
			'no_content_message'   => '',
			'number'               => '12',
			'offset'               => '0',
			'order'                => '',
			'order_by'             => '',
			'overlay'              => '',
			'parent'               => '',
			'row_class'            => '',
			'show'                 => 'image, title',  // image, title, add_to_cart, author, content, date, excerpt, image, more_link, price, meta, title
			'status'               => '',  // Comma separated for multiple
			'tags'                 => '',  // Comma separated tag IDs
			'tax_include_children' => true,
			'tax_operator'         => 'IN',
			'tax_field'            => 'term_id',
			'taxonomy'             => '',
			'terms'                => '',
			'title_wrap'           => 'h3',
			'class'                => '',
			'id'                   => '',
			'slider'               => false,   // (slider only) Make the columns a slider
			'arrows'               => true,    // (slider only) Whether to display arrows
			'autoplay'             => false,   // (slider only) Whether to autoplay the slider
			'center_mode'          => false,   // (slider only) Mobile 'peek'
			'dots'                 => false,   // (slider only) Whether to display dots
			'fade'                 => false,   // (slider only) Fade instead of left/right scroll (works requires slidestoshow 1)
			'infinite'             => true,    // (slider only) Loop slider
			'slidestoscroll'       => $this->get_slidestoscroll_default( $args ),  // (slider only) The amount of posts to scroll. Defaults to the amount of columns to show.
			'speed'                => '3000',  // (slider only) Autoplay Speed in milliseconds
		), $args, 'grid' );

		$args = array(
			'align'                => mai_sanitize_keys( $args['align'] ),
			'align_cols'           => mai_sanitize_keys( $args['align_cols'] ),
			'align_text'           => mai_sanitize_keys( $args['align_text'] ),
			'author_after'         => sanitize_key( $args['author_after'] ),
			'author_before'        => sanitize_key( $args['author_before'] ),
			'authors'              => $args['authors'], // Validated later
			'bottom'               => ! empty( $args['bottom'] ) ? absint( $args['bottom'] ) : '',
			'categories'           => array_filter( explode( ',', sanitize_text_field( $args['categories'] ) ) ),
			'columns'              => absint( $args['columns'] ),
			'content'              => array_filter( explode( ',', sanitize_text_field( $args['content'] ) ) ),
			'content_limit'        => absint( $args['content_limit'] ),
			'content_type'         => sanitize_text_field( $args['content_type'] ),
			'date_after'           => sanitize_text_field( $args['date_after'] ),
			'date_before'          => sanitize_text_field( $args['date_before'] ),
			'date_format'          => sanitize_text_field( $args['date_format'] ),
			'date_query_after'     => sanitize_text_field( $args['date_query_after'] ),
			'date_query_before'    => sanitize_text_field( $args['date_query_before'] ),
			'entry_class'          => sanitize_text_field( $args['entry_class'] ),
			'exclude'              => array_filter( explode( ',', sanitize_text_field( $args['exclude'] ) ) ),
			'exclude_categories'   => array_filter( explode( ',', sanitize_text_field( $args['exclude_categories'] ) ) ),
			'exclude_current'      => filter_var( $args['exclude_current'], FILTER_VALIDATE_BOOLEAN ),
			'facetwp'              => filter_var( $args['facetwp'], FILTER_VALIDATE_BOOLEAN ),
			'grid_title'           => sanitize_text_field( $args['grid_title'] ),
			'grid_title_class'     => sanitize_text_field( $args['grid_title_class'] ),
			'grid_title_wrap'      => sanitize_key( $args['grid_title_wrap'] ),
			'gutter'               => $this->is_valid_gutter( absint( $args['gutter'] ) ) ? absint( $args['gutter'] ) : 30,
			'hide_empty'           => filter_var( $args['hide_empty'], FILTER_VALIDATE_BOOLEAN ),
			'ids'                  => array_filter( array_map( 'absint', explode( ',', sanitize_text_field( $args['ids'] ) ) ) ),
			'ignore_sticky_posts'  => filter_var( $args['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN ),
			'image_align'          => sanitize_key( $args['image_align'] ),
			'image_location'       => sanitize_key( $args['image_location'] ),
			'image_size'           => sanitize_key( $args['image_size'] ),
			'link'                 => filter_var( $args['link'], FILTER_VALIDATE_BOOLEAN ),
			'meta_key'             => sanitize_text_field( $args['meta_key'] ),
			'meta_value'           => sanitize_text_field( $args['meta_value'] ),
			'more_link_text'       => sanitize_text_field( $args['more_link_text'] ),
			'no_content_message'   => sanitize_text_field( $args['no_content_message'] ),
			'number'               => $args['number'], // Validated later, after check for 'all'
			'offset'               => absint( $args['offset'] ),
			'order'                => sanitize_key( $args['order'] ),
			'order_by'             => sanitize_key( $args['order_by'] ),
			'overlay'              => sanitize_key( $args['overlay'] ),
			'parent'               => $args['parent'], // Validated later, after check for 'current'
			'row_class'            => mai_sanitize_html_classes( $args['row_class'] ),
			'show'                 => mai_sanitize_keys( $args['show'] ),
			'status'               => array_filter( explode( ',', $args['status'] ) ),
			'tags'                 => array_filter( explode( ',', sanitize_text_field( $args['tags'] ) ) ),
			'tax_include_children' => filter_var( $args['tax_include_children'], FILTER_VALIDATE_BOOLEAN ),
			'tax_operator'         => $args['tax_operator'], // Validated later as one of a few values
			'tax_field'            => sanitize_key( $args['tax_field'] ),
			'taxonomy'             => sanitize_key( $args['taxonomy'] ),
			'terms'                => $args['terms'], // Validated later, after check for 'current'
			'title_wrap'           => sanitize_key( $args['title_wrap'] ),
			'class'                => mai_sanitize_html_classes( $args['class'] ),
			'id'                   => sanitize_html_class( $args['id'] ),
			'slider'               => filter_var( $args['slider'], FILTER_VALIDATE_BOOLEAN ),
			'arrows'               => filter_var( $args['arrows'], FILTER_VALIDATE_BOOLEAN ),
			'autoplay'             => filter_var( $args['autoplay'], FILTER_VALIDATE_BOOLEAN ),
			'center_mode'          => filter_var( $args['center_mode'], FILTER_VALIDATE_BOOLEAN ),
			'dots'                 => filter_var( $args['dots'], FILTER_VALIDATE_BOOLEAN ),
			'fade'                 => filter_var( $args['fade'], FILTER_VALIDATE_BOOLEAN ),
			'infinite'             => filter_var( $args['infinite'], FILTER_VALIDATE_BOOLEAN ),
			'slidestoscroll'       => absint( $args['slidestoscroll'] ),
			'speed'                => absint( $args['speed'] ),
		);

		$this->args = $args;

		return $this->render();

	}

	function render() {

		$html = '';

		// Get the content type.
		$this->content_type = $this->get_content_type( $this->args['content'] );

		// Bail if we don't have a valid content type.
		if ( empty( $this->content_type ) ) {
			return;
		}

		// Set attributes.
		$attributes = array(
			'class' => $this->add_classes( $this->args['class'] ), // TODO, does 'flex-grid' get added?
			'id'    => ! empty( $this->args['id'] ) ? $this->args['id'] : '',
		);

		// Start the main grid html.
		$html .= sprintf( '<div %s>', genesis_attr( 'flex-grid', $attributes, $this->args ) );

		switch ( $this->content_type ) {
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

	/**
	 * Get the content type.
	 */
	function get_content_type() {
		// If we already have a value.
		if ( ! empty( $this->args['content'] ) ) {
			return $this->args['content'];
		}
		// If types are all post types. get_post_type() gets all built in and custom post types.
		if ( array_intersect( $this->args['content'], get_post_types() ) == $this->args['content'] ) {
			return 'post'; // Means any post_type
		}
		// Get public taxonomies.
		$taxos = get_taxonomies( array(
			'public' => true,
		), 'names' );
		// If types are all taxonomies.
		if ( array_intersect( $this->args['content'], $taxos ) == $this->args['content'] ) {
			return 'term';
		}
		// Nada.
		return false;
	}

	function get_row_wrap_open() {

		// Row attributes.
		$attributes = array(
			'class' => 'row',
		);

		// FacetWP support.
		if ( $this->args['facetwp'] ) {
			$attributes['class'] = $this->add_classes( 'facetwp-template', $attributes['class'] );
		}

		// If slider.
		if ( $this->args['slider'] ) {

			// Enqueue Slick Carousel.
			wp_enqueue_script( 'mai-slick' );
			wp_enqueue_script( 'mai-slick-init' );

			// Slider wrapper class.
			$attributes['class'] = $this->add_classes( 'mai-slider', $attributes['class'] );

			// Slider HTML data attributes.
			$attributes = $this->add_slider_data_attributes( $attributes );

		}
		// Not on slider.
		else {

			// Add gutter.
			$attributes['class'] = $this->add_classes( sprintf( ' gutter-%s', $this->args['gutter'] ), $attributes['class'] );

			// Add row align classes.
			$attributes['class'] = $this->add_row_align_classes( $attributes['class'], $this->args );

		}

		// WooCommerce.
		if ( class_exists( 'WooCommerce' ) && in_array( 'product', $this->args['content'] ) ) {
			$attributes['class'] .= ' woocommerce';
		}

		// Custom row classes.
		$attributes['class'] = $this->add_classes( $this->args['row_class'], $attributes['class'] );

		// Inline styles.
		if ( $this->args['style'] ) {
			$attributes['style'] = $this->args['style'];
		}

		// Bring it home.
		return sprintf( '<div %s>', genesis_attr( 'flex-row', $attributes, $this->args ) );
	}

	function get_row_wrap_close( $atts ) {
		return '</div>';
	}

	// TODO: Entry class stuff, and everything else!

	/**
	 * Add align classes to the row.
	 *
	 * @param   string  $classes  The existing classes.
	 * @param   array   $args     The args from the shortcode or helper function.
	 * @param   string  $context  The shortcode context (grid or columns).
	 *
	 * @return  string  The modified classes
	 */
	function add_row_align_classes( $classes, $args, $context = 'grid' ) {
		/**
		 * "align" takes precendence over "align_cols" and "align_text".
		 * "align" forces the text to align along with the cols.
		 */
		if ( isset( $args['align'] ) && ! empty( $args['align'] ) ) {
			$classes = $this->add_align_classes( $classes, $args['align'] );
		} else {
			// Align columns.
			if ( $args['align_cols'] && ! empty( $args['align_cols'] ) ) {
				$classes = $this->add_align_classes( $classes, $args['align_cols'] );
			}
		}
		return $classes;
	}


	function add_slider_data_attributes( $attributes ) {
		$attributes['data-arrows']         = $this->args['arrows'] ? 'true' : 'false';
		$attributes['data-autoplay']       = $this->args['autoplay'] ? 'true' : 'false';
		$attributes['data-center']         = in_array( 'center', $this->args['align'] ) ? 'true' : 'false';
		$attributes['data-centermode']     = $this->args['center_mode'] ? 'true' : 'false';
		$attributes['data-dots']           = $this->args['dots'] ? 'true' : 'false';
		$attributes['data-fade']           = $this->args['fade'] ? 'true' : 'false';
		$attributes['data-infinite']       = $this->args['infinite'] ? 'true' : 'false';
		$attributes['data-middle']         = in_array( 'middle', $this->args['align'] ) ? 'true' : 'false';
		$attributes['data-slidestoscroll'] = $this->args['slidestoscroll'];
		$attributes['data-slidestoshow']   = $this->args['columns'];
		$attributes['data-speed']          = $this->args['speed'];
		$attributes['data-gutter']         = $this->args['gutter'];
		return $attributes;
	}

	/**
	 * Get default slidestoscroll.
	 * First check slidestoscroll, then columns, then fallback to default.
	 */
	function get_slidestoscroll_default( $args ) {
		$slidestoscroll = 3;
		if ( isset( $args['slidestoscroll'] ) ) {
			$slidestoscroll = $args['slidestoscroll'];
		} elseif ( isset( $args['columns'] ) ) {
			$slidestoscroll = $args['columns'];
		}
		return $slidestoscroll;
	}

}
