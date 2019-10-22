<?php

/**
 * Plugin Name:     Mai Theme Engine
 * Plugin URI:      https://maitheme.com/
 * Description:     The Mai Theme Engine plugin
 *
 * Version:         1.11.0-beta.10
 *
 * GitHub URI:      maithemewp/mai-theme-engine
 *
 * Author:          MaiTheme.com
 * Author URI:      https://maitheme.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Mai_Theme_Engine Class.
 *
 * @since 1.0.0
 */
final class Mai_Theme_Engine {

	/**
	 * @var Mai_Theme_Engine The one true Mai_Theme_Engine
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Main Mai_Theme_Engine Instance.
	 *
	 * Insures that only one instance of Mai_Theme_Engine exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   1.0.0
	 * @static  var array $instance
	 * @uses    Mai_Theme_Engine::setup_constants() Setup the constants needed.
	 * @uses    Mai_Theme_Engine::includes() Include the required files.
	 * @return  object | Mai_Theme_Engine The one true Mai_Theme_Engine
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Mai_Theme_Engine;
			// Methods
			self::$instance->setup_constants();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-theme-engine' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-theme-engine' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function setup_constants() {

		// Plugin version.
		define( 'MAI_THEME_ENGINE_VERSION', '1.11.0-beta.10' );

		// DB version.
		define( 'MAI_THEME_ENGINE_DB_VERSION', '1400' );

		// Plugin Folder Path.
		define( 'MAI_THEME_ENGINE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin Lib Path.
		define( 'MAI_THEME_ENGINE_LIB_DIR', MAI_THEME_ENGINE_PLUGIN_DIR . 'lib/' );

		// Plugin Folder URL.
		define( 'MAI_THEME_ENGINE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Root File.
		define( 'MAI_THEME_ENGINE_PLUGIN_FILE', __FILE__ );

		// Plugin Base Name.
		define( 'MAI_THEME_ENGINE_BASENAME', dirname( plugin_basename( __FILE__ ) ) );

	}

	/**
	 * Run the hooks.
	 *
	 * composer require yahnis-elsts/plugin-update-checker
	 * composer require cmb2/cmb2
	 *
	 * v2.6.0   CBM2
	 * v4.5.1   Plugin Update Checker
	 *
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function hooks() {

		// Include vendor libraries.
		require_once __DIR__ . '/vendor/autoload.php';

		// Run the updater.
		add_action( 'admin_init', function() {

			// Bail if current user cannot manage plugins.
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			// Bail if plugin updater is not loaded.
			if ( ! class_exists( 'Puc_v4_Factory' ) ) {
				return;
			}

			// Setup the updater.
			$updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/maithemewp/mai-theme-engine/', __FILE__, 'mai-theme-engine' );

			// Get the branch. If checking for beta releases.
			$tester = function_exists( 'genesis_get_option' ) && genesis_get_option( 'mai_tester' );
			$tester = $tester ? 'beta' : 'master';

			// Allow branch and updater object manipulation.
			$branch = apply_filters( 'mai_updater_branch', $tester );

			// Set the branch.
			$updater->setBranch( $branch );

			// Allow tokens to be used to bypass GitHub rate limit.
			if ( defined( 'MAI_UPDATER_TOKEN' ) ) {
				$updater->setAuthentication( MAI_UPDATER_TOKEN );
			}

			// Add icons for Dashboard > Updates screen.
			$updater->addResultFilter( function( $info, $response = null ) {
				$info->icons = array(
					'1x' => MAI_THEME_ENGINE_PLUGIN_URL . 'assets/images/icon-128x128.png',
					'2x' => MAI_THEME_ENGINE_PLUGIN_URL . 'assets/images/icon-256x256.png',
				);
				return $info;
			});
		});

		/**
		 * Include files after theme is loaded, to mimic being run in a child theme.
		 */
		add_action( 'genesis_setup', function() {

			// Do not load old stuff.
			add_filter( 'genesis_load_deprecated', '__return_false' );

			// Add HTML5 markup structure.
			add_theme_support( 'html5' );

			// Add HTML5 gallery and caption support.
			add_theme_support( 'html5', array( 'gallery', 'caption' ) );

			// Add title tag support.
			add_theme_support( 'title-tag' );

			// Add viewport meta tag for mobile browsers.
			add_theme_support( 'genesis-responsive-viewport' );

			// Add support for Genesis menus.
			add_theme_support( 'genesis-menus', array(
				'primary'      => __( 'Primary Menu', 'mai-theme-engine' ),
				'header_left'  => __( 'Header Left Menu', 'mai-theme-engine' ),
				'header_right' => __( 'Header Right Menu', 'mai-theme-engine' ),
				'secondary'    => __( 'Footer Menu', 'mai-theme-engine' ),
				'mobile'       => __( 'Mobile Menu', 'mai-theme-engine' ),
			) );

			// Add support for Genesis structural wraps.
			add_theme_support( 'genesis-structural-wraps', array(
				'archive-description',
				'breadcrumb',
				'header',
				'menu-primary',
				'menu-secondary',
				'footer-widgets',
				'footer',
			) );

			// Add Genesis accessibility support.
			add_theme_support( 'genesis-accessibility', array(
				'404-page',
				'drop-down-menu',
				'headings',
				'search-form',
				'skip-links',
			) );

			// Add custom logo support.
			add_theme_support( 'custom-logo', array(
				'height'      => 120, // Optional size
				'width'       => 240, // Optional size
				'flex-height' => true,
				'flex-width'  => true,
			) );

			// Add alignfull and alignwide support.
			add_theme_support( 'align-wide' );

			// Add excerpt support for pages.
			add_post_type_support( 'page', 'excerpt' );

			/**
			 * Move Genesis child theme style sheet to a later priority
			 * to make sure Mai Theme Engine loads CSS first.
			 *
			 * Add file time to the version for easier cache-busting when editing files.
			 */
			remove_action( 'genesis_meta', 'genesis_load_stylesheet' );
			add_action( 'wp_enqueue_scripts', 'mai_enqueue_main_stylesheet', 999 );
			function mai_enqueue_main_stylesheet() {
				$version  = defined( 'CHILD_THEME_VERSION' ) && CHILD_THEME_VERSION ? CHILD_THEME_VERSION : PARENT_THEME_VERSION;
				$version .= '.' . date ( 'njYHi', filemtime( get_stylesheet_directory() . '/style.css' ) );
				wp_enqueue_style( mai_get_handle(), get_stylesheet_uri(), false, $version );
			}

			// Load default favicon.
			add_filter( 'genesis_pre_load_favicon', 'mai_default_favicon' );
			function mai_default_favicon( $favicon ) {
				return MAI_THEME_ENGINE_PLUGIN_URL . 'assets/images/favicon.png';
			}

		}, 15 );

		/**
		 * Load our files after theme setup but at an earlier hook
		 * so devs can override some actions/filters without needing to specific priority.
		 */
		add_action( 'after_setup_theme', function(){

			/**
			 * Deactivate theme and show notice
			 * if Mai Theme Engine is not supported in the child theme.
			 * or if Mai Pro Engine is not supported in the child theme (backwards compatibility).
			 *
			 * @link https://10up.com/blog/2012/wordpress-plug-in-self-deactivation/
			 */
			if ( ! current_theme_supports( 'mai-theme-engine' ) && ! current_theme_supports( 'mai-pro-engine' ) ) {
				add_action( 'admin_init',    array( $this, 'deactivate_plugin' ) );
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				return;
			}

			// Lib.
			foreach ( glob( MAI_THEME_ENGINE_LIB_DIR . '*.php' ) as $file ) { include_once $file; }
			foreach ( glob( MAI_THEME_ENGINE_LIB_DIR . 'admin/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( MAI_THEME_ENGINE_LIB_DIR . 'classes/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( MAI_THEME_ENGINE_LIB_DIR . 'functions/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( MAI_THEME_ENGINE_LIB_DIR . 'settings/customizer/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( MAI_THEME_ENGINE_LIB_DIR . 'settings/metaboxes/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( MAI_THEME_ENGINE_LIB_DIR . 'structure/*.php' ) as $file ) { include_once $file; }

			// Get the image sizes to register.
			$image_sizes = mai_get_image_sizes();

			// If image sizes.
			if ( $image_sizes ) {

				// Loop through and add the image sizes.
				foreach ( $image_sizes as $name => $values ) {
					add_image_size( $name, $values['width'], $values['height'], $values['crop'] );
				}
			}

		}, 8 );

	}

	function deactivate_plugin() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	function admin_notices() {
		printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', __( '<strong>Your theme does not support the Mai Theme Engine plugin</strong>. As a result, Mai Theme Engine has been deactivated.', 'mai-theme-engine' ) );
		// Remove "Plugin activated" notice.
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

}

// This is only here for backwards compatibility with older Mai Pro themes.
final class Mai_Pro_Engine {}

/**
 * The main function for that returns Mai_Theme_Engine
 *
 * The main function responsible for returning the one true Mai_Theme_Engine
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Mai_Theme_Engine(); ?>
 *
 * @since 1.0.0
 *
 * @return object|Mai_Theme_Engine The one true Mai_Theme_Engine Instance.
 */
function Mai_Theme_Engine() {
	return Mai_Theme_Engine::instance();
}

// Get Mai_Theme_Engine Running.
Mai_Theme_Engine();
