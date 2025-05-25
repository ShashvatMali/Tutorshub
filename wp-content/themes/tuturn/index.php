<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tuturn
 */
global $wp_query;
$show_posts    	= get_option('posts_per_page');
get_header();
$section_col = 'col-xl-8 col-xxl-9';
if ( !is_active_sidebar( 'tuturn-sidebar' ) ) {
	$section_col = 'col-xl-12';
}
?>
<div class="tu-main-section">
	<div class="container">
		<div class="row tu-blogs-bottom">
			<div class="<?php echo esc_attr($section_col);?>">
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'archive' );
					endwhile;
					if ($wp_query->found_posts > $show_posts) {
						if (function_exists('tuturn_pagination')) {
							echo tuturn_pagination('' , $show_posts);
						}
					}
				}else{
					get_template_part( 'template-parts/content', 'none' );
				}
				?>
			</div>
			<?php if ( is_active_sidebar( 'tuturn-sidebar' ) ) {?>
				<div class="col-xl-4 col-xxl-3">
					<aside>
						<div class="tu-theme-asideholder">
							<div class="tu-asidewrapper">
								<a href="javascript:void(0)" class="tu-dbmenu"><i class="icon icon-chevron-left"></i></a>
								<div class="tu-aside-menu">
									<?php get_sidebar();?>
								</div>
							</div>
						</div>
					</aside>
				</div>
			<?php }?>
		</div>
	</div>
</div>
<?php
get_footer();
