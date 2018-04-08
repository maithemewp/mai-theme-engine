<?php

/**
 * Build columns wrap.
 *
 * @access  private
 */
class Mai_Columns {

	private $args;

	private $content;

	public function __construct( $args = array(), $content = null ) {

		$this->args    = $args;
		$this->content = $content;

		// Parse defaults and args.
		$this->args = shortcode_atts( array(
			'align'      => '',
			'align_cols' => '',
			'align_text' => '',
			'class'      => '',   // HTML classes (space separated)
			'gutter'     => '30', // Space between columns (5, 10, 20, 30, 40, 50) only
			'id'         => '',   // Add HTML id
			'style'      => '',   // Inline styles
		), $this->args, 'columns' );

		// Sanitize args.
		$this->args = array(
			'align'      => mai_sanitize_keys( $this->args['align'] ),
			'align_cols' => mai_sanitize_keys( $this->args['align_cols'] ),
			'align_text' => mai_sanitize_keys( $this->args['align_text'] ),
			'class'      => mai_sanitize_html_classes( $this->args['class'] ),
			'gutter'     => absint( $this->args['gutter'] ),
			'id'         => sanitize_html_class( $this->args['id'] ),
			'style'      => sanitize_text_field( $this->args['style'] ),
		);

	}

	/**
	 * Return the columns HTML.
	 *
	 * @return  string|HTML
	 */
	function render() {

		// Bail if no content.
		if ( null === $this->content ) {
			return;
		}

		// Row attributes.
		$attributes = array(
			'class' => mai_add_classes( $this->args['class'], 'columns-shortcode row' ),
			'id'    => ! empty( $this->args['id'] ) ? $this->args['id'] : '',
		);

		// Add gutter.
		$attributes['class'] = mai_add_classes( sprintf( 'gutter-%s', $this->args['gutter'] ), $attributes['class'] );

		// Add row align classes.
		$attributes['class'] = mai_add_align_classes( $attributes['class'], $this->args, 'row' );

		// Maybe add inline styles.
		$attributes = mai_add_inline_styles( $attributes, $this->args['style'] );

		// Only do_shortcode cause mai_get_processed_content() happens inside each col.
		return sprintf( '<div %s>%s</div>', genesis_attr( 'flex-row', $attributes, $this->args ), do_shortcode( $this->content ) );
	}

}
