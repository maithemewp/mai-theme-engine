<?php

abstract class Mai_Content {

	/**
	 * Add classes to an existing string of classes.
	 *
	 * @param  string|array  $classes           The classes to add.
	 * @param  string        $existing_classes  The existing classes.
	 *
	 * @return string  HTML ready classes.
	 */
	function add_classes( $classes, $existing_classes = '' ) {
		if ( ! empty( $classes ) ) {
			$space   = ! empty( $existing_classes ) ? ' ' : '';
			$classes = is_array( $classes ) ? implode( ' ', $classes ) : $classes;
			return $existing_classes . $space . $classes;
		}
		return $existing_classes;
	}

	/**
	 * If gutter is a valid Flexington size.
	 */
	function is_valid_gutter( $gutter ) {
		return in_array( $gutter, array( 5, 10, 20, 30, 40, 50 ) );
	}

	/**
	 * Add align classes.
	 *
	 * @param   string  $classes  The existing HTML classes.
	 * @param   array   $align    The array of alignment values.
	 *
	 * @return  $string  HTML ready classes.
	 */
	function add_align_classes( $classes, $alignment ) {
		// Left.
		if ( in_array( 'left', $alignment ) ) {
			$classes .= ' start-xs text-xs-left';
		}
		// Center.
		if ( in_array( 'center', $alignment ) ) {
			$classes .= ' center-xs text-xs-center';
		}
		// Right.
		if ( in_array( 'right', $alignment ) ) {
			$classes .= ' end-xs text-xs-right';
		}
		// Top.
		if ( in_array( 'top', $alignment ) ) {
			$classes .= ' top-xs';
		}
		// Middle.
		if ( in_array( 'middle', $alignment ) ) {
			$classes .= ' middle-xs';
		}
		// Bottom.
		if ( in_array( 'bottom', $alignment ) ) {
			$classes .= ' bottom-xs';
		}
		return $classes;
	}

	function add_align_cols_classes( $classes, $alignment ) {
		// Left.
		if ( in_array( 'left', $alignment ) ) {
			$classes .= ' start-xs';
		}
		// Center.
		if ( in_array( 'center', $alignment ) ) {
			$classes .= ' center-xs';
		}
		// Right.
		if ( in_array( 'right', $alignment ) ) {
			$classes .= ' end-xs';
		}
		// Top.
		if ( in_array( 'top', $alignment ) ) {
			$classes .= ' top-xs';
		}
		// Middle.
		if ( in_array( 'middle', $alignment ) ) {
			$classes .= ' middle-xs';
		}
		// Bottom.
		if ( in_array( 'bottom', $alignment ) ) {
			$classes .= ' bottom-xs';
		}
		return $classes;
	}

	function add_align_text_classes( $classes, $alignment ) {
		// Left.
		if ( in_array( 'left', $alignment ) ) {
			$classes .= ' start-xs';
		}
		// Center.
		if ( in_array( 'center', $alignment ) ) {
			$classes .= ' center-xs';
		}
		// Right.
		if ( in_array( 'right', $alignment ) ) {
			$classes .= ' end-xs';
		}
		return $classes;
	}

}
