<?php

/**
 *
 * Class 'TuturnParentalEmails' defines parental email
 *
 * @package     Ttuturn
 * @subpackage  Ttuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */

if (!class_exists('TuturnParentalEmails')) {
    class TuturnParentalEmails extends Tuturn_Email_Helper
    {
        public function __construct()
        {
            //do stuff here
        }

        /* Email to student after submitting documnets */
        public function student_email_submit_documents($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty($user_email) ? $user_email : ''; //user email
            $user_name      = !empty($user_name) ? $user_name : '';
            $login_url      = !empty($get_logged_in) ? $get_logged_in : '';

            $subject_default    = esc_html__('Identity Documents Received', 'tuturn'); //default email subject
            $contact_default    = wp_kses(
                __('Thank you so much for submitting the documents.<br/>Your profile approval documents have been received. After the parent confirmation, we will approve your profile', 'tuturn'),
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

            $subject        = !empty($tuturn_settings['student_submit_doc_subject']) ? $tuturn_settings['student_submit_doc_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['student_submit_doc_content']) ? $tuturn_settings['student_submit_doc_content'] : $contact_default; //getting content

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{user_email}}", $email_to, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'user_name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'student_submit_doc_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_student_doc_submit_email', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }

        /**
         * Email to student after submitting documnets
         * if parent consent if off then
         */
        public function email_student_submit_documents($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty($user_email) ? $user_email : ''; //user email
            $user_name      = !empty($user_name) ? $user_name : '';

            $subject_default    = esc_html__('Identity Documents Received', 'tuturn'); //default email subject
            $contact_default    = wp_kses(
                __('Thank you so much for submitting the documents.<br/>Your profile approval documents have been received. After the review, we will approve your profile.', 'tuturn'),
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

            $subject        = !empty($tuturn_settings['submit_student_doc_subject']) ? $tuturn_settings['submit_student_doc_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['submit_student_doc_content']) ? $tuturn_settings['submit_student_doc_content'] : $contact_default; //getting content

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{user_email}}", $email_to, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'user_name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'submit_student_doc_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_submit_student_doc_email', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* Email to instructor after submitting documnets */
        public function instructor_email_submit_documents($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty($user_email) ? $user_email : ''; //user email
            $user_name      = !empty($user_name) ? $user_name : '';
            $login_url      = !empty($get_logged_in) ? $get_logged_in : '';

            $subject_default    = esc_html__('Identity Documents Received', 'tuturn'); //default email subject
            $contact_default    = wp_kses(
                __('Thank you so much for submitting the documents.<br/>Your profile approval documents have been received. After the review, we will approve your profile.', 'tuturn'),
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

            $subject        = !empty($tuturn_settings['instructor_submit_doc_subject']) ? $tuturn_settings['instructor_submit_doc_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['instructor_submit_doc_content']) ? $tuturn_settings['instructor_submit_doc_content'] : $contact_default; //getting content

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{user_email}}", $email_to, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'user_name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'instructor_submit_doc_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_instructor_submit_doc_email', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* Email to user after approved profile by admin */
        public function user_email_approved_identification($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty($user_email) ? $user_email : ''; //user email
            $user_name      = !empty($user_name) ? $user_name : '';
            $login_url      = !empty($get_logged_in) ? $get_logged_in : '';

            $subject_default    = esc_html__('Identification Approved', 'tuturn'); //default email subject
            $contact_default    = wp_kses(
                __('Congratulations!<br/>Your profile has been approved. You can log in and start editing your profile {{get_logged_in}}', 'tuturn'),
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

            $subject        = !empty($tuturn_settings['user_profile_approved_subject']) ? $tuturn_settings['user_profile_approved_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['user_profile_approved_content']) ? $tuturn_settings['user_profile_approved_content'] : $contact_default; //getting content

            $login_link        = $this->process_email_links($login_url, esc_html__('Login', 'tuturn'));

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{get_logged_in}}", $login_link, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'user_name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'user_profile_approved_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_identity_approved_email', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }

        /* Email to user after reject identification by admin */
        public function user_email_reject_identification($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty($user_email) ? $user_email : ''; //user email
            $user_name      = !empty($user_name) ? $user_name : '';
            $login_url      = !empty($get_logged_in) ? $get_logged_in : '';
            $reject_reason  = !empty($reject_reason) ? $reject_reason : '';

            $subject_default    = esc_html__('Identification Rejected', 'tuturn'); //default email subject
            $contact_default    = wp_kses(
                __('The admin has reject your identification and leave some comments {{reject_reason}}', 'tuturn'),
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

            $subject        = !empty($tuturn_settings['user_profile_rejected_subject']) ? $tuturn_settings['user_profile_rejected_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['user_profile_rejected_content']) ? $tuturn_settings['user_profile_rejected_content'] : $contact_default; //getting content

            $login_link        = $this->process_email_links($login_url, esc_html__('Login', 'tuturn'));

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{get_logged_in}}", $login_link, $email_content);
            $email_content = str_replace("{{reject_reason}}", $reject_reason, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'user_name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'user_profile_rejected_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_identity_rejected_email', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }
    }
}
