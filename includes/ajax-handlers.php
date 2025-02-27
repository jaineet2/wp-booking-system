<?php
if (!defined('ABSPATH')) {
    exit;
}

// Check date availability
add_action('wp_ajax_check_date_availability', 'wbs_ajax_check_date_availability');
add_action('wp_ajax_nopriv_check_date_availability', 'wbs_ajax_check_date_availability');

function wbs_ajax_check_date_availability() {
    check_ajax_referer('wbs_nonce', 'nonce');
    
    $date = sanitize_text_field($_POST['date']);
    $available_slots = wbs_get_available_time_slots($date);
    
    wp_send_json_success(array(
        'slots' => $available_slots
    ));
}

// Submit booking form
add_action('wp_ajax_submit_booking', 'wbs_ajax_submit_booking');
add_action('wp_ajax_nopriv_submit_booking', 'wbs_ajax_submit_booking');

function wbs_ajax_submit_booking() {
    check_ajax_referer('wbs_nonce', 'nonce');
    
    $booking_data = array(
        'date' => sanitize_text_field($_POST['date']),
        'time' => sanitize_text_field($_POST['time']),
        'name' => sanitize_text_field($_POST['name']),
        'email' => sanitize_email($_POST['email']),
        'phone' => sanitize_text_field($_POST['phone']),
        'notes' => sanitize_textarea_field($_POST['notes'])
    );
    
    $booking_id = wbs_create_booking($booking_data);
    
    if ($booking_id) {
        $order_id = get_post_meta($booking_id, 'order_id', true);
        $order = wc_get_order($order_id);
        
        wp_send_json_success(array(
            'booking_id' => $booking_id,
            'checkout_url' => $order->get_checkout_payment_url()
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('Failed to create booking. Please try again.', 'wp-booking-system')
        ));
    }
}