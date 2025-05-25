<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Toolset_Gallery extends Data_Tag {

	public function get_name() {
		return 'thegem-toolset-gallery';
	}

	public function get_title() {
		return esc_html__( 'Toolset', 'thegem' ) . ' ' . esc_html__( 'Gallery Field', 'thegem' );
	}

	public function get_categories() {
		return [ TagsModule::GALLERY_CATEGORY ];
	}

	public function get_group() {
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		if(get_post_type() === 'thegem_title' || (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'title')) {
			return 'thegem-title';
		} else {
			return 'thegem';
		}
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
		if ( empty( $key ) ) {
			return [];
		}

		$images = [];

		list( $field_group, $field_key ) = explode( ':', $key );

		$field = wpcf_admin_fields_get_field( $field_key );

		if ( $field && ! empty( $field['type'] ) ) {

			$galley_images = types_render_field( $field_key, [
				'separator' => '|',
				'url' => true,
			] );
			$galley_images = explode( '|', $galley_images );
			foreach ( $galley_images as $image_url ) {
				$images[] = [
					'id' => $this->get_cached_attachment_url_to_post_id( $image_url ),
					'url' => $image_url,
				];
			}
		}

		return $images;
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
	}

	protected function get_supported_fields() {
		return [
			'toolset_gallery',
		];
	}

	/**
	 * @param $attachment_url
	 *
	 * @return false|int|mixed
	 */
	private function get_cached_attachment_url_to_post_id( $attachment_url ) {
		$id = wp_cache_get( $attachment_url, __CLASS__ );

		if ( false === $id ) {
			$id = attachment_url_to_postid( $attachment_url );

			wp_cache_set( $attachment_url, $id, __CLASS__ );
		}

		return $id;
	}

	/**
	 * Toolset_Gallery constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data = [] ) {
		parent::__construct( $data );

		wp_cache_add_non_persistent_groups( __CLASS__ );
	}
}
