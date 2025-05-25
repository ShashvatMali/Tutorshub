<?php 
/**
 * Provider search listing view v3
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructor-search
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post, $tuturn_settings, $current_user;
$profile_id                 = !empty($post->ID) ? intval($post->ID) : 0 ;
$student_profile_id    	    = tuturn_get_linked_profile_id($current_user->ID );
$profile_details            = get_post_meta($profile_id, 'profile_details', true);
$profile_details            = !empty($profile_details) ? $profile_details : array();  
$teaching_subject           = !empty($profile_details['subject']) ? $profile_details['subject'] : ''; 
$username                   = !empty($profile_details['name']) ? $profile_details['name'] : '';
$tagline                    = !empty($profile_details['tagline']) ? $profile_details['tagline'] : '';
$location                   = get_post_meta( $profile_id,'_address',true );   
$is_verified                = get_post_meta( $post->ID,'_is_verified',true );
$hourly_rate                = get_post_meta($profile_id,'hourly_rate',true );
$default_image_placeholder  = TUTURN_DIRECTORY_URI . 'public/images/placeholder-default.jpg';
$favourite_instructor       = get_post_meta($student_profile_id, 'favourite_instructor', true);
$userType			        = apply_filters('tuturnGetUserType', $current_user->ID);

$tu_average_rating          = get_post_meta($profile_id, 'tu_average_rating', true );
$tu_average_rating		    = !empty($tu_average_rating) ? $tu_average_rating : 0;
$tu_review_users		    = get_post_meta( $profile_id, 'tu_review_users', true );
$tu_review_users		    = !empty($tu_review_users) ? $tu_review_users : 0;

$avatar = apply_filters(
    'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $profile_id), array('width' => 100, 'height' => 100)
);

$default_placeholder    = !empty($tuturn_settings['empty_listing_image']['id']) ? wp_get_attachment_image_src($tuturn_settings['empty_listing_image']['id'], 'tu_blog_medium') : '';
$default_placeholder    = !empty($default_placeholder) ? $default_placeholder[0] : $default_image_placeholder;

/* gallery image */
$mediaGallery       = get_post_meta($profile_id, 'media_gallery', true);
$mediaGallery	    = ! empty($mediaGallery) ? $mediaGallery : array();
if(!empty( $mediaGallery )){
    foreach( $mediaGallery as $item){
        $attachmentType = ! empty( $item['attachment_type'] ) ? $item['attachment_type'] : '';
        if( $attachmentType === 'image' ) {
            $single_imaged = wp_get_attachment_image_src($item['attachment_id'], 'tu_blog_medium');
            $default_placeholder = !empty($single_imaged[0]) ? $single_imaged[0] : $default_placeholder;
            break;
        }
        continue;
    }
} 
?>
<div class="tu-instructors">
    <?php if(!empty($default_placeholder)){ ?>
        <figure>
            <img src="<?php echo esc_url($default_placeholder)?>" alt="<?php esc_attr_e('image','tuturn')?>">
            <?php do_action('tuturn_featured_profile', $post->ID);?>
        </figure>
    <?php } ?>
    <div class="tu-instructors_content">
        <div class="tu-instructors_header">
            <?php do_action('tuturn_instructor_image', $post); ?>
            <?php
            if(!empty($username) || !empty($location)) {?>
                <div class="tu-instructors_title">
                    <?php if(!empty($username)){?>
                        <a href="<?php echo esc_url(get_permalink()); ?>">
                            <h5>
                                <?php if(!empty($username)){?>
                                    <span><?php echo esc_html($username)?></span>
                                <?php }
                                if(!empty($is_verified)) {?>
                                    <i class="icon icon-check-circle"></i>
                                <?php } ?>
                            </h5>
                        </a>
                    <?php }
                    if(!empty($location)){?>
                        <span><?php echo esc_html($location)?></span>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php if(!empty($hourly_rate)){ ?>
            <div class="tu-instructors_price">
                <span><?php esc_html_e('Starting from:','tuturn')?> </span>
                <h5><?php tuturn_price_format($hourly_rate). esc_html_e('/hr','tuturn')?></h5>
            </div>
        <?php } ?>
        <?php do_action('tuturn_teaching_preference', $post); ?>
        <div class="tu-instructors_footer">
            <div class="tu-rating">
                <h6><?php echo number_format((float)$tu_average_rating, 1, '.', '');?></h6>
                <i class="fas fa-star"></i>
                <span>(<?php echo str_pad($tu_review_users, 2, '0', STR_PAD_LEFT);?>)</span>
            </div>
            <div class="tu-instructors_footer-right">
                <?php do_action('tuturn_instructor_add_to_save', array('post'=>$post, 'label' => '')); ?>                
            </div>
        </div>
    </div>
</div>
    