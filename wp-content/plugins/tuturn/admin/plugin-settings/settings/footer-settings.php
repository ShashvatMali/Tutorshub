<?php
/**
 * Footer Settings
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
$theme_version 		= wp_get_theme();
if(!empty($theme_version->get( 'TextDomain' )) && ( $theme_version->get( 'TextDomain' ) === 'tuturn' || $theme_version->get( 'TextDomain' ) === 'tuturn-child' || $theme_version->get( 'TextDomain' ) === 'tuteur' || $theme_version->get( 'TextDomain' ) === 'tuteur-child' )){
	Redux::setSection( $opt_name, array(
			'title'            => esc_html__( 'Footer Settings', 'tuturn' ),
			'id'               => 'footer_settings',
			'subsection'       => false,
			'icon'			   => 'el el-align-center',
			'fields'           => array(
				array(
					'id'       => 'copyright',
					'type'     => 'textarea',
					'title'    => esc_html__( 'Copyright text', 'tuturn' ),
					'desc'     => esc_html__( '', 'tuturn' ),
					'default'  => esc_html__( '© 1994 - 2022 All Rights Reserved.', 'tuturn' ),
				)	
			)
		)
	);
}