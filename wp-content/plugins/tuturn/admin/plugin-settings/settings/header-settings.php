<?php
/**
 * Header Settings
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
$theme_version 		= wp_get_theme();
if(!empty($theme_version->get( 'TextDomain' )) && ( $theme_version->get( 'TextDomain' ) === 'tuturn' || $theme_version->get( 'TextDomain' ) === 'tuturn-child' || $theme_version->get( 'TextDomain' ) === 'tuteur' || $theme_version->get( 'TextDomain' ) === 'tuteur-child' )){
	Redux::setSection( $opt_name, array(
			'title'            => esc_html__( 'Header Settings', 'tuturn' ),
			'id'               => 'header_settings',
			'icon'			   => 'el el-align-justify',
			'subsection'       => false,
			'fields'           => array(
				array(
					'id'		=> 'logo',
					'type' 		=> 'media',
					'url'		=> true,
					'title' 	=> esc_html__('Logo', 'tuturn'),
					'desc' 		=> esc_html__('Upload site header logo.', 'tuturn'),
				),
				array(
					'id'		=> 'transparent_logo',
					'type' 		=> 'media',
					'url'		=> true,
					'title' 	=> esc_html__('Transparent logo', 'tuturn'),
					'desc' 		=> esc_html__('Upload site header transparent logo.', 'tuturn'),
				),
				array(
					'id' 		=> 'logo_wide',
					'type' 		=> 'slider',
					'title' 	=> esc_html__('Set logo width', 'tuturn'),
					'desc' 		=> esc_html__('Leave it empty to hide', 'tuturn'),
					"default" 	=> 143,
					"min" 		=> 0,
					"step" 		=> 1,
					"max" 		=> 500,
					'display_value' => 'label',
				),

				array(
					'id'    	=> 'logo_white',
					'type'  	=> 'select',
					'multi'    => true,
					'title' 	=> esc_html__( 'Select white logo location', 'taskbot' ),
					'data'  	=> 'pages',
					'desc'      => esc_html__('Select the pages where you want to display white logo', 'taskbot'),
				),
			)
		)
	);
}