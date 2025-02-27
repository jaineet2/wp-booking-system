jQuery(document).ready(function($) {
    const bookingForm = $('#wbs-booking-form');
    const dateInput = $('#booking-date');
    const timeSelect = $('#booking-time');
    
    // Handle date selection
    dateInput.on('change', function() {
        const selectedDate = $(this).val();
        
        if (!selectedDate) {
            timeSelect.prop('disabled', true);
            return;
        }
        
        // Show loading state
        timeSelect.prop('disabled', true);
        timeSelect.html('<option value="">' + wbsAjax.loadingText + '</option>');
        
        // Fetch available time slots
        $.ajax({
            url: wbsAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'check_date_availability',
                date: selectedDate,
                nonce: wbsAjax.nonce
            },
            success: function(response) {
                if (response.success && response.data.slots) {
                    // Populate time slots
                    timeSelect.html('<option value="">' + 
                        wbsAjax.selectTimeText + '</option>');
                    
                    response.data.slots.forEach(function(slot) {
                        timeSelect.append(
                            $('<option></option>').val(slot).text(slot)
                        );
                    });
                    
                    timeSelect.prop('disabled', false);
                } else {
                    timeSelect.html('<option value="">' + 
                        wbsAjax.noSlotsText + '</option>');
                }
            },
            error: function() {
                timeSelect.html('<option value="">' + 
                    wbsAjax.errorText + '</option>');
            }
        });
    });
    
    // Handle form submission
    bookingForm.on('submit', function(e) {
        e.preventDefault();
        
        const submitButton = bookingForm.find('button[type="submit"]');
        submitButton.prop('disabled', true);
        
        const formData = new FormData(this);
        formData.append('action', 'submit_booking');
        formData.append('nonce', wbsAjax.nonce);
        
        $.ajax({
            url: wbsAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Redirect to checkout
                    window.location.href = response.data.checkout_url;
                } else {
                    alert(response.data.message);
                    submitButton.prop('disabled', false);
                }
            },
            error: function() {
                alert(wbsAjax.errorText);
                submitButton.prop('disabled', false);
            }
        });
    });
});