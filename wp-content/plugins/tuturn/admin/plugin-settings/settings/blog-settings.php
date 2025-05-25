<?php
/**
 * Blog  Settings
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
 
Redux::setSection( $opt_name, array(
	'title'      => esc_html__( 'Blog settings', 'tuturn' ),
	'id'    	 => 'blog_settings',
	'subsection' => false,
	'icon'		 => 'el el-align-center',
	'fields'  	 => array(
		array(
			'id'       => 'blog_listing_view',
			'type'     => 'select',
			'title'    => esc_html__( 'Blog listing view', 'tuturn' ),
			'desc'     => esc_html__( 'Select view for blog main page', 'tuturn' ),
			'options'   => array(
				'v1' => esc_html__('View 1', 'tuturn'),
				'v2' => esc_html__('View 2', 'tuturn'),
				'v3' => esc_html__('View 3', 'tuturn'),
			),
			'default'  => 'v1',
		),
		array(
			'id'      => 'blog_settings_divider_1',
			'type'    => 'info',
			'title'   => esc_html__( 'Blog detail settings', 'tuturn' ),
			'style'   => 'info',
		),
		array(
			'id'       => 'author_details',
			'type'     => 'switch',
			'title'    => esc_html__( 'Author box', 'tuturn' ),
			'default'  => false,
			'desc'     => esc_html__( 'Enable author box on the blog detail page', 'tuturn' ),
		),	
		array(
			'id'       => 'related_article',
			'type'     => 'switch',
			'title'    => esc_html__( 'Related article', 'tuturn' ),
			'default'  => false,
			'desc'     => esc_html__( 'Enable related article on the blog detail page', 'tuturn' ),
		),
		array(
			'id'       => 'side-layout',
			'type'     => 'image_select',
			'title'    => __('Select Layout', 'tuturn'), 
			'subtitle' => __('Select main content and sidebar alignment. Choose between 1, 2 or 3 column layout.', 'tuturn'),
			'options'  => array(
				'none'      => array(
					'alt'   => '1 Column', 
					'img'   => ReduxFramework::$_url.'assets/img/1col.png'
				),
				'left'      => array(
					'alt'   => '2 Column Left', 
					'img'   => ReduxFramework::$_url.'assets/img/2cl.png'
				),
				'right'      => array(
					'alt'   => '2 Column Right', 
					'img'  => ReduxFramework::$_url.'assets/img/2cr.png'
				),
			),
			'default' => 'left'
		)
	)
));
 