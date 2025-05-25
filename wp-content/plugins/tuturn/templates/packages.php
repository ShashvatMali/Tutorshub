<?php
/**
 * Template Name: Packages
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $tuturn_settings,$current_user;
$user_id 			= $current_user->ID;
$userType 	        = apply_filters('tuturnGetUserType', $user_id );

if(!empty($userType) && $userType === 'student'){
	$title		= !empty($tuturn_settings['student_pkg_page_title']) ? $tuturn_settings['student_pkg_page_title'] : '';
	$sub_title	= !empty($tuturn_settings['student_pkg_page_sub_title']) ? $tuturn_settings['student_pkg_page_sub_title'] : '';
	$details	= !empty($tuturn_settings['student_pkg_page_details']) ? $tuturn_settings['student_pkg_page_details'] : '';
}else{
	$title		= !empty($tuturn_settings['pkg_page_title']) ? $tuturn_settings['pkg_page_title'] : '';
	$sub_title	= !empty($tuturn_settings['pkg_page_sub_title']) ? $tuturn_settings['pkg_page_sub_title'] : '';
	$details	= !empty($tuturn_settings['pkg_page_details']) ? $tuturn_settings['pkg_page_details'] : '';
}

if( !is_user_logged_in() ){
	$current_page_link  = get_permalink();
	set_transient( 'tu_redirect_page_url', esc_url_raw($current_page_link), 200 );

    $redirect_url = tuturn_get_page_uri('login');
    if(empty($redirect_url)){
        $redirect_url   = get_home_url();        
    }
    if( !empty($redirect_url) ){
    	wp_redirect( $redirect_url );
        exit;
    }
}
get_header();
?>
<section class="tu-main-section">
	<div class="container">
		<div class="row">
			<?php do_action('tuturn_notification_content');?>
			<?php if( !empty($title) || !empty($sub_title) || !empty($details) ){?>
				<div class="col-sm-12">
					<div class="tu-pricingtop">
						<?php if( !empty($title) ){?>
							<h4><?php echo esc_html($title); ?></h4>
						<?php } ?>
						<?php if( !empty($sub_title) ){?>
							<h2><?php echo esc_html($sub_title) ?></h2>
						<?php } ?>
						<?php if( !empty($details) ){?>
							<p> <?php echo do_shortcode($details); ?></p>
						<?php } ?>
					</div>
				</div>
			<?php }
			
			if(class_exists('WooCommerce')){
				if(!empty($userType) && $userType === 'student'){		
					tuturn_get_template_part('dashboard/instructor/user-package-detail');

					$args = array(
						'limit'     => -1,
						'status'    => 'publish',
						'type'      => array('packages'),
						'orderby'   => 'date',
						'order'     => 'ASC',
						'user_type'	=> 'student'
					);
					$tuturn_packages = wc_get_products( $args );

					if(isset($tuturn_packages) && is_array($tuturn_packages) && count($tuturn_packages)>0){?>
						<div class="col-lg-12">
							<div class="row">
								<div class="col-sm-12">
									<ul class="tu-pricinglist">
										<?php foreach($tuturn_packages as $package){
											$package_id	= $package->get_id();?>
											<li><?php tuturn_get_template_part('dashboard/student/package', 'item',array('package_id'=>$package_id,'buy_btn' => 'yes'));?></li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</div>
						<?php
					}
				}else{
					tuturn_get_template_part('dashboard/instructor/user-package-detail');
					$args = array(
						'limit'     => -1,
						'status'    => 'publish',
						'type'      => array('packages'),
						'orderby'   => 'date',
						'order'     => 'ASC',
						'user_type'	=> 'tutor'
					);
					$tuturn_packages = wc_get_products( $args );

					if(isset($tuturn_packages) && is_array($tuturn_packages) && count($tuturn_packages)>0){?>
						<div class="col-lg-12">
							<div class="row">
								<div class="col-sm-12">
									<ul class="tu-pricinglist">
										<?php foreach($tuturn_packages as $package){
											$package_id	= $package->get_id();?>
											<li><?php tuturn_get_template_part('dashboard/instructor/package', 'item',array('package_id'=>$package_id,'buy_btn' => 'yes'));?></li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</div>
						<?php
					}
				}
			} else {
				do_action('tuturn_woocommerce_install_notice');
			}
			?>
		</div>
	</div>
</section>
<?php
get_footer();
