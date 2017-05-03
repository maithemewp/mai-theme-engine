<?php
/**
 * Mai Theme.
 *
 * WARNING: This file is part of the core Mai Theme framework.
 * The goal is to keep all files in /lib/ untouched.
 * That way we can easily update the core structure of the theme on existing sites without breaking things
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.0
 */

/**
 * Main Mai_Col_Shortcode Class.
 *
 * @since 1.0.0
 */
final class Mai_Col_Shortcode {

	/**
	 * Singleton
	 * @var   Mai_Col_Shortcode The one true Mai_Col_Shortcode
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Main Mai_Col_Shortcode Instance.
	 *
	 * Insures that only one instance of Mai_Col_Shortcode exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   1.0.0
	 * @static  var array $instance
	 * @return  object | Mai_Col_Shortcode The one true Mai_Col_Shortcode
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Mai_Col_Shortcode;
            // Initialize
            self::$instance->init();
		}
		return self::$instance;
	}

	function init() {
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
			'class'	=> '',
			'id'	=> '',
			'style' => '',
		), $atts, 'col' );

		$atts = apply_filters( 'mai_col_shortcode_defaults', $atts );

		// Sanitize atts
		$atts = array(
			'class'	=> array_map( 'sanitize_html_class', ( array_filter( explode( ' ', $atts['class'] ) ) ) ),
			'id'	=> sanitize_html_class( $atts['id'] ),
			'style'	=> esc_attr( $atts['style'] ),
		);

		$flex_col = array( 'class' => mai_get_flex_entry_classes_by_fraction( $fraction ) );

		if ( ! empty($atts['wrapper_id']) ) {
			$flex_col['id'] = $atts['wrapper_id'];
		}

		if ( ! empty($atts['class']) ) {
			$flex_col['class'] .= ' ' . implode( ' ', $atts['class'] );
		}

	    /**
	     * Return the content with col wrap.
	     * With flex-col attr so devs can filter elsewhere.
	     */
	    return sprintf( '<div %s>%s</div>', genesis_attr( 'flex-col', $flex_col ), wpautop( do_shortcode( trim($content) ) ) );

	}

}

/**
 * The main function for that returns Mai_Col_Shortcode
 *
 * The main function responsible for returning the one true Mai_Col_Shortcode
 * Instance to functions everywhere.
 *
 * @since 1.0.0
 *
 * @return object|Mai_Col_Shortcode The one true Mai_Col_Shortcode Instance.
 */
function Mai_Col_Shortcode() {
	return Mai_Col_Shortcode::instance();
}

// Get Mai_Col_Shortcode Running.
Mai_Col_Shortcode();
