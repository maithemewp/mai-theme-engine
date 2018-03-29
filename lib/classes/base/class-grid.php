<?php

add_shortcode( 'grid_new', function( $atts ) {
	return new Mai_Grid( $atts );
});

class Mai_Grid {

	private $args;

	private $original_args;

	private $content_type;

	private $facetwp = false;

	function __contruct( $args ) {

		// All displayed items incase exclude_existing is true in any instance of grid.
		static $existing_post_ids = array();
		static $existing_term_ids = array();

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
			'exclude_existing'     => false,
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
			'terms'                => '',  // Comma-separated or 'current'
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
			'exclude_existing'     => filter_var( $args['exclude_existing'], FILTER_VALIDATE_BOOLEAN ),
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

	}

	function render() {

		// Get the content type.
		$this->content_type = $this->get_content_type( $this->args['content'] );

		// Bail if we don't have a valid content type.
		if ( empty( $this->content_type ) ) {
			return;
		}

		// Set attributes.
		$attributes = array(
			'class' => mai_add_classes( $this->args['class'] ), // TODO, does 'flex-grid' get added?
			'id'    => ! empty( $this->args['id'] ) ? $this->args['id'] : '',
		);

		// Start the main grid html.
		$open = sprintf( '<div %s>', genesis_attr( 'flex-grid', $attributes, $this->args ) );

		switch ( $this->content_type ) {
			case 'post':
				// $content = $this->get_posts();
				$content = new Mai_Grid_Posts( $this->args );
			break;
			case 'term':
				// $content = $this->get_terms( $atts, $original_args );
			break;
			// TODO: $this->get_users( $atts );
			default:
				$content = '';
			break;
		}

		$close = '</div>';

		if ( $content ) {
			return $open . $content . $close;
		}

		return '';

	}

	function get_grid_title() {
		// Bail if no title
		if ( empty( $this->args['grid_title'] ) ) {
			return;
		}
		$classes = 'heading';
		$classes = mai_add_classes( $this->args['grid_title_class'], $classes );
		return sprintf( '<%s class="%s">%s</%s>', $this->args['grid_title_wrap'], trim($classes), $this->args['grid_title'], $this->args['grid_title_wrap'] );
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
			$attributes['class'] = mai_add_classes( 'facetwp-template', $attributes['class'] );
		}

		// If slider.
		if ( $this->args['slider'] ) {

			// Enqueue Slick Carousel.
			wp_enqueue_script( 'mai-slick' );
			wp_enqueue_script( 'mai-slick-init' );

			// Slider wrapper class.
			$attributes['class'] = mai_add_classes( 'mai-slider', $attributes['class'] );

			// Slider HTML data attributes.
			$attributes = $this->add_slider_data_attributes( $attributes );

		}
		// Not on slider.
		else {

			// Add gutter.
			$attributes['class'] = mai_add_classes( sprintf( ' gutter-%s', $this->args['gutter'] ), $attributes['class'] );

			// Add row align classes.
			$attributes['class'] = mai_add_align_classes( $attributes['class'] );

		}

		// WooCommerce.
		if ( class_exists( 'WooCommerce' ) && in_array( 'product', $this->args['content'] ) ) {
			$attributes['class'] .= ' woocommerce';
		}

		// Custom row classes.
		$attributes['class'] = mai_add_classes( $this->args['row_class'], $attributes['class'] );

		// Inline styles.
		if ( $this->args['style'] ) {
			$attributes['style'] = $this->args['style'];
		}

		// Bring it home.
		return sprintf( '<div %s>', genesis_attr( 'flex-row', $attributes, $this->args ) );
	}

	function get_row_wrap_close() {
		return '</div>';
	}

	function get_entry_wrap_open( $object, $has_bg_image ) {

		$attributes = array();

		// Set the entry classes.
		$attributes['class'] = mai_add_classes( $this->get_entry_classes() );

		// Add the align classes.
		$attributes['class'] = mai_add_classes( $this->get_entry_align_classes() );

		$light_content = false;
		$valid_overlay = mai_is_valid_overlay( $this->args['overlay'] );

		if ( $this->is_bg_image() ) {

			// Get the object ID.
			$object_id = $this->get_object_id( $object );

			if ( $object_id ) {

				// Add background image with aspect ratio attributes.
				$attributes = mai_add_bg_image_attributes( $attributes, $this->get_image_id( $this->args, $object_id ), $this->args['image_size'] );

				if ( $has_bg_image ) {

					$light_content = true;

					// Set dark overlay if we don't have one.
					$this->args['overlay'] = ! $valid_overlay ? 'dark' : $this->args['overlay'];
				}
			}

			if ( $this->has_bg_link( $atts ) ) {

				// Add has-bg-link class.
				$attributes['class'] .= ' has-bg-link';
			}
		}

		if ( $valid_overlay ) {

			// If we have a dark overlay, content is light.
			if ( 'dark' === $this->args['overlay'] ) {
				$light_content = true;
			}

			// Add overlay classes.
			$attributes['class'] .= mai_add_classes( mai_get_overlay_classes( $this->args['overlay'] ) );
		}

		// Shade class
		$attributes['class'] .= $light_content ? ' light-content' : '';

		/**
		 * Main entry col wrap.
		 * If we use genesis_attr( 'entry' ) then it resets the classes.
		 */
		return sprintf( '<div %s>', genesis_attr( 'flex-entry', $attributes, $this->args ) );
	}

	function get_entry_wrap_close() {
		return '</div>';
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
	 *
	 * Can't use $this->args cause not set yet. This is for defaults.
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

	/**
	 * Get the flex entry classes.
	 *
	 * @return  string  The HTML ready classes.
	 */
	function get_entry_classes() {

		// We need classes to be an array so we can use them in get_post_class().
		$classes = array( 'flex-entry', 'entry' );

		// If image is not aligned.
		if ( $this->args['image_align'] ) {
			$classes[] = 'image-' . $this->args['image_align'];
		}

		// Add bottom margin classes.
		if ( ! empty( $this->args['bottom'] ) ) {
			$bottom = mai_get_classes_by_columns( $this->args['columns'] );
		} else {
			// Add slide class.
			$classes[] = 'mai-slide';
		}

		// If dealing with a post object.
		if ( 'post' === $this->args['content_type'] ) {

			/**
			 * Merge our new classes with the default WP generated classes.
			 * Also removes potential duplicate flex-entry since we need it even if slider.
			 */
			$classes = get_post_class( $classes, get_the_ID() );

		}

		// Remove duplicates and sanitize.
		$classes = array_map( 'sanitize_html_class', array_unique( $classes ), get_the_ID() );

		// Turn array into a string of space separated classes
		return implode( ' ', $classes );
	}

	/**
	 * Get the flex entry align classes.
	 * We can't use get_align_classes() method since this may add 'column'
	 * which reverses the left/top center/middle right/bottom directional classes.
	 *
	 * @return  string  The HTML ready classes.
	 */
	function get_entry_align_classes() {

		/**
		 * "align" takes precendence over "align_cols" and "align_text".
		 * "align" forces the text to align along with the cols.
		 */
		if ( ! empty( $this->args['align'] ) ) {

			// If no image align, this can be a column so reverse classes.
			if ( empty( $this->args['image_align'] ) ) {

				$classes .= ' column';

				// Left
				if ( in_array( 'left', $this->args['align'] ) ) {
					$classes .= ' top-xs text-xs-left';
				}

				// Center
				if ( in_array( 'center', $this->args['align'] ) ) {
					$classes .= ' middle-xs text-xs-center';
				}

				// Right
				if ( in_array( 'right', $this->args['align'] ) ) {
					$classes .= ' bottom-xs text-xs-right';
				}

				// Top
				if ( in_array( 'top', $this->args['align'] ) ) {
					$classes .= ' start-xs';
				}

				// Middle
				if ( in_array( 'middle', $this->args['align'] ) ) {
					$classes .= ' center-xs';
				}

				// Bottom
				if ( in_array( 'bottom', $this->args['align'] ) ) {
					$classes .= ' end-xs';
				}

			} else {

				// Left
				if ( in_array( 'left', $this->args['align'] ) ) {
					$classes .= ' start-xs text-xs-left';
				}

				// Center
				if ( in_array( 'center', $this->args['align'] ) ) {
					$classes .= ' center-xs text-xs-center';
				}

				// Right
				if ( in_array( 'right', $this->args['align'] ) ) {
					$classes .= ' end-xs text-xs-right';
				}

				// Top
				if ( in_array( 'top', $this->args['align'] ) ) {
					$classes .= ' top-xs';
				}

				// Middle
				if ( in_array( 'middle', $this->args['align'] ) ) {
					$classes .= ' middle-xs';
				}

				// Bottom
				if ( in_array( 'bottom', $this->args['align'] ) ) {
					$classes .= ' bottom-xs';
				}

			}

		} else {

			// Align text
			if ( ! empty( $this->args['align_text'] ) ) {

				// Column. Save as variable first cause php 5.4 broke, and not sure I care to support that but WTH.
				$vertical_align = array_intersect( array( 'top', 'middle', 'bottom' ), $this->args['align_text'] );
				if ( ! empty( $vertical_align ) ) {
					$classes .= ' column';
				}

				// Left
				if ( in_array( 'left', $this->args['align_text']) ) {
					$classes .= ' text-xs-left';
				}

				// Center
				if ( in_array( 'center', $this->args['align_text'] ) ) {
					$classes .= ' text-xs-center';
				}

				// Right
				if ( in_array( 'right', $this->args['align_text'] ) ) {
					$classes .= ' text-xs-right';
				}

				// Top
				if ( in_array( 'top', $this->args['align_text'] ) ) {
					$classes .= ' start-xs';
				}

				// Middle
				if ( in_array( 'middle', $this->args['align_text'] ) ) {
					$classes .= ' center-xs';
				}

				// Bottom
				if ( in_array( 'bottom', $this->args['align_text'] ) ) {
					$classes .= ' end-xs';
				}

			}

		}

		return $classes;
	}

	function get_object_id( $object ) {
		switch ( $this->args['content_type'] ) {
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
				$id = false;
		}
		return $id;
	}

	/**
	 * Get a link for a given entry.
	 *
	 * @param   object|int  $object_or_id  The object or object ID, either from $post, $term, or $user.
	 *
	 * @return  int         The image ID.
	 */
	function get_entry_link( $object_or_id ) {
		switch ( $this->args['content_type'] ) {
			case 'post':
				$link = get_permalink( $object_or_id );
			break;
			case 'term':
				$link = get_term_link( $object_or_id );
			break;
			default:
				$link = '';
		}
		return $link;
	}

	/**
	 * Get the add to cart link with screen reader text.
	 *
	 * @return  string|HTML
	 */
	function get_add_to_cart_link() {
		$link = '';
		if ( class_exists( 'WooCommerce' ) ) {
			ob_start();
			woocommerce_template_loop_add_to_cart();
			$link = ob_get_clean();
		}
		return $link ? sprintf( '<p class="more-link-wrap">%s</p>', $link ) : '';
	}

	/**
	 * Get a type of content main image ID.
	 * Needs to be used in the loop, so it can get the correct content type ID.
	 *
	 * @param   int   $object_id   The object ID, either $post_id, $term_id, or $user_id.
	 *
	 * @return  int  The image ID.
	 */
	function get_image_id( $object_id ) {
		switch ( $this->args['content_type'] ) {
			case 'post':
				$image_id = get_post_thumbnail_id( $object_id );
			break;
			case 'term':
				$key      = ( class_exists( 'WooCommerce' ) && ( 'product_cat' == $this->args['taxonomy'] ) ) ? 'thumbnail_id' : 'banner_id';
				$image_id = get_term_meta( $object_id, $key, true );
			break;
			case 'user':
				$image_id = get_user_meta( $object_id, 'banner_id', true );
			break;
			default:
				$image_id = '';
		}
		return $image_id;
	}

	/**
	 * Whether the main entry element should be a link or not.
	 *
	 * @return  bool
	 */
	function has_bg_link() {
		if ( $this->is_bg_image() && $this->args['link'] ) {
			return true;
		}
		return false;
	}

	/**
	 * If the grid is set to show image as a background.
	 *
	 * @return  bool
	 */
	function is_bg_image() {
		if ( 'bg' === $this->args['image_location'] ) {
			return true;
		}
		return false;
	}

	function is_entry_header_image() {
		switch ( $this->args['image_location'] ) {
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

	function get_image_html( $image_id, $url, $att_title ) {
		$image      = wp_get_attachment_image( $image_id, $this->args['image_size'], false, array( 'class' => 'wp-post-image' ) );
		$attributes = array();
		// Add the default class and add location as a class to the image link.
		$attributes['class'] = 'entry-image-link';
		if ( $this->args['image_location'] ) {
			$attributes['class'] .= sprintf( ' entry-image-%s', str_replace( '_', '-', $this->args['image_location'] ) );
		}
		if ( $this->args['image_align'] ) {
			switch ( $this->args['image_align'] ) {
				case 'left':
					$attributes['class'] .= ' alignleft';
				break;
				case 'center':
					$attributes['class'] .= ' aligncenter';
				break;
				case 'right':
					$attributes['class'] .= ' alignright';
				break;
			}
		}
		$attributes['title'] = $att_title;
		if ( $this->args['link'] ) {
			$attributes['href'] = $url;
			$image_wrap = 'a';
		} else {
			$image_wrap = 'span';
		}
		return sprintf( '<%s %s>%s</%s>', $image_wrap, genesis_attr( 'grid-entry-image-link', $attributes ), $image, $image_wrap );
	}

	/**
	 * Get the number of items to show.
	 * If all, return the appropriate value depending on content type.
	 *
	 * @return  int  The number of items
	 */
	function get_number() {
		if ( 'all' === $this->args['number'] ) {
			switch ( $this->args['content_type'] ) {
				case 'post':
					$number = -1; // wp_query uses -1 for all.
				break;
				case 'term':
					$number = 0;  // get_terms() uses 0 for all.
				break;
				default:
					$number = 100; // Just to be safe, cause we may add user later.
			}
		} else {
			$number = $this->args['number'];
		}
		return intval( $number );
	}

}
