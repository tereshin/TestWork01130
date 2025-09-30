<?php
/**
 * Weather API Handler
 * 
 * Handles all interactions with OpenWeatherMap API
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Storefront_Child_Weather_API
 * 
 * Manages weather data fetching and caching
 */
class Storefront_Child_Weather_API {
    
    /**
     * API base URL
     *
     * @var string
     */
    const API_BASE_URL = 'https://api.openweathermap.org/data/2.5/weather';
    
    /**
     * Cache group name
     *
     * @var string
     */
    const CACHE_GROUP = 'storefront_child_weather';
    
    /**
     * Cache expiration time (30 minutes)
     *
     * @var int
     */
    const CACHE_EXPIRATION = 1800;
    
    /**
     * Get weather data for coordinates
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @return array Weather data or error
     */
    public static function get_weather_data($latitude, $longitude) {
        // Validate coordinates
        if (!self::validate_coordinates($latitude, $longitude)) {
            return array('error' => __('Invalid coordinates provided', 'storefront-child'));
        }
        
        // Generate cache key
        $cache_key = 'weather_' . md5($latitude . '_' . $longitude);
        
        // Try to get from cache
        $cached = wp_cache_get($cache_key, self::CACHE_GROUP);
        if (false !== $cached) {
            return $cached;
        }
        
        // Get API key
        $api_key = get_option('openweather_api_key', '');
        
        if (empty($api_key)) {
            return array('error' => __('OpenWeatherMap API key not configured', 'storefront-child'));
        }
        
        // Build API URL
        $url = add_query_arg(array(
            'lat'   => $latitude,
            'lon'   => $longitude,
            'appid' => $api_key,
            'units' => 'metric',
        ), self::API_BASE_URL);
        
        // Fetch data
        $response = wp_remote_get($url, array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/json',
            ),
        ));
        
        // Handle errors
        if (is_wp_error($response)) {
            return array('error' => __('Failed to fetch weather data', 'storefront-child'));
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return array('error' => sprintf(__('API returned error code: %d', 'storefront-child'), $response_code));
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Parse weather data
        $weather_data = self::parse_weather_data($data);
        
        // Cache the result
        wp_cache_set($cache_key, $weather_data, self::CACHE_GROUP, self::CACHE_EXPIRATION);
        
        return $weather_data;
    }
    
    /**
     * Parse API response into structured data
     *
     * @param array $data Raw API response
     * @return array Parsed weather data
     */
    private static function parse_weather_data($data) {
        if (!isset($data['main']['temp'])) {
            return array('error' => __('Invalid weather data received', 'storefront-child'));
        }
        
        return array(
            'temperature' => round($data['main']['temp']),
            'description' => isset($data['weather'][0]['description']) ? $data['weather'][0]['description'] : '',
            'humidity'    => isset($data['main']['humidity']) ? $data['main']['humidity'] : 0,
            'pressure'    => isset($data['main']['pressure']) ? $data['main']['pressure'] : 0,
            'wind_speed'  => isset($data['wind']['speed']) ? $data['wind']['speed'] : 0,
            'icon'        => isset($data['weather'][0]['icon']) ? $data['weather'][0]['icon'] : '',
        );
    }
    
    /**
     * Validate coordinates
     *
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @return bool True if valid
     */
    private static function validate_coordinates($latitude, $longitude) {
        return is_numeric($latitude) 
            && is_numeric($longitude)
            && $latitude >= -90 
            && $latitude <= 90
            && $longitude >= -180 
            && $longitude <= 180;
    }
    
    /**
     * Clear weather cache
     *
     * @return void
     */
    public static function clear_cache() {
        wp_cache_flush_group(self::CACHE_GROUP);
    }
    
    /**
     * Get weather icon URL
     *
     * @param string $icon_code Icon code from API
     * @return string Icon URL
     */
    public static function get_icon_url($icon_code) {
        if (empty($icon_code)) {
            return '';
        }
        
        return sprintf('https://openweathermap.org/img/wn/%s@2x.png', $icon_code);
    }
}
