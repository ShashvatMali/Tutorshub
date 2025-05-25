<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Toolset_URL extends Data_Tag {

	public function get_name() {
		return 'thegem-toolset-url';
	}

	public function get_title() {
		return esc_html__( 'Toolset', 'thegem' ) . ' ' . esc_html__( 'URL Field', 'thegem' );
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
		return [ TagsModule::URL_CATEGORY ];
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	public function get_value( array $options = [] ) {
		// Toolset Embedded version loads its bootstrap later
		if ( ! function_exists( 'types_render_field' ) ) {
			return;
		}

		$key = $this->get_settings( 'key' );
		if ( empty( $key ) ) {
			return;
		}

		list( $field_group, $field_key ) = explode( ':', $key );

		$field = wpcf_admin_fields_get_field( $field_key );

		if ( $field && ! empty( $field['type'] ) ) {
			$value = '';
			switch ( $field['type'] ) {
				case 'email':
					$value = 'mailto:' . types_render_field( $field_key, [ 'output' => 'raw' ] );
					break;
				case 'image':
					$value = types_render_field( $field_key, [ 'url' => true ] );
					break;
				default:
					$value = types_render_field( $field_key, [ 'output' => 'raw' ] );
			} // End switch().
		}

		if ( empty( $value ) && $this->get_settings( 'fallback' ) ) {
			$value = $this->get_settings( 'fallback' );
		}

		return wp_kses_post( $value );
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
			]
		);
	}

	protected function get_supported_fields() {
		return [
			'email',
			'image',
			'file',
			'audio',
			'url',
		];
	}
}
