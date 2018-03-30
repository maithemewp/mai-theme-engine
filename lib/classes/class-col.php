<?php

/**
 * Build column.
 *
 * @access  private
 */
class Mai_Col {

	private $args;

	private $content;

	public function __construct( $args = array(), $content = null ) {

		$this->args    = $args;
		$this->content = $content;

		// Parse defaults and args.
		$this->args = shortcode_atts( array(
			'align'      => '', // "top, left" Comma separted. overrides align_cols and align_text for most times one setting makes sense
			'align_cols' => '', // "top, left" Comma separted
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
			'width'      => 'col', // Comma separated for each breakpoint. Mobile-first. Accepts 'col', 'auto', and 1-12.
		), $atts, 'col' );

		// Sanitize args.
		$this->args = array(
			'align'      => mai_sanitize_keys( $this->args['align'] ),
			'align_cols' => mai_sanitize_keys( $this->args['align_cols'] ),
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
			'width'      => mai_sanitize_keys( $this->args['width'] ),
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

		// Trim because testing returned string of nbsp.
		$this->content = trim( $this->content );

	}

	function get_col() {

		$attributes = array(
			'class' => $this->get_col_classes(),
		);

		// TODO: EVERYTHING HERE!


		$attributes = array( 'class' => mai_get_flex_entry_classes_by_fraction( $fraction ) );

		// URL.
		$bg_link = $bg_link_title = '';
		if ( ! empty( $atts['link'] ) ) {
			if ( is_numeric( $atts['link'] ) ) {
				$bg_link_url   = get_permalink( (int) $atts['link'] );
				$bg_link_title = get_the_title( (int) $atts['link'] );
			} else {
				$bg_link_url   = esc_url( $atts['link'] );
			}
			$bg_link              = mai_get_bg_image_link( $bg_link_url, $bg_link_title );
			$attributes['class'] .= ' has-bg-link';
		}

		// ID
		if ( ! empty( $atts['wrapper_id'] ) ) {
			$attributes['id'] = $atts['wrapper_id'];
		}

		// Classes
		if ( ! empty( $atts['class'] ) ) {
			$attributes['class'] .= ' ' . $atts['class'];
		}

		// Add the align classes
		$attributes['class'] = $this->add_entry_align_classes( $attributes['class'], $atts, 'columns' );

		$light_content = false;

		// Maybe add the inline background color
		if ( $atts['bg'] ) {

			// Add the background color
			$attributes = mai_add_background_color_attributes( $attributes, $atts['bg'] );

			if ( mai_is_dark_color( $atts['bg'] ) ) {
				$light_content = true;
			}
		}

		// If we have an image ID
		if ( $atts['image'] ) {

			// If we have content
			if ( $content ) {
				// Set dark overlay if we don't have one
				$atts['overlay'] = ! $atts['overlay'] ? 'dark' : $atts['overlay'];
				$light_content   = true;
			}

			// If showing featured image and link is a post ID.
			if ( ( 'featured' == $atts['image'] ) && is_numeric( $atts['link'] ) ) {
				$image_id = get_post_thumbnail_id( absint( $atts['link'] ) );
			} else {
				$image_id = absint( $atts['image'] );
			}

			// Add the aspect ratio attributes
			$attributes = mai_add_background_image_attributes( $attributes, $image_id, $atts['image_size'] );
		}

		if ( $this->has_overlay( $atts ) ) {
			$attributes['class'] .= ' overlay';
			// Only add overlay classes if we have a valid overlay type
			switch ( $atts['overlay'] ) {
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

		// Add content shade class
		$attributes['class'] .= $light_content ? ' light-content' : '';

		// Add bottom margin classes.
		if ( ! empty( $atts['bottom'] ) ) {
			$bottom = $this->get_bottom_class( $atts );
			if ( $bottom ) {
				$attributes['class'] .= ' ' . $bottom;
			}
		}

		// Maybe add inline styles
		if ( isset( $atts['style'] ) && $atts['style'] ) {
			$attributes['style'] = $atts['style'];
		}

		/**
		 * Return the content with col wrap.
		 * With flex-col attr so devs can filter elsewhere.
		 *
		 * Only do_shortcode() on content because get_columns() wrap runs get_processed_content() which cleans things up.
		 */
		return sprintf( '<div %s>%s%s</div>', genesis_attr( 'flex-col', $attributes, $atts ), do_shortcode( $content ), $bg_link );
	}

	function get_col_classes() {

		$classes = 'col';

		$i = 0;

		foreach( $this->width as $width ) {
			$classes = mai_add_classes( $this->add_col_class( $i, $width ), $classes );
			$i++;
		}

	}
	function add_col_class( $i, $width ) {
		return sprintf( '%s%s', $this->get_col_prefix( $i ), $this->get_col_suffix( $width ) );
	}

	function get_col_prefix( $i ) {
		$prefixes = $this->get_col_prefixes();
		if ( isset( $prefixes[$i] ) ) {
			return $prefixes[$i];
		}
		return '';
	}

	// Order is super important. This is here solely to grab them by the index.
	function get_col_prefixes() {
		return array( 'col-xs', 'col-sm', 'col-md', 'col-lg', 'col-xl' );
	}

	function get_col_suffix( $width ) {
		switch ( (string) $width ) {
			case 'col':
				$suffix = '';
				break;
			case 'auto':
				$suffix = '-auto';
				break;
			case '12':
				$suffix = '-12';
				break;
			case '11':
				$suffix = '-11';
				break;
			case '10':
				$suffix = '-10';
				break;
			case '9':
				$suffix = '-9';
				break;
			case '8':
				$suffix = '-8';
				break;
			case '7':
				$suffix = '-7';
				break;
			case '6':
				$suffix = '-6';
				break;
			case '5':
				$suffix = '-5';
				break;
			case '4':
				$suffix = '-4';
				break;
			case '3':
				$suffix = '-3';
				break;
			case '2':
				$suffix = '-2';
				break;
			case '1':
				$suffix = '-1';
				break;
			default:
				$suffix = '';
		}
		return $suffix;
	}

}
