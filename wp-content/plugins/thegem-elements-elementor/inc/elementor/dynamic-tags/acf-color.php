<?php
namespace TheGem_Elementor\DynamicTags;

use TheGem_Elementor\Plugin;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_Color extends Data_Tag {

	public function get_name() {
		return 'thegem-acf-color';
	}

	public function get_title() {
		return esc_html__( 'ACF', 'thegem' ) . ' ' . esc_html__( 'Color Picker Field', 'thegem' );
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
		return [ TagsModule::COLOR_CATEGORY ];
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	public function get_value( array $options = [] ) {
		list( $field, $meta_key ) = Plugin::dynamic_tags_acf_get_tag_value_field( $this );

		if ( $field ) {
			$value = $field['value'];
		} else {
			// Field settings has been deleted or not available.
			$single_post = thegem_templates_init_post();
			$value = get_field( $meta_key );
			thegem_templates_close_post();
		}

		if ( empty( $value ) && $this->get_settings( 'fallback' ) ) {
			$value = $this->get_settings( 'fallback' );
		}

		return $value;
	}

	protected function register_controls() {
		Plugin::dynamic_tags_acf_add_key_control( $this );
	}

	public function get_supported_fields() {
		return [
			'color_picker',
		];
	}
}
