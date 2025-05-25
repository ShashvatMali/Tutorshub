<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Tuturn
 */
global $post, $tuturn_settings;

$title_404			= !empty($tuturn_settings['title_404']) ? $tuturn_settings['title_404'] : esc_html__('You running into nowhere','tuturn');
$subtitle_404		= !empty($tuturn_settings['subtitle_404']) ? $tuturn_settings['subtitle_404'] : esc_html__('Uhoo! Page not found','tuturn');
$description_404	= !empty($tuturn_settings['description_404']) ? $tuturn_settings['description_404'] : esc_html__( 'It looks like nothing was found on this path. Maybe you should try with another link or make a quick new keyword search below?', 'tuturn' );;
$image_404			= !empty($tuturn_settings['image_404']['url']) ? $tuturn_settings['image_404']['url'] : '';
get_header();
?>
<div class="tu-main-section">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-6">
				<div class="tu-notfound">
					<div class="tu-notfound-title">
						<h4><?php echo esc_html($title_404);?></h4>
						<h2><?php echo esc_html($subtitle_404);?></h2>
						<p><?php echo esc_html($description_404);?></p>	
					</div>
					<?php get_search_form();?>
					<div class="tu-description">
						<p><?php echo sprintf( __( "You can also start from scratch. Goto %sHomepage%s instead", 'tuturn' ), '<a href="'.esc_url(home_url('/')).'">', '</a>' );?></p>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<figure class="tu-notfound-img"><img src="<?php echo esc_url($image_404);?>"></figure>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
