<?php
/**
 * Post Types Registration
 * 
 * Handles custom post types and taxonomies registration
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Storefront_Child_Post_Types
 * 
 * Registers custom post types and taxonomies
 */
class Storefront_Child_Post_Types {
    
    /**
     * Singleton instance
     *
     * @var Storefront_Child_Post_Types
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     *
     * @return Storefront_Child_Post_Types
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
        add_action('init', array($this, 'register_post_types'), 0);
        add_action('init', array($this, 'register_taxonomies'), 0);
    }
    
    /**
     * Register custom post types
     *
     * @return void
     */
    public function register_post_types() {
        $this->register_cities_post_type();
    }
    
    /**
     * Register Cities post type
     *
     * @return void
     */
    private function register_cities_post_type() {
        $labels = array(
            'name'                  => __('Cities', 'storefront-child'),
            'singular_name'         => __('City', 'storefront-child'),
            'menu_name'             => __('Cities', 'storefront-child'),
            'name_admin_bar'        => __('City', 'storefront-child'),
            'archives'              => __('City Archives', 'storefront-child'),
            'attributes'            => __('City Attributes', 'storefront-child'),
            'parent_item_colon'     => __('Parent City:', 'storefront-child'),
            'all_items'             => __('All Cities', 'storefront-child'),
            'add_new_item'          => __('Add New City', 'storefront-child'),
            'add_new'               => __('Add New', 'storefront-child'),
            'new_item'              => __('New City', 'storefront-child'),
            'edit_item'             => __('Edit City', 'storefront-child'),
            'update_item'           => __('Update City', 'storefront-child'),
            'view_item'             => __('View City', 'storefront-child'),
            'view_items'            => __('View Cities', 'storefront-child'),
            'search_items'          => __('Search Cities', 'storefront-child'),
            'not_found'             => __('Not found', 'storefront-child'),
            'not_found_in_trash'    => __('Not found in Trash', 'storefront-child'),
            'featured_image'        => __('Featured Image', 'storefront-child'),
            'set_featured_image'    => __('Set featured image', 'storefront-child'),
            'remove_featured_image' => __('Remove featured image', 'storefront-child'),
            'use_featured_image'    => __('Use as featured image', 'storefront-child'),
            'insert_into_item'      => __('Insert into city', 'storefront-child'),
            'uploaded_to_this_item' => __('Uploaded to this city', 'storefront-child'),
            'items_list'            => __('Cities list', 'storefront-child'),
            'items_list_navigation' => __('Cities list navigation', 'storefront-child'),
            'filter_items_list'     => __('Filter cities list', 'storefront-child'),
        );
        
        $args = array(
            'label'                 => __('City', 'storefront-child'),
            'description'           => __('Cities with coordinates for weather data', 'storefront-child'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail'),
            'taxonomies'            => array('countries'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-location-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );
        
        register_post_type('cities', $args);
    }
    
    /**
     * Register custom taxonomies
     *
     * @return void
     */
    public function register_taxonomies() {
        $this->register_countries_taxonomy();
    }
    
    /**
     * Register Countries taxonomy
     *
     * @return void
     */
    private function register_countries_taxonomy() {
        $labels = array(
            'name'                       => __('Countries', 'storefront-child'),
            'singular_name'              => __('Country', 'storefront-child'),
            'menu_name'                  => __('Countries', 'storefront-child'),
            'all_items'                  => __('All Countries', 'storefront-child'),
            'parent_item'                => __('Parent Country', 'storefront-child'),
            'parent_item_colon'          => __('Parent Country:', 'storefront-child'),
            'new_item_name'              => __('New Country Name', 'storefront-child'),
            'add_new_item'               => __('Add New Country', 'storefront-child'),
            'edit_item'                  => __('Edit Country', 'storefront-child'),
            'update_item'                => __('Update Country', 'storefront-child'),
            'view_item'                  => __('View Country', 'storefront-child'),
            'separate_items_with_commas' => __('Separate countries with commas', 'storefront-child'),
            'add_or_remove_items'        => __('Add or remove countries', 'storefront-child'),
            'choose_from_most_used'      => __('Choose from the most used', 'storefront-child'),
            'popular_items'              => __('Popular Countries', 'storefront-child'),
            'search_items'               => __('Search Countries', 'storefront-child'),
            'not_found'                  => __('Not Found', 'storefront-child'),
            'no_terms'                   => __('No countries', 'storefront-child'),
            'items_list'                 => __('Countries list', 'storefront-child'),
            'items_list_navigation'      => __('Countries list navigation', 'storefront-child'),
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        );
        
        register_taxonomy('countries', array('cities'), $args);
    }
}
