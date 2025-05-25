<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Toolset_Image extends Data_Tag {

	public function get_name() {
		return 'thegem-toolset-image';
	}

	public function get_title() {
		return esc_html__( 'Toolset', 'thegem' ) . ' ' . esc_html__( 'Image Field', 'thegem' );
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
		// Toolset Embedded version loads its bootstrap later
		if ( ! function_exists( 'types_render_field' ) ) {
			return [];
		}

		$key = $this->get_settings( 'key' );
		$image_data = $this->get_settings( 'fallback' );
		if ( empty( $key ) ) {
			return $image_data;
		}

		list( $field_group, $field_key ) = explode( ':', $key );

		$field = wpcf_admin_fields_get_field( $field_key );

		if ( $field && ! empty( $field['type'] ) ) {

			$url = types_render_field( $field_key, [ 'url' => true ] );

			if ( empty( $url ) ) {
				return $image_data;
			}

			$image_data = [
				'id' => attachment_url_to_postid( $url ),
				'url' => $url,
			];
		}

		return $image_data;
	}

	protected function register_controls() {
		$this->add_control(
			'key',
			[
				'label' => esc_html__( 'Key', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'groups' => Plugin::dynamic_tags_toolset_get_control_options( $this->get_supported_fields() ),
			]
		);

		$this->add_control(
			'fallback',
			[
				'label' => esc_html__( 'Fallback', 'thegem' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
	}

	protected function get_supported_fields() {
		return [
			'toolset_image',
		];
	}
}
