<?php
/**
 * Metaboxes Handler
 * 
 * Handles custom metaboxes for cities
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Storefront_Child_Metaboxes
 * 
 * Manages custom metaboxes
 */
class Storefront_Child_Metaboxes {
    
    /**
     * Singleton instance
     *
     * @var Storefront_Child_Metaboxes
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     *
     * @return Storefront_Child_Metaboxes
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
        add_action('add_meta_boxes', array($this, 'add_metaboxes'));
        add_action('save_post', array($this, 'save_coordinates'));
    }
    
    /**
     * Add metaboxes
     *
     * @return void
     */
    public function add_metaboxes() {
        add_meta_box(
            'city_coordinates',
            __('City Coordinates', 'storefront-child'),
            array($this, 'render_coordinates_metabox'),
            'cities',
            'normal',
            'default'
        );
    }
    
    /**
     * Render coordinates metabox
     *
     * @param WP_Post $post Current post object
     * @return void
     */
    public function render_coordinates_metabox($post) {
        // Add nonce for security
        wp_nonce_field('city_coordinates_save', 'city_coordinates_nonce');
        
        // Get current values
        $latitude  = get_post_meta($post->ID, '_city_latitude', true);
        $longitude = get_post_meta($post->ID, '_city_longitude', true);
        
        ?>
        <div class="city-coordinates-wrapper">
            <!-- Search Location -->
            <div class="city-coordinates-search">
                <input 
                    type="text" 
                    id="location-search" 
                    placeholder="<?php esc_attr_e('Search for a location (e.g., Paris, France)', 'storefront-child'); ?>" 
                />
                <button type="button" id="search-location-btn">
                    <?php esc_html_e('Search', 'storefront-child'); ?>
                </button>
                <div class="search-loading"><?php esc_html_e('Searching...', 'storefront-child'); ?></div>
                <div class="search-results" id="search-results"></div>
            </div>
            
            <!-- Map -->
            <div id="city-map"></div>
            
            <!-- Coordinate Fields -->
            <div class="city-coordinates-fields">
                <div class="coordinate-field">
                    <label for="city_latitude">
                        <span class="dashicons dashicons-location"></span> 
                        <?php esc_html_e('Latitude', 'storefront-child'); ?>
                    </label>
                    <input 
                        type="number" 
                        step="any" 
                        id="city_latitude" 
                        name="city_latitude" 
                        value="<?php echo esc_attr($latitude); ?>" 
                        placeholder="<?php esc_attr_e('e.g., 48.8566', 'storefront-child'); ?>" 
                    />
                </div>
                <div class="coordinate-field">
                    <label for="city_longitude">
                        <span class="dashicons dashicons-location-alt"></span> 
                        <?php esc_html_e('Longitude', 'storefront-child'); ?>
                    </label>
                    <input 
                        type="number" 
                        step="any" 
                        id="city_longitude" 
                        name="city_longitude" 
                        value="<?php echo esc_attr($longitude); ?>" 
                        placeholder="<?php esc_attr_e('e.g., 2.3522', 'storefront-child'); ?>" 
                    />
                </div>
            </div>
            
            <!-- Help Text -->
            <div class="coordinates-help">
                <p>
                    <strong><?php esc_html_e('How to use:', 'storefront-child'); ?></strong><br>
                    • <?php esc_html_e('Search for a location using the search bar above', 'storefront-child'); ?><br>
                    • <?php esc_html_e('Click on the map to set coordinates', 'storefront-child'); ?><br>
                    • <?php esc_html_e('Or manually enter latitude and longitude values', 'storefront-child'); ?><br>
                    • <?php esc_html_e('Coordinates will update automatically when you interact with the map', 'storefront-child'); ?>
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save coordinates metabox data
     *
     * @param int $post_id Post ID
     * @return void
     */
    public function save_coordinates($post_id) {
        // Verify nonce
        if (!isset($_POST['city_coordinates_nonce']) || 
            !wp_verify_nonce($_POST['city_coordinates_nonce'], 'city_coordinates_save')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check post type
        if (get_post_type($post_id) !== 'cities') {
            return;
        }
        
        // Save latitude
        if (isset($_POST['city_latitude'])) {
            $latitude = sanitize_text_field($_POST['city_latitude']);
            update_post_meta($post_id, '_city_latitude', $latitude);
        }
        
        // Save longitude
        if (isset($_POST['city_longitude'])) {
            $longitude = sanitize_text_field($_POST['city_longitude']);
            update_post_meta($post_id, '_city_longitude', $longitude);
        }
        
        // Clear caches after save
        Storefront_Child_Cities_Query::clear_cache();
        Storefront_Child_Weather_API::clear_cache();
    }
}
