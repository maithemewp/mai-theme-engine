<?php

/**
 * Add dismissible upgrade notice.
 *
 * @since 1.13.0
 * @since 1.14.0 Restyled to match Mai Engine notices. Security-only messaging.
 *
 * @return void
 */
add_action( 'admin_notices', 'mai_upgrade_notice', 4 );
function mai_upgrade_notice() {
	if ( ! current_user_can( 'switch_themes' ) ) {
		return;
	}

	$utm       = 'utm_source=mai-theme-engine&utm_medium=notice&utm_campaign=v1-security-only';
	$url       = 'https://bizbudding.com/mai-theme/?' . $utm;
	$docs_url  = 'https://help.bizbudding.com/article/209-upgrade?' . $utm;
	$timestamp = get_user_option( 'mai_upgrade_notice', get_current_user_id() );

	// Bail if already dismissed.
	if ( $timestamp && current_time( 'timestamp' ) < $timestamp ) {
		return;
	}
	?>
	<style>
		.mai-upgrade-notice {
			position: relative;
			padding: 1rem;
			border-left-color: #bcda83;

			/* Core's `.update-core-php h2 { margin-top: 4em }` on the Updates screen shoves this title down. */
			h2 {
				margin: 0;
			}

			ul {
				margin-left: 0;
			}

			.mai-upgrade-links {
				display: flex;
				flex-wrap: wrap;
				align-items: center;
				gap: 1rem;
			}

			.mai-dismissers {
				position: absolute;
				top: 0;
				right: 0;
				display: flex;
				gap: .5rem;
				padding: 8px;
				color: #787c82;
			}

			.mai-upgrade-dismiss {
				color: #787c82;
				text-decoration: none;
			}

			.mai-upgrade-dismiss:hover,
			.mai-upgrade-dismiss:focus-visible {
				color: #bcda83;
				text-decoration: underline;
			}

			.button {
				color: #181f09;
				background: #bcda83;
				border-color: #bcda83;
			}

			.button:hover,
			.button:focus {
				color: #181f09;
				background: #a0b96f;
				border-color: #a0b96f;
			}
		}
	</style>
	<div class="notice mai-upgrade-notice">
		<div style="display:flex;align-items:center;gap:.5rem;flex-wrap:nowrap;">
			<img width="24" height="24" src="<?php echo esc_url( MAI_THEME_ENGINE_PLUGIN_URL . 'assets/images/icon-128x128.png' ); ?>" alt="Mai" style="flex-shrink:0">
			<h2><?php esc_html_e( 'Mai Theme v1 now only gets security updates.', 'mai-theme-engine' ); ?></h2>
		</div>
		<p><strong><?php esc_html_e( 'Future versions will only fix security issues, so new features are going into Mai Theme v2.', 'mai-theme-engine' ); ?></strong></p>
		<ul>
			<li>✅&nbsp;&nbsp;<?php esc_html_e( 'Built for the block editor, not the Classic Editor.', 'mai-theme-engine' ); ?></li>
			<li>✅&nbsp;&nbsp;<?php esc_html_e( 'Faster, lighter pages.', 'mai-theme-engine' ); ?></li>
			<li>✅&nbsp;&nbsp;<?php esc_html_e( 'Easier to customize, with no code needed.', 'mai-theme-engine' ); ?></li>
		</ul>
		<p class="mai-upgrade-links">
			<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php esc_html_e( 'See Mai Theme v2', 'mai-theme-engine' ); ?></a>
			<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $docs_url ); ?>"><?php esc_html_e( 'What to expect when upgrading', 'mai-theme-engine' ); ?></a>
		</p>
		<div class="mai-dismissers">
			<a href="#" role="button" class="mai-upgrade-dismiss" data-weeks="9999"><?php esc_html_e( "Don't show again", 'mai-theme-engine' ); ?></a>
			<span>·</span>
			<a href="#" role="button" class="mai-upgrade-dismiss" data-weeks="4"><?php esc_html_e( 'Remind me later', 'mai-theme-engine' ); ?></a>
		</div>
	</div>
	<?php
	/**
	 * Adds ajax script to the footer.
	 *
	 * @return void
	 */
	add_action( 'admin_footer', function() {
		$ajaxurl = admin_url( 'admin-ajax.php' );
		?>
		<script>
			jQuery(document).ready(function($) {
				$( '.mai-upgrade-notice' ).on( 'click', '.mai-upgrade-dismiss', function(e) {
					e.preventDefault();
					jQuery.ajax({
						type: 'post',
						url: '<?php echo $ajaxurl; ?>',
						data: {
							action: 'mai_dismiss_upgrade_notice',
							nonce: '<?php echo esc_js( wp_create_nonce( 'mai_dismiss_upgrade_notice' ) ); ?>',
							weeks: $(this).data( 'weeks' ),
						},
						success: function( response ) {
							$( '.mai-upgrade-notice' ).fadeOut();
						},
					});
				});
			});
		</script>
		<?php
	});
}

/**
 * Adds user option to dismiss upgrade notice for set amount of time.
 *
 * @since 1.13.1
 *
 * @return void
 */
add_action( 'wp_ajax_mai_dismiss_upgrade_notice', 'mai_dismiss_upgrade_notice' );
function mai_dismiss_upgrade_notice() {
	check_ajax_referer( 'mai_dismiss_upgrade_notice', 'nonce' );

	if ( ! current_user_can( 'switch_themes' ) ) {
		wp_die(
			__( 'You do not have permission.', 'mai-theme-engine' ),
			__( 'Error', 'mai-theme-engine' ),
			[
				'response' 	=> 403,
			]
		);
	}

	$weeks = filter_input( INPUT_POST, 'weeks', FILTER_SANITIZE_NUMBER_INT );
	$weeks = $weeks ?: 4;
	$now   = current_time( 'timestamp' );
	$again = strtotime( sprintf( '+%s weeks', $weeks ), $now );

	update_user_option( get_current_user_id(), 'mai_upgrade_notice', $again );
}

/**
 * Enqueue an admin script, for custom editor styles and other stuff.
 *
 * @return void
 */
add_action( 'admin_enqueue_scripts', 'mai_admin_enqueue_scripts' );
function mai_admin_enqueue_scripts() {
	$suffix = mai_get_suffix();
	wp_register_style( 'mai-admin', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/admin/mai-admin{$suffix}.css", array(), MAI_THEME_ENGINE_VERSION );
	wp_register_script( 'mai-admin', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/admin/mai-admin{$suffix}.js", array( 'jquery' ), MAI_THEME_ENGINE_VERSION, true );
}

/**
 * Enqueue customizer scripts.
 *
 * @since   1.8.0
 *
 * @return  void
 */
add_action( 'customize_controls_enqueue_scripts', 'mai_customizer_enqueue_scripts' );
function mai_customizer_enqueue_scripts() {
	$suffix = mai_get_suffix();
	wp_enqueue_style( 'mai-customize', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/admin/mai-customizer{$suffix}.css", array(), MAI_THEME_ENGINE_VERSION );
}

/**
 * Add our image sizes to the media chooser.
 *
 * @since   Unknown
 * @since   1.8.0
 *
 * @param   $sizes  The size options.
 *
 * @return  array   Modified size options.
 */
add_filter( 'image_size_names_choose', 'mai_do_media_chooser_sizes' );
function mai_do_media_chooser_sizes( $sizes ) {

	// Get the image sizes to register.
	$new_sizes = mai_get_image_sizes();

	// Bail if no image sizes.
	if ( ! $new_sizes ) {
		return $sizes;
	}

	// Unset the big images.
	unset( $new_sizes['banner'] );
	unset( $new_sizes['section'] );
	unset( $new_sizes['full-width'] );

	// Build new array with 'name' => 'label';
	$new_sizes = wp_list_pluck( $new_sizes, 'label' );

	return array_merge( $sizes, $new_sizes );;
}

/**
 * Remove unsupported FlexGrid gallery options from admin
 *
 * @return void
 */
add_action( 'admin_head', 'mai_remove_unsupported_flexgrid_gallery_options' );
function mai_remove_unsupported_flexgrid_gallery_options() {
	echo '<style type="text/css">
		.gallery-settings .columns option[value="5"],
		.gallery-settings .columns option[value="7"],
		.gallery-settings .columns option[value="8"],
		.gallery-settings .columns option[value="9"] {
			display:none !important;
			visibility: hidden !important;
		}
	</style>';
}

/**
 * Adds a new select bar to the WP editor.
 * Insert 'styleselect' into the $buttons array.
 * _2 places the new button on the second line.
 *
 * @return  array
 */
add_filter( 'mce_buttons_2', 'mai_add_styleselect_dropdown' );
function mai_add_styleselect_dropdown( $buttons ) {
	// Bail if we already have styleselect.
	if ( in_array( 'styleselect', $buttons ) ) {
		return $buttons;
	}
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}

/**
 * Add a button option to the editor.
 *
 * @param   array  $init_array
 *
 * @return  array
 */
add_filter( 'tiny_mce_before_init', 'mai_add_style_format_options_to_editor' );
function mai_add_style_format_options_to_editor( $init_array ) {
	$style_formats = array(
		array(
			'title'    => __( 'Primary Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button',
		),
		array(
			'title'    => __( 'Primary Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button large',
		),
		array(
			'title'    => __( 'Primary Ghost Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost',
		),
		array(
			'title'    => __( 'Primary Ghost Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost large',
		),
		array(
			'title'    => __( 'Secondary Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button alt',
		),
		array(
			'title'    => __( 'Secondary Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button alt large',
		),
		array(
			'title'    => __( 'Secondary Ghost Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost',
		),
		array(
			'title'    => __( 'Secondary Ghost Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost large',
		),
		array(
			'title'    => __( 'White Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button white',
		),
		array(
			'title'    => __( 'White Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button white large',
		),
		array(
			'title'    => __( 'White Ghost Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost',
		),
		array(
			'title'    => __( 'White Ghost Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost large',
		),
	);

	// Add to existing formats.
	if ( isset( $init_array['style_formats'] ) && ! empty( $init_array['style_formats'] ) ) {
		$decoded       = json_decode( $init_array['style_formats'], true );
		$style_formats = array_merge( $decoded, $style_formats );
	}

	// JSON encode the array.
	$style_formats = json_encode( $style_formats );

	// Add the formats.
	$init_array['style_formats'] = $style_formats;

	return $init_array;
}

/**
 * Yoast (and possibly others) run do_shortcode on category descriptions in admin list,
 * which blows things up on [grid] when parent="current" and it searches for the category/post ID.
 *
 * @return  string
 */
add_filter( 'category_description', 'mai_limit_term_description' );
function mai_limit_term_description( $desc ) {
	if ( ! is_admin() ) {
		return $desc;
	}
	return wp_trim_words( strip_tags( $desc ), 24, '...' );
}

/**
 * Enable the editor on the page for posts.
 * Force an empty space as the post content so block editor (Gutenberg) will show in the admin when editing this page,
 * otherwise default editor will show. This is stupid and hacky and I wish Gutenberg didn't do this.
 *
 * @since   1.6.2
 *
 * @return  void
 */
add_action( 'admin_head', 'mai_posts_page_edit_form' );
function mai_posts_page_edit_form() {

	$current_screen = get_current_screen();

	// Bail if not editing a page.
	if ( ! $current_screen || 'page' !== $current_screen->id ) {
		return;
	}

	// Bail if not editing the "Page for Posts".
	if ( (int) get_the_ID() !== (int) get_option( 'page_for_posts' ) ) {
		return;
	}

	// Add the editor.
	add_post_type_support( 'page', 'editor' );

	// Get the post object.
	$post = get_post( get_the_ID() );

	// Bail if no post. Safety first!
	if ( ! $post ) {
		return;
	}

	// Bail the post has content.
	if ( ! empty( $post->post_content ) ) {
		return;
	}

	// Update the post, adding a space as the content.
	wp_update_post( array(
		'ID'           => $post->ID,
		'post_content' => ' ',
	) );
}

// Change login logo.
add_action( 'login_head',  'mai_login_logo_css' );
function mai_login_logo_css() {

	$logo_id = get_theme_mod( 'custom_logo' );

	// Bail if we don't have a custom logo.
	if ( ! $logo_id ) {
		return;
	}

	$width    = get_theme_mod( 'custom_logo_width', 180 );
	$width_px = absint( $width ) . 'px';

	// Hide the default logo and heading.
	echo "<style>
		.login h1,
		.login h1 a {
			background: none !important;
			position: absolute !important;
			clip: rect(0, 0, 0, 0) !important;
			height: 1px !important;
			width: 1px !important;
			padding: 0 !important;
			margin: 0 !important;
			border: 0 !important;
			overflow: hidden !important;
		}
		.login .mai-login-logo a {
			max-width: {$width_px};
			display: block;
			margin: auto;
		}
		.login .mai-login-logo img {
			display: block !important;
			height: auto !important;
			width: auto !important;
			max-width: 100% !important;
			margin: 0 auto !important;
		}
		.login #login_error,
		.login .message {
			margin-top: 16px !important;
		}
		.login #nav,
		.login #backtoblog {
			text-align: center;
		}
	</style>";

	// Add our own inline logo.
	add_action( 'login_message', function() use ( $logo_id ) {
		// From WP core.
		if ( is_multisite() ) {
			$login_header_url   = network_home_url();
			$login_header_title = get_network()->site_name;
		} else {
			$login_header_url   = __( 'https://wordpress.org/' );
			$login_header_title = __( 'Powered by WordPress' );
		}
		printf( '<h2 class="mai-login-logo"><a href="%s" title="%s" tabindex="-1">%s</a></h2>',
			esc_url( apply_filters( 'login_headerurl', $login_header_url ) ),
			esc_attr( apply_filters( 'login_headertitle', $login_header_title ) ),
			wp_get_attachment_image( $logo_id, 'medium' )
		);
	});

}

// Change login link.
add_filter( 'login_headerurl', 'mai_login_link' );
function mai_login_link() {
	return get_site_url();
}
