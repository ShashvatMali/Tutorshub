<?php
$thegem_post_data = thegem_get_sanitize_page_title_data(get_the_ID());
$thegem_size = 'thegem-featured-post-slide';

if ($settings['fullwidth'] == 'yes') {
	$thegem_size = $thegem_size . '-fullwidth';
}

$featured_image_src = null;
$featured_image_width = null;
$featured_image_height = null;

if (has_post_thumbnail()) {
	list($featured_image_src, $featured_image_width, $featured_image_height) = thegem_generate_thumbnail_src(get_post_thumbnail_id(), $thegem_size);
}

$article_style = array();

if ($settings['fullheight'] == 'yes') {
	$article_style[] = 'height: 100vh';
}

$background = '';
if (!empty($featured_image_src) && $settings['slider_show_featured'] == 'yes' ) {
	$article_style[] = 'background-image: url(' . esc_url($featured_image_src) . ')';
	$background = 'url('.esc_url($featured_image_src).')';
	if ($slide_num == 0) {
		$article_style[] = 'background-image: url('.esc_url($featured_image_src).')';
	}
}
$slide_num++;

$article_style = implode(';', $article_style);
?>

<article id="post-<?php the_ID(); ?>" class="slide-item portfolio-item" <?php if (!empty($article_style)) echo 'style="' . $article_style . '"'; ?> data-background="<?php echo $background; ?>">

	<div class="gem-featured-posts-slide-overlay"></div>

	<div class="gem-featured-posts-slide-item">
		<?php if ($settings['thegem_elementor_preset'] == 'default' && $settings['slider_show_date'] == 'yes'): ?>
			<div class="gem-featured-post-date"><?php echo get_the_date('F d, Y'); ?></div>
		<?php endif; ?>
		<?php if ($settings['thegem_elementor_preset'] == 'new' && $settings['slider_show_categories'] == 'yes'): ?>
			<div>
				<?php thegem_get_additional_meta($settings); ?>
			</div>
		<?php endif; ?>

		<?php if (get_the_title() && $settings['slider_show_title'] == 'yes'):
			$title_tag = isset($settings['title_tag']) ? $settings['title_tag'] : 'div';
			$title_class = '';
			if (isset($settings['blog_title_preset']) && $settings['blog_title_preset'] != 'default') {
				$title_class = $settings['blog_title_preset'];
			}
			if (isset($settings['title_weight'])) {
				$title_class .= ' ' . $settings['title_weight'];
			} ?>
			<<?php echo $title_tag; ?> class="gem-featured-post-title <?php echo $title_class ?>"><div><?php the_title(); ?></div></<?php echo $title_tag; ?>>
		<?php endif; ?>

		<?php if ($settings['slider_show_excerpt'] == 'yes'):
			$description_preset = '';
			if (isset($settings['blog_description_preset']) && $settings['blog_description_preset'] != 'default') {
				$description_preset = $settings['blog_description_preset'];
			} ?>
			<div class="gem-featured-post-excerpt styled-subtitle <?php echo $description_preset; ?>">
				<div>
					<?php if ( !has_excerpt() && !empty( $thegem_post_data['title_excerpt'] ) ): ?>
						<?php echo $thegem_post_data['title_excerpt']; ?>
					<?php else: ?>
						<?php echo preg_replace('%&#x[a-fA-F0-9]+;%', '', apply_filters('the_excerpt', get_the_excerpt())); ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif;

		thegem_get_details_custom_fields($settings);

		if ($settings['thegem_elementor_preset'] == 'default' && $settings['slider_show_categories'] == 'yes'): ?>
			<div>
				<?php thegem_get_additional_meta($settings); ?>
			</div>
		<?php endif; ?>
		<?php if ($settings['thegem_elementor_preset'] == 'new' && $settings['slider_show_date'] == 'yes'): ?>
			<div class="gem-featured-post-date"><?php echo get_the_date('F d, Y'); ?></div>
		<?php endif; ?>
		<?php if ($settings['slider_show_author'] == 'yes'): ?>
			<div class="gem-featured-post-meta-author">
				<div class="author-wrap">
					<div class="author">
						<?php if ($settings['slider_show_author_avatar'] == 'yes'): ?>
							<span class="author-avatar"><?php echo get_avatar(get_the_author_meta('ID'), 50) ?></span>
						<?php endif; ?>
						<span class="author-name">
						<?php if ($settings['by_text']) {
							echo wp_kses($settings['by_text'], 'post').' ';
						};
						echo get_the_author_link() ?>
					</span>
					</div>
				</div>

			</div>
		<?php endif; ?>

		<?php if ($settings['slider_show_button'] == 'yes'):
			$link = !isset($settings['more_button_link']) || $settings['more_button_link'] == 'default' ? get_the_permalink() : $settings['more_button_custom_link']['url'];
			$id = isset($settings['more_button_id']) ? $settings['more_button_id'] : ''; ?>
			<div class="gem-featured-post-btn-box">
				<a id="<?php echo esc_attr($id); ?>" href="<?php echo $link; ?>" <?php echo $this->get_render_attribute_string('button-wrap'); ?>>
				<span class="gem-inner-wrapper-btn">
					<?php if (!empty($settings['more_button_icon']['value'])) : ?>
						<span class="gem-button-icon">
							<?php \Elementor\Icons_Manager::render_icon($settings['more_button_icon'], ['aria-hidden' => 'true']); ?>
						</span>
					<?php endif; ?>
                	<span class="gem-text-button">
						<?php echo '<span>' . wp_kses($settings['more_button_text'], 'post') . '</span>'; ?>
					</span>
				</span>
				</a>
			</div>
		<?php endif; ?>
	</div>
</article>


