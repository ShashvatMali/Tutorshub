<?php
global $post, $product;
$thegem_classes = ['product', 'compact-product-item'];
$post_id = get_the_ID();
$rating_count = $product->get_rating_count();
if ($rating_count > 0) {
	$thegem_classes[] = 'has-rating';
}

if ($params['layout'] == 'grid' && $params['columns'] == '1x') {
	$image_size = 'double-s';
	$image_size_double = 'double-m';
} else if ($params['layout'] == 'grid' && $params['columns'] == '2x') {
	$image_size = 's';
	$image_size_double = 'l';
} else if ($params['layout'] == 'grid' && in_array($params['columns'], array('3x', '4x', '5x'))) {
	$image_size = 'xs';
	$image_size_double = 'double-xs';
} else {
	$image_size = 'xxs';
	$image_size_double = 'xs';
}

$thegem_size = 'thegem-product-justified-' . $params['image_aspect_ratio'] . '-' . $image_size;
$thegem_size_double = 'thegem-product-justified-' . $params['image_aspect_ratio'] . '-' . $image_size_double; ?>
<div <?php post_class($thegem_classes, $post_id); ?>>
	<div class="wrap clearfix">
		<div <?php post_class(array('image'), $post_id); ?>>
			<a href="<?php echo get_permalink(); ?>">
				<?php if (has_post_thumbnail($post_id) || has_post_thumbnail($product->get_parent_id())) { ?>
					<?php
					if (has_post_thumbnail($post_id)) {
						$thumbnail_id = get_post_thumbnail_id($post_id);
					} else {
						$thumbnail_id = get_post_thumbnail_id($product->get_parent_id());
					}

					$thegem_sources = array(
						array('srcset' => array('1x' => $thegem_size, '2x' => $thegem_size_double))
					);
					thegem_generate_picture($thumbnail_id, $thegem_size, $thegem_sources, array('alt' => get_the_title($post_id), /*'style' => 'max-width: 110%'*/), true);
					?>
				<?php } else {
					echo '<span class="gem-dummy"></span>';
				} ?>
			</a>
			<?php if ($product->is_on_sale()) : ?>
				<div class="label onsale">%</div>
			<?php endif; ?>
		</div>

		<div <?php post_class(array('caption')); ?>>
			<div class="product-info clearfix">
				<?php if ($params['product_show_categories'] == 'yes') {
					if ($product->post_type == 'product_variation') {
						$terms = get_the_terms($product->get_parent_id(), 'product_cat');
					} else {
						$terms = get_the_terms($product->get_id(), 'product_cat');
					}
					if ($terms) {
						foreach ($terms as $term) {
							$term_links[] = '<a href="#" data-filter-type="category" data-filter="' . $term->slug . '">' . $term->name . '</a>';
						}
						if (!empty($term_links)) {
							echo '<div class="categories">' . implode(', ', $term_links) . '</div>';
						}
					}
				} ?>
				<?php if ($params['product_show_title'] == 'yes') { ?>
					<div class="title text-body-tiny"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
					</div>
				<?php } ?>
				<?php if ($params['product_show_reviews'] == 'yes') { 
					$product_show_reviews_count = (isset($params['product_show_reviews_count']) && ($params['product_show_reviews_count'] == 'yes' || $params['product_show_reviews_count'] == '1')) ? ' show-count' : '';
				?>
					<div class="reviews<?php echo $product_show_reviews_count; ?>"><?php woocommerce_template_loop_rating(); ?></div>
				<?php } ?>
				<?php if ($params['product_show_price'] == 'yes') {
					woocommerce_template_loop_price();
				} ?>
			</div>
		</div>

	</div>
</div>