/**
 * AJAX Search Functionality
 * 
 * Handles city search and widget weather display
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

jQuery(document).ready(function($) {
    'use strict';
    
    /**
     * AJAX city search functionality
     */
    $('#city-search-form').on('submit', function(e) {
        e.preventDefault();
        
        var searchTerm = $('#city-search-input').val();
        var $loading = $('.loading');
        var $results = $('#search-results');
        
        if (searchTerm.length < 2) {
            alert(storefrontChildAjax.i18n.minChars || 'Please enter at least 2 characters to search');
            return;
        }
        
        // Show loading indicator
        $loading.show();
        $results.empty();
        
        $.ajax({
            url: storefrontChildAjax.ajaxUrl,
            type: 'POST',
            data: {
                action: 'search_cities',
                search_term: searchTerm,
                nonce: storefrontChildAjax.nonce
            },
            success: function(response) {
                $loading.hide();
                
                if (response.success && response.data.length > 0) {
                    var html = '<table class="cities-table">';
                    html += '<thead><tr><th>City</th><th>Country</th><th>Coordinates</th><th>Temperature</th></tr></thead>';
                    html += '<tbody>';
                    
                    $.each(response.data, function(index, city) {
                        html += '<tr>';
                        html += '<td>' + city.name + '</td>';
                        html += '<td>' + city.countries + '</td>';
                        html += '<td>' + (city.latitude && city.longitude ? city.latitude + ', ' + city.longitude : 'Not set') + '</td>';
                        html += '<td>' + city.temperature + '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    $results.html(html);
                } else {
                    $results.html('<p>No cities found matching your search criteria.</p>');
                }
            },
            error: function() {
                $loading.hide();
                $results.html('<p>Error occurred while searching. Please try again.</p>');
            }
        });
    });
    
    /**
     * Widget city selection handler
     */
    $('.cities-widget select').on('change', function() {
        var cityId = $(this).val();
        var $widget = $(this).closest('.cities-widget');
        var $weatherInfo = $widget.find('.weather-info');
        
        if (!cityId) {
            $weatherInfo.hide();
            return;
        }
        
        $weatherInfo.html('<p>' + (storefrontChildAjax.i18n.loading || 'Loading...') + '</p>').show();
        
        $.ajax({
            url: storefrontChildAjax.ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_city_weather',
                city_id: cityId,
                nonce: storefrontChildAjax.nonce
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
    
    /**
     * Auto-load weather for default city if selected in widget
     */
    $('.cities-widget select[data-selected-city]').each(function() {
        var selectedCity = $(this).data('selected-city');
        if (selectedCity) {
            $(this).trigger('change');
        }
    });
    
    /**
     * Real-time search as user types (with debounce)
     */
    var searchTimeout;
    $('#city-search-input').on('input', function() {
        var searchTerm = $(this).val();
        
        clearTimeout(searchTimeout);
        
        if (searchTerm.length >= 2) {
            searchTimeout = setTimeout(function() {
                $('#city-search-form').trigger('submit');
            }, 500); // 500ms delay
        } else if (searchTerm.length === 0) {
            $('#search-results').empty();
        }
    });
});