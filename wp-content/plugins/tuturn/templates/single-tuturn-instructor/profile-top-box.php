<?php

/**
 * Instructor profile top box
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */

global $post, $current_user, $tuturn_settings;
$persoanl_detail = $contact_detail = $education_detail = $subject_detail = $media_detail = array();
$page_url       = set_url_scheme(get_permalink());

$booking_option             = !empty($tuturn_settings['booking_option']) ? $tuturn_settings['booking_option'] : 'yes';
$student_package_option     = !empty($tuturn_settings['student_package_option']) ? $tuturn_settings['student_package_option'] : false;
$instructor_package_option     = !empty($tuturn_settings['package_option']) ? $tuturn_settings['package_option'] : false;
$sub_categories_price       = !empty($tuturn_settings['sub_categories_price']) ? $tuturn_settings['sub_categories_price'] : false;
$show_address_type          = !empty($tuturn_settings['show_address_type']) ? $tuturn_settings['show_address_type'] : 'address';
$profile_state              = !empty($tuturn_settings['profile_state']) ? $tuturn_settings['profile_state'] : false;

if(!empty($instructor_package_option)){
    if (empty($args['package_info']['allowed']) || (!empty($args['userType']) && $args['userType'] !== 'student')) {
        $booking_option = 'disable';
    }
}

$user_type      = apply_filters('tuturnGetUserType', $current_user->ID);

if (!empty($student_package_option) && !empty($user_type) && $user_type === 'student') {
    $package_info          = apply_filters('tuturn_user_package', $current_user->ID);

    if (empty($package_info['allowed'])) {
        $booking_option = 'disable';
    }
}

$username       = tuturn_get_username($post->ID);
$tagline        = !empty($profile_details['tagline']) ? $profile_details['tagline'] : '';
$languages      = !empty($profile_details['languages']) ? $profile_details['languages'] : '';
$contact_info   = !empty($profile_details['contact_info']) ? $profile_details['contact_info'] : array();
$phone          = !empty($contact_info['phone']) ? $contact_info['phone'] : '';
$email_address  = !empty($contact_info['email_address']) ? $contact_info['email_address'] : '';
$skypeid        = !empty($contact_info['skypeid']) ? $contact_info['skypeid'] : '';
$whatsapp       = !empty($contact_info['whatsapp_number']) ? $contact_info['whatsapp_number'] : '';
$website        = !empty($contact_info['website']) ? $contact_info['website'] : '';
$location       = get_post_meta($post->ID, '_address', true);
$hourly_rate    = get_post_meta($post->ID, 'hourly_rate', true);
$country        = get_post_meta($post->ID, '_country', true);
$zipcode        = get_post_meta($post->ID, '_zipcode', true);
$is_verified    = get_post_meta($post->ID, '_is_verified', true);
$featured_expriy_date   = get_post_meta($post->ID, 'featured_expriy_date', true);
$featured_profile       = get_post_meta($post->ID, 'featured_profile', true);
$current_date_time        = date("Y-m-d H:i:s");
$address                 = get_post_meta($post->ID, '_address', true);
$teaching_preference    = get_post_meta($post->ID, 'teaching_preference', true);
$introduction           = get_post_field('post_content', $post->ID);
$mediaGallery           = get_post_meta($post->ID, 'media_gallery', true);
$user_languages            = !empty($profile_details['languages']) ? 5 : 0;
$teaching_preference    = !empty($teaching_preference) ? 5 : 0;
$hourly_rate_            = !empty($hourly_rate) ? 5 : 0;
$address                 = !empty($address) ? 5 : 0;
$introduction             = !empty($introduction) ? 5 : 0;
$mediaGallery            = !empty($mediaGallery)     ? 10     : 0;
$phone                    = !empty($profile_details['contact_info']['phone']) ? 5 : 0;
$skypeid                = !empty($profile_details['contact_info']['skypeid']) ? 5 : 0;
$website                = !empty($profile_details['contact_info']['website']) ? 5 : 0;
$whatsapp_number        = !empty($profile_details['contact_info']['whatsapp_number']) ? 5 : 0;
$email_address            = !empty($profile_details['contact_info']['email_address']) ? 5 : 0;
$education              = !empty($profile_details['education']) ? 15 : 0;
$subject                = !empty($profile_details['subject']) ? 25 : 0;

$persoanl_detail    = $user_languages + $teaching_preference + $hourly_rate_ + $address + $introduction;
$contact_detail     = $phone + $skypeid + $website + $whatsapp_number + $email_address;
$education_detail   = $education;
$subject_detail     = $subject;
$media_detail       = $mediaGallery;

$profile_conpletion = array(
    'personal_detail'   => $persoanl_detail,
    'contact_detail'    => $contact_detail,
    'education_deatil'  => $education_detail,
    'subject_detail'    => $subject_detail,
    'media_detail'      => $media_detail,
);

$profile_conpletion = apply_filters('tutturn_instructor_profile_completion', $profile_conpletion, $post->ID);
$percentage = 0;
foreach ($profile_conpletion as $key => $value) {
    $percentage = $percentage + $value;
}

$avatar = apply_filters(
    'tuturn_avatar_fallback',
    tuturn_get_user_avatar(array('width' => 400, 'height' => 400), $post->ID),
    array('width' => 100, 'height' => 100)
);

$featured_expriy_date   = get_post_meta($post->ID, 'featured_expriy_date', true);
$featured_profile       = get_post_meta($post->ID, 'featured_profile', true);
$current_date_time        = date("Y-m-d H:i:s");

//Check if current user is author
$hide_data            = false;
if (!empty($current_user->ID) && !empty($user_id) && $current_user->ID == $user_id) {
    $hide_data            = true;
}

if (!empty($show_address_type) && $show_address_type != 'address' && $profile_state == true) {
    $list_adress        = array();
    $country_region     = get_post_meta( $post->ID, '_country_region', true);
    $_country           = get_post_meta( $post->ID, '_country', true);
    $_state             = get_post_meta( $post->ID, '_state', true);
    $_city              = get_post_meta( $post->ID, '_city', true);

    if(!empty($_city)){
        $list_adress[]    = $_city;
    }

    $states             = !empty($country_region) ? tuturn_country_array($country_region,'') : array();

    if(!empty($states) && !empty($states[strtoupper($_state)])){
        $list_adress[]      = $states[strtoupper($_state)];
    }
   
    if (!empty($show_address_type) && $show_address_type == 'city_state_country' && !empty($_country)){
        $countries 		    = tuturn_country_array();
        $list_adress[]      = !empty($countries[strtoupper($_country)]) ? $countries[strtoupper($_country)] : $_country;
    }
    
    $location   = !empty($list_adress) ? implode(', ',$list_adress) : $location;
}

?>
<div class="tu-tutorprofilewrapp">
    <div class="tu-profileview">
        <?php if (!empty($avatar)) { ?>
            <figure>
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($username); ?>">
            </figure>
        <?php } ?>
        <div class="tu-protutorinfo">
            <div class="tu-protutordetail">
                <div class="tu-productorder-content">
                    <?php if (!empty($avatar)) { ?>
                        <figure>
                            <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($username); ?>">
                        </figure>
                    <?php } ?>
                    <div class="tu-product-title">
                        <?php if (!empty($username) || !empty($is_verified)) { ?>
                            <h3>
                                <?php if (!empty($username)) {
                                    echo esc_html($username);
                                } ?>
                                <?php if (!empty($is_verified)) { ?><i class="icon icon-check-circle  tu-icongreen"></i><?php } ?>
                                <?php if ($featured_profile == 'yes') {
                                    if (strtotime($featured_expriy_date) > strtotime($current_date_time)) { ?>
                                        <span class="tu-cardtag"></span>
                                <?php }
                                } ?>
                            </h3>
                        <?php }

                        if (!empty($tagline)) { ?>
                            <h5><?php echo esc_html($tagline) ?></h5>
                        <?php } ?>
                    </div>
                    <?php if (!empty($hourly_rate)) { ?>
                        <div class="tu-startingrate">
                            <span><?php esc_html_e('Starting from:', 'tuturn') ?></span>
                            <h4><?php echo tuturn_price_format($hourly_rate) ?><?php esc_html_e('/hr', 'tuturn'); ?></h4>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="tu-infoprofile">
                <ul class="tu-tutorreview">
                    <?php
                        $tu_average_rating      = get_post_meta($post->ID, 'tu_average_rating', true);
                        $tu_average_rating        = !empty($tu_average_rating) ? $tu_average_rating : 0;
                        $tu_review_users        = get_post_meta($post->ID, 'tu_review_users', true);
                        $tu_review_users        = !empty($tu_review_users) ? $tu_review_users : 0;
                    ?>
                    <li>
                        <span><i class="fa fa-star tu-coloryellow"> <em><?php echo number_format((float)$tu_average_rating, 1, '.', ''); ?> <span>/<?php echo number_format(5.0, 1, '.', ''); ?></span></em> </i> <em>(<?php echo str_pad($tu_review_users, 2, '0', STR_PAD_LEFT); ?>)</em></span>
                    </li>
                    <li>
                        <span><i class="fa fa-check-circle tu-colorgreen"><em><?php echo esc_html($percentage) ?><?php esc_html_e('%', 'tuturn') ?></em></i><em><?php esc_html_e('Profile completion', 'tuturn') ?></em></span>
                    </li>
                    <?php if (!empty($location)) { ?>
                        <li>
                            <span><i class="icon icon-map-pin"></i><em><?php echo esc_html($location) ?></em></span>
                        </li>
                    <?php } ?>
                </ul>
                <?php if (!empty($languages)) { ?>
                    <div class="tu-detailitem">
                        <h6><?php esc_html_e('Languages I know', 'tuturn') ?> </h6>
                        <div class="tu-languagelist">
                            <ul class="tu-languages">
                                <?php
                                $count          = 6;
                                $counter_langs  = 0;
                                $language_arr     = [];
                                foreach ($languages as $language) {
                                    $counter_langs++;
                                    if ($counter_langs <= $count) {
                                ?><li> <?php echo esc_html($language); ?> </li>
                                    <?php } else {
                                        $language_arr[]  =     esc_html($language);
                                    }
                                }
                                if (($counter_langs) > $count && !empty($language_arr)) { ?>
                                    <li>
                                        <a class="tu-showmore tu-tooltip-tags" href="javascript:void(0);" data-tippy-trigger="<?php esc_attr_e('click', 'tuturn') ?> " data-template="tu-industrypro" data-tippy-interactive="true" data-tippy-placement="top-start"> <?php echo sprintf(esc_html__('+%02d more', 'tuturn'), intval($counter_langs) - $count); ?></a>
                                        <div id="tu-industrypro" class="tu-tippytooltip d-none">
                                            <div class="tu-selecttagtippy tu-tooltip ">
                                                <ul class="tu-posttag tu-posttagv2">
                                                    <?php foreach ($language_arr as $item) { ?>
                                                        <li>
                                                            <a href="javascript:void(0);"><?php echo esc_html($item) ?></a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="tu-actionbts">
        <?php if (!empty($page_url)) { ?>
            <a href="javascript:void(0);"><i class="icon icon-globe"></i><span>
                    <p id="urlcopy" class="tu-tiny-url"><?php echo esc_html($post->post_name); ?></p><i id="copyurl" class="icon icon-copy copytext"></i>
                </span></a>
        <?php } ?>
        <?php if (!$hide_data) { ?>
            <ul class="tu-profilelinksbtn">
                <li>
                    <?php do_action('tuturn_instructor_add_to_save', array('post' => $post, 'label' => esc_html__('Save', 'tuturn'))); ?>
                </li>
                <?php if(apply_filters( 'tuturn_chat_solution_guppy',false ) === true ){
                        $class              = '';
                        if(is_user_logged_in()){
                            $tuturn_inbox_url   = apply_filters('tuturn_guppy_inbox_url', $user_id);
                            $class              = 'wpguppy_start_chat';
                        } else {
                            $redirect_url       = apply_filters('tuturn_guppy_inbox_url', $user_id);
                            $tuturn_inbox_url   = tuturn_get_page_uri('login');
                            $tuturn_inbox_url   = add_query_arg(array('redirect'=>"$redirect_url"), $tuturn_inbox_url );

                        }
                    ?>
                    <li><a href="<?php echo esc_url_raw($tuturn_inbox_url);?>" data-receiver_id="<?php echo esc_attr($user_id);?>" class="tu-secbtn <?php echo esc_attr($class);?>"><?php esc_html_e('Let\'s talk now','tuturn') ?></a></li>
                <?php }?>
                <?php if(!empty($booking_option) && $booking_option !== 'disable'){?>
                    <li><?php do_action('tuturn_booking_button',$current_user->ID,$post->ID );?></li>
                <?php }?>
         </ul>
         <?php }?>
    </div>
</div>
<?php
if (!empty($page_url)) {
    $script = '
        function makeTinyUrl(url)
        {
            jQuery.get("https://tinyurl.com/api-create.php?url=" + url, function(shorturl){        
                jQuery("#urlcopy").html("<a target=_blank href="+shorturl+">"+shorturl+"</a>");
            });            
        }
        makeTinyUrl("' . $page_url . '");
    ';
    wp_add_inline_script('tuturn-public', $script, 'after');
}
