<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Terms extends Tag {
	public function get_name() {
		return 'thegem-post-terms';
	}

	public function get_title() {
		return esc_html__( 'Post Terms', 'thegem' );
	}

	public function get_group() {
		return 'thegem';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	protected function register_controls() {
		$taxonomy_filter_args = [
			'show_in_nav_menus' => true,
			'object_type' => [ get_post_type() ],
		];

		$taxonomy_filter_args = apply_filters( 'thegem/dynamic_tags/post_terms/taxonomy_args', $taxonomy_filter_args );

		$taxonomies = Plugin::dynamic_tags_get_taxonomies( $taxonomy_filter_args, 'objects' );

		$options = [];

		foreach ( $taxonomies as $taxonomy => $object ) {
			$options[ $taxonomy ] = $object->label;
		}

		$this->add_control(
			'taxonomy',
			[
				'label' => esc_html__( 'Taxonomy', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'post_tag',
			]
		);

		$this->add_control(
			'separator',
			[
				'label' => esc_html__( 'Separator', 'thegem' ),
				'type' => Controls_Manager::TEXT,
				'default' => ', ',
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	public function render() {
		$settings = $this->get_settings();

		if ( 'yes' === $settings['link'] ) {
			$value = get_the_term_list( get_the_ID(), $settings['taxonomy'], '', $settings['separator'] );
		} else {
			$terms = get_the_terms( get_the_ID(), $settings['taxonomy'] );

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				return '';
			}

			$term_names = [];

			foreach ( $terms as $term ) {
				$term_names[] = '<span>' . $term->name . '</span>';
			}

			$value = implode( $settings['separator'], $term_names );
		}

		echo wp_kses_post( $value );
	}
}
