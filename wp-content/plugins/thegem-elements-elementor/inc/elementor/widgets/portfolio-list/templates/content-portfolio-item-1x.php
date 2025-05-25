<?php use Elementor\Icons_Manager;
$thegem_large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
$thegem_has_post_thumbnail = has_post_thumbnail(get_the_ID());
$grid_appearance_type = isset($thegem_portfolio_item_data['grid_appearance_type']) ? $thegem_portfolio_item_data['grid_appearance_type'] : 'featured_image';
$is_image_type = $grid_appearance_type == 'featured_image';
$is_gif_type = $grid_appearance_type == 'animated_gif';
$is_video_type = $grid_appearance_type == 'video';
$is_gallery_type = $grid_appearance_type == 'gallery';

if ($settings['portfolio_show_title'] != 'yes') {
	$thegem_classes[] = 'without-title';
}
if ($is_gif_type) {
	$gif_preload = true;
	if ($thegem_portfolio_item_data['grid_appearance_gif_start'] == 'play_on_hover' && empty($thegem_portfolio_item_data['grid_appearance_gif_preload'])) {
		$gif_preload = false;
		$thegem_image_classes[] = 'gif-load-on-hover';
	}
}
$thegem_classes[] = 'appearance-type-' . $grid_appearance_type;
if ($is_video_type) {
	$ratio = !empty($thegem_portfolio_item_data['grid_appearance_video_aspect_ratio']) ? $thegem_portfolio_item_data['grid_appearance_video_aspect_ratio'] : '16:9';
	$ratio_arr = explode(":", $ratio);
	if (!empty($ratio_arr[0]) && !empty($ratio_arr[1])) {
		$aspect_ratio = $ratio_arr[0] / $ratio_arr[1];
		$thegem_classes[] = 'custom-ratio';
	}
} ?>
<div <?php post_class($thegem_classes); ?> data-sort-date="<?php echo get_the_date('U'); ?>">
	<div class="wrap clearfix">
		<div <?php post_class($thegem_image_classes); ?>>
			<div class="image-inner <?php echo $is_image_type && !$thegem_has_post_thumbnail ? 'without-image' : ''; ?>">
				<?php if ($is_video_type) {
					switch ($thegem_portfolio_item_data['grid_appearance_video_type']) {
						case 'youtube':
							$video_id = thegem_parcing_youtube_url($thegem_portfolio_item_data['grid_appearance_video']);
							$thegem_video_link = '//www.youtube.com/embed/' . $video_id . '?autoplay=1';
							$thegem_video_class = 'youtube';
							break;
						case 'vimeo':
							$video_id = thegem_parcing_vimeo_url($thegem_portfolio_item_data['grid_appearance_video']);
							$thegem_video_link = '//player.vimeo.com/video/' . $video_id . '?autoplay=1';
							$thegem_video_class = 'vimeo';
							break;
						default:
							$video_id = $thegem_video_link = $thegem_portfolio_item_data['grid_appearance_video'];
							$thegem_video_class = 'self_video';
					}
					if ($thegem_portfolio_item_data['grid_appearance_video_start'] == 'open_in_lightbox') {
						if ($thegem_portfolio_item_data['grid_appearance_video_type'] == 'self' && !empty($thegem_portfolio_item_data['grid_appearance_video_poster'])) { ?>
							<img src="<?php echo $thegem_portfolio_item_data['grid_appearance_video_poster']; ?>" alt="Video Poster">
						<?php } else if ($thegem_has_post_thumbnail) {
							thegem_post_picture($thegem_size, [], array('alt' => get_the_title()));
						} ?>
						<a href="<?php echo esc_url($thegem_video_link); ?>"
						   class="portfolio-video-icon <?php echo $thegem_video_class; ?>"
						   <?php if (isset($aspect_ratio)) { ?>data-ratio="<?php echo $aspect_ratio; ?>"<?php } ?>
						   data-fancybox="thegem-portfolio"
						   data-elementor-open-lightbox="no">
						</a>
					<?php } else {
						$play_on_mobile = true;
						if (!isset($aspect_ratio) || !$thegem_portfolio_item_data['grid_appearance_video_play_on_mobile']) {
							if ($thegem_has_post_thumbnail) {
								thegem_post_picture($thegem_size, [], array('alt' => get_the_title(), "class" => "video-image-mobile"));
							} else {
								thegem_generate_picture('THEGEM_TRANSPARENT_IMAGE', $thegem_size, array(), array('alt' => get_the_title(), "class" => "video-image-mobile"));
							}
							$play_on_mobile = false;
						}
						$autoplay = $thegem_portfolio_item_data['grid_appearance_video_start'] == 'autoplay'; ?>

						<div class="gem-video-portfolio type-<?php echo $thegem_portfolio_item_data['grid_appearance_video_type']; ?> <?php echo $autoplay ? 'autoplay' : 'play-on-hover'; ?> <?php echo $play_on_mobile ? '' : 'hide-on-mobile'; ?> <?php echo !$autoplay || $play_on_mobile ? 'run-embed-video' : ''; ?>"
							data-video-type="<?php echo $thegem_portfolio_item_data['grid_appearance_video_type']; ?>"
							data-video-id="<?php echo $video_id; ?>"
							<?php if (isset($aspect_ratio)) { ?>style="aspect-ratio: <?php echo $aspect_ratio; ?>"<?php } ?>>
							<?php if (function_exists('thegem_portfolio_video_background')) {
								echo thegem_portfolio_video_background(
									$thegem_portfolio_item_data['grid_appearance_video_type'],
									$video_id,
									$thegem_portfolio_item_data['grid_appearance_video_overlay'],
									$thegem_portfolio_item_data['grid_appearance_video_poster'],
									$autoplay,
									$play_on_mobile);
							} ?>
						</div>
					<?php } ?>
					<svg class="video-type-icon" enable-background="new 0 0 9 5" viewBox="0 0 9 5" xmlns="http://www.w3.org/2000/svg"><path d="m5.3.2h-4.2c-.4 0-.6.3-.6.6v3.2c0 .4.3.6.6.6h4.2c.4.2.7-.1.7-.5v-3.2c0-.4-.3-.7-.7-.7zm3.1.4c-.1-.1-.3-.1-.4 0l-1.4 1v1.9l1.4 1c.1.1.2.1.3 0s.2-.2.2-.3v-3.4c0-.1 0-.2-.1-.2z"/></svg>
				<?php } else if ($is_gif_type) {
					if (!empty($thegem_portfolio_item_data['grid_appearance_gif'])) {
						$gif_src = wp_get_attachment_image_src($thegem_portfolio_item_data['grid_appearance_gif'], 'full');
						if ($gif_src) { ?>
							<img width="<?php echo $gif_src[1]; ?>" height="<?php echo $gif_src[2]; ?>" class="gem-gif-portfolio" src="<?php if ($gif_preload) { echo $gif_src[0]; } ?>" <?php if (!$gif_preload) { echo 'data-src="' . $gif_src[0] . '"'; } ?>>
						<?php }
					} else {
						thegem_generate_picture('THEGEM_TRANSPARENT_IMAGE', $thegem_size, [], array('alt' => get_the_title()));
					}

					if ($thegem_portfolio_item_data['grid_appearance_gif_start'] == 'play_on_hover') {
						if (!empty($thegem_portfolio_item_data['grid_appearance_gif_poster'])) { ?>
							<img class="gem-gif-poster" src="<?php echo $thegem_portfolio_item_data['grid_appearance_gif_poster']; ?>">
						<?php } else {
							thegem_generate_picture($thegem_portfolio_item_data['grid_appearance_gif'], $thegem_size, [], array("class" => "gem-gif-poster"));
						}
					} ?>
					<svg class="gif-type-icon" enable-background="new 0 0 9 5" viewBox="0 0 9 5" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m8.1 0h-7.2c-.5 0-.9.4-.9.9v3.2c0 .5.4.9.9.9h7.2c.5 0 .9-.4.9-.9v-3.2c0-.5-.4-.9-.9-.9zm-4.6 1.8h-1.7v1.5h1v-.8h.8v1c-.1.3-.3.5-.6.5h-1.5c-.3 0-.5-.2-.5-.5v-2c0-.2.2-.5.5-.5h1.5c.3 0 .5.2.5.5zm1.5 2.2h-.7v-3h.7zm3-2.2h-1.5v.5h1v.7h-1v1h-.7v-3h2.2z" fill-rule="evenodd"/></svg>
				<?php } else if ($is_gallery_type) {
					$thegem_gallery_images_ids = get_post_meta(get_the_ID(), 'thegem_portfolio_item_gallery_images', true);
					$thegem_gallery_images_urls = [];
					if ($thegem_gallery_images_ids) {
						$attachments_ids = array_filter(explode(',', $thegem_gallery_images_ids)); ?>
						<div class="portfolio-image-slider <?php if ($thegem_portfolio_item_data['grid_appearance_gallery_autoscroll']) { echo 'autoscroll'; } ?>" <?php if ($thegem_portfolio_item_data['grid_appearance_gallery_autoscroll']) { ?> data-interval="<?php echo $thegem_portfolio_item_data['grid_appearance_gallery_autoscroll_speed']; ?>"<?php } ?>>
							<?php foreach ($attachments_ids as $i => $slide) {
								$slide_image = wp_get_attachment_image($slide, $thegem_size);
								if (empty($slide_image)) {
									continue;
								}
								$thegem_gallery_images_urls[] = wp_get_attachment_image_url($slide, 'full'); ?>
								<div class="slide <?php echo $slide; ?>"<?php if ($i != 0) {echo ' style="opacity: 0;"';} ?>>
									<?php echo $slide_image; ?>
								</div>
							<?php } ?>
							<button class="btn btn-next" data-direction="next"></button>
							<button class="btn btn-prev" data-direction="prev"></button>
						</div>
					<?php }
					thegem_generate_picture('THEGEM_TRANSPARENT_IMAGE', $thegem_size, [], array('alt' => get_the_title()));
				} else {
					if ($thegem_has_post_thumbnail) {
						thegem_post_picture($thegem_size, [], array('alt' => get_the_title()));
					} else {
						thegem_generate_picture('THEGEM_TRANSPARENT_IMAGE', $thegem_size, array(), array('alt' => get_the_title()));
					}
				} ?>
			</div>

			<?php
			$icons = [];
			if (isset($settings['search_portfolio'])) {
				$link_type = 'self-link';
				$link_target = '';
			} else {
				if ($is_video_type) {
					$link_type = $thegem_portfolio_item_data['grid_appearance_video_behavior'];
				} else if ($is_gif_type) {
					$link_type = $thegem_portfolio_item_data['grid_appearance_gif_behavior'];
				} else if ($is_gallery_type) {
					$link_type = $thegem_portfolio_item_data['grid_appearance_gallery_behavior'];
				} else {
					$link_type = isset($thegem_portfolio_item_data['grid_appearance_image_behavior']) ? $thegem_portfolio_item_data['grid_appearance_image_behavior'] : 'multiple_choice';
				}

				$link_target = isset($thegem_portfolio_item_data['grid_appearance_behavior_target']) ? $thegem_portfolio_item_data['grid_appearance_behavior_target'] : '_self';
				if ($link_type == 'link_to_page') {
					if (isset($settings['disable_link']) && $settings['disable_link'] == 'yes') {
						$thegem_link = '';
						$thegem_caption_classes[] = 'click-disabled';
					} else {
						$thegem_link = get_permalink();
						$icon['type'] = $link_type = 'self-link';
						$icon['link_target'] = $link_target;
						$icons[] = $icon;
						$thegem_bottom_line = true;
						$thegem_portfolio_button_link = $thegem_link;
					}
				} else if ($link_type == 'click_disabled') {
					$thegem_link = '';
					$thegem_caption_classes[] = 'click-disabled';
				} else if ($link_type == 'lightbox') {
					if ($is_video_type) {
						$thegem_link = $thegem_video_link;
					} else if ($is_gif_type) {
						$thegem_link = wp_get_attachment_image_url($thegem_portfolio_item_data['grid_appearance_gif'], 'full');
					} else if ($is_gallery_type) {
						if (!empty($thegem_gallery_images_urls)) {
							$thegem_link = $thegem_gallery_images_urls[0];
						}
					} else {
						if (!empty($thegem_portfolio_item_data['grid_appearance_lightbox_image'])) {
							$thegem_link = $thegem_portfolio_item_data['grid_appearance_lightbox_image'];
							$icon['lightbox_image'] = $thegem_portfolio_item_data['grid_appearance_lightbox_image'];
						} else {
							$thegem_link = $thegem_large_image_url ? $thegem_large_image_url[0] : '';
						}
					}
					$link_target = '_self';
					$icon['type'] = $link_type = 'full-image';
					$icon['link_target'] = $link_target;
					$icons[] = $icon;
				} else if ($link_type == 'custom_link') {
					$thegem_link = $thegem_portfolio_item_data['grid_appearance_behavior_custom_link'];
					$link_target = $thegem_portfolio_item_data['grid_appearance_behavior_custom_link_target'];
					$icon['type'] = $link_type = 'outer-link';
					$icon['link'] = $thegem_link;
					$icon['link_target'] = $link_target;
					$icons[] = $icon;
				} else if ($link_type == 'multiple_choice') {
					$icons = $thegem_portfolio_item_data['types'];
					$first_icon = $icons[0];
					if ($first_icon) {
						$type = $first_icon['type'];
						if ($type == 'self-link') {
							if (isset($settings['disable_link']) && $settings['disable_link'] == 'yes') {
								$thegem_link = '';
								$thegem_caption_classes[] = 'click-disabled';
							} else {
								$thegem_link = get_permalink();
								$thegem_bottom_line = true;
								$thegem_portfolio_button_link = $thegem_link;
							}
						} else if ($type == 'full-image') {
							if (!empty($first_icon['lightbox_image'])) {
								$thegem_link = $first_icon['lightbox_image'];
							} else {
								$thegem_link = $thegem_large_image_url ? $thegem_large_image_url[0] : '';
							}
						} else if ($type == 'youtube') {
							$thegem_link = '//www.youtube.com/embed/' . $first_icon['link'] . '?autoplay=1';
						} else if ($type == 'vimeo') {
							$thegem_link = '//player.vimeo.com/video/' . $first_icon['link'] . '?autoplay=1';
						} else if ($type == 'self_video') {
							$thegem_link = $first_icon['link'];
						} else {
							$thegem_link = $first_icon['link'];
						}
						$link_target = $first_icon['link_target'];
						$link_type = $type;
					}
				}
			} ?>
			<div class="overlay <?php echo empty($thegem_link) ? 'click-disabled' : ''; ?>">
				<?php if ($is_image_type) { ?>
					<div class="overlay-circle"></div>
				<?php }
				if ($settings['icons_show'] !== 'yes' || $settings['image_hover_effect'] == 'zoom-overlay' || $settings['image_hover_effect'] == 'disabled' || !$is_image_type) {
					if (!empty($thegem_link)) { ?>
						<a href="<?php echo esc_url($thegem_link); ?>"
						   target="<?php echo esc_attr($link_target); ?>"
						   class="portfolio-item-link <?php echo esc_attr($link_type); ?>"
						   <?php if (in_array($link_type, ['full-image', 'self_video', 'youtube', 'vimeo'])) { ?>data-fancybox="thegem-portfolio" <?php } ?>
						   data-elementor-open-lightbox="no">
							<span class="screen-reader-text"><?php the_title(); ?></span>
						</a>
					<?php }
					if (!empty($thegem_gallery_images_urls)) {
						foreach ($thegem_gallery_images_urls as $i => $url) {
							if ($i == 0) continue; ?>
							<a style="display: none" href="<?php echo esc_url($url); ?>" data-fancybox="thegem-portfolio" data-elementor-open-lightbox="no"></a>
						<?php }
					}
				}
				if ($is_image_type || $settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular' || $settings['image_hover_effect'] == 'disabled') { ?>
				<div class="links-wrapper">
					<div class="links">
						<?php if ($is_image_type && $settings['icons_show'] == 'yes' && $settings['image_hover_effect'] != 'zoom-overlay' && $settings['image_hover_effect'] != 'disabled') { ?>
							<div class="portfolio-icons">
								<?php if (!empty($icons)) {
									foreach ($icons as $icon) {
										$link_target = isset($icon['link_target']) ? $icon['link_target'] : '_self';
										if ($icon['type'] == 'full-image') {
											if (!empty($icon['lightbox_image'])) {
												$thegem_link = $icon['lightbox_image'];
											} else {
												$thegem_link = $thegem_large_image_url ? $thegem_large_image_url[0] : '';
											}
											if (!$thegem_link) {
												continue;
											}
											$link_target = '_self';
										} elseif ($icon['type'] == 'self-link') {
											if (isset($settings['disable_link']) && $settings['disable_link'] == 'yes') {
												$thegem_link = '';
											} else {
												$thegem_link = get_permalink();
												$thegem_bottom_line = true;
												$thegem_portfolio_button_link = $thegem_link;
											}
										} elseif ($icon['type'] == 'youtube') {
											$thegem_link = '//www.youtube.com/embed/' . $icon['link'] . '?autoplay=1';
										} elseif ($icon['type'] == 'vimeo') {
											$thegem_link = '//player.vimeo.com/video/' . $icon['link'] . '?autoplay=1';
										} else {
											$thegem_link = $icon['link'];
										}
										if (empty($thegem_link)) {
											continue;
										}
										if ($icon['type'] == 'self_video') {
											$thegem_self_video = $icon['link'];
										}

										if ($icon['type'] == 'youtube' || $icon['type'] == 'vimeo' || $icon['type'] == 'self_video') {
											$link_icon = 'video';
										} else {
											$link_icon = $icon['type'];
										} ?>
										<a href="<?php echo esc_url($thegem_link); ?>"
										   target="<?php echo esc_attr($link_target); ?>"
										   class="icon <?php echo esc_attr($icon['type']); ?> <?php echo $icon['type'] !== $link_icon ? esc_attr($link_icon) : ''; ?>"
										   <?php if (in_array($icon['type'], ['full-image', 'self_video', 'youtube', 'vimeo'])) { ?>data-fancybox="thegem-portfolio" <?php } ?>
										   data-elementor-open-lightbox="no">
											<?php if ($settings['hover_icon_' . $link_icon] && $settings['hover_icon_' . $link_icon]['value']) {
												Icons_Manager::render_icon($settings['hover_icon_' . $link_icon], ['aria-hidden' => 'true']);
											} else { ?>
												<i class="default"></i>
											<?php } ?>
										</a>
									<?php }
								}
								if ($settings['social_sharing'] == 'yes'): ?>
									<a href="javascript: void(0);" class="icon share">
										<?php if ($settings['hover_icon_share'] && $settings['hover_icon_share']['value']) {
											Icons_Manager::render_icon($settings['hover_icon_share'], ['aria-hidden' => 'true']);
										} else { ?>
											<i class="default"></i>
										<?php } ?>
									</a>
								<?php endif; ?>
								<div class="overlay-line"></div>
								<?php if ($settings['social_sharing'] == 'yes'): ?>
									<div class="portfolio-sharing-pane"><?php include 'socials-sharing.php'; ?></div>
								<?php endif; ?>
							</div>
						<?php } ?>
						<?php if ($settings['caption_position'] == 'image' || $settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular'): ?>
							<div class="caption">
								<?php if ($settings['portfolio_show_title'] == 'yes') { ?>
									<div class="title title-h4">
										<?php if ($hover_effect != 'default' && $hover_effect != 'gradient' && $hover_effect != 'circular') {
											echo '<span class="light">';
										} ?>
										<?php if (!empty($thegem_portfolio_item_data['overview_title'])) : ?>
											<?php echo $thegem_portfolio_item_data['overview_title']; ?>
										<?php else : ?>
											<?php the_title(); ?>
										<?php endif; ?>
										<?php if ($settings['image_hover_effect'] != 'default') {
											echo '</span>';
										} ?>
									</div>
								<?php } ?>
								<div class="description">
									<?php if ($settings['portfolio_show_description'] == 'yes' && has_excerpt()) : ?>
										<div class="subtitle"><?php the_excerpt(); ?></div>
									<?php endif; ?>
									<?php if ($settings['portfolio_show_date'] == 'yes' || $settings['portfolio_show_sets'] == 'yes'): ?>
										<div class="info">
											<?php if ($settings['portfolio_show_date'] == 'yes') {
												echo '<span class="date">' . get_the_date('j F, Y') . '</span>';
											}
											if ($settings['portfolio_show_date'] == 'yes' && $settings['portfolio_show_sets'] == 'yes') {
												echo(' ');
											}
											if ($settings['portfolio_show_sets'] == 'yes') {
												echo '<span class="set">';
												foreach ($slugs as $thegem_k => $thegem_slug) {
													if (in_array($thegem_slug, $terms)) {
														$term = get_term_by('slug', $thegem_slug, 'thegem_portfolios');
														if ($term) {
															echo '<a data-slug="' . $term->slug . '">' . $term->name . '</a>';
														}
													}
												}
												echo '</span>';
											} ?>
										</div>
									<?php endif ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
		<?php if ($settings['caption_position'] != 'image' && $settings['image_hover_effect'] != 'gradient' && $settings['image_hover_effect'] != 'circular'): ?>
			<div <?php post_class($thegem_caption_classes); ?>>
				<div class="caption-sizable-content<?php echo($thegem_bottom_line ? ' with-bottom-line' : ''); ?>">
					<?php if ($settings['portfolio_show_title'] == 'yes') { ?>
						<div class="title title-h3"><span class="light">
						<?php if (!empty($thegem_portfolio_item_data['overview_title'])) : ?>
							<?php echo $thegem_portfolio_item_data['overview_title']; ?>
						<?php else : ?>
							<?php the_title(); ?>
						<?php endif; ?>
					</span></div>
					<?php } ?>
					<?php if ($settings['portfolio_show_date'] == 'yes' || $settings['portfolio_show_likes'] == 'yes'): ?>
						<div class="info clearfix">
							<?php if ($settings['portfolio_show_date'] == 'yes') { ?>
								<div class="caption-separator-line"><span
										class="date"><?php echo get_the_date('j F, Y'); ?></span></div><?php }
							if ($settings['portfolio_show_likes'] == 'yes' && function_exists('zilla_likes')) {
								?><div class="caption-separator-line-hover"><?php
								if ($settings['portfolio_show_date'] == 'yes') { ?><span class="sep"></span><?php }
								?><span class="post-meta-likes portfolio-list-likes"><?php
										if ($settings['likes_icon'] && $settings['likes_icon']['value']) {
											Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);
										} else { ?><i class="default"></i><?php }
										zilla_likes();
										?></span>
								</div>
							<?php } ?>
						</div>
					<?php endif; ?>
					<?php
					if ($settings['portfolio_show_description'] == 'yes') {
						if (has_excerpt()) : ?>
							<div class="subtitle"><?php the_excerpt(); ?></div>
						<?php elseif ($thegem_title_data['title_excerpt']) : ?>
							<div class="subtitle"><?php echo nl2br($thegem_title_data['title_excerpt']); ?></div>
						<?php endif;
					} ?>
					<?php if ($settings['portfolio_show_sets'] == 'yes'): ?>
						<div class="info">
							<?php
							echo '<span class="set">';
							if (count($slugs) > 0) {

								echo('<span class="in_text">' . $in_text . '</span> ');
							}
							$thegem_index = 0;
							foreach ($slugs as $thegem_k => $thegem_slug) {
								if (in_array($thegem_slug, $terms)) {
									$term = get_term_by('slug', $thegem_slug, 'thegem_portfolios');
									if ($term) {
										echo ($thegem_index > 0 ? '<span class="portfolio-set-comma">,</span> ' : '') . '<a data-slug="' . $term->slug . '">' . $term->name . '</a>';
										$thegem_index++;
									}
								}
							}
							echo '</span>';
							?>
						</div>
					<?php endif; ?>
					<div class="after-overlay"></div>
				</div>
				<div class="caption-bottom-line">
					<?php if ($thegem_portfolio_item_data['project_link']) {
						thegem_button(array('size' => 'tiny', 'href' => $thegem_portfolio_item_data['project_link'], 'text' => ($thegem_portfolio_item_data['project_text'] ? $thegem_portfolio_item_data['project_text'] : __('Launch', 'thegem')), 'extra_class' => 'project-button'), 1);
					} ?>
					<?php if ($settings['portfolio_show_details'] == 'yes' && $thegem_portfolio_button_link) { ?>
						<div class="details-button gem-button-container gem-widget-button gem-button-position-inline">
							<a href="<?= get_permalink() ?>" <?php post_class($button_classes); ?>>
								<span class="gem-inner-wrapper-btn">
									<span class="gem-text-button">
										<?php echo '<span>' . wp_kses($settings['details_button_text'], 'post') . '</span>'; ?>
									</span>
								</span>
							</a>
						</div>
					<?php } ?>
					<?php if ($settings['social_sharing'] == 'yes'): ?>
						<div class="post-footer-sharing">
							<div class="gem-button-container gem-widget-button gem-button-position-inline">
								<?php thegem_button(array('icon' => 'share', 'size' => 'tiny'), 1); ?>
							</div>
							<div class="sharing-popup"><?php include 'socials-sharing.php'; ?>
								<svg class="sharing-styled-arrow">
									<use xlink:href="<?php echo esc_url(THEGEM_THEME_URI . '/css/post-arrow.svg'); ?>#dec-post-arrow"></use>
								</svg>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
