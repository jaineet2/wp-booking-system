<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get available dates for the next 3 months
 */
function wbs_get_available_dates() {
    $available_dates = array();
    $start_date = current_time('Y-m-d');
    $end_date = date('Y-m-d', strtotime('+3 months'));
    
    $current = strtotime($start_date);
    $end = strtotime($end_date);
    
    while ($current <= $end) {
        $date = date('Y-m-d', $current);
        if (wbs_is_date_available($date)) {
            $available_dates[] = $date;
        }
        $current = strtotime('+1 day', $current);
    }
    
    return $available_dates;
}

/**
 * Check if a specific date is available for booking
 */
function wbs_is_date_available($date) {
    // Get business hours for the day
    $day_of_week = strtolower(date('l', strtotime($date)));
    $business_hours = get_field('business_hours', 'option');
    
    // Check if we're open on this day
    $is_open = false;
    foreach ($business_hours as $hours) {
        if ($hours['day'] === $day_of_week && !empty($hours['opening_time'])) {
            $is_open = true;
            break;
        }
    }
    
    if (!$is_open) {
        return false;
    }
    
    // Check if date is not in blocked dates
    $blocked_dates = get_field('blocked_dates', 'option');
    if (is_array($blocked_dates) && in_array($date, $blocked_dates)) {
        return false;
    }
    
    return true;
}

/**
 * Get bookings for a specific date
 */
function wbs_get_date_bookings($date) {
    $args = array(
        'post_type' => 'booking',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'booking_date',
                'value' => $date,
                'compare' => '=',
            ),
            array(
                'key' => 'booking_status',
                'value' => 'cancelled',
                'compare' => '!=',
            ),
        ),
        'posts_per_page' => -1,
    );
    
    return get_posts($args);
}

/**
 * Calculate available time slots for a date
 */
function wbs_calculate_available_slots($date) {
    $booking_duration = get_field('booking_duration', 'option');
    $buffer_time = get_field('buffer_time', 'option') ?: 0;
    $total_duration = $booking_duration + $buffer_time;
    
    $day_of_week = strtolower(date('l', strtotime($date)));
    $business_hours = get_field('business_hours', 'option');
    $available_slots = array();
    
    // Find business hours for the day
    foreach ($business_hours as $hours) {
        if ($hours['day'] === $day_of_week) {
            $start_time = strtotime($hours['opening_time']);
            $end_time = strtotime($hours['closing_time']);
            
            for ($time = $start_time; $time <= $end_time - ($booking_duration * 60); $time += ($total_duration * 60)) {
                $slot_time = date('H:i', $time);
                if (wbs_is_slot_available($date, $slot_time)) {
                    $available_slots[] = $slot_time;
                }
            }
            break;
        }
    }
    
    return $available_slots;
}

/**
 * Check if a specific time slot is available
 */
function wbs_is_slot_available($date, $time) {
    $booking_capacity = get_field('booking_capacity', 'option') ?: 1;
    $current_bookings = wbs_count_slot_bookings($date, $time);
    
    return $current_bookings < $booking_capacity;
}

/**
 * Count bookings for a specific time slot
 */
function wbs_count_slot_bookings($date, $time) {
    $args = array(
        'post_type' => 'booking',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'booking_date',
                'value' => $date,
            ),
            array(
                'key' => 'booking_time',
                'value' => $time,
            ),
            array(
                'key' => 'booking_status',
                'value' => 'cancelled',
                'compare' => '!=',
            ),
        ),
    );
    
    $bookings = get_posts($args);
    return count($bookings);
}
