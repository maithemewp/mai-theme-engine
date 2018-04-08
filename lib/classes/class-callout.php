<?php

/**
 * Build a callout.
 *
 * @access  private
 */
class Mai_Callout {

	private $args;

	private $content;

	public function __construct( $args = array(), $content = null ) {

		$this->args    = $args;
		$this->content = $content;

		// Parse defaults and args.
		$this->args = shortcode_atts( array(
			'bg'    => '',
			'class' => '',
			'id'    => '',
			'style' => '',
		), $this->args, 'callout' );

		// Sanitize args.
		$this->args = array(
			'bg'    => mai_sanitize_hex_color( $this->args['bg'] ), // 3 or 6 dig hex color with or without hash
			'class' => mai_sanitize_html_classes( $this->args['class'] ),
			'id'    => sanitize_html_class( $this->args['id'] ),
			'style' => sanitize_text_field( $this->args['style'] ),
		);

	}

	/**
	 * Return the callout HTML.
	 *
	 * @return  string|HTML
	 */
	function render() {

		// Bail if no content.
		if ( null === $this->content ) {
			return;
		}

		// Set attributes.
		$attributes = array(
			'class' => mai_add_classes( $this->args['class'], 'callout' ),
			'id'    => ! empty( $this->args['id'] ) ? $this->args['id'] : '',
		);

		$dark_bg = false;

		// Maybe add the inline background color.
		if ( $this->args['bg'] ) {

			// Add the background color.
			$attributes = mai_add_background_color_attributes( $attributes, $this->args['bg'] );

			// If dark bg.
			if ( mai_is_dark_color( $this->args['bg'] ) ) {
				$dark_bg = true;
			}
		}

		// Add content shade class
		$attributes['class'] .= $dark_bg ? ' light-content' : '';

		// Maybe add inline styles.
		$attributes = mai_add_inline_styles( $attributes, $this->args['style'] );

		return sprintf( '<div %s>%s</div>', genesis_attr( 'mai-callout', $attributes, $this->args ), mai_get_processed_content( $this->content ) );
	}

}
