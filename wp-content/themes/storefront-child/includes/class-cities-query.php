<?php
/**
 * Cities Query Handler
 * 
 * Centralized database queries for cities
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Storefront_Child_Cities_Query
 * 
 * Handles all database queries related to cities
 */
class Storefront_Child_Cities_Query {
    
    /**
     * Cache group name
     *
     * @var string
     */
    const CACHE_GROUP = 'storefront_child_cities';
    
    /**
     * Cache expiration time (1 hour)
     *
     * @var int
     */
    const CACHE_EXPIRATION = 3600;
    
    /**
     * Get all cities with their coordinates and countries
     *
     * @param string $search_term Optional search term
     * @return array Array of city objects
     */
    public static function get_cities_with_data($search_term = '') {
        global $wpdb;
        
        // Generate cache key
        $cache_key = 'cities_data_' . md5($search_term);
        
        // Try to get from cache
        $cached = wp_cache_get($cache_key, self::CACHE_GROUP);
        if (false !== $cached) {
            return $cached;
        }
        
        $where = "WHERE p.post_type = 'cities' AND p.post_status = 'publish'";
        
        if (!empty($search_term)) {
            $where .= $wpdb->prepare(" AND p.post_title LIKE %s", '%' . $wpdb->esc_like($search_term) . '%');
        }
        
        $query = "
            SELECT DISTINCT 
                p.ID, 
                p.post_title AS city_name, 
                pm1.meta_value AS latitude, 
                pm2.meta_value AS longitude,
                GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') AS countries
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_city_latitude'
            LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_city_longitude'
            LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'countries'
            LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            {$where}
            GROUP BY p.ID
            ORDER BY p.post_title ASC
        ";
        
        $results = $wpdb->get_results($query);
        
        // Cache the results
        wp_cache_set($cache_key, $results, self::CACHE_GROUP, self::CACHE_EXPIRATION);
        
        return $results;
    }
    
    /**
     * Get single city data by ID
     *
     * @param int $city_id City post ID
     * @return object|null City object or null if not found
     */
    public static function get_city_by_id($city_id) {
        $city_id = absint($city_id);
        
        if (!$city_id) {
            return null;
        }
        
        // Generate cache key
        $cache_key = 'city_' . $city_id;
        
        // Try to get from cache
        $cached = wp_cache_get($cache_key, self::CACHE_GROUP);
        if (false !== $cached) {
            return $cached;
        }
        
        $city = get_post($city_id);
        
        if (!$city || $city->post_type !== 'cities') {
            return null;
        }
        
        $city_data = (object) array(
            'ID'        => $city->ID,
            'city_name' => $city->post_title,
            'latitude'  => get_post_meta($city_id, '_city_latitude', true),
            'longitude' => get_post_meta($city_id, '_city_longitude', true),
            'countries' => self::get_city_countries($city_id),
        );
        
        // Cache the result
        wp_cache_set($cache_key, $city_data, self::CACHE_GROUP, self::CACHE_EXPIRATION);
        
        return $city_data;
    }
    
    /**
     * Get city countries as comma-separated string
     *
     * @param int $city_id City post ID
     * @return string Countries string
     */
    public static function get_city_countries($city_id) {
        $terms = get_the_terms($city_id, 'countries');
        
        if (!$terms || is_wp_error($terms)) {
            return '';
        }
        
        $country_names = array_map(function($term) {
            return $term->name;
        }, $terms);
        
        return implode(', ', $country_names);
    }
    
    /**
     * Clear cities cache
     *
     * @return void
     */
    public static function clear_cache() {
        wp_cache_flush_group(self::CACHE_GROUP);
    }
    
    /**
     * Get cities for dropdown/select options
     *
     * @return array Array of city objects for select options
     */
    public static function get_cities_for_select() {
        $cities = get_posts(array(
            'post_type'   => 'cities',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby'     => 'title',
            'order'       => 'ASC',
        ));
        
        return $cities;
    }
}
