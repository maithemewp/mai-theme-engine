<?php

/**
 * Build column.
 *
 * @access  private
 */
class Mai_Col {

	private $size;

	private $args;

	private $content;

	public function __construct( $size = '', $args = array(), $content = null ) {

		$this->size    = $size;
		$this->args    = $args;
		$this->content = trim( $content );

		// Parse defaults and args.
		$this->args = shortcode_atts( array(
			'align'      => '', // "top, left" Comma separted. overrides align_cols and align_text for most times one setting makes sense
			'align_text' => '', // "center, middle" Comma separted
			'bg'         => '', // 3 or 6 dig hex color with or without hash
			'bottom'     => '', // Bottom margin. none, xxxs, xxs, xs, sm, md, lg, xl, xxl
			'class'      => '',
			'id'         => '',
			'image'      => '', // image id or 'featured' if link is a post id
			'image_size' => 'one-third',
			'overlay'    => '', // 'dark', 'light', 'gradient', or none/false to force disable
			'link'       => '',
			'style'      => '', // HTML inline style
			'top'        => '', // Top margin. none, xxxs, xxs, xs, sm, md, lg, xl, xxl
			'xs'         => '12',
			'sm'         => '',
			'md'         => '',
			'lg'         => '',
			'xl'         => '',
		), $this->args, 'col' );

		// Sanitize args.
		$this->args = array(
			'align'      => mai_sanitize_keys( $this->args['align'] ),
			'align_text' => mai_sanitize_keys( $this->args['align_text'] ),
			'bg'         => mai_sanitize_hex_color( $this->args['bg'] ),
			'bottom'     => sanitize_key( $this->args['bottom'] ),
			'class'      => mai_sanitize_html_classes( $this->args['class'] ),
			'id'         => sanitize_html_class( $this->args['id'] ),
			'image'      => sanitize_key( $this->args['image'] ),
			'image_size' => sanitize_key( $this->args['image_size'] ),
			'overlay'    => sanitize_key( $this->args['overlay'] ),
			'link'       => sanitize_text_field( $this->args['link'] ), // URL or post ID
			'style'      => sanitize_text_field( $this->args['style'] ),
			'top'        => sanitize_key( $this->args['top'] ),
			'xs'         => sanitize_key( $this->args['xs'] ),
			'sm'         => sanitize_key( $this->args['sm'] ),
			'md'         => sanitize_key( $this->args['md'] ),
			'lg'         => sanitize_key( $this->args['lg'] ),
			'xl'         => sanitize_key( $this->args['xl'] ),
		);
	}

	/**
	 * Return the column HTML.
	 *
	 * @return  string|HTML
	 */
	function render() {

		// Bail if no background image and no content.
		if ( ! $this->args['image'] && null === $this->content ) {
			return;
		}

		return $this->get_col();
	}

	/**
	 * Get the col with markup and content.
	 *
	 * @return  string|HTML
	 */
	function get_col() {

		// Trim because testing returned string of nbsp.
		$this->content = mai_get_processed_content( trim( $this->content ) );

		$image = $overlay = '';

		$attributes = array(
			'class' => $this->get_classes(),
			'id'    => ! empty( $this->args['id'] ) ? $this->args['id'] : '',
		);

		// Custom classes.
		$attributes['class'] = mai_add_classes( $this->args['class'], $attributes['class'] );

		// Add the align classes.
		$attributes['class'] = mai_add_entry_align_classes( $attributes['class'], $this->args, $this->get_direction() );

		// URL.
		$bg_link = $bg_link_title = '';
		if ( ! empty( $this->args['link'] ) ) {
			if ( is_numeric( $this->args['link'] ) ) {
				$bg_link_url   = get_permalink( (int) $this->args['link'] );
				$bg_link_title = get_the_title( (int) $this->args['link'] );
			} else {
				$bg_link_url   = esc_url( $this->args['link'] );
			}
			$bg_link             = mai_get_bg_image_link( $bg_link_url, $bg_link_title );
			$attributes['class'] = mai_add_classes( 'has-bg-link', $attributes['class'] );
		}

		$light_content = false;

		// Maybe add the inline background color.
		if ( $this->args['bg'] ) {

			// Add the background color.
			$attributes = mai_add_background_color_attributes( $attributes, $this->args['bg'] );

			if ( mai_is_dark_color( $this->args['bg'] ) ) {
				$light_content = true;
			}
		}

		// If we have an image ID.
		if ( $this->args['image'] ) {

			global $_wp_additional_image_sizes;

			// If we have content.
			if ( $this->content ) {
				// Set dark overlay if we don't have one.
				$this->args['overlay'] = ! $this->args['overlay'] ? 'dark' : $this->args['overlay'];
				$light_content         = true;
			}

			// If showing featured image and link is a post ID.
			if ( ( 'featured' == $this->args['image'] ) && is_numeric( $this->args['link'] ) ) {
				$image_id = get_post_thumbnail_id( absint( $this->args['link'] ) );
			} else {
				$image_id = absint( $this->args['image'] );
			}

			// Do the image.
			$image_html = wp_get_attachment_image( $image_id, $this->args['image_size'], false, array( 'class' => 'bg-image' ) );
			if ( $image_html ) {
				$attributes['class'] .= ' has-bg-image';
				$image = wp_image_add_srcset_and_sizes( $image_html, wp_get_attachment_metadata( $image_id ), $image_id );
			}

			// If image size is in the global (it should be).
			if ( isset( $_wp_additional_image_sizes[ $this->args['image_size'] ] ) ) {
				$registered_image = $_wp_additional_image_sizes[ $this->args['image_size'] ];
				$width  = $registered_image['width'];
				$height = $registered_image['height'];
			}
			// Otherwise use the actual image dimensions.
			elseif ( $image ) {
				$width  = $image[1];
				$height = $image[2];
			}
			// Fallback.
			else {
				$width  = 4;
				$height = 3;
			}

			$attributes = mai_add_aspect_ratio_attributes( $attributes, $width, $height );
		}

		// If we have a valid overlay.
		if ( mai_is_valid_overlay( $this->args['overlay'] ) ) {

			// If we have a dark overlay, content is light.
			if ( 'dark' === $this->args['overlay'] ) {
				$light_content = true;
			}

			// Build the overlay.
			$overlay = mai_get_overlay_html( $this->args['overlay'] );

			// Add overlay classes.
			$attributes['class'] .= ' has-overlay';
		}

		// Shade class
		$attributes['class'] .= $light_content ? ' light-content' : '';

		// Add top margin classes.
		if ( mai_is_valid_top( $this->args['top'] ) ) {
			$attributes['class'] = mai_add_classes( mai_get_top_class( $this->args['top'] ), $attributes['class'] );
		}

		// Add bottom margin classes.
		if ( mai_is_valid_bottom( $this->args['bottom'] ) ) {
			$attributes['class'] = mai_add_classes( mai_get_bottom_class( $this->args['bottom'] ), $attributes['class'] );
		}

		// Maybe add inline styles.
		$attributes = mai_add_inline_styles( $attributes, $this->args['style'] );

		/**
		 * Return the content with col wrap.
		 * With flex-col attr so devs can filter elsewhere.
		 */
		return sprintf( '<div %s>%s%s%s%s</div>',
			genesis_attr( 'flex-col', $attributes, $this->args ),
			$image,
			$overlay,
			$this->content,
			$bg_link
		);
	}

	/**
	 * Get the col classes.
	 *
	 * @return  string  HTML ready classes.
	 */
	function get_classes() {
		$classes = mai_add_classes( mai_get_col_classes_by_breaks( $this->args, $this->size ), 'flex-entry col' );
		return $classes;
	}

	/**
	 * Get the flex direction.
	 * Used by the align functions.
	 *
	 * @return  string  'columns' or 'row'.
	 */
	function get_direction() {
		if ( $this->is_vertically_aligned() ) {
			return 'column';
		}
		return 'row';
	}

	/**
	 * Check if column is vertically aligned.
	 * True if we have a bg image, or text is vertically aligned.
	 *
	 * @return  bool
	 */
	function is_vertically_aligned() {
		return $this->args['image'] || array_intersect( array( 'top', 'middle', 'bottom' ), $this->args['align_text'] );
	}

}
