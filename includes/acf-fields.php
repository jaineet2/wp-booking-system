<?php
if (!defined('ABSPATH')) {
    exit;
}

function wbs_register_acf_fields() {
    if (function_exists('acf_add_local_field_group')):

        acf_add_local_field_group(array(
            'key' => 'group_booking_settings',
            'title' => 'Booking Settings',
            'fields' => array(
                array(
                    'key' => 'field_booking_duration',
                    'label' => 'Booking Duration (minutes)',
                    'name' => 'booking_duration',
                    'type' => 'number',
                    'required' => 1,
                    'default_value' => 60,
                    'min' => 15,
                    'max' => 480,
                ),
                array(
                    'key' => 'field_booking_capacity',
                    'label' => 'Maximum Bookings per Slot',
                    'name' => 'booking_capacity',
                    'type' => 'number',
                    'required' => 1,
                    'default_value' => 1,
                    'min' => 1,
                ),
                array(
                    'key' => 'field_business_hours',
                    'label' => 'Business Hours',
                    'name' => 'business_hours',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_day_of_week',
                            'label' => 'Day',
                            'name' => 'day',
                            'type' => 'select',
                            'choices' => array(
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday',
                            ),
                        ),
                        array(
                            'key' => 'field_opening_time',
                            'label' => 'Opening Time',
                            'name' => 'opening_time',
                            'type' => 'time_picker',
                        ),
                        array(
                            'key' => 'field_closing_time',
                            'label' => 'Closing Time',
                            'name' => 'closing_time',
                            'type' => 'time_picker',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_booking_form_fields',
                    'label' => 'Additional Booking Form Fields',
                    'name' => 'booking_form_fields',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_form_field_label',
                            'label' => 'Field Label',
                            'name' => 'label',
                            'type' => 'text',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_form_field_type',
                            'label' => 'Field Type',
                            'name' => 'type',
                            'type' => 'select',
                            'choices' => array(
                                'text' => 'Text',
                                'textarea' => 'Textarea',
                                'email' => 'Email',
                                'tel' => 'Phone',
                                'select' => 'Select',
                            ),
                        ),
                        array(
                            'key' => 'field_form_field_required',
                            'label' => 'Required',
                            'name' => 'required',
                            'type' => 'true_false',
                            'ui' => 1,
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'booking-settings',
                    ),
                ),
            ),
        ));

    endif;
}
add_action('acf/init', 'wbs_register_acf_fields');

// Add options page
function wbs_add_options_page() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => 'Booking Settings',
            'menu_title' => 'Booking Settings',
            'menu_slug' => 'booking-settings',
            'capability' => 'manage_options',
            'position' => '59.5',
            'icon_url' => 'dashicons-calendar-alt',
        ));
    }
}
add_action('acf/init', 'wbs_add_options_page');