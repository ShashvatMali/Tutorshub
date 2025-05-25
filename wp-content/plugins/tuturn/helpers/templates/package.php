<?php
/**
 *
 * Class 'TuturnPackagesEmail' defines packages email
 *
 * @package     Ttuturn
 * @subpackage  Ttuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */

if (!class_exists('TuturnPackagesEmail')) {

    class TuturnPackagesEmail extends Tuturn_Email_Helper
    {

        public function __construct()
        {
            //do stuff here
        }

        /* email to instructor on Package Purchase */
        public function package_purchase_instructor_email($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to           = !empty($instructor_email) ? $instructor_email : '';
            $instructor_name    = !empty($instructor_name) ? $instructor_name : '';
            $order_id           = !empty($order_id) ? $order_id : '';
            $order_amount       = !empty($order_amount) ? $order_amount : '';
            $package_name       = !empty($package_name) ? $package_name : '';

            $subject_default  = esc_html__('Thank you for purchasing the package.', 'tuturn'); //default email subject
            $contact_default  = wp_kses(
                __('Thank you for purchasing the package “{{package_name}}” <br/> You can now post a courses and get orders.', 'tuturn'), //default email content
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

            $subject        = !empty($tuturn_settings['packages_purchase_instructor_subject']) ? $tuturn_settings['packages_purchase_instructor_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['package_purchase_instructor_content']) ? $tuturn_settings['package_purchase_instructor_content'] : $contact_default; //getting content

            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{order_amount}}", $order_amount, $email_content);
            $email_content = str_replace("{{package_name}}", $package_name, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'instructor_name';
            $greeting['greet_value']        = $instructor_name;
            $greeting['greet_option_key']   = 'packages_purchase_instructor_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_instructor_purchase_package_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }
    }
}