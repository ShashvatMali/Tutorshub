<?php 
/**
 * Provider search listing view V1
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructor-search
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */

global $post, $current_user;
$profile_id             = !empty($post->ID) ? intval($post->ID) : 0 ;
$average_rating         = get_post_meta( $post->ID,'tu_average_rating',true );
$review_users           = get_post_meta( $post->ID,'tu_review_users',true );
$profile_details        = get_post_meta($profile_id, 'profile_details', true);
$profile_details        = !empty($profile_details) ? $profile_details : array();  
$location               = get_post_meta( $profile_id,'_address',true );
$userType	            = apply_filters('tuturnGetUserType', $current_user->ID);
$student_profile_id    	= tuturn_get_linked_profile_id($current_user->ID );
$favourite_instructor   = get_post_meta($student_profile_id, 'favourite_instructor', true);
$instructor_id          = tuturn_get_linked_profile_id($profile_id, 'post');
$average_rating         = !empty($average_rating) ? number_format($average_rating,1) : 0;
$review_users           = !empty($review_users) ? intval($review_users) : 0;
$featured_expriy_date   = get_post_meta( $profile_id,'featured_expriy_date',true );
$featured_profile       = get_post_meta( $profile_id,'featured_profile',true );
$current_date_time		= date("Y-m-d H:i:s");


?>
<div class="tu-listinginfo tu-listinginfovthree">
    <?php
    if($featured_profile == 'yes'){
        if(strtotime($featured_expriy_date) > strtotime($current_date_time)){?>
        <span class="tu-cardtag"></span>
    <?php }}?>
    <div class="tu-listinginfo_wrapper">
        <div class="tu-listinginfo_title">
            <div class="tu-listinginfo-img">
                <?php do_action('tuturn_instructor_image', $post); ?>
                <div class="tu-listing-heading">
                    <?php do_action('tuturn_instructor_title', $post); ?>
                    <div class="tu-listing-location">
                        <span><?php echo esc_html($average_rating)?><i class="fa-solid fa-star"></i>
                        <em>(<?php echo str_pad($review_users, 2, '0', STR_PAD_LEFT);?>)</em>
                        <?php if(!empty($location)) {?></span><address><i class="icon icon-map-pin"></i><?php echo esc_html($location)?></address> <?php } ?>
                    </div>
                </div>
            </div>
            <?php do_action('tuturn_instructor_hourly_rate', $post); ?>
        </div>
        <?php do_action('tuturn_instructor_avilibility', $post); ?>
        <?php do_action('tuturn_instructor_short_description', $post); ?>
        <?php do_action('tuturn_teaching_preference', $post); ?>
    </div>
    <div class="tu-listinginfo_btn">
        <div class="tu-iconheart">
            <?php do_action('tuturn_instructor_add_to_save', array('post'=>$post, 'label' => esc_html__('Add to save', 'tuturn'))); ?>
        </div>
        <div class="tu-btnarea">
            <?php if(apply_filters( 'tuturn_chat_solution_guppy',false ) === true){
                if(is_user_logged_in()){
                    $tuturn_inbox_url   = apply_filters('tuturn_guppy_inbox_url', $instructor_id);
                    $chat_class         = 'wpguppy_start_chat';
                    $chat_with          = $instructor_id;
                } else {
                    $tuturn_inbox_url   = tuturn_get_page_uri('login');
                    $chat_class         = '';
                    $chat_with          = '';
                }?>
            <a href="<?php echo esc_url($tuturn_inbox_url);?>" data-receiver_id="<?php echo esc_attr($chat_with);?>"  class="tu-secbtn <?php echo esc_attr($chat_class);?>"><?php esc_html_e('Let’s chat','tuturn')?> </a>
            <?php } ?>
            <a href="<?php echo esc_url(get_permalink()); ?>" class="tu-primbtn"><?php esc_html_e('View full profile','tuturn')?> </a>
        </div>
    </div>
</div>