<?php

/**
 *
 * Class 'Tuturnmeetingdetail' defines order email
 *
 * @package     Tuturn
 * @subpackage  Tuturn/helpers/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (!class_exists('Tuturnmeetingdetail')) {

    class Tuturnmeetingdetail extends Tuturn_Email_Helper
    {

        public function __construct()
        {
            //do stuff here
        }

        /* new order seller/instructor email */
        public function new_meeting_detail($params = '')
        {
            global  $tuturn_settings;
            extract($params);
            $meeting_type        = !empty($meeting_type) ? $meeting_type : '';
            $meeting_url         = !empty($meeting_url) ? $meeting_url : '';
            $meeting_desc        = !empty($meeting_desc) ? $meeting_desc : '';
            $current_date        = !empty($current_date) ? $current_date : '';
            $email_to            = !empty($student_email) ? $student_email : '';
            $instructor_email    = !empty($instructor_email) ? $instructor_email : '';
            $student_name        = !empty($student_name) ? $student_name : '';
            $order_id            = !empty($order_id) ? $order_id : '';
            $subject_default     = esc_html__('Meeting detail', 'tuturn'); //default email subject
            $contact_default     = wp_kses(__('Instructor has been update meeting detail agianst order no: “{{order_id}}”', 'tuturn'), //default email content
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

            $subject            = !empty($tuturn_settings['booking_meeting_detail_subject']) ? $tuturn_settings['booking_meeting_detail_subject'] : $subject_default; //getting subject
            $email_content      = !empty($tuturn_settings['booking_meeting_detail_content']) ? $tuturn_settings['booking_meeting_detail_content'] : $contact_default; //getting conetnt

 
            $email_content = str_replace("{{meeting_type}}", $meeting_type, $email_content);
            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{meeting_url}}", $meeting_url, $email_content);
            $email_content = str_replace("{{meeting_description}}", $meeting_desc, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'student_name';
            $greeting['greet_value']        = $student_name;
            $greeting['greet_option_key']   = 'booking_meeting_detail_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);

            $body  = apply_filters('tuturn_new_order_instructor_email_content', $body);

            wp_mail($email_to, $subject, $body); //send Email

        }
      
          /* reminder to parent  */
        public function send_reminder_to_parents($params = '')
        {
            global  $tuturn_settings;
            extract($params);
            $meeting_type   = !empty($meeting_type) ? $meeting_type : '';
            $tutor_name     = !empty($tutor_name) ? $tutor_name : '';
            $parent_name    = !empty($parent_name) ? $parent_name : '';
            $parent_email   = !empty($parent_email) ? $parent_email : '';
            $email_to       = !empty($parent_email) ? $parent_email : '';

            $subject_default    = esc_html__('Hour log reminder', 'tuturn'); //default email subject
            $contact_default     = wp_kses(__('Hi, This is reminder email for volunteer log.<br/>', 'tuturn'), //default email content
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
  
            $subject            = !empty($tuturn_settings['volunteer_hours_update_subject']) ? $tuturn_settings['volunteer_hours_update_subject'] : $subject_default; //getting subject
            $email_content      = !empty($tuturn_settings['volunteer_hours_update_content']) ? $tuturn_settings['volunteer_hours_update_content'] : $contact_default; //getting conetnt

            $email_content = str_replace("{{parent_name}}", $parent_name, $email_content);
            $email_content = str_replace("{{tutor_name}}", $tutor_name, $email_content);
            $email_content = str_replace("{{instructor_name}}", $tutor_name, $email_content);
            $email_content = str_replace("{{parent_email}}", $parent_email, $email_content);
   

            /* data for greeting */
            $greeting['greet_keyword']      = 'parent_name';
            $greeting['greet_value']        = $parent_name;
            $greeting['greet_option_key']   = 'volunteer_hours_update_greeting';
  
            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_parent_reminder_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* email to instrutor on hour log approve */
        public function send_hour_log_approve($params = '')
        {
            global  $tuturn_settings;
            extract($params);
            $student_name = !empty($student_name) ? $student_name : '';
            $student_id   = !empty($student_id) ? $student_id : '';
            
            $instructor_id      = !empty($instructor_id) ? $instructor_id : '';
            $tutor_name         = !empty($tutor_name) ? $tutor_name : '';
            $instructor_email   = !empty($instructor_email) ? $instructor_email : '';

            $email_to            = !empty($instructor_email) ? $instructor_email : '';

            $subject_default    = esc_html__('Hour log approve', 'tuturn'); //default email subject
            $contact_default    = wp_kses(__('Hi {{tutor_name}},
            Your submitted hours to the {{student_name}} have been approved', 'tuturn'), //default email content
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
    
            $subject            = !empty($tuturn_settings['hours_log_approve_subject']) ? $tuturn_settings['hours_log_approve_subject'] : $subject_default; //getting subject
            $email_content      = !empty($tuturn_settings['hours_log_approve_content']) ? $tuturn_settings['hours_log_approve_content'] : $contact_default; //getting conetnt

            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{tutor_name}}", $tutor_name, $email_content);
            $email_content = str_replace("{{instructor_email}}", $instructor_email, $email_content);
     
            /* data for greeting */
            $greeting['greet_keyword']      = 'tutor_name';
            $greeting['greet_value']        = $tutor_name;
            $greeting['greet_option_key']   = 'hours_log_approve_greeting';
    
            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_parent_reminder_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

         /* email to instrutor on decline log request */
         public function decline_hours_log_request($params = '')
         {
            global  $tuturn_settings;
            extract($params);
            $student_name = !empty($student_name) ? $student_name : '';
            $student_id   = !empty($student_id) ? $student_id : '';
            
            $instructor_id      = !empty($instructor_id) ? $instructor_id : '';
            $tutor_name         = !empty($tutor_name) ? $tutor_name : '';
            $instructor_email   = !empty($instructor_email) ? $instructor_email : '';
            $decline_reason     = !empty($decline_reason) ? $decline_reason : '';

            $email_to            = !empty($instructor_email) ? $instructor_email : '';

            $subject_default    = esc_html__('Hours declined', 'tuturn'); //default email subject
            $contact_default    = wp_kses(__('Hi {{tutor_name}},
            Your submitted hours to the {{student_name}} have been approved', 'tuturn'), //default email content
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
    
            $subject            = !empty($tuturn_settings['decline_hours_request_subject']) ? $tuturn_settings['decline_hours_request_subject'] : $subject_default; //getting subject
            $email_content      = !empty($tuturn_settings['decline_hours_request_content']) ? $tuturn_settings['decline_hours_request_content'] : $contact_default; //getting conetnt

            $email_content = str_replace("{{student_name}}", $student_name, $email_content);
            $email_content = str_replace("{{instructor_name}}", $instructor_name, $email_content);
            $email_content = str_replace("{{tutor_name}}", $tutor_name, $email_content);
            $email_content = str_replace("{{instructor_email}}", $instructor_email, $email_content);
            $email_content = str_replace("{{decline_reason}}", $decline_reason, $email_content);
    
            /* data for greeting */
            $greeting['greet_keyword']      = 'tutor_name';
            $greeting['greet_value']        = $tutor_name;
            $greeting['greet_option_key']   = 'decline_hours_request_greeting';
    
            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_parent_reminder_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }
        
    }
}
