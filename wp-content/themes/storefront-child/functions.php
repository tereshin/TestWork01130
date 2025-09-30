<?php
/**
 * Storefront Child Theme Functions
 * 
 * Modern, modular architecture for the child theme.
 * This file serves as the entry point for all theme functionality.
 *
 * Features:
 * - Custom Post Types (Cities)
 * - Custom Taxonomies (Countries)
 * - Weather Widget with AJAX
 * - City coordinates with interactive map
 * - OpenWeatherMap API integration
 * - Fully documented and optimized code
 *
 * @package StorefrontChild
 * @since 1.0.0
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize the theme
 * 
 * Includes the main theme setup class and initializes all components
 */
require_once get_stylesheet_directory() . '/includes/class-theme-setup.php';

// Initialize theme
Storefront_Child_Theme_Setup::get_instance();

/**
 * Theme activation hook
 * 
 * Flush rewrite rules on theme activation to ensure custom post types work
 *
 * @return void
 */
function storefront_child_activation() {
    // Register post types
    if (class_exists('Storefront_Child_Post_Types')) {
        $post_types = Storefront_Child_Post_Types::get_instance();
        $post_types->register_post_types();
        $post_types->register_taxonomies();
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'storefront_child_activation');

/**
 * Theme deactivation hook
 * 
 * Flush rewrite rules on theme deactivation
 *
 * @return void
 */
function storefront_child_deactivation() {
    flush_rewrite_rules();
}
add_action('switch_theme', 'storefront_child_deactivation');

/**
 * Helper function to get weather data
 * 
 * Wrapper function for backward compatibility
 *
 * @param float $latitude Latitude coordinate
 * @param float $longitude Longitude coordinate
 * @return array Weather data
 */
function get_weather_data($latitude, $longitude) {
    return Storefront_Child_Weather_API::get_weather_data($latitude, $longitude);
}

/**
 * Clear all caches
 * 
 * Clears cities and weather caches
 *
 * @return void
 */
function storefront_child_clear_caches() {
    Storefront_Child_Cities_Query::clear_cache();
    Storefront_Child_Weather_API::clear_cache();
}

/**
 * Clear caches when a city is updated
 */
add_action('save_post_cities', 'storefront_child_clear_caches');
add_action('edited_countries', 'storefront_child_clear_caches');
add_action('create_countries', 'storefront_child_clear_caches');
add_action('delete_countries', 'storefront_child_clear_caches');
