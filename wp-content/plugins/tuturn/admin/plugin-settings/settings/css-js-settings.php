<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Custom Scripts Settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Plugin_Settings/Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
*/

Redux::setSection( $opt_name, array(
		'title'      => esc_html__( 'CSS/JS Scripts', 'tuturn' ),
		'id'         => 'custom_code',
		'desc'       => '',
		'icon' 		 => 'el el-css',
		'subsection'       => false,
		'fields'     => array(
			array(
				'id'       => 'custom_css',
				'type'     => 'ace_editor',
				'title'    => esc_html__('Custom CSS', 'tuturn'),
				'subtitle' => esc_html__('Paste your CSS code here.', 'tuturn'),
				'mode'     => 'css',
				'theme'    => 'monokai',
				'desc'     => '',
				'default'  => ""
			),
			array(
				'id'       => 'custom_js',
				'type'     => 'ace_editor',
				'title'    => esc_html__('Custom JS', 'tuturn'),
				'subtitle' => esc_html__('Paste your JS code here.', 'tuturn'),
				'mode'     => 'css',
				'theme'    => 'monokai',
				'desc'     => '',
				'default'  => ""
			),
		)
	)
);
