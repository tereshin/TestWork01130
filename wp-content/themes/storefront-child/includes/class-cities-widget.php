<?php
/**
 * Cities Weather Widget
 * 
 * Allows users to select a city and display its current weather information
 */

class Cities_Weather_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'cities_weather_widget',
            __('Cities Weather Widget', 'storefront-child'),
            array(
                'description' => __('Display city selector with current weather information', 'storefront-child'),
            )
        );
    }
    
    /**
     * Front-end display of widget
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        // Get all cities
        $cities = get_posts(array(
            'post_type' => 'cities',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        if (empty($cities)) {
            echo '<p>' . __('No cities available.', 'storefront-child') . '</p>';
            echo $args['after_widget'];
            return;
        }
        ?>
        
        <div class="cities-widget">
            <select id="city-selector-<?php echo $this->id; ?>" class="city-selector">
                <option value=""><?php _e('Select a city...', 'storefront-child'); ?></option>
                <?php foreach ($cities as $city) : ?>
                    <option value="<?php echo $city->ID; ?>"><?php echo esc_html($city->post_title); ?></option>
                <?php endforeach; ?>
            </select>
            
            <div class="weather-info" style="display: none;"></div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#city-selector-<?php echo $this->id; ?>').on('change', function() {
                var cityId = $(this).val();
                var $weatherInfo = $(this).siblings('.weather-info');
                
                if (!cityId) {
                    $weatherInfo.hide();
                    return;
                }
                
                $weatherInfo.html('<p>Loading weather data...</p>').show();
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'get_city_weather',
                        city_id: cityId,
                        nonce: '<?php echo wp_create_nonce('city_weather_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            var weather = response.data;
                            var html = '<h4>' + weather.city_name + '</h4>';
                            if (weather.temperature !== 'N/A') {
                                html += '<p><strong>Temperature:</strong> ' + weather.temperature + '</p>';
                                if (weather.description) {
                                    html += '<p><strong>Conditions:</strong> ' + weather.description + '</p>';
                                }
                                if (weather.humidity) {
                                    html += '<p><strong>Humidity:</strong> ' + weather.humidity + '%</p>';
                                }
                            } else {
                                html += '<p>Weather data not available for this city.</p>';
                            }
                            $weatherInfo.html(html);
                        } else {
                            $weatherInfo.html('<p>Error loading weather data.</p>');
                        }
                    },
                    error: function() {
                        $weatherInfo.html('<p>Error occurred while loading weather data.</p>');
                    }
                });
            });
        });
        </script>
        
        <?php
        echo $args['after_widget'];
    }
    
    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Weather by City', 'storefront-child');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'storefront-child'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        
        return $instance;
    }
}

/**
 * AJAX handler for getting city weather in widget
 */
function ajax_get_city_weather() {
    check_ajax_referer('city_weather_nonce', 'nonce');
    
    $city_id = intval($_POST['city_id']);
    
    if (!$city_id) {
        wp_send_json_error('Invalid city ID');
    }
    
    $city = get_post($city_id);
    if (!$city || $city->post_type !== 'cities') {
        wp_send_json_error('City not found');
    }
    
    $latitude = get_post_meta($city_id, '_city_latitude', true);
    $longitude = get_post_meta($city_id, '_city_longitude', true);
    
    $weather_data = array(
        'city_name' => $city->post_title,
        'temperature' => 'N/A',
        'description' => '',
        'humidity' => ''
    );
    
    if (!empty($latitude) && !empty($longitude)) {
        $weather = get_weather_data($latitude, $longitude);
        
        if (!isset($weather['error'])) {
            $weather_data['temperature'] = $weather['temperature'] . '°C';
            $weather_data['description'] = ucfirst($weather['description']);
            $weather_data['humidity'] = $weather['humidity'];
        }
    }
    
    wp_send_json_success($weather_data);
}
add_action('wp_ajax_get_city_weather', 'ajax_get_city_weather');
add_action('wp_ajax_nopriv_get_city_weather', 'ajax_get_city_weather');