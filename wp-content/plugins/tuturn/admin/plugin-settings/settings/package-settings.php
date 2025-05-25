<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Api Settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Plugin_Settings/Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
*/

Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Packages Settings ', 'tuturn' ),
	'id'               => 'package_settings',
	'desc'       	   => '',
	'subsection'       => false,
	'icon'			   => 'el el-braille',	
	'fields'           => array(
		array(
			'id'       => 'package_option',
			'type'     => 'switch',
			'title'    => esc_html__( 'Enable packages', 'tuturn' ),
			'default'  => false,
			'desc'     => esc_html__( 'Enable packages for the instructor, this means by enable this instructor will be able to get online bookings for free', 'tuturn' )
		),
		array(
			'id'       => 'pkg_page_title',
			'type'     => 'text',
			'default'  => esc_html__( 'Expand your great experience', 'tuturn' ),
			'title'    => esc_html__('Package title', 'tuturn'),
			'required'  => array('package_option', 'equals', '1')
		),
		array(
			'id'       => 'pkg_page_sub_title',
			'type'     => 'text',
			'default'  => esc_html__( 'Buy a best price package', 'tuturn' ),
			'title'    => esc_html__('Package subtitle', 'tuturn'),
			'required'  => array('package_option', 'equals', '1')
		),
		array(
			'id'       => 'pkg_page_details',
			'type'     => 'textarea',
			'default'  => esc_html__( 'Get a package today to start using the seamless experience. Make your profile prominent among top-notch instructor and get the attention of the students to profile.', 'tuturn' ),
			'title'    => esc_html__('Package description', 'tuturn'),
			'required'  => array('package_option', 'equals', '1')
		),
		array(
			'id'    =>'divider_student_packages',
			'type'  => 'info',
			'title' => esc_html__('Student packages', 'tuturn'),
			'style' => 'info',
		),
		array(
			'id'       => 'student_package_option',
			'type'     => 'switch',
			'title'    => esc_html__( 'Enable packages', 'tuturn' ),
			'default'  => true,
			'desc'     => esc_html__( 'Enable packages for the students, this means by enable this students must has to buy a package to get appointment with the tutor', 'tuturn' )
		),
		array(
			'id'       => 'student_pkg_page_title',
			'type'     => 'text',
			'default'  => esc_html__( 'Expand your great experience', 'tuturn' ),
			'title'    => esc_html__('Package title', 'tuturn'),
			'required'  => array('student_package_option', 'equals', '1')
		),
		array(
			'id'       => 'student_pkg_page_sub_title',
			'type'     => 'text',
			'default'  => esc_html__( 'Buy a best price package', 'tuturn' ),
			'title'    => esc_html__('Package subtitle', 'tuturn'),
			'required'  => array('student_package_option', 'equals', '1')
		),
		array(
			'id'       => 'student_pkg_page_details',
			'type'     => 'textarea',
			'default'  => esc_html__( 'Get a package today to start using the seamless experience. This will enable you to make a booking with tutor', 'tuturn' ),
			'title'    => esc_html__('Package description', 'tuturn'),
			'required'  => array('student_package_option', 'equals', '1')
		),
	)
));
