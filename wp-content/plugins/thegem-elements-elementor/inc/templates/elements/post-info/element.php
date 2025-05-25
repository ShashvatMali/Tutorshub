<?php

namespace TheGem_Elementor\Widgets\TemplatePostInfo;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Controls_Stack;
use Elementor\Control_Media;
use Elementor\Repeater;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Blog Title.
 */

#[\AllowDynamicProperties]
class TheGem_TemplatePostInfo extends Widget_Base {
	public function __construct($data = [], $args = null) {

		$template_type = isset($GLOBALS['thegem_template_type']) ? $GLOBALS['thegem_template_type'] : thegem_get_template_type(get_the_ID());
		$this->is_loop_builder_template = false; //$template_type === 'loop-item';

		if (isset($data['settings']) && (empty($_REQUEST['action']) || !in_array($_REQUEST['action'], array('thegem_importer_process', 'thegem_templates_new', 'thegem_blocks_import')))) {
			if (!isset($data['settings']['skin'])) {
				if ($this->is_loop_builder_template) {
					$data['settings']['skin'] = 'classic';
				} else {
					$data['settings']['skin'] = 'modern';
				}
			}

			if (!isset($data['settings']['text_style'])) {
				if ($this->is_loop_builder_template) {
					$data['settings']['text_style'] = 'text-body-tiny';
				} else {
					$data['settings']['text_style'] = '';
				}
			}

			foreach ($data['settings']['info_content'] as $key => $setting) {
				if (!isset($setting['date_icon'])) {
					if ($this->is_loop_builder_template) {
						$data['settings']['info_content'][$key]['date_icon'] = '';
					} else {
						$data['settings']['info_content'][$key]['date_icon'] = 'default';
					}
				}

				if (!isset($setting['date_link'])) {
					if ($this->is_loop_builder_template) {
						$data['settings']['info_content'][$key]['date_link'] = '';
					} else {
						$data['settings']['info_content'][$key]['date_link'] = 'yes';
					}
				}

				if (!isset($setting['time_icon'])) {
					if ($this->is_loop_builder_template) {
						$data['settings']['info_content'][$key]['time_icon'] = '';
					} else {
						$data['settings']['info_content'][$key]['time_icon'] = 'default';
					}
				}

				if (!isset($setting['comments_link'])) {
					if ($this->is_loop_builder_template) {
						$data['settings']['info_content'][$key]['comments_link'] = '';
					} else {
						$data['settings']['info_content'][$key]['comments_link'] = 'yes';
					}
				}
			}
		}

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
		return 'thegem-template-post-info';
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
		return get_post_type($post_id) !== 'thegem_templates' || thegem_get_template_type($post_id) === 'single-post' || thegem_get_template_type($post_id) === 'loop-item' || thegem_get_template_type($post_id) === 'title';
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
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'loop-item') {
			return __('Post Meta/Product Attributes', 'thegem');
		}
		return __('Post Meta', 'thegem');
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
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'loop-item') return ['thegem_loop_builder'];
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'title') return ['thegem_title_area_builder'];
		if(get_post_type($post_id) === 'thegem_templates') return ['thegem_single_post_builder'];
		return ['thegem_single_post'];
	}

	/**
	 * Show reload button
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Get widget wrapper
	 */
	public function get_widget_wrapper() {
		return 'thegem-te-post-info';
	}

	/**
	 * Get customize class
	 */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}

		return ' .'.$this->get_widget_wrapper().' .post-info';
	}

	/**
	 * Get taxonomy
	 */
	public function get_post_types_list()
	{
		$post_types = array();
		foreach (get_post_types(array('public' => true), 'object') as $slug => $post_type) {
			if (!in_array($slug, array('product', 'thegem_news', 'thegem_footer', 'thegem_title', 'thegem_templates', 'attachment', 'elementor_library', 'e-landing-page', 'e-floating-buttons'), true)) {
				$post_types[] = $slug;
			}
		}

		$post_types[] = 'thegem_team_person';
		$post_types[] = 'thegem_testimonial';

		return $post_types;
	}

	public function get_taxonomy_list()
	{
		$taxonomies = get_object_taxonomies( $this->get_post_types_list(), 'objects' );
		if (!empty($taxonomies)) {
			foreach ($taxonomies as $taxonomy) {
				$data[$taxonomy->name] = ucwords($taxonomy->label);
			}
		}

		return $data;
	}

	public function get_product_taxonomy_list()
	{
		$data = array();
		$taxonomies = get_object_taxonomies( array('product'), 'objects' );
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

	public function build_terms_hierarchy($elements) {
		$parents_elements = array();
		foreach($elements as $el) {
			$parents = get_ancestors($el, get_term($el)->taxonomy);
			$is_parent = true;
			foreach($parents as $parent) {
				if(in_array($parent, $elements)) {
					$is_parent = false;
				}
			}
			if($is_parent) {
				$parents_elements[] = $el;
			}
		}
		$childrens = array_diff($elements, $parents_elements);
		$result = array();
		foreach($parents_elements as $parent_id) {
			$result[$parent_id] = array();
			foreach($childrens as $child_id) {
				if(term_is_ancestor_of($parent_id, $child_id, get_term($parent_id)->taxonomy)) {
					$result[$parent_id][] = $child_id;
				}
			}
			$result[$parent_id] = $this->build_terms_hierarchy($result[$parent_id]);
		}
		return $result;
	}

	public function terms_hierarchy_to_list($elements = array()) {
		$result = array();
		foreach($elements as $parent => $children) {
			$result[] = $parent;
			if(!empty($children)) {
				$result = array_merge($result, $this->terms_hierarchy_to_list($children));
			}
		}
		return $result;
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
					'modern' => __('Modern', 'thegem')
				],
				'default' => $this->is_loop_builder_template ? 'classic' : 'modern',
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
			]
		);

		$repeater = new Repeater();

		// Type
		$repeater->add_control(
			'type',
			[
				'label' => __('Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'author' => __('Author', 'thegem'),
					'terms' => __('Terms', 'thegem'),
					'product_terms' => __('Product Terms', 'thegem'),
					'date' => __('Date', 'thegem'),
					'date_modified' => __('Date Updated', 'thegem'),
					'time' => __('Time', 'thegem'),
					'time_modified' => __('Time Updated', 'thegem'),
					'comments' => __('Comments', 'thegem'),
					'likes' => __('Likes', 'thegem'),
				],
				'default' => 'author',
			]
		);

		// Author
		$repeater->add_control(
			'author_label',
			[
				'label' => __('Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('By', 'thegem'),
				'condition' => [
					'type' => 'author',
				],
			]
		);

		$repeater->add_control(
			'author_avatar',
			[
				'label' => __('Avatar', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => 'author',
				],
			]
		);

		$repeater->add_control(
			'author_avatar_size',
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
					'type' => 'author',
					'author_avatar' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' ' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'author_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => 'author',
				],
			]
		);

		// Terms
		$repeater->add_control(
			'terms_taxonomy',
			[
				'label' => __('Terms', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_taxonomy_list(),
				'default' => $this->get_default_taxonomy(),
				'condition' => [
					'type' => 'terms'
				],
			]
		);

		// Terms
		$repeater->add_control(
			'terms_product_taxonomy',
			[
				'label' => __('Product Terms', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_product_taxonomy_list(),
				'default' => 'product_cat',
				'condition' => [
					'type' => 'product_terms'
				],
			]
		);

		$repeater->add_control(
			'terms_taxonomy_truncate',
			[
				'label' => __('Truncate Terms', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['terms', 'product_terms']
				],
			]
		);

		$repeater->add_control(
			'terms_taxonomy_truncate_number',
			[
				'label' => __('Number of terms to show', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [
					'type' => ['terms', 'product_terms'],
					'terms_taxonomy_truncate' => 'yes'
				],
			]
		);

		$repeater->add_control(
			'terms_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['terms', 'product_terms']
				],
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
					'type' => ['date', 'date_modified']
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
				'default' => $this->is_loop_builder_template ? '' : 'default',
				'condition' => [
					'type' => ['date', 'date_modified']
				],
			]
		);

		$repeater->add_control(
			'date_icon_select',
			[
				'label' => __('Select Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'type' => ['date', 'date_modified'],
					'date_icon' => 'custom',
				],
			]
		);

		$repeater->add_control(
			'date_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => $this->is_loop_builder_template ? '' : 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => 'date',
				],
			]
		);

		// Time
		$repeater->add_control(
			'time_label',
			[
				'label' => __('Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'type' => ['time', 'time_modified'],
				],
			]
		);

		$repeater->add_control(
			'time_format',
			[
				'label' => __('Time Format', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'1' => __('3:31 pm (g:i a)', 'thegem'),
					'2' => __('3:31 PM (g:i A)', 'thegem'),
					'3' => __('15:31 (H:i)', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'type' => ['time', 'time_modified'],
				],
			]
		);

		$repeater->add_control(
			'time_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('None', 'thegem'),
					'default' => __('Default', 'thegem'),
					'custom' => __('Custom', 'thegem'),
				],
				'default' => $this->is_loop_builder_template ? '' : 'default',
				'condition' => [
					'type' => ['time', 'time_modified'],
				],
			]
		);

		$repeater->add_control(
			'time_icon_select',
			[
				'label' => __('Select Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'type' => ['time', 'time_modified'],
					'time_icon' => 'custom',
				],
			]
		);

		// Comments
		$repeater->add_control(
			'comments_label',
			[
				'label' => __('Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'type' => 'comments',
				],
			]
		);

		$repeater->add_control(
			'comments_icon',
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
					'type' => 'comments',
				],
			]
		);

		$repeater->add_control(
			'comments_icon_select',
			[
				'label' => __('Select Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'type' => 'comments',
					'comments_icon' => 'custom',
				],
			]
		);

		$repeater->add_control(
			'comments_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => $this->is_loop_builder_template ? '' : 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => 'comments',
				],
			]
		);

		// Likes
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item .zilla-likes:before' => '{{VALUE}};',
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

		// Container Section Style
		$this->start_controls_section(
			'container_style',
			[
				'label' => __('Container', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'container_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .thegem-te-post-info',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-post-info',
			]
		);

		$this->add_responsive_control(
			'container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-post-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-post-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-post-info',
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
					'skin' => ['classic'],
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
					'skin' => ['classic'],
					'list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item:not(:last-child):after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats .separator' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'list_spacing_horizontal',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'layout' => 'horizontal',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item:not(.post-info-item-cats-list)' => 'margin-right: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item.post-info-item-cats-list' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats .separator' => 'margin-left: calc({{SIZE}}{{UNIT}} / 2); margin-right: calc({{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->add_responsive_control(
			'list_spacing_vertical',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'layout' => 'vertical',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item' => 'margin-top: {{SIZE}}{{UNIT}};',
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
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item .icon i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item .zilla-likes:before' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item .icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item .zilla-likes:before' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item .avatar' => 'margin-right: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item .icon' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item .zilla-likes' => 'color: {{VALUE}};',
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
			'text_style',
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
					'title-text-body' => __('Body', 'thegem'),
					'title-text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => $this->is_loop_builder_template ? 'title-text-body-tiny' : '',
			]
		);

		$this->add_control(
			'text_font_weight',
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
			'text_letter_spacing',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_transform',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item a:hover .icon' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item .zilla-likes:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-info-item, {{WRAPPER}}'.$this->get_customize_class().' .post-info-item a',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-info-item, {{WRAPPER}}'.$this->get_customize_class().' .post-info-item a',
			]
		);

		$this->end_controls_section();

		// Terms Section Default Style
		$this->start_controls_section(
			'section_cats_style',
			[
				'label' => __('Terms', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin' => 'modern',
				],
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats a' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats span.readonly' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cats_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => 'modern',
					'cats_border' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats a' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats span.readonly' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cats_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => 'modern',
					'cats_border' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats a' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats span.readonly' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats a' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats span.readonly' => 'color: {{VALUE}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats a:not(.readonly):hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats span.readonly' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats a:not(.readonly):hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats a' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats span.readonly' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats a:not(.readonly):hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Terms Section List Style
		$this->start_controls_section(
			'section_terms_list_style',
			[
				'label' => __('Terms', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin' => 'classic',
				],
			]
		);

		$this->add_control(
			'terms_taxonomy_style',
			[
				'label' => __('Display as', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Simple List', 'thegem'),
					'list' => __('Label', 'thegem'),
				],
				'default' => 'default',
				'condition' => [
					'skin' => 'classic',
				],
			]
		);

		$this->add_control(
			'terms_taxonomy_delimiter',
			[
				'label' => __('List Delimiter', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __(',', 'thegem'),
				'condition' => [
					'skin' => 'classic',
					'terms_taxonomy_style' => 'list',
				],
			]
		);

		$this->add_control(
			'terms_list_border',
			[
				'label' => __('Border', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __('None', 'thegem'),
					'solid' => __('Solid', 'thegem'),
				],
				'default' => 'none',
				'condition' => [
					'skin' => 'classic',
					'terms_taxonomy_style' => 'list',
				],
				'selectors_dictionary' => [
					'' => 'border: 0',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats-list' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'terms_list_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => 'classic',
					'terms_taxonomy_style' => 'list',
					'terms_list_border' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats-list' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'terms_list_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'skin' => 'classic',
					'terms_taxonomy_style' => 'list',
					'terms_list_border' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats-list' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'terms_list_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'classic',
					'terms_taxonomy_style' => 'list',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats-list' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats-list a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'terms_list_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'classic',
					'terms_taxonomy_style' => 'list',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats-list a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'terms_list_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'classic',
					'terms_taxonomy_style' => 'list',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats-list' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'terms_list_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'skin' => 'classic',
					'terms_taxonomy_style' => 'list',
					'terms_list_border' => 'solid',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-info-item-cats-list' => 'border-color: {{VALUE}};',
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
		$info_content = $params['info_content'];

		if (empty($single_post) || empty($info_content)) {
			ob_end_clean();
			echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$skin = 'post-info--'.$params['skin'];
		$layout = 'post-info--'.$params['layout'];
		$alignment = 'post-info--'.$params['list_alignment'];
		$divider = $params['skin'] == 'classic' && empty($params['list_divider']) ? 'post-info--divider-hide' : 'post-info--divider-show';
		$loop_item = $this->is_loop_builder_template ? 'post-info--loop-item' : '';
		$params['element_class'] = implode(' ', array(
		$this->get_widget_wrapper(), $skin, $layout, $alignment, $divider, $loop_item
		));
		$text_styled = implode(' ', array($params['text_style'], $params['text_font_weight']));

		foreach ($info_content as $item) {
			if($item['type'] === 'product_terms') {
				$item['terms_taxonomy'] = $item['terms_product_taxonomy'];
			}
			switch ($item['type']) {
				case 'cats':
					$cats = get_the_category();
					$cats_list = [];
					foreach ($cats as $cat) {
						$cats_list[] = '<a href="' . esc_url(get_category_link($cat->term_id)) . '" title="' . esc_attr(sprintf(__("View all posts in %s", "thegem"), $cat->name)) . '">' . $cat->cat_name . '</a>';
					}

					$cats_output = implode(' <span class="separator"></span> ', $cats_list);

					if (!empty($cats_output)) {
						echo '<div class="post-info-item post-info-item-' . $item['type'] . ' ' . $text_styled . '">' . $cats_output . '</div>';
					}

					break;
				case 'terms':
				case 'product_terms':
					$taxes = array();
					if(!taxonomy_exists($item['terms_taxonomy'])) break;
					$post_terms_ids = wp_get_post_terms($single_post->ID, $item['terms_taxonomy'], array('fields' => 'ids'));
					$post_terms_hierarchy = $this->build_terms_hierarchy($post_terms_ids);
					$post_terms_ids_list = $this->terms_hierarchy_to_list($post_terms_hierarchy);

					if (!empty($item['terms_taxonomy_truncate']) && !empty($item['terms_taxonomy_truncate_number'])) {
						$post_terms_ids_list = array_slice($post_terms_ids_list, 0, $item['terms_taxonomy_truncate_number']);
					}
					foreach($post_terms_ids_list as $term_id) {
						$taxes[] = get_term($term_id);
					}

					$taxes_list = [];
					foreach ($taxes as $tax) {
						if (!empty($item['terms_link'])) {
							$taxes_list[] = '<a href="' . esc_url(get_category_link($tax->term_id)) . '">' . $tax->name . '</a>';
						} else {
							$taxes_list[] = '<span class="readonly">' . $tax->name . '</span>';
						}
					}

					if ($params['skin'] == 'classic' && !empty($params['terms_taxonomy_style']) && $params['terms_taxonomy_style'] == 'list') {
						$taxes_output = implode('<span class="separator">'.$params['terms_taxonomy_delimiter'].'</span>', $taxes_list);

						if (!empty($taxes_output)) {
							echo '<div class="post-info-item post-info-item-cats-list ' . $text_styled . '">' . $taxes_output . '</div>';
						}
					} else {
						$taxes_output = implode(' <span class="separator"></span> ', $taxes_list);

						if (!empty($taxes_output)) {
							echo '<div class="post-info-item post-info-item-cats ' . $text_styled . '">' . $taxes_output . '</div>';
						}
					}

					break;
				case 'author':
					$author_output = $author_label = '';

					if (!empty($item['author_avatar'])) {
						$size = !empty($item['author_avatar_size']['size']) ? $item['author_avatar_size']['size'] : 20;
						$author_output .= '<div class="avatar">'.get_avatar($single_post, $size ).'</div>';
					}

					if (!empty($item['author_label'])) {
						$author_label = esc_html($item['author_label']);
					}

					if (!empty($item['author_link'])) {
						$author_output .= '<div class="name">'.$author_label.' '.get_the_author_posts_link().'</div>';
					} else {
						$author_output .= '<div class="name">'.$author_label.' '.get_the_author_meta('display_name').'</div>';
					}

					if (!empty($author_output)) {
						echo '<div class="post-info-item post-info-item-'.$item['type'].' '.$text_styled.'">'.$author_output.'</div>';
					}

					break;
				case 'date':
				case 'date_modified':
					$date_output = $format = '';

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
						$date_output .= '<div class="icon">'.ob_get_clean().'</div>';
					} else if (!empty($item['date_icon']) && $item['date_icon'] == 'default') {
						$date_output .= '<div class="icon"><i class="icon-default"></i></div>';
					}

					if (!empty($item['date_label'])) {
						$date_output .= '<div class="label-before">'.esc_html($item['date_label']).'</div>';
					}

					if (!empty($item['date_link'])) {
						$year = get_the_date('Y');
						$month = get_the_date('m');
						$day = get_the_date('d');

						$date_text = get_the_date($format, $single_post);
						if($format === 'relative') {
							$date_text = sprintf(__('%s ago'), human_time_diff(get_the_date('U', $single_post), current_time('timestamp')));
						}
						$date_output .= '<a class="date" href="'.get_day_link( $year, $month, $day).'">'.$date_text.'</a>';
					} else {
						$date_text = $item['type'] === 'date_modified' ? get_the_modified_date($format, $single_post) : get_the_date($format, $single_post);
						if($format === 'relative') {
							$date_text = sprintf(__('%s ago'), human_time_diff($item['type'] === 'date_modified' ? get_the_modified_date('U', $single_post) : get_the_date('U', $single_post), current_time('timestamp')));
						}
						$date_output .= '<div class="date">'.$date_text.'</div>';
					}

					if (!empty($date_output)) {
						echo '<div class="post-info-item post-info-item-'.$item['type'].' '.$text_styled.'">'.$date_output.'</div>';
					}

					break;
				case 'time':
				case 'time_modified':
					$time_output = $format = '';

					if (!empty($item['time_format'])) {
						if ($item['time_format'] == '1') $format = 'g:i a';
						if ($item['time_format'] == '2') $format = 'g:i A';
						if ($item['time_format'] == '3') $format = 'H:i';
					}

					if (!empty($item['time_icon']) && $item['time_icon'] == 'custom' && !empty($item['time_icon_select']['value'])) {
						ob_start();
						Icons_Manager::render_icon($item['time_icon_select'], ['aria-hidden' => 'true']);
						$time_output .= '<div class="icon">'.ob_get_clean().'</div>';
					} else if (!empty($item['time_icon']) && $item['time_icon'] == 'default') {
						$time_output .= '<div class="icon"><i class="icon-default"></i></div>';
					}

					if (!empty($item['time_label'])) {
						$time_output .= '<div class="label-before">'.esc_html($item['time_label']).'</div>';
					}

					$time_output .= '<div class="time">'.($item['type'] === 'time_modified' ? get_the_modified_time($format, $single_post) : get_the_time($format, $single_post)).'</div>';

					if (!empty($time_output)) {
						echo '<div class="post-info-item post-info-item-'.$item['type'].' '.$text_styled.'">'.$time_output.'</div>';
					}

					break;
				case 'comments':
					if ( !comments_open()) break;

					$comments_output = $comments_label = $comments_icon = '';
					if (!empty($item['comments_icon']) && $item['comments_icon'] == 'custom' && !empty($item['comments_icon_select']['value'])) {
						ob_start();
						Icons_Manager::render_icon($item['comments_icon_select'], ['aria-hidden' => 'true']);
						$comments_icon = '<div class="icon">'.ob_get_clean().'</div>';
					} else if (!empty($item['comments_icon']) && $item['comments_icon'] == 'default') {
						$comments_icon = '<div class="icon"><i class="icon-default"></i></div>';
					}

					$comments_label = '<div class="count">'.$single_post->comment_count.'</div>';

					if (!empty($item['comments_label'])) {
						$comments_label .= '<div class="label-after">'.esc_html($item['comments_label']).'</div>';
					}

					if (!empty($item['comments_link'])) {
						$comments_output = $comments_icon.' '.'<a class="comments-link" href="'.get_permalink( $single_post ).'#respond">'.$comments_label.'</a>';
					} else{
						$comments_output = $comments_icon.' '.$comments_label;
					}

					if (!empty($comments_output)) {
						echo '<div class="post-info-item post-info-item-'.$item['type'].' '.$text_styled.'">'.$comments_output.'</div>';
					}

					break;
				case 'likes':
					if ( !function_exists('zilla_likes')) break;

					ob_start();
					zilla_likes();
					$likes_output = '<div class="likes">'.ob_get_clean().'</div>';

					if (!empty($item['likes_label'])) {
						$likes_output .= '<div class="label-after">'.esc_html($item['likes_label']).'</div>';
					}

					if (!empty($likes_output)) {
						echo '<div class="post-info-item post-info-item-'.$item['type'].' '.$text_styled.'">'.$likes_output.'</div>';
					}

					break;
			}
		}

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		if (!empty($return_html)) { ?>
			<div class="<?= esc_attr($params['element_class']); ?>">
				<div class="post-info"><?= $return_html ?></div>
			</div>
			<?php thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html); ?>
		<?php } else {
			echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
		}
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplatePostInfo());