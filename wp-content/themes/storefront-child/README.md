# Storefront Child Theme - Cities Weather

A modern, modular WordPress child theme for Storefront with custom post types, taxonomies, and weather functionality using the OpenWeatherMap API.

## 🚀 Features

- **Custom Post Type**: Cities with interactive coordinate selection
- **Custom Taxonomy**: Countries linked to Cities
- **Weather Widget**: Dynamic city selector with real-time weather display
- **Custom Page Template**: Cities/weather table with AJAX search
- **Custom Action Hooks**: Extensible architecture
- **Modular Design**: Clean, organized, documented code
- **Caching System**: Optimized API calls with WordPress Object Cache
- **Responsive Design**: Mobile-friendly interface

## 📦 Installation

1. Ensure you have the Storefront parent theme installed and activated
2. Upload this child theme to your `/wp-content/themes/` directory
3. Activate the child theme in WordPress admin
4. Get a free API key from [OpenWeatherMap](https://openweathermap.org/api)
5. Go to **Settings > Weather Settings** and enter your API key

## 📁 File Structure

```
storefront-child/
├── functions.php                          # Theme entry point
├── style.css                             # Child theme styles
├── page-cities-weather.php               # Custom page template
├── custom-hooks-examples.php             # Hook usage examples
├── screenshot-info.txt                   # Theme info
│
├── assets/                               # Assets directory
│   ├── css/
│   │   └── admin-coordinates.css         # Admin metabox styles
│   └── js/
│       ├── ajax-search.js                # AJAX search functionality
│       └── city-coordinates-map.js       # Map integration
│
├── includes/                             # Core classes
│   ├── class-theme-setup.php             # Main theme initialization
│   ├── class-post-types.php              # CPT and taxonomy registration
│   ├── class-assets-manager.php          # Scripts and styles management
│   ├── class-cities-query.php            # Database queries
│   ├── class-ajax-handlers.php           # AJAX request handlers
│   │
│   ├── admin/                            # Admin functionality
│   │   ├── class-metaboxes.php           # Custom metaboxes
│   │   └── class-settings-page.php       # Settings page
│   │
│   ├── api/                              # API integrations
│   │   └── class-weather-api.php         # Weather API handler
│   │
│   └── widgets/                          # Widgets
│       └── class-cities-widget.php       # Weather widget
│
└── sample-data/                          # Sample import data
```

## 🎯 Usage

### Adding Cities

1. Go to **WordPress admin > Cities > Add New**
2. Enter the city name and description
3. Use the interactive map to set coordinates:
   - Search for a location
   - Click on the map
   - Or manually enter coordinates
4. Assign the city to one or more countries

### Adding Countries

1. Go to **WordPress admin > Cities > Countries**
2. Add new countries as needed
3. Countries can be assigned when creating/editing cities

### Using the Weather Widget

1. Go to **Appearance > Widgets**
2. Add the "Cities Weather Widget" to any widget area
3. Configure the widget:
   - Set a custom title
   - Optionally select a default city
4. Users can select a city to view current weather information

### Creating the Cities Weather Page

1. Create a new page in WordPress
2. In the **Page Attributes** box, select "Cities and Weather Table" as the template
3. Publish the page to display the full cities and weather table with AJAX search

## 🔧 Custom Action Hooks

Developers can hook into custom actions:

```php
// Add content before the cities table
add_action('storefront_child_before_cities_table', 'my_custom_before_table_content');
function my_custom_before_table_content() {
    echo '<div class="custom-notice">Custom content before table</div>';
}

// Add content after the cities table
add_action('storefront_child_after_cities_table', 'my_custom_after_table_content');
function my_custom_after_table_content() {
    echo '<div class="custom-footer">Custom content after table</div>';
}
```

See `custom-hooks-examples.php` for more examples.

## 🏗️ Architecture

### Singleton Pattern
All main classes use the singleton pattern to ensure only one instance exists:

```php
$instance = Storefront_Child_Weather_API::get_instance();
```

### Caching System
- Cities data cached for 1 hour
- Weather data cached for 30 minutes
- Automatic cache clearing on content updates

### API Integration
- Validates coordinates before API calls
- Handles errors gracefully
- Returns structured data arrays

### Security
- Nonce verification for all AJAX requests
- Capability checks for admin functions
- Input sanitization and output escaping

## 📊 Constants

The theme defines the following constants:

- `STOREFRONT_CHILD_VERSION` - Theme version
- `STOREFRONT_CHILD_PATH` - Theme directory path
- `STOREFRONT_CHILD_URL` - Theme directory URL
- `STOREFRONT_CHILD_INCLUDES` - Includes directory path
- `STOREFRONT_CHILD_ASSETS` - Assets directory URL

## 🔌 Main Classes

### Storefront_Child_Theme_Setup
Main theme initialization and autoloading

### Storefront_Child_Post_Types
Registers custom post types and taxonomies

### Storefront_Child_Assets_Manager
Manages all CSS and JavaScript enqueuing

### Storefront_Child_Cities_Query
Centralized database queries with caching

### Storefront_Child_Weather_API
Weather data fetching with validation and caching

### Storefront_Child_Ajax_Handlers
Handles all AJAX requests

### Storefront_Child_Metaboxes
Custom metaboxes for city coordinates

### Storefront_Child_Settings_Page
Admin settings page for API configuration

### Storefront_Child_Cities_Widget
Weather widget for sidebars

## 🎨 Customization

The theme includes CSS classes for easy customization:

- `.storefront-child-cities-widget` - Weather widget container
- `.cities-table` - Main cities/weather table
- `.ajax-search` - Search form container
- `.weather-info` - Weather information display
- `.city-coordinates-wrapper` - Admin coordinates metabox

## 🌍 Internationalization

The theme is translation-ready:
- Text domain: `storefront-child`
- All strings are wrapped in translation functions
- Translation files should be placed in `/languages/`

## ⚡ Performance Optimizations

1. **Caching**: WordPress Object Cache for queries and API calls
2. **Lazy Loading**: Weather data loaded only when needed
3. **Debouncing**: Search input debounced to reduce requests
4. **Minification Ready**: Organized assets for build processes

## 🔒 Security Features

- CSRF protection with nonces
- Capability checks
- Input sanitization
- Output escaping
- Prepared SQL statements

## 📋 Requirements

- WordPress 5.0+
- Storefront parent theme
- OpenWeatherMap API key (free)
- PHP 7.4+
- MySQL 5.6+ or MariaDB 10.0+

## 🐛 Debugging

To enable debug mode, add to your `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 🤝 Contributing

This is a custom development theme. For modifications:
1. Follow WordPress coding standards
2. Maintain PHPDoc comments
3. Test thoroughly before deployment
4. Update documentation

## 📝 Changelog

### Version 1.0.0
- Initial release with modular architecture
- Custom post types and taxonomies
- Weather API integration
- Interactive coordinate selection
- AJAX search functionality
- Caching system
- Full documentation

## 📄 License

This theme follows the GPL license as WordPress.

## 🆘 Support

For modifications or issues:
1. Check the code comments
2. Review WordPress documentation
3. Consult OpenWeatherMap API documentation

## 📚 Additional Resources

- [WordPress Codex](https://codex.wordpress.org/)
- [Storefront Documentation](https://docs.woocommerce.com/documentation/themes/storefront/)
- [OpenWeatherMap API](https://openweathermap.org/api)
- [Leaflet Map Documentation](https://leafletjs.com/)

---

**Author**: Storefront Child Theme  
**Version**: 1.0.0  
**Last Updated**: 2025