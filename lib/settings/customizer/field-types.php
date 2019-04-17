<?php

/**
 * Register custom customizer controls/fields.
 *
 * @access  private
 *
 * @return  void
 */
add_action( 'customize_register', 'mai_register_customizer_field_types', 8 );
function mai_register_customizer_field_types() {

	/**
	 * Multiple checkbox customize control class.
	 *
	 * @since  1.0.0
	 *
	 * @access private
	 */
	class Mai_Customize_Control_Multicheck extends WP_Customize_Control {

		/**
		 * The type of customize control being rendered.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    string
		 */
		public $type = 'multicheck';

		/**
		 * Enqueue scripts/styles.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue() {
			$suffix = mai_get_suffix();
			wp_enqueue_script( 'mai-customize-controls', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/admin/mai-customizer{$suffix}.js", array( 'jquery', 'jquery-ui-core' ), MAI_THEME_ENGINE_VERSION, true );
		}

		/**
		 * Displays the control content.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function render_content() {

			if ( empty( $this->choices ) ) {
				return;
			}

			if ( ! empty( $this->label ) ) {
				printf( '<label class="customize-control-title">%s</label>', esc_html( $this->label ) );
			}

			if ( ! empty( $this->description ) ) {
				printf( '<span class="description customize-control-description">%s</span>', $this->description );
			}

			$multi_values = ! is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value();

			?>
			<ul>
				<?php foreach ( $this->choices as $value => $label ) { ?>
					<li>
						<label>
							<input type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $multi_values ) ); ?> />
							<?php echo esc_html( $label ); ?>
						</label>
					</li>
				<?php } ?>
			</ul>

			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $multi_values ) ); ?>" />
			<?php

		}

	}

	/**
	 * Slider custom control.
	 *
	 * @since    1.8.0
	 * @author   Anthony Hortin <http://maddisondesigns.com>
	 * @license  http://www.gnu.org/licenses/gpl-2.0.html
	 * @link     https://github.com/maddisondesigns
	 */
	class Mai_Customize_Control_Slider extends WP_Customize_Control {

		/**
		 * The type of customize control being rendered.
		 *
		 * @since  1.8.0
		 * @access public
		 * @var    string
		 */
		public $type = 'slider_control';

		/**
		 * Enqueue our scripts and styles.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue() {
			// Use minified files if script debug is not being used.
			$suffix = mai_get_suffix();
			// Enqueue.
			wp_enqueue_script( 'mai-customize-controls', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/admin/mai-customizer{$suffix}.js", array( 'jquery', 'jquery-ui-core' ), MAI_THEME_ENGINE_VERSION, true );
			wp_enqueue_style( 'mai-customize-controls', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/admin/mai-customizer{$suffix}.css", array(), MAI_THEME_ENGINE_VERSION, 'all' );
		}

		/**
		 * Displays the control content.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function render_content() {
		?>
			<div class="slider-custom-control">
				<?php
				if ( ! empty( $this->label ) ) {
					printf( '<label class="customize-control-title">%s</label>', esc_html( $this->label ) );
				}

				if ( ! empty( $this->description ) ) {
					printf( '<span class="description customize-control-description">%s</span>', $this->description );
				}
				?>
				<div class="slider-controls-wrap">
					<span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="<?php echo esc_attr( $this->value() ); ?>"></span>
					<div class="slider" slider-min-value="<?php echo esc_attr( $this->input_attrs['min'] ); ?>" slider-max-value="<?php echo esc_attr( $this->input_attrs['max'] ); ?>" slider-step-value="<?php echo esc_attr( $this->input_attrs['step'] ); ?>"></div>
					<input type="number" id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" class="customize-control-slider-value" <?php $this->link(); ?> />&nbsp;<strong>px</strong>
				</div>
			</div>
		<?php
		}
	}

	/**
	 * Multiple checkbox customize control class.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	class Mai_Customize_Control_Break extends WP_Customize_Control {

		/**
		 * The type of customize control being rendered.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    string
		 */
		public $type = 'break';

		/**
		 * Displays the control content.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function render_content() {

			if ( ! empty( $this->label ) ) {
				printf( '<span class="customize-control-title">%s</span>', esc_html( $this->label ) );
			}

			if ( ! empty( $this->description ) ) {
				printf( '<span class="description customize-control-description">%s</span>', $this->description );
			}
		}

	}

	/**
	 * Multiple checkbox customize control class.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	class Mai_Customize_Control_Content extends WP_Customize_Control {

		/**
		 * The type of customize control being rendered.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    string
		 */
		public $type = 'heading';

		/**
		 * Displays the control content.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function render_content() {

			if ( ! empty( $this->label ) ) {
				printf( '<label class="customize-control-title">%s</label>', esc_html( $this->label ) );
			}

			if ( ! empty( $this->description ) ) {
				printf( '<span class="description customize-control-description">%s</span>', $this->description );
			}
		}

	}

}
