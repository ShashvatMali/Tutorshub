<?php

namespace TheGem_Elementor\Widgets\ExtendedProductsCarousel;

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
use WP_Query;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Products Grid.
 */
#[\AllowDynamicProperties]
class TheGem_ExtendedProductsCarousel extends Widget_Base {

	public function __construct($data = [], $args = null) {

		$template_type = isset($GLOBALS['thegem_template_type']) ? $GLOBALS['thegem_template_type'] : thegem_get_template_type(get_the_ID());
		$this->is_product_single = $template_type === 'single-product' || (empty($template_type) && is_singular('product'));
		$this->is_product_cart = $template_type === 'cart' || (empty($template_type) && get_the_ID() == get_option('woocommerce_cart_page_id'));

		if (isset($data['settings']) && (empty($_REQUEST['action']) || !in_array($_REQUEST['action'], array('thegem_importer_process', 'thegem_templates_new', 'thegem_blocks_import')))) {

			if (!isset($data['settings']['source_type'])) {
				if ($this->is_product_single) {
					$data['settings']['source_type'] = 'related_upsell';
				} else if ($this->is_product_cart) {
					$data['settings']['source_type'] = 'cross_sell';
				} else {
					$data['settings']['source_type'] = 'custom';
				}
			}
		}

		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_EXTENDEDPRODUCTSCAROUSEL_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_EXTENDEDPRODUCTSCAROUSEL_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_EXTENDEDPRODUCTSCAROUSEL_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_EXTENDEDPRODUCTSCAROUSEL_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-products-carousel-styles',
			THEGEM_ELEMENTOR_WIDGET_EXTENDEDPRODUCTSCAROUSEL_URL . '/assets/css/thegem-product-carousel.css',
			array('owl', 'thegem-portfolio-products-extended'),
			null
		);

		wp_register_script(
			'thegem-products-carousel-scripts',
			THEGEM_ELEMENTOR_WIDGET_EXTENDEDPRODUCTSCAROUSEL_URL . '/assets/js/thegem-product-carousel.js',
			array('jquery', 'owl', 'thegem-woocommerce', 'thegem-portfolio-grid-extended'),
			null,
			true
		);

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
		return 'thegem-extended-products-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Products Carousel', 'thegem');
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
		return defined( 'WC_PLUGIN_FILE' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'thegem-eicon thegem-eicon-products-carousel';
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-product') {
			return ['thegem_single_product_builder'];
		}
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'cart') {
			return ['thegem_cart_builder'];
		}
		return ['thegem_woocommerce'];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-hovers-default',
				'thegem-hovers-zooming-blur',
				'thegem-hovers-horizontal-sliding',
				'thegem-hovers-vertical-sliding',
				'thegem-hovers-gradient',
				'thegem-hovers-circular',
				'thegem-hovers-slide',
				'thegem-hovers-fade',
				'thegem-products-carousel-styles'];
		}
		return ['thegem-products-carousel-styles'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-scroll-monitor',
				'thegem-items-animations',
				'wc-single-product',
				'wc-add-to-cart-variation',
				'thegem-product-quick-view',
				'thegem-products-carousel-scripts'];
		}
		return ['thegem-products-carousel-scripts'];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Create presets options for Select
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_presets_options() {
		$out = array_merge(
			[
				'below-default-cart-button' => [
					'label' => __('Default, Cart Button', 'thegem'),
					'group' => 'caption-below',
				],
				'below-default-cart-icon' => [
					'label' => __('Default, Cart Icon', 'thegem'),
					'group' => 'caption-below',
				],
				'below-cart-disabled' => [
					'label' => __('Solid, Cart Disabled', 'thegem'),
					'group' => 'caption-below',
				],
				'below-border-cart-icon' => [
					'label' => __('Border, Cart Icon', 'thegem'),
					'group' => 'caption-below',
				],
				'below-shadow-hover-01' => [
					'label' => __('Shadow Hover 01', 'thegem'),
					'group' => 'caption-below',
				],
				'below-shadow-hover-02' => [
					'label' => __('Shadow Hover 02', 'thegem'),
					'group' => 'caption-below',
				],
				'below-rounded-images' => [
					'label' => __('Rounded Images', 'thegem'),
					'group' => 'caption-below',
				],
				'below-rectangle-button-01' => [
					'label' => __('Rectangle Button 01', 'thegem'),
					'group' => 'caption-below',
				],
				'below-rectangle-button-02' => [
					'label' => __('Rectangle Button 02', 'thegem'),
					'group' => 'caption-below',
				],
				'below-separator-01' => [
					'label' => __('Product Separator 01', 'thegem'),
					'group' => 'caption-below',
				],
				'below-separator-02' => [
					'label' => __('Product Separator 02', 'thegem'),
					'group' => 'caption-below',
				],
				'image-default-cart-button' => [
					'label' => __('Default, Cart Button', 'thegem'),
					'group' => 'caption-image',
				],
				'image-default-cart-icon' => [
					'label' => __('Default, Cart Icon', 'thegem'),
					'group' => 'caption-image',
				],
				'image-solid-background' => [
					'label' => __('Solid Caption Background', 'thegem'),
					'group' => 'caption-image',
				],
				'image-rounded-corners' => [
					'label' => __('Rounded Corners', 'thegem'),
					'group' => 'caption-image',
				],
				'image-shadow-hover-01' => [
					'label' => __('Shadow Hover 01', 'thegem'),
					'group' => 'caption-image',
				],
				'image-shadow' => [
					'label' => __('Shadow', 'thegem'),
					'group' => 'caption-image',
				],
				'image-separator-01' => [
					'label' => __('Product Separator 01', 'thegem'),
					'group' => 'caption-image',
				],
				'image-separator-02' => [
					'label' => __('Product Separator 02', 'thegem'),
					'group' => 'caption-image',
				],
				'hover-default' => [
					'label' => __('Default', 'thegem'),
					'group' => 'caption-hover',
				],
				'hover-rounded-corners' => [
					'label' => __('Rounded Corners', 'thegem'),
					'group' => 'caption-hover',
				],
				'hover-solid-background' => [
					'label' => __('Solid Caption Background', 'thegem'),
					'group' => 'caption-hover',
				],
				'hover-separator' => [
					'label' => __('Product Separator', 'thegem'),
					'group' => 'caption-hover',
				],
				'hover-centered-caption' => [
					'label' => __('Centered Caption', 'thegem'),
					'group' => 'caption-hover',
				],
				'hover-shadow-hover' => [
					'label' => __('Shadow Hover', 'thegem'),
					'group' => 'caption-hover',
				],
				'hover-gradient-hover' => [
					'label' => __('Gradient Hover', 'thegem'),
					'group' => 'caption-hover',
				],
			]
		);
		return $out;
	}

	private function get_options_by_groups($skins, $group = false) {
		$group_labels = [
			'caption-below' => __('Caption Below', 'thegem'),
			'caption-image' => __('Caption on Image', 'thegem'),
			'caption-hover' => __('Caption on Hover', 'thegem'),
		];
		foreach ($skins as $key => $skin) {
			if (!isset($groups[$skin['group']])) {
				$groups[$skin['group']] = [
					'label' => $group_labels[$skin['group']],
					'options' => [],
				];
			}
			$groups[$skin['group']]['options'][$key] = $skin['label'];
		}

		if ($group && isset($groups[$group])) {
			return $groups[$group];
		}
		return $groups;
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
		return 'below-default-cart-button';
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
	 * Make options select products tags
	 * @access protected
	 * @return array
	 */
	protected function select_products_tags() {
		$out = [];
		$terms = get_terms( [
			'taxonomy' => 'product_tag',
			'hide_empty' => true,
		] );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return $out;
		}

		foreach ( (array) $terms as $term ) {
			if ( ! empty( $term->name ) ) {
				$out[ $term->slug ] = $term->name;
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
		$skins = $this->get_presets_options();

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'thegem_elementor_preset',
			[
				'label' => __('Skin', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'groups' => array_values($this->get_options_by_groups($skins)),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'columns_desktop',
			[
				'label' => __('Columns Desktop', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '4x',
				'options' => [
					'1x' => __('1x columns', 'thegem'),
					'2x' => __('2x columns', 'thegem'),
					'3x' => __('3x columns', 'thegem'),
					'4x' => __('4x columns', 'thegem'),
					'5x' => __('5x columns', 'thegem'),
					'6x' => __('6x columns', 'thegem'),
					'100%' => __('100% width', 'thegem'),
				],
			]
		);

		$this->add_control(
			'columns_tablet',
			[
				'label' => __('Columns Tablet', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '3x',
				'options' => [
					'1x' => __('1x columns', 'thegem'),
					'2x' => __('2x columns', 'thegem'),
					'3x' => __('3x columns', 'thegem'),
					'4x' => __('4x columns', 'thegem'),
				],
			]
		);

		$this->add_control(
			'columns_mobile',
			[
				'label' => __('Columns Mobile', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '2x',
				'options' => [
					'1x' => __('1x columns', 'thegem'),
					'2x' => __('2x columns', 'thegem'),
				],
			]
		);

		$this->add_control(
			'columns_100',
			[
				'label' => __('100% Width Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => [
					'4' => __('4 Columns', 'thegem'),
					'5' => __('5 Columns', 'thegem'),
					'6' => __('6 Columns', 'thegem'),
				],
				'condition' => [
					'columns_desktop' => '100%',
				],
				'description' => __('Number of columns for 100% width grid for desktop resolutions starting from 1920 px and above', 'thegem'),
			]
		);

		$this->add_control(
			'image_size',
			[
				'label' => __('Image Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('As in Grid Layout (TheGem Thumbnails)', 'thegem'),
					'full' => __('Full Size', 'thegem'),
					'woocommerce_thumbnail' => __('WooCommerce Thumbnail', 'thegem'),
					'woocommerce_single' => __('WooCommerce Single', 'thegem'),
					'thumbnail' => __('WordPress Thumbnail', 'thegem'),
					'medium' => __('WordPress Medium', 'thegem'),
					'medium_large' => __('WordPress Medium Large', 'thegem'),
					'large' => __('WordPress Large', 'thegem'),
					'1536x1536' => __('1536x1536', 'thegem'),
					'2048x2048' => __('2048x2048', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'image_ratio_full',
			[
				'label' => __('Image Ratio', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item:not(.custom-ratio, .double-item) .image-inner:not(.empty)' => 'aspect-ratio: {{SIZE}} !important; height: auto;',
				],
				'description' => __('Leave blank to show the original image ratio', 'thegem'),
				'condition' => [
					'image_size' => 'full'
				],
				'render_type' => 'template',
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
					'custom' => __('Custom Selection', 'thegem'),
				],
				'condition' => [
					'image_size' => 'default'
				],
			]
		);

		$this->add_control(
			'image_ratio_custom',
			[
				'label' => __('Image Ratio', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item:not(.custom-ratio, .double-item) .image-inner:not(.empty)' => 'aspect-ratio: {{SIZE}} !important; height: auto;',
				],
				'description' => __('Leave blank to show the original image ratio', 'thegem'),
				'condition' => [
					'image_size' => 'default',
					'image_aspect_ratio' => 'custom'
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'disable_preloader',
			[
				'label' => __('Disable Preloader', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->end_controls_section();

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

		if ($this->is_product_single) {

			$this->add_control(
				'source_type',
				[
					'label' => __('Products Source', 'thegem'),
					'default' => 'related_upsell',
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'options' => [
						'related_upsell' => __('Display related or upsell products', 'thegem'),
						'custom' => __('Display products by categories / attributes', 'thegem'),
					],
				]
			);

		} else if ($this->is_product_cart) {

			$this->add_control(
				'source_type',
				[
					'label' => __('Products Source', 'thegem'),
					'default' => 'cross_sell',
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'options' => [
						'cross_sell' => __('Cross-Sell Products', 'thegem'),
						'custom' => __('Display products by categories / attributes', 'thegem'),
					],
				]
			);

		} else {

			$this->add_control(
				'source_type',
				[
					'type' => Controls_Manager::HIDDEN,
					'default' => 'custom',
				]
			);
		}

		$this->add_control(
			'related_upsell_source',
			[
				'label' => __('Select Products to Display', 'thegem'),
				'default' => 'related',
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'related' => __('Related Products', 'thegem'),
					'upsell' => __('Upsell Products', 'thegem')
				],
				'condition' => [
					'source_type' => 'related_upsell',
				],
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
					'tag' => __('Tag', 'thegem'),
					'products' => __('Products', 'thegem'),
				],
				'condition' => [
					'source_type' => 'custom',
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
					'source_type' => 'custom',
					'source' => 'category',
				],
			]
		);

		$this->add_control(
			'content_products_attr',
			[
				'label' => __('Select Products Attributes', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->select_products_attributes(),
				'default' => '0',
				'frontend_available' => true,
				'condition' => [
					'source_type' => 'custom',
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
								'source_type' => 'custom',
								'source' => 'attribute',
								'content_products_attr' => $attr->attribute_name,
							],
						]
					);
				}
			}
		}

		$this->add_control(
			'content_products_tags',
			[
				'label' => __( 'Select Products Tags', 'thegem' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->select_products_tags(),
				'frontend_available' => true,
				'label_block' => true,
				'condition' => [
					'source_type' => 'custom',
					'source' => 'tag',
				],
			]
		);

		$this->add_control(
			'content_products',
			[
				'label' => __('Select Products', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => 'product',
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'source_type' => 'custom',
					'source' => 'products',
				],
				'description' => __('Add products by title.', 'thegem'),
			]
		);

		$this->add_control(
			'offset',
			[
				'label' => __('Offset', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'description' => __('Number of items to displace or pass over', 'thegem'),
			]
		);

		$this->add_control(
			'exclude_type',
			[
				'label' => __('Exclude', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'manual' => __('Manual Selection', 'thegem'),
					'current' => __('Current Post', 'thegem'),
					'term' => __('Term', 'thegem'),
				],
				'default' => 'manual',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'exclude_products',
			[
				'label' => __('Exclude Products', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => 'product',
				'label_block' => true,
				'multiple' => true,
				'description' => __('Add products by title.', 'thegem'),
				'condition' => [
					'exclude_type' => 'manual',
				],
			]
		);

		$this->add_control(
			'exclude_product_terms',
			[
				'label' => __('Exclude Product Terms', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_taxonomy_terms_by_query',
				'render' => 'thegem_get_taxonomy_terms_by_id',
				'post_type' => 'product',
				'label_block' => true,
				'multiple' => true,
				'description' => __('Add product term by name.', 'thegem'),
				'condition' => [
					'exclude_type' => 'term',
				],
			]
		);

		$this->add_control(
			'sorting_header',
			[
				'label' => __('Default Sorting', 'thegem'),
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
			'section_cross_sell_title',
			[
				'label' => __('Title', 'thegem'),
				'condition' => [
					'source_type' => 'cross_sell',
				],
			]
		);

		$this->add_control(
			'show_cross_sell_title', [
				'label' => __('Title', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
			]
		);

		$this->add_control(
			'cross_sell_title_text',
			[
				'label' => __('Title Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('You may also like', 'thegem'),
				'condition' => [
					'show_cross_sell_title' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_responsive_control(
			'cross_sell_title_alignment',
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
				'default' => 'left',
				'selectors_dictionary' => [
					'left' => 'justify-content: left; text-align: left;',
					'right' => 'justify-content: right; text-align: right;',
					'center' => 'justify-content: center; text-align: center;',
					'justify' => 'justify-content: space-between; text-align: justify;',
				],
				'selectors' => [
					'{{WRAPPER}} .cross-sell-title' => '{{VALUE}};',
				],
				'condition' => [
					'show_cross_sell_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'cross_sell_title_tag',
			[
				'label' => __('HTML Tag', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => __('H1', 'thegem'),
					'h2' => __('H2', 'thegem'),
					'h3' => __('H3', 'thegem'),
					'h4' => __('H4', 'thegem'),
					'h5' => __('H5', 'thegem'),
					'h6' => __('H6', 'thegem'),
					'p' => __('p', 'thegem'),
					'div' => __('div', 'thegem'),
				],
				'default' => 'div',
				'condition' => [
					'show_cross_sell_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'cross_sell_title_style',
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
				'default' => 'title-h3',
				'condition' => [
					'show_cross_sell_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'cross_sell_title_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => 'light',
				'condition' => [
					'show_cross_sell_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'cross_sell_title_letter_spacing',
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
					'{{WRAPPER}} .cross-sell-title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_cross_sell_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'cross_sell_title_transform',
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
					'{{WRAPPER}} .cross-sell-title' => '{{VALUE}};',
				],
				'condition' => [
					'show_cross_sell_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'cross_sell_title_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .cross-sell-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_cross_sell_title' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'cross_sell_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .cross-sell-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_cross_sell_title' => 'yes',
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
			'caption_position',
			[
				'label' => __('Caption Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'page',
				'options' => [
					'page' => __('Below Image', 'thegem'),
					'hover' => __('On Hover', 'thegem'),
					'image' => __('On Image', 'thegem'),
				],
			]
		);

		$this->add_responsive_control(
			'product_show_categories', [
				'label' => __('Categories', 'thegem'),
				'desktop_default' => 'yes',
				'tablet_default' => 'yes',
				'mobile_default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'selectors_dictionary' => [
					'' => 'none',
					'yes' => 'block',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .categories' => 'display: {{VALUE}}',
				],
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
			'truncate_titles',
			[
				'label' => __('Truncate Title (Lines)', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .caption .title span, {{WRAPPER}} .portfolio-item .caption .title a' => 'max-height: initial; white-space: initial; display: -webkit-box; -webkit-line-clamp: {{VALUE}}; line-clamp: {{VALUE}}; -webkit-box-orient: vertical;',
				],
				'condition' => [
					'product_show_title' => 'yes',
				]
			]
		);

		$this->add_control(
			'product_show_description', [
				'label' => __('Product Description', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'hover',
				],
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

		$this->add_responsive_control(
			'product_show_reviews', [
				'label' => __('Reviews', 'thegem'),
				'desktop_default' => 'yes',
				'tablet_default' => 'yes',
				'mobile_default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'selectors_dictionary' => [
					'' => 'none',
					'yes' => 'block',
				],
				'selectors' => [
					'{{WRAPPER}} .reviews' => 'display: {{VALUE}}',
				],
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
			'add_to_cart_header',
			[
				'label' => __('Add to Cart', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_show_add_to_cart', [
				'label' => __('Add to Cart', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'product_show_add_to_cart_mobiles', [
				'label' => __('Show On Mobiles', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'product_show_add_to_cart' => 'yes',
				],
			]
		);

		$this->add_control(
			'add_to_cart_type',
			[
				'label' => __('Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'icon' => __('Icon', 'thegem'),
					'button' => __('Button', 'thegem'),
				],
				'default' => 'button',
				'frontend_available' => true,
				'condition' => [
					'product_show_add_to_cart' => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_button_show_icon', [
				'label' => __('Show Icon', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'product_show_add_to_cart' => 'yes',
					'add_to_cart_type' => 'button',
				],
			]
		);

		$this->add_control(
			'cart_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Add To Cart', 'thegem'),
				'condition' => [
					'product_show_add_to_cart' => 'yes',
					'add_to_cart_type' => 'button',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'cart_icon',
			[
				'label' => __('Cart Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'product_show_add_to_cart',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'add_to_cart_type',
									'operator' => '=',
									'value' => 'icon',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'add_to_cart_type',
											'operator' => '=',
											'value' => 'button',
										], [
											'name' => 'cart_button_show_icon',
											'operator' => '=',
											'value' => 'yes',
										],
									]
								]
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'select_options_button_text',
			[
				'label' => __('Select Options Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Select Options', 'thegem'),
				'condition' => [
					'product_show_add_to_cart' => 'yes',
					'add_to_cart_type' => 'button',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'select_options_icon',
			[
				'label' => __('Select Options Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'product_show_add_to_cart',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'add_to_cart_type',
									'operator' => '=',
									'value' => 'icon',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'add_to_cart_type',
											'operator' => '=',
											'value' => 'button',
										], [
											'name' => 'cart_button_show_icon',
											'operator' => '=',
											'value' => 'yes',
										],
									]
								]
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'add_to_wishlist_header',
			[
				'label' => __('Add to Wishlist', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		if (!defined('YITH_WCWL')) {

			$this->add_control(
				'wishlist_warning_text',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('Please install “YITH WooCommerce Wishlist” plugin to activate “add to wishlist” feature.', 'thegem'),
					'content_classes' => 'elementor-descriptor',
				]
			);

		} else {

			$this->add_control(
				'product_show_wishlist', [
					'label' => __('Add to Wishlist', 'thegem'),
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true
				]
			);

			$this->add_control(
				'add_wishlist_icon',
				[
					'label' => __('Add to Wishlist Icon', 'thegem'),
					'type' => Controls_Manager::ICONS,
					'condition' => [
						'product_show_wishlist' => 'yes',
					],
				]
			);

			$this->add_control(
				'added_wishlist_icon',
				[
					'label' => __('Added to Wishlist Icon', 'thegem'),
					'type' => Controls_Manager::ICONS,
					'condition' => [
						'product_show_wishlist' => 'yes',
					],
				]
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pagination',
			[
				'label' => __('Navigation', 'thegem'),
			]
		);

		$this->add_control(
			'items_per_page',
			[
				'label' => __('Number of Items', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => 12,
				'description' => __('Use -1 to show all', 'thegem'),
			]
		);

		$this->add_control(
			'show_dots_navigation',
			[
				'label' => __('Dots Navigation', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_arrows_navigation',
			[
				'label' => __('Arrows Navigation', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'arrows_navigation_position',
			[
				'label' => __('Arrows Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'outside',
				'options' => [
					'outside' => __('Outside Product Items', 'thegem'),
					'on' => __('On Product Items', 'thegem'),
				],
				'condition' => [
					'show_arrows_navigation' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_navigation_visibility',
			[
				'label' => __('Arrows Visibility', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover' => __('Visible on Hover', 'thegem'),
					'always' => __('Always Visible', 'thegem'),
				],
				'condition' => [
					'show_arrows_navigation' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_navigation_left_icon',
			[
				'label' => __('Left Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_arrows_navigation' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_navigation_right_icon',
			[
				'label' => __('Right Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_arrows_navigation' => 'yes',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_labels',
			[
				'label' => __('Labels', 'thegem'),
			]
		);

		$this->add_control(
			'labels_design',
			[
				'label' => __('Labels Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => __('Style 1', 'thegem'),
					'2' => __('Style 2', 'thegem'),
					'3' => __('Style 3', 'thegem'),
					'4' => __('Style 4', 'thegem'),
					'5' => __('Style 5', 'thegem'),
					'6' => __('Style 6', 'thegem'),
				],
			]
		);

		$this->add_control(
			'product_show_new', [
				'label' => __('"New" Label', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'new_label_text',
			[
				'label' => __('Label Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('New', 'thegem'),
				'condition' => [
					'product_show_new' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'product_show_sale', [
				'label' => __('"Sale" Label', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'sale_label_type',
			[
				'label' => __('Sale Label Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'percentage',
				'options' => [
					'percentage' => __('Show Discount Percentage', 'thegem'),
					'text' => __('Show Text', 'thegem'),
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$this->add_control(
			'sale_label_prefix',
			[
				'label' => __('Prefix', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('-', 'thegem'),
				'condition' => [
					'product_show_sale' => 'yes',
					'sale_label_type' => 'percentage',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'sale_label_suffix',
			[
				'label' => __('Suffix', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('%', 'thegem'),
				'condition' => [
					'product_show_sale' => 'yes',
					'sale_label_type' => 'percentage',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'sale_label_text',
			[
				'label' => __('Label Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('On Sale', 'thegem'),
				'condition' => [
					'product_show_sale' => 'yes',
					'sale_label_type' => 'text',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'product_show_out', [
				'label' => __('"Out of stock" Label', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'out_label_text',
			[
				'label' => __('Label Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Out of stock', 'thegem'),
				'condition' => [
					'product_show_out' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs',
			[
				'label' => __('Product Tabs', 'thegem'),
			]
		);

		$this->add_control(
			'product_show_tabs', [
				'label' => __('Show Tabs', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'filters_tabs_style',
			[
				'label' => __('Tabs Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'alternative' => __('Alternative', 'thegem'),
				],
				'condition' => [
					'product_show_tabs' => 'yes',
				],
			]
		);

		$this->add_control(
			'filters_tabs_title_header',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_tabs' => 'yes',
				],
			]
		);

		$this->add_control(
			'filters_tabs_title_text',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'condition' => [
					'product_show_tabs' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filters_tabs_title_style_preset',
			[
				'label' => __('Style Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold' => __('Bold Title', 'thegem'),
					'thin' => __('Thin Title', 'thegem'),
				],
				'condition' => [
					'product_show_tabs' => 'yes',
				],
			]
		);

		$this->add_control(
			'filters_tabs_title_separator',
			[
				'label' => __('Separator', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'product_show_tabs' => 'yes',
					'filters_tabs_style' => 'alternative',
				],
			]
		);

		$this->add_control(
			'filters_tabs_tabs_header',
			[
				'label' => __('Tabs', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_tabs' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'filters_tabs_tab_title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Tabs Title', 'thegem'),
				'default' => __('Title', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'filters_tabs_tab_filter_by',
			[
				'label' => __('Filter By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'default' => 'categories',
				'options' => [
					'categories' => __('Categories', 'thegem'),
					'featured' => __('Featured Products', 'thegem'),
					'sale' => __('On Sale Products', 'thegem'),
					'recent' => __('Recent Products', 'thegem'),
				],
			]
		);

		$repeater->add_control(
			'filters_tabs_tab_products_cat',
			[
				'label' => __('Select Category', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => $this->select_products_sets(true),
				'frontend_available' => true,
				'condition' => [
					'filters_tabs_tab_filter_by' => 'categories',
				],
			]
		);

		$this->add_control(
			'filters_tabs_tabs',
			[
				'type' => Controls_Manager::REPEATER,
				'label' => __('Tabs', 'thegem'),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ filters_tabs_tab_title }}}',
				'default' => [
					[
						'filters_tabs_tab_title' => __('Featured Products', 'thegem'),
						'filters_tabs_tab_filter_by' => 'featured',
					],
					[
						'filters_tabs_tab_title' => __('On Sale Products', 'thegem'),
						'filters_tabs_tab_filter_by' => 'sale',
					]
				],
				'condition' => [
					'product_show_tabs' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_notification',
			[
				'label' => __('Notification', 'thegem'),
			]
		);

		$this->add_control(
			'added_cart_text',
			[
				'label' => __('“Added to Cart” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => thegem_get_option('product_archive_added_cart_text'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'added_wishlist_text',
			[
				'label' => __('“Added to Wishlist” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => thegem_get_option('product_archive_added_wishlist_text'),
				'condition' => [
					'product_show_wishlist' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'removed_wishlist_text',
			[
				'label' => __('“Removed from Wishlist” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => thegem_get_option('product_archive_removed_wishlist_text'),
				'condition' => [
					'product_show_wishlist' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'view_cart_button_text',
			[
				'label' => __('“View Cart” Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => thegem_get_option('product_archive_view_cart_button_text'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'checkout_button_text',
			[
				'label' => __('“Checkout” Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => thegem_get_option('product_archive_checkout_button_text'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'view_wishlist_button_text',
			[
				'label' => __('“View Wishlist” Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => thegem_get_option('product_archive_view_wishlist_button_text'),
				'condition' => [
					'product_show_wishlist' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'not_found_text',
			[
				'label' => __('“No Products Found” Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'label_block' => true,
				'default' => thegem_get_option('product_archive_not_found_text'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sharing',
			[
				'label' => __('Social Sharing', 'thegem'),
			]
		);

		$this->add_control(
			'social_sharing',
			[
				'label' => __('Social Sharing', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'sharing_icon',
			[
				'label' => __('Sharing Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'social_sharing' => 'yes'
				],
			]
		);

		$share_options = [
			'facebook' => __('Facebook', 'thegem'),
			'twitter' => __('Twitter (X)', 'thegem'),
			'pinterest' => __('Pinterest', 'thegem'),
			'tumblr' => __('Tumblr', 'thegem'),
			'linkedin' => __('Linkedin', 'thegem'),
			'reddit' => __('Reddit', 'thegem'),
		];

		foreach ($share_options as $ekey => $elem) {

			$this->add_control(
				'shares_show_' . $ekey, [
					'label' => $elem,
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('Show', 'thegem'),
					'label_off' => __('Hide', 'thegem'),
					'condition' => [
						'social_sharing' => 'yes',
					]
				]
			);
		}

		$this->end_controls_section();


		$this->start_controls_section(
			'section_additional',
			[
				'label' => __('Additional Options', 'thegem'),
			]
		);

		$this->add_control(
			'additional_display_header',
			[
				'label' => __('Additional Display Options', 'thegem'),
				'type' => Controls_Manager::HEADING,
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
			'stock_only',
			[
				'label' => __('Hide “out of stock“ products', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'quick_view_group_header',
			[
				'label' => __('Quick View', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		if (thegem_get_option('product_quick_view')) {
			$quick_default = 'yes';
		} else {
			$quick_default = '';
		}

		$this->add_control(
			'quick_view',
			[
				'label' => __('Quick View', 'thegem'),
				'default' => $quick_default,
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'quick_view_text',
			[
				'label' => __('Quick View Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Quick View', 'thegem'),
				'condition' => [
					'quick_view' => 'yes',
					'caption_position' => 'page',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'other_options_header',
			[
				'label' => __('Other Options', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sliding_animation',
			[
				'label' => __('Sliding Animation', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'one-by-one' => __('One-by-One', 'thegem'),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slider_loop',
			[
				'label' => __('Slider Loop', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'autoscroll',
			[
				'label' => __('Autoscroll', 'thegem'),
				'default' => 'no',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'autoscroll_speed',
			[
				'label' => __('Autoplay Speed', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 500,
					],
				],
				'default' => [
					'size' => 2000,
				],
				'condition' => [
					'autoscroll' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_scroll_init',
			[
				'label' => __('Init carousel on scroll', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'description' => __('This option allows you to init carousel script only when visitor scroll the page to the slider. Useful for performance optimization.', 'thegem'),
			]
		);

		$this->add_control(
			'loading_animation',
			[
				'label' => __('Lazy Loading Animation', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'animation_effect',
			[
				'label' => __('Animation Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'bounce',
				'options' => [
					'bounce' => __('Bounce', 'thegem'),
					'move-up' => __('Move Up', 'thegem'),
					'fade-in' => __('Fade In', 'thegem'),
					'fall-perspective' => __('Fall Perspective', 'thegem'),
					'scale' => __('Scale', 'thegem'),
					'flip' => __('Flip', 'thegem'),
				],
				'condition' => [
					'loading_animation' => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_hook',
			[
				'label' => __('WooCommerce Add to Cart Hook', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
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

		/* Images Style */
		$this->image_style($control);

		/* Caption Style */
		$this->caption_style($control);

		/* Caption Container Style */
		$this->caption_container_style($control);

		/* Icons Style */
		$this->icons_style($control);

		/* Buttons Style */
		$this->buttons_style($control);

		/* Product Tabs Style */
		$this->product_tabs_style($control);

		/* Navigation Style */
		$this->navigation_style($control);

		/* Labels Style */
		$this->labels_style($control);

		/* Notification Style */
		$this->notification_style($control);
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
			'image_gaps',
			[
				'label' => __('Gaps', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'render_type' => 'template',
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
					'size' => 42,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid.item-separator .portfolio-item ' => 'padding: calc({{SIZE}}{{UNIT}}/2) !important;',
					'{{WRAPPER}} .portfolio.extended-products-grid:not(.item-separator) .fullwidth-block' => 'padding: 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.extended-products-grid:not(.item-separator).has-shadowed-items .owl-carousel .owl-stage-outer' => 'padding: calc({{SIZE}}{{UNIT}}/2) !important; margin: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .extended-products-grid-carousel-item:not(.inited) .portfolio-item,
					{{WRAPPER}} .skeleton-posts .portfolio-item' => 'padding: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .extended-products-grid-carousel-item:not(.inited) .owl-stage,
					{{WRAPPER}} .skeleton-posts.portfolio-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2);',
				]
			]
		);

		$control->add_control(
			'product_separator',
			[
				'label' => __('Product Separator', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$control->add_control(
			'product_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .portfolio.item-separator .portfolio-item:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item:after, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:after' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'product_separator' => 'yes',
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
					'{{WRAPPER}} .portfolio.item-separator .portfolio-item:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item:after, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:after' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.item-separator .portfolio-item:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item:after' => 'height: calc(100% + {{SIZE}}{{UNIT}}); top: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:after' => 'width: calc(100% + {{SIZE}}{{UNIT}}); left: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .owl-carousel .owl-stage-outer' => 'padding: calc({{SIZE}}{{UNIT}}/2); width: calc(100% + {{SIZE}}{{UNIT}}); margin-left: calc(-{{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'product_separator' => 'yes',
				],
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
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .image-inner,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay,
					{{WRAPPER}} .portfolio.extended-products-grid.caption-position-hover .portfolio-item .wrap,
					{{WRAPPER}} .portfolio.extended-products-grid.caption-position-image .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.extended-products-grid.caption-position-page .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
				],
			]
		);

		$control->add_responsive_control(
			'image_content_paddings',
			[
				'label' => __('Content Paddings', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons' => 'padding-top: {{TOP}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption' => 'padding-bottom: {{BOTTOM}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links' => 'padding: 0;',
				],
				'condition' => [
					'caption_position!' => 'page',
				],
			]
		);


		$control->add_control(
			'image_hover_effect_page',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'slide' => __('Show Next Image (Slide)', 'thegem'),
					'fade' => __('Show Next Image (Fade)', 'thegem'),
					'disabled' => __('Disabled', 'thegem'),
				],
				'condition' => [
					'caption_position' => 'page',
				],
			]
		);

		$control->add_control(
			'image_hover_effect_image',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'slide' => __('Show Next Image (Slide)', 'thegem'),
					'fade' => __('Show Next Image (Fade)', 'thegem'),
					'gradient' => __('Gradient', 'thegem'),
					'circular' => __('Circular Overlay', 'thegem'),
					'disabled' => __('Disabled', 'thegem'),
				],
				'condition' => [
					'caption_position' => 'image',
				],
			]
		);

		$control->add_control(
			'image_hover_effect_hover',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'slide' => __('Show Next Image (Slide)', 'thegem'),
					'fade' => __('Show Next Image (Fade)', 'thegem'),
					'default' => __('Cyan Breeze', 'thegem'),
					'zooming-blur' => __('Zooming White', 'thegem'),
					'horizontal-sliding' => __('Horizontal Sliding', 'thegem'),
					'vertical-sliding' => __('Vertical Sliding', 'thegem'),
					'gradient' => __('Gradient', 'thegem'),
					'circular' => __('Circular Overlay', 'thegem'),
					'disabled' => __('Disabled', 'thegem'),
				],
				'condition' => [
					'caption_position' => 'hover',
				],
			]
		);

		$control->add_control(
			'image_hover_effect_fallback',
			[
				'label' => __('Fallback Hover', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'zooming',
				'options' => [
					'disabled' => __('Disabled', 'thegem'),
					'zooming' => __('Zooming White', 'thegem'),
				],
				'description' => __('Used in case of only one product image', 'thegem'),
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'page',
								],
								[
									'name' => 'image_hover_effect_page',
									'operator' => 'in',
									'value' => ['slide', 'fade']
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'image',
								],
								[
									'name' => 'image_hover_effect_image',
									'operator' => 'in',
									'value' => ['slide', 'fade']
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
									'operator' => 'in',
									'value' => ['slide', 'fade']
								],
							],
						],
					],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'image_hover_overlay',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'default' => 'classic',
				'fields_options' => [
					'background' => [
						'label' => _x('Overlay Type', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.extended-products-grid.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.extended-products-grid.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.extended-products-grid.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'image',
								],
								[
									'name' => 'image_hover_effect_image',
									'operator' => '!in',
									'value' => ['slide', 'fade'],
								],
							]
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
									'operator' => '!in',
									'value' => ['slide', 'fade'],
								],
							]
						]
					]
				]
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_hover_css',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item:hover .image-inner,
				{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.hover-effect .image-inner',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'image',
								],
								[
									'name' => 'image_hover_effect_image',
									'operator' => '!in',
									'value' => ['slide', 'fade'],
								],
							]
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
									'operator' => '!in',
									'value' => ['slide', 'fade'],
								],
							]
						]
					]
				]
			]
		);

		$control->start_controls_tabs('full_item_border_tabs');

		$control->start_controls_tab('full_item_border_tab_normal', ['label' => __('Normal', 'thegem')]);


		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'full_item_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image',
			]
		);

		$control->remove_control('full_item_border_type_color');


		$control->add_control(
			'full_item_border_color_normal',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'full_item_border_type_border!' => '',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow_normal',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid:not(.shadowed-container) .portfolio-item .image, 
						{{WRAPPER}} .portfolio.extended-products-grid.shadowed-container .portfolio-item .wrap',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('full_item_border_tab_hover', ['label' => __('Hover', 'thegem')]);

		$control->add_control(
			'full_item_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#dfe5e8',
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item:hover .image,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.hover-effect .image' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'full_item_border_type_border!' => '',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow_hover',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid:not(.shadowed-container) .portfolio-item:hover .image,
					{{WRAPPER}} .portfolio.extended-products-grid:not(.shadowed-container) .portfolio-item.hover-effect .image, 
					{{WRAPPER}} .portfolio.extended-products-grid.shadowed-container .portfolio-item:hover .wrap, 
					{{WRAPPER}} .portfolio.extended-products-grid.shadowed-container .portfolio-item.hover-effect .wrap',
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_control(
			'shadowed_container',
			[
				'label' => __('Apply shadow on caption container', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'page',
						],
					],
				],
			]
		);


		$control->add_control(
			'quick_view_header',
			[
				'label' => __('Quick View', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'quick_view' => 'yes',
					'caption_position' => 'page',
				],
			]
		);

		$control->add_control(
			'quick_view_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .quick-view-button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'quick_view' => 'yes',
					'caption_position' => 'page',
				],
			]
		);

		$control->add_control(
			'quick_view_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .quick-view-button' => 'color: {{VALUE}};',
				],
				'condition' => [
					'quick_view' => 'yes',
					'caption_position' => 'page',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'fquick_view_text_typography',
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .quick-view-button',
				'condition' => [
					'quick_view' => 'yes',
					'caption_position' => 'page',
				],
			]
		);

		$control->add_control(
			'fullwidth_section_images',
			[
				'label' => __('Better Thumbnails Quality', 'thegem'),
				'separator' => 'before',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'description' => __('Activate for better image quality in case of using in fullwidth section', 'thegem'),
				'condition' => [
					'columns_desktop!' => '100%',
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
			'font_size_preset',
			[
				'label' => __('Font size preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'enlarged',
				'options' => [
					'enlarged' => __('Enlarged', 'thegem'),
					'normal' => __('Normal', 'thegem'),
				],
				'condition' => [
					'caption_position' => 'hover',
					'columns_desktop' => ['1x', '2x'],
				],
			]
		);

		$control->add_control(
			'caption_container_header_hover',
			[
				'label' => __('Container', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'image',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
									'operator' => 'in',
									'value' => ['slide', 'fade']
								],
							],
						],
					],
				],
			]
		);

		$control->add_control(
			'caption_container_preset_hover',
			[
				'label' => __('Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => __('Light Gradient', 'thegem'),
					'dark' => __('Dark Gradient', 'thegem'),
					'solid' => __('Solid transparent', 'thegem'),
					'transparent' => __('Transparent', 'thegem'),
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'image',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
									'operator' => 'in',
									'value' => ['slide', 'fade']
								],
							],
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_height_hover',
			[
				'label' => __('Container Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links-wrapper .links' => 'height: {{SIZE}}%',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'image',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
									'operator' => 'in',
									'value' => ['slide', 'fade']
								],
							],
						],
					],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'caption_container_background_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links-wrapper .links',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'image',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
									'operator' => 'in',
									'value' => ['slide', 'fade']
								],
							],
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_alignment_hover',
			[
				'label' => __('Content Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'separator' => 'after',
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
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'image',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
									'operator' => 'in',
									'value' => ['slide', 'fade']
								],
							],
						],
					],
				],
			]
		);

		$caption_options = [
			'categories' => __('Categories', 'thegem'),
			'title' => __('Product Title', 'thegem'),
			'price' => __('Product Price', 'thegem'),
		];

		foreach ($caption_options as $ekey => $elem) {

			$title_condition = [];
			$condition = [
				'terms' => [
					[
						'name' => 'caption_position',
						'operator' => 'in',
						'value' => ['hover', 'image'],
					],
				],
			];
			$separator = 'before';

			if ($ekey == 'categories') {
				$condition = [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => 'in',
							'value' => ['hover', 'image'],
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'product_show_categories',
									'operator' => '=',
									'value' => 'yes',
								],
								[
									'name' => 'product_show_categories_tablet',
									'operator' => '=',
									'value' => 'yes',
								],
								[
									'name' => 'product_show_categories_mobile',
									'operator' => '=',
									'value' => 'yes',
								],
							],
						],
					],
				];
				$selector = '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .overlay .caption .categories,
				{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .overlay .caption .categories a';
				$separator = 'default';
			} else if ($ekey == 'title') {
				$selector = '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .overlay .caption .title, 
				{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .overlay .caption .title a';
			} else if ($ekey == 'price') {
				$selector = '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .caption .product-price .price,
				{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .empty-price';
			} else {
				$selector = '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .overlay .caption .' . $ekey;
			}

			$control->add_control(
				$ekey . '_header',
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'separator' => $separator,
					'condition' => $title_condition
				]
			);

			$control->add_group_control(Group_Control_Typography::get_type(),
				[
					'label' => __('Typography', 'thegem'),
					'name' => $ekey . '_typography',
					'selector' => $selector,
					'conditions' => $condition
				]
			);

			$control->add_control(
				$ekey . '_color',
				[
					'label' => __('Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						$selector => 'color: {{VALUE}} !important;',
					],
					'conditions' => $condition
				]
			);

			if ($ekey == 'categories') {
				$control->add_control(
					$ekey . '_background_color',
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .caption .categories' => 'background-color: {{VALUE}} !important;',
						],
						'conditions' => $condition
					]
				);
			}

			$control->start_controls_tabs($ekey . '_tabs', [
				'condition' => [
					'caption_position' => 'page',
				],
			]);

			if (!empty($control->states_list)) {
				foreach ((array)$control->states_list as $stkey => $stelem) {
					$condition = [];
					$state = '';
					$state_touch = '';
					if ($stkey == 'active') {
						continue;
					} else if ($stkey == 'hover') {
						$condition = [
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'page',
								],
							],
						];
						$state = ':hover';
						$state_touch = '.hover-effect';
					}

					if ($ekey == 'categories') {
						$condition = [
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'page',
								],
								[
									'relation' => 'or',
									'terms' => [
										[
											'name' => 'product_show_categories',
											'operator' => '=',
											'value' => 'yes',
										],
										[
											'name' => 'product_show_categories_tablet',
											'operator' => '=',
											'value' => 'yes',
										],
										[
											'name' => 'product_show_categories_mobile',
											'operator' => '=',
											'value' => 'yes',
										],
									],
								],
							],
						];
						if ($stkey == 'hover') {
							$selector = '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .caption .categories a' . $state;
						} else {
							$selector = '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .caption .categories,
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .caption .categories a';
						}
					} else if ($ekey == 'title') {
						$selector = '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .caption .title a' . $state;
					} else if ($ekey == 'price') {
						$selector = '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product' . $state . '  .caption .product-price .price';

						if ($stkey == 'hover') {
							$selector .= ', {{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product' . $state_touch . '  .caption .product-price .price';
						}
					}

					$control->start_controls_tab($ekey . '_tab_' . $stkey, [
						'label' => $stelem,
						'conditions' => $condition
					]);

					$control->add_group_control(Group_Control_Typography::get_type(),
						[
							'label' => __('Typography', 'thegem'),
							'name' => $ekey . '_typography_' . $stkey,
							'selector' => $selector,
							'conditions' => $condition
						]
					);

					$control->add_control(
						$ekey . '_color_' . $stkey,
						[
							'label' => __('Color', 'thegem'),
							'type' => Controls_Manager::COLOR,
							'label_block' => false,
							'selectors' => [
								$selector => 'color: {{VALUE}};',
							],
							'conditions' => $condition
						]
					);

					$control->end_controls_tab();

				}
			}

			$control->end_controls_tabs();
		}

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
					'{{WRAPPER}} .woocommerce .portfolio-item .star-rating > span:before' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'caption_position' => ['hover', 'image'],
				]
			]
		);

		$control->add_control(
			'base_color',
			[
				'label' => __('Base Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .portfolio-item .star-rating:before' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'caption_position' => ['hover', 'image'],
				]
			]
		);

		$control->start_controls_tabs('rating_tabs', [
			'condition' => [
				'caption_position' => 'page',
			],
		]);

		$control->start_controls_tab('rating_tab_normal', [
			'label' => __('Normal', 'thegem'),
		]);

		$control->add_control(
			'rated_color_normal',
			[
				'label' => __('Rated Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .portfolio-item .star-rating > span:before' => 'color: {{VALUE}} !important;',
				]
			]
		);

		$control->add_control(
			'base_color_normal',
			[
				'label' => __('Base Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .portfolio-item .star-rating:before' => 'color: {{VALUE}} !important;',
				]
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('rating_tab_hover', [
			'label' => __('Hover', 'thegem'),
			'condition' => ['caption_position' => 'page']
		]);

		$control->add_control(
			'rated_color_hover',
			[
				'label' => __('Rated Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .portfolio-item:hover .star-rating > span:before,
					{{WRAPPER}} .woocommerce .portfolio-item.hover-effect .star-rating > span:before' => 'color: {{VALUE}} !important;',
				],
				'condition' => ['caption_position' => 'page']
			]
		);

		$control->add_control(
			'base_color_hover',
			[
				'label' => __('Base Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .portfolio-item:hover .star-rating:before,
					{{WRAPPER}} .woocommerce .portfolio-item.hover-effect .star-rating:before' => 'color: {{VALUE}} !important;',
				],
				'condition' => ['caption_position' => 'page']
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

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
				'condition' => ['caption_position' => 'page']
			]
		);

		$control->add_control(
			'caption_container_preset',
			[
				'label' => __('Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'transparent',
				'options' => [
					'transparent' => __('Transparent', 'thegem'),
					'white' => __('White', 'thegem'),
					'gray ' => __('Gray', 'thegem'),
					'dark' => __('Dark', 'thegem'),
				]
			]
		);

		$control->add_responsive_control(
			'caption_container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.extended-products-grid.title-on-page .portfolio-item .wrap' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_alignment',
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
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .star-rating' => 'margin-{{VALUE}}: 0',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-rating .empty-rating:before' => 'margin-{{VALUE}}: 0',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .categories' => 'margin-{{VALUE}}: 0',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom' => 'margin-{{VALUE}}: 0',
				],
			]
		);

		$control->start_controls_tabs('caption_container_tabs');

		$control->start_controls_tab('caption_container_tab_normal', ['label' => __('Normal', 'thegem')]);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'caption_container_background_page_normal',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'caption_container_border_normal',
				'label' => __('Border', 'thegem'),
//				'fields_options' => [
//					'color' => [
//						'default' => '#dfe5e8',
//					],
//				],
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('caption_container_tab_hover', ['label' => __('Hover', 'thegem')]);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'caption_container_background_page_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item:hover .wrap > .caption,
				{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.hover-effect .wrap > .caption',
			]
		);

		$control->add_control(
			'caption_container_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item:hover .wrap > .caption,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.hover-effect .wrap > .caption' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'caption_container_border_normal_border!' => '',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_control(
			'spacing_title_categories',
			[
				'label' => 'Categories',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'spacing_categories_top',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .categories' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_control(
			'spacing_title_header',
			[
				'label' => 'Product Title',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'spacing_title',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'spacing_price_header',
			[
				'label' => 'Product Price',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'spacing_price',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$control->add_control(
			'spacing_separator_header',
			[
				'label' => 'Separator',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'spacing_separator_weight',
			[
				'label' => __('Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
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
					'size' => 1,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .product-info .product-rating .empty-rating:before' => 'border-width: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->start_controls_tabs('spacing_separator_tabs');

		$control->start_controls_tab('spacing_separator_tab_normal', ['label' => __('Normal', 'thegem')]);

		$control->add_control(
			'spacing_separator_color_normal',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .product-info .product-rating .empty-rating:before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'spacing_separator_size_normal',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
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
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .product-info .product-rating .empty-rating:before' => 'width: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('spacing_separator_tab_hover', ['label' => __('Hover', 'thegem')]);

		$control->add_control(
			'spacing_separator_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item:hover .product-info .product-rating .empty-rating:before,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.hover-effect .product-info .product-rating .empty-rating:before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'spacing_separator_size_hover',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
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
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item:hover .product-info .product-rating .empty-rating:before,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.hover-effect .product-info .product-rating .empty-rating:before' => 'width: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Icons Style
	 * @access protected
	 */
	protected function icons_style($control) {

		$control->start_controls_section(
			'icons_style',
			[
				'label' => __('Icons Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'icons_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon' => 'width: {{SIZE}}{{UNIT}}!important; height: {{SIZE}}{{UNIT}}!important; line-height: {{SIZE}}{{UNIT}} !important;',

					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .product-bottom.on-page-caption a.icon' => 'width: {{SIZE}}{{UNIT}}!important; height: {{SIZE}}{{UNIT}}!important; border-radius: calc({{SIZE}}{{UNIT}}/2)!important;',

					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon:before' => 'font-size: {{SIZE}}{{UNIT}}!important;',

					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .product-bottom.on-page-caption a.icon i, 
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .product-bottom.on-page-caption a.icon:before,
					{{WRAPPER}} .portfolio.extended-products-grid.hover-slide .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.extended-products-grid.hover-slide .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon:before,
					{{WRAPPER}} .portfolio.extended-products-grid.hover-fade .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.extended-products-grid.hover-fade .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon:before,
					{{WRAPPER}} .portfolio.extended-products-grid.hover-zooming-blur .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.extended-products-grid.hover-zooming-blur .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon:before, 
					{{WRAPPER}} .portfolio.extended-products-grid.hover-gradient .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.extended-products-grid.hover-gradient .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon:before' => 'font-size: calc({{SIZE}}{{UNIT}}/2) !important;',

					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon svg' => 'width: {{SIZE}}{{UNIT}}!important; height: {{SIZE}}{{UNIT}}!important;',

					'{{WRAPPER}} .portfolio.extended-products-grid.hover-zooming-blur .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon svg,
					{{WRAPPER}} .portfolio.extended-products-grid.hover-gradient .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon svg,
					{{WRAPPER}} .portfolio.extended-products-grid.hover-slide .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon svg,
					{{WRAPPER}} .portfolio.extended-products-grid.hover-fade .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon svg' => 'width: calc({{SIZE}}{{UNIT}}/2)!important; height: calc({{SIZE}}{{UNIT}}/2)!important;',
				],
			]
		);

		$control->add_responsive_control(
			'icons_spacing',
			[
				'label' => __('Spacing Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .product-bottom.on-page-caption a.icon' => 'margin: 0 calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons .portfolio-icons-inner' => 'margin-right: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons .portfolio-icons-inner > .icon' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .product-bottom.on-page-caption .icons-top .icon' => 'margin: 0 0 {{SIZE}}{{UNIT}} 0',
				],
			]
		);


		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icons_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .product-bottom.on-page-caption a.icon,
				{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons .portfolio-icons-inner a.icon',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'page',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
									'operator' => 'in',
									'value' => ['zooming-blur', 'gradient', 'slide', 'fade'],
								],
							]
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'image',
								],
								[
									'name' => 'image_hover_effect_image',
									'operator' => 'in',
									'value' => ['gradient', 'slide', 'fade'],
								],
							]

						],
					],
				],
			]
		);
		$control->remove_control('icons_border_type_color');

		$control->start_controls_tabs('icons_style_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$condition = [];
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('icons_tab_' . $stkey, [
					'label' => $stelem,
					'condition' => $condition
				]);

				$control->add_control(
					'icons_color_' . $stkey,
					[
						'label' => __('Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .product-bottom.on-page-caption a.icon' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons .portfolio-icons-inner a.icon' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'icons_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .product-bottom.on-page-caption a.icon' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons .portfolio-icons-inner a.icon' . $state => 'background-color: {{VALUE}};',
						],
						'conditions' => [
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'page',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'caption_position',
											'operator' => '=',
											'value' => 'hover',
										],
										[
											'name' => 'image_hover_effect_hover',
											'operator' => 'in',
											'value' => ['zooming-blur', 'gradient', 'slide', 'fade']
										],
									]
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'caption_position',
											'operator' => '=',
											'value' => 'image',
										],
										[
											'name' => 'image_hover_effect_image',
											'operator' => 'in',
											'value' => ['gradient', 'slide', 'fade']
										],
									]

								],
							],
						],
					]
				);

				$control->add_control(
					'icons_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap .product-bottom.on-page-caption a.icon' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .portfolio-icons .portfolio-icons-inner a.icon' . $state => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'icons_border_type_border!' => '',
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
	 * Buttons Style
	 * @access protected
	 */
	protected function buttons_style($control) {

		$control->start_controls_section(
			'buttons_style',
			[
				'label' => __('Buttons Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'buttons_general_header',
			[
				'label' => __('General Settings', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$control->add_responsive_control(
			'buttons_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.type_button .button,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .actions .button' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => ['caption_position!' => 'page'],
			]
		);

		$control->add_responsive_control(
			'buttons_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button .button,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.type_button .button,
					.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message a.button,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .actions .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'buttons_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button .button,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.type_button .button,
					.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message a.button,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .actions .button',
			]
		);
		$control->remove_control('buttons_border_type_color');

		$control->add_responsive_control(
			'buttons_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button .button,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.type_button .button,
					.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message a.button,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .actions .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$control->add_responsive_control(
			'buttons_icon_alignment',
			[
				'label' => __('Icon Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;'
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button .button,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.type_button .button' => '{{VALUE}}',
				],
				'condition' => [
					'add_to_cart_type' => 'button',
				],
			]
		);

		$control->add_responsive_control(
			'buttons_icon_spacing',
			[
				'label' => __('Icon Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button .button .space,
					{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption a.cart.type_button .button .space' => 'width: calc({{SIZE}}{{UNIT}} - 5px)',
				],
				'condition' => [
					'add_to_cart_type' => 'button',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'button_text_typography',
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button .button,
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart .button,
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .actions .button',
				'condition' => [
					'add_to_cart_type' => 'button',
				],
			]
		);

		$control->add_control(
			'button_cart_header',
			[
				'label' => __('Add to Cart Button Colors', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'add_to_cart_type' => 'button',
				],
			]
		);

		$control->start_controls_tabs('button_cart_tabs', [
			'condition' => [
				'add_to_cart_type' => 'button',
			],
		]);

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('button_cart_tab_' . $stkey, [
					'label' => $stelem,
				]);

				$control->add_control(
					'button_cart_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button.simple-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.simple-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .actions .button' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_cart_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button.simple-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.simple-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .actions .button' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_cart_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button.simple-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.simple-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .actions .button' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'button_cart_shadow_' . $stkey,
						'label' => __('Shadow', 'thegem'),
						'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button.simple-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.simple-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .actions .button' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'button_options_header',
			[
				'label' => __('Select Options Button Colors', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'add_to_cart_type' => 'button',
				],
			]
		);

		$control->start_controls_tabs('button_options_tabs', [
			'condition' => [
				'add_to_cart_type' => 'button',
			],
		]);

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('button_options_tab_' . $stkey, [
					'label' => $stelem,
				]);

				$control->add_control(
					'button_options_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button.variable-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.variable-type-button .button' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_options_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button.variable-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.variable-type-button .button' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_options_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button.variable-type-button .button' . $state . ',
							{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.variable-type-button .button' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'button_options_shadow_' . $stkey,
						'label' => __('Shadow', 'thegem'),
						'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .wrap > .caption .product-bottom .cart.type_button.variable-type-button .button' . $state . ',
						{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item .image .overlay .links .caption .cart.variable-type-button .button' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();


		$control->end_controls_section();
	}

	/**
	 * Product Tabs Style
	 * @access protected
	 */
	protected function product_tabs_style($control) {

		$control->start_controls_section(
			'tabs_style',
			[
				'label' => __('Product Tabs Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['product_show_tabs' => 'yes']
			]
		);

		$control->add_control(
			'product_tabs_alignment',
			[
				'label' => __('Alignment', 'thegem'),
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
			]
		);

		$control->add_control(
			'product_tabs_title_header',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'product_tabs_title_typography',
				'selector' => '{{WRAPPER}} .portfolio-filter-tabs .portfolio-filter-tabs-title',
			]
		);

		$control->add_control(
			'product_tabs_title_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter-tabs .portfolio-filter-tabs-title' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'product_tabs_title_bottom_spacing',
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
					'{{WRAPPER}} .portfolio-filter-tabs.style-default .portfolio-filter-tabs-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'filters_tabs_style' => 'default',
				],
			]
		);

		$control->add_control(
			'product_tabs_tab_header',
			[
				'label' => __('Tabs', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('product_tabs_tab');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'hover') {
					continue;
				} else if ($stkey == 'active') {
					$state = '.active';
				} else if ($stkey == 'normal') {
					$state = ':not(.active)';
				}

				$control->start_controls_tab('product_tabs_tab_' . $stkey, ['label' => $stelem]);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'product_tabs_tab_typography_' . $stkey,
						'selector' => '{{WRAPPER}} .portfolio-filter-tabs ul.portfolio-filter-tabs-list li' . $state,
					]
				);

				$control->add_control(
					'product_tabs_tab_color_' . $stkey,
					[
						'label' => __('Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-filter-tabs ul.portfolio-filter-tabs-list li' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->end_controls_tab();
			}
		}

		$control->add_control(
			'product_tabs_tab_separator_header',
			[
				'label' => __('Separator', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filters_tabs_style' => 'alternative',
					'filters_tabs_title_separator' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'product_tabs_tab_separator_width',
			[
				'label' => __('Separator Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter-tabs.style-alternative.separator,
					{{WRAPPER}} .portfolio-filter-tabs.style-alternative.separator .portfolio-filter-tabs-list' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'filters_tabs_style' => 'alternative',
					'filters_tabs_title_separator' => 'yes',
				],
			]
		);

		$control->add_control(
			'product_tabs_tab_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter-tabs.style-alternative.separator,
					{{WRAPPER}} .portfolio-filter-tabs.style-alternative.separator .portfolio-filter-tabs-list' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'filters_tabs_style' => 'alternative',
					'filters_tabs_title_separator' => 'yes',
				],
			]
		);

		$control->end_controls_tabs();

		$control->end_controls_section();
	}


	/**
	 * Navigation Style
	 * @access protected
	 */
	protected function navigation_style($control) {

		$control->start_controls_section(
			'navigation_style',
			[
				'label' => __('Navigation Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'navigation_arrows_header',
			[
				'label' => __('Arrows', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'navigation_arrows_icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 300,
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
					'{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-prev div i, 
					{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-next div i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'navigation_arrows_spacing',
			[
				'label' => __('Horizontal Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .extended-carousel-grid.arrows-position-outside:not(.prevent-arrows-outside) .extended-carousel-item .owl-nav .owl-prev' => 'transform: translate(calc(-100% - {{SIZE}}{{UNIT}}), -50%);',
					'{{WRAPPER}} .extended-carousel-grid.arrows-position-outside:not(.prevent-arrows-outside) .extended-carousel-item .owl-nav .owl-next' => 'transform: translate(calc(100% + {{SIZE}}{{UNIT}}), -50%);',
					'{{WRAPPER}} .extended-carousel-grid.arrows-position-outside.prevent-arrows-outside .extended-carousel-item .owl-nav .owl-prev,
					{{WRAPPER}} .extended-carousel-grid.arrows-position-on .extended-carousel-item .owl-nav .owl-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .extended-carousel-grid.arrows-position-outside.prevent-arrows-outside .extended-carousel-item .owl-nav .owl-next,
					{{WRAPPER}} .extended-carousel-grid.arrows-position-on .extended-carousel-item .owl-nav .owl-next' => 'right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'navigation_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
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
					'{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-prev, 
					{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-next' => 'top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$control->add_responsive_control(
			'navigation_arrows_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-prev div.position-on, 
					{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-next div.position-on' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'arrows_navigation_position' => 'on',
				],
			]
		);

		$control->add_responsive_control(
			'navigation_arrows_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-prev div.position-on, 
					{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-next div.position-on' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'arrows_navigation_position' => 'on',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'navigation_arrows_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-prev div.position-on, 
					{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-next div.position-on',
				'condition' => [
					'arrows_navigation_position' => 'on',
				],
			]
		);
		$control->remove_control('navigation_arrows_border_type_color');

		$control->start_controls_tabs('pagination_arrows_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}
				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('pagination_arrows_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'navigation_arrows_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-prev' . $state . ' div.position-on, 
							{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-next' . $state . ' div.position-on' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'arrows_navigation_position' => 'on',
						],
					]
				);

				$control->add_control(
					'navigation_arrows_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-prev' . $state . ' div.position-on, 
							{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-next' . $state . ' div.position-on' => 'color: {{VALUE}};',
						],
						'condition' => [
							'arrows_navigation_position' => 'on',
						],
					]
				);

				$control->add_control(
					'navigation_arrows_icon_color_' . $stkey,
					[
						'label' => __('Icon Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-prev' . $state . ' div, 
							{{WRAPPER}} .extended-products-grid-carousel .extended-carousel-item .owl-nav .owl-next' . $state . ' div' => 'color: {{VALUE}};',
						],
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'navigation_dots_header',
			[
				'label' => __('Dots Navigation', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_dots_navigation' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'navigation_dots_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 78,
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
					'{{WRAPPER}} .extended-products-grid-carousel .owl-dots .owl-dot span' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_dots_navigation' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'navigation_dots_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
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
					'{{WRAPPER}} .extended-products-grid-carousel .owl-dots' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_dots_navigation' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'navigation_dots_between',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
					'{{WRAPPER}} .extended-products-grid-carousel .owl-dots .owl-dot' => 'margin: 0 calc({{SIZE}}{{UNIT}}/2)',
				],
				'condition' => [
					'show_dots_navigation' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'navigation_dots_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .extended-products-grid-carousel .owl-dots .owl-dot span',
				'condition' => [
					'show_dots_navigation' => 'yes',
				],
			]
		);
		$control->remove_control('navigation_dots_border_type_color');

		$control->start_controls_tabs('navigation_dots_tabs', [
			'condition' => [
				'show_dots_navigation' => 'yes',
			],
		]);

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'hover') {
					continue;
				}

				$state = '';
				if ($stkey == 'active') {
					$state = '.active';
				}

				$control->start_controls_tab('navigation_dots_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'navigation_dots_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .extended-products-grid-carousel .owl-dots .owl-dot' . $state . ' span' => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'navigation_dots_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .extended-products-grid-carousel .owl-dots .owl-dot' . $state . ' span' => 'border-color: {{VALUE}};',
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
	 * Labels Style
	 * @access protected
	 */
	protected function labels_style($control) {

		$control->start_controls_section(
			'labels_style',
			[
				'label' => __('Labels Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'product_show_new',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'name' => 'product_show_sale',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'name' => 'product_show_out',
							'operator' => '=',
							'value' => 'yes',
						]
					],
				],
			]
		);

		$this->add_responsive_control(
			'labels_margin',
			[
				'label' => __('Labels Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product .product-labels' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'new_label_heading',
			[
				'label' => __('“New” Label', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'new_label_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 300,
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
					'{{WRAPPER}} .portfolio.extended-products-grid .product .label.new-label' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.extended-products-grid .product .product-labels.style-1 .label.new-label' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_control(
			'new_label_box_rotate',
			[
				'label' => __('Rotate Box', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product .label.new-label' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .portfolio.extended-products-grid .product .label.new-label .rotate-back' => 'transform: rotate(-{{SIZE}}deg); -webkit-transform: rotate(-{{SIZE}}deg);',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_control(
			'new_label_text_rotate',
			[
				'label' => __('Rotate Text', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product .label.new-label .text' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'new_label_background',
			[
				'label' => __('Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .product-labels .label.new-label' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .product-labels .label.new-label:after' => 'border-left-color: {{VALUE}}; border-right-color: {{VALUE}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'new_label_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product .label.new-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'new_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .product .label.new-label',
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'new_label_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product .label.new-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_control(
			'new_label_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product .label.new-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'new_label_text_typo',
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .product .label.new-label .text',
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_control(
			'sale_label_heading',
			[
				'label' => __('“Sale” Label', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sale_label_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 300,
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
					'{{WRAPPER}} .portfolio.extended-products-grid .product .label.onsale' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.extended-products-grid .product .product-labels.style-1 .label.onsale' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_control(
			'sale_label_box_rotate',
			[
				'label' => __('Rotate Box', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.onsale' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.onsale .rotate-back' => 'transform: rotate(-{{SIZE}}deg); -webkit-transform: rotate(-{{SIZE}}deg);',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_control(
			'sale_label_text_rotate',
			[
				'label' => __('Rotate Text', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.onsale .text' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sale_label_background',
			[
				'label' => __('Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .product-labels .label.onsale' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .product-labels .label.onsale:after' => 'border-left-color: {{VALUE}}; border-right-color: {{VALUE}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sale_label_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sale_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.onsale',
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'sale_label_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.onsale' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_control(
			'sale_label_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.onsale' => 'color: {{VALUE}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'sale_label_text_typo',
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.onsale .text',
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_control(
			'out_label_heading',
			[
				'label' => __('“Out of stock” Label', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'out_label_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 300,
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
					'{{WRAPPER}} .portfolio.extended-products-grid .product .label.out-of-stock-label' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.extended-products-grid .product .product-labels.style-1 .label.out-of-stock-label' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_control(
			'out_label_box_rotate',
			[
				'label' => __('Rotate Box', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.out-of-stock-label' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.out-of-stock-label .rotate-back' => 'transform: rotate(-{{SIZE}}deg); -webkit-transform: rotate(-{{SIZE}}deg);',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_control(
			'out_label_text_rotate',
			[
				'label' => __('Rotate Text', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.out-of-stock-label .text' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'out_label_background',
			[
				'label' => __('Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .product-labels .label.out-of-stock-label' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .portfolio.extended-products-grid .portfolio-item.product .product-labels .label.out-of-stock-label:after' => 'border-left-color: {{VALUE}}; border-right-color: {{VALUE}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'out_label_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.out-of-stock-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'out_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.out-of-stock-label',
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'out_label_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.out-of-stock-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_control(
			'out_label_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.out-of-stock-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'out_label_text_typo',
				'selector' => '{{WRAPPER}} .portfolio.extended-products-grid .product-labels .label.out-of-stock-label .text',
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->end_controls_section();
	}


	/**
	 * Notification Style
	 * @access protected
	 */
	protected function notification_style($control) {

		$control->start_controls_section(
			'notification_style',
			[
				'label' => __('Notification Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'stay_visible',
			[
				'label' => __('Stay visible, ms', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 500,
				'max' => 10000,
				'step' => 100,
				'default' => 4000,
			]
		);

		$control->add_responsive_control(
			'notification_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'notification_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'notification_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'notification_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message:before' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'button_wishlist_header',
			[
				'label' => __('View Cart & View Wishlist Buttons Colors', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('button_wishlist_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('button_wishlist_tab_' . $stkey, [
					'label' => $stelem,
				]);

				$control->add_control(
					'button_wishlist_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message a.button' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_wishlist_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message a.button' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_wishlist_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'.thegem-popup-notification-wrap#style-notification-{{ID}} .thegem-popup-notification .notification-message a.button' . $state => 'border-color: {{VALUE}};',
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

		if (!defined( 'WC_PLUGIN_FILE' )) {
			return '';
		}

		global $post;
		$portfolio_posttemp = $post;
		$params = $this->get_settings_for_display();
		$grid_uid = $this->get_id();
		$params['portfolio_uid'] = $params['style_uid'] = $grid_uid;

		$taxonomy_filter_current = [];
		if (!empty($params['source']) && in_array('category', $params['source'])) {
			$taxonomy_filter_current['product_cat'] = $params['content_products_cat'];
		} else {
			$taxonomy_filter_current['product_cat'] = ['0'];
		}

		$attributes = [];
		if (!empty($params['source']) && in_array('attribute', $params['source'])) {
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

		if (!empty($params['source']) && in_array('tag', $params['source'])) {
			$taxonomy_filter_current['product_tag'] = $params['content_products_tags'];
		}

		$post__in = null;
		if (!empty($params['source']) && in_array('products', $params['source'])) {
			$post__in = $params['content_products'];
		}

		if ($params['caption_position'] == 'image') {
			$hover_effect = $params['image_hover_effect_image'];
		} else if ($params['caption_position'] == 'page') {
			$hover_effect = $params['image_hover_effect_page'];
		} else {
			$hover_effect = $params['image_hover_effect_hover'];
		}

		$params['image_shadow_normal_box_shadow_type'] = empty($params['image_shadow_normal_box_shadow_type']) ? 'no' : $params['image_shadow_normal_box_shadow_type'];
		$params['image_shadow_hover_box_shadow_type'] = empty($params['image_shadow_hover_box_shadow_type']) ? 'no' : $params['image_shadow_hover_box_shadow_type'];
		$params['full_item_border_type_border'] = empty($params['full_item_border_type_border']) ? '' : $params['full_item_border_type_border'];

		wp_enqueue_style('thegem-hovers-' . $hover_effect);

		if ($params['quick_view'] == 'yes') {
			wp_enqueue_script('wc-single-product');
			wp_enqueue_script('wc-add-to-cart-variation');
			wp_enqueue_script('thegem-product-quick-view');
            wp_enqueue_script('thegem-quick-view');
            wp_enqueue_style('thegem-quick-view');

            if(thegem_get_option('product_page_layout') == 'default') {
                if(thegem_get_option('product_page_button_add_to_cart_icon') && thegem_get_option('product_page_button_add_to_cart_icon_pack')) {
                    wp_enqueue_style('icons-'.thegem_get_option('product_page_button_add_to_cart_icon_pack'));
                }
                if(thegem_get_option('product_page_button_add_to_wishlist_icon') && thegem_get_option('product_page_button_add_to_wishlist_icon_pack')) {
                    wp_enqueue_style('icons-'.thegem_get_option('product_page_button_add_to_wishlist_icon_pack'));
                }
                if(thegem_get_option('product_page_button_added_to_wishlist_icon') && thegem_get_option('product_page_button_added_to_wishlist_icon_pack')) {
                    wp_enqueue_style('icons-'.thegem_get_option('product_page_button_added_to_wishlist_icon_pack'));
                }
            }
			if (thegem_get_option('product_gallery') != 'legacy') {
				wp_enqueue_style('thegem-product-gallery');
			} else {
				wp_enqueue_style('thegem-hovers');
			}
		}

		if ($params['loading_animation'] === 'yes') {
			wp_enqueue_style('thegem-animations');
			wp_enqueue_script('thegem-items-animations');
			wp_enqueue_script('thegem-scroll-monitor');
		}

		if ($params['slider_scroll_init'] === 'yes') {
			wp_enqueue_script('thegem-scroll-monitor');
		}

		$items_per_page = $params['items_per_page'] ? intval($params['items_per_page']) : 8;

		$page = 1;

		$orderby = $params['orderby'];
		$order = $params['order'];

		$featured_only = $params['featured_only'] == 'yes';
		$sale_only = $params['sale_only'] == 'yes';
		$stock_only = $params['stock_only'] == 'yes';
		$recently_viewed_only = !empty($params['recently_viewed_only']) && $params['recently_viewed_only'] == 'yes';
		$new_only = !empty($params['new_only']) && $params['new_only'] == 'yes';

		if ($params['exclude_type'] == 'current') {
			$params['exclude_products'] = [get_the_ID()];
		} else if ($params['exclude_type'] == 'term' && !empty($params['exclude_product_terms'])) {
			$params['exclude_products'] = thegem_get_posts_query_section_exclude_ids($params['exclude_product_terms'], 'product');
		}

		$product_loops = [];
		$active_tab = 1;
		$is_related_upsell = false;
		if ($params['product_show_tabs'] == 'yes') {
			if (isset($_GET[$grid_uid . '-tab'])) {
				$active_tab = intval($_GET[$grid_uid . '-tab']);
			}
			$filter_tabs = $params['filters_tabs_tabs'];
			if (!empty($filter_tabs)) {
				foreach ($filter_tabs as $item) {
					if ($item['filters_tabs_tab_filter_by'] == 'featured') {
						$product_loops[] = thegem_extended_products_get_posts($page, $items_per_page, $orderby, $order, true, $sale_only, $stock_only, $recently_viewed_only, $new_only, $taxonomy_filter_current, [], $attributes, null, null, null, $post__in, $params['offset'], $params['exclude_products']);
					} else if ($item['filters_tabs_tab_filter_by'] == 'sale') {
						$product_loops[] = thegem_extended_products_get_posts($page, $items_per_page, $orderby, $order, $featured_only, true, $stock_only, $recently_viewed_only, $new_only, $taxonomy_filter_current, [], $attributes, null, null, null, $post__in, $params['offset'], $params['exclude_products']);
					} else if ($item['filters_tabs_tab_filter_by'] == 'recent') {
						$product_loops[] = thegem_extended_products_get_posts($page, $items_per_page, 'date', 'desc', $featured_only, $sale_only, $stock_only, $recently_viewed_only, $new_only, $taxonomy_filter_current, [], $attributes, null, null, null, $post__in, $params['offset'], $params['exclude_products']);
					} else if ($item['filters_tabs_tab_filter_by'] == 'categories') {
						$product_loops[] = thegem_extended_products_get_posts($page, $items_per_page, $orderby, $order, $featured_only, $sale_only, $stock_only, $recently_viewed_only, $new_only, ['product_cat' => [$item['filters_tabs_tab_products_cat']]], [], $attributes, null, null, null, $post__in, $params['offset'], $params['exclude_products']);
					}
				}
				$is_related_upsell = true;
			}
		} else {
			if ($params['source_type'] == 'related_upsell') {
				$product = thegem_templates_init_product();
				if (empty($product)) {
					$post = $portfolio_posttemp;
					return;
				}
				$is_related_upsell = true;
				if ($params['related_upsell_source'] == 'related') {
					$related_products = wc_get_related_products($product->get_id(), -1);
					$post__in = $related_products;
				} else {
					global $thegem_product_data;
					$upsells = $product->get_upsell_ids();
					if(intval($thegem_product_data['product_page_elements_upsell_items']) > -1) {
						$upsells = array_slice($upsells, 0, intval($thegem_product_data['product_page_elements_upsell_items']));
					}
					$post__in = $upsells;
				}

				if (empty($post__in) && !is_admin()) {
					$post = $portfolio_posttemp;
					return;
				}
			} else if ($params['source_type'] == 'cross_sell') {
				$cross_sells_ids_in_cart = array();

				if (WC()->cart) {
					foreach ( WC()->cart->get_cart() as $cart_item ) {
						if ( $cart_item['quantity'] > 0 ) {
							$cross_sells_ids_in_cart = array_merge( $cart_item['data']->get_cross_sell_ids(), $cross_sells_ids_in_cart );
						}
					}
				}

				$cross_sells = wp_parse_id_list( $cross_sells_ids_in_cart );
				$post__in = $cross_sells;

				if (empty($post__in) && !is_admin()) {
					$post = $portfolio_posttemp;
					return;
				}

				if ($params['show_cross_sell_title'] == 'yes') {
					$text_styled_class = implode(' ', array($params['cross_sell_title_style'], $params['cross_sell_title_weight'])); ?>
					<<?php echo $params['cross_sell_title_tag']; ?> class="cross-sell-title <?php echo $text_styled_class; ?>">
					<?php echo $params['cross_sell_title_text']; ?>
					</<?php echo $params['cross_sell_title_tag']; ?>>
					<?php
				}
			}

			$product_loops[] = thegem_extended_products_get_posts($page, $items_per_page, $orderby, $order, $featured_only, $sale_only, $stock_only, $recently_viewed_only, $new_only, $taxonomy_filter_current, [], $attributes, null, null, null, $post__in, $params['offset'], $params['exclude_products']);
		}

		if ($params['product_show_tabs'] == 'yes' || (!empty($product_loops[0]) && $product_loops[0]->have_posts())) :
			$params['layout'] = 'justified';
			$params['ignore_highlights'] = 'yes';

			if ($params['product_separator'] == 'yes' && intval($params['product_separator_width']['size']) % 2 !== 0) {
				$floor = floor(intval($params['product_separator_width']['size']) / 2);
				$ceil = ceil(intval($params['product_separator_width']['size']) / 2); ?>
				<style>
					.elementor-element-<?php echo $grid_uid; ?> .portfolio-set .portfolio-item:before {
						transform: translateX(-<?php echo $floor; ?>px) !important;
						top: -<?php echo $floor; ?>px !important;
					}

					.elementor-element-<?php echo $grid_uid; ?> .portfolio-set .portfolio-item:after {
						transform: translateX(<?php echo $ceil; ?>px) !important;
						top: -<?php echo $floor; ?>px !important;
					}

					.elementor-element-<?php echo $grid_uid; ?> .portfolio-set .portfolio-item .item-separator-box:before {
						transform: translateY(-<?php echo $floor; ?>px) !important;
						left: -<?php echo $floor; ?>px !important;
					}

					.elementor-element-<?php echo $grid_uid; ?> .portfolio-set .portfolio-item .item-separator-box:after {
						transform: translateY(<?php echo $ceil; ?>px) !important;
						left: -<?php echo $floor; ?>px !important;
					}

					.elementor-element-<?php echo $grid_uid; ?> .owl-carousel .owl-stage-outer {
						padding: <?php echo $ceil; ?>px !important;
						width: calc(100% + <?php echo $ceil; ?>px * 2) !important;
						margin-left: calc(-<?php echo $ceil; ?>px) !important;
					}
				</style>
			<?php }

			if ($params['image_aspect_ratio'] == 'custom') {
				$params['image_ratio_custom'] = $params['image_ratio_custom']['size'];
			}

			$item_classes = get_thegem_portfolio_render_item_classes($params);
			$item_classes[] = 'owl-item';
			$thegem_sizes = get_thegem_portfolio_render_item_image_sizes($params);

			if ($params['disable_preloader'] == '') {
				if ($params['columns_desktop'] == '100%') {
					echo apply_filters('thegem_portfolio_preloader_html', '<div class="preloader"><div class="preloader-spin"></div></div>');
				} else { ?>
					<div class="preloader skeleton-carousel">
						<div class="skeleton">
							<div class="skeleton-posts row portfolio-row">
								<?php for ($x = 0; $x < $product_loops[0]->post_count; $x++) {
									echo thegem_extended_products_render_item($params, $item_classes);
								} ?>
							</div>
						</div>
					</div>
				<?php }
			} ?>

			<div class="portfolio-preloader-wrapper">
				<?php
				if ($params['caption_position'] == 'hover') {
					$title_on = 'hover';
				} else {
					$title_on = 'page';
				}

				$gaps_size = isset($params['image_gaps']['size']) && $params['image_gaps']['size'] != '' ? $params['image_gaps']['size'] : 0;
				$gaps_size_tablet = isset($params['image_gaps_tablet']['size']) && $params['image_gaps_tablet']['size'] != '' ? $params['image_gaps_tablet']['size'] : $gaps_size;
				$gaps_size_mobile = isset($params['image_gaps_mobile']['size']) && $params['image_gaps_mobile']['size'] != '' ? $params['image_gaps_mobile']['size'] : $gaps_size_tablet;

				$this->add_render_attribute(
					'products-wrap',
					[
						'class' => [
							'portfolio portfolio-grid extended-portfolio-grid extended-products-grid extended-products-grid-carousel extended-carousel-grid',
							'woocommerce',
							'products',
							'no-padding',
							'disable-isotope',
							'portfolio-preset-' . $params['thegem_elementor_preset'],
							'portfolio-style-justified',
							'background-style-' . $params['caption_container_preset'],
							'caption-container-preset-' . $params['caption_container_preset_hover'],
							'caption-position-' . $params['caption_position'],
							'caption-alignment-' . $params['caption_container_alignment_hover'],
							'hover-' . $hover_effect,
							'title-on-' . $title_on,
							'arrows-position-' . $params['arrows_navigation_position'],
							($params['image_size'] == 'default' ? 'aspect-ratio-' . $params['image_aspect_ratio'] : ''),
							($params['loading_animation'] == 'yes' ? 'loading-animation' : ''),
							($params['loading_animation'] == 'yes' && $params['animation_effect'] ? 'item-animation-' . $params['animation_effect'] : ''),
							($gaps_size == 0 ? 'no-gaps' : ''),
							($params['image_shadow_normal_box_shadow_type'] == 'yes' || $params['image_shadow_hover_box_shadow_type'] == 'yes' ? 'has-shadowed-items' : ''),
							($params['shadowed_container'] == 'yes' ? 'shadowed-container' : ''),
							($params['columns_desktop'] == '100%' ? 'fullwidth-columns fullwidth-columns-desktop-' . $params['columns_100'] : ''),
							($params['columns_desktop'] == '100%' && ($gaps_size < 24 || $params['product_separator'] == 'yes') ? 'prevent-arrows-outside' : ''),
							($params['caption_position'] == 'image' && $params['image_hover_effect_image'] == 'gradient' ? 'hover-gradient-title' : ''),
							($params['caption_position'] == 'image' && $params['image_hover_effect_image'] == 'circular' ? 'hover-circular-title' : ''),
							($params['caption_position'] == 'hover' || $params['caption_position'] == 'image' ? 'hover-title' : ''),
							($params['social_sharing'] != 'yes' ? 'portfolio-disable-socials' : ''),
							($params['columns_desktop'] != '100%' ? 'columns-desktop-' . $params['columns_desktop'] : 'columns-desktop-' . $params['columns_100']),
							('columns-tablet-' . $params['columns_tablet']),
							('columns-mobile-' . $params['columns_mobile']),
							($params['product_separator'] == 'yes' ? 'item-separator' : ''),
							($params['arrows_navigation_visibility'] == 'hover' ? 'arrows-hover' : ''),
							($params['full_item_border_type_border'] != '' ? 'full-item-border' : ''),
							($params['slider_scroll_init'] === 'yes' || $params['slider_loop'] == 'yes' ? 'carousel-scroll-init' : ''),
							(($params['image_size'] == 'full' && empty($params['image_ratio_full']['size']) || !in_array($params['image_size'], ['full', 'default'])) ? 'full-image' : 'aspect-ratio-custom'),
						],
						'id' => 'style-'.esc_attr($grid_uid),
						'data-style-uid' => esc_attr($grid_uid),
						'data-columns-mobile' => esc_attr(str_replace("x", "", $params['columns_mobile'])),
						'data-columns-tablet' => esc_attr(str_replace("x", "", $params['columns_tablet'])),
						'data-columns-desktop' => $params['columns_desktop'] != '100%' ? esc_attr(str_replace("x", "", $params['columns_desktop'])) : esc_attr($params['columns_100']),
						'data-margin-mobile' => esc_attr($gaps_size_mobile),
						'data-margin-tablet' => esc_attr($gaps_size_tablet),
						'data-margin-desktop' => esc_attr($gaps_size),
						'data-hover' => esc_attr($hover_effect),
						'data-dots' => $params['show_dots_navigation'] == 'yes' ? '1' : '0',
						'data-arrows' => $params['show_arrows_navigation'] == 'yes' ? '1' : '0',
						'data-loop' => $params['slider_loop'] == 'yes' ? '1' : '0',
						'data-sliding-animation' => $params['sliding_animation'],
						'data-autoscroll-speed' => $params['autoscroll'] == 'yes' ? $params['autoscroll_speed']['size'] : '0',
					]
				);
				?>

				<div <?php echo $this->get_render_attribute_string('products-wrap'); ?>>

					<div class="portfolio-row-outer <?php if ($params['columns_desktop'] == '100%'): ?>fullwidth-block no-paddings<?php endif; ?>">
						<?php if ($params['product_show_tabs'] == 'yes'): ?>
							<div class="portfolio-top-panel">
								<div class="portfolio-filter-tabs style-<?php echo $params['filters_tabs_style']; ?> alignment-<?php echo $params['product_tabs_alignment']; ?> <?php echo $params['filters_tabs_title_separator'] == 'yes' ? 'separator' : ''; ?>">
									<?php if ($params['filters_tabs_title_text'] != '') { ?>
										<div class="title-h4 portfolio-filter-tabs-title <?php echo $params['filters_tabs_title_style_preset'] == 'thin' ? 'light' : ''; ?>"><?php echo $params['filters_tabs_title_text']; ?></div>
									<?php }

									if (!empty($filter_tabs)) { ?>
										<ul class="portfolio-filter-tabs-list">
											<?php foreach ($filter_tabs as $index => $item) {
												if (!empty($item['filters_tabs_tab_title'])) { ?>
													<li class="portfolio-filter-tabs-list-tab <?php echo $index == $active_tab - 1 ? 'active' : ''; ?>"
														data-num="<?php echo $index + 1; ?>">
														<?php echo $item['filters_tabs_tab_title'] ?>
													</li>
												<?php }
											} ?>
										</ul>
									<?php } ?>
								</div>
							</div>
						<?php endif; ?>
						<div class="portfolio-filter-tabs-content">
							<?php foreach ($product_loops as $index => $product_loop) { ?>
								<div class="extended-products-grid-carousel-item <?php echo $index == $active_tab - 1 ? 'active' : ''; ?>"
									 data-num="<?php echo $index + 1; ?>">
									<div class="portfolio-row clearfix">
										<div class="portfolio-set">
											<div class="extended-carousel-wrap">
												<div class="extended-carousel-item owl-carousel owl-theme owl-loaded">
													<div class="owl-stage-outer">
														<div class="owl-stage">
															<?php
															if ($product_loop->have_posts()) {
																remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
																remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
																remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
																remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
																remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
																remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
																remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
																remove_action('woocommerce_after_shop_loop_item', 'thegem_woocommerce_after_shop_loop_item_link', 15);
																remove_action('woocommerce_after_shop_loop_item', 'thegem_woocommerce_after_shop_loop_item_wishlist', 20);

																while ($product_loop->have_posts()) : $product_loop->the_post(); ?>
																	<?php echo thegem_extended_products_render_item($params, $item_classes, $thegem_sizes, get_the_ID(), false, true); ?>
																<?php endwhile;
															} else { ?>
																<div class="portfolio-item not-found">
																	<div class="found-wrap">
																		<div class="image-inner empty"></div>
																		<div class="msg">
																			<?php echo wp_kses($params['not_found_text'], 'post'); ?>
																		</div>
																	</div>
																</div>
															<?php }
															wp_reset_postdata();
															?>
														</div>
													</div>
												</div>
											</div>

										</div><!-- .portflio-set -->
									</div><!-- .row-->
									<?php if ($params['show_arrows_navigation'] == 'yes'): ?>
										<div class="slider-prev-icon position-<?php echo $params['arrows_navigation_position']; ?>">
											<?php if ($params['arrows_navigation_left_icon']['value']) {
												Icons_Manager::render_icon($params['arrows_navigation_left_icon'], ['aria-hidden' => 'true']);
											} else { ?>
												<i class="default"></i>
											<?php } ?>
										</div>
										<div class="slider-next-icon position-<?php echo $params['arrows_navigation_position']; ?>">
											<?php if ($params['arrows_navigation_right_icon']['value']) {
												Icons_Manager::render_icon($params['arrows_navigation_right_icon'], ['aria-hidden' => 'true']);
											} else { ?>
												<i class="default"></i>
											<?php } ?>
										</div>
									<?php endif; ?>
								</div>
							<?php } ?>
						</div>
						<?php thegem_woocommerce_product_page_ajax_notification($params); ?>
					</div><!-- .full-width -->
				</div><!-- .portfolio-->
			</div><!-- .portfolio-preloader-wrapper-->
		<?php

		else: ?>
			<?php if (!$is_related_upsell && !$recently_viewed_only) { ?>
				<div class="bordered-box centered-box styled-subtitle">
					<?php echo esc_html__('Please select products in "Products" section', 'thegem') ?>
				</div>
			<?php } ?>
		<?php endif;
		$post = $portfolio_posttemp;

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script>
				if (typeof widget_settings == 'undefined') {
					var widget_settings = [];
				}
				widget_settings['<?php echo $grid_uid ?>'] = <?php echo json_encode($params) ?>;
			</script>
			<script type="text/javascript">
				(function ($) {

					$(document).trigger("yith_wcwl_init");

					setTimeout(function () {
						if (!$('.elementor-element-<?php echo $this->get_id(); ?> .extended-products-grid-carousel').length) {
							return;
						}
						$('.elementor-element-<?php echo $this->get_id(); ?> .extended-products-grid-carousel').initProductGalleries();
					}, 1000);

					elementor.channels.editor.on('change', function (view) {
						var changed = view.elementSettingsModel.changed;

						if (changed.image_gaps !== undefined || changed.image_gaps_mobile !== undefined || changed.image_gaps_tablet !== undefined ||
							changed.caption_container_padding !== undefined || changed.spacing_title !== undefined || changed.spacing_description !== undefined) {
							setTimeout(function () {
								$('.elementor-element-<?php echo $this->get_id(); ?> .extended-carousel-item').trigger('refresh.owl.carousel');
							}, 500);
						}
					});

				})(jQuery);

			</script>
		<?php endif;
	}

	public function get_preset_data() {

		return array(
			/** Caption Below: Default, Cart Button */
			'below-default-cart-button' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'button',
				'product_show_add_to_cart_mobiles' => 'yes',
				'cart_button_show_icon' => 'yes',
				'labels_design' => '1',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',
			),

			/** Caption Below: Default, Cart Icon */
			'below-default-cart-icon' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => 'yes',
				'labels_design' => '4',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',
			),

			/** Caption Below: Solid, Cart Disabled */
			'below-cart-disabled' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '3',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 22, 'unit' => 'px'],
				'caption_container_preset' => 'gray',
				'pagination_numbers_radius' => ['top' => '100', 'right' => '100', 'bottom' => '100', 'left' => '100', 'unit' => 'px'],
				'pagination_numbers_border_type_normal_border' => 'solid',
				'pagination_numbers_border_type_hover_border' => 'solid',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_normal_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_hover_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_active_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
			),

			/** Caption Below: Border, Cart Icon */
			'below-border-cart-icon' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => 'yes',
				'labels_design' => '2',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 12, 'unit' => 'px'],
				'full_item_border_type_width' => ['top' => '1', 'right' => '1', 'bottom' => '0', 'left' => '1', 'unit' => 'px'],
				'full_item_border_type_border' => 'solid',
				'caption_container_preset' => 'white',
				'caption_container_border_normal_width' => ['top' => '0', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'caption_container_border_normal_border' => 'solid',
			),

			/** Caption Below: Shadow Hover 01 */
			'below-shadow-hover-01' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '3',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => 'yes',

				'image_shadow_hover_box_shadow_type' => 'yes',
				'image_shadow_hover_box_shadow' => [
					'horizontal' => 0,
					'vertical' => 0,
					'blur' => 7,
					'spread' => 0,
					'color' => 'rgba(0,0,0,0.2)',
				],
				'shadowed_container' => 'yes',
				'caption_container_preset' => 'transparent',
			),

			/** Caption Below: Shadow Hover 02 */
			'below-shadow-hover-02' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'button',
				'product_show_add_to_cart_mobiles' => 'yes',
				'cart_button_show_icon' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '3',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 30, 'unit' => 'px'],
				'image_border_radius' => ['top' => '12', 'right' => '12', 'bottom' => '0', 'left' => '0', 'unit' => 'px'],
				'full_item_border_type_border' => 'solid',
				'full_item_border_type_width' => ['top' => '1', 'right' => '1', 'bottom' => '0', 'left' => '1', 'unit' => 'px'],
				'full_item_border_color_hover' => '#DFE5E800',
				'image_shadow_hover_box_shadow_type' => 'yes',
				'image_shadow_hover_box_shadow' => [
					'horizontal' => 0,
					'vertical' => 0,
					'blur' => 30,
					'spread' => 0,
					'color' => 'rgba(49, 50, 51, 0.18)',
				],
				'shadowed_container' => 'yes',
				'caption_container_preset' => 'transparent',
				'caption_container_radius' => ['top' => '0', 'right' => '0', 'bottom' => '12', 'left' => '12', 'unit' => 'px'],
				'caption_container_padding' => ['top' => '23', 'right' => '20', 'bottom' => '35', 'left' => '20', 'unit' => 'px'],
				'caption_container_border_normal_border' => 'solid',
				'caption_container_border_normal_width' => ['top' => '0', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'caption_container_border_color_hover' => '#02010100',
				'icons_border_type_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'icons_border_type_border' => 'solid',
				'icons_color_normal' => '#99A9B5',
				'icons_background_color_normal' => '#02010100',
				'icons_border_color_normal' => '#99A9B5',
				'icons_color_hover' => '#FFFFFF',
				'icons_background_color_hover' => '#00BCD4',
				'icons_border_color_hover' => '#00BCD4',
				'spacing_separator_weight' => ['size' => 0, 'unit' => 'px'],
				'button_cart_color_normal' => '#99A9B5',
				'button_cart_color_hover' => '#FFFFFF',
				'button_cart_background_color_normal' => '#02010100',
				'button_cart_background_color_hover' => '#00BCD4',
				'button_cart_border_color_normal' => '#99A9B5',
				'button_cart_border_color_hover' => '#00BCD4',
				'button_options_color_normal' => '#99A9B5',
				'button_options_color_hover' => '#FFFFFF',
				'button_options_background_color_normal' => '#02010100',
				'button_options_background_color_hover' => '#00BCD4',
				'button_options_border_color_normal' => '#99A9B5',
				'button_options_border_color_hover' => '#00BCD4',
				'pagination_spacing' => ['size' => 70, 'unit' => 'px'],
				'pagination_numbers_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => 'px'],
				'pagination_numbers_padding' => ['top' => '2', 'right' => '2', 'bottom' => '2', 'left' => '2', 'unit' => 'px'],
				'pagination_numbers_background_color_normal' => '#FFFFFF',
				'pagination_numbers_background_color_hover' => '#00BCD4',
				'pagination_numbers_background_color_active' => '#99A9B5',
				'pagination_numbers_border_type_normal_border' => 'solid',
				'pagination_numbers_border_type_hover_border' => 'solid',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_normal_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_hover_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_active_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_active_color' => '#99A9B5',
				'pagination_numbers_border_type_hover_color' => '#00BCD4',
				'pagination_numbers_text_color_hover' => '#FFFFFF',
				'pagination_numbers_text_color_active' => '#FFFFFF',
				'pagination_numbers_text_typography_normal_typography' => 'custom',
				'pagination_numbers_text_typography_normal_font_weight' => 400,
				'pagination_arrows_left_icon' => ['value' => 'gem-elegant arrow-carrot-left', 'library' => 'thegem-elegant'],
				'pagination_arrows_right_icon' => ['value' => 'gem-elegant arrow-carrot-right', 'library' => 'thegem-elegant'],
				'pagination_arrows_icon_size' => ['size' => 16, 'unit' => 'px'],
				'pagination_arrows_background_color_hover' => '#FFFFFF',
				'pagination_arrows_border_type_normal_border' => 'solid',
				'pagination_arrows_border_type_hover_border' => 'solid',
				'pagination_arrows_border_type_normal_color' => '#FFFFFF',
				'pagination_arrows_border_type_hover_color' => '#00BCD4',
				'pagination_arrows_icon_color_normal' => '#99A9B5',
				'pagination_arrows_icon_color_hover' => '#00BCD4',
			),

			/** Caption Below: Rounded Images */
			'below-rounded-images' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => '',
				'labels_design' => '1',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 42, 'unit' => 'px'],
				'image_border_radius' => ['top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'unit' => 'px'],
				'caption_container_preset' => 'transparent',
				'pagination_numbers_radius' => ['top' => '100', 'right' => '100', 'bottom' => '100', 'left' => '100', 'unit' => 'px'],
			),

			/** Caption Below: Rectangle Button 01 */
			'below-rectangle-button-01' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'button',
				'product_show_add_to_cart_mobiles' => 'yes',
				'cart_button_show_icon' => '',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '3',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 32, 'unit' => 'px'],
				'caption_container_preset' => 'transparent',
				'caption_container_alignment' => 'left',
				'caption_container_padding' => ['top' => '20', 'right' => '0', 'bottom' => '20', 'left' => '0', 'unit' => 'px'],
				'buttons_border_radius' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px'],
			),

			/** Caption Below: Rectangle Button 02 */
			'below-rectangle-button-02' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'button',
				'product_show_add_to_cart_mobiles' => 'yes',
				'cart_button_show_icon' => 'yes',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '1',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 16, 'unit' => 'px'],
				'caption_container_preset' => 'transparent',
				'caption_container_alignment' => 'left',
				'caption_container_padding' => ['top' => '0', 'right' => '20', 'bottom' => '20', 'left' => '0', 'unit' => 'px'],
				'spacing_categories_top' => ['size' => 13, 'unit' => 'px'],
				'spacing_title' => ['top' => '8', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px'],
				'spacing_price' => ['top' => '7', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px'],
				'buttons_border_radius' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px'],
				'buttons_text_padding' => ['top' => '10', 'right' => '16', 'bottom' => '10', 'left' => '16', 'unit' => 'px'],
				'button_options_background_color_normal' => '#DFE5E8',
				'button_options_background_color_hover' => '#00BCD4',
				'button_options_border_color_normal' => '#02010100',
				'pagination_spacing' => ['size' => 70, 'unit' => 'px'],
				'pagination_numbers_radius' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px'],
				'pagination_numbers_background_color_normal' => '#FFFFFF',
				'pagination_numbers_background_color_hover' => '#00BCD4',
				'pagination_numbers_background_color_active' => '#99A9B5',
				'pagination_numbers_border_type_normal_border' => 'solid',
				'pagination_numbers_border_type_hover_border' => 'solid',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_normal_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_hover_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_active_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_active_color' => '#99A9B5',
				'pagination_numbers_border_type_hover_color' => '#00BCD4',
				'pagination_numbers_text_color_hover' => '#FFFFFF',
				'pagination_numbers_text_color_active' => '#FFFFFF',
				'pagination_numbers_text_typography_normal_typography' => 'custom',
				'pagination_numbers_text_typography_normal_font_weight' => 400,
				'pagination_arrows_left_icon' => ['value' => 'gem-elegant arrow-carrot-left', 'library' => 'thegem-elegant'],
				'pagination_arrows_right_icon' => ['value' => 'gem-elegant arrow-carrot-right', 'library' => 'thegem-elegant'],
				'pagination_arrows_icon_size' => ['size' => 16, 'unit' => 'px'],
				'pagination_arrows_background_color_hover' => '#FFFFFF',
				'pagination_arrows_border_type_normal_border' => 'solid',
				'pagination_arrows_border_type_hover_border' => 'solid',
				'pagination_arrows_border_type_normal_color' => '#FFFFFF',
				'pagination_arrows_border_type_hover_color' => '#00BCD4',
				'pagination_arrows_icon_color_normal' => '#99A9B5',
				'pagination_arrows_icon_color_hover' => '#00BCD4',
			),

			/** Caption Below: Product Separator 01 */
			'below-separator-01' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'button',
				'product_show_add_to_cart_mobiles' => 'yes',
				'cart_button_show_icon' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '4',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 36, 'unit' => 'px'],
				'product_separator' => 'yes',
				'product_separator_color' => '#F0F3F2',
				'product_separator_width' => ['size' => 2, 'unit' => 'px'],
				'title_color_normal' => '#5F727F',
				'price_color_normal' => '#5F727F',
				'caption_container_preset' => 'gray',
				'caption_container_background_page_hover_background' => 'classic',
				'caption_container_background_page_hover_color' => '#FFFFFF',
				'spacing_price' => ['top' => '2', 'right' => '0', 'bottom' => '2', 'left' => '0', 'unit' => 'px'],
				'pagination_numbers_radius' => ['top' => '5', 'right' => '5', 'bottom' => '5', 'left' => '5', 'unit' => 'px'],
			),

			/** Caption Below: Product Separator 02 */
			'below-separator-02' => array(
				'caption_position' => 'page',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'labels_design' => '6',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 0, 'unit' => 'px'],
				'product_separator' => 'yes',
				'product_separator_color' => '#212227',
				'product_separator_width' => ['size' => 1, 'unit' => 'px'],
				'caption_container_preset' => 'transparent',
				'caption_container_background_page_hover_background' => 'classic',
				'caption_container_background_page_hover_color' => '#FFFFFF',
				'caption_container_padding' => ['top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px'],
				'spacing_price' => ['top' => '2', 'right' => '0', 'bottom' => '2', 'left' => '0', 'unit' => 'px'],
				'icons_spacing' => ['size' => 0, 'unit' => 'px'],
				'icons_background_color_normal' => '#02010100',
				'icons_color_hover' => '#00BCD4',
				'pagination_numbers_border_type_normal_border' => 'solid',
				'pagination_numbers_border_type_hover_border' => 'solid',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_normal_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_hover_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_active_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
			),

			/** Caption on Image: Default, Cart Button */
			'image-default-cart-button' => array(
				'caption_position' => 'image',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'button',
				'product_show_add_to_cart_mobiles' => '',
				'cart_button_show_icon' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '3',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',
			),

			/** Caption on Image: Default, Cart Icon */
			'image-default-cart-icon' => array(
				'caption_position' => 'image',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => '',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '4',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',
			),

			/** Caption on Image: Solid Caption Background */
			'image-solid-background' => array(
				'caption_position' => 'image',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '3',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'caption_container_preset_hover' => 'solid',
			),

			/** Caption on Image: Rounded Corners */
			'image-rounded-corners' => array(
				'caption_position' => 'image',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => '',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '1',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_border_radius' => ['top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'unit' => 'px'],
				'caption_container_alignment_hover' => 'center',
				'pagination_numbers_radius' => ['top' => '5', 'right' => '5', 'bottom' => '5', 'left' => '5', 'unit' => 'px'],
				'pagination_numbers_background_color_normal' => '#99a9b5',
				'pagination_numbers_background_color_hover' => '#00BCD4',
				'pagination_numbers_background_color_active' => '#3c3950',
				'pagination_numbers_border_type_normal_border' => 'solid',
				'pagination_numbers_border_type_hover_border' => 'solid',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_normal_width' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px'],
				'pagination_numbers_border_type_hover_width' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px'],
				'pagination_numbers_border_type_active_width' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px'],
				'pagination_numbers_text_color_normal' => '#FFFFFF',
			),

			/** Caption on Image: Shadow Hover 01 */
			'image-shadow-hover-01' => array(
				'caption_position' => 'image',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => '',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '2',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 22, 'unit' => 'px'],
				'image_shadow_hover_box_shadow_type' => 'yes',
				'image_shadow_hover_box_shadow' => [
					'horizontal' => 0,
					'vertical' => 0,
					'blur' => 7,
					'spread' => 0,
					'color' => 'rgba(0, 0, 0, 0.25)',
				],
				'caption_container_alignment_hover' => 'center',
			),

			/** Caption on Image: Shadow */
			'image-shadow' => array(
				'caption_position' => 'image',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'button',
				'product_show_add_to_cart_mobiles' => '',
				'cart_button_show_icon' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '2',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_border_radius' => ['top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px'],
				'image_content_paddings' => ['top' => '20', 'right' => '20', 'bottom' => '20', 'left' => '20', 'unit' => 'px'],
				'image_shadow_normal_box_shadow_type' => 'yes',
				'image_shadow_normal_box_shadow' => [
					'horizontal' => 0,
					'vertical' => 0,
					'blur' => 40,
					'spread' => 5,
					'color' => 'rgba(33, 34, 39, 0.2)',
				],
				'image_shadow_hover_box_shadow_type' => 'yes',
				'image_shadow_hover_box_shadow' => [
					'horizontal' => 0,
					'vertical' => 10,
					'blur' => 50,
					'spread' => 10,
					'color' => 'rgba(33, 34, 39, 0.3)',
				],
				'caption_container_alignment_hover' => 'right',
				'pagination_numbers_radius' => ['top' => '100', 'right' => '100', 'bottom' => '100', 'left' => '100', 'unit' => 'px'],
				'pagination_numbers_background_color_normal' => '#99a9b5',
				'pagination_numbers_background_color_hover' => '#00BCD4',
				'pagination_numbers_background_color_active' => '#3c3950',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_active_width' => ['top' => '2', 'right' => '2', 'bottom' => '2', 'left' => '2', 'unit' => 'px'],
				'pagination_numbers_border_type_active_color' => '#3C3950',
				'pagination_numbers_text_color_normal' => '#FFFFFF',
			),

			/** Caption on Image: Product Separator 01 */
			'image-separator-01' => array(
				'caption_position' => 'image',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => '',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '3',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 0, 'unit' => 'px'],
				'image_content_paddings' => ['top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'unit' => 'px'],
				'product_separator' => 'yes',
				'product_separator_color' => '#DFE5E8',
				'product_separator_width' => ['size' => 4, 'unit' => 'px'],
				'caption_container_alignment_hover' => 'center',
				'pagination_numbers_background_color_active' => '#3c3950',
				'pagination_numbers_border_type_normal_border' => 'solid',
				'pagination_numbers_border_type_hover_border' => 'solid',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_normal_width' => ['top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4', 'unit' => 'px'],
				'pagination_numbers_border_type_hover_width' => ['top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4', 'unit' => 'px'],
				'pagination_numbers_border_type_active_width' => ['top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4', 'unit' => 'px'],
				'pagination_numbers_border_type_normal_color' => '#dfe5eb',
				'pagination_numbers_border_type_hover_color' => '#00BCD4',
				'pagination_numbers_border_type_active_color' => '#3C3950',
				'pagination_arrows_background_color_normal' => '#DFE5E8',
				'pagination_arrows_background_color_hover' => '#00BCD4',
			),

			/** Caption on Image: Product Separator 02 */
			'image-separator-02' => array(
				'caption_position' => 'image',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => '',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'labels_design' => '5',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 0, 'unit' => 'px'],
				'product_separator' => 'yes',
				'product_separator_color' => '#212227',
				'product_separator_width' => ['size' => 1, 'unit' => 'px'],
				'image_hover_effect_image' => 'gradient',
				'image_hover_overlay_background' => 'classic',
				'image_hover_overlay_color' => '#F0F3F200',
				'caption_container_preset_hover' => 'solid',
				'caption_container_alignment_hover' => 'center',
				'icons_background_color_normal' => '#02010100',
				'pagination_numbers_border_type_normal_border' => 'solid',
				'pagination_numbers_border_type_hover_border' => 'solid',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_normal_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_hover_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_active_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
			),

			/** Caption on Hover: Default */
			'hover-default' => array(
				'caption_position' => 'hover',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '4',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',
			),

			/** Caption on Hover: Rounded Corners */
			'hover-rounded-corners' => array(
				'caption_position' => 'hover',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'labels_design' => '1',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_border_radius' => ['top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'unit' => 'px'],
				'image_hover_effect_hover' => 'zooming-blur',
				'image_hover_overlay_background' => 'classic',
				'image_hover_overlay_color' => '#F0F3F2D4',
				'pagination_numbers_radius' => ['top' => '5', 'right' => '5', 'bottom' => '5', 'left' => '5', 'unit' => 'px'],
			),

			/** Caption on Hover: Solid Caption Background */
			'hover-solid-background' => array(
				'caption_position' => 'hover',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_reviews' => '',
				'product_show_reviews_tablet' => '',
				'product_show_reviews_mobile' => '',
				'labels_design' => '2',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 32, 'unit' => 'px'],
				'caption_container_preset_hover' => 'solid',
			),

			/** Caption on Hover: Product Separator */
			'hover-separator' => array(
				'caption_position' => 'hover',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'labels_design' => '3',
				'sale_label_type' => 'text',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 0, 'unit' => 'px'],
				'product_separator' => 'yes',
				'product_separator_color' => '#dfe5e8',
				'product_separator_width' => ['size' => 1, 'unit' => 'px'],
				'caption_container_alignment_hover' => 'center',
				'pagination_numbers_border_type_normal_border' => 'solid',
				'pagination_numbers_border_type_hover_border' => 'solid',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_normal_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_hover_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_active_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
			),

			/** Caption on Hover: Centered Caption */
			'hover-centered-caption' => array(
				'caption_position' => 'hover',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'button',
				'product_show_add_to_cart_mobiles' => 'yes',
				'cart_button_show_icon' => 'yes',
				'product_show_categories' => '',
				'product_show_categories_tablet' => '',
				'product_show_categories_mobile' => '',
				'product_show_new' => '',
				'product_show_sale' => '',
				'product_show_out' => '',
				'social_sharing' => '',

				'image_gaps' => ['size' => 6, 'unit' => 'px'],
				'image_content_paddings' => ['top' => '20', 'right' => '20', 'bottom' => '20', 'left' => '20', 'unit' => 'px'],
				'caption_container_height_hover' => ['size' => 70, 'unit' => '%'],
				'caption_container_background_hover_background' => 'gradient',
				'caption_container_background_hover_color' => '#F0F3F200',
				'caption_container_background_hover_color_b' => '#F0F3F2',
				'caption_container_alignment_hover' => 'center',
				'pagination_numbers_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => 'px'],
				'pagination_numbers_border_type_normal_border' => 'solid',
				'pagination_numbers_border_type_hover_border' => 'solid',
				'pagination_numbers_border_type_active_border' => 'solid',
				'pagination_numbers_border_type_normal_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_hover_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'pagination_numbers_border_type_active_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
			),

			/** Caption on Hover: Shadow Hover */
			'hover-shadow-hover' => array(
				'caption_position' => 'hover',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => 'yes',
				'labels_design' => '1',
				'sale_label_type' => 'percentage',
				'product_show_new' => 'yes',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 26, 'unit' => 'px'],
				'image_content_paddings' => ['top' => '10', 'right' => '30', 'bottom' => '0', 'left' => '30', 'unit' => 'px'],
				'image_hover_effect_hover' => 'zooming-blur',
				'image_shadow_hover_box_shadow_type' => 'yes',
				'image_shadow_hover_box_shadow' => [
					'horizontal' => 0,
					'vertical' => 0,
					'blur' => 20,
					'spread' => 0,
					'color' => 'rgba(29, 42, 47, 0.21)',
				],
			),

			/** Caption on Hover: Gradient Hover */
			'hover-gradient-hover' => array(
				'caption_position' => 'hover',
				'product_show_add_to_cart' => 'yes',
				'add_to_cart_type' => 'icon',
				'product_show_add_to_cart_mobiles' => 'yes',
				'labels_design' => '1',
				'sale_label_type' => 'percentage',
				'product_show_new' => '',
				'product_show_sale' => 'yes',
				'product_show_out' => 'yes',
				'social_sharing' => '',

				'image_gaps' => ['size' => 16, 'unit' => 'px'],
				'image_content_paddings' => ['top' => '30', 'right' => '30', 'bottom' => '30', 'left' => '30', 'unit' => 'px'],
				'image_hover_effect_hover' => 'gradient',
				'image_hover_overlay_background' => 'gradient',
				'image_hover_overlay_color' => '#FFDE1596',
				'image_hover_overlay_color_b' => '#E9135CBA',
				'image_hover_overlay_gradient_angle' => ['size' => 225, 'unit' => 'deg'],
			),

		);
	}
}

if (defined('WC_PLUGIN_FILE') && function_exists('get_thegem_portfolio_render_item_image_sizes')) {
	\Elementor\Plugin::instance()->widgets_manager->register( new TheGem_ExtendedProductsCarousel() );
}