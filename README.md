# WordPress Booking System

This is a custom WordPress booking system with WooCommerce payments, availability checking, email notifications, and a calendar display.

## Features
- ✅ Custom Booking Form
- ✅ WooCommerce Payment Integration
- ✅ Email Notifications (User & Admin)
- ✅ Availability Checking (Prevents Double Bookings)
- ✅ Calendar Display for Available Dates

## Installation

### 1. Install WordPress & WooCommerce
- Ensure WordPress and WooCommerce are installed and activated.
- Create a WooCommerce product for booking payments.

### 2. Add Code to `functions.php`
Copy the provided PHP functions into your theme's `functions.php` file.

### 3. Add Booking Form
Use the shortcode `[booking_form]` on any page to display the booking form.

### 4. Add Calendar Display
Use the shortcode `[booking_calendar]` to show available dates.

## How It Works
1. Users check available dates using the calendar.
2. They fill out the booking form and proceed to WooCommerce checkout.
3. After payment, the booking is saved in WordPress.
4. Email confirmations are sent to both the user and admin.
5. Admin can view all bookings in the WordPress dashboard.

## Future Enhancements
- ✅ Add cancellation and rescheduling options.
- ✅ Implement admin approval for bookings.
- ✅ Display booking history for users.

## Contributing
Pull requests are welcome! If you find issues, open an issue in the repository.

## License
MIT License

