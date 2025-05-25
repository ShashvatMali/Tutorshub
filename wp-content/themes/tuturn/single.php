<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Tuturn
 */
global $post, $tuturn_settings;
$author_details		= !empty($tuturn_settings['author_details']) ? $tuturn_settings['author_details'] : '';
$related_article	= !empty($tuturn_settings['related_article']) ? $tuturn_settings['related_article'] : '';
$side_layout		= !empty($tuturn_settings['side-layout']) ? $tuturn_settings['side-layout'] : 'right';
$args				= array(
	'post_id'	=> $post->ID,
	'key'		=> 'tuturn_post_views'
);

do_action('tuturn_post_views',$args);
get_header();
$section_col = 'col-xl-12';

if(!empty($side_layout) && ($side_layout == 'left' || $side_layout == 'right') && is_active_sidebar( 'tuturn-single-sidebar' )){
	$section_col = 'col-xl-8 col-xxl-9';
}

?>
<div class="tu-main-section">
	<div class="container">
		<?php while ( have_posts() ) :
			global $post;
			the_post();
			$author_id 			= get_the_author_meta( 'ID' );?>
			<div class="row">
				<?php if(!empty($side_layout) && $side_layout == 'left' && is_active_sidebar( 'tuturn-single-sidebar' ) ){?>
					<div class="col-xl-4 col-xxl-3">
						<?php if ( is_active_sidebar( 'tuturn-single-sidebar' ) ) {?>
							<div class="tu-asidewrapper">
								<a href="javascript:void(0)" class="tu-dbmenu"><i class="icon icon-chevron-left"></i></a>
                        		<div class="tu-aside-menu">
									<?php dynamic_sidebar( 'tuturn-single-sidebar' ); ?>
								</div>
							</div>
						<?php }?>
					</div>
				<?php }?>
				<div class="<?php echo esc_attr($section_col);?>">
					<div class="tu-blogwrapper">
						<?php tuturn_post_thumbnail(); ?>
						<div class="tu-bloginfo">
							<?php tuturn_entry_footer(); ?>
						</div>
						<div class="tu-blogtitle">
							<h3><?php the_title()?></h3>
						</div>
						<ul class="tu-blogiteminfo">
							<li><?php tuturn_posted_on();?></li>
							<li>
								<i class="icon icon-message-square">
									<span><?php comments_number(esc_html__('0 Comments' , 'tuturn') , esc_html__('1 Comment' , 'tuturn') , esc_html__('% Comments' , 'tuturn')); ?></span>
								</i>
							</li>
							<?php do_action('tutrn_post_views',$post->ID);?>
						</ul>
						<div class="tu-description">
							<?php	get_template_part( 'template-parts/content', get_post_type() );?>
						</div>
						<?php	
						// Blog post tags
						tuturn_post_tags();

						// Related posts
						if(!empty($related_article)){
							get_template_part( 'template-parts/content','related-articles' );
						}
						
						// Author details
						if(!empty($author_details)){
							$avatar_url = get_avatar_url($author_id, 100);
							$avatar = '<img src="'.esc_url($avatar_url).'" alt="'.esc_attr(get_the_author()).'">';
							?>
							<div class="tu-blogprofileuser tu-single-author-box">
								<div class="tu-authorhead">
									<?php echo do_shortcode($avatar); ?>
									<div class="tu-profilrtitle">
										<h6><?php esc_html_e('Author','tuturn');?></h6>
										<h5><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author(); ?></a></h5>
									</div>								
								</div>
								<?php if ( get_the_author_meta( 'description',$author_id ) ) : ?>
									<div class="tu-blogprofileuser__description"><p><?php the_author_meta( 'description',$author_id ); ?></p></div>
								<?php endif; ?>
							</div>
						<?php }
						// If comments are open or we have at least one comment, load up the comment template.
						if ( ( comments_open() || get_comments_number() ) && get_post_type() === 'post' ) :
							comments_template();
						endif;
						?>						
					</div>
				</div>
				<?php if(!empty($side_layout) && $side_layout == 'right' && is_active_sidebar( 'tuturn-single-sidebar' ) ){?>
					<div class="col-xl-4 col-xxl-3">						
						<?php if ( is_active_sidebar( 'tuturn-single-sidebar' ) ) {?>
							<div class="tu-asidewrapper">
								<a href="javascript:void(0)" class="tu-dbmenu"><i class="icon icon-chevron-left"></i></a>
                        		<div class="tu-aside-menu">
									<?php dynamic_sidebar( 'tuturn-single-sidebar' ); ?>
								</div>
							</div>
						<?php }?>
					</div>
				<?php }?>
			</div>
		<?php endwhile; // End of the loop.?>
	</div>
</div>
<?php
get_footer();
