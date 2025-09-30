<?php
/**
 * Settings Page Handler
 * 
 * Handles admin settings page for weather API
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Storefront_Child_Settings_Page
 * 
 * Manages admin settings page
 */
class Storefront_Child_Settings_Page {
    
    /**
     * Singleton instance
     *
     * @var Storefront_Child_Settings_Page
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     *
     * @return Storefront_Child_Settings_Page
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
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_notices', array($this, 'show_api_key_notice'));
    }
    
    /**
     * Add settings page to admin menu
     *
     * @return void
     */
    public function add_settings_page() {
        add_options_page(
            __('Weather Settings', 'storefront-child'),
            __('Weather Settings', 'storefront-child'),
            'manage_options',
            'weather-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register settings
     *
     * @return void
     */
    public function register_settings() {
        register_setting(
            'storefront_child_weather_settings',
            'openweather_api_key',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            )
        );
        
        add_settings_section(
            'storefront_child_weather_section',
            __('OpenWeatherMap API Configuration', 'storefront-child'),
            array($this, 'render_section_description'),
            'weather-settings'
        );
        
        add_settings_field(
            'openweather_api_key',
            __('API Key', 'storefront-child'),
            array($this, 'render_api_key_field'),
            'weather-settings',
            'storefront_child_weather_section'
        );
    }
    
    /**
     * Render settings page
     *
     * @return void
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('storefront_child_weather_settings');
                do_settings_sections('weather-settings');
                submit_button(__('Save Settings', 'storefront-child'));
                ?>
            </form>
            
            <div class="card">
                <h2><?php esc_html_e('How to Get Your API Key', 'storefront-child'); ?></h2>
                <ol>
                    <li><?php esc_html_e('Visit', 'storefront-child'); ?> 
                        <a href="https://openweathermap.org/api" target="_blank">
                            <?php esc_html_e('OpenWeatherMap API', 'storefront-child'); ?>
                        </a>
                    </li>
                    <li><?php esc_html_e('Create a free account', 'storefront-child'); ?></li>
                    <li><?php esc_html_e('Navigate to your API keys section', 'storefront-child'); ?></li>
                    <li><?php esc_html_e('Copy your API key and paste it above', 'storefront-child'); ?></li>
                    <li><?php esc_html_e('Save the settings', 'storefront-child'); ?></li>
                </ol>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render section description
     *
     * @return void
     */
    public function render_section_description() {
        echo '<p>' . esc_html__('Configure your OpenWeatherMap API key to enable weather data fetching.', 'storefront-child') . '</p>';
    }
    
    /**
     * Render API key field
     *
     * @return void
     */
    public function render_api_key_field() {
        $api_key = get_option('openweather_api_key', '');
        ?>
        <input 
            type="text" 
            name="openweather_api_key" 
            value="<?php echo esc_attr($api_key); ?>" 
            class="regular-text" 
            placeholder="<?php esc_attr_e('Enter your API key', 'storefront-child'); ?>"
        />
        <p class="description">
            <?php
            printf(
                /* translators: %s: OpenWeatherMap link */
                esc_html__('Get your free API key from %s', 'storefront-child'),
                '<a href="https://openweathermap.org/api" target="_blank">OpenWeatherMap</a>'
            );
            ?>
        </p>
        <?php
    }
    
    /**
     * Show admin notice if API key is not configured
     *
     * @return void
     */
    public function show_api_key_notice() {
        $api_key = get_option('openweather_api_key', '');
        
        if (empty($api_key) && current_user_can('manage_options')) {
            $settings_url = admin_url('options-general.php?page=weather-settings');
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong><?php esc_html_e('Cities Weather Theme:', 'storefront-child'); ?></strong>
                    <?php
                    printf(
                        /* translators: %s: Settings page link */
                        esc_html__('Please configure your OpenWeatherMap API key in %s to enable weather data.', 'storefront-child'),
                        '<a href="' . esc_url($settings_url) . '">' . esc_html__('Weather Settings', 'storefront-child') . '</a>'
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }
}
