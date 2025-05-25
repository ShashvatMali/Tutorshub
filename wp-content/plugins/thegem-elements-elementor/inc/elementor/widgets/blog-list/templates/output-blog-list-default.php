<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Icons_Manager;

$thegem_post_data = thegem_get_sanitize_page_title_data(get_the_ID());

$thegem_categories = wp_get_object_terms(get_the_ID(), 'category');
$thegem_categories_list = array();
foreach($thegem_categories as $thegem_category) {
	$thegem_categories_list[] = '<a href="'.esc_url(get_category_link( $thegem_category->term_id )).'" title="'.esc_attr( sprintf( __( "View all posts in %s", "thegem" ), $thegem_category->name ) ).'">'.$thegem_category->name.'</a>';
}

$thegem_featured_content = thegem_get_post_featured_content(get_the_ID());

$thegem_classes = array('post-item');

$is_sticky = is_sticky() && $settings['ignore_sticky_posts'] !== 'yes' && !is_paged();
if($is_sticky) {
	$thegem_classes = array_merge($thegem_classes, array('sticky', 'default-background'));
}

if(empty($thegem_featured_content)) {
	$thegem_classes[] = 'no-image';
}
$thegem_classes[] = 'item-animations-not-inited';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($thegem_classes); ?> >
	<?php if( get_post_format() == 'quote' && $thegem_featured_content ) : ?>
		<?php echo $thegem_featured_content; ?>
	<?php else : ?>

		<?php if( ! is_single() && $is_sticky ) {?>
			<div class="sticky-label">
					<div class="elementor-icon">
						<?php if ($settings['sticky_label_icon']['value']) {
							Icons_Manager::render_icon( $settings['sticky_label_icon'], [ 'aria-hidden' => 'true' ] );
						} else { ?>
							<i class="default"></i>
						<?php } ?>
					</div>
				</div>
			<?php } elseif ($is_sticky && is_admin()) {	?>
				<div class="sticky-label">
					<div class="elementor-icon">
						<?php if ($settings['sticky_label_icon']['value']) {
							Icons_Manager::render_icon( $settings['sticky_label_icon'], [ 'aria-hidden' => 'true' ] );
						} else { ?>
							<i class="default"></i>
						<?php } ?>
					</div>
				</div>
			<?php } ?>

		<div class="item-post-container">
			<div class="item-post clearfix">

				<?php if ( $thegem_featured_content && $settings['show_featured_image'] ) : ?>
					<div class="post-image post-img"><?php echo $thegem_featured_content; ?></div>
				<?php endif; ?>

				<div class="caption-container">

					<div class="post-meta">
						<div class="entry-meta clearfix gem-post-date">
							<div class="post-meta-right">
								<?php if ( comments_open() && $settings['show_comments'] ): ?>
									<span class="comments-link"><div class="elementor-icon"><?php if ($settings['comments_icon']['value']) : Icons_Manager::render_icon( $settings['comments_icon'], [ 'aria-hidden' => 'true' ] ); else : ?><i class="default"></i><?php endif; ?></div><?php comments_popup_link(0, 1, '%'); ?></span>
								<?php endif; ?>
								<?php if ( comments_open() && $settings['show_comments'] && function_exists('zilla_likes') && $settings['show_likes'] ): ?>
									<span class="sep"></span>
								<?php endif; ?>
								<?php if ( function_exists( 'zilla_likes' ) && $settings['show_likes'] ) { echo '<span class="post-meta-likes">';if ($settings['likes_icon']['value']) :	Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);	else: ?><i class="default"></i><?php endif;zilla_likes(); echo '</span>'; } ?>
							</div>
							<div class="post-meta-left">
								<?php if ( $settings['show_author'] ) : ?>
									<span class="post-meta-author">
										<?php printf( esc_attr( $settings['caption_author_by_text'] ) . esc_html__( " %s", "thegem" ), get_the_author_link() ); ?>
									</span>
								<?php endif; ?>
								<?php if ( $thegem_categories && $settings['show_categories'] ): ?>
									<?php if ( $settings['show_author'] ) : ?>
										<span class="sep"></span> 
									<?php endif; ?>
									<span class="post-meta-categories">
										<?php echo implode(' <span class="sep"></span> ', $thegem_categories_list); ?>
									</span>
								<?php endif ?>
							</div>
						</div><!-- .entry-meta -->
					</div>

					<?php if ( 'yes' === ( $settings['show_title'] ) ) : ?>
						<div class="post-title">
							<?php the_title('<'.($is_sticky ? 'h2' : 'h3').' class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark"><span class="entry-title-date">'.( $settings['show_date'] ? get_the_date('d M').': ' : '').'</span><span class="light">', '</span></a></'.($is_sticky ? 'h2' : 'h3').'>' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( 'yes' === ( $settings['show_description'] ) ) : ?>
						<div class="post-text">
							<div class="summary">
								<?php
									$excerpt = '';
									if ( !has_excerpt() && !empty( $thegem_post_data['title_excerpt'] ) ) {
										$excerpt = $thegem_post_data['title_excerpt'];
									} else {
										$excerpt = preg_replace('%&#x[a-fA-F0-9]+;%', '', apply_filters('the_excerpt', get_the_excerpt()));
									}
									if ($settings['truncate_description'] && $settings['description_size']) {
										echo thegem_truncate_by_words($excerpt, $settings['description_size']);
									} else {
										echo $excerpt;
									}
								?>
							</div>
						</div>
					<?php endif; ?>

					<div class="post-footer">
						<div class="post-footer-sharing">
							<?php if ( $settings['show_social_sharing'] && ! empty( $bl_social_sharing ) && file_exists( $bl_social_sharing ) ) : include $bl_social_sharing; endif; ?>
						</div>
						<div class="post-read-more">
							<?php if ( $settings['show_readmore_button'] && ! empty( $bl_readmore_button ) && file_exists( $bl_readmore_button ) ) : include $bl_readmore_button; endif; ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
