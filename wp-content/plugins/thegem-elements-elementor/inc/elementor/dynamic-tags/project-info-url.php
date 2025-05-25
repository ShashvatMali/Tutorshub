<?php
namespace TheGem_Elementor\DynamicTags;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Project_Info_URL extends Data_Tag {

	public function get_name() {
		return 'thegem-project-info-url';
	}

	public function get_title() {
		return esc_html__( 'Project Detail (TheGem)', 'thegem' );
	}

	public function get_group() {
		return 'thegem';
	}

	public function get_categories() {
		return [
			TagsModule::URL_CATEGORY,
		];
	}

	public function get_value( array $options = [] ) {
		$key = $this->get_settings( 'key' );

		if ( empty( $key ) ) {
			$key = $this->get_settings( 'custom_key' );
		}

		if ( empty( $key ) ) {
			return;
		}

		$post_id = get_the_ID();
		if(thegem_get_template_type($post_id) === 'portfolio') {
			$single_post = thegem_templates_init_portfolio();
			if(!empty($single_post)) {
				$post_id = $single_post->ID;
			}
			thegem_templates_close_portfolio();
		}

		$value = get_post_meta( $post_id, $key, true );

		return wp_kses_post( $value );
	}


	public function get_panel_template_setting_key() {
		return 'key';
	}

	protected function register_controls() {
		Plugin::dynamic_tags_project_info_add_key_control( $this, 'link' );
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
