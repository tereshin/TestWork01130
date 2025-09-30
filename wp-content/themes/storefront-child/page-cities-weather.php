<?php
/**
 * Template Name: Cities and Weather Table
 * 
 * Displays all cities with their weather information
 * Includes AJAX search functionality
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title"><?php esc_html_e('Cities and Weather Information', 'storefront-child'); ?></h1>
            </header>
            
            <div class="entry-content">
                
                <?php
                /**
                 * Custom action hook: Before cities table
                 * 
                 * @hooked add_custom_content_before_cities_table - 10 (in custom-hooks-examples.php)
                 */
                do_action('storefront_child_before_cities_table');
                ?>
                
                <!-- AJAX Search Form -->
                <div class="ajax-search">
                    <form id="city-search-form">
                        <input 
                            type="text" 
                            id="city-search-input" 
                            placeholder="<?php esc_attr_e('Search cities...', 'storefront-child'); ?>" 
                        />
                        <button type="submit"><?php esc_html_e('Search', 'storefront-child'); ?></button>
                    </form>
                    <div class="loading"><?php esc_html_e('Searching...', 'storefront-child'); ?></div>
                </div>
                
                <!-- Search Results -->
                <div id="search-results"></div>
                
                <!-- Full Cities Table -->
                <div id="all-cities-table">
                    <h2><?php esc_html_e('All Cities and Weather Data', 'storefront-child'); ?></h2>
                    
                    <?php
                    // Get cities using centralized query
                    $cities = Storefront_Child_Cities_Query::get_cities_with_data();
                    
                    if ($cities) : ?>
                        <table class="cities-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('City', 'storefront-child'); ?></th>
                                    <th><?php esc_html_e('Country', 'storefront-child'); ?></th>
                                    <th><?php esc_html_e('Coordinates', 'storefront-child'); ?></th>
                                    <th><?php esc_html_e('Current Temperature', 'storefront-child'); ?></th>
                                    <th><?php esc_html_e('Weather Description', 'storefront-child'); ?></th>
                                    <th><?php esc_html_e('Humidity', 'storefront-child'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cities as $city) : 
                                    $weather_data = array();
                                    
                                    // Get weather data if coordinates are available
                                    if (!empty($city->latitude) && !empty($city->longitude)) {
                                        $weather_data = Storefront_Child_Weather_API::get_weather_data(
                                            $city->latitude,
                                            $city->longitude
                                        );
                                    }
                                ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($city->city_name); ?></strong></td>
                                        <td><?php echo esc_html($city->countries ?: __('No country assigned', 'storefront-child')); ?></td>
                                        <td>
                                            <?php if (!empty($city->latitude) && !empty($city->longitude)) : ?>
                                                <?php echo esc_html($city->latitude . ', ' . $city->longitude); ?>
                                            <?php else : ?>
                                                <em><?php esc_html_e('Not set', 'storefront-child'); ?></em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($weather_data['temperature'])) : ?>
                                                <?php echo esc_html($weather_data['temperature']); ?>°C
                                            <?php else : ?>
                                                <em><?php esc_html_e('N/A', 'storefront-child'); ?></em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($weather_data['description'])) : ?>
                                                <?php echo esc_html(ucfirst($weather_data['description'])); ?>
                                            <?php else : ?>
                                                <em><?php esc_html_e('N/A', 'storefront-child'); ?></em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($weather_data['humidity'])) : ?>
                                                <?php echo esc_html($weather_data['humidity']); ?>%
                                            <?php else : ?>
                                                <em><?php esc_html_e('N/A', 'storefront-child'); ?></em>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p>
                            <?php
                            printf(
                                /* translators: %s: Link to add new city */
                                esc_html__('No cities found. %s to display weather information.', 'storefront-child'),
                                '<a href="' . esc_url(admin_url('post-new.php?post_type=cities')) . '">' . 
                                esc_html__('Add some cities', 'storefront-child') . '</a>'
                            );
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <?php
                /**
                 * Custom action hook: After cities table
                 * 
                 * @hooked add_custom_content_after_cities_table - 10 (in custom-hooks-examples.php)
                 */
                do_action('storefront_child_after_cities_table');
                ?>
                
            </div>
        </article>
        
    </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
