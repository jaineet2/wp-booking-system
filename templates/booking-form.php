<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wbs-booking-form" id="wbs-booking-form">
    <form action="" method="post">
        <?php wp_nonce_field('wbs_booking_nonce'); ?>
        
        <div class="form-group">
            <label for="booking-date"><?php _e('Select Date', 'wp-booking-system'); ?></label>
            <input type="date" id="booking-date" name="booking_date" required
                   min="<?php echo date('Y-m-d'); ?>"
                   class="form-control">
        </div>
        
        <div class="form-group">
            <label for="booking-time"><?php _e('Select Time', 'wp-booking-system'); ?></label>
            <select id="booking-time" name="booking_time" required class="form-control" disabled>
                <option value=""><?php _e('Select date first', 'wp-booking-system'); ?></option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="customer-name"><?php _e('Your Name', 'wp-booking-system'); ?></label>
            <input type="text" id="customer-name" name="customer_name" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="customer-email"><?php _e('Email Address', 'wp-booking-system'); ?></label>
            <input type="email" id="customer-email" name="customer_email" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="customer-phone"><?php _e('Phone Number', 'wp-booking-system'); ?></label>
            <input type="tel" id="customer-phone" name="customer_phone" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="booking-notes"><?php _e('Additional Notes', 'wp-booking-system'); ?></label>
            <textarea id="booking-notes" name="booking_notes" class="form-control"></textarea>
        </div>
        
        <button type="submit" class="button submit-booking">
            <?php _e('Book Now', 'wp-booking-system'); ?>
        </button>
    </form>
</div>