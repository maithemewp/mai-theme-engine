<?php

/**
 * Plugin Name:     Mai Pro Engine
 * Plugin URI:      https://maitheme.com/
 * Description:     The Mai Pro Engine plugin
 *
 * Version:         0.0.1.beta.28
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
            define( 'MAI_PRO_ENGINE_VERSION', '0.0.1.beta.28' );
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
        require_once MAI_PRO_ENGINE_INCLUDES_DIR . 'PHPColors/Color.php';
        require_once MAI_PRO_ENGINE_INCLUDES_DIR . 'Shortcode_Button/shortcode-button.php';
        require_once MAI_PRO_ENGINE_INCLUDES_DIR . 'plugin-update-checker/plugin-update-checker.php';

        // Setup the updater
        $updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/bizbudding/mai-pro-engine/', __FILE__, 'mai-pro-engine' );

        /**
         * Include files after theme is loaded, to mimic being run in a child theme.
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
                'primary'       => __( 'Primary Menu', 'mai-pro-engine' ),
                'header_left'   => __( 'Header Left Menu', 'mai-pro-engine' ),
                'header_right'  => __( 'Header Right Menu', 'mai-pro-engine' ),
                'secondary'     => __( 'Footer Menu', 'mai-pro-engine' ),
                'mobile'        => __( 'Mobile Menu', 'mai-pro-engine' ),
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
            add_action( 'wp_enqueue_scripts', 'genesis_enqueue_main_stylesheet', 999 );

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

            /**
             * Deactivate theme and show notice
             * if Mai Pro Engine is not supported in the child theme.
             *
             * @link https://10up.com/blog/2012/wordpress-plug-in-self-deactivation/
             */
            if ( ! current_theme_supports( 'mai-pro-engine' ) ) {
                add_action( 'admin_init', array( $this, 'deactivate_plugin' ) );
                add_action( 'admin_notices', array( $this, 'admin_notices' ) );
                return;
            }

            // Lib
            foreach ( glob( MAI_PRO_ENGINE_LIB_DIR . '*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAI_PRO_ENGINE_LIB_DIR . 'functions/*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAI_PRO_ENGINE_LIB_DIR . 'settings/*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAI_PRO_ENGINE_LIB_DIR . 'structure/*.php' ) as $file ) { include_once $file; }

        }, 8 );

        // This hook, with this priority ensures the Shortcode_Button library is loaded.
        // add_action( 'shortcode_button_load', 'mai_col_shortcode_button', ( SHORTCODE_BUTTONS_LOADED + 1 ) );
        function mai_col_shortcode_button() {

            $button_slug = 'col';

            // Set up the button data that will be passed to the javascript files
            $js_button_data = array(
                'qt_button_text' => __( 'Column', 'mai-pro-engine' ),
                'button_tooltip' => __( 'Column', 'mai-pro-engine' ),
                'icon'           => 'dashicons-editor-insertmore',
                // Optional parameters
                'include_close'  => true,  // Will wrap your selection in the shortcode
                'mceView'        => true,  // Live preview of shortcode in editor. YMMV.
                // Use your own textdomain
                'l10ncancel'     => __( 'Cancel', 'mai-pro-engine' ),
                'l10ninsert'     => __( 'Insert Column', 'mai-pro-engine' ),
            );

            // Optional additional parameters
            $additional_args = array(
                'cmb_metabox_config' => 'col_shortcode_button_config',
            );

            // $button = new Shortcode_Button( $button_slug, $js_button_data );
            $button = new Shortcode_Button( $button_slug, $js_button_data, $additional_args );

        }

        // This hook, with this priority ensures the Shortcode_Button library is loaded.
        // add_action( 'shortcode_button_load', 'mai_columns_shortcode_button', ( SHORTCODE_BUTTONS_LOADED + 1 ) );
        function mai_columns_shortcode_button() {

            $button_slug = 'columns';

            // Set up the button data that will be passed to the javascript files
            $js_button_data = array(
                'qt_button_text' => __( 'Columns Wrap', 'mai-pro-engine' ),
                'button_tooltip' => __( 'Columns Wrap', 'mai-pro-engine' ),
                'icon'           => 'dashicons-align-center',
                // Optional parameters
                'include_close'  => true,  // Will wrap your selection in the shortcode
                'mceView'        => false, // Live preview of shortcode in editor. YMMV.
                // Use your own textdomain
                'l10ncancel'     => __( 'Cancel', 'mai-pro-engine' ),
                'l10ninsert'     => __( 'Insert Columns Wrap', 'mai-pro-engine' ),
            );

            // Optional additional parameters
            $additional_args = array(
                'cmb_metabox_config' => 'columns_shortcode_button_config',
            );

            // $button = new Shortcode_Button( $button_slug, $js_button_data );
            $button = new Shortcode_Button( $button_slug, $js_button_data, $additional_args );

        }

        // This hook, with this priority ensures the Shortcode_Button library is loaded.
        add_action( 'shortcode_button_load', 'mai_grid_shortcode_button', ( SHORTCODE_BUTTONS_LOADED + 1 ) );
        function mai_grid_shortcode_button() {

            // $button_slug = 'grid';

            // // Set up the button data that will be passed to the javascript files
            // $js_button_data = array(
            //     'qt_button_text' => __( 'Grid Content', 'mai-pro-engine' ),
            //     'button_tooltip' => __( 'Grid Content', 'mai-pro-engine' ),
            //     'icon'           => 'dashicons-grid-view',
            //     // Optional parameters
            //     'include_close'  => false, // Will wrap your selection in the shortcode
            //     'mceView'        => true,  // Live preview of shortcode in editor. YMMV.
            //     // Use your own textdomain
            //     'l10ncancel'     => __( 'Cancel', 'mai-pro-engine' ),
            //     'l10ninsert'     => __( 'Insert Grid Content', 'mai-pro-engine' ),
            // );

            // // Optional additional parameters
            // $additional_args = array(
            //     'cmb_metabox_config' => 'grid_shortcode_button_config',
            // );

            // // $button = new Shortcode_Button( $button_slug, $js_button_data );
            // $button = new Shortcode_Button( $button_slug, $js_button_data, $additional_args );



            $button_slug = 'columns';

            // Set up the button data that will be passed to the javascript files
            $js_button_data = array(
                'qt_button_text' => __( 'Columns Wrap', 'mai-pro-engine' ),
                'button_tooltip' => __( 'Columns Wrap', 'mai-pro-engine' ),
                'icon'           => 'dashicons-align-center',
                // Optional parameters
                'include_close'  => true,  // Will wrap your selection in the shortcode
                'mceView'        => true, // Live preview of shortcode in editor. YMMV.
                // Use your own textdomain
                'l10ncancel'     => __( 'Cancel', 'mai-pro-engine' ),
                'l10ninsert'     => __( 'Insert Columns Wrap', 'mai-pro-engine' ),
            );

            // Optional additional parameters
            $additional_args = array(
                'cmb_metabox_config' => 'columns_shortcode_button_config',
            );

            // $button = new Shortcode_Button( $button_slug, $js_button_data );
            $button = new Shortcode_Button( $button_slug, $js_button_data, $additional_args );


        }

        function col_shortcode_button_config( $button_data ) {

            return array(
                'id'     => 'shortcode_'. $button_data['slug'],
                'fields' => array(
                    // array(
                    //     'name'    => __( 'Content Alignment', 'mai-pro-engine' ),
                    //     'desc'    => __( 'Horizontally/Vertically align the columns and content.', 'mai-pro-engine' ),
                    //     'id'      => 'align',
                    //     'type'    => 'select',
                    //     'options' => array(
                    //         'left, top'      => __( 'Left/Top', 'mai-pro-engine' ),
                    //         'center, top'    => __( 'Center/Top', 'mai-pro-engine' ),
                    //         'right, top'     => __( 'Right/Top', 'mai-pro-engine' ),
                    //         'left, middle'   => __( 'Left/Middle', 'mai-pro-engine' ),
                    //         'center, middle' => __( 'Center/Middle', 'mai-pro-engine' ),
                    //         'right, middle'  => __( 'Right/Middle', 'mai-pro-engine' ),
                    //         'left, bottom'   => __( 'Left/Bottom', 'mai-pro-engine' ),
                    //         'center, bottom' => __( 'Center/Bottom', 'mai-pro-engine' ),
                    //         'right, bottom'  => __( 'Right/Bottom', 'mai-pro-engine' ),
                    //     ),
                    // ),
                    array(
                        'name'    => __( 'Background Image', 'mai-pro-engine' ),
                        'id'      => 'image',
                        'type'    => 'file',
                        'options' => array(
                            'url' => false,
                        ),
                    ),
                ),
                // keep this w/ a key of 'options-page' and use the button slug as the value
                'show_on' => array( 'key' => 'options-page', 'value' => $button_data['slug'] ),
            );

        }

        function columns_shortcode_button_config( $button_data ) {

            return array(
                'id'     => 'shortcode_'. $button_data['slug'],
                'fields' => array(
                    array(
                        'name'    => __( 'Gutter', 'mai-pro-engine' ),
                        'id'      => 'gutter',
                        'type'    => 'select',
                        'default' => 30,
                        'options' => array(
                            0  => __( 'None', 'mai-pro-engine' ),
                            5  => '5px',
                            10 => '10px',
                            20 => '20px',
                            30 => '30px',
                            40 => '40px',
                            50 => '50px',
                        ),
                    ),
                    // array(
                    //     'name'    => __( 'Content Alignment', 'mai-pro-engine' ),
                    //     'desc'    => __( 'Horizontally/Vertically align the columns and content.', 'mai-pro-engine' ),
                    //     'id'      => 'align',
                    //     'type'    => 'select',
                    //     'options' => array(
                    //         'left, top'      => __( 'Left/Top', 'mai-pro-engine' ),
                    //         'center, top'    => __( 'Center/Top', 'mai-pro-engine' ),
                    //         'right, top'     => __( 'Right/Top', 'mai-pro-engine' ),
                    //         'left, middle'   => __( 'Left/Middle', 'mai-pro-engine' ),
                    //         'center, middle' => __( 'Center/Middle', 'mai-pro-engine' ),
                    //         'right, middle'  => __( 'Right/Middle', 'mai-pro-engine' ),
                    //         'left, bottom'   => __( 'Left/Bottom', 'mai-pro-engine' ),
                    //         'center, bottom' => __( 'Center/Bottom', 'mai-pro-engine' ),
                    //         'right, bottom'  => __( 'Right/Bottom', 'mai-pro-engine' ),
                    //     ),
                    // ),
                ),
                // keep this w/ a key of 'options-page' and use the button slug as the value
                'show_on' => array( 'key' => 'options-page', 'value' => $button_data['slug'] ),
            );

        }

        /**
         * Return CMB2 config array
         *
         * @param   array  $button_data  Array of button data
         *
         * @return  array                CMB2 config array
         */
        function grid_shortcode_button_config( $button_data ) {

            // Post Types
            $post_types = get_post_types( array( 'public' => true, 'publicly_queryable' => true ), 'objects' );
            $post_type_options = array();
            foreach ( $post_types as $post_type ) {
                $post_type_options[$post_type->name] = $post_type->label;
            }

            // Taxos
            $taxos = get_taxonomies( array( 'public' => true ), 'objects' );
            $taxo_options = array();
            foreach ( $taxos as $taxo ) {
                $taxo_options[$taxo->name] = $taxo->label;
            }

            return array(
                'id'     => 'shortcode_'. $button_data['slug'],
                'fields' => array(
                    array(
                        'name'    => __( 'Content', 'mai-pro-engine' ),
                        'default' => 'post',
                        'id'      => 'content',
                        'type'    => 'select',
                        'options' => array_merge( $post_type_options, $taxo_options ),
                    ),
                    array(
                        'name'              => __( 'Show', 'mai-pro-engine' ),
                        'id'                => 'show',
                        'type'              => 'multicheck_inline',
                        'select_all_button' => false,
                        'options'           => array(
                            'title'     => __( 'Title', 'mai-pro-engine' ),
                            'image'     => __( 'Image', 'mai-pro-engine' ),
                            'excerpt'   => __( 'Excerpt', 'mai-pro-engine' ),
                            'content'   => __( 'Content', 'mai-pro-engine' ),
                            'more_link' => __( 'More Link', 'mai-pro-engine' ),
                        ),
                    ),
                    array(
                        'name'    => __( 'Content Alignment', 'mai-pro-engine' ),
                        'desc'    => __( 'Horizontally/Vertically align the columns and content.', 'mai-pro-engine' ),
                        'id'      => 'align',
                        'type'    => 'select',
                        'options' => array(
                            'left, top'      => __( 'Left/Top', 'mai-pro-engine' ),
                            'center, top'    => __( 'Center/Top', 'mai-pro-engine' ),
                            'right, top'     => __( 'Right/Top', 'mai-pro-engine' ),
                            'left, middle'   => __( 'Left/Middle', 'mai-pro-engine' ),
                            'center, middle' => __( 'Center/Middle', 'mai-pro-engine' ),
                            'right, middle'  => __( 'Right/Middle', 'mai-pro-engine' ),
                            'left, bottom'   => __( 'Left/Bottom', 'mai-pro-engine' ),
                            'center, bottom' => __( 'Center/Bottom', 'mai-pro-engine' ),
                            'right, bottom'  => __( 'Right/Bottom', 'mai-pro-engine' ),
                        ),
                    ),
                    array(
                        'name'    => __( 'Image Location', 'mai-pro-engine' ),
                        'id'      => 'image_location',
                        'type'    => 'select',
                        'options' => array(
                            'before_entry' => __( 'Before Entry', 'mai-pro-engine' ),
                            'before_title' => __( 'Before Title', 'mai-pro-engine' ),
                            'after_title'  => __( 'After Title', 'mai-pro-engine' ),
                            'bg'           => __( 'Background', 'mai-pro-engine' ),
                        ),
                    ),
                    array(
                        'name'       => __( 'Columns', 'mai-pro-engine' ),
                        'desc'       => __( 'The number of columns', 'mai-pro-engine' ),
                        'default'    => 3,
                        'id'         => 'columns',
                        'type'       => 'text',
                        'attributes' => array(
                            'class'   => 'cmb2-text-small',
                            'type'    => 'number',
                            'pattern' => '\d*',
                        ),
                    ),
                    array(
                        'name'       => __( 'Number', 'mai-pro-engine' ),
                        'desc'       => __( 'The number of entries', 'mai-pro-engine' ),
                        'default'    => 12,
                        'id'         => 'number',
                        'type'       => 'text',
                        'attributes' => array(
                            'class'   => 'cmb2-text-small',
                            'type'    => 'number',
                            'pattern' => '\d*',
                        ),
                    ),
                    array(
                        'name' => __( 'Slider', 'mai-pro-engine' ),
                        'desc' => __( 'Display as a slider', 'mai-pro-engine' ),
                        'id'   => 'slider',
                        'type' => 'checkbox',
                    ),
                    // Below doesn't work!!!!
                    // array(
                    //     'name'              => __( 'Slider', 'mai-pro-engine' ),
                    //     // 'desc'              => __( 'Display as a slider', 'mai-pro-engine' ),
                    //     'id'                => 'sliders',
                    //     'type'              => 'multicheck_inline',
                    //     'select_all_button' => false,
                    //     'options'           => array(
                    //         true => __( 'Display as a slider', 'mai-pro-engine' ),
                    //     ),
                    // ),
                ),
                // keep this w/ a key of 'options-page' and use the button slug as the value
                'show_on' => array( 'key' => 'options-page', 'value' => $button_data['slug'] ),
            );


        }

        // add_filter( 'grid_shortcode_fields', function( $values, $this ) {
        //     // trace( absint( filter_var( $values['slider'][0], FILTER_VALIDATE_BOOLEAN ) ) );
        //     // trace( $values['slider'] );
        //     // if ( isset( $values['slider'] ) && '1' == $values['slider'] ) {
        //         $values['slider'] = absint( filter_var( $values['slider'][0], FILTER_VALIDATE_BOOLEAN ) );
        //     // }
        //     return $values;
        // }, 10, 2 );

        // Filter the rendered shortcode and show a simple block
        add_filter( 'shortcode_button_parse_mce_view_before_send_grid', function( $send ) {
            return '<div class="grid-shortcode" style="background-color:#f7f7f7;text-align:left;padding:24px;"><span class="dashicons dashicons-grid-view" style="display:inline-block;width:auto;height:auto;vertical-align:middle;font-size:36px;margin-right:8px;"></span> <strong>Grid Content</strong></div>';
        });

    }

    function deactivate_plugin() {
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }

    function admin_notices() {
        printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', __( '<strong>Your theme does not support the Mai Pro Engine plugin</strong>. As a result, this plugin has been deactivated.', 'mai-pro-engine' ) );
        // Remove "Plugin activated" notice
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
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
