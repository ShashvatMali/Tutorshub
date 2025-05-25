<?php

function thegem_products_compact_grid_ajax_load() {
	$response = array();
	$params = isset($_POST['params']) ? $_POST['params'] : array();
	$response = array('status' => 'success');
	ob_start();
	thegem_products_compact_grid_ajax_content($params);
	$response['html'] = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
	$response = json_encode($response);
	header( "Content-Type: application/json" );
	echo $response;
	exit;
}
add_action('wp_ajax_thegem_products_compact_grid_ajax_load', 'thegem_products_compact_grid_ajax_load');
add_action('wp_ajax_nopriv_thegem_products_compact_grid_ajax_load', 'thegem_products_compact_grid_ajax_load');

function thegem_products_compact_grid_ajax_content($params) {
	if (!defined( 'WC_PLUGIN_FILE' )) {
		return '';
	}

	global $post;
	$portfolio_posttemp = $post;
	$grid_uid = $params['block_id'];

	$taxonomy_filter_current = [];
	if (in_array('category', $params['source'])) {
		$taxonomy_filter_current['product_cat'] = $params['content_products_cat'];
	} else {
		$taxonomy_filter_current['product_cat'] = ['0'];
	}

	$attributes = [];
	if (in_array('attribute', $params['source'])) {
		$attrs = $params['content_products_attr'];

		if ($attrs) {
			foreach ($attrs as $attr) {
				$values = $params['content_products_attr_val_' . $attr];
				if (empty($values) || in_array('0', $values)) {
					$values = get_terms('pa_' . $attr, array('fields' => 'slugs'));
				}
				$attributes[$attr] = $values;
			}
		}
	}

	$items_per_page = $params['items_per_page'] ? intval($params['items_per_page']) : 8;

	$page = 1;
	$orderby = $params['orderby'];
	$order = $params['order'];

	$featured_only = $params['featured_only'] == 'yes';
	$sale_only = $params['sale_only'] == 'yes';
	$recently_viewed_only = !empty($params['recently_viewed_only']) && $params['recently_viewed_only'] == 'yes';
	$new_only = !empty($params['new_only']) && $params['new_only'] == 'yes';

	$product_loop = thegem_extended_products_get_posts($page, $items_per_page, $orderby, $order, $featured_only, $sale_only, $stock_only = false, $recently_viewed_only, $new_only, $taxonomy_filter_current, [], $attributes);

	if ($product_loop && $product_loop->have_posts()) :

		$portfolio_classes = array(
			'products-compact-grid woocommerce',
			'layout-' . $params['layout'],
			'columns-' . $params['columns'],
			'alignment-' . $params['caption_alignment'],
			'aspect-ratio-' . $params['image_aspect_ratio'],
			$params['product_separator'] == 'yes' ? 'with-separator' : '',
		);
		?>

		<div id="<?php echo(esc_attr($grid_uid)); ?>"
			 class="<?php echo esc_attr(implode(' ', $portfolio_classes)) ?>">
			<?php
			if ($product_loop->have_posts()) {
				while ($product_loop->have_posts()) : $product_loop->the_post();
					$preset_path = __DIR__ . '/templates/content-product-grid-item-compact.php';

					if (!empty($preset_path) && file_exists($preset_path)) {
						include($preset_path);
					}
				endwhile;
				wp_reset_postdata();
			} else { ?>
				<div class="portfolio-item not-found">
					<div class="found-wrap">
						<div class="image-inner empty"></div>
						<div class="msg">
							<?php echo wp_kses($params['not_found_text'], 'post'); ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

	<?php else: ?>
		<?php if (!$recently_viewed_only) { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo esc_html__('Please select products in "Products" section', 'thegem') ?>
			</div>
		<?php } ?>
	<?php endif;

	$post = $portfolio_posttemp;
}