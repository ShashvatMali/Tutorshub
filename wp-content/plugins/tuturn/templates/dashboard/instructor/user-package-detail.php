<?php
/**
 * Seller packages
 *
 * @package     Ttuturn
 * @subpackage  Tuturn/Templates/Dashboard/Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $current_user,$tuturn_settings;
if (!class_exists('WooCommerce')) {
    do_action('tuturn_woocommerce_install_notice');
    return;
}
$date_format	= !empty($tuturn_settings['dateformat']) ? $tuturn_settings['dateformat'] : 'Y-m-d';
$order_id 		= get_user_meta($current_user->ID, 'package_order_id', true);
$order_id		= !empty($order_id) ? intval($order_id) : 0;
if(!empty($order_id) && class_exists('WooCommerce')){ ?>
<div class="col-sm-12">
	<div class="tu-package-plan">
		<?php 
			$order 			= wc_get_order($order_id);
			if(empty($order )){
				return;
			}

			$order_status	= $order->get_status();
			
			if ( !empty($order_status) && $order_status === 'completed' ) {
				$remaing_days			= 0;
				$current_time			= time();
				$package_id				= get_post_meta($order_id, 'package_id', true);
				$package_details		= get_post_meta($order_id, 'package_details', true);
				$package_details		= !empty($package_details) ? $package_details : array();
				$package_create_date	= !empty($package_details['package_create_date']) ? $package_details['package_create_date'] : 0;
				$package_expriy_date	= !empty($package_details['package_expriy_date']) ? $package_details['package_expriy_date'] : 0;
				$package_create_date	= !empty($package_create_date) ? strtotime($package_create_date) : 0;
				$package_expriy_date	= !empty($package_expriy_date) ? strtotime($package_expriy_date) : 0;
				$remaining_featured		= empty($featured_allowed) || ( !empty($featured_allowed) && $featured_allowed < $featured_task)	? 0 : $featured_allowed - $featured_task;
				$package_id				= !empty($package_id) ? intval($package_id) : 0;
				$product_instant		= !empty($package_id)	? get_post( $package_id ) : '';
				$product_title			= !empty($product_instant) ? sanitize_text_field($product_instant->post_title) : '';
				$pkg_content			= !empty($product_instant) ? sanitize_text_field($product_instant->post_content) : '';
				   
				if($package_expriy_date >= $current_time ){
					$remaing_days	= $package_expriy_date-$current_time;
					$remaing_days	= round((($remaing_days/24)/60)/60); 
				}
 				?>
				<div class="tu-package-heading">
					
					<div class="tu-package-tags">
						<?php if( !empty($remaing_days)){?>
							<span class="tu-onging"><?php esc_html_e('Ongoing','tuturn');?></span>
						<?php } else { ?> 
							<span class="tu-onging tu-expire"><?php esc_html_e('Expired','tuturn');?></span>
						<?php } ?>
						<?php if( !empty($product_title) ){?>
							<h4><?php echo esc_html($product_title);?></h4>
						<?php } ?>
					</div>
				</div>
				<?php if( !empty($pkg_content) ){?>
					<div class="tu-description">
						<p><?php echo esc_html($pkg_content);?></p>
					</div>
				<?php } ?>
				<ul class="tu-package-list"> 
					<?php if( !empty($package_create_date) ){?>
						<li>
							<h6><?php esc_html_e('Purchased on','tuturn');?></h6>
							<span><?php echo date_i18n( get_option('date_format'), $package_create_date );?></span>
						</li>
					<?php } ?>
					<?php if( !empty($package_expriy_date) ){?>
						<li>
							<h6><?php esc_html_e('Expiry date','tuturn');?></h6>
							<span><?php echo date_i18n( get_option('date_format'), $package_expriy_date );?></span>
						</li>
					<?php } ?>
					<?php if( isset($remaing_days) ){?>
						<li>
							<h6><?php esc_html_e('Package duration','tuturn');?></h6>
							<span><?php echo wp_sprintf( esc_html__('%s days left', 'tuturn'), $remaing_days );?></span>
						</li>
					<?php } ?>
				</ul>
			<?php 
			}
		?>		
	</div>
</div>
<?php }
