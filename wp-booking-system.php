<?php
/**
 * Plugin Name: WP Booking System
 * Plugin URI: https://example.com/wp-booking-system
 * Description: A custom WordPress booking system with WooCommerce integration
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: wp-booking-system
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WBS_VERSION', '1.0.0');
define('WBS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WBS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once WBS_PLUGIN_DIR . 'includes/functions.php';
require_once WBS_PLUGIN_DIR . 'includes/acf-fields.php';
require_once WBS_PLUGIN_DIR . 'includes/woocommerce-integration.php';
require_once WBS_PLUGIN_DIR . 'includes/availability.php';
require_once WBS_PLUGIN_DIR . 'includes/email-notifications.php';
require_once WBS_PLUGIN_DIR . 'includes/ajax-handlers.php';

// Initialize plugin
function wbs_init() {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'wbs_woocommerce_missing_notice');
        return;
    }

    // Check if ACF is active
    if (!class_exists('ACF')) {
        add_action('admin_notices', 'wbs_acf_missing_notice');
        return;
    }

    // Enqueue scripts and styles
    add_action('wp_enqueue_scripts', 'wbs_enqueue_scripts');
    add_action('admin_enqueue_scripts', 'wbs_admin_enqueue_scripts');

    // Initialize booking functionality
    wbs_initialize_booking_system();
}
add_action('plugins_loaded', 'wbs_init');

// Enqueue frontend scripts and styles
function wbs_enqueue_scripts() {
    wp_enqueue_style('wbs-frontend-style', 
        WBS_PLUGIN_URL . 'assets/css/frontend-style.css', 
        array(), 
        WBS_VERSION
    );
    
    wp_enqueue_script('wbs-frontend-script', 
        WBS_PLUGIN_URL . 'assets/js/frontend-script.js', 
        array('jquery'), 
        WBS_VERSION, 
        true
    );

    wp_localize_script('wbs-frontend-script', 'wbsAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wbs_nonce')
    ));
}

// Enqueue admin scripts and styles
function wbs_admin_enqueue_scripts() {
    wp_enqueue_style('wbs-admin-style', 
        WBS_PLUGIN_URL . 'assets/css/admin-style.css', 
        array(), 
        WBS_VERSION
    );
    
    wp_enqueue_script('wbs-admin-script', 
        WBS_PLUGIN_URL . 'assets/js/admin-script.js', 
        array('jquery'), 
        WBS_VERSION, 
        true
    );
}

// WooCommerce missing notice
function wbs_woocommerce_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php _e('WP Booking System requires WooCommerce to be installed and activated.', 'wp-booking-system'); ?></p>
    </div>
    <?php
}

// ACF missing notice
function wbs_acf_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php _e('WP Booking System requires Advanced Custom Fields to be installed and activated.', 'wp-booking-system'); ?></p>
    </div>
    <?php
}

// Activation hook
function wbs_activate() {
    // Create necessary database tables
    wbs_create_tables();
    
    // Set up default options
    wbs_set_default_options();
    
    // Clear permalinks
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wbs_activate');

// Deactivation hook
function wbs_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wbs_deactivate');
