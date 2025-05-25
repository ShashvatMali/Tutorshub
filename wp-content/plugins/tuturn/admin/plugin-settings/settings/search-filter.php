<?php

/**
 * Search filter
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
        'title'         => esc_html__('Search Settings', 'tuturn'),
        'id'            => 'search_filter',
        'subsection'    => false,
        'icon'          => 'el el-globe',
        'fields'        =>  array(
            array(
                'id'        => 'hide_price',
                'type'      => 'switch',
                'title'     => esc_html__('Show price', 'tuturn'),
                'subtitle'  => esc_html__('Make it off to hide the price filter in the search instructor page', 'tuturn'),
                'default'   => true,
            ),
            array(
                'id'        => 'hide_rating',
                'type'      => 'switch',
                'title'     => esc_html__('Show rating', 'tuturn'),
                'subtitle'  => esc_html__('Make it off to hide the rating filter in the search instructor page', 'tuturn'),
                'default'   => true,
            ),
            array(
                'id'        => 'tutur_availability',
                'type'      => 'switch',
                'title'     => esc_html__('Show tutur availability', 'tuturn'),
                'subtitle'  => esc_html__('Make it off to hide the tutur availability filter in the search instructor page', 'tuturn'),
                'default'   => true,
            ),
            array(
                'id'        => 'hide_tutor_location',
                'type'      => 'switch',
                'title'     => esc_html__('Show tutor location', 'tuturn'),
                'subtitle'  => esc_html__('Make it off to hide the tutor location filter in the search instructor page', 'tuturn'),
                'default'   => true,
            ),
            array(
                'id'        => 'hide_miscellaneous',
                'type'      => 'switch',
                'title'     => esc_html__('Show miscellaneous', 'tuturn'),
                'subtitle'  => esc_html__('Make it off to hide the miscellaneous filter in the search instructor page', 'tuturn'),
                'default'   => true,
            ),
            array(
                'id'        => 'defult_search_location_type',
                'type'      => 'select',
                'title'     => esc_html__('Default search location', 'tuturn'),
                'desc'      => esc_html__('Please select search location miles/kilometers', 'tuturn'),
                'options'   => array(
                    'miles' => esc_html__('Miles', 'tuturn'),
                    'km' => esc_html__('Kilometers', 'tuturn'),
                ),
                'default'   => 'miles',
            ),
            array(
                'id'        => 'disable_range_slider',
                'type'      => 'switch',
                'title'     => esc_html__('Disable range slider', 'tuturn'),
                'default'   => false,
                'desc'      => esc_html__('Disable range slider for price filter', 'tuturn')
            ),
            array(
                'id'        => 'min_search_price',
                'type'      => 'text',
                'title'     => esc_html__('Min search price', 'tuturn'),
                'default'   => 1,
            ),

            array(
                'id'        => 'max_search_price',
                'type'      => 'text',
                'title'     => esc_html__('Max search price', 'tuturn'),
                'default'   => 5000,
            ),
            array(
                'id'        => 'search_instructor_template',
                'type'      => 'select',
                'title'     => esc_html__('Instructor search template view', 'tuturn'),
                'desc'      => esc_html__('Please seelct instructor search template view', 'tuturn'),
                'options'   => array(
                    'v1' => esc_html__('Listing v1', 'tuturn'),
                    'v2' => esc_html__('Listing v2', 'tuturn'),
                    'v3' => esc_html__('Listing v3', 'tuturn'),
                ),
                'default'   => 'v1',
            ),
        )
    )
);
