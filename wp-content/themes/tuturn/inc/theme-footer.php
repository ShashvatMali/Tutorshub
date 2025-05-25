<?php
/**
 * Tuturn footer template
 *
 * @link https://themeforest.net/user/amentotech/portfolio
 *
 * @package Tuturn
 */
global $tuturn_settings;

$footer_copyright 	= !empty($tuturn_settings['copyright']) ? $tuturn_settings['copyright'] : esc_html__('Copyright &copy;', 'tuturn') . date('Y') . '&nbsp;' . get_bloginfo();
$topbar	= 'tu-empty-sidebar';

if ( is_active_sidebar('tuturn-sidebar-f1')
	|| is_active_sidebar('tuturn-sidebar-f2')
	|| is_active_sidebar('tuturn-sidebar-f3')
	|| is_active_sidebar('tuturn-sidebar-f4')
	|| is_active_sidebar('tuturn-sidebar-f5')
) {
	$topbar	= '';
}
?>
<footer>
	<div class="tu-footerdark <?php echo esc_attr($topbar);?>">
		<?php if ( empty($topbar) ) {?>
			<div class="tu-footerwrap">
				<div class="container">
					<div class="row">
						<?php if ( is_active_sidebar('tuturn-sidebar-f1')) {?>
							<div class="col-12">
								<?php dynamic_sidebar('tuturn-sidebar-f1'); ?>
							</div>
						<?php }?>
						<?php if ( is_active_sidebar('tuturn-sidebar-f2')  || is_active_sidebar('tuturn-sidebar-fonlineclasses')) {?>
						<div class="col-12 tu-seperator">
							<div class="row gy-4">
								<?php if ( is_active_sidebar('tuturn-sidebar-f2') ){?> <div class="col-12 col-xl-7 tuturn-sidebar-f2"><?php dynamic_sidebar('tuturn-sidebar-f2'); ?></div><?php }?>
								<?php if ( is_active_sidebar('tuturn-sidebar-fonlineclasses')){?><div class="col-12 col-xl-5 tuturn-sidebar-fonlineclasses"><?php dynamic_sidebar('tuturn-sidebar-fonlineclasses'); ?></div><?php }?>
							</div>
						</div>
						<?php }?>
						<?php if ( is_active_sidebar('tuturn-sidebar-f3')  
						|| is_active_sidebar('tuturn-sidebar-f4') 
						|| is_active_sidebar('tuturn-sidebar-f5')) {?>
							<div class="col-12 tu-seperator">
								<div class="row gy-4">
									<?php if ( is_active_sidebar('tuturn-sidebar-f3') ){?>
										<div class="col-md-6 col-lg-4 tuturn-sidebar-f3"><?php dynamic_sidebar('tuturn-sidebar-f3'); ?></div>
									<?php }?>
									<?php if ( is_active_sidebar('tuturn-sidebar-f4') ){?>
										<div class="col-md-6 col-lg-4 tuturn-sidebar-f4"><?php dynamic_sidebar('tuturn-sidebar-f4'); ?></div>
									<?php }?>
									<?php if ( is_active_sidebar('tuturn-sidebar-f5') ){?>
										<div class="col-md-12 col-lg-4 tuturn-sidebar-f5"><?php dynamic_sidebar('tuturn-sidebar-f5'); ?></div>
									<?php }?>
								</div>
							</div>
						<?php }?>
					</div>
				</div>
			</div>
		<?php }?>
		<div class="tu-footercopyright">
			<div class="container">
				<div class="tu-footercopyright_content">
					<?php if(!empty($footer_copyright)){?>
						<p><?php echo esc_html($footer_copyright);?></p>
					<?php }?>
					<?php 
						if ( has_nav_menu( 'footer-menu' ) ) {
							wp_nav_menu(
								array(
									'menu_class'		=> 'tu-footercopyright_list',
									'theme_location' 	=> 'footer-menu',
									'menu_id'        	=> 'footer-menu',
								)
							);
						}
					?>
				</div>
			</div>
		</div>
	</div>
</footer>
