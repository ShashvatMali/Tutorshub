<?php

/**
 * Booking Settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Plugin_Settings/Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */
Redux::setSection(
    $opt_name,
    array(
        'title'         => esc_html__('Booking settings', 'tuturn'),
        'id'            => 'booking_settings',
        'subsection'    => false,
        'icon'          => 'el el-calendar',
        'fields'        =>  array(
            array(
                'id'        => 'booking_decline_reasons',
                'type'      => 'multi_text',
                'title'     => esc_html__('Booking decline', 'tuturrn'),
                'subtitle'  => esc_html__('Add booking declined reasons', 'tuturrn'),
                'default'   => array(
                    esc_html__('Instructor not available', 'tuturrn'),
                    esc_html__('Not available to serve this time', 'tuturrn'),
                    esc_html__('Unkown requests', 'tuturrn'),
                    esc_html__('Lack of important details from user', 'tuturrn'),
                    esc_html__('Web designing', 'tuturrn'),
                    esc_html__('Miscellaneous', 'tuturrn'),
                ),
                'desc'      => esc_html__('Add multiple declined reasons', 'tuturrn')
            ),
            array(
                'id'        => 'booking_cancle_reasons',
                'type'      => 'multi_text',
                'title'     => esc_html__('Booking cancel', 'tuturrn'),
                'subtitle'  => esc_html__('Add booking cancel reasons', 'tuturrn'),
                'default'   => array(
                    esc_html__('Instructor not available', 'tuturrn'),
                    esc_html__('Not available to serve this time', 'tuturrn'),
                    esc_html__('Unkown requests', 'tuturrn'),
                    esc_html__('Lack of important details from user', 'tuturrn'),
                    esc_html__('Web designing', 'tuturrn'),
                    esc_html__('Miscellaneous', 'tuturrn'),
                ),
                'desc'      => esc_html__('Add multiple cancel reasons', 'tuturrn')
            ),
            array(
                'id'         => 'booking_interval',
                'type'         => 'slider',
                'title'     => esc_html__('Set booking time interval', 'tuturn'),
                'desc'         => esc_html__('Select booking time interval', 'tuturn'),
                "default"     => 60,
                "min"         => 0,
                "step"         => 5,
                "max"         => 120,
                'display_value' => 'label',
            ),
            array(
                'id'       => 'hide_conteact_details',
                'type'     => 'switch',
                'title'    => esc_html__('Show contact detail', 'tuturn'),
                'default'  => false,
                'desc'     => esc_html__('Show contact details to students only if student has at-least one booking with tutor', 'tuturn')
            ),
            array(
                'id'        => 'booking_option',
                'type'      => 'select',
                'title'     => esc_html__('Allow booking?', 'tuturn'),
                'desc'      => esc_html__('You can enable with checkout page and without checkout page. If without checkout page selected, booking will be done without payment. By disable selection booking feature will be disabled.', 'tuturn'),
                'options'   => array(
                    'yes'   	=> esc_html__('With checkout page', 'tuturn'),
                    'no'    	=> esc_html__('Without checkout page', 'tuturn'),
                    'disable'	=> esc_html__('Disable', 'tuturn'),
                ),
                'default'   => 'yes',
            ),
            array(
                'id'        => 'allow_free_booking',
                'type'      => 'select',
                'title'     => esc_html__('Allow free booking', 'tuturn'),
                'desc'      => esc_html__('Allow free direct booking with 0 hourly price and skip the checkout process.', 'tuturn'),
                'options'   => array(
                    'yes'         => esc_html__('Yes', 'tuturn'),
                    'no'          => esc_html__('No', 'tuturn')
                ),
                'default'   => 'no',
                'required'  => array('booking_option', 'equals', 'yes')
            ),
            array(
                'id'            => 'expired_booking_complete_days',
                'type'          => 'slider',
                'title'         => esc_html__('Expired booking complete', 'tuturn'),
                'desc'          => esc_html__('Expired booking auto complete after these days if student does not complete the booking.', 'tuturn'),
                "default"       => 2,
                "min"           => 2,
                "step"          => 1,
                "max"           => 30
            ),
        )
    )
);
