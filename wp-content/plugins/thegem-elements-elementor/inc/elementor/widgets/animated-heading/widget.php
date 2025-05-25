<?php

namespace TheGem_Elementor\Widgets\TheGem_Animated_Heading;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use TheGemHeadingAnimation;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Heading.
 */
#[\AllowDynamicProperties]
class TheGem_Animated_Heading extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_HEADING_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_HEADING_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_HEADING_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_HEADING_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */

	public function get_name() {
		return 'thegem-animated-heading';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */

	public function get_title() {
		return __('Animated Heading', 'thegem');
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return str_replace('thegem-', 'thegem-eicon thegem-eicon-', $this->get_name());
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'megamenu') {
			return ['thegem_megamenu_builder'];
		}
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-product') {
			return ['thegem_single_product_builder'];
		}
		return ['thegem_elements'];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-heading-animation'];
		}
		return [];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-heading-main',
					'thegem-heading-prepare-animation',
					'thegem-heading-rotating'];
		}
		return [];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_title',
			[
				'label' => __('Title', 'thegem'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'heading_text',
			[
				'label' => __('Text', 'thegem'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('Enter your title', 'thegem'),
				'default' => __('Add Your Heading Text Here', 'thegem'),
			]
		);

		$repeater->add_control(
			'heading_text_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
			]
		);

		$repeater->add_control(
			'heading_text_style',
			[
				'label' => __('Font italic', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$repeater->add_control(
			'heading_text_decoration',
			[
				'label' => __('Underline', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$repeater->add_control(
			'heading_text_color',
			[
				'label' => __('Text color', 'thegem'),
				'type' => Controls_Manager::COLOR,
			]
		);

		$repeater->add_control(
			'rotating_text_enabled',
			[
				'label' => __('Rotating text', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$repeater->add_control(
			'words_animation1',
			[
				'label' => __('Rotating text', 'thegem') . ' 1',
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'rotating_text_enabled' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'words_animation2',
			[
				'label' => __('Rotating text', 'thegem') . ' 2',
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'rotating_text_enabled' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'words_animation3',
			[
				'label' => __('Rotating text', 'thegem') . ' 3',
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'rotating_text_enabled' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'words_animation4',
			[
				'label' => __('Rotating text', 'thegem') . ' 4',
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'rotating_text_enabled' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'words_animation5',
			[
				'label' => __('Rotating text', 'thegem') . ' 5',
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'rotating_text_enabled' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'rotating_animation_duration',
			[
				'label' => __('Rotating Speed (ms)', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
						'step' => 100,
					],
				],
				'condition' => [
					'rotating_text_enabled' => 'yes'
				],
			]
		);

		$this->add_control(
			'text_content_header',
			[
				'label' => __('Heading Content', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'text_content_decsription',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Use repeater below to style different heading text fragments separately (eg. mix different colors & font weights in one heading). Click on "+" to add new text fragments.', 'thegem'),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'text_content',
			[
				'label' => __('Heading Content', 'thegem'),
				'type' => Controls_Manager::REPEATER,
				'show_label' => false,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'heading_text' => __('This is heading', 'thegem'),
					],
				],
				'title_field' => '{{{ heading_text }}}',
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'header_size',
			[
				'label' => __('HTML Tag', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __('Justified', 'thegem'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __('View', 'thegem'),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->add_control(
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
			]
		);

		$this->add_control(
			'heading_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'heading_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_label_text',
			[
				'label' => __('Label Text', 'thegem'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_separator',
			[
				'label' => __('Separator', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'animations',
			[
				'label' => __('Animations', 'thegem'),
			]
		);

		$this->add_control(
			'animation_enable',
			[
				'label' => __('Animation', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'heading_animation',
			[
				'label' => __('CSS Animation', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'lines-slide-up',
				'options' => TheGemHeadingAnimation::getAnimationList(),
				'condition' => [
					'animation_enable' => 'yes'
				],
			]
		);

		$this->add_control(
			'heading_animation_duration',
			[
				'label' => __('Animation Speed (ms)', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_enable' => 'yes'
				],
			]
		);

		$this->add_control(
			'heading_animation_delay',
			[
				'label' => __('Animation Delay (ms)', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_enable' => 'yes'
				],
			]
		);

		$this->add_control(
			'heading_animation_interval',
			[
				'label' => __('Animation Interval (ms)', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_enable' => 'yes'
				],
			]
		);

		$this->add_control(
			'heading_animation_timing_function',
			[
				'label' => __('Animation Timing Function', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'animation_enable' => 'yes'
				],
				'description' => sprintf(__('(Please refer to this %s)', 'thegem'), '<a href="https://www.w3schools.com/cssref/css3_pr_animation-timing-function.asp" target="_blank">article</a>'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __('Title', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .thegem-heading, {{WRAPPER}} .thegem-heading .light',
			]
		);

		$this->add_control(
			'heading_text_color_hover',
			[
				'label' => __('Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-heading:hover > span,
					{{WRAPPER}} .thegem-heading:hover > a,
					{{WRAPPER}} .thegem-heading:hover span.colored' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .thegem-heading',
			]
		);

		$this->add_control(
			'blend_mode',
			[
				'label' => __('Blend Mode', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Normal', 'thegem'),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-heading' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->add_control(
			'heading_label_color',
			[
				'label' => __('Label Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-heading span.label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_label_background',
			[
				'label' => __('Label Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-heading span.label' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'separator_header',
			[
				'label' => __('Separator', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .separator span' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'separator_weight',
			[
				'label' => __('Weight, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 6,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .separator span' => 'height:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'separator_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'rem' => [
						'min' => 1,
						'max' => 100,
					],
					'em' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .separator span' => 'width:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'separator_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .separator' => 'margin-top:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'separator_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'margin-bottom:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('title-fancy', 'class', 'thegem-heading');
		$this->add_render_attribute('title-fancy', 'class', 'thegem-animated-heading');

		if (!empty($settings['thegem_heading_style'])) {
			$this->add_render_attribute('title-fancy', 'class', $settings['thegem_heading_style']);
		}

		if (!empty($settings['thegem_heading_weight']) && $settings['thegem_heading_weight'] === 'thin') {
			$this->add_render_attribute('title-fancy', 'class', 'light');
		}

		if (!empty($settings['size'])) {
			$this->add_render_attribute('title-fancy', 'class', 'elementor-size-' . $settings['size']);
		}

		$id = 'thegem-heading-' . $this->get_id();
		$widget_id = '.elementor-element-' . $this->get_id();
		$title = '';

		$this->add_render_attribute('title-fancy', 'id', $id);

		$inline_css = '#'.$id.' {margin: 0;}';

		if (!empty($settings['align'])) {
			if ($settings['align'] == 'right') {
				$inline_css .= '#'.$id.' {margin-left: auto; margin-right: 0;}';
				$inline_css .= '#'.$id.' > * {justify-content: flex-end;}';
			} else if ($settings['align'] == 'center') {
				$inline_css .= '#'.$id.' {margin-left: auto; margin-right: auto;}';
				$inline_css .= '#'.$id.' > * {justify-content: center;}';
			}
		}

		if (!empty($settings['animation_enable']) && $settings['animation_enable'] == 'yes') {
			$this->add_render_attribute('title-fancy', 'class', 'thegem-heading-animate');
			$this->add_render_attribute('title-fancy', 'class', $settings['heading_animation']);
			wp_enqueue_style('thegem-heading-animation');
			wp_enqueue_script('thegem-heading-main');
			wp_enqueue_script('thegem-heading-prepare-animation');
			TheGemHeadingAnimation::instance()->includeInlineJs();

			$animation_duration = !empty($settings['heading_animation_duration']['size']) ? (int)$settings['heading_animation_duration']['size'] : 0;
			$animation_delay = !empty($settings['heading_animation_delay']['size']) ? (int)$settings['heading_animation_delay']['size'] : 0;
			$animation_timing_function = !empty($settings['heading_animation_timing_function']) ? $settings['heading_animation_timing_function'] : null;
			$animation_interval = !empty($settings['heading_animation_interval']['size']) ? (int)$settings['heading_animation_interval']['size'] : TheGemHeadingAnimation::getDefaultInterval($settings['heading_animation']);

			if (in_array($settings['heading_animation'], [TheGemHeadingAnimation::ANIMATION_LINES_SLIDE_UP, TheGemHeadingAnimation::ANIMATION_LINES_SLIDE_UP_RANDOM])) {
				$this->add_render_attribute('title-fancy', 'data-animation-name', $settings['heading_animation']);

				if (isset($settings['heading_animation_delay']['size']) && !empty($settings['heading_animation_delay']['size'])) {
					$this->add_render_attribute('title-fancy', 'data-animation-delay', $settings['heading_animation_delay']['size']);
				}

				$this->add_render_attribute('title-fancy', 'data-animation-interval', $animation_interval);
			}

			if (in_array($settings['heading_animation'], [TheGemHeadingAnimation::ANIMATION_WORDS_SLIDE_UP, TheGemHeadingAnimation::ANIMATION_WORDS_SLIDE_LEFT, TheGemHeadingAnimation::ANIMATION_WORDS_SLIDE_RIGHT, TheGemHeadingAnimation::ANIMATION_LINES_SLIDE_UP_RANDOM])) {
				if ($animation_duration > 0) {
					$inline_css .= '#'.$id.' .thegem-heading-word {animation-duration: ' . $animation_duration . 'ms;}';
				}

				if ($animation_timing_function) {
					$inline_css .= '#'.$id.' .thegem-heading-word {animation-timing-function: ' . $animation_timing_function . ';}';
				}
			}

			if (in_array($settings['heading_animation'], [TheGemHeadingAnimation::ANIMATION_LINES_SLIDE_UP])) {
				if ($animation_duration > 0) {
					$inline_css .= '#'.$id.' .thegem-heading-line {animation-duration: ' . $animation_duration . 'ms;}';
				}

				if ($animation_timing_function) {
					$inline_css .= '#'.$id.' .thegem-heading-line {animation-timing-function: ' . $animation_timing_function . ';}';
				}
			}

			if (in_array($settings['heading_animation'], [TheGemHeadingAnimation::ANIMATION_LETTERS_SLIDE_UP, TheGemHeadingAnimation::ANIMATION_TYPEWRITER, TheGemHeadingAnimation::ANIMATION_LETTERS_SCALE_OUT])) {
				if ($animation_duration > 0) {
					$inline_css .= '#'.$id.' .thegem-heading-letter {animation-duration: ' . $animation_duration . 'ms;}';
				}

				if ($animation_timing_function) {
					$inline_css .= '#'.$id.' .thegem-heading-letter {animation-timing-function: ' . $animation_timing_function . ';}';
				}
			}

			if ($settings['heading_animation'] == TheGemHeadingAnimation::ANIMATION_BACKGROUND_SLIDING && !empty($settings['_background_color'])) {
				$inline_css .= $widget_id.' .elementor-widget-container {background: none !important;}';
				$inline_css .= '#'.$id.':before {background-color: ' . $settings['_background_color'] . '}';

				if (!empty($settings['_padding'])) {
					$inline_css .= $widget_id.' .elementor-widget-container {padding: 0 !important;}';
					$inline_css .= '#'.$id.' {padding: ' . $settings['_padding']['top'] . $settings['_padding']['unit'] . ' ' . $settings['_padding']['right'] . $settings['_padding']['unit'] . ' ' . $settings['_padding']['bottom'] . $settings['_padding']['unit'] . ' ' . $settings['_padding']['left'] . $settings['_padding']['unit'] . '}';
				}

				if ($animation_duration > 0) {
					$inline_css .= '#'.$id.':before {animation-duration: ' . $animation_duration . 'ms;}';
					$inline_css .= '#'.$id.'.thegem-heading-animated .thegem-heading-text-wrap {transition-duration: ' . $animation_duration . 'ms;}';
				}

				if ($animation_timing_function) {
					$inline_css .= '#'.$id.':before {animation-timing-function: ' . $animation_timing_function . ';}';
					$inline_css .= '#'.$id.'.thegem-heading-animated .thegem-heading-text-wrap {transition-timing-function: ' . $animation_timing_function . 'ms;}';
				}

				if ($animation_delay > 0) {
					$inline_css .= '#'.$id.':before {animation-delay: ' . $settings['heading_animation_delay']['size'] . 'ms;}';
					$inline_css .= '#'.$id.'.thegem-heading-animated .thegem-heading-text-wrap {transition-delay: ' . $settings['heading_animation_delay']['size'] . 'ms;}';
				}
			}


			if (in_array($settings['heading_animation'], [TheGemHeadingAnimation::ANIMATION_BACKGROUND_SLIDING, TheGemHeadingAnimation::ANIMATION_FADE_TB, TheGemHeadingAnimation::ANIMATION_FADE_BT, TheGemHeadingAnimation::ANIMATION_FADE_LR, TheGemHeadingAnimation::ANIMATION_FADE_RL, TheGemHeadingAnimation::ANIMATION_FADE_SIMPLE])) {
				if ($animation_duration > 0) {
					$inline_css .= '#'.$id.' {animation-duration: ' . $animation_duration . 'ms;}';
				}

				if ($animation_timing_function) {
					$inline_css .= '#'.$id.' {animation-timing-function: ' . $animation_timing_function . ';}';
				}

				if ($animation_delay > 0) {
					$inline_css .= '#'.$id.' {animation-delay: ' . $animation_delay . 'ms;}';
				}
			}
		}

		$heading_animation_index = 0;

		$text_content = $settings['text_content'];

		foreach ($text_content as $k => $value) {
			if (isset($value['heading_text'])) {
				$value['heading_text'] = do_shortcode($value['heading_text']);
			}

			if ($k > 0) {
				$title .= ' ';
			}

			$text_style = $rotating_text = $inner_class = '';

			if (isset($value['heading_text_weight']) && $value['heading_text_weight'] == 'light') {
				$inner_class = 'light';
			}

			if (isset($value['heading_text_style']) && !empty($value['heading_text_style'])) {
				$text_style .= ' font-style: italic;';
			}

			if (isset($value['heading_text_decoration']) && !empty($value['heading_text_decoration'])) {
				$text_style .= ' text-decoration: underline;';
			}

			if (isset($value['heading_text_color']) && !empty($value['heading_text_color'])) {
				$inner_class .= ' colored';
				$text_style .= ' color: ' . $value['heading_text_color'] . ';';
			}

			if (isset($value['rotating_text_enabled']) && $value['rotating_text_enabled'] == 'yes') {
				$this->add_render_attribute('title-fancy', 'class', 'thegem-heading-animate');
				wp_enqueue_style('thegem-heading-animation');
				wp_enqueue_script('thegem-heading-main');
				wp_enqueue_script('thegem-heading-rotating');
				TheGemHeadingAnimation::instance()->includeInlineJs();

				$rotating_text = '<span class="thegem-heading-rotating-text">' . nl2br(esc_html($value['heading_text'])) . '</span>';

				if (isset($value['words_animation1']) && !empty($value['words_animation1'])) {
					$rotating_text .= ' <span class="thegem-heading-rotating-text" style="opacity: 0; position: absolute;">' . nl2br(esc_html($value['words_animation1'])) . '</span>';
				}
				if (isset($value['words_animation2']) && !empty($value['words_animation2'])) {
					$rotating_text .= ' <span class="thegem-heading-rotating-text" style="opacity: 0; position: absolute;">' . nl2br(esc_html($value['words_animation2'])) . '</span>';
				}
				if (isset($value['words_animation3']) && !empty($value['words_animation3'])) {
					$rotating_text .= ' <span class="thegem-heading-rotating-text" style="opacity: 0; position: absolute;">' . nl2br(esc_html($value['words_animation3'])) . '</span>';
				}
				if (isset($value['words_animation4']) && !empty($value['words_animation4'])) {
					$rotating_text .= ' <span class="thegem-heading-rotating-text" style="opacity: 0; position: absolute;">' . nl2br(esc_html($value['words_animation4'])) . '</span>';
				}
				if (isset($value['words_animation5']) && !empty($value['words_animation5'])) {
					$rotating_text .= ' <span class="thegem-heading-rotating-text" style="opacity: 0; position: absolute;">' . nl2br(esc_html($value['words_animation5'])) . '</span>';
				}
			}

			if (!empty($rotating_text)) {

				$inner_class .= ' thegem-heading-rotating';

				$dataAttrs = '';
				if (!empty($value['rotating_animation_duration']['size'])) {
					$rotating_animation_duration = esc_attr($value['rotating_animation_duration']['size']);
					$dataAttrs = 'data-duration="' . $rotating_animation_duration . '"';
				}

				$title .= '<span class="' . esc_attr(trim($inner_class)) . '"' . (!empty($text_style) ? ' style="' . esc_attr(trim($text_style)) . '"' : '') . ' ' . $dataAttrs . '>' . $rotating_text . '</span> ';
			} else {
				if ($settings['animation_enable'] == 'yes') {
					$title .= TheGemHeadingAnimation::parse(strip_tags($value['heading_text']), $settings, $heading_animation_index, $inner_class, $text_style);
				} else {
					$title .= '<span' . (!empty($inner_class) ? ' class="' . esc_attr(trim($inner_class)) . '"' : '') . (!empty($text_style) ? ' style="' . esc_attr(trim($text_style)) . '"' : '') . '>' . nl2br(esc_html($value['heading_text'])) . '</span> ';
				}
			}
		}

		$icon = $label = '';

		if (!empty($settings['heading_icon']['value'])) {
			ob_start();
			Icons_Manager::render_icon($settings['heading_icon'], ['aria-hidden' => 'true']);
			$icon = '<span class="icon">'.ob_get_clean().'</span>';
		}

		if (!empty($settings['heading_label_text'])) {
			$label = '<span class="label title-h6">'.$settings['heading_label_text'].'</span>';
		}

		if (!empty($icon) || !empty($label)) {
			$this->add_render_attribute('title-fancy', 'class', 'with-label-icon');
			$title = $icon . '<span>' . $title . '</span>' . $label;
		}

		if ($settings['heading_animation'] == TheGemHeadingAnimation::ANIMATION_BACKGROUND_SLIDING) {
			$title = '<span class="thegem-heading-text-wrap"><span class="thegem-heading-text">' . $title . '</span></span>';
		}

		if (!empty($settings['link']['url'])) {
			$this->add_link_attributes('url', $settings['link']);

			$title = sprintf('<a %1$s>%2$s</a>', $this->get_render_attribute_string('url'), $title);
		} else if (!empty($icon) || !empty($label)) {
			$title = '<span class="label-icon-wrap">'.$title.'</span>';
		}

		$content = sprintf('<%1$s %2$s>%3$s</%1$s><style type="text/css">%4$s</style>', Utils::validate_html_tag($settings['header_size']), $this->get_render_attribute_string('title-fancy'), $title, $inline_css);

		if (!empty($settings['show_separator'])) {
			$content .= '<div class="separator"><span></span></div>';
		}

		if ($settings['heading_animation'] == TheGemHeadingAnimation::ANIMATION_BACKGROUND_SLIDING) {
			$content = '<div class="thegem-heading-wrap">' . $content . '</div>';
		}

		echo $content;

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script type="text/javascript">
				window.theGemHeading.initialize();
			</script>
		<?php endif;
	}
}

Plugin::instance()->widgets_manager->register(new TheGem_Animated_Heading());
