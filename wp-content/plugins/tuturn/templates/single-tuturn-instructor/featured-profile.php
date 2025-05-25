<?php
/**
 * Instructor featured tag
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
if (!empty($args) && is_array($args)) {
    extract($args);
}
$featured_expriy_date   = get_post_meta( $profile_id,'featured_expriy_date',true );
$featured_profile       = get_post_meta( $profile_id,'featured_profile',true );
$current_date_time		= date("Y-m-d H:i:s");

 if($featured_profile == 'yes'){
    if(strtotime($featured_expriy_date) > strtotime($current_date_time)){?>
        <span class="tu-featuretag"><?php esc_html_e('FEATURED', 'tuturn'); ?></span>
    <?php }
}
