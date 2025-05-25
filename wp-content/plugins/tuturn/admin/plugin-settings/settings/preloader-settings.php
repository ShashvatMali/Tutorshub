<?php
/**
 * General Settings
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
$theme_version 		= wp_get_theme();
if(!empty($theme_version->get( 'TextDomain' )) && ( $theme_version->get( 'TextDomain' ) === 'tuturn' || $theme_version->get( 'TextDomain' ) === 'tuturn-child' )){
	Redux::setSection( $opt_name, array(
		'title'            => esc_html__( 'Preloader Settings', 'tuturn' ),
		'id'               => 'preloader_settings',
		'subsection'       => false,
		'icon'			   => 'el el-globe',
		'fields'           => array(
				array(
					'id'       => 'site_loader',
					'type'     => 'switch',
					'title'    => esc_html__( 'Preloader on/off', 'tuturn' ),
					'default'  => false,
					'desc'     => esc_html__( '', 'tuturn' ),
				),	
				array(
					'id'       => 'loader_type',
					'type'     => 'select',
					'title'    => esc_html__('Select Type', 'tuturn'), 
					'desc'     => esc_html__('Please select loader type.', 'tuturn'),
					'options'  => array(
						'default' 	=> esc_html__('Default', 'tuturn'), 
						'custom' 	=> esc_html__('Custom', 'tuturn'), 
					),
					'default'  => 'default',
					'required' => array( 'site_loader', '=', true ),
				),
				array(
					'id'       => 'loader_image',
					'type'     => 'media',
					'url'      => true,
					'title'    => esc_html__( 'Loader image?', 'tuturn' ),
					'compiler' => 'true',
					'desc'     => esc_html__( 'Uplaod loader image', 'tuturn' ),
					'required' => array( 'loader_type', '=', 'custom' )
				),	
				
				array(
					'id'       => 'loader_duration',
					'type'     => 'select',
					'title'    => esc_html__('Loader duration?', 'tuturn'), 
					'desc'     => esc_html__('Select site loader speed', 'tuturn'),
					'options'  => array(
						'250' 	=> esc_html__('1/4th Seconds', 'tuturn'), 
						'500' 	=> esc_html__('Half Second', 'tuturn'), 
						'1000' 	=> esc_html__('1 Second', 'tuturn'), 
						'2000' 	=> esc_html__('2 Seconds', 'tuturn'), 
					),
					'default'  => '250',
					'required' => array( 'site_loader', '=', true ),
				),
			)
		)
	);
}