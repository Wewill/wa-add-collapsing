<?php

/*
Plugin Name: WA Add Collapsing Taxonomies UI
Description: Add a feature to make any hierarchical taxonomy collapsable on the admin screen
Version: 1.0
 * Author:            Wilhem Arnoldy ( based on Advanced woo search )
 * Author URI:        https://www.wilhemarnoldy.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wa-rsfp
 * Domain Path:       /languages
*/


if ( !defined( 'ABSPATH' ) ) {
    exit;
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
        protected $taxonomies = array( 'category', 'production', 'thematic' );
        protected $posts = array( 'page' );

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
        }

        /*
         * Load assets for search form
         */
        public function load_scripts() {
            global $pagenow;
            if ( 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && array_search( $_GET['taxonomy'], $this->taxonomies) !== false && !isset( $_GET['s'] ) && !isset( $_GET['orderby'] ) ) {
                wp_enqueue_style( 'add-collapsing-tax', plugin_dir_url( __FILE__ ) . '/css/add-collapsing-tax.css', array(), '1.0' );
                wp_enqueue_script( 'add-collapsing-tax', plugin_dir_url( __FILE__ ) . '/js/add-collapsing-tax.js', array('jquery'), '1.0', true );
            }
            if ( 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && array_search( $_GET['post_type'], $this->posts) !== false && !isset( $_GET['s'] ) && !isset( $_GET['orderby'] ) ) {
                wp_enqueue_style( 'add-collapsing-post', plugin_dir_url( __FILE__ ) . '/css/add-collapsing-post.css', array(), '1.0' );
                wp_enqueue_script( 'add-collapsing-post', plugin_dir_url( __FILE__ ) . '/js/add-collapsing-post.js', array('jquery'), '1.0', true );
            }
        }

    }

endif;
Add_Collapsing_Tax::instance();