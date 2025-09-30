<?php
/**
 * Custom Hooks Examples
 * 
 * Add this code to your child theme's functions.php or a custom plugin
 * to demonstrate the custom action hooks provided by the theme.
 */

/**
 * Example: Add content before the cities table
 */
function add_custom_content_before_cities_table() {
    echo '<div class="custom-notice" style="background: #e1f5fe; padding: 15px; margin-bottom: 20px; border-left: 4px solid #0277bd;">';
    echo '<h3>Welcome to the Cities Weather Portal</h3>';
    echo '<p>This page displays real-time weather information for cities around the world. Use the search function to find specific cities or browse the complete table below.</p>';
    echo '</div>';
}
add_action('storefront_child_before_cities_table', 'add_custom_content_before_cities_table');

/**
 * Example: Add content after the cities table
 */
function add_custom_content_after_cities_table() {
    echo '<div class="custom-footer" style="background: #f3e5f5; padding: 15px; margin-top: 20px; border-left: 4px solid #7b1fa2;">';
    echo '<h4>Weather Data Information</h4>';
    echo '<p><strong>Data Source:</strong> Weather information is provided by OpenWeatherMap API.</p>';
    echo '<p><strong>Update Frequency:</strong> Weather data is fetched in real-time when viewing this page.</p>';
    echo '<p><strong>Coordinates:</strong> City coordinates are used to fetch accurate weather data for each location.</p>';
    echo '<p><em>Note: Weather data availability depends on the OpenWeatherMap API service and requires a valid API key.</em></p>';
    echo '</div>';
}
add_action('storefront_child_after_cities_table', 'add_custom_content_after_cities_table');

/**
 * Example: Add a custom admin notice for API key setup
 */
function cities_weather_admin_notice() {
    $api_key = get_option('openweather_api_key', '');
    
    if (empty($api_key) && current_user_can('manage_options')) {
        $settings_url = admin_url('options-general.php?page=weather-settings');
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>Cities Weather Theme:</strong> Please configure your OpenWeatherMap API key in ';
        echo '<a href="' . esc_url($settings_url) . '">Weather Settings</a> to enable weather data.</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'cities_weather_admin_notice');

/**
 * Example: Custom shortcode to display a random city weather
 */
function random_city_weather_shortcode($atts) {
    $cities = get_posts(array(
        'post_type' => 'cities',
        'post_status' => 'publish',
        'numberposts' => 1,
        'orderby' => 'rand'
    ));
    
    if (empty($cities)) {
        return '<p>No cities available.</p>';
    }
    
    $city = $cities[0];
    $latitude = get_post_meta($city->ID, '_city_latitude', true);
    $longitude = get_post_meta($city->ID, '_city_longitude', true);
    
    $output = '<div class="random-city-weather" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px;">';
    $output .= '<h4>Featured City: ' . esc_html($city->post_title) . '</h4>';
    
    if (!empty($latitude) && !empty($longitude)) {
        $weather = get_weather_data($latitude, $longitude);
        if (!isset($weather['error'])) {
            $output .= '<p><strong>Current Temperature:</strong> ' . $weather['temperature'] . '°C</p>';
            $output .= '<p><strong>Conditions:</strong> ' . ucfirst($weather['description']) . '</p>';
        } else {
            $output .= '<p>Weather data not available.</p>';
        }
    } else {
        $output .= '<p>Coordinates not set for this city.</p>';
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('random_city_weather', 'random_city_weather_shortcode');

/**
 * Example: Add custom meta to cities in REST API
 */
function add_cities_meta_to_rest_api() {
    register_rest_field('cities', 'coordinates', array(
        'get_callback' => function($post) {
            return array(
                'latitude' => get_post_meta($post['id'], '_city_latitude', true),
                'longitude' => get_post_meta($post['id'], '_city_longitude', true)
            );
        },
        'schema' => array(
            'description' => 'City coordinates',
            'type' => 'object'
        )
    ));
}
add_action('rest_api_init', 'add_cities_meta_to_rest_api');