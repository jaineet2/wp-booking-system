<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Send booking confirmation email to customer
 */
function wbs_send_booking_confirmation($booking_id) {
    $booking = get_post($booking_id);
    $customer_email = get_post_meta($booking_id, 'customer_email', true);
    $customer_name = get_post_meta($booking_id, 'customer_name', true);
    $booking_date = get_post_meta($booking_id, 'booking_date', true);
    $booking_time = get_post_meta($booking_id, 'booking_time', true);
    
    // Get email template
    ob_start();
    include WBS_PLUGIN_DIR . 'templates/email/booking-confirmation.php';
    $message = ob_get_clean();
    
    // Email headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
    );
    
    // Send email
    wp_mail(
        $customer_email,
        sprintf(__('Booking Confirmation - %s', 'wp-booking-system'), get_bloginfo('name')),
        $message,
        $headers
    );
}

/**
 * Send notification email to admin
 */
function wbs_send_admin_notification($booking_id) {
    $booking = get_post($booking_id);
    $admin_email = get_bloginfo('admin_email');
    $customer_name = get_post_meta($booking_id, 'customer_name', true);
    $customer_email = get_post_meta($booking_id, 'customer_email', true);
    $booking_date = get_post_meta($booking_id, 'booking_date', true);
    $booking_time = get_post_meta($booking_id, 'booking_time', true);
    
    // Get email template
    ob_start();
    include WBS_PLUGIN_DIR . 'templates/email/admin-notification.php';
    $message = ob_get_clean();
    
    // Email headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
    );
    
    // Send email
    wp_mail(
        $admin_email,
        sprintf(__('New Booking - %s', 'wp-booking-system'), $customer_name),
        $message,
        $headers
    );
}

/**
 * Send booking cancellation email
 */
function wbs_send_booking_cancellation($booking_id) {
    $booking = get_post($booking_id);
    $customer_email = get_post_meta($booking_id, 'customer_email', true);
    
    $message = sprintf(
        __('Your booking for %s at %s has been cancelled. If you have any questions, please contact us.', 'wp-booking-system'),
        get_post_meta($booking_id, 'booking_date', true),
        get_post_meta($booking_id, 'booking_time', true)
    );
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
    );
    
    wp_mail(
        $customer_email,
        __('Booking Cancellation Notice', 'wp-booking-system'),
        $message,
        $headers
    );
}

