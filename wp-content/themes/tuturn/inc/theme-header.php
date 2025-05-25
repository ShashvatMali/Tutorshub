<?php
/**
 * Tuturn header template
 *
 * @link https://themeforest.net/user/amentotech/portfolio
 *
 * @package Tuturn
 */
global $tuturn_settings,$wp_query;

$site_loader 		= !empty($tuturn_settings['site_loader']) ? $tuturn_settings['site_loader'] : '';
$loader_type 		= !empty($tuturn_settings['loader_type']) ? $tuturn_settings['loader_type'] : 'default';
$loader_image 		= !empty($tuturn_settings['loader_image']['url']) ? $tuturn_settings['loader_image']['url'] : '';
$logo_white 		= !empty($tuturn_settings['logo_white']) ? $tuturn_settings['logo_white'] : '';
$transparent_logo 	= !empty($tuturn_settings['transparent_logo']['url']) ? $tuturn_settings['transparent_logo']['url'] : '';
$preloader = '';

$logo = !empty($tuturn_settings['logo']['url']) ? $tuturn_settings['logo']['url'] : '';
$logo = !empty($tuturn_settings['logo']['url']) ? $tuturn_settings['logo']['url'] : get_template_directory_uri() . '/images/logo.svg';
$page_id = get_the_ID();
$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

if(!empty($logo_white) && !empty($transparent_logo)){
	if(!empty($wp_query->post->ID) && in_array($wp_query->post->ID,$logo_white)){
		$logo	= $transparent_logo;
	}
}

if (!empty($site_loader)) {
    if (!empty($loader_type) && $loader_type == 'default') {?>
		<div class="preloader-outer">
			<div class="tu-preloader-holder">
				<div class="tu-logo">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/images/page-loader.png'); ?>" alt="<?php esc_attr_e('Loading...', 'tuturn');?>">
				</div>
				<div class="tu-loader"></div>
			</div>
		</div>
		<?php
} elseif ($loader_type == 'custom' && !empty($loader_image)) {?>
		<div class="preloader-outer">
			<div class="tu-preloader-holder">
				<div class="tu-logo">
					<img src="<?php echo esc_url($loader_image); ?>" alt="<?php echo esc_attr_e('Loading...', 'tuturn'); ?>">
				</div>
				<div class="tu-loader"></div>
			</div>
		</div>
		<?php
}
}
?>
<header class="tu-header">
	<nav class="navbar navbar-expand-xl tu-navbar">
		<div class="container-fluid">
			<?php if (!empty($logo)) {?>
				<strong><a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>"><img class="amsvglogo" src="<?php echo esc_url($logo); ?>" alt="<?php esc_attr($blogname);?>"></a></strong>
			<?php }?>
			<button class="tu-menu"  aria-label="Main Menu" data-bs-target="#navbarSupportedContent" data-bs-toggle="collapse">
				<i class="icon icon-menu"></i>
			</button>
			<div class="collapse navbar-collapse tu-themenav" id="navbarSupportedContent">
				<?php
					if (has_nav_menu('primary-menu')) {
						wp_nav_menu(
							array(
								'theme_location' => 'primary-menu',
								'menu_id' => 'primary-menu',
								'menu_class' => 'navbar-nav tu-headernav',
								'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
							)
						);
					}
				?>
			</div>
		</div>
	</nav>
</header>