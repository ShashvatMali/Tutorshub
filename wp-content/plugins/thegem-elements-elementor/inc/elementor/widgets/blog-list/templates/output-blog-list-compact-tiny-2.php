<?php

$thegem_post_data = thegem_get_sanitize_page_title_data(get_the_ID());

$item_colors = isset($settings['item_colors']) ? $settings['item_colors'] : array();

$thegem_classes = array('post-item');

$is_sticky = is_sticky() && $settings['ignore_sticky_posts'] !== 'yes' && !is_paged();
if ($is_sticky) {
	$thegem_classes = array_merge($thegem_classes, array('sticky'));
}

if (!has_post_thumbnail() || $settings['show_featured_image'] !== 'yes') {
	$thegem_classes[] = 'no-image';
}

if ($settings['show_separator']) {
	$thegem_classes[] = 'with-separator';
}

$thegem_classes[] = 'clearfix';

if ($settings['title_preset'] == 'default') {
	$settings['title_preset'] = 'text-body-tiny';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($thegem_classes); ?>>
	<?php if ($settings['show_featured_image'] == 'yes') { ?>
		<div class="gem-compact-tiny-left">
			<div class="gem-news-item-image">
				<a href="<?php echo esc_url(get_permalink()); ?>"><?php thegem_post_thumbnail('thegem-news-carousel', true, 'img-responsive'); ?></a>
			</div>
		</div>
	<?php } ?>

	<div class="gem-compact-tiny-right">
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
		</div>
		<div class="post-meta">
			<div class="entry-meta clearfix text-body-tiny">
				<div class="post-meta-left gem-news-item-date">
					<?php if ($settings['show_author'] == 'yes') : ?><span
							class="post-meta-author tiny-post-author"><?php printf(esc_html__("By %s", "thegem"), get_the_author_link()) ?></span><br><?php endif; ?>
					<?php if ($settings['show_date'] == 'yes') : ?><span
							class="post-meta-date tiny-post-date"><?php echo get_the_date() ?></span><?php endif; ?>
				</div>
				<div class="post-meta-right">
					<?php if (comments_open() && $settings['show_comments'] == 'yes'): ?>
						<span class="comments-link tiny-post-comments"><?php comments_popup_link(0, 1, '%'); ?></span>
					<?php endif; ?>
				</div>
			</div><!-- .entry-meta -->
		</div>

	</div>
</article><!-- #post-<?php the_ID(); ?> -->
