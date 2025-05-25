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
$location               = get_post_meta( $post->ID,'_address',true );
$average_rating         = get_post_meta( $post->ID,'tu_average_rating',true );
$review_users           = get_post_meta( $post->ID,'tu_review_users',true );
$userType	            = apply_filters('tuturnGetUserType', $current_user->ID); 
$student_profile_id    	= tuturn_get_linked_profile_id($current_user->ID );
$favourite_instructor   = get_post_meta($student_profile_id, 'favourite_instructor', true);
$instructor_link    = get_the_permalink($profile_id);
$instructor_id          = tuturn_get_linked_profile_id($profile_id, 'post'); 
$average_rating         = !empty($average_rating) ? number_format($average_rating,1) : 0;
$review_users           = !empty($review_users) ? intval($review_users) : 0;
?>
<div class="tu-listinginfo tu-listinginfo_two">
    <div class="tu-listing-slider">
        <?php do_action('tuturn_featured_profile', $post->ID);?>
        <?php do_action('tuturn_instructor_image_gallery', $post); ?>
        <a class="tu-btn" href="<?php echo esc_url($instructor_link);?>"><?php echo esc_html__('View Profile','tuturn');?></a>
    </div>
    <div class="tu-listinginfo_wrapper">
        <div class="tu-listinginfo_title">
            <div class="tu-listinginfo-img">
                <?php do_action('tuturn_instructor_image', $post); ?>
                <div class="tu-listing-heading">
                    <?php do_action('tuturn_instructor_title', $post); ?>
                    <div class="tu-listing-location">
                        <?php do_action('tuturn_instructor_location',$post); ?>
                        <div class="tu-iconheart">
                            <?php do_action('tuturn_instructor_add_to_save', array('post'=>$post, 'label' => esc_html__('Save', 'tuturn'))); ?>                            
                        </div>
                    </div>
                </div>
            </div>
            <?php do_action('tuturn_instructor_hourly_rate', $post); ?>
        </div>
        <?php do_action('tuturn_instructor_avilibility', $post); ?>
        <?php do_action('tuturn_teaching_preference', $post); ?>
        <?php do_action('tuturn_instructor_short_description', $post); ?>
        <?php do_action('tuturn_instructor_subjects', $post); ?>
    </div>
</div>