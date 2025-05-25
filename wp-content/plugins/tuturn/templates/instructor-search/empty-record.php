<?php
/**
 * No record found
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructor-search
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
?>
<div class="tu-freelanceremptylist">
    <div class="tu-freelanemptytitle">
        <h4><?php esc_html_e('Oops! No data match with your keyword', 'tuturn');?></h4>
        <p><?php esc_html_e('We\'re sorry but there is no instructors found according to your search criteria', 'tuturn');?></p>
        <?php do_action('tuturn_categories_listing');?>       
        <a href="<?php the_permalink();?>" class="tu-primbtn"><?php esc_html_e('Reset search & start over', 'tuturn');?></a>
    </div>
</div>