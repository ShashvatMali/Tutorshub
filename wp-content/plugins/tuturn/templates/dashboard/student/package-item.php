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
$package_type       = get_post_meta($package_id, 'package_type', true);
$type               = tuturn_price_plans_duration($package_type);
$package_duration   = get_post_meta($package_id, 'package_duration', true);

$package_field          = '';
?>
<div class="tu-planlist">
	<div class="tu-plandetail">
		<h4><?php echo esc_html($package->get_name()); ?></h4>
		<h3><?php echo do_shortcode($package->get_price_html()); ?> <span>/<?php echo wp_sprintf( _n( '%1$s %2$s', '%1$s %3$s', $package_duration, 'tuturn' ), $package_duration, $type, $type.'s' );?></span></h3>
		<p><?php echo apply_filters('the_content', $package->get_description()); ?></p>
	</div>
	<?php if( !empty($show_button) && $show_button === 'yes'){?>
		<div class="tu-btnarea">
			<a href="javascript:void(0);" data-pakcage_id="<?php echo intval($package_id);?>" data-user_id="<?php echo intval($user_id);?>" class="tu-primbtn tu-btnplain tu-buy-package"><?php esc_html_e('Buy now', 'tuturn'); ?></a>
		</div>
	<?php } ?>
</div>