# Storefront Child Theme - Cities Weather

A WordPress child theme for Storefront that includes custom post types, taxonomies, and weather functionality using the OpenWeatherMap API.

## Features

- **Custom Post Type**: Cities with latitude/longitude metabox
- **Custom Taxonomy**: Countries linked to Cities
- **Weather Widget**: Select city and display current weather
- **Custom Page Template**: Display cities/countries/weather table with AJAX search
- **Custom Action Hooks**: `storefront_child_before_cities_table` and `storefront_child_after_cities_table`

## Installation

1. Ensure you have the Storefront parent theme installed and activated
2. Upload this child theme to your `/wp-content/themes/` directory
3. Activate the child theme in WordPress admin
4. Get a free API key from [OpenWeatherMap](https://openweathermap.org/api)
5. Go to Settings > Weather Settings and enter your API key

## Usage

### Adding Cities

1. Go to WordPress admin > Cities > Add New
2. Enter the city name and description
3. Set the latitude and longitude coordinates
4. Assign the city to one or more countries

### Adding Countries

1. Go to WordPress admin > Cities > Countries
2. Add new countries as needed
3. Countries can be assigned when creating/editing cities

### Using the Weather Widget

1. Go to Appearance > Widgets
2. Add the "Cities Weather Widget" to any widget area
3. Users can select a city to view current weather information

### Creating the Cities Weather Page

1. Create a new page in WordPress
2. In the Page Attributes box, select "Cities and Weather Table" as the template
3. Publish the page to display the full cities and weather table with AJAX search

### Custom Action Hooks

Developers can hook into the custom actions:

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

## File Structure

```
storefront-child/
├── style.css                          # Child theme styles
├── functions.php                      # Main theme functions
├── page-cities-weather.php           # Custom page template
├── js/
│   └── ajax-search.js                # AJAX search functionality
└── includes/
    └── class-cities-widget.php       # Weather widget class
```

## Requirements

- WordPress 5.0+
- Storefront parent theme
- OpenWeatherMap API key (free)
- PHP 7.4+

## API Usage

The theme uses the OpenWeatherMap Current Weather Data API to fetch weather information. Make sure to:

1. Sign up for a free account at OpenWeatherMap
2. Generate an API key
3. Configure the API key in Settings > Weather Settings

## Customization

The theme includes CSS classes for easy customization:

- `.cities-widget` - Weather widget container
- `.cities-table` - Main cities/weather table
- `.ajax-search` - Search form container
- `.weather-info` - Weather information display

## Support

This is a custom development theme. For modifications or issues, please refer to the code comments and WordPress documentation.