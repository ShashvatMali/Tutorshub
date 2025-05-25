<?php
/**
 * Instructor feature image
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructors-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings,$post;
$profile_id = !empty($post->ID) ? intval($post->ID) : '';
$user_name  = tuturn_get_username($profile_id);
$user_dp    = TUTURN_DIRECTORY_URI . 'public/images/default-avatar.jpg';

if(!empty($tuturn_settings['defaul_instructor_profile']['id'])){
    $placeholder_id = !empty($tuturn_settings['defaul_instructor_profile']['id']) ? $tuturn_settings['defaul_instructor_profile']['id'] : '';
    $img_atts       = wp_get_attachment_image_src($placeholder_id, 'tu_profile_thumbnail');
    $profile_image  = !empty($img_atts['0']) ? $img_atts['0'] : '';
} else {
    $profile_image  = !empty($tuturn_settings['defaul_instructor_profile']['url']) ? $tuturn_settings['defaul_instructor_profile']['url'] : $user_dp;
}

$class              = '';
if(has_post_thumbnail($profile_id)) {
    $profile_imaged  = get_the_post_thumbnail_url( $profile_id, 'tu_profile_thumbnail' );
    $profile_image  = !empty($profile_imaged) ? $profile_imaged : $profile_image;
    $class          = 'class="tu-thumbnail-image"';
}
if(!empty($profile_image)) { ?>
    <figure>
        <img src="<?php echo esc_url($profile_image)?>" alt="<?php echo esc_attr($user_name) ?>" <?php echo esc_html($class);?>>
    </figure>
<?php }
