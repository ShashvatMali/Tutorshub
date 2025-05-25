<?php
/**
 *
 * The template used for displaying instructor data
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $post, $current_user;
get_header();
$loggedInUser = $current_user->ID;
$userType 	= apply_filters('tuturnGetUserType', $loggedInUser );
while (have_posts()) : the_post();
	$user_id        = tuturn_get_linked_profile_id($post->ID,'post');
	$editOption     = apply_filters('allowEditProfile', $loggedInUser, $post->ID);
	$user_data      = get_post_meta($post->ID, 'profile_details', true);
	$user_data      = !empty($user_data) ? $user_data : array();
    $tuturn_args    =  $dom_data = $business_hours =  $working_detail = array();   
    $tuturn_args['post_id']         = $post->ID;
	$tuturn_args['user_id']         = $user_id;
	$tuturn_args['profile_details'] = $user_data;
	$tuturn_args['editOption']      = $editOption;
    $package_info                   = apply_filters('tuturn_user_package', $user_id);
    $package_info                   = ! empty($package_info) ? $package_info : array();
	$tuturn_args['package_info']    = $package_info;
	$tuturn_args['userType']        = $userType;
    ?>	
    <section class="tu-main-section">
        <div class="container">
            <div class="row gy-4">
                <div class="col-12 col-xxl-9">
                    <?php tuturn_get_template( 'single-tuturn-instructor/profile-top-box.php',$tuturn_args);?> 
                    <?php tuturn_get_template( 'single-tuturn-instructor/profile-book-appointment.php',$tuturn_args);?>
                    <?php tuturn_get_template( 'single-tuturn-instructor/profile-tabs.php',$tuturn_args);?>
                    <?php tuturn_get_template( 'single-tuturn-instructor/community-ads.php',$tuturn_args);?>                   
                    <?php tuturn_get_template( 'single-tuturn-instructor/related-instructors.php',$tuturn_args);?>
                </div>
                <div class="col-12 col-xxl-3">
                    <?php tuturn_get_template( 'single-tuturn-instructor/aside-info.php',$tuturn_args);?>
                </div>
            </div>
        </div>
    </section> 
    <?php     
endwhile;
get_footer();
