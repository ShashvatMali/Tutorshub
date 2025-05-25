<?php

/**
 * General Settings
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
        'title'         => esc_html__('404 page settings', 'tuturn'),
        'id'            => '404_page_settings',
        'subsection'    => false,
        'icon'          => 'el el-globe',
        'fields'        =>  array(
            array(
                'id'        => 'title_404',
                'type'      => 'text',
                'title'     => esc_html__('404 page title', 'tuturn'),
            ),
            array(
                'id'        => 'subtitle_404',
                'type'      => 'text',
                'title'     => esc_html__('404 page sub title', 'tuturn'),
            ),
            array(
                'id'       => 'description_404',
                'type'     => 'textarea',
                'title'    => esc_html__('404 page description', 'tuturn' ),
                'default'  => '',
            ),
            array(
                'id'		=> 'image_404',
                'type' 		=> 'media',
                'url'		=> true,
                'title' 	=> esc_html__('404 image', 'tuturn'),
                'desc' 		=> esc_html__('Upload site 404 page image, leave it empty to hide', 'tuturn'),
            ),
        )
    )
);
