<?php
/**
 * Storefront Child Theme Functions
 * 
 * This file handles all the custom functionality for the child theme including:
 * - Custom Post Types
 * - Custom Taxonomies  
 * - Widgets
 * - AJAX handlers
 * - Custom hooks
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue styles and scripts
 */
function storefront_child_enqueue_styles() {
    // Enqueue parent theme styles
    wp_enqueue_style('storefront-style', get_template_directory_uri() . '/style.css');
    
    // Enqueue child theme styles
    wp_enqueue_style('storefront-child-style', 
        get_stylesheet_directory_uri() . '/style.css',
        array('storefront-style'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue jQuery and custom scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('storefront-child-ajax', 
        get_stylesheet_directory_uri() . '/js/ajax-search.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // Localize script for AJAX
    wp_localize_script('storefront-child-ajax', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('city_search_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');

/**
 * Register Custom Post Type: Cities
 */
function register_cities_post_type() {
    $labels = array(
        'name'                  => 'Cities',
        'singular_name'         => 'City',
        'menu_name'             => 'Cities',
        'name_admin_bar'        => 'City',
        'archives'              => 'City Archives',
        'attributes'            => 'City Attributes',
        'parent_item_colon'     => 'Parent City:',
        'all_items'             => 'All Cities',
        'add_new_item'          => 'Add New City',
        'add_new'               => 'Add New',
        'new_item'              => 'New City',
        'edit_item'             => 'Edit City',
        'update_item'           => 'Update City',
        'view_item'             => 'View City',
        'view_items'            => 'View Cities',
        'search_items'          => 'Search Cities',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into city',
        'uploaded_to_this_item' => 'Uploaded to this city',
        'items_list'            => 'Cities list',
        'items_list_navigation' => 'Cities list navigation',
        'filter_items_list'     => 'Filter cities list',
    );
    
    $args = array(
        'label'                 => 'City',
        'description'           => 'Cities with coordinates for weather data',
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
add_action('init', 'register_cities_post_type', 0);

/**
 * Register Custom Taxonomy: Countries
 */
function register_countries_taxonomy() {
    $labels = array(
        'name'                       => 'Countries',
        'singular_name'              => 'Country',
        'menu_name'                  => 'Countries',
        'all_items'                  => 'All Countries',
        'parent_item'                => 'Parent Country',
        'parent_item_colon'          => 'Parent Country:',
        'new_item_name'              => 'New Country Name',
        'add_new_item'               => 'Add New Country',
        'edit_item'                  => 'Edit Country',
        'update_item'                => 'Update Country',
        'view_item'                  => 'View Country',
        'separate_items_with_commas' => 'Separate countries with commas',
        'add_or_remove_items'        => 'Add or remove countries',
        'choose_from_most_used'      => 'Choose from the most used',
        'popular_items'              => 'Popular Countries',
        'search_items'               => 'Search Countries',
        'not_found'                  => 'Not Found',
        'no_terms'                   => 'No countries',
        'items_list'                 => 'Countries list',
        'items_list_navigation'      => 'Countries list navigation',
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
add_action('init', 'register_countries_taxonomy', 0);

/**
 * Add metabox for city coordinates
 */
function add_city_coordinates_metabox() {
    add_meta_box(
        'city_coordinates',
        'City Coordinates',
        'city_coordinates_callback',
        'cities',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_city_coordinates_metabox');

/**
 * Metabox callback function
 */
function city_coordinates_callback($post) {
    wp_nonce_field('city_coordinates_save', 'city_coordinates_nonce');
    
    $latitude = get_post_meta($post->ID, '_city_latitude', true);
    $longitude = get_post_meta($post->ID, '_city_longitude', true);
    
    echo '<div class="city-coordinates">';
    echo '<div>';
    echo '<label for="city_latitude">Latitude:</label>';
    echo '<input type="number" step="any" id="city_latitude" name="city_latitude" value="' . esc_attr($latitude) . '" />';
    echo '</div>';
    echo '<div>';
    echo '<label for="city_longitude">Longitude:</label>';
    echo '<input type="number" step="any" id="city_longitude" name="city_longitude" value="' . esc_attr($longitude) . '" />';
    echo '</div>';
    echo '</div>';
    echo '<p><em>Enter the latitude and longitude coordinates for this city to fetch weather data.</em></p>';
}

/**
 * Save metabox data
 */
function save_city_coordinates($post_id) {
    if (!isset($_POST['city_coordinates_nonce']) || !wp_verify_nonce($_POST['city_coordinates_nonce'], 'city_coordinates_save')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['city_latitude'])) {
        update_post_meta($post_id, '_city_latitude', sanitize_text_field($_POST['city_latitude']));
    }
    
    if (isset($_POST['city_longitude'])) {
        update_post_meta($post_id, '_city_longitude', sanitize_text_field($_POST['city_longitude']));
    }
}
add_action('save_post', 'save_city_coordinates');

/**
 * Weather API functions
 */
function get_weather_data($latitude, $longitude) {
    // You'll need to get an API key from OpenWeatherMap
    $api_key = get_option('openweather_api_key', ''); 
    
    if (empty($api_key)) {
        return array('error' => 'OpenWeatherMap API key not configured');
    }
    
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$api_key}&units=metric";
    
    $response = wp_remote_get($url, array('timeout' => 10));
    
    if (is_wp_error($response)) {
        return array('error' => 'Failed to fetch weather data');
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (isset($data['main']['temp'])) {
        return array(
            'temperature' => round($data['main']['temp']),
            'description' => $data['weather'][0]['description'],
            'humidity' => $data['main']['humidity'],
            'pressure' => $data['main']['pressure']
        );
    }
    
    return array('error' => 'Invalid weather data received');
}

/**
 * AJAX handler for city search
 */
function ajax_search_cities() {
    check_ajax_referer('city_search_nonce', 'nonce');
    
    $search_term = sanitize_text_field($_POST['search_term']);
    
    global $wpdb;
    
    $query = $wpdb->prepare("
        SELECT p.ID, p.post_title, pm1.meta_value as latitude, pm2.meta_value as longitude,
               GROUP_CONCAT(t.name SEPARATOR ', ') as countries
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_city_latitude'
        LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_city_longitude'
        LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'countries'
        LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE p.post_type = 'cities' 
        AND p.post_status = 'publish' 
        AND p.post_title LIKE %s
        GROUP BY p.ID
        ORDER BY p.post_title ASC
    ", '%' . $search_term . '%');
    
    $cities = $wpdb->get_results($query);
    
    $results = array();
    foreach ($cities as $city) {
        $weather_data = array();
        if (!empty($city->latitude) && !empty($city->longitude)) {
            $weather_data = get_weather_data($city->latitude, $city->longitude);
        }
        
        $results[] = array(
            'id' => $city->ID,
            'name' => $city->post_title,
            'countries' => $city->countries ?: 'No country assigned',
            'latitude' => $city->latitude,
            'longitude' => $city->longitude,
            'temperature' => isset($weather_data['temperature']) ? $weather_data['temperature'] . '°C' : 'N/A'
        );
    }
    
    wp_send_json_success($results);
}
add_action('wp_ajax_search_cities', 'ajax_search_cities');
add_action('wp_ajax_nopriv_search_cities', 'ajax_search_cities');

/**
 * Add settings page for OpenWeatherMap API key
 */
function add_weather_settings_page() {
    add_options_page(
        'Weather Settings',
        'Weather Settings',
        'manage_options',
        'weather-settings',
        'weather_settings_page'
    );
}
add_action('admin_menu', 'add_weather_settings_page');

function weather_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('openweather_api_key', sanitize_text_field($_POST['openweather_api_key']));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    $api_key = get_option('openweather_api_key', '');
    ?>
    <div class="wrap">
        <h1>Weather Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">OpenWeatherMap API Key</th>
                    <td>
                        <input type="text" name="openweather_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" />
                        <p class="description">
                            Get your free API key from <a href="https://openweathermap.org/api" target="_blank">OpenWeatherMap</a>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Include widget class
 */
require_once get_stylesheet_directory() . '/includes/class-cities-widget.php';

/**
 * Register widget
 */
function register_cities_widget() {
    register_widget('Cities_Weather_Widget');
}
add_action('widgets_init', 'register_cities_widget');