<?php

/**
 * Instructor title
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructor-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post;
$profile_id    = !empty($post->ID) ? intval($post->ID) : 0 ;
$username      = tuturn_get_username($profile_id); 
$is_verified   = get_post_meta( $profile_id,'_is_verified',true );
?>
<?php if(!empty($username)){?>
    <h5>
        <a href="<?php echo get_permalink(); ?>"> 
            <?php echo esc_html($username); ?>
        </a>    
        <?php if(!empty($is_verified)) { ?>
            <i class="icon icon-check-circle tu-greenclr"></i>
        <?php } ?>
    </h5>
<?php }
