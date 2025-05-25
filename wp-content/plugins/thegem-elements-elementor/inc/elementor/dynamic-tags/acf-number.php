<?php
namespace TheGem_Elementor\DynamicTags;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_Number extends Tag {

	public function get_name() {
		return 'thegem-acf-number';
	}

	public function get_title() {
		return esc_html__( 'ACF', 'thegem' ) . ' ' . esc_html__( 'Number', 'thegem' ) . ' ' . esc_html__( 'Field', 'thegem' );
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
			TagsModule::NUMBER_CATEGORY,
			TagsModule::POST_META_CATEGORY,
		];
	}

	public function render() {
		list( $field, $meta_key ) = Plugin::dynamic_tags_acf_get_tag_value_field( $this );

		if ( $field && ! empty( $field['type'] ) ) {
			$value = $field['value'];
		} else {
			// Field settings has been deleted or not available.
			$single_post = thegem_templates_init_post();
			$value = get_field( $meta_key );
			thegem_templates_close_post();
		} // End if().

		echo wp_kses_post( $value );
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	protected function register_controls() {
		Plugin::dynamic_tags_acf_add_key_control( $this );
	}

	public function get_supported_fields() {
		return [
			'number',
		];
	}
}
