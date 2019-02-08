<?php

/**
 * Build a grid of content.
 *
 * @access  private
 */
class Mai_Section {

	private $args;
	private $content;
	private $full_width_image;
	private $has_content;
	private $has_wrap;
	private $has_overlay;
	private $has_inner;


	public function __construct( $args = array(), $content = null ) {

		// Save original args in a variable for filtering later.
		$this->args    = $this->original_args = $args;
		$this->content = $content;

		// Shortcode section atts.
		$this->args = shortcode_atts( array(
			'align'         => '',
			'align_content' => 'center',
			'bg'            => '',
			'class'         => '',
			'content_width' => '',
			'context'       => 'section',
			'height'        => 'md',
			'id'            => '',
			'image'         => '',
			'image_size'    => '',
			'inner'         => '',
			'overlay'       => '',
			'style'         => '',
			'text_size'     => '',
			'title'         => '',
			'title_wrap'    => 'h2',
			'wrapper'       => 'section',
			'wrap_class'    => '',
		), $this->args, 'section' );

		// Set image size.
		if ( $this->args['image'] && ! $this->args['image_size'] ) {
			$this->args['image_size'] = $this->get_image_size();
		}

		// Sanitized args. Allows new items added via shortcode_atts_ filter to still pass.
		$this->args = wp_parse_args( array(
			'align'         => mai_sanitize_keys( $this->args['align'] ), // array with left, center, right, mostly for text-align (array for back compat)
			'align_content' => sanitize_key( $this->args['align_content'] ), // left, lefttop, leftbottom, center, centertop, centerbottom, right, righttop, rightbottom
			'bg'            => mai_sanitize_hex_color( $this->args['bg'] ), // 3 or 6 dig hex color with or without hash
			'class'         => mai_sanitize_html_classes( $this->args['class'] ),
			'content_width' => sanitize_key( $this->args['content_width'] ),
			'context'       => sanitize_key( $this->args['context'] ),
			'height'        => sanitize_key( $this->args['height'] ),
			'id'            => sanitize_html_class( $this->args['id'] ),
			'image'         => absint( $this->args['image'] ), // 'image=246' with an image ID from the media library to use a full width background image.
			'image_size'    => sanitize_key( $this->args['image_size'] ),
			'inner'         => sanitize_key( $this->args['inner'] ),
			'overlay'       => sanitize_key( $this->args['overlay'] ),
			'style'         => sanitize_text_field( $this->args['style'] ), // HTML inline style
			'text_size'     => sanitize_key( $this->args['text_size'] ),
			'title'         => sanitize_text_field( $this->args['title'] ),
			'title_wrap'    => sanitize_key( $this->args['title_wrap'] ),
			'wrapper'       => sanitize_key( $this->args['wrapper'] ),
			'wrap_class'    => mai_sanitize_html_classes( $this->args['wrap_class'] ),
		), $this->args );

		/**
		 * Section args filter.
		 *
		 * @since   1.3.0
		 *
		 * @param   array  $args           The current grid args.
		 * @param   array  $original_args  The original grid args.
		 *
		 * @return  array  The args.
		 */
		$this->args = apply_filters( 'mai_section_args', $this->args, $this->original_args );
	}

	/**
	 * Get the default image size.
	 *
	 * @return  string
	 */
	function get_image_size() {

		$size = 'banner';

		if ( in_array( 'site_container', (array) genesis_get_option( 'boxed_elements' ) ) ) {
			$size = 'full-width';
		}
		// Set the image size to larger size if height is tall.
		elseif ( in_array( $this->args['height'], array( 'lg', 'xl' ) ) ) {
			$size = 'section';
		}

		return $size;
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
		$this->has_content      = ! empty( $this->content );
		$this->full_width_image = false !== strpos( $this->args['class'], 'full-width-image' );
		$this->has_wrap         = ! $this->full_width_image;
		$this->has_overlay      = mai_is_valid_overlay( $this->args['overlay'] );
		$this->has_inner        = mai_is_valid_inner( $this->args['inner'] ) && ! empty( $this->content );

		return genesis_markup( array(
			'open'    => $this->get_section_open(),
			'close'   => $this->get_section_close(),
			'content' => $this->get_section_inside(),
			'context' => $this->args['context'],
			'params'  => $this->args,
			'echo'    => false,
		) );
	}

	/**
	 * Get opening HTML.
	 *
	 * @since   1.1.0
	 *
	 * @return  string|HTML
	 */
	function get_section_open() {

		$inner_html = '';

		// Set attributes.
		$attributes = array(
			'class' => mai_add_classes( $this->args['class'], 'section' ),
			'id'    => ! empty( $this->args['id'] ) ? $this->args['id'] : '',
		);

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

			// Build img to be used as background via CSS.
			add_filter( 'wp_calculate_image_srcset', array( $this, 'calculate_srcset' ), 10, 5 );
			$image = wp_get_attachment_image( $this->args['image'], $this->args['image_size'], false, array( 'class' => 'bg-image' ) );
			$image = wp_image_add_srcset_and_sizes( $image, wp_get_attachment_metadata( $this->args['image'] ), $this->args['image'] );
			remove_filter( 'wp_calculate_image_srcset', array( $this, 'calculate_srcset' ), 10, 5 );

			if ( $image ) {
				$inner_html .= $image;
				// Add has-background-image class.
				$attributes['class'] = mai_add_classes( 'has-bg-image', $attributes['class'] );
			}

			/**
			 * Add content shade class if we don't have inner.
			 * Inner will handle these classes if we have it.
			 */
			if ( ! ( $this->has_overlay && $this->has_inner ) ) {
				$attributes['class'] .= $dark_bg ? ' light-content' : '';
			}

		}

		// Maybe add inline styles.
		$attributes = mai_add_inline_styles( $attributes, $this->args['style'] );

		// If we have an overlay.
		if ( $this->has_overlay ) {

			$light_content = false;

			// Add has-overlay class to the section.
			$attributes['class'] = mai_add_classes( 'has-overlay', $attributes['class'] );

			// Get overlay.
			$inner_html .= mai_get_overlay_html( $this->args['overlay'] );
		}

		// Build the opening markup.
		return sprintf( '<%s %s>%s',
			$this->args['wrapper'],
			genesis_attr( $this->args['context'], $attributes, $this->args ),
			$inner_html
		);
	}

	/**
	 * Get closing HTML.
	 *
	 * @since   1.1.0
	 *
	 * @return  string|HTML
	 */
	function get_section_close() {
		return sprintf( '</%s>', $this->args['wrapper'] );
	}

	/**
	 * Get closing inside HTML.
	 *
	 * @since   1.1.0
	 *
	 * @return  string|HTML
	 */
	function get_section_inside() {
		$html = '';
		$html .= $this->get_section_wrap_open();
		$html .= $this->get_section_content_open();
		$html .= $this->get_section_inner_open();
		$html .= $this->get_section_content();
		$html .= $this->get_section_inner_close();
		$html .= $this->get_section_content_close();
		$html .= $this->get_section_wrap_close();
		return $html;
	}

	/**
	 * Get wrap opening HTML.
	 *
	 * @since   1.1.0
	 *
	 * @return  string|HTML
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
				$attributes['class'] = mai_add_content_height_classes( $attributes['class'], $this->args['height'] );
			}

			// If full width content.
			if ( 'full' === $this->args['content_width'] ) {
				$attributes['class'] .= ' has-width-full';
			}

			// Align Content.
			switch ( $this->args['align_content'] ) {
				case 'left':
					$attributes['class'] .= ' start-xs';
				break;
				case 'lefttop':
					$attributes['class'] .= ' top start-xs';
				break;
				case 'leftbottom':
					$attributes['class'] .= ' bottom start-xs';
				break;
				case 'center':
					$attributes['class'] .= ' center-xs';
				break;
				case 'centertop':
					$attributes['class'] .= ' top center-xs';
				break;
				case 'centerbottom':
					$attributes['class'] .= ' bottom center-xs';
				break;
				case 'right':
					$attributes['class'] .= ' end-xs';
				break;
				case 'righttop':
					$attributes['class'] .= ' top end-xs';
				break;
				case 'rightbottom':
					$attributes['class'] .= ' bottom end-xs';
				break;
				default:
					$attributes['class'] .= ' center-xs';
			}

			// Align text.
			if ( $this->args['align'] ) {
				$attributes['class'] = mai_add_align_text_classes( $attributes['class'], $this->args['align'] );
			}

			// Text size.
			if ( $this->args['text_size'] ) {
				$attributes['class'] = mai_add_text_size_classes( $attributes['class'], $this->args['text_size'] );
			}

			// Custom classes.
			$attributes['class'] = mai_add_classes( $this->args['wrap_class'], $attributes['class'] );

			$html = sprintf( '<div %s>', genesis_attr( 'section-wrap', $attributes, $this->args ) );
		}

		return $html;
	}

	/**
	 * Get section-content opening HTML.
	 *
	 * @since   1.3.0
	 *
	 * @return  string|HTML
	 */
	function get_section_content_open() {

		$html = '';

		// Maybe build section content wrap.
		if ( $this->has_wrap ) {

			$attributes = array(
				'class' => 'section-content',
			);

			// If we have content. We may have hidden title in banner.
			if ( $this->has_content ) {

				// Add extra content width.
				$attributes['class'] = mai_add_content_width_classes( $attributes['class'], $this->args['content_width'] );
			}

			$html = sprintf( '<div %s>', genesis_attr( 'section-content', $attributes, $this->args ) );
		}

		return $html;
	}

	/**
	 * Get inner opening HTML.
	 *
	 * @since   1.1.0
	 *
	 * @return  string|HTML
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
	 * Get title.
	 *
	 * @since   1.1.0
	 *
	 * @return  string|HTML
	 */
	function get_section_title() {
		$html = '';
		if ( $this->args['title'] ) {
			$html = sprintf( '<%s class="heading">%s</%s>', $this->args['title_wrap'], $this->args['title'], $this->args['title_wrap'] );
		}
		return $html;
	}

	/**
	 * Get content.
	 *
	 * @since   1.1.0
	 *
	 * @return  string|HTML
	 */
	function get_section_content() {
		$html = '';
		$html .= $this->get_section_title();
		if ( $this->full_width_image ) {
			$html .= wp_kses_post( wp_make_content_images_responsive( trim( $this->content ) ) );
		} else {
			$html .= mai_get_processed_content( $this->content );
		}
		return $html;
	}

	/**
	 * Get inner closing HTML.
	 *
	 * @since   1.1.0
	 *
	 * @return  string|HTML
	 */
	function get_section_inner_close() {
		$html = '';
		if ( $this->has_inner ) {
			$html = '</div>';
		}
		return $html;
	}

	/**
	 * Get section-content closing HTML.
	 *
	 * @since   1.3.0
	 *
	 * @return  string|HTML
	 */
	function get_section_content_close() {
		$html = '';
		if ( $this->has_wrap ) {
			$html = '</div>';
		}
		return $html;
	}

	/**
	 * Get wrap closing HTML.
	 *
	 * @since   1.1.0
	 *
	 * @return  string|HTML
	 */
	function get_section_wrap_close() {
		$html = '';
		if ( $this->has_wrap ) {
			$html = '</div>';
		}
		return $html;
	}

	/**
	 * Calculate section image srcset.
	 * Adds srcset of smaller images with a broad (but still landscape) aspect ratio.
	 *
	 * @since   1.8.0
	 *
	 * @return  string|HTML
	 */
	function calculate_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {

		$sizes = $image_meta['sizes'];

		// Bail if no sizes.
		if ( ! array( $sizes ) || empty( $sizes ) ) {
			return $sources;
		}

		foreach( $sizes as $size => $values ) {

			// Skip if not a size we want to check.
			if ( ! in_array( $size, array( 'full-width', 'featured', 'one-half', 'one-third', 'one-fourth' ) ) ) {
				continue;
			}

			// Get new image data.
			$source   = wp_get_attachment_image_src( $attachment_id, $size );
			$url      = $source[0];
			$width    = $source[1];
			$height   = $source[2];
			$ratio    = $width/$height;
			$in_range = ( $ratio > 1 ) && ( $ratio < 3 );

			// Skip if this image size isn't a valid aspect ratio.
			if ( ! $in_range ) {
				continue;
			}

			// Add to our new srcset.
			$sources[ $values['width'] ] = array(
				'url'        => $url,
				'descriptor' => 'w',
				'value'      => $width,
			);
		}

		return $sources;
	}

}
