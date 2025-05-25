<?php

namespace TheGem_Elementor\Widgets\TemplatePortfolioInfo;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Repeater;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Portfolio Info.
 */

#[\AllowDynamicProperties]
class TheGem_TemplatePortfolioInfo extends Widget_Base {
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-portfolio-info';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'portfolio';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Project Meta/Details', 'thegem');
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
		return ['thegem_portfolio_builder'];
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-portfolio-info';
	}

	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}

		return ' .'.$this->get_widget_wrapper().' .portfolio-info';
	}

	public function get_meta_data()
	{
		$details_status = thegem_get_option('portfolio_project_details');

		if (isset($details_status) && !empty($details_status)) {
			return json_decode(thegem_get_option('portfolio_project_details_data'), true);
		}

		return [];
	}

	public function get_info_content_types() {
		$data = array(
			'date' => __('Date', 'thegem'),
			'cats' => __('Categories', 'thegem'),
			'likes' => __('Likes', 'thegem'),
		);

		$meta_data = $this->get_meta_data();
		if (!empty($meta_data)) {
			foreach($meta_data as $meta) {
				$key = 'meta_thegem_cf_'.str_replace( '-', '_', sanitize_title($meta['title']));
				$value = __($meta['title'], 'thegem');
				$data[$key] = $value;
			}
		}

		return $data;
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		// General Section
		$this->start_controls_section(
			'section_general',
			[
				'label' => __('General', 'thegem'),
			]
		);

		$this->add_control(
			'skin',
			[
				'label' => __('Skin', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'classic' => __('Classic', 'thegem'),
					'modern' => __('Modern', 'thegem'),
					'table' => __('Table', 'thegem'),
				],
				'default' => 'modern',
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'horizontal' => __('Horizontal', 'thegem'),
					'vertical' => __('Vertical', 'thegem'),
				],
				'default' => 'horizontal',
				'condition' => [
					'skin' => ['classic', 'modern'],
				],
			]
		);

		$repeater = new Repeater();

		// Type
		$repeater->add_control(
			'type',
			[
				'label' => __('Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_info_content_types(),
				'default' => 'date',
				'description' => __('Go to the <a href="' . get_site_url() . '/wp-admin/admin.php?page=thegem-theme-options#/single-pages/portfolio" target="_blank">Theme Options -> Portfolio Page</a> to manage your project details fields.', 'thegem')
			]
		);

		// Date
		$repeater->add_control(
			'date_label',
			[
				'label' => __('Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'type' => 'date',
				],
			]
		);

		$repeater->add_control(
			'date_format',
			[
				'label' => __('Date Format', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'1' => __('March 6, 2018 (F j, Y)', 'thegem'),
					'2' => __('2018-03-06 (Y-m-d)', 'thegem'),
					'3' => __('03/06/2018 (m/d/Y)', 'thegem'),
					'4' => __('06/03/2018 (d/m/Y)', 'thegem'),
					'5' => __('06.03.2018 (d.m.Y)', 'thegem'),
					'6' => __('5 days ago (relative)', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'type' => 'date',
				],
			]
		);

		$repeater->add_control(
			'date_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('None', 'thegem'),
					'default' => __('Default', 'thegem'),
					'custom' => __('Custom', 'thegem'),
				],
				'default' => 'default',
				'condition' => [
					'type' => 'date',
				],
			]
		);

		$repeater->add_control(
			'date_icon_select',
			[
				'label' => __('Select Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'type' => 'date',
					'date_icon' => 'custom',
				],
			]
		);

		$repeater->add_control(
			'date_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => 'date',
				],
			]
		);

		/* Categories
		$repeater->add_control(
			'cats_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => 'cats'
				],
			]
		);
		*/

		// Likes
		if(thegem_is_plugin_active('zilla-likes/zilla-likes.php') && function_exists('zilla_likes')) {
			$repeater->add_control(
				'likes_label',
				[
					'label' => __('Label', 'thegem'),
					'type' => Controls_Manager::TEXT,
					'condition' => [
						'type' => 'likes',
					],
				]
			);

			$repeater->add_control(
				'likes_icon',
				[
					'label' => __('Icon', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'' => __('None', 'thegem'),
						'default' => __('Default', 'thegem'),
						//'custom' => __('Custom', 'thegem'),
					],
					'default' => 'default',
					'condition' => [
						'type' => 'likes',
					],
					'selectors_dictionary' => [
						'' => 'display: none;',
					],
					'selectors' => [
						'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .zilla-likes:before' => '{{VALUE}};',
					],

				]
			);

			$repeater->add_control(
				'likes_icon_select',
				[
					'label' => __('Select Icon', 'thegem'),
					'type' => Controls_Manager::ICONS,
					'condition' => [
						'type' => 'likes',
						'likes_icon' => 'custom',
					],
				]
			);
		} else {
			$repeater->add_control(
				'plugin_not_active',
				[
					'type' => Controls_Manager::RAW_HTML,
					'condition' => [
						'type' => 'likes',
					],
					'raw' => __('<div class="thegem-param-alert">To show likes please install and activate <a href="'.get_site_url().'/wp-admin/admin.php?page=install-required-plugins" target="_blank">ZillaLikes plugin</a>.</div>', 'thegem'),
					'content_classes' => 'elementor-descriptor',
				]
			);
		}

		$meta_data = $this->get_meta_data();
		if (!empty($meta_data)) {
			foreach ($meta_data as $meta) {
				$value = 'meta_thegem_cf_' . str_replace('-', '_', sanitize_title($meta['title']));

				$repeater->add_control(
					$value.'_field_type',
					[
						'label' => __('Field Type', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'text' => __('Text', 'thegem'),
							'number' => __('Number', 'thegem'),
						],
						'default' => '',
						'condition' => [
							'type' => $value,
						],
					]
				);

				$repeater->add_control(
					$value.'_field_format',
					[
						'label' => __('Number Format', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'wp_locale' => __('WP Locale', 'thegem'),
							'' => __('Disabled', 'thegem'),
						],
						'default' => 'wp_locale',
						'condition' => [
							'type' => $value,
							$value.'_field_type' => 'number',
						],
					]
				);

				$repeater->add_control(
					$value.'_field_prefix',
					[
						'label' => __('Prefix', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => $value,
							$value.'_field_type' => 'number',
						],
					]
				);

				$repeater->add_control(
					$value.'_field_suffix',
					[
						'label' => __('Suffix', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => $value,
							$value.'_field_type' => 'number',
						],
					]
				);

				$repeater->add_control(
					$value.'_icon',
					[
						'label' => __('Icon', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => __('None', 'thegem'),
							'custom' => __('Custom', 'thegem'),
						],
						'default' => '',
						'condition' => [
							'type' => $value,
						],
					]
				);

				$repeater->add_control(
					$value.'_icon_select',
					[
						'label' => __('Select Icon', 'thegem'),
						'type' => Controls_Manager::ICONS,
						'condition' => [
							'type' => $value,
							$value.'_icon' => 'custom',
						],
					]
				);

				$repeater->add_control(
					$value.'_label',
					[
						'label' => __('Label', 'thegem'),
						'type' => Controls_Manager::SWITCHER,
						'default' => 'yes',
						'return_value' => 'yes',
						'label_on' => __('On', 'thegem'),
						'label_off' => __('Off', 'thegem'),
						'condition' => [
							'type' => $value,
						],
					]
				);

				$repeater->add_control(
					$value.'_label_text',
					[
						'label' => __('Label Text', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => $value,
							$value.'_label' => 'yes',
						],
					]
				);

				$repeater->add_control(
					$value.'_link',
					[
						'label' => __('Link', 'thegem'),
						'type' => Controls_Manager::SWITCHER,
						'default' => '',
						'label_on' => __('On', 'thegem'),
						'label_off' => __('Off', 'thegem'),
						'condition' => [
							'type' => $value,
						],
					]
				);

				$repeater->add_control(
					$value.'_field_link',
					[
						'label' => __('Link Type', 'thegem' ),
						'type' => Controls_Manager::URL,
						'default' => [
							'url' => '#'
						],
						'condition' => [
							'type' => $value,
							$value.'_link' => 'yes',
						],
						'placeholder' => __( 'https://your-link.com', 'thegem' ),
						'show_external' => true,
						'dynamic' => [
							'active' => true,
						],
					]
				);
			}
		}

		$this->add_control(
			'info_content',
			[
				'label' => __('Items', 'thegem'),
				'type' => Controls_Manager::REPEATER,
				'show_label' => false,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => 'Item #1',
						'source' => 'editor',
					],
				]
			]
		);

		$this->end_controls_section();

		// List Section Style
		$this->start_controls_section(
			'section_list_style',
			[
				'label' => __('List', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'list_alignment',
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
				'default' => 'left',
			]
		);

		$this->add_control(
			'list_divider',
			[
				'label' => __('Divider', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'skin' => ['classic', 'table'],
				],
			]
		);

		$this->add_control(
			'list_divider_color',
			[
				'label' => __('Divider Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => ['classic', 'table'],
					'list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item:not(:last-child):after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats .separator' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'list_spacing_horizontal',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => ['classic', 'modern'],
					'layout' => 'horizontal',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item' => 'margin-right: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats .separator' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'list_spacing_vertical',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => ['classic', 'modern'],
					'layout' => 'vertical',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_vertical_spacing',
			[
				'label' => __('Vertical Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => ['table'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-label' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-value' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_horizontal_spacing',
			[
				'label' => __('Horizontal Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => ['table'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-label' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Icon Section Style
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => __('Icon', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .icon i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .zilla-likes:before' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .zilla-likes:before' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing_vertical',
			[
				'label' => __('Vertical Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .icon' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .zilla-likes:before' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .icon' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .zilla-likes' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Text Section Style
		$this->start_controls_section(
			'section_text_style',
			[
				'label' => __('Text', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_layout',
			[
				'label' => __('Text Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'inline' => __('Inline', 'thegem'),
					'vertical' => __('Vertical', 'thegem'),
				],
				'default' => 'inline',
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label' => __('Label', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'label_text_spacing',
			[
				'label' => __('Label Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => ['classic', 'modern'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-label' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_text_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'title-h1' => __('Title H1', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'title-main-menu' => __('Main Menu', 'thegem'),
					'title-text-body' => __('Body', 'thegem'),
					'title-text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'label_text_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'label_text_letter_spacing',
			[
				'label' => __('Letter Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-label' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_text_transform',
			[
				'label' => __('Text Transform', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
				],
				'default' => 'default',
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-label' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_text_typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-label',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'label_text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-label',
			]
		);

		$this->add_control(
			'label_colon',
			[
				'label' => __('Colon', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'value_title_heading',
			[
				'label' => __('Value', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'value_text_spacing',
			[
				'label' => __('Value Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => ['classic', 'modern'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-value' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'value_text_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'title-h1' => __('Title H1', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'title-main-menu' => __('Main Menu', 'thegem'),
					'title-text-body' => __('Body', 'thegem'),
					'title-text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'value_text_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'value_text_letter_spacing',
			[
				'label' => __('Letter Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-value' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .label-after' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'value_text_transform',
			[
				'label' => __('Text Transform', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
				],
				'default' => 'default',
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-value' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .label-after' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'value_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-value' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item a' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .label-after' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'value_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item a:hover .icon' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .zilla-likes:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'value_text_typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-value, {{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .label-after',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'cats_text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .item-value, {{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item .label-after',
			]
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'section_cats_style',
			[
				'label' => __('Categories', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cats_border',
			[
				'label' => __('Border', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('None', 'thegem'),
					'solid' => __('Solid', 'thegem'),
				],
				'default' => 'solid',
				'condition' => [
					'skin' => 'modern',
				],
				'selectors_dictionary' => [
					'' => 'border: 0',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats a' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cats_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => 'modern',
					'cats_border' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats a' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cats_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => 'modern',
					'cats_border' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats a' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cats_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'modern',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cats_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'modern',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats a:not(.readonly):hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cats_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'modern',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cats_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'modern',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats a:not(.readonly):hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cats_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'modern',
					'cats_border' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cats_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'modern',
					'cats_border' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .portfolio-info-item-cats a:not(.readonly):hover' => 'border-color: {{VALUE}};',
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
	public function render()
	{
		$settings = $this->get_settings_for_display();
		$params = array_merge(array(), $settings);
		$uniqid = $this->get_id();

		ob_start();
		$portfolio = thegem_templates_init_portfolio();
		$info_content = $params['info_content'];

		if (empty($portfolio) || empty($info_content)) {
			ob_end_clean();
			echo thegem_templates_close_portfolio(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$skin = 'portfolio-info--' . $params['skin'];
		$layout = 'portfolio-info--' . $params['layout'];
		$alignment = 'portfolio-info--' . $params['list_alignment'];
		$divider = !empty($params['list_divider']) ? 'portfolio-info--divider-show' : 'portfolio-info--divider-hide';
		$colon = !empty($params['label_colon']) ? 'portfolio-info--colon-show' : 'portfolio-info--colon-hide';
		$text_layout = 'portfolio-info--text-' . $params['text_layout'];
		$params['element_class'] = implode(' ', array(
			$this->get_widget_wrapper(),
			$skin,
			$layout,
			$alignment,
			$divider,
			$colon,
			$text_layout
		));
		$label_text_styled = implode(' ', array($params['label_text_style'], $params['label_text_font_weight']));
		$value_text_styled = implode(' ', array($params['value_text_style'], $params['value_text_font_weight']));

		foreach ($info_content as $item) {
			switch ($item['type']) {
				case 'cats':
					$cats = get_the_terms(get_the_ID(), 'thegem_portfolios');
					$cats_list = array();
					if ($cats) {
						foreach ($cats as $cat) {
							if (!empty($item['cats_link'])) {
								//$cats_list[] = '<a href="' . esc_url(get_category_link($cat->name)) . '" title="' . esc_attr(sprintf(__("View all posts in %s", "thegem"), $cat->name)) . '">' . $cat->cat_name . '</a>';
							} else {
								$cats_list[] = '<a href="javascript:void(0)" class="readonly">' . $cat->name . '</a>';
							}
						}
					}

					$cats_output = implode(' <span class="separator"></span> ', $cats_list);

					if ($params['skin'] == 'table') {
						$cats_output = '<div class="item-label"></div><div class="item-value">' . implode('<span class="separator"></span>', $cats_list) . '</div>';
					}

					if (!empty($cats_output)) {
						echo '<div class="portfolio-info-item portfolio-info-item-' . $item['type'] . ' ' . $value_text_styled . '">' . $cats_output . '</div>';
					}

					break;
				case 'date':
					$label_output = $value_output = $format = '';

					if (!empty($item['date_format'])) {
						if ($item['date_format'] == '1') $format = 'F j, Y';
						if ($item['date_format'] == '2') $format = 'Y-m-d';
						if ($item['date_format'] == '3') $format = 'm/d/Y';
						if ($item['date_format'] == '4') $format = 'd/m/Y';
						if ($item['date_format'] == '5') $format = 'd.m.Y';
						if ($item['date_format'] == '6') $format = 'relative';
					}

					if (!empty($item['date_icon']) && $item['date_icon'] == 'custom' && !empty($item['date_icon_select']['value'])) {
						ob_start();
						Icons_Manager::render_icon($item['date_icon_select'], ['aria-hidden' => 'true']);
						$label_output .= '<div class="icon">' . ob_get_clean() . '</div>';
					} else if (!empty($item['date_icon']) && $item['date_icon'] == 'default') {
						$label_output .= '<div class="icon"><i class="icon-default"></i></div>';
					}
					if (!empty($item['date_label'])) {
						$label_output .= '<div class="label-before">' . esc_html($item['date_label']) . '</div>';
					}
					$label_output = '<div class="item-label ' . $label_text_styled . '">' . $label_output . '</div>';

					if (!empty($item['date_link'])) {
						$year = get_the_time('Y');
						$month = get_the_time('m');
						$day = get_the_time('d');

						$date_text = get_the_date($format, $portfolio);
						if($format === 'relative') {
							$date_text = sprintf(__('%s ago'), human_time_diff(get_the_date('U', $portfolio), current_time('timestamp')));
						}
						$value_output .= '<a class="date" href="' . get_day_link($year, $month, $day) . '">' . $date_text . '</a>';
					} else {
						$date_text = get_the_date($format, $portfolio);
						if($format === 'relative') {
							$date_text = sprintf(__('%s ago'), human_time_diff(get_the_date('U', $portfolio), current_time('timestamp')));
						}
						$value_output .= '<div class="date">' . $date_text . '</div>';
					}
					$value_output = '<div class="item-value ' . $value_text_styled . '">' . $value_output . '</div>';

					if (!empty($value_output)) {
						echo '<div class="portfolio-info-item portfolio-info-item-' . $item['type'] . '">' . $label_output . ' ' . $value_output . '</div>';
					}

					break;
				case 'likes':
					if (!function_exists('zilla_likes')) break;

					ob_start();
					zilla_likes();
					$likes_output = '<div class="likes">' . ob_get_clean() . '</div>';

					if (!empty($item['likes_label'])) {
						$likes_output .= '<div class="label-after">' . esc_html($item['likes_label']) . '</div>';
					}

					if ($params['skin'] == 'table') {
						$likes_output = '<div class="item-label"></div><div class="item-value">' . $likes_output . '</div>';
					}

					if (!empty($likes_output)) {
						echo '<div class="portfolio-info-item portfolio-info-item-' . $item['type'] . ' ' . $value_text_styled . '">' . $likes_output . '</div>';
					}

					break;
				default:
					if (empty($this->get_meta_data())) break;

					$meta_data = $this->get_meta_data();
					if (!empty($meta_data)) {
						foreach ($meta_data as $meta) {
							$meta_output = $label_output = $value_output = '';
							$key = '_thegem_cf_' . str_replace('-', '_', sanitize_title($meta['title']));
							$value = 'meta_thegem_cf_' . str_replace('-', '_', sanitize_title($meta['title']));

							if ($item['type'] == $value) {
								if (!empty($item[$value . '_icon']) && $item[$value . '_icon'] == 'custom' && !empty($item[$value . '_icon_select']['value'])) {
									ob_start();
									Icons_Manager::render_icon($item[$value . '_icon_select'], ['aria-hidden' => 'true']);
									$label_output .= '<div class="icon">' . ob_get_clean() . '</div>';
								}

								if (!empty($item[$value . '_label'])) {
									$label_text = !empty($item[$value . '_label_text']) ? $item[$value . '_label_text'] : $meta['title'];
									$label_output .= '<div class="label-before">' . esc_html($label_text) . '<span class="colon">:</span></div>';
								}
								$label_output = '<div class="item-label ' . $label_text_styled . '">' . $label_output . '</div>';

								$meta_value = get_post_meta($portfolio->ID, $key, true);
								if (!empty($meta_value)) {
									if (!empty($item[$value . '_field_type']) && $item[$value . '_field_type'] == 'number') {
										$meta_value = floatval($meta_value);
										$decimal = explode('.', $meta_value);
										$decimal = isset($decimal[1]) ? strlen(($decimal[1])) : 0;
										$decimal = $decimal <= 3 ? $decimal : 3;

										if (!empty($item[$value . '_field_format']) && $item[$value . '_field_format'] == 'wp_locale') {
											$meta_value = number_format_i18n($meta_value, $decimal);
										}

										if (!empty($item[$value . '_field_prefix'])) {
											$meta_value = $item[$value . '_field_prefix'] . '' . $meta_value;
										}

										if (!empty($item[$value . '_field_suffix'])) {
											$meta_value = $meta_value . '' . $item[$value . '_field_suffix'];
										}
									}

									if (!empty($item[$value . '_link'])) {
										if (!empty($item[$value . '_field_link']['url'])) {
											$this->add_link_attributes('field_link', $item[$value . '_field_link']);
										}
										$value_output .= '<a ' . $this->get_render_attribute_string('field_link') . '>' . $meta_value . '</a>';
									} else {
										$value_output .= '<div class="meta">' . $meta_value . '</div>';
									}
								}

								if (!empty($value_output)) {
									$value_output = '<div class="item-value ' . $value_text_styled . '">' . $value_output . '</div>';
									echo '<div class="portfolio-info-item portfolio-info-item-meta">' . $label_output . ' ' . $value_output . '</div>';
								}
							}
						}
					}

					break;
			}
		}

		// Output Widget
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		if (empty($return_html)) {
			echo thegem_templates_close_portfolio(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
		}

		ob_start()

		?>

		<div class="<?= esc_attr($params['element_class']); ?>">
			<div class="portfolio-info"><?= $return_html ?></div>
		</div>

		<?php

		// Print html
		$return_html = ob_get_clean();

		echo thegem_templates_close_portfolio(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplatePortfolioInfo());
