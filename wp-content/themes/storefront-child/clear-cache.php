<?php
/**
 * Cache Clear Script
 * 
 * Temporary script to clear all theme caches
 * Run once after deployment, then delete this file
 *
 * Usage: Visit this file in browser or run via WP-CLI
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You do not have permission to access this page.');
}

echo '<h1>Clearing Storefront Child Theme Caches</h1>';

// Clear WordPress object cache
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo '<p>✅ WordPress Object Cache cleared</p>';
}

// Clear cities cache
if (class_exists('Storefront_Child_Cities_Query')) {
    Storefront_Child_Cities_Query::clear_cache();
    echo '<p>✅ Cities cache cleared</p>';
}

// Clear weather cache
if (class_exists('Storefront_Child_Weather_API')) {
    Storefront_Child_Weather_API::clear_cache();
    echo '<p>✅ Weather cache cleared</p>';
}

// Clear transients
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_storefront_child_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_storefront_child_%'");
echo '<p>✅ Transients cleared</p>';

// Flush rewrite rules
flush_rewrite_rules();
echo '<p>✅ Rewrite rules flushed</p>';

echo '<hr>';
echo '<h2>✅ All caches cleared successfully!</h2>';
echo '<p><strong>Important:</strong> Delete this file (clear-cache.php) after use for security.</p>';
echo '<p><a href="' . admin_url() . '">Go to Dashboard</a></p>';
