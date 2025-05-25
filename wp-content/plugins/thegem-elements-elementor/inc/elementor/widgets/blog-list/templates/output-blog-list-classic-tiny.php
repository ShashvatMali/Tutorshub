<?php

$thegem_post_data = thegem_get_sanitize_page_title_data(get_the_ID());

$item_colors = isset($settings['item_colors']) ? $settings['item_colors'] : array();

$thegem_classes = array('post-item');

$is_sticky = is_sticky() && $settings['ignore_sticky_posts'] !== 'yes' && !is_paged();
if ($is_sticky) {
	$thegem_classes = array_merge($thegem_classes, array('sticky'));
}

$thegem_featured_content = thegem_get_post_featured_content(get_the_ID());
if (empty($thegem_featured_content) || $settings['show_featured_image'] !== 'yes') {
	$thegem_classes[] = 'no-image';
}

if ($settings['show_separator']) {
	$thegem_classes[] = 'with-separator';
}

if ($settings['icon_on_hover'] !== 'yes') {
	$thegem_classes[] = 'without-hover-icon';
}

$thegem_classes[] = 'clearfix';

if ($settings['title_preset'] == 'default') {
	$settings['title_preset'] = 'main-menu-item';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($thegem_classes); ?>>
	<?php if (get_post_format() == 'quote' && $thegem_featured_content) : ?>
		<?php echo $thegem_featured_content; ?>
	<?php else : ?>

		<?php if ($thegem_featured_content && $settings['show_featured_image'] == 'yes') : ?>
			<div class="post-image gem-compact-tiny-item-image">
				<?php echo $thegem_featured_content; ?>
				<?php if ($settings['show_categories'] == 'yes' && get_post_format() !== 'audio') {
					$categories = wp_get_object_terms(get_the_ID(), 'category');
					$separator = ', ';
					$output = '';
					if (!empty($categories)) {
						foreach ($categories as $category) {
							$output .= '<a href="' . esc_url(get_category_link($category->term_id)) . '" alt="' . esc_attr(sprintf(__('View all posts in %s', 'textdomain'), $category->name)) . '">' . esc_html($category->name) . '</a>' . $separator;
						}
						echo '<div class="categories tiny-post-categories">' . trim($output, $separator) . '</div>';
					}
				} ?>
			</div>
		<?php endif; ?>

		<div class="gem-compact-caption">
			<div class="gem-compact-item-content">
				<?php if ($settings['show_title'] == 'yes') {
					$color_class = '';
					if ($settings['title_preset'] !== 'main-menu-item') {
						$color_class = 'reverse-link-color ';
					}
					$title = get_the_title();
					if ($settings['truncate_title']) {
						$title = thegem_truncate_by_words($title, $settings['title_size']);
					}
					echo('<div class="tiny-post-title gem-news-item-title ' . esc_attr($settings['title_preset']) . '"><a class="' . $color_class . '" href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $title . '</a></div>');
				} ?>
				<?php if ($settings['show_description'] == 'yes') { ?>
					<div class="post-text tiny-post-description">
						<div class="summary <?php echo esc_attr($settings['post_excerpt_preset']); ?>">
							<?php if (!has_excerpt() && !empty($thegem_post_data['title_excerpt'])): ?>
								<?php $excerpt = $thegem_post_data['title_excerpt']; ?>
							<?php else: ?>
								<?php $excerpt = preg_replace('%&#x[a-fA-F0-9]+;%', '', apply_filters('the_excerpt', get_the_excerpt())); ?>
							<?php endif; ?>
							<?php if ($settings['truncate_description']) {
								echo thegem_truncate_by_words($excerpt, $settings['description_size']);
							} else {
								echo $excerpt;
							} ?>
						</div>
					</div>
				<?php } ?>
			</div>
			<div class="post-meta date-color">
				<div class="entry-meta clearfix text-body-tiny">
					<div class="post-meta-left gem-news-item-date">
						<?php if ($settings['show_date'] == 'yes') : ?><span
								class="post-meta-date tiny-post-date"><?php echo get_the_date() ?></span><?php endif; ?>
						<?php if ($settings['show_author'] == 'yes') : ?><span
								class="post-meta-author tiny-post-author"><?php printf(esc_html__("by %s", "thegem"), get_the_author_link()) ?></span><?php endif; ?>
					</div>
					<div class="post-meta-right">
						<?php if (comments_open() && $settings['show_comments'] == 'yes'): ?>
							<span class="comments-link tiny-post-comments"><?php comments_popup_link(0, 1, '%'); ?></span>
						<?php endif; ?>
					</div>
				</div><!-- .entry-meta -->
			</div>
		</div>
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
