/**
 * City Coordinates Map Handler
 * 
 * Handles OpenStreetMap integration with Leaflet for city coordinates selection
 *
 * @package StorefrontChild
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    let map;
    let marker;
    
    /**
     * Initialize map when DOM is ready
     */
    $(document).ready(function() {
        initializeMap();
        setupEventListeners();
    });
    
    /**
     * Initialize Leaflet map
     */
    function initializeMap() {
        // Default coordinates (Paris, France)
        let defaultLat = 48.8566;
        let defaultLng = 2.3522;
        let defaultZoom = 13;
        
        // Use saved coordinates if available
        if (cityCoordinatesData.latitude && cityCoordinatesData.longitude) {
            defaultLat = parseFloat(cityCoordinatesData.latitude);
            defaultLng = parseFloat(cityCoordinatesData.longitude);
            defaultZoom = 13;
        }
        
        // Initialize map
        map = L.map('city-map').setView([defaultLat, defaultLng], defaultZoom);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Fix map rendering issue - invalidate size after initialization
        setTimeout(function() {
            map.invalidateSize();
        }, 100);
        
        // Add marker if coordinates exist
        if (cityCoordinatesData.latitude && cityCoordinatesData.longitude) {
            addMarker(defaultLat, defaultLng);
        }
        
        // Handle map click
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            updateCoordinates(lat, lng);
        });
    }
    
    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        // Search button click
        $('#search-location-btn').on('click', function() {
            performSearch();
        });
        
        // Enter key in search input
        $('#location-search').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                performSearch();
            }
        });
        
        // Manual coordinate input change
        $('#city_latitude, #city_longitude').on('change', function() {
            const lat = parseFloat($('#city_latitude').val());
            const lng = parseFloat($('#city_longitude').val());
            
            if (!isNaN(lat) && !isNaN(lng)) {
                map.setView([lat, lng], 13);
                addMarker(lat, lng);
            }
        });
    }
    
    /**
     * Perform location search using Nominatim API
     */
    function performSearch() {
        const searchQuery = $('#location-search').val().trim();
        
        if (searchQuery === '') {
            alert('Please enter a location to search');
            return;
        }
        
        // Show loading indicator
        $('.search-loading').show();
        $('#search-results').hide().empty();
        
        // Call Nominatim API
        const apiUrl = 'https://nominatim.openstreetmap.org/search';
        const params = {
            q: searchQuery,
            format: 'json',
            limit: 5,
            addressdetails: 1
        };
        
        $.ajax({
            url: apiUrl,
            data: params,
            dataType: 'json',
            headers: {
                'User-Agent': 'WordPress City Coordinates Plugin'
            },
            success: function(data) {
                $('.search-loading').hide();
                displaySearchResults(data);
            },
            error: function(xhr, status, error) {
                $('.search-loading').hide();
                alert('Error searching for location. Please try again.');
                console.error('Search error:', error);
            }
        });
    }
    
    /**
     * Display search results
     */
    function displaySearchResults(results) {
        const $resultsContainer = $('#search-results');
        $resultsContainer.empty();
        
        if (results.length === 0) {
            $resultsContainer.html('<div class="search-result-item">No results found</div>');
            $resultsContainer.show();
            return;
        }
        
        results.forEach(function(result) {
            const displayName = result.display_name;
            const lat = parseFloat(result.lat);
            const lng = parseFloat(result.lon);
            
            const $resultItem = $('<div class="search-result-item"></div>')
                .text(displayName)
                .on('click', function() {
                    updateCoordinates(lat, lng);
                    $resultsContainer.hide();
                    $('#location-search').val(displayName);
                });
            
            $resultsContainer.append($resultItem);
        });
        
        $resultsContainer.show();
    }
    
    /**
     * Update coordinates and map marker
     */
    function updateCoordinates(lat, lng) {
        // Update input fields
        $('#city_latitude').val(lat.toFixed(6));
        $('#city_longitude').val(lng.toFixed(6));
        
        // Update map view
        map.setView([lat, lng], 13);
        
        // Add/update marker
        addMarker(lat, lng);
        
        // Trigger change event to ensure WordPress recognizes the change
        $('#city_latitude, #city_longitude').trigger('change');
    }
    
    /**
     * Add or update marker on map
     */
    function addMarker(lat, lng) {
        // Remove existing marker if any
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Create custom icon
        const customIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        // Add new marker
        marker = L.marker([lat, lng], { 
            icon: customIcon,
            draggable: true 
        }).addTo(map);
        
        // Add popup with coordinates
        marker.bindPopup(`<b>Coordinates:</b><br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`).openPopup();
        
        // Handle marker drag
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });
    }
    
    /**
     * Close search results when clicking outside
     */
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.city-coordinates-search').length) {
            $('#search-results').hide();
        }
    });
    
})(jQuery);
