<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php _e('Booking Confirmation', 'wp-booking-system'); ?></title>
</head>
<body style="background-color: #f7f7f7; padding: 20px; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h1 style="color: #333333; margin-bottom: 20px;">
            <?php _e('Booking Confirmation', 'wp-booking-system'); ?>
        </h1>
        
        <p style="color: #666666; font-size: 16px; line-height: 1.5;">
            <?php printf(
                __('Dear %s,', 'wp-booking-system'),
                esc_html($customer_name)
            ); ?>
        </p>
        
        <p style="color: #666666; font-size: 16px; line-height: 1.5;">
            <?php _e('Your booking has been confirmed. Here are the details:', 'wp-booking-system'); ?>
        </p>
        
        <div style="background-color: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 10px 0;">
                <strong><?php _e('Date:', 'wp-booking-system'); ?></strong> 
                <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($booking_date))); ?>
            </p>
            
            <p style="margin: 10px 0;">
                <strong><?php _e('Time:', 'wp-booking-system'); ?></strong> 
                <?php echo esc_html(date_i18n(get_option('time_format'), strtotime($booking_time))); ?>
            </p>
            
            <?php if ($order_id = get_post_meta($booking->ID, 'order_id', true)): ?>
                <p style="margin: 10px 0;">
                    <strong><?php _e('Order Number:', 'wp-booking-system'); ?></strong> 
                    #<?php echo esc_html($order_id); ?>
                </p>
            <?php endif; ?>
        </div>
        
        <?php if ($notes = get_post_meta($booking->ID, 'booking_notes', true)): ?>
            <div style="margin: 20px 0;">
                <h3 style="color: #333333;"><?php _e('Your Notes:', 'wp-booking-system'); ?></h3>
                <p style="color: #666666; font-size: 16px; line-height: 1.5;">
                    <?php echo esc_html($notes); ?>
                </p>
            </div>
        <?php endif; ?>
        
        <p style="color: #666666; font-size: 16px; line-height: 1.5;">
            <?php _e('If you need to make any changes to your booking, please contact us.', 'wp-booking-system'); ?>
        </p>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eeeeee; color: #999999; font-size: 14px;">
            <p>
                <?php printf(
                    __('This email was sent from %s', 'wp-booking-system'),
                    get_bloginfo('name')
                ); ?>
            </p>
        </div>
    </div>
</body>
</html>