<?php
/**
 * Instructor home tab content
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $tuturn_settings;
?>
<div class="tu-tabswrapper">
    <div class="tu-tabstitle">
        <h4><?php esc_html_e('A brief introduction', 'tuturn'); ?></h4>
    </div>
    <div class="tu-description">
        <?php echo apply_filters('the_content', get_the_content()); ?>
    </div>
</div>
<?php
if (!empty($args['package_info']['education'])) {
    tuturn_get_template('single-tuturn-instructor/education.php', $args);
}

if (!empty($args['package_info']['teaching'])) {
    if( !empty($tuturn_settings['sub_categories_price']) ){
        tuturn_get_template('single-tuturn-instructor/subjects-with-prices.php', $args);
    }else{
        tuturn_get_template('single-tuturn-instructor/subjects.php', $args);
    }
}