<?php

use Elementor\Controls_Manager;

class TheGem_Editor_Styles {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {
		add_action( 'elementor/element/text-editor/section_style/before_section_end', array( $this, 'before_section_style_end' ), 10, 2 );
	}


	public function before_section_style_end( $element, $args ) {

		$kit = Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();

		$kit_settings = $kit->get_meta( Elementor\Core\Settings\Page\Manager::META_KEY );

		$default_fonts = isset( $kit_settings['default_generic_fonts'] ) ? $kit_settings['default_generic_fonts'] : 'Sans-serif';

		if ( $default_fonts ) {
			$default_fonts = ', ' . $default_fonts;
		}

		$element->update_control(
			'text_color',
			[
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}} .elementor-text-editor *:not(.elementor-drop-cap-letter)' => 'color: {{VALUE}};',
				],
			]
		);

		$element->update_control(
			'typography_font_family',
			[
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}} .elementor-text-editor *' => 'font-family: "{{VALUE}}"' . $default_fonts . ';',
				],
			]
		);

		$element->update_responsive_control(
			'typography_font_size',
			[
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}} .elementor-text-editor *' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$element->update_control(
			'typography_font_weight',
			[
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}} .elementor-text-editor *' => 'font-weight: {{VALUE}};',
				],
			]
		);

		$element->update_control(
			'typography_text_transform',
			[
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}} .elementor-text-editor *' => 'text-transform: {{VALUE}};',
				],
			]
		);

		$element->update_control(
			'typography_font_style',
			[
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}} .elementor-text-editor *' => 'font-style: {{VALUE}};',
				],
			]
		);

		$element->update_responsive_control(
			'typography_line_height',
			[
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}} .elementor-text-editor *' => 'line-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$element->update_responsive_control(
			'typography_letter_spacing',
			[
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}} .elementor-text-editor *' => 'letter-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

	}


}

TheGem_Editor_Styles::instance();
