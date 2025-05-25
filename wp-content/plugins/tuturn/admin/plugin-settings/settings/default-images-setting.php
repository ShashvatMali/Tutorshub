<?php

/**
 * Default Images Settings
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
		'title'            => esc_html__('Default images', 'tuturn'),
		'id'               => 'default_images_settings',
		'subsection'       => false,
		'icon'			   => 'el el-random',
		'fields'           => array(
			array(
				'id'       => 'defaul_student_profile',
				'type'     => 'media',
				'title'    => esc_html__('Student default profile image', 'tuturn'),
				'default'  => array('url' => TUTURN_DIRECTORY_URI . 'public/images/placeholder.jpg'),
			),
			array(
				'id'       => 'defaul_instructor_profile',
				'type'     => 'media',
				'title'    => esc_html__('Instructor default profile image', 'tuturn'),
				'default'  => array('url' => TUTURN_DIRECTORY_URI . 'public/images/placeholder.jpg'),
			),
			array(
				'id'       => 'empty_listing_image',
				'type'     => 'media',
				'title'    => esc_html__('Default listing empty image', 'tuturn'),
			),
		)
	)
);
