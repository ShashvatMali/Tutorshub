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
$mailchip_list	=  get_transient( 'latest-mailchimp-list' );
Redux::setSection( $opt_name,
    array(
        'title'       => esc_html__( 'Api settings', 'tuturn' ),
        'id'          => 'api-settings',
        'subsection'  => false,
        'desc'       	=> '',
        'icon'       	=> 'el el-key',
        'fields'      => array(
            array(
                'id'    =>'divider_1',
                'type'  => 'info',
                'title' => esc_html__('Google API Key', 'tuturn'),
                'style' => 'info',
            ),
            array(
                'id'       => 'google_map',
                'type'     => 'text',
                'title'    => esc_html__( 'Google Map Key', 'tuturn' ),
                'desc' 	   => wp_kses( __( 'Enter google map key here. It will be used for google maps. Get and Api key From <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"> Get API KEY </a>', 'tuturn' ), array(
                'a' => array(
                    'href' => array(),
                    'class' => array(),
                    'title' => array()
                    ),
                'br' => array(),
                'em' => array(),
                'strong' => array(),
                ) ),
                'default'  => '',
            ),
            array(
                'id'        => 'enable_social_connect',
                'type'      => 'switch',
                'title'     => esc_html__('Google connect?', 'tuturn'),
                'subtitle'  => esc_html__('When enable user will able to login and register using google account', 'tuturn'),
                'default'   => true,
            ),
            array(
                'id'    => 'google_client_id',
                'type'  => 'text',
                'title' => esc_html__( 'Client ID', 'tuturn' ),
                'required'  => array('enable_social_connect', '=', true),
            ),
            array(
                'id'    => 'google_client_secret',
                'type'  => 'text',
                'title' => esc_html__( 'Client secret', 'tuturn' ),
                'required'  => array('enable_social_connect', '=', true),
            ),
             /* Zipcode settings */
             array(
                'id'    =>'divider_2',
                'type'  => 'info',
                'title' => esc_html__('Zipcode validation', 'tuturn'),
                'style' => 'info',
            ),
            array(
                'id'        => 'geocoding_option',
                'type'      => 'select',
                'title'     => esc_html__('Geocoding verification for location', 'tuturn'),
                'desc'      => esc_html__('You can enable to disable geocoding in the location settings in profile settings', 'tuturn'),
                'options'   => array(
                    'yes'   => esc_html__('Enable', 'tuturn'),
                    'no'    => esc_html__('Disable', 'tuturn'),
                ),
                'default'   => 'yes',
            ), 
            /* MailChimp settings */
            array(
                'id'    =>'divider_3',
                'type'  => 'info',
                'title' => esc_html__('Mailchimp', 'tuturn'),
                'style' => 'info',
            ),
            array(
                'id'        => 'enable_mailchimp_connect',
                'type'      => 'switch',
                'title'     => esc_html__('Mailchimp', 'tuturn'),
                'subtitle'  => esc_html__('When enable you can use mailchimp', 'tuturn'),
                'default'   => false,
            ),
            array(
                'id'       => 'mailchimp_key',
                'type'     => 'text',
                'title'    => esc_html__( 'MailChimp Key', 'tuturn' ),
                'desc' 	=> wp_kses( __( 'Get Api key From <a href="https://us11.admin.mailchimp.com/account/api/" target="_blank"> Get API KEY </a> <br/> You can create list <a href="https://us11.admin.mailchimp.com/lists/" target="_blank">here</a><br>'.esc_html__('Latest MailChimp List ','tuturn').'<a href="#" class="tu-latest-mailchimp-list">'.esc_html__('Click here','tuturn').'</a>', 'tuturn' ), array(
							'a' => array(
								'href' => array(),
								'class' => array(),
								'title' => array()
							),
							'br' => array(),
							'em' => array(),
							'strong' => array(),
						) ),
                'default'  => '',
                'required'  => array('enable_mailchimp_connect', '=', true),
            ),
			array(
				'id'       => 'mailchimp_list',
				'type'     => 'select',
				'title'    => esc_html__('MailChimp List', 'tuturn'), 
				'desc'     => esc_html__('Select one of the list for newsletters', 'tuturn'),
				'options'  => $mailchip_list,
                'required'  => array('enable_mailchimp_connect', '=', true),
			),
        )
    )
);
