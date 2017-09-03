<?php

/**
 * Register custom customizer controls/fields.
 *
 * @return  void.
 */
add_action( 'customize_register', 'mai_register_customizer_field_types', 8 );
function mai_register_customizer_field_types() {

	/**
	 * Multiple checkbox customize control class.
	 *
	 * @since  1.0.0
	 * @access public
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
			// Use minified files if script debug is not being used.
			$suffix = mai_get_suffix();
			// Enqueue.
			wp_enqueue_script( 'mai-customize-controls', MAI_PRO_ENGINE_PLUGIN_URL . "assets/js/customize-controls{$suffix}.js", array( 'jquery' ) );
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
				printf( '<span class="customize-control-title">%s</span>', esc_html( $this->label ) );
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
				printf( '<span class="customize-control-title">%s</span>', esc_html( $this->label ) );
			}

			if ( ! empty( $this->description ) ) {
				printf( '<span class="description customize-control-description">%s</span>', $this->description );
			}
		}

	}

}
