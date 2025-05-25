<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

#[\AllowDynamicProperties]
class TheGem_Divider_Extended {

	private static $instance = null;


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {
		add_action( 'elementor/element/divider/section_divider/before_section_end', array( $this, 'before_section_end' ), 10, 2 );
		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render' ));
	}

	public function before_section_end( $element, $args ) {

		$element->add_control(
			'thegem_text_style',
			[
				'label' => 'Text Style',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => 'default',
					'title-h1' => __('Title H1', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
				],
				'default' => 'default',
				'condition' => [
					'look' => 'line_text',
				],
			]
		);

		$element->add_control(
			'thegem_text_weight',
			[
				'label' => 'Title Weight',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'bold' => 'Bold',
					'thin' => 'Thin',
				],
				'default' => 'bold',
				'condition' => [
					'thegem_text_style!' => ['styled-subtitle', 'default']
				]
			]
		);

	}

	public function before_render($element) {

		if('divider' !== $element->get_name()) {
			return ;
		}

		$settings = $element->get_settings_for_display();

		if ( ! empty( $settings['thegem_text_style'] ) ) {
			$element->add_render_attribute( 'text', 'class', [$settings['thegem_text_style'], 'gem-style-text'] );
		}

		if ( ! empty( $settings['thegem_text_weight'] ) && $settings['thegem_text_weight'] === 'thin' ) {
			$element->add_render_attribute( 'text', 'class', 'light' );
		}

	}

}

TheGem_Divider_Extended::instance();