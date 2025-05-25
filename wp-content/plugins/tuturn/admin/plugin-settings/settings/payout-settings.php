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
	'title'            => esc_html__( 'Payout Settings ', 'tuturn' ),
	'id'               => 'payout_settings',
	'desc'       	   => '',
	'subsection'       => false,
	'icon'			   => 'el el-braille',	
	'fields'           => array(	
			array(
				'id' 		=> 'admin_commision',
				'type' 		=> 'slider',
				'title' 	=> esc_html__('Set admin commision', 'tuturn'),
				'desc' 		=> esc_html__('Select service commission in percentage ( % ), set it to 0 to make commission free website', 'tuturn'),
				"default" 	=> 1,
				"min" 		=> 0,
				"step" 		=> 1,
				"max" 		=> 100,
				'display_value' => 'label',
			),
			array(
				'id'       => 'min_amount',
				'type'     => 'text',
				'title'    => esc_html__('Add min amount', 'tuturn'), 
				'desc'     => esc_html__('Add minimum amount that a tutor/instructor can withdraw', 'tuturn'),
				'default'  => '',
			),
		)
	)
);
