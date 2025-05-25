<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

#[\AllowDynamicProperties]
class TheGem_Heading_Extended {

	private static $instance = null;


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {
		add_action( 'elementor/element/heading/section_title/before_section_end', array( $this, 'before_section_end' ), 10, 2 );
		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render' ));
		add_filter( 'elementor/widget/print_template', array( $this, 'print_template'), 10, 2);
		add_filter( 'elementor/widget/render_content', array( $this, 'heading_render_content'), 10, 2 );
	}

	public function heading_render_content( $widget_content, $widget ) {
		if('heading' === $widget->get_name()) {
			return do_shortcode($widget_content);
		}

		return $widget_content;
	}

	public function before_section_end( $element, $args ) {

		$element->add_control(
			'thegem_heading_style',
			[
				'label' => 'DIV Style',
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
					'main-menu-item' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'title-h2',
				'condition' => [
					'header_size' => 'div',
				]
			]
		);

		$element->add_control(
			'thegem_heading_weight',
			[
				'label' => 'Title Weight',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'bold' => 'Bold',
					'thin' => 'Thin',
				],
				'default' => 'bold',
				'condition' => [
					'thegem_heading_style!' => ['styled-subtitle', 'default']
				]
			]
		);

	}

	public function before_render($element) {

		if('heading' !== $element->get_name()) {
			return ;
		}

		$settings = $element->get_settings_for_display();

		if ( ! empty( $settings['thegem_heading_style'] ) ) {
			$element->add_render_attribute( 'title', 'class', $settings['thegem_heading_style'] );
		}

		if ( ! empty( $settings['thegem_heading_weight'] ) && $settings['thegem_heading_weight'] === 'thin' ) {
			$element->add_render_attribute( 'title', 'class', 'light' );
		}

	}

	public function print_template($template, $element) {

		if('heading' === $element->get_name()) {
			$old_template = 'view.addInlineEditingAttributes( \'title\' )';
			ob_start();
?>

		if ( 'div' === settings.header_size && '' !== settings.thegem_heading_style ) {
			view.addRenderAttribute( 'title', 'class', settings.thegem_heading_style );
		}

		if ( 'thin' === settings.thegem_heading_weight ) {
			view.addRenderAttribute( 'title', 'class', 'light' );
		}

		view.addInlineEditingAttributes( 'title' );
<?php
			$new_template = ob_get_clean();

			$template = str_replace( $old_template, $new_template, $template );
		}

		return $template;
	}

}

TheGem_Heading_Extended::instance();
