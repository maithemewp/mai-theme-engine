<?php

/**
 * Build a grid of content.
 *
 * @access  private
 */
class Mai_Section {

	private $args;

	private $content;

	private $has_content;

	private $has_wrap;

	private $has_overlay;

	private $has_inner;

	public function __construct( $args = array(), $content = null ) {

		$this->args    = $args;
		$this->content = $content;

		// Shortcode section atts.
		$this->args = shortcode_atts( array(
			'align'         => '',
			'bg'            => '',
			'class'         => '',
			'content_width' => '',
			'context'       => 'section',
			'height'        => 'md',
			'id'            => '',
			'image'         => '', // 'image=246' with an image ID from the media library to use a full width background image.
			'image_size'    => 'banner',
			'inner'         => '',
			'overlay'       => '',
			'text_size'     => '',
			'title'         => '',
			'title_wrap'    => 'h2',
			'wrapper'       => 'section',
			'wrap_class'    => '',
		), $this->args, 'section' );

		// Sanitized args.
		$this->args = array(
			'align'         => mai_sanitize_keys( $this->args['align'] ),   // left, center, right
			'bg'            => mai_sanitize_hex_color( $this->args['bg'] ), // 3 or 6 dig hex color with or without hash
			'class'         => mai_sanitize_html_classes( $this->args['class'] ),
			'content_width' => sanitize_key( $this->args['content_width'] ),
			'context'       => sanitize_html_class( $this->args['context'] ),
			'height'        => sanitize_key( $this->args['height'] ),
			'id'            => sanitize_html_class( $this->args['id'] ),
			'image'         => absint( $this->args['image'] ),
			'image_size'    => sanitize_key( $this->args['image_size'] ),
			'inner'         => sanitize_key( $this->args['inner'] ),
			'overlay'       => sanitize_key( $this->args['overlay'] ),
			'text_size'     => sanitize_key( $this->args['text_size'] ),
			'title'         => sanitize_text_field( $this->args['title'] ),
			'title_wrap'    => sanitize_key( $this->args['title_wrap'] ),
			'wrapper'       => sanitize_key( $this->args['wrapper'] ),
			'wrap_class'    => mai_sanitize_html_classes( $this->args['wrap_class'] ),
		);

	}

	/**
	 * Return the section HTML.
	 * On layouts with no sidebar it will be a full browser/window width section.
	 *
	 * @return  string|HTML
	 */
	function render() {

		// Bail if no content.
		if ( null === $this->content ) {
			return;
		}

		// Set some vars.
		$this->has_content = ! empty( $this->content );
		$this->has_wrap    = ! empty( $this->args['title'] ) || $this->has_content;
		$this->has_overlay = mai_is_valid_overlay( $this->args['overlay'] );
		$this->has_inner   = mai_is_valid_inner( $this->args['inner'] ) && ! empty( $this->content );

		return genesis_markup( array(
			'open'    => $this->get_section_open(),
			'close'   => $this->get_section_close(),
			'content' => $this->get_section_inside(),
			'context' => $this->args['context'],
			'echo'    => false,
		) );

	}

	/**
	 * Get opening HTML.
	 *
	 * @version  1.1.0
	 *
	 * @return   string|HTML
	 */
	function get_section_open() {

		// Set attributes.
		$attributes = array(
			'class' => mai_add_classes( $this->args['class'], 'section' ),
			'id'    => ! empty( $this->args['id'] ) ? $this->args['id'] : '',
		);

		// Align text.
		if ( ! empty( $this->args['align'] ) ) {
			$attributes['class'] = mai_add_align_text_classes( $attributes['class'], $this->args['align'] );
		}

		// Text size.
		if ( $this->args['text_size'] ) {
			$attributes['class'] = mai_add_text_size_classes( $attributes['class'], $this->args['text_size'] );
		}

		$dark_bg = false;

		// Content shade.
		if ( ! $this->has_inner ) {
			/**
			 * If bg color and no image,
			 * bg shade is based on bg color.
			 */
			if ( $this->args['bg'] && ! $this->args['image'] ) {
				$dark_bg = mai_is_dark_color( $this->args['bg'] );
			} elseif ( $this->args['image'] && ! $this->has_overlay ) {
				// For now, anytime we have an image it's considered dark.
				$dark_bg = true;
			} elseif ( $this->args['image'] && in_array( $this->args['overlay'], array( 'dark', 'gradient' ) ) ) {
				$dark_bg = true;
			}

			/**
			 * Add content shade class if we don't have inner.
			 * Inner will handle these classes if we have it.
			 */
			$attributes['class'] .= $dark_bg ? ' light-content' : '';
		}

		// Maybe add the inline background color.
		if ( $this->args['bg'] ) {

			// Add the background color.
			$attributes = mai_add_background_color_attributes( $attributes, $this->args['bg'] );
		}

		// If we have an image ID.
		if ( $this->args['image'] ) {

			// If using aspect ratio.
			$has_aspect_ratio = $this->has_content ? false : true;

			// Add the aspect ratio attributes.
			$attributes = mai_add_background_image_attributes( $attributes, $this->args['image'], $this->args['image_size'], $has_aspect_ratio );

			/**
			 * Add content shade class if we don't have inner.
			 * Inner will handle these classes if we have it.
			 */
			if ( ! ( $this->has_overlay && $this->has_inner ) ) {
				$attributes['class'] .= $dark_bg ? ' light-content' : '';
			}

		}

		// If we have an overlay.
		if ( $this->has_overlay ) {

			$light_content = false;

			// Add overlay classes.
			$attributes['class'] = mai_add_overlay_classes( $attributes['class'], $this->args['overlay'] );

		}

		// Build the opening markup.
		return sprintf( '<%s %s>', $this->args['wrapper'], genesis_attr( $this->args['context'], $attributes, $this->args ) );
	}

	/**
	 * Get closing HTML.
	 *
	 * @version  1.1.0
	 *
	 * @return   string|HTML
	 */
	function get_section_close() {
		return sprintf( '</%s>', $this->args['wrapper'] );
	}

	/**
	 * Get closing inside HTML.
	 *
	 * @version  1.1.0
	 *
	 * @return   string|HTML
	 */
	function get_section_inside() {
		$html = '';
		$html .= $this->get_section_wrap_open();
		$html .= $this->get_section_inner_open();
		$html .= $this->get_section_content();
		$html .= $this->get_section_inner_close();
		$html .= $this->get_section_wrap_close();
	}

	/**
	 * Get wrap opening HTML.
	 *
	 * @version  1.1.0
	 *
	 * @return   string|HTML
	 */
	function get_section_wrap_open() {

		$html = '';

		// Maybe build opening wrap.
		if ( $this->has_wrap ) {

			$attributes = array(
				'class' => 'wrap',
			);

			// Height.
			if ( $this->args['height'] ) {
				$attributes['class'] = mai_add_height_classes( $attributes['class'], $this->args['height'] );
			}

			// Content width.
			$attributes['class'] = mai_add_content_width_classes( $attributes['class'], $this->args['content_width'] );

			// Custom classes.
			$attributes['class'] = mai_add_classes( $this->args['wrap_class'], $attributes['class'] );

			$html = sprintf( '<div %s>', genesis_attr( 'section-wrap', $attributes, $this->args ) );
		}

		return $html;
	}

	/**
	 * Get inner opening HTML.
	 *
	 * @version  1.1.0
	 *
	 * @return   string|HTML
	 */
	function get_section_inner_open() {

		$html = '';

		if ( $this->has_inner ) {

			$attributes = array(
				'class' => 'inner',
			);

			$dark_bg = false;

			switch ( $this->args['inner'] ) {
				case 'light':
					$attributes['class'] .= ' inner-light';
				break;
				case 'dark':
					$attributes['class'] .= ' inner-dark';
					$dark_bg = true;
				break;
			}

			// Add content shade classes.
			$attributes['class'] .= $dark_bg ? ' light-content' : '';

			// Build the inner HTML.
			$html = sprintf( '<div %s>', genesis_attr( 'section-inner', $attributes, $this->args ) );

		}

		return $html;
	}

	/**
	 * Get content.
	 *
	 * @version  1.1.0
	 *
	 * @return   string|HTML
	 */
	function get_section_content() {
		$html = '';
		$html .= $this->get_section_title();
		$html .= mai_get_processed_content( $this->content );
		return $html;
	}

	/**
	 * Get title.
	 *
	 * @version  1.1.0
	 *
	 * @return   string|HTML
	 */
	function get_section_title() {
		$html = '';
		if ( $this->args['title'] ) {
			$html = sprintf( '<%s class="heading">%s</%s>', $this->args['title_wrap'], $this->args['title'], $this->args['title_wrap'] );
		}
		return $html;
	}

	/**
	 * Get inner closing HTML.
	 *
	 * @version  1.1.0
	 *
	 * @return   string|HTML
	 */
	function get_section_inner_close() {
		$html = '';
		if ( $this->has_inner ) {
			$html = '</div>';
		}
		return $html;
	}

	/**
	 * Get wrap closing HTML.
	 *
	 * @version  1.1.0
	 *
	 * @return   string|HTML
	 */
	function get_section_wrap_close() {
		$html = '';
		if ( $this->has_wrap ) {
			$html = '</div>';
		}
		return $html;
	}

}
