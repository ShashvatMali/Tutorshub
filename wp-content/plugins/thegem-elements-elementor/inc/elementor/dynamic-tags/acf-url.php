<?php
namespace TheGem_Elementor\DynamicTags;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_URL extends Data_Tag {

	public function get_name() {
		return 'thegem-acf-url';
	}

	public function get_title() {
		return esc_html__( 'ACF', 'thegem' ) . ' ' . esc_html__( 'URL Field', 'thegem' );
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
		list( $field, $meta_key ) = Plugin::dynamic_tags_acf_get_tag_value_field( $this );

		if ( $field ) {
			$value = $field['value'];

			if ( is_array( $value ) && isset( $value[0] ) ) {
				$value = $value[0];
			}

			if ( $value ) {
				if ( ! isset( $field['return_format'] ) ) {
					$field['return_format'] = isset( $field['save_format'] ) ? $field['save_format'] : '';
				}

				switch ( $field['type'] ) {
					case 'email':
						if ( $value ) {
							$value = 'mailto:' . $value;
						}
						break;
					case 'image':
					case 'file':
						switch ( $field['return_format'] ) {
							case 'array':
							case 'object':
								$value = $value['url'];
								break;
							case 'id':
								if ( 'image' === $field['type'] ) {
									$src = wp_get_attachment_image_src( $value, 'full' );
									$value = $src[0];
								} else {
									$value = wp_get_attachment_url( $value );
								}
								break;
						}
						break;
					case 'link':
						switch ( $field['return_format'] ) {
							case 'array':
							case 'object':
								$value = $value['url'];
								break;
						}
						break;
					case 'post_object':
					case 'relationship':
						$value = get_permalink( $value );
						break;
					case 'taxonomy':
						$value = get_term_link( $value, $field['taxonomy'] );
						break;
				} // End switch().
			}
		} else {
			// Field settings has been deleted or not available.
			$single_post = thegem_templates_init_post();
			$value = get_field( $meta_key );
			thegem_templates_close_post();
		} // End if().

		if ( empty( $value ) && $this->get_settings( 'fallback' ) ) {
			$value = $this->get_settings( 'fallback' );
		}

		return wp_kses_post( $value );
	}

	protected function register_controls() {
		Plugin::dynamic_tags_acf_add_key_control( $this );

		$this->add_control(
			'fallback',
			[
				'label' => esc_html__( 'Fallback', 'thegem' ),
			]
		);
	}

	public function get_supported_fields() {
		return [
			'text',
			'email',
			'image',
			'file',
			'link',
			'page_link',
			'post_object',
			'relationship',
			'taxonomy',
			'url',
		];
	}
}
