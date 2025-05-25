<?php

namespace TheGem_Elementor\Widgets\Extended_Sorting;

use Elementor\Controls_Manager;use Elementor\Core\Schemes;use Elementor\Group_Control_Background;use Elementor\Group_Control_Border;use Elementor\Group_Control_Box_Shadow;use Elementor\Group_Control_Css_Filter;use Elementor\Group_Control_Typography;use Elementor\Icons_Manager;use Elementor\Plugin;use Elementor\Repeater;use Elementor\Widget_Base;use TheGem_Elementor\Group_Control_Background_Light;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Extended Sorting.
 */
#[\AllowDynamicProperties]
class TheGem_Extended_Sorting extends Widget_Base {

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

		$this->states_list = [
			'normal' => __('Normal', 'thegem'),
			'hover' => __('Hover', 'thegem'),
			'active' => __('Active', 'thegem'),
		];

	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-extended-sorting';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Grid Sorting', 'thegem');
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'blog-archive') {
			return ['thegem_blog_archive_builder'];
		}
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'product-archive') {
			return ['thegem_product_archive_builder'];
		}
		return ['thegem_elements'];
	}

	public function get_style_depends() {
		return ['thegem-portfolio-filters-list'];
	}

	public function get_script_depends() {
		if (Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-portfolio-grid-extended'
			];
		}
		return ['thegem-portfolio-grid-extended'];
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
				'label' => __('Filters Layout', 'thegem'),
			]
		);

		$this->add_control(
			'grid_filter',
			[
				'label' => __('Grid to Filter', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'id',
				'options' => [
					'id' => __('Grid ID', 'thegem'),
					'archive' => __('Current Query (Archive)', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'grid_id',
			[
				'label' => __('Grid ID', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'description' => __('Specify the ID of the grid to filter. For archive templates use "Current Query" setting in "Grid to Fllter" field.', 'thegem'),
				'condition' => [
					'grid_filter' => 'id'
				]
			]
		);

		$this->add_control(
			'sorting_dropdown_open',
			[
				'label' => __('Open Dropdown', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover' => __('On Hover', 'thegem'),
					'click' => __('On Click', 'thegem'),
				],
			]
		);

		$this->add_control(
			'sorting_scroll_top', [
				'label' => __('Scroll To Top', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Title', 'thegem'),
				'default' => __('Sort by', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$product_arr = [];
		if (defined( 'WC_PLUGIN_FILE' )) {
			$product_arr = [
				'price' => __('Price', 'thegem'),
				'rating' => __('Rating', 'thegem'),
				'popularity' => __('Popularity', 'thegem'),
			];
		}

		$repeater->add_control(
			'sort_by',
			[
				'label' => __('Sort By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => array_merge(
					[
						'title' => __('Title', 'thegem'),
						'date' => __('Date', 'thegem'),
						'custom_fields' => __('Custom Fields (TheGem)', 'thegem'),
						'details' => __('Project Details', 'thegem'),
						'manual_key' => __('Manual Meta Key', 'thegem'),
					],
					$product_arr,
					thegem_get_acf_plugin_groups()
				),
			]
		);

		$repeater->add_control(
			'manual_key_field',
			[
				'label' => __('Specify Field`s Name', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'description' => __('Use field`s name / meta key as set in custom fields settings', 'thegem'),
				'condition' => [
					'sort_by' => 'manual_key',
				],
			]
		);

		$options = thegem_select_theme_options_custom_fields_all();
		$default = !empty($options) ? array_keys($options)[0] : '';

		$repeater->add_control(
			'attribute_custom_fields',
			[
				'label' => __('Select Field', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => $default,
				'condition' => [
					'sort_by' => 'custom_fields',
				],
				'description' => __('Go to the <a href="' . get_site_url() . '/wp-admin/admin.php?page=thegem-theme-options#/single-pages" target="_blank">Theme Options -> Single Pages</a> to manage your custom fields.', 'thegem')
			]
		);

		$options = thegem_select_portfolio_details();
		$default = !empty($options) ? array_keys($options)[0] : '';

		$repeater->add_control(
			'attribute_details',
			[
				'label' => __('Select Field', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => $default,
				'condition' => [
					'sort_by' => 'details',
				],
				'description' => __('Go to the <a href="' . get_site_url() . '/wp-admin/admin.php?page=thegem-theme-options#/single-pages/portfolio" target="_blank">Theme Options -> Single Pages -> Portfolio Page</a> to manage your custom fields.', 'thegem')
			]
		);

		if (class_exists( 'ACF' ) && !empty(thegem_get_acf_plugin_groups())) {
			foreach (thegem_get_acf_plugin_groups() as $gr => $label) {

				$options = thegem_get_acf_plugin_fields_by_group($gr);
				$default = !empty($options) ? array_keys($options)[0] : '';

				$repeater->add_control(
					'attribute_custom_fields_acf_' . $gr,
					[
						'label' => __('Select Custom Field', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => $options,
						'default' => $default,
						'condition' => [
							'sort_by' => $gr,
						],
						'description' => __('Go to the <a href="' . get_site_url() . '/wp-admin/edit.php?post_type=acf-field-group" target="_blank">ACF -> Field Groups</a> to manage your custom fields.', 'thegem'),
					]
				);
			}
		}

		$repeater->add_control(
			'field_type',
			[
				'label' => __('Field Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => __('Text', 'thegem'),
					'number' => __('Number', 'thegem'),
				],
				'condition' => [
					'sort_by!' => ['title', 'date', 'price', 'rating', 'popularity'],
				],
			]
		);

		$repeater->add_control(
			'sort_order',
			[
				'label' => __('Order', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'asc',
				'options' => [
					'asc' => __('ASC', 'thegem'),
					'desc' => __('DESC', 'thegem'),
				],
			]
		);

		$this->add_control(
			'repeater_sort',
			[
				'type' => Controls_Manager::REPEATER,
				'label' => __('Attributes', 'thegem'),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default' => [
					[
						'title' => __('Sort by Title', 'thegem'),
					]
				],
				'frontend_available' => true,
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

		/* Sorting Style */
		$this->sorting_style_extended($control);
	}

	/**
	 * Sorting Style
	 * @access protected
	 */
	protected function sorting_style_extended($control) {

		$control->start_controls_section(
			'sorting_style_extended',
			[
				'label' => __('Sorting Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'sorting_default_text',
			[
				'label' => __('“Default sorting” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Default sorting', 'woocommerce'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_responsive_control(
			'sorting_width',
			[
				'label' => __('Field Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
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
					'{{WRAPPER}} .portfolio-sorting-select' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_control(
			'sorting_horizontal_alignment',
			[
				'label' => __('Horizontal Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
					'justify' => [
						'title' => __('Justified', 'thegem'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors_dictionary' => [
					'left' => 'margin-left: 0; margin-right: auto;',
					'center' => 'margin-left: auto; margin-right: auto;',
					'right' => 'margin-left: auto; margin-right: 0;',
					'justify' => 'width: 100%; margin-left: 0; margin-right: 0;',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-sorting-select' => '{{VALUE}};',
				],
				'default' => 'right',
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'sorting_extended_text_typography',
				'selector' => '{{WRAPPER}} .portfolio-sorting-select',
			]
		);

		$control->add_control(
			'filter_buttons_dropdown_selector_header',
			[
				'label' => __('Dropdown Selector', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'sorting_extended_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-sorting-select div.portfolio-sorting-select-current' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'sorting_extended_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-sorting-select div.portfolio-sorting-select-current' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'sorting_extended_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-sorting-select div.portfolio-sorting-select-current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sorting_extended_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-sorting-select div.portfolio-sorting-select-current',
			]
		);

		$control->add_responsive_control(
			'sorting_extended_padding',
			[
				'label' => __('Paddings', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-sorting-select div.portfolio-sorting-select-current' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'sorting_extended_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
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
					'{{WRAPPER}} .extended-posts-sorting' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->add_control(
			'sorting_extended_dropdown_header',
			[
				'label' => __('Dropdown', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'sorting_extended_dropdown_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio-sorting-select ul',
			]
		);

		$control->add_responsive_control(
			'sorting_extended_dropdown_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-sorting-select ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'sorting_extended_dropdown_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-sorting-select ul',
			]
		);

		$control->start_controls_tabs('sorting_extended_dropdown_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					$state = '.portfolio-sorting-select-current';
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}
				$control->start_controls_tab('sorting_extended_dropdown_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'sorting_extended_dropdown_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-sorting-select ul li' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->end_controls_section();
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
		if ($settings['grid_filter'] == 'id') {
			$grid_uid = $settings['grid_id'];
			$grid_uid_url = $grid_uid . '-';
		} else {
			$grid_uid = $grid_uid_url = '';
		}

		parse_str($_SERVER['QUERY_STRING'], $url_params);

		$orderby = $order = 'default';

		if (!empty($url_params)) {
			foreach ($url_params as $name => $value) {

				if (str_contains($name, $grid_uid_url . 'orderby')) {
					$orderby = $value;
				}
				if (str_contains($name, $grid_uid_url . 'order')) {
					$order = $value;
				}
			}
		}

		if (!empty($settings['repeater_sort'])) { ?>
			<div class="extended-posts-sorting" data-portfolio-uid="<?php echo esc_attr($grid_uid); ?>">
				 <div class="portfolio-sorting-select open-dropdown-<?php
				 echo $settings['sorting_dropdown_open'];
				 echo $settings['sorting_scroll_top'] == 'yes' ? ' scroll-top' : ''; ?>">
					<div class="portfolio-sorting-select-current">
						<div class="portfolio-sorting-select-name">
							<?php
							if ($orderby == 'default') {
								echo esc_html($settings['sorting_default_text']);
							} else {
								foreach ($settings['repeater_sort'] as $item) {
									if (in_array($item['sort_by'], ['date', 'title', 'price', 'rating', 'popularity'])) {
										$sort_by = $item['sort_by'];
									} else if ($item['sort_by'] == 'details') {
										$sort_by = $item['attribute_details'];
									} else if ($item['sort_by'] == 'custom_fields') {
										$sort_by = $item['attribute_custom_fields'];
									} else if ($item['sort_by'] == 'manual_key') {
										$sort_by = $item['manual_key_field'];
									} else {
										$sort_by = $item['attribute_custom_fields_acf_' . $item['sort_by']];
									}
									if ($orderby == $sort_by && $order == $item['sort_order']) {
										echo esc_html($item['title']);
										break;
									}
								}
							} ?>
						</div>
						<span class="portfolio-sorting-select-current-arrow"></span>
					</div>
					<ul>
						<li class="default <?php echo $orderby == 'default' ? 'portfolio-sorting-select-current' : ''; ?>"
							data-orderby="default" data-order="default">
							<?php echo esc_html($settings['sorting_default_text']); ?>
						</li>
						<?php foreach ($settings['repeater_sort'] as $item) {
							if (in_array($item['sort_by'], ['date', 'title', 'price', 'rating', 'popularity'])) {
								$sort_by = $item['sort_by'];
							} else if ($item['sort_by'] == 'details') {
								$sort_by = $item['attribute_details'];
							} else if ($item['sort_by'] == 'custom_fields') {
								$sort_by = $item['attribute_custom_fields'];
							} else if ($item['sort_by'] == 'manual_key') {
								$sort_by = $item['manual_key_field'];
							} else {
								$sort_by = $item['attribute_custom_fields_acf_' . $item['sort_by']];
							}
							if ($item['field_type'] == 'number') {
								$sort_by = 'num_' . $sort_by;
							} ?>
							<li class="<?php echo $orderby == $sort_by && $order == $item['sort_order'] ? 'portfolio-sorting-select-current' : ''; ?>"
								data-orderby="<?php echo esc_attr($sort_by); ?>" data-order="<?php echo esc_attr($item['sort_order']); ?>">
								<?php echo esc_html($item['title']); ?>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script type="text/javascript">
				(function ($) {
					setTimeout(function () {
						$('.elementor-element-<?php echo $this->get_id(); ?> .extended-posts-sorting').initPortfolioSorting();
					}, 1000);
				})(jQuery);

			</script>
		<?php endif;
		}
	}
}

Plugin::instance()->widgets_manager->register(new TheGem_Extended_Sorting());
