<?php
/**
 * Template Settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Plugin_Settings/Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
*/

/**
 * @init            Check if chat solution installed
 * @package         Amentotech
 * @since           1.0
 * @desc            If plugin activated return true else false
 */

$inbox_template	= array();
$inbox_template	= array(
	'id'    	=> 'tpl_inbox',
	'type'  	=> 'select',
	'title' 	=> esc_html__( 'Inbox page', 'tuturn' ),
	'data'  	=> 'pages',
	'desc'      => esc_html__('', 'tuturn'),
	'desc' 	   => wp_kses( __( 'Select page for the inbox page. Please note you must install and activate the WP Guppy Plugin <a href="https://codecanyon.net/item/wpguppy-a-live-chat-plugin-for-wordpress/34619534" target="_blank"> Get chat plugin </a>', 'tuturn' ), array(
		'a' => array(
			'href' => array(),
			'class' => array(),
			'title' => array()
			),
		'br' => array(),
		'em' => array(),
		'strong' => array(),
		) ),
);
Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Template Settings', 'tuturn' ),
        'id'               => 'template_settings',
        'desc'       	   => '',
		'icon' 			   => 'el el-search',
		'subsection'       => false,
        'fields'           => array(			
			array(
				'id'    	=> 'tpl_dashboard',
				'type'  	=> 'select',
				'title' 	=> esc_html__( 'Dashboard', 'tuturn' ),
				'data'  	=> 'pages',
				'desc'      => esc_html__('Select dashboard page template', 'tuturn'),
			),
			array(
				'id'    	=> 'tpl_instructor_search',
				'type'  	=> 'select',
				'title' 	=> esc_html__( 'Instructor search page', 'tuturn' ),
				'data'  	=> 'pages',
				'desc'      => esc_html__('Select page for the instructor search listing', 'tuturn'),
			),
			array(
				'id'    	=> 'tpl_package_page',
				'type'  	=> 'select',
				'title' 	=> esc_html__( 'Instructor packages', 'tuturn' ),
				'data'  	=> 'pages',
				'desc'      => esc_html__('Select page for the instructor packages', 'tuturn'),
			),
			$inbox_template,
			array(
				'id'    	=> 'tpl_login',
				'type'  	=> 'select',
				'title' 	=> esc_html__( 'Login page', 'tuturn' ),
				'data'  	=> 'pages',
				'desc'      => esc_html__('Select login page template', 'tuturn'),
			),
			array(
				'id'    	=> 'tpl_registration',
				'type'  	=> 'select',
				'title' 	=> esc_html__( 'Registration page', 'tuturn' ),
				'data'  	=> 'pages',
				'desc'      => esc_html__('Select registration page template', 'tuturn'),
			),
      		array(
				'id'    	=> 'tpl_reset',
				'type'  	=> 'select',
				'title' 	=> esc_html__( 'Reset password page', 'tuturn' ),
				'data'  	=> 'pages',
				'desc'      => esc_html__('Select reset password page template', 'tuturn'),
			),
      		array(
				'id'    	=> 'tpl_terms_conditions',
				'type'  	=> 'select',
				'title' 	=> esc_html__( 'Terms & conditions', 'tuturn' ),
				'data'  	=> 'pages',
				'desc'      => esc_html__('Select terms & conditions template', 'tuturn'),
			),
			
		)
	)
);
 