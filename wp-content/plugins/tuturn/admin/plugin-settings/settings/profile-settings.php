<?php

/**
 * Profile Settings
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
        'title'         => esc_html__('Profile Settings', 'tuturn'),
        'id'            => 'profile_settings',
        'subsection'    => false,
        'icon'          => 'el el-globe',
        'fields'        =>  array(
            array(
                'id'        => 'profile_gender',
                'type'      => 'switch',
                'title'     => esc_html__('Gender option', 'tuturn'),
                'subtitle'  => esc_html__('To show the gender option on the profile settings if enabled.', 'tuturn'),
                'default'   => false,
            ),
            array(
                'id'        => 'profile_grade',
                'type'      => 'switch',
                'title'     => esc_html__('Grade option', 'tuturn'),
                'subtitle'  => esc_html__('To show the grade option on the profile settings if enabled.', 'tuturn'),
                'default'   => false,
            ),
            array(
                'id'        => 'profile_state',
                'type'      => 'switch',
                'title'     => esc_html__('State option', 'tuturn'),
                'subtitle'  => esc_html__('To show the state/city option on the profile settings if enabled.', 'tuturn'),
                'default'   => false,
            ),
            array(
                'id'       => 'show_address_type',
                'type'     => 'select',
                'title'    => esc_html__( 'Show address', 'tuturn' ),
                'desc'     => esc_html__( 'Select to show the address type of the detail page. City state will only display if above settings is enabled', 'tuturn' ),
                'options'   => array(
                    'address' => esc_html__('Address', 'tuturn'),
                    'city_state_country' => esc_html__('City, State, Country', 'tuturn'),
                    'city_state' => esc_html__('City, State', 'tuturn'),
                ),
                'default'  => 'address',
            ),
            array(
                'id'        => 'profile_hourlyprice',
                'type'      => 'switch',
                'title'     => esc_html__('Hourly price', 'tuturn'),
                'subtitle'  => esc_html__('To make your hourly price optional.', 'tuturn'),
                'default'   => false,
            ),
            array(
                'id'        => 'teach_settings',
                'type'      => 'radio',
                'title'     => esc_html__('I can teach settings', 'tuturn'),
                'subtitle'  => __('<strong>Default settings</strong><br><br>This will be as (My home, Student home, Online)<br><br><strong>Custom settings</strong><br><br>This will be as Online/Offline(Student place, Tutor place => Take tutor address with country, state, city, postal code)<br>', 'tuturn'),
                'options'   => array(
                    'default' => esc_html__('Default settings','tuturn' ),
                    'custom' => esc_html__('Custom settings','tuturn' ),
                ),
                'default' => 'default'
            ),
            array(
                'id'        => 'eductation_date_option',
                'type'      => 'switch',
                'title'     => esc_html__('Enable/disable education dates', 'tuturn'),
                'subtitle'  => esc_html__('Enable/disable education start & end date', 'tuturn'),
                'desc'      => esc_html__('When disabled it will show the input field instead.', 'tuturn'),
                'default'  => false,
            )
        )
    )
);
