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
		$this->content = $content;

		// Parse defaults and args.
		$this->args = shortcode_atts( array(
			'align'      => '', // "top, left" Comma separted. overrides align_cols and align_text for most times one setting makes sense
			'align_text' => '', // "center" Comma separted
			'bg'         => '', // 3 or 6 dig hex color with or without hash
			'bottom'     => '',
			'class'      => '',
			'id'         => '',
			'image'      => '', // image id or 'featured' if link is a post id
			'image_size' => 'one-third',
			'overlay'    => '', // 'dark', 'light', 'gradient', or none/false to force disable
			'link'       => '',
			'style'      => '', // HTML inline style
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
			'bottom'     => ! empty( $this->args['bottom'] ) ? absint( $this->args['bottom'] ): '',
			'class'      => mai_sanitize_html_classes( $this->args['class'] ),
			'id'         => sanitize_html_class( $this->args['id'] ),
			'image'      => sanitize_key( $this->args['image'] ),
			'image_size' => sanitize_key( $this->args['image_size'] ),
			'overlay'    => sanitize_key( $this->args['overlay'] ),
			'link'       => sanitize_text_field( $this->args['link'] ), // URL or post ID
			'style'      => sanitize_text_field( $this->args['style'] ),
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

	function get_col() {

		$attributes = array(
			'class' => $this->get_classes(),
			'id'    => ! empty( $this->args['id'] ) ? $this->args['id'] : '',
		);

		// Custom classes.
		$attributes['class'] = mai_add_classes( $this->args['class'], $attributes['class'] );

		// Align.
		if ( ! empty( $this->args['align'] ) ) {
			$attributes['class'] = mai_add_align_classes_column( $attributes['class'], $this->args['align'] );
		} elseif ( ! empty( $this->args['align_text'] ) ) {
			// Column. Save as variable first cause php 5.4 broke, and not sure I care to support that but WTH.
			$vertical_align = array_intersect( array( 'top', 'middle', 'bottom' ), $this->args['align_text'] );
			if ( ! empty( $vertical_align ) ) {
				$attributes['class'] .= ' column';
				$attributes['class'] = mai_add_align_text_classes_column( $attributes['class'], $this->args['align_text'] );
			}
			$attributes['class'] = mai_add_align_text_classes( $attributes['class'], $this->args['align_text'] );
		}

		// URL.
		$bg_link = $bg_link_title = '';
		if ( ! empty( $this->args['link'] ) ) {
			if ( is_numeric( $this->args['link'] ) ) {
				$bg_link_url   = get_permalink( (int) $this->args['link'] );
				$bg_link_title = get_the_title( (int) $this->args['link'] );
			} else {
				$bg_link_url   = esc_url( $this->args['link'] );
			}
			$bg_link              = mai_get_bg_image_link( $bg_link_url, $bg_link_title );
			$attributes['class'] .= ' has-bg-link';
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

		// If we have an image ID
		if ( $this->args['image'] ) {

			// If we have content.
			if ( $this->content ) {
				// Set dark overlay if we don't have one.
				$this->args['overlay'] = ! $this->args['overlay'] ? 'dark' : $this->args['overlay'];
				$light_content   = true;
			}

			// If showing featured image and link is a post ID.
			if ( ( 'featured' == $this->args['image'] ) && is_numeric( $this->args['link'] ) ) {
				$image_id = get_post_thumbnail_id( absint( $this->args['link'] ) );
			} else {
				$image_id = absint( $this->args['image'] );
			}

			// Add the aspect ratio attributes.
			$attributes = mai_add_background_image_attributes( $attributes, $image_id, $this->args['image_size'] );
		}

		// If we have a valid overlay.
		if ( mai_is_valid_overlay( $this->args['overlay'] ) ) {
			$attributes['class'] .= ' overlay';
			// Only add overlay classes if we have a valid overlay type
			switch ( $this->args['overlay'] ) {
				case 'gradient':
					$attributes['class'] .= ' overlay-gradient';
					$light_content = false;
				break;
				case 'light':
					$attributes['class'] .= ' overlay-light';
					$light_content = false;
				break;
				case 'dark':
					$attributes['class'] .= ' overlay-dark';
					$light_content = true;
				break;
			}
		}

		// Add content shade class.
		$attributes['class'] .= $light_content ? ' light-content' : '';

		// Add bottom margin classes.
		if ( mai_is_valid_bottom( $this->args['bottom'] ) ) {
			$attributes['class'] = mai_add_class( mai_get_bottom_class( $this->args['bottom'] ), $attributes['class'] );
		}

		// Maybe add inline styles.
		$attributes = mai_add_inline_styles( $attributes, $this->args['style'] );

		// Trim because testing returned string of nbsp.
		$this->content = trim( $this->content );

		/**
		 * Return the content with col wrap.
		 * With flex-col attr so devs can filter elsewhere.
		 */
		return sprintf( '<div %s>%s%s</div>', genesis_attr( 'flex-col', $attributes, $this->args ), mai_get_processed_content( $this->content ), $bg_link );
	}

	function get_classes() {
		$classes = mai_add_classes( mai_get_col_classes_by_breaks( $this->args, $this->size ), 'flex-entry col' );
		return $classes;
	}

}
