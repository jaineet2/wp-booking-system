jQuery(document).ready(function($) {
    // Calendar navigation
    $('.wbs-calendar-nav').on('click', function(e) {
        e.preventDefault();
        const month = $(this).data('month');
        const year = $(this).data('year');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wbs_load_calendar',
                month: month,
                year: year,
                nonce: wbsAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.wbs-calendar-wrapper').html(response.data.html);
                }
            }
        });
    });
    
    // Booking status update
    $('.wbs-status-update').on('change', function() {
        const bookingId = $(this).data('booking-id');
        const status = $(this).val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wbs_update_booking_status',
                booking_id: bookingId,
                status: status,
                nonce: wbsAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });
    
    // Date blocking
    $('.wbs-block-date').on('click', function(e) {
        e.preventDefault();
        const date = $(this).data('date');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wbs_toggle_date_block',
                date: date,
                nonce: wbsAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });
    
    // Initialize datepicker for blocked dates
    if ($('#wbs-blocked-dates').length) {
        $('#wbs-blocked-dates').datepicker({
            multidate: true,
            format: 'yyyy-mm-dd'
        });
    }
});