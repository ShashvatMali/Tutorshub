<?php

/**
 * Instructor hoursly rate
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
$hourly_rate   = get_post_meta($profile_id,'hourly_rate',true );
?>
<?php if(!empty($hourly_rate)){?>
    <div class="tu-listinginfo_price">
        <span><?php esc_html_e('Starting from:','tuturn')?> </span>
        <?php if(!empty($hourly_rate)){?>
            <h4><?php tuturn_price_format($hourly_rate). esc_html_e('/hr','tuturn')?></h4>
        <?php } ?>
    </div>
<?php } ?>
