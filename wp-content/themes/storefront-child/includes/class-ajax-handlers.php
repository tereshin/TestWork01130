<?php
/**
 * AJAX Handlers
 * 
 * Handles all AJAX requests
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Storefront_Child_Ajax_Handlers
 * 
 * Manages all AJAX callbacks
 */
class Storefront_Child_Ajax_Handlers {
    
    /**
     * Singleton instance
     *
     * @var Storefront_Child_Ajax_Handlers
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     *
     * @return Storefront_Child_Ajax_Handlers
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
        // Search cities AJAX
        add_action('wp_ajax_search_cities', array($this, 'handle_search_cities'));
        add_action('wp_ajax_nopriv_search_cities', array($this, 'handle_search_cities'));
        
        // Get city weather AJAX
        add_action('wp_ajax_get_city_weather', array($this, 'handle_get_city_weather'));
        add_action('wp_ajax_nopriv_get_city_weather', array($this, 'handle_get_city_weather'));
    }
    
    /**
     * Handle city search AJAX request
     *
     * @return void
     */
    public function handle_search_cities() {
        // Verify nonce
        if (!check_ajax_referer('storefront_child_nonce', 'nonce', false)) {
            wp_send_json_error(array(
                'message' => __('Security check failed', 'storefront-child'),
            ));
        }
        
        // Get and sanitize search term
        $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
        
        if (empty($search_term) || strlen($search_term) < 2) {
            wp_send_json_error(array(
                'message' => __('Search term must be at least 2 characters', 'storefront-child'),
            ));
        }
        
        // Get cities data
        $cities = Storefront_Child_Cities_Query::get_cities_with_data($search_term);
        
        if (empty($cities)) {
            wp_send_json_success(array());
            return;
        }
        
        // Process results with weather data
        $results = array();
        foreach ($cities as $city) {
            $weather_data = array();
            
            if (!empty($city->latitude) && !empty($city->longitude)) {
                $weather_data = Storefront_Child_Weather_API::get_weather_data(
                    $city->latitude,
                    $city->longitude
                );
            }
            
            $results[] = array(
                'id'          => $city->ID,
                'name'        => $city->city_name,
                'countries'   => $city->countries ?: __('No country assigned', 'storefront-child'),
                'latitude'    => $city->latitude,
                'longitude'   => $city->longitude,
                'temperature' => isset($weather_data['temperature']) 
                    ? $weather_data['temperature'] . '°C' 
                    : __('N/A', 'storefront-child'),
            );
        }
        
        wp_send_json_success($results);
    }
    
    /**
     * Handle get city weather AJAX request
     *
     * @return void
     */
    public function handle_get_city_weather() {
        // Verify nonce
        if (!check_ajax_referer('storefront_child_nonce', 'nonce', false)) {
            wp_send_json_error(array(
                'message' => __('Security check failed', 'storefront-child'),
            ));
        }
        
        // Get and validate city ID
        $city_id = isset($_POST['city_id']) ? absint($_POST['city_id']) : 0;
        
        if (!$city_id) {
            wp_send_json_error(array(
                'message' => __('Invalid city ID', 'storefront-child'),
            ));
        }
        
        // Get city data
        $city = Storefront_Child_Cities_Query::get_city_by_id($city_id);
        
        if (!$city) {
            wp_send_json_error(array(
                'message' => __('City not found', 'storefront-child'),
            ));
        }
        
        // Prepare response data
        $weather_data = array(
            'city_name'   => $city->city_name,
            'temperature' => __('N/A', 'storefront-child'),
            'description' => '',
            'humidity'    => '',
        );
        
        // Get weather data if coordinates are available
        if (!empty($city->latitude) && !empty($city->longitude)) {
            $weather = Storefront_Child_Weather_API::get_weather_data(
                $city->latitude,
                $city->longitude
            );
            
            if (!isset($weather['error'])) {
                $weather_data['temperature'] = $weather['temperature'] . '°C';
                $weather_data['description'] = ucfirst($weather['description']);
                $weather_data['humidity']    = $weather['humidity'];
            }
        }
        
        wp_send_json_success($weather_data);
    }
}
