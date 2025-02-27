<?php
// These are additional functions to be added to the existing functions.php

/**
 * Create necessary database tables
 */
function wbs_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Create custom tables if needed
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wbs_availability (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        date date NOT NULL,
        time time NOT NULL,
        capacity int(11) NOT NULL DEFAULT '1',
        booked int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY  (id),
        KEY date (date),
        KEY time (time)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Set default options
 */
function wbs_set_default_options() {
    $default_options = array(
        'booking_duration' => 60, // minutes
        'buffer_time' => 15, // minutes
        'booking_capacity' => 1,
        'minimum_notice' => 24, // hours
        'maximum_advance' => 90, // days
        'currency' => 'USD',
        'email_notifications' => true,
        'admin_notification_email' => get_option('admin_email'),
        'business_hours' => array(
            'monday' => array('09:00', '17:00'),
            'tuesday' => array('09:00', '17:00'),
            'wednesday' => array('09:00', '17:00'),
            'thursday' => array('09:00', '17:00'),
            'friday' => array('09:00', '17:00'),
            'saturday' => array(),
            'sunday' => array(),
        )
    );

    foreach ($default_options as $key => $value) {
        if (get_option('wbs_' . $key) === false) {
            update_option('wbs_' . $key, $value);
        }
    }
}

/**
 * Format date according to WordPress settings
 */
function wbs_format_date($date) {
    return date_i18n(get_option('date_format'), strtotime($date));
}

/**
 * Format time according to WordPress settings
 */
function wbs_format_time($time) {
    return date_i18n(get_option('time_format'), strtotime($time));
}

/**
 * Get booking price
 */
function wbs_get_booking_price($booking_id = null) {
    if (!$booking_id) {
        $product_id = get_option('wbs_booking_product_id');
        $product = wc_get_product($product_id);
        return $product ? $product->get_price() : 0;
    }

    $order_id = get_post_meta($booking_id, 'order_id', true);
    if ($order_id) {
        $order = wc_get_order($order_id);
        return $order ? $order->get_total() : 0;
    }

    return 0;
}

/**
 * Check if a date is blocked
 */
function wbs_is_date_blocked($date) {
    $blocked_dates = get_option('wbs_blocked_dates', array());
    return in_array($date, $blocked_dates);
}

/**
 * Add booking menu page
 */
function wbs_add_menu_page() {
    add_menu_page(
        __('Bookings', 'wp-booking-system'),
        __('Bookings', 'wp-booking-system'),
        'manage_options',
        'wbs-bookings',
        'wbs_bookings_page',
        'dashicons-calendar-alt',
        30
    );

    add_submenu_page(
        'wbs-bookings',
        __('Settings', 'wp-booking-system'),
        __('Settings', 'wp-booking-system'),
        'manage_options',
        'wbs-settings',
        'wbs_settings_page'
    );
}
add_action('admin_menu', 'wbs_add_menu_page');

/**
 * Render bookings page
 */
function wbs_bookings_page() {
    include WBS_PLUGIN_DIR . 'templates/admin/bookings.php';
}

/**
 * Render settings page
 */
function wbs_settings_page() {
    include WBS_PLUGIN_DIR . 'templates/admin/settings.php';
}

/**
 * Register booking widgets
 */
function wbs_register_widgets() {
    register_widget('WBS_Calendar_Widget');
    register_widget('WBS_Upcoming_Bookings_Widget');
}
add_action('widgets_init', 'wbs_register_widgets');

/**
 * Get next available slot
 */
function wbs_get_next_available_slot() {
    $date = current_time('Y-m-d');
    $time = current_time('H:i:s');
    
    for ($i = 0; $i < 30; $i++) {
        $check_date = date('Y-m-d', strtotime("+$i days", strtotime($date)));
        $available_slots = wbs_calculate_available_slots($check_date);
        
        if (!empty($available_slots)) {
            foreach ($available_slots as $slot) {
                if ($check_date === $date && strtotime($slot) <= strtotime($time)) {
                    continue;
                }
                return array(
                    'date' => $check_date,
                    'time' => $slot
                );
            }
        }
    }
    
    return false;
}

/**
 * Generate JSON-LD schema for booking
 */
function wbs_generate_booking_schema($booking_id) {
    $booking = get_post($booking_id);
    $date = get_post_meta($booking_id, 'booking_date', true);
    $time = get_post_meta($booking_id, 'booking_time', true);
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Reservation',
        'reservationId' => $booking_id,
        'reservationStatus' => get_post_meta($booking_id, 'booking_status', true),
        'underName' => array(
            '@type' => 'Person',
            'name' => get_post_meta($booking_id, 'customer_name', true)
        ),
        'reservationFor' => array(
            '@type' => 'Service',
            'name' => get_bloginfo('name'),
            'url' => get_bloginfo('url')
        ),
        'startTime' => date('c', strtotime("$date $time"))
    );
    
    return wp_json_encode($schema);
}

/**
 * Add schema to booking confirmation page
 */
function wbs_add_booking_schema() {
    if (!is_page('booking-confirmation')) {
        return;
    }
    
    $booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
    if (!$booking_id) {
        return;
    }
    
    $schema = wbs_generate_booking_schema($booking_id);
    if ($schema) {
        echo '<script type="application/ld+json">' . $schema . '</script>';
    }
}
add_action('wp_head', 'wbs_add_booking_schema');