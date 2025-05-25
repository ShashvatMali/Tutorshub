<?php

namespace TheGem_Elementor\Widgets\InfoBox;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;

if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for InfoBox.
 */
#[\AllowDynamicProperties]
class TheGem_InfoBox extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_INFOBOX_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_INFOBOX_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_INFOBOX_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_INFOBOX_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}
		wp_register_style('thegem-te-infobox', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_INFOBOX_URL . '/css/infobox.css', array(), null);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-infobox';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'header';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Infobox', 'thegem');
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
		return ['thegem_header_builder'];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-te-infobox'];
		}
		return ['thegem-te-infobox'];
	}

	/*Show reload button*/
	public function is_reload_preview_required() {
		return true;
	}


	/**
	 * Retrieve the value setting
	 * @access public
	 *
	 * @param string $control_id Control id
	 * @param string $control_sub Control value name (size, unit)
	 *
	 * @return string
	 */
	public function get_val($control_id, $control_sub = null) {
		if (empty($control_sub)) {
			return $this->get_settings()[$control_id];
		} else {
			return $this->get_settings()[$control_id][$control_sub];
		}
	}

	/**
	 * Create presets options for Select
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_presets_options() {
		$out = array(
			'tiny' => __('Tiny', 'thegem'),
			'highlighted' => __('Highlighted', 'thegem'),
			'classic' => __('Classic', 'thegem'),
			'right-icon-classic' => __('Right Icon (Classic)', 'thegem'),
			'right-icon-tiny' => __('Right Icon (Tiny)', 'thegem'),
		);
		return $out;
	}

	/**
	 * Get default presets options for Select
	 *
	 * @param int $index
	 *
	 * @access protected
	 * @return string
	 */
	protected function set_default_presets_options() {
		return 'tiny';
	}

	/**
	 * Get the list of all local section templates
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_local_section_templates() {
		$items = Plugin::instance()->templates_manager->get_source('local')->get_items();
		if (!empty($items)) {
			$items = wp_list_filter($items, ['type' => 'section']);
			$items = wp_list_pluck($items, 'title', 'template_id');
			return $items;
		}
		return [];
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'thegem_elementor_preset',
			[
				'label' => __('Skin', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_subtitle_settings',
			[
				'label' => __('Title & Subtitle', 'thegem'),
			]
		);

		$this->add_control(
			'source',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __('Content Source', 'thegem'),
				'default' => 'editor',
				'separator' => 'before',
				'options' => [
					'editor' => __('Editor', 'thegem'),
					'template' => __('Template', 'thegem'),
				]
			]
		);

		$this->add_control(
			'template',
			[
				'label' => __('Section Template', 'thegem'),
				'placeholder' => __('Select a section template for as tab content', 'thegem'),
				'label_block' => true,
				'description' => sprintf(
					__('Wondering what is section template or need to create one? Please click %1$shere%2$s ', 'thegem'),
					'<a target="_blank" href="' . esc_url(admin_url('/edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=section')) . '">',
					'</a>'
				),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_local_section_templates(),
				'condition' => [
					'source' => 'template',
				]
			]
		);

		$this->add_control(
			'heading_block_title',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'content_textbox_title',
			[
				'label' => __('Title Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('+123 4567 890', 'thegem'),
				'placeholder' => __('Type your title here', 'thegem'),
				'label_block' => 'true',
				'condition' => [
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'content_textbox_title_html_tag',
			[
				'label' => __('Title Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'title-default' => __('Main Menu', 'thegem'),
					'title-h1' => __('Title H1', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'text-body-tiny',
				'condition' => [
					'source' => 'editor',
				],
			]
		);

		$this->add_control(
			'content_textbox_title_html_tag_weight',
			[
				'label' => __('Title Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'bold' => __('Bold', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'condition' => [
					'source' => 'editor'
				],
			]
		);

		$this->add_control(
			'content_textbox_title_html_tag_style',
			[
				'label' => __('Title Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'italic' => __('Italic', 'thegem'),
				],
				'selectors_dictionary' => [
					'default' => '',
					'italic' => 'font-style: italic;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-title' => '{{VALUE}};',
				],
				'condition' => [
					'source' => 'editor',
				],
			]
		);

		$this->add_control(
			'content_textbox_title_html_tag_align',
			[
				'label' => __('Title Align', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
					'center' => __('Center', 'thegem'),
					'justify' => __('Justify', 'thegem'),
				],
				'selectors_dictionary' => [
					'default' => '',
					'left' => 'text-align: left;',
					'right' => 'text-align: right;',
					'center' => 'text-align: center;',
					'justify' => 'text-align: justify;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-title' => '{{VALUE}};',
				],
				'condition' => [
					'source' => 'editor',
				],
			]
		);

		$this->add_control(
			'heading_block_subtitle',
			[
				'label' => __('Subtitle', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'source' => 'editor',
				],
			]
		);

		$this->add_control(
			'content_textbox_subtitle',
			[
				'label' => __('Subtitle Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('Type your title here', 'thegem'),
				'label_block' => 'true',
				'condition' => [
					'source' => 'editor',
				],
			]
		);

		$this->add_control(
			'content_textbox_subtitle_html_tag',
			[
				'label' => __('Subtitle Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'title-default' => __('Main Menu', 'thegem'),
					'title-h1' => __('Title H1', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'text-body-tiny',
				'condition' => [
					'source' => 'editor',
				],
			]
		);

		$this->add_control(
			'content_textbox_subtitle_html_tag_weight',
			[
				'label' => __('Subtitle Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
					'bold' => __('Bold', 'thegem'),
				],
				'selectors_dictionary' => [
					'default' => '',
					'light' => 'font-weight: 300;',
					'bold' => 'font-weight: 700;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-subtitle' => '{{VALUE}};',
				],
				'condition' => [
					'source' => 'editor',
				],
			]
		);

		$this->add_control(
			'content_textbox_subtitle_html_tag_style',
			[
				'label' => __('Subtitle Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'italic' => __('Italic', 'thegem'),
				],
				'selectors_dictionary' => [
					'default' => '',
					'italic' => 'font-style: italic;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-subtitle' => '{{VALUE}};',
				],
				'condition' => [
					'source' => 'editor',
				],
			]
		);

		$this->add_control(
			'content_textbox_subtitle_html_tag_align',
			[
				'label' => __('Subtitle Align', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
					'center' => __('Center', 'thegem'),
					'justify' => __('Justify', 'thegem'),
				],
				'selectors_dictionary' => [
					'default' => '',
					'left' => 'text-align: left;',
					'right' => 'text-align: right;',
					'center' => 'text-align: center;',
					'justify' => 'text-align: justify;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-subtitle' => '{{VALUE}};',
				],
				'condition' => [
					'source' => 'editor',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'heading_block_icon_image',
			[
				'label' => __('Icon / Image', 'thegem'),
			]
		);

		$this->add_control(
			'content_textbox_icon_image_select',
			[
				'label' => __('Icon / Image', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'none' => [
						'title' => __('None', 'thegem'),
						'icon' => 'eicon-ban',
					],
					'image' => [
						'title' => __('Image', 'thegem'),
						'icon' => 'fa fa-picture-o',
					],
					'icon' => [
						'title' => __('Icon', 'thegem'),
						'icon' => 'eicon-star',
					],
				],
				'default' => 'icon',
			]
		);

		$this->add_control(
			'content_textbox_graphic_image',
			[
				'label' => __('Choose Image', 'thegem'),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				],
				'show_label' => false,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'content_textbox_graphic_image',
				'default' => 'thumbnail',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
					'content_textbox_graphic_image[id]!' => '',
				],
			]
		);

		$this->add_control(
			'content_textbox_selected_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'tgh-icon phone',
					'library' => 'thegem-hbi',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				],
			]
		);

		$this->add_control(
			'content_textbox_icon_image_link_to',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __('None', 'thegem'),
					'file' => __('Media File', 'thegem'),
					'custom' => __('Custom URL', 'thegem'),
				],
			]
		);

		$this->add_control(
			'content_textbox_icon_image_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('https://your-link.com', 'thegem'),
				'condition' => [
					'content_textbox_icon_image_link_to' => 'custom',

				],
				'show_label' => false,
			]
		);

		$this->add_control(
			'content_textbox_icon_image_open_lightbox',
			[
				'label' => __('Lightbox', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'yes',
				'options' => [
					'yes' => __('Yes', 'thegem'),
					'no' => __('No', 'thegem'),
				],
				'condition' => [
					'content_textbox_icon_image_link_to' => 'file',

				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'heading_block_textbox_link',
			[
				'label' => __('Textbox Link', 'thegem'),
			]
		);

		$this->add_control(
			'content_textbox_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->add_styles_controls($this);

	}

	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls($control) {

		$this->control = $control;

		/*Textbox Container Styles*/
		$this->textbox_container_styles($control);

		/* TextBox Title and Subtitle Styles */
		$this->textbox_title_subtitle_styles($control);

		/* TextBox Icon Image Styles */
		$this->textbox_icon_image_styles($control);

	}

	/**
	 * TextBox Container Styles
	 * @access protected
	 */
	protected function textbox_container_styles($control) {


		$control->start_controls_section(
			'textbox_container',
			[
				'label' => __('Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->start_controls_tabs('textbox_container_tabs');
		$control->start_controls_tab('textbox_container_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_container_bg_color',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .thegem-te-infobox-content',
			]
		);

		$control->add_responsive_control(
			'textbox_container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'textbox_container_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-infobox-content',
			]
		);

		$control->add_responsive_control(
			'textbox_container_align',
			[
				'label' => __('Content Align', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
				'label_block' => false,
				'separator' => 'before',
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'text-align: left;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;',

				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content, {{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'textbox_container_top_shape',
			[
				'label' => __('Top Shape', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'none',
				'options' => [
					'none' => __('None', 'thegem'),
					'flag' => __('Flag', 'thegem'),
					'shield' => __('Shield', 'thegem'),
					'ticket' => __('Ticket', 'thegem'),
					'sentence' => __('Sentence', 'thegem'),
					'note-1' => __('Note 1', 'thegem'),
					'note-2' => __('Note 2', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'textbox_container_top_shape_color',
			[
				'label' => __('Top Shape Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-top svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'textbox_container_top_shape!' => 'none',
				],
			]
		);

		$control->add_control(
			'textbox_container_bottom_shape',
			[
				'label' => __('Bottom Shape', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'none',
				'options' => [
					'none' => __('None', 'thegem'),
					'flag' => __('Flag', 'thegem'),
					'shield' => __('Shield', 'thegem'),
					'ticket' => __('Ticket', 'thegem'),
					'sentence' => __('Sentence', 'thegem'),
					'note-1' => __('Note 1', 'thegem'),
					'note-2' => __('Note 2', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'textbox_container_bottom_shape_color',
			[
				'label' => __('Bottom Shape Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-bottom svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'textbox_container_bottom_shape!' => 'none',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-infobox-content',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('textbox_container_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_container_bg_color_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content',
			]
		);

		$control->add_responsive_control(
			'textbox_container_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content' => 'border-color: {{VALUE}};',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_container_shadow_hover',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * TextBox Title and Subtitle Styles
	 * @access protected
	 */
	protected function textbox_title_subtitle_styles($control) {

		$control->start_controls_section(
			'textbox_block_title_subtitle',
			[
				'label' => __('Title & Subtitle Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'textbox_content_title_heading',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('textbox_content_title_tabs');
		$control->start_controls_tab('textbox_content_title_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'textbox_content_title_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox .thegem-te-infobox-content .thegem-te-infobox-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_title_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox .thegem-te-infobox-content .thegem-te-infobox-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'textbox_content_title_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox .thegem-te-infobox-content .thegem-te-infobox-title' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'textbox_content_title_typography',
				'selector' => '{{WRAPPER}} .thegem-te-infobox .thegem-te-infobox-content .thegem-te-infobox-title',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('textbox_content_title_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'textbox_content_title_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .thegem-te-infobox-title' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'textbox_content_title_typography_hover',
				'selector' => '{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .thegem-te-infobox-title',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'textbox_content_subtitle_heading',
			[
				'label' => __('Subtitle', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('textbox_content_subtitle_tabs');
		$control->start_controls_tab('textbox_content_subtitle_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'textbox_content_subtitle_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox .thegem-te-infobox-content .thegem-te-infobox-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_subtitle_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox .thegem-te-infobox-content .thegem-te-infobox-subtitle' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'textbox_content_subtitle_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox .thegem-te-infobox-content .thegem-te-infobox-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'textbox_content_subtitle_typography',
				'selector' => '{{WRAPPER}} .thegem-te-infobox .thegem-te-infobox-content .thegem-te-infobox-subtitle',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('textbox_content_subtitle_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'textbox_content_subtitle_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .thegem-te-infobox-subtitle' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'textbox_content_subtitle_typography_hover',
				'selector' => '{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .thegem-te-infobox-subtitle',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * TextBox Icon Image Styles
	 * @access protected
	 */
	protected function textbox_icon_image_styles($control) {

		$control->start_controls_section(
			'textbox_block_icon_image',
			[
				'label' => __('Icon / Image Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_textbox_icon_image_select!' => 'none',
				],
			]
		);

		$control->add_control(
			'textbox_content_icon_image_heading',
			[
				'label' => __('Icon / Image', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'textbox_content_icon_image_vertical_position',
			[
				'label' => __('Vertical Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'icon-top' => __('Top', 'thegem'),
					'icon-bottom' => __('Bottom', 'thegem'),
				],
			]
		);

		$control->add_control(
			'textbox_content_icon_image_horizontal_position',
			[
				'label' => __('Horizontal Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'left',
				'options' => [
					'right' => __('Right', 'thegem'),
					'center' => __('Center', 'thegem'),
					'left' => __('Left', 'thegem'),
				],
			]
		);

		$this->add_control(
			'textbox_content_icon_image_text_wrapping',
			[
				'label' => __('Text Wrapping', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => [
					'none' => __('None', 'thegem'),
					'inline' => __('Inline, no wrap', 'thegem'),
					'wrap' => __('Wrap Text', 'thegem'),
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'textbox_content_icon_image_horizontal_position',
							'operator' => '!=',
							'value' => 'center',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 16,
					'unit' => 'px'
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon i' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon svg' => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image img, {{WRAPPER}} .thegem-te-infobox-content a .gem-image img' => 'width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};height: auto;',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image span, {{WRAPPER}} .thegem-te-infobox-content a .gem-image span' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-infobox-content a .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'textbox_content_icon_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'none',
				'options' => [
					'none' => __('None', 'thegem'),
					'solid' => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image span' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'textbox_content_icon_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image span' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'default' => [
					'top' => '0',
					'right' => '10',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_content_icon_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon',
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'textbox_content_img_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-infobox-content .gem-image span',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->start_controls_tabs('textbox_content_icon_image_tabs');
		$control->start_controls_tab('textbox_content_icon_image_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_content_icon_bg_color',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon',
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_control(
			'textbox_content_img_opacity',
			[
				'label' => __('Opacity', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'step' => '0.1',
				'min' => '0',
				'max' => '1',
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image img' => 'opacity: {{VALUE}} !important;',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'textbox_content_img_css_filters',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-infobox-content .gem-image img',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->add_control(
			'textbox_content_img_blend_mode',
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
					'color-burn' => 'Color Burn',
					'hue' => 'Hue',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'exclusion' => 'Exclusion',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-image img' => 'mix-blend-mode: {{VALUE}}',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_rotate',
			[
				'label' => __('Rotate Icon', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon i, {{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_box_rotate',
			[
				'label' => __('Rotate Box', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('textbox_content_icon_image_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_content_icon_bg_color_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon',
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .gem-image span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_color_hover',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_rotate_hover',
			[
				'label' => __('Rotate Icon', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon i, {{WRAPPER}} .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_responsive_control(
			'textbox_content_icon_box_rotate_hover',
			[
				'label' => __('Rotate Box', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .gem-texbox-icon-image-wrapper .elementor-icon' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'icon',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'textbox_content_img_overlay_color_hover',
				'label' => __('Overlay Color Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'default' => 'classic',
				'fields_options' => [
					'background' => [
						'label' => _x('Overlay Color Type', 'Background Control', 'thegem'),
					],
					'color' => [
						'default' => thegem_get_option('styled_elements_background_color'),
					],
				],
				'selector' => '{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .gem-image span::before',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->remove_control('textbox_content_img_overlay_color_hover_image');

		$control->add_control(
			'textbox_content_img_opacity_opacity_hover',
			[
				'label' => __('Overlay Opacity', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'default' => '0.5',
				'step' => '0.1',
				'min' => '0',
				'max' => '1',
				'selectors' => [
					'{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .gem-image > span::before' => 'opacity: {{VALUE}} !important;',
				],
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'textbox_content_img_css_filters_hover',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-infobox:hover .thegem-te-infobox-content .gem-image img',
				'condition' => [
					'content_textbox_icon_image_select' => 'image',
				]
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}


	/**
	 * Retrieve image widget link URL for Icon Image
	 *
	 * @param array $settings
	 *
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 * @since 1.0.0
	 * @access private
	 *
	 */
	private function get_icon_image_link_url($settings) {
		if ('none' === $settings['content_textbox_icon_image_link_to']) {
			return false;
		}

		if ('custom' === $settings['content_textbox_icon_image_link_to']) {
			if (empty($settings['content_textbox_icon_image_link']['url'])) {
				return false;
			}

			return $settings['content_textbox_icon_image_link'];
		}

		return [
			'url' => !empty($settings['content_textbox_graphic_image']['url']) ? $settings['content_textbox_graphic_image']['url'] : '',
		];
	}

	/**
	 * Retrieve image textbox link URL.
	 *
	 * @param array $settings
	 *
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 * @since 1.0.0
	 * @access private
	 *
	 */
	private function get_textbox_link_url($settings) {

		if (empty($settings['content_textbox_link']['url'])) {
			return false;
		}

		return [
			'url' => $settings['content_textbox_link']['url'],
			'nofollow' => $settings['content_textbox_link']['nofollow'],
			'is_external' => $settings['content_textbox_link']['is_external'],
		];
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

		$preset_path = __DIR__ . '/templates/preset_html.php';

		if (!empty($preset_path) && file_exists($preset_path)) {
			include($preset_path);
		}
	}

	public function get_preset_data() {

		return array(
			'tiny' => array(
				'content_textbox_title' => '+123 4567 890',
				'content_textbox_subtitle' => '',
				'textbox_container_padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_horizontal_position' => 'left',
				'textbox_container_align' => 'left',
				'textbox_content_icon_margin' => ['top' => '-2', 'right' => '8', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_text_wrapping' => 'inline',
				'textbox_content_icon_size' => ['size' => 16, 'unit' => 'px'],
				'content_textbox_title_html_tag' => 'text-body-tiny',
				'content_textbox_title_html_tag_weight' => 'light',
				'content_textbox_subtitle_html_tag' => 'text-body-tiny',
				'textbox_content_title_bottom_spacing' => ['size' => 0, 'unit' => 'px'],
				'textbox_content_title_top_spacing' => ['size' => 0, 'unit' => 'px'],
				'content_textbox_selected_icon' => ['value' => 'tgh-icon phone', 'library' => 'thegem-hbi'],
			),
			'highlighted' => array(
				'content_textbox_title' => 'Call us now',
				'content_textbox_subtitle' => '+123 4567 890',
				'textbox_container_padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_horizontal_position' => 'left',
				'textbox_container_align' => 'left',
				'textbox_content_icon_color' => thegem_get_option('main_menu_level1_hover_color'),
				'textbox_content_icon_margin' => ['top' => '-2', 'right' => '8', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_text_wrapping' => 'inline',
				'textbox_content_icon_size' => ['size' => 48, 'unit' => 'px'],
				'content_textbox_title_html_tag' => 'text-body-tiny',
				'content_textbox_title_html_tag_weight' => 'light',
				'textbox_content_title_color' => thegem_get_option('body_color').'80',
				'content_textbox_subtitle_html_tag' => 'styled-subtitle',
				'textbox_content_subtitle_typography_typography' => 'custom',
				'textbox_content_subtitle_typography_line_height' => ['size' => 1, 'unit' => 'em'],
				'textbox_content_title_bottom_spacing' => ['size' => 0, 'unit' => 'px'],
				'textbox_content_title_top_spacing' => ['size' => 0, 'unit' => 'px'],
				'content_textbox_selected_icon' => ['value' => 'tgh-icon phone', 'library' => 'thegem-hbi'],
			),
			'classic' => array(
				'content_textbox_title' => 'FREE SHIPPING & RETURN',
				'content_textbox_subtitle' => 'Free shipping on all orders over $99',
				'textbox_container_padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_horizontal_position' => 'left',
				'textbox_container_align' => 'left',
				'textbox_content_icon_margin' => ['top' => '-2', 'right' => '20', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_text_wrapping' => 'inline',
				'textbox_content_icon_size' => ['size' => 30, 'unit' => 'px'],
				'textbox_content_icon_padding' => ['size' => 7, 'unit' => 'px'],
				'textbox_content_icon_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
				'textbox_content_icon_bg_color_background' => 'classic',
				'textbox_content_icon_bg_color_color' => thegem_get_option('styled_elements_background_color'),
				'content_textbox_title_html_tag' => 'title-default',
				'content_textbox_title_html_tag_weight' => 'bold',
				'content_textbox_subtitle_html_tag' => 'text-body-tiny',
				'textbox_content_title_bottom_spacing' => ['size' => 0, 'unit' => 'px'],
				'textbox_content_title_top_spacing' => ['size' => 0, 'unit' => 'px'],
				'content_textbox_selected_icon' => ['value' => 'tgh-icon globe-2', 'library' => 'thegem-hbi'],
			),
			'right-icon-classic' => array(
				'content_textbox_title' => 'FREE SHIPPING & RETURN',
				'content_textbox_subtitle' => '',
				'textbox_container_padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_horizontal_position' => 'right',
				'textbox_container_align' => 'left',
				'textbox_content_icon_margin' => ['top' => '-2', 'right' => '0', 'bottom' => '0', 'left' => '75', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_text_wrapping' => 'inline',
				'textbox_content_icon_size' => ['size' => 30, 'unit' => 'px'],
				'content_textbox_title_html_tag' => 'title-default',
				'content_textbox_title_html_tag_weight' => 'bold',
				'content_textbox_subtitle_html_tag' => 'text-body-tiny',
				'textbox_content_title_bottom_spacing' => ['size' => 0, 'unit' => 'px'],
				'textbox_content_title_top_spacing' => ['size' => 0, 'unit' => 'px'],
				'content_textbox_selected_icon' => ['value' => 'tgh-icon globe-2', 'library' => 'thegem-hbi'],
			),
			'right-icon-tiny' => array(
				'content_textbox_title' => 'Contact Us',
				'content_textbox_subtitle' => '',
				'textbox_container_padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_horizontal_position' => 'right',
				'textbox_container_align' => 'left',
				'textbox_content_icon_margin' => ['top' => '-6', 'right' => '0', 'bottom' => '0', 'left' => '8', 'unit' => 'px', 'isLinked' => false],
				'textbox_content_icon_image_text_wrapping' => 'inline',
				'textbox_content_icon_size' => ['size' => 16, 'unit' => 'px'],
				'content_textbox_title_html_tag' => 'text-body-tiny',
				'content_textbox_title_html_tag_weight' => 'light',
				'content_textbox_subtitle_html_tag' => 'text-body-tiny',
				'textbox_content_title_bottom_spacing' => ['size' => 0, 'unit' => 'px'],
				'textbox_content_title_top_spacing' => ['size' => 0, 'unit' => 'px'],
				'content_textbox_selected_icon' => ['value' => 'tgh-icon mail', 'library' => 'thegem-hbi'],
			),
		);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_InfoBox());