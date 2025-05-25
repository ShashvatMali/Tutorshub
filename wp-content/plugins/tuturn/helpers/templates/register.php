<?php

/**
 *
 * Class 'TuturnRegistrationEmail' defines packages email
 *
 * @package     Ttuturn
 * @subpackage  Ttuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */

if (!class_exists('TuturnRegistrationEmail')) {
    class TuturnRegistrationEmail extends Tuturn_Email_Helper
    {
        public function __construct()
        {
            //do stuff here
        }

        /* email to user on registration with auto approve */
        public function registration_user_auto_approve_email($params = ''){
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty($email) ? $email : '';
            $name           = !empty($name) ? $name : '';
            $sitename       = !empty($site) ? $site : '';
            $login_url      = !empty($login_url) ? $login_url : '';

            $subject_default  = esc_html__('Thank you for registration at {{sitename}}', 'tuturn'); //default email subject
            $contact_default  = wp_kses(__('Congratulation you have been registered at "{{sitename}}. Please buy a package for further process.', 'tuturn'), //default email content
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

            $subject        = !empty($tuturn_settings['user_registration_auto_approve_subject']) ? $tuturn_settings['user_registration_auto_approve_subject'] : $subject_default; //getting subject
            $subject	    = str_replace("{{sitename}}", $sitename, $subject); //getting subject
            $email_content  = !empty($tuturn_settings['user_registration_auto_approve_content']) ? $tuturn_settings['user_registration_auto_approve_content'] : $contact_default; //getting content

            $email_content = str_replace("{{name}}", $name, $email_content);
            $email_content = str_replace("{{email}}", $email, $email_content);
            $email_content = str_replace("{{sitename}}", $sitename, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'name';
            $greeting['greet_value']        = $name;
            $greeting['greet_option_key']   = 'email_user_registration_auto_approve_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_registration_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email

        }

        /* email to admin on user registration */
        public function new_user_register_admin_email($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty( $tuturn_settings['email_sender_email'] ) ? $tuturn_settings['email_sender_email'] : get_option('admin_email', 'info@example.com'); //admin email
            $name           = !empty($name) ? $name : '';
            $sitename       = !empty($site) ? $site : '';
            $login_url      = !empty($login_url) ? $login_url : '';
            $user_email     = !empty($email) ? $email : '';

            $subject_default  = esc_html__('New registration at {{sitename}}', 'tuturn'); //default email subject
            $contact_default  = wp_kses(__('A new user has been registered on the site with the name {{name}} and email address {{email}}', 'tuturn'), //default email content
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

            $subject        = !empty($tuturn_settings['admin_registration_subject']) ? $tuturn_settings['admin_registration_subject'] : $subject_default; //getting subject
            $subject	    = str_replace("{{sitename}}", $sitename, $subject); //getting subject
            $email_content  = !empty($tuturn_settings['admin_registration_content']) ? $tuturn_settings['admin_registration_content'] : $contact_default; //getting content

            $email_content = str_replace("{{name}}", $name, $email_content);
            $email_content = str_replace("{{sitename}}", $sitename, $email_content);
            $email_content = str_replace("{{email}}", $email, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = '';
            $greeting['greet_value']        = '';
            $greeting['greet_option_key']   = 'email_admin_registration_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_registration_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* email to admin on user registration */
        public function delete_account($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty( $tuturn_settings['email_sender_email'] ) ? $tuturn_settings['email_sender_email'] : get_option('admin_email', 'info@example.com'); //admin email
            $reason         = !empty($reason) ? $reason : '';
            $comments       = !empty($comments) ? $comments : '';
            $useremail      = !empty($useremail) ? $useremail : '';
            $username      = !empty($username) ? $username : '';
            $sitename       = !empty($site) ? $site : '';

            $subject_default  = esc_html__('User deleted', 'tuturn'); //default email subject
            $contact_default  = wp_kses(__('A user with the name {{username}} have deleted the account. Reason is given below
            {{reason}}
            {{comments}}', 'tuturn'), //default email content
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

            $subject        = !empty($tuturn_settings['delete_account_admin_subject']) ? $tuturn_settings['delete_account_admin_subject'] : $subject_default; //getting subject
            $subject	    = str_replace("{{sitename}}", $sitename, $subject); //getting subject
            $email_content  = !empty($tuturn_settings['delete_account_mail_content']) ? $tuturn_settings['delete_account_mail_content'] : $contact_default; //getting content

            $email_content = str_replace("{{reason}}", $reason, $email_content);
            $email_content = str_replace("{{comments}}", $comments, $email_content);
            $email_content = str_replace("{{useremail}}", $useremail, $email_content);
            $email_content = str_replace("{{username}}", $username, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = '';
            $greeting['greet_value']        = '';
            $greeting['greet_option_key']   = 'delete_account_email_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_delete_account', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* Email to user for account approved soon */
        public function registration_account_approval_request($params = '')
        {
            global $tuturn_settings;
            extract($params);
            $email_to 		    = !empty($email) ? $email : '';
            $user_name          = !empty($name) ? $name : '';
            $user_password      = !empty($password) ? $password : '';
            $user_email         = $email_to;
            $site_name          = !empty($site) ? $site : '';
            $subject_default    = esc_html__('Thank you for registration at {{sitename}}', 'tuturn'); //default email subject
            $contact_default    = wp_kses(__('Thank you for the registration at "{{sitename}}". Your account will be approved after the verification.', 'tuturn'), //default email content
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

            $subject	    = !empty( $tuturn_settings['user_account_approval_subject'] ) ? $tuturn_settings['user_account_approval_subject'] : $subject_default; //getting subject
            $subject	    = str_replace("{{sitename}}", $site_name, $subject); //getting subject
            $email_content  = !empty( $tuturn_settings['user_account_approval_content'] ) ? $tuturn_settings['user_account_approval_content'] : $contact_default; //getting content

            $email_content = str_replace("{{name}}", $user_name, $email_content);
            $email_content = str_replace("{{email}}", $user_email, $email_content);
            $email_content = str_replace("{{password}}", $user_password, $email_content);
            $email_content = str_replace("{{sitename}}", $site_name, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'user_account_approval_request_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_account_approval_email_content', $body);

            wp_mail($email_to, $subject, $body); //send Email
        }

        /* Email to user on account approved */
        public function user_account_approved($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to 		        = !empty($email) ? $email : '';
            $user_name              = !empty($name) ? $name : '';
            $user_email             = $email_to;
            $site_name              = !empty($site) ? $site : '';
            $subject_default        = esc_html__('Account approved', 'tuturn'); //default email subject
            $contact_default 	    = wp_kses(__('Congratulations! <br/> Your account has been approved by the admin.', 'tuturn'), //default email content
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

            $subject		  = !empty( $tuturn_settings['user_account_approved_subject'] ) ? $tuturn_settings['user_account_approved_subject'] : $subject_default; //getting subject
            $email_content    = !empty( $tuturn_settings['user_account_approved_content'] ) ? $tuturn_settings['user_account_approved_content'] : $contact_default; //getting content

            $email_content = str_replace("{{name}}", $user_name, $email_content);
            $email_content = str_replace("{{email}}", $user_email, $email_content);
            $email_content = str_replace("{{sitename}}", $site_name, $email_content);
            /* data for greeting */
            $greeting['greet_keyword']      = 'name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'user_account_approved_greeting';
            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_account_approved_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* Email to Admin on User Registration if verify by admin */
        public function registration_verify_by_admin_email($params = ''){
            global $tuturn_settings;
            extract($params);
            $email_to 		    = !empty( $tuturn_settings['email_sender_email'] ) ? $tuturn_settings['email_sender_email'] : get_option('admin_email', 'info@example.com'); //admin email
            $user_name          = !empty($name) ? $name : '';
            $user_email         = !empty($email) ? $email : '';
            $site_name          = !empty($site) ? $site : '';
            $login_url          = !empty($login_url) ? $login_url : '';
            $subject_default    = esc_html__('New registration approval request at {{sitename}}', 'tuturn'); //default email subject
            $contact_default    = wp_kses(__('A new user has been registered on the site with the name {{name}} and email address {{email}}. <br /> The registration is pending for approval, you can login  {{login_url}} to the admin to approve the account.', 'tuturn'), //default email content
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
    
            $subject		    = !empty( $tuturn_settings['admin_verify_register_user_subject'] ) ? $tuturn_settings['admin_verify_register_user_subject'] : $subject_default; //getting subject
            $subject		    = str_replace("{{sitename}}", $site_name, $subject); //getting subject
            $email_content      = !empty( $tuturn_settings['admin_verify_user_registration_content'] ) ? $tuturn_settings['admin_verify_user_registration_content'] : $contact_default; //getting content
            $login_link_        = $this->process_email_links($login_url, esc_html__('Login', 'tuturn')); //task/post link
    
            $email_content = str_replace("{{name}}", $user_name, $email_content);
            $email_content = str_replace("{{email}}", $user_email, $email_content);
            $email_content = str_replace("{{sitename}}", $site_name, $email_content);
            $email_content = str_replace("{{login_url}}", $login_link_, $email_content);
    
            /* data for greeting */
            $greeting['greet_keyword']      = '';
            $greeting['greet_value']        = '';
            $greeting['greet_option_key']   = 'email_admin_verify_user_registration_greeting';
    
            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_admin_verify_user_registration_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* Email to user for verification by link */
        public function registration_user_email($params = ''){
            global $tuturn_settings;
            extract($params);
    
            $email_to 			        = !empty($email) ? $email : '';
            $user_name                  = !empty($name) ? $name : '';
            $user_password              = !empty($password) ? $password : '';
            $user_email                 = $email_to;
            $user_verification_link     = !empty($verification_link) ? $verification_link : '';
            $site_name                  = !empty($site) ? $site : '';
            $subject_default 	        = esc_html__('Thank you for registration at {{sitename}}', 'tuturn'); //default email subject
            $contact_default 	        = wp_kses(__('Thank you for the registration at "{{sitename}}". Please click below to verify your account<br/> {{verification_link}}', 'tuturn'),
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
    
            $subject		        = !empty( $tuturn_settings['user_registration_subject'] ) ? $tuturn_settings['user_registration_subject'] : $subject_default; //getting subject
            $subject		        = str_replace("{{sitename}}", $site_name, $subject); //getting subject
            $email_content          = !empty( $tuturn_settings['user_registration_content'] ) ? $tuturn_settings['user_registration_content'] : $contact_default; //getting content
            $verification_link_     = $this->process_email_links($user_verification_link, esc_html__('Verification link','tuturn')); //verification link
    
            $email_content = str_replace("{{name}}", $user_name, $email_content);
            $email_content = str_replace("{{email}}", $user_email, $email_content);
            $email_content = str_replace("{{password}}", $user_password, $email_content);
            $email_content = str_replace("{{sitename}}", $site_name, $email_content);
            $email_content = str_replace("{{verification_link}}", $verification_link_, $email_content);    
            /* data for greeting */
            $greeting['greet_keyword']  = 'name';
            $greeting['greet_value']    = $user_name;
            $greeting['greet_option_key'] = 'email_user_registration_greeting';    
            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_user_registration_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email    
        }

        /* Email to User on Reset Password */
        public function user_reset_password($params = ''){
            global $tuturn_settings;
            extract($params);
            $email_to           = !empty($email) ? $email : '';
            $name 			    = !empty($name) ? $name : '';
            $sitename 		    = !empty($sitename) ? $sitename : '';
            $reset_link 	    = !empty($reset_link) ? $reset_link : '';
            $subject_default    = esc_html__('Reset password - {{sitename}}', 'tuturn'); //default email subject
            $content_default    = wp_kses(__('Someone requested to reset the password of following account: <br/> Email Address: {{email}} <br/>If this was a mistake, just ignore this email and nothing will happen.<br/>To reset your password, click reset link below:<br/>{{reset_link}}', 'tuturn'),
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
            $subject	      = !empty( $tuturn_settings['user_password_reset_subject'] ) ? $tuturn_settings['user_password_reset_subject'] : $subject_default; //getting subject
            $subject	      = str_replace("{{sitename}}", $sitename, $subject); //getting subject
            $email_content  = !empty( $tuturn_settings['user_reset_password_content'] ) ? $tuturn_settings['user_reset_password_content'] : $content_default; //getting content
            $reset_link_    = $this->process_email_links($reset_link, esc_html__('Reset link','tuturn')); //Reset link
    
            $email_content = str_replace("{{name}}", $name, $email_content);
            $email_content = str_replace("{{sitename}}", $sitename, $email_content);
            $email_content = str_replace("{{email}}", $email, $email_content);
            $email_content = str_replace("{{reset_link}}", $reset_link_, $email_content);
            $email_content = str_replace("{{email}} ", $email, $email_content);
    
            /* data for greeting */
            $greeting['greet_keyword']  = 'name';
            $greeting['greet_value']    = $name;
            $greeting['greet_option_key'] = 'user_reset_password_greeting';
    
            $body   = $this->tuturn_email_body($email_content, $greeting);
            $body   = apply_filters('tuturn_user_reset_password_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
    
        }

        /* Account approval request on Registration by social */
        public function social_registration_account_approval_request($params = '')
        {
            global $tuturn_settings;
            extract($params);
            $email_to 	    = !empty($email) ? $email : '';
            $user_name        = !empty($name) ? $name : '';
            $user_email       = $email_to;
            $site_name        = !empty($site) ? $site : '';
            $subject_default  = esc_html__('Registration at {{sitename}} via google account', 'tuturn'); //default email subject
            $contact_default  = wp_kses(__('Thank you for the registration at "{{sitename}}". Your account will be approved after the verification.', 'tuturn'), //default email content
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

            $subject		    = !empty( $tuturn_settings['social_user_account_approval_subject'] ) ? $tuturn_settings['social_user_account_approval_subject'] : $subject_default; //getting subject
            $subject		    = str_replace("{{sitename}}", $site_name, $subject); //getting subject
            $email_content  = !empty( $tuturn_settings['user_social_account_approval_content'] ) ? $tuturn_settings['user_social_account_approval_content'] : $contact_default; //getting content

            $email_content = str_replace("{{name}}", $user_name, $email_content);
            $email_content = str_replace("{{email}}", $user_email, $email_content);
            $email_content = str_replace("{{sitename}}", $site_name, $email_content);
            /* data for greeting */
            $greeting['greet_keyword']      = 'name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'user_social_account_approval_request_greeting';

            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_social_user_account_approval_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

      /* Email to user on Registration by social */
        public function social_registration_user_email($params = ''){
            global $tuturn_settings;
            extract($params);
    
            $email_to 		    = !empty($email) ? $email : '';
            $user_name          = !empty($name) ? $name : '';
            $login_url          = !empty($login_url) ? $login_url : '';
            $user_email         = $email_to;
            $site_name          = !empty($site) ? $site : '';
            $subject_default    = esc_html__('Registration at {{sitename}} via google account', 'tuturn'); //default email subject
            $contact_default    = wp_kses(__('Thank you for the registration at "{{sitename}}" Your account has been created. ', 'tuturn'),
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
    
            $subject		    = !empty( $tuturn_settings['subject_social_registration_user_email'] ) ? $tuturn_settings['subject_social_registration_user_email'] : $subject_default; //getting subject
            $subject		    = str_replace("{{sitename}}", $site_name, $subject); //getting subject
            $email_content  = !empty( $tuturn_settings['content_social_registration_user_email'] ) ? $tuturn_settings['content_social_registration_user_email'] : $contact_default; //getting content
    
            $email_content = str_replace("{{name}}", $user_name, $email_content);
            $email_content = str_replace("{{email}}", $user_email, $email_content);
            $email_content = str_replace("{{login_url}}", $login_url, $email_content);
            $email_content = str_replace("{{sitename}}", $site_name, $email_content);
            /* data for greeting */
            $greeting['greet_keyword'] = 'name';
            $greeting['greet_value'] = $user_name;
            $greeting['greet_option_key'] = 'greeting_social_registration_user_email';
    
            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_social_registration_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
    
        }

        /* Email to admin after parent confirmation*/
        public function parent_confirmation_identification_request($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to               = !empty( $tuturn_settings['email_sender_email'] ) ? $tuturn_settings['email_sender_email'] : get_option('admin_email', 'info@example.com'); //admin email
            $user_name              = !empty($user_name) ? $user_name : '';
            $user_email             = !empty($user_email) ? $user_email : '';
            $user_email             = $email_to;
            $gender                 = !empty($gender) ? $gender : '';
            $phone_number           = !empty($phone_number) ? $phone_number : '';
            $address                = !empty($address) ? $address : '';
            $school_name            = !empty($school_name) ? $school_name : '';
            $parent_phone           = !empty($parent_phone) ? $parent_phone : '';
            $other_introduction     = !empty($other_introduction) ? $other_introduction : '';
            $parent_email           = !empty($parent_email) ? $parent_email : '';
            $parent_name            = !empty($parent_name) ? $parent_name : '';
            $login_url              = !empty($login_url) ? $login_url : '';
            $approve_profile        = !empty($approve_profile) ? $approve_profile : '';

            $subject_default  = esc_html__('Parent confirm student submission', 'tuturn'); //default email subject
            $contact_default  = __('The parent has confirmed the user {{user_name}} verification. You can approve the user profile now.', 'tuturn');

            $subject		  = !empty( $tuturn_settings['parent_confirmation_request_admin_subject'] ) ? $tuturn_settings['parent_confirmation_request_admin_subject'] : $subject_default; //getting subject
            $email_content    = !empty( $tuturn_settings['parent_confirmation_subbmision_request_admin_content'] ) ? $tuturn_settings['parent_confirmation_subbmision_request_admin_content'] : $contact_default; //getting content

            $login_link         = $this->process_email_links($approve_profile, esc_html__('User Profile', 'tuturn'));

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{user_email}}", $user_email, $email_content);
            $email_content = str_replace("{{gender}}", $gender, $email_content);
            $email_content = str_replace("{{phone_number}}", $phone_number, $email_content);
            $email_content = str_replace("{{address}}", $address, $email_content);
            $email_content = str_replace("{{school_name}}", $school_name, $email_content);
            $email_content = str_replace("{{parent_phone}}", $parent_phone, $email_content);
            $email_content = str_replace("{{other_introduction}}", $other_introduction, $email_content);
            $email_content = str_replace("{{parent_email}}", $parent_email, $email_content);
            $email_content = str_replace("{{parent_name}}", $parent_name, $email_content);
            $email_content = str_replace("{{approve_profile}}", $login_link, $email_content);
            
            /* data for greeting */
            $greeting['greet_keyword']      = 'name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'parent_confirmation_admin_greeting';
            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_account_approved_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }


        /* Email to admin on account identification request submisiion*/
        public function user_identification_request($params = '')
        {
            global $tuturn_settings;
            extract($params);

            $email_to       = !empty( $tuturn_settings['email_sender_email'] ) ? $tuturn_settings['email_sender_email'] : get_option('admin_email', 'info@example.com'); //admin email

            $user_name        = !empty($user_name) ? $user_name : '';
            $user_email       = !empty($user_email) ? $user_email : '';
            $user_email       = $email_to;

            $gender                 = !empty($gender) ? $gender : '';
            $phone_number           = !empty($phone_number) ? $phone_number : '';
            $address                = !empty($address) ? $address : '';
            $school_name            = !empty($school_name) ? $school_name : '';
            $parent_phone           = !empty($parent_phone) ? $parent_phone : '';
            $other_introduction     = !empty($other_introduction) ? $other_introduction : '';
            $parent_email           = !empty($parent_email) ? $parent_email : '';
            $parent_name            = !empty($parent_name) ? $parent_name : '';
            $user_photo             = !empty($user_photo) ? $user_photo : '';
            $attachments            = !empty($attachments) ? $attachments : '';
            $confirmation_html      = !empty($confirmation_html) ? $confirmation_html : '';
            $confirmation_link      = !empty($confirmation_link) ? $confirmation_link : '';

            $subject_default  = esc_html__('Identity verfication request', 'tuturn'); //default email subject
            $contact_default  = __('A new submission for identity verification has been submitted from the {{student_name}}.<br/>Below information has been submitted <br/>{{submission_details}}', 'tuturn');

            $subject		  = !empty( $tuturn_settings['identity_submision_request_admin_subject'] ) ? $tuturn_settings['identity_submision_request_admin_subject'] : $subject_default; //getting subject
            $email_content    = !empty( $tuturn_settings['identity_subbmision_request_admin_content'] ) ? $tuturn_settings['identity_subbmision_request_admin_content'] : $contact_default; //getting content

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{user_email}}", $user_email, $email_content);
            $email_content = str_replace("{{submission_details}}", $submission_details, $email_content);

            $email_content = str_replace("{{gender}}", $gender, $email_content);
            $email_content = str_replace("{{phone_number}}", $phone_number, $email_content);
            $email_content = str_replace("{{address}}", $address, $email_content);
            $email_content = str_replace("{{school_name}}", $school_name, $email_content);
            $email_content = str_replace("{{parent_phone}}", $parent_phone, $email_content);
            $email_content = str_replace("{{other_introduction}}", $other_introduction, $email_content);
            $email_content = str_replace("{{parent_email}}", $parent_email, $email_content);
            $email_content = str_replace("{{parent_name}}", $parent_name, $email_content);
            $email_content = str_replace("{{user_photo}}", $user_photo, $email_content);
            $email_content = str_replace("{{attachments}}", $attachments, $email_content);
            $email_content = str_replace("{{confirmation_html}}", $confirmation_html, $email_content);
            $email_content = str_replace("{{confirmation_link}}", $confirmation_link, $email_content);
            
            /* data for greeting */
            $greeting['greet_keyword']      = 'name';
            $greeting['greet_value']        = $user_name;
            $greeting['greet_option_key']   = 'identity_subbmision_request_admin_greeting';
            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_account_approved_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

        /* Email to parent on account identification request submisiion*/
        public function user_identity_verification_parent_consent($params = '')
        {
            global $tuturn_settings;
            extract($params);
            
            $user_name       = !empty($user_name) ? $user_name : '';
            $user_email      = !empty($user_email) ? $user_email : '';
            $parent_name        = !empty($parent_name) ? $parent_name : '';
            $parent_email       = !empty($parent_email) ? $parent_email : '';
            $email_to           = $parent_email;
            
            $gender                 = !empty($gender) ? $gender : '';
            $phone_number           = !empty($phone_number) ? $phone_number : '';
            $address                = !empty($address) ? $address : '';
            $school_name            = !empty($school_name) ? $school_name : '';
            $parent_phone           = !empty($parent_phone) ? $parent_phone : '';
            $other_introduction     = !empty($other_introduction) ? $other_introduction : '';
            $parent_email           = !empty($parent_email) ? $parent_email : '';
            $parent_name            = !empty($parent_name) ? $parent_name : '';
            $user_photo             = !empty($user_photo) ? $user_photo : '';
            $attachments            = !empty($attachments) ? $attachments : '';
            $confirmation_html      = !empty($confirmation_html) ? $confirmation_html : '';
            $confirmation_link      = !empty($confirmation_link) ? $confirmation_link : '';

            $subject_default  = esc_html__('A parental consent email', 'tuturn'); //default email subject
            $contact_default  = esc_html__('We have received the parental consent submission from your child. You can verify the below details.<br/>{{submission_details}}', 'tuturn');

            $subject        = !empty( $tuturn_settings['identity_request_user_subject'] ) ? $tuturn_settings['identity_request_user_subject'] : $subject_default; //getting subject
            $email_content  = !empty( $tuturn_settings['user_identity_request_parent_content'] ) ? $tuturn_settings['user_identity_request_parent_content'] : $contact_default; //getting content

            $email_content = str_replace("{{user_name}}", $user_name, $email_content);
            $email_content = str_replace("{{parent_name}}", $parent_name, $email_content);
            $email_content = str_replace("{{user_email}}", $user_email, $email_content);
            $email_content = str_replace("{{submission_details}}", $submission_details, $email_content);

            $email_content = str_replace("{{gender}}", $gender, $email_content);
            $email_content = str_replace("{{phone_number}}", $phone_number, $email_content);
            $email_content = str_replace("{{address}}", $address, $email_content);
            $email_content = str_replace("{{school_name}}", $school_name, $email_content);
            $email_content = str_replace("{{parent_phone}}", $parent_phone, $email_content);
            $email_content = str_replace("{{other_introduction}}", $other_introduction, $email_content);
            $email_content = str_replace("{{parent_email}}", $parent_email, $email_content);
            $email_content = str_replace("{{parent_name}}", $parent_name, $email_content);
            $email_content = str_replace("{{user_photo}}", $user_photo, $email_content);
            $email_content = str_replace("{{attachments}}", $attachments, $email_content);
            $email_content = str_replace("{{confirmation_html}}", $confirmation_html, $email_content);
            $email_content = str_replace("{{confirmation_link}}", $confirmation_link, $email_content);

            /* data for greeting */
            $greeting['greet_keyword']      = 'parent_name';
            $greeting['greet_value']        = $parent_name;
            $greeting['greet_option_key']   = 'user_identity_request_parent_greeting';
            $body = $this->tuturn_email_body($email_content, $greeting);
            $body  = apply_filters('tuturn_user_account_approvel_request_email_content', $body);
            wp_mail($email_to, $subject, $body); //send Email
        }

    }
}
