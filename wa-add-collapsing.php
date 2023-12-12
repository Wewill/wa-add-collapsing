<?php

/*
Plugin Name: WA Add Collapsing Taxonomies UI
Description: Add a feature to make any hierarchical taxonomy collapsable on the admin screen
Version: 1.0
 * Author:            Wilhem Arnoldy ( based on Advanced woo search )
 * Author URI:        https://www.wilhemarnoldy.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wa-adco
 * Domain Path:       /languages
*/


if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !is_admin() && !function_exists('rwmb_meta') ) {
	wp_die('Error : please install Meta Box plugin.');
}

if ( !is_admin() && !function_exists('mb_settings_page_load') ) {
	wp_die('Error : please install Meta Box Settings plugin.');
}

if ( !class_exists( 'Add_Collapsing_Tax' ) ) :

    /**
     * Main plugin class
     *
     * @class Add_Collapsing_Tax
     */
    final class Add_Collapsing_Tax {

        /**
         * @var Add_Collapsing_Tax The single instance of the class
         */
        protected static $_instance = null;

        /**
         * @var Add_Collapsing_Tax Array of taxonomies to implement folded view
         */
        protected $default_taxonomies = array();
        protected $default_posts = array();
        protected $taxonomies = array();
        protected $posts = array();

        /**
         * Main Add_Collapsing_Tax Instance
         *
         * Ensures only one instance of Add_Collapsing_Tax is loaded or can be loaded.
         *
         * @static
         * @return Add_Collapsing_Tax - Main instance
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Constructor
         */
        public function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

            // Add a page in settings
            add_filter( 'mb_settings_pages', array( $this, 'add_setting_page' ) );
            add_filter( 'rwmb_meta_boxes',  array( $this, 'add_custom_fields_to_setting_page' ) );

            // @TODO language
        }

        /*
         * Load assets for search form
         */
        public function load_scripts() {
            global $pagenow;

            // Get settings
            $this->taxonomies   = array_unique(array_merge($this->default_taxonomies, $this->get_taxonomies_from_setting_page()), SORT_REGULAR);
            $this->posts        = array_unique(array_merge($this->default_posts, $this->get_posts_from_setting_page()), SORT_REGULAR);

            // Init scripts & styles
            if ( 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && array_search( $_GET['taxonomy'], $this->taxonomies) !== false && !isset( $_GET['s'] ) && !isset( $_GET['orderby'] ) ) {
                wp_enqueue_style( 'add-collapsing-tax', plugin_dir_url( __FILE__ ) . '/css/add-collapsing-tax.css', array(), '1.0' );
                wp_enqueue_script( 'add-collapsing-tax', plugin_dir_url( __FILE__ ) . '/js/add-collapsing-tax.js', array('jquery'), '1.0', true );
            }
            if ( 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && array_search( $_GET['post_type'], $this->posts) !== false && !isset( $_GET['s'] ) && !isset( $_GET['orderby'] ) ) {
                wp_enqueue_style( 'add-collapsing-post', plugin_dir_url( __FILE__ ) . '/css/add-collapsing-post.css', array(), '1.0' );
                wp_enqueue_script( 'add-collapsing-post', plugin_dir_url( __FILE__ ) . '/js/add-collapsing-post.js', array('jquery'), '1.0', true );
            }
        }

        /*
         * Settings
         */ 
        public function add_setting_page( $settings_pages ) {
            $settings_pages[] = [
                'menu_title' => __( 'Collapsing Taxonomies', 'wa-rsfp' ),
                'id'         => 'collapsing-taxonomies',
                'parent'     => 'options-general.php',
                'class'      => 'custom_css',
                'style'      => 'no-boxes',
                // 'message'    => __( 'Custom message', 'wa-rsfp' ), // Saved custom message
                'customizer' => true,
                'icon_url'   => 'dashicons-admin-generic',
            ];
        
            return $settings_pages;
        }

        public function add_custom_fields_to_setting_page( $meta_boxes ) {
            $prefix = 'waadco_';
        
            $meta_boxes[] = [
                'id'             => 'collapsing-taxonomies-fields',
                'settings_pages' => ['collapsing-taxonomies'],
                'fields'         => [
                    [
                        'name'            => __( 'Allowed taxonomy.ies', 'wa-adco' ),
                        'id'              => $prefix . 'allowed_taxonomy',
                        'type'            => 'checkbox_list',
                        'inline'          => true,
                        'select_all_none' => true,
                        'options'         => $this->taxonomies_options_callback(),
                    ],
                ],
            ];

            $meta_boxes[] = [
                'id'             => 'collapsing-posts-fields',
                'settings_pages' => ['collapsing-taxonomies'],
                'fields'         => [
                    [
                        'name'            => __( 'Allowed post type.s', 'wa-adco' ),
                        'id'              => $prefix . 'allowed_post',
                        'type'            => 'checkbox_list',
                        'inline'          => true,
                        'select_all_none' => true,
                        'options'         => $this->posts_options_callback(),
                    ],
                ],
            ];
        
            return $meta_boxes;
        }

        public function taxonomies_options_callback() {
            return get_taxonomies();
        }
        
        public function posts_options_callback() {
            return get_post_types();
        }

        public function get_taxonomies_from_setting_page() {
            return rwmb_meta( 'waadco_allowed_taxonomy', [ 'object_type' => 'setting' ], 'collapsing-taxonomies' );
        }

        public function get_posts_from_setting_page() {
            return rwmb_meta( 'waadco_allowed_post', [ 'object_type' => 'setting' ], 'collapsing-taxonomies' );
        }

    }

endif;
Add_Collapsing_Tax::instance();