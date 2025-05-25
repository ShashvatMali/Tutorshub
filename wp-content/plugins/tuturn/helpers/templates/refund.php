<?php

/**
 *
 * Class 'TuturnRefundsStatuses' defines packages email
 *
 * @package     Tuturn
 * @subpackage  Tuturn/helpers/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (!class_exists('TuturnRefundsStatuses')) {

    class TuturnRefundsStatuses extends Tuturn_Email_helper
    {

        public function __construct()
        {
            //do stuff here
        }

        /* Refund Approved */
        public function refund_approved_student_email($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty($student_email) ? $student_email : '';
            $instructor_name  = !empty($instructor_name) ? $instructor_name : '';
            $student_name    = !empty($student_name) ? $student_name : '';
            $order_id       = !empty($order_id) ? $order_id : '';
            $order_amount   = !empty($order_amount) ? $order_amount : 0;
            $login_url      = !empty($login_url) ? $login_url : '';

            $subject_default    = esc_html__('Payment refunded', 'tuturn'); //default email subject
            $contact_default    = wp_kses(
                __('Congratulations! <br/> Your payment has been refunded by the {{instructor_name}} against the order #{{order_id}}', 'tuturn'),
                array(
                    'a'       => array(
                        'href'  => array(),
                        'title' => array()
                    ),
                    'br'      => array(),
                    'em'      => array(),
                    'strong'  => array(),
                )
            );

            $subject        = !empty($tuturn_settings['student_approved_refund_subject']) ? $tuturn_settings['student_approved_refund_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['approved_student_refund_content']) ? $tuturn_settings['approved_student_refund_content'] : $contact_default; //getting content

            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{login_url}}", $login_url, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']  = 'student_name';
            $greeting['greet_value']    = $student_name;
            $greeting['greet_option_key'] = 'student_approved_refund_email_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_student_refund_approved_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }
    }
}
