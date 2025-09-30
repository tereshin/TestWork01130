<?php
/**
 * Sample Data for Cities and Countries
 * 
 * Run this file once to populate the database with sample cities and countries.
 * Access via: yoursite.com/wp-content/themes/storefront-child/sample-data/populate-sample-data.php
 * 
 * Make sure to delete this file after use for security reasons.
 */

// Include WordPress
require_once('../../../../wp-load.php');

if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}

// Sample countries data
$countries = array(
    'United States',
    'Canada', 
    'United Kingdom',
    'France',
    'Germany',
    'Japan',
    'Australia',
    'Brazil',
    'India',
    'China'
);

// Sample cities data with coordinates
$cities = array(
    array(
        'name' => 'New York',
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'country' => 'United States'
    ),
    array(
        'name' => 'London',
        'latitude' => 51.5074,
        'longitude' => -0.1278,
        'country' => 'United Kingdom'
    ),
    array(
        'name' => 'Paris',
        'latitude' => 48.8566,
        'longitude' => 2.3522,
        'country' => 'France'
    ),
    array(
        'name' => 'Tokyo',
        'latitude' => 35.6762,
        'longitude' => 139.6503,
        'country' => 'Japan'
    ),
    array(
        'name' => 'Sydney',
        'latitude' => -33.8688,
        'longitude' => 151.2093,
        'country' => 'Australia'
    ),
    array(
        'name' => 'Toronto',
        'latitude' => 43.6532,
        'longitude' => -79.3832,
        'country' => 'Canada'
    ),
    array(
        'name' => 'Berlin',
        'latitude' => 52.5200,
        'longitude' => 13.4050,
        'country' => 'Germany'
    ),
    array(
        'name' => 'São Paulo',
        'latitude' => -23.5505,
        'longitude' => -46.6333,
        'country' => 'Brazil'
    ),
    array(
        'name' => 'Mumbai',
        'latitude' => 19.0760,
        'longitude' => 72.8777,
        'country' => 'India'
    ),
    array(
        'name' => 'Beijing',
        'latitude' => 39.9042,
        'longitude' => 116.4074,
        'country' => 'China'
    )
);

echo '<h1>Populating Sample Data...</h1>';

// Create countries
echo '<h2>Creating Countries:</h2>';
$country_term_ids = array();

foreach ($countries as $country) {
    $term = wp_insert_term($country, 'countries');
    if (!is_wp_error($term)) {
        $country_term_ids[$country] = $term['term_id'];
        echo '<p>✓ Created country: ' . $country . '</p>';
    } else {
        echo '<p>✗ Error creating country: ' . $country . ' - ' . $term->get_error_message() . '</p>';
    }
}

// Create cities
echo '<h2>Creating Cities:</h2>';

foreach ($cities as $city_data) {
    $city_post = array(
        'post_title' => $city_data['name'],
        'post_content' => 'Sample city data for ' . $city_data['name'],
        'post_status' => 'publish',
        'post_type' => 'cities'
    );
    
    $city_id = wp_insert_post($city_post);
    
    if ($city_id && !is_wp_error($city_id)) {
        // Add coordinates
        update_post_meta($city_id, '_city_latitude', $city_data['latitude']);
        update_post_meta($city_id, '_city_longitude', $city_data['longitude']);
        
        // Assign country
        if (isset($country_term_ids[$city_data['country']])) {
            wp_set_post_terms($city_id, array($country_term_ids[$city_data['country']]), 'countries');
        }
        
        echo '<p>✓ Created city: ' . $city_data['name'] . ' (' . $city_data['latitude'] . ', ' . $city_data['longitude'] . ') in ' . $city_data['country'] . '</p>';
    } else {
        echo '<p>✗ Error creating city: ' . $city_data['name'] . '</p>';
    }
}

echo '<h2>Sample Data Population Complete!</h2>';
echo '<p><strong>Important:</strong> Please delete this file for security reasons.</p>';
echo '<p><a href="' . admin_url() . '">Go to WordPress Admin</a></p>';
echo '<p><a href="' . admin_url('edit.php?post_type=cities') . '">View Cities</a></p>';
echo '<p><a href="' . admin_url('edit-tags.php?taxonomy=countries&post_type=cities') . '">View Countries</a></p>';

?>