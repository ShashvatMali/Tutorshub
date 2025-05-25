<?php

namespace TheGem_Elementor\Widgets\TemplatePostNavigation;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Blog Title.
 */

#[\AllowDynamicProperties]
class TheGem_TemplatePostNavigation extends Widget_Base {
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
		return 'thegem-template-post-navigation';
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
		return get_post_type($post_id) !== 'thegem_templates' || thegem_get_template_type($post_id) === 'single-post' || thegem_get_template_type($post_id) === 'title';
	}
	
	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Post Navigation', 'thegem');
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
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'title') return ['thegem_title_area_builder'];
		if(get_post_type($post_id) === 'thegem_templates') return ['thegem_single_post_builder'];
		return ['thegem_single_post'];
	}
	
	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}
	
	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-post-navigation';
	}
	
	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}
		
		return ' .'.$this->get_widget_wrapper().' .post-navigation';
	}
	
	public function is_first_post() {
		return empty(get_previous_post());
	}
	
	public function is_last_post() {
		return empty(get_next_post());
	}
	
	public function get_taxonomy_list() {
		$data = array();
		$taxonomies = get_taxonomies(['object_type' => ['post']], 'objects');
		
		if (!empty($taxonomies)) {
			foreach ($taxonomies as $taxonomy) {
				$data[$taxonomy->name] = ucwords($taxonomy->label);
			}
		}
		
		return $data;
	}
	
	public function get_default_taxonomy() {
		$taxonomies = $this->get_taxonomy_list();
		if (empty($taxonomies)) return;
		
		return array_keys($taxonomies)[0];
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
			'navigate_by',
			[
				'label' => __('Navigate by', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'chronology' => __('Chronology', 'thegem'),
					'taxonomy' => __('Same Taxonomy', 'thegem'),
				],
				'default' => 'chronology',
			]
		);
		
		$this->add_control(
			'taxonomy',
			[
				'label' => __('Select Taxonomy', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_taxonomy_list(),
				'default' => $this->get_default_taxonomy(),
				'condition' => [
					'navigate_by' => 'taxonomy',
				],
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
					'justified' => [
						'title' => __('Justified', 'thegem'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'justified',
			]
		);
		
		$this->add_control(
			'max_width',
			[
				'label' => __('Max Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' a' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .nav-previous' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .nav-next' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Label Section
		$this->start_controls_section(
			'section_label',
			[
				'label' => __('Label', 'thegem'),
			]
		);
		
		$this->add_control(
			'label',
			[
				'label' => __('Label', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'label_prev',
			[
				'label' => __('Previous Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Previous Post', 'thegem'),
				'condition' => [
					'label' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'label_next',
			[
				'label' => __('Next Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Next Post', 'thegem'),
				'condition' => [
					'label' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'label_text_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
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
				'default' => 'title-text-body',
				'condition' => [
					'label' => 'yes',
				],
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
				'condition' => [
					'label' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'label_text_letter_spacing',
			[
				'label' => __('Letter Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'label' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-label span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'label_text_transform',
			[
				'label' => __('Text Transform', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'label' => 'yes',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-label span' => '{{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Title Section
		$this->start_controls_section(
			'section_title',
			[
				'label' => __('Post Title', 'thegem'),
			]
		);
		
		$this->add_control(
			'title',
			[
				'label' => __('Post Title', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'title_text_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
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
				'default' => 'title-h5',
				'condition' => [
					'title' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'title_text_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'title' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'title_text_letter_spacing',
			[
				'label' => __('Letter Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'title' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-title span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'title_text_transform',
			[
				'label' => __('Text Transform', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
				],
				'default' => 'none',
				'condition' => [
					'title' => 'yes',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-title span' => '{{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Arrows Section
		$this->start_controls_section(
			'section_arrows',
			[
				'label' => __('Arrows', 'thegem'),
			]
		);
		
		$this->add_control(
			'arrows',
			[
				'label' => __('Arrows', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'arrow_prev',
			[
				'label' => __('Previous Arrow', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'arrow_next',
			[
				'label' => __('Next Arrow', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'arrows_type',
			[
				'label' => __('Arrows Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'simple' => __('Simple', 'thegem'),
					'round' => __('Round Border', 'thegem'),
					'square' => __('Square Border', 'thegem'),
				],
				'default' => 'simple',
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'arrows_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'arrows' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .nav-previous .meta-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .nav-next .meta-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'arrows_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .meta-icon' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'arrows_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .meta-icon' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Label Section Style
		$this->start_controls_section(
			'section_label_style',
			[
				'label' => __('Label', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'label' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-label span',
				'condition' => [
					'label' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'name_text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-label span',
				'condition' => [
					'label' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'label_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'label' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-label span' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'label_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'label' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' a:hover .post-label span' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Title Section Style
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __('Title', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'title' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-title span',
				'condition' => [
					'title' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-title span',
				'condition' => [
					'title' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'title_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'title' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-title span' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'title_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'title' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' a:hover .post-title span' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Arrows Section Style
		$this->start_controls_section(
			'section_arrows_style',
			[
				'label' => __('Arrows', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);
  
		$this->add_control(
			'arrows_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .meta-icon' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
  
		$this->add_control(
			'arrows_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .meta-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'arrows_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .meta-icon' => 'color: {{VALUE}};',
				],
			]
		);
  
		$this->add_control(
			'arrows_color_hover',
			[
				'label' => __('Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' a:hover .meta-icon' => 'color: {{VALUE}};',
				],
			]
		);
  
		$this->add_control(
			'arrows_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .meta-icon' => 'background-color: {{VALUE}};',
				],
			]
		);
  
		$this->add_control(
			'arrows_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' a:hover .meta-icon' => 'background-color: {{VALUE}};',
				],
			]
		);
  
		$this->add_control(
			'arrows_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .meta-icon' => 'border-color: {{VALUE}};',
				],
			]
		);
  
		$this->add_control(
			'arrows_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' a:hover .meta-icon' => 'border-color: {{VALUE}};',
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
		$params = array_merge(array(), $settings);
		
		ob_start();
		$single_post = thegem_templates_init_post();
  
		if (empty($single_post)) {
			ob_end_clean();
			echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}
		
		$is_taxonomy = !empty($params['navigate_by']) && $params['navigate_by'] == 'taxonomy' ? true : false;
		$taxonomy = !empty($params['taxonomy']) ? $params['taxonomy'] : 'category';
  
		$alignment = 'post-nav--'.$params['alignment'];
		$last_post = $this->is_first_post() || $this->is_last_post() ? 'post-nav--hide-gaps' : '';
		$label = empty($params['label']) ? 'post-label--hide' : '';
		$title = empty($params['title']) ? 'post-title--hide' : '';
		$arrows = empty($params['arrows']) ? 'post-arrows--hide' : '';
		$arrow_prev = empty($params['arrow_prev']) ? 'post-arrows--prev-hide' : '';
		$arrow_next = empty($params['arrow_next']) ? 'post-arrows--next-hide' : '';
		$arrows_type = 'post-arrows--'.$params['arrows_type'];
		$params['element_class'] = implode(' ', array(
			$this->get_widget_wrapper(),
            $alignment, $last_post, $label, $title, $arrows, $arrow_prev, $arrow_next, $arrows_type,
		));
  
		$prev_text_output = $next_text_output = '';
		$label_text_styled = implode(' ', array($params['label_text_style'], $params['label_text_font_weight']));
		$title_text_styled = implode(' ', array($params['title_text_style'], $params['title_text_font_weight']));
		
		$prev_text_output .= !empty($params['arrows']) && !empty($params['arrow_prev']) ? '<i class="meta-icon"></i>' : '';
		$prev_text_output .= !empty($params['label']) || !empty($params['title']) ? '<span class="meta-nav" aria-hidden="true">' : '';
		$prev_text_output .= !empty($params['label']) ? '<span class="post-label"><span class="'.$label_text_styled.'">'.$params['label_prev'].'</span></span>' : '';
		$prev_text_output .= !empty($params['title']) ? '<span class="post-title"><span class="'.$title_text_styled.'">%title</span></span>' : '';
		$prev_text_output .= !empty($params['label']) || !empty($params['title']) ?'</span>' : '';
		
		$next_text_output .= !empty($params['label']) || !empty($params['title']) ? '<span class="meta-nav" aria-hidden="true">' : '';
		$next_text_output .= !empty($params['label']) ? '<span class="post-label"><span class="'.$label_text_styled.'">'.$params['label_next'].'</span></span>' : '';
		$next_text_output .= !empty($params['title']) ? '<span class="post-title"><span class="'.$title_text_styled.'">%title</span></span>' : '';
		$next_text_output .= !empty($params['label']) || !empty($params['title']) ? '</span>' : '';
		$next_text_output .= !empty($params['arrows']) && !empty($params['arrow_next']) ? '<i class="meta-icon"></i>' : '';
		
		?>

        <div class="<?= esc_attr($params['element_class']); ?>">
	        <?php
                the_post_navigation( array(
                    'next_text' => $next_text_output,
                    'prev_text' => $prev_text_output,
	                'in_same_term' => $is_taxonomy,
	                'taxonomy' => $taxonomy
                ) );
	        ?>
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplatePostNavigation());
