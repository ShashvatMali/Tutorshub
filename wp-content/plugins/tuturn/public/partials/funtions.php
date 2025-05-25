<?php

/**
 * Provide a public-facing funtions
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/public/partials
 */

/**
 * @init            Site demo content
 * @package         Amentotech
 * @since           1.0
 * @desc            Display The Tab System URL
 */
if (!function_exists('tuturn_is_demo_site')) {
	function tuturn_is_demo_site(){
		$json   = array();
		if( isset( $_SERVER["SERVER_NAME"] ) && $_SERVER["SERVER_NAME"] === 'demos.wp-guppy.com' ){
            $json['type'] 	 	= 'error';
            $json['title']		= esc_html__('Restricted access!','tuturn');
            $json['message']	= esc_html__('You are not allowed to update the record on the demo site','tuturn');
            wp_send_json($json , 203);				
		}
	}
}


/**
 * @Validte if user is logged  and right privileges
 * @return {}
 */
if (!function_exists('tuturn_validate_privileges')) {
	function tuturn_validate_privileges($post_id='',$message=''){
		global $current_user;
		$json = array();
		$message	=  !empty($message) ? $message : esc_html__('You are not authorized to perform this action', 'tuturn');
		
		if (!is_user_logged_in()) {
            $message	=  esc_html__('You must logged in to save this listing', 'tuturn');
			$json['type'] 	 = 'error';
            $json['message'] = $message;
            wp_send_json( $json );
		}
		
		$post_data 		= get_post( $post_id );
     	$post_author	= !empty( $post_data->post_author ) ? intval($post_data->post_author) : 0;
		
		if(isset($post_author) && $post_author  !== $current_user->ID ){
			$json['type'] 	 = 'error';
            $json['message'] = $message;
            wp_send_json( $json );
		}
	}
}

/**
 * Custom image sizes
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
add_image_size('tu_profile_thumbnail', 100, 100, true);
add_image_size('tu_user_profile', 400, 400, true);
add_image_size('tu_category_dispaly', 480, 700, true);
add_image_size('tu_gallery_medium', 580, 452, true);
add_image_size('tu_blog_medium', 612, 400, true);
add_image_size('tu_gallery_large', 1812, 800, true);
add_image_size('tu_post_detail', 1296, 572, true);

/**
 * Email mask
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_maskEmailAddress')) {
    function tuturn_maskEmailAddress($email='')
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            list($first, $last) = explode('@', $email);
            $last   = explode('.', $last);
            $mask   = str_repeat('*', 5);
            $hideEmailAddress   = $first.'@<span>'.$mask.'</span>.'.$last['1'];
            $arr    = array( 'span' => array() );
            return wp_kses( $hideEmailAddress, $arr );
        }
        return $email;
    }
}

/**
 * Skype mask
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_maskSkypeAddress')) {
    function tuturn_maskSkypeAddress($skype='')
    {
        if($skype){
            $length = strlen($skype);
            $first  = substr($skype, 0, $length/2);
            $mask   = str_repeat('*', 8);
            $hideskype   = $first.'<span>'.$mask.'</span>';
            $arr    = array( 'span' => array() );
            return wp_kses( $hideskype, $arr );
        }
        return $skype;
    }
}

/**
 * Phone mask
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_maskPhone')) {
    function tuturn_maskPhone($phone){
        $matches    = array();
        $phone = preg_replace("/[^0-9]/", "",$phone);;
        preg_match('/(\\d{4})(\\d+)(\\d{4})/', $phone, $matches);
        if(!empty($matches))
        {
            $mask   = str_repeat('*', 3);     
            $phonenumber    = '';
            if(!empty($matches[1])){
                $phonenumber .= $matches[1];
            }
            
            if(!empty($matches[2])){
                $phonenumber .= ' - ';
                $phonenumber .= substr($matches[2],0,2);
            }

            if(!empty($matches[3])){
                $mask   = str_repeat('*', 3);
                $phonenumber .= '<span>'.str_repeat('*', 3).' - '.$mask.'</span>';
            }
            return $phonenumber;
        }
        return $phone;
    }
}

/**
 * Website URL mask
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_maskwebisteURL')) {
    function tuturn_maskwebisteURL($URL)
    {
        $strToLower         = strtolower(trim($URL));
        $httpPregReplace    = preg_replace('/^http:\/\//i', '', $strToLower);
        $httpsPregReplace   = preg_replace('/^https:\/\//i', '', $httpPregReplace);
        $wwwPregReplace     = preg_replace('/^www\./i', '', $httpsPregReplace);
        $explodeToArray     = explode('/', $wwwPregReplace);
        $finalDomainName    = trim($explodeToArray[0]);
        $re                 = '/(.+?)(\.[a-zA-Z]{2,11})/m';
        $result             = [];

        preg_match_all($re, $finalDomainName, $matches, PREG_SET_ORDER, 0);

        foreach($matches as $match){
            $mask       = str_repeat('*', strlen($match['1'])-1);
            $result[]   = '<span>'.$mask.'</span>'.$match[2];            
        }
        return implode(PHP_EOL,$result);        
    }
}

/**
 * Plugin pagination
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_paginate')) {
    function tuturn_paginate($tuturn_query = '', $class = '')
    {
        if ($tuturn_query) {
            $tuturn_total = $tuturn_query->max_num_pages;
        } else {
            global $wp_query;
            $tuturn_total = $wp_query->max_num_pages;
        }
        if ($tuturn_total > 1) {
            $tb_number = 999999999;
            if( !empty($class)){ ?>
                <div class="<?php echo esc_attr($class);?>">
            <?php } ?>
            <div class="tu-pagination">
                <?php
                echo paginate_links(array(
                    'base'         => str_replace($tb_number, '%#%', html_entity_decode(esc_url_raw(get_pagenum_link($tb_number,false)))),
                    'total'        => $tuturn_total,
                    'current'      => max(1, get_query_var('paged')),
                    'format'       => '?paged=%#%',
                    'show_all'     => false,
                    'type'         => 'list',
                    'end_size'     => 2,
                    'mid_size'     => 1,
                    'prev_next'    => true,
                    'prev_text'    => sprintf('<i class="icon icon-chevron-left"></i>'),
                    'next_text'    => sprintf('<i class="icon icon-chevron-right"></i>'),
                    'add_args'     => false,
                    'add_fragment' => '',
                ));
                ?>
            </div>
            <?php
            if( !empty($class)){ ?>
                </div>
                <?php
            }
        }
    }
}

/**
 * User verification check
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_get_username')) {
    function tuturn_get_username($profile_id = '')
    {
        global $tuturn_settings;
        $shortname_option  =  !empty($tuturn_settings['shortname_option']) ? $tuturn_settings['shortname_option'] : '';
        $title  = get_the_title($profile_id);

        if (!empty($shortname_option) && $shortname_option == 'yes') {
            $full_name      = explode(' ', $title);
            $first_name     = !empty($full_name[0]) ? ucfirst($full_name[0]) : '';
            $second_name    = !empty($full_name[1]) ? ' ' . strtoupper($full_name[1][0]) : '';
            return  esc_html($first_name . $second_name);
        } else {
            return  esc_html($title);
        }
    }
}

/**
 * Get user avatar
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_get_user_avatar' ) ) {
	function tuturn_get_user_avatar( $sizes = array(), $profile_id = '' ) {
		global	$tuturn_settings;
		extract( shortcode_atts( array(
			"width" => '100',
			"height" => '100',
		), $sizes ) );
        $user_identity  = tuturn_get_linked_profile_id( $profile_id, 'post' );
        $user_type      = apply_filters('tuturnGetUserType', $user_identity );
        $user_dp        = TUTURN_DIRECTORY_URI . 'public/images/default-avatar.jpg';
		$thumb_id       = get_post_thumbnail_id( $profile_id );

		if ( !empty( $thumb_id ) ) {
			$thumb_url = wp_get_attachment_image_src( $thumb_id, array( $width, $height ), true );

			if ( $thumb_url[1] == $width and $thumb_url[2] == $height ) {
				return !empty( $thumb_url[0] ) ? $thumb_url[0] : '';
			} else {
				$thumb_url = wp_get_attachment_image_src( $thumb_id, 'full', true );

				if (strpos($thumb_url[0],'media/default.png') !== false) {
					return $user_dp;
				} else {
					return !empty( $thumb_url[0] ) ? $thumb_url[0] : '';
				}
			}
		} else {
            $avatar = $user_dp;
            if($user_type == 'instructor'){
                if(!empty($tuturn_settings['defaul_instructor_profile']['id'])){
                    $placeholder_id	    = !empty($tuturn_settings['defaul_instructor_profile']['id']) ? $tuturn_settings['defaul_instructor_profile']['id'] : '';
                    $img_atts           = wp_get_attachment_image_src($placeholder_id, 'tu_user_profile');
                    $avatar             = !empty($img_atts['0']) ? $img_atts['0'] : '';
                } else {
                    $avatar = !empty($tuturn_settings['defaul_instructor_profile']['url']) ? $tuturn_settings['defaul_instructor_profile']['url'] : $user_dp;
                }                
            } elseif($user_type == 'student'){
                if(!empty($tuturn_settings['defaul_student_profile']['id'])){
                    $placeholder_id = !empty($tuturn_settings['defaul_student_profile']['id']) ? $tuturn_settings['defaul_student_profile']['id'] : '';
                    $img_atts       = wp_get_attachment_image_src($placeholder_id, 'tu_user_profile');
                    $avatar         = !empty($img_atts['0']) ? $img_atts['0'] : '';
                } else {
                    $avatar         = !empty($tuturn_settings['defaul_student_profile']['url']) ? $tuturn_settings['defaul_student_profile']['url'] : $user_dp;
                }
            }

            if( !empty($user_type) && $user_type === 'administrator'){
                $avatar = get_avatar_url($user_identity,array('size' => 100));
            }
            return $avatar;
		}
	}
}


/**
 * Verify token
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_verify_token')) {
    function tuturn_verify_token($tu_check_security)
    {
        $json   = array();
        if (!wp_verify_nonce($tu_check_security, 'ajax_nonce')) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'tuturn');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'tuturn');
            wp_send_json($json);
        }
    }
}

/**
 * Verify admin token
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_verify_admin_token')) {
    function tuturn_verify_admin_token($tu_check_security)
    {
        $json   = array();
        if (!wp_verify_nonce($tu_check_security, 'ajax_nonce')) {
            $json['type']               = 'error';
            $json['message']            = esc_html__('Restricted Access', 'tuturn');
            $json['message_desc']       = esc_html__('You are not allowed to perform this action.', 'tuturn');
            wp_send_json($json);
        }
    }
}

/**
 * Verify post author
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_verify_post_author')) {
    function tuturn_verify_post_author($post_id)
    {
        global $current_user;
        $post_author  = !empty($post_id) ? get_post_field( 'post_author', $post_id ) : 0;
        $post_author  = !empty($post_author) ? $post_author : 0;
        $json         = array();

        if (empty($post_author) || $post_author != $current_user->ID) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'tuturn');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'tuturn');
            wp_send_json($json);
        }
    }
}

/**
 * User authentication
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_authenticate_user_validation')) {
    function tuturn_authenticate_user_validation($user_id, $validate_user)
    {
        global $current_user;
        $json   = array();
        if (is_user_logged_in()) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'tuturn');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'tuturn');
            wp_send_json($json);
        }

        if (empty($validate_user) && $validate_user === 'both') {

            if ($user_id != $current_user->ID) {
                $json['type']           = 'error';
                $json['message']        = esc_html__('Restricted Access', 'tuturn');
                $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'tuturn');
                wp_send_json($json);
            }
        }
    }
}

/**
 * @Strong password validation
 * @return
 */
if (!function_exists('tuturn_strong_password_validation')) {
    add_action('tuturn_strong_password_validation', 'tuturn_strong_password_validation', 10, 1);
    function tuturn_strong_password_validation($password)
    {
        if (!empty($password)) {
            $number       = preg_match('@[0-9]@', $password);
            $uppercase     = preg_match('@[A-Z]@', $password);
            $lowercase     = preg_match('@[a-z]@', $password);
            $specialChars   = preg_match('@[^\w]@', $password);

            if (strlen($password) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
                $json               = array();
                $json['type']       = 'error';
                $json['message']    = esc_html__("Password must be at least 8 characters in length and must contain at-least one number, one upper case letter, one lower case letter and one special character.", 'tuturn');
                wp_send_json($json);
            }

        }
    }
}

/**
 * price format
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_price_format')) {
    function tuturn_price_format($price = '', $type = 'echo')
    {
        if (class_exists('WooCommerce')) {
            $price  = wc_price($price, array('decimals' => 2));
        } else {
            $currency   = tuturn_get_current_currency();
            $price      = !empty($currency['symbol']) ? $currency['symbol'] . $price : '$';
        }

        if ($type === 'return') {
            return wp_strip_all_tags($price);
        } else {
            echo wp_strip_all_tags($price);
        }
    }
    add_action('tuturn_price_format', 'tuturn_price_format', 10, 2);
    add_filter('tuturn_price_format', 'tuturn_price_format', 10, 2);
}

/**
 * Get woocommmerce currency settings
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_get_current_currency')) {
    function tuturn_get_current_currency()
    {
        $currency  = array();
        if (class_exists('WooCommerce')) {
            $currency['code']   = get_woocommerce_currency();
            $currency['symbol'] = get_woocommerce_currency_symbol();
        } else {
            $currency['code']   = 'USD';
            $currency['symbol'] = '$';
        }
        return $currency;
    }
}

/**
 * Canonical
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_canonical')) {
    add_filter('redirect_canonical', 'tuturn_canonical');
    function tuturn_canonical($redirect_url)
    {
        if (is_paged() && (is_singular('tuturn-instructor') || is_singular('tuturn-student'))) {
            $redirect_url = false;
        }
        return $redirect_url;
    }
}

/**
 * Get template
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */

if (!function_exists('tuturn_get_template')) {
    function tuturn_get_template($template_name, $args = array(), $template_path = 'tuturn', $default_path = '')
    {
        if (!empty($args) && is_array($args)) {
            extract($args);
        }
        $located = tuturn_locate_template($template_name, $template_path, $default_path);

        if (!empty($return) && $return === true) {
            return $located;
        } else {
            include($located);
        }
    }
}


/**
 * Plugin template part
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_get_template_part')) {
    function tuturn_get_template_part($slug, $name = '', $args = '', $template_path = 'tuturn', $default_path = '')
    {
        $template = '';
        if ($name) {
            $template = tuturn_locate_template("{$slug}-{$name}.php", $template_path, $default_path);
        }

        if (!$template) {
            $template = tuturn_locate_template("{$slug}.php", $template_path, $default_path);
        }

        $template = apply_filters('tuturn_get_template_part', $template, $slug, $name, $args);
        if ($template) {
            load_template($template, FALSE, $args);
        }
    }
}

/**
 * Locate template
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_locate_template')) {
    function tuturn_locate_template($template_name, $template_path = 'tuturn', $default_path = '')
    {
        $template   = locate_template(
            array(
                trailingslashit($template_path) . $template_name,
            )
        );

        if (!$template && $default_path !== false) {
            $default_path = $default_path ? $default_path : untrailingslashit(plugin_dir_path(dirname(__DIR__))) . '/templates/';

            if (file_exists(trailingslashit($default_path) . $template_name)) {
                $template = trailingslashit($default_path) . $template_name;
            }
        }
        return apply_filters('tuturn_locate_template', $template, $template_name, $template_path);
    }
}

/**
 * Get user type
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_get_linked_profile_id')) {
    add_filter('tuturn_get_linked_profile_id','tuturn_get_linked_profile_id',10,3);
    function tuturn_get_linked_profile_id($id, $type='users',$role='') {
        if( $type === 'post') {
            $linked_profile = get_post_meta($id, '_linked_profile', true);
        } else {
            if(empty($role)){
                $role   = get_user_meta($id,'_user_type',true);
            }

            $linked_profile = get_user_meta($id, '_linked_profile', true);
        }

        $linked_profile = !empty( $linked_profile ) ? $linked_profile : '';
        return intval( $linked_profile );
    }
}

/**
 * Get Country List array
 * @author Amentotech <theamentotech@gmail.com>
 * @return crop sizes
 */
if ( ! function_exists( 'tuturn_country_array' ) ) {
	function tuturn_country_array($countery='',$return_type=''){
        $countries_arr  = array();
        if($_POST){
            $countery       = !empty($_POST['country']) ? ($_POST['country']) : '';
            $return_type    = !empty($_POST['type']) ? ($_POST['type']) : '';
        }
        if ( class_exists( 'WooCommerce' ) ) {
            global $woocommerce;
            $countries_obj  = new WC_Countries();
            $countries      = $countries_obj->get_allowed_countries('countries');
            
            if( !empty($countery) ){
                $countries  = $countries_obj->get_states(strtoupper($countery));
            }
            if( !empty($countries) ){
                foreach($countries as $key=>$val){
                    $countries_arr[$key] = $val;
                }
            }
        }
        if( !empty($return_type) && $return_type ==='ajax' ){
            $json['type']       = 'success';
            $json['countries']  = $countries_arr;
            wp_send_json($json);
        } else {
            return $countries_arr;
        }
    }
    add_action('wp_ajax_tuturn_country_array', 'tuturn_country_array');
    add_action('wp_ajax_nopriv_tuturn_country_array', 'tuturn_country_array');
}

/**
* Get template page uri
*
* @throws error
* @author Amentotech <theamentotech@gmail.com>
* @return
*/
if ( ! function_exists( 'tuturn_get_page_uri' ) ) {
    function tuturn_get_page_uri( $type = '' ) {
        global $tuturn_settings;
        $tpl_page		= !empty($tuturn_settings['tpl_'.$type]) ? $tuturn_settings['tpl_'.$type] : '';
        $search_page 	= !empty($tpl_page) ? get_permalink((int) $tpl_page) : '';
        return $search_page;
    }
}

/**
* Get dashbod page uri
*
* @throws error
* @author Amentotech <theamentotech@gmail.com>
* @return
*/
if ( ! function_exists( 'tuturn_dashboard_page_uri' ) ) {
    function tuturn_dashboard_page_uri( $user_id = '',$key='personal_details' ) {
        global $current_user;
        $redirect   = Tuturn_Profile_Menu::tuturn_profile_menu_link($key, $user_id, true, '');
        $redirect   = !empty($redirect) ? ($redirect) : home_url('/');
        return $redirect;
    }
}
/**
 * @Strong password validation
 * @return
 */
if (!function_exists('tuturn_strong_password_validation')) {
    add_action('tuturn_strong_password_validation', 'tuturn_strong_password_validation', 10, 1);
    function tuturn_strong_password_validation($password)
    {
        if (!empty($password)) {
            $number         = preg_match('@[0-9]@', $password);
            $uppercase      = preg_match('@[A-Z]@', $password);
            $lowercase      = preg_match('@[a-z]@', $password);
            $specialChars   = preg_match('@[^\w]@', $password);

            if (strlen($password) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
                $json               = array();
                $json['type']       = 'error';
                $json['message']    = esc_html__("Password must be at least 8 characters in length and must contain at-least one number, one upper case letter, one lower case letter and one special character.", 'tuturn');
                wp_send_json($json);
            }

        }

    }
}
    
/**
* Get user registeration
*
* @throws error
* @author Amentotech <theamentotech@gmail.com>
* @return
*/
if(!function_exists('tuturnRegistration')){
    add_action('tuturnRegistration', 'tuturnRegistration', 10, 1);
    function tuturnRegistration($output=array(), $option_type=''){
        global $tuturn_settings;
        $json               = array();
        $user_name_option   = !empty($tuturn_settings['user_name_option']) ? $tuturn_settings['user_name_option'] : false;
        //Validation
        $validations = array(
            'first_name'              => esc_html__('First name is required', 'tuturn'),
            'last_name'               => esc_html__('Last name is required', 'tuturn'),
            'user_email'              => esc_html__('Email is required', 'tuturn'),
            'user_password'           => esc_html__('Password is required', 'tuturn'),
            'user_agree_terms'        => esc_html__('You should agree to terms and conditions.', 'tuturn'),
        );

        if( !empty($user_name_option) ){
            $validations['username']   = esc_html__('User name is required', 'tuturn');
        }

        foreach ($validations as $key => $value) {

            if (empty($output['user_registration'][$key])) {
                $json['type']         = 'error';
                $json['message_desc']  = $value;
                if( !empty($option_type) && $option_type === 'mobile' ){
                    $json['message']   = $json['message_desc'];
                    return $json;
                } else {
                    wp_send_json($json);
                }
            }

            //Validate email address
            if ($key === 'user_email') {
                if (!is_email($output['user_registration']['user_email'])) {
                    $json['type']           = 'error';
                    $json['message_desc']   = esc_html__('Please add a valid email address.', 'tuturn');
                if( !empty($option_type) && $option_type === 'mobile' ){
                        $json['message']   = $json['message_desc'];
                        return $json;
                    } else {
                        wp_send_json($json);
                    }
                }

                $user_exists = email_exists($output['user_registration']['user_email']);
                if ($user_exists) {
                    $json['type']           = 'error';
                    $json['message_desc']   = esc_html__('This email already registered', 'tuturn');
                if( !empty($option_type) && $option_type === 'mobile' ){
                        $json['message']   = $json['message_desc'];
                        return $json;
                    } else {
                        wp_send_json($json);
                    }
                }
            } else if( !empty($user_name_option) && $key === 'user_name') {

            }

            //Password
            if ($key === 'user_password') {
                if (strlen($output['user_registration'][$key]) < 6) {
                    $json['type']           = 'error';
                    $json['message_desc']   = esc_html__('Password length should be minimum 6', 'tuturn');
                    if( !empty($option_type) && $option_type === 'mobile' ){
                        $json['message']   = $json['message_desc'];
                        return $json;
                    } else {
                        wp_send_json($json);
                    }
                }
            }
        }


        //Get user data from session
        $first_name         = !empty($output['user_registration']['first_name']) ? sanitize_text_field($output['user_registration']['first_name']) : '';
        $last_name          = !empty($output['user_registration']['last_name']) ? sanitize_text_field($output['user_registration']['last_name']) : '';
        $email              = !empty($output['user_registration']['user_email']) ? is_email($output['user_registration']['user_email']) : '';
        $password           = !empty($output['user_registration']['user_password']) ? ($output['user_registration']['user_password']) : '';
        $user_type          = !empty($output['user_registration']['user_type']) ? sanitize_text_field($output['user_registration']['user_type']) : 'tuturn-student';
        $user_agree_terms   = !empty($output['user_registration']['user_agree_terms']) ? esc_html($output['user_registration']['user_agree_terms']) : '';
        $user_name          = !empty($output['user_registration']['username']) ? sanitize_text_field($output['user_registration']['username']) : '';

        //Session data validation
        if (empty($first_name)
        || empty($last_name)
        || empty($email)
        || empty($user_type)
        ) {
            $json['type']           = 'error';
            $json['message_desc']    = esc_html__('All the fields are required added in first step', 'tuturn');
            if( !empty($option_type) && $option_type === 'mobile' ){
                $json['message']   = $json['message_desc'];
                return $json;
            } else {
                wp_send_json($json);
            }
        }

        $user_name  = !empty($user_name_option) ? $user_name : $email;
        //User Registration
        $random_password  = $password;
        $full_name        = $first_name . ' ' . $last_name;
        $user_nicename    = sanitize_title($full_name);

        $userdata = array(
            'user_login'    => $user_name,
            'user_pass'     => $random_password,
            'user_email'    => $email,
            'user_nicename' => $user_nicename,
            'display_name'  => $full_name,
        );

        $user_identity = wp_insert_user($userdata);

        if (is_wp_error($user_identity)) {
            $json['type']           = "error";
            $json['message_desc']   = esc_html__("User already exists. Please try another one.", 'tuturn');
            if( !empty($option_type) && $option_type === 'mobile' ){
                $json['message']   = $json['message_desc'];
                return $json;
            } else {
                wp_send_json($json);
            }
        } else {
            global $wpdb;
            wp_update_user(array('ID' => esc_sql($user_identity), 'role' => 'subscriber', 'user_status' => 0));

            $wpdb->update(
                $wpdb->prefix . 'users', array('user_status' => 0), array('ID' => esc_sql($user_identity))
            );

            update_user_meta($user_identity, 'first_name', $first_name);
            update_user_meta($user_identity, 'last_name', $last_name);
            update_user_meta($user_identity, 'termsconditions', esc_html($user_agree_terms));
            update_user_meta($user_identity, 'show_admin_bar_front', false);
            update_user_meta($user_identity, '_is_verified', 'no');

            $verify_link            = '';
            $verify_new_user        = !empty($tuturn_settings['email_user_registration']) ? $tuturn_settings['email_user_registration'] : 'verify_by_link';
            $identity_verification	= !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : false;
            
            if (!empty($verify_new_user) && $verify_new_user == 'verify_by_link') {
                //verification link
                $key_hash     = md5(uniqid(openssl_random_pseudo_bytes(32)));
                update_user_meta($user_identity, 'confirmation_key', $key_hash);
                $protocol     = is_ssl() ? 'https' : 'http';
                $verify_link  = esc_url(add_query_arg(array('key' => $key_hash . '&verifyemail=' . $email), home_url('/', $protocol)));
            }

            //Create Post
            $user_post = array(
                'post_title'    => wp_strip_all_tags($full_name),
                'post_status'   => 'publish',
                'post_author'   => $user_identity,
                'post_type'     => apply_filters('tuturn_profiles_post_type_name', $user_type),
            );

            $post_id = wp_insert_post($user_post);

            if (!is_wp_error($post_id)) {
                $dir_latitude     = !empty($tuturn_settings['dir_latitude']) ? $tuturn_settings['dir_latitude'] : 0.0;
                $dir_longitude    = !empty($tuturn_settings['dir_longitude']) ? $tuturn_settings['dir_longitude'] : 0.0;
                $verify_user      = !empty($tuturn_settings['user_account_approve']) ? $tuturn_settings['user_account_approve'] : '';

                //add extra fields as a null
                update_post_meta($post_id, '_address', '');
                update_post_meta($post_id, '_latitude', $dir_latitude);
                update_post_meta($post_id, '_longitude', $dir_longitude);
                update_post_meta($post_id, 'hourly_rate', 0);
                update_post_meta($post_id, '_linked_profile', $user_identity);
                update_post_meta($post_id, '_is_verified', 'no');
                update_post_meta($post_id, 'zipcode', '');
                update_post_meta($post_id, 'country', '');
                update_user_meta($user_identity, '_notification_email', $email);
                
                if (!empty($user_type) && $user_type === 'tuturn-student') {
                    update_user_meta($user_identity, '_linked_profile_student', $post_id);
                    update_user_meta($user_identity, '_user_type', 'student');
                    $notifyData['user_type']		= 'student';
                } else if (!empty($user_type) && $user_type === 'tuturn-instructor') {
                    update_post_meta($post_id, 'hourly_rate', '');
                    update_user_meta($user_identity, '_linked_profile', $post_id);
                    update_user_meta($user_identity, '_user_type', 'instructor');
                    $notifyData['user_type']		= 'instructor';
                }

                
                $identity_verification          = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : '';
                update_user_meta($user_identity, 'identity_verified', 1);
                update_post_meta($post_id,'identity_verified','yes');

                /* for student */
                if(!empty($user_type) && $user_type === 'tuturn-student' && ($identity_verification === 'students' || $identity_verification === 'both')){
                    update_user_meta($user_identity, 'identity_verified', 0);
                    update_post_meta($post_id,'identity_verified','no');
                }

                /* for instructor */
                if(!empty($user_type) && $user_type === 'tuturn-instructor' && ($identity_verification === 'tutors' || $identity_verification === 'both')){
                    update_user_meta($user_identity, 'identity_verified', 0);
                    update_post_meta($post_id,'identity_verified','no');
                }
            } else {
                $json['type']           = 'error';
                $json['message_desc']   = esc_html__('Some error occurs, please try again later', 'tuturn');
                if( !empty($option_type) && $option_type === 'mobile' ){
                    $json['message']   = $json['message_desc'];
                    return $json;
                } else {
                    wp_send_json($json);
                }
            }

            $login_url    = !empty( $tuturn_settings['tpl_login'] ) ? get_permalink($tuturn_settings['tpl_login']) : wp_login_url();

            //Send email to users & admin
            if (class_exists('Tuturn_Email_helper')) {
                $blogname                       = get_option('blogname');
                $emailData                      = array();
                $emailData['name']              = $full_name;
                $emailData['password']          = $random_password;
                $emailData['email']             = $email;
                $emailData['verification_link'] = $verify_link;
                $emailData['site']              = $blogname;
                $emailData['login_url']         = $login_url;

                //Welcome Email
                if (class_exists('TuturnRegistrationEmail')) {
                    $email_helper = new TuturnRegistrationEmail();

                    if (!empty($verify_new_user) && $verify_new_user == 'verify_by_link') {
                        $email_helper->registration_user_email($emailData);
                    }elseif(!empty($verify_new_user) && $verify_new_user == 'verify_by_admin'){
                        // to user
                        $email_helper->registration_account_approval_request($emailData);
                        // to admin
                        $email_helper->registration_verify_by_admin_email($emailData);
                    } else{
                        $email_helper->new_user_register_admin_email($emailData);
                    }
                }
            }
            //User Login
            $user_array                   = array();
            $user_array['user_login']     = $email;
            $user_array['user_password']  = $random_password;
            $status = wp_signon($user_array, false);

            if (empty($verify_user)) {
                $json_message = esc_html__("Your account have been created. Please wait while your account is verified by the admin.", 'tuturn');
            } else {
                $json_message = esc_html__("Your account have been created. Please verify your account, an email have been sent your email address.", 'tuturn');
            }

            $dashboard            = tuturn_dashboard_page_uri($user_identity);
            $json['type']         = 'success';
            $json['message']      = $json_message;
            $json['retrun_url']   = wp_specialchars_decode($dashboard);
            if( !empty($option_type) && $option_type === 'mobile' ){
                $json['message']    = $json_message;
                $json['user_id']    = !empty($user_identity) ? intval($user_identity) : 0;
                return $json;
            } else {
                wp_send_json($json);
            }
        }
    }
}

/**
* Get user role
*
* @throws error
* @author Amentotech <theamentotech@gmail.com>
* @return
*/
if (!function_exists('tuturn_get_profile_type')) {
    function tuturn_get_profile_type($profile_id) {
        if (!empty($profile_id)) {
            $user_type = get_post_type($profile_id);
            if (!empty($user_type) && $user_type === 'tuturn-instructor') {
                return 'instructor';
            } else {
                return 'student';
            }
        }
        return 'student';
    }
    add_filter('tuturn_get_profile_type', 'tuturn_get_profile_type', 10);
}


/**
 * Upload temp files to WordPress media
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_temp_upload_to_media')) {
    function tuturn_temp_upload_to_media($file_url, $post_id, $encrypt_file=true) {
		global $wp_filesystem, $current_user;
		if (empty($wp_filesystem)) {
			require_once (ABSPATH . '/wp-admin/includes/file.php');
			WP_Filesystem();
		}

        if (!is_user_logged_in()) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'tuturn');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'tuturn');
            wp_send_json($json);
        }

        $json       =  array();
        $upload_dir = wp_upload_dir();
		$folderRalativePath = $upload_dir['baseurl']."/tuturn-temp";
		$folderAbsolutePath = $upload_dir['basedir']."/tuturn-temp";

		$args = array(
			'timeout'	=> 15,
			'headers'	=> array('Accept-Encoding' => ''),
			'sslverify'	=> false
		);

		$response   	= wp_remote_get( $file_url, $args );
		$file_data		= wp_remote_retrieve_body($response);

		if(empty($file_data)){
			$json['attachment_id']  = '';
			$json['url']            = '';
			$json['name']			= '';
			return $json;
		}

		$filename 			= basename($file_url);
		$temp_filename 		= $filename;

        if (wp_mkdir_p($upload_dir['path'])){
			$file = $upload_dir['path'] . '/' . $filename;
		}  else {
            $file = $upload_dir['basedir'] . '/' . $filename;
		}

		$file_detail  		= Tuturn_file_permission::getEncryptFile($file, $post_id, true, $encrypt_file);
		$new_filename		= $file_detail['name'];
		$new_path 			= $upload_dir['path'] . '/' . $new_filename;
		$file				= $new_path;
		$filename 			= basename($file);
		$actual_filename 	= pathinfo($file, PATHINFO_FILENAME);

		//put content to the file
		file_put_contents($file, $file_data);
        $wp_filetype = wp_check_filetype($filename, null);

		$attachment = array(
            'post_mime_type' 	=> $wp_filetype['type'],
            'post_title' 		=> sanitize_file_name($filename),
            'post_content' 		=> '',
            'post_status' 		=> 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $file, $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
		wp_update_attachment_metadata($attach_id, $attach_data);
		$post_type = get_post_type($post_id);

        if( ($post_type == 'product' || $post_type == 'user-verification') && !empty($encrypt_file)) {
		    update_post_meta($attach_id,'is_encrypted','1');
            add_filter( 'intermediate_image_sizes_advanced', 'tuturn_remove_crop_sizes', 999 );
        }
        
        $json['attachment_id']  = $attach_id;
        $json['url']            = $upload_dir['url'] . '/' . basename( $filename );
		$json['name']			= $filename;
		$target_path 			= $folderAbsolutePath . "/" . $temp_filename;
        
        unlink($target_path); //delete file after upload
        return $json;
    }
}

/**
 * Hide attachemnts and files on media
 * @author Amentotech <theamentotech@gmail.com>
 * @return crop sizes
 */
if ( ! function_exists( 'tuturn_hide_media_encrypted_files' ) ) {
    add_filter( 'ajax_query_attachments_args', 'tuturn_hide_media_encrypted_files' );
    function tuturn_hide_media_encrypted_files( $args ) {
        if ( ! is_admin() ) {
            return $args;
        }
        $args['meta_query'] = [
            [
                'key'     => 'is_encrypted',
                'compare' => 'NOT EXISTS',
            ]
        ];
    
        return $args;
    }
}

/**
 * Remove images crop sizes
 * @author Amentotech <theamentotech@gmail.com>
 * @return crop sizes
 */
if ( ! function_exists( 'tuturn_remove_crop_sizes' ) ) {
	function tuturn_remove_crop_sizes($sizes = array()){
		$sizes = array();
		return $sizes;	
	}
}
/**
 * Price plans packages
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_price_plans_duration')) {
    function tuturn_price_plans_duration($package_type)
    {
        $label  = '';
        switch($package_type){
            case 'year':
                $label  = esc_html__('Year', 'tuturn');
                break;
            case 'month':
                $label  = esc_html__('Month', 'tuturn');
                break;
            case 'days':
                $label  = esc_html__('Day', 'tuturn');
                break;
            default:
                $label  = $package_type;
        }    
        return $label;  
    }
}

/**
 * Remove protocol from URL
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if(!function_exists('tuturn_removeProtocol')){
    function tuturn_removeProtocol($url){
        $remove = array("http://","https://");
        return str_replace($remove,"",$url);
    }
}

/**
 * Get user role
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_get_user_type')) {

    function tuturn_get_user_type($user_identity) {

        if (!empty($user_identity)) {

            $user_type = get_user_meta($user_identity,'_user_type',true);
			
            if (!empty($user_type) && $user_type === 'instructor') {
                return 'instructor';
            } elseif (!empty($user_type) && $user_type === 'student') {
               return 'student';
            } elseif (empty($user_type)) {

				$data = get_userdata( $user_identity );
				if ( !empty( $data->roles[0] ) && $data->roles[0] == 'administrator') {
					return 'administrator';
				}
			}
        }

        return 'administrator';
    }

    add_filter('tuturn_get_user_type', 'tuturn_get_user_type', 10);
}

/**
 * Get mailchimp list
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_mailchimp_list')) {

    function tuturn_mailchimp_list() {
		global $tuturn_settings;
        $mailchimp_list 	= array();
        $mailchimp_list	    = array();
		$mailchimp_option 	= !empty( $tuturn_settings['mailchimp_key'] ) ? $tuturn_settings['mailchimp_key'] : '';

        if (!empty($mailchimp_option)) {
            if (class_exists('Tuturn_MailChimp')) {
                $mailchim_obj = new Tuturn_MailChimp();
                $lists = $mailchim_obj->tuturn_mailchimp_list($mailchimp_option);
                if (is_array($lists) && isset($lists['data'])) {
                    foreach ($lists['data'] as $list) {
                        if (!empty($list['name'])) :
                            $mailchimp_list[$list['id']] = $list['name'];
                        endif;
                    }
                }
            }
        }
        return $mailchimp_list;
    }

}

if(!function_exists('tuturn_bookeAppointment_sidebar')){
    function tuturn_bookeAppointment_sidebar($extra_data=array()){
        $extra_info         = array();
        $img_typing 		= TUTURN_GlobalSettings::get_plugin_url().'public/images/typing-white.gif';
        $img_tick 			= TUTURN_GlobalSettings::get_plugin_url().'public/images/tick.svg';
        $img_cross 			= TUTURN_GlobalSettings::get_plugin_url().'public/images/cross.svg';
        $service_title      = !empty($extra_data['titles']) ? $extra_data['titles'] : array();
        $slot_date          = !empty($extra_data['date']) ? $extra_data['date'] : '';
        $slots              = !empty($extra_data['slot']) ? $extra_data['slot'] : array();
        $step               = !empty($extra_data['step']) ? $extra_data['step'] : 1;
        $slot_time  = '';

        if(!empty($slots)){
            $time_format 			= get_option('time_format');
            $date_format 			= get_option('date_format');
            $slot_key_val 	= explode('-', $slots);
            $first_time 	= date($time_format, strtotime('2016-01-01' . $slot_key_val[0]));
            $second_time 	= date($time_format, strtotime('2016-01-01' . $slot_key_val[1]));
            $slot_time      = $slot_date . ' @ ' . $first_time. ' - ' . $second_time;
            $process1_class  = '';
            $process2_class  = '';
            $process3_class  = '';
            $process4_class  = '';
        }

        switch($step){
            case 2:
                $img1 = $img_tick;
                $img2 = $img_typing;
                $img3 = $img_cross;
                $img4 = $img_cross;
                $process1_class  = 'tu-donetab';
                $process2_class  = 'tu-currenttab';
                $process3_class  = 'tu-yettodone';
                $process4_class  = 'tu-yettodone';
                break;
            case 3:
                $img1 = $img_tick;
                $img2 = $img_tick;
                $img3 = $img_typing;
                $img4 = $img_cross;
                $process1_class  = 'tu-donetab';
                $process2_class  = 'tu-donetab';
                $process3_class  = 'tu-currenttab';
                $process4_class  = 'tu-yettodone';
                break;
            case 4:
                $img1 = $img_tick;
                $img2 = $img_tick;
                $img3 = $img_tick;
                $img4 = $img_typing;
                $process1_class  = 'tu-donetab';
                $process2_class  = 'tu-donetab';
                $process3_class  = 'tu-donetab';
                $process4_class  = 'tu-currenttab';
                break;
            default:
                $img1 = $img_typing;
                $img2 = $img_cross;
                $img3 = $img_cross;
                $img4 = $img_cross;
                $process1_class  = 'tu-currenttab';
                $process2_class  = 'tu-yettodone';
                $process3_class  = 'tu-yettodone';
                $process4_class  = 'tu-yettodone';
        }

        $extra_info[0] = array(
            'process_class'=> $process1_class,
            'icon'      => $img1,
            'title'     => esc_html__('Services', 'tuturn'),
            'extras'    => $service_title,
        );

        $extra_info[1] = array(
            'process_class'=> $process2_class,
            'icon'      => $img2,
            'title'     => esc_html__('Services date and time', 'tuturn'),
            'extras'    => array($slot_time),
        );

        $extra_info[2] = array(
            'process_class'=> $process3_class,
            'icon'      => $img3,
            'title'     => esc_html__('Personal information', 'tuturn'),
            'extras'    => array(),
        );

        $extra_info[3] = array(
            'process_class'=> $process4_class,
            'icon'          => $img4,
            'title'         => esc_html__('Summary', 'tuturn'),
            'extras'        => array(),
        );

        return $extra_info;
    }
}

/**
 * Account details
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_account_details')) {
    function tuturn_account_details($user_id,$type,$status)
    {
        $arg  = array(
            array(
                'key'       => 'instructor_id',
                'value'     => $user_id,
                'compare'   => '=',
                'type'      => 'NUMERIC'
            ),
            array(
                'key'       => 'booking_status',
                'value'     => $status,
                'compare'   => 'IN'
            )
        );
        $posts    = tuturn_get_post_count_by_meta('shop_order',$type, $arg,'array');
        $total_amount = 0;
        
        if( !empty($posts) ){
            foreach($posts as $post ){
                $post_id      = $post->ID;
                $seller_share = get_post_meta( $post_id, 'instructor_shares',true );
                $seller_share = !empty($seller_share) ? $seller_share : 0;
                $total_amount = $total_amount+ $seller_share;
            }
        }
        return $total_amount;
    }
}

/**
* Process Order Refund
* @return WC_Order_Refund|WP_Error
*/
if (!function_exists('tuturn_wc_refund_order')) {
	function tuturn_wc_refund_order( $order_id = '', $refund_reason = '' ) { 
		$order  = wc_get_order( $order_id );
		$order_items   = $order->get_items();
		// Refund Amount
		$refund_amount = 0;

		// Prepare line items which we are refunding
		$line_items = array();
		$previous_refunded	= $order->get_total_refunded();
		if ( $order_items ) {
			foreach ( $order_items as $item_id => $item ) {	
				$tax_data = $item->get_subtotal_tax();	
				$refund_tax = 0;
				if( $tax_data ) {
					$refund_tax = array_map( 'wc_format_decimal', $tax_data );
				}
				$refund_amount = wc_format_decimal( $refund_amount ) + wc_format_decimal( $item->get_total() );
				$line_items[ $item_id ]	= array( 
					'qty' => $item->get_quantity(), 
					'refund_total' => wc_format_decimal( $item->get_total() ), 
					'refund_tax' =>  $refund_tax 
				);		
			}

			if(!empty($previous_refunded) && $refund_amount >= $previous_refunded){
				$refund_amount	= $refund_amount - $previous_refunded;
			}

			$refund = wc_create_refund( array(
				'amount'         => $refund_amount,
				'reason'         => $refund_reason,
				'order_id'       => $order_id,
				'line_items'     => $line_items,
				'refund_payment' => true
			));	
			return $refund;
		}
	}
}

/**
 * @tuturn Unique Increment
 * @return {}
 */
if (!function_exists('tuturn_unique_increment')) {
    function tuturn_unique_increment($length = 5)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}
/**
 * Account details including withdrawal request(s)
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_account_withdraw_details')) {
    function tuturn_account_withdraw_details($user_id = '', $status=array('publish','pending'))
    {
        $total_withdraw_amount = 0;
        $args = array(
            'posts_per_page'    => "-1",
            'author'            =>  $user_id,
            'post_type'         => 'withdraw',
            'order'             => 'DESC',
            'orderby'           => 'ID',
            'post_status'       => $status,
            'ignore_sticky_posts'   => 1
        );
        $withdrawal_posts = get_posts($args);

        if (!empty($withdrawal_posts)){
            foreach ($withdrawal_posts as $post_data):
                $withdraw_amount  = get_post_meta( $post_data->ID, '_withdraw_amount', true );
                $withdraw_amount = !empty($withdraw_amount) ? $withdraw_amount : 0;
                $total_withdraw_amount = $total_withdraw_amount + $withdraw_amount;
            endforeach;
        }

        return $total_withdraw_amount;
    }
}

/**
 * Get days in a month
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_days_in_month')) {
    function tuturn_days_in_month($month, $year)
    {
    // calculate number of days in a month
    return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }
}

/**
 * Count custom earning array
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */

if (!function_exists('tuturn_instructor_earnings')) {
    function tuturn_instructor_earnings($post_type = '', $status='any',$meta_array=array(), $selected_date='')
    {
        if(!empty($selected_date)){
            $previous_1_month   = date('F 01, Y', strtotime($selected_date));
        } else {
            $previous_1_month   = date('F 01, Y');
        }

        $year  = date('Y', strtotime($previous_1_month));
        $month  = date('m', strtotime($previous_1_month));
        
        if(function_exists('cal_days_in_month')){
            $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        } else {
            $number = tuturn_days_in_month($month, $year);
        }
        $previous_2_month   = date('F d, Y');
        $previous_2_month   = date('F d, Y', strtotime($previous_1_month. ' + '.($number-1).' days'));;
        $end_day    = date('d');
        $day_keys   = '';
        $day_values = array();

        for($i=1;$i<=$end_day;$i++){
            $day_keys       = !empty($day_keys) ? $day_keys.','.$i : $i;
            $day_values[$i]	= 0;
        }

		$args = array(
			'post_type'         => $post_type,
			'posts_per_page'    => -1,
			'post_status'       => $status,
			'date_query' => array(
				array(
					'after'     => $previous_1_month,
					'before'    => $previous_2_month,
					'inclusive' => true,
				),
			),
		);

		if (!empty($meta_array)) {
			foreach ($meta_array as $meta) {
				$args['meta_query'][]  = $meta;
			}
		}

		$day_amount     = 0;
		$tuturn_posts = get_posts( $args );

		if( !empty($tuturn_posts) ){
			foreach($tuturn_posts as $post ){
				$date_completed = get_post_meta( $post->ID, '_date_completed', true );

				$date_completed = !empty($date_completed) ? intval($date_completed) : 0;
				$date_val       = !empty($date_completed) ? date('j',$date_completed) : 0;

				if( !empty($date_val) ){

                    if(!empty($day_values[$date_val])){
					    $day_amount		= $day_values[$date_val];
                    }
					$seller_shares 	= get_post_meta( $post->ID, 'instructor_shares', true );
					$seller_shares 	= !empty($seller_shares) ? ($seller_shares) : 0;
					$day_amount		= $day_amount+$seller_shares;
                    
                    if(!empty($day_values[$date_val])){
                        $day_amount = $day_amount+$day_values[$date_val];
                    }

					$day_values[$date_val]	= $day_amount;
 
				}

			}
		}

		$day_values = implode(",", $day_values);
       
		return array(
			'key'		=> $day_keys,
			'values'	=> $day_values
		);

    }
}

/**
 * gender List
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */

if (!function_exists('tuturn_get_gender_lists')) {
	function tuturn_get_gender_lists()
	{
	  	$lists = array(
			'male'      => esc_html__('Male','tuturn' ),
            'female'    => esc_html__('Female','tuturn' ),
	    );

	  $lists = apply_filters('tuturn_filter_gender_lists', $lists);
	  return $lists;
	}
}

/**
 * List social media optuions
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */

if (!function_exists('tuturn_social_media_lists')) {
	function tuturn_social_media_lists()
	{
        $lists          = array(
            'facebook'  => array(
                'title' => esc_html__('Enter facebook URL','tuturn'),
                'name'  => esc_html__('Facebook','tuturn'),
                'icon'  => 'fa-brands fa-facebook',
                'icon_class'    => 'fab fa-facebook',
                'bg_class'      => 'tu-bg-blue'
            ),
            'twitter'   =>  array(
                'name'  => esc_html__('Twitter','tuturn'),
                'title' => esc_html__('Enter twitter URL','tuturn'),
                'icon'  => 'fa-brands fa-twitter',
                'icon_class'    => 'fab fa-twitter',
                'bg_class'      => 'tu-bg-blue'
            ),
            'linkedin'   =>  array(
                'name'  => esc_html__('Linkedin','tuturn'),
                'title' => esc_html__('Enter linkedin URL','tuturn'),
                'icon'  => 'fa-brands fa-linkedin',
                'icon_class'    => 'fab fa-linkedin',
                'bg_class'      => 'tu-bg-blue'
            ),
            'google'   =>  array(
                'name'  => esc_html__('Google','tuturn'),
                'title' => esc_html__('Enter google URL','tuturn'),
                'icon'  => 'fa-brands fa-google',
                'icon_class'    => 'fab fa-google',
                'bg_class'      => 'tu-bg-orange'
            ),
            'dribbble'   =>  array(
                'name'  => esc_html__('Dribbble','tuturn'),
                'title' => esc_html__('Enter dribbble URL','tuturn'),
                'icon'  => 'fa-brands fa-dribbble',
                'icon_class'    => 'fab fa-dribbble',
                'bg_class'      => 'tu-bg-voilet'
            ),
            'twitch'   =>  array(
                'name'  => esc_html__('Twitch','tuturn'),
                'title' => esc_html__('Enter twitch URL','tuturn'),
                'icon'  => 'fa-brands fa-twitch',
                'icon_class'    => 'fab fa-twitch',
                'bg_class'      => 'tu-bg-maroon'
            ),
        );

	  $lists = apply_filters('tuturn_filter_social_media_lists', $lists);
	  return $lists;
	}
}

/**
 * List offline places
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */

if (!function_exists('tuturn_offline_places_lists')) {
	function tuturn_offline_places_lists($key='')
	{
	  	$lists = array(
			'student'  => esc_html__('Student place','tuturn' ),
            'tutor'    => esc_html__('Tutor place','tuturn' ),
	    );

	  $lists = apply_filters('tuturn_filter_offline_places_lists', $lists);
      if( !empty($key) ){
          $lists = !empty($lists[$key]) ? $lists[$key] : ''; 
      }
	  return $lists;
	}
}

/**
 * Payouts List
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */

if (!function_exists('tuturn_get_payouts_lists')) {
	function tuturn_get_payouts_lists()
	{
		global $tuturn_settings;
		$payout_bank_icon     = !empty($tuturn_settings['payout_bank_icon']['url']) ? $tuturn_settings['payout_bank_icon']['url'] : TUTURN_DIRECTORY_URI . 'public/images/earning/bank.png';
		$payout_paypal_icon   = !empty($tuturn_settings['payout_paypal_icon']['url']) ? $tuturn_settings['payout_paypal_icon']['url'] : TUTURN_DIRECTORY_URI . 'public/images/earning/paypal.png';
    	$payout_payoneer_icon = !empty($tuturn_settings['payout_payoneer_icon']['url']) ? $tuturn_settings['payout_payoneer_icon']['url'] : TUTURN_DIRECTORY_URI . 'public/images/earning/payoneer.png';

	  	$lists = array(
			/* paypal */
			'paypal' => array(
				'id'                => 'paypal',
				'label'             => esc_html__('Paypal', 'tuturn'),
				'title'             => esc_html__('Add/edit paypal account', 'tuturn'),
				'img_url'           => esc_url($payout_paypal_icon),
				'status'            => 'enable',
				'desc'              => wp_kses(__('<p>You need to add your PayPal email ID above. For more about</p> <ul class="tu-accountmethods"> <li><a target="_blank" href="https://www.paypal.com/"> PayPal </a></li><li><a target="_blank" href="https://www.paypal.com/signup/">Create an account</a></li></ul>', 'tuturn'), array(
					'a'         => array(
                        'href'      => array(),
                        'target'    => array(),
                        'title'     => array()
					),
					'ul'         => array(
                        'class'      => array(),
					),
					'li'        => array(),
					'br'        => array(),
					'em'        => array(),
					'strong'    => array(),
					'p'         => array(),
				)),
				'fields'	=> array(
					'paypal_email'    => array(
						'type'          => 'text',
						'classes'       => '',
						'required'      => true,
						'show_this'     => true,
						'title'         => esc_html__('PayPal email address', 'tuturn'),
						'placeholder'   => esc_html__('Enter paypal email here', 'tuturn'),
						'message'       => esc_html__('PayPal Email Address is required', 'tuturn'),
					)
				)
			),
			/* payoneer */
			'payoneer' => array(
				'id'		=> 'payoneer',
				'label'		=> esc_html__('Payoneer', 'tuturn'),
				'title'		=> esc_html__('Add/edit payoneer account', 'tuturn'),
				'img_url'	=> esc_url($payout_payoneer_icon),
				'status'	=> 'enable',
				
                'desc'      => wp_kses(__('<p>You need to add your payoneer email ID below in the text field. For more about</p> <ul class="tu-accountmethods"> <li><a target="_blank" href="https://www.payoneer.com/"> Payoneer </a></li><li><a target="_blank" href="https://www.payoneer.com/accounts/">Create an account</a></li></ul>', 'tuturn'), array(
					'a'         => array(
                        'href'      => array(),
                        'target'    => array(),
                        'title'     => array()
					),
					'ul'         => array(
                        'class'      => array(),
					),
					'li'        => array(),
					'br'        => array(),
					'em'        => array(),
					'strong'    => array(),
					'p'         => array(),
				)),
				'fields'	=> array(
					'payoneer_email' => array(
						'type'			=> 'text',
						'show_this'		=> true,
						'classes'		=> '',
						'required'		=> true,
						'title'			=> esc_html__('Payoneer email address','tuturn'),
						'placeholder'	=> esc_html__('Add Payoneer email address','tuturn'),
						'message'		=> esc_html__('Payoneer email address is required','tuturn'),
					)
				)
			),
			/* bank */
			'bank'                => array(
				'id'                => 'bank',
				'label'             => esc_html__('Bank', 'tuturn'),
				'title'             => esc_html__('Add/edit bank account', 'tuturn'),
				'img_url'           => esc_url($payout_bank_icon),
				'status'            => 'enable',
				'desc'              => wp_kses('', 
                array(
					'a'               => array(
					'href'          => array(),
					'target'        => array(),
					'title'         => array()
					),
					'br'              => array(),
					'em'              => array(),
					'strong'          => array(),
				)),
				'fields'	=> array(
					'bank_account_title'	=> array(
						'type'          => 'text',
						'classes'       => '',
						'required'      => true,
						'show_this'     => true,
						'title'         => esc_html__('Bank account title', 'tuturn'),
						'placeholder'   => esc_html__('Bank account title', 'tuturn'),
						'message'       => esc_html__('Bank Account Title is required', 'tuturn'),
					),
					'bank_account_number' => array(
						'type'          => 'text',
						'classes'       => '',
						'required'      => true,
						'show_this'     => true,
						'title'         => esc_html__('Bank account number', 'tuturn'),
						'placeholder'   => esc_html__('Bank account number', 'tuturn'),
						'message'       => esc_html__('Bank Account Number is required', 'tuturn'),
					),
					'bank_account_name' => array(
						'type'          => 'text',
						'classes'       => '',
						'required'      => true,
						'show_this'     => true,
						'title'         => esc_html__('Bank name', 'tuturn'),
						'placeholder'   => esc_html__('Bank name', 'tuturn'),
						'message'       => esc_html__('Bank Name is required', 'tuturn'),
					),
					'bank_routing_number' => array(
						'type'          => 'text',
						'classes'       => '',
						'required'      => true,
						'show_this'     => true,
						'title'         => esc_html__('Bank routing number', 'tuturn'),
						'placeholder'   => esc_html__('Bank routing number', 'tuturn'),
						'message'       => esc_html__('Bank Routing Number is required', 'tuturn'),
					),
					'bank_iban' => array(
						'type'          => 'text',
						'classes'       => '',
						'required'      => true,
						'show_this'     => true,
						'title'         => esc_html__('Bank IBAN', 'tuturn'),
						'placeholder'   => esc_html__('Bank IBAN', 'tuturn'),
						'message'       => esc_html__('Bank IBN is required', 'tuturn'),
					),
					'bank_bic_swift' => array(
						'type'			=> 'text',
						'classes'		=> '',
						'required'		=> false,
						'show_this'		=> true,
						'title'	=> esc_html__('Bank BIC/SWIFT','tuturn'),
						'placeholder'	=> esc_html__('Bank BIC/SWIFT','tuturn'),
						'message'		=> esc_html__('Bank BIC/SWIFT is required','tuturn'),
					)
				)
			),
	  );

	  $lists = apply_filters('tuturn_filter_payouts_lists', $lists);
	  return $lists;
	}
}

/**
 * get total service companies
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_get_post_count_by_meta')) {
    function tuturn_get_post_count_by_meta($post_type, $status = array(), $meta_array = array(), $return = 'count', $count = '-1')
    {
        $args = array(
            'post_type'         => $post_type,
            'posts_per_page'    => $count,
            'post_status'       => $status
        );

        if (!empty($meta_array)) {
            foreach ($meta_array as $meta) {
                $args['meta_query'][]  = $meta;
            }
        }
        
        $post_data   = get_posts($args);
        if ($return === 'count') {
            $post_data  = !empty($post_data) ? count($post_data) : 0;
        }
        return $post_data;
    }
}

/**
 * get total hours
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_hours_data_by_meta')) {
    function tuturn_hours_data_by_meta($meta_array = array())
    {
        $args = array(
            'post_type'         => 'volunteer-hours',
            'posts_per_page'    => -1,
            'post_status'       => 'any'
        );

        if (!empty($meta_array)) {
            foreach ($meta_array as $meta) { 
                $args['meta_query'][]  = $meta;
            }
        }
        
        $post_data  = get_posts($args);
        $hours_array= array();
        $total      = 0;
        $pending    = 0;
        $completed  = 0;
        if( !empty($post_data) ){
            foreach($post_data as $post){
                $hours  = get_post_meta($post->ID,'total_hours',true );
                $hours  = isset($hours) ? $hours : 0;
                $total  = $total + $hours;
                if( $post->post_status === 'publish' ){
                    $completed  = $completed + $hours;
                } else if( in_array($post->post_status,array('decline','pending') )){
                    $pending  = $pending + $hours;
                }
            }
        }
        $hours_array['completed']   = $completed;
        $hours_array['pending']     = $pending;
        $hours_array['total']       = $total;
        return $hours_array;
    }
}
/**
 * get post count by metadata
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_get_post_count_by_metadata')) {
    function tuturn_get_post_count_by_metadata($post_type, $args_array = array(), $meta_array = array(), $return = 'count')
    {
        $args = array(
            'post_type'         => $post_type,
        );

        if (!empty($args_array)) {
            foreach ($args_array as $key => $val) {
                $args[$key]  = $val;
            }
        }
        if (!empty($meta_array)) {
            foreach ($meta_array as $meta) {
                $args['meta_query'][]  = $meta;
            }
        }

        $post_data   = get_posts($args);
        if ($return === 'count') {
            $post_data  = !empty($post_data) ? count($post_data) : 0;
        }
        return $post_data;
    }
}

if(!function_exists('tuturn_allow_pending_listings') ) {
	function tuturn_allow_pending_listings($query) {
        $post_type	= $query->get( 'post_type' );
		if( is_admin() && is_user_logged_in()  && $query->is_main_query() ){
            $author_id  = !empty($_GET['author_id']) ? intval($_GET['author_id']) : 0;
			if( !empty($post_type) && $post_type === 'user-verification' && !empty($author_id) ){
				$query->set('author', $author_id);
			} else if( !empty($post_type) && $post_type === 'volunteer-hours'){
                $profile_id 	= tuturn_get_linked_profile_id( $author_id);
                $user_type      = get_post_type( $profile_id );
                if (!empty($user_type) && $user_type === 'tuturn-student') {
                    $query->set('meta_key','student_id');
                    $query->set('meta_value',$author_id);
                } elseif (!empty($user_type) && $user_type === 'tuturn-instructor') {
                    $query->set('author', $author_id);
                }
			} else if( !empty($post_type) && $post_type === 'attachment' ){
                $query->set( 'meta_query', [
                    [
                        'key'     => 'is_encrypted',
                        'compare' => 'NOT EXISTS',
                    ]
                ]);
            } 
            return $query;
        } 
	}
	add_action('pre_get_posts','tuturn_allow_pending_listings');
}

/**
 * List Months
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( ! function_exists( 'tuturn_list_month' ) ) {
    function tuturn_list_month( ) {
		$month_names    = array(
			'01'	=> esc_html__("January",'tuturn'),
			'02'	=> esc_html__("February",'tuturn'),
			'03' 	=> esc_html__("March",'tuturn'),
			'04'	=> esc_html__("April",'tuturn'),
			'05'	=> esc_html__("May",'tuturn'),
			'06'	=> esc_html__("June",'tuturn'),
			'07'	=> esc_html__("July",'tuturn'),
			'08'	=> esc_html__("August",'tuturn'),
			'09'	=> esc_html__("September",'tuturn'),
			'10'	=> esc_html__("October",'tuturn'),
			'11'	=> esc_html__("November",'tuturn'),
			'12'	=> esc_html__("December",'tuturn')
		);
		return $month_names;
	}
}

/**
 * Update commisssion fee
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_commission_fee' ) ) {
	function tuturn_commission_fee( $price='' ) {
		global $tuturn_settings;
		$percentage		    = !empty($tuturn_settings['admin_commision']) ? $tuturn_settings['admin_commision'] : 0;
		$admin_shares 	    = $price/100 * $percentage;
		$instructor_shares 	= $price - $admin_shares;

		$settings['admin_shares'] 	    = !empty($admin_shares) && $admin_shares > 0 ? number_format($admin_shares,2,'.', '') : 0.0;
		$settings['instructor_shares'] 	= !empty($instructor_shares) && $instructor_shares > 0 ? number_format($instructor_shares,2,'.', '') : 0.0;

		return $settings;
	}
}


/**
 * Check any prerequisites for our REST request.
 */
function check_prerequisites($userId='') {
    if ( defined( 'WC_ABSPATH' ) ) {
        // WC 3.6+ - Cart and other frontend functions are not included for REST requests.
        include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
        include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
        include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
    }

    if ( null === WC()->session ) {
        $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

        WC()->session = new $session_class();
        WC()->session->init();
    }
    if ( null === WC()->customer ) {
        WC()->customer = new WC_Customer( $userId, true );
    }
    if ( null === WC()->cart ) {
        WC()->cart = new WC_Cart();
        // We need to force a refresh of the cart contents from session here (cart contents are normally refreshed on wp_loaded, which has already happened by this point).
        WC()->cart->get_cart();
    }
}

/**
 * Get admin user
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */

if (!function_exists('tuturn_get_administrator_user_id')) {
	function tuturn_get_administrator_user_id(){
		$args = array(
			'role'		=> 'administrator',
			'fields'	=> array( 'ID' ),
			'orderby' 	=> 'ID',
			'order'   	=> 'ASC'
		);
		$users 			= get_users( $args );
		$admin_user	= !empty($users[0]) ? $users[0] : '';
		if(!empty($admin_user)){
			return $admin_user->ID;
		}

	}
}

/**
 * Order options
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_instructor_service_create')) {
		function tuturn_instructor_service_create($total_price=1){
            if (class_exists('WooCommerce')) {
			$args = array(
				'limit'     => -1, // All products
				'status'    => 'publish',
				'type'      => 'service',
				'orderby'   => 'date',
				'order'     => 'DESC',
			);

			$tuturn_funds = wc_get_products( $args );

			$wallet_post	= !empty($tuturn_funds[0]) ? $tuturn_funds[0] : '';
            
			if(!empty($wallet_post)){
				return (int)$wallet_post->get_id();
			} else {

                $item = array(
                    'name' 		    => esc_html__('Tuition service','tuturn'),
                    'description' 	=> esc_html__('Instructor service','tuturn'),
                );
				$admin_user_id = tuturn_get_administrator_user_id();

				$post_args = array(
					'post_author' 	=> intval($admin_user_id),
					'post_title' 	=> $item['name'],
					'post_content' 	=> $item['description'],
					'post_status' 	=> 'publish',
					'post_type' 	=> "product",
				);

				$post_id = wp_insert_post( $post_args );

				wp_set_object_terms( $post_id, 'service', 'product_type' );
				update_post_meta( $post_id, '_visibility', '' );
				update_post_meta( $post_id, '_downloadable', 'no' );
				update_post_meta( $post_id, '_virtual', 'yes' );
				update_post_meta( $post_id, '_regular_price', 1.0 );
				update_post_meta( $post_id, '_sale_price', '' );
				update_post_meta( $post_id, '_purchase_note', '' );
				update_post_meta( $post_id, '_featured', 'no' );
				update_post_meta( $post_id, '_product_attributes', array() );
				update_post_meta( $post_id, '_price', 1.0 );
				update_post_meta( $post_id, '_sold_individually', '' );
				update_post_meta( $post_id, '_manage_stock', 'no' );
				update_post_meta( $post_id, '_backorders', 'no' );
				update_post_meta( $post_id, '_stock', '' );
				return $post_id;
			}
		}
	}
}

/**
 * tuturn_get_choosed_date_slots
 * @author Amentotech <theamentotech@gmail.com>
 * @since    1.0.0
 */
if(!function_exists('tuturn_get_choosed_date_slots')){
    function tuturn_get_choosed_date_slots($slots=array()){
        $date_slot_arr  = array();
        if(!empty($slots) && is_array($slots)){
            $time_format    = get_option('time_format');
            $saved_value    = $slots;
            foreach($saved_value as $date_slot=>$time_slot_arr){
                $date_slot = date_i18n(get_option('date_format'), strtotime($date_slot));
                $single_arr = array();
                if(!empty($time_slot_arr)){
                    foreach($time_slot_arr as $innerTimeSlots){
                        $time_slot_val 	        = explode('-', $innerTimeSlots);
                        $first_time_slot 	    = date($time_format, strtotime('2022-01-01' . $time_slot_val[0]));
                        $second_time_slot 	    = date($time_format, strtotime('2022-01-01' . $time_slot_val[1]));
                        $single_arr[] = array(
                            'slotStart_time'    => $first_time_slot,
                            'slotEnd_time'      => $second_time_slot,
                        );
                    }
                }
                $date_slot_arr[$date_slot] = $single_arr;
            }
        }
        return $date_slot_arr;
    }
}

/**
 * (=)
 * getAppointmentSelectedSlots
 * @author Amentotech <theamentotech@gmail.com>
 * @since    1.0.0
 */
if(!function_exists('getAppointmentSelectedSlots')){
    function getAppointmentSelectedSlots($instructor_profile_id=0){
        $book_days = array();
        $instructor_id 		    = tuturn_get_linked_profile_id($instructor_profile_id, 'post');
        $time_format 			= get_option('time_format');
        $bookings_data   		= get_post_meta($instructor_profile_id, 'tuturn_bookings', true);
        $bookings_data   		= !empty($bookings_data) ? $bookings_data : array();
        $current_time 			= date_i18n($time_format, current_time('timestamp'));
        $booking_detail 		= !empty($bookings_data['bookings']['timeSlots']) ? $bookings_data['bookings']['timeSlots'] : array();

        $data_transient         = get_transient('tu_booked_appointment_data');
        $slot_transit 	        = !empty($data_transient['booked_data']['booked_slots']) ? $data_transient['booked_data']['booked_slots'] : array();
        $service_ids 	        = !empty($data_transient['booked_data']['booked_ids']) ? $data_transient['booked_data']['booked_ids'] : array();
        $filter_form            = !empty($data_transient['booked_data']['filter_form']) ? $data_transient['booked_data']['filter_form'] : array();
        $filter_startDate       = !empty($filter_form['start_date']) ? $filter_form['start_date'] : '';
        $filter_endDate         = !empty($filter_form['end_date']) ? $filter_form['end_date'] : '';
        $filter_time_start      = !empty($filter_form['start_time']) ? ($filter_form['start_time']) : '';
        $filter_start_time      = !empty($filter_time_start) ? strtotime($filter_time_start) : '';
        $filter_time_end        = !empty($filter_form['end_time']) ? ($filter_form['end_time']) : '';
        $filter_end_time        = !empty($filter_time_end) ? strtotime($filter_time_end) : '';
        $filter_week_days       = !empty($filter_form['week_days']) ? $filter_form['week_days'] : array();
        /* slots for saved date */
        
        if( !empty( $booking_detail['bookings_slots'] ) ) {
            if(!empty($slot_transit)){
                $day_slots = array();
                foreach($slot_transit as $slot_date=>$slot_value){
                    $slot_arr           = array();
                    $day_by_filter_date = strtotime($slot_date);
                    $find_dayName_by_date   = date('l', strtotime($slot_date));
                    $find_day_by_date   = strtolower($find_dayName_by_date);
                    $day_slots          = array_column($booking_detail, $find_day_by_date);
                    if(!empty($day_slots[0]['slots'])){
                        foreach($day_slots[0]['slots'] as $slot_key=>$slot_val){

                            $slots = $slot_val['slot'];
                            $slot_key_val 	= explode('-', $slot_val['time']);
                            $first_time 	= date($time_format, strtotime('2022-01-01' . $slot_key_val[0]));
                            $second_time 	= date($time_format, strtotime('2022-01-01' . $slot_key_val[1]));
                            if($filter_start_time <= strtotime($first_time) && $filter_end_time >= strtotime($second_time) ){
                                $disabled       = "";
                                /* slot count if buy */
                                //pending......
                                $count_posts			= 0;
                                if( ($count_posts >= $slots) ) { 
                                    $disabled	= 'disabled'; 
                                    $spaces		= 0;
                                } else {
                                    $spaces		= $slots - $count_posts; 
                                }
                                /* selected slot */
                                $is_selected = in_array($slot_val['time'],$slot_value);
                                $selected = $is_selected==true ? 'checked' : '';

                                $slot_arr[] 	= array(
                                    'dateString'    => 	$day_by_filter_date,
                                    'date'          => 	$slot_date,
                                    'slot_key'		=> 	$slot_val['time'],
                                    'start_time' 	=>	$first_time,
                                    'end_time' 		=>  $second_time,
                                    'slots'			=> 	$spaces,
                                    'disabled'		=> 	$disabled,
                                    'selected'		=>  $selected,
                                );
                            }
                        }
                        $book_days[$day_by_filter_date] = $slot_arr;
                        $book_days[$day_by_filter_date]['date'] = date_i18n('l', $day_by_filter_date) .', '. date_i18n('F d Y',$day_by_filter_date);
                        $book_days['filter_weekDays']   = $filter_week_days;
                        $book_days['filter_timeStart']  = $filter_time_start;
                        $book_days['filter_timeEnd']    = $filter_time_end;
                        $book_days['filter_dateStart']  = $filter_startDate;
                        $book_days['filter_dateEnd']    = $filter_endDate;
                    }
                }
            } else {
            }
            
        }
        return $book_days;
    }
}

/**
 * Appointment slots
 * @author Amentotech <theamentotech@gmail.com>
 * @since    1.0.0
 */
if(!function_exists('tuturn_get_appointment_start_time')){
    function tuturn_get_appointment_start_time($booked_slots, $key_element = 'key_first'){
        $appointment_date   = '';
        if($key_element == 'key_first'){
            $first_appointment_date = array_key_first($booked_slots);
            if(!empty($booked_slots[$first_appointment_date])){
                $appointment_date = $booked_slots[$first_appointment_date];
            }
        } else {
            $appointment_date = array_key_last($booked_slots);
            if(!empty($booked_slots[$appointment_date]) && is_array($booked_slots[$appointment_date])){
                $appointment_date = $booked_slots[$appointment_date];
                array_reverse($appointment_date);
            }
        }
       

        $booking_start_time = '';
        $booking_end_time = '';

        if(!empty($appointment_date) && is_array($appointment_date)){

            $first_index = array_key_first($appointment_date);

            if(!empty($appointment_date[$first_index])){
                $values =explode("-",$appointment_date[$first_index]);
                $booking_start_time= $values[0];
                $booking_end_time    = $values[1];
                $booking_start_time  = substr($booking_start_time, 0, 2).':'.substr($booking_start_time, -2);
                $booking_end_time    = substr($booking_end_time, 0, 2).':'.substr($booking_end_time, -2);

                return array(
                    'start_time'    => $booking_start_time,
                    'end_time'      => $booking_end_time
                );
            }
        }
    }
}

/**
 * Get registered sidebars
 * @author Amentotech <theamentotech@gmail.com>
 * @since    1.0.0
 */
if( !function_exists( 'tuturnGetRegisterSidebars' ) ) {
	function tuturnGetRegisterSidebars(){
		global $wp_registered_sidebars;
		$tu_sidebarsArray		= array();
		$tu_sidebarsArray[''] 	= esc_html__('No Sidebar','tuturn');
		$sidebars = $wp_registered_sidebars;
		if (is_array($sidebars) && !empty($sidebars)) {
			foreach ($sidebars as $key => $sidebar) {
				$tu_sidebarsArray[$key] = $sidebar['name'];
			}
		}
		
		return $tu_sidebarsArray;
	}
}

/**
 * Posts multiple meta
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if(!function_exists('tuturn_get_total_posts_by_multiple_meta')){
    function tuturn_get_total_posts_by_multiple_meta($type='shop_order', $status='', $metas=array()){
        if( !empty( $metas ) ) {
			foreach( $metas as $key => $val ) {
				$meta_query_args[] = array(
					'key' 			=> $key,
					'value' 		=> $val,
					'compare' 		=> '='
				);
			}
		}

        $query_args = array(
			'posts_per_page'      => -1,
			'post_type' 	      => $type,
			'post_status'	 	  => $status,
			'ignore_sticky_posts' => 1
		);
        
        if (!empty($meta_query_args)) {
			$query_relation = array('relation' => 'AND',);
			$meta_query_args = array_merge($query_relation, $meta_query_args);
			$query_args['meta_query'] = $meta_query_args;
		}
       
        return $query = new WP_Query($query_args); 
    }
}


/**
 * Place order
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_place_order')) {
	function tuturn_place_order($user_id, $type='') {
		global $woocommerce, $tuturn_settings;
		if( class_exists('WooCommerce') ) {
			$first_name         = get_user_meta( $user_id, 'billing_first_name',true );
			$last_name          = get_user_meta( $user_id, 'billing_last_name',true );
			$billing_city       = get_user_meta( $user_id, 'billing_city',true );
			$billing_email      = get_user_meta( $user_id, 'billing_email',true );
			$billing_postcode   = get_user_meta( $user_id, 'billing_postcode',true );
			$billing_phone      = get_user_meta( $user_id, 'billing_phone',true );
			$billing_state      = get_user_meta( $user_id, 'billing_state',true );
			$billing_country    = get_user_meta( $user_id, 'billing_country',true );

			$address_1         = get_user_meta( $user_id, 'billing_address_1',true );
			$billing_company   = get_user_meta( $user_id, 'billing_company',true );
			$address_2         = get_user_meta( $user_id, 'billing_address_2',true );

			$billing_email      = !empty($billing_email) ? $billing_email : get_userdata($user_id)->user_email;
			$first_name         = !empty($first_name) ? $first_name : '';
			$last_name          = !empty($last_name) ? $last_name : '';
			$billing_city       = !empty($billing_city) ? $billing_city : '';
			$billing_postcode   = !empty($billing_postcode) ? $billing_postcode : '';
			$billing_phone      = !empty($billing_phone) ? $billing_phone : '';
			$address_1      	= !empty($address_1) ? $address_1 : '';
			$address_2      	= !empty($address_2) ? $address_2 : '';
			
			$address = array(
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'company'    => $billing_company,
				'email'      => $billing_email,
				'phone'      => $billing_phone,
				'address_1'  => $address_1,
				'address_2'  => $address_2,
				'city'       => $billing_city,
				'state'      => $billing_state,
				'postcode'   => $billing_postcode,
				'country'    => $billing_country
			);
			$order_data = array(
				'status'        => apply_filters('woocommerce_default_order_status', 'completed'),
				'customer_id'	=> $user_id
			);
			
			$order 		= wc_create_order( array('customer_id' => $user_id ) );
			$order_id 	= $order->get_id();
			$items 		= WC()->cart->get_cart();
           
			foreach($items as $item => $values) {
				$item_id = $order->add_product(
                    $values['data'], $values['quantity'], array(
                		'variation' => $values['variation'],
						'totals' => array(
							'subtotal' 		=> 0.0,
							'subtotal_tax' 	=> 0.0,
							'total' 		=> 0.0,
							'tax' 			=> 0.0,
							'tax_data' 		=> 0.0
						)
                    )
            	);
               
				if( !empty($item_id) ){
					if ( !empty( $values['cart_data'] ) ) {
                        if( !empty( $values['payment_type'] ) && $values['payment_type'] == 'booking' ){
                            $booked_data    	= !empty($values['cart_data']['booked_data']) ? $values['cart_data']['booked_data'] : array();
                            $student_id    		= !empty($values['cart_data']['student_id']) ? $values['cart_data']['student_id'] : '';
                            $instructor_id    	= !empty($values['cart_data']['instructor_id']) ? $values['cart_data']['instructor_id'] : '';
                            $booked_slots   	= !empty($booked_data['booked_slots']) ? $booked_data['booked_slots'] : array();
                            $booking_user   	= !empty($booked_data['information']['info_someone_else']) ? 1 : 0;
                            $admin_share   		= 0.0;
                            $instructor_shares  = 0.0;

                            wc_update_order_item_meta( $item_id, 'cus_woo_product_data', $values['cart_data'] );
                            update_post_meta( $order_id, 'cus_woo_product_data', $values['cart_data'] );
                            update_post_meta( $order_id, 'payment_type', $values['payment_type'] );

                            $booked_slots_dates	= array_keys($booked_slots);
                            update_post_meta( $order_id, '_booking_date', $booked_slots_dates );

                            update_post_meta( $order_id, 'is_user_booking', $booking_user );
                            update_post_meta( $order_id, 'booking_status', 'pending' );
                            if(!empty($booked_slots)){
                                update_post_meta( $order_id, '_booking_slots', $booked_slots );	
                            }
                            
                            update_post_meta( $order_id, 'student_id',$student_id );
                            update_post_meta( $order_id, 'instructor_id',$instructor_id );
                            $instructor_profile_id 		= tuturn_get_linked_profile_id( $instructor_id );
                            $student_profile_id 		= tuturn_get_linked_profile_id( $student_id );
                            update_post_meta( $order_id, '_linked_profile',$student_profile_id );
                            update_post_meta( $order_id, 'student_profile_id',$student_profile_id );
                            update_post_meta( $order_id, 'instructor_profile_id',$instructor_profile_id );

                            update_post_meta( $order_id, 'admin_share', $admin_share );
                            update_post_meta( $order_id, 'instructor_shares', $instructor_shares );	

                            /* email to instructor and student on booking */
                            if (class_exists('Tuturn_Email_helper')) {
                                $emailData	= array();
                                if (class_exists('TuturnOrderStatuses')) {
                                    $default_chat_mesage	= wp_kses(__('Congratulations! You have hired for the booking "{{booking_name}}".<br/> with order link: {{order_link}}.', 'tuturn'),
                                        array(
                                            'a' => array(
                                            'href' => array(),
                                            'title' => array()
                                            ),
                                            'br' => array(),
                                            'em' => array(),
                                            'strong' => array(),
                                        ));
                                    $instructor_profile_id 			= tuturn_get_linked_profile_id( $instructor_id, 'instructor' );
                                    $student_profile_id 			= tuturn_get_linked_profile_id( $student_id,'', 'student' );
                                    /* instructor details */
                                    $instructor_profileData   		= get_post_meta($instructor_profile_id, 'profile_details', true);
                                    $instructor_name				= !empty($instructor_profileData['first_name']) ? $instructor_profileData['first_name'] : '';
                                    $instructor_contact_detail		= !empty($instructor_profileData['contact_info']) ? $instructor_profileData['contact_info'] : array();
                                    $instructor_data				= get_userdata($instructor_id);
                                    $instructorprofile_name			= !empty($instructor_data->display_name) ? $instructor_data->display_name : '';
                                    $instructorprofile_email		= !empty($instructor_data->user_email) ? $instructor_data->user_email : '';
                                    /* student details */
                                    $student_profileData   			= get_post_meta($student_profile_id, 'profile_details', true);
                                    $student_name					= !empty($student_profileData['first_name']) ? $student_profileData['first_name'] : '';
                                    $student_contact_detail			= !empty($student_profileData['contact_info']) ? $student_profileData['contact_info'] : array();
                                    $student_data					= get_userdata($student_id);
                                    $studentprofile_name			= !empty($student_data->display_name) ? $student_data->display_name : '';
                                    $studentprofile_email			= !empty($student_data->user_email) ? $student_data->user_email : '';
                                    $email_helper 					= new TuturnOrderStatuses();
                                    $emailData['instructor_name'] 	= !empty($instructor_name) ? $instructor_name : $instructorprofile_name;
                                    $emailData['instructor_email'] 	= !empty($instructor_contact_detail['email']) ? $instructor_contact_detail['email'] : $instructorprofile_email;
                                    $emailData['student_name'] 		= !empty($student_name) ? $student_name : $studentprofile_name;
                                    $emailData['student_email']		= !empty($student_contact_detail['email']) ? $student_contact_detail['email'] : $studentprofile_email;
                                    $emailData['order_id'] 			= !empty($order_id) ? $order_id : 0;
                                    $emailData['order_amount'] 		= $order->get_total();
                                    $emailData['sender_id']         = $instructor_id; //instructor id
                                    $emailData['receiver_id']       = $student_id; //student id
                                    $emailData['login_url'] 		= Tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $instructor_id, true, 'listings');
                                    $service_names					= esc_html__('Tutor Service', 'tuturn');
                                    $current_page_link  			= get_permalink().'profile-settings/';
                                    $invoice_url                    = add_query_arg(array('tab' => 'invoices','mode' => 'detail', 'id'=>intval( $order_id)), $current_page_link);
                                    if( apply_filters( 'tuturn_chat_solution_guppy',false ) === true && $tuturn_settings['hire_instructor_chat_switch']==true){
                                        $message = !empty($tuturn_settings['hire_instructor_chat_mesage']) ? $tuturn_settings['hire_instructor_chat_mesage'] : $default_chat_mesage;
                                        $chat_mesage  = str_replace("{{booking_name}}", $service_names, $message);
                                        $chat_mesage  = str_replace("{{order_link}}", $invoice_url, $chat_mesage);
                                        do_action('wpguppy_send_message_to_user', $student_id, $instructor_id, $chat_mesage);
                                    }

                                    if (!empty($tuturn_settings['email_new_booking_instructor'])) {
                                        $email_helper->new_booking_instructor_email($emailData);
                                    }

                                    if ( !empty($tuturn_settings['email_new_booking_student'])) {
                                        $email_helper->new_booking_student_email($emailData);
                                    }

                                    do_action('noty_push_notification', $emailData);
                                }
                            }
						}
					}
				}
			}

			$order->set_address( $address, 'billing' );
			$order->set_address( $address, 'shipping' );
			$order->calculate_totals();
			$order_id 		= $order->get_id();
			$order_id		= !empty($order_id) ? $order_id : 0;

            $order->set_status( 'wc-completed' );
            $order_id = $order->save();
			WC()->cart->empty_cart();
			return $order_id;
		}
	}
}

/**
 * Custom query for woocommerce get products
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_products_custom_query_var')) {
    function tuturn_products_custom_query_var( $query, $query_vars ) {
        if ( !empty($query_vars['user_type']) ) {
            $query['meta_query'][] = array(
                'key'   => 'user_type',
                'value' => esc_attr( $query_vars['user_type'] ),
            );
        }

        return $query;
    }
    add_filter( 'woocommerce_product_data_store_cpt_get_products_query', 'tuturn_products_custom_query_var', 10, 2 );
}

/**
 * @init            booking time of the day
 * @package         Tuturn
 * @since           1.0
 */
if(!function_exists('tuturn_booking_time_of_day')){
	function tuturn_booking_time_of_day(){
		$time_of_days = array(
			'0000-1159' 	=> array(
				'icon'			=> 'tu-pre-12pm icon icon-sunrise',
				'time'			=> '0000-1159',
				'heading' 		=> esc_html__('PRE 12PM' , 'tuturn'),
			),
			'1200-1659' 	=> array(
				'icon'			=> 'tu-after-12pm-5pm icon icon-sun',
				'time'			=> '1200-1659',
				'heading'		=> esc_html__('12PM-5PM' , 'tuturn'),
			),
			'1700-2359' 	=> array(
				'icon'			=> 'tu-after-5pm icon icon-sunset',
				'time'			=> '1700-2359',
				'heading'		=> esc_html__('AFTER 5PM' , 'tuturn'),
			),
		);
		$time_of_days 	= apply_filters('tuturn_filter_time_of_days',$time_of_days);
		return $time_of_days;
	}
	add_filter('tuturn_booking_time_of_day', 'tuturn_booking_time_of_day', 10);
}


/**
 * @init            getting booking time of the day
 * @package         Tuturn
 * @since           1.0
 */
if(!function_exists('tuturn_get_time_of_day_key')){
	function tuturn_get_time_of_day_key($slots = array()){
		$time_format 	        = get_option('time_format');
		$time_slots_arr 		= $slots['slots'];
		$day_time_keys          = array();
        $day_time_keys_arr      = array();
		if(!empty($time_slots_arr)){
			$start_end_time = array();
			$start_slot_time_index 		= key(array_slice($time_slots_arr, 0, 1, true));
			$end_slot_time_index 		= key(array_slice($time_slots_arr, -1, 1, true));
			$firstSlotArr 	= !empty($start_slot_time_index) ? $time_slots_arr[$start_slot_time_index] : array();
			$lastSlotArr 	= !empty($end_slot_time_index) ? $time_slots_arr[$end_slot_time_index] : array();
			//getting first slot first time
			if(!empty($firstSlotArr)){
				$start_time_interval 	= !empty($firstSlotArr['time']) ? $firstSlotArr['time'] : '';
				$slot_key_val 			= explode('-', $start_time_interval);
				$first_time 			= $slot_key_val[0];
			}
            
			//getting last slot last time
			if(!empty($lastSlotArr)){
				$end_time_interval 		= !empty($lastSlotArr['time']) ? $lastSlotArr['time'] : '';
				$slot_key_val 			= explode('-', $end_time_interval);
				$last_time 				= $slot_key_val[1];
			} 
            
            $first_time         = !empty($first_time) ? intval($first_time) : 0;
            $last_time          = !empty($last_time) ? intval($last_time) : 0;
			$time_day_key_arr   = apply_filters('tuturn_booking_time_of_day', '');
			foreach($time_day_key_arr as $keys=>$key_val){
				if(isset($keys)){
					$day_key_slot_arr		= explode('-', $keys);
					if(isset($day_key_slot_arr) && is_array($day_key_slot_arr)){ 
						$key_start_time 	= $day_key_slot_arr[0];
						$key_end_time 		= $day_key_slot_arr[1];
						$key_start_time     = isset($key_start_time) ? intval($key_start_time) : 0;
                        $key_end_time       = isset($key_end_time) ? intval($key_end_time) : 0;
                        if(in_array($key_start_time, range($first_time, $last_time)) || in_array($key_end_time, range($first_time, $last_time)) || in_array($first_time, range($key_start_time, $key_end_time)) || in_array($last_time, range($key_start_time, $key_end_time))) {
                            $day_time_keys_arr[] = $keys;
                        }
					} 
				}
			}

		}
        return ($day_time_keys_arr);
	}
	add_filter('tuturn_get_time_of_day_key', 'tuturn_get_time_of_day_key');
}

/**
 * @init            getting random string
 * @package         Tuturn
 * @since           1.0
 */
if(!function_exists('tuturnGenerateRandomString')){
    function tuturnGenerateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
}


/**
 * @init            Add footer model
 * @package         Tuturn
 * @since           1.0
 */
if(!function_exists('tuturnUpdateVerificationCode')){
    add_action('wp_footer', 'tuturnUpdateVerificationCode');
    function tuturnUpdateVerificationCode() {
        global $tuturn_settings;
        $confirmation_key   = !empty($_GET['confirmation_key']) ? $_GET['confirmation_key'] : '';
        if( !empty($confirmation_key) ){
            $confirmation_key   = utf8_decode(base64_decode($confirmation_key));
            $confirmation_data  = !empty($confirmation_key) ? explode('-',$confirmation_key) : '';
            $verification_id    = !empty($confirmation_data[0]) ? intval($confirmation_data[0]) : 0;
            $code               = !empty($confirmation_data[1]) ? $confirmation_data[1] : '';
            $verification       = !empty($verification_id) ? get_post_meta($verification_id,'parent_verification',true) : '';
            if( !empty($verification_id) && !empty($verification) && $verification === 'no' ){
                update_post_meta($verification_id,'parent_verification','yes');
                $post_data  = get_post_meta( $verification_id, 'verification_info', true );
                $post_data  = !empty($post_data['info']) ? $post_data['info'] : array();
                if (class_exists('Tuturn_Email_helper')) {
                    if (class_exists('TuturnRegistrationEmail')) {
                        $email_helper   = new TuturnRegistrationEmail(); 
                        $emailData = array();
                        $emailData['user_name']         = !empty($post_data['name']) ? $post_data['name'] : '';
                        $emailData['user_email']        = !empty($post_data['email_address']) ? $post_data['email_address'] : '';
                        $emailData['phone_number']      = !empty($post_data['phone_number']) ? $post_data['phone_number'] : '';
                        $emailData['gender']            = !empty($post_data['gender']) ? $post_data['gender'] : '';
                        $emailData['address']           = !empty($post_data['address']) ? $post_data['address'] : '';
                        $emailData['school_name']       = !empty($post_data['school']) ? $post_data['school'] : '';
                        
                        if ( !empty($tuturn_settings['email_parent_confirmation_request_admin'])){

                            $profile_id     = !empty($post_data['profile_id']) ? $post_data['profile_id'] : 0;
                            $user_id        = tuturn_get_linked_profile_id($profile_id,'post'); 

                            if(!empty($user_id)){
                                /* get latest user post type identification */
                                $arguments = array(
                                    'fields'        => 'ids',
                                    'post_type'     => 'user-verification',
                                    'post_status'   => 'draft',
                                    'author'        => $user_id,
                                );

                                $post_types = get_posts($arguments);

                                $queryArgs = http_build_query( array(
                                    'action' => 'edit', 
                                    'post' => $post_types[0], 
                                ) );

                                $url = admin_url( 'post.php' ) . '?' . $queryArgs;
                                $emailData['approve_profile']   = $url;
                            }
                            $email_helper->parent_confirmation_identification_request($emailData);
                        }
                    }
                }
                $message_title      = esc_html__('Submission confirmation','tuturn');
                $message_content    = esc_html__('Thank you so much for the verification. Our team will review and approve the profile as soon as possible.','tuturn');
                $script = "
                jQuery(document).ready(function () {
                    stickyAlert('".esc_js($message_title)."', '".esc_js($message_content)."', { classList: 'success'});
                });";
                wp_add_inline_script('tuturn-public', $script, 'after');
                ?>

                <?php
            }
        }
    }
}

if(!function_exists('tuturnListWeekDays')){
    function tuturnListWeekDays() {

        $days_array             = array(
            'monday' => esc_html__('Monday', 'tuturn'), 
            'tuesday' => esc_html__('Tuesday', 'tuturn'),
            'wednesday' => esc_html__('Wednesday', 'tuturn'),
            'thursday' => esc_html__('Thursday', 'tuturn'),
            'friday' => esc_html__('Friday', 'tuturn'),
            'saturday' => esc_html__('Saturday', 'tuturn'),
            'sunday' => esc_html__('Sunday', 'tuturn'),
        );
        $days_array = apply_filters('tuturn_filter_ListWeekDays', $days_array);
        return $days_array;
    }
}