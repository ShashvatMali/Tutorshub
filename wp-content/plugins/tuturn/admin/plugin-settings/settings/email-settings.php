<?php

/**
 * Email Settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Plugin_Settings/Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */

/* email general setting tab */
Redux::set_section($opt_name, array(
	'title'       => esc_html__('Email Settings', 'tuturn'),
	'id'          => 'email_settings',
	'desc'        => '',
	'icon'        => 'el el-inbox',
	'subsection'  => false,
	'fields'      => array(
		array(
			'id'      => 'divider_1',
			'type'    => 'info',
			'title'   => esc_html__('Email General Settings', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      => 'email_logo',
			'type'    => 'media',
			'compiler' => 'true',
			'url'     => true,
			'title'   => esc_html__('Email Logo', 'tuturn'),
			'desc'    => esc_html__('Upload your email logo here.', 'tuturn'),
		),
		array(
			'id'      => 'email_sender_name',
			'type'    => 'text',
			'title'   => esc_html__('Email sender name', 'tuturn'),
			'desc'    => esc_html__('Add email sender name here like: Shawn Biyeam. Default your site name will be used.', 'tuturn'),
			'default' => esc_html__('Amentotech.Pvt.Ltd', 'tuturn'),
		),
		array(
			'id'      => 'email_sender_email',
			'type'    => 'text',
			'title'   => esc_html__('Email sender email', 'tuturn'),
			'desc'    => esc_html__('Add email sender email here like: johndoe@example.com. Default your site email will be used.', 'tuturn'),
			'default' => esc_html__('johndoe@example.com', 'tuturn'),
		),
		array(
			'id'      => 'email_copyrights',
			'type'    => 'textarea',
			'title'   => esc_html__('Footer copyright text', 'tuturn'),
			'desc'    => esc_html__('Add copyright text for the emails in footer', 'tuturn'),
		),
		array(
			'id'      => 'email_signature',
			'type'    => 'textarea',
			'title'   => esc_html__('Email Sender Signature ', 'tuturn'),
			'desc'    => esc_html__('Add email sender Signature here like: Team tuturn.', 'tuturn'),
			'default' => esc_html__('Regards,', 'tuturn'),
		),
		array(
			'id'      => 'email_footer_color',
			'type'    => 'color',
			'title'   => esc_html__('Email footer color ', 'tuturn'),
			'desc'    => esc_html__('Add email footer background color here', 'tuturn'),
			'default' => '#353648',
		),
		array(
			'id'      => 'email_footer_color_text',
			'type'    => 'color',
			'title'   => esc_html__('Email footer color ', 'tuturn'),
			'desc'    => esc_html__('Add email footer text color here', 'tuturn'),
			'default' => '#FFFFFF',
		),
		array(
			'id' 		=> 'email_container_wide',
			'type' 		=> 'slider',
			'title' 	=> esc_html__('Set email content width', 'tuturn'),
			'desc' 		=> esc_html__('Set email content width', 'tuturn'),
			"default" 	=> 600,
			"min" 		=> 200,
			"step" 		=> 5,
			"max" 		=> 1200,
		),
	)
));

/* email administrator setting tab */
Redux::set_section($opt_name, array(
	'title'			=> esc_html__('Administrator', 'tuturn'),
	'id'			=> 'administrator_email_templates',
	'desc'			=> 'Administrator email templates',
	'icon'			=> '',
	'subsection'	=> true,
	'fields'		=> array(
		/* Admin Email to verify new user */
		array(
			'id'      => 'divider_verify_user_admin_registration_templates',
			'type'    => 'info',
			'title'   => esc_html__('Admin email verify user', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      => 'admin_verify_register_user_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('New user account approval request at {{sitename}}', 'tuturn'),
		),
		array(
			'id'      => 'divider_adminemail_verify_user_confirmation_information',
			'desc'    =>	wp_kses(
				__(
					'{{name}} — To display the user name.<br>
								{{email}} — To display the user email.<br>
								{{sitename}} — To display the sitename.<br>
								{{login_url}} — To display the login url.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle'
		),
		array(
			'id'      	=> 'email_admin_verify_user_registration_greeting',
			'type'    	=> 'text',
			'title'   	=>  esc_html__('Greeting', 'tuturn'),
			'desc'    	=>  esc_html__('Please add text.', 'tuturn'),
			'default' 	=>  esc_html__('Hello,', 'tuturn'),
		),
		array(
			'id'        => 'admin_verify_user_registration_content',
			'type'      => 'textarea',
			'default'   =>  wp_kses(
				__('A new user “{{name}}” signed up with an email address “{{email}}”. <br /> Please login here to approve user account {{login_url}}.', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     =>  esc_html__('Email Contents', 'tuturn'),
		),
		/* Admin Email on Register */
		array(
			'id'      => 'divider_email_admin_registration_templates',
			'type'    => 'info',
			'title'   => esc_html__('Admin email on new user registration', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      => 'admin_registration_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('New user registration at {{sitename}}', 'tuturn'),
		),
		array(
			'id'      => 'divider_adminemail_confirmation_information',
			'desc'    => wp_kses(
				__(
					'{{name}} — To display the user name.<br>
							{{email}} — To display the user email.<br>
							{{sitename}} — To display the sitename.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle'
		),
		array(
			'id'      	=> 'email_admin_registration_greeting',
			'type'    	=> 'text',
			'title'   	=>  esc_html__('Greeting', 'tuturn'),
			'desc'    	=>  esc_html__('Please add text.', 'tuturn'),
			'default' 	=>  esc_html__('Hello,', 'tuturn'),
		),
		array(
			'id'        => 'admin_registration_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('A new user “{{name}}” signed up with an email address “{{email}}”.', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     =>  esc_html__('Email Contents', 'tuturn')
		),
		/* Emil to admin on user identity request subbmission */
		array(
			'id'      => 'divider_admin_identity_request_templates',
			'type'    => 'info',
			'title'   => esc_html__('Identity verification submission', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_identity_submision_request_admin',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to admin on identity submission.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      => 'identity_submision_request_admin_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Identity approvel request submission.', 'tuturn'),
			'required'  => array('email_identity_submision_request_admin', 'equals', '1')
		),
		array(
			'id'      => 'divider_email_identity_submision_request_admin',
			'desc'    => wp_kses(
				__(
					'{{user_name}} — To display the user Name.<br>
					{{user_email}} — To display the user email.<br>
					{{submission_details}} — To display submission details<br>
					{{confirmation_link}} — To display confirmation URL<br>
					{{confirmation_html}} — To display confirmation button html<br>
					{{parent_name}} 	— To display the user parent name.<br>
					{{gender}} 	— To display the user gender.<br>
					{{phone_number}} 	— To display the user phone number.<br>
					{{address}} 	— To display the user address.<br>
					{{school_name}} 	— To display the user school name.<br>
					{{parent_phone}} 	— To display the user parent phone.<br>
					{{other_introduction}} 	— To display the user other introduction.<br>
					{{parent_email}} 	— To display the user parent email.<br>
					{{user_photo}} 	— To display the user other photo.<br>
					{{attachments}} 	— To display the user attachments.<br>
					','tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_identity_submision_request_admin', 'equals', '1')
		),
		array(
			'id'      	=> 'identity_subbmision_request_admin_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello admin,', 'tuturn'),
			'required'  => array('email_identity_submision_request_admin', 'equals', '1')
		),
		array(
			'id'        => 'identity_subbmision_request_admin_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('A new submission for identity verification has been submitted from the {{user_name}}.<br/>Below information has been submitted<br/>{{submission_details}}', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_identity_submision_request_admin', 'equals', '1')
		),

		/* Emil to admin after parent confirmation */
		array(
			'id'      => 'divider_admin_parent_confirmation_templates',
			'type'    => 'info',
			'title'   => esc_html__('Parent confirmation', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_parent_confirmation_request_admin',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to admin after parent confirmation.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      => 'parent_confirmation_request_admin_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Parent confirm student verification submissions', 'tuturn'),
			'required'  => array('email_parent_confirmation_request_admin', 'equals', '1')
		),
		array(
			'id'      => 'divider_email_parent_confirmation_request_admin',
			'desc'    => wp_kses(
				__(
					'{{name}} — To display the user Name.<br>
					{{approve_profile}} — To display the user identification link.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_parent_confirmation_request_admin', 'equals', '1')
		),
		array(
			'id'      	=> 'parent_confirmation_admin_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello admin,', 'tuturn'),
			'required'  => array('email_parent_confirmation_request_admin', 'equals', '1')
		),
		array(
			'id'        => 'parent_confirmation_subbmision_request_admin_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('The parent has confirmed the user {{user_name}} verification. You can approve the user profile now.<br/>{{approve_profile}}', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_parent_confirmation_request_admin', 'equals', '1')
		),

		/* admin email on withdraw request */
		array(
			'id'      => 'divider_withdraw_request_templates',
			'type'    => 'info',
			'title'   => esc_html__('Withdraw Request From Instructor', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      	=> 'withdraw_request_admin_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add withdraw request email subject.', 'tuturn'),
			'default' 	=> esc_html__('A new withdrawal request.', 'tuturn'),
		),
		array(
			'id'      => 'divider_withdraw_req_information',
			'desc'    => wp_kses(
				__(
					'{{user_name}} — To display the Sender Name.<br>
							{{user_link}} — To display the user link.<br>
							{{amount}} — To display the amount.<br>
							{{detail}} — To display the withdraw detail link.<br>',
					'tuturn'
				),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
		),
		array(
			'id'      	=> 'withdraw_request_email_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add text.', 'tuturn'),
			'default' 	=> esc_html__('Hello,', 'tuturn'),
		),
		array(
			'id'        => 'withdraw_request_mail_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__(
					'You have received a new withdraw request from the “{{user_name}}” <br /> Please <a href="{{detail}}">click here</a> to view the withdrawal details <br />',
					'tuturn'
				),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
		),

		array(
			'id'      => 'divider_delete_account',
			'type'    => 'info',
			'title'   => esc_html__('Delete account', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      	=> 'delete_account_admin_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add delete account subject', 'tuturn'),
			'default' 	=> esc_html__('Account deleted', 'tuturn'),
		),
		array(
			'id'      => 'divider_delete_account_information',
			'desc'    => wp_kses(
				__(
					'{{username}} — To display the username.<br>
									{{useremail}} — To display the user email.<br>
								{{reason}} — To display the reason.<br>
								{{comments}} — To display the comments.<br>',
					'tuturn'
				),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
		),
		array(
			'id'      	=> 'delete_account_email_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add text.', 'tuturn'),
			'default' 	=> esc_html__('Hello,', 'tuturn'),
		),
		array(
			'id'        => 'delete_account_mail_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('A user with the name {{username}} have deleted the account. Reason is given below
				{{reason}}
				{{comments}}
			', 'tuturn'),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
		)
	),
));

/* email package purchase setting tab */
Redux::set_section($opt_name, array(
	'title'			=> esc_html__('Instructor', 'tuturn'),
	'id'			=> 'instructor_email_templates',
	'desc'			=> 'Instructor email templates',
	'icon'			=> '',
	'subsection'	=> true,
	'fields'		=> array(

		/* Instructor Email on Package purchase */
		array(
			'id'      => 'divider_instructor_packages_templates',
			'type'    => 'info',
			'title'   => esc_html__('Package Purchase', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_package_instructor',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to instructor on purchase package.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      	=> 'packages_purchase_instructor_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add email subject.', 'tuturn'),
			'default' 	=> esc_html__('Thank you for buying a package', 'tuturn'),
			'required'  => array('email_package_instructor', 'equals', '1')
		),
		array(
			'id'      => 'divider_instructor_packages_information',
			'desc'    => wp_kses(
				__('{{instructor_name}} — To display the instructor Name.<br>
					 {{order_id}} — To display the Order ID.<br>
					{{order_amount}} — To display the Order Ammount.<br>
					{{package_name}} — To display the Package Name.<br>', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_package_instructor', 'equals', '1')
		),
		array(
			'id'      => 'packages_purchase_instructor_greeting',
			'type'    => 'text',
			'title'   => esc_html__('Greeting', 'tuturn'),
			'desc'    => esc_html__('Please add text.', 'tuturn'),
			'default' => esc_html__('Hello {{instructor_name}},', 'tuturn'),
		),
		array(
			'id'        => 'package_purchase_instructor_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Thank you for buying “{{package_name}}”. <br />Your Order ID is #{{order_id}}<br /> You can now post courses and get orders.', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_package_instructor', 'equals', '1')
		),

		/* Instructor email on booking cancel */
		array(
			'id'      => 'divider_order_instructor_cancel_templates',
			'type'    => 'info',
			'title'   => esc_html__('Booking canceled', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_booking_cancel_instructor',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to instructor on booking cancel.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      => 'booking_cancel_instructor_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Booking has been canceled', 'tuturn'),
			'required'  => array('email_booking_cancel_instructor', 'equals', '1')
		),
		array(
			'id'      => 'divider_order_cancel_information_instructor',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{student_name}} — To display the student Name.<br>
					{{cancel_reason}} — To display the Order Ammount.<br>
					{{cancel_desc}} — To display the student Rating.<br>
					{{order_id}} — To display the Order ID.<br>
					{{login_url}} — To display the login Url.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_booking_cancel_instructor', 'equals', '1')
		),
		array(
			'id'      	=> 'booking_cancel_instructor_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{instructor_name}},', 'tuturn'),
			'required'  => array('email_booking_cancel_instructor', 'equals', '1')
		),
		array(
			'id'        => 'booking_cancel_instructor_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('We’re sorry but booking from “{{student_name}}” has been canceled with the reason "{{cancel_reason}}" <br /> Read the cancellation details: "{{cancel_desc}}". <br /> Order ID is #{{order_id}}', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_booking_cancel_instructor', 'equals', '1')
		),



		/* Instructor Email on New Order/booking */
		array(
			'id'      => 'divider_new_booking_instructor_templates',
			'type'    => 'info',
			'title'   =>  esc_html__('New booking', 'tuturn'),
			'style'   => 'info',
		),

		array(
			'id'       => 'email_new_booking_instructor',
			'type'     => 'switch',
			'title'    =>  esc_html__('Send Email', 'tuturn'),
			'subtitle' =>  esc_html__('Email to instructor on new booking recived.', 'tuturn'),
			'default'  =>  true,
		),

		array(
			'id'      	=> 'new_booking_instructor_email_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add email subject.', 'tuturn'),
			'default' 	=> esc_html__('New booking order received.', 'tuturn'),
			'required'  => array('email_new_booking_instructor', 'equals', '1')

		),
		array(
			'id'      => 'divider_new_booking_instructor_information',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{student_name}} — To display the student Name.<br>
					{{order_id}} — To display the Order ID.<br>
					{{login_url}} — To display the Login link.<br>
					{{order_amount}} — To display the Order Ammount.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_new_booking_instructor', 'equals', '1')

		),
		array(
			'id'      	=> 'new_booking_instructor_email_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add text.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{instructor_name}},', 'tuturn'),
			'required'  => array('email_new_booking_instructor', 'equals', '1')

		),
		array(
			'id'        => 'new_booking_instructor_mail_content',
			'type'      => 'textarea',
			'default'	=> wp_kses(
				__('You have received a new booking order with the Order ID #{{order_id}} from student “{{student_name}}”.', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_new_booking_instructor', 'equals', '1')

		),

		/* Instructor Email on booking declined */
		array(
			'id'      => 'divider_booking_instructor_declined_templates',
			'type'    => 'info',
			'title'   => esc_html__('Booking request declined', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_booking_decline_instructor',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to instructor on booking request rejection.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      	=> 'booking_request_declined_instructor_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add email subject.', 'tuturn'),
			'default' 	=> esc_html__('Booking request declined', 'tuturn'),
			'required'  => array('email_booking_decline_instructor', 'equals', '1')
		),
		array(
			'id'      => 'booking_decline_instructor_information',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{student_name}} — To display the student Name.<br>
					{{order_id}} — To display the Order ID.<br>
					{{decline_reason}} — To display the decline reason.<br>
					{{decline_desc}} — To display the decline detail.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_booking_decline_instructor', 'equals', '1')

		),
		array(
			'id'      => 'booking_declined_instructor_greeting',
			'type'    => 'text',
			'default' => esc_html__('Hello {{instructor_name}},', 'tuturn'),
			'title'   => esc_html__('Greeting', 'tuturn'),
			'desc'    => esc_html__('Add text', 'tuturn'),
			'required'  => array('email_booking_decline_instructor', 'equals', '1')
		),
		array(
			'id'        => 'booking_declined_instructor_content',
			'type'      => 'textarea',
			'default'	=> wp_kses(
				__(
					'You have declined the booking with the reason: “{{decline_reason}}”<br /> and left some comments: “{{decline_desc}}” <br />Against order ID #{{order_id}}.',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_booking_decline_instructor', 'equals', '1')
		),

		/* Instructor Email on Booking Completed */
		array(
			'id'      => 'divider_booking_status_templates',
			'type'    => 'info',
			'title'   => esc_html__('Booking completed', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_oder_complete_instructor',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to instructor on booking complete.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      => 'order_completed_instructor_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Booking completed', 'tuturn'),
			'required'  => array('email_oder_complete_instructor', 'equals', '1')
		),
		array(
			'id'      => 'divider_order_completed_information',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{student_name}} — To display the student Name.<br>
						{{order_id}} — To display the Order ID.<br>
					{{login_url}} — To display the login Url.<br>
					{{student_rating}} — To display the student Rating.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_oder_complete_instructor', 'equals', '1')
		),
		array(
			'id'      	=> 'order_completed_instructor_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{instructor_name}},', 'tuturn'),
			'required'  => array('email_oder_complete_instructor', 'equals', '1')
		),
		array(
			'id'        => 'order_completed_instructor_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Congratulations! <br /> The student “{{student_name}}” has closed the booking with the order ID #{{order_id}} and gave “{{student_rating}}” rating to you.', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_oder_complete_instructor', 'equals', '1')
		),

		/* instructor Email on withdraw request approved */
		array(
			'id'      => 'divider_withdraw_approved_templates',
			'type'    => 'info',
			'title'   => esc_html__('Withdraw Approved', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      	=> 'withdraw_approve_user_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add withdraw approved email subject.', 'tuturn'),
			'default' 	=> esc_html__('Your withdrawal request has been approved', 'tuturn'),
		),
		array(
			'id'      	=> 'withdraw_approve_user_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add text.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{user_name}},', 'tuturn'),
		),
		array(
			'id'      => 'divider_withdraw_approved_information',
			'desc'    => wp_kses(
				__('{{user_name}} — To display the Sender Name.<br>
							{{user_link}} — To display the user link.<br>
							{{amount}} — To display the amount.<br>', 'tuturn'),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
		),
		array(
			'id'        => 'withdraw_approved_mail_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Hooray! Your withdrawal request has been approved. <br /> <a href="{{user_link}}">Click here</a> to view the withdrawal details.', 'tuturn'),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
		),

		/* Reminder email to instrutor on volunteer hours log*/
		array(
			'id'      => 'divider_hour_log_approve_templates',
			'type'    => 'info',
			'title'   => esc_html__('Instructor hour log approve email', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_hours_log_approvel',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email trigger to the instructor on the submitted hour log for approval.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      => 'hours_log_approve_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Hours Approved.', 'tuturn'),
			'required'  => array('email_hours_log_approvel', 'equals', '1')
		),
		array(
			'id'      => 'divider_hours_log_approve',
			'desc'    => wp_kses(
				__(
					'{{student_name}} — To display the student name.<br>
					{{tutor_name}} — To display the tutor name.<br>
					{{instructor_email}} — To display the tutor email ID.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_hours_log_approvel', 'equals', '1')
		),
		array(
			'id'      	=> 'hours_log_approve_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{tutor_name}},', 'tuturn'),
			'required'  => array('email_hours_log_approvel', 'equals', '1')
		),
		array(
			'id'        => 'hours_log_approve_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Hi {{tutor_name}},
		Your submitted hours to the {{student_name}} have been approved', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_hours_log_approvel', 'equals', '1')
		),

		/* Email to instrutor on decline hour log request decline */
		array(
			'id'      => 'divider_hour_log_decline_templates',
			'type'    => 'info',
			'title'   => esc_html__('Instructor hour log decline email', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_hours_log_decline',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to the instructor on submitted hours log request declined.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      	=> 'decline_hours_request_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add email subject.', 'tuturn'),
			'default'	=> esc_html__('Hours request decline.', 'tuturn'),
			'required'  => array('email_hours_log_decline', 'equals', '1')
		),
		array(
			'id'      => 'divider_hours_log_decline',
			'desc'    => wp_kses(
				__(
					'{{student_name}} — To display the Student name.<br>
					{{tutor_name}}		 — To display the tutor name.<br>
					{{instructor_email}} — To display the tutor email ID.<br>
					{{decline_reason}} — To display the decline reason.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_hours_log_decline', 'equals', '1')
		),
		array(
			'id'      	=> 'decline_hours_request_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{tutor_name}},', 'tuturn'),
			'required'  => array('email_hours_log_decline', 'equals', '1')
		),
		array(
			'id'        => 'decline_hours_request_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Hi {{tutor_name}},
		Your submitted hours to the {{student_name}} have been approved', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_hours_log_decline', 'equals', '1')
		),
	)
));



/* Email template for Student on booking */
Redux::setSection($opt_name, array(
	'title'			=> esc_html__('Student', 'tuturn'),
	'id'			=> 'student_email_templates',
	'desc'			=> 'student email templtes',
	'icon'			=> '',
	'subsection'	=> true,
	'fields'		=> array(

		/*Student Email on Order/booking */
		array(
			'id'      => 'divider_new_booking_student_templates',
			'type'    => 'info',
			'title'   => esc_html__('New Booking', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_new_booking_student',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to student on new booking.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      	=> 'new_booking_student_email_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add email subject.', 'tuturn'),
			'default' 	=> esc_html__('You have made a new booking', 'tuturn'),
			'required'  => array('email_new_booking_student', 'equals', '1')
		),
		array(
			'id'      => 'new_booking_student_information',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{student_name}} — To display the Student Name.<br>
					{{order_id}} — To display the Order ID.<br>
					{{order_amount}} — To display the Order Ammount.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_new_booking_student', 'equals', '1')

		),
		array(
			'id'      	=> 'new_booking_student_email_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add text.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{student_name}},', 'tuturn'),
			'required'  => array('email_new_booking_student', 'equals', '1')

		),
		array(
			'id'        => 'new_booking_student_mail_content',
			'type'      => 'textarea',
			'default'	=> wp_kses(
				__('Thank you so much for considering my service. <br />Your order ID is #{{order_id}}<br /> You will receive a response shortly.', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_new_booking_student', 'equals', '1')
		),

		/*Student Email on Booking Approved */
		array(
			'id'      => 'divider_booking_approve_templates',
			'type'    => 'info',
			'title'   => esc_html__('Booking request approved', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_booking_approved_student',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to student on booking request approved.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      	=> 'booking_request_approved_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add email subject.', 'tuturn'),
			'default' 	=> esc_html__('Booking request approved', 'tuturn'),
			'required'  => array('email_booking_approved_student', 'equals', '1')
		),
		array(
			'id'      => 'booking_approved_student_information',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{student_name}} — To display the student Name.<br>
					{{order_id}} — To display the Order ID.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_booking_approved_student', 'equals', '1')
		),
		array(
			'id'      => 'booking_approved_greeting',
			'type'    => 'text',
			'default' => esc_html__('Hello {{student_name}},', 'tuturn'),
			'title'   => esc_html__('Greeting', 'tuturn'),
			'desc'    => esc_html__('Add text', 'tuturn'),
			'required'  => array('email_booking_approved_student', 'equals', '1')
		),
		array(
			'id'        => 'booking_request_approved_content',
			'type'      => 'textarea',
			'default'	=> wp_kses(
				__('Congratulations! <br /> The instructor “{{instructor_name}}” has approved the booking with the order ID #{{order_id}}', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_booking_approved_student', 'equals', '1')
		),

		/* Instructor email on booking cancel */
		array(
			'id'      => 'divider_booking_meeting_detail_templates',
			'type'    => 'info',
			'title'   => esc_html__('Meeting detail', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_booking_meeting_detail',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to student on updat booking detail.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      => 'booking_meeting_detail_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Booking meeting detail has been updated.', 'tuturn'),
			'required'  => array('email_booking_meeting_detail', 'equals', '1')
		),
		array(
			'id'      => 'divider_booking_meeting_information_student',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{order_id}} 			— To display the Order id.<br>
					{{student_name}} 		— To display the student Name.<br>
					{{meeting_type}} 		— To display the meeting type.<br>
					{{meeting_url}} 		— To display the meeting URL.<br>
					{{meeting_description}} — To display the description.<br>
					{{current_date}}		— To display the updation date.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_booking_meeting_detail', 'equals', '1')
		),
		array(
			'id'      	=> 'booking_meeting_detail_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{student_name}},', 'tuturn'),
			'required'  => array('email_booking_meeting_detail', 'equals', '1')
		),
		array(
			'id'        => 'booking_meeting_detail_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Hi “{{student_name}}” meeting detail has been updated.<br /> Order ID is #{{order_id}}', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_booking_meeting_detail', 'equals', '1')
		),

		/* Reminder email to parent on volunteer hours log*/
		array(
			'id'      => 'divider_reminder_email_templates',
			'type'    => 'info',
			'title'   => esc_html__('Parent reminder email', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_parent_volunteer_hours_reminder',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email trigger to the parents to remind volunteer hours log.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      => 'volunteer_hours_update_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Volunteer update hours log.', 'tuturn'),
			'required'  => array('email_parent_volunteer_hours_reminder', 'equals', '1')
		),
		array(
			'id'      => 'divider_volunteer_hours_information',
			'desc'    => wp_kses(
				__('{{parent_name}}  — To display the Parent Name.<br>{{tutor_name}}  — To display the tutor name', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_parent_volunteer_hours_reminder', 'equals', '1')
		),
		array(
			'id'      	=> 'volunteer_hours_update_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{parent_name}},', 'tuturn'),
			'required'  => array('email_parent_volunteer_hours_reminder', 'equals', '1')
		),
		array(
			'id'        => 'volunteer_hours_update_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Hi {{parent_name}},
		Tutor "{{tutor_name}}" has sent the hours for approval. You can log in to your child"s account and approve the submitted hours', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_booking_meeting_detail', 'equals', '1')
		),


		/* Student email on booking cancel */
		array(
			'id'      => 'divider_order_cancel_student_templates',
			'type'    => 'info',
			'title'   => esc_html__('Booking canceled', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_booking_cancel_student',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to student on booking cancel.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      => 'booking_cancel_student_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Booking canceled', 'tuturn'),
			'required'  => array('email_booking_cancel_student', 'equals', '1')
		),
		array(
			'id'      => 'divider_order_cancel_information',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{student_name}} — To display the student Name.<br>
					{{cancel_reason}} — To display the Order Ammount.<br>
					{{cancel_desc}} — To display the student Rating.<br>
					{{order_id}} — To display the Order ID.<br>
					{{login_url}} — To display the login Url.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_booking_cancel_student', 'equals', '1')
		),
		array(
			'id'      	=> 'booking_cancel_student_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{student_name}},', 'tuturn'),
			'required'  => array('email_booking_cancel_student', 'equals', '1')
		),
		array(
			'id'        => 'booking_cancel_student_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('You have canceled the booking with the reason: <br />"{{cancel_reason}}" <br />Detail: "{{cancel_desc}}". <br /> Your order ID #{{order_id}}', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_booking_cancel_student', 'equals', '1')
		),

		/* Student receive email on submistion hour log */
		array(
			'id'      => 'divider_hour_log_subbmision_templates',
			'type'    => 'info',
			'title'   => esc_html__('Hours log approval request', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_hour_log_submission',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email trigger to the student on hour log submission.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      => 'hour_log_submission_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Hour log submission', 'tuturn'),
			'required'  => array('email_hour_log_submission', 'equals', '1')
		),
		array(
			'id'      => 'divider_hour_log_submission',
			'desc'    => wp_kses(
				__(
					'{{student_name}} — To display the Student Name.<br>
					{{login_url}} — To display the login Url.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_hour_log_submission', 'equals', '1')
		),
		array(
			'id'      	=> 'update_hour_logt_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{student_name}},', 'tuturn'),
			'required'  => array('email_hour_log_submission', 'equals', '1')
		),
		array(
			'id'        => 'update_hour_log_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Hi {{student_name}},
		A tutor has submitted the hours for approval. You can accept or decline with reason', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_hour_log_submission', 'equals', '1')
		),

		/* Student Email on booking declined */
		array(
			'id'      => 'divider_booking_declined_templates',
			'type'    => 'info',
			'title'   => esc_html__('Booking request declined', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_booking_decline_student',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to student on booking request rejection.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      	=> 'booking_request_declined_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add email subject.', 'tuturn'),
			'default' 	=> esc_html__('Booking request declined', 'tuturn'),
			'required'  => array('email_booking_decline_student', 'equals', '1')
		),
		array(
			'id'      => 'booking_decline_student_information',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{student_name}} — To display the student Name.<br>
					{{order_id}} — To display the Order ID.<br>
					{{decline_reason}} — To display the decline reason.<br>
					{{decline_desc}} — To display the decline detail.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_booking_decline_student', 'equals', '1')

		),
		array(
			'id'      => 'booking_declined_greeting',
			'type'    => 'text',
			'default' => esc_html__('Hello {{student_name}},', 'tuturn'),
			'title'   => esc_html__('Greeting', 'tuturn'),
			'desc'    => esc_html__('Add text', 'tuturn'),
			'required'  => array('email_booking_decline_student', 'equals', '1')
		),
		array(
			'id'        => 'order_complete_request_declined_content',
			'type'      => 'textarea',
			'default'	=> wp_kses(
				__(
					'The instructor “{{instructor_name}}” has declined the booking with the reason: <br /> "{{decline_reason}}" and left some comments <br /> "{{decline_desc}}" <br />against the order ID #{{order_id}}.',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_booking_decline_student', 'equals', '1')
		),

		/* Student Email on Refund */
		array(
			'id'      => 'divider_student_refund_approved_templates',
			'type'    => 'info',
			'title'   => esc_html__('Student Refund approved', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'       => 'email_refund_approv_student',
			'type'     => 'switch',
			'title'    => esc_html__('Send Email', 'tuturn'),
			'subtitle' => esc_html__('Email to student on refund approve.', 'tuturn'),
			'default'  => true,
		),
		array(
			'id'      	=> 'student_approved_refund_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add email subject.', 'tuturn'),
			'default' 	=> esc_html__('Payment refunded successfully', 'tuturn'),
			'required'  => array('email_refund_approv_student', 'equals', '1')

		),
		array(
			'id'      => 'divider_approved_student_refund_information',
			'desc'    => wp_kses(
				__(
					'{{instructor_name}} — To display the instructor Name.<br>
					{{student_name}} — To display the Student Name.<br>
					{{order_id}} — To display the Order ID.<br>
					{{login_url}} — To display the Login Url.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
			'required'  => array('email_refund_approv_student', 'equals', '1')
		),
		array(
			'id'      	=> 'student_approved_refund_email_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add text', 'tuturn'),
			'default' 	=> esc_html__('Hello {{student_name}},', 'tuturn'),
			'required'  => array('email_refund_approv_student', 'equals', '1')
		),
		array(
			'id'        => 'approved_student_refund_content',
			'type'      => 'textarea',
			'default'   =>  wp_kses(
				__('Congratulations! <br /> Your payment has been refunded by the “{{instructor_name}}” against the order ID #{{order_id}}', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
			'required'  => array('email_refund_approv_student', 'equals', '1')
		),
	)
));

/* email Registration setting tab */
Redux::set_section($opt_name, array(
	'title'			=> esc_html__('Registration', 'tuturn'),
	'id'			=> 'registration_email_templates',
	'desc'			=> 'Registration email templates',
	'icon'			=> '',
	'subsection'	=> true,
	'fields'		=> array(

		/* Email to user on auto approve */
		array(
			'id'      => 'divider_user_register_auto_templates',
			'type'    => 'info',
			'title'   => esc_html__('Email on user registration with auto approve', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      	=> 'user_registration_auto_approve_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add subject for user registration.', 'tuturn'),
			'default' 	=> esc_html__('Thank you for signing up at {{sitename}}', 'tuturn'),
		),
		array(
			'id'      => 'register_user_auto_approve_info',
			'desc'    => wp_kses(
				__('{{name}} — To display the user name.<br>
						{{email}} — To display the email<br/>
						{{sitename}} — To display the sitename<br/>', 'tuturn'),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
		),
		array(
			'id'      	=> 'email_user_registration_auto_approve_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add text for greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{name}},', 'tuturn'),
		),
		array(
			'id'        => 'user_registration_auto_approve_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Congratulations! <br /> You have successfully signed up at “{{sitename}}”.', 'tuturn'),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
		),

		/* Email to user on registration */
		array(
			'id'      => 'divider_user_register_templates',
			'type'    => 'info',
			'title'   => esc_html__('Email on user registration with verification link', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      	=> 'user_registration_subject',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Subject', 'tuturn'),
			'desc'    	=> esc_html__('Please add subject for user registration.', 'tuturn'),
			'default' 	=> esc_html__('Thank you for signing up at {{sitename}}', 'tuturn'),
		),
		array(
			'id'      => 'register_user_new_information',
			'desc'    => wp_kses(
				__('{{name}} — To display the user name.<br>
						{{email}} — To display the email<br/>
						{{sitename}} — To display the sitename<br/>
						{{password}} — To display the password<br/>
						{{verification_link}} — To display the verification link<br/>', 'tuturn'),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle',
		),
		array(
			'id'      	=> 'email_user_registration_greeting',
			'type'    	=> 'text',
			'title'   	=> esc_html__('Greeting', 'tuturn'),
			'desc'    	=> esc_html__('Please add text for greeting.', 'tuturn'),
			'default' 	=> esc_html__('Hello {{name}},', 'tuturn'),

		),
		array(
			'id'        => 'user_registration_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Thank you for signing up at “{{sitename}}”. <br />Please click the link below to verify your account <br /> {{verification_link}}.', 'tuturn'),
				array(
					'a'	=> array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email Contents', 'tuturn'),
		),
		/* Email on Password reset */
		array(
			'id'      => 'divider_password_reset_templates',
			'type'    => 'info',
			'title'   =>  esc_html__('Password Reset', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      => 'user_password_reset_subject',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Password reset request', 'tuturn'),
		),
		array(
			'id'      => 'divider_user_reset_password_information',
			'desc'    => wp_kses(
				__('{{name}} — To display the user name.<br>
								{{email}} — To display the user email.<br>
								{{sitename}} — To display the sitename.<br>
								{{reset_link}} — To display the sitename.<br>', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle'
		),
		array(
			'id'      => 'user_reset_password_greeting',
			'type'    => 'text',
			'title'   => esc_html__('Greeting', 'tuturn'),
			'desc'    => esc_html__('Please add text.', 'tuturn'),
			'default' => esc_html__('Hello {{name}},', 'tuturn'),
		),
		array(
			'id'        => 'user_reset_password_content',
			'type'      => 'textarea',
			'default'   => wp_kses(
				__('Click on the link below to reset your password:<br />{{reset_link}}<br />Please be advised, If you don’t want to reset your password then please ignore this email and nothing will happen to your current password.<br />', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'		=> esc_html__('Email Content', 'tuturn'),
		),

		/* email to user on social registration */
		array(
			'id'      => 'divider_email_social_registration_templates',
			'type'    => 'info',
			'title'   => esc_html__('Google registration email', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      => 'subject_social_registration_user_email',
			'type'    => 'text',
			'title'   => esc_html__('Subject', 'tuturn'),
			'desc'    => esc_html__('Please add email subject.', 'tuturn'),
			'default' => esc_html__('Signed up at {{sitename}} via google account', 'tuturn'),
		),
		array(
			'id'      => 'information_social_registration_user_email',
			'desc'    =>	wp_kses(
				__(
					'{{name}} — To display the user name.<br>
								{{email}} — To display the user email.<br>
								{{login_url}} — To display the login url.<br>
								{{sitename}} — To display the sitename.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle'
		),
		array(
			'id'      	=> 'greeting_social_registration_user_email',
			'type'    	=> 'text',
			'title'   	=>  esc_html__('Greeting', 'tuturn'),
			'desc'    	=>  esc_html__('Please add text.', 'tuturn'),
			'default' 	=>  esc_html__('Hello {{name}},', 'tuturn'),
		),
		array(
			'id'        => 'content_social_registration_user_email',
			'type'      => 'textarea',
			'default'   =>  wp_kses(
				__('Thank you for the signing up at “{{sitename}}” Your account has been created successfully. ', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     =>  esc_html__('Email Contents', 'tuturn'),
		),
		/* Email on Account Approve Request */
		array(
			'id'      => 'divider_approval_request_user_account_templates',
			'type'    => 'info',
			'title'   => 	esc_html__('Account Approval Request', 'tuturn'),
			'style'   => 'info',
		),
		array(
			'id'      => 'user_account_approval_subject',
			'type'    => 'text',
			'title'   =>  esc_html__('Subject', 'tuturn'),
			'desc'    =>  esc_html__('Please add email subject.', 'tuturn'),
			'default' =>  esc_html__('Thank you for signing up at {{sitename}}', 'tuturn'),
		),
		array(
			'id'      => 'divider_user_account_request_approval_information',
			'desc'    => wp_kses(
				__(
					'{{name}} — To display the user name.<br>
						{{email}} — To display the user email.<br>
						{{password}} — To display the user password.<br>
						{{sitename}} — To display the sitename.<br>',
					'tuturn'
				),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     => esc_html__('Email setting variables', 'tuturn'),
			'type'      => 'info',
			'class'     => 'dc-center-content',
			'icon'      => 'el el-info-circle'
		),
		array(
			'id'      => 'user_account_approval_request_greeting',
			'type'    => 'text',
			'title'   =>  esc_html__('Greeting', 'tuturn'),
			'desc'    =>  esc_html__('Please add text.', 'tuturn'),
			'default' =>  esc_html__('Hello {{name}},', 'tuturn'),
		),
		array(
			'id'        => 'user_account_approval_content',
			'type'      => 'textarea',
			'default'   =>  wp_kses(
				__('Thank you for the signing up at {{sitename}}. Your account will be approved after the verification.', 'tuturn'),
				array(
					'a'       => array(
						'href'  => array(),
						'title' => array()
					),
					'br'      => array(),
					'em'      => array(),
					'strong'  => array(),
				)
			),
			'title'     =>  esc_html__('Email Contents', 'tuturn')
		),


		/* Emil to parent on student identity request submission */

	)
));

//Consent email
Redux::set_section(
	$opt_name,
	array(
		'title'			=> esc_html__('Parent Consent', 'tuturn'),
		'id'			=> 'consent_email_templates',
		'desc'			=> esc_html__('Parent consent email template', 'tuturn'),
		'icon'			=> '',
		'subsection'	=> true,
		'fields'		=> array(
			/**
			 * Email to parent after
			 * identity verification form 
			 * submission by the user
			 */
			array(
				'id'      => 'divider_user_identity_request_templates',
				'type'    => 'info',
				'title'   => esc_html__('A parental consent email', 'tuturn'),
				'style'   => 'info',
			),
			array(
				'id'       => 'email_identity_submision_request_user',
				'type'     => 'switch',
				'title'    => esc_html__('Send Email', 'tuturn'),
				'subtitle' => esc_html__('Email to parent on identity submission.', 'tuturn'),
				'default'  => true,
			),
			array(
				'id'      => 'identity_request_user_subject',
				'type'    => 'text',
				'title'   => esc_html__('Subject', 'tuturn'),
				'desc'    => esc_html__('Please add email subject.', 'tuturn'),
				'default' => esc_html__('A parental consent', 'tuturn'),
				'required'  => array('email_identity_submision_request_user', 'equals', '1')
			),
			array(
				'id'      => 'divider_email_identity_submision_request_user',
				'desc'    => wp_kses(
					__(
						'{{user_name}}	— To display the user name.<br>
						{{user_email}} 	— To display the email.<br>
						{{parent_name}} 	— To display the user parent name.<br>
						{{submission_details}} — To display the submission details<br> 
						{{confirmation_link}} — To display confirmation URL<br>
						{{confirmation_html}} — To display confirmation button html<br>
						{{gender}} 	— To display the user gender.<br>
						{{phone_number}} 	— To display the user phone number.<br>
						{{address}} 	— To display the user address.<br>
						{{school_name}} 	— To display the user school name.<br>
						{{parent_phone}} 	— To display the user parent phone.<br>
						{{other_introduction}} 	— To display the user other introduction.<br>
						{{parent_email}} 	— To display the user parent email.<br>',
						'tuturn'
					),
					array(
						'a'       => array(
							'href'  => array(),
							'title' => array()
						),
						'br'      => array(),
						'em'      => array(),
						'strong'  => array(),
					)
				),
				'title'     => esc_html__('Email setting variables', 'tuturn'),
				'type'      => 'info',
				'class'     => 'dc-center-content',
				'icon'      => 'el el-info-circle',
				'required'  => array('email_identity_submision_request_user', 'equals', '1')
			),
			array(
				'id'      	=> 'user_identity_request_parent_greeting',
				'type'    	=> 'text',
				'title'   	=> esc_html__('Greeting', 'tuturn'),
				'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
				'default' => esc_html__('Hello {{parent_name}},', 'tuturn'),
				'required'  => array('email_identity_submision_request_user', 'equals', '1')
			),
			array(
				'id'        => 'user_identity_request_parent_content',
				'type'      => 'textarea',
				'default'   => wp_kses(
					__('We have received the parental consent submission from your child. You can verify the below details.<br/>{{submission_details}}<br/>{{confirmation_html}}', 'tuturn'),
					array(
						'a'       => array(
							'href'  => array(),
							'title' => array()
						),
						'br'      => array(),
						'em'      => array(),
						'strong'  => array(),
					)
				),
				'title'     => esc_html__('Email Contents', 'tuturn'),
				'required'  => array('email_identity_submision_request_user', 'equals', '1')
			),

			/**
			 * Email to user after
			 * identity verification approved by admin
			 */
			array(
				'id'      => 'divider_user_profile_approved_templates',
				'type'    => 'info',
				'title'   => esc_html__('Identity Verification Approved', 'tuturn'),
				'style'   => 'info',
			),
			array(
				'id'       => 'user_profile_approved_switch',
				'type'     => 'switch',
				'title'    => esc_html__('Send Email', 'tuturn'),
				'subtitle' => esc_html__('Email to user after profile approved', 'tuturn'),
				'default'  => true,
			),
			array(
				'id'      => 'user_profile_approved_subject',
				'type'    => 'text',
				'title'   => esc_html__('Subject', 'tuturn'),
				'desc'    => esc_html__('Please add email subject.', 'tuturn'),
				'default' => esc_html__('Identification Approved', 'tuturn'),
				'required'  => array('user_profile_approved_switch', 'equals', '1')
			),
			array(
				'id'      => 'divider_user_profile_approved',
				'desc'    => wp_kses(
					__(
						'{{user_name}}	— To display the user name.<br>
						{{user_email}} 	— To display the email.<br>
						{{get_logged_in}} 	— To display the login link.<br>',
						'tuturn'
					),
					array(
						'a'       => array(
							'href'  => array(),
							'title' => array()
						),
						'br'      => array(),
						'em'      => array(),
						'strong'  => array(),
					)
				),
				'title'     => esc_html__('Email setting variables', 'tuturn'),
				'type'      => 'info',
				'class'     => 'dc-center-content',
				'icon'      => 'el el-info-circle',
				'required'  => array('user_profile_approved_switch', 'equals', '1')
			),
			array(
				'id'      	=> 'user_profile_approved_greeting',
				'type'    	=> 'text',
				'title'   	=> esc_html__('Greeting', 'tuturn'),
				'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
				'default' => esc_html__('Hello {{user_name}},', 'tuturn'),
				'required'  => array('user_profile_approved_switch', 'equals', '1')
			),
			array(
				'id'        => 'user_profile_approved_content',
				'type'      => 'textarea',
				'default'   => wp_kses(
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
				),
				'title'     => esc_html__('Email Contents', 'tuturn'),
				'required'  => array('user_profile_approved_switch', 'equals', '1')
			),

			/**
			 * Email to user after
			 * identity verification rejected by admin
			 */
			array(
				'id'      => 'divider_user_profile_rejected_templates',
				'type'    => 'info',
				'title'   => esc_html__('Identity Verification Rejected', 'tuturn'),
				'style'   => 'info',
			),
			array(
				'id'       => 'user_profile_rejected_switch',
				'type'     => 'switch',
				'title'    => esc_html__('Send Email', 'tuturn'),
				'subtitle' => esc_html__('Email to user after profile rejected', 'tuturn'),
				'default'  => true,
			),
			array(
				'id'      => 'user_profile_rejected_subject',
				'type'    => 'text',
				'title'   => esc_html__('Subject', 'tuturn'),
				'desc'    => esc_html__('Please add email subject.', 'tuturn'),
				'default' => esc_html__('Identification Rejected', 'tuturn'),
				'required'  => array('user_profile_rejected_switch', 'equals', '1')
			),
			array(
				'id'      => 'divider_user_profile_rejected',
				'desc'    => wp_kses(
					__(
						'{{user_name}}	— To display the user name.<br>
						{{user_email}} 	— To display the email.<br>
						{{reject_reason}} 	— To display the reject reason.<br>
						{{get_logged_in}} 	— To display the login link.<br>',
						'tuturn'
					),
					array(
						'a'       => array(
							'href'  => array(),
							'title' => array()
						),
						'br'      => array(),
						'em'      => array(),
						'strong'  => array(),
					)
				),
				'title'     => esc_html__('Email setting variables', 'tuturn'),
				'type'      => 'info',
				'class'     => 'dc-center-content',
				'icon'      => 'el el-info-circle',
				'required'  => array('user_profile_rejected_switch', 'equals', '1')
			),
			array(
				'id'      	=> 'user_profile_rejected_greeting',
				'type'    	=> 'text',
				'title'   	=> esc_html__('Greeting', 'tuturn'),
				'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
				'default' => esc_html__('Hello {{user_name}},', 'tuturn'),
				'required'  => array('user_profile_rejected_switch', 'equals', '1')
			),
			array(
				'id'        => 'user_profile_rejected_content',
				'type'      => 'textarea',
				'default'   => wp_kses(
					__('The admin has reject your identification and leave some comments <br/> {{reject_reason}}', 'tuturn'),
					array(
						'a'       => array(
							'href'  => array(),
							'title' => array()
						),
						'br'      => array(),
						'em'      => array(),
						'strong'  => array(),
					)
				),
				'title'     => esc_html__('Email Contents', 'tuturn'),
				'required'  => array('user_profile_rejected_switch', 'equals', '1')
			),

			/**
			 * Email to student on
			 * submitting documents
			 */
			array(
				'id'      => 'divider_student_submit_doc_templates',
				'type'    => 'info',
				'title'   => esc_html__('Student Submitting Documents', 'tuturn'),
				'style'   => 'info',
			),
			array(
				'id'       => 'student_submit_doc_switch',
				'type'     => 'switch',
				'title'    => esc_html__('Send Email', 'tuturn'),
				'subtitle' => esc_html__('Email to student after submitting documents', 'tuturn'),
				'default'  => true,
			),
			array(
				'id'      => 'student_submit_doc_subject',
				'type'    => 'text',
				'title'   => esc_html__('Subject', 'tuturn'),
				'desc'    => esc_html__('Please add email subject.', 'tuturn'),
				'default' => esc_html__('Identity Documents Received', 'tuturn'),
				'required'  => array('student_submit_doc_switch', 'equals', '1')
			),
			array(
				'id'      => 'divider_student_submit_doc',
				'desc'    => wp_kses(
					__(
						'{{user_name}}	— To display the user name.<br>
						{{user_email}} 	— To display the email.<br>',
						'tuturn'
					),
					array(
						'a'       => array(
							'href'  => array(),
							'title' => array()
						),
						'br'      => array(),
						'em'      => array(),
						'strong'  => array(),
					)
				),
				'title'     => esc_html__('Email setting variables', 'tuturn'),
				'type'      => 'info',
				'class'     => 'dc-center-content',
				'icon'      => 'el el-info-circle',
				'required'  => array('student_submit_doc_switch', 'equals', '1')
			),
			array(
				'id'      	=> 'student_submit_doc_greeting',
				'type'    	=> 'text',
				'title'   	=> esc_html__('Greeting', 'tuturn'),
				'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
				'default' => esc_html__('Hello {{user_name}},', 'tuturn'),
				'required'  => array('student_submit_doc_switch', 'equals', '1')
			),
			array(
				'id'        => 'student_submit_doc_content',
				'type'      => 'textarea',
				'default'   => wp_kses(
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
				),
				'title'     => esc_html__('Email Contents', 'tuturn'),
				'required'  => array('student_submit_doc_switch', 'equals', '1')
			),

			/**
			 * Email to instructor on
			 * submitting documents
			 */
			array(
				'id'      => 'divider_instructor_submit_doc_templates',
				'type'    => 'info',
				'title'   => esc_html__('Instructor Submitting Documents', 'tuturn'),
				'style'   => 'info',
			),
			array(
				'id'       => 'instructor_submit_doc_switch',
				'type'     => 'switch',
				'title'    => esc_html__('Send Email', 'tuturn'),
				'subtitle' => esc_html__('Email to instructor after submitting documents', 'tuturn'),
				'default'  => true,
			),
			array(
				'id'      => 'instructor_submit_doc_subject',
				'type'    => 'text',
				'title'   => esc_html__('Subject', 'tuturn'),
				'desc'    => esc_html__('Please add email subject.', 'tuturn'),
				'default' => esc_html__('Identity Documents Received', 'tuturn'),
				'required'  => array('instructor_submit_doc_switch', 'equals', '1')
			),
			array(
				'id'      => 'divider_instructor_submit_doc',
				'desc'    => wp_kses(
					__(
						'{{user_name}}	— To display the user name.<br>
						{{user_email}} 	— To display the email.<br>',
						'tuturn'
					),
					array(
						'a'       => array(
							'href'  => array(),
							'title' => array()
						),
						'br'      => array(),
						'em'      => array(),
						'strong'  => array(),
					)
				),
				'title'     => esc_html__('Email setting variables', 'tuturn'),
				'type'      => 'info',
				'class'     => 'dc-center-content',
				'icon'      => 'el el-info-circle',
				'required'  => array('instructor_submit_doc_switch', 'equals', '1')
			),
			array(
				'id'      	=> 'instructor_submit_doc_greeting',
				'type'    	=> 'text',
				'title'   	=> esc_html__('Greeting', 'tuturn'),
				'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
				'default' => esc_html__('Hello {{user_name}},', 'tuturn'),
				'required'  => array('instructor_submit_doc_switch', 'equals', '1')
			),
			array(
				'id'        => 'instructor_submit_doc_content',
				'type'      => 'textarea',
				'default'   => wp_kses(
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
				),
				'title'     => esc_html__('Email Contents', 'tuturn'),
				'required'  => array('instructor_submit_doc_switch', 'equals', '1')
			),


			/**
			 * Email to Student on
			 * submitting documents
			 * whent parent consent is off
			 */
			array(
				'id'      => 'divider_submit_student_doc_templates',
				'type'    => 'info',
				'title'   => esc_html__('Student submitting documents if parent consent is off', 'tuturn'),
				'style'   => 'info',
			),
			array(
				'id'       => 'submit_student_doc_switch',
				'type'     => 'switch',
				'title'    => esc_html__('Send Email', 'tuturn'),
				'subtitle' => esc_html__('Email to student after submitting documents', 'tuturn'),
				'default'  => true,
			),
			array(
				'id'      	=> 'submit_student_doc_subject',
				'type'    	=> 'text',
				'title'   	=> esc_html__('Subject', 'tuturn'),
				'desc'    	=> esc_html__('Please add email subject.', 'tuturn'),
				'default' 	=> esc_html__('Identity Documents Received', 'tuturn'),
				'required'  => array('submit_student_doc_switch', 'equals', '1')
			),
			array(
				'id'      => 'divider_submit_student_doc',
				'desc'    => wp_kses(
					__(
						'{{user_name}}	— To display the user name.<br>
						{{user_email}} 	— To display the email.<br>',
						'tuturn'
					),
					array(
						'a'       => array(
							'href'  => array(),
							'title' => array()
						),
						'br'      => array(),
						'em'      => array(),
						'strong'  => array(),
					)
				),
				'title'     => esc_html__('Email setting variables', 'tuturn'),
				'type'      => 'info',
				'class'     => 'dc-center-content',
				'icon'      => 'el el-info-circle',
				'required'  => array('submit_student_doc_switch', 'equals', '1')
			),
			array(
				'id'      	=> 'submit_student_doc_greeting',
				'type'    	=> 'text',
				'title'   	=> esc_html__('Greeting', 'tuturn'),
				'desc'    	=> esc_html__('Please add email greeting.', 'tuturn'),
				'default' => esc_html__('Hello {{user_name}},', 'tuturn'),
				'required'  => array('submit_student_doc_switch', 'equals', '1')
			),
			array(
				'id'        => 'submit_student_doc_content',
				'type'      => 'textarea',
				'default'   => wp_kses(
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
				),
				'title'     => esc_html__('Email Contents', 'tuturn'),
				'required'  => array('submit_student_doc_switch', 'equals', '1')
			),



		)
	)
);
