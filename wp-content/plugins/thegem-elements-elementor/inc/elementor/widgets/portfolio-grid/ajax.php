<?php
function portfolio_grid_more_callback() {
	$settings = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : array();
	ob_start();
	$response = array('status' => 'success');

	$portfolios_posts = $portfolios_filters_tax = $portfolios_filters_meta = [];

	if ($settings['query_type'] == 'related') {
		$portfolios_filters_tax = $settings['related_tax_filter'];
	} else {
		foreach ($settings['source'] as $source) {
			if ($source == 'posts') {
				$portfolios_posts = $settings['content_portfolios_posts'];
			} else {
				$tax_terms = $settings['content_portfolios_' . $source];
				if (!empty($tax_terms)) {
					$portfolios_filters_tax[$source] = $tax_terms;
				}
			}
		}
	}

	if (!empty($settings['has_categories_filter']) && !empty($settings['content_portfolios_cat'])) {
		$portfolios_filters_tax['thegem_portfolios'] = $settings['content_portfolios_cat'];
	}

	if (!empty($settings['has_attributes_filter'])) {
		$attrs = explode(",", $settings['filters_attr']);
		foreach ($attrs as $attr) {
			$values = json_decode($settings['filters_attr_val_' . $attr]);
			if (!empty($values)) {
				if (strpos($attr, "tax_") === 0) {
					$portfolios_filters_tax[str_replace("tax_","", $attr)] = $values;
				} else {
					$portfolios_filters_meta[str_replace("meta_","", $attr)] = $values;
				}
			}
		}
	}

	$search = isset($settings['portfolio_search_filter']) && $settings['portfolio_search_filter'] != '' ? $settings['portfolio_search_filter'] : null;

	$page = isset($settings['more_page']) ? intval($settings['more_page']) : 1;
	if ($page == 0)
		$page = 1;

	$show_all = $settings['load_more_show_all'] == 'yes' && $page != 1;
	$items_per_page = $settings['items_per_page'] ? intval($settings['items_per_page']) : 8;

	$portfolio_loop = thegem_get_portfolio_posts($portfolios_posts, $portfolios_filters_tax, $portfolios_filters_meta, $search, $settings['search_by'], $page, $items_per_page, $settings['orderby'], $settings['order'], intval($settings['offset']), $settings['exclude_portfolios'], $show_all);

	$max_page = ceil(($portfolio_loop->found_posts - intval($settings['offset'])) / $items_per_page);
	$next_page = $max_page > $page ? $page + 1 : 0;
	if ($show_all) {
		$next_page = 0;
	}

	if ($portfolio_loop->have_posts()) {

		$item_classes = get_thegem_portfolio_render_item_classes($settings);
		$thegem_sizes = get_thegem_portfolio_render_item_image_sizes($settings); ?>

		<div data-page="<?php echo esc_attr($page); ?>" data-next-page="<?php echo esc_attr($next_page); ?>" data-pages-count="<?php echo esc_attr($portfolio_loop->max_num_pages); ?>">
			<?php
			if ($settings['layout'] == 'creative') {
				$creative_blog_schemes_list = [
					'6' => [
						'6a' => [
							'count' => 9,
							0 => 'squared',
						],
						'6b' => [
							'count' => 7,
							0 => 'squared',
							1 => 'horizontal',
							6 => 'horizontal',
						],
						'6c' => [
							'count' => 9,
							0 => 'horizontal',
							3 => 'horizontal',
							6 => 'horizontal',
						],
						'6d' => [
							'count' => 9,
							0 => 'horizontal',
							1 => 'horizontal',
							2 => 'horizontal',
						],
						'6e' => [
							'count' => 6,
							0 => 'squared',
							1 => 'squared',
						]
					],
					'5' => [
						'5a' => [
							'count' => 7,
							0 => 'squared',
						],
						'5b' => [
							'count' => 8,
							0 => 'horizontal',
							4 => 'horizontal',
						],
						'5c' => [
							'count' => 6,
							0 => 'horizontal',
							1 => 'horizontal',
							4 => 'horizontal',
							5 => 'horizontal',
						],
						'5d' => [
							'count' => 4,
							0 => 'squared',
							1 => 'vertical',
							2 => 'horizontal',
							3 => 'horizontal',
						]
					],
					'4' => [
						'4a' => [
							'count' => 5,
							0 => 'squared',
						],
						'4b' => [
							'count' => 4,
							0 => 'squared',
							1 => 'horizontal',
						],
						'4c' => [
							'count' => 4,
							0 => 'squared',
							1 => 'vertical',
						],
						'4d' => [
							'count' => 7,
							0 => 'vertical',
						],
						'4e' => [
							'count' => 4,
							0 => 'vertical',
							1 => 'vertical',
							2 => 'horizontal',
							3 => 'horizontal',
						],
						'4f' => [
							'count' => 6,
							0 => 'horizontal',
							5 => 'horizontal',
						]
					],
					'3' => [
						'3a' => [
							'count' => 4,
							0 => 'vertical',
							1 => 'vertical',
						],
						'3b' => [
							'count' => 4,
							1 => 'horizontal',
							2 => 'horizontal',
						],
						'3c' => [
							'count' => 5,
							0 => 'vertical',
						],
						'3d' => [
							'count' => 5,
							0 => 'horizontal',
						],
						'3e' => [
							'count' => 3,
							0 => 'squared',
						],
						'3f' => [
							'count' => 4,
							0 => 'horizontal',
							1 => 'vertical',
						],
						'3g' => [
							'count' => 4,
							0 => 'vertical',
							3 => 'horizontal',
						],
						'3h' => [
							'count' => 5,
							2 => 'vertical',
						]
					],
					'2' => [
						'2a' => [
							'count' => 5,
							0 => 'vertical',
						],
						'2b' => [
							'count' => 5,
							3 => 'vertical',
						],
						'2c' => [
							'count' => 4,
							0 => 'vertical',
							2 => 'vertical',
						],
						'2d' => [
							'count' => 4,
							0 => 'horizontal',
							1 => 'vertical',
						],
						'2e' => [
							'count' => 5,
							0 => 'horizontal',
						],
						'2f' => [
							'count' => 4,
							0 => 'horizontal',
							1 => 'horizontal',
						],
						'2g' => [
							'count' => 5,
							2 => 'horizontal',
						],
						'2h' => [
							'count' => 4,
							0 => 'horizontal',
							3 => 'horizontal',
						],
					]
				];
				$columns = $settings['columns_desktop'] != '100%' ? str_replace("x", "", $settings['columns_desktop']) : $settings['columns_100'];
				$items_sizes = $creative_blog_schemes_list[$columns][$settings['layout_scheme_' . $columns . 'x']];
				$items_count = $items_sizes['count'];
			}
			$i = 0;
			while ($portfolio_loop->have_posts()) {
				$portfolio_loop->the_post();
				$thegem_highlight_type_creative = null;
				if ($settings['layout'] == 'creative') {
					$thegem_highlight_type_creative = 'disabled';
					$item_num = $i % $items_count;
					if (isset($items_sizes[$item_num])) {
						$thegem_highlight_type_creative = $items_sizes[$item_num];
					}
				}
				echo thegem_portfolio_grid_render_item($settings, $item_classes, $thegem_sizes, get_the_ID(), $thegem_highlight_type_creative);
				if ($settings['layout'] == 'creative' && $i == 0) {
					echo thegem_portfolio_grid_render_item($settings, ['size-item'], $thegem_sizes);
				}
				$i++;
			} ?>
		</div>
	<?php } else { ?>
		<div data-page="1" data-next-page="0" data-pages-count="1">
			<div class="portfolio-item not-found">
				<div class="found-wrap">
					<div class="image-inner empty"></div>
					<div class="msg">
						<?php echo wp_kses($settings['not_found_text'], 'post'); ?>
					</div>
				</div>
			</div>
		</div>
	<?php }

	$response['html'] = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
	$response = json_encode($response);
	header("Content-Type: application/json");
	echo $response;
	exit;
}
add_action('wp_ajax_portfolio_grid_load_more', 'portfolio_grid_more_callback');
add_action('wp_ajax_nopriv_portfolio_grid_load_more', 'portfolio_grid_more_callback');

function thegem_get_portfolio_posts($portfolios_posts, $portfolios_filters_tax, $portfolios_filters_meta, $search = null, $search_by = 'content', $page = 1, $ppp = -1, $orderby = 'menu_order ID', $order = 'ASC', $offset = 0, $exclude = false, $show_all = false) {

	$tax_query = $meta_query = [];

	if (!empty($portfolios_filters_tax)) {
		foreach ($portfolios_filters_tax as $tax => $tax_arr) {
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

	if (!empty($portfolios_filters_meta)) {
		foreach ($portfolios_filters_meta as $meta => $meta_arr) {
			if (!empty($meta_arr)) {
				if (strpos($meta, "__range") > 0) {
					$meta = str_replace("__range","", $meta);
					$query_arr = array(
						'key' => $meta,
						'value' => $meta_arr,
						'compare'   => 'BETWEEN',
						'type'   => 'NUMERIC',
					);
				} else if (strpos($meta, "__check") > 0) {
					$meta = str_replace("__check","", $meta);
					$check_meta_query = array(
						'relation' => 'OR',
					);
					foreach ($meta_arr as $value) {
						$check_meta_query[] = array(
							'key' => $meta,
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

	$args = array(
		'post_type' => 'thegem_pf_item',
		'post_status' => 'publish',
		'posts_per_page' => $ppp,
	);

	if ($orderby == 'default') {
		$args['orderby'] = 'menu_order ID';
	} else if (!empty($orderby)) {
		$args['orderby'] = $orderby;
		if (!in_array($orderby, ['date', 'id', 'author', 'title', 'name', 'modified', 'comment_count', 'rand', 'menu_order ID'])) {
			if (strpos($orderby, 'num_') === 0) {
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = str_replace('num_', '', $orderby);
			} else {
				$args['orderby'] = 'meta_value';
				$args['meta_key'] = $orderby;
			}
		}
	}

	if ($orderby == 'default') {
		$args['order'] = 'ASC';
	} else if (!empty($order)) {
		$args['order'] = $order;
	}

	if (!empty($tax_query)) {
		$args['tax_query'] = $tax_query;
	}

	if (!empty($meta_query)) {
		$args['meta_query'] = $meta_query;
	}

	if (!empty($portfolios_posts)) {
		$args['post__in'] = $portfolios_posts;
	}

	if (!empty($offset) || $show_all) {
		$args['offset'] = $ppp * ($page - 1) + $offset;
	} else {
		$args['paged'] = $page;
	}

	if ($show_all) {
		$args['posts_per_page'] = 999;
	}

	if (!empty($exclude)) {
		$args['post__not_in'] = $exclude;
	}

	if (!empty($search) && $search_by == 'content') {
		$args['s'] = $search;
	}

	$portfolio_loop = new WP_Query($args);

	return $portfolio_loop;
}