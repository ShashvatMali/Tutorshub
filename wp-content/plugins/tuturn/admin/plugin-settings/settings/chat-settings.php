<?php
if (!defined('ABSPATH')) exit;
/**
 * Chat Settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Plugin_Settings/Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
*/
Redux::set_section($opt_name, 
    array(
        'title' => esc_html__('Chat Settings', 'tuturn'),
        'id' => 'setting_chat_mesages',
        'desc' => '',
        'icon' => 'el el-comment-alt',
        'subsection' => false,
        'fields' => array(
            array(
                'id'       => 'hire_instructor_chat_switch',
                'type'     => 'switch',
                'title'    => esc_html__( 'Send Message', 'tuturn' ),
                'subtitle' => esc_html__( 'Set default message for instructor on hiring.', 'tuturn' ),
                'default'  => true,
            ),
            array(
                'id'      => 'divider_chat_message_to_seller',
                'desc'    => wp_kses( __( '{{booking_name}} — To display the booking name.<br>
                                {{order_link}} — To display the order link.<br>'
                , 'tuturn' ),
                array(
                    'a'     => array(
                    'href'  => array(),
                    'title' => array()
                    ),
                    'br'      => array(),
                    'em'      => array(),
                    'strong'  => array(),
                ) ),
                'title'     => esc_html__( 'Message setting variables', 'tuturn' ),
                'type'      => 'info',
                'class'     => 'dc-center-content',
                'icon'      => 'el el-info-circle',
                'required' 	=> array('hire_instructor_chat_switch','equals','1')
            ),
            array(
                'id'        => 'hire_instructor_chat_mesage',
                'type'      => 'textarea',
                'title'     => esc_html__('Chat Message', 'tuturn'),
                'subtitle'  => esc_html__('Default chat message for  on hiring', 'tuturn'),
                'required' 	=> array('hire_instructor_chat_switch','equals','1'),
                'default'   => wp_kses(__('Congratulations! You have hired for the booking "{{booking_name}}".<br/> with order link: {{order_link}}.', 'tuturn'),
                array(
                    'a' => array(
                    'href' => array(),
                    'title' => array()
                    ),
                    'br'      => array(),
                    'em'      => array(),
                    'strong'  => array(),
                )
                ),
            ),
        )
    )
);
