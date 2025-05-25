<?php
/**
 * Instructor singel package details
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Dashboard/Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $current_user;
if (!class_exists('WooCommerce')) {return;}
if (!empty($args) && is_array($args)) {
    extract($args);
}
$user_id            = $current_user->ID;
$package_id         = !empty($package_id) ? intval($package_id) : 0;
$package            = !empty($package_id) ? wc_get_product($package_id) : array();
$show_button        = !empty($buy_btn) ? 'yes' : '';
$tuturn_package     = get_post_meta($package_id, 'tuturn_package_detail', true);
$package_type       = get_post_meta($package_id, 'package_type', true);
$type               = tuturn_price_plans_duration($package_type);
$package_duration   = get_post_meta($package_id, 'package_duration', true);
$tuturn_package_field   = Tuturn_Packages_Feilds::package_fields();
$package_field          = '';
foreach ($tuturn_package_field as $field_id => $field) {

    if (!empty($field['type']) && !empty($field['front_display'])) {
        $label          = !empty($field['label']) ? $field['label'] : '';
        $description    = !empty($field['description']) ? $field['description'] : '';
        $caption_text   = !empty($field['caption_text']) ? $field['caption_text'] : '';
        $field_value    = !empty($tuturn_package[$field_id]) ? $tuturn_package[$field_id] : '0';
        switch ($field['type']) {
            case 'select':
                $options        = !empty($field['options']) ? $field['options'] : '';
                $package_field  .= '<li> <span>' . esc_html($label) . '<em>' . esc_html($field_value) . '</em></span></li>';
                break;
            case 'text':
                $package_field  .= '<li> <span>' . esc_html($label) . '<em>' . esc_html($field_value) .' '. $caption_text . '</em></span></li>';
                break;
            case 'checkbox':
                $allowed_class    = "fa fa-times tu-colorred";
                if (!empty($field_value) && $field_value == 'yes') {
                    $allowed_class    = "fa fa-check tu-colorgreen";
                }
                $package_field  .= '<li> <span>' . esc_html($label) . ' <i class="' . esc_attr($allowed_class) . '"></i></span></li>';
                break;
            default:
                $package_field  .= do_action('tuturn_package_field_render', $field, $field_id, $package_id);
        }
    }
} ?>
<div class="tu-planlist">
	<div class="tu-plandetail">
		<h4><?php echo esc_html($package->get_name()); ?></h4>
		<h3><?php echo do_shortcode($package->get_price_html()); ?> <span>/<?php echo wp_sprintf( _n( '%1$s %2$s', '%1$s %3$s', $package_duration, 'tuturn' ), $package_duration, $type, $type.'s' );?></span></h3>
		<p><?php echo apply_filters('the_content', $package->get_description()); ?></p>
	</div>
	<?php if ($package_field) { ?>
		<ul class="tu-planperks">
			<?php echo do_shortcode($package_field); ?>
		</ul>
	<?php } ?>
	<?php if( !empty($show_button) && $show_button === 'yes'){?>
		<div class="tu-btnarea">
			<a href="javascript:void(0);" data-pakcage_id="<?php echo intval($package_id);?>" data-user_id="<?php echo intval($user_id);?>" class="tu-primbtn tu-btnplain tu-buy-package"><?php esc_html_e('Buy now', 'tuturn'); ?></a>
		</div>
	<?php } ?>
</div>