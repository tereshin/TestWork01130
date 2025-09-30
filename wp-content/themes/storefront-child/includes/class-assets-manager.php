<?php
/**
 * Assets Manager
 * 
 * Handles all CSS and JavaScript enqueuing
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Storefront_Child_Assets_Manager
 * 
 * Manages theme assets (scripts and styles)
 */
class Storefront_Child_Assets_Manager {
    
    /**
     * Singleton instance
     *
     * @var Storefront_Child_Assets_Manager
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     *
     * @return Storefront_Child_Assets_Manager
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Enqueue frontend assets
     *
     * @return void
     */
    public function enqueue_frontend_assets() {
        // Enqueue parent theme styles
        wp_enqueue_style(
            'storefront-style',
            get_template_directory_uri() . '/style.css',
            array(),
            wp_get_theme(get_template())->get('Version')
        );
        
        // Enqueue child theme styles
        wp_enqueue_style(
            'storefront-child-style',
            get_stylesheet_directory_uri() . '/style.css',
            array('storefront-style'),
            STOREFRONT_CHILD_VERSION
        );
        
        // Enqueue AJAX search script
        wp_enqueue_script(
            'storefront-child-ajax',
            STOREFRONT_CHILD_ASSETS . '/js/ajax-search.js',
            array('jquery'),
            STOREFRONT_CHILD_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('storefront-child-ajax', 'storefrontChildAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('storefront_child_nonce'),
            'i18n'    => array(
                'searching'     => __('Searching...', 'storefront-child'),
                'noResults'     => __('No results found', 'storefront-child'),
                'error'         => __('An error occurred', 'storefront-child'),
                'minChars'      => __('Please enter at least 2 characters', 'storefront-child'),
                'loading'       => __('Loading...', 'storefront-child'),
            ),
        ));
    }
    
    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook
     * @return void
     */
    public function enqueue_admin_assets($hook) {
        // Enqueue for widget pages
        if ($hook === 'widgets.php' || $hook === 'customize.php') {
            $this->enqueue_widget_assets();
        }
        
        // Enqueue for city edit pages
        if (($hook === 'post-new.php' || $hook === 'post.php')) {
            global $post;
            if ($post && $post->post_type === 'cities') {
                $this->enqueue_city_edit_assets($post);
            }
        }
    }
    
    /**
     * Enqueue assets for widget pages
     *
     * @return void
     */
    private function enqueue_widget_assets() {
        wp_enqueue_script(
            'storefront-child-ajax',
            STOREFRONT_CHILD_ASSETS . '/js/ajax-search.js',
            array('jquery'),
            STOREFRONT_CHILD_VERSION,
            true
        );
        
        wp_localize_script('storefront-child-ajax', 'storefrontChildAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('storefront_child_nonce'),
        ));
    }
    
    /**
     * Enqueue assets for city edit pages
     *
     * @param WP_Post $post Current post object
     * @return void
     */
    private function enqueue_city_edit_assets($post) {
        // Leaflet CSS
        wp_enqueue_style(
            'leaflet-css',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            array(),
            '1.9.4'
        );
        
        // Custom admin CSS for coordinates
        wp_enqueue_style(
            'storefront-child-admin-coordinates',
            STOREFRONT_CHILD_ASSETS . '/css/admin-coordinates.css',
            array('leaflet-css'),
            STOREFRONT_CHILD_VERSION
        );
        
        // Leaflet JS
        wp_enqueue_script(
            'leaflet-js',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            array(),
            '1.9.4',
            true
        );
        
        // Custom coordinates map script
        wp_enqueue_script(
            'city-coordinates-map',
            STOREFRONT_CHILD_ASSETS . '/js/city-coordinates-map.js',
            array('jquery', 'leaflet-js'),
            STOREFRONT_CHILD_VERSION,
            true
        );
        
        // Localize script with current coordinates
        wp_localize_script('city-coordinates-map', 'cityCoordinatesData', array(
            'latitude'  => get_post_meta($post->ID, '_city_latitude', true),
            'longitude' => get_post_meta($post->ID, '_city_longitude', true),
        ));
    }
}
