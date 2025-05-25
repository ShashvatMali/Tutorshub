<?php

namespace TheGem_Elementor\Widgets\Countdown;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
	exit;
}


/**
 * Elementor widget for Countdown.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateCountdown extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_COUNTDOWN_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_COUNTDOWN_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_COUNTDOWN_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_COUNTDOWN_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-te-countdown', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_COUNTDOWN_URL . '/assets/css/thegem-countdown.css', array(), null);
		wp_register_script('thegem-te-countdown', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_COUNTDOWN_URL . '/assets/js/thegem-countdown.js', array('jquery', 'raphael', 'odometr'), null, true);

	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-countdown';
	}

	/**
	 * Show in panel.
	 *
	 * Whether to show the widget in the panel or not. By default returns true.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		return get_post_type($post_id) === 'thegem_templates' && (thegem_get_template_type($post_id) === 'header' || thegem_get_template_type($post_id) === 'content');
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'content') {
			return __('Countdown Compact', 'thegem');
		}
		return __('Countdown', 'thegem');
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

	public function get_keywords() {
		return ['countdown', 'number', 'timer', 'time', 'date', 'evergreen'];
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
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'content') return ['thegem_elements'];
		return ['thegem_header_builder'];
	}

	public function get_style_depends() {
		return ['thegem-te-countdown'];
	}

	public function get_script_depends() {
		return ['thegem-te-countdown'];
	}

	/*Show reload button*/
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->content_settings();

		$this->style_settings();

	}

	/**
	 * Content Settings
	 * @access protected
	 */
	protected function content_settings() {

		$this->start_controls_section(
			'content_countdown',
			[
				'label' => __('Countdown', 'thegem'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'due_date',
			[
				'label' => __('Due Date', 'plugin-domain'),
				'type' => Controls_Manager::DATE_TIME,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_labels',
			[
				'label' => __('Labels', 'thegem'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'content_labels_show_days',
			[
				'label' => __('Show Days', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'content_labels_days_label',
			[
				'label' => __('Days Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Days', 'thegem'),
				'condition' => [
					'content_labels_show_days' => 'yes',
				]
			]
		);

		$this->add_control(
			'content_labels_show_hours',
			[
				'label' => __('Show Hours', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'content_labels_hours_label',
			[
				'label' => __('Hours Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Hrs', 'thegem'),
				'condition' => [
					'content_labels_show_hours' => 'yes',
				],
			]
		);

		$this->add_control(
			'content_labels_show_minutes',
			[
				'label' => __('Show Minutes', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'content_labels_minutes_label',
			[
				'label' => __('Minutes Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Min', 'thegem'),
				'condition' => [
					'content_labels_show_minutes' => 'yes',
				],
			]
		);

		$this->add_control(
			'content_labels_show_seconds',
			[
				'label' => __('Show Seconds', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'content_labels_seconds_label',
			[
				'label' => __('Seconds Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Sec', 'thegem'),
				'condition' => [
					'content_labels_show_seconds' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_link',
			[
				'tab' => Controls_Manager::TAB_CONTENT,
				'label' => __('Link', 'thegem'),
			]
		);

		$this->add_control(
			'content_link_url',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::URL,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Settings
	 */
	protected function style_settings() {
		$this->container_style();
		$this->numbers_style();
		$this->labels_style();
		$this->times_style('days_style_', __('Days Style (Custom)', 'thegem'), '.count-1');
		$this->times_style('hours_style_', __('Hours Style (Custom)', 'thegem'), '.count-2');
		$this->times_style('minutes_style_', __('Minutes Style (Custom)', 'thegem'), '.count-3');
		$this->times_style('seconds_style_', __('Seconds Style (Custom)', 'thegem'), '.count-4');
	}

	protected function container_style() {

		$this->start_controls_section(
			'style_container_settings',
			[
				'label' => __('Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'alignment',
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
				],
				'selectors_dictionary' => [
					'left' => 'justify-content: left; text-align: left;',
					'right' => 'justify-content: right; text-align: right;',
					'center' => 'justify-content: center; text-align: center;',
				],
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper' => '{{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'        => 'wrapper_background',
				'label'       => __( 'Background Type', 'thegem' ),
				'types'       => [ 'classic', 'gradient' ],
				'render_type' => 'template',
				'selector'    => '{{WRAPPER}} .countdown-wrapper',
			]
		);

		$this->remove_control('wrapper_background_image');

		$this->add_responsive_control(
			'wrapper_padding',
			[
				'label'          => __( 'Padding', 'thegem' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => [ 'px', '%', 'em' ],
				'selectors'      => [
					'{{WRAPPER}} .countdown-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __('Separator Width', 'thegem'),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .wrap' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Separator Color', 'thegem'),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .wrap' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'container_style_customize_separately',
			[
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Customize separately', 'thegem' ),
				'default'   => '',
				'label_on'  => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
			]
		);

		$this->end_controls_section();
	}

	protected function numbers_style() {

		$this->start_controls_section(
			'numbers_style',
			[
				'label' => __('Numbers Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'numbers_style_weight',
			[
				'label' => __('Numbers Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
			]
		);

		$this->add_control(
			'numbers_style_right_spacing',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __('Right Spacing', 'thegem'),
				'size_units' => ['px', '%', 'em', 'rem'],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -400,
						'max' => 400,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
					'rem' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-countdown .item-count' => 'margin-right: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->start_controls_tabs('number_tabs');

		$this->start_controls_tab(
			'number_tabs_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_typography_normal',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .item-count',
			]
		);

		$this->add_control(
			'number_color_normal',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Color', 'thegem'),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .item-count' => 'color:{{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'number_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_typography_hover',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item:hover .item-count',
			]
		);

		$this->add_control(
			'number_color_hover',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Color', 'thegem'),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item:hover .item-count' => 'color:{{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'number_style_customize_separately',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __('Customize separately', 'thegem'),
				'default' => '',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->end_controls_section();
	}

	protected function labels_style() {

		$this->start_controls_section(
			'labels_style',
			[
				'label' => __('Labels Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->start_controls_tabs('labels_tabs');

		$this->start_controls_tab(
			'labels_tabs_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'labels_typography_normal',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .item-title',
			]
		);

		$this->add_control(
			'labels_color_normal',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Color', 'thegem'),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item .item-title' => 'color:{{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'labels_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'labels_typography_hover',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item:hover .item-title',
			]
		);

		$this->add_control(
			'labels_color_hover',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Color', 'thegem'),
				'selectors' => [
					'{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item:hover .item-title' => 'color:{{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'labels_style_customize_separately',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __('Customize separately', 'thegem'),
				'default' => '',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->end_controls_section();
	}

	protected function times_style($section_id, $section_title, $item_class) {
		/*
		 * Container
		 */
		$this->start_controls_section(
			$section_id,
			[
				'label' => $section_title,
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'container_style_customize_separately',
							'value' => 'yes'
						],
						[
							'name' => 'number_style_customize_separately',
							'value' => 'yes'
						],
						[
							'name' => 'labels_style_customize_separately',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$this->add_control(
			$section_id . 'separator_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __('Separator Width', 'thegem'),
				'render_type' => 'template',
				'size_units' => 'px',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					"{{WRAPPER}} .thegem-te-countdown .countdown-wrapper.countdown-info .countdown-item{$item_class} .wrap" => 'border-width: {{SIZE}}px;',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'container_style_customize_separately',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$this->add_control(
			$section_id . 'separator_color',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Separator Color', 'thegem'),
				'render_type' => 'template',
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .wrap" => 'border-color: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'container_style_customize_separately',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		/*
		 * Numbers
		 */
		$this->add_control(
			$section_id . 'numbers_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __('Numbers', 'thegem'),
				'condition' => ['number_style_customize_separately' => 'yes']
			]
		);

		$this->add_control(
			$section_id . 'numbers_style_right_spacing',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __('Right Spacing', 'thegem'),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'number_style_customize_separately',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				],
				'size_units' => ['px', '%', 'em', 'rem'],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -400,
						'max' => 400,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
					'rem' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-count" => 'margin-right: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->start_controls_tabs(
			$section_id . 'number_tabs',
			[
				'condition' => ['number_style_customize_separately' => 'yes']
			]
		);

		$this->start_controls_tab(
			$section_id . 'number_tabs_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $section_id . 'number_typography_normal',
				'label' => __('Typography', 'thegem'),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-count",
			]
		);

		$this->add_control(
			$section_id . 'number_color_normal',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Color', 'thegem'),
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-count" => 'color: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$section_id . 'number_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $section_id . 'number_typography_hover',
				'label' => __('Typography', 'thegem'),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .item-count",
			]
		);

		$this->add_control(
			$section_id . 'number_color_hover',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Color', 'thegem'),
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .item-count" => 'color: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/*
		 * Label
		 */
		$this->add_control(
			$section_id . 'label',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __('Label', 'thegem'),
				'condition' => ['labels_style_customize_separately' => 'yes']
			]
		);

		$this->start_controls_tabs(
			$section_id . 'labels_tabs', [
				'condition' => ['labels_style_customize_separately' => 'yes']
			]
		);

		$this->start_controls_tab(
			$section_id . 'labels_tabs_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $section_id . 'labels_typography_normal',
				'label' => __('Typography', 'thegem'),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-title",
			]
		);

		$this->add_control(
			$section_id . 'labels_color_normal',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Color', 'thegem'),
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class} .item-title" => 'color: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$section_id . 'labels_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $section_id . 'labels_typography_hover',
				'label' => __('Typography', 'thegem'),
				'selector' => "{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .item-title",
			]
		);

		$this->add_control(
			$section_id . 'labels_color_hover',
			[
				'type' => Controls_Manager::COLOR,
				'label' => __('Color', 'thegem'),
				'selectors' => [
					"{{WRAPPER}} .countdown-wrapper.countdown-info .countdown-item{$item_class}:hover .item-title" => 'color: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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

		$template = __DIR__ . "/templates/preset_html.php";

		$show_link = isset($settings['content_link_url']);
		$countdown_wrap_link = 'countdown-wrap-link';
		if ($show_link) {
			$this->add_link_attributes($countdown_wrap_link, $settings['content_link_url']);
			$this->add_render_attribute($countdown_wrap_link, 'class', 'countdown-wrap-link');
		}

		if (!empty($template) && file_exists($template)) {
			include $template;
		}

	}
}

Plugin::instance()->widgets_manager->register(new TheGem_TemplateCountdown());
