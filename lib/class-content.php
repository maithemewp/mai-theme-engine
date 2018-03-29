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

	function add_align_classes( $classes, $args ) {
		/**
		 * "align" takes precendence over "align_cols" and "align_text".
		 * "align" forces the text to align along with the cols.
		 */
		if ( isset( $this->args['align'] ) && ! empty( $this->args['align'] ) ) {
			$classes = $this->add_align_both_classes( $classes, $this->args['align'] );
		} else {
			// Align columns.
			if ( isset( $this->args['align_cols'] ) && ! empty( $this->args['align_cols'] ) ) {
				$classes = $this->add_align_cols_classes( $classes, $this->args['align_cols'] );
			}
			// Align columns.
			if ( isset( $this->args['align_text'] ) && ! empty( $this->args['align_text'] ) ) {
				$classes = $this->add_align_text_classes( $classes, $this->args['align_text'] );
			}
		}
		return $classes;
	}

	/**
	 * Add align classes.
	 *
	 * @param   string  $classes  The existing HTML classes.
	 * @param   array   $align    The array of alignment values.
	 *
	 * @return  $string  HTML ready classes.
	 */
	function add_align_both_classes( $classes, $alignment ) {
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

	/**
	 * Add align column classes.
	 *
	 * @param   string  $classes  The existing HTML classes.
	 * @param   array   $align    The array of alignment values.
	 *
	 * @return  $string  HTML ready classes.
	 */
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

	/**
	 * Add align text classes.
	 *
	 * @param   string  $classes  The existing HTML classes.
	 * @param   array   $align    The array of alignment values.
	 *
	 * @return  $string  HTML ready classes.
	 */
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

	/**
	 * Get the classes needed for an entry from number of columns.
	 *
	 * @param  string  $columns  number of columns to get classes for.
	 *
	 * @return string  the classes
	 */
	public static function get_classes_by_columns( $columns ) {
		switch ( (int) $columns ) {
			case 1:
				$classes = 'flex-entry col col-xs-12';
			break;
			case 2:
				$classes = 'flex-entry col col-xs-12 col-sm-6';
			break;
			case 3:
				$classes = 'flex-entry col col-xs-12 col-sm-6 col-md-4';
			break;
			case 4:
				$classes = 'flex-entry col col-xs-12 col-sm-6 col-md-3';
			break;
			case 6:
				$classes = 'flex-entry col col-xs-6 col-sm-4 col-md-2';
			break;
			default:
				$classes = 'flex-entry col col-xs-12 col-sm-6 col-md-4';
		}
		return $classes;
	}

	public static function get_bottom_class( $bottom ) {
		if ( ! $bottom ) {
			return '';
		}
		switch ( (int) $bottom ) {
			case 0:
				$class = 'bottom-xs-0';
			break;
			case 5:
				$class = 'bottom-xs-5';
			break;
			case 10:
				$class = 'bottom-xs-10';
			break;
			case 20:
				$class = 'bottom-xs-20';
			break;
			case 30:
				$class = 'bottom-xs-30';
			break;
			case 40:
				$class = 'bottom-xs-40';
			break;
			case 50:
				$class = 'bottom-xs-50';
			break;
			case 60:
				$class = 'bottom-xs-60';
			break;
			default:
				$class = '';
		}
		return $class;
	}

	/**
	 * If gutter is a valid Flexington size.
	 */
	function is_valid_gutter( $gutter ) {
		return in_array( $gutter, array( 5, 10, 20, 30, 40, 50 ) );
	}

}
