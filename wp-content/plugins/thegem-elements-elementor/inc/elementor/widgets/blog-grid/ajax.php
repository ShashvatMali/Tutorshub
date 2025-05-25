<?php
function bloggrid_load_more_callback() {
	$settings = isset( $_POST['data'] ) ? json_decode(stripslashes($_POST['data']), true) : array();
	ob_start();
	$response = array( 'status' => 'success' );

	$taxonomy_filter = $manual_selection = $blog_authors = $date_query = [];

	if ($settings['query_type'] == 'post') {

		$post_type = 'post';
		foreach ($settings['source'] as $source) {
			if ($source == 'categories' && !empty($settings['categories'])) {
				$taxonomy_filter['category'] = $settings['categories'];
			} else if ($source == 'tags' && !empty($settings['select_blog_tags'])) {
				$taxonomy_filter['post_tag'] = $settings['select_blog_tags'];
			} else if ($source == 'posts' && !empty($settings['select_blog_posts'])) {
				$manual_selection = $settings['select_blog_posts'];
			} else if ($source == 'authors' && !empty($settings['select_blog_authors'])) {
				$blog_authors = $settings['select_blog_authors'];
			}
		}
		$exclude = $settings['exclude_blog_posts'];

	} else if ($settings['query_type'] == 'related') {

		$post_type = isset($settings['taxonomy_related_post_type']) ? $settings['taxonomy_related_post_type'] : 'any';
		$taxonomy_filter = $settings['related_tax_filter'];
		$exclude = $settings['exclude_posts_manual'];

	} else if ($settings['query_type'] == 'archive') {

		$post_type = $settings['archive_post_type'];
		if (!empty($settings['select_blog_authors'])) {
			$blog_authors = $settings['select_blog_authors'];
		} else if (!empty($settings['archive_tax_filter'])) {
			$taxonomy_filter = $settings['archive_tax_filter'];
		} else if (!empty($settings['date_query'])) {
			$date_query = $settings['date_query'];
		}
		$exclude = $settings['exclude_posts_manual'];

	} else if ($settings['query_type'] == 'manual') {

		$post_type = 'any';
		$manual_selection = $settings['select_posts_manual'];
		$exclude = $settings['exclude_posts_manual'];

	} else {

		$post_type = $settings['query_type'];
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
		$exclude = $settings['source_post_type_' . $post_type . '_exclude'];

	}

	$orderby = $order = '';
	if (isset($settings['order_by']) && $settings['order_by'] != 'default') {
		$orderby = $settings['order_by'];
	}
	if (isset($settings['order']) && $settings['order'] != 'default') {
		$order = $settings['order'];
	}

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$paged = $settings['paged'];
	$items_per_page = $settings['items_per_page'] ? intval($settings['items_per_page']) : 8;

	$bg_readmore_button = __DIR__ . '/templates/parts/bg_readmore_button.php';
	$bg_social_sharing  = __DIR__ . '/templates/parts/bg_social_sharing.php';
	$bg_loadmore_button  = __DIR__ . '/templates/parts/bg_loadmore_button.php';

	$posts = get_thegem_extended_blog_posts($post_type, $taxonomy_filter, [], $manual_selection, $exclude, $blog_authors, $paged, $items_per_page, $orderby, $order, $settings['offset'], $settings['ignore_sticky_posts'], false, false, $date_query);

	$next_page = 0;
		if( $posts->max_num_pages > $paged ) {
			$next_page = $paged + 1;
		} else {
			$next_page = 0;
		}
	?>

	<div data-page="<?php echo esc_attr( $paged ); ?>" data-paged="<?php echo esc_attr( $paged ); ?>" data-next-page="<?php echo esc_attr( $next_page ); ?>">
		<?php
		$button_container_attributes_wrap = array (
			'gem-button-container',
			'gem-widget-button',
			'gem-button-position-inline',
		);
		$button_container_attributes = 'class="' . implode(" ", $button_container_attributes_wrap) . '"';

		$readmore_button_wrap = array (
			'gem-button',
			'gem-button-size-'. $settings['readmore_button_size'],
			'gem-button-style-'. $settings['readmore_button_type'],
		);
		$readmore_button = 'class="' . implode(" ", $readmore_button_wrap) . '"';

		$readmore_button_text = $settings['readmore_button_text'];

		$preset_path = __DIR__ . '/templates/output-blog-grid.php';
		$preset_path_filtered = apply_filters( 'thegem_blog_grid_item_preset', $preset_path);
		$preset_path_theme = get_stylesheet_directory() . '/templates/blog-grid/output-blog-grid.php';

		if ($posts -> have_posts()) :  while ($posts -> have_posts()) : $posts -> the_post();
			if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
				include($preset_path_theme);
			} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
				include($preset_path_filtered);
			}
		endwhile;
		endif;
		?>
	</div>

	<?php
	$response['html'] = trim( preg_replace( '/\s\s+/', ' ', ob_get_clean() ) );
	$response = json_encode( $response );
	header('Content-Type: application/json');
	echo $response;
	die;
}
add_action( 'wp_ajax_thegem_bloggrid_load_more', 'bloggrid_load_more_callback' );
add_action( 'wp_ajax_nopriv_thegem_bloggrid_load_more', 'bloggrid_load_more_callback' );