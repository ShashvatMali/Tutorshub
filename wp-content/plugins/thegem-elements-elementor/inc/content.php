<?php

function thegem_slideshow_block($params = array()) {
	$slider_editor_class = '';
	$slider_editor_block = '';
	if(class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->preview->is_preview_mode()) {
		$slider_editor_class = ' gem-slideshow-editor';
		$button_text = esc_html__('Edit Slider', 'thegem');
		if($params['slideshow_type'] == 'LayerSlider') {
			$button_text = esc_html__('Edit LayerSlider', 'thegem');
		} elseif($params['slideshow_type'] == 'revslider') {
			$button_text = esc_html__('Edit Revolution Slider', 'thegem');
		}
		$slider_editor_block = '<div class="edit-template-overlay"><div class="buttons"><a class="gem-tta-template-edit" data-tta-template-edit-link="%s" target="_blank">'.$button_text.'</a></div></div>';
		$params['preloader'] = false;
	}
	$slider_start = '<div class="gem-slideshow'.$slider_editor_class.'">';
	if(!empty($params['preloader'])) {
		$sr_styles = '';
		if($params['slideshow_type'] == 'revslider') {
			global $SR_GLOBALS;
			if(!empty($SR_GLOBALS) && !empty($SR_GLOBALS['front_version']) && $SR_GLOBALS['front_version'] === 7) {
				$sr_styles = '<style>.slideshow-preloader + .gem-slideshow-rs {position: relative; top: calc(var(--thegem-sr-height) * -1); height: auto !important; opacity: 0;}</style>';
			}
		}
		$slider_start = '<div class="preloader slideshow-preloader">'.$sr_styles.'<div class="preloader-spin"></div></div><div class="gem-slideshow gem-slideshow-with-preloader gem-slideshow-rs'.$slider_editor_class.'">';
	}
	if($params['slideshow_type'] == 'LayerSlider') {
		if($params['lslider']) {
			echo '<div class="preloader slideshow-preloader"><div class="preloader-spin"></div></div><div class="gem-slideshow gem-slideshow-with-preloader'.$slider_editor_class.'">';
			echo do_shortcode('[layerslider id="'.$params['lslider'].'"]');
			echo sprintf($slider_editor_block, admin_url('admin.php?page=layerslider&action=edit&id='.(int)$params['lslider']));
			echo '</div>';
		}
	} elseif($params['slideshow_type'] == 'revslider' && class_exists('RevSliderSlider')) {
		if($params['slider']) {
			echo $slider_start;
			echo do_shortcode('[rev_slider alias="'.$params['slider'].'"]');
			$slider = new RevSliderSlider();
			$slider->init_by_alias($params['slider'], false);
			$slides = $slider->get_slides();
			$slide_id = '0';
			if(!empty($slides)){
				foreach($slides as $slide){
					$slide_id = $slide->get_id();
					break;
				}
			}
			echo sprintf($slider_editor_block, admin_url('admin.php?page=revslider&view=slide&id='.$slide_id));
			echo '</div>';
			if(!empty($params['preloader'])) {
?>
<script type="text/javascript">
var thegemSlideshowPreloader = document.querySelector('.slideshow-preloader');
var thegemSlideshow = document.querySelector('.gem-slideshow');
var thegemSlideshowError = thegemSlideshow.querySelector('.rs_error_message_content');
if(thegemSlideshow.hasChildNodes()) {
	thegemSlideshow.parentNode.insertBefore(thegemSlideshow, thegemSlideshowPreloader);
	var thegemSlideshowHeight = thegemSlideshow.clientHeight;
	thegemSlideshowPreloader.style.height = thegemSlideshowHeight +'px';
	thegemSlideshow.style.setProperty('--thegem-sr-height', thegemSlideshowHeight +'px');
	thegemSlideshow.parentNode.insertBefore(thegemSlideshowPreloader,thegemSlideshow);
} else {
	thegemSlideshowPreloader.remove();
	thegemSlideshow.remove();
}
if(thegemSlideshowError) {
	thegemSlideshowPreloader.remove();
}
</script>
<?php
			}
		}
	} elseif($params['slideshow_type'] == 'NivoSlider') {
		echo '<div class="preloader slideshow-preloader"><div class="preloader-spin"></div></div><div class="gem-slideshow gem-slideshow-with-preloader">';
		thegem_nivoslider($params);
		echo '</div>';
	}
}

function thegem_revslider_preloader_fix() {
?>
(function() {
	jQuery(document).ready(function() {
		jQuery('.gem-slideshow-with-preloader.gem-slideshow-rs').each(function() {
			var slideshow = jQuery(this);
			slideshow.trigger('thegem-preloader-loaded');
		});
	});
	var revapi = jQuery(document).ready(function() {});
	revapi.one('revolution.slide.onloaded', function() {
		jQuery('.gem-slideshow').prev('.slideshow-preloader').remove();
	});
	document.addEventListener("sr.module.ready", function(e) {
		jQuery('.gem-slideshow').prev('.slideshow-preloader').remove();
	});
})();
<?php
}
add_action('revslider_fe_javascript_output', 'thegem_revslider_preloader_fix', 101);

function portolios_cmp($term1, $term2) {
	$order1 = get_option('portfoliosets_' . $term1->term_id . '_order', 0);
	$order2 = get_option('portfoliosets_' . $term2->term_id . '_order', 0);
	if($order1 == $order2)
		return 0;
	return $order1 > $order2;
}

function thegem_nivoslider($params = array()) {
	$params = array_merge(array('slideshow' => ''), $params);
	$args = array(
		'post_type' => 'thegem_slide',
		'orderby' => 'menu_order ID',
		'order' => 'ASC',
		'posts_per_page' => -1,
	);
	if($params['slideshow']) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'thegem_slideshows',
				'field' => 'slug',
				'terms' => explode(',', $params['slideshow'])
			)
		);
	}
	$slides = new WP_Query($args);

	if($slides->have_posts()) {

		wp_enqueue_style('nivo-slider');
		wp_enqueue_script('thegem-nivoslider-init-script');

		echo '<div class="preloader"><div class="preloader-spin"></div></div>';
		echo '<div class="gem-nivoslider">';
		while($slides->have_posts()) {
			$slides->the_post();
			if(has_post_thumbnail()) {
				$item_data = thegem_get_sanitize_slide_data(get_the_ID());
?>
	<?php if($item_data['link']) : ?>
		<a href="<?php echo esc_url($item_data['link']); ?>" target="<?php echo esc_attr($item_data['link_target']); ?>" class="gem-nivoslider-slide">
	<?php else : ?>
		<div class="gem-nivoslider-slide">
	<?php endif; ?>
	<?php thegem_post_thumbnail('full', false, ''); ?>
	<?php if($item_data['text_position']) : ?>
		<div class="gem-nivoslider-caption" style="display: none;">
			<div class="caption-<?php echo esc_attr($item_data['text_position']); ?>">
				<div class="gem-nivoslider-title"><?php the_title(); ?></div>
				<div class="clearboth"></div>
				<div class="gem-nivoslider-description"><?php the_excerpt(); ?></div>
			</div>
		</div>
	<?php endif; ?>
	<?php if($item_data['link']) : ?>
		</a>
	<?php else : ?>
		</div>
	<?php endif; ?>
<?php
			}
		}
		echo '</div>';
	}
	wp_reset_postdata();
}

function thegem_atts_product_category_grid($out, $pairs, $atts, $shortcode) {
	if (isset($atts['thegem_grid_params'])) {
		$out['thegem_grid_params'] = unserialize(htmlspecialchars_decode($atts['thegem_grid_params']));
	}
	return $out;
}
add_filter('shortcode_atts_product_category', 'thegem_atts_product_category_grid', 10, 4);

function thegem_query_product_category_grid($query_args, $atts, $loop_name) {
	if (($loop_name == 'product_cat' || $loop_name == 'product_category') && isset($atts['thegem_grid_params'])) {
		$query_args['orderby'] = $atts['thegem_grid_params']['orderby'];
		$query_args['order'] = $atts['thegem_grid_params']['order'];

		if ($atts['thegem_grid_params']['pagination'] == 'more' || $atts['thegem_grid_params']['pagination'] == 'scroll') {
			$query_args['paged'] = $atts['thegem_grid_params']['grid_page'];
			$query_args['no_found_rows'] = false;
		} else {
			$query_args['posts_per_page'] = -1;
		}
	}
	return $query_args;
}
add_filter('woocommerce_shortcode_products_query', 'thegem_query_product_category_grid', 10, 3);

function thegem_product_category_grid_before_loop($atts) {
	if (isset($GLOBALS['thegem_grid_params'])) {
		unset($GLOBALS['thegem_grid_params']);
	}
	if (!isset($atts['thegem_grid_params'])) {
		return;
	}
	$GLOBALS['thegem_grid_params'] = $atts['thegem_grid_params'];
}
add_action('woocommerce_shortcode_before_product_cat_loop', 'thegem_product_category_grid_before_loop');
add_action('woocommerce_shortcode_before_product_category_loop', 'thegem_product_category_grid_before_loop');

function thegem_product_category_grid_loop_start($wp_query) {
	if (!isset($GLOBALS['thegem_grid_params'])) {
		return;
	}
	$params = $GLOBALS['thegem_grid_params'];

	$terms = explode(',', $params['categories']);
	foreach($terms as $key => $term) {
		$terms[$key] = get_term_by('slug', $term, 'product_cat');
		if(!$terms[$key]) {
			unset($terms[$key]);
		}
	}

	$thegem_terms_set = array();
	foreach ($terms as $term) {
		$thegem_terms_set[$term->slug] = $term;
	}

	$gap_size = round(intval($params['gaps_size'])/2);

	( isset($params['gem_product_grid_featured_products_hide_label']) && $params['gem_product_grid_featured_products_hide_label'] == 1 ) ? $class_hide_label_new = 'hide_label_new' : $class_hide_label_new = '';
	( isset($params['gem_product_grid_onsale_products_hide_label']) && $params['gem_product_grid_onsale_products_hide_label'] == 1 ) ? $class_hide_label_sale = 'hide_label_onsale' : $class_hide_label_sale = '';

	$next_page = 0;
	if ($wp_query->max_num_pages > $params['grid_page']) {
		$next_page = $params['grid_page'] + 1;
	} else {
		$next_page = 0;
	}
	$GLOBALS['thegem_grid_params']['next_page'] = $next_page;
?>

	<?php if(!$params['is_ajax']) : ?>
		<?php echo apply_filters('thegem_portfolio_preloader_html', '<div class="preloader"><div class="preloader-spin"></div></div>'); ?>
		<div class="portfolio-preloader-wrapper">
		<?php if($params['title']): ?>
			<h3 class="title portfolio-title"><?php echo $params['title']; ?></h3>
		<?php endif; ?>

		<?php

			$portfolio_classes = array(
				'portfolio',
				'products-grid',
				'products',
				'no-padding',
				'portfolio-pagination-' . $params['pagination'],
				'portfolio-style-' . $params['style'],
				'background-style-' . $params['background_style'],
				'title-style-' . $params['title_style'],
				'hover-' . esc_attr($params['hover']),
				'item-animation-' . $params['loading_animation'],
				'title-on-' . $params['display_titles'],
				$class_hide_label_new,
				$class_hide_label_sale,
			);

			if ($params['layout_columns'] == '1x') {
				$portfolio_classes[] = 'caption-position-' . $params['caption_position'];
			}

			if ($gap_size == 0) {
				$portfolio_classes[] = 'no-gaps';
			}

			if ($params['layout'] == '100%') {
				$portfolio_classes[] = 'fullwidth-columns-' . intval($params['fullwidth_columns']);
			}

			if ($params['display_titles'] == 'page' && $params['hover'] == 'gradient') {
				$portfolio_classes[] = 'hover-gradient-title';
			}

			if ($params['display_titles'] == 'page' && $params['hover'] == 'circular') {
				$portfolio_classes[] = 'hover-circular-title';
			}

			if ($params['display_titles'] == 'hover') {
				$portfolio_classes[] = 'hover-title';
			}

			if ($params['style'] == 'masonry' && $params['layout'] != '1x') {
				$portfolio_classes[] = 'portfolio-items-masonry';
			}

			if ($params['layout_columns'] != -1) {
				$portfolio_classes[] = 'columns-' . intval($params['layout_columns']);
			}

			if ( $params['item_separator'] && ( $params['display_titles'] == 'hover' || ($params['display_titles'] == 'page' && ( $params['hover'] == 'gradient' || $params['hover'] == 'circular' ) ) ) ) {
				$portfolio_classes[] = 'item-separator';
			}

			if ($params['disable_socials']) {
				$portfolio_classes[] = 'portfolio-disable-socials';
			}

			$portfolio_classes = apply_filters('portfolio_classes_filter', $portfolio_classes);

			$row_styles = '';
			if ($params['layout'] == '100%') {
				$row_styles .= 'margin: 0;';
				if ($gap_size) {
					if (thegem_get_option('page_padding_left')) {
						$row_styles .= 'margin-left: -' . $gap_size . 'px;';
					} else {
						$row_styles .= 'padding-left: ' . $gap_size . 'px;';
					}

					if (thegem_get_option('page_padding_right')) {
						$row_styles .= 'margin-right: -' . $gap_size . 'px;';
					} else {
						$row_styles .= 'padding-right: ' . $gap_size . 'px;';
					}
				}
			} else {
				if ($gap_size) {
					$row_styles .= 'margin: -' . $gap_size . 'px;';
				} else {
					$row_styles .= 'margin: 0;';
				}
			}
		?>

			<div data-per-page="<?php echo $params['items_per_page']; ?>" data-portfolio-uid="<?php echo esc_attr($params['portfolio_uid']); ?>" class="<?php echo implode(' ', $portfolio_classes); ?>" data-hover="<?php echo $params['hover']; ?>" <?php if($params['pagination'] == 'more' || $params['pagination'] == 'scroll'): ?>data-next-page="<?php echo esc_attr($next_page); ?>"<?php endif; ?>>
				<?php if(($params['with_filter'] && count($terms) > 0) || $params['sorting']): ?>
					<div class="portfolio-top-panel<?php if($params['layout'] == '100%'): ?> fullwidth-block<?php endif; ?>" <?php if ($gap_size && $params['layout'] == '100%'): ?>style="padding-left: <?php echo 2*$gap_size; ?>px; padding-right: <?php echo 2*$gap_size; ?>px;"<?php endif; ?>><div class="portfolio-top-panel-row">
						<div class="portfolio-top-panel-left">
						<?php if($params['with_filter'] && count($terms) > 0): ?>


							<div <?php if(!$params['sorting']): ?> style="text-align: center;"<?php endif; ?>  class="portfolio-filters">
								<a href="#" data-filter="*" class="active all title-h6"><?php echo thegem_build_icon('thegem-icons', 'portfolio-show-all'); ?><span class="light"><?php echo apply_filters('portfolio_show_all_filter', __('All', 'thegem')); ?></span></a>
								<?php foreach($terms as $term) : ?>
									<a href="#" data-filter=".<?php echo $term->slug; ?>" class="title-h6"><span class="light"><?php echo $term->name; ?></span></a>
								<?php endforeach; ?>
							</div>
							<div class="portfolio-filters-resp">
								<button class="menu-toggle dl-trigger"><?php _e('Portfolio filters', 'thegem'); ?><span class="menu-line-1"></span><span class="menu-line-2"></span><span class="menu-line-3"></span></button>
								<ul class="dl-menu">
									<li><a href="#" data-filter="*"></span><?php _e('Show All', 'thegem'); ?></a></li>
									<?php foreach($terms as $term) : ?>
										<li><a href="#" data-filter=".<?php echo esc_attr($term->slug); ?>"><?php echo $term->name; ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						</div>
						<div class="portfolio-top-panel-right">
						<?php if($params['sorting']): ?>
							<div class="portfolio-sorting title-h6">
								<div class="orderby light">
									<label for="" data-value="date"><?php _e('Date', 'thegem') ?></label>
									<a href="javascript:void(0);" class="sorting-switcher" data-current="date"></a>
									<label for="" data-value="name"><?php _e('Name', 'thegem') ?></label>
								</div>
								<div class="portfolio-sorting-sep"></div>
								<div class="order light">
									<label for="" data-value="DESC"><?php _e('Desc', 'thegem') ?></label>
									<a href="javascript:void(0);" class="sorting-switcher" data-current="DESC"></a>
									<label for="" data-value="ASC"><?php _e('Asc', 'thegem') ?></label>
								</div>
							</div>

						<?php endif; ?>
						</div>
					</div></div>
				<?php endif; ?>
				<div class="<?php if($params['layout'] == '100%'): ?>fullwidth-block no-paddings<?php endif; ?>">
				<div class="row" style="<?php echo $row_styles; ?>">
				<div class="portfolio-set clearfix" data-max-row-height="<?php echo floatval($params['metro_max_row_height']); ?>">
	<?php else: ?>
		<div data-page="<?php echo $params['grid_page']; ?>" data-next-page="<?php echo $next_page; ?>">
	<?php endif; ?>

<?php
}
add_action('loop_start', 'thegem_product_category_grid_loop_start');
add_action('thegem_products_loop_start', 'thegem_product_category_grid_loop_start');

function thegem_product_category_grid_after_loop($atts) {
	if (!isset($atts['thegem_grid_params']) || !isset($GLOBALS['thegem_grid_params'])) {
		return;
	}
	$params = $GLOBALS['thegem_grid_params'];
	$next_page = $params['next_page'];
	unset($GLOBALS['thegem_grid_params']);
?>
	<?php if(!$params['is_ajax']) : ?>
				</div><!-- .portflio-set -->
				<?php if ($params['layout'] != '1x'): ?>
					<div class="portfolio-item-size-container">
						<?php $product_grid_item_size = true; ?>
						<?php include(locate_template(array('woocommerce/content-product-grid-item.php'))); ?>
					</div>
				<?php endif; ?>
				</div><!-- .row-->
				<?php if($params['pagination'] == 'normal'): ?>
					<div class="portfolio-navigator gem-pagination">
					</div>
				<?php endif; ?>
				<?php if($params['pagination'] == 'more' && $next_page > 0): ?>
					<div class="portfolio-load-more">
						<div class="inner">
							<?php thegem_button(array_merge($params['button'], array('tag' => 'button')), 1); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if($params['pagination'] == 'scroll' && $next_page > 0): ?>
					<div class="portfolio-scroll-pagination"></div>
				<?php endif; ?>
			</div><!-- .full-width -->
		</div><!-- .portfolio-->
	</div><!-- .portfolio-preloader-wrapper-->
	<?php else: ?>
	</div>
	<?php endif; ?>
<?php
}
add_action('woocommerce_shortcode_after_product_cat_loop', 'thegem_product_category_grid_after_loop');
add_action('woocommerce_shortcode_after_product_category_loop', 'thegem_product_category_grid_after_loop');


if(!function_exists('thegem_video_background')) {
function thegem_video_background($video_type, $video, $aspect_ratio = '16:9', $headerUp = false, $color = '', $opacity = '', $poster='', $play_on_mobile = '', $background_fallback = '', $background_style = '', $background_position_horizontal = 'center', $background_position_vertical = 'top') {
	$output = $link = $uniqid = $video_class = $mobile = '';
	$uniqid = uniqid('thegem-video-frame-').rand(1,9999);
	$video_type = thegem_check_array_value(array('', 'youtube', 'vimeo', 'self'), $video_type, '');
	if($video_type && $video && defined('ELEMENTOR_VERSION')) {
		$video_block = $overlay_css = $fallback_css = $video_css = $video_data = '';
		if(!function_exists('isMobile')) {
			function isMobile() {
				return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
			}
		}
		if($video_type == 'youtube' || $video_type == 'vimeo') {
			if($play_on_mobile) {
				wp_enqueue_script('thegem-video');
				$video_class = ' background-video-container';
				$video_block = '<div class="background-video-embed"></div>';
			} elseif($background_fallback && !$play_on_mobile) {
				$fallback_css .= 'style="';
					$fallback_css .= 'background-image: url('.esc_url($background_fallback).');';
					if(!empty($background_style)) {
						$fallback_css .= 'background-size: '.esc_attr($background_style).';';
					} else {
						$fallback_css .= 'background-size: cover;';
					}
					$fallback_css .= 'background-position: '.$background_position_horizontal.' '.$background_position_vertical.';';
				$fallback_css .= '"';
				$output .= '<script type="text/javascript">
								(function($) {
									$("head").append("<style>@media (max-width: 767px) {#'.esc_attr($uniqid).' {display: none;}}</style>");
								})(jQuery);
							</script>';
			}
			if($video_type == 'youtube') {
				if($play_on_mobile && !\Elementor\Plugin::$instance->preview->is_preview_mode()) {
					$video_data = ' data-settings=\'{"url": "https://www.youtube.com/watch?v='.$video.'", "play_on_mobile": true, "background_play_once": false }\'';
				} else {
					$link = '//www.youtube.com/embed/'.$video.'?playlist='.$video.'&autoplay=1&mute=1&controls=0&playsinline=1&enablejsapi=1&loop=1&fs=0&showinfo=0&autohide=1&iv_load_policy=3&rel=0&disablekb=1&wmode=transparent';
					$video_block = '<iframe id="'.$uniqid.'" class="gem-video-background-iframe" src="'.esc_url($link).'" frameborder="0" muted="muted"></iframe>';
				}
			}
			if($video_type == 'vimeo') {
				if($play_on_mobile && !\Elementor\Plugin::$instance->preview->is_preview_mode()) {
					$video_data = ' data-settings=\'{"url": "https://vimeo.com/'.$video.'", "play_on_mobile": true, "background_play_once": false }\'';
				} elseif(empty($play_on_mobile) && !\Elementor\Plugin::$instance->preview->is_preview_mode() && !empty(isMobile())) {
					$link = '//player.vimeo.com/video/'.$video.'?autoplay=0&muted=1&controls=0&loop=1&title=0&badge=0&byline=0&autopause=0';
					$video_block = '<iframe id="'.$uniqid.'" class="gem-video-background-iframe" src="'.esc_url($link).'" frameborder="0" muted="muted"></iframe>';
				} else {
					$link = '//player.vimeo.com/video/'.$video.'?autoplay=1&muted=1&controls=0&loop=1&title=0&badge=0&byline=0&autopause=0';
					$video_block = '<iframe id="'.$uniqid.'" class="gem-video-background-iframe" src="'.esc_url($link).'" frameborder="0" muted="muted"></iframe>';
				}
			}
		} else {
			if($play_on_mobile && !\Elementor\Plugin::$instance->preview->is_preview_mode()) {
				wp_enqueue_script('thegem-video');
				$video_class = ' background-video-container';
				$video_data = ' data-settings=\'{"url": "'.$video.'", "play_on_mobile": true, "background_play_once": false }\'';
				$video_block = '<video id="'.$uniqid.'" class="background-video-hosted html5-video" autoplay muted playsinline loop'.($poster ? ' poster="'.esc_url($poster).'"' : '').'></video>';
			} elseif($background_fallback && !$play_on_mobile) {
				$fallback_css .= 'style="';
					$fallback_css .= 'background-image: url('.esc_url($background_fallback).');';
					if(!empty($background_style)) {
						$fallback_css .= 'background-size: '.esc_attr($background_style).';';
					} else {
						$fallback_css .= 'background-size: cover;';
					}
					$fallback_css .= 'background-position: '.$background_position_horizontal.' '.$background_position_vertical.';';
				$fallback_css .= '"';
				$output .= '<script type="text/javascript">
								(function($) {
									$("head").append("<style>@media (max-width: 767px) {#'.esc_attr($uniqid).' {display: none;}}</style>");
								})(jQuery);
							</script>';
				$video_block = '<video id="'.$uniqid.'" autoplay="autoplay" loop="loop" src="'.$video.'" muted="muted"'.($poster ? ' poster="'.esc_url($poster).'"' : '').'></video>';
			} else {
				$video_block = '<video id="'.$uniqid.'" autoplay="autoplay" loop="loop" src="'.$video.'" muted="muted"'.($poster ? ' poster="'.esc_url($poster).'"' : '').'></video>';
			}
		}

		if($color) {
			$overlay_css .= 'background-color: '.$color.'; opacity: '.floatval($opacity).';';
		}

		$output .= '<div class="gem-video-background" data-aspect-ratio="'.esc_attr($aspect_ratio).'"'.($headerUp ? ' data-headerup="1"' : '').''.$fallback_css.'>';
			$output .= '<div class="gem-video-background-inner'.$video_class.'"'.$video_data.'>'.$video_block.'</div>';
			$output .= '<div class="gem-video-background-overlay" style="'.$overlay_css.'"></div>';
		$output .= '</div>';
	}

	if (class_exists('TheGemGdpr')) {
		$type = null;
		switch ($video_type) {
			case 'youtube':
				$type = TheGemGdpr::CONSENT_NAME_YOUTUBE;
				break;
			case 'vimeo':
				$type = TheGemGdpr::CONSENT_NAME_VIMEO;
				break;
		}

		if (!empty($type)) {
			return TheGemGdpr::getInstance()->replace_disallowed_content($output, $type);
		}
	}


	return $output;
}
}


// Print Product Slider
function thegem_atts_product_category_slider($out, $pairs, $atts, $shortcode) {
	if (isset($atts['thegem_slider_params'])) {
		$out['thegem_slider_params'] = unserialize(htmlspecialchars_decode($atts['thegem_slider_params']));
	}
	return $out;
}
add_filter('shortcode_atts_product_category', 'thegem_atts_product_category_slider', 10, 4);

function thegem_product_category_slider_before_loop($atts) {
	if (isset($GLOBALS['thegem_slider_params'])) {
		unset($GLOBALS['thegem_slider_params']);
	}
	if (!isset($atts['thegem_slider_params'])) {
		return;
	}
	$GLOBALS['thegem_slider_params'] = $atts['thegem_slider_params'];
}
add_action('woocommerce_shortcode_before_product_cat_loop', 'thegem_product_category_slider_before_loop');
add_action('woocommerce_shortcode_before_product_category_loop', 'thegem_product_category_slider_before_loop');

function thegem_product_category_slider_loop_start($wp_query) {
	if (!isset($GLOBALS['thegem_slider_params'])) {
		return;
	}
	$params = $GLOBALS['thegem_slider_params'];

	$gap_size = round(intval($params['gaps_size'])/2);

	$layout_columns_count = -1;
	if ($params['layout'] == '3x')
		$layout_columns_count = 3;
	if ($params['layout'] == '2x')
		$layout_columns_count = 2;

	$layout_fullwidth = false;
	if ($params['layout'] == '100%')
		$layout_fullwidth = true;

	$classes = array('portfolio', 'portfolio-slider', 'products-slider', 'products', 'clearfix', 'no-padding', 'col-lg-12', 'col-md-12', 'col-sm-12', 'hover-'.$params['hover']);
	if($layout_fullwidth)
		$classes[] = 'full';
	if( ($params['display_titles'] == 'hover' && $params['layout'] != '1x') || $params['hover'] == 'gradient' || $params['hover'] == 'circular' )
		$classes[] = 'hover-title';
	if ($params['display_titles'] == 'page' && $params['hover'] == 'gradient')
		$classes[] = 'hover-gradient-title';
	if ($params['display_titles'] == 'page' && $params['hover'] == 'circular')
		$classes[] = 'hover-circular-title';
	if($layout_columns_count != -1)
		$classes[] = 'columns-'.$layout_columns_count;
	if($params['no_gaps'])
		$classes[] = 'without-padding';
	if($params['layout'] == '100%')
		$classes[] = 'fullwidth-columns-'.$params['fullwidth_columns'];

	$classes[] = 'portfolio-items-' . $params['style'];

	if ($params['effects_enabled']) {
		$classes[] = 'lazy-loading';
		thegem_lazy_loading_enqueue();
	}

	if ($params['disable_socials'])
		$classes[] = 'disable-socials';
	if ($params['slider_arrow'])
		$classes[] = $params['slider_arrow'];
	if ($params['background_style'])
		$classes[] = 'background-style-'.$params['background_style'];
	if ($params['title_style'])
		$classes[] = 'title-style-'.$params['title_style'];
	if ( $params['item_separator'] && ( $params['display_titles'] == 'hover' || ($params['display_titles'] == 'page' && ( $params['hover'] == 'gradient' || $params['hover'] == 'circular' ) ) ) ) {
		$classes[] = 'item-separator';
	}
	if ($params['disable_socials']) {
		$classes[] = 'portfolio-disable-socials';
	}

	$classes[] = 'title-on-' . $params['display_titles'];
	$classes[] = 'gem-slider-animation-' . $params['animation'];

	?>

	<div class="preloader"><div class="preloader-spin"></div></div>
	<div <?php post_class($classes); ?> <?php if($params['effects_enabled']): ?>data-ll-item-delay="0"<?php endif;?> data-hover="<?php echo esc_attr($params['hover']); ?>">
		<div class="navigation <?php if($layout_fullwidth): ?>fullwidth-block<?php endif; ?>">
			<?php if($params['title']): ?>
				<h3 class="title <?php if($params['effects_enabled']): ?>lazy-loading-item<?php endif;?>" <?php if($params['effects_enabled']): ?>data-ll-effect="fading"<?php endif;?>><?php echo $params['title']; ?></h3>
			<?php endif; ?>
			<div class="portolio-slider-prev">
				<span>&#xe603;</span>
			</div>

			<div class="portolio-slider-next">
				<span>&#xe601;</span>
			</div>

			<div class="portolio-slider-content">
				<div class="portolio-slider-center">
					<div class="<?php if($params['layout'] == '100%'): ?>fullwidth-block<?php endif; ?>">
						<div style="margin: -<?php echo $gap_size; ?>px;">
							<div class="portfolio-set clearfix" <?php if(intval($params['autoscroll'])) { echo 'data-autoscroll="'.intval($params['autoscroll']).'"'; } ?>>
	<?php
}
add_action('loop_start', 'thegem_product_category_slider_loop_start');
add_action('thegem_products_loop_start', 'thegem_product_category_slider_loop_start');

function thegem_product_category_slider_after_loop($atts) {
	if (!isset($atts['thegem_slider_params']) || !isset($GLOBALS['thegem_slider_params'])) {
		return;
	}
	unset($GLOBALS['thegem_slider_params']);

	?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
add_action('woocommerce_shortcode_after_product_cat_loop', 'thegem_product_category_slider_after_loop');
add_action('woocommerce_shortcode_after_product_category_loop', 'thegem_product_category_slider_after_loop');

function thegem_tag_cloud_args($args){
	$args['smallest'] = 12;
	$args['largest'] = 30;
	$args['unit'] = 'px';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'thegem_tag_cloud_args');
add_filter( 'woocommerce_product_tag_cloud_widget_args', 'thegem_tag_cloud_args');

function thegem_yith_frontend_action_list($actions) {
	$actions[] = 'extended_products_grid_load_more';
	return $actions;
}
add_filter('yith_ywraq_frontend_action_list', 'thegem_yith_frontend_action_list');
add_filter('yith_woocompare_actions_to_check_frontend', 'thegem_yith_frontend_action_list');

if (!function_exists('get_thegem_select_post_types')) {
	function get_thegem_select_post_types() {
		$out = ['page' => __('Pages', 'thegem')];
		$post_types = get_post_types(array(
			'public' => true,
			'_builtin' => false
		), 'objects');

		if (empty($post_types) || is_wp_error($post_types)) {
			return $out;
		}

		foreach ($post_types as $post_type) {
			if (!empty($post_type->name) && !in_array($post_type->name, ['elementor_library', 'thegem_title', 'thegem_footer', 'thegem_templates'])) {
				$out[$post_type->name] = $post_type->label;
			}
		}

		$out['thegem_team_person'] = __('Team Persons', 'thegem');
		$out['thegem_testimonial'] = __('Testimonials', 'thegem');

		return $out;
	}
}

if (!function_exists('get_thegem_select_post_type_taxonomies')) {
	function get_thegem_select_post_type_taxonomies($post_type = false, $add_authors = false) {
		$out = [];
		if ($post_type && $post_type != 'any')  {
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		} else {
			$taxonomies = get_taxonomies(array(
				'public' => true,
			), 'objects');
			$out = ['thegem_portfolios' => __('Portfolio Categories', 'thegem')];
			if ($add_authors) {
				$out['authors'] = __('Authors', 'thegem');
			}
		}


		if (empty($taxonomies) || is_wp_error($taxonomies)) {
			return $out;
		}

		foreach ($taxonomies as $taxonomy) {
			if (!empty($taxonomy->name) && !in_array($taxonomy->name, ['product_shipping_class'])) {
				$out[$taxonomy->name] = $taxonomy->label;
			}
		}

		$out['thegem_teams'] = __('Teams', 'thegem');
		$out['thegem_testimonials_sets'] = __(' Testimonials Sets', 'thegem');

		return $out;
	}
}

if (!function_exists('get_thegem_select_taxonomy_terms')) {
	function get_thegem_select_taxonomy_terms($taxonomy, $show_all = false) {
		$out = $show_all ? ['0' => __('All', 'thegem')] : [];

		$terms = get_terms([
			'taxonomy' => $taxonomy,
			'hide_empty' => true,
		]);

		if (empty($terms) || is_wp_error($terms)) {
			return $out;
		}

		foreach ($terms as $term) {
			if (!empty($term->name)) {
				$out[$term->slug] = $term->name;
			}
		}

		return $out;
	}
}

if (!function_exists('get_thegem_select_blog_authors')) {
	function get_thegem_select_blog_authors() {
		$out = [];
		$authors = get_users(array('has_published_posts' => array('post')));

		if (empty($authors) || is_wp_error($authors)) {
			return $out;
		}

		foreach ($authors as $author) {
			if (!empty($author->data->display_name)) {
				$out[$author->ID] = $author->data->display_name;
			}
		}

		return $out;
	}
}

function thegem_posts_query_section($control) {

	$control->start_controls_section(
		'section_blog',
		[
			'label' => __('Query', 'thegem'),
		]
	);

	if ($control->is_single_post_template) {
		$query_type_default = 'related';
	} else if ($control->is_blog_archive_template) {
		$query_type_default = 'archive';
	} else {
		$query_type_default = 'post';
	}

	$control->add_control(
		'query_type',
		[
			'label' => __('Show', 'thegem'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'label_block' => true,
			'options' => array_merge(
				[
					'post' => __('Posts (Blog)', 'thegem'),
				],
				get_thegem_select_post_types(),
				[
					'related' => __('Same Taxonomy Items (Related)', 'thegem'),
					'archive' => __('Posts Archive', 'thegem'),
					'manual' => __('Manual Selection', 'thegem'),
				]
			),
			'default' => $query_type_default,
			'frontend_available' => true,
		]
	);

	foreach (get_thegem_select_post_types() as $post_type_name => $post_type_label) {

		$options = get_thegem_select_post_type_taxonomies($post_type_name);

		$control->add_control(
			'source_post_type_' . $post_type_name,
			[
				'label' => __('Source', 'thegem'),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => array_merge(
					[
						'all' => __('All', 'thegem'),
					],
					$options,
					[
						'manual' => __('Manual Selection', 'thegem'),
					]
				),
				'default' => ['all'],
				'frontend_available' => true,
				'condition' => [
					'query_type' => $post_type_name,
				],
			]
		);

		foreach ($options as $tax_name => $tax_label) {

			$control->add_control(
				'source_post_type_' . $post_type_name . '_tax_' . $tax_name,
				[
					'label' => sprintf(__('Select %s Terms', 'thegem'), $tax_label),
					'type' => \Elementor\Controls_Manager::SELECT2,
					'multiple' => true,
					'options' => get_thegem_select_taxonomy_terms($tax_name),
					'frontend_available' => true,
					'label_block' => true,
					'condition' => [
						'query_type' => $post_type_name,
						'source_post_type_' . $post_type_name => $tax_name,
					],
				]
			);
		}

		$control->add_control(
			'source_post_type_' . $post_type_name . '_manual',
			[
				'label' => sprintf(__('Select %s', 'thegem'), $post_type_label),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => $post_type_name,
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'condition' => [
					'query_type' => $post_type_name,
					'source_post_type_' . $post_type_name => 'manual',
				],
			]
		);

		$control->add_control(
			'source_post_type_' . $post_type_name . '_exclude_type',
			[
				'label' => __('Exclude', 'thegem'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'manual' => __('Manual Selection', 'thegem'),
					'current' => __('Current Post', 'thegem'),
					'term' => __('Term', 'thegem'),
				],
				'default' => 'manual',
				'frontend_available' => true,
				'condition' => [
					'query_type' => $post_type_name,
				],
			]
		);

		$control->add_control(
			'source_post_type_' . $post_type_name . '_exclude',
			[
				'label' => sprintf(__('Exclude %s', 'thegem'), $post_type_label),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => $post_type_name,
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'description' => __('Add post by title.', 'thegem'),
				'condition' => [
					'query_type' => $post_type_name,
					'source_post_type_' . $post_type_name . '_exclude_type' => 'manual',
				],
			]
		);

		$control->add_control(
			'source_post_type_' . $post_type_name . '_exclude_terms',
			[
				'label' => sprintf(__('Exclude %s Terms', 'thegem'), $post_type_label),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_taxonomy_terms_by_query',
				'render' => 'thegem_get_taxonomy_terms_by_id',
				'post_type' => $post_type_name,
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'description' => __('Add term by name.', 'thegem'),
				'condition' => [
					'query_type' => $post_type_name,
					'source_post_type_' . $post_type_name . '_exclude_type' => 'term',
				],
			]
		);

	}

	$control->add_control(
		'source',
		[
			'label' => __('Source', 'thegem'),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'label_block' => true,
			'multiple' => true,
			'options' => [
				'categories' => __('Categories', 'thegem'),
				'tags' => __('Tags', 'thegem'),
				'authors' => __('Authors', 'thegem'),
				'posts' => __('Posts', 'thegem'),
			],
			'default' => ['categories'],
			'frontend_available' => true,
			'condition' => [
				'query_type' => 'post',
			],
		]
	);

	$control->add_control(
		'categories',
		[
			'label' => __('Select Blog Categories', 'thegem'),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
			'options' => get_thegem_select_taxonomy_terms('category', true),
			'frontend_available' => true,
			'label_block' => true,
			'condition' => [
				'query_type' => 'post',
				'source' => 'categories',
			],
		]
	);

	$control->add_control(
		'select_blog_tags',
		[
			'label' => __( 'Select Blog Tags', 'thegem' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
			'options' => get_thegem_select_taxonomy_terms('post_tag', true),
			'frontend_available' => true,
			'label_block' => true,
			'condition' => [
				'query_type' => 'post',
				'source' => 'tags',
			],
		]
	);

	$control->add_control(
		'select_blog_authors',
		[
			'label' => __( 'Select Blog Authors', 'thegem' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
			'options' => get_thegem_select_blog_authors(),
			'frontend_available' => true,
			'label_block' => true,
			'condition' => [
				'query_type' => 'post',
				'source' => 'authors',
			],
		]
	);

	$control->add_control(
		'select_blog_posts',
		[
			'label' => __('Select Blog Posts', 'thegem'),
			'type' => 'gem-query-control',
			'search' => 'thegem_get_posts_by_query',
			'render' => 'thegem_get_posts_title_by_id',
			'post_type' => 'post',
			'label_block' => true,
			'multiple' => true,
			'frontend_available' => true,
			'condition' => [
				'query_type' => 'post',
				'source' => 'posts',
			],
		]
	);

	$control->add_control(
		'exclude_blog_posts_type',
		[
			'label' => __('Exclude', 'thegem'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'label_block' => true,
			'multiple' => true,
			'options' => [
				'manual' => __('Manual Selection', 'thegem'),
				'current' => __('Current Post', 'thegem'),
				'term' => __('Term', 'thegem'),
			],
			'default' => 'manual',
			'frontend_available' => true,
			'condition' => [
				'query_type' => 'post',
			],
		]
	);

	$control->add_control(
		'exclude_blog_posts',
		[
			'label' => __('Exclude Blog Posts', 'thegem'),
			'type' => 'gem-query-control',
			'search' => 'thegem_get_posts_by_query',
			'render' => 'thegem_get_posts_title_by_id',
			'post_type' => 'post',
			'label_block' => true,
			'multiple' => true,
			'frontend_available' => true,
			'description' => __('Add post by title.', 'thegem'),
			'condition' => [
				'query_type' => 'post',
				'exclude_blog_posts_type' => 'manual',
			],
		]
	);

	$control->add_control(
		'exclude_blog_posts_terms',
		[
			'label' => __('Exclude Posts Terms', 'thegem'),
			'type' => 'gem-query-control',
			'search' => 'thegem_get_taxonomy_terms_by_query',
			'render' => 'thegem_get_taxonomy_terms_by_id',
			'post_type' => 'post',
			'label_block' => true,
			'multiple' => true,
			'frontend_available' => true,
			'description' => __('Add term by name.', 'thegem'),
			'condition' => [
				'query_type' => 'post',
				'exclude_blog_posts_type' => 'term',
			],
		]
	);

	if ($control->is_single_post_template) {
		$taxonomy_related_default = ['category'];
	} else {
		$taxonomy_related_default = [];
	}

	$control->add_control(
		'taxonomy_related',
		[
			'label' => __('Related by', 'thegem'),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'label_block' => true,
			'multiple' => true,
			'options' => get_thegem_select_post_type_taxonomies(false, true),
			'default' => $taxonomy_related_default,
			'frontend_available' => true,
			'condition' => [
				'query_type' => 'related',
			],
		]
	);

	$control->add_control(
		'taxonomy_related_post_type',
		[
			'label' => __('Related by Post Type', 'thegem'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'label_block' => true,
			'options' => array_merge(
				[
					'any' => __('Any', 'thegem'),
					'post' => __('Posts (Blog)', 'thegem'),
				],
				get_thegem_select_post_types()
			),
			'default' => 'any',
			'frontend_available' => true,
			'condition' => [
				'query_type' => 'related',
			],
		]
	);

	$control->add_control(
		'select_posts_manual',
		[
			'label' => __('Select Posts', 'thegem'),
			'type' => 'gem-query-control',
			'search' => 'thegem_get_posts_by_query',
			'render' => 'thegem_get_posts_title_by_id',
			'post_type' => 'any',
			'label_block' => true,
			'multiple' => true,
			'frontend_available' => true,
			'condition' => [
				'query_type' => 'manual',
			],
		]
	);

	if ($control->is_single_post_template) {
		$exclude_posts_default = 'current';
	} else {
		$exclude_posts_default = 'manual';
	}

	$control->add_control(
		'exclude_posts',
		[
			'label' => __('Exclude', 'thegem'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'label_block' => true,
			'multiple' => true,
			'options' => [
				'manual' => __('Manual Selection', 'thegem'),
				'current' => __('Current Post', 'thegem'),
				'term' => __('Term', 'thegem'),
			],
			'default' => $exclude_posts_default,
			'frontend_available' => true,
			'condition' => [
				'query_type' => ['manual', 'related', 'archive'],
			],
		]
	);

	$control->add_control(
		'exclude_posts_manual',
		[
			'label' => __('Exclude Posts', 'thegem'),
			'type' => 'gem-query-control',
			'search' => 'thegem_get_posts_by_query',
			'render' => 'thegem_get_posts_title_by_id',
			'post_type' => 'any',
			'label_block' => true,
			'multiple' => true,
			'frontend_available' => true,
			'condition' => [
				'exclude_posts' => 'manual',
				'query_type' => ['manual', 'related', 'archive'],
			],
		]
	);

	$control->add_control(
		'exclude_posts_manual_terms',
		[
			'label' => __('Exclude Posts Terms', 'thegem'),
			'type' => 'gem-query-control',
			'search' => 'thegem_get_taxonomy_terms_by_query',
			'render' => 'thegem_get_taxonomy_terms_by_id',
			'post_type' => 'any',
			'label_block' => true,
			'multiple' => true,
			'frontend_available' => true,
			'description' => __('Add term by name.', 'thegem'),
			'condition' => [
				'exclude_posts' => 'term',
				'query_type' => ['manual', 'related', 'archive'],
			],
		]
	);

	$control->add_control(
		'order_by',
		[
			'label' => __('Order By', 'thegem'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'label_block' => true,
			'options' => [
				'default' => __('Default', 'thegem'),
				'date' => __('Date', 'thegem'),
				'id' => __('ID', 'thegem'),
				'author' => __('Author', 'thegem'),
				'title' => __('Title', 'thegem'),
				'modified' => __('Last modified date', 'thegem'),
				'comment_count' => __('Number of comments', 'thegem'),
				'rand' => __('Random', 'thegem'),
			],
			'default' => 'default',
			'frontend_available' => true,
		]
	);

	$control->add_control(
		'order',
		[
			'label' => __('Sort Order', 'thegem'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'label_block' => true,
			'options' => [
				'default' => __('Default', 'thegem'),
				'desc' => __('Descending', 'thegem'),
				'asc' => __('Ascending', 'thegem'),
			],
			'default' => 'default',
			'frontend_available' => true,
		]
	);

	$control->add_control(
		'offset',
		[
			'label' => __('Offset', 'thegem'),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'min' => 1,
			'max' => 100,
			'step' => 1,
			'frontend_available' => true,
			'description' => __('Number of items to displace or pass over', 'thegem'),
		]
	);

	$control->end_controls_section();
}

function thegem_posts_query_section_render($settings, $featured = false) {
	$post_type = $settings['query_type'];
	$taxonomy_filter = $manual_selection = $blog_authors = $date_query = [];
	$single_post_id = thegem_templates_init_post() ? thegem_templates_init_post()->ID : get_the_ID();

	if ($settings['query_type'] == 'post') {

		if ($featured) {
			if ($settings['source'] == 'categories' && !empty($settings['select_blog_cat']) && !in_array('0', $settings['select_blog_cat'])) {
				$taxonomy_filter['category'] = $terms = $settings['select_blog_cat'];
			} else if ($settings['source'] == 'tags' && !empty($settings['select_blog_tags'])) {
				$taxonomy_filter['post_tag'] = $settings['select_blog_tags'];
			} else if ($settings['source'] == 'posts' && !empty($settings['select_blog_posts'])) {
				$manual_selection = $settings['select_blog_posts'];
			} else if ($settings['source'] == 'authors' && !empty($settings['select_blog_authors'])) {
				$blog_authors = $settings['select_blog_authors'];
			} else if ($settings['source'] == 'featured' && !empty($settings['categories'])) {
				$taxonomy_filter['category'] = $terms = $settings['categories'];
			}
		} else {
			if (!is_array($settings['source'])) {
				$settings['source'] = array($settings['source']);
			}

			foreach ($settings['source'] as $source) {
				if ($source == 'categories' && !empty($settings['categories']) && !in_array('0', $settings['categories'])) {
					$taxonomy_filter['category'] = $settings['categories'];
				} else if ($source == 'tags' && !empty($settings['select_blog_tags']) && !in_array('0', $settings['select_blog_tags'])) {
					$taxonomy_filter['post_tag'] = $settings['select_blog_tags'];
				} else if ($source == 'posts' && !empty($settings['select_blog_posts']) && !in_array('0', $settings['select_blog_posts'])) {
					$manual_selection = $settings['select_blog_posts'];
				} else if ($source == 'authors' && !empty($settings['select_blog_authors']) && !in_array('0', $settings['select_blog_authors'])) {
					$blog_authors = $settings['select_blog_authors'];
				}
			}
		}

		if ($settings['exclude_blog_posts_type'] == 'current') {
			$settings['exclude_blog_posts'] = [$single_post_id];
		} else if ($settings['exclude_blog_posts_type'] == 'term' && !empty($settings['exclude_blog_posts_terms'])) {
			$settings['exclude_blog_posts'] = thegem_get_posts_query_section_exclude_ids($settings['exclude_blog_posts_terms'], $post_type);
		}
		$exclude = $settings['exclude_blog_posts'];

	} else if ($settings['query_type'] == 'related' || $settings['query_type'] == 'archive' || $settings['query_type'] == 'manual') {

		if ($settings['query_type'] == 'related') {
			$post_type = isset($settings['taxonomy_related_post_type']) ? $settings['taxonomy_related_post_type'] : 'any';
			$taxonomies = $settings['taxonomy_related'];
			if (!empty($taxonomies)) {
				foreach ($taxonomies as $tax) {
					if ($tax == 'authors') {
						$blog_authors = $settings['select_blog_authors'] = array(get_the_author_meta('ID'));
					} else {
						$tax_terms = get_the_terms($single_post_id, $tax);
						if (!empty($tax_terms) && !is_wp_error($tax_terms)) {
							$taxonomy_filter[$tax] = [];
							foreach ($tax_terms as $term) {
								$taxonomy_filter[$tax][] = $term->slug;
							}
						}
					}
				}
			}
			$settings['related_tax_filter'] = $taxonomy_filter;
		} else if ($settings['query_type'] == 'archive') {
			$post_type = $settings['archive_post_type'] = get_post_type() == 'thegem_templates' ? 'post' : get_post_type();

			if (get_post_type() == 'thegem_templates') {
				$post_id = get_the_ID();
				$editor_post_id = wp_get_post_autosave($post_id) ? wp_get_post_autosave($post_id)->ID : $post_id;
				$elementor_page_settings = get_post_meta($editor_post_id, '_elementor_page_settings', true);
				if (!empty($elementor_page_settings)) {
					$elementor_preview_tax = empty($elementor_page_settings['thegem_preview_type']) ? 'category' : $elementor_page_settings['thegem_preview_type'];
					if (!empty($elementor_page_settings['thegem_preview_term_' . $elementor_preview_tax])) {
						$elementor_preview_term_id = $elementor_page_settings['thegem_preview_term_' . $elementor_preview_tax];
						$elementor_preview_term = get_term($elementor_preview_term_id);
						if (!empty($elementor_preview_term) && !is_wp_error($elementor_preview_term)) {
							$obj = $elementor_preview_term;
							$taxonomy_filter[$elementor_preview_tax] = array($obj->slug);
							$elementor_preview_taxonomy = get_taxonomy($elementor_preview_tax);
							$post_type = !empty($elementor_preview_taxonomy->object_type) ? $elementor_preview_taxonomy->object_type[0] : $post_type;
						}
					}
				}
			}

			if (is_author()) {
				$blog_authors = $settings['select_blog_authors'] = array(get_queried_object()->ID);
			} else if (is_category() || is_tag() || is_tax()) {
				$taxonomy_filter[get_queried_object()->taxonomy] = array(get_queried_object()->slug);
				$settings['archive_tax_filter'] = $taxonomy_filter;
			} else if (is_date()) {
				if (!empty(get_query_var('year'))) {
					$date_query['year'] = get_query_var('year');
				}
				if (!empty(get_query_var('monthnum'))) {
					$date_query['month'] = get_query_var('monthnum');
				}
				if (!empty(get_query_var('day'))) {
					$date_query['day'] = get_query_var('day');
				}
				$settings['date_query'] = $date_query;
			}
		} else if ($settings['query_type'] == 'manual') {
			$post_type = 'any';
			$manual_selection = $settings['select_posts_manual'];
		}

		if ($settings['exclude_posts'] == 'current') {
			$settings['exclude_posts_manual'] = [$single_post_id];
		} else if ($settings['exclude_posts'] == 'term' && !empty($settings['exclude_posts_manual_terms'])) {
			$settings['exclude_posts_manual'] = thegem_get_posts_query_section_exclude_ids($settings['exclude_posts_manual_terms'], $post_type);
		}

		$exclude = $settings['exclude_posts_manual'];
		$settings['blog_show_filter'] = '';

	} else {

		foreach ($settings['source_post_type_' . $post_type] as $source) {
			if ($source == 'all') {

			} else if ($source == 'manual') {
				$manual_selection = $settings['source_post_type_' . $post_type . '_manual'];
			} else {
				$tax_terms = $settings['source_post_type_' . $post_type . '_tax_' . $source];
				if (!empty($tax_terms)) {
					$taxonomy_filter[$source] = $tax_terms;
				}
			}
		}

		if ($settings['source_post_type_' . $post_type . '_exclude_type'] == 'current') {
			$settings['source_post_type_' . $post_type . '_exclude'] = [$single_post_id];
		} else if ($settings['source_post_type_' . $post_type . '_exclude_type'] == 'term' && !empty($settings['source_post_type_' . $post_type . '_exclude_terms'])) {
			$settings['source_post_type_' . $post_type . '_exclude'] = thegem_get_posts_query_section_exclude_ids($settings['source_post_type_' . $post_type . '_exclude_terms'], $post_type);
		}

		$exclude = $settings['source_post_type_' . $post_type . '_exclude'];

	}

	return [
		"settings" => $settings,
		"post_type" => $post_type,
		"taxonomy_filter" => $taxonomy_filter,
		"manual_selection" => $manual_selection,
		"blog_authors" => $blog_authors,
		"date_query" => $date_query,
		"exclude" => $exclude,
	];
}

function thegem_get_posts_query_section_exclude_ids($terms, $post_type) {
	$exclude_ids = [];
	foreach ($terms as $id) {
		$arr = explode("|", $id);
		$term = get_term_by('id', $arr[1], $arr[0]);

		if(empty($term)) continue ;

		$args = array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'tax_query' => array(
				array(
					'taxonomy' => $term->taxonomy,
					'field' => 'term_id',
					'terms' => $term->term_id,
				),
			),
		);
		$wp_query_result = new WP_Query($args);
		$post_ids = !empty($wp_query_result->posts) ? $wp_query_result->posts : [];
		$exclude_ids = array_unique(array_merge($exclude_ids, $post_ids));
	}

	return $exclude_ids;
}

if (!function_exists('thegem_get_acf_plugin_fields_by_group')) {
	function thegem_get_acf_plugin_fields_by_group($gr) {
		if (!class_exists( 'ACF' )) return array();

		$fields = array();
		foreach ( acf_get_fields($gr) as $field ) {
			if ( !empty($field) ) {
				$fields[$field['name']] = $field['label'];
			}
		}

		return $fields;
	}
}

if (!function_exists('thegem_get_acf_plugin_groups')) {
	function thegem_get_acf_plugin_groups($post_type = 'any') {
		if (!class_exists( 'ACF' )) return array();

		$groups = array();
		if ($post_type == 'any') {
			$field_groups = acf_get_field_groups();
		} else {
			$field_groups = acf_get_field_groups(array('post_type' => $post_type));
		}

		foreach ($field_groups as $group) {
			if (!empty($group)) {
				$groups[$group['key']] = $group['title'];
			}
		}

		return $groups;
	}
}

if (!function_exists('thegem_select_portfolio_details')) {
	function thegem_select_portfolio_details() {
		$out = [];
		$details = thegem_get_option('portfolio_project_details');

		if (empty($details)) {
			return $out;
		}

		$details_data = json_decode(thegem_get_option('portfolio_project_details_data'), true);
		if (!empty($details_data)) {
			foreach ($details_data as $v) {
				$out['_thegem_cf_' . str_replace('-', '_', sanitize_title($v['title']))] = $v['title'];
			}
		}

		return $out;
	}
}

if (!function_exists('thegem_select_theme_options_custom_fields')) {
	function thegem_select_theme_options_custom_fields($post_type) {
		$out = [];

		switch ($post_type) {
			case 'page':
				$post_type = 'default';
				break;
			case 'thegem_pf_item':
				$post_type = 'portfolio';
				break;
			case 'thegem_team_person':
				$post_type = 'team_persons';
				break;
			case 'thegem_testimonial':
				$post_type = 'testimonials';
				break;
		}

		if (!function_exists('thegem_theme_options_get_page_settings')) return $out;

		$pt_data = thegem_theme_options_get_page_settings($post_type);
		$custom_fields = !empty($pt_data['custom_fields']) ? $pt_data['custom_fields'] : null;

		if($post_type === 'team_persons') {
			$out = array_merge(array(
				'thegem_team_person_name' => __('Name (Team Person)', 'thegem'),
				'thegem_team_person_position' => __('Position (Team Person)', 'thegem'),
				'thegem_team_person_phone' => __('Phone (Team Person)', 'thegem'),
				'thegem_team_person_email' => __('Email (Team Person)', 'thegem'),
				'thegem_team_person_link' => __('Link (Team Person)', 'thegem'),
				'thegem_team_person_social_link_facebook' => __('Facebook Link (Team Person)', 'thegem'),
				'thegem_team_person_social_link_twitter' => __('Twitter (X) Link (Team Person)', 'thegem'),
				'thegem_team_personsocial_link_linkedin' => __('LinkedIn Link (Team Person)', 'thegem'),
				'thegem_team_person_social_link_instagram' => __('Instagram Link (Team Person)', 'thegem'),
				'thegem_team_person_social_link_skype' => __('Skype Link (Team Person)', 'thegem'),
			), $out);
		}

		if($post_type === 'testimonials') {
			$out = array_merge(array(
				'thegem_testimonial_name' => __('Name (Testimonial)', 'thegem'),
				'thegem_testimonial_company' => __('Company (Testimonial)', 'thegem'),
				'thegem_testimonial_position' => __('Position (Testimonial)', 'thegem'),
				'thegem_testimonial_link' => __('Link (Testimonial)', 'thegem'),
			), $out);
		}

		if (empty($custom_fields)) return $out;

		$custom_fields_data = !empty($pt_data['custom_fields_data']) ? json_decode($pt_data['custom_fields_data'], true) : null;
		if (!empty($custom_fields_data)) {
			foreach ($custom_fields_data as $field) {
				$out[$field['key']] = $field['title'];
			}
		}

		return $out;
	}
}

if (!function_exists('thegem_select_theme_options_custom_fields_all')) {
	function thegem_select_theme_options_custom_fields_all() {
		$out = [];
		$post_types = get_post_types(array(
			'public' => true,
		), 'names');

		$post_types[] = 'thegem_team_person';
		$post_types[] = 'thegem_testimonial';

		foreach ($post_types as $post_type) {
			$out = array_merge($out, thegem_select_theme_options_custom_fields($post_type));
		}

		return $out;
	}
}

// Thegem CF Shorthand
if (!function_exists('thegem_cf_get_edit_template_type')) {
	function thegem_cf_get_edit_template_type()
	{
		$type = get_post_type(get_the_ID());

		if (thegem_get_template_type(get_the_ID()) === 'single-post') {
			$type = 'post';
		}

		if (thegem_get_template_type(get_the_ID()) === 'portfolio') {
			$type = 'thegem_pf_item';
		}

		if (thegem_get_template_type(get_the_ID()) === 'single-product') {
			$type = 'product';
		}

		return $type;
	}
}

if (!function_exists('thegem_cf_get_edit_template_post_id')) {
	function thegem_cf_get_edit_template_post_id()
	{
		$post_id = get_the_ID();

		switch (thegem_cf_get_edit_template_type()) {
			case 'post':
				$single_post = thegem_templates_init_post();
				$post_id = !empty($single_post->ID) ? $single_post->ID : $post_id;
				wp_reset_postdata();
				break;
			case 'thegem_pf_item':
				$portfolio = thegem_templates_init_portfolio();
				$post_id = !empty($portfolio->ID) ? $portfolio->ID : $post_id;
				wp_reset_postdata();
				break;
			case 'product':
				$product = thegem_templates_init_product();
				$post_id = (!empty($product) && !empty($product->get_id())) ? $product->get_id() : $post_id;
				wp_reset_postdata();
				break;
		}

		return $post_id;
	}
}

if (!function_exists('tg_meta')) {
	add_shortcode('tg_meta', function ($atts) {
		extract(shortcode_atts(array(
			'key' => '',
		), $atts, 'tg_meta'));

		$post_id = thegem_cf_get_edit_template_post_id();
		wp_reset_postdata();

		$return_html = isset($key) && !empty($key) ? get_post_meta($post_id, $key, true) : '';
		return $return_html;
	});
}

if (!function_exists('get_thegem_extended_blog_posts')) {
	function get_thegem_extended_blog_posts($post_type, $taxonomy_filter, $meta_filter, $manual_selection, $exclude, $authors, $page = 1, $ppp = -1, $orderby = '', $order = '', $offset = false, $ignore_sticky_posts = false, $search = null, $search_by = 'content', $date_query = '', $show_all = false, $sale_only = false, $stock_only = false) {

		$args = array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'posts_per_page' => $ppp,
		);

		if ($orderby == 'default') {
			$args['orderby'] = 'menu_order date';
		} else if ($orderby == 'popularity') {
			$args['orderby'] = array('meta_value_num' => 'DESC', 'ID' => 'DESC');
			$args['meta_key'] = 'total_sales';
		} else if ($orderby == 'price') {
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = '_price';
		} else if ($orderby == 'rating') {
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = '_wc_average_rating';
		} else if (!empty($orderby)) {
			$args['orderby'] = $orderby;
			if (!in_array($orderby, ['date', 'id', 'author', 'title', 'name', 'modified', 'comment_count', 'rand', 'menu_order date'])) {
				if (strpos($orderby, 'num_') === 0) {
					$args['orderby'] = 'meta_value_num';
					$args['meta_key'] = str_replace('num_', '', $orderby);
				} else {
					$args['orderby'] = 'meta_value';
					$args['meta_key'] = $orderby;
				}
			}
		}

		if (!empty($order) && $orderby !== 'default') {
			$args['order'] = $order;
		}

		if (!empty($date_query)) {
			$args['date_query'] = array($date_query);
		}

		$tax_query = $meta_query = [];

		if (!empty($taxonomy_filter)) {
			foreach ($taxonomy_filter as $tax => $tax_arr) {
				if (!empty($tax_arr) && !in_array('0', $tax_arr)) {
					$query_arr = array(
						'taxonomy' => $tax,
						'field' => 'slug',
						'terms' => $tax_arr,
					);
				} else {
					$query_arr = array(
						'taxonomy' => $tax,
						'operator' => 'EXISTS'
					);
				}
				$tax_query[] = $query_arr;
			}
		}

		if (!empty($meta_filter)) {
			foreach ($meta_filter as $meta => $meta_arr) {
				if (!empty($meta_arr)) {
					if (strpos($meta, "__range") > 0) {
						$query_arr = array(
							'key' => str_replace("__range","", $meta),
							'value' => $meta_arr,
							'compare'   => 'BETWEEN',
							'type'   => 'NUMERIC',
						);
					} else if (strpos($meta, "__check") > 0) {
						$check_meta_query = array(
							'relation' => 'OR',
						);
						foreach ($meta_arr as $value) {
							$check_meta_query[] = array(
								'key' => str_replace("__check","", $meta),
								'value' => sprintf('"%s"', $value),
								'compare' => 'LIKE',
							);
						}
						$query_arr = $check_meta_query;
					} else {
						$query_arr = array(
							'key' => $meta,
							'value' => $meta_arr,
							'compare' => 'IN',
						);
					}
					$meta_query[] = $query_arr;
				}
			}
		}

		if (!empty($search) && $search_by != 'content') {
			$search_meta_query = array(
				'relation' => 'OR',
			);
			foreach ($search_by as $key) {
				$search_meta_query[] = array(
					'key' => $key,
					'value' => $search,
					'compare' => 'LIKE'
				);
			}
			$meta_query[] = $search_meta_query;
		}

		if ($stock_only) {
			$tax_query[] = array(
				'taxonomy' => 'product_visibility',
				'field' => 'name',
				'terms' => array('outofstock'),
				'operator' => 'NOT IN'
			);
		}

		if (!empty($tax_query)) {
			$args['tax_query'] = $tax_query;
		}

		if (!empty($meta_query)) {
			$args['meta_query'] = $meta_query;
		}

		if ($sale_only) {
			$args['post__in'] = array_merge(array(0), wc_get_product_ids_on_sale());
		}

		if (!empty($manual_selection)) {
			if ($sale_only) {
				$args['post__in'] = array_intersect($args['post__in'], $manual_selection);
			} else {
				$args['post__in'] = $manual_selection;
			}
		}

		if (!empty($exclude)) {
			$args['post__not_in'] = $exclude;
		}

		if (!empty($authors)) {
			$args['author__in'] = $authors;
		}

		if ($ignore_sticky_posts == 'yes') {
			$args['ignore_sticky_posts'] = 1;
		}

		if (!empty($offset) || $show_all) {
			$args['offset'] = $ppp * ($page - 1) + $offset;
		} else {
			$args['paged'] = $page;
		}

		if ($show_all) {
			$args['posts_per_page'] = 999;
		}

		if (!empty($search) && $search_by == 'content') {
			$args['s'] = $search;
		}

		return new WP_Query($args);
	}
}

add_shortcode('gem_current_date', 'thegem_current_date_shortcode');
function thegem_current_date_shortcode($atts) {
	extract(shortcode_atts(array(
		'date' => '',
		'format' => 'd/m/Y',
	), $atts, 'gem_current_date'));
	$date = $date ? strtotime($date) : time();
	return date_i18n($format, $date);
}

function thegem_wcml_multi_currency_ajax_actions( $ajax_actions ) {
	$ajax_actions[] = 'portfolio_grid_load_more';
	$ajax_actions[] = 'thegem_ajax_search_form';
	$ajax_actions[] = 'extended_products_grid_load_more';
	return $ajax_actions;
}
add_filter( 'wcml_multi_currency_ajax_actions', 'thegem_wcml_multi_currency_ajax_actions', 10, 1 );

function thegem_relevanssi_sql_query($sql) {
	global $wpdb;
	if (function_exists('pll_current_language')) {
		$current_language = pll_current_language('slug');
		if ($current_language) {
			$language_filter = "
				AND relevanssi.doc IN (
					SELECT DISTINCT(posts.ID)
					FROM {$wpdb->posts} AS posts
					INNER JOIN {$wpdb->prefix}term_relationships AS tr ON tr.object_id = posts.ID
					INNER JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
					INNER JOIN {$wpdb->prefix}terms AS t ON tt.term_id = t.term_id
					WHERE tt.taxonomy = 'language'
					AND t.slug = '{$current_language}'
				)
			";
			if (strpos($sql, 'WHERE') !== false) {
				$sql = preg_replace('/(WHERE[^ ]*)/', '$0 1=1' . $language_filter . ' AND ', $sql, 1);
			} else {
				$sql .= " WHERE 1=1 " . $language_filter;
			}
		}
	}

	return $sql;
}
add_filter('relevanssi_query_filter', 'thegem_relevanssi_sql_query', 10, 1);