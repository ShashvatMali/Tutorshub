<?php

namespace TheGem_Elementor\Widgets\TemplateSearchForm;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Search.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateSearchForm extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCHFORM_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCHFORM_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCHFORM_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCHFORM_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-te-search-form', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCHFORM_URL . '/css/search.css', array(), null);
		wp_register_script('thegem-te-search-form', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCHFORM_URL . '/js/search.js', array('jquery'), null, true);
		wp_localize_script('thegem-te-search-form', 'thegem_search_form_data', array(
			'ajax_url' => esc_url(admin_url('admin-ajax.php')),
			'ajax_nonce' => wp_create_nonce('ajax_security'),
		));
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-search-form';
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
	/*public function show_in_panel() {
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'header';
	}*/

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Search Form', 'thegem');
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
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'header') return ['thegem_header_builder'];
		return ['thegem_elements'];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-te-search-form-fullscreen',
				'thegem-te-search-form'];
		}
		return ['thegem-te-search-form'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-te-search-form'];
		}
		return ['thegem-te-search-form'];
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
			'section_layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'placeholder_text',
			[
				'label' => __('Placeholder Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Search...', 'thegem'),
			]
		);

		$this->add_control(
			'post_types_header',
			[
				'label' => __('Post Types', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'post_type_products',
			[
				'label' => __('Products', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_responsive_control(
			'product_categories_dropdown',
			[
				'label' => __('Product Categories List', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'tablet_default' => '1',
				'mobile_default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'selectors_dictionary' => [
					'1' => 'display: flex;',
					'' => 'display: none;',
				],
				'selectors' => [
					'{{WRAPPER}} .select-category' => '{{VALUE}};',
				],
				'condition' => [
					'search_ajax' => '1',
					'post_type_products' => '1'
				],
			]
		);

		$this->add_control(
			'product_categories_placeholder_text',
			[
				'label' => __('Placeholder Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('All Categories', 'thegem'),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'search_ajax',
							'value' => '1',
						],
						[
							'name' => 'post_type_products',
							'value' => '1',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'product_categories_dropdown',
									'value' => '1',
								],
								[
									'name' => 'product_categories_dropdown_tablet',
									'value' => '1',
								],
								[
									'name' => 'product_categories_dropdown_mobile',
									'value' => '1',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'post_type_posts',
			[
				'label' => __('Blog Posts', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'post_type_pages',
			[
				'label' => __('Pages', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'post_type_portfolio',
			[
				'label' => __('Portfolio', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'results_header',
			[
				'label' => __('Live Search', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'search_ajax',
			[
				'label' => __('AJAX Live Search', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'products_auto_suggestions',
			[
				'label' => __('Products Auto-Suggestions', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 24,
					],
				],
				'default' => [
					'size' => 16,
					'unit' => '%',
				],
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'products_result_title',
			[
				'label' => __('Products Results Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Products', 'thegem'),
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'posts_auto_suggestions',
			[
				'label' => __('Posts Auto-Suggestions', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 16,
					],
				],
				'default' => [
					'size' => 8,
					'unit' => '%',
				],
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'posts_result_title',
			[
				'label' => __('Posts Results Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Blog', 'thegem'),
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'pages_auto_suggestions',
			[
				'label' => __('Pages Auto-Suggestions', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 16,
					],
				],
				'default' => [
					'size' => 8,
					'unit' => '%',
				],
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'pages_result_title',
			[
				'label' => __('Pages Results Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Pages', 'thegem'),
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'portfolio_auto_suggestions',
			[
				'label' => __('Portfolio Auto-Suggestions', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 16,
					],
				],
				'default' => [
					'size' => 8,
					'unit' => '%',
				],
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'portfolio_result_title',
			[
				'label' => __('Portfolio Results Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Portfolio', 'thegem'),
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'view_results_button_text',
			[
				'label' => __('"View Results" Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('View all search results', 'thegem'),
				'condition' => [
					'search_ajax' => '1'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_icon_section',
			[
				'label' => __('Search Icon', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_search_icon',
			[
				'label' => __('Icon', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'search_icon',
			[
				'label' => __('Search Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_search_icon' => '1',
				],
			]
		);

		$this->add_control(
			'close_icon',
			[
				'label' => __('Search Icon Close', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_search_icon' => '1',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
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
					'{{WRAPPER}} .thegem-te-search-form .search-submit' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_search_icon' => '1',
				],
			]
		);

		$this->start_controls_tabs('icon_tabs', [
			'condition' => [
				'show_search_icon' => '1',
			]]);
		$this->start_controls_tab('icon_tab_normal', ['label' => __('Normal', 'thegem'),]);
		$this->add_control(
			'icon_color_normal',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .search-submit' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab('icon_tab_hover', ['label' => __('Hover', 'thegem'),]);
		$this->add_control(
			'icon_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .search-submit:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'style_form_section',
			[
				'label' => __('Search Form', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'form_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 310,
					'unit' => 'px',
				],
				'size_units' => ['%', 'px'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} ' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'form_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 34,
					'unit' => 'px',
				],
				'size_units' => ['%', 'px'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .search-field' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-search-form .search-submit' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .thegem-te-search-form .search-submit i' => 'line-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'form_input_padding',
			[
				'label' => __('Left Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'size_units' => ['%', 'px'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .search-field' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_buttons_right',
			[
				'label' => __('Right Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'size_units' => ['%', 'px'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .search-buttons' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .search-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_field_state');

		$this->start_controls_tab('tab_field_normal', ['label' => __('Normal', 'thegem'),]);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border',
				'selector' => '{{WRAPPER}} .thegem-te-search-form .search-field',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'form_box_shadow',
				'selector' => '{{WRAPPER}} .thegem-te-search-form .search-field',
			]
		);

		$this->add_control(
			'form_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .search-field,
					{{WRAPPER}} .thegem-te-search-form .ajax-search-results,
					{{WRAPPER}} .thegem-te-search-form .select-category .select' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('tab_field_focus', ['label' => __('Focus', 'thegem'),]);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_focus_border',
				'selector' => '{{WRAPPER}} .thegem-te-search-form .search-field:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'form_focus_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .thegem-te-search-form .search-field:focus',
			]
		);

		$this->add_control(
			'form_focus_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .search-field:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'input_results_heading',
			[
				'label' => __('Input & Results Text', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'input_results_typography',
				'selector' => '{{WRAPPER}} .thegem-te-search-form',
			]
		);

		$this->start_controls_tabs('input_results_tabs');
		$this->start_controls_tab('input_results_tab_normal', ['label' => __('Normal', 'thegem')]);

		$this->add_responsive_control(
			'input_results_color_normal',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form' => 'color: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('input_results_tab_hover', ['label' => __('Hover', 'thegem'), 'show_likes' => 'yes']);

		$this->add_responsive_control(
			'input_results_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .ajax-search-results .ajax-search-item a:hover,
					{{WRAPPER}} .thegem-te-search-form .select-category .select .term.active, 
					{{WRAPPER}} .thegem-te-search-form .select-category .select .term:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'input_placeholder_color',
			[
				'label' => __('Placeholder Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .search-field::-webkit-input-placeholder,
					{{WRAPPER}} .thegem-te-search-form .search-field::placeholder' => 'color: {{VALUE}}; opacity: 1',
				],
			]
		);


		$this->add_control(
			'results_title_heading',
			[
				'label' => __('Results Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'results_title_typography',
				'selector' => '{{WRAPPER}} .thegem-te-search-form .title',
			]
		);

		$this->add_responsive_control(
			'results_title_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search-form .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function termLoop($all_text = false, $parent = 0) {
		$terms = get_terms('product_cat', ['parent' => $parent]);

		if (!empty($terms) && !is_wp_error($terms)) { ?>
			<ul>
				<?php
				if ($all_text) { ?>
					<li>
						<div class="term active" data-term="">
							<?php echo esc_html($all_text) ?>
						</div>
					</li>
				<?php }
				foreach ($terms as $term) { ?>
					<li>
						<div class="term" data-term="<?php echo $term->slug; ?>">
							<?php echo $term->name; ?>
						</div>
						<?php $this->termLoop(false, $term->term_id); ?>
					</li>
				<?php } ?>
			</ul>
		<?php }
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if (!defined('WC_PLUGIN_FILE')) {
			$settings['post_type_products'] = '';
		}

		$this->add_render_attribute(
			'searchform-wrap',
			[
				'class' => [
					'thegem-te-search-form',
					($settings['search_ajax'] == '1' ? 'ajax-search-form' : ''),
					($settings['search_ajax'] == '1' && $settings['post_type_products'] == '1' && ($settings['product_categories_dropdown'] == '1' || $settings['product_categories_dropdown_tablet'] == '1' || $settings['product_categories_dropdown_mobile'] == '1') ? 'with-categories' : ''),
				]]);

		?>

		<div <?php echo $this->get_render_attribute_string('searchform-wrap'); ?>>
			<form role="search" method="get" class="search-form" action="<?php echo esc_url(thegem_home_url('/')); ?>">
				<input class="search-field" type="search" name="s"
					   placeholder="<?php echo $settings['placeholder_text']; ?>"/>

				<?php if ($settings['search_ajax'] == '1') { ?>
					<div class="ajax-search-results-wrap"><div class="ajax-search-results"></div></div>
					<div class="search-buttons">
						<?php if ($settings['post_type_products'] == '1' && ($settings['product_categories_dropdown'] == '1' || $settings['product_categories_dropdown_tablet'] == '1' || $settings['product_categories_dropdown_mobile'] == '1')) { ?>
							<div class="select-category">
								<div class="current">
								<span class="text"
									  data-term=""><?php echo esc_html($settings['product_categories_placeholder_text']) ?></span>
									<span class="arrow-down"></span>
								</div>
								<div class="select">
									<div class="scroll-block">
										<?php $this->termLoop($settings['product_categories_placeholder_text']); ?>
									</div>
								</div>
							</div>
						<?php } ?>
						<?php if ($settings['show_search_icon']) { ?>
							<div class="search-submit">
								<span class="open">
									<?php if ($settings['search_icon']['value']) {
										Icons_Manager::render_icon($settings['search_icon'], ['aria-hidden' => 'true']);
									} else { ?>
										<i class="default"></i>
									<?php } ?>
								</span>
								<span class="close">
									<?php if ($settings['close_icon']['value']) {
										Icons_Manager::render_icon($settings['close_icon'], ['aria-hidden' => 'true']);
									} else { ?>
										<i class="default"></i>
									<?php } ?>
								</span>
							</div>
						<?php } ?>
					</div>
					<?php
					$post_types_arr = [];
					$post_types_ppp_arr = [];
					$result_title_arr = [];
					if ($settings['post_type_products'] == '1') {
						array_push($post_types_arr, 'product');
						array_push($post_types_ppp_arr, $settings['products_auto_suggestions']['size']);
						array_push($result_title_arr, $settings['products_result_title']);
					}
					if ($settings['post_type_posts'] == '1') {
						array_push($post_types_arr, 'post');
						array_push($post_types_ppp_arr, $settings['posts_auto_suggestions']['size']);
						array_push($result_title_arr, $settings['posts_result_title']);
					}
					if ($settings['post_type_portfolio'] == '1') {
						array_push($post_types_arr, 'thegem_pf_item');
						array_push($post_types_ppp_arr, $settings['portfolio_auto_suggestions']['size']);
						array_push($result_title_arr, $settings['portfolio_result_title']);
					}
					if ($settings['post_type_pages'] == '1') {
						array_push($post_types_arr, 'page');
						array_push($post_types_ppp_arr, $settings['pages_auto_suggestions']['size']);
						array_push($result_title_arr, $settings['pages_result_title']);
					}
					$post_types = json_encode($post_types_arr);
					$post_types_ppp = json_encode($post_types_ppp_arr);
					$result_title = json_encode($result_title_arr);
					$ajax_data = 'data-post-types="' . esc_attr($post_types) . '"
					data-post-types-ppp="' . esc_attr($post_types_ppp) . '"
					data-result-title="' . esc_attr($result_title) . '"
					data-show-all="' . esc_attr($settings['view_results_button_text']) . '"';

					if ($settings['post_type_products'] == '1' && thegem_get_option('website_search_mixed_disabled') != '1') { ?>
						<input type="hidden" name="post_type" value="product" />
					<?php } ?>

					<div class="ajax-search-params" <?php echo $ajax_data; ?>></div>
				<?php } else { ?>
					<div class="search-buttons">
						<button class="search-submit" type="submit">
						<span class="open">
							<?php if ($settings['search_icon']['value']) {
								Icons_Manager::render_icon($settings['search_icon'], ['aria-hidden' => 'true']);
							} else { ?>
								<i class="default"></i>
							<?php } ?>
						</span>
						</button>
					</div>
				<?php } ?>
			</form>
		</div>

		<?php
		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>

			<script type="text/javascript">
				(function ($) {
					setTimeout(function () {
						$('.elementor-element-<?php echo $this->get_id(); ?> .thegem-te-search-form.ajax-search-form').initSearchForms();
					}, 1000);
				})(jQuery);
			</script>
		<?php endif;
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateSearchForm());