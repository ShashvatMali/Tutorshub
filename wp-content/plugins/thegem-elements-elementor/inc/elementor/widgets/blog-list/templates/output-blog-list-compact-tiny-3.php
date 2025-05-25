<?php

$thegem_post_data = thegem_get_sanitize_page_title_data(get_the_ID());

$item_colors = isset($settings['item_colors']) ? $settings['item_colors'] : array();

$thegem_classes = array('post-item');

$is_sticky = is_sticky() && $settings['ignore_sticky_posts'] !== 'yes' && !is_paged();
if ($is_sticky) {
	$thegem_classes = array_merge($thegem_classes, array('sticky'));
}

if (!has_post_thumbnail()) {
	$thegem_classes[] = 'no-image';
}

if ($settings['show_separator']) {
	$thegem_classes[] = 'with-separator';
}

$thegem_classes[] = 'clearfix';

if ($settings['title_preset'] == 'default') {
	$settings['title_preset'] = 'main-menu-item';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($thegem_classes); ?>>
	<div class="wrap">

		<a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark" class="over-link"></a>

		<div class="post-image gem-compact-tiny-item-image">
			<?php thegem_post_thumbnail('thegem-blog-default-large', true, 'img-responsive'); ?>
		</div>

		<?php if ($settings['show_categories'] == 'yes') {
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

		<div class="gem-compact-caption">
			<?php if ($settings['show_author'] == 'yes') : ?>
				<div class="post-meta text-body-tiny">
					<span class="post-meta-author tiny-post-author"><?php printf(esc_html__("By %s", "thegem"), get_the_author_link()) ?></span>
				</div>
			<?php endif; ?>

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
			<?php if ($settings['show_date'] == 'yes') : ?>
				<div class="post-meta text-body-tiny">
					<span class="post-meta-date tiny-post-date"><?php echo get_the_date() ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
