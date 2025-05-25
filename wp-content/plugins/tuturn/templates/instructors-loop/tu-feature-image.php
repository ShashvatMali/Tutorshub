<?php
/**
 * Instructor image gallery
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructors-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post,$tuturn_settings;
$instructor_id      = !empty($post->ID) ? intval($post->ID) : '';
$tu_post_meta       = get_post_meta($instructor_id, 'profile_details', true);
$tu_post_meta       = !empty($tu_post_meta) ? $tu_post_meta : array();
$instructor_name    = tuturn_get_username($instructor_id);
$user_dp            = TUTURN_DIRECTORY_URI . 'public/images/default-avatar_416x281.jpg';
$defaul_profile	    = !empty($tuturn_settings['defaul_instructor_profile']['url']) ? $tuturn_settings['defaul_instructor_profile']['url'] : $user_dp;
$profile_image      = !empty($tu_post_meta['profile_image']['featureImage']) ? esc_url_raw($tu_post_meta['profile_image']['featureImage']) : $defaul_profile;
$class              =   empty($tu_post_meta['profile_image']['featureImage']) ? 'class="tu-thumbnail-image"' : '';
$media_images       = !empty($tu_post_meta['media_gallery']) ? $tu_post_meta['media_gallery'] : array(); 

if(!empty($profile_image) || !empty($media_images) ) {?>
    <figure <?php echo do_shortcode($class);?>>
         <?php if(!empty($profile_image)) {?>
            <img src="<?php echo esc_url($profile_image);?>" alt="<?php echo esc_attr($instructor_name);?>">
        <?php } ?>
        <?php if(!empty($media_images)){?>
            <figcaption>
                <?php foreach( $media_images as $media_image) {
                    if(!empty($media_image['thumbnail'])){ ?>
                        <span><img src="<?php echo esc_url($media_image['thumbnail'])?>" alt="<?php esc_attr_e('User gallery','tuturn')?>"></span>
                    <?php }
                }?>
            </figcaption>
        <?php }?>
    </figure>
    <?php 
}
