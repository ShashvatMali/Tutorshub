<?php

/**
 *
 * Class 'TuturnWithDrawStatuses' defines withdraw email
 *
 * @package     Tuturn
 * @subpackage  Tuturn/helpers/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */

if (!class_exists('TuturnWithDrawStatuses')) {
    class TuturnWithDrawStatuses extends Tuturn_Email_helper
    {
        public function __construct()
        {
            //do something
        }

        /* Email to admin withdraw request */
        public function withdraw_admin_email_request($params = '')
        {
            global $tuturn_settings;
            extract($params);
            $email_to       = !empty($tuturn_settings['email_sender_email']) ? $tuturn_settings['email_sender_email'] : get_option('admin_email', 'info@example.com');
            $user_name      = !empty($user_name) ? $user_name : '';
            $user_link      = !empty($user_link) ? $user_link : '';
            $amount         = !empty($amount) ? $amount : 0;
            $detail         = !empty($detail) ? $detail : '';
            $subject_default             = esc_html__('New withdrawal request has been received', 'tuturn'); //default email subject
            $contact_default             = wp_kses(__('You have received a new withdraw request from the {{user_name}} <br/> You can click <a href="{{detail}}">this link</a> to view the withdrawal details <br/>', 'tuturn'), //default email content
                array(
                    'a' => array(
                        'href' => array(),
                        'title' => array()
                    ),
                    'br' => array(),
                    'em' => array(),
                    'strong' => array(),
                )
            );
            $subject            = !empty($tuturn_settings['withdraw_request_admin_subject']) ? $tuturn_settings['withdraw_request_admin_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['withdraw_request_mail_content']) ? $tuturn_settings['withdraw_request_mail_content'] : $contact_default; //getting content

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{user_link}}", $user_link, $email_content);
            $email_content = str_replace("{{amount}}", $amount, $email_content);
            $email_content = str_replace("{{detail}}", $detail, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = '';
            $greeting['greet_value']        = '';
            $greeting['greet_option_key']   = 'withdraw_request_email_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_admin_withdraw_request_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        public function withdraw_approved_user_email($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to     = !empty($user_email) ? $user_email : '';
            $user_name    = !empty($user_name) ? $user_name : '';
            $user_link    = !empty($user_link) ? $user_link : '';
            $amount       = !empty($amount) ? $amount : 0;

            $subject_default             = esc_html__('Your withdrawal request has been approved', 'tuturn'); //default email subject
            $contact_default             = wp_kses(__('Your withdraw request has been approved. <br/> You can click <a href="{{user_link}}">this link</a> to view the withdrawal details.<br/>', 'tuturn'), //default email content
                array(
                    'a' => array(
                        'href' => array(),
                        'title' => array()
                    ),
                    'br' => array(),
                    'em' => array(),
                    'strong' => array(),
                )
            );
            
            $subject            = !empty($tuturn_settings['withdraw_approve_user_subject']) ? $tuturn_settings['withdraw_approve_user_subject'] : $subject_default; //getting subject
            $email_content      = !empty($tuturn_settings['withdraw_approved_mail_content']) ? $tuturn_settings['withdraw_approved_mail_content'] : $contact_default; //getting content

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{user_link}}", $user_link, $email_content);
            $email_content = str_replace("{{amount}}", $amount, $email_content);
            /* data for greeting */
            $greeting['greet_keyword']      = 'user_name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'withdraw_approve_user_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_withdraw_approved_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }
    }
}
