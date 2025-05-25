<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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
			<?php if ( have_posts() ) {?>
				<header class="page-header">
					<h1 class="page-title">
						<?php printf( esc_html__( 'Search Results for: %s', 'tuturn' ), '<span>' . get_search_query() . '</span>' );?>
					</h1>
				</header>
				<?php
					while ( have_posts() ){
						the_post();
						get_template_part( 'template-parts/content', 'search' );

					}
									   
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
					<div class="tu-theme-asideholder">
						<div class="tu-asidewrapper">
							<a href="javascript:void(0)" class="tu-dbmenu"><i class="icon icon-chevron-left"></i></a>
							<div class="tu-aside-menu">
								<?php get_sidebar();?>
							</div>
						</div>
					</div>
				</div>
			<?php }?>
		</div>
	</div>
</div>
<?php
get_footer();
