<?php

/**
 * Typograpy Settings
 *
 * @package     tuturn
 * @subpackage  tuturn/admin/Theme_Settings/Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        https://amentotech.com/
 * @version     1.0
 * @since       1.0
 */
Redux::setSection(
    $opt_name,
    array(
        'title'         => esc_html__('Colors settings', 'tuturn'),
        'id'            => 'styling_settings',
        'subsection'    => false,
        'icon'          => 'el el-globe',
        'fields'        => array(
            array(
                'id'        => 'tu_primary_color',
                'type'      => 'color',
                'title'     => esc_html__('Primary color', 'tuturn'),
                'subtitle'  => esc_html__('Add primary color', 'tuturn'),
                'default'   => '#6A307D',
            ),
            array(
                'id'        => 'tu_secondary_color',
                'type'      => 'color',
                'title'     => esc_html__('Secondary color', 'tuturn'),
                'subtitle'  => esc_html__('Select secondary color', 'tuturn'),
                'default'   => '#F97316',
            ),
            array(
                'id'        => 'tu_font_color',
                'type'      => 'color',
                'title'     => esc_html__('Font color', 'tuturn'),
                'subtitle'  => esc_html__('Select font color', 'tuturn'),
                'default'   => '#1C1C1C',
            ),
            array(
                'id'        => 'text_dark_color',
                'type'      => 'color',
                'title'     => esc_html__('Text dark color', 'tuturn'),
                'subtitle'  => esc_html__('Select text dark  color', 'tuturn'),
                'default'   => '#484848',
            ),
            array(
                'id'        => 'text_light_color',
                'type'      => 'color',
                'title'     => esc_html__('Text light color', 'tuturn'),
                'subtitle'  => esc_html__('Select light dark  color', 'tuturn'),
                'default'   => '#676767',
            ),
            array(
                'id'        => 'button_bgcolor',
                'type'      => 'color',
                'title'     => esc_html__('Button background color', 'tuturn'),
                'subtitle'  => esc_html__('Select button background color', 'tuturn'),
                'default'   => '#6A307D',
            ),
            array(
                'id'        => 'button_textcolor',
                'type'      => 'color',
                'title'     => esc_html__('Button text color', 'tuturn'),
                'subtitle'  => esc_html__('Select button text color', 'tuturn'),
                'default'   => '#ffffff',
            ),
            array(
                'id'        => 'hyperlink',
                'type'      => 'color',
                'title'     => esc_html__('Hyperlink color', 'tuturn'),
                'subtitle'  => esc_html__('Select hyperlink color', 'tuturn'),
                'default'   => '#1DA1F2',
            ),
            array(
                'id'        => 'footerbg',
                'type'      => 'color',
                'title'     => esc_html__('Footer Background color', 'tuturn'),
                'subtitle'  => esc_html__('Select footer Background color', 'tuturn'),
                'default'   => '#2a1332',
            ),
        )
    )
);
