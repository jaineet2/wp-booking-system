<?php
if (!defined('ABSPATH')) {
    exit;
}

function wbs_create_woocommerce_order($booking_id, $data) {
    // Create WooCommerce order
    $order = wc_create_order();
    
    // Get booking product
    $product_id = get_option('wbs_booking_product_id');
    $product = wc_get_product($product_id);
    
    if (!$product) {
        return false;
    }
    
    // Add product to order
    $order->add_product($product, 1);
    
    // Set customer data
    $order->set_billing_first_name($data['name']);
    $order->set_billing_email($data['email']);
    if (!empty($data['phone'])) {
        $order->set_billing_phone($data['phone']);
    }
    
    // Add booking details to order notes
    $order->add_order_note(sprintf(
        __('Booking Details:\nDate: %s\nTime: %s\nBooking ID: %s', 'wp-booking-system'),
        $data['date'],
        $data['time'],
        $booking_id
    ));
    
    // Calculate totals
    $order->calculate_totals();
    
    // Store booking ID in order meta
    update_post_meta($order->get_id(), '_booking_id', $booking_id);
    
    return $order->get_id();
}

// Handle order status changes
add_action('woocommerce_order_status_changed', 'wbs_handle_order_status_change', 10, 4);

function wbs_handle_order_status_change($order_id, $old_status, $new_status, $order) {
    $booking_id = get_post_meta($order_id, '_booking_id', true);
    
    if (!$booking_id) {
        return;
    }
    
    switch ($new_status) {
        case 'completed':
            // Confirm booking
            update_post_meta($booking_id, 'booking_status', 'confirmed');
            wbs_send_booking_confirmation($booking_id);
            break;
            
        case 'cancelled':
            // Cancel booking
            update_post_meta($booking_id, 'booking_status', 'cancelled');
            wbs_send_booking_cancellation($booking_id);
            break;
    }
}
