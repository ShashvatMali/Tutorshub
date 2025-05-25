<?php

namespace TheGem_Elementor\Widgets\ProductsCompactGrid;

use Elementor\Controls_Manager;
use TheGem_Elementor\Group_Control_Background_Light;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Products Grid.
 */
#[\AllowDynamicProperties]
class TheGem_ProductsCompactGrid extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRODUCTS_COMPACT_GRID_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRODUCTS_COMPACT_GRID_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRODUCTS_COMPACT_GRID_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRODUCTS_COMPACT_GRID_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-products-compact-grid', THEGEM_ELEMENTOR_WIDGET_PRODUCTS_COMPACT_GRID_URL . '/assets/css/thegem-products-compact-grid.css');

	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-products-compact-grid';
	}

	/**
	 * Show in panel.
	 *
	 * Whether to show the widget in the panel or not. By default returns true.
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function show_in_panel() {
		return defined('WC_PLUGIN_FILE');
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Products Compact Grid/List', 'thegem');
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'thegem-eicon thegem-eicon-products-grid';
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
		return ['thegem_woocommerce'];
	}

	public function get_style_depends() {
		return ['thegem-products-compact-grid'];
	}

	public function get_script_depends() {
		return [];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Make options select products sets
	 * @access protected
	 * @return array
	 */
	protected function select_products_sets($exclude_all = false) {
		$out = [];
		if (!$exclude_all) {
			$out = ['0' => __('All', 'thegem')];
		}
		$terms = get_terms([
			'taxonomy' => 'product_cat',
			'hide_empty' => true,
		]);

		if (empty($terms) || is_wp_error($terms)) {
			return $out;
		}

		foreach ((array)$terms as $term) {
			if (!empty($term->name)) {
				$out[$term->slug] = $term->name;
			}
		}
		return $out;
	}

	/**
	 * Make options select products attributes
	 * @access protected
	 * @return array
	 */
	protected function select_products_attributes() {
		global $wc_product_attributes;
		$out = [];

		if (empty($wc_product_attributes) || is_wp_error($wc_product_attributes)) {
			return $out;
		}

		foreach ((array)$wc_product_attributes as $attr) {
			if (!empty($attr->attribute_name)) {
				$out[$attr->attribute_name] = $attr->attribute_label;
			}
		}
		return $out;
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_products',
			[
				'label' => __('Products', 'thegem'),
			]
		);

		$this->add_control(
			'source_header',
			[
				'label' => __('Source', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'source',
			[
				'label' => __('Source', 'thegem'),
				'default' => ['category'],
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'category' => __('Category', 'thegem'),
					'attribute' => __('Attribute', 'thegem'),
				],
			]
		);

		$this->add_control(
			'content_products_cat',
			[
				'label' => __('Select Products Categories', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->select_products_sets(),
				'frontend_available' => true,
				'condition' => [
					'source' => 'category',
				],
			]
		);

		$this->add_control(
			'content_products_attr',
			[
				'label' => __('Attributes', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->select_products_attributes(),
				'default' => '0',
				'frontend_available' => true,
				'condition' => [
					'source' => 'attribute',
				],
			]
		);

		global $wc_product_attributes;

		if (!empty($wc_product_attributes) && !is_wp_error($wc_product_attributes)) {

			foreach ((array)$wc_product_attributes as $attr) {

				if (!empty($attr->attribute_name)) {
					$terms = get_terms('pa_' . $attr->attribute_name);
					$options = ['0' => __('All', 'thegem')];
					if (!empty($terms) && !is_wp_error($terms)) {

						foreach ($terms as $term) {
							$options[$term->slug] = $term->name;
						}
					}

					$this->add_control(
						'content_products_attr_val_' . $attr->attribute_name,
						[
							'label' => __('Select', 'thegem') . ' ' . $attr->attribute_label . ' ' . __('Values', 'thegem'),
							'type' => Controls_Manager::SELECT2,
							'label_block' => true,
							'multiple' => true,
							'options' => $options,
							'frontend_available' => true,
							'condition' => [
								'source' => 'attribute',
								'content_products_attr' => $attr->attribute_name,
							],
						]
					);
				}
			}
		}

		$this->add_control(
			'sorting_header',
			[
				'label' => __('Sorting', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __('Order By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'default' => __('WooCommerce Default Sorting', 'thegem'),
					'date' => __('Date', 'thegem'),
					'popularity' => __('Popularity', 'thegem'),
					'rating' => __('Rating', 'thegem'),
					'title' => __('Name', 'thegem'),
					'price' => __('Price', 'thegem'),
					'rand' => __('Random', 'thegem'),
				],
				'default' => 'default',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __('Order', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'asc' => __('ASC', 'thegem'),
					'desc' => __('DESC', 'thegem'),
				],
				'default' => 'asc',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'list',
				'options' => [
					'list' => __('Compact List', 'thegem'),
					'grid' => __('Compact Grid', 'thegem'),
				],
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => __('Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '2x',
				'options' => [
					'1x' => __('1x columns', 'thegem'),
					'2x' => __('2x columns', 'thegem'),
					'3x' => __('3x columns', 'thegem'),
					'4x' => __('4x columns', 'thegem'),
					'5x' => __('5x columns', 'thegem'),
					'6x' => __('6x columns', 'thegem'),
				],
				'condition' => [
					'layout' => 'grid',
				],
			]
		);

		$this->add_control(
			'image_aspect_ratio',
			[
				'label' => __('Image Aspect Ratio', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'portrait',
				'options' => [
					'portrait' => __('Portrait', 'thegem'),
					'square' => __('Square', 'thegem'),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_caption',
			[
				'label' => __('Caption', 'thegem'),
			]
		);

		$this->add_control(
			'product_show_categories', [
				'label' => __('Categories', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'product_show_title', [
				'label' => __('Product Title', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'product_show_price', [
				'label' => __('Product Price', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'product_show_reviews', [
				'label' => __('Reviews', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'product_show_reviews_count', [
				'label' => __('Reviews Count', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'product_show_reviews' => 'yes',
				],
			]
		);

		$this->add_control(
			'items_per_page',
			[
				'label' => __('Items Count', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => 4,
				'description' => __('Use -1 to show all', 'thegem'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional',
			[
				'label' => __('Additional Options', 'thegem'),
			]
		);

		$this->add_control(
			'featured_only',
			[
				'label' => __('Show only “Featured” Products', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'sale_only',
			[
				'label' => __('Show only “On Sale” Products', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'recently_viewed_only',
			[
				'label' => __('Show only “Recently Viewed” Products', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'new_only',
			[
				'label' => __('Show only “New” Products', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'grid_ajax_load',
			[
				'label' => __('Load with AJAX', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'description' => __('Enable this in case you have activated any of the additional display options and you use full-page cache like WP Super Cache.', 'thegem'),
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

		/* Images Style */
		$this->image_style($control);

		/* Caption Style */
		$this->caption_style($control);

		/* Caption Container Style */
		$this->caption_container_style($control);

	}

	/**
	 * Grid Images Style
	 * @access protected
	 */
	protected function image_style($control) {
		$control->start_controls_section(
			'image_style',
			[
				'label' => __('Grid Images Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'gaps',
			[
				'label' => __('Gaps', 'thegem'),
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
				'default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid.layout-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}; grid-column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .products-compact-grid.layout-list:not(.with-separator) .compact-product-item' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .products-compact-grid.layout-list.with-separator .compact-product-item' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}}/2);',
				]
			]
		);
		
		$control->add_responsive_control(
			'image_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid.layout-list .image a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'layout' => 'list',
				],
			]
		);

		$control->add_control(
			'product_separator',
			[
				'label' => __('Product Separator', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'layout' => 'list',
				],
			]
		);

		$control->add_control(
			'product_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid.layout-list.with-separator .compact-product-item' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'product_separator' => 'yes',
					'layout' => 'list',
				],
			]
		);

		$control->add_control(
			'product_separator_width',
			[
				'label' => __('Separator Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 4,
					],
				],
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid.layout-list.with-separator .compact-product-item' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_separator' => 'yes',
					'layout' => 'list',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Caption Style
	 * @access protected
	 */
	protected function caption_style($control) {

		$control->start_controls_section(
			'caption_style',
			[
				'label' => __('Caption Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'categories_header',
			[
				'label' => __('Categories', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'product_show_categories' => 'yes',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'categories_typography',
				'selector' => '{{WRAPPER}} .products-compact-grid .caption .categories',
				'condition' => [
					'product_show_categories' => 'yes',
				],
			]
		);

		$control->start_controls_tabs('categories_tabs', [
			'condition' => [
				'product_show_categories' => 'yes',
			],
		]);

		$control->start_controls_tab('categories_tab_normal', ['label' => __('Normal', 'thegem')]);

		$control->add_control(
			'categories_color_normal',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid .caption .categories' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('categories_tab_hover', ['label' => __('Hover', 'thegem')]);

		$control->add_control(
			'categories_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid .caption .categories a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_control(
			'title_header',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_title' => 'yes',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .products-compact-grid .caption .title',
				'condition' => [
					'product_show_title' => 'yes',
				],
			]
		);

		$control->start_controls_tabs('title_tabs', [
			'condition' => [
				'product_show_title' => 'yes',
			],
		]);

		$control->start_controls_tab('title_tab_normal', ['label' => __('Normal', 'thegem')]);

		$control->add_control(
			'title_color_normal',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid .caption .title' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('title_tab_hover', ['label' => __('Hover', 'thegem')]);

		$control->add_control(
			'title_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid .caption .title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_control(
			'price_header',
			[
				'label' => __('Price', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_price' => 'yes',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'price_typography',
				'selector' => '{{WRAPPER}} .products-compact-grid .product-price .price',
				'condition' => [
					'product_show_price' => 'yes',
				],
			]
		);

		$control->add_control(
			'price_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid .product-price .price' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'rating_header',
			[
				'label' => __('Rating Stars', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'rated_color',
			[
				'label' => __('Rated Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid .star-rating > span:before' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'base_color',
			[
				'label' => __('Base Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .products-compact-grid .star-rating:before' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Caption Container Style
	 * @access protected
	 */
	protected function caption_container_style($control) {
		$control->start_controls_section(
			'caption_container_style',
			[
				'label' => __('Caption Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'caption_alignment',
			[
				'label' => __('Content Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Centered', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'default' => 'left',
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'caption_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .products-compact-grid .wrap',
			]
		);

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

		if (!defined( 'WC_PLUGIN_FILE' )) {
			return '';
		}

		global $post;
		$portfolio_posttemp = $post;
		$params = $this->get_settings_for_display();
		$grid_uid = $this->get_id();

		if(!empty($params['grid_ajax_load'])) {
			unset($params['grid_ajax_load']);
			$params['block_id'] = $grid_uid;
?>
<div id="<?php echo esc_attr($grid_uid); ?>" class="products-compact-grid ajax-load" data-params="<?php echo esc_attr(json_encode($params)); ?>">
<script type="text/javascript">
(function($) {
	$(function() {
		var $grid = $('#<?php echo esc_attr($grid_uid); ?>');
		var gridParams = $grid.data('params');
		$.ajax({
			url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
			data: {
				action: 'thegem_products_compact_grid_ajax_load',
				params: gridParams,
			},
			method: 'POST',
			timeout: 50000
		}).done(function(msg) {
			if(msg && msg.html) {
				$grid.replaceWith($(msg.html));
			}
		});
	});
})(jQuery);
</script>
</div>
<?php
			return ;
		}

		$taxonomy_filter_current = [];
		if (in_array('category', $params['source'])) {
			$taxonomy_filter_current['product_cat'] = $params['content_products_cat'];
		} else {
			$taxonomy_filter_current['product_cat'] = ['0'];
		}

		$attributes = [];
		if (in_array('attribute', $params['source'])) {
			$attrs = $params['content_products_attr'];

			if ($attrs) {
				foreach ($attrs as $attr) {
					$values = $params['content_products_attr_val_' . $attr];
					if (empty($values) || in_array('0', $values)) {
						$values = get_terms('pa_' . $attr, array('fields' => 'slugs'));
					}
					$attributes[$attr] = $values;
				}
			}
		}

		$items_per_page = $params['items_per_page'] ? intval($params['items_per_page']) : 8;

		$page = 1;
		$orderby = $params['orderby'];
		$order = $params['order'];

		$featured_only = $params['featured_only'] == 'yes';
		$sale_only = $params['sale_only'] == 'yes';
		$recently_viewed_only = !empty($params['recently_viewed_only']) && $params['recently_viewed_only'] == 'yes';
		$new_only = !empty($params['new_only']) && $params['new_only'] == 'yes';

		$product_loop = thegem_extended_products_get_posts($page, $items_per_page, $orderby, $order, $featured_only, $sale_only, $stock_only = false, $recently_viewed_only, $new_only, $taxonomy_filter_current, [], $attributes);

		if ($product_loop && $product_loop->have_posts()) :

			$portfolio_classes = array(
				'products-compact-grid woocommerce',
				'layout-' . $params['layout'],
				'columns-' . $params['columns'],
				'alignment-' . $params['caption_alignment'],
				'aspect-ratio-' . $params['image_aspect_ratio'],
				$params['product_separator'] == 'yes' ? 'with-separator' : '',
			);
			?>

			<div id="<?php echo(esc_attr($grid_uid)); ?>"
				 class="<?php echo esc_attr(implode(' ', $portfolio_classes)) ?>">
				<?php
				if ($product_loop->have_posts()) {
					while ($product_loop->have_posts()) : $product_loop->the_post();
						$preset_path = __DIR__ . '/templates/content-product-grid-item-compact.php';

						if (!empty($preset_path) && file_exists($preset_path)) {
							include($preset_path);
						}
					endwhile;
					wp_reset_postdata();
				} else { ?>
					<div class="portfolio-item not-found">
						<div class="found-wrap">
							<div class="image-inner empty"></div>
							<div class="msg">
								<?php echo wp_kses($params['not_found_text'], 'post'); ?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>

		<?php else: ?>
			<?php if (!$recently_viewed_only) { ?>
				<div class="bordered-box centered-box styled-subtitle">
					<?php echo esc_html__('Please select products in "Products" section', 'thegem') ?>
				</div>
			<?php } ?>
		<?php endif;

		$post = $portfolio_posttemp;
	}
}

if (defined('WC_PLUGIN_FILE') && function_exists('thegem_extended_products_get_posts')) {
	\Elementor\Plugin::instance()->widgets_manager->register( new TheGem_ProductsCompactGrid() );
}