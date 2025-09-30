<?php
/*
Template Name: Cities and Weather Table
*/

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Cities and Weather Information</h1>
            </header>
            
            <div class="entry-content">
                
                <?php
                /**
                 * Custom action hook: Before cities table
                 */
                do_action('storefront_child_before_cities_table');
                ?>
                
                <!-- AJAX Search Form -->
                <div class="ajax-search">
                    <form id="city-search-form">
                        <input type="text" id="city-search-input" placeholder="Search cities..." />
                        <button type="submit">Search</button>
                    </form>
                    <div class="loading">Searching...</div>
                </div>
                
                <!-- Search Results -->
                <div id="search-results"></div>
                
                <!-- Full Cities Table -->
                <div id="all-cities-table">
                    <h2>All Cities and Weather Data</h2>
                    
                    <?php
                    global $wpdb;
                    
                    // Custom query using $wpdb to get cities with countries and coordinates
                    $query = "
                        SELECT DISTINCT p.ID, p.post_title as city_name, 
                               pm1.meta_value as latitude, pm2.meta_value as longitude,
                               GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') as countries
                        FROM {$wpdb->posts} p
                        LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_city_latitude'
                        LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_city_longitude'
                        LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'countries'
                        LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
                        WHERE p.post_type = 'cities' 
                        AND p.post_status = 'publish'
                        GROUP BY p.ID
                        ORDER BY p.post_title ASC
                    ";
                    
                    $cities = $wpdb->get_results($query);
                    
                    if ($cities) : ?>
                        <table class="cities-table">
                            <thead>
                                <tr>
                                    <th>City</th>
                                    <th>Country</th>
                                    <th>Coordinates</th>
                                    <th>Current Temperature</th>
                                    <th>Weather Description</th>
                                    <th>Humidity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cities as $city) : 
                                    $weather_data = array();
                                    if (!empty($city->latitude) && !empty($city->longitude)) {
                                        $weather_data = get_weather_data($city->latitude, $city->longitude);
                                    }
                                ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($city->city_name); ?></strong></td>
                                        <td><?php echo esc_html($city->countries ?: 'No country assigned'); ?></td>
                                        <td>
                                            <?php if (!empty($city->latitude) && !empty($city->longitude)) : ?>
                                                <?php echo esc_html($city->latitude . ', ' . $city->longitude); ?>
                                            <?php else : ?>
                                                <em>Not set</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($weather_data['temperature'])) : ?>
                                                <?php echo esc_html($weather_data['temperature']); ?>°C
                                            <?php else : ?>
                                                <em>N/A</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($weather_data['description'])) : ?>
                                                <?php echo esc_html(ucfirst($weather_data['description'])); ?>
                                            <?php else : ?>
                                                <em>N/A</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($weather_data['humidity'])) : ?>
                                                <?php echo esc_html($weather_data['humidity']); ?>%
                                            <?php else : ?>
                                                <em>N/A</em>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p>No cities found. <a href="<?php echo admin_url('post-new.php?post_type=cities'); ?>">Add some cities</a> to display weather information.</p>
                    <?php endif; ?>
                </div>
                
                <?php
                /**
                 * Custom action hook: After cities table
                 */
                do_action('storefront_child_after_cities_table');
                ?>
                
            </div>
        </article>
        
    </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>