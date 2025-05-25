<?php
namespace TheGem_Elementor\DynamicTags;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_Image extends Data_Tag {

	public function get_name() {
		return 'thegem-acf-image';
	}

	public function get_title() {
		return esc_html__( 'ACF', 'thegem' ) . ' ' . esc_html__( 'Image Field', 'thegem' );
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

	public function get_panel_template_setting_key() {
		return 'key';
	}

	public function get_value( array $options = [] ) {
		$image_data = [
			'id' => null,
			'url' => '',
		];

		list( $field, $meta_key ) = Plugin::dynamic_tags_acf_get_tag_value_field( $this );

		if ( $field && is_array( $field ) ) {
			$field['return_format'] = isset( $field['save_format'] ) ? $field['save_format'] : $field['return_format'];
			switch ( $field['return_format'] ) {
				case 'object':
				case 'array':
					$value = $field['value'];
					break;
				case 'url':
					$value = [
						'id' => 0,
						'url' => $field['value'],
					];
					break;
				case 'id':
					$src = wp_get_attachment_image_src( $field['value'], $field['preview_size'] );
					$value = [
						'id' => $field['value'],
						'url' => $src[0],
					];
					break;
			}
		}

		if ( ! isset( $value ) ) {
			// Field settings has been deleted or not available.
			$single_post = thegem_templates_init_post();
			$value = get_field( $meta_key );
			thegem_templates_close_post();
		}

		if ( empty( $value ) && $this->get_settings( 'fallback' ) ) {
			$value = $this->get_settings( 'fallback' );
		}

		if ( ! empty( $value ) && is_array( $value ) ) {
			$image_data['id'] = $value['id'];
			$image_data['url'] = $value['url'];
		}

		return $image_data;
	}

	protected function register_controls() {
		Plugin::dynamic_tags_acf_add_key_control( $this );

		$this->add_control(
			'fallback',
			[
				'label' => esc_html__( 'Fallback', 'thegem' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
	}

	public function get_supported_fields() {
		return [
			'image',
		];
	}
}
