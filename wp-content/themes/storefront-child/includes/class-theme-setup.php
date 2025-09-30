<?php
/**
 * Theme Setup Class
 * 
 * Main theme initialization and configuration
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Storefront_Child_Theme_Setup
 * 
 * Handles theme initialization, constants, and autoloading
 */
class Storefront_Child_Theme_Setup {
    
    /**
     * Theme version
     *
     * @var string
     */
    const VERSION = '1.0.0';
    
    /**
     * Singleton instance
     *
     * @var Storefront_Child_Theme_Setup
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     *
     * @return Storefront_Child_Theme_Setup
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     * 
     * Private to enforce singleton pattern
     */
    private function __construct() {
        $this->define_constants();
        $this->init();
    }
    
    /**
     * Define theme constants
     *
     * @return void
     */
    private function define_constants() {
        if (!defined('STOREFRONT_CHILD_VERSION')) {
            define('STOREFRONT_CHILD_VERSION', self::VERSION);
        }
        
        if (!defined('STOREFRONT_CHILD_PATH')) {
            define('STOREFRONT_CHILD_PATH', get_stylesheet_directory());
        }
        
        if (!defined('STOREFRONT_CHILD_URL')) {
            define('STOREFRONT_CHILD_URL', get_stylesheet_directory_uri());
        }
        
        if (!defined('STOREFRONT_CHILD_INCLUDES')) {
            define('STOREFRONT_CHILD_INCLUDES', STOREFRONT_CHILD_PATH . '/includes');
        }
        
        if (!defined('STOREFRONT_CHILD_ASSETS')) {
            define('STOREFRONT_CHILD_ASSETS', STOREFRONT_CHILD_URL . '/assets');
        }
    }
    
    /**
     * Initialize theme components
     *
     * @return void
     */
    private function init() {
        // Include required files
        $this->includes();
        
        // Initialize components
        add_action('after_setup_theme', array($this, 'theme_setup'));
    }
    
    /**
     * Include required files
     *
     * @return void
     */
    private function includes() {
        // Core classes
        require_once STOREFRONT_CHILD_INCLUDES . '/class-assets-manager.php';
        require_once STOREFRONT_CHILD_INCLUDES . '/class-post-types.php';
        require_once STOREFRONT_CHILD_INCLUDES . '/admin/class-metaboxes.php';
        require_once STOREFRONT_CHILD_INCLUDES . '/api/class-weather-api.php';
        require_once STOREFRONT_CHILD_INCLUDES . '/class-cities-query.php';
        require_once STOREFRONT_CHILD_INCLUDES . '/class-ajax-handlers.php';
        
        // Widgets
        require_once STOREFRONT_CHILD_INCLUDES . '/widgets/class-cities-widget.php';
        
        // Admin
        require_once STOREFRONT_CHILD_INCLUDES . '/admin/class-settings-page.php';
    }
    
    /**
     * Theme setup
     *
     * @return void
     */
    public function theme_setup() {
        // Load text domain
        load_child_theme_textdomain('storefront-child', STOREFRONT_CHILD_PATH . '/languages');
        
        // Initialize components
        Storefront_Child_Assets_Manager::get_instance();
        Storefront_Child_Post_Types::get_instance();
        Storefront_Child_Metaboxes::get_instance();
        Storefront_Child_Ajax_Handlers::get_instance();
        Storefront_Child_Settings_Page::get_instance();
        
        // Register widgets
        add_action('widgets_init', array($this, 'register_widgets'));
    }
    
    /**
     * Register widgets
     *
     * @return void
     */
    public function register_widgets() {
        register_widget('Storefront_Child_Cities_Widget');
    }
}
