<?php
/**
 * File uploader
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_save_profile_settings')) {
    function tu_save_profile_settings()
    {
        global $tuturn_settings, $wpdb;
        $shortname_option  =  !empty($tuturn_settings['shortname_option']) ? $tuturn_settings['shortname_option'] : '';

        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $post_data      = !empty($_POST['data']) ? $_POST['data'] : '';
        parse_str($post_data, $data);
    
        $profile_id = !empty($data['profile_id']) ? intval($data['profile_id']) : 0;
        
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        if (empty($profile_id)) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Error', 'tuturn');
            $json['message']    = esc_html__('Something wrong, please try again.', 'tuturn');
            wp_send_json($json);
        }
        $user_email_option  = !empty($tuturn_settings['user_email_option']) ? $tuturn_settings['user_email_option'] : '';
        $user_id            = tuturn_get_linked_profile_id($profile_id, 'post');
        $profile_tab        = !empty($data['profile_settings_tab']) ? sanitize_text_field($data['profile_settings_tab']) : 'personal_details';
        $profile_details    = get_post_meta($profile_id, 'profile_details', true);
        $profile_details    = !empty($profile_details) ? $profile_details : array();
        $profile_type       = get_post_type( $profile_id );


        if ($profile_tab == 'contact_details') {
            //Validation
            $validations = array(
                'phone'         => esc_html__('Please enter your phone number', 'tuturn'),
                'email_address' => esc_html__('Please enter your email address', 'tuturn'),
            );
            foreach ($validations as $key => $value) {
                if (isset($data['contact_info'][$key]) && empty($data['contact_info'][$key])) {
                    $json['title']      = esc_html__("Oops!", 'tuturn');
                    $json['type']       = 'error';
                    $json['message']    = $value;
                    wp_send_json($json);
                }

                if ($key === 'email_address') {
                    if (!is_email($data['contact_info']['email_address'])) {
                        $json['type']       = 'error';
                        $json['title']      = esc_html__("Oops!", 'tuturn');
                        $json['message']    = esc_html__('Please enter a valid email address.', 'tuturn');
                        wp_send_json($json);
                    }
                }
            }

            $website        = !empty($data['contact_info']['website']) ? sanitize_text_field($data['contact_info']['website']) : '';
            $email_address  = !empty($data['contact_info']['email_address']) ? sanitize_text_field($data['contact_info']['email_address']) : '';
            if( !empty($user_email_option) ){
                $user_data  = wp_update_user(array(
                    'ID'            => $user_id,
                    'user_url'      => $website,
                    'user_email'    => $email_address,
                ));
                if (!is_wp_error($user_data)) {
					$query	= "UPDATE " . $wpdb->prefix . "users SET `user_email` = '" . $email_address . "'  
							WHERE ID='" . $user_id . "'";

					$user_update = $wpdb->query(
						$wpdb->prepare($query)
					);
				} else {
					$error_string = $user_data->get_error_message();
					$json['type'] = 'error';

					if (!empty($error_string)) {
						$json['message'] = $error_string;
					} else {
						$json['message'] = esc_html__('Error occurred', 'tuturn-api');
					}

					$json['type']       = 'error';
					$json['title']      = esc_html__("Oops!", 'tuturn-api');
					wp_send_json($json);
				}
            }
            $profile_details['contact_info']    = array_map('sanitize_text_field', $data['contact_info']);
            update_post_meta($profile_id, 'profile_details', $profile_details);
           
        } elseif ($profile_tab == 'education') {
            $education  = !empty($data['education']) ? $data['education'] : array();
            if (!empty($education)) {
                $education  = array_map(function ($json) {
                    return json_decode($json, true);
                }, $education);
            }

            $profile_details['education']   = $education;
            update_post_meta($profile_id, 'profile_details', $profile_details);
        } elseif ($profile_tab == 'subjects') {
            $category_array = array();
            $subject  = !empty($data['subject']) ? $data['subject'] : array();
            $category_ids_array = array();

            if (!empty($subject)) {
                $subject  = array_map(function ($json) {
                    return json_decode($json, true);
                }, $subject);

                
                foreach ($subject as $key => $subject_category) {
                    $parent_category    = $subject_category['parent_category'];
                    if(!empty($parent_category['slug'])){
                        $slug   = $parent_category['slug'];
                        $name   = $parent_category['name'];
                        $category_array[$slug]  = $name;
                    }

                    if(!empty($parent_category['id'])){
                        $category_ids_array[]   = $parent_category['id'];
                    }

                    if (!empty($subject_category['subcategories']) && is_array($subject_category['subcategories']) && count($subject_category['subcategories']) > 0) {
                        $subcategories  = $subject_category['subcategories']; 
                        foreach ($subcategories as $subcategory) {
                            if(!empty($subcategory['id'])){
                                $category_ids_array[]   = $subcategory['id'];
                                $slug   = $subcategory['slug'];
                                $name   = $subcategory['name'];
                                $category_array[$slug]  = $name;
                            }
                        }
                    }    
                }
            }

            wp_set_post_terms($profile_id, $category_ids_array, 'product_cat');           
            $profile_details['subject']   = $subject;
            $profile_details['categories']   = $category_array;

            update_post_meta($profile_id, 'profile_details', $profile_details);
        } elseif ($profile_tab == 'media') {
            $files          = !empty($data['attachments']) ? $data['attachments'] : array();
            $attachment_ids = array();
            if (!empty($files)) {
                foreach ($files as $key => $value) {
                    if (!empty($value['attachment_id'])) {
                        $attachment_ids[]   = intval($value['attachment_id']);
                    } else {
                        if (!empty($value['url'])) {
                            $attachment_url     = sanitize_text_field($value['url']);
                            $new_attachemt      = tuturn_temp_upload_to_media($attachment_url, $profile_id);
                            $attachment_ids[]   = $new_attachemt['attachment_id'];
                        }
                    }
                }
                $attachment_ids_string  = implode(',', $attachment_ids);
                update_post_meta($profile_id, 'media_gallery', $attachment_ids_string);
            }
        } elseif ($profile_tab == 'personal_details') {
            $state_option   = !empty($tuturn_settings['profile_state']) ? $tuturn_settings['profile_state'] : false;
            $profile_gender = !empty($tuturn_settings['profile_gender']) ? $tuturn_settings['profile_gender'] : false;
            $profile_grade  = !empty($tuturn_settings['profile_grade']) ? $tuturn_settings['profile_grade'] : false;
            $profile_hourlyprice    = !empty($tuturn_settings['profile_hourlyprice']) ? $tuturn_settings['profile_hourlyprice'] : false;
            $teach_settings         = !empty($tuturn_settings['teach_settings']) ? $tuturn_settings['teach_settings'] : 'default';
            $geocoding_option   = !empty($tuturn_settings['geocoding_option']) ? $tuturn_settings['geocoding_option'] : 'no';
            $validations = array(
                'profile_id'    => esc_html__('Something wrong. please try again.', 'tuturn'),
                'first_name'    => esc_html__('First name is required', 'tuturn'),
                'last_name'     => esc_html__('Last name is required', 'tuturn'),
                'hourly_rate'   => esc_html__('Hourly fee is required', 'tuturn'),
                'country'       => esc_html__('Please select country', 'tuturn'),
            );

            if($geocoding_option == 'yes'){
                $validations['zipcode'] = esc_html__('Please enter your zipcode', 'tuturn');
            }
            
            if( !empty($profile_type) && $profile_type === 'tuturn-student' ){
                unset($validations['hourly_rate']);
            } else if(  !empty($profile_type) && $profile_type === 'tuturn-instructor' && !empty($profile_hourlyprice) ){
                unset($validations['hourly_rate']);
            }
           
            $validations    = apply_filters('tuturn_filter_personal_details_validation',$validations);
           
            if(  !empty($profile_type) && $profile_type === 'tuturn-instructor' && !empty($teach_settings) && $teach_settings === 'custom' ){
                $validations['teaching_preference'] = esc_html__('Please select location option', 'tuturn');
                if(!empty($data['teaching_preference']) && is_array($data['teaching_preference']) && in_array('offline',$data['teaching_preference']) ){
                    $validations['offline_place'] = esc_html__('Please select Offline place', 'tuturn');
                    if(!empty($data['offline_place']) && $data['offline_place'] ==='tutor' ){
                        $validations['tutor_country']   = esc_html__('Please select tutor country', 'tuturn');
                    }
                }
            }

            if( !empty($profile_gender) ){
                $validations['gender']   = esc_html__('Please select gender', 'tuturn');
            }

            if( !empty($profile_grade) ){
                $validations['grade']   = esc_html__('grade is required', 'tuturn');
            }

            
            foreach ($validations as $key => $value) {
                if (empty($data[$key])) {
                    $json['title']      = esc_html__("Oops!", 'tuturn');
                    $json['type']       = 'error';
                    $json['message']    = $value;
                    wp_send_json($json);
                }
            }

            if(!empty($data['hourly_rate']) && !preg_match('/^[0-9]+(\\.[0-9]+)?$/', $data['hourly_rate']) && $profile_type === 'tuturn-instructor'){
                $json['title']      = esc_html__("Oops!", 'tuturn');
                $json['type']       = 'error';
                $json['message']    = esc_html__("Only numeric value for an hourly fee", 'tuturn');
                wp_send_json($json);
            }
            
            $introduction       = !empty($data['brief_introduction']) ? $data['brief_introduction']  : '';
            $hourly_rate        = isset($data['hourly_rate']) ? sanitize_text_field($data['hourly_rate']) : '';
            $country_region     = !empty($data['country']) ? sanitize_text_field($data['country']) : '';
            $zipcode            = !empty($data['zipcode']) ? sanitize_text_field($data['zipcode']) : '';
            $address            = !empty($data['address']) ? sanitize_text_field($data['address']) : ''; 
            $introduction       = !empty($_POST['introduction']) ? $_POST['introduction'] : '';
            $country            = $country_region;
            $latitude           = $longitude = '0';
            if($geocoding_option == 'yes'){

                $geoLocation    = tuturn_process_geocode_info($zipcode, $country_region);

                if (!empty($geoLocation) && $geoLocation['type'] == 'success') {
                    $geoData     = !empty($geoLocation['geo_data']['geometry']['location'])     ? $geoLocation['geo_data']['geometry']['location'] : array();
                    
                    if (empty($address)) {
                        $address = !empty($geoLocation['geo_data']['formatted_address']) ? $geoLocation['geo_data']['formatted_address'] : '';
                    }
                    $latitude     = !empty($geoData['lat']) ? $geoData['lat'] : '';
                    $longitude    = !empty($geoData['lng']) ? $geoData['lng'] : '';
                } elseif (!empty($geoLocation) && $geoLocation['type'] == 'error') {
                    $json['type']       = 'error';
                    $json['title']      = esc_html__('Validation error', 'tuturn');
                    $json['message']    = !empty($geoLocation['message'] ) ?  $geoLocation['message'] : esc_html__('Please add a valid country and zipcode', 'tuturn');
                    wp_send_json($json);
                } elseif (!empty($geoLocation) && $geoLocation['type'] == 'api_key_error') {
                    $json['type']       = 'error';
                    $json['title']      = esc_html__('Validation error', 'tuturn');
                    $json['message']    = !empty($geoLocation['message'] ) ?  $geoLocation['message'] : esc_html__('Google map key required for geocode verfication.', 'tuturn');
                    wp_send_json($json);
                }
            }
           
            if(  !empty($profile_type) && $profile_type === 'tuturn-instructor' && !empty($teach_settings) && $teach_settings === 'custom' ){
                if(!empty($data['teaching_preference']) && is_array($data['teaching_preference']) && in_array('offline',$data['teaching_preference']) ){
                    update_post_meta($profile_id, 'offline_place', ($data['offline_place']));
                    if(!empty($data['offline_place']) && in_array('tutor',$data['offline_place']) ){
                        if($geocoding_option == 'yes'){
                            $tutor_country     = !empty($data['tutor_country']) ? sanitize_text_field($data['tutor_country']) : '';
                            $tutor_state       = !empty($data['tutor_state']) ? sanitize_text_field($data['tutor_state']) : '';
                            $tutor_city        = !empty($data['tutor_city']) ? sanitize_text_field($data['tutor_city']) : '';
                            $tutor_zipcode     = !empty($data['tutor_zipcode']) ? sanitize_text_field($data['tutor_zipcode']) : '';
                            $tutor_address     = !empty($data['tutor_address']) ? sanitize_text_field($data['tutor_address']) : ''; 
                            $geoLocation       = tuturn_process_geocode_info($tutor_zipcode, $tutor_country);

                            if (!empty($geoLocation) && $geoLocation['type'] == 'success') {
                                $geoData     = !empty($geoLocation['geo_data']['geometry']['location'])     ? $geoLocation['geo_data']['geometry']['location'] : array();
                                
                                if (empty($address)) {
                                    $address = !empty($geoLocation['geo_data']['formatted_address']) ? $geoLocation['geo_data']['formatted_address'] : '';
                                }
                                $tutor_latitude     = !empty($geoData['lat']) ? $geoData['lat'] : '';
                                $tutor_longitude    = !empty($geoData['lng']) ? $geoData['lng'] : '';
                                update_post_meta($profile_id, '_tutor_latitude', $tutor_latitude);
                                update_post_meta($profile_id, '_tutor_longitude', $tutor_longitude);
                                update_post_meta($profile_id, '_tutor_address', $tutor_address);
                                update_post_meta($profile_id, '_tutor_city', $tutor_city);
                                update_post_meta($profile_id, '_tutor_state', $tutor_state);
                                update_post_meta($profile_id, '_tutor_country', $tutor_country);
                                update_post_meta($profile_id, '_tutor_country_region', $tutor_country);
                                update_post_meta($profile_id, '_tutor_zipcode', $tutor_zipcode);
                            } elseif (!empty($geoLocation) && $geoLocation['type'] == 'error') {
                                $json['type']       = 'error';
                                $json['title']      = esc_html__('Validation error', 'tuturn');
                                $json['message']    = !empty($geoLocation['message'] ) ?  $geoLocation['message'] : esc_html__('Please add a valid tutor country and zipcode', 'tuturn');
                                wp_send_json($json);
                            } elseif (!empty($geoLocation) && $geoLocation['type'] == 'api_key_error') {
                                $json['type']       = 'error';
                                $json['title']      = esc_html__('Validation error', 'tuturn');
                                $json['message']    = !empty($geoLocation['message'] ) ?  $geoLocation['message'] : esc_html__('Google map key required for geocode verfication.', 'tuturn');
                                wp_send_json($json);
                            }
                        }
                    }
                }
            }
            
            
            $languages          = !empty($data['languages']) ? $data['languages'] : array();
            $updatedLanguages   = array();
            $language_ids       = array();
            foreach ($languages as $lang) {
                if (!empty($lang)) {
                    $term_obj   = get_term_by('slug', $lang, 'languages');;
                    if ($term_obj) {
                        $updatedLanguages[$term_obj->slug] = $term_obj->name;
                        $language_ids[] = $term_obj->term_id;
                    }
                }
            }

            $first_name             = !empty($data['first_name']) ? sanitize_text_field($data['first_name']) : '';
            $last_name              = !empty($data['last_name']) ? sanitize_text_field($data['last_name']) : '';
            $phone_number           = !empty($data['phone_number']) ? sanitize_text_field($data['phone_number']) : '';
            $teaching_preference    = !empty($data['teaching_preference']) ? $data['teaching_preference'] : array();

            $instructor_post = array(
                'ID'            => $profile_id,
                'post_title'    => wp_strip_all_tags($first_name . ' ' . $last_name),
                'post_content'  => $introduction,
            );
            wp_update_post( $instructor_post );
            
            wp_update_user( array(
                'ID'            => $user_id, 
                'first_name'    => $first_name,
                'last_name'     => $last_name,
            ) );


            $profile_details['first_name']  = $first_name;
            $profile_details['last_name']   = $last_name;
            
            $profile_details['name']        = $first_name . ' ' . $last_name;
            $profile_details['tagline']     = sanitize_text_field($data['tagline']);
            $profile_details['languages']   = $updatedLanguages;
            wp_set_post_terms($profile_id, $language_ids, 'languages');

            if( !empty($profile_type) && $profile_type === 'tuturn-instructor' ){
                $old_hourly_rate    = get_post_meta($profile_id,'hourly_rate', true );
                update_post_meta($profile_id, 'hourly_rate', $hourly_rate);
                do_action('tuturn_update_hourly_price',$profile_id,$old_hourly_rate,$hourly_rate );
            }

            if( !empty($state_option) ){
                $city               = !empty($data['city']) ? sanitize_text_field($data['city']) : '';
                $state              = !empty($data['state']) ? sanitize_text_field($data['state']) : '';
                update_post_meta($profile_id, '_city', $city);
                update_post_meta($profile_id, '_state', $state);
            }

            if( !empty($profile_gender) ){
                $gender = !empty($data['gender']) ? sanitize_text_field($data['gender']) : '';
                update_post_meta($profile_id, '_gender', $gender);
            }

            if( !empty($profile_grade) ){
                $grade  = !empty($data['grade']) ? sanitize_text_field($data['grade']) : '';
                update_post_meta($profile_id, '_grade', $grade);
            }

            update_post_meta($profile_id, '_latitude', $latitude);
            update_post_meta($profile_id, '_longitude', $longitude);
            update_post_meta($profile_id, '_address', $address);
            update_post_meta($profile_id, '_country', $country);
            update_post_meta($profile_id, '_country_region', $country_region);
            update_post_meta($profile_id, '_zipcode', $zipcode);
            update_post_meta($profile_id, 'teaching_preference', ($teaching_preference));
            update_post_meta($profile_id, 'profile_details', $profile_details);
        }

        //Update slug
        if(!empty($shortname_option) && $shortname_option == 'yes'){
            $user_name  = tuturn_get_username($profile_id);
            $post_name_update = array(
                'ID'            => intval($profile_id),
                'post_name'    => sanitize_title($user_name)
            );
            wp_update_post( $post_name_update );
        }
        
        $json['type']       = 'success';
        $json['title']      = esc_html__('Updated!', 'tuturn');
        $json['message']    = esc_html__('Record has been updated', 'tuturn');
        wp_send_json($json);
    }
    add_action('wp_ajax_tu_save_profile_settings', 'tu_save_profile_settings');
}

/**
 * File uploader
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_update_media_gallery')) {
    function tuturn_update_media_gallery()
    {
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json       = array();
        /*=================== Wp Nonce Verification =================*/
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $profile_id    = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : '';
        if (empty($profile_id)) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Restricted Access', 'tuturn');
            $json['message']    = esc_html__('You are not allowed to perform this action.', 'tuturn');
            wp_send_json($json);
        }

        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent
        $attachments    = !empty($_POST['attachments']) ? $_POST['attachments'] : array();

        foreach ($attachments as $attachment) {
            $signleRecord     = array();
            $attachmentType = !empty($attachment['attachmentType']) ? $attachment['attachmentType'] : '';
            $signleRecord['attachment_type'] = $attachmentType;

            if ($attachmentType == 'image') {

                if (!empty($attachment['isSaveImage'])) {
                    $fileSizes                  = array();
                    $attachment_url             = $attachment['file'];
                    $new_attachemt              = tuturn_temp_upload_to_media($attachment_url, $profile_id);
                    $signleRecord['file']       = $new_attachemt['url'];
                    $signleRecord['fileName']       = $new_attachemt['name'];
                    $signleRecord['attachment_id']  = $new_attachemt['attachment_id'];
                } else {
                    $signleRecord['file']           = $attachment['file'];
                    $signleRecord['fileName']       = $attachment['fileName'];
                    $signleRecord['attachment_id']  = $attachment['attachment_id'];
                }
            } elseif ($attachmentType == 'video') {

                if (!empty($attachment['isSaveVideo'])) {
                    $attachment_url = $attachment['file'];
                    $new_attachemt  = tuturn_temp_upload_to_media($attachment_url, $profile_id);
                    $signleRecord['videofile']      = $new_attachemt['url'];
                    $signleRecord['attachment_id']  = $new_attachemt['attachment_id'];
                } else {
                    $signleRecord['videofile']      = $attachment['file'];
                }
            } else {
                $signleRecord['url']    = !empty($attachment['url']) ? $attachment['url'] : '';
            }

            $mediaGallery[] = $signleRecord;
        }

        update_post_meta($profile_id, 'media_gallery', $mediaGallery);
        $json['type']       = 'success';
        $json['title']      = esc_html__('Updated!', 'tuturn');
        $json['message']    = esc_html__('Record has been updated', 'tuturn');
        wp_send_json($json);
    }

    add_action('wp_ajax_tuturn_update_media_gallery', 'tuturn_update_media_gallery');
}

/**
 * Approve hours
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_approve_hours')) {
    function tu_approve_hours()
    {
        global $current_user,$tuturn_settings;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json       = array();
        $profile_id  = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : '';
        
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent
        $post_id    = !empty($_POST['post_id']) ? $_POST['post_id'] : '';
        $validation_fields  = array(
            'post_id'           => esc_html__('Something went wrong','tuturn')
        );

        $json['type']       = 'error';
        $json['title']      = esc_html__('Approve hours','tuturn');
        $user               = array();
        foreach($validation_fields as $data_key => $validation_field ){
            if( empty($_POST[$data_key]) ){
                $json['message']        = $validation_field;
                wp_send_json($json);
            }
        }

        // instructor data
        $instructor_id          = get_post_meta($post_id,'instructor_id',true );
        $instructor_obj         = get_user_by('id', $instructor_id);
        $instructor_email       = !empty($instructor_obj) ? esc_html($instructor_obj->data->user_email) : '';
        $instructor_profile_id  = get_user_meta($instructor_id, '_linked_profile', true);
        $instructor_name        = !empty($instructor_profile_id) ? esc_html(get_the_title( $instructor_profile_id )) : '';

        // student data
        $student_id             = get_post_meta($post_id,'student_id',true );

        $student_profile_id     = get_user_meta($student_id, '_linked_profile', true);
        $student_name           = !empty($student_profile_id) ? esc_html(get_the_title( $student_profile_id )) : '';
        $post_data              = array('ID' => $post_id, 'post_status' => 'publish');
        wp_update_post( $post_data );

        $hourly_data    = get_post_meta($post_id,'hourly_data',true );
        $hourly_data    = !empty($hourly_data) ? $hourly_data : array();
        $hourly_data['approve_date']    = current_time('mysql');
        update_post_meta($post_id,'hourly_data', $hourly_data );


        if (class_exists('Tuturn_Email_Helper')) {
            if (class_exists('Tuturnmeetingdetail')) {
                $emailData                  = array();
                $emailData['student_name']  = !empty($student_name) ? $student_name : '';
                $emailData['student_id']    = !empty($student_id) ? $student_id : '';

                $emailData['tutor_name']        = !empty($instructor_name) ? $instructor_name : '';
                $emailData['instructor_id']     = !empty($instructor_id) ? $instructor_id : '';
                $emailData['instructor_email']  = !empty($instructor_email) ? $instructor_email : '';
                if(!empty($tuturn_settings['email_hours_log_approvel'])){
                    $email_helper   = new Tuturnmeetingdetail();
                    $email_helper->send_hour_log_approve($emailData);
                }
               
            }
        }

        $json['type']           = 'success';
        $json['message']        = esc_html__('You have successfully approve hours','tuturn');
        wp_send_json($json);
    }
    add_action('wp_ajax_tu_approve_hours', 'tu_approve_hours');
}

/**
 * Decline hours
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_decline_hours')) {
    function tu_decline_hours()
    {
        global $current_user,$tuturn_settings;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $json       = array();
        $profile_id  = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : '';
        
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent
        $post_data  = !empty($_POST['data']) ? $_POST['data'] : '';
        parse_str($post_data, $data);
       
        $json['type']       = 'error';
        $json['title']      = esc_html__('Decline hours','tuturn');

        $post_id                = !empty($data['post_id']) ? intval($data['post_id']) : 0;
        $decline_reason         = !empty($data['decline_reason']) ? sanitize_textarea_field($data['decline_reason']) : '';

        if(empty($decline_reason)){
            $json['type']           = 'error';
            $json['message']        = esc_html__('Decline reason is required','tuturn');
            wp_send_json($json);
        }

        // instructor data
        $instructor_id          = get_post_meta($post_id,'instructor_id',true );
        $instructor_obj         = get_user_by('id', $instructor_id);
        $instructor_email       = !empty($instructor_obj) ? esc_html($instructor_obj->data->user_email) : '';
        $instructor_profile_id  = get_user_meta($instructor_id, '_linked_profile', true);
        $instructor_name        = !empty($instructor_profile_id) ? esc_html(get_the_title( $instructor_profile_id )) : '';

        // student data
        $student_id             = get_post_meta($post_id,'student_id',true );
        $student_profile_id     = get_user_meta($student_id, '_linked_profile', true);
        $student_name           = !empty($student_profile_id) ? esc_html(get_the_title( $student_profile_id )) : '';

        $post_data              = array('ID' => $post_id, 'post_status' => 'decline');
        wp_update_post( $post_data );

        $hourly_data    = get_post_meta($post_id,'hourly_data',true );
        $hourly_data    = !empty($hourly_data) ? $hourly_data : array();

        $hourly_data['decline_reason']  = $decline_reason;
        $hourly_data['decline_date']    = current_time('mysql');
        $update = update_post_meta($post_id,'hourly_data', $hourly_data );
        if ( ! is_wp_error( $update ) ) { 

            if(!empty($tuturn_settings['email_hours_log_decline'])){     
                if (class_exists('Tuturn_Email_Helper')) {
                    if (class_exists('Tuturnmeetingdetail')) {
                        $emailData                  = array();
                        $emailData['student_name']  = !empty($student_name) ? $student_name : '';
                        $emailData['student_id']    = !empty($student_id) ? $student_id : '';
        
                        $emailData['tutor_name']        = !empty($instructor_name) ? $instructor_name : '';
                        $emailData['instructor_id']     = !empty($instructor_id) ? $instructor_id : '';
                        $emailData['instructor_email']  = !empty($instructor_email) ? $instructor_email : '';

                        $emailData['decline_reason']    = !empty($decline_reason) ? $decline_reason : '';

                        $email_helper   = new Tuturnmeetingdetail();
                        $email_helper->decline_hours_log_request($emailData);
                    }
                }
            }
            $json['type']           = 'success';
            $json['message']        = esc_html__('You have successfully decline hours','tuturn');
            wp_send_json($json);
        } else {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Something went wrong','tuturn');
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_tu_decline_hours', 'tu_decline_hours');
}

/**
 * Send hours reminder
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_send_reminder')) {
    function tu_send_reminder()
    {
        global $current_user,$tuturn_settings;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $json       = array();
        $profile_id  = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then preve

        $post_data  = !empty($_POST['data']) ? $_POST['data'] : '';
        parse_str($post_data, $data);
        $validation_fields  = array(
            'post_id'       => esc_html__('Something went wrong','tuturn'),
            'email'         => esc_html__('Parent email is required','tuturn'),
            'name'          => esc_html__('Parent name is required','tuturn')
        );
        $json['type']       = 'error';
        $json['title']      = esc_html__('Send reminder to parents','tuturn');
        $user               = array();
        foreach($validation_fields as $data_key => $validation_field ){

            if( empty($data[$data_key]) ){
                $json['message']        = $validation_field;
                wp_send_json($json);
            }
        }

        $post_id    = !empty($data['post_id']) ? intval($data['post_id']) : 0;
        $email      = !empty($data['email']) ? sanitize_email($data['email']) : '';
        $name       = !empty($data['name']) ? sanitize_text_field($data['name']) : '';
        if( !is_email($email) ){
            $json['message']        = esc_html__('Add vaild email address','tuturn');
            wp_send_json($json);
        }
        $hourly_data    = get_post_meta($post_id,'hourly_data',true );
        $hourly_data    = !empty($hourly_data) ? $hourly_data : array();
        update_post_meta($post_id,'_send_reminder', 1 );
        $profileId  = get_user_meta($current_user->ID, '_linked_profile', true);
        $user_name  = tuturn_get_username($profileId);
        $hourly_data['reminder']['parent_name']     = $name;
        $hourly_data['reminder']['parent_email']    = $email;
        $hourly_data['reminder']['status']          = 'pending';
        $update = update_post_meta($post_id,'hourly_data', $hourly_data );

        if ( ! is_wp_error( $update ) ) {
            if(!empty($tuturn_settings['email_parent_volunteer_hours_reminder'])){   
                if (class_exists('Tuturn_Email_Helper')) {
                    if (class_exists('Tuturnmeetingdetail')) {
                        $emailData                  = array();
                        $emailData['parent_name']   = !empty($name) ? $name : '';
                        $emailData['tutor_name']    = $user_name;
                        $emailData['parent_email']  = !empty($email) ? $email : '';
                        $email_helper               = new Tuturnmeetingdetail();
                        $email_helper->send_reminder_to_parents($emailData);
                    }
                }
            }
            $json['type']           = 'success';
            $json['message']        = esc_html__('You have successfully submit hourly requests','tuturn');
            wp_send_json($json);
        } else {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Something went wrong','tuturn');
            wp_send_json($json);
        }
 
    }
    add_action('wp_ajax_tu_send_reminder', 'tu_send_reminder');
}
/**
 * Update hours
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_check_student_email')) {
    function tuturn_check_student_email()
    {
        global $current_user,$tuturn_settings;
        $profile_id  = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $email_address  = !empty($_POST['email']) ? $_POST['email'] : '';
        $json['type']       = 'error';
        $json['title']      = esc_html__('Volunteer hours','tuturn');
        $user               = array();
        if( empty($email_address) ){
            $json['message']        = esc_html__('Please enter valid eamil address','tuturn');
            wp_send_json($json);
        } else if( !is_email($email_address) ){
            $json['message']        = esc_html__('Please enter valid eamil address','tuturn');
            wp_send_json($json);
        }
        $exists = email_exists( $email_address );
        if($exists){
            $user               = get_user_by('email',$email_address);
            $user_type          = get_user_meta($user->ID,'_user_type',true );
            $log_search_type    = !empty($tuturn_settings['log_search_type']) ? $tuturn_settings['log_search_type'] : '';
            if( !empty($user_type) && $user_type != 'student'){
                $json['message']        = esc_html__('Please add only student email address','tuturn');
                wp_send_json($json);
            } else if( !empty($user_type) && $user_type == 'student'){
                if(!empty($log_search_type) && $log_search_type === 'booking' ){
                    $arg  = array(
                        array(
                            'key'       => 'instructor_id',
                            'value'     => $current_user->ID,
                            'compare'   => '=',
                            'type'      => 'NUMERIC'
                        ),
                        array(
                            'key'       => 'student_id',
                            'value'     => $user->ID,
                            'compare'   => '=',
                            'type'      => 'NUMERIC'
                        ),
                        array(
                            'key'       => 'payment_type',
                            'value'     => 'booking',
                            'compare'   => '='
                        )
                    );
                    $count_bookings    = tuturn_get_post_count_by_meta('shop_order','any', $arg);
                    if( empty($count_bookings) ){
                        $json['message']        = esc_html__('You have not any booking with this user','tuturn');
                        wp_send_json($json);
                    }
                }
                $profileId          = get_user_meta($user->ID, '_linked_profile', true);
                $user_name          = !empty($profileId) ? tuturn_get_username($profileId) : $user->display_name;
                $avatar             = apply_filters('tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $profileId), array('width' => 100, 'height' => 100));
                $avatar_html        = !empty($avatar) ? '<img src="'.esc_url($avatar).'" atl="'.esc_attr($user_name).'"/>' : '';
                $json['type']       = 'success';
                $json['user_html']  = $avatar_html.'<span>'.$user_name.'</span>';
                wp_send_json($json);

            }
        } else {
            $json['message']        = esc_html__('This email address is not exists','tuturn');
            wp_send_json($json);
        }
        
    }
    add_action('wp_ajax_tuturn_check_student_email', 'tuturn_check_student_email');
}
/**
 * Update hours
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_update_hours')) {
    function tu_update_hours()
    {
        global $current_user,$tuturn_settings;
        $json       = array();
        $profile_id  = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : '';

        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $current_user_type          = get_user_meta($current_user->ID,'_user_type',true );
        $linked_profile_id          = get_user_meta($current_user->ID, '_linked_profile', true);
       
        if($current_user_type != 'instructor'){
            $json['type']       = 'error';
            $json['title']      = esc_html__('Error','tuturn');
            $json['message']    = esc_html__('Only instructor allowed to do this','tuturn');
            wp_send_json($json);
        }

        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent
        
        $post_data  = !empty($_POST['data']) ? $_POST['data'] : '';
        parse_str($post_data, $data);
        
        $validation_fields  = array(
            'title'         => esc_html__('Title is required','tuturn'),
            'email'         => esc_html__('Email is required','tuturn'),
            'date'          => esc_html__('Date is required','tuturn'),
            'start_time'    => esc_html__('Start time is required','tuturn'),
            'end_time'      => esc_html__('End time is required','tuturn'),
            'description'   => esc_html__('Description field is required','tuturn'),
        );

        $json['type']       = 'error';
        $json['title']      = esc_html__('Volunteer hours','tuturn');
        $user               = array();

        foreach($validation_fields as $data_key => $validation_field ){

            if( empty($data[$data_key]) ){
                $json['message']        = $validation_field;
                wp_send_json($json);
            } elseif( !empty($data_key) && $data_key === 'email' && !empty($data[$data_key]) ){
                if( !is_email($data[$data_key]) ){
                    $json['message']        = esc_html__('Please enter valid email address','tuturn');
                    wp_send_json($json);
                }

                $exists = email_exists( $data[$data_key] );
                if($exists){
                    $user               = get_user_by('email',$data[$data_key]);
                    $user_type          = get_user_meta($user->ID,'_user_type',true );
                    $log_search_type    = !empty($tuturn_settings['log_search_type']) ? $tuturn_settings['log_search_type'] : '';
                   
                    if( !empty($user_type) && $user_type != 'student'){
                        $json['message']        = esc_html__('Please add only student email address','tuturn');
                        wp_send_json($json);
                    } else if( !empty($user_type) && $user_type == 'student'){
                        if(!empty($log_search_type) && $log_search_type === 'booking' ){
                            $arg  = array(
                                array(
                                    'key'       => 'instructor_id',
                                    'value'     => $current_user->ID,
                                    'compare'   => '=',
                                    'type'      => 'NUMERIC'
                                ),
                                array(
                                    'key'       => 'student_id',
                                    'value'     => $user->ID,
                                    'compare'   => '=',
                                    'type'      => 'NUMERIC'
                                ),
                                array(
                                    'key'       => 'payment_type',
                                    'value'     => 'booking',
                                    'compare'   => '='
                                )
                            );
                            
                            $count_bookings    = tuturn_get_post_count_by_meta('shop_order','any', $arg);
                            if( empty($count_bookings) ){
                                $json['message']        = esc_html__('You have not any booking with this user','tuturn');
                                wp_send_json($json);
                            }
                    }
                }
                } else {
                    $json['message']        = esc_html__('This email address is not exists','tuturn');
                    wp_send_json($json);
                }
            }
        }

        $title      = !empty($data['title']) ? sanitize_text_field($data['title']) : '';
        $post_id    = !empty($data['post_id']) ? sanitize_text_field($data['post_id']) : '';
        $email      = !empty($data['email']) ? sanitize_email($data['email']) : '';
        $date       = !empty($data['date']) ? sanitize_text_field($data['date']) : '';
        $start_time = !empty($data['start_time']) ? sanitize_text_field($data['start_time']) : 0;
        $end_time   = !empty($data['end_time']) ? sanitize_text_field($data['end_time']) : 0;
        $description= !empty($data['description']) ? sanitize_textarea_field($data['description']) : '';
        $files      = !empty($data['attachments']) ? ($data['attachments']) : '';
        $user_id    = $current_user->ID;
        $student_id = !empty($user->ID) ? intval($user->ID) : 0;
        $start_time1    = strtotime('2022-01-01 ' . $start_time);
        $end_time2      = strtotime('2022-01-01 ' . $end_time);
        $profile_id     = get_user_meta($user_id, '_linked_profile', true);

        $total_hours    = 0;
        if( $end_time2 <= $start_time1 ) {
            $json['message']        = esc_html__('Please select valid end time','tuturn');
            wp_send_json($json);
        } else {
            $total_hours = round(abs($end_time2 - $start_time1) / 3600,2);
        }

        $documents = array();
        if (!empty($files)) {
            $attachment_record     = array();

            foreach( $files as $file_url ){
                if(is_array($file_url) ){
                    $new_attachemt['url']               =  !empty($file_url['url']) ? $file_url['url'] : '';
                    $new_attachemt['name']              =  !empty($file_url['name']) ? $file_url['name'] : '';
                    $new_attachemt['attachment_id']     =  !empty($file_url['attachment_id']) ? $file_url['attachment_id'] : '';
                } else {
                    $new_attachemt  = tuturn_temp_upload_to_media($file_url, $profile_id);
                }
               
                $attachment_record[]  = array(
                    'url'           => $new_attachemt['url'],
                    'name'          => $new_attachemt['name'],
                    'attachment_id' => $new_attachemt['attachment_id'],
                );
            }
            $documents['attachments']  = $attachment_record;
        }

        $volunteer_post_data    = array(
            'post_title'    => wp_strip_all_tags($title),
            'post_content'  => $description,
        );

        if( !empty($post_id) ){
            $volunteer_post_data['post_status']     = 'pending';
            $volunteer_post_data['ID']              = $post_id;
            wp_update_post($volunteer_post_data);
        } else {
            $volunteer_post_data['post_author']     = $user_id;
            $volunteer_post_data['post_status']     = 'pending';
            $volunteer_post_data['post_type']       = 'volunteer-hours';
            $post_id                                = wp_insert_post($volunteer_post_data);
        }
        
        if($post_id){
            
            $hourly_data                = array();
            $hourly_data['email']       = $email;
            $hourly_data['documents']   = $documents;
            $hourly_data['date']        = $date;
            $hourly_data['start_time']  = $start_time;
            $hourly_data['end_time']    = $end_time;
            $hourly_data['total_hours']  = $total_hours;
            $hourly_data['student_id']  = $student_id;

            update_post_meta($post_id,'hourly_data',$hourly_data );
            update_post_meta($post_id,'total_hours',$total_hours );
            update_post_meta($post_id,'student_id',$student_id );
            update_post_meta($post_id,'instructor_id',$user_id );
 
            if(!empty($tuturn_settings['email_hour_log_submission'])){   
                if (class_exists('Tuturn_Email_Helper')) {
                    if (class_exists('TuturnOrderStatuses')) {

                        $instructor_id              = $user_id;
                        $student_email              = $email;
                        $student_data               = get_user_by( 'email', $student_email );
                        $student_profile_id         = !empty($student_data) ? get_user_meta($student_data->ID, '_linked_profile', true) : '';
                        $student_name               = !empty($student_profile_id) ? esc_html(get_the_title( $student_profile_id )) : '';

                        $instructor_profile_id      = get_user_meta($instructor_id, '_linked_profile', true);
                        $instructor_name            = !empty($instructor_profile_id) ? esc_html(get_the_title( $instructor_profile_id )) : '';

                        $emailData                      = array();
                        $emailData['student_email']     = !empty($student_email) ? $student_email : '';
                        $emailData['student_name']      = !empty($student_name) ? $student_name : '';
                        $emailData['instructor_name']   = !empty($instructor_name) ? $instructor_name : '';
                        $emailData['login_url']         = tuturn_Profile_Menu::tuturn_profile_menu_link('hours', $student_id, true, 'listings');
                        $email_helper   = new TuturnOrderStatuses();
                        $email_helper->update_hour_log($emailData);
                    }
                }
            }

            $json['type']           = 'success';
            $json['message']        = esc_html__('You have successfully submitted hour log request.','tuturn');
            $json['data']       = $emailData;
            wp_send_json($json);
        } else {
            $json['message'] = esc_html__('Something went wrong', 'tuturn');
            wp_send_json($json);
        }

    }
    add_action('wp_ajax_tu_update_hours', 'tu_update_hours');
}

/**
 * Remove hours log
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_remove_log')) {
    function tu_remove_log()
    {
        global $current_user;
        $json       = array();
        $profile_id  = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $post_id  = !empty($_POST['post_id']) ? intval($_POST['post_id']) : '';
        $user_id  = !empty($_POST['user_id']) ? intval($_POST['user_id']) : '';

        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        if(empty($post_id)){
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html('Something went wrong','tuturn');
            wp_send_json($json);
        }

        if ( is_user_logged_in() ) {
            $update = wp_delete_post( $post_id, true); 
            if ( ! is_wp_error( $update ) ) {
                $json['type']           = 'success';
                $json['message']        = esc_html__('You have successfully delete hour log','tuturn');
                wp_send_json($json);
            }
            else {
                $json['type']    = 'error';
                $json['message'] = esc_html__('Something went wrong', 'tuturn');
                wp_send_json($json);
           }
        } else {
            $json['type']    = 'error';
            $json['message'] = esc_html__('You are not authorized.', 'tuturn');
            wp_send_json($json);
        } 
    }
    add_action('wp_ajax_tu_remove_log', 'tu_remove_log');
}

/**
 * Download files
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_download_zip_file')) {
    function tu_download_zip_file()
    {
        global $current_user;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        if( function_exists('tuturn_verify_token') ){
            tuturn_verify_token($_POST['security']);
        }

        $json               = array();
        $post_id            = !empty($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $json['message']    = esc_html__('Download files','tuturn');
        if( !empty($post_id)){
            $hourly_data        = get_post_meta( $post_id, 'hourly_data',true );
            $attachments_files  = !empty($hourly_data['documents']['attachments']) ? $hourly_data['documents']['attachments'] : array();
            
            $download_url       = '';
            if( !empty( $attachments_files ) ){

                if( class_exists('ZipArchive') ){
                    $zip                = new ZipArchive();
                    $uploadspath        = wp_upload_dir();
                    $folderRalativePath = $uploadspath['baseurl']."/downloads";
                    $folderAbsolutePath = $uploadspath['basedir']."/downloads";
                    wp_mkdir_p($folderAbsolutePath);
                    $rand       = tuturn_unique_increment(5);
                    $filename   = $rand.round(microtime(true)).'.zip';
                    $zip_name   = $folderAbsolutePath.'/'.$filename;
                    $zip->open($zip_name,  ZipArchive::CREATE);
                    $download_url   = $folderRalativePath.'/'.$filename;

                    foreach($attachments_files as $key => $value) {
                        $file_url   = $value['url'];
                        $response   = wp_remote_get( $file_url );
                        $filedata   = wp_remote_retrieve_body( $response );
                        $zip->addFromString(basename( $file_url ), $filedata);
                    }

                    $zip->close();
                } else {
                    $json['type']           = 'error';
                    $json['message']        = esc_html__('Oops', 'tuturn');
                    $json['message_desc']   = esc_html__('Zip library is not installed on the server, please contact to hosting provider', 'tuturn');
                    wp_send_json($json);
                }
            }

            $json['type']           = 'success';
            $json['attachment']     = tuturn_add_http_protcol( $download_url );
            $json['message_desc']   = esc_html__('Your files have been donwloaded', 'tuturn');
            wp_send_json($json);
        }
    }
    add_action( 'wp_ajax_tu_download_zip_file', 'tu_download_zip_file' );
}
/**
 * Payout settings
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_payout_settings')) {
    function tu_payout_settings()
    {
        global $current_user;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json       = array();
        $profileId  = !empty($_POST['profileId']) ? intval($_POST['profileId']) : '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profileId);
        } //if user is not logged in and author check then prevent

        $post_data  = !empty($_POST['data']) ? $_POST['data'] : '';
        parse_str($post_data, $data);
        $payout_setings = $data['payout_settings'];
        $payout_list    = tuturn_get_payouts_lists();
        $fields         = !empty($payout_list[$payout_setings['payout_settings']['type']]['fields']) ? $payout_list[$payout_setings['payout_settings']['type']]['fields'] : array();
        $type           = !empty($data['payout_settings']['type']) ? $data['payout_settings']['type'] : '';
        $user_id        = !empty($current_user->ID) ? intval($current_user->ID) : '';
        $profileId      = get_user_meta($user_id, '_linked_profile', true);

        if (empty($profileId) || empty($user_id) || empty($type)) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Something went wrong', 'tuturn');
            wp_send_json($json);
        }

        $payout_method_arr = get_user_meta($user_id, 'tuturn_payout_method', true);
        if(empty($payout_method_arr) || !is_array($payout_method_arr)){
            $payout_method_arr = array();
        }
        $default_payout = '';
        if(!empty($payout_method_arr['default'])){
            $default_payout = $payout_method_arr['default'];
        }
        if (!empty($payout_setings)) {
            foreach ($payout_setings as $key => $val) {
                if (preg_match("/{$payout_setings['type']}/i", $key)) {
                    $payout_method_arr[$payout_setings['type']][$key] = $val;
                }
            }
        }
        if(!empty($default_payout)){
            $payout_method_arr['default']   = $default_payout;
        }

        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                if ($field['required'] === true && empty($data['payout_settings'][$key])) {
                    $json['type']         = 'error';
                    $json['title']      = esc_html__('Opps!', 'tuturn');
                    $json['message'] = $field['message'];
                     wp_send_json($json);
                }
            }
        }
        
        $update_data    = update_user_meta($user_id, 'tuturn_payout_method', $payout_method_arr);
        if(! is_wp_error($update_data)) {
            $json['type']       = 'success';
            $json['title']      = esc_html__('Woohoo!','tuturn');
            $json['message']    =  esc_html__('Record has been updated','tuturn');
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn');
            $json['message']    =  esc_html__('Something went wrong','tuturn');
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_tu_payout_settings', 'tu_payout_settings');
}

/**
 * Payout settings
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_withdraw_money')) {
    function tu_withdraw_money() {
        global $current_user, $tuturn_settings;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json               = array();
        $post_data          = !empty($_POST['data']) ? $_POST['data'] : '';
        parse_str($post_data, $data);
        $profileId          = !empty($data['profileId']) ? intval($data['profileId']) :'';       

        $payment_method     = !empty($data['withdraw']['gateway']) ? $data['withdraw']['gateway'] : '';
        $requested_amount   = !empty($data['withdraw']['amount']) ? floatval($data['withdraw']['amount']) : 0;
        $min_withdraw_amount = !empty($tuturn_settings['min_amount']) ? $tuturn_settings['min_amount'] : '';
        $userId             = !empty($current_user->ID) ? intval( $current_user->ID) : '';

        if (empty($payment_method) || empty($profileId)) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Something went wrong', 'tuturn'); 
            wp_send_json($json);
        }

        $linked_profile_id  = get_user_meta($userId, '_linked_profile', true);

        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($linked_profile_id);
        } //if user is not logged in and author check then prevent

        // verify requested amount is selected
        if ( empty($requested_amount) || $requested_amount < 1) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Hold right there!','tuturn');
            $json['message']    = esc_html__("Please make sure your entered amount should not be less than 1", 'tuturn');
            wp_send_json($json);
        }
        // get amount which is available to be withdraw
        $current_balance    = tuturn_account_details($userId,array('wc-completed'), array('completed'));
        $current_balance    = !empty($current_balance) ? $current_balance : 0;

        // get amount which is already withdrawn or withdraw requested
        $withdrawn_amount   = tuturn_account_withdraw_details($userId,array('pending','publish'));
        $withdrawn_amount   = !empty($withdrawn_amount) ? $withdrawn_amount : 0;
        $account_balance    = $current_balance - $withdrawn_amount;
        // verify amount before further process
        if ( $requested_amount > $account_balance) {
            $json['type']       = 'error';
            $json['amount']     = $requested_amount.'======='.$withdrawn_amount.'==='.$current_balance;
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__("We are sorry, you haven't enough amount to withdraw", 'tuturn');
            wp_send_json($json);
        }

        // verify minimum amount
        if ( $requested_amount <= 0) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__("We are sorry, you must select greater amount to process", 'tuturn');
            wp_send_json($json);
        }

        if(!empty($min_withdraw_amount) && $requested_amount < $min_withdraw_amount){
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = sprintf(__("Minimum withdraw amount limit is %s", 'tuturn'), $min_withdraw_amount);
            wp_send_json($json);
        }

        // get user's selected payment method details
        $contents   = get_user_meta($userId,'tuturn_payout_method',true);
        // get user's specific selected payment method details

        // if selected method is payoneer
        if( !empty($payment_method) && $payment_method === 'payoneer' ){
            if( !empty($contents) && array_key_exists($payment_method, $contents) ){
                $email      = !empty($contents['payoneer']['payoneer_email']) ? $contents['payoneer']['payoneer_email'] : "";
            }
            $insert_payouts     = serialize( array('payoneer_email' => $email) );
            //check if email is valid
            if( empty( $email ) || !is_email( $email ) ){
                $json['type']       = "error";
                $json['title']      = esc_html__('Failed!', 'tuturn');
                $json['message']    = esc_html__("Please update the payout settings for the selected payment gateway in payout settings", 'tuturn');
                wp_send_json($json);
            }
        } elseif ( !empty($payment_method) && $payment_method === 'paypal' ){
                    
            if( !empty($contents) && array_key_exists($payment_method, $contents) ){
                $email      = !empty($contents['paypal']['paypal_email']) ? $contents['paypal']['paypal_email'] : "";
            }
            $insert_payouts     = serialize( array('paypal_email' => $email) );
            //check if email is valid
            if( empty( $email ) || !is_email( $email ) ){
                $json['type']       = "error";
                $json['title']      = esc_html__('Failed!', 'tuturn');
                $json['message']    = esc_html__("Please update the payout settings for the selected payment gateway in payout settings", 'tuturn');
                wp_send_json($json);
            }
        } elseif( !empty($payment_method) && $payment_method === 'bank' ){
            // if selected method is bank
            if( !empty($contents) && array_key_exists($payment_method, $contents) ){
                if( empty( $contents['bank']['bank_account_title'] ) || empty( $contents['bank']['bank_account_number'] ) || empty( $contents['bank']['bank_account_name'] ) || empty( $contents['bank']['bank_routing_number'] ) || empty( $contents['bank']['bank_iban'] ) ){
                    $json['type']       = "error";
                    $json['title']      = esc_html__('Failed!', 'tuturn');
                    $json['message']    = esc_html__("One or more required fields are missing please update the payout settings for the selected payment gateway in payout settings", 'tuturn');
                    wp_send_json( $json );
                }
                $bank_details   = array();
                $bank_details['bank_account_title']     = $contents['bank']['bank_account_title'];
                $bank_details['bank_account_number']    = $contents['bank']['bank_account_number'];
                $bank_details['bank_account_name']      = $contents['bank']['bank_account_name'];
                $bank_details['bank_routing_number']    = $contents['bank']['bank_routing_number'];
                $bank_details['bank_iban']              = $contents['bank']['bank_iban'];
                $bank_details['bank_bic_swift']         = !empty($contents['bank']['bank_bic_swift']) ? $contents['bank']['bank_bic_swift'] : "";
                $bank_details                           = apply_filters('payout_bank_transfer_filter_details',$bank_details,$contents);
            }

            $insert_payouts = serialize( $bank_details );
        } else{
            // user do not have any selected payment method
            //check if email is valid
            $json['type']       = "error";
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__("Please update the payout settings for the selected payment gateway in payout settings", 'tuturn');
            wp_send_json( $json );
        }
        // prepare data to insert in withdraw post_type
        $unique_key       = tuturn_unique_increment(16);
        $account_details  = !empty($insert_payouts) ? $insert_payouts : array();
        $user_name        = !empty($userId) ? tuturn_get_username($profileId) . '-' . $requested_amount : '';
        $withdraw_post    = array(
            'post_title'    => wp_strip_all_tags($user_name),
            'post_status'   => 'pending',
            'post_author'   => $userId,
            'post_type'     => 'withdraw',
        );

        // record withdrawal request into withdraw post_type
        $withdrawal_post_id    = wp_insert_post($withdraw_post);
        $current_date          = current_time('mysql');
        // update relevant info in medata
        update_post_meta($withdrawal_post_id, '_withdraw_amount', $requested_amount);
        update_post_meta($withdrawal_post_id, '_payment_method', $payment_method);
        update_post_meta($withdrawal_post_id, '_timestamp', strtotime($current_date));
        update_post_meta($withdrawal_post_id, '_year', date('Y',strtotime($current_date)));
        update_post_meta($withdrawal_post_id, '_month', date('m',strtotime($current_date)));
        update_post_meta($withdrawal_post_id, '_account_details', $account_details);
        update_post_meta($withdrawal_post_id, '_unique_key', $unique_key);
        // send withdrawal email notification to admin

        if (class_exists('Tuturn_Email_Helper')) {
            if (class_exists('TuturnWithDrawStatuses')) {
                $emailData                          = array();
                $user_name                          = tuturn_get_username($profileId);
                $post_id                            = $profileId;
                $emailData['user_name']             = !empty($user_name) ? $user_name : '';
                $emailData['user_link']             = admin_url( 'post.php?post='.$post_id.'&action=edit');
                $emailData['amount']                = !empty($requested_amount) ? tuturn_price_format($requested_amount,'return') : '';
                $emailData['detail']                = admin_url( 'edit.php?post_type=withdraw&author='.$current_user->ID);
                $email_helper   = new TuturnWithDrawStatuses();
                $email_helper->withdraw_admin_email_request($emailData);
            }
        }
        // everything gone well, lets send success response to actual request
        $json['type']       = "success";
        $json['title']      = esc_html__('Woohoo!', 'tuturn');
        $json['message']    = esc_html__('Your withdrawal request has been submitted. We will process your withdrawal request.', 'tuturn');
        wp_send_json($json);
    }
    add_action('wp_ajax_tu_withdraw_money', 'tu_withdraw_money');

}

/**
 * Payout settings
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_default_payout')) {
    function tu_default_payout(){
        global $current_user;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $json               = array();
        $payout_method_arr  = array();
        $post_data          = !empty($_POST['data']) ? $_POST['data'] : '';
        $payout_type        = !empty($post_data['type'] ) ? sanitize_text_field($post_data['type']) : '';
        $user_id            = !empty($current_user->ID ) ? intval($current_user->ID) : '';

        if (empty($payout_type) || empty($user_id)) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Something went wrong', 'tuturn');
            wp_send_json($json);
        }

        $payout_method_arr = get_user_meta($user_id, 'tuturn_payout_method', true);

        if(empty($payout_method_arr) || !is_array($payout_method_arr)){
            $payout_method_arr = array();
        }

        $payout_method_arr['default']   = $payout_type;
        $update_data    = update_user_meta($user_id, 'tuturn_payout_method', $payout_method_arr);
        if(! is_wp_error($update_data)){
            $json['type']       = 'success';
            $json['title']      = esc_html__('Woohoo!', 'tuturn');
            $json['message']    = esc_html__('Record has been updated.!', 'tuturn');
            wp_send_json($json);

        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed.!', 'tuturn');
            $json['message']    = esc_html__('Something went wrong', 'tuturn');
            wp_send_json($json);
        }
       
    }

    add_action('wp_ajax_tu_default_payout', 'tu_default_payout');
}

/**
 * Booking cancel/Decline
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_decline_appointment')) {
    function tu_decline_appointment()
    {
        global $current_user, $tuturn_settings;

        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json           = array();
        $profile_id   = !empty($_POST['profile_id']) ? intval($_POST['profile_id']): '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $user_id        = !empty($current_user->ID) ? intval($current_user->ID) : '';
        $profileId      = get_user_meta($user_id, '_linked_profile', true);

        $validation_fields  = array(
            'decline_reason'        => esc_html__('Decline reason is required','tuturn'),
            'decline_reason_desc'   => esc_html__('Decline description is required','tuturn'),
            'booking_order_id'      => esc_html__('Something went wrong','tuturn'),
            'booking_action_type'   => esc_html__('Something went wrong','tuturn'),
        );

        $json               = array();
        $json['type']       = 'error';
        $json['title']      = esc_html__('Oops!', 'tuturn');
        $json['refund_error']   = 0;

        foreach($validation_fields as $data_key => $validation_field ){
            if( empty($_POST[$data_key]) ){
                $json['message']       = $validation_field;
                wp_send_json($json);
            }
        }

        $postId                 = !empty($_POST['booking_order_id']) ? sanitize_text_field($_POST['booking_order_id']) : '';
        $action_type            = !empty($_POST['booking_action_type']) ? sanitize_text_field($_POST['booking_action_type']) : '';
        $decline_reason         = !empty($_POST['decline_reason']) ? sanitize_text_field($_POST['decline_reason']) : '';
        $decline_reason_desc    = !empty($_POST['decline_reason_desc']) ? sanitize_text_field($_POST['decline_reason_desc']) : '';

        $order  = wc_get_order( $postId );
        $order_previous_status  = $order->get_status();

        if( ! is_a( $order, 'WC_Order') ) {
            $json['type']       = "error";
            $json['title']      = esc_html__('Oops!', 'tuturn');
            $json['message']    = esc_html__('Provided order is not a WC Order', 'tuturn');
            wp_send_json($json);
        }
        
        if( 'refunded' == $order_previous_status ) {
            $json['type']       = "error";
            $json['title']      = esc_html__('Oops!', 'tuturn');
            $json['message']    = esc_html__('Order has been already refunded', 'tuturn');
            wp_send_json($json);
        }
        
        $booking_date           = get_post_meta( $postId, '_booking_date',true );
        $booking_detail         = get_post_meta( $order->get_id(), 'cus_woo_product_data',true );
        $booked_data            = !empty($booking_detail['booked_data']) ? $booking_detail['booked_data'] : array();
        $student_id             = !empty($booking_detail['student_id']) ? $booking_detail['student_id'] : 0;
        $instructor_id          = !empty($booking_detail['instructor_id']) ? $booking_detail['instructor_id'] : 0;
       
        $booking_start_time    = '';
        $booking_end_time    = '';
        if(!empty($booked_data['booked_slots'])){                   
            $booking_start_timeslots    = tuturn_get_appointment_start_time($booked_data['booked_slots'], 'key_first');                   
            $first_appointment_date     = array_key_first($booked_data['booked_slots']);
            $booking_start_time         = !empty($booking_start_timeslots['start_time']) ? $first_appointment_date.' '.$booking_start_timeslots['start_time'] : '';
            $booking_end_time           = !empty($booking_start_timeslots['end_time']) ? $first_appointment_date.' '.$booking_start_timeslots['end_time'] : '';
            $booking_start_time         = date('Y-m-d H:i:s', strtotime($booking_start_time));
            $booking_last_timeslots     = tuturn_get_appointment_start_time($booked_data['booked_slots'], 'key_last');                   
            $last_appointment_date      = array_key_last($booked_data['booked_slots']);
            $booking_start_lastday_time = !empty($booking_last_timeslots['start_time']) ? $last_appointment_date.' '.$booking_last_timeslots['start_time'] : '';
            $booking_end_lastday_time   = !empty($booking_last_timeslots['end_time']) ? $last_appointment_date.' '.$booking_last_timeslots['end_time'] : '';
            $booking_end_lastday_time   = date('Y-m-d H:i:s', strtotime($booking_end_lastday_time));
        }

        $gmt_time               = current_time( 'mysql', 1 );
        $gmt_time               = date('Y-m-d H:i:s', strtotime($gmt_time));
        $booking_status         = get_post_meta( $postId, 'booking_status',true );

        if(strtotime($booking_start_time) > strtotime($gmt_time)){
            $student_profile_id         = tuturn_get_linked_profile_id( $student_id );
            $instructor_profile_id      = tuturn_get_linked_profile_id( $instructor_id );
            /* instructor details */
            $instructor_profileData     = get_post_meta($instructor_profile_id, 'profile_details', true);
            $instructor_name            = !empty($instructor_profileData['first_name']) ? $instructor_profileData['first_name'] : '';
            $instructor_contact_detail  = !empty($instructor_profileData['contact_info']) ? $instructor_profileData['contact_info'] : array();
            /* student details */
            $student_profileData        = get_post_meta($student_profile_id, 'tuturn_options', true);
            $student_name               = !empty($student_profileData['first_name']) ? $student_profileData['first_name'] : '';
            $student_contact_detail     = !empty($student_profileData['contact_info']) ? $student_profileData['contact_info'] : array();
            $student_data               = get_userdata($student_id);
            $studentprofile_name        = !empty($student_data->display_name) ? $student_data->display_name : '';
            $studentprofile_email       = !empty($student_data->user_email) ? $student_data->user_email : '';
            $instructor_data            = get_userdata($instructor_id);
            $instructorprofile_name     = !empty($instructor_data->display_name) ? $instructor_data->display_name : '';
            $instructorprofile_email    = !empty($instructor_data->user_email) ? $instructor_data->user_email : '';

            if(!empty($action_type) && $action_type == 'decline'){
                $status = 'declined';
                $json['message']    = esc_html__('Your booking has been declined.', 'tuturn');
                /* email on decline booking */
                if (class_exists('Tuturn_Email_helper')) {
                    $emailData  = array();
                    if (class_exists('tuturnOrderStatuses')) {
                        $email_helper = new tuturnOrderStatuses();
                        $emailData['student_email']         = !empty($student_contact_detail['email']) ? $student_contact_detail['email'] : $studentprofile_email;
                        $emailData['decline_reason']    = $decline_reason;
                        $emailData['student_name']      = !empty($student_name) ? $student_name : $studentprofile_name;
                        $emailData['instructor_name']   = !empty($instructor_name) ? $instructor_name : $instructorprofile_name;
                        $emailData['instructor_email']  = !empty($instructor_contact_detail['email']) ? $instructor_contact_detail['email'] : $instructorprofile_email;
                        $emailData['decline_desc']      = $decline_reason_desc;
                        $emailData['order_id']          = !empty($order->get_id()) ? $order->get_id() : 0;
                        if(!empty($tuturn_settings['email_booking_decline_student'])){
                            $emailData['login_url'] = tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $student_id, true, 'listings');
                            $email_helper->booking_decline_student_email($emailData);
                        }
                        if(!empty($tuturn_settings['email_booking_decline_instructor'])){
                            $emailData['login_url'] = tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $instructor_id, true, 'listings');
                            $email_helper->booking_decline_instructor_email($emailData);
                        }
                    }
                }
            } else {
                $status = 'cancelled';      
                $json['message']    = esc_html__('Your booking has been cancelled.', 'tuturn');
                /* email to instructor on cancel booking by student */
                if (class_exists('Tuturn_Email_helper')) {
                    $emailData  = array();
                    if (class_exists('tuturnOrderStatuses')) {
                        $email_helper = new tuturnOrderStatuses();
                        $emailData['student_email']     = !empty($student_contact_detail['email']) ? $student_contact_detail['email'] : $studentprofile_email;
                        $emailData['cancel_reason']     = $decline_reason;
                        $emailData['student_name']      = !empty($student_name) ? $student_name : $studentprofile_name;
                        $emailData['instructor_name']   = !empty($instructor_name) ? $instructor_name : $instructorprofile_name;
                        $emailData['instructor_email']  = !empty($instructor_contact_detail['email']) ? $instructor_contact_detail['email'] : $instructorprofile_email;
                        $emailData['cancel_desc']       = $decline_reason_desc;
                        $emailData['order_id']          = !empty($order->get_id()) ? $order->get_id() : 0;
                        
                        if(!empty($tuturn_settings['email_booking_cancel_instructor'])){
                            $emailData['login_url']         = tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $instructor_id, true, 'listings');
                            $email_helper->booking_cancel_instructor_email($emailData);
                        }
                        if(!empty($tuturn_settings['email_booking_cancel_student'])){
                            $emailData['login_url']         = tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $student_id, true, 'listings');
                            $email_helper->booking_cancel_student_email($emailData);
                        }
                    }
                }   
            }

            update_post_meta( $postId, 'booking_status', $status ); 
            update_post_meta( $postId, 'decline_reason', $decline_reason ); 
            update_post_meta( $postId, 'decline_description', $decline_reason_desc );
            $order_status   = 'cancelled';
            $order = wc_get_order($postId);
            $order->set_status($order_status);
            $order->save();
            if( 'refunded' !== $order_previous_status &&  $order_previous_status == 'completed') {
                if(function_exists('tuturn_wc_refund_order')){
                    $refund = tuturn_wc_refund_order($order->get_id(), $decline_reason);
                    $json['refund']     = $refund;
                    if ( is_wp_error( $refund ) ) {
                        $json['type']       = "error";
                        $json['title']      = esc_html__('Oops!', 'tuturn');
                        $json['refund']     = $refund;
                        $json['refund_error']   = 1;
                        $json['message']    .= esc_html__('There is error while refund request. Please contact administrator from dashboard help & support', 'tuturn');
                        wp_send_json($json);
                    } else {
                        $json['message']    .= esc_html__('Your payment has been refunded.', 'tuturn');
                        $json['refund_error']   = 0;
                        /* email to student on refunded */
                        if (class_exists('Tuturn_Email_helper')) {
                            $emailData  = array();
                            if (class_exists('TuturnRefundsStatuses')) {
                                $email_helper = new TuturnRefundsStatuses();
                                $emailData['student_email']         = !empty($student_contact_detail['email']) ? $student_contact_detail['email'] : $studentprofile_email;
                                $emailData['student_name']          = !empty($student_name) ? $student_name : $studentprofile_name;
                                $emailData['instructor_name']       = !empty($instructor_name) ? $instructor_name : $instructorprofile_name;
                                $emailData['instructor_email']      = !empty($instructor_contact_detail['email']) ? $instructor_contact_detail['email'] : $instructorprofile_email;
                                $emailData['order_id']              = !empty($order->get_id()) ? $order->get_id() : 0;
                                $emailData['login_url']             = Tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $student_id, true, 'listings');
                                if(!empty($tuturn_settings['email_refund_approv_student'])){
                                    $email_helper->refund_approved_student_email($emailData);
                                }
                            }
                        }
                    }
                }                       
            }
            $json['type']       = "success";
            $json['title']      = esc_html__('Woohoo!', 'tuturn');
            wp_send_json($json);
        } else {
            $json['type']       = "error";
            $json['title']      = esc_html__('Oops!', 'tuturn');
            $json['message']    = esc_html__('Booking date has been expired. ', 'tuturn');
            wp_send_json($json);
        }   
    }
    add_action('wp_ajax_tu_decline_appointment', 'tu_decline_appointment');
}

/**
 * delete account
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'tuturn_delete_account' ) ) {

	function tuturn_delete_account() {
		global $current_user;
		
		//security check
		$json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }
		
		if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

		$post_id	= tuturn_get_linked_profile_id($current_user->ID);
		$user 		= wp_get_current_user(); //trace($user);
		$json 		= array();
        $useremail	= $user->user_email;
        $username   = tuturn_get_username($post_id);

		$required = array(
            'reason' 		=> esc_html__('Select reason to delete your account', 'tuturn'),
            'password'   	=> esc_html__('Password is required', 'tuturn'),
        );

        foreach ($required as $key => $value) {
           if( empty( $_POST[$key] ) ){
            $json['type'] = 'error';
            $json['message'] = $value;        
            wp_send_json($json);
           }
        }

		$is_password = wp_check_password($_POST['password'], $user->user_pass, $user->data->ID);

		if( $is_password ){
			wp_delete_user($user->data->ID);
			wp_delete_post($post_id,true);
			extract($_POST);

			//Send email to users
            if (class_exists('Tuturn_Email_helper')) {
                $emailData  = array();
    
                if (class_exists('TuturnRegistrationEmail')) {
                    $blogname = get_option('blogname');
                    
                    $email_helper               = new TuturnRegistrationEmail();
                    $emailData['reason']        = !empty($reason) ? $reason : '';
                    $emailData['comments']      = !empty($comments) ? $comments : '';
                    $emailData['username']      = !empty($username) ? $username : '';
                    $emailData['useremail']     = !empty($useremail) ? $useremail : '';
                    $emailData['site']          = $blogname;
                    $email_helper->delete_account($emailData);
                }
            }

			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('You account has been deleted.', 'tuturn');
			$json['redirect'] 	= esc_url(home_url('/'));
			wp_send_json( $json );
		} else{
			$json['type'] = 'error';
			$json['message'] = esc_html__('Password doesn\'t match', 'tuturn');
			wp_send_json( $json );
		}
	}

	add_action( 'wp_ajax_tuturn_delete_account', 'tuturn_delete_account' );
}

/**
 * Approve/Complete appointments
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_booking_appointment')) {
    function tu_booking_appointment(){
        global $current_user;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json           = array();
        $profile_id   = !empty($_POST['profile_id']) ? intval($_POST['profile_id']): '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $postId         = !empty($_POST['postId'] ) ? intval($_POST['postId']) : '';
        $action_type    = !empty($_POST['action_type'] ) ? sanitize_text_field($_POST['action_type']) : '';
        $user_rating    = !empty($_POST['rating'] ) ? intval($_POST['rating']) : '';
        $user_id        = !empty($current_user->ID) ? intval($current_user->ID) : '';
        $profileId      = get_user_meta($user_id, '_linked_profile', true);

        if(empty($postId) || empty($action_type)){
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Something went wrong', 'tuturn'); 
            wp_send_json($json);
        } else {            
            $order                          = wc_get_order($postId);
            $order_previous_status          = !empty($order) ? $order->get_status() : '';
            $booking_date                   = get_post_meta( $postId, '_booking_date',true );
            $booking_detail                 = get_post_meta( $order->get_id(), 'cus_woo_product_data',true );
            $booked_data                    = !empty($booking_detail['booked_data']) ? $booking_detail['booked_data'] : array();
            $booking_start_time             = '';
            $booking_end_time               = '';
            $booking_start_lastday_time     = '';
            $booking_end_lastday_time       = '';

            if(!empty($booked_data['booked_slots'])){                   
                $booking_start_timeslots    = tuturn_get_appointment_start_time($booked_data['booked_slots'], 'key_first');                   
                $first_appointment_date     = array_key_first($booked_data['booked_slots']);
                $booking_start_time         = !empty($booking_start_timeslots['start_time']) ? $first_appointment_date.' '.$booking_start_timeslots['start_time'] : '';
                $booking_end_time           = !empty($booking_start_timeslots['end_time']) ? $first_appointment_date.' '.$booking_start_timeslots['end_time'] : '';
                $booking_start_time         = date('Y-m-d H:i:s', strtotime($booking_start_time));
                $booking_last_timeslots     = tuturn_get_appointment_start_time($booked_data['booked_slots'], 'key_last');                   
                $last_appointment_date      = array_key_last($booked_data['booked_slots']);
                $booking_start_lastday_time = !empty($booking_last_timeslots['start_time']) ? $last_appointment_date.' '.$booking_last_timeslots['start_time'] : '';
                $booking_end_lastday_time   = !empty($booking_last_timeslots['end_time']) ? $last_appointment_date.' '.$booking_last_timeslots['end_time'] : '';
                $booking_end_lastday_time   = date('Y-m-d H:i:s', strtotime($booking_end_lastday_time));
            }
            
            $gmt_time                       = current_time( 'mysql', 1 );
            $gmt_time                       = date('Y-m-d H:i:s', strtotime($gmt_time));
            $booking_status                 = get_post_meta( $postId, 'booking_status',true );
            $instructor_id                  = get_post_meta( $postId, 'instructor_id',true );
            $instructor_profileId           = tuturn_get_linked_profile_id( $instructor_id );
            $instructor_data                = get_userdata($instructor_id);
            $instructorprofile_name         = !empty($instructor_data->display_name) ? $instructor_data->display_name : '';
            $instructorprofile_email        = !empty($instructor_data->user_email) ? $instructor_data->user_email : '';
            $student_id                     = !empty($booking_detail['student_id']) ? $booking_detail['student_id'] : 0;
            $student_data                   = get_userdata($student_id);
            $studentprofile_name                = !empty($student_data->display_name) ? $student_data->display_name : '';
            $studentprofile_email           = !empty($student_data->user_email) ? $student_data->user_email : '';
            /* instructor details */
            $instructor_profileData         = get_post_meta($instructor_profileId, 'tuturn_options', true);
            $instructor_name                    = !empty($instructor_profileData['first_name']) ? $instructor_profileData['first_name'] : '';
            $instructor_contact_detail      = !empty($instructor_profileData['contact_info']) ? $instructor_profileData['contact_info'] : array();
            /* student details */
            $student_profileData            = get_post_meta($profileId, 'tuturn_options', true);
            $student_name                   = !empty($student_profileData['first_name']) ? $student_profileData['first_name'] : '';
            $student_contact_detail         = !empty($student_profileData['contact_info']) ? $student_profileData['contact_info'] : array();

            if(!empty($action_type) && $action_type == 'complete'){

                if(strtotime($booking_end_lastday_time) < strtotime($gmt_time)){
                    $validations = array(
                        'rating'        => esc_html__('Please click on stars to add rating', 'tuturn'),
                        'reviews_content'   => esc_html__('Please enter reviews description', 'tuturn'),
                        'termsconditions'   => esc_html__('You must agree terms and condition to add review', 'tuturn'),
                        'user_id'       => esc_html__('Something went wrong, please try again', 'tuturn'),
                        'profile_id'    => esc_html__('Something went wrong, please try again', 'tuturn'),
                    );
                    foreach ($validations as $key => $value) {
                        if (isset($_POST[$key]) && empty($_POST[$key])) {
                            $json['title']      = esc_html__("Oops!", 'tuturn');
                            $json['type']       = 'error';
                            $json['message']    = $value;
                            wp_send_json($json);
                        }
                    }

                    if(!empty($user_rating)){

                        $content        = !empty($_POST['reviews_content']) ?  sanitize_textarea_field($_POST['reviews_content'])  : '';
                        $user_id        = !empty($_POST['user_id']) ? intval($_POST['user_id'])  : '';
                        $profile_id     = !empty($_POST['profile_id']) ? intval($_POST['profile_id'])  : '';
                        $rating         = !empty($_POST['rating']) ? intval($_POST['rating'])  : '';
                        $userdata       = !empty($user_id)  ? get_userdata( $user_id ) : array();
                        $user_email     = !empty($userdata) ? $userdata->user_email : '';
                        $user_name      = !empty($userdata) ? $userdata->display_name : '';
                        $time           = current_time('mysql');
                        $comment_data   = array(
                            'comment_post_ID'           => $instructor_profileId,
                            'comment_author'            => $user_name,
                            'comment_author_email'      => $user_email,
                            'comment_author_url'        => 'http://',
                            'comment_content'           => $content,
                            'comment_type'              => 'instructor_reviews',
                            'comment_parent'            => 0,
                            'user_id'                   => $user_id,
                            'comment_date'              => $time,
                            'comment_approved'          => 1,
                        );
                        // insert data
                        $comment_id = wp_insert_comment($comment_data);

                        if (!empty($comment_id)) {
                            update_comment_meta($comment_id, 'rating', $rating);
                            $tu_total_rating        = get_post_meta( $instructor_profileId, 'tu_total_rating', true );
                            $tu_total_rating        = !empty($tu_total_rating) ? $tu_total_rating : 0;
                            $tu_review_users        = get_post_meta( $instructor_profileId, 'tu_review_users', true );
                            $tu_review_users        = !empty($tu_review_users) ? $tu_review_users : 0;
                            $tu_total_rating        = $tu_total_rating + $rating;
                            $tu_review_users++;
                            $tu_average_rating      = ($tu_total_rating / $tu_review_users);
                            update_post_meta( $instructor_profileId, 'tu_average_rating', $tu_average_rating );
                            update_post_meta( $instructor_profileId, 'tu_total_rating', $tu_total_rating );
                            update_post_meta( $instructor_profileId, 'tu_review_users', $tu_review_users );
                        } else {
                            $json['title']          = esc_html__("Oops!", 'tuturn');
                            $json['type']       = "error";
                            $json['message']    = esc_html__("Something went wrong, please try again.", 'tuturn');
                            wp_send_json($json, 203);
                        }
                    }

                    /* Email to instructor on appointment/booking complete */
                    if (class_exists('Tuturn_Email_helper')) {
                        $emailData  = array();

                        if (class_exists('TuturnOrderStatuses')) {
                            $email_helper                   = new TuturnOrderStatuses();
                            $login_url                      =  Tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $user_id, true, '');
                            $emailData['instructor_email']  = !empty($instructor_contact_detail['email']) ? $instructor_contact_detail['email'] : $instructorprofile_email;
                            $emailData['instructor_name']   = !empty($instructor_name) ? $instructor_name : $instructorprofile_name;
                            $emailData['student_name']      = !empty($student_name) ? $student_name : $studentprofile_name;
                            $emailData['student_email']     = !empty($student_contact_detail['email']) ? $student_contact_detail['email'] : $studentprofile_email;
                            $emailData['order_id']          = !empty($order->get_id()) ? $order->get_id() : 0;
                            $emailData['student_rating']    = !empty($user_rating) ? $user_rating : 0;
                            $emailData['login_url']         = $login_url;
                            $email_helper->booking_complete_instructor_email($emailData);
                        }
                    }

                    $status = 'completed';
                    $json['message']    = esc_html__('Your booking has been completed.', 'tuturn');
                } else {
                    $json['type']       = "error";
                    $json['title']      = esc_html__('Oops!', 'tuturn');
                    $json['message']    = esc_html__('After appointment date you can complete your booking.. ', 'tuturn');
                    wp_send_json($json);
                }

                if($order_previous_status == 'pending' || $order_previous_status == 'processing' || $order_previous_status == 'on-hold'){
                $order_status   = 'completed';
            }
            
            //Update status
            if(!empty($order_status) && ($booking_status == 'pending' || $booking_status == 'publish')){
                $order = wc_get_order($postId);
                $order->set_status($order_status);
                $order->save();
            }
                
            } elseif(!empty($action_type) && $action_type == 'approve'){

                if(strtotime($booking_start_time) > strtotime($gmt_time)){
                    /* Email to student on approve booking */
                    if (class_exists('Tuturn_Email_helper')) {
                        $emailData  = array();
                        if (class_exists('TuturnOrderStatuses')) {
                            $email_helper                   = new TuturnOrderStatuses();
                            $login_url                      = Tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $user_id, true, '');
                            $emailData['instructor_email']  = !empty($instructor_contact_detail['email']) ? $instructor_contact_detail['email'] : $instructorprofile_email;
                            $emailData['instructor_name']   = !empty($instructor_name) ? $instructor_name : $instructorprofile_name;
                            $emailData['student_name']      = !empty($student_name) ? $student_name : $studentprofile_name;
                            $emailData['student_email']     = !empty($student_contact_detail['email']) ? $student_contact_detail['email'] : $studentprofile_email;
                            $emailData['order_id']          = !empty($order->get_id()) ? $order->get_id() : 0;
                            $emailData['login_url']         = $login_url;
                            $email_helper->booking_approve_student_email($emailData);
                        }
                    }

                    $status             = 'publish';        
                    $json['message']    = esc_html__('Your booking has been approved.', 'tuturn');              
                } else {
                    $json['type']       = "error";
                    $json['title']      = esc_html__('Oops!', 'tuturn');
                    $json['message']    = esc_html__('Booking date has been expired. ', 'tuturn');
                    wp_send_json($json);
                }
            }

            update_post_meta( $postId, 'booking_status', $status ); 

            $json['type']       = "success";
            $json['title']      = esc_html__('Woohoo!', 'tuturn');
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_tu_booking_appointment', 'tu_booking_appointment');
}

/**
 * Meeting detail
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_meeting_detail')) {
    function tu_meeting_detail(){
        global $current_user;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $json           = array();
        
        $profile_id   = !empty($_POST['profile_id']) ? intval($_POST['profile_id']): '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $post_data      = !empty($_POST['data']) ? $_POST['data']: array();
        parse_str($post_data,$post_data);
        $postId         = !empty($post_data['postId'] ) ? intval($post_data['postId']) : '';
        $meeting_type   = !empty($post_data['meeting_type']) ?  sanitize_text_field($post_data['meeting_type'])  : '';
        $meeting_url    = !empty($post_data['meeting_url']) ?  sanitize_url($post_data['meeting_url'])  : '';
        $meeting_desc   = !empty($post_data['meeting_description']) ?  sanitize_textarea_field($post_data['meeting_description'])  : '';
        $user_id        = !empty($current_user->ID) ? intval($current_user->ID) : '';
        $profileId      = get_user_meta($user_id, '_linked_profile', true);
        $validations = array(
            'postId'        => esc_html__('Something went wrong', 'tuturn'),
            'meeting_type'  => esc_html__('Please select meeting type.', 'tuturn'),
            'meeting_url'   => esc_html__('Meeting url is missing.', 'tuturn'),            
        );

        foreach ( $validations as $key => $value ) {
            if ( empty( $post_data[$key] ) ) {
                $json['title']      = esc_html__("Oops!", 'tuturn');
                $json['type']       = 'error';
                $json['message']    = $value;
                wp_send_json($json, 203);
            }
        }
        $meeting_detail = array();
        $current_date   = date("F j, Y");
        $meeting_detail = array(
            'meeting_type'    => $meeting_type,
            'meeting_url'     => $meeting_url,
            'meeting_desc'    => $meeting_desc,
            'meeting_date'    => $current_date,
        );
        $meeting_data   = update_post_meta($postId, 'meeting_detail', $meeting_detail);
        if ( ! is_wp_error( $meeting_data ) ) {
            $json['type']       = "success";

              /* Email to student on approve booking */
              if (class_exists('Tuturn_Email_helper')) {
                $emailData  = array();
                if(!empty($meeting_type) && $meeting_type=='zoom_meet'){
                    $meeting_type   = 'Zoom meeting';
                }
                if(!empty($meeting_type) && $meeting_type=='google_meet'){
                    $meeting_type   = 'Google meet';
                }
                if(!empty($meeting_type) && $meeting_type=='other'){
                    $meeting_type   = 'Other';
                }

                $student_id         = get_post_meta( $postId, 'student_id',true );
                $instructor_id      = get_post_meta( $postId, 'instructor_id',true );

                
                $student_info       = get_userdata($student_id);
                $instructor_info    = get_userdata($instructor_id);
                $student_email      = !empty($student_info) ? $student_info->user_email : '';
                $instructor_email   = !empty($instructor_info) ? $instructor_info->user_email : '';
                $student_name       = !empty($student_info) ? $student_info->display_name : '';


                if (class_exists('Tuturnmeetingdetail')) {
                    $email_helper = new Tuturnmeetingdetail();
                    $emailData['order_id']           = !empty($postId) ? esc_html($postId) : '';
                    $emailData['student_name']      = !empty($student_name) ? esc_html($student_name) : ''; 
                    $emailData['meeting_type']      = !empty($meeting_type) ? esc_html($meeting_type) : '';
                    $emailData['meeting_url']       = !empty($meeting_url) ? esc_url($meeting_url) : '';
                    $emailData['meeting_desc']      = !empty($meeting_desc) ? esc_html($meeting_desc) : '';
                    $emailData['current_date']      = !empty($current_date) ? do_shortcode($current_date) : '';
                    $emailData['student_email']     = !empty($student_email) ? $student_email : '';
                    $emailData['instructor_email']  = !empty($instructor_email) ? $instructor_email : ''; 
                    $email_helper->new_meeting_detail($emailData);
                }
            }
            $json['title']      = esc_html__('Woohoo!', 'tuturn');
            $json['message']    = esc_html__('Meeting detail has been updated.!','tuturn'); 
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['title']      =  esc_html__('Failed!','tuturn');
            $json['message']    = esc_html__('Something went wrong','tuturn');  
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_tu_meeting_detail', 'tu_meeting_detail');
}


/**
 * Update identity
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_update_identity')) {
    function tu_update_identity(){
        global $current_user,$tuturn_settings;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json               = array();
        $profile_id   = !empty($_POST['profile_id']) ? intval($_POST['profile_id']): '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $post_data          = !empty($_POST['data']) ? $_POST['data']: array();
        $post_attachments   = !empty($_POST['attachments']) ? $_POST['attachments']: array();
        $user_id        = !empty($current_user->ID) ? intval($current_user->ID) : '';
        parse_str($post_data,$post_data);

        $name            = !empty($post_data['name']) ?  sanitize_text_field($post_data['name'])  : '';
        $phone           = !empty($post_data['phone']) ?  sanitize_text_field($post_data['phone'])  : '';
        $email_address   = !empty($post_data['email_address']) ?  sanitize_email($post_data['email_address'])  : '';
        $address         = !empty($post_data['address'] ) ? sanitize_text_field($post_data['address']) : '';
        $verification_number   = !empty($post_data['verification_number']) ?  sanitize_text_field($post_data['verification_number'])  : '';
         $validations = array(
            'name'                  => esc_html__('Name is required.', 'tuturn'),
            'phone'                 => esc_html__('Phone number is required.', 'tuturn'),
            'address'               => esc_html__('Address is required.', 'tuturn'),     
            'email_address'         => esc_html__('Email address is missing.', 'tuturn'),                   
            'verification_number'   => esc_html__('Verfication number is required.', 'tuturn'),            
         );

        foreach ( $validations as $key => $value ) {
            if ( empty( $post_data[$key] ) ) {
                $json['title']      = esc_html__("Oops!", 'tuturn');
                $json['type']       = 'error';
                $json['message']    = $value;
                wp_send_json($json);
            }
        }
        if(empty($post_attachments)){
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['type']       = 'error';
            $json['message']    = $value;
            wp_send_json($json);
        }
        $identity_array                                 = array();
        $files                                          = !empty($post_attachments ) ? $post_attachments : array();
        $identity_array['info']['name']                 = !empty($name) ? sanitize_text_field($name) : '';
        $identity_array['info']['contact_number']       = !empty($phone) ? sanitize_text_field($phone) : '';
        $identity_array['info']['email_address']        = !empty($email_address) ? ($email_address) : '';
        $identity_array['info']['verification_number']  = !empty($verification_number ) ? sanitize_text_field($verification_number) : '';
        $identity_array['info']['address']              = !empty($address ) ? sanitize_textarea_field($address) : '';
        $profile_id                                     = tuturn_get_linked_profile_id($user_id);

        if (!empty($files)) {
            $attachment_record     = array();
            foreach( $files as $file_url ){
                $new_attachemt  = tuturn_temp_upload_to_media($file_url, $profile_id);
                $attachment_record[]  = array(
                    'url'           => $new_attachemt['url'],
                    'name'          => $new_attachemt['name'],
                    'attachment_id' => $new_attachemt['attachment_id'],
                );
            }
            $identity_array['attachments']  = $attachment_record;
        }
        $update_identity    = update_user_meta($user_id,'verification_attachments',$identity_array);
        if ( ! is_wp_error( $update_identity ) ) {
            update_user_meta($user_id,'identity_verified',0);
            update_post_meta($profile_id,'identity_verified','no'); 

            if (class_exists('Tuturn_Email_helper')) {
                $emailData  = array();
                if (class_exists('TuturnRegistrationEmail')) {
                    $email_helper               = new TuturnRegistrationEmail();     
                    $emailData['user_name']     = esc_html($name) ;
                    $emailData['user_email']    = esc_html($email_address) ;
 
                    if ( !empty($tuturn_settings['email_identity_submision_request_admin'])){
                        $email_helper->user_identification_request($emailData);
                    }
                }
            }

            $json['type']       = 'success';
            $json['title']      = esc_html__("Woohoo!", 'tuturn');
            $json['message']    = esc_html__('Your identity verfication document has been submitted','tuturn');
            wp_send_json($json);
        } else {
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['type']       = 'error';
            $json['message']    = esc_html__('Something went wrong','tuturn');
            wp_send_json($json);
        }
            

    }
    add_action('wp_ajax_tu_update_identity', 'tu_update_identity');
}



/**
 * User identity verification
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tu_user_identity')) {
    function tu_user_identity(){
        global $current_user,$tuturn_settings;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        
        $json       = array();
        $profile_id = !empty($_POST['profile_id']) ? intval($_POST['profile_id']): '';

        $post_data          = !empty($_POST['data']) ? $_POST['data']: array();
        $post_attachments   = !empty($_POST['attachments']) ? $_POST['attachments']: array();
        $personal_photo     = !empty($_POST['profile_photo']) ? $_POST['profile_photo']: array();
        $user_type          = !empty($_POST['user_type']) ? $_POST['user_type']: '';
        $user_id            = !empty($current_user->ID) ? intval($current_user->ID) : '';
        $check_userType     = apply_filters('tuturnGetUserType', $user_id);
        $profileId          = tuturn_get_linked_profile_id( $user_id );
        $parental_consent   = !empty($tuturn_settings['parental_consent']) ? $tuturn_settings['parental_consent'] : 'no';
        $student_fields     = !empty($tuturn_settings['student_fields']) ? $tuturn_settings['student_fields'] : array();
        $verification_terms = !empty($tuturn_settings['verification_terms']) ? $tuturn_settings['verification_terms'] : '';
        
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profileId);
        } //if user is not logged in and author check then prevent

        parse_str($post_data,$post_data);

        $name                   = !empty($post_data['name']) ?  sanitize_text_field($post_data['name'])  : '';
        $phone_number           = !empty($post_data['phone_number']) ?  sanitize_text_field($post_data['phone_number'])  : '';
        $email_address          = !empty($post_data['email_address']) ?  sanitize_email($post_data['email_address'])  : '';
        $address                = !empty($post_data['address'] ) ? sanitize_text_field($post_data['address']) : '';
        $gender                 = !empty($post_data['gender'] ) ? sanitize_text_field($post_data['gender']) : '';
        $other_introduction     = !empty($post_data['other_introduction'] ) ? sanitize_textarea_field($post_data['other_introduction']) : '';
        $terms                  = !empty($post_data['terms']) ?  sanitize_text_field($post_data['terms'])  : '';
        $verification_number    = !empty($post_data['verification_number']) ?  sanitize_text_field($post_data['verification_number'])  : '';

        /* Only for student */
        $student_number     = !empty($post_data['student_number'] ) ? sanitize_text_field($post_data['student_number']) : '';
        $school             = !empty($post_data['school'] ) ? sanitize_text_field($post_data['school']) : '';
        $parent_name        = !empty($post_data['parent_name'] ) ? sanitize_text_field($post_data['parent_name']) : '';
        $parent_phone       = !empty($post_data['parent_phone']) ?  sanitize_text_field($post_data['parent_phone'])  : '';
        $parent_email       = !empty($post_data['parent_email']) ?  sanitize_email($post_data['parent_email'])  : '';

        if(!empty($user_type)){
            if($user_type != $check_userType){
                $json['title']      = esc_html__("Oops!", 'tuturn');
                $json['type']       = 'error';
                $json['message']    = esc_html__('Something went wrong','tuturn');
                wp_send_json($json);
            }
        } else {
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['type']       = 'error';
            $json['message']    = esc_html__('Something went wrong..!!','tuturn');
            wp_send_json($json);
        }

        $validations = array(
            'name'                  => esc_html__('Name is required', 'tuturn'),
            'email_address'         => esc_html__('Email address is missing', 'tuturn'),     
            'gender'                => esc_html__('Please add your gender', 'tuturn'),            
            'verification_number'   => esc_html__('CNIC/Passport/NIN/SSN number is missing', 'tuturn'),           
        );

        if (!empty($verification_terms)) {
            $validations['terms']              = esc_html__('Term & condition is required', 'tuturn');
        }
        
        if($user_type === 'student' && $parental_consent === 'yes'){
            if(!empty($student_fields)){
                if(in_array('school', $student_fields)){
                    $validations['school']              = esc_html__('School is required', 'tuturn');
                } 
                if(in_array('parent_name', $student_fields)){
                    $validations['parent_name']         = esc_html__('Parent name is required', 'tuturn');
                } 
                if(in_array('parent_phone', $student_fields)){
                    $validations['parent_phone']        = esc_html__('Parent phone is required', 'tuturn');
                }
                if(in_array('parent_email', $student_fields)){
                    $validations['parent_email']        = esc_html__('Parent email is required', 'tuturn');
                }
            }
        }
        
        foreach ( $validations as $key => $value ) {
            if(!empty($key) && $key == 'terms' && $post_data[$key] == 'no'){
                $json['title']      = esc_html__("Oops!", 'tuturn');
                $json['type']       = 'error';
                $json['message']    = $value;
                wp_send_json($json);
            }else if ( empty( $post_data[$key] ) ) {
                $json['title']      = esc_html__("Oops!", 'tuturn');
                $json['type']       = 'error';
                $json['message']    = $value;
                wp_send_json($json);
            }
        }

        $identity_array = array();
        $files                                          = !empty($post_attachments ) ? $post_attachments : array();
        $personal_photo                                 = !empty($personal_photo ) ? $personal_photo : array();
        $identity_array['info']['name']                 = !empty($name) ? sanitize_text_field($name) : '';
        $identity_array['info']['gender']               = !empty($gender) ? sanitize_text_field($gender) : '';
        $identity_array['info']['address']              = !empty($address ) ? sanitize_text_field($address) : '';
        $identity_array['info']['email_address']        = !empty($email_address) ? ($email_address) : '';
        $identity_array['info']['other_introduction']   = !empty($other_introduction ) ? sanitize_text_field($other_introduction) : '';
        $identity_array['info']['verification_number']   = !empty($verification_number ) ? sanitize_text_field($verification_number) : '';
        $profile_id                                     = tuturn_get_linked_profile_id($user_id);
        $verfication_post_name                          = !empty($name) ? sanitize_text_field($name) : '';
        $verfication_post_content                       = !empty($other_introduction) ? sanitize_text_field($other_introduction) : '';

        $identity_array['info']['phone_number']         = !empty($phone_number) ? sanitize_text_field($phone_number) : '';
        $identity_array['info']['user_type']            = esc_html($user_type);
        $identity_array['info']['profile_id']           = intval($profile_id); 
        $identity_array['info']['terms']                = !empty($terms) ? $terms : 'no';
        
        /* only for student */
        if($user_type === 'student' && $parental_consent === 'yes'){
            $identity_array['info']['student_number']       = !empty($student_number) ? sanitize_text_field($student_number) : '';
            $identity_array['info']['school']               = !empty($school) ? sanitize_text_field($school) : '';
            $identity_array['info']['parent_name']          = !empty($parent_name) ? sanitize_text_field($parent_name) : '';
            $identity_array['info']['parent_phone']         = !empty($parent_phone) ? sanitize_text_field($parent_phone) : '';
            $identity_array['info']['parent_email']         = !empty($parent_email) ? sanitize_text_field($parent_email) : '';  
        }

        $random_key             = tuturnGenerateRandomString(10);

        $verification_post    = array(
            'post_title'    => wp_strip_all_tags($verfication_post_name),
            'post_status'   => 'draft',
            'post_author'   => $user_id,
            'post_type'     => 'user-verification',
        );

        // Verfication Post type record
        $verification_post_id   = wp_insert_post($verification_post);
        $photo_email            = '';
        $attachment_email       = '';
        if ( !empty($verification_post_id ) ) {
            // attachment and images add in indentity array 
            $attachment_record      = array();
            if (!empty($files)) {
                $attachment_email       .= '<ul>';
                foreach( $files as $file_url ){
                    $new_attachemt  = tuturn_temp_upload_to_media($file_url, $verification_post_id);
                    $attachment_record[]  = array(
                        'url'           => $new_attachemt['url'],
                        'name'          => $new_attachemt['name'],
                        'attachment_id' => $new_attachemt['attachment_id'],
                    );
                    $attachment_email      .= '<li><a href="'.esc_url($new_attachemt['url']).'">'.esc_html($new_attachemt['name']).'</a></li>';
                }
                $attachment_email       .= '<ul>';
                $identity_array['attachments']  = $attachment_record;
            }
            update_user_meta($user_id,'verification_attachments',$attachment_record);

            if (!empty($personal_photo)) {
                $personal_photo_     = array();
                foreach( $personal_photo as $file_url ){
                    $new_attachemt  = tuturn_temp_upload_to_media($file_url, $verification_post_id);
                    $personal_photo_[]  = array(
                        'url'           => $new_attachemt['url'],
                        'name'          => $new_attachemt['name'],
                        'attachment_id' => $new_attachemt['attachment_id'],
                    );
                    $photo_email    .= '<a href="'.esc_url($new_attachemt['url']).'"><img width="200" height="150" alt="'.esc_attr($new_attachemt['name']).'" src="'.esc_url($new_attachemt['url']).'" /></a>';
                }
                $identity_array['personal_photo']  = $personal_photo_;  
            }
            
            if(!empty($identity_array['personal_photo'])){
                set_post_thumbnail( $verification_post_id, $identity_array['personal_photo'][0]['attachment_id'] );
            }

            $update_identity    = update_post_meta($verification_post_id,'verification_info',$identity_array);

            if ( ! is_wp_error( $update_identity ) ) {
                update_user_meta($user_id,'identity_verified',0);
                update_post_meta($profile_id,'identity_verified','no'); 
                update_post_meta($verification_post_id,'parent_verification','no');
                update_post_meta($verification_post_id,'identity_verification_key',$random_key);

                $attachment_links   = array();
                $verification_info  = get_post_meta( $verification_post_id, 'verification_info',true );
                $meta_attachment    = array();
                $meta_attachment   = !empty($verification_info) ?  $verification_info['attachments'] : '' ;

                foreach($meta_attachment as $index  =>  $link){
                    $attachment_links[] = array(
                        $index  => $link['url']
                    );
                }

                $submission_details  = '<table width="600" cellpadding="0" cellspacing="0"><tbody>';
                if( !empty($name) ){
                    $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Name','tuturn').'</strong></th></tr>';
                    $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($name).'</td></tr>';
                }

                if( !empty($email_address) ){
                    $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Email','tuturn').'</strong></th></tr>';
                    $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($email_address).'</td></tr>';
                }

                if( !empty($gender) ){
                    $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Gender','tuturn').'</strong></th></tr>';
                    $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($gender).'</td></tr>';
                }

                if( !empty($phone_number) ){
                    $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Phone','tuturn').'</strong></th></tr>';
                    $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($phone_number).'</td></tr>';
                }

                if( !empty($address) ){
                    $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Address','tuturn').'</strong></th></tr>';
                    $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($address).'</td></tr>';
                }

                if( !empty($other_introduction) ){
                    $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Introduction','tuturn').'</strong></th></tr>';
                    $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($other_introduction).'</td></tr>';
                }


                if($user_type === 'student' && $parental_consent === 'yes'){
                    if( !empty($student_number) ){
                        $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Student number','tuturn').'</strong></th></tr>';
                        $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($student_number).'</td></tr>';
                    }
                    if( !empty($school) ){
                        $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('School name','tuturn').'</strong></th></tr>';
                        $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($school).'</td></tr>';
                    }
                    if( !empty($parent_name) ){
                        $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Parent name','tuturn').'</strong></th></tr>';
                        $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($parent_name).'</td></tr>';
                    }
                    if( !empty($parent_phone) ){
                        $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Parent phone','tuturn').'</strong></th></tr>';
                        $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($parent_phone).'</td></tr>';
                    }
                    if( !empty($parent_email) ){
                        $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Parent email','tuturn').'</strong></th></tr>';
                        $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.esc_html($parent_email).'</td></tr>';
                    }
                }

                if( !empty($photo_email) ){
                    $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('Profile-pic','tuturn').'</strong></th></tr>';
                    $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.do_shortcode($photo_email).'</td></tr>';
                }

                if( !empty($attachment_email) ){
                    $submission_details .= '<tr><th style="padding:6px 12px;background-color:#f8f8f8;text-align:left"><strong>'.esc_html__('File(s)','tuturn').'</strong></th></tr>';
                    $submission_details .= '<tr><td style="padding:6px 12px 12px 12px;white-space:pre-line">'.do_shortcode($attachment_email).'</td></tr>';
                }
                $submission_details .= '</tbody></table>';
                
                /* Email to admin for approve user identification request*/
                $confirmation_link                      = get_home_url().'?confirmation_key='.base64_encode(utf8_encode($verification_post_id.'-link-'.$random_key));
                $confirmation_btn                       = '<p style="margin:0 0 20px;text-align:center"><a href="'.esc_url($confirmation_link).'" style="color:#ffffff;font-weight:normal;text-decoration:none;background-color:#0072ff;font-size:16px;border-radius:5px;font-style:normal;padding:0.8rem 1rem;border-color:#0072ff">'.esc_html__('Click here to Confirm','tuturn').'</a></p>';
                $emailData                              = array();
                $emailData['user_name']                 = $name;
                $emailData['user_email']                = $email_address;
                $emailData['gender']                    = $gender;
                $emailData['phone_number']              = $phone_number;
                $emailData['address']                   = $address;
                $emailData['school_name']               = $school;
                $emailData['parent_phone']              = $parent_phone;
                $emailData['other_introduction']        = $other_introduction;
                $emailData['parent_email']              = $parent_email;
                $emailData['parent_name']               = $parent_name;
                $emailData['confirmation_link']         = $confirmation_link;
                $emailData['user_photo']                = $photo_email;
                $emailData['attachments']               = $attachment_email;
                $emailData['submission_details']        = $submission_details;
                $emailData['confirmation_html']         = $confirmation_btn;

                if (class_exists('Tuturn_Email_helper')) {
                    if (class_exists('TuturnRegistrationEmail')) {
                        $email_helper   = new TuturnRegistrationEmail();  

                        /* email to admin on identification submission */
                        if ( !empty($tuturn_settings['email_identity_submision_request_admin'])){
                            $email_helper->user_identification_request($emailData);
                        }

                        /* email to parent if user type is student */
                        if ( !empty($tuturn_settings['email_identity_submision_request_user']) && $user_type==='student'){
                            if( $parental_consent === 'yes' && in_array('parent_email', $student_fields)){
                                $email_helper->user_identity_verification_parent_consent($emailData);
                            }
                        }
                    }

                    /* identity verification emails */
                    if(class_exists('TuturnParentalEmails')){
                        $parent_email_helper   = new TuturnParentalEmails();
                        if($user_type === 'student'){
                            if( $parental_consent === 'yes' && in_array('parent_email', $student_fields)){
                                /* email to student on submitting identity documents */
                                $parent_email_helper->student_email_submit_documents($emailData);
                            } else {
                                /* email submitting identity if parent-consent is off or parent email is missing */
                                $parent_email_helper->email_student_submit_documents($emailData);
                            }
                        } else {
                            /* email to instructor on submitting identity documents */
                            $parent_email_helper->instructor_email_submit_documents($emailData);
                        }
                    }
                }
            }
            
            $json['type']       = 'success';
            $json['title']      = esc_html__("Woohoo!", 'tuturn');
            $json['redirect']   = Tuturn_Profile_Menu::tuturn_profile_menu_link('verfication-listing', $current_user->ID, true);
            $json['message']    = esc_html__('You have successfully submitted verfication request','tuturn');
     
            wp_send_json($json);
        } else {
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['type']       = 'error';
            $json['message']    = esc_html__('Something went wrong','tuturn');
            wp_send_json($json);
        }
            

    }
    add_action('wp_ajax_tu_user_identity', 'tu_user_identity');
}

/**
 * Cancelled identity documents
 *
 * @since    1.0.0
*/
if (!function_exists('cancelledIdentity')) {
    function cancelledIdentity(){

        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $json    = array();
        $user_id = !empty($_POST['data']) ? $_POST['data']: array();

        if( empty($user_id) ){
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['type']       = 'error';
            $json['message']    = esc_html__('Something went wrong','tuturn');
            wp_send_json($json);
        }
        
        update_user_meta($user_id,'verification_attachments','');
        update_user_meta($user_id,'identity_verified',0);
        $profileId      = tuturn_get_linked_profile_id( $user_id );
        update_post_meta($profileId,'identity_verified','no');
        $json['type']       = 'success';
        $json['title']          = esc_html__('Woohoo!', 'tuturn');
        $json['message']        = esc_html__('Your identity documents have been cancelled', 'tuturn');  
              wp_send_json($json);
    }
    add_action('wp_ajax_cancelledIdentity', 'cancelledIdentity');
}


/**
 * File uploader
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_temp_file_uploader')) {
    function tuturn_temp_file_uploader()
    {
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $json       = array();
        /*=================== Wp Nonce Verification =================*/
        $do_check   = check_ajax_referer('ajax_nonce', 'ajax_nonce', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }
        /*=================== End Wp Nonce Verification =================*/
        $response = Tuturn_file_permission::uploadFile($_FILES['file_name']);
        wp_send_json($response);
    }
    add_action('wp_ajax_tuturn_temp_file_uploader', 'tuturn_temp_file_uploader');
}


/**
 * File uploader
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_package_checkout')) {
    function tuturn_package_checkout()
    {
        global $woocommerce;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $user_id    = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $product_id = !empty($_POST['package_id']) ? intval($_POST['package_id']) : 0;
        if (empty($user_id) || empty($product_id)) {
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['type']       = 'error';
            $json['message']    = esc_html__('Something went wrong, please try again', 'tuturn');
            wp_send_json($json);
        }

        if (class_exists('WooCommerce')) {
            $woocommerce->cart->empty_cart();
            $product                    = wc_get_product($product_id);
            $user_id                    = $user_id;
            $cart_meta                  = array();
            $cart_meta['package_id']    = $product_id;
            $cart_meta['instructor_id'] = $user_id;
            $cart_meta['product_name']  = $product->get_name();
            $cart_meta['price']         = $product->get_price();
            $cart_meta['payment_type']  = 'package';
            $cart_data  = array(
                'package_id'    => $product_id,
                'cart_data'     => $cart_meta,
                'payment_type'  => 'package',
            );
            $cart_item_data = $cart_data;
            WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);
            $json['title']          = esc_html__('All set to go', 'tuturn');
            $json['type']           = 'success';
            $json['message']        = esc_html__("You're now redirecting to the checkout page", 'tuturn');
            $json['checkout_url']   = wc_get_checkout_url();
            wp_send_json($json);
        } else {
            $json['title']          = esc_html__('Failed!', 'tuturn');
            $json['type']           = 'error';
            $json['message']        = esc_html__('Please install WooCommerce plugin to process this order', 'tuturn');
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_tuturn_package_checkout', 'tuturn_package_checkout');
}

/**
 * File uploader
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_temp_multiple_files_uploader')) {
    function tuturn_temp_multiple_files_uploader()
    {
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        /*=================== Wp Nonce Verification =================*/
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'ajax_nonce', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $files      = !empty($_FILES['file']) ? $_FILES['file'] : array();
        $fileSizes  = !empty($_POST['fileSizes']) ? $_POST['fileSizes'] : array();
        $_POST = array(
            'files'     => $files,
            'sizes'     => $fileSizes,
            'dir'       => 'temp',
        );
        /*=================== End Wp Nonce Verification =================*/
        $response = Tuturn_file_permission::uploadAttachment($_POST);
        
        if (!empty($response['type']) && $response['type'] == 'success') {
            $json['attachments']    = $response['attachments']['0'];
            $json['type']           = 'success';
            wp_send_json($json);
        } else {
            wp_send_json($response);
        }
    }
    add_action('wp_ajax_tuturn_temp_multiple_files_uploader', 'tuturn_temp_multiple_files_uploader');
}

/**
 * Profile avatar update
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_update_avatar')) {
    function tuturn_update_avatar()
    {
        global $current_user;
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $user_identity  = $current_user->ID;
        $linked_profile = tuturn_get_linked_profile_id($current_user->ID);
        $uploadspath    = wp_upload_dir();
        $upload_dir     = $uploadspath['basedir'] . '/tuturn-temp/';
        $bse64Url       = !empty($_POST['image_url']) ? $_POST['image_url'] : '';
        $json           = array();

        if (!empty($bse64Url)) {
            // if user upload new image
            $bse64 = explode(',', $bse64Url);
            $bse64 = trim($bse64[1]);

            if (empty($bse64)) {
                $json['type']       = 'error';
                $json['title']      = esc_html__('OH', 'tuturn');
                $json['message']    = esc_html__('Image is not in correct format', 'tuturn');
                wp_send_json($json);
            }

            $timestamp    = time(); // create new timestamp
            $file_name    = $user_identity . '-' . $timestamp . '.jpg';
            $image_url    = $upload_dir . $file_name;
            file_put_contents($image_url, file_get_contents($bse64Url));

            if (file_exists($image_url)) {
                $pre_attachment_id  = get_post_thumbnail_id($linked_profile);
                $new_image          = $uploadspath['baseurl'] . '/tuturn-temp/' . $file_name;

                if (!empty($pre_attachment_id)) {
                    wp_delete_attachment($pre_attachment_id, true);
                }

                $profile_avatar     = tuturn_temp_upload_to_media($new_image, $linked_profile);
                set_post_thumbnail($linked_profile, $profile_avatar['attachment_id']);
                $avatar_150_x_150           = apply_filters('tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $linked_profile), array('width' => 150, 'height' => 150));
                $json['avatar_150_x_150']   = !empty($avatar_150_x_150)  ? $avatar_150_x_150 : '';
                $json['type']               = 'success';
                $json['title']              = esc_html__('Success', 'tuturn');
                $json['message']            = esc_html__('Settings have been updated successfully.', 'tuturn');
            } else {
                $json['type']               = 'error';
                $json['title']              = esc_html__('OH', 'tuturn');
                $json['message']            = esc_html__('Something went wrong.', 'tuturn');
            }
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('OH', 'tuturn');
            $json['message']    = esc_html__('Something went wrong.', 'tuturn');
        }

        wp_send_json($json);
    }
    add_action('wp_ajax_tuturn_update_avatar', 'tuturn_update_avatar');
}

/**
 * Load subcategories dropdown
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_load_categories')) {
    function tuturn_load_categories()
    {
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $selected_category      = !empty($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $selected_subcategories = !empty($_POST['subcategories']) ? $_POST['subcategories'] : array();
        $operation      = !empty($_POST['operation']) ? sanitize_text_field($_POST['operation']) : '';
        $category_args  = array(
            'show_option_none'  => esc_html__('Please select what you can teach', 'tuturn'),
            'show_count'        => false,
            'hide_empty'        => false,
            'name'              => 'category',
            'class'             => 'form-control',
            'taxonomy'          => 'product_cat',
            'id'                => 'tu-profile-categories-drop-down',
            'value_field'       => 'slug',
            'orderby'           => 'name',
            'selected'          => $selected_category,
            'hide_if_empty'     => false,
            'echo'              => false,
            'parent'            => 0,
            'required'          => true,
            'disabled'             => true,
        );
        if (!empty($operation) && $operation == 'add') {
            $categories                 = apply_filters('tuturnGetCategories', $category_args);
            $json['type']               = 'success';
            $json['title']              = '';
            $json['message']            = '';
            $json['categories']         = $categories;
            $json['sub_categories']     = '';
            wp_send_json($json);
        }

        if (!empty($operation) && $operation == 'edit') {
            $categories    = apply_filters('tuturnGetCategories', $category_args);
            $term = get_term_by('slug', $selected_category, 'product_cat');
            if (!empty($term->term_id)) {
                $sub_categories    = apply_filters('tuturnGetSubCategories', $term->term_id, '', 'tu-profile-sub-categories-drop-down');
                $selected_sub_categories_html   = '';
                foreach ($selected_subcategories as $key => $sub_category_slug) {

                    if(!empty($sub_category_slug['slug'])){
                        $sub_cat_data = get_term_by('slug', $sub_category_slug['slug'], 'product_cat');
                        if (!empty($sub_cat_data->name)) {
                            $sub_cat_name = !empty($sub_cat_data->name) ? $sub_cat_data->name : '';
                            $selected_sub_categories_html .= '<li id="' . esc_attr($sub_cat_data->slug) . '">';
                            $selected_sub_categories_html .= '<span>' . esc_html($sub_cat_name) . ' <a href="javascript:void(0);" data-slug="' . esc_attr($sub_cat_data->slug) . '"  class="select2-remove-item"><i class="icon icon-x"></i></a></span>';
                            $selected_sub_categories_html .= '<input type="hidden" data-slug="' . esc_attr($sub_cat_data->slug) . '"  name="subject_sub_categories[]" value="' . esc_attr($sub_cat_data->slug) . '">';
                            $selected_sub_categories_html .= '</li>';
                        }
                    }
                }

                $json['type']                   = 'success';
                $json['title']                  = '';
                $json['message']                = '';
                $json['selected_category']      = $selected_category;
                $json['selected_categories']    = $selected_sub_categories_html;
                $json['categories']             = $categories;
                $json['sub_categories']         = $sub_categories;
                wp_send_json($json);
            }
        }
    }
    add_action('wp_ajax_tuturn_load_categories', 'tuturn_load_categories');
}

/**
 * Save child subject data
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if(!function_exists('tuturn_save_subcat_subject_data')){
    function tuturn_save_subcat_subject_data(){
        global $current_user;
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $profile_id   = !empty($_POST['profile_id']) ? intval($_POST['profile_id']): '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $parent_term_id         = !empty($_POST['parent_term_id']) ? intval($_POST['parent_term_id']) : 0;
        $child_term_id          = !empty($_POST['child_term_id']) ? intval($_POST['child_term_id']) : 0;

        if(!empty($parent_term_id) && !empty($child_term_id)){
            $price                  = !empty($_POST['price']) ? sanitize_text_field($_POST['price']) : '';
            $desc                   = !empty($_POST['desc']) ? sanitize_text_field($_POST['desc']) : '';

            $profile_id         = tuturn_get_linked_profile_id($current_user->ID); 
            $profile_details    = get_post_meta($profile_id, 'profile_details', true);
            $profile_details    = !empty($profile_details) ? $profile_details : array();
            $subject_data       = !empty($profile_details['subject']) ? $profile_details['subject'] : '';
            $subjects_listings  = !empty($subject_data) ? $subject_data : array();
            $updated_array      = $subjects_listings;
            if( !empty($subjects_listings[$parent_term_id]['subcategories']) ){
                foreach($subjects_listings[$parent_term_id]['subcategories'] as $key => $values ){
                    if( !empty($values['id']) && intval($values['id']) === $child_term_id ){
                        $updated_array[$parent_term_id]['subcategories'][$key]['price']     = $price;
                        $updated_array[$parent_term_id]['subcategories'][$key]['content']   = htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');
                    }
                }
                $profile_details['subject'] = ($updated_array);
            } else {
                $updated_array[$parent_term_id]['subcategories'][$child_term_id]['price']     = $price;
                $updated_array[$parent_term_id]['subcategories'][$child_term_id]['content']   = htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');
                $profile_details['subject'] = ($updated_array);
            }
            
            update_post_meta($profile_id, 'profile_details', $profile_details);
            $profile_settings_url   = tuturn_dashboard_page_uri( $current_user->ID,'subjects' );          
            $profile_settings_url   = (add_query_arg(array('cat_id'=>$parent_term_id), $profile_settings_url));
            $json['redirect']       = esc_url_raw($profile_settings_url);
            $json['type']           = 'success';
            $json['message']        = esc_html__('You has successfully update record', 'tuturn');
            $json['title']          = esc_html__('Success', 'tuturn');
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed to update record', 'tuturn');
            wp_send_json($json);
        }

    }
    add_action('wp_ajax_tuturn_save_subcat_subject_data', 'tuturn_save_subcat_subject_data');
}

/**
 * delete parent subject
 */
if(!function_exists('tuturn_delete_subcategory_subject')){
    function tuturn_delete_subcategory_subject(){
        global $current_user;
        $json       = array();
        $profile_id         = tuturn_get_linked_profile_id($current_user->ID); 

        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $parent_subject_id         = !empty($_POST['subject_id']) ? intval($_POST['subject_id']) : 0;
        if(!empty($parent_subject_id)){
            
            $profile_details    = get_post_meta($profile_id, 'profile_details', true);
            $profile_details    = !empty($profile_details) ? $profile_details : array();
            $subject_data       = !empty($profile_details['subject']) ? $profile_details['subject'] : array();

            if(!empty($subject_data)){
                if(array_key_exists($parent_subject_id, $subject_data) ){
                    unset($subject_data[$parent_subject_id]);
                    $profile_details['subject'] = ($subject_data);
                }
            } 
            
            update_post_meta($profile_id, 'profile_details', $profile_details);

            $profile_settings_url   = tuturn_dashboard_page_uri( $current_user->ID,'subjects' );
            $profile_settings_url   = (add_query_arg(array('cat_id'=>$parent_subject_id), $profile_settings_url));
            $json['redirect']       = esc_url_raw($profile_settings_url);
            $json['type']           = 'success';
            $json['message']        = esc_html__('Subject has been deleted.', 'tuturn');
            $json['title']          = esc_html__('Success', 'tuturn');
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Something went wronge!', 'tuturn');
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_tuturn_delete_subcategory_subject', 'tuturn_delete_subcategory_subject');
}


/**
 * Save child subject data
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if(!function_exists('tuturn_remove_subcat_subject_data')){
    function tuturn_remove_subcat_subject_data(){
        global $current_user;
        $json       = array();

        $profile_id         = tuturn_get_linked_profile_id($current_user->ID); 

        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $data_array     = !empty($_POST['data']) ? $_POST['data'] : array();
        parse_str($data_array, $data_array);
        
        if(!empty($data_array)){
            $parent_term_id         = !empty($data_array['parent_term_id']) ? intval($data_array['parent_term_id']) : 0;
            $child_term_id          = !empty($data_array['child_term_id']) ? intval($data_array['child_term_id']) : 0;

            $profile_details    = get_post_meta($profile_id, 'profile_details', true);
            $profile_details    = !empty($profile_details) ? $profile_details : array();
            $subject_data       = !empty($profile_details['subject']) ? $profile_details['subject'] : '';
            $subjects_listings  = !empty($subject_data) ? $subject_data : array();
            $updated_array      = $subjects_listings;

            if( !empty($subjects_listings[$parent_term_id]['subcategories']) ){
                foreach($subjects_listings[$parent_term_id]['subcategories'] as $key => $values ){
                    if( !empty($values['id']) && intval($values['id']) === $child_term_id ){
                        unset($updated_array[$parent_term_id]['subcategories'][$key]);
                    }
                }
                $profile_details['subject'] = $updated_array;
            }

            update_post_meta($profile_id, 'profile_details', $profile_details);
            $profile_settings_url   = tuturn_dashboard_page_uri( $current_user->ID,'subjects' );          
            $profile_settings_url   = (add_query_arg(array('cat_id'=>$parent_term_id), $profile_settings_url));
            $json['redirect']       = esc_url_raw($profile_settings_url);
            $json['type']           = 'success';
            $json['message']        = esc_html__('You has successfully remove sub category', 'tuturn');
            $json['title']          = esc_html__('Success', 'tuturn');
            $json['categories']     = $category_array;
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed to update record', 'tuturn');
            wp_send_json($json);
        }

    }
    add_action('wp_ajax_tuturn_remove_subcat_subject_data', 'tuturn_remove_subcat_subject_data');
}

/**
 * Load subcategories dropdown
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_submit_categories_form')) {
    function tuturn_submit_categories_form()
    {
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $profile_id   = !empty($_POST['profile_id']) ? intval($_POST['profile_id']): '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $category_array = array();
        $category       = !empty($_POST['category']) ? ($_POST['category']) : '';
        $subcategories  = !empty($_POST['subcategories']) ? $_POST['subcategories'] : array();
        $term           = get_term_by('slug', $category, 'product_cat');

        if (!empty($term->term_id)) {
            $category_array['parent_category_id']  = $term->term_id;
            $category_array['parent_category']  = array(
                'id'    => $term->term_id,
                'slug'    => $term->slug,
                'name'    => $term->name,
            );

            foreach($subcategories as $subcategory){
                $sub_term = get_term_by('slug', $subcategory, 'product_cat');
                if (!empty($term->term_id)) {
            
                    $category_array['subcategories'][]  = array(
                        'id'        => $sub_term->term_id,
                        'slug'      => $sub_term->slug,
                        'name'      => $sub_term->name,
                        'price'     => 0,
                        'content'   => '',
                    );
                }
            }     

            $json['type']           = 'success';
            $json['title']          = esc_html__('Success', 'tuturn');
            $json['message']        = '';
            $json['categories']     = $category_array;
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed to update record', 'tuturn');
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_tuturn_submit_categories_form', 'tuturn_submit_categories_form');
}

/**
 * Update sub categories
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_update_categories_form')) {
    function tuturn_update_categories_form()
    {
        global $current_user;
        $profile_id         = tuturn_get_linked_profile_id($current_user->ID); 
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);

        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $category_array = $category_terms = array();
        $category       = !empty($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $subcategories  = !empty($_POST['subcategories']) ? $_POST['subcategories'] : array();
        $operation      = !empty($_POST['is_edit']) ? $_POST['is_edit'] : '';
        $term           = get_term_by('slug', $category, 'product_cat');
        $parent_term_id = $term->term_id;
        $category_terms[] = $parent_term_id;
        
        if (!empty($parent_term_id)) {
            
            $profile_details    = get_post_meta($profile_id, 'profile_details', true);
            $profile_details    = !empty($profile_details) ? $profile_details : array();
            $subject_data       = !empty($profile_details['subject']) ? $profile_details['subject'] : '';
            $subjects_listings  = !empty($subject_data) ? $subject_data : array();
            $profile_settings_url   = tuturn_dashboard_page_uri( $current_user->ID,'subjects' );          
            $profile_settings_url   = (add_query_arg(array('cat_id'=>$parent_term_id), $profile_settings_url));

            $parent_term_obj = get_term($parent_term_id);
            $parent_category = array();
            if(!empty($parent_term_obj)){
                $parent_category = array(
                    'id'        => $parent_term_obj->term_id,
                    'slug'      => $parent_term_obj->slug,
                    'name'      => $parent_term_obj->name,
                );
            }
            
            $sub_keys           = array();
            if( !empty($subjects_listings[$parent_term_id]['subcategories']) ){
                foreach($subjects_listings[$parent_term_id]['subcategories'] as $key => $values ){
                    $sub_keys[] = !empty($values['id']) ? intval($values['id']) : 0;
                }
            }

            $updated_cats = array();
            foreach($subcategories as $subcategory){
                $sub_term = get_term_by('slug', $subcategory, 'product_cat');
                if (!empty($sub_term->term_id) && in_array($sub_term->term_id, $sub_keys)) {
                    $child_term_data = $subjects_listings[$parent_term_id]['subcategories'][$sub_term->term_id];
                    $update_sub_cat = array(
                        'id'        => $child_term_data['id'],
                        'slug'      => $child_term_data['slug'],
                        'name'      => $child_term_data['name'],
                        'price'     => !empty($child_term_data['price']) ? $child_term_data['price'] : 0,
                        'content'   => !empty($child_term_data['content']) ? $child_term_data['content'] : '',
                    );      
                    $category_terms[] = $sub_term->term_id;              
                } else {
                    $update_sub_cat = array(
                        'id'        => $sub_term->term_id,
                        'slug'      => $sub_term->slug,
                        'name'      => $sub_term->name,
                        'price'     => 0,
                        'content'   => '',
                    );
                    $category_terms[] = $sub_term->term_id;
                }
                $updated_cats[$sub_term->term_id] = $update_sub_cat;
            }
            
            $subjects_listings[$parent_term_id]['parent_category_id']       = $parent_term_id;
            $subjects_listings[$parent_term_id]['parent_category']          = $parent_category;
            $subjects_listings[$parent_term_id]['subcategories']            = $updated_cats;
            $profile_details['subject'] = $subjects_listings; 
            
            update_post_meta($profile_id, 'profile_details', $profile_details);
            wp_set_post_terms( $profile_id, array_unique($category_terms), 'product_cat' );
            $json['redirect']       = esc_url_raw($profile_settings_url);
            $json['type']           = 'success';
            $json['title']          = esc_html__('Success', 'tuturn');
            $json['message']        = esc_html__('Subject updated successfully!', 'tuturn');
            $json['categories']     = $category_array;
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed to update record', 'tuturn');
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_tuturn_update_categories_form', 'tuturn_update_categories_form');
}
/**
 * Load subcategories dropdown
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_load_subcategories_dropdown')) {
    function tuturn_load_subcategories_dropdown()
    {
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $category_slug      = !empty($_POST['category_slug']) ? $_POST['category_slug'] : '';
        $term               = get_term_by('slug', $category_slug, 'product_cat');

        if (!empty($term->term_id)) {
            $sub_categories     = apply_filters('tuturnGetSubCategories', $term->term_id, '', 'tu-profile-sub-categories-drop-down');
            $json['type']       = 'success';
            $json['title']      = esc_html__('Success', 'tuturn');
            $json['message']    = '';
            $json['subcategories']  = $sub_categories;
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed to update record', 'tuturn');
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_tuturn_load_subcategories_dropdown', 'tuturn_load_subcategories_dropdown');
}

/**
 * Load sub categories
 * userRegistration
 * @since    1.0.0
 */
if (!function_exists('tuturn_load_subcategories')) {
    function tuturn_load_subcategories()
    {
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $category   = sanitize_text_field($_POST['category']);

        if (!empty($category)) {
            ob_start();
            $args   = array(
                'category'          => $category,
                'sub_categories'    => array()
            );

            tuturn_get_template('instructor-search/sub-categories.php', $args);
            $subcategories            = ob_get_clean();
        }else{
            $subcategories  = '';
        }


        $json['type']              = 'success';
        $json['title']              = esc_html__('Success', 'tuturn');
        $json['message']            = esc_html__('Settings have been updated successfully.', 'tuturn');
        $json['subcategories']    = $subcategories;

        wp_send_json($json);
    }
    add_action('wp_ajax_nopriv_tuturn_load_subcategories', 'tuturn_load_subcategories');
    add_action('wp_ajax_tuturn_load_subcategories', 'tuturn_load_subcategories');
}

/**
 * Remove sub categories
 * userRegistration
 * @since    1.0.0
 */
if (!function_exists('tuturn_remove_subcategories')) {
    function tuturn_remove_subcategories() {
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ($do_check == false) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json($json);
        }

        $category   = sanitize_text_field($_POST['sub-category']);

         if(empty($category)){
            $json['type']    = 'error';
            $json['title']   = esc_html__('Error', 'tuturn');
            $json['message'] = esc_html__('Something went wrong.', 'tuturn');
            wp_send_json($json);
        } else{
            $term = get_term_by('slug', $category, 'product_cat');
            $sub_category = $term->term_id; 

            $json['type']           = 'success';
            $json['title']          = esc_html__('Success', 'tuturn');
            $json['message']        = esc_html__('Settings have been updated successfully.', 'tuturn');
            $json['subcategories']  = $sub_category;
            wp_send_json($json);
        }

    }
    add_action('wp_ajax_nopriv_tuturn_remove_subcategories', 'tuturn_remove_subcategories');
    add_action('wp_ajax_tuturn_remove_subcategories', 'tuturn_remove_subcategories');
}

/**
 * Mark as favourite
 * userRegistration
 * @since    1.0.0
 */
if (!function_exists('tuturn_profile_add_to_save')) {
    function tuturn_profile_add_to_save()
    {
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $userId             = !empty($_POST['userId']) ? intval($_POST['userId']) : ''; //student user id
        $profile_id         = tuturn_get_linked_profile_id($userId); //current user profile id
        $profileId          = !empty($_POST['profileId']) ? intval($_POST['profileId']) : ''; //instructor profile id
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profile_id);
        } //if user is not logged in and author check then prevent

        $student_profile_id = tuturn_get_linked_profile_id($userId);        

        if (!empty($userId)) {
            
            $userType            = apply_filters('tuturnGetUserType', $userId);
             if (!empty($userType) && $userType == 'student') {
                $favourite_instructor   = get_post_meta($student_profile_id, 'favourite_instructor', true);
                $instructor               = array($profileId);
                $instructorStatus         = 0;

                if (empty($favourite_instructor)) {
                    update_post_meta($student_profile_id, 'favourite_instructor', $instructor);
                    $instructorStatus = 1;
                    $message = esc_html__('Instructor has been added to your favorite list', 'tuturn');
                } else {
                    if (in_array($profileId, $favourite_instructor)) {
                        array_splice($favourite_instructor, array_search($profileId, $favourite_instructor), 1);
                        $instructorStatus = 0;
                        $message = esc_html__('Instructor has been removed from favorite list', 'tuturn');
                    } else {
                        $favourite_instructor[] = $profileId;
                        $instructorStatus = 1;
                        $message = esc_html__('Instructor has been added to your favorite list', 'tuturn');
                    }
                    update_post_meta($student_profile_id, 'favourite_instructor', $favourite_instructor);
                }
                $json['type']               = 'success';
                $json['instructorStatus']   = !empty($instructorStatus) ? $instructorStatus : 0;
                $json['statusText']         = !empty($instructorStatus) ? esc_html__('Saved', 'tuturn') : esc_html__('Add to save', 'tuturn');
                $json['title']              = esc_html__('Updated!', 'tuturn');
                $json['message']            = $message;
                wp_send_json($json);
            } else {
                $json['type']       = 'error';
                $json['title']      = esc_html__('Failed!', 'tuturn');
                $json['message']    = esc_html__('Only Student can favourite instructor!', 'tuturn');
                wp_send_json($json);
            }

        } else {
            $login                  = tuturn_get_page_uri('login');
            $json['type']           = 'error';
            $json['title']          = esc_html__('Failed!', 'tuturn');
            $json['message']        = esc_html__('You should be login to mark as favourite.', 'tuturn');
            $json['login']          = $login;
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_nopriv_tuturn_profile_add_to_save', 'tuturn_profile_add_to_save');
    add_action('wp_ajax_tuturn_profile_add_to_save', 'tuturn_profile_add_to_save');
}

/**
 * User typr check for book tution
 * Book tution permission
 * @since    1.0.0
 */
if (!function_exists('tuturn_add_an_tution')) {
    function tuturn_add_an_tution(){
        global $tuturn_settings;
        $identity_verification          = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : '';
        $identity_verification_booking  = !empty($tuturn_settings['identity_verification_booking']) ? $tuturn_settings['identity_verification_booking'] : false;

        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $userId                     = !empty($_POST['userId']) ? intval($_POST['userId']) : '';
        $instructor_profile_id      = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : '';
        $profile_id                 = tuturn_get_linked_profile_id($userId);        

        if (!empty($userId)) {
            $userType            = apply_filters('tuturnGetUserType', $userId);
            if( !empty($identity_verification_booking) && !empty($identity_verification) && ($identity_verification === 'students' || $identity_verification === 'both') ){
                $identity_verified  = get_user_meta($userId, 'identity_verified', true);
                if( empty($identity_verified) ){
                    $json['type']           = 'error';
                    $json['title']          = esc_html__('Failed!', 'tuturn');
                    $json['message']        = esc_html__('You must verify your identity to get online bookings', 'tuturn');
                    wp_send_json($json);
                }
            }
            if (!empty($userType) && $userType == 'student') {

                $json['type']               = 'success';
                $json['title']              = esc_html__('Success!', 'tuturn');
                $json['message']            = esc_html__('Your are allowed to book.', 'tuturn');
                wp_send_json($json);
            } else {
                $json['type']           = 'error';
                $json['title']          = esc_html__('Failed!', 'tuturn');
                $json['message']        = esc_html__('You are not allowed to perform this action.', 'tuturn');
                wp_send_json($json);
            }
            

        } else {
            $current_page_link  = get_permalink($instructor_profile_id);
            $login              = tuturn_get_page_uri('login');

            if(!empty($_GET['tab'])){
                $current_page_link  = add_query_arg('tab', esc_html($_GET['tab']), $current_page_link);
            }
        
            $redirect_url       = tuturn_get_page_uri('login');
            if(empty($redirect_url)){
                $redirect_url   = get_home_url();        
            }
        
            set_transient( 'tu_redirect_page_url', esc_url_raw($current_page_link), 200 );

            $json['type']           = 'error';
            $json['title']          = esc_html__('Failed!', 'tuturn');
            $json['message']        = esc_html__('Something went wrong', 'tuturn');
            $json['login']          = $login;
            $json['redirect_url']   = $current_page_link;
            wp_send_json($json);
        }

    }

    add_action('wp_ajax_nopriv_tuturn_add_an_tution', 'tuturn_add_an_tution');
    add_action('wp_ajax_tuturn_add_an_tution', 'tuturn_add_an_tution');
}

/**
 * Showing subject slots
 * subjects Slots
 * @since    1.0.0
 */
if(!function_exists('tuturn_display_subject_slots')){
    function tuturn_display_subject_slots(){
        global $tuturn_settings;
        $identity_verification          = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : false;
        $identity_verification_booking  = !empty($tuturn_settings['identity_verification_booking']) ? $tuturn_settings['identity_verification_booking'] : false;

        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $userId                     = !empty($_POST['userId']) ? intval($_POST['userId']) : '';
        $profile_id                 = tuturn_get_linked_profile_id($userId);  
        $instructor_profile_id      = !empty($_POST['instructor_profile_id']) ? intval($_POST['instructor_profile_id']) : 0;
        if (!empty($userId) && !empty($instructor_profile_id)) {
            $instr_profile_details    = get_post_meta($instructor_profile_id, 'profile_details', true);
            $instr_profile_details    = !empty($instr_profile_details) ? $instr_profile_details : array();
            if(!empty($instr_profile_details['subject'])){
                $teach_subjects = $instr_profile_details['subject'];
                foreach($teach_subjects as $subj_values){
                    $subjects_arr = $child_subject_cats = array();
                    if(!empty($subj_values['subcategories']) && is_array($subj_values['subcategories'])){
                        $subj_sub_categories = $subj_values['subcategories'];
                        foreach($subj_sub_categories as $cat_key=>$subject_cats ){
                            $subject_id         = !empty($subject_cats['id']) ? intval($subject_cats['id']) : 0;
                            $subject_name       = !empty($subject_cats['name']) ? $subject_cats['name'] : '';
                            $subject_price      = !empty($subject_cats['price']) ? $subject_cats['price'] : '';
                            $subject_content    = !empty($subject_cats['content']) ? $subject_cats['content'] : '';
                            $subject_image      = '';

                            $child_subject_cats[] = array(
                                'subject_id'        => $subject_id,
                                'subject_name'      => $subject_name,
                                'subject_price'     => $subject_price,
                                'subject_content'   => $subject_content,
                                'subject_image'     => $subject_image,
                            );
                        }

                        $subjects_arr['parent_cat'] = $subj_values['parent_category'];
                        $subjects_arr['child_cats'] = $child_subject_cats;

                    }

                    $subject_categ_results[] = $subjects_arr;
                }

                $json['subjects']   = $subject_categ_results;
                $json['type']                   = 'success';
                wp_send_json($json, 200);
            } else {
                $json['type']           = 'error';
                $json['title']          = esc_html__('Failed!', 'tuturn');
                $json['message']        = esc_html__('subjects not found', 'tuturn');
                wp_send_json($json);
            }

        } else {
            $current_page_link  = get_permalink($instructor_profile_id);
            $login              = tuturn_get_page_uri('login');

            if(!empty($_GET['tab'])){
                $current_page_link  = add_query_arg('tab', esc_html($_GET['tab']), $current_page_link);
            }
        
            $redirect_url       = tuturn_get_page_uri('login');
            if(empty($redirect_url)){
                $redirect_url   = get_home_url();        
            }
        
            set_transient( 'tu_redirect_page_url', esc_url_raw($current_page_link), 200 );

            $json['type']           = 'error';
            $json['title']          = esc_html__('Failed!', 'tuturn');
            $json['message']        = esc_html__('Something went wrong', 'tuturn');
            $json['login']          = $login;
            $json['redirect_url']   = $current_page_link;
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_nopriv_tuturn_display_subject_slots', 'tuturn_display_subject_slots');
    add_action('wp_ajax_tuturn_display_subject_slots', 'tuturn_display_subject_slots');
}

/**
 * Remove/add favourite instrutor saved list
 * userRegistration
 * @since    1.0.0
 */
if (!function_exists('tuturn_favourite_profile_add_remove')) {
    function tuturn_favourite_profile_add_remove()
    {
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $userId             = !empty($_POST['userId']) ? intval($_POST['userId']) : '';
        $profile_id          = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : '';
        $profileId          = !empty($_POST['profileId']) ? intval($_POST['profileId']) : '';
        $student_profile_id = tuturn_get_linked_profile_id($userId);        

        if (!empty($userId)) {

            if( function_exists('tuturn_validate_privileges') ) { 
                tuturn_validate_privileges($profile_id);
            } //if user is not logged in and author check then prevent

            $userType            = apply_filters('tuturnGetUserType', $userId);
            if (!empty($userType) && $userType == 'student') {
                $favourite_instructor   = get_post_meta($student_profile_id, 'favourite_instructor', true);
                $instructor               = array($profileId);
                $instructorStatus         = 0;

                if (empty($favourite_instructor)) {
                    update_post_meta($student_profile_id, 'favourite_instructor', $instructor);
                    $instructorStatus = 1;
                } else {
                    if (in_array($profileId, $favourite_instructor)) {
                        array_splice($favourite_instructor, array_search($profileId, $favourite_instructor), 1);
                        $instructorStatus = 0;
                    } else {
                        $favourite_instructor[] = $profileId;
                        $instructorStatus = 1;
                    }
                    update_post_meta($student_profile_id, 'favourite_instructor', $favourite_instructor);
                }
                $json['type']           = 'success';
                $json['instructorStatus'] = !empty($instructorStatus) ? $instructorStatus : 0;
                $json['statusText']     = !empty($instructorStatus) ? esc_html__('Saved', 'tuturn') : esc_html__('Add to save', 'tuturn');
                $json['title']          = esc_html__('Updated!', 'tuturn');
                $json['message']        = esc_html__('Instructor has been remove from favourite list!', 'tuturn');
                wp_send_json($json);
            } else {
                $json['type']           = 'error';
                $json['title']          = esc_html__('Failed!', 'tuturn');
                $json['message']        = esc_html__('Only Student can favourite instructor!', 'tuturn');
                wp_send_json($json);
            }

        } else {
            $login                  = tuturn_get_page_uri('login');
            $json['type']           = 'error';
            $json['title']          = esc_html__('Failed!', 'tuturn');
            $json['message']        = esc_html__('You should be login to mark as favourite.', 'tuturn');
            $json['login']          = $login;
            wp_send_json($json);
        }
    }
    add_action('wp_ajax_nopriv_tuturn_favourite_profile_add_remove', 'tuturn_favourite_profile_add_remove');
    add_action('wp_ajax_tuturn_favourite_profile_add_remove', 'tuturn_favourite_profile_add_remove');
}

/**
 * Password validation
 * userRegistration
 * @since    1.0.0
 */
function userPasswordValidation($password)
{
    global $tuturn_settings;
    $json               = array();
    $password_strength  = !empty($tuturn_settings['password_strength']) ? $tuturn_settings['password_strength'] : '';
    $password_strength  = !empty($password_strength) ? $password_strength : array('length');
    $choices            = array(
        'length'            => wp_kses(__('Password must be 8 characters<br>', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
        'upper'             => wp_kses(__('1 upper case<br>', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
        'lower'             => wp_kses(__('1 lower case<br>', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
        'special_character' => wp_kses(__('1 special character<br>', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
        'number'            => wp_kses(__('1 number<br>', 'tuturn'), array('a' => array('href' => array(), 'title' => array()), 'br' => array(), 'em' => array(), 'strong' => array(),)),
    );

    if (!empty($password)) {
        $number         = preg_match('@[0-9]@', $password);
        $uppercase      = preg_match('@[A-Z]@', $password);
        $lowercase      = preg_match('@[a-z]@', $password);
        $specialChars   = preg_match('@[^\w]@', $password);
        $errors         = '';

        foreach ($password_strength as $key => $item) {
            if ($item === 'length') {
                if (strlen($password) < 8) {
                    $errors .= $choices[$item];
                }
            } else if ($item === 'upper' && !$uppercase) {
                $errors .= $choices[$item];
            } else if ($item === 'lower' && !$lowercase) {
                $errors .= $choices[$item];
            } else if ($item === 'number' && !$number) {
                $errors .= $choices[$item];
            } else if ($item === 'special_character' && !$specialChars) {
                $errors .= $choices[$item];
            }
        }

        if (!empty($errors)) {
            $json['type']        = 'error';
            $json['title']       = esc_html__('Oops!', 'tuturn');;
            $json['message']     = $errors;
            return $json;
        }
    }else{
        $json['type']         = 'error';
        $json['title']       = esc_html__('Oops!', 'tuturn');;
        $json['message']     = esc_html__('Password should not be empty', 'tuturn');
        return $json;
    }
}

/**
 * User Registration
 * @since    1.0.0
*/
if(!function_exists('tuturn_user_register')){
    function tuturn_user_register(){
        global $tuturn_settings;
        $json   = array();

        $user_name_option       = !empty($tuturn_settings['user_name_option']) ? $tuturn_settings['user_name_option'] : false;
        $user_phone_option      = !empty( $tuturn_settings['user_phone_option'] ) ? ($tuturn_settings['user_phone_option']) : '';
        $is_phone_required      = !empty($tuturn_settings['is_phone_required']) ? $tuturn_settings['is_phone_required'] : false;

        /* Demo site check */
        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        
        /* security check */
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);        
        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $post_data  = !empty($_POST['data']) ? $_POST['data'] : '';
        parse_str($post_data, $data);
        $registration = !empty($data['registration']) ? $data['registration'] : array();

        $validations = array(
            'fname'         => esc_html__('First name is required', 'tuturn'),
            'lname'         => esc_html__('Last name is required.', 'tuturn'),
            'email'         => esc_html__('Email field is required.', 'tuturn'),    
            'password'      => esc_html__('Password field is required.', 'tuturn'),    
            'terms'         => esc_html__('You must accept terms and conditions', 'tuturn'),            
            'user_type'     => esc_html__('User type is required', 'tuturn'),            
        );

        if( !empty($user_name_option) ){
            $validations['username']   = esc_html__('User name is required', 'tuturn');
        }
        
        if( !empty($is_phone_required) && !empty($user_phone_option) ){
            $validations['phone_number']   = esc_html__('Phone number is required', 'tuturn');
        }

        foreach ( $validations as $key => $value ) {
            if ( empty( $registration[$key] ) ) {
                $json['title']      = esc_html__("Oops!", 'tuturn');
                $json['type']       = 'error';
                $json['message']    = $value;
                wp_send_json($json, 203);
            }

            /* Validate email address */
            if ( $key === 'email' ) {
                if ( !is_email( $registration[$key] ) ) {
                    $json['type']       = 'error';
                    $json['message']    = esc_html__('Please add a valid email address.', 'tuturn');
                    $json['title']      = esc_html__("Oops!", 'tuturn');
                    wp_send_json($json, 203);
                }
            }

            if ($key === 'password') {
                $data = userPasswordValidation($registration[$key]);
                if ( !empty( $data ) ) {
                    $data['title']  = esc_html__("Oops!", 'tuturn');
                    wp_send_json($data, 203);
                }
            }
        }

        $first_name         = !empty($registration['fname']) ? sanitize_text_field($registration['fname']) : '';
        $last_name          = !empty($registration['lname']) ? sanitize_text_field($registration['lname']) : '';
        $username           = !empty($registration['username']) ? sanitize_text_field($registration['username']) : '';
        $email              = !empty($registration['email']) ? sanitize_email($registration['email']) : '';
        $password           = !empty($registration['password']) ? esc_html($registration['password']) : '';
        $phone_number       = !empty($registration['phone_number']) ? esc_html($registration['phone_number']) : '';
        $user_type          = !empty($registration['user_type']) ? esc_html($registration['user_type']) : 'student';
        $user_agree_terms   = !empty($registration['terms']) ? esc_html($registration['terms']) : '';
        $full_name          = $first_name . ' ' . $last_name;
        $user_nicename      = sanitize_title($full_name);
        $user_name          = !empty($user_name_option) ? $username : $email;

        $userdata = array(
            'user_login'    => $user_name,
            'user_pass'     => $password,
            'user_email'    => $email,
            'user_nicename' => $user_nicename,
            'display_name'  => $full_name,
        );
        
        $user_identity = wp_insert_user($userdata);

        if (is_wp_error($user_identity)) {
            $json['title']          = esc_html__("Oops!", 'tuturn');
            $json['type']           = "error";
            $json['message']        = esc_html__("User already exists, please try another one.", 'tuturn');
            wp_send_json($json, 203);
        } else {
            global $wpdb;
            wp_update_user(array('ID' => esc_sql($user_identity), 'role' => 'subscriber', 'user_status' => 0));
            $wpdb->update($wpdb->prefix . 'users', array('user_status' => 0), array('ID' => esc_sql($user_identity)));

            //===
            update_user_meta($user_identity, 'first_name', $first_name);
            update_user_meta($user_identity, 'last_name', $last_name);
            update_user_meta($user_identity, '_user_type', $user_type );
            update_user_meta($user_identity, 'termsconditions', true);
            update_user_meta($user_identity, 'show_admin_bar_front', false);
            update_user_meta($user_identity, '_is_verified', 'no');
            update_user_meta($user_identity, 'identity_verified', 0);

            $verify_link = '';
            $verify_new_user = !empty($tuturn_settings['email_user_registration']) ? $tuturn_settings['email_user_registration'] : '';

            if (!empty($verify_new_user) && $verify_new_user == 'verify_by_link' && empty($tuturn_settings['user_account_approve'])) {
                /* verification link */
                $key_hash     = md5(uniqid(openssl_random_pseudo_bytes(32)));
                update_user_meta($user_identity, 'confirmation_key', $key_hash);
                $protocol     = is_ssl() ? 'https' : 'http';
                $verify_link  = esc_url(add_query_arg(array('key' => $key_hash . '&verifyemail=' . $email), home_url('/', $protocol)));
            }

            $post_type = ($user_type == 'instructor') ? 'tuturn-instructor' : 'tuturn-student';
            $user_post = array(
                'post_title'    => wp_strip_all_tags($user_nicename),
                'post_status'   => 'publish',
                'post_author'   => $user_identity,
                'post_type'     => apply_filters('tuturn_profiles_post_type_name', $post_type),
            );

            $post_id = wp_insert_post($user_post);
            if (!is_wp_error($post_id)) {
                $dir_latitude       = !empty($tuturn_settings['dir_latitude']) ? $tuturn_settings['dir_latitude'] : 0.0;
                $dir_longitude      = !empty($tuturn_settings['dir_longitude']) ? $tuturn_settings['dir_longitude'] : 0.0;
                $shortname_option   =  !empty($tuturn_settings['shortname_option']) ? $tuturn_settings['shortname_option'] : '';
                $post_meta          = array();
                
                //add extra fields as a null
                update_post_meta($post_id, '_tag_line', '');
                update_post_meta($post_id, '_address', '');
                update_post_meta($post_id, 'hourly_rate', 0);
                update_post_meta($post_id, '_latitude', $dir_latitude);
                update_post_meta($post_id, '_longitude', $dir_longitude);
                update_post_meta($post_id, '_linked_profile', $user_identity);
                update_post_meta($post_id, '_is_verified', 'no');
                update_user_meta($user_identity, '_linked_profile', $post_id );
                
                $post_meta['first_name']                = $first_name;
                $post_meta['last_name']                 = $last_name;
                $post_meta['contact_info']['phone']     = $phone_number;
                $post_meta['name']                      = wp_strip_all_tags($user_nicename);
                $post_meta['tagline']                   = '';

                update_post_meta($post_id, 'profile_details', $post_meta);
                
                //Update slug
                if(!empty($shortname_option) && $shortname_option == 'yes'){
                    $user_name  = tuturn_get_username($post_id);
                    $post_name_update = array(
                        'ID'            => intval($post_id),
                        'post_name'    => sanitize_title($user_name)
                    );
                    wp_update_post( $post_name_update );
                }

                /* account approved */
                if(!empty($tuturn_settings['user_account_approve'])){
                    update_user_meta($user_identity, '_is_verified', 'yes');
                    update_post_meta($post_id, '_is_verified', 'yes');
                }

                /* identity verification approved */
                $identity_verification          = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : '';
                update_user_meta($user_identity, 'identity_verified', 1);
                update_post_meta($post_id,'identity_verified','yes');
                
                if(!empty($post_type) && $post_type === 'tuturn-student' && ($identity_verification === 'students' || $identity_verification === 'both')){
                    update_user_meta($user_identity, 'identity_verified', 0);
                    update_post_meta($post_id,'identity_verified','no');
                }
                
                if(!empty($post_type) && ($post_type === 'tuturn-instructor') && ($identity_verification === 'tutors' || $identity_verification === 'both')){
                    update_user_meta($user_identity, 'identity_verified', 0);
                    update_post_meta($post_id,'identity_verified','no');
                }
                
                $login_url    = !empty( $tuturn_settings['tpl_login'] ) ? get_permalink($tuturn_settings['tpl_login']) : wp_login_url();

                /* Send email to users & admin */
                if (class_exists('Tuturn_Email_Helper')) {
                    $blogname                       = get_option('blogname');
                    $emailData                      = array();
                    $emailData['name']              = $user_nicename;
                    $emailData['email']             = $email;
                    $emailData['verification_link'] = $verify_link;
                    $emailData['site']              = $blogname;
                    $emailData['login_url']         = $login_url;
                    
                    /* Welcome Email */
                    if (class_exists('TuturnRegistrationEmail')) {
                        $email_helper = new TuturnRegistrationEmail();

                        if(!empty($tuturn_settings['user_account_approve'])){
                            //email to user on auto approve
                            $email_helper->registration_user_auto_approve_email($emailData);
                        } elseif (!empty($verify_new_user) && $verify_new_user == 'verify_by_link' && empty($tuturn_settings['user_account_approve'])) {
                            //email to user verify by link
                            $email_helper->registration_user_email($emailData);
                        } else{
                            /* to user approved by admin */
                            $email_helper->registration_account_approval_request($emailData);
                            /* to admin if verify by admin */
                            $email_helper->registration_verify_by_admin_email($emailData);
                        }
                        
                        $email_helper->new_user_register_admin_email($emailData);
                    }
                }
            }

            //===
            $user_array = array();
            $dashboard = home_url('/');
            if(!empty($tuturn_settings['user_account_approve'])){
                $message = esc_html__("Congratulation Your account has been created successfully!", 'tuturn');
                $dashboard = tuturn_dashboard_page_uri($user_identity);

                $user_array['user_login'] 	 = $email;
                $user_array['user_password'] = $password;
                wp_signon($user_array, false);
            } elseif(!empty($tuturn_settings['email_user_registration']) && $tuturn_settings['email_user_registration'] == 'verify_by_link'){
                $message = esc_html__("Your account has been created. Please verify your account through the email that has been sent to your email address.", 'tuturn');
            } elseif(!empty($tuturn_settings['email_user_registration']) && $tuturn_settings['email_user_registration']=='verify_by_admin') {
                $message = esc_html__("Your account has been created. Please wait while your account is verified by the admin.", 'tuturn');
            }

            $json['type']           = 'success';
            $json['title']          = esc_html__('Woohoo!', 'tuturn');
            $json['message']        = $message;
            $json['redirect']       = esc_url($dashboard);
            wp_send_json($json, 200);

        }
    }
    add_action('wp_ajax_nopriv_tuturn_user_register', 'tuturn_user_register');
    add_action('wp_ajax_tuturn_user_register', 'tuturn_user_register');
}


/**
 * User Login
 * @since    1.0.0
*/
if(!function_exists('tuturn_user_signin')){
    function tuturn_user_signin(){
        global $tuturn_settings;
        $json       = array();
        $post_data  = !empty($_POST['data']) ? $_POST['data'] : '';
        parse_str($post_data, $data);
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);

        $login  = !empty($data['login']) ? $data['login'] : array();

        $user_array = array();
        $user_array['user_login']       = sanitize_text_field($login['email']);
        $user_array['user_password']    = sanitize_text_field($login['password']);

        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $validations    = array(
            'email'     => esc_html__('Email/username is required', 'tuturn'),
            'password'  => esc_html__('Password is required.', 'tuturn'),      
        );

        foreach ( $validations as $key => $value ) {
            if ( empty( $login[$key] ) ) {
                $json['type']       = 'error';
                $json['title']      = esc_html__("Oops!", 'tuturn');
                $json['message']    = $value;
                wp_send_json($json, 203);
            }
        }

        $user               = get_user_by( 'email', $user_array['user_login'] );       
        $is_verified 	    = get_user_meta($user->ID, '_is_verified', true); 
        $is_verified	    = !empty($is_verified) ? $is_verified : 'no'; 

        if($is_verified == 'no'){
            if (empty($tuturn_settings['user_account_approve'])) {
                $json['type'] 		= 'error';
                $json['title']      = esc_html__("Oops!", 'tuturn');
                $verify_new_user = !empty($tuturn_settings['email_user_registration']) ? $tuturn_settings['email_user_registration'] : '';
                
                if (!empty($verify_new_user) && $verify_new_user == 'verify_by_link' && empty($tuturn_settings['user_account_approve'])) {
                    $message = esc_html__("Please verify your account through the email that has been sent to your email address.", 'tuturn');

                    $json['title']      = esc_html__('Oops!', 'tuturn');
                    $json['message'] 	= $message;
                    wp_send_json($json, 203);
                } elseif (!empty($verify_new_user) && $verify_new_user == 'verify_by_admin' && empty($tuturn_settings['user_account_approve'])) {
                    $message = esc_html__("Please wait while your account is verified by the admin.", 'tuturn');
                    $json['title']      = esc_html__('Oops!', 'tuturn');
                    $json['message'] 	= $message;
                    wp_send_json($json, 203);
                }
            }
        }


        $user = wp_signon($user_array, false);
        
        if (is_wp_error($user)) {
            $json   = array(
                'type'          => 'error',
                'title'         => esc_html__('Oops!', 'tuturn'), 
                'loggedin'      => false, 
                'message'       => esc_html__('Wrong email/username or password.', 'tuturn')
            );
            wp_send_json($json, 203);
        } else { 
            $userDetail             = $user->data;
            $profile_id             = tuturn_get_linked_profile_id($user->ID);
            $profile_id             = !empty($profile_id) ? $profile_id : 0;
            $identity_verified      = get_user_meta((int)$userDetail->ID, 'identity_verified', true);
            $identity_verified      = !empty($identity_verified) ? $identity_verified : 'no';
            $is_verified            = get_post_meta($profile_id, '_is_verified', true);
            $is_verified            = !empty($is_verified) ? $is_verified : 'no';

            $deactivate_account     = get_post_meta( $profile_id, '_deactive_account', true );
            $deactivate_account     = (!empty($deactivate_account) && $deactivate_account == 1) ? 'true' : 'false';
           
            $redirect               = tuturn_dashboard_page_uri((int)$userDetail->ID);  
            $redirect_transient     = get_transient( 'tu_redirect_page_url' );
            $redirect               = !empty($redirect_transient) ? html_entity_decode($redirect_transient) : $redirect;
            
            $json['type']       = 'success';
            $json['title']      = esc_html__('Woohoo!', 'tuturn');
            $json['message']    = esc_html__('You are successfully logged in', 'tuturn');
            $json['loggedin']   = true;
            $json['redirect']   = !empty($redirect) ? esc_url($redirect) : home_url('/');
            wp_send_json( $json, 200 );

        }
    }
    add_action('wp_ajax_nopriv_tuturn_user_signin', 'tuturn_user_signin');
    add_action('wp_ajax_tuturn_user_signin', 'tuturn_user_signin');
}

/**
 * User Social Login/Register
 * @since    1.0.0
*/
if(!function_exists('tuturn_user_social_login')){
    function tuturn_user_social_login(){
        global $tuturn_settings;

        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json           = array();
        $post_data      = !empty($_POST['data']) ? $_POST['data'] : '';
        $do_check       = check_ajax_referer('ajax_nonce', 'security', false);

        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        if (!empty($post_data['email'])) {
            $name           = sanitize_text_field($post_data['name']);
            $last_name      = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $first_name     = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));
            $user_type      = !empty($post_data['user_type']) ? sanitize_text_field($post_data['user_type']) : '';
            $user_email     = !empty($post_data['email']) && is_email($post_data['email']) ? sanitize_email($post_data['email']) : '';
            $login_type     = !empty($post_data['login_type']) ? sanitize_text_field($post_data['login_type']) : '';
            $ID             = email_exists($user_email);
            $full_name      = $first_name . ' ' . $last_name;

            /* User exists do login */
            if (!empty($ID)) {
                $user_data      = get_user_by('email', $user_email);
                $user_identity  = !empty($user_data) ? $user_data->ID : 0;
                $user_type      = apply_filters('tuturnGetUserType', $user_identity );
                if (!empty($user_type) && ($user_type == 'instructor' || $user_type == 'student')) {
                    $redirect   = tuturn_dashboard_page_uri($user_data->ID);
                } else {
                    $redirect   = home_url('/');
                }

                if (!is_user_logged_in()) {
                    if (!is_wp_error($user_data) && isset($user_data->ID) && !empty($user_data->ID)) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user_data->ID, $user_data->user_login);
                        wp_set_auth_cookie($user_data->ID, true);
                        update_user_caches($user_data);
                        do_action('wp_login', $user_data->user_login, $user_data);
                    }
                }

                $json['type']       = 'success';
                $json['redirect']   = $redirect;
                $json['title']      = esc_html__('Logged in!', 'tuturn');
                $json['message']    = esc_html__('You have successfully logged in', 'tuturn');
                wp_send_json($json , 200);
            } else {
                if(empty($user_type)){
                    $json['type']           = 'success';
                    $json['cooseUserType']  = 'model';
                    $json['userData']       = ($post_data);
                    $json['title']          = esc_html__('Registered!', 'tuturn');
                    $json['message']        = esc_html__('Choose user type', 'tuturn');
                    wp_send_json($json , 200);
                }

                $picture        = !empty($post_data['picture']) ? sanitize_text_field($post_data['picture']) : '';
                $user_nicename  = sanitize_title($name);
                $userdata   = array(
                    'user_login'    => $user_email,
                    'user_pass'     => '',
                    'user_email'    => $user_email,
                    'user_nicename' => $user_nicename,
                    'display_name'  => $name,
                );

                $user_identity = wp_insert_user($userdata);
                
                if (is_wp_error($user_identity)) {
                    $json['type']       = 'error';
                    $json['title']      = esc_html__('Oops!', 'tuturn');
                    $json['message']    = esc_html__('Something went wrong', 'tuturn');
                    wp_send_json($json , 200);
                } else {
                    global $wpdb;
                    wp_update_user(array('ID' => esc_sql($user_identity), 'role' => 'subscriber', 'user_status' => 0));
                    $wpdb->update($wpdb->prefix . 'users', array('user_status' => 0), array('ID' => esc_sql($user_identity)));

                    update_user_meta($user_identity, 'first_name', $first_name);
                    update_user_meta($user_identity, 'last_name', $last_name);
                    update_user_meta($user_identity, 'login_type', $login_type);
                    update_user_meta($user_identity, 'show_admin_bar_front', false);
                    update_user_meta($user_identity, '_user_type', $user_type);
                    update_user_meta($user_identity, '_is_verified', 'yes');
                    update_user_meta($user_identity, 'identity_verified', 0);

                    /* Create Post */
                    $post_type = ($user_type == 'instructor') ? 'tuturn-instructor' : 'tuturn-student';
                    $user_post = array(
                        'post_title'    => wp_strip_all_tags($full_name),
                        'post_status'   => 'publish',
                        'post_author'   => $user_identity,
                        'post_type'     => apply_filters('tuturn_profiles_post_type_name', $post_type),
                    );

                    $post_id = wp_insert_post($user_post);
                    if (!is_wp_error($post_id)) {
                        $dir_latitude = !empty($tuturn_settings['dir_latitude']) ? $tuturn_settings['dir_latitude'] : 0.0;
                        $dir_longitude = !empty($tuturn_settings['dir_longitude']) ? $tuturn_settings['dir_longitude'] : 0.0;
                        
                        update_post_meta($post_id, '_tag_line', '');
                        update_post_meta($post_id, '_address', '');
                        update_post_meta($post_id, 'hourly_rate', 0);
                        update_post_meta($post_id, '_latitude', $dir_latitude);
                        update_post_meta($post_id, '_longitude', $dir_longitude);
                        update_post_meta($post_id, '_linked_profile', $user_identity);
                        update_user_meta( $user_identity, '_linked_profile', $post_id );
                        update_post_meta($post_id, '_is_verified', 'yes');

                        /* identity verification */
                        $identity_verification          = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : '';
                        update_user_meta($user_identity, 'identity_verified', 1);
                        update_post_meta($post_id,'identity_verified','yes');

                        if(!empty($post_type) && $post_type === 'tuturn-student' && ($identity_verification === 'students' || $identity_verification === 'both')){
                            update_user_meta($user_identity, 'identity_verified', 0);
                            update_post_meta($post_id,'identity_verified','no');
                        }
                        
                        if(!empty($post_type) && ($post_type === 'tuturn-instructor') && ($identity_verification === 'tutors' || $identity_verification === 'both')){
                            update_user_meta($user_identity, 'identity_verified', 0);
                            update_post_meta($post_id,'identity_verified','no');
                        }

                        $post_meta  = array();
                        $post_meta['first_name']    = $first_name;
                        $post_meta['last_name']     = $last_name;
                        $post_meta['name']          = $full_name;

                        if($picture){
                            $profile_images = array();
                            if($login_type == 'google'){
                                $picture_array  = explode('=', $picture);
                                if(!empty($picture_array['0'])){
                                    $picture    = $picture_array['0'];
                                    $profile_images['image']        = $picture;
                                    $profile_images['thumbnail']    = $picture.'=w100-h100-c';
                                    $profile_images['medium']       = $picture.'=w286-h182-p';
                                    $profile_images['featureImage'] = $picture.'=w416-h281-p';
                                    $post_meta['profile_image']     = $profile_images;
                                }
                            }
                        }

                        update_post_meta($post_id, 'profile_details', $post_meta);
                        $login_url = !empty($tuturn_settings['tpl_login']) ? get_permalink($tuturn_settings['tpl_login']) : wp_login_url();

                        /* Send email to users & admin */
                        if (class_exists('Tuturn_Email_Helper')) {
                            $blogname = get_option('blogname');
                            $emailData = array();
                            $emailData['name']      = $name;
                            $emailData['email']     = $user_email;
                            $emailData['site']      = $blogname;
                            $emailData['login_url'] = $login_url;
                            /* Welcome Email */

                            if (class_exists('TuturnRegistrationEmail')) {
                                $email_helper = new TuturnRegistrationEmail();
                                $email_helper->social_registration_user_email($emailData);
                                $email_helper->new_user_register_admin_email($emailData);
                            }
                        }

                        $user_data = get_user_by('email', $user_email);
                        if (!is_user_logged_in()) {
                            if (!is_wp_error($user_data) && isset($user_data->ID) && !empty($user_data->ID)) {
                                wp_clear_auth_cookie();
                                wp_set_current_user($user_data->ID, $user_data->user_login);
                                wp_set_auth_cookie($user_data->ID, true);
                                update_user_caches($user_data);
                                do_action('wp_login', $user_data->user_login, $user_data);
                            }
                        }

                        $json_message = esc_html__("Congratulation Your account has been created successfully!", 'tuturn');
                        $dashboard              = tuturn_dashboard_page_uri( $user_data->ID,'personal_details' );
                        $json['type']           = 'success';
                        $json['message']        = $json_message;
                        $json['redirect']       = wp_specialchars_decode($dashboard);
                        wp_send_json($json);
                    } 
                }
            }

        }

    }
    add_action('wp_ajax_nopriv_tuturn_user_social_login', 'tuturn_user_social_login');
    add_action('wp_ajax_tuturn_user_social_login', 'tuturn_user_social_login');
}

/**
 * Reset Forgot Password
 * @since    1.0.0
*/
if(!function_exists('tuturn_password_reset')){
    function tuturn_password_reset(){
        global $tuturn_settings;

        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json           = array();
        $post_data      = !empty($_POST['data']) ? $_POST['data']  : '';
        $email = !empty($post_data['email']) ? sanitize_email($post_data['email']) : '';

        if ( empty( $email ) ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__('Email address is required', 'tuturn');
            wp_send_json($json, 203);
        }
        /* Validate email address */
        if ( !is_email( $email ) ) {
            $json['type']       = 'error';
            $json['message']    = esc_html__('Please add a valid email address.', 'tuturn');
            $json['title']      = esc_html__("Oops!", 'tuturn');
            wp_send_json($json, 203);
        }

        $user_data = get_user_by('email', $email);
        if (empty($user_data) ) {
            $json['type']       = "error";
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['message']    = esc_html__("The email address does not exist", 'tuturn');
            wp_send_json($json, 203);
        }
        $user_id    = $user_data->ID;
        $key        = $user_data->user_activation_key;
        if (empty($key)) {
            $key  = wp_generate_password(20, false);
            wp_update_user( array( 'ID' => $user_id, 'user_activation_key' => $key ) );
        }
        $forgot_page_url  = !empty( $tuturn_settings['tpl_reset'] ) ? get_permalink($tuturn_settings['tpl_reset']) : '';
        $reset_link       = esc_url_raw(add_query_arg(array('action' => 'reset_pwd', 'key' => $key, 'login' => $email), $forgot_page_url));
        /* Send email to user */ 
        if (class_exists('Tuturn_Email_Helper')) {
            $user_login                 = $user_data->user_login;
            $userprofile_email          = !empty($user_data->user_email) ? $user_data->user_email : '';
            $userprofile_name           = !empty($user_data->display_name) ? $user_data->display_name : '';
            $user_profile_id            = tuturn_get_linked_profile_id($user_id);
            $username                   = tuturn_get_username($user_profile_id);
            $username                   = !empty($username) ? $username : $userprofile_name;
            $blogname                   = get_option('blogname');
            $emailData                  = array();
            $emailData['name']          = $username;
            $emailData['email']         = $userprofile_email;
            $emailData['reset_link']    = $reset_link;
            $emailData['sitename']      = $blogname;
            /* Forgot password email */
            if (class_exists('TuturnRegistrationEmail')) {
                $email_helper = new TuturnRegistrationEmail();
                $email_helper->user_reset_password($emailData);
            }
        }

        $json['type']       = "success";
        $json['title']      = esc_html__("Woohoo", 'tuturn');
        $json['message']    = esc_html__("Reset password link has been sent, please check your email.", 'tuturn');
        wp_send_json($json, 200);
    }
    add_action('wp_ajax_nopriv_tuturn_password_reset', 'tuturn_password_reset');
    add_action('wp_ajax_tuturn_password_reset', 'tuturn_password_reset');
}

/**
 * Password recovery
 * userRegistration
 * @since    1.0.0
 */
if(!function_exists('tuturn_recover_password')){
    function tuturn_recover_password(){
        global $wpdb;
        $json           = array();

        $post_data  = !empty($_POST['data']) ? $_POST['data'] : '';
        parse_str($post_data, $data);
        
        $password       = !empty($data['password']) ?  sanitize_text_field($data['password'])  : '';
        $reset_key      = !empty($data['key']) ? sanitize_text_field($data['key'])  : '';
        $password_valid = userPasswordValidation($password);

        if ( !empty( $password_valid ) ) {
            $json['title']  = esc_html__("Oops!", 'tuturn');
            wp_send_json($password_valid, 203);
        }

        $user_data = $wpdb->get_row($wpdb->prepare("SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s", $reset_key));

        if (!empty($reset_key) && !empty($user_data)) {
            wp_set_password($password, $user_data->ID);
            $json['title']          = esc_html__("Woohoo", 'tuturn');
            $json['redirect']       = tuturn_get_page_uri('login');
            $json['type']           = "success";
            $json['message']        = esc_html__("Congratulation! your password has been changed.", 'tuturn');
            wp_send_json($json, 200);
        } else {
            $json['type']       = "error";
            $json['message']    = esc_html__("Oops! Invalid request", 'tuturn');
            wp_send_json($json, 203);
        }

    }
    add_action('wp_ajax_nopriv_tuturn_recover_password', 'tuturn_recover_password');
    add_action('wp_ajax_tuturn_recover_password', 'tuturn_recover_password');
}

/**
 * @init            Booking details
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 */
if(!function_exists('tu_booking_details')){
    function tu_booking_details(){
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);

        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn' );
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $booking_id = !empty($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
        $user_id    = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        if(!class_exists('WooCommerce') || empty($booking_id) || empty($user_id)){
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn');
            $json['message']    = esc_html__('Something went wrong!','tuturn');
            wp_send_json($json, 203);
        }
        $order          = wc_get_order($booking_id);
        $booking_detail = get_post_meta( $order->get_id(), 'cus_woo_product_data',true );
        $booked_data    = !empty($booking_detail['booked_data']) ? $booking_detail['booked_data'] : array();
        $profile_id     = tuturn_get_linked_profile_id( $user_id );
        $username       = tuturn_get_username($profile_id);
        $avatar         = apply_filters(
                            'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $profile_id), array('width' => 100, 'height' => 100)
                            );
        ob_start();
        ?>
        <div class="modal-header">
            <div class="tu-popimghead">
                <?php if(!empty($avatar)){?>
                    <img src="<?php echo esc_url($avatar);?>" alt="<?php echo esc_attr($username);?>">
                <?php }?>
               
                <h5><?php echo esc_html($username);?> <?php esc_html_e('booking details', 'tuturn');?></h5>
            </div>
            <a href="javascript:void(0);" class="tu-close" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
        </div>
        <div class="modal-body">
            <div class="tu-bookedslotwrapper mCustomScrollbar">
            <?php  
            if(!empty($booked_data['booked_slots'])){
                foreach($booked_data['booked_slots'] as $date=>$slots){?>
                    <div class="tu-bookedslots">
                        <h5><?php  echo date_i18n('D j F, Y', strtotime($date));?></h5>
                        <ul class="tu-checkout tu-checkoutvtwo">
                            <?php 
                            foreach($slots as $key=>$timeslot){
                                $Hour                = date("H:i", strtotime($timeslot));  
                                $values              = explode("-",$timeslot);
                                $booking_start_time  = $values[0];
                                $booking_end_time    = $values[1];
                                $booking_start_time  = substr($booking_start_time, 0, 2).':'.substr($booking_start_time, -2);
                                $booking_end_time    = substr($booking_end_time, 0, 2).':'.substr($booking_end_time, -2);
                                ?>
                                <li>
                                    <span><?php echo esc_html($booking_start_time); ?> - <?php echo esc_html($booking_end_time) ;?></span>
                                </li>                               
                            <?php }?>
                            
                        </ul>
                    </div>
                    <?php 
                }
            }?>
            </div>
        </div>
        <?php
        $booking_detail             = ob_get_clean();
        $json['type']               = 'success';    
        $json['booking_details']    = $booking_detail;  
        $json['message']            = esc_html__('booking details','tuturn' );
        wp_send_json($json);
    }
    add_action('wp_ajax_tu_booking_details', 'tu_booking_details');
}

/**
 * @init            update mailchimp Array
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 */
if(!function_exists('tuturn_mailchimp_array')){
    function tuturn_mailchimp_array(){
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);

        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn' );
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $transName  = 'latest-mailchimp-list';
        delete_transient( $transName );
        $list_array = array();
        if( function_exists('tuturn_mailchimp_list') ) {
            $list_array = tuturn_mailchimp_list();
            set_transient( $transName, $list_array, 60 * 60 * 24 );
        }
        
        $json['type']       = 'success';    
        $json['message']    = esc_html__('MailChimp is updated','tuturn' );
        wp_send_json($json);
    }
    add_action('tuturn_mailchimp_array', 'tuturn_mailchimp_array');
    add_action('wp_ajax_tuturn_mailchimp_array', 'tuturn_mailchimp_array');
}

/**
 * @init            get unavailable days of tutor 
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 */
if(!function_exists('tuturn_get_tutor_unavailable_days')){
    function tuturn_get_tutor_unavailable_days(){
        $json = $dates_arr = array();
        $userId                 = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $profileId              = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : 0;
        $booking_data           = get_post_meta($profileId, 'tuturn_bookings', true);
        $booking_data           = !empty($booking_data) ? $booking_data : array();

        if(!empty($booking_data)){
            /* saved bookings */
            $unavailable_days   = !empty($booking_data['bookings']['unavailabledays']) ? $booking_data['bookings']['unavailabledays'] : array();
            if(!empty($unavailable_days)){
                    foreach ($unavailable_days['unavailabledays'] as $book_val_){
                        $dates_arr[] = array(
                            'date_string'   => $book_val_['date_string'],
                            'today_day'     => $book_val_['today_day'],
                            'start_date'    => $book_val_['start_date'],
                            'end_date'      => $book_val_['end_date'],
                        );
                    }
                $json['type']                   = 'success';
                $json['unavailable_days_slots'] = $dates_arr;
                wp_send_json($json , 200);
            }
        }
    }
    add_action('wp_ajax_tuturn_get_tutor_unavailable_days', 'tuturn_get_tutor_unavailable_days');
}

/**
 * @init            generate timeslots of tutor
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 */
if(!function_exists('tuturn_generate_appointment_timeslots')){
    function tuturn_generate_appointment_timeslots(){
        $json       = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);

        if ( $do_check == false ) {
            $json['type'] = 'error';
            $json['title'] = esc_html__('Failed!','tuturn' );
            $json['message'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        if( function_exists('tuturn_is_demo_site') ) { 
            tuturn_is_demo_site();
        }

        $userId             = !empty($_POST['userId']) ? intval($_POST['userId']) : 0;
        $profileId          = !empty($_POST['profileId']) ? intval($_POST['profileId']) : 0;
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profileId);
        } //if user is not logged in and author check then prevent
        
        $post_data          = !empty($_POST['data']) ? $_POST['data'] : '';        

        if(!empty($post_data) && !empty($userId)){

            $userType   = apply_filters('tuturnGetUserType', $userId);

            if (!empty($userType) && $userType != 'instructor') {
                $json['type'] = 'error';
                $json['title'] = esc_html__('Failed!','tuturn' );
                $json['message']    = esc_html__('Something went wrong!','tuturn');
                wp_send_json( $json );
            }
            $weekdays               = !empty($_POST['weekdays_']) ? $_POST['weekdays_'] : array(); //only week days
            $starttime              = !empty($post_data['tu_appointment_starttime']) ? esc_html($post_data['tu_appointment_starttime']) : '';
            $tu_slot_title          = !empty($post_data['tu_slot_title']) ? esc_html($post_data['tu_slot_title']) : '';
            $endtime                = !empty($post_data['tu_appointment_endtime']) ? esc_html($post_data['tu_appointment_endtime']) : '';
            $interval               = !empty($post_data['tu_appointment_interval']) ? intval($post_data['tu_appointment_interval']) : '';
            $duration               = !empty($post_data['tu_appointment_duration']) ? intval($post_data['tu_appointment_duration']) : '';
            $appointment_spaces     = !empty($post_data['appointment_spaces']) ? $post_data['appointment_spaces'] : '';
            $custom_spaces          = !empty($post_data['appointment_custom_val']) ? intval($post_data['appointment_custom_val']) : '1';
            $week_days_slots        = !empty($_POST['week_days_slots']) ? $_POST['week_days_slots'] : array();
            $time_format            = get_option('time_format');
            if( empty($tu_slot_title) || empty($weekdays) || empty($starttime) || empty($endtime) || empty($duration) || empty($appointment_spaces)) {
                $json['type']       = 'error';
                $json['title']      = esc_html__('Validation Error!', 'tuturn');
                $json['message']    = esc_html__('Plase fill all the required fields', 'tuturn');
                wp_send_json($json , 203);
            }

            if( $starttime > $endtime) {
                $json['type']       = 'error';
                $json['title']      = esc_html__('Failed!', 'tuturn');
                $json['message']    = esc_html__('start time is less then end time', 'tuturn');
                wp_send_json($json , 203);
            }

            if( !empty( $appointment_spaces ) && $appointment_spaces === 'others' ) {
                if( empty($custom_spaces)) {
                    $json['type']       = 'error';
                    $json['title']      = esc_html__('Failed!', 'tuturn');
                    $json['message']    = esc_html__('Custom spaces value is requird.','tuturn');        
                    wp_send_json($json , 203);
                } else {
                    $appointment_spaces = $custom_spaces;
                }

            }
            $total_duration     = intval($duration) + intval($interval);
            $diff_time          = ((intval($endtime) - intval($starttime))/100)*60;
            $check_interval     = $diff_time - $total_duration;
            if( $starttime > $endtime || $check_interval <  0 ) {
                $json['type']       = 'error';
                $json['title']      = esc_html__('Failed!', 'tuturn');
                $json['message']    = esc_html__('Your end date is less then time interval.','tuturn');        
                wp_send_json($json , 203);
            }
            $timeslots_data     = get_post_meta($profileId, 'tuturn_bookings', true);
            $timeslots_data     = !empty($timeslots_data) ? $timeslots_data : array();

            if(empty($week_days_slots) && !empty($timeslots_data['bookings']['timeSlots']['bookings_slots'])){
                $timeslots_data    = $timeslots_data['bookings']['timeSlots']['bookings_slots'];
                foreach($timeslots_data as $day=>$slots){
                    $week_days_slots[$day]    = !empty($slots['slots']) ? array_values($slots['slots']) : array();
                }
            }

            $starttime_ = $starttime;
            $endtime_   = $endtime;
            $spaces_data    =  array();
            $spaces_data['spaces'] = $appointment_spaces;
            /* generate slots */
            $new_slots_array = array();
            foreach($weekdays as $day){
                $first_time_ = date($time_format, strtotime('2022-01-01' . $starttime_));
                $second_time_ = date($time_format, strtotime('2022-01-01' . $endtime_));
                if(!empty($week_days_slots[$day])){
                    $slot_arr = $previous_slots = $week_days_slots[$day];

                    foreach($slot_arr as $key=>$slot){
                        $slot_start_time    = !empty($slot['start_time']) ? $slot['start_time'] : '00:00';
                        $slot_end_time      = !empty($slot['end_time']) ? $slot['end_time'] : '00:00';
                        
                        if(
                            ($slot_start_time == $first_time_)
                            || ($second_time_ == $slot_end_time)
                            || ($slot_start_time > $first_time_ && $slot_end_time < $second_time_)
                            || ($slot_start_time < $first_time_ && ($slot_end_time < $second_time_ && $slot_end_time > $first_time_))
                            || ($second_time_ > $slot_start_time && $second_time_ < $slot_end_time)
                        ){
                            unset($week_days_slots[$day][$key]);
                        }

                    }
                }
                while($starttime < $endtime){

                    $newStartTime   = date("Hi", strtotime('+' . $duration . ' minutes', strtotime($starttime)));
                    $slots[$starttime . '-' . $newStartTime] = $spaces_data;
                    if ($interval):
                        $time_to_add = $interval + $duration;
                    else :
                        $time_to_add = $duration;
                    endif;
                    $starttime = date("Hi", strtotime('+' . $time_to_add . ' minutes', strtotime($starttime)));
                    if ($starttime == '0000'){
                        $starttime = '2400';
                    }
                }
                $new_slots_array[$day] = $slots;
            }
            /* arrange the slots according to week days */
            $days_order = array();
            $days_order = [
                'monday'    => 1,
                'tuesday'   => 2,
                'wednesday' => 3,
                'thursday'  => 4,
                'friday'    => 5,
                'saturday'  => 6,
                'sunday'    => 7,
            ];
            uksort($new_slots_array, function ($day_index, $day_val) use ($days_order) {
                    return $days_order[$day_index] > $days_order[$day_val];
            });
            /* make time slot html */
            $arr_timeslot = array();

            if(!empty($new_slots_array) && is_array($new_slots_array)){
                
                foreach( $new_slots_array as $slot_key => $slot_val ) {
                    $week_days = $slot_key;
                    $slot_arr = array();

                    if(!empty($new_slots_array[$slot_key]) && !empty($week_days_slots[$slot_key])){
                        $slot_arr = $week_days_slots[$slot_key];
                    }
                   
                    foreach($slot_val as $key=>$val){
                        $slot_key_val = explode('-', $key);
                        $first_time = date($time_format, strtotime('2022-01-01' . $slot_key_val[0]));
                        $second_time = date($time_format, strtotime('2022-01-01' . $slot_key_val[1]));
                        $slot_arr[] = array(
                            'slot_title'    => $tu_slot_title,
                            'slot_key'      =>  $key,
                            'start_time'    =>  $first_time,
                            'end_time'      =>  $second_time,
                            'slots'         =>  $val['spaces'],
                        );
                    }
                    $arr_timeslot[$week_days] = $slot_arr;
                }
            }
            $days_array             = tuturnListWeekDays();
           
            if(!empty($arr_timeslot)){   
                $timeslot_array = array();
                foreach($arr_timeslot as $day=>$slots){
                    usort($slots, function($a, $b) {
                        $a['start_time'] = strtotime('2022-01-01 ' . $a['start_time']);
                        $b['start_time'] = strtotime('2022-01-01 ' . $a['start_time']);
                        return $a['start_time'] - $b['start_time'];
                        //return new DateTime('2022-01-01' .' '. $a['start_time']) <=> new DateTime('2022-01-01' .' '. $b['start_time']);
                    });

                    $timeslot_array[$day]   = $slots;
                }
                
                $json['type']               = 'success';
                $json['week_days_slots']    = $arr_timeslot;
                $json['week_days']          = $days_array;
                wp_send_json($json , 200);
            } else {
                $json['type']       = 'error';
                $json['title']      = esc_html__('Failed!', 'tuturn');
                $json['message']    = esc_html__('No timeslot added', 'tuturn');
                wp_send_json($json, 203);
            }
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn');
            $json['message']    = esc_html__('Something went wrong!','tuturn');
            wp_send_json($json, 203);
        }

    }
    add_action('wp_ajax_tuturn_generate_appointment_timeslots', 'tuturn_generate_appointment_timeslots');
}


/**
 * @init            save timeslots of tutor
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 */
if(!function_exists('tuturn_save_appointment_timeslots')){
    function tuturn_save_appointment_timeslots(){
        $json           = array();
        $do_check       = check_ajax_referer('ajax_nonce', 'security', false);
        $userId         = !empty($_POST['userId']) ? intval($_POST['userId']) : '';
        $profileId      = !empty($_POST['profileId']) ? intval($_POST['profileId']) : '';

        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profileId);
        } //if user is not logged in and author check then prevent

        if ( $do_check == false ) {
            $json['type'] = 'error';
            $json['title'] = esc_html__('Failed!','tuturn' );
            $json['message'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        if( function_exists('tuturn_is_demo_site') ) { 
            tuturn_is_demo_site();
        }     

        $userType   = apply_filters('tuturnGetUserType', $userId);
        if (!empty($userType) && $userType != 'instructor') {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn' );
            $json['message']    = esc_html__('Something went wrong!','tuturn');
            wp_send_json( $json );
        }

        $profileId          = !empty($_POST['profileId']) ? intval($_POST['profileId']) : '';
        $timeslots_data     = get_post_meta($profileId, 'tuturn_bookings', true);
        $timeslots_data     = !empty($timeslots_data) ? $timeslots_data : array();
        $bookings           = !empty($_POST['data']) ? $_POST['data'] : '';
        
        parse_str($bookings, $bookings);
        
        $profile_details    = get_post_meta($profileId,'profile_details',true );
        $profile_details    = !empty($profile_details) ? $profile_details : array();
        $available_time     = !empty($profile_details['available_time']) ? $profile_details['available_time'] : array();
        if(!empty($profileId)){
            $booking_slots  = array();
            $orderedArray   = array();
            $weekdays           = tuturnGetWeekDays();
            foreach($weekdays as $key=>$val ){
                delete_post_meta($profileId,$key);
            }
            
            if(!empty($bookings)){

                if(!empty($bookings)){

                    $available_time  = array();
                    foreach($bookings as $book=>$val_book){
                        $booking_slots[$book] = $val_book;
                        //============
                        foreach($val_book as $day_name=>$slots){
                            $time_day_key               = tuturn_get_time_of_day_key($slots);
                            $available_time[$day_name]  = $time_day_key;
                            update_post_meta($profileId,$day_name,$time_day_key);
                        }
                        //============

                    }

                    
                    if(!empty($booking_slots['bookings_slots'])){
                        $days_order = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $ordered_slots  = $booking_slots['bookings_slots'];
                        $orderedArray = array();
                        foreach ($days_order as $key) {
                            if(!empty($ordered_slots[$key])){
                                $orderedArray[$key] = $ordered_slots[$key];
                            }
                        }

                        $booking_slots['bookings_slots']   = $orderedArray;
                    }

                }
            }
            
            $profile_details['available_time']          = $available_time;
            $timeslots_data['bookings']['timeSlots']    = $booking_slots;
            update_post_meta($profileId, 'profile_details', $profile_details);
            update_post_meta($profileId, 'tuturn_bookings', $timeslots_data);
            $json['type']       = 'success';
            $json['title']      = esc_html__('Updated!','tuturn');
            $json['message']    = esc_html__('Record has been updated','tuturn');
            wp_send_json($json , 200);
        } else{
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn');
            $json['message']    = esc_html__('Something is missing!','tuturn');
            wp_send_json($json, 203);
        }

    }
    add_action('wp_ajax_tuturn_save_appointment_timeslots', 'tuturn_save_appointment_timeslots');
}


/**
 * @init            get saved timeslots tutor
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 */
if(!function_exists('tuturn_get_instructor_appointment_slots')){
    function tuturn_get_instructor_appointment_slots(){
        $json = $book_days = array();
        $userId             = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $profileId          = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : 0;
        $timeslots_data     = get_post_meta($profileId, 'tuturn_bookings', true);
        $timeslots_data     = !empty($timeslots_data) ? $timeslots_data : array();
        $bookings           = !empty($timeslots_data['bookings']['timeSlots']['bookings_slots']) ? $timeslots_data['bookings']['timeSlots']['bookings_slots'] : array();
        $time_format        = get_option('time_format');

        $days_array             = tuturnListWeekDays();

        if(!empty($bookings) && !empty($profileId)){
            foreach($bookings as $book_key=>$book_val){
                $day_name  = $book_key;
                $slot_arr= array();
                foreach ($book_val['slots'] as $book_val_){
                    $slot_key_val   = explode('-', $book_val_['time']);
                    $first_time     = date($time_format, strtotime('2016-01-01' . $slot_key_val[0]));
                    $second_time    = date($time_format, strtotime('2016-01-01' . $slot_key_val[1]));
                    $slot_arr[]     = array(
                        'slot_title'        =>  $book_val_['slot_title'],
                        'slot_key'      =>  $book_val_['time'],
                        'start_time'    =>  $first_time,
                        'end_time'      =>  $second_time,
                        'slots'         =>  $book_val_['slot'],
                    );
                }
                $book_days[$day_name] = $slot_arr;
            }
            $json['type']               = 'success';
            $json['week_days_slots']    = $book_days;
            $json['week_days']  = $days_array;
            wp_send_json($json , 200);
        } else {
            $json['type']               = 'success';
            $json['week_days_slots']    = $book_days;
            wp_send_json($json , 200);
        }

    }
    add_action('wp_ajax_tuturn_get_instructor_appointment_slots', 'tuturn_get_instructor_appointment_slots');
}

/**
 * generate unavailable days slot of tutor
 *
 * @since    1.0.0
 */
if(!function_exists('tuturn_generate_instructor_unavailable_days')){
    function tuturn_generate_instructor_unavailable_days(){
        $userId             = !empty($_POST['userId']) ? intval($_POST['userId']) : 0;
        $userType   = apply_filters('tuturnGetUserType', $userId);

        if (!empty($userType) && $userType != 'instructor') {
            $json['type'] = 'error';
            $json['title'] = esc_html__('Failed!','tuturn' );
            $json['message']    = esc_html__('Something went wrong!','tuturn');
            wp_send_json( $json );
        }

        $profileId          = !empty($_POST['profileId']) ? intval($_POST['profileId']) : 0;
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profileId);
        } //if user is not logged in and author check then prevent

        $booking_data       = get_post_meta($profileId, 'tuturn_bookings', true);
        $booking_data       = !empty($booking_data) ? $booking_data : array();
        $date_format        = get_option('date_format');
        $unavailable_days   = !empty($_POST['selectedDate']) ? $_POST['selectedDate'] : '';
        if(!empty($profileId)){
            //get saved days slots
            $unavailable_days_saved = !empty($booking_data['bookings']['unavailable_slots']) ? $booking_data['bookings']['unavailable_slots'] : array(); 
            $days_saved = array();

            if(!empty($unavailable_days_saved)){
                foreach($unavailable_days_saved as $book_val){
                    $days_saved[] = array(
                        'today_day'     => $book_val['today_day'],
                        'date_string'   => $book_val['date_string'],
                        'start_date'    => $book_val['start_date'],
                        'end_date'      => $book_val['end_date'],
                    );
                }
            }

            if(empty($unavailable_days)){
                $json['type']       = 'error';
                $json['title']      = esc_html__('Failed!', 'tuturn');
                $json['message']    = esc_html__('Date is required', 'tuturn');
                wp_send_json($json , 203);
            }
            $selected_dated     = explode('-', $unavailable_days);
            $start_date = $end_date = strtotime(date($date_format));
            
            if(!empty($selected_dated) && is_array($selected_dated)){
                $start_date = !empty($selected_dated[0]) ? strtotime(trim($selected_dated[0])) : $start_date;
                $end_date   = !empty($selected_dated[1]) ? strtotime(trim($selected_dated[1])) : $end_date;
            }
            $today_day = date('l', $start_date);
            
            if($start_date == $end_date){
                $unavailable_days = $today_day . " - " . date($date_format, $start_date);
            }

            /* create array by incoming data */
            $dates_arr[] = array(
                'date_string'   => $unavailable_days,
                'today_day'     => $today_day,
                'start_date'    => $start_date,
                'end_date'      => $end_date
            );

            /* merge old and new unavailable days */
            $new_slots_array                = array_merge($dates_arr, $days_saved);
            $json['type']                   = 'success';
            $json['booking_unavailable']    = $new_slots_array;
            wp_send_json($json , 200);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Something went wrong!', 'tuturn');
            wp_send_json($json, 203);
        }

    }
    add_action('wp_ajax_tuturn_generate_instructor_unavailable_days', 'tuturn_generate_instructor_unavailable_days');
}


/**
 * save unavailable days tutor
 *
 * @since    1.0.0
 */
if(!function_exists('tuturn_save_instructor_unavailable_days')){
    function tuturn_save_instructor_unavailable_days(){

        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);
        $json       = array();
        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn' );
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        if( function_exists('tuturn_is_demo_site') ) { 
            tuturn_is_demo_site();
        }

        $userId                 = !empty($_POST['userId']) ? intval($_POST['userId']) : '';
        $userType   = apply_filters('tuturnGetUserType', $userId);
        if (!empty($userType) && $userType != 'instructor') {
            $json['type'] = 'error';
            $json['title'] = esc_html__('Failed!','tuturn' );
            $json['message']    = esc_html__('Something went wrong!','tuturn');
            wp_send_json( $json );
        }
        $profileId  = !empty($_POST['profileId']) ? intval($_POST['profileId']) : '';
        if( function_exists('tuturn_validate_privileges') ) { 
            tuturn_validate_privileges($profileId);
        } //if user is not logged in and author check then prevent

        $unavailable_days_data  = get_post_meta($profileId, 'tuturn_bookings', true);
        $unavailable_days_data  = !empty($unavailable_days_data) ? $unavailable_days_data : array();
        $unavailable_bookings   = !empty($_POST['data']) ? $_POST['data'] : array();
        parse_str($unavailable_bookings, $unavailable_bookings);
        if(!empty($profileId)){
            $unavailable_slots = array();

            if(!empty($unavailable_bookings)){
                foreach($unavailable_bookings as $book=>$val_book){
                    $unavailable_slots[$book] = $val_book;
                }
            }

            $unavailable_days_data['bookings']['unavailabledays'] = $unavailable_slots;
            update_post_meta($profileId, 'tuturn_bookings', $unavailable_days_data);
            $json['type']       = 'success';
            $json['title']      = esc_html__('Updated!','tuturn');
            $json['message']    = esc_html__('Record has been updated','tuturn');
            wp_send_json($json , 200);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Something went wrong!', 'tuturn');
            wp_send_json($json, 203);
        }
    }
    add_action('wp_ajax_tuturn_save_instructor_unavailable_days', 'tuturn_save_instructor_unavailable_days');
}


/**
 * get saved slots
 *
 * @since    1.0.0
 */
if(!function_exists('tuturn_get_book_appointment_step1')){
    function tuturn_get_book_appointment_step1(){
        $json                   = array();
        $studentID              = !empty($_POST['studentId']) ? intval($_POST['studentId']) : '';
        $instructor_profile_id  = !empty($_POST['instructor_profile_id']) ? intval($_POST['instructor_profile_id']) : '';
        $instructor_id          = tuturn_get_linked_profile_id($instructor_profile_id, 'post');
        $bookings_data          = get_post_meta($instructor_profile_id, 'tuturn_bookings', true);
        $bookings_data          = !empty($bookings_data) ? $bookings_data : array();
        $userType               = apply_filters('tuturnGetUserType', $studentID );
        $time_format            = get_option('time_format');

        if($userType == 'student'){
            $doday_date = date(get_option('date_format'));
            $today_day  = date('l', strtotime($doday_date));
            /* getting slots for current day */
            $booked_slots = !empty($bookings_data['bookings']['timeSlots']) ? $bookings_data['bookings']['timeSlots'] : array();
            if(!empty($booked_slots)){
                $book_days = array();
                $today_off = 'on';
                /* unavailable day */
                $unavailable_slots = !empty($profile_data['bookings']['unavailabledays']) ? $profile_data['bookings']['unavailabledays'] : array();

                if(!empty($unavailable_slots) && is_array($unavailable_slots)){
                    $today_date_str = date(strtotime($doday_date));
                    foreach($unavailable_slots as $unavailable_val){
                        if($unavailable_val['start_date'] === $unavailable_val['end_date']){
                            $unavailable_date = $unavailable_val['start_date'];
                            if($today_date_str === $unavailable_date){
                                $today_off = 'off';
                                $slot_arr = array();
                            }
                        } elseif($today_date_str >= $unavailable_val['start_date'] && $today_date_str <= $unavailable_val['start_date']){
                            $slot_arr = array();
                            $today_off = 'off';
                        }
                    }
                }

            if($today_off=='on') {
                $current_time   = date_i18n($time_format, current_time('timestamp'));
                foreach($booked_slots['bookings_slots'] as $book_key=>$book_val){
                    if($book_key === strtolower($today_day)){
                        $slot_arr   = array();
                        foreach ($book_val['slots'] as $book_val_){
                            $slots          = $book_val_['slot'];
                            $slot_key_val   = explode('-', $book_val_['time']);
                            $first_time     = date($time_format, strtotime('2022-01-01' . $slot_key_val[0]));
                            $second_time    = date($time_format, strtotime('2022-01-01' . $slot_key_val[1]));
                            $disabled       = "";

                            $post_meta_data = array(
                                '_booking_date_slot'    => date('d-m-Y', strtotime($doday_date)),
                                '_booking_slots_slot'   => $book_val_['time'],
                                'instructor_id'         => $instructor_id,
                            );
                            $count_posts            = tuturn_get_total_posts_by_multiple_meta('shop_order', array('wc-completed'), $post_meta_data);
                            $count_posts            = !empty($count_posts->found_posts) ? intval($count_posts->found_posts) : 0;
                            
                            if( ($count_posts >= $slots) ) { 
                                $disabled   = 'disabled'; 
                                $spaces     = 0;
                            } else { 
                                $spaces     = $slots - $count_posts; 
                            }

                            if(strtotime($current_time) > strtotime($second_time)){
                                $disabled = "disabled";
                            }

                            $slot_arr[]     = array(
                                'slot_key'      =>  $book_val_['time'],
                                'start_time'    =>  $first_time,
                                'end_time'      =>  $second_time,
                                'slots'         =>  $spaces,
                                'disabled'      =>  $disabled,
                                'selected_date' =>  $doday_date,
                                'selected'      =>  '',
                            );
                        }
                        $book_days = $slot_arr;
                    }
                }
            }

            $slots_results['slots']     = $book_days;
            $json['type']               = 'success';
            $json['today_day_slots']    = $slots_results;
            wp_send_json($json , 200);

        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn');
            $json['message']    = esc_html__('Today not found any slot','tuturn');
            wp_send_json($json, 203);
        }

        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed!', 'tuturn');
            wp_send_json($json, 203);
        }

    }
    add_action('wp_ajax_tuturn_get_book_appointment_step1', 'tuturn_get_book_appointment_step1');
}

/**
 * get saved slots according to 
 * date range
 *
 * @since    1.0.0
 */
if(!function_exists('tuturn_get_book_appointment_slots')){
    function tuturn_get_book_appointment_slots(){
        $json                       = array();
        $studentId                  = !empty($_POST['studentId']) ? intval($_POST['studentId']) : 0;
        $instructor_profile_id      = !empty($_POST['instructor_profile_id']) ? intval($_POST['instructor_profile_id']) : 0;
        $instructor_id              = tuturn_get_linked_profile_id($instructor_profile_id, 'post');
        $userType                   = apply_filters('tuturnGetUserType', $studentId );
        $bookings_data              = get_post_meta($instructor_profile_id, 'tuturn_bookings', true);
        $bookings_data              = !empty($bookings_data) ? $bookings_data : array();
        $selected_date              = !empty($_POST['selected_date']) ? $_POST['selected_date'] : '';
        $selected_date              = date('Y-m-d', strtotime($selected_date));
        $today_day                  = date('l', strtotime($selected_date));
        $time_format                = get_option('time_format');

        if($userType == 'student'){
            $book_days = array();
            /* getting slots for current day */
            $booked_slots = !empty($bookings_data['bookings']['timeSlots']) ? $bookings_data['bookings']['timeSlots'] : array();

            if(!empty($booked_slots)){
                $today_off = 'on';
                    /* unavailable day */
                    $unavailable_slots = !empty($profile_data['bookings']['unavailabledays']) ? $profile_data['bookings']['unavailabledays'] : array();
                    if(!empty($unavailable_slots) && is_array($unavailable_slots)){
                        $today_date_str = date(strtotime($selected_date));
                        foreach($unavailable_slots as $unavailable_val){
                            
                            if($unavailable_val['start_date']===$unavailable_val['end_date']){
                                $unavailable_date = $unavailable_val['start_date'];
                                if($today_date_str===$unavailable_date){
                                    $today_off = 'off';
                                    $slot_arr = array();
                                }
                            } elseif($today_date_str >= $unavailable_val['start_date'] && $today_date_str <= $unavailable_val['end_date']){
                                $slot_arr = array();
                                $today_off = 'off';
                            }
                        }
                    }

                    if($today_off=='on'){
                        $current_time = date_i18n($time_format, current_time('timestamp'));
                        $today_date = date('Y-m-d');
                        foreach($booked_slots['bookings_slots'] as $book_key=>$book_val){
                            
                            if($book_key == strtolower($today_day)){
                                foreach ($book_val['slots'] as $book_key_=>$book_val_){
                                    $slots = $book_val_['slot'];
                                    $slot_key_val   = explode('-', $book_val_['time']);
                                    $first_time     = date($time_format, strtotime('2016-01-01' . $slot_key_val[0]));
                                    $second_time    = date($time_format, strtotime('2016-01-01' . $slot_key_val[1]));
                                    $disabled= "";
                                    if(strtotime($current_time) > strtotime($second_time) && (strtotime($today_date) >= strtotime($selected_date))){
                                        $disabled = "disabled";
                                    }

                                    $post_meta_data = array(
                                        '_booking_date_slot'    => date('d-m-Y', strtotime($selected_date)),
                                        '_booking_slots_slot'   => $book_val_['time'],
                                        'instructor_id'         => $instructor_id,
                                    );
                                    $count_posts            = tuturn_get_total_posts_by_multiple_meta('shop_order', array('wc-completed'), $post_meta_data);
                                    $count_posts            = !empty($count_posts->found_posts) ? intval($count_posts->found_posts) : 0;

                                    
                                    if( ($count_posts >= $slots) ) {
                                        $disabled   = 'disabled'; 
                                        $spaces     = 0;
                                    } else { 
                                        $spaces     = $slots - $count_posts; 
                                    }
                                    $slot_arr[]     = array(
                                        'slot_key'      =>  $book_val_['time'],
                                        'start_time'    =>  $first_time,
                                        'end_time'      =>  $second_time,
                                        'slots'         =>  $spaces,
                                        'disabled'      =>  $disabled,
                                        'selected_date' =>  $selected_date,
                                    );
                                }
                                $book_days = $slot_arr;
                            }
                        }
                    }

                    $services_results['slots'] = $book_days;
                    $services_results['selected_date'] = $selected_date;
                    $json['type']               = 'success';
                    $json['today_day_slots']    = $services_results;
                    wp_send_json($json , 200);

            } else {
                $json['type']       = 'error';
                $json['title']      = esc_html__('Failed!','tuturn');
                $json['message']    = esc_html__('Today not found any slot','tuturn');
                wp_send_json($json, 203);
            }
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed!', 'tuturn');
            wp_send_json($json, 203);
        }
    }
    add_action('wp_ajax_tuturn_get_book_appointment_slots', 'tuturn_get_book_appointment_slots');
}

/**
 * update slot and dates
 * (=)
 * @since    1.0.0
 */
if(!function_exists('tuturn_update_book_appointment_step2')){
    function tuturn_update_book_appointment_step2(){
        $booked_data = $json = array();
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);

        if ( $do_check == false ) {
            $json['type'] = 'error';
            $json['title'] = esc_html__('Failed!','tuturn' );
            $json['message'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $studentID              = !empty($_POST['student_id']) ? $_POST['student_id'] : 0;
        $instructor_profile_id  = !empty($_POST['instructor_profile_id']) ? $_POST['instructor_profile_id'] : 0;
        $userType               = apply_filters('tuturnGetUserType', $studentID );
        $selectedSlots          = !empty($_POST['selectedSlots']) ? $_POST['selectedSlots'] : '';
        parse_str($selectedSlots, $booked_slot);
        $booked_slots           = !empty($booked_slot['booked_slot']) ? $booked_slot['booked_slot'] : array();
        
        if(empty($booked_slots)){
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Please Select atleast one slot', 'tuturn');
            wp_send_json($json, 203);
        }

        if(!empty($userType) && $userType=='student' ){
            $relations_data     = get_terms(array( 'taxonomy' => 'relations', 'hide_empty' => false));
            $relations_data     = !empty($relations_data) ? $relations_data : array();

            /* update data in transient */
            $transient_data = get_transient('tu_booked_appointment_data');
            $transient_data = !empty($transient_data) ? $transient_data : array();
            $transient_data['booked_data']['booked_slots'] = $booked_slots;
            set_transient( 'tu_booked_appointment_data', $transient_data, 600 );

            /* form info if sved */
            $formInformation = !empty($transient_data['booked_data']['information']) ? $transient_data['booked_data']['information'] : array();
            
            $services_results['student_detail']     = $formInformation;
            $services_results['info_relation']      = $relations_data;
            $json['book_student_detail']            = $services_results;
            $json['type']                           = 'success';
            $json['message']                        = esc_html__('Record has been updated.','tuturn');
            wp_send_json($json, 200);

        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed!', 'tuturn');
            wp_send_json($json, 203);
        }

    }
    add_action('wp_ajax_tuturn_update_book_appointment_step2', 'tuturn_update_book_appointment_step2');
}


/**
 * back to step2
 * (=)
 * @since    1.0.0
 */
if(!function_exists('tuturn_back_book_appointment_step2')){
    function tuturn_back_book_appointment_step2(){
        $student_id             = !empty($_POST['student_id']) ? intval($_POST['student_id']) : 0;
        $instructor_profile_id  = !empty($_POST['instructor_profile_id']) ? intval($_POST['instructor_profile_id']) : 0;
        $userType               = apply_filters('tuturnGetUserType', $student_id );

        if($userType == 'student'){
            $book_days      = getAppointmentSelectedSlots($instructor_profile_id);
            $json['type']               = 'success';
            $json['filter_day_slots']   = $book_days;
            wp_send_json($json , 200);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed!', 'tuturn');
            wp_send_json($json, 203);
        }

    }
    add_action('wp_ajax_tuturn_back_book_appointment_step2', 'tuturn_back_book_appointment_step2');
}

/**
 * information form
 * (=)
 * @since    1.0.0
 */
if(!function_exists('tuturn_send_booked_form_information')){
    function tuturn_send_booked_form_information(){
        global $tuturn_settings;

        $json       = array();
        $objData    = $_POST['dataObj'];
        $total_price = 0;
        $do_check   = check_ajax_referer('ajax_nonce', 'security', false);

        if ( $do_check == false ) {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!','tuturn' );
            $json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        $info_someOne = !empty($objData['info_someone_else']) ? $objData['info_someone_else'] : 'off';
        if($info_someOne == 'on'){
            $validations = array(
                'info_first_name'       => esc_html__('First name is required', 'tuturn'),
                'info_last_name'        => esc_html__('Last name is required', 'tuturn'),
                'info_email'            => esc_html__('Email is required', 'tuturn'),
                'info_phone'            => esc_html__('Phone is required', 'tuturn'),
                'info_address'          => esc_html__('Address is required', 'tuturn'),
                'info_relation'         => esc_html__('Relation is required', 'tuturn'),
                'description'           => esc_html__('Description is required', 'tuturn'),
                'info_verified'         => esc_html__('Accept term and condition is required', 'tuturn'),
            );
            foreach ($validations as $key => $value) {
                if (isset($objData[$key]) && empty($objData[$key])) {
                    $json['title']      = esc_html__("Oops!", 'tuturn');
                    $json['type']       = 'error';
                    $json['message']    = $value;
                    wp_send_json($json);
                }
            }
        }

        $studentID              = !empty($objData['student_id']) ? $objData['student_id'] : '';
        $instructor_profile_id  = !empty($objData['instructor_profile_id']) ? $objData['instructor_profile_id'] : '';
        $instructor_id          = tuturn_get_linked_profile_id($instructor_profile_id, 'post');
        $userType               = apply_filters('tuturnGetUserType', $studentID );
        $info_someone_else      = !empty($objData['info_someone_else']) ? esc_html($objData['info_someone_else']) : '';
        $info_first_name        = !empty($objData['info_first_name']) ? sanitize_text_field($objData['info_first_name']) : '';
        $info_last_name         = !empty($objData['info_last_name']) ? sanitize_text_field($objData['info_last_name']) : '';
        $full_name              = (!empty($info_first_name) && !empty($info_last_name)) ? ($info_first_name. " " . $info_last_name) : '';
        $info_email             = !empty($objData['info_email']) ? sanitize_email($objData['info_email']) : '';
        $info_phone             = !empty($objData['info_phone']) ? esc_html($objData['info_phone']) : '';
        $info_address           = !empty($objData['info_address']) ? sanitize_text_field($objData['info_address']) : '';
        $info_relation          = !empty($objData['info_relation']) ? esc_html($objData['info_relation']) : '';
        $info_desc              = !empty($objData['description']) ? sanitize_textarea_field($objData['description']) : '';
        $info_verified          = !empty($objData['info_verified']) ? esc_html($objData['info_verified']) : '';
        $hourly_rate            = get_post_meta( $instructor_profile_id,'hourly_rate',true );
        $time_format            = get_option('time_format');

        if($userType == 'student'){
            $relation_name = !empty(get_term( $info_relation )->name) ? get_term( $info_relation )->name : '';
                if(!empty($info_someone_else) && $info_someone_else === 'on'){
                    if(empty($info_first_name) || empty($info_last_name) || empty($info_email) || empty($info_phone) || empty($info_address) || empty($info_relation)){
                        $json['type']       = 'error';
                        $json['title']      = esc_html__('Oops!', 'tuturn');
                        $json['message']    = esc_html__('Some fields are required', 'tuturn');
                        wp_send_json($json, 203);
                    }
                    if( empty($info_verified) ){
                        $json['type']       = 'error';
                        $json['title']      = esc_html__('Oops!', 'tuturn');
                        $json['message']    = esc_html__('Some fields are required.', 'tuturn');
                        wp_send_json($json, 203);
                    }
                    $old_data = array(
                        'info_someone_else'     => $info_someone_else,
                        'info_first_name'       => $info_first_name,
                        'info_last_name'        => $info_last_name,
                        'info_full_name'        => $full_name,
                        'info_email'            => $info_email,
                        'info_phone'            => $info_phone,
                        'info_address'          => $info_address,
                        'info_relation'         => $relation_name,
                        'relation_id'           => $info_relation,
                        'info_desc'             => $info_desc,
                        'info_verified'         => $info_verified,
                    );
                } else {
                    $old_data = array();
                }

                /* get transient data if exist */
                $transit_data           = get_transient('tu_booked_appointment_data');
                $transit_data           = !empty($transit_data) ? $transit_data : array();
                $trans_saved_slot       = !empty($transit_data['booked_data']['booked_slots']) ? $transit_data['booked_data']['booked_slots'] : array();
                $trans_booked_ids       = !empty($transit_data['booked_data']['booked_ids']) ? $transit_data['booked_data']['booked_ids'] : array();
                
                 /* calculate price according to hours rate */
                 $final_price = 0;
                 if(!empty($trans_saved_slot)){
                     $time_in_minutes = 0;

                     foreach($trans_saved_slot as $slot_date=>$slot_time_arr){
                         foreach($slot_time_arr as $innerTimeSlots){
                            $slot_time_val          = explode('-', $innerTimeSlots);
                            $first_time             = strtotime('2022-01-01' . $slot_time_val[0]);
                            $second_time            = strtotime('2022-01-01' . $slot_time_val[1]);
                            $time_diff_in_minutes   = ($second_time-$first_time)/60;
                            $time_in_minutes = $time_in_minutes + $time_diff_in_minutes;
                         }
                     }

                    /* hourly rate per minutes */
                    $hourly_rate        = !empty($hourly_rate) ? $hourly_rate : 0;
                    $price_per_minutes  = $hourly_rate/60;
                    /* calculate final price */
                    $final_price = ($time_in_minutes*$price_per_minutes);
                 }

                 $product_id    = tuturn_instructor_service_create($final_price);
    
                 $transit_data['booked_data']['booked_slots'] = $trans_saved_slot;
                 $transit_data['booked_data']['information']  = (!empty($info_someone_else) && $info_someone_else === 'on' && !empty($old_data)) ? $old_data : array();
                 $transit_data['booked_data']['booked_ids']   = $product_id;
                 $transit_data['booked_data']['final_price']   = $final_price;
                 set_transient( 'tu_booked_appointment_data', $transit_data, 600 );

                /* getting saved appointment subjects */
                $service_title_arr = $data_array = array();  
                $saved_data = get_transient('tu_booked_appointment_data');
                if(!empty($saved_data['booked_data'])){
                    
                    foreach($saved_data['booked_data'] as $saved_key=>$saved_value){
                        if($saved_key === 'booked_ids'){
                            if(!empty($saved_value)){
                                $total_price = 0;
                                $product = wc_get_product( $saved_value );
                                $service_title = !empty($product->get_name()) ? $product->get_name() : '';
                                $service_slug = !empty($product->get_slug()) ? $product->get_slug() : '';
                                $service_price = $final_price;

                                if( ! empty($service_price) ){
                                    $total_price = $total_price + $service_price;
                                }

                                $service_arr[] = array(
                                    'service_id'            => $saved_value,
                                    'service_name'          => $service_title,
                                    'service_slug'          => $service_slug,
                                    'service_price'         => html_entity_decode(tuturn_price_format($service_price, 'return')),
                                );

                                $service_title_arr[] = $service_title;
                                $data_array['service_detail']   = $service_arr;
                                $data_array['total_price']      = html_entity_decode(tuturn_price_format($total_price, 'return'));
                                $data_array['total_int_price']  = $total_price;
                            }
                        }

                        if($saved_key === 'booked_slots'){
                            $date_slot_arr = tuturn_get_choosed_date_slots($saved_value);
                            $data_array['total_booked_slots'] = $date_slot_arr;
                        }

                        if($saved_key === 'information'){
                            if(!empty($saved_value) && is_array($saved_value)){
                                foreach($saved_value as $inf_key=>$info_val){
                                    $service_infor[$inf_key] = $info_val;
                                }
                                $data_array['service_info'] = $service_infor;
                            }
                        }
                    }
                }

                $booking_option             = !empty($tuturn_settings['booking_option']) ? $tuturn_settings['booking_option'] : 'yes';
                $allow_free_booking         = !empty($tuturn_settings['allow_free_booking']) ? $tuturn_settings['allow_free_booking'] : 'no';
                
                if(!empty($booking_option) && $booking_option == 'yes'){
                    if(!empty($allow_free_booking) && $allow_free_booking == 'yes' && empty($total_price)){
                        $json['booking_option']      = 'no';
                    }else{
                        $json['booking_option']      = 'yes';
                    } 
                }else{
                    $json['booking_option']      = 'no';
                }

                $services_results['student_detail'] = $data_array;
                $json['type']                       = 'success';
                $json['booked_student_detail']      = $services_results;
                wp_send_json($json, 200);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed!', 'tuturn');
            wp_send_json($json, 203);
        }
    }
    add_action('wp_ajax_tuturn_send_booked_form_information', 'tuturn_send_booked_form_information');
}

/**
 * back to form
 *
 * @since    1.0.0
 */
if(!function_exists('tuturn_back_book_appointment_step3')){
    function tuturn_back_book_appointment_step3(){
        $json       = array();
        $student_Id         = !empty($_POST['student_id']) ? intval($_POST['student_id']) : 0;
        $userType           = apply_filters('tuturnGetUserType', $student_Id );
        $relations_data     = get_terms(array( 'taxonomy' => 'relations', 'hide_empty' => false));
        $relations_data     = !empty($relations_data) ? $relations_data : array();

        if($userType == 'student'){
            $data_transient = get_transient('tu_booked_appointment_data');
            $data_form      = !empty($data_transient['booked_data']['information']) ? $data_transient['booked_data']['information'] : array();

            $services_results['student_detail']     = $data_form;
            $services_results['info_relation']      = $relations_data;
            $json['type']                           = 'success';
            $json['book_student_detail']            = $services_results;
            wp_send_json($json, 200);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed!', 'tuturn');
            wp_send_json($json, 203);
        }
    }
    add_action('wp_ajax_tuturn_back_book_appointment_step3', 'tuturn_back_book_appointment_step3');
}

/**
 * Service checkout
 *
 * @since    1.0.0
 */
if(!function_exists('tuturn_service_checkout')){
    function tuturn_service_checkout(){

        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }
        $json               = array();
        $student_userId     = !empty($_POST['studentId']) ? intval($_POST['studentId']) : 0;
        $instructor_id      = !empty($_POST['instructor_id']) ? intval($_POST['instructor_id']) : 0;
        $data_transient     = get_transient('tu_booked_appointment_data'); 
        $products           = !empty($data_transient['booked_data']['booked_ids']) ? $data_transient['booked_data']['booked_ids'] : array();
        $single_price       = !empty($data_transient['booked_data']['final_price']) ? $data_transient['booked_data']['final_price'] : 0.0;

        if ( class_exists('WooCommerce') && !empty($products) ) {
            global $woocommerce;
            check_prerequisites($student_userId);
            $woocommerce->cart->empty_cart();
            $total_price    = 0.0;
            $total_price    = $total_price + $single_price;
            $data_transient['booked_data']['product']['price']  = $single_price;
            $data_transient['booked_data']['product']['name']   = esc_html__('Tuition service', 'tuturn');
            $service_fee        = tuturn_commission_fee($total_price);
            $admin_shares       = !empty($service_fee['admin_shares']) ? $service_fee['admin_shares'] : 0.0;
            $instructor_shares  = !empty($service_fee['instructor_shares']) ? $service_fee['instructor_shares'] : $total_price;
            $product_id         = !empty($products) ? intval($products) : 0;
            $booked_data        = !empty($data_transient['booked_data']) ? $data_transient['booked_data'] : array();
            $cart_meta['product_id']        = $product_id;
            $cart_meta['total_amount']      = $total_price;
            $cart_meta['price']             = $total_price;
            $cart_meta['booked_data']       = $booked_data;
            $cart_meta['student_id']        = $student_userId;
            $cart_meta['instructor_id']     = $instructor_id;
            $cart_meta['admin_shares']      = $admin_shares;
            $cart_meta['instructor_shares'] = $instructor_shares;
            $cart_meta['payment_type']      = 'booking';
            $cart_data = array(
                'cart_data'             => $cart_meta,
                'price'                 => $total_price,
                'payment_type'          => 'booking',
                'admin_shares'          => $admin_shares,
                'instructor_shares'     => $instructor_shares,
                'instructor_id'         => $instructor_id,
                'student_id'            => $student_userId,
            );

            $woocommerce->cart->empty_cart();
            $cart_item_data = $cart_data;
            WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);

            if (session_status() === PHP_SESSION_NONE) {
                session_start(['read_and_close' => true]);
                $_SESSION["redirect_type"]  = 'service_checkout';
                $_SESSION["redirect_url"]   = !empty($_POST['url']) ? esc_url_raw($_POST['url']): '';
            }
            
            $json['title']              = esc_html__('All set to go', 'tuturn');
            $json['type']               = 'success';
            $json['message']            = esc_html__("You're now redirecting to the checkout page", 'tuturn');
            $json['checkout_url']       = wc_get_checkout_url();
            $json['cart_item_data']     = $cart_item_data;
            wp_send_json($json, 200);

        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Please install WooCommerce plugin to process this order', 'tuturn');
            wp_send_json($json);
        }

    }
    add_action('wp_ajax_tuturn_service_checkout', 'tuturn_service_checkout');
}

/**
 * Service checkout
 * When Allow booking set to "without checkout page"
 *
 * @since    1.0.0
 */
if(!function_exists('tuturn_service_complete_booking')){
    function tuturn_service_complete_booking(){
        global $current_user, $woocommerce;

        if (function_exists('tuturn_is_demo_site')) {
            tuturn_is_demo_site();
        }

        $json               = array();
        $student_userId     = !empty($_POST['studentId']) ? intval($_POST['studentId']) : 0;
        $instructor_id      = !empty($_POST['instructor_id']) ? intval($_POST['instructor_id']) : 0;
        $data_transient     = get_transient('tu_booked_appointment_data'); 
        $products           = !empty($data_transient['booked_data']['booked_ids']) ? $data_transient['booked_data']['booked_ids'] : array();
        $single_price       = !empty($data_transient['booked_data']['final_price']) ? $data_transient['booked_data']['final_price'] : 0.0;
      
        if ( class_exists('WooCommerce') && !empty($products) ) {
            global $woocommerce;
            check_prerequisites($student_userId);
            $woocommerce->cart->empty_cart();
            $total_price    = 0.0;
            $data_transient['booked_data']['product']['price']  = $total_price;
            $data_transient['booked_data']['product']['name']   = esc_html__('Tuition service', 'tuturn');
            $service_fee        = tuturn_commission_fee($total_price);
            $admin_shares       = !empty($service_fee['admin_shares']) ? $service_fee['admin_shares'] : 0.0;
            $instructor_shares  = !empty($service_fee['instructor_shares']) ? $service_fee['instructor_shares'] : $total_price;
            $product_id         = !empty($products) ? intval($products) : 0;
            $booked_data        = !empty($data_transient['booked_data']) ? $data_transient['booked_data'] : array();
            $cart_meta['product_id']        = $product_id;
            $cart_meta['total_amount']      = $total_price;
            $cart_meta['price']             = $total_price;
            $cart_meta['booked_data']       = $booked_data;
            $cart_meta['student_id']        = $student_userId;
            $cart_meta['instructor_id']     = $instructor_id;
            $cart_meta['admin_shares']      = $admin_shares;
            $cart_meta['instructor_shares'] = $instructor_shares;
            $cart_meta['payment_type']      = 'booking';
            $cart_data = array(
                'cart_data'             => $cart_meta,
                'price'                 => $total_price,
                'payment_type'          => 'booking',
                'admin_shares'          => $admin_shares,
                'instructor_shares'     => $instructor_shares,
                'instructor_id'         => $instructor_id,
                'student_id'            => $student_userId,
            );

            $woocommerce->cart->empty_cart();
            $cart_item_data = $cart_data;
            WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);
            
            $order_id               = tuturn_place_order($current_user->ID, 'booking');
            $profile_settings_url   = tuturn_dashboard_page_uri( $current_user->ID,'personal_details' );;
            update_post_meta( $order_id, 'booking_type', 'without_checkout' );

            if (session_status() === PHP_SESSION_NONE) {
                session_start(['read_and_close' => true]);
                $_SESSION["redirect_type"]  = 'service_checkout';
                $_SESSION["redirect_url"]   = !empty($_POST['url']) ? esc_url_raw($_POST['url']): '';
            }
            
            $json['title']              = esc_html__('All set to go', 'tuturn');
            $json['type']               = 'success';
            $json['message']            = esc_html__("You're now redirecting to the checkout page", 'tuturn');            
            $json['checkout_url']       = esc_url(add_query_arg(array('tab'=>'booking-listings'), $profile_settings_url));
            $json['cart_item_data']     = $cart_item_data;
            wp_send_json($json, 200);

        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('Please install WooCommerce plugin to process this order', 'tuturn');
            wp_send_json($json);
        }

    }
    add_action('wp_ajax_tuturn_service_complete_booking', 'tuturn_service_complete_booking');
}


/**
 * Get days slots
 * after filter
 * (=)
 * @since    1.0.0
 */
if(!function_exists('tuturn_get_filtered_days_slots')){
    function tuturn_get_filtered_days_slots(){
        global $post,$tuturn_settings;
        $allow_free_booking   = !empty($tuturn_settings['allow_free_booking']) ? $tuturn_settings['allow_free_booking'] : 'no';

        $json   = array();
        if(!class_exists('WooCommerce')){
            $json['type']       = 'error';
            $json['title']      = esc_html__('Error!', 'tuturn');
            $json['message']    = esc_html__('Woocommerce is missing!', 'tuturn');
            wp_send_json($json);
        }

        $slots_filter = $unavailable_dates = array();
        $time_format                = get_option('time_format');
        $formData                   = !empty($_POST['data']) ? $_POST['data'] : array(); 
        $instructor_profile_id      = !empty($formData['instructor_profile_id']) ? intval($formData['instructor_profile_id']) : 0;
        $instructor_id              = tuturn_get_linked_profile_id($instructor_profile_id, 'post');
        $student_Id                 = !empty($formData['student_id']) ? intval($formData['student_id']) : 0;
        $user_type                  = apply_filters('tuturnGetUserType', $student_Id );
        $filtered_data              = !empty($formData['filtered_data']) ? ($formData['filtered_data']) : array();
        parse_str($filtered_data, $data);
        $start_date                 = !empty($data['tu_start_date']) ? strtotime($data['tu_start_date']) : '';
        $end_date                   = !empty($data['tu_end_date']) ? strtotime($data['tu_end_date']) : '';
        $start_time                 = !empty($data['tu_start_time']) ? strtotime($data['tu_start_time']) : '';
        $end_time                   = !empty($data['tu_end_time']) ? strtotime($data['tu_end_time']) : '';
        $weekDays                   = !empty($data['weekDays']) ? $data['weekDays'] : array();
        $hourly_rate                = get_post_meta( $instructor_profile_id,'hourly_rate',true );
        $hourly_rate                = !empty($hourly_rate) ? $hourly_rate : 0;
        $bookings_data              = get_post_meta($instructor_profile_id, 'tuturn_bookings', true);
        $bookings_data              = !empty($bookings_data['bookings']) ? $bookings_data['bookings'] : array();

        /* empty week days */
        if(empty($hourly_rate) && !empty($allow_free_booking) && $allow_free_booking == 'no'){
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['type']       = 'error';
            $json['message']    = esc_html__('Hourly rate not set by the instructor!', 'tuturn');
            wp_send_json($json);
        }

        if(empty($start_date) || empty($end_date) || empty($start_time) || empty($end_time)){
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['type']       = 'error';
            $json['message']    = esc_html__('Some fields are missing!', 'tuturn');
            wp_send_json($json);
        }

        /* empty week days */
        if(empty($weekDays)){
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['type']       = 'error';
            $json['message']    = esc_html__('Please choose days!', 'tuturn');
            wp_send_json($json);
        }

        /* start date greater than end date */
        if($end_date < $start_date){
            $json['title']      = esc_html__("Oops!", 'tuturn');
            $json['type']       = 'error';
            $json['message']    = esc_html__('Start date is greater than end date!', 'tuturn');
            wp_send_json($json);
        }

        if($user_type == 'student'){
            $booking_data   = array();
            /* save form value temporary */
            $booking_data['booked_data']['filter_form']['start_date']  = !empty($start_date) ? date('d-m-Y',$start_date) : '';
            $booking_data['booked_data']['filter_form']['end_date']    = !empty($end_date) ? date('d-m-Y',$end_date) : '';
            $booking_data['booked_data']['filter_form']['start_time']  = !empty($data['tu_start_time']) ? $data['tu_start_time'] : '';
            $booking_data['booked_data']['filter_form']['end_time']    = !empty($data['tu_end_time']) ? $data['tu_end_time'] : '';
            $booking_data['booked_data']['filter_form']['week_days']   = !empty($weekDays) ? $weekDays : array();
            set_transient( 'tu_booked_appointment_data', $booking_data, 600 );
            $counter = 1;
            $current_time       = date_i18n($time_format, current_time('timestamp'));
            $all_booking_slots  = $bookings_data['timeSlots']['bookings_slots'];
            if($all_booking_slots){
                $unavailable_days = !empty($bookings_data['unavailabledays']) ? $bookings_data['unavailabledays'] : array();
                /* create array by unavailable days/dates */
                if(!empty($unavailable_days['unavailabledays']) && is_array($unavailable_days['unavailabledays'])){

                    foreach($unavailable_days['unavailabledays'] as $unavailable_val){
                        $unavailable_startDate = $unavailable_val['start_date'];
                        $unavailable_endDate = $unavailable_val['end_date'];

                        if($unavailable_startDate === $unavailable_endDate){
                            $unavailable_dates[] = $unavailable_val['start_date'];
                        } else {
                            /* looping on selected dates */
                            for ( $unavailableDate = $unavailable_startDate; $unavailableDate <= $unavailable_endDate; $unavailableDate = ($unavailableDate + 86400) ) {
                                $unavailable_dates[] = $unavailableDate;
                            }
                        }
                    }
                }
                
                /* looping on selected dates */
                $book_days = array();
                for ( $filter_date = $start_date; $filter_date <= $end_date; $filter_date = ($filter_date + 86400) ) {
                    $after_filter_date = '';
                    /* ignore unavailable days */
                    $checkUnavailable = in_array($filter_date, $unavailable_dates);
                    if($checkUnavailable == true){
                        continue;
                    }
                    
                    $after_filter_date = $filter_date;
                    $find_dayName_by_date   = date('l', $after_filter_date);
                    $day_by_filter_date = strtolower($find_dayName_by_date);
                    /*slots only available days */
                    $remaining_filter_days = in_array($day_by_filter_date, $weekDays, false);

                    if($remaining_filter_days == true){
                        /* getting slots according to day */
                        foreach($all_booking_slots as $slot_key=>$slot_val){

                            if($slot_key === $day_by_filter_date ){
                                $slot_arr= array();                                

                                foreach ($slot_val['slots'] as $book_val_){
                                    $first_time = $second_time = '';
                                    $slots = $book_val_['slot'];
                                    $slot_key_val   = explode('-', $book_val_['time']);
                                    $first_time     = date($time_format, strtotime('2022-01-01' . $slot_key_val[0]));
                                    $second_time    = date($time_format, strtotime('2022-01-01' . $slot_key_val[1]));
                                    /* filter the slots */
                                    if($start_time <= strtotime($first_time) && $end_time >= strtotime($second_time) ){
                                        $disabled= "";
                                        /* slot count if buy */                            
                                        $count_posts            = 0;
                                        if( ($count_posts >= $slots) ) { 
                                            $disabled   = 'disabled'; 
                                            $spaces     = 0;
                                        } else { 
                                            $spaces     = $slots - $count_posts; 
                                        }

                                        if( (strtotime($current_time) >= strtotime($second_time)) && (strtotime(date("d-m-Y")) == $filter_date)){
                                            $disabled = "disabled";
                                        }
                                        $counter++;
                                        $slot_arr[]     = array(
                                            'dateString'    =>  $after_filter_date,
                                            'date_key'      => 'date-slot-'.$counter,
                                            'counter'       =>  $counter,                                            
                                            'date'          =>  date_i18n('d-m-Y',$after_filter_date),
                                            'day'           =>  date_i18n('l', $after_filter_date),
                                            'slot_key'      =>  $book_val_['time'],
                                            'slot_title'    =>  $book_val_['slot_title'],
                                            'start_time'    =>  $first_time,
                                            'end_time'      =>  $second_time,
                                            'slots'         =>  $spaces,
                                            'disabled'      =>  $disabled,
                                            'selected'      =>  '',
                                        );
                                    }
                                }

                                $book_days[$after_filter_date] = $slot_arr;
                                $book_days[$after_filter_date]['date'] = date_i18n('d M, Y',$after_filter_date);
                                $book_days[$after_filter_date]['day'] = date_i18n('l', $after_filter_date);
                                $book_days[$after_filter_date]['date_key'] = 'date-slot-'.$counter;
                            }
                        }
                    }
                }
                
                $post_meta_data = array(
                    'instructor_id'         => $instructor_id,
                );
                $previous_order_key_query   = tuturn_get_total_posts_by_multiple_meta('shop_order',array('wc-completed'),$post_meta_data);

                if ( $previous_order_key_query->have_posts() ) {
                    while ( $previous_order_key_query->have_posts() ) {
                        $previous_order_key_query->the_post(); 
                        $booking_status = get_post_meta($post->ID, 'booking_status', true);
                        $_booking_slots = get_post_meta($post->ID, '_booking_slots', true);

                        if($booking_status  == 'publish' || $booking_status == 'pening'){

                            if(!empty($_booking_slots) && is_array($_booking_slots)){
                                foreach($_booking_slots as $date=>$slot){
                                    foreach($book_days as $slotkey=>$slot_array){
                                        foreach($slot_array as $slotvalkey=>$slot_val){
                                            if(!empty($slot_val['date']) && $date == $slot_val['date'] && !empty($slot_val['slot_key']) && in_array($slot_val['slot_key'], $slot) ){
                                                $slot_count  = $slot_val['slots'];
                                                $slot_count = (int)$slot_count-1;
                                                $book_days[$slotkey][$slotvalkey]['disabled']  = '';

                                                if($slot_count < 1){
                                                    $book_days[$slotkey][$slotvalkey]['disabled']  = 'disabled';
                                                }                                                   
                                                $book_days[$slotkey][$slotvalkey]['slots']  = $slot_count;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                $json['type']               = 'success';
                $json['filter_day_slots']   = $book_days;
                wp_send_json($json , 200);
            } else {
                $json['type']       = 'error';
                $json['title']      = esc_html__('Failed!', 'tuturn');
                $json['message']    = esc_html__('No slot available!', 'tuturn');
                wp_send_json($json);
            }
            
            $json['type']           = 'success';
            $json['message']        = esc_html__('Result', 'tuturn');;
            $json['slots_filter']   = $slots_filter;
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['title']      = esc_html__('Failed!', 'tuturn');
            $json['message']    = esc_html__('You are not allowed!', 'tuturn');
            wp_send_json($json);
        }

    }
    add_action('wp_ajax_tuturn_get_filtered_days_slots', 'tuturn_get_filtered_days_slots');
}


/**
 * Proposal start chat
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('wp_guppy_start_chat')) {
    function wp_guppy_start_chat()
    {
        global $current_user;
        
        if( function_exists('tuturn_verify_token') ){
            tuturn_verify_token($_POST['security']);
        }

        $json               = array();
        $receiverId          = !empty($_POST['post_id']) ? intval($_POST['post_id']): '';

        do_action('wpguppy_send_message_to_user',$current_user->ID,$receiverId,'');

        $inbox_url  = tuturn_get_page_uri('inbox');

        $inbox_url  = add_query_arg(
            array(
                'chat_type' => 1,
                'chat_id'   => $receiverId.'_'.'1',
                'type'      => 'messanger',
            ),
            $inbox_url
        );

        $json['type']           = 'success';
        $json['redirect']       = $inbox_url;
        $json['message_desc']   = esc_html__('Woohoo! your message has been sent successfully', 'tuturn');
        wp_send_json( $json );

    }
    add_action( 'wp_ajax_wp_guppy_start_chat', 'wp_guppy_start_chat' );
}

/**
 * Download single file
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_download_single_attachment')) {
    function tuturn_download_single_attachment()
    {
        global $current_user;
        $post_id            = !empty($_POST['post_id']) ? intval($_POST['post_id']) : '';
        $post_author        = get_post_field('post_author',$post_id );
        $response           = array();
        if( (is_admin( ) || $post_author === $current_user->ID ) && !empty($post_id) && !empty($_POST['attachment_id']) ) {
            $response           = Tuturn_file_permission::downloadFile($_POST['attachment_id']);
        } else {
            $response['type']       = 'error';
            $response['title']      = esc_html__('Restricted Access', 'tuturn');
            $response['message']    = esc_html__('You are not allowed to perform this action.', 'tuturn');
        }
        wp_send_json($response);

    }
    add_action( 'wp_ajax_tuturn_download_single_attachment', 'tuturn_download_single_attachment' );
}

/* Download attachmenst
 *
 * @throws error
 * @return 
 */
if (!function_exists('tuturn_download_attachments')) {

    function tuturn_download_attachments()
    {
        global $current_user;
        $post_id            = !empty($_POST['post_id']) ? intval($_POST['post_id']) : '';
        $verification_info  = get_post_meta($post_id,'verification_info',true);
        $attachments        = !empty($verification_info['attachments']) ? $verification_info['attachments'] : array();
        $post_author        = get_post_field('post_author',$post_id );
        $response           = array();
        if( (is_admin( ) || $post_author === $current_user->ID ) && !empty($post_id) ) {
            $response           = Tuturn_file_permission::downloadZipFile($post_id,$attachments);
        } else {
            $response['type']       = 'error';
            $response['title']      = esc_html__('Restricted Access', 'tuturn');
            $response['message']    = esc_html__('You are not allowed to perform this action.', 'tuturn');
        }
        wp_send_json($response);
    }

    add_action('wp_ajax_tuturn_download_attachments', 'tuturn_download_attachments');
}

/**
 * Delete verificaton post 
 * on Cancel&Resubmit verification 
 */
if (!function_exists('tuturn_cancel_resend_verification')) {
    function tuturn_cancel_resend_verification()
    {
        global $current_user;
        $json               = array();
        $post_id            = !empty($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $user_id            = $current_user->ID;

        if (!empty($post_id)) {
            wp_delete_post($post_id, true);

            update_user_meta($user_id, 'verification_attachments', '');
            update_user_meta($user_id, 'identity_verified', 0);

            $profileId      = tuturn_get_linked_profile_id($user_id);
            update_post_meta($profileId, 'identity_verified', 'no');

            $json['type']         = 'success';
            $json['message']     = esc_html__('Verification has been deleted.', 'tuturn');
            $json['redirect']     = wp_specialchars_decode(tuturn_dashboard_page_uri($user_id, 'user-verification'));
            wp_send_json($json);
        } else {
            $json['type']       = "error";
            $json['message']    = esc_html__("Something is missing!", 'tuturn');
            wp_send_json($json, 203);
        }
    }
}
add_action('wp_ajax_tuturn_cancel_resend_verification', 'tuturn_cancel_resend_verification');