<?php
namespace TheGem_Elementor\DynamicTags;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Field extends Tag {

	public function get_name() {
		return 'thegem-custom-field';
	}

	public function get_title() {
		return esc_html__( 'Custom Field (TheGem)', 'thegem' );
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
		return [
			TagsModule::TEXT_CATEGORY,
			TagsModule::POST_META_CATEGORY,
		];
	}

	public function render() {
		$key = $this->get_settings( 'key' );

		if ( empty( $key ) ) {
			$key = $this->get_settings( 'custom_key' );
		}

		if ( empty( $key ) ) {
			return;
		}

		$single_post = thegem_templates_init_post();
		$value = get_post_meta( get_the_ID(), $key, true );
		thegem_templates_close_post();

		echo wp_kses_post( $value );
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	protected function register_controls() {
		Plugin::dynamic_tags_cf_add_key_control( $this );
	}

	private function get_queried_object_meta( $meta_key ) {
		$value = '';
		if ( is_singular() ) {
			$value = get_post_meta( get_the_ID(), $meta_key, true );
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$value = get_term_meta( get_queried_object_id(), $meta_key, true );
		}

		return $value;
	}
}
