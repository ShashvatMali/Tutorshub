<?php

/**
 *
 * Class 'TuturnOrderStatuses' defines order email
 *
 * @package     Tuturn
 * @subpackage  Tuturn/helpers/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (!class_exists('TuturnOrderStatuses')) {

    class TuturnOrderStatuses extends Tuturn_Email_Helper
    {

        public function __construct()
        {
            //do stuff here
        }

        /* new order seller/instructor email */
        public function new_booking_instructor_email($params = '')
        {
            global  $tuturn_settings;
            extract($params);
            $instructor_name            = !empty($instructor_name) ? $instructor_name : '';
            $student_name               = !empty($student_name) ? $student_name : '';
            $order_id                   = !empty($order_id) ? $order_id : '';
            $order_amount               = !empty($order_amount) ? $order_amount : '';
            $email_to                   = !empty($instructor_email) ? $instructor_email : '';
            $login_url                  = !empty($login_url) ? $login_url : '';
            $subject_default            = esc_html__('New Booking', 'tuturn'); //default email subject
            $contact_default            = wp_kses(__('You have received a new booking for the orderId “{{order_id}}”', 'tuturn'), //default email content
                array(
                    'a' => array(
                        'href'    => array(),
                        'title'   => array()
                    ),
                    'br'        => array(),
                    'em'        => array(),
                    'strong'    => array(),
                )
            );

            $subject            = !empty($tuturn_settings['new_booking_instructor_email_subject']) ? $tuturn_settings['new_booking_instructor_email_subject'] : $subject_default; //getting subject
            $email_content      = !empty($tuturn_settings['new_booking_instructor_mail_content']) ? $tuturn_settings['new_booking_instructor_mail_content'] : $contact_default; //getting conetnt

            $login_link_        = $this->process_email_links($login_url, esc_html__('Login', 'tuturn'));

            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{login_url}}", $login_link_, $email_content);
            $email_content = str_replace("{{order_amount}}", $order_amount, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'instructor_name';
            $greeting['greet_value']        = $instructor_name;
            $greeting['greet_option_key']   = 'new_booking_instructor_email_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);

            $body  = apply_filters('tuturn_new_order_instructor_email_content', $body);

            wp_mail($email_to, $subject, $body); //send Email

        }

        /* new order buyer/student email */
        public function new_booking_student_email($params = '')
        {
            global  $tuturn_settings;
            extract($params);

            $instructor_name        = !empty($instructor_name) ? $instructor_name : '';
            $student_name           = !empty($student_name) ? $student_name : '';
            $order_id               = !empty($order_id) ? $order_id : '';
            $order_amount           = !empty($order_amount) ? $order_amount : '';
            $instructor_email       = !empty($instructor_email) ? $instructor_email : '';
            $student_email          = !empty($student_email) ? $student_email : '';
            $email_to               = !empty($student_email) ? $student_email : get_option('admin_email', 'info@example.com'); //admin email
            $subject_default        = esc_html__('New booking', 'tuturn'); //default email subject
            $contact_default        = wp_kses(__('Thank you so much for ordering my booking.<br/> With order is #"{{order_id}}" I will get in touch with you shortly.<br/>', 'tuturn'), //default email content
                array(
                    'a' => array(
                        'href'    => array(),
                        'title'   => array()
                    ),
                    'br'        => array(),
                    'em'        => array(),
                    'strong'    => array(),
                )
            );

            $subject            = !empty($tuturn_settings['new_booking_student_email_subject']) ? $tuturn_settings['new_booking_student_email_subject'] : $subject_default; //getting subject
            $content            = !empty($tuturn_settings['new_booking_student_mail_content']) ? $tuturn_settings['new_booking_student_mail_content'] : $contact_default; //getting content
            $email_content  = $content; //getting content
            

            $email_content  = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content  = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content  = str_replace("{{order_amount}}", $order_amount, $email_content);
            $email_content  = str_replace("{{student_name}}", $student_name, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']    = 'student_name';
            $greeting['greet_value']      = $student_name;
            $greeting['greet_option_key'] = 'new_booking_student_email_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_new_order_student_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }

        /* Booking decline email to student */
        public function booking_decline_student_email($params = '')
        {
            global  $tuturn_settings;
            extract($params);

            $email_to           = !empty($student_email) ? $student_email : '';
            $instructor_name    = !empty($instructor_name) ? $instructor_name : '';
            $student_name       = !empty($student_name) ? $student_name : '';
            $order_id           = !empty($order_id) ? $order_id : '';
            $decline_reason     = !empty($decline_reason) ? $decline_reason : '';
            $decline_desc       = !empty($decline_desc) ? $decline_desc : '';
            $login_url          = !empty($login_url) ? $login_url : '';
            $subject_default    = esc_html__('Booking declined', 'tuturn'); //default email subject
            $contact_default    = wp_kses(
                __('The instructor “{{instructor_name}}” has declined the booking by the <br/> reason: "{{decline_reason}}" and has left some comments against the order #{{order_id}} <br/> and reason description is <br/> "{{decline_desc}}" <br/>', 'tuturn'),
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

            $subject          = !empty($tuturn_settings['booking_request_declined_subject']) ? $tuturn_settings['booking_request_declined_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['order_complete_request_declined_content']) ? $tuturn_settings['order_complete_request_declined_content'] : $contact_default; //getting content

            $login_url_     = $this->process_email_links($login_url, esc_html__('Login', 'tuturn'));

            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{login_url}}", $login_url_, $email_content);
            $email_content = str_replace("{{decline_reason}}", $decline_reason, $email_content);
            $email_content = str_replace("{{decline_desc}}", $decline_desc, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']    = 'student_name';
            $greeting['greet_value']      = $student_name;
            $greeting['greet_option_key'] = 'booking_declined_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_booking_declined_student_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }

        /* Booking decline email to instructor */
        public function booking_decline_instructor_email($params = '')
        {
            global  $tuturn_settings;
            extract($params);

            $email_to           = !empty($instructor_email) ? $instructor_email : '';
            $instructor_name    = !empty($instructor_name) ? $instructor_name : '';
            $student_name       = !empty($student_name) ? $student_name : '';
            $order_id           = !empty($order_id) ? $order_id : '';
            $decline_reason     = !empty($decline_reason) ? $decline_reason : '';
            $decline_desc       = !empty($decline_desc) ? $decline_desc : '';
            $login_url          = !empty($login_url) ? $login_url : '';
            $subject_default    = esc_html__('Booking declined', 'tuturn'); //default email subject
            $contact_default    = wp_kses(
                __('You has declined the booking by the <br/> reason: "{{decline_reason}}" and has left some comments against the order #{{order_id}} <br/> and reason description is <br/> "{{decline_desc}}" <br/>', 'tuturn'),
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

            $subject          = !empty($tuturn_settings['booking_request_declined_instructor_subject']) ? $tuturn_settings['booking_request_declined_instructor_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['order_complete_request_declined_content']) ? $tuturn_settings['order_complete_request_declined_content'] : $contact_default; //getting content

            $login_url_     = $this->process_email_links($login_url, esc_html__('Login', 'tuturn'));

            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{login_url}}", $login_url_, $email_content);
            $email_content = str_replace("{{decline_reason}}", $decline_reason, $email_content);
            $email_content = str_replace("{{decline_desc}}", $decline_desc, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']    = 'instructor_name';
            $greeting['greet_value']      = $instructor_name;
            $greeting['greet_option_key'] = 'booking_declined_instructor_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_booking_declined_instructor_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }

        /* Seeker Email on booking approved */
        public function booking_approve_student_email($params = '')
        {
            global  $tuturn_settings;
            extract($params);

            $email_to            = !empty($student_email) ? $student_email : '';
            $instructor_name     = !empty($instructor_name) ? $instructor_name : '';
            $student_name        = !empty($student_name) ? $student_name : '';
            $instructor_email    = !empty($instructor_email) ? $instructor_email : '';
            $order_id            = !empty($order_id) ? $order_id : '';
            $login_url           = !empty($login_url) ? $login_url : '';

            $subject_default      = esc_html__('Booking approved', 'tuturn'); //default email subject
            $contact_default      = wp_kses(__('Congratulations! <br/> The instructor “{{instructor_name}}” has approved the booking with the order #{{order_id}} <br/> ', 'tuturn'),
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

            $subject        = !empty($tuturn_settings['booking_request_approved_subject']) ? $tuturn_settings['booking_request_approved_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['booking_request_approved_content']) ? $tuturn_settings['booking_request_approved_content'] : $contact_default; //getting content

            $login_link_    = $this->process_email_links($login_url, esc_html__('Login', 'tuturn'));

            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{login_url}}", $login_link_, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']    = 'student_name';
            $greeting['greet_value']      = $student_name;
            $greeting['greet_option_key'] = 'booking_approved_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_booking_approve_student_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }

        /* Provider Email on booking completed */
        public function booking_complete_instructor_email($params = '')
        {
            global  $tuturn_settings;
            extract($params);

            $email_to             = !empty($instructor_email) ? $instructor_email : '';
            $instructor_name      = !empty($instructor_name) ? $instructor_name : '';
            $student_name         = !empty($student_name) ? $student_name : '';
            $student_email        = !empty($student_email) ? $student_email : '';
            $order_id             = !empty($order_id) ? $order_id : '';
            $login_url            = !empty($login_url) ? $login_url : '';
            $student_rating       = !empty($student_rating) ? $student_rating : '';

            $subject_default      = esc_html__('Booking completed', 'tuturn'); //default email subject
            $contact_default      = wp_kses(
                __('Congratulations! <br/> The student “{{student_name}}” has approved the booking with the order #{{order_id}} and leave <br/> rating: {{student_rating}} <br/> ', 'tuturn'),
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

            $subject          = !empty($tuturn_settings['order_completed_instructor_subject']) ? $tuturn_settings['order_completed_instructor_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['order_completed_instructor_content']) ? $tuturn_settings['order_completed_instructor_content'] : $contact_default; //getting content

            $login_link_    = $this->process_email_links($login_url, esc_html__('Login', 'tuturn'));
            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{login_url}}", $login_link_, $email_content);
            $email_content = str_replace("{{student_rating}}", $student_rating, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']    = 'instructor_name';
            $greeting['greet_value']      = $instructor_name;
            $greeting['greet_option_key'] = 'order_completed_instructor_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_order_completed_instructor_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* email on cancel booking/order */
        public function booking_cancel_instructor_email($params = '')
        {
            global  $tuturn_settings;
            extract($params);
            $email_to           = !empty($instructor_email) ? $instructor_email : '';
            $student_name       = !empty($student_name) ? $student_name : '';
            $instructor_name    = !empty($instructor_name) ? $instructor_name : '';
            $instructor_email   = !empty($instructor_email) ? $instructor_email : '';
            $cancel_reason      = !empty($cancel_reason) ? $cancel_reason : '';
            $cancel_desc        = !empty($cancel_desc) ? $cancel_desc : '';
            $order_id           = !empty($order_id) ? $order_id : '';
            $login_url          = !empty($login_url) ? $login_url : '';

            $subject_default      = esc_html__('Booking Canceled', 'tuturn'); //default email subject
            $contact_default      = wp_kses(
                __('Booking has been canceled by the reason "{{cancel_reason}}" and <br/> reason detail is : "{{cancel_desc}}". <br/> order id is {{order_id}}', 'tuturn'),
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

            $subject        = !empty($tuturn_settings['booking_cancel_instructor_subject']) ? $tuturn_settings['booking_cancel_instructor_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['booking_cancel_instructor_content']) ? $tuturn_settings['booking_cancel_instructor_content'] : $contact_default; //getting content

            $login_link_    = $this->process_email_links($login_url, esc_html__('Login', 'tuturn'));

            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{instructor_email}}", $instructor_email, $email_content);
            $email_content = str_replace("{{cancel_reason}}", $cancel_reason, $email_content);
            $email_content = str_replace("{{cancel_desc}}", $cancel_desc, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{login_url}}", $login_link_, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']    = 'instructor_name';
            $greeting['greet_value']      = $instructor_name;
            $greeting['greet_option_key'] = 'booking_cancel_instructor_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_order_completed_instructor_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* email on cancel booking/order */
        public function booking_cancel_student_email($params = '')
        {
            global  $tuturn_settings;
            extract($params);
            $email_to           = !empty($student_email) ? $student_email : '';
            $student_name       = !empty($student_name) ? $student_name : '';
            $instructor_name    = !empty($instructor_name) ? $instructor_name : '';
            $instructor_email   = !empty($instructor_email) ? $instructor_email : '';
            $cancel_reason      = !empty($cancel_reason) ? $cancel_reason : '';
            $cancel_desc        = !empty($cancel_desc) ? $cancel_desc : '';
            $order_id           = !empty($order_id) ? $order_id : '';
            $login_url          = !empty($login_url) ? $login_url : '';

            $subject_default      = esc_html__('Booking Canceled', 'tuturn'); //default email subject
            $contact_default      = wp_kses(__('Booking has been canceled by the reason "{{cancel_reason}}" and <br/> reason detail is : "{{cancel_desc}}". <br/> order id is {{order_id}}', 'tuturn'),
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

            $subject          = !empty($tuturn_settings['booking_cancel_student_subject']) ? $tuturn_settings['booking_cancel_student_subject'] : $subject_default; //getting subject
            $email_content  = !empty($tuturn_settings['booking_cancel_student_content']) ? $tuturn_settings['booking_cancel_student_content'] : $contact_default; //getting content

            $login_link_    = $this->process_email_links($login_url, esc_html__('Login', 'tuturn'));

            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{student_email}}", $student_email, $email_content);
            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{cancel_reason}}", $cancel_reason, $email_content);
            $email_content = str_replace("{{cancel_desc}}", $cancel_desc, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{login_url}}", $login_link_, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']    = 'student_name';
            $greeting['greet_value']      = $student_name;
            $greeting['greet_option_key'] = 'booking_cancel_student_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_order_completed_instructor_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* email on hour log update */
        public function update_hour_log($params = '')
        {
            global  $tuturn_settings;
            extract($params);
            $email_to           = !empty($student_email) ? $student_email : '';
            $student_name       = !empty($student_name) ? $student_name : '';
            $instructor_name    = !empty($instructor_name) ? $instructor_name : '';
            $login_url          = !empty($login_url) ? $login_url : '';

            $subject_default      = esc_html__('Hour log updated.', 'tuturn'); //default email subject
            $contact_default      = wp_kses(__('A tutor has submitted the hours for approval. You can accept or decline with reason', 'tuturn'),
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

            $subject       = !empty($tuturn_settings['update_hour_log_subject']) ? $tuturn_settings['update_hour_log_subject'] : $subject_default; //getting subject
            $email_content = !empty($tuturn_settings['update_hour_log_content']) ? $tuturn_settings['update_hour_log_content'] : $contact_default; //getting content

            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{login_url}}", $login_url, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']    = 'student_name';
            $greeting['greet_value']      = $student_name;
            $greeting['greet_option_key'] = 'update_hour_logt_greeting';

            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_order_completed_instructor_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }
    }
}
