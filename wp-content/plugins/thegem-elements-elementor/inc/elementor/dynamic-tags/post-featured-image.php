<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Featured_Image extends Data_Tag {

	public function get_name() {
		return 'thegem-post-featured-image';
	}

	public function get_group() {
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		if(get_post_type() === 'thegem_title' || (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'title')) {
			return 'thegem-title';
		} else {
			return 'thegem';
		}
	}

	public function get_categories() {
		return [ TagsModule::IMAGE_CATEGORY ];
	}

	public function get_title() {
		return __( 'Page/Post Featured Image', 'thegem' );
	}

	public function get_value( array $options = [] ) {
		$thumbnail_id = 0;

		if(is_singular() && !is_singular('thegem_templates')) {
			$pid = get_queried_object_id();
			$thumbnail_id = get_post_thumbnail_id($pid);
			if(thegem_get_template_type($pid) === 'title') {
				return array('url' => Utils::get_placeholder_image_src());
			}
		}

		if(isset($GLOBALS['thegem_template_type']) && $GLOBALS['thegem_template_type'] == 'loop-item') {
			$thumbnail_id = get_post_thumbnail_id();
		}

		if(is_post_type_archive('product')) {
			$shop_page_id = wc_get_page_id('shop');
			if(has_post_thumbnail($shop_page_id)) {
				$thumbnail_id = get_post_thumbnail_id($shop_page_id);
			}
		}

		$pid = get_the_ID();
		if(thegem_get_template_type($pid) === 'single-product') {
			$product = thegem_templates_init_product();
			if(!empty($product)) {
				$pid = $product->get_id();
				if(has_post_thumbnail($pid)) {
					$thumbnail_id = get_post_thumbnail_id($pid);
				}
			}
			thegem_templates_close_product();
		}

		if(thegem_get_template_type($pid) === 'single-post' || thegem_get_template_type($pid) === 'loop-item' || thegem_get_template_type($pid) === 'title') {
			$single_post = thegem_templates_init_post();
			if(!empty($single_post)) {
				$pid = $single_post->ID;
				if(has_post_thumbnail($pid)) {
					$thumbnail_id = get_post_thumbnail_id($pid);
				}
			}
			thegem_templates_close_post();
		}

		if(thegem_get_template_type($pid) === 'portfolio') {
			$single_post = thegem_templates_init_portfolio();
			if(!empty($single_post)) {
				$pid = $single_post->ID;
				if(has_post_thumbnail($pid)) {
					$thumbnail_id = get_post_thumbnail_id($pid);
				}
			}
			thegem_templates_close_portfolio();
		}

		if(is_singular('blocks')) {
			$pid = get_queried_object_id();
			if(get_post_meta($pid, 'thegem_is_product_archive', true) && $slug = get_post_meta($pid, 'thegem_product_archive_slug', true)) {
				$term = get_term_by( 'slug', $slug, 'product_cat' );
				if($term) {
					$attachment_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
					if($attachment_id) {
						$thumbnail_id = $attachment_id;
					}
				}
			}
		}
		if(is_tax('product_cat')) {
			$tid = get_queried_object_id();
			$attachment_id = get_term_meta( $tid, 'thumbnail_id', true );
			if($attachment_id) {
				$thumbnail_id = $attachment_id;
			}
		}

		if ( $thumbnail_id && get_post_type($thumbnail_id) === 'attachment') {
			$image_data = [
				'id' => $thumbnail_id,
				'url' => wp_get_attachment_image_src( $thumbnail_id, 'full' )[0],
			];
		} else {
			$image_data = $this->get_settings( 'fallback' );
		}

		return $image_data;
	}

	protected function register_controls() {
		$this->add_control(
			'fallback',
			[
				'label' => __( 'Fallback', 'thegem' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
	}
}
