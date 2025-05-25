<?php

namespace TheGem_Elementor\Widgets\TemplatePostComments;

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
class TheGem_TemplatePostComments extends Widget_Base {
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
		return 'thegem-template-post-comments';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-post';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Post Comments', 'thegem');
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
		return ['thegem_single_post_builder'];
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-post-comments';
	}

	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}

		return ' .'.$this->get_widget_wrapper().' .post-comments';
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
			'separator',
			[
				'label' => __('Separator', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_responsive_control(
			'panel_padding',
			[
				'label' => esc_html__('Panel Paddings', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Titles Section
		$this->start_controls_section(
			'section_titles',
			[
				'label' => __('Titles', 'thegem'),
			]
		);

		$this->add_control(
			'heading_title_comments',
			[
				'label' => __('Title Comments', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title_list',
			[
				'label' => __('Title Comments', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'title_list_label',
			[
				'label' => __('Title Comments Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Comments', 'thegem'),
				'condition' => [
					'title_list' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_list_separator',
			[
				'label' => __('Title Comments Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'title_list' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' h2.post-comments__title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_title_form',
			[
				'label' => __('Title Form', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_form',
			[
				'label' => __('Title Form', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'title_form_label',
			[
				'label' => __('Title Form Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Add Comment', 'thegem'),
				'condition' => [
					'title_form' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_form_separator',
			[
				'label' => __('Title Form Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'title_form' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' h3.comment-reply-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_title_preset',
			[
				'label' => __('Titles Preset', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_font_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'title-main-menu' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'title-h4',
			]
		);

		$this->add_control(
			'title_font_weight',
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
			'title_letter_spacing',
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
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' h2.post-comments__title span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' h3.comment-reply-title span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' h3.comment-reply-title a' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_text_transform',
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
				'default' => 'capitalize',
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' h2.post-comments__title span' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' h3.comment-reply-title span' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' h3.comment-reply-title a' => '{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Avatar Section
		$this->start_controls_section(
			'section_avatar',
			[
				'label' => __('Avatar', 'thegem'),
			]
		);

		$this->add_control(
			'avatar',
			[
				'label' => __('Avatar', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'avatar_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'avatar_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'avatar' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__avatar' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Name Section
		$this->start_controls_section(
			'section_name',
			[
				'label' => __('Name', 'thegem'),
			]
		);

		$this->add_control(
			'name',
			[
				'label' => __('Name', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'name_font_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default-title' => __('Default', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'title-main-menu' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'default-title',
				'condition' => [
					'name' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'name' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_letter_spacing',
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
					'name' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__name-author' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'name_text_transform',
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
				'condition' => [
					'name' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__name-author' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'name_link',
			[
				'label' => __('Name Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'name' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// Reply Section
		$this->start_controls_section(
			'section_reply',
			[
				'label' => __('Reply', 'thegem'),
			]
		);

		$this->add_control(
			'reply',
			[
				'label' => __('Reply', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'reply_label',
			[
				'label' => __('Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Reply', 'thegem'),
				'condition' => [
					'reply' => 'yes',
				],
			]
		);

		$this->add_control(
			'reply_font_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default-title' => __('Default', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'title-main-menu' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'default-title',
				'condition' => [
					'reply' => 'yes',
				],
			]
		);

		$this->add_control(
			'reply_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'reply' => 'yes',
				],
			]
		);

		$this->add_control(
			'reply_letter_spacing',
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
					'reply' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__name-reply' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'reply_text_transform',
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
				'condition' => [
					'reply' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__name-reply' => '{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Date Section
		$this->start_controls_section(
			'section_date',
			[
				'label' => __('Date', 'thegem'),
			]
		);

		$this->add_control(
			'date',
			[
				'label' => __('Date', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'date_font_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default-title' => __('Default', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'title-main-menu' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'default-title',
				'condition' => [
					'date' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'date' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_letter_spacing',
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
					'date' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__date' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'date_text_transform',
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
				'condition' => [
					'date' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__date' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'date_link',
			[
				'label' => __('Date Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'date' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// Description Section
		$this->start_controls_section(
			'section_desc',
			[
				'label' => __('Description', 'thegem'),
			]
		);

		$this->add_control(
			'desc',
			[
				'label' => __('Description', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'desc_font_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default-title' => __('Default', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'title-main-menu' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'default-title',
				'condition' => [
					'desc' => 'yes',
				],
			]
		);

		$this->add_control(
			'desc_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'desc' => 'yes',
				],
			]
		);

		$this->add_control(
			'desc_letter_spacing',
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
					'desc' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__desc' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__approved' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'desc_text_transform',
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
				'condition' => [
					'desc' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__desc' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__approved' => '{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Separator Section Style
		$this->start_controls_section(
			'section_separator_style',
			[
				'label' => __('Separator', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'separator_weight',
			[
				'label' => __('Separator Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'separator' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comments__list .comment' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-respond' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'separator' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comments__list .comment' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-respond' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Titles Section Style
		$this->start_controls_section(
			'section_titles_style',
			[
				'label' => __('Titles', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' h2.post-comments__title span' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' h3.comment-reply-title span' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' h3.comment-reply-title a' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .no-comments' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Name Section Style
		$this->start_controls_section(
			'section_name_style',
			[
				'label' => __('Name', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'name' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'name' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__name-author' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__name-author a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'name_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'name' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__name-author a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Reply Section Style
		$this->start_controls_section(
			'section_reply_style',
			[
				'label' => __('Reply', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'reply' => 'yes',
				],
			]
		);

		$this->add_control(
			'reply_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'reply' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__name-reply a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reply_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'reply' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__name-reply a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Date Section Style
		$this->start_controls_section(
			'section_date_style',
			[
				'label' => __('Date', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'date' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'date' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__date' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__date a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'date_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'date' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__date a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Description Section Style
		$this->start_controls_section(
			'section_desc_style',
			[
				'label' => __('Description', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'desc' => 'yes',
				],
			]
		);

		$this->add_control(
			'desc_max_width',
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
				'condition' => [
					'desc' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__desc' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'desc' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__desc' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-comment__approved' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Form Section Style
		$this->start_controls_section(
			'section_form_style',
			[
				'label' => __('Form', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_text_color',
			[
				'label' => __('Label Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label' => __('Input Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form input' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form textarea' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_background_color',
			[
				'label' => __('Input Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form input' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form textarea' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form .checkbox-sign' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_border_color',
			[
				'label' => __('Input Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form input' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form textarea' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form .checkbox-sign' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_marker_color',
			[
				'label' => __('Input Marker Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form .checkbox-sign.checked::before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form input' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form textarea' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'input_checkbox_border_radius',
			[
				'label' => __('Checkbox Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .comment-form .checkbox-sign' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Button Section Style
		$this->start_controls_section(
			'section_btn_style',
			[
				'label' => __('"Send Comment" Button', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'send_btn_size',
			[
				'label' => __('Button Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'tiny' => __('Tiny', 'thegem'),
					'small' => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem'),
					'giant' => __('Giant', 'thegem'),
				],
				'default' => 'small',
			]
		);

		$this->add_control(
			'send_btn_alignment',
			[
				'label' => __('Button Alignment', 'thegem'),
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
					'fullwidth' => [
						'title' => __('Fullwidth', 'thegem'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'left',
			]
		);

		$this->add_control(
			'send_btn_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 6,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .form-submit .gem-button' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'send_btn_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .form-submit .gem-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'send_btn_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .form-submit .gem-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'send_btn_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .form-submit .gem-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'send_btn_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .form-submit .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'send_btn_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .form-submit .gem-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'send_btn_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .form-submit .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'send_btn_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .form-submit .gem-button:hover' => 'border-color: {{VALUE}};',
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

		$comments_count = get_comments_number( $single_post );
		if (empty($single_post) || ((thegem_get_template_type(get_the_ID()) === 'single-post' || \Elementor\Plugin::$instance->editor->is_edit_mode()) && !comments_open() && $comments_count == '0')) {
			ob_end_clean();
			echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$separator = empty($params['separator']) ? 'post-comments--separator-hide' : '';
		$title_form = empty($params['title_form']) ? 'post-comments--title-hide' : '';
		$btn_alignment = !empty($params['send_btn_alignment']) ? 'post-comments-btn--'.$params['send_btn_alignment'] : '';
		$params['element_class'] = implode(' ', array(
			$this->get_widget_wrapper(), $separator, $title_form, $btn_alignment,
		));

		$params['title_styled'] = implode(' ', array($params['title_font_style'], $params['title_font_weight']));
		$params['name_styled'] = implode(' ', array($params['name_font_style'], $params['name_font_weight']));
		$params['reply_styled'] = implode(' ', array($params['reply_font_style'], $params['reply_font_weight']));
		$params['date_styled'] = implode(' ', array($params['date_font_style'], $params['date_font_weight']));
		$params['desc_styled'] = implode(' ', array($params['desc_font_style'], $params['desc_font_weight']));

		global $thegem_comments_params;
		$thegem_comments_params = $params;

		?>

        <div class="<?= esc_attr($params['element_class']); ?>">
	        <?php comments_template('/comments.php', $thegem_comments_params); unset($thegem_comments_params); ?>
        </div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplatePostComments());
