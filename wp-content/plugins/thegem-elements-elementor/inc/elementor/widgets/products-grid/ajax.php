<?php
function products_grid_more_callback() {
	$settings = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : array();
	ob_start();
	$response = array('status' => 'success');
	$page = isset($settings['more_page']) ? intval($settings['more_page']) : 1;
	if ($page == 0)
		$page = 1;
	$featured_only = $settings['featured_only'] == 'yes' ? true : false;
	$sale_only = $settings['sale_only'] == 'yes' ? true : false;
	$categories = explode(",", $settings['content_products_cat']);
	$items_per_page = $settings['items_per_page'] ? intval($settings['items_per_page']) : 8;
	$products_grid_loop = thegem_get_product_posts($categories, $page, $items_per_page, $settings['orderby'], $settings['order'], $featured_only, $sale_only);
	if ($products_grid_loop->max_num_pages > $page)
		$next_page = $page + 1;
	else
		$next_page = 0;

	$item_classes = get_thegem_portfolio_render_item_classes($settings);
	$thegem_sizes = get_thegem_portfolio_render_item_image_sizes($settings);
	?>

	<div data-page="<?php echo esc_attr($page); ?>" data-next-page="<?php echo esc_attr($next_page); ?>" data-pages-count="<?php echo esc_attr($products_grid_loop->max_num_pages); ?>">
		<?php while ($products_grid_loop->have_posts()) : $products_grid_loop->the_post(); ?>
			<?php echo thegem_products_render_item($settings, $item_classes, $thegem_sizes, get_the_ID()); ?>
		<?php endwhile; ?>
	</div>
	<?php $response['html'] = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
	$response = json_encode($response);
	header("Content-Type: application/json");
	echo $response;
	exit;
}

add_action('wp_ajax_products_grid_load_more', 'products_grid_more_callback');
add_action('wp_ajax_nopriv_products_grid_load_more', 'products_grid_more_callback');

function thegem_get_product_posts($products_cat, $page = 1, $ppp = -1, $orderby = 'menu_order ID', $order = 'ASC', $featured_only = false, $sale_only = false) {
	if (empty($products_cat)) {
		return null;
	}

	$tax_query = [];

	if (!in_array('0', $products_cat, true)) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => $products_cat
		);
	}

	if ($featured_only) {
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'featured',
		);
	}

	$args = array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'orderby' => $orderby,
		'order' => $order,
		'paged' => $page,
		'posts_per_page' => $ppp,
		'tax_query' => $tax_query,
	);

	if ($sale_only) {
		$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
	}

	$portfolio_loop = new WP_Query($args);

	return $portfolio_loop;
}

function thegem_products_render_item($settings, $item_classes, $thegem_sizes = null, $post_id = false) {
	global $post, $product, $woocommerce_loop;

	if ($post_id) {
		$slugs = wp_get_object_terms($post_id, 'product_cat', array('fields' => 'slugs'));

		$thegem_product_featured_data = thegem_get_sanitize_product_featured_data(get_the_ID());

		if ($settings['ignore_highlights'] != 'yes' && !empty($thegem_product_featured_data['highlight'])) {
			$thegem_highlight_type = $thegem_product_featured_data['highlight_type'];
		} else {
			$thegem_highlight_type = 'disabled';
		}
	} else {
		$slugs = array();
		$product_grid_item_size = true;
		$thegem_highlight_type = 'disabled';
	}
	$terms = $settings['content_products_cat'];

	$thegem_classes = array('portfolio-item', 'inline-column', 'product');
	$thegem_classes = array_merge($thegem_classes, $slugs);

	$thegem_image_classes = array('image');
	$thegem_caption_classes = array('caption');

	if ($settings['layout'] != 'metro' || isset($product_grid_item_size)) {
		if ($thegem_highlight_type != 'disabled' && $thegem_highlight_type != 'vertical') {
			$thegem_classes = array_merge($thegem_classes, get_thegem_portfolio_render_item_classes($settings, $thegem_highlight_type));
		} else {
			$thegem_classes = array_merge($thegem_classes, $item_classes);
		}
	}

	if ($thegem_highlight_type != 'disabled') {
		$thegem_classes[] = 'double-item';
		$thegem_classes[] = 'double-item-' . $thegem_highlight_type;

		$thegem_sizes = get_thegem_portfolio_render_item_image_sizes($settings, $thegem_highlight_type);
	}

	if ($settings['loading_animation'] === 'yes') {
		$thegem_classes[] = 'item-animations-not-inited';
	}

	if (!isset($product_grid_item_size)) {
		$product_hover_image_id = 0;
		if ($settings['caption_position'] == 'page') {
			$gallery = $product->get_gallery_image_ids();
			$has_product_hover = get_post_meta($post_id, 'thegem_product_disable_hover', true);
			if (isset($gallery[0]) && !$has_product_hover) {
				$product_hover_image_id = $gallery[0];
				$thegem_classes[] = 'image-hover';
			}
		}

		$rating_count = $product->get_rating_count();
		if ($rating_count > 0) {
			$thegem_classes[] = 'has-rating';
		}

		$product_short_description = $product->get_short_description();
		$product_short_description = strip_shortcodes($product_short_description);
		$product_short_description = wp_strip_all_tags($product_short_description);
		$product_short_description_length = apply_filters('excerpt_length', 20);
		$product_short_description_more = apply_filters('excerpt_more', ' ' . '[&hellip;]');
		$product_short_description = wp_trim_words($product_short_description, $product_short_description_length, $product_short_description_more);
	}

	if ($settings['caption_position'] == 'image') {
		$hover_effect = $settings['image_hover_effect_image'];
	} else if ($settings['caption_position'] == 'page') {
		$hover_effect = $settings['image_hover_effect_page'];
	} else {
		$hover_effect = $settings['image_hover_effect_hover'];
	}

	$preset_path = __DIR__ . '/templates/content-product-grid-item.php';
	$preset_path_filtered = apply_filters( 'thegem_products_grid_item_preset', $preset_path);
	$preset_path_theme = get_stylesheet_directory() . '/templates/products-grid/content-product-grid-item.php';

	if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
		include($preset_path_theme);
	} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
		include($preset_path_filtered);
	}
}