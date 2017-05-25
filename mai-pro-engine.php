<?php

/**
 * Plugin Name:     Mai Pro Engine
 * Plugin URI:      https://maitheme.com/
 * Description:     The Mai Pro Engine plugin
 *
 * Version:         0.0.1-beta.24
 *
 * GitHub URI:      bizbudding/mai-pro-engine
 *
 * Author:          Mike Hemberger, BizBudding Inc
 * Author URI:      https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Mai_Pro_Engine' ) ) :

/**
 * Main Mai_Pro_Engine Class.
 *
 * @since 1.0.0
 */
final class Mai_Pro_Engine {

    /**
     * @var Mai_Pro_Engine The one true Mai_Pro_Engine
     * @since 1.0.0
     */
    private static $instance;

    /**
     * Main Mai_Pro_Engine Instance.
     *
     * Insures that only one instance of Mai_Pro_Engine exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @since   1.0.0
     * @static  var array $instance
     * @uses    Mai_Pro_Engine::setup_constants() Setup the constants needed.
     * @uses    Mai_Pro_Engine::includes() Include the required files.
     * @return  object | Mai_Pro_Engine The one true Mai_Pro_Engine
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            // Setup the setup
            self::$instance = new Mai_Pro_Engine;
            // Methods
            self::$instance->setup_constants();
            self::$instance->setup();
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
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'Mai_Pro_Engine' ), '1.0' );
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
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'Mai_Pro_Engine' ), '1.0' );
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
        if ( ! defined( 'MAI_PRO_ENGINE_VERSION' ) ) {
            define( 'MAI_PRO_ENGINE_VERSION', '0.0.1-beta.24' );
        }

        // Plugin Folder Path.
        if ( ! defined( 'MAI_PRO_ENGINE_PLUGIN_DIR' ) ) {
            define( 'MAI_PRO_ENGINE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        // Plugin Lib Path
        if ( ! defined( 'MAI_PRO_ENGINE_LIB_DIR' ) ) {
            define( 'MAI_PRO_ENGINE_LIB_DIR', MAI_PRO_ENGINE_PLUGIN_DIR . 'lib/' );
        }

        // Plugin Includes Path
        if ( ! defined( 'MAI_PRO_ENGINE_INCLUDES_DIR' ) ) {
            define( 'MAI_PRO_ENGINE_INCLUDES_DIR', MAI_PRO_ENGINE_PLUGIN_DIR . 'includes/' );
        }

        // Plugin Folder URL.
        if ( ! defined( 'MAI_PRO_ENGINE_PLUGIN_URL' ) ) {
            define( 'MAI_PRO_ENGINE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }

        // Plugin Root File.
        if ( ! defined( 'MAI_PRO_ENGINE_PLUGIN_FILE' ) ) {
            define( 'MAI_PRO_ENGINE_PLUGIN_FILE', __FILE__ );
        }

        // Plugin Base Name
        if ( ! defined( 'MAI_PRO_ENGINE_BASENAME' ) ) {
            define( 'MAI_PRO_ENGINE_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
        }

    }

    /**
     * Include required files.
     *
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function setup() {

        // Includes (Vendor)
        require_once MAI_PRO_ENGINE_INCLUDES_DIR . 'CMB2/init.php';
        require_once MAI_PRO_ENGINE_INCLUDES_DIR . 'plugin-update-checker/plugin-update-checker.php';

        // Setup the updater
        $updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/bizbudding/mai-pro-engine/', __FILE__, 'mai-pro-engine' );

        /**
         * Include files after theme is loaded, to mimic being run in a child theme.
         * Priority must be earlier than 10 to make sure 'genesis_initial_layouts' filter fires.
         */
        add_action( 'genesis_setup', function(){

            // Do not load old stuff
            add_filter( 'genesis_load_deprecated', '__return_false' );

            // Add HTML5 markup structure
            add_theme_support( 'html5' );

            // Add title tag support
            add_theme_support( 'title-tag' );

            // Add viewport meta tag for mobile browsers
            add_theme_support( 'genesis-responsive-viewport' );

            add_theme_support( 'genesis-menus', array(
                'primary'       => __( 'Primary Menu', 'mai-pro' ),
                'header_left'   => __( 'Header Left Menu', 'mai-pro' ),
                'header_right'  => __( 'Header Right Menu', 'mai-pro' ),
                'secondary'     => __( 'Footer Menu', 'mai-pro' ),
                'mobile'        => __( 'Mobile Menu', 'mai-pro' ),
            ) );

            // Add support for structural wraps
            add_theme_support( 'genesis-structural-wraps', array(
                'archive-description',
                'breadcrumb',
                'header',
                'menu-primary',
                'menu-secondary',
                'footer-widgets',
                'footer',
            ) );

            // Add Accessibility support
            add_theme_support( 'genesis-accessibility', array(
                '404-page',
                'drop-down-menu',
                'headings',
                'search-form',
                'skip-links',
            ) );

            // Add custom logo support
            add_theme_support( 'custom-logo', array(
                'height'        => 120, // Optional size
                'width'         => 240, // Optional size
                'flex-height'   => true,
                'flex-width'    => true,
            ) );

            // Add excerpt support for pages
            add_post_type_support( 'page', 'excerpt' );

            /**
             * Move Genesis child theme style sheet to a later priority
             * to make sure Mai Theme Engine loads CSS first.
             */
            remove_action( 'genesis_meta', 'genesis_load_stylesheet' );
            add_action( 'wp_enqueue_scripts', 'genesis_enqueue_main_stylesheet', 14 );

            /**
             * Create the initial image sizes.
             * @link http://andrew.hedges.name/experiments/aspect_ratio/
             */
            $image_sizes = array(
                'banner' => array(
                    'width'  => 1600,
                    'height' => 533,
                    'crop'   => true, // 3x1
                ),
                'featured' => array(
                    'width'  => 800,
                    'height' => 600,
                    'crop'   => true, // 4x3 (works better for no sidebar)
                ),
                'one-half' => array(
                    'width'  => 550,
                    'height' => 413,
                    'crop'   => true, // 4x3
                ),
                'one-third' => array(
                    'width'  => 350,
                    'height' => 263,
                    'crop'   => true, // 4x3
                ),
                'one-fourth' => array(
                    'width'  => 260,
                    'height' => 195,
                    'crop'   => true, // 4x3
                ),
                'tiny' => array(
                    'width'  => 80,
                    'height' => 80,
                    'crop'   => true, // square
                ),
            );

            /**
             * Filter the image sizes to allow the theme to override.
             *
             * // Change the default Mai image sizes
             * add_filter( 'mai_image_sizes', 'prefix_custom_image_sizes' );
             * function prefix_custom_image_sizes( $image_sizes ) {
             *
             *   // Change one-third image size
             *   $image_sizes['one-third'] = array(
             *       'width'  => 350,
             *       'height' => 350,
             *       'crop'   => true,
             *   );
             *
             *   // Change one-fourth image size
             *   $image_sizes['one-fourth'] = array(
             *       'width'  => 260,
             *       'height' => 260,
             *       'crop'   => true,
             *   );
             *
             *   return $image_sizes;
             *
             * }
             *
             */
            $image_sizes = apply_filters( 'mai_image_sizes', $image_sizes );

            // Loop through and add the image sizes.
            foreach ( $image_sizes as $name => $data ) {
                add_image_size( $name, $data['width'], $data['height'], $data['crop'] );
            }

        }, 15 );

        /**
         * Load our files after theme setup but at an earlier hook
         * so devs can override some actions/filters without needing to specific priority.
         */
        add_action( 'after_setup_theme', function(){

            // Lib
            foreach ( glob( MAI_PRO_ENGINE_LIB_DIR . '*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAI_PRO_ENGINE_LIB_DIR . 'functions/*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAI_PRO_ENGINE_LIB_DIR . 'settings/*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAI_PRO_ENGINE_LIB_DIR . 'structure/*.php' ) as $file ) { include_once $file; }

        }, 8 );

    }

}
endif; // End if class_exists check.

/**
 * The main function for that returns Mai_Pro_Engine
 *
 * The main function responsible for returning the one true Mai_Pro_Engine
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Mai_Pro_Engine(); ?>
 *
 * @since 1.0.0
 *
 * @return object|Mai_Pro_Engine The one true Mai_Pro_Engine Instance.
 */
function Mai_Pro_Engine() {
    return Mai_Pro_Engine::instance();
}

// Get Mai_Pro_Engine Running.
Mai_Pro_Engine();
