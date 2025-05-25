<?php

/**
 * Provider search listing view v4
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructor-search
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post, $tuturn_settings, $current_user;
$show_address_type          = !empty($tuturn_settings['show_address_type']) ? $tuturn_settings['show_address_type'] : 'address';
$profile_state              = !empty($tuturn_settings['profile_state']) ? $tuturn_settings['profile_state'] : false;
$teach_settings         = !empty($tuturn_settings['teach_settings']) ? $tuturn_settings['teach_settings'] : 'default';
$conteact_details       = !empty($tuturn_settings['hide_conteact_details']) ? $tuturn_settings['hide_conteact_details'] : false;
$profile_id             = !empty($post->ID) ? intval($post->ID) : 0;
$instructor_user_id     = tuturn_get_linked_profile_id($profile_id, 'post');
$student_profile_id        = tuturn_get_linked_profile_id($current_user->ID);
$profile_details        = get_post_meta($profile_id, 'profile_details', true);
$profile_details        = !empty($profile_details) ? $profile_details : array();
$teaching_subject       = !empty($profile_details['subject']) ? $profile_details['subject'] : '';
$username               = tuturn_get_username($profile_id);
$tagline                = !empty($profile_details['tagline']) ? $profile_details['tagline'] : '';
$education_listings     = !empty($profile_details['education']) ? $profile_details['education'] : array();
$contact_info           = !empty($profile_details['contact_info']) ? $profile_details['contact_info'] : '';
$phone                  = !empty($contact_info['phone']) ? esc_html($contact_info['phone']) : '';
$whatsapp_number        = !empty($contact_info['whatsapp_number']) ? esc_html($contact_info['whatsapp_number']) : '';
$skypeid                = !empty($contact_info['skypeid']) ? esc_html($contact_info['skypeid']) : '';
$email_address          = !empty($contact_info['email_address']) ? esc_html($contact_info['email_address']) : '';
$location               = get_post_meta($profile_id, '_address', true);
$is_verified            = get_post_meta($post->ID, '_is_verified', true);
$hourly_rate            = get_post_meta($profile_id, 'hourly_rate', true);
$teaching_preference    = get_post_meta($profile_id, 'teaching_preference', true);
$single_image           = TUTURN_DIRECTORY_URI . 'public/images/placeholder-612x400.png';
$favourite_instructor   = get_post_meta($student_profile_id, 'favourite_instructor', true);
$userType                = apply_filters('tuturnGetUserType', $current_user->ID);
$package_info           = apply_filters('tuturn_user_package', $instructor_user_id);
$package_info            = !empty($package_info) ? $package_info : array();

$pkg_contact_info       = !empty($package_info) ? intval($package_info['contact_info'])  : '';
$pkg_languages_info     = !empty($package_info) ? intval($package_info['languages'])  : '';
$pkg_education_info     = !empty($package_info) ? intval($package_info['education'])  : '';
$pkg_teaching_info      = !empty($package_info) ? intval($package_info['teaching'])  : '';

$tu_average_rating      = get_post_meta($profile_id, 'tu_average_rating', true);
$tu_average_rating        = !empty($tu_average_rating) ? $tu_average_rating : 0;
$tu_review_users        = get_post_meta($profile_id, 'tu_review_users', true);
$tu_review_users        = !empty($tu_review_users) ? $tu_review_users : 0;

$featured_expriy_date   = get_post_meta($profile_id, 'featured_expriy_date', true);
$featured_profile       = get_post_meta($profile_id, 'featured_profile', true);
$current_date_time      = date("Y-m-d H:i:s");

if (!is_user_logged_in() || (empty($package_info['allowed']) &&  $userType == 'student')) {
    $whatsapp_number    = tuturn_maskPhone($whatsapp_number);
    $phone              = tuturn_maskPhone($phone);
    $skypeid            = tuturn_maskPhone($skypeid);
    $email_address      = tuturn_maskEmailAddress($email_address);
}

$show_conteact_details  = 'show';
if (!empty($conteact_details)) {
    if (is_user_logged_in() && $userType == 'student') {
        $post_meta_data    = array(
            'instructor_id'         => $instructor_user_id,
            'student_id'            => $current_user->ID
        );

        $previous_order_key_query   = tuturn_get_total_posts_by_multiple_meta('shop_order', array('wc-completed'), $post_meta_data);
        $show_conteact_details      = isset($previous_order_key_query->found_posts) ? intval($previous_order_key_query->found_posts) : 0;
        if (empty($show_conteact_details)) {
            $show_conteact_details  = 'hide';
        }
    }
}

if ((!empty($show_conteact_details) && $show_conteact_details === 'hide')
    || !is_user_logged_in()
    || (empty($args['package_info']['allowed']) &&  $userType == 'student')
    || ($userType == 'instructor' && $current_user->ID !== $instructor_user_id)
) {
    $whatsapp_number    = tuturn_maskPhone($whatsapp_number);
    $phone              = tuturn_maskPhone($phone);
    $skypeid            = tuturn_maskSkypeAddress($skypeid);
    $email_address      = tuturn_maskEmailAddress($email_address);
}

$any_image  = false;

/* gallery image */
$mediaGallery       = get_post_meta($profile_id, 'media_gallery', true);
$mediaGallery        = !empty($mediaGallery) ? $mediaGallery : array();
if (!empty($mediaGallery)) {
    foreach ($mediaGallery as $item) {
        $attachmentType = !empty($item['attachment_type']) ? $item['attachment_type'] : '';
        if ($attachmentType == 'image') {
            $any_image  = true;
            $single_imaged = wp_get_attachment_image_src($item['attachment_id'], 'tu_blog_medium');
            $single_image = !empty($single_imaged[0]) ? $single_imaged[0] : $single_image;
            break;
        }
        continue;
    }
}

if (empty($mediaGallery) || $any_image == false) {
    if (!empty($tuturn_settings['defaul_instructor_profile']['id'])) {
        $placeholder_id = !empty($tuturn_settings['defaul_instructor_profile']['id']) ? $tuturn_settings['defaul_instructor_profile']['id'] : '';
        $img_atts       = wp_get_attachment_image_src($placeholder_id, 'tu_blog_medium');
        $single_image   = !empty($img_atts['0']) ? $img_atts['0'] : $single_image;
    } else {
        $single_image  = !empty($tuturn_settings['defaul_instructor_profile']['url']) ? $tuturn_settings['defaul_instructor_profile']['url'] : $single_image;
    }
}

if (!empty($show_address_type) && $show_address_type != 'address' && $profile_state == true) {
    $list_adress        = array();
    $country_region     = get_post_meta($post->ID, '_country_region', true);
    $_country           = get_post_meta($post->ID, '_country', true);
    $_state             = get_post_meta($post->ID, '_state', true);
    $_city              = get_post_meta($post->ID, '_city', true);

    if (!empty($_city)) {
        $list_adress[]    = $_city;
    }

    $states             = !empty($country_region) ? tuturn_country_array($country_region, '') : array();

    if (!empty($states) && !empty($states[strtoupper($_state)])) {
        $list_adress[]      = $states[strtoupper($_state)];
    }

    if (!empty($show_address_type) && $show_address_type == 'city_state_country' && !empty($_country)) {
        $countries             = tuturn_country_array();
        $list_adress[]      = !empty($countries[strtoupper($_country)]) ? $countries[strtoupper($_country)] : $_country;
    }

    $location   = !empty($list_adress) ? implode(', ', $list_adress) : $location;
}

?>
<div class="tu-featureitem">
    <?php if (!empty($single_image)) { ?>
        <figure>
            <img src="<?php echo esc_url($single_image) ?>" alt="<?php esc_attr_e('image', 'tuturn') ?>">
            <?php if ($featured_profile == 'yes') {
                if (strtotime($featured_expriy_date) > strtotime($current_date_time)) { ?>
                    <span class="tu-featuretag"><?php esc_html_e('FEATURED', 'tuturn'); ?></span>
            <?php }
            } ?>
        </figure>
    <?php } ?>
    <div class="tu-authorinfo">
        <div class="tu-authordetail">
            <div class="tu-authorinfo_head">
                <?php do_action('tuturn_instructor_image', $post); ?>
                <?php if (!empty($username)) { ?>
                    <div class="tu-authorname">
                        <h5><a href="<?php echo get_permalink(); ?>"><?php echo esc_html($username); ?></a> <?php if (!empty($is_verified)) { ?><i class="icon icon-check-circle tu-greenclr"></i><?php } ?></h5>
                        <?php if (!empty($location)) { ?>
                            <span><?php echo esc_html($location) ?></span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <ul class="tu-authorlist">
                <?php if (!empty($hourly_rate)) { ?>
                    <li>
                        <span><?php esc_html_e('Starting from', 'tuturn'); ?>:<em><?php tuturn_price_format($hourly_rate) . esc_html_e('/hr', 'tuturn') ?></em></span>
                    </li>
                <?php } ?>

                <?php if (!empty($phone)) { ?>
                    <li>
                        <span><?php esc_html_e('Mobile', 'tuturn'); ?>:<em><?php echo do_shortcode($phone); ?></em></span>
                    </li>
                <?php } ?>

                <?php if (!empty($whatsapp_number)) { ?>
                    <li>
                        <span><?php esc_html_e('Whatsapp', 'tuturn'); ?>:<em><?php echo do_shortcode($whatsapp_number); ?></em></span>
                    </li>
                <?php } ?>
                <?php if (!empty($education_listings)) {
                    $degree_title   = '';

                    if (isset($education_listings) && is_array($education_listings) && count($education_listings) > 0) {
                        foreach ($education_listings as $key => $education) {
                            $degree_title  = $education['degree_title'];
                            break;
                        }

                        if (!empty($degree_title)) {
                            $degree_title   = explode(", ", $degree_title);

                            if (!empty($degree_title[0])) {
                                $degree_title  = $degree_title[0];
                            }
                        }
                    } ?>
                    <li><span><?php esc_html_e('Qualification', 'tuturn'); ?>:<em><?php echo esc_html($degree_title) ?></em></span></li>
                <?php } ?>
            </ul>
        </div>
        <div class="tu-instructors_footer">
            <div class="tu-rating">
                <h6><?php echo number_format((float)$tu_average_rating, 1, '.', ''); ?></h6>
                <i class="fas fa-star"></i>
                <span>(<?php echo str_pad($tu_review_users, 2, '0', STR_PAD_LEFT); ?>)</span>
            </div>
            <div class="tu-instructors_footer-right">
                <?php do_action('tuturn_instructor_add_to_save', array('post' => $post, 'label' => '')); ?>
            </div>
        </div>
    </div>
</div>