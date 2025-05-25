<?php

/**
 * General Settings
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
        'title'         => esc_html__('Directory Settings', 'tuturn'),
        'id'            => 'general_settings',
        'subsection'    => false,
        'icon'          => 'el el-globe',
        'fields'        =>  array(
            array(
                'id'       => 'sec_registration_start',
                'type'     => 'info',
                'title' => esc_html__('Registration settings', 'tuturn'),
                'style' => 'info',
            ),
            array(
                'id'        => 'user_name_option',
                'type'      => 'switch',
                'title'     => esc_html__('Enable/disable username', 'tuturn'),
                'subtitle'  => esc_html__('Enable/disable username in the registration', 'tuturn'),
                'default'   => false,
            ),

            array(
                'id'        => 'user_phone_option',
                'type'      => 'switch',
                'title'     => esc_html__('Enable/disable phone number', 'tuturn'),
                'subtitle'  => esc_html__('Enable/disable phone number in the registration', 'tuturn'),
                'default'   => false,
            ),
            array(
                'id'       => 'password_strength',
                'type'     => 'select',
                'multi'    => true,
                'title'    => esc_html__('Password strength', 'tuturn'),
                'desc'     => esc_html__('Select password strength', 'tuturn'),
                'options'  => array(
                    'length'               => wp_kses(__('Password must be 8 characters', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
                    'upper'                => wp_kses(__('1 upper case', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
                    'lower'              => wp_kses(__('1 lower case', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
                    'special_character' => wp_kses(__('1 special character', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
                    'number'              => wp_kses(__('1 number', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
                ),
            ),
            array(
                'id'        => 'is_phone_required',
                'type'      => 'switch',
                'title'     => esc_html__('Phone field required', 'tuturn'),
                'subtitle'  => esc_html__('Enable/disable phone field required', 'tuturn'),
                'desc'      => esc_html__('Enable mean phone field is required on registation form', 'tuturn'),
                'default'   => false,
            ),
            array(
                'id'        => 'defult_register_type',
                'type'      => 'select',
                'title'     => esc_html__('Default user registration', 'tuturn'),
                'desc'      => esc_html__('Please select new user type for defult registration', 'tuturn'),
                'options'   => array(
                    'tuturn-instructor' => esc_html__('instructor', 'tuturn'),
                    'tuturn-student' => esc_html__('Student', 'tuturn'),
                ),
                'default'   => 'tuturn-instructor',
            ),
            array(
                'id'       => 'verification_terms',
                'type'     => 'editor',
                'title'    => esc_html__('Verification term page link', 'tuturn'),
                'default'  => false,
                'desc'     => esc_html__('Add the verification text with link', 'tuturn')
            ),
            array(
                'id'       => 'upload_media_divider',
                'type'     => 'info',
                'title' => esc_html__('Upload media setting', 'tuturn'),
                'style' => 'info',
            ),
            array(
                'id'       => 'prof_mx_image_size',
                'type'     => 'text',
                'default'  => '5MB',
                'title'    => esc_html__('Profile photo Max upload file size(in MB)', 'tuturn')
            ),
            array(
                'id'       => 'prof_min_image_width',
                'type'     => 'text',
                'default'  => '400',
                'title'    => esc_html__('Profile photo min width(in px)', 'tuturn')
            ),
            array(
                'id'       => 'prof_min_image_height',
                'type'     => 'text',
                'default'  => '400',
                'title'    => esc_html__('Profile photo min height(in px)', 'tuturn')
            ),
            array(
                'id'       => 'prof_mx_image_width',
                'type'     => 'text',
                'default'  => '1000',
                'title'    => esc_html__('Profile photo max width(in px)', 'tuturn')
            ),
            array(
                'id'       => 'prof_mx_image_height',
                'type'     => 'text',
                'default'  => '1000',
                'title'    => esc_html__('Profile photo max height(in px)', 'tuturn')
            ),
            array(
                'id'       => 'upload_max_file_size',
                'type'     => 'text',
                'default'  => '5MB',
                'title'    => esc_html__('Upload Max file size(in MB)', 'tuturn')
            ),

            array(
                'id'       => 'media_gallery_image_max_width',
                'type'     => 'text',
                'default'  => '1200',
                'title'    => esc_html__('Maximum image width in media gallery (in px)', 'tuturn')
            ),
            array(
                'id'       => 'media_gallery_image_max_height',
                'type'     => 'text',
                'default'  => '1200',
                'title'    => esc_html__('Maximum image height in media gallery (in px)', 'tuturn')
            ),
            array(
                'id'       => 'media_gallery_items_limit',
                'type'     => 'text',
                'default'  => '5',
                'title'    => esc_html__('Maximum upload items in media gallery', 'tuturn')
            ),
            array(
                'id'       => 'list_record_divider',
                'type'     => 'info',
                'title' => esc_html__('show record items', 'tuturn'),
                'style' => 'info',
            ),
            array(
                'id'       => 'show_record_limit',
                'type'     => 'text',
                'default'  => '4',
                'title'    => esc_html__('Show record limit', 'tuturn')
            ),
            array(
                'id'       => 'show_record_team_member_limit',
                'type'     => 'text',
                'default'  => '8',
                'title'    => esc_html__('Show record limit for team members', 'tuturn')
            ),
            array(
                'id'       => 'ads_html',
                'type'     => 'textarea',
                'title'    => esc_html__('Ads HTML code', 'tuturn'),
                'desc'     => esc_html__('', 'tuturn'),
                'default'  => '',
            ),

            array(
                'id'        => 'default_file_extensions',
                'type'      => 'textarea',
                'title'     => esc_html__('File extensions', 'tuturn'),
                'default'   => esc_html__('pdf,doc,docx', 'tuturn'),
                'subtitle'  => esc_html__('Add file extension by comma seperated text', 'tuturn'),
            ),
            array(
                'id'        => 'default_image_extensions',
                'type'      => 'textarea',
                'title'     => esc_html__('Image extensions', 'tuturn'),
                'default'   => esc_html__('jpg,jpeg,png', 'tuturn'),
                'subtitle'  => esc_html__('Add file extension by comma seperated text', 'tuturn'),
            ),

            array(
                'id'       => 'instructor_hours_submission',
                'type'     => 'switch',
                'title'    => esc_html__('Volunteer hours log submission', 'tuturn'),
                'default'  => true,
                'desc'     => esc_html__('Tutors can create hours logs and send this to Students', 'tuturn')
            ),
            array(
                'id'        => 'log_search_type',
                'type'      => 'select',
                'title'     => esc_html__('Log student search', 'tuturn'),
                'options'   => array(
                    'booking'         => esc_html__('With booking', 'tuturn'),
                    'without_booking' => esc_html__('Without booking', 'tuturn')
                ),
                'default'   => 'without_booking',
                'required'  => array('instructor_hours_submission', 'equals', '1')
            ),
            array(
                'id'       => 'sub_categories_price',
                'type'     => 'switch',
                'title'    => esc_html__('Sub categories with prices', 'tuturn'),
                'default'  => false,
                'desc'     => esc_html__('Enable price for the sub categories', 'tuturn')
            ),

            array(
                'id'        => 'earing_page_hide',
                'type'      => 'select',
                'title'     => esc_html__('Show/Hide profile settings earning page', 'tuturn'),
                'desc'      => esc_html__('You can show/hide earning page for instructors', 'tuturn'),
                'options'   => array(
                    'show'       => esc_html__('Show', 'tuturn'),
                    'hide'        => esc_html__('Hide', 'tuturn'),
                ),
                'default'   => 'show',
            ),
            array(
                'id'        => 'invoice_page_hide',
                'type'      => 'select',
                'title'     => esc_html__('Show/Hide profile settings invoice page', 'tuturn'),
                'desc'      => esc_html__('You can show/hide invoice page for instructors/students', 'tuturn'),
                'options'   => array(
                    'show'       => esc_html__('Show', 'tuturn'),
                    'hide'        => esc_html__('Hide', 'tuturn'),
                ),
                'default'   => 'show',
            ),

            array(
                'id'        => 'shortname_option',
                'type'      => 'select',
                'title'     => esc_html__('Display short names', 'tuturn'),
                'desc'      => esc_html__('Display short names instead of showing full names', 'tuturn'),
                'options'   => array(
                    'yes'       => esc_html__('Show short names', 'tuturn'),
                    'no'        => esc_html__('Show full names', 'tuturn'),
                ),
                'default'   => 'no',
            ),
            array(
                'id'        => 'user_email_option',
                'type'      => 'switch',
                'title'     => esc_html__('Update user email', 'tuturn'),
                'subtitle'  => esc_html__('Allow the users to change the email, that will change the user login email', 'tuturn'),
                'default'   => false,
            ),
            array(
                'id'        => 'enable_cart_redirect',
                'type'      => 'switch',
                'title'     => esc_html__('Empty cart redirect', 'tuturn'),
                'subtitle'  => esc_html__('Enable redirect to tutor search page if cart is empty', 'tuturn'),
                'default'   => false,
            ),
            array(
                'id'        => 'enable_delete_account',
                'type'      => 'select',
                'title'     => esc_html__('Delete account', 'tuturn'),
                'options'   => array(
                    'yes'         => esc_html__('Yes, allow the users to delete account', 'tuturn'),
                    'no'          => esc_html__('No, Hide this from user menu', 'tuturn')
                ),
                'default'   => 'no',
                'desc'      => esc_html__('Enable to allow the users to delete their accounts', 'tuturrn'),
            ),
            array(
                'id'        => 'delete_account_reasons',
                'type'      => 'multi_text',
                'title'     => esc_html__('Delete account', 'tuturrn'),
                'default'   => array(
                    esc_html__('I don\'t want to use any more', 'tuturrn'),
                    esc_html__('Not fix as per my expectations', 'tuturrn'),
                    esc_html__('Others', 'tuturrn'),
                ),
                'desc'      => esc_html__('Add multiple delete account reasons', 'tuturrn'),
                'required'  => array('enable_delete_account', 'equals', 'yes')
            ),
            array(
                'id'       => 'hide_product_uncat',
                'type'     => 'select',
                'multi'    => true,
                'title'    => esc_html__('Hide uncategorized category', 'tuturn'),
                'desc'     => esc_html__('Hide uncategorized category from search dropdown', 'tuturn'),
                'options'  => array(
                    'uncategorized' => esc_html__('Uncategorized', 'tuturn'),
                ),
            ),
        )
    )
);
