<?php
/**
 * Cities Weather Widget
 * 
 * Widget for displaying city weather information
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Storefront_Child_Cities_Widget
 * 
 * Weather widget for cities
 */
class Storefront_Child_Cities_Widget extends WP_Widget {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'storefront_child_cities_widget',
            __('Cities Weather Widget', 'storefront-child'),
            array(
                'description' => __('Display city selector with current weather information', 'storefront-child'),
                'classname'   => 'storefront-child-cities-widget',
            )
        );
    }
    
    /**
     * Front-end display of widget
     *
     * @param array $args Widget arguments
     * @param array $instance Saved values from database
     * @return void
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        // Display title
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        // Get all cities
        $cities = Storefront_Child_Cities_Query::get_cities_for_select();
        
        if (empty($cities)) {
            echo '<p>' . esc_html__('No cities available.', 'storefront-child') . '</p>';
            echo $args['after_widget'];
            return;
        }
        
        $selected_city = !empty($instance['selected_city']) ? absint($instance['selected_city']) : '';
        $widget_id     = esc_attr($this->id);
        
        ?>
        <div class="cities-widget" data-widget-id="<?php echo $widget_id; ?>">
            <select 
                id="city-selector-<?php echo $widget_id; ?>" 
                class="city-selector" 
                data-selected-city="<?php echo esc_attr($selected_city); ?>"
            >
                <option value=""><?php esc_html_e('Select a city...', 'storefront-child'); ?></option>
                <?php foreach ($cities as $city) : ?>
                    <option 
                        value="<?php echo esc_attr($city->ID); ?>" 
                        <?php selected($selected_city, $city->ID); ?>
                    >
                        <?php echo esc_html($city->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <div class="weather-info" style="display: none;"></div>
        </div>
        <?php
        
        echo $args['after_widget'];
    }
    
    /**
     * Back-end widget form
     *
     * @param array $instance Previously saved values from database
     * @return void
     */
    public function form($instance) {
        $title         = !empty($instance['title']) ? $instance['title'] : __('Weather by City', 'storefront-child');
        $selected_city = !empty($instance['selected_city']) ? absint($instance['selected_city']) : '';
        
        // Get all cities for the dropdown
        $cities = Storefront_Child_Cities_Query::get_cities_for_select();
        
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'storefront-child'); ?>
            </label>
            <input 
                class="widefat" 
                id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                type="text" 
                value="<?php echo esc_attr($title); ?>"
            >
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('selected_city')); ?>">
                <?php esc_html_e('Default City:', 'storefront-child'); ?>
            </label>
            <select 
                class="widefat" 
                id="<?php echo esc_attr($this->get_field_id('selected_city')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('selected_city')); ?>"
            >
                <option value=""><?php esc_html_e('-- Select a city --', 'storefront-child'); ?></option>
                <?php foreach ($cities as $city) : ?>
                    <option 
                        value="<?php echo esc_attr($city->ID); ?>" 
                        <?php selected($selected_city, $city->ID); ?>
                    >
                        <?php echo esc_html($city->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="description">
                <?php esc_html_e('Optional: Select a city to display by default', 'storefront-child'); ?>
            </small>
        </p>
        <?php
    }
    
    /**
     * Sanitize widget form values as they are saved
     *
     * @param array $new_instance Values just sent to be saved
     * @param array $old_instance Previously saved values from database
     * @return array Updated safe values to be saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title']         = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['selected_city'] = !empty($new_instance['selected_city']) ? absint($new_instance['selected_city']) : '';
        
        return $instance;
    }
}
