               data-year="<?php echo $prev_year; ?>">
                &larr; <?php _e('Previous', 'wp-booking-system'); ?>
            </a>
            <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" 
               class="button wbs-calendar-nav" 
               data-month="<?php echo $next_month; ?>" 
               data-year="<?php echo $next_year; ?>">
                <?php _e('Next', 'wp-booking-system'); ?> &rarr;
            </a>
        </div>
    </div>
    
    <div class="calendar-grid">
        <?php
        // Display week days header
        $week_days = array(
            __('Sun', 'wp-booking-system'),
            __('Mon', 'wp-booking-system'),
            __('Tue', 'wp-booking-system'),
            __('Wed', 'wp-booking-system'),
            __('Thu', 'wp-booking-system'),
            __('Fri', 'wp-booking-system'),
            __('Sat', 'wp-booking-system')
        );
        
        foreach ($week_days as $day) {
            echo '<div class="calendar-header-day">' . esc_html($day) . '</div>';
        }
        
        // Add blank days before start of month
        for ($i = 0; $i < $day_of_week; $i++) {
            echo '<div class="calendar-day empty"></div>';
        }
        
        // Display days of month
        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = sprintf('%04d-%02d-%02d', $current_year, $current_month, $day);
            $bookings = wbs_get_date_bookings($date);
            $is_today = $date === current_time('Y-m-d');
            $is_past = strtotime($date) < strtotime(current_time('Y-m-d'));
            
            $classes = array('calendar-day');
            if ($is_today) $classes[] = 'today';
            if ($is_past) $classes[] = 'past';
            if (!empty($bookings)) $classes[] = 'has-bookings';
            
            ?>
            <div class="<?php echo implode(' ', $classes); ?>">
                <div class="day-number"><?php echo $day; ?></div>
                <?php if (!empty($bookings)): ?>
                    <div class="booking-count">
                        <?php echo sprintf(
                            _n('%s booking', '%s bookings', count($bookings), 'wp-booking-system'),
                            count($bookings)
                        ); ?>
                    </div>
                    <div class="booking-list">
                        <?php foreach ($bookings as $booking): ?>
                            <?php
                            $time = get_post_meta($booking->ID, 'booking_time', true);
                            $customer = get_post_meta($booking->ID, 'customer_name', true);
                            ?>
                            <div class="booking-item">
                                <span class="booking-time"><?php echo esc_html($time); ?></span>
                                <span class="booking-customer"><?php echo esc_html($customer); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
        
        // Add blank days after end of month
        $total_days = $day_of_week + $days_in_month;
        $remaining_days = ceil($total_days / 7) * 7 - $total_days;
        for ($i = 0; $i < $remaining_days; $i++) {
            echo '<div class="calendar-day empty"></div>';
        }
        ?>
    </div>
</div>