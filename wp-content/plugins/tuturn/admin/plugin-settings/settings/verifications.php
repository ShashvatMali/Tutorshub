<?php

/**
 * User verification Settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Plugin_Settings/Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */

Redux::setSection(
    $opt_name,
    array(
        'title'         => esc_html__('User verification', 'tuturn'),
        'id'            => 'verification_settings',
        'subsection'    => false,
        'icon'          => 'el el-globe',
        'fields'        =>  array(
            array(
                'id'    =>'email_verify_wrap',
                'type'  => 'info',
                'title' => esc_html__('User email verification', 'tuturn'),
                'style' => 'info',
            ),
            array(
                'id'        => 'user_account_approve',
                'type'      => 'switch',
                'title'     => esc_html__('Auto approve', 'tuturn'),
                'subtitle'  => esc_html__('Auto approve user account upon registration', 'tuturn'),
                'default'   => true,
            ),
            array(
                'id'        => 'email_user_registration',
                'type'      => 'select',
                'title'     => esc_html__('Send Email', 'tuturn'),
                'desc'      => esc_html__('Please select new user account verification type', 'tuturn'),
                'options'   => array(
                    'verify_by_link'    => esc_html__('Verify by auto generated link', 'tuturn'),
                    'verify_by_admin'   => esc_html__('Verify by admin', 'tuturn'),
                ),
                'required'  => array('user_account_approve','equals','0'),
                'default'   => 'verify_by_link',
            ),
            array(
                'id'    =>'id_verify_wrap',
                'type'  => 'info',
                'title' => esc_html__('Identity verification', 'tuturn'),
                'style' => 'info',
            ),
            array(
                'id'        => 'identity_verification',
                'type'      => 'select',
                'title'     => esc_html__('Identity verification', 'tuturn'),
                'desc'      => esc_html__('Select the identity verification type', 'tuturn'),
                'options'   => array(
                    'tutors'        => esc_html__('For Tutor only', 'tuturn'),
                    'students'      => esc_html__('For Students only', 'tuturn'),
                    'both'          => esc_html__('For both students and tutors', 'tuturn'),
                    'none'          => esc_html__('Disable for both type of users', 'tuturn'),
                ),
                'default'   => 'tutors',
            ),
            array(
                'id'        => 'resubmit_verification',
                'type'      => 'switch',
                'title'     => esc_html__('Resubmit verification', 'tuturn'),
                'default'   => false,
                'desc'      => esc_html__('If that is enabled then both users will be able to resubmit their documents multiple times.', 'tuturn')
            ),
            array(
                'id'        => 'parental_consent',
                'type'      => 'select',
                'title'     => esc_html__('Parental consent', 'tuturn'),
                'desc'      => esc_html__('You can enable parent consent email for the student ID verification process', 'tuturn'),
                'options'   => array(
                    'yes'        => esc_html__('Yes', 'tuturn'),
                    'no'         => esc_html__('No', 'tuturn'),
                ),
                'default'   => 'no',
            ),
            array(
                'id'       => 'student_fields',
                'type'     => 'select',
                'multi'    => true,
                'title'    => esc_html__('Include student fields', 'tuturn'), 
                'desc'     => esc_html__('Please select whatever fields do you want to include in the student verification form. In case of parental consent, email of parent or guardian is required', 'tuturn'),
                'options'  => array(
                    'student_id'            => esc_html__('Student ID/Number', 'tuturn'),
                    'school'                => esc_html__('School', 'tuturn'),
                    'parent_name'           => esc_html__('Parent name', 'tuturn'),
                    'parent_email'          => esc_html__('Parent email', 'tuturn'),
                    'parent_phone'          => esc_html__('Parent phone', 'tuturn'),
                ),
                'default'   => 'parent_email',
            ),
            array(
                'id'        => 'identity_verification_listings',
                'type'      => 'switch',
                'title'     => esc_html__('Identity verified instructor listings', 'tuturn'),
                'default'   => false,
                'desc'      => esc_html__('If that is enabled then only identity verified instructors will be appear in search result.', 'tuturn')
            ),
            array(
                'id'        => 'identity_verification_booking',
                'type'      => 'switch',
                'title'     => esc_html__('Identity verified student booking', 'tuturn'),
                'default'   => false,
                'desc'      => esc_html__('If enabled then identity verified students will be able to book the instructor.', 'tuturn')
            ),
        )
    )
);
