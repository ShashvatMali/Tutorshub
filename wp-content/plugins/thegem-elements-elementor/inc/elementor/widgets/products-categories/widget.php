<?php

namespace TheGem_Elementor\Widgets\ProductsCategories;

use Elementor\Controls_Manager;
use TheGem_Elementor\Group_Control_Background_Light;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Products Grid.
 */
#[\AllowDynamicProperties]
class TheGem_ProductsCategories extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRODUCTSCATEGORIES_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRODUCTSCATEGORIES_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRODUCTSCATEGORIES_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRODUCTSCATEGORIES_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_script(
			'thegem-products-categories-scripts',
			THEGEM_ELEMENTOR_WIDGET_PRODUCTSCATEGORIES_URL . '/assets/js/thegem-products-categories.js',
			array('jquery'),
			null,
			true
		);

		$this->states_list = [
			'normal' => __('Normal', 'thegem'),
			'hover' => __('Hover', 'thegem'),
			'active' => __('Active', 'thegem'),
		];

		$this->schemes_list = [
			'6' => [
				'6a' => [
					'count' => 9,
					0 => 'squared',
				],
				'6b' => [
					'count' => 7,
					0 => 'squared',
					1 => 'horizontal',
					6 => 'horizontal',
				],
				'6c' => [
					'count' => 9,
					0 => 'horizontal',
					3 => 'horizontal',
					6 => 'horizontal',
				],
				'6d' => [
					'count' => 9,
					0 => 'horizontal',
					1 => 'horizontal',
					2 => 'horizontal',
				],
				'6e' => [
					'count' => 6,
					0 => 'squared',
					1 => 'squared',
				]
			],
			'5' => [
				'5a' => [
					'count' => 7,
					0 => 'squared',
				],
				'5b' => [
					'count' => 8,
					0 => 'horizontal',
					4 => 'horizontal',
				],
				'5c' => [
					'count' => 6,
					0 => 'horizontal',
					1 => 'horizontal',
					4 => 'horizontal',
					5 => 'horizontal',
				],
				'5d' => [
					'count' => 4,
					0 => 'squared',
					1 => 'vertical',
					2 => 'horizontal',
					3 => 'horizontal',
				]
			],
			'4' => [
				'4a' => [
					'count' => 5,
					0 => 'squared',
				],
				'4b' => [
					'count' => 4,
					0 => 'squared',
					1 => 'horizontal',
				],
				'4c' => [
					'count' => 4,
					0 => 'squared',
					1 => 'vertical',
				],
				'4d' => [
					'count' => 7,
					0 => 'vertical',
				],
				'4e' => [
					'count' => 4,
					0 => 'vertical',
					1 => 'vertical',
					2 => 'horizontal',
					3 => 'horizontal',
				],
				'4f' => [
					'count' => 6,
					0 => 'horizontal',
					5 => 'horizontal',
				]
			],
			'3' => [
				'3a' => [
					'count' => 4,
					0 => 'vertical',
					1 => 'vertical',
				],
				'3b' => [
					'count' => 4,
					1 => 'horizontal',
					2 => 'horizontal',
				],
				'3c' => [
					'count' => 5,
					0 => 'vertical',
				],
				'3d' => [
					'count' => 5,
					0 => 'horizontal',
				],
				'3e' => [
					'count' => 3,
					0 => 'squared',
				],
				'3f' => [
					'count' => 4,
					0 => 'horizontal',
					1 => 'vertical',
				],
				'3g' => [
					'count' => 4,
					0 => 'vertical',
					3 => 'horizontal',
				],
				'3h' => [
					'count' => 5,
					2 => 'vertical',
				]
			],
		];

		$this->is_product_archive = thegem_get_template_type( get_the_ID() ) === 'product-archive' || is_tax( 'product_cat' ) || is_tax( 'product_tag' ) || is_post_type_archive( 'product' );
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-products-categories';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Product Categories', 'thegem');
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
		return 'thegem-eicon thegem-eicon-products-categories';
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'product-archive') {
			return ['thegem_product_archive_builder'];
		}
		return ['thegem_woocommerce'];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'owl',
				'thegem-products-categories-styles'];
		}
		return ['thegem-products-categories-styles'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-animations',
				'thegem-items-animations',
				'thegem-scroll-monitor',
				'owl',
				'thegem-products-categories-scripts'];
		}
		return ['thegem-products-categories-scripts'];
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
				'image-light-caption' => [
					'label' => __('Light Caption', 'thegem'),
					'group' => 'caption-image',
				],
				'image-dark-caption' => [
					'label' => __('Dark Caption', 'thegem'),
					'group' => 'caption-image',
				],
				'image-transparent-light-title' => [
					'label' => __('Transparent, Light Title', 'thegem'),
					'group' => 'caption-image',
				],
				'image-transparent-dark-title' => [
					'label' => __('Transparent, Dark Title', 'thegem'),
					'group' => 'caption-image',
				],
				'image-bold-title-light' => [
					'label' => __('Bold Title, Light', 'thegem'),
					'group' => 'caption-image',
				],
				'image-bold-title-dark' => [
					'label' => __('Bold Title, Dark', 'thegem'),
					'group' => 'caption-image',
				],
				'below-default' => [
					'label' => __('Default', 'thegem'),
					'group' => 'caption-below',
				],
				'below-bordered' => [
					'label' => __('Bordered', 'thegem'),
					'group' => 'caption-below',
				],
				'below-solid' => [
					'label' => __('Solid', 'thegem'),
					'group' => 'caption-below',
				],
			]
		);
		return $out;
	}

	private function get_options_by_groups($skins, $group = false) {
		$group_labels = [
			'caption-image' => __('Caption on Image', 'thegem'),
			'caption-below' => __('Caption Below', 'thegem'),
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
		return 'image-light-caption';
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
			'layout_type',
			[
				'label' => __('Layout Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'grid' => __('Grid', 'thegem'),
					'creative' => __('Creative Grid', 'thegem'),
					'carousel' => __('Carousel', 'thegem'),
				],
			]
		);

		$this->add_control(
			'columns_desktop',
			[
				'label' => __('Columns Desktop', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '4x',
				'options' => [
					'1x' => __('1x columns (for mega menu, sidebar, narrow column)', 'thegem'),
					'2x' => __('2x columns (for mega menu, sidebar, narrow column)', 'thegem'),
					'3x' => __('3x columns', 'thegem'),
					'4x' => __('4x columns', 'thegem'),
					'5x' => __('5x columns', 'thegem'),
					'6x' => __('6x columns', 'thegem'),
					'8x' => __('8x columns', 'thegem'),
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

		foreach ((array)$this->schemes_list as $scheme_col => $scheme_values) {

			$options = [];
			$default = '';
			$i = 1;

			foreach ($scheme_values as $scheme_key => $scheme_val) {
				$options[$scheme_key] = __('Scheme', 'thegem') . $i;
				if ($i == 1) {
					$default = $scheme_key;
				}
				$i++;
			}

			$this->add_control(
				'layout_scheme_' . strval($scheme_col) . 'x',
				[
					'label' => __('Layout Scheme', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'default' => $default,
					'options' => $options,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'layout_type',
								'operator' => '=',
								'value' => 'creative',
							],
							[
								'relation' => 'or',
								'terms' => [
									[
										'name' => 'columns_desktop',
										'operator' => '=',
										'value' => strval($scheme_col) . 'x',
									],
									[
										'relation' => 'and',
										'terms' => [
											[
												'name' => 'columns_desktop',
												'operator' => '=',
												'value' => '100%',
											], [
												'name' => 'columns_100',
												'operator' => '=',
												'value' => strval($scheme_col),
											],
										]
									]
								],
							],
						],
					],
				]
			);

			foreach ($scheme_values as $scheme_key => $scheme_val) {

				$this->add_control(
					'layout_scheme_' . $scheme_key,
					[
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'raw' => '<img src="' . THEGEM_ELEMENTOR_URL . '/assets/img/creative/scheme' . $scheme_key . '.png">',
						'conditions' => [
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'layout_type',
									'operator' => '=',
									'value' => 'creative',
								],
								[
									'relation' => 'or',
									'terms' => [
										[
											'name' => 'columns_desktop',
											'operator' => '=',
											'value' => strval($scheme_col) . 'x',
										],
										[
											'relation' => 'and',
											'terms' => [
												[
													'name' => 'columns_desktop',
													'operator' => '=',
													'value' => '100%',
												], [
													'name' => 'columns_100',
													'operator' => '=',
													'value' => strval($scheme_col),
												],
											]
										]
									],
								],
								[
									'name' => 'layout_scheme_' . strval($scheme_col) . 'x',
									'operator' => '=',
									'value' => strval($scheme_key),
								]
							],
						],
					]
				);
			}
		}

		$this->add_control(
			'scheme_apply_mobiles', [
				'label' => __('Apply on mobiles', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout_type' => 'creative',
				],
			]
		);

		$this->add_control(
			'scheme_apply_tablets', [
				'label' => __('Apply on tablets', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout_type' => 'creative',
				],
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
					'{{WRAPPER}} .products-category-item:not(.double-item) .category-thumbnail' => 'aspect-ratio: {{SIZE}} !important; height: auto; padding: 0;',
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
					'{{WRAPPER}} .products-category-item:not(.double-item) .category-thumbnail' => 'aspect-ratio: {{SIZE}} !important; height: auto; padding: 0;',
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
			'custom_images_height', [
				'label' => __('Custom Images Height', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'layout_type' => 'grid',
				],
			]
		);

		$this->add_control(
			'images_height',
			[
				'label' => __('Images Height, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .products-category-item .wrap .category-thumbnail' => 'height:{{SIZE}}{{UNIT}}; padding: 0;',
				],
				'condition' => [
					'layout_type' => 'grid',
					'custom_images_height' => 'yes',
				],
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
				'condition' => [
					'layout_type' => 'carousel',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_categories',
			[
				'label' => __('Categories', 'thegem'),
			]
		);

		if ( $this->is_product_archive ) {

			$this->add_control(
				'source',
				[
					'label' => __('Source', 'thegem'),
					'default' => 'manual',
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'options' => [
						'manual' => __('Manual Selection', 'thegem'),
						'subcategories' => __('Show Subcategories', 'thegem'),
					],
				]
			);

		} else {

			$this->add_control(
				'source',
				[
					'type' => Controls_Manager::HIDDEN,
					'default' => 'manual',
				]
			);
		}

		$this->add_control(
			'content_products_cat',
			[
				'label' => __('Select Product Categories', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->select_products_sets(),
				'frontend_available' => true,
				'condition' => [
					'source' => 'manual',
				],
			]
		);

		$this->add_control(
			'hide_empty', [
				'label' => __('Hide Empty', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
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
					'name' => __('Title', 'thegem'),
					'term_id' => __('ID', 'thegem'),
					'count' => __('Product Count', 'thegem'),
					'id' => __('Date', 'thegem'),
					'order' => __('Menu Order', 'thegem'),
				],
				'default' => 'name',
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
				'default' => 'image',
				'options' => [
					'image' => __('On Image', 'thegem'),
					'below' => __('Below Image', 'thegem'),
				],
			]
		);

		$this->add_control(
			'product_counts',
			[
				'label' => __('Product Counts', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'always ' => __('Always Visible', 'thegem'),
					'hover' => __('Visible on Hover', 'thegem'),
					'hidden' => __('Hidden', 'thegem'),
				],
			]
		);

		$this->add_control(
			'product_singular_text',
			[
				'label' => __('"Product" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('product', 'thegem'),
				'condition' => [
					'product_counts!' => 'hidden',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'product_plural_text',
			[
				'label' => __('"Products" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('products', 'thegem'),
				'condition' => [
					'product_counts!' => 'hidden',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'caption_separator',
			[
				'label' => __('Separator', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'items_count',
			[
				'label' => __('Items Count', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pagination',
			[
				'label' => __('Navigation', 'thegem'),
				'condition' => [
					'layout_type' => 'carousel',
				],
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
			'section_additional',
			[
				'label' => __('Additional Options', 'thegem'),
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
				'condition' => [
					'layout_type' => 'carousel',
				],
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
				'condition' => [
					'layout_type' => 'carousel',
				],
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
				'condition' => [
					'layout_type' => 'carousel',
				],
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
					'layout_type' => 'carousel',
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
				'condition' => [
					'layout_type' => 'carousel',
				],
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
			'skeleton_loader',
			[
				'label' => __('Skeleton Preloader on grid loading', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'layout_type' => 'grid',
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

		/* Images Style */
		$this->image_style($control);

		/* Caption Style */
		$this->caption_style($control);

		/* Caption Container Style */
		$this->caption_container_style($control);

		/* Navigation Style */
		$this->navigation_style($control);
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
					'{{WRAPPER}} .products-categories-widget:not(.extended-carousel-grid) .products-category-item,
					{{WRAPPER}} .skeleton-posts .products-category-item' => 'padding: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .products-categories-widget:not(.item-separator):not(.extended-carousel-grid) .categories-row,
					{{WRAPPER}} .skeleton-posts.categories-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .products-categories-widget.layout-type-carousel:not(.inited) .owl-stage,
					{{WRAPPER}} .products-categories-widget.item-separator:not(.extended-carousel-grid) .categories-row' => 'margin: 0 calc(-{{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}} .products-categories-widget.fullwidth-columns:not(.item-separator):not(.extended-carousel-grid) .categories-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2) 0;',
					'{{WRAPPER}} .products-categories-widget.fullwidth-columns.item-separator:not(.extended-carousel-grid) .categories-row' => 'margin: 0;',
					'{{WRAPPER}} .products-categories-widget.layout-type-carousel:not(.inited) .owl-stage .products-category-item,
					{{WRAPPER}} .products-categories-widget:not(.extended-carousel-grid) .fullwidth-block .categories-row' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}} .products-categories-widget.extended-carousel-grid.item-separator .products-category-item' => 'padding: calc({{SIZE}}{{UNIT}}/2) !important;',
					'{{WRAPPER}} .products-categories-widget.extended-carousel-grid:not(.item-separator) .fullwidth-block' => 'padding: 0 {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_control(
			'product_separator',
			[
				'label' => __('Categories Separator', 'thegem'),
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
				'selectors' => [
					'{{WRAPPER}} .products-categories-widget.item-separator .products-category-item:before, 
					{{WRAPPER}} .products-categories-widget.item-separator .products-category-item:after, 
					{{WRAPPER}} .products-categories-widget.item-separator .products-category-item .item-separator-box:before, 
					{{WRAPPER}} .products-categories-widget.item-separator .products-category-item .item-separator-box:after' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'product_separator' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
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
					'{{WRAPPER}} .products-categories-widget.item-separator .products-category-item:before, 
					{{WRAPPER}} .products-categories-widget.item-separator .products-category-item:after, 
					{{WRAPPER}} .products-categories-widget.item-separator .products-category-item .item-separator-box:before, 
					{{WRAPPER}} .products-categories-widget.item-separator .products-category-item .item-separator-box:after' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .products-categories-widget.item-separator .products-category-item:before, 
					{{WRAPPER}} .products-categories-widget.item-separator .products-category-item:after' => 'height: calc(100% + {{SIZE}}{{UNIT}}); top: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .products-categories-widget.item-separator .products-category-item .item-separator-box:before, 
					{{WRAPPER}} .products-categories-widget.item-separator .products-category-item .item-separator-box:after' => 'width: calc(100% + {{SIZE}}{{UNIT}}); left: calc(-{{SIZE}}{{UNIT}}/2);',
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
					'{{WRAPPER}} .products-categories-widget.caption-position-image .products-category-item .wrap,
					{{WRAPPER}} .products-categories-widget.caption-position-below .products-category-item .wrap .category-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .products-categories-widget.caption-position-below .products-category-item .wrap' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('full_item_border_tabs');

		$control->start_controls_tab('full_item_border_tab_normal', ['label' => __('Normal', 'thegem')]);


		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'full_item_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .products-categories-widget.caption-position-image .products-category-item .wrap,
				{{WRAPPER}} .products-categories-widget.caption-position-below .products-category-item .wrap .category-thumbnail',
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
					'{{WRAPPER}} .products-categories-widget.caption-position-image .products-category-item .wrap,
					{{WRAPPER}} .products-categories-widget.caption-position-below .products-category-item .wrap .category-thumbnail' => 'border-color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .products-categories-widget.caption-position-image .products-category-item .wrap,
				{{WRAPPER}} .products-categories-widget.caption-position-below:not(.shadowed-container) .products-category-item .wrap .category-thumbnail,
				{{WRAPPER}} .products-categories-widget.caption-position-below.shadowed-container .products-category-item .wrap',
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'image_overlay_normal',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .products-categories-widget .products-category-item .category-thumbnail:after',
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-thumbnail',
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
					'{{WRAPPER}} .products-categories-widget .products-category-item:hover .wrap .category-thumbnail' => 'border-color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .products-categories-widget:not(.shadowed-container) .products-category-item:hover .wrap .category-thumbnail,
				{{WRAPPER}} .products-categories-widget.shadowed-container .products-category-item:hover .wrap',
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'image_overlay_hover',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .products-categories-widget .products-category-item:hover .category-thumbnail:after',
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_hover',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .products-categories-widget .products-category-item:hover .wrap .category-thumbnail',
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
							'value' => 'below',
						],
					],
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
			'caption_container_header',
			[
				'label' => __('Container', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'caption_position' => 'image',
				],
			]
		);

		$control->add_control(
			'caption_container_preset',
			[
				'label' => __('Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid' => __('Solid', 'thegem'),
					'transparent' => __('Transparent', 'thegem'),
					'bold' => __('Bold Title', 'thegem'),
				],
				'condition' => [
					'caption_position' => 'image',
				],
			]
		);

		$control->add_control(
			'caption_container_preset_color',
			[
				'label' => __('Color Scheme', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => __('Light', 'thegem'),
					'dark' => __('Dark', 'thegem'),
				],
				'condition' => [
					'caption_position' => 'image',
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_vertical_position',
			[
				'label' => __('Vertical Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'top' => [
						'title' => __('Top', 'thegem'),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'thegem'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'bottom',
				'toggle' => false,
				'selectors_dictionary' => [
					'top' => 'align-items: flex-start;',
					'center' => 'align-items: center;',
					'bottom' => 'align-items: flex-end',

				],
				'selectors' => [
					'{{WRAPPER}} .products-categories-widget.caption-position-image .products-category-item .wrap .category-overlay' => '{{VALUE}}',
				],
				'condition' => [
					'caption_position' => 'image',
				],
				'frontend_available' => true,
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
				'default' => 'center',
				'toggle' => false,
				'condition' => [
					'caption_position' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'caption_container_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'caption_position' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'caption_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-overlay-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'caption_position' => 'image',
				],
			]
		);

		$control->start_controls_tabs('caption_container_tabs', [
			'condition' => [
				'caption_position' => 'image',
				'caption_container_preset' => 'solid',
			]
		]);

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('caption_container_tab_' . $stkey, [
					'label' => $stelem,
				]);

				$control->add_control(
					'caption_container_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .products-categories-widget.caption-position-image.caption-container-preset-solid .products-category-item' . $state . ' .wrap .category-overlay .category-overlay-inner' => 'background: {{VALUE}};',
						],
						'separator' => 'after',

					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'categories_title_header',
			[
				'label' => __('Categories Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'categories_title_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'condition' => [
					'caption_position' => 'image',
					'caption_container_preset' => 'bold',
				],
				'selectors' => [
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'categories_title_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-title' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'caption_position' => 'image',
					'caption_container_preset' => 'bold',
				],
			]
		);

		$control->add_responsive_control(
			'categories_title_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'caption_position' => 'image',
					'caption_container_preset' => 'bold',
				],
			]
		);

		$control->start_controls_tabs('categories_title_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('categories_title_tab_' . $stkey, [
					'label' => $stelem,
				]);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'categories_title_typography_' . $stkey,
						'selector' => '{{WRAPPER}} .products-categories-widget .products-category-item' . $state . ' .wrap .category-overlay .category-title',
					]
				);

				$control->add_control(
					'categories_title_color_' . $stkey,
					[
						'label' => __('Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .products-categories-widget .products-category-item' . $state . ' .wrap .category-overlay .category-title' => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'categories_title_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .products-categories-widget .products-category-item' . $state . ' .wrap .category-overlay .category-title' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'caption_position' => 'image',
							'caption_container_preset' => 'bold',
						],
					]
				);

				$control->add_control(
					'categories_title_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .products-categories-widget .products-category-item' . $state . ' .wrap .category-overlay .category-title' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'caption_position' => 'image',
							'caption_container_preset' => 'bold',
						],
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'categories_counts_header',
			[
				'label' => __('Product Counts', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('categories_counts_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('categories_counts_tab_' . $stkey, [
					'label' => $stelem,
				]);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'categories_counts_typography_' . $stkey,
						'selector' => '{{WRAPPER}} .products-categories-widget .products-category-item' . $state . ' .wrap .category-overlay .category-count',
					]
				);

				$control->add_control(
					'categories_counts_color_' . $stkey,
					[
						'label' => __('Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .products-categories-widget .products-category-item' . $state . ' .wrap .category-overlay .category-count' => 'color: {{VALUE}};',
						],
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'caption_separator_header',
			[
				'label' => 'Separator',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'caption_separator' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_separator_weight',
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
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-overlay-separator' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'caption_separator' => 'yes',
				]
			]
		);

		$control->start_controls_tabs('caption_separator_tabs', [
			'condition' => [
				'caption_separator' => 'yes',
			]]);

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('caption_separator_tab_' . $stkey, [
					'label' => $stelem,
				]);

				$control->add_control(
					'caption_separator_color_' . $stkey,
					[
						'label' => __('Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .products-categories-widget .products-category-item' . $state . ' .wrap .category-overlay .category-overlay-separator' => 'background-color: {{VALUE}}',
						]
					]
				);

				$control->add_responsive_control(
					'caption_separator_size_' . $stkey,
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
							'{{WRAPPER}} .products-categories-widget .products-category-item' . $state . ' .wrap .category-overlay .category-overlay-separator' => 'width: {{SIZE}}{{UNIT}}',
						]
					]
				);

				$control->end_controls_tab();

			}
		}

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
				'condition' => ['caption_position' => 'below']
			]
		);

		$control->add_control(
			'caption_container_preset_below',
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
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-overlay-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_padding_below',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-overlay-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_alignment_below',
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
			]
		);

		$control->start_controls_tabs('caption_container_tabs_below');

		$control->start_controls_tab('caption_container_tab_below_normal', ['label' => __('Normal', 'thegem')]);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'caption_container_background_below_normal',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-overlay-inner',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'caption_container_border_normal',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-overlay-inner',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('caption_container_tab_below_hover', ['label' => __('Hover', 'thegem')]);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'caption_container_background_below_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .products-categories-widget .products-category-item:hover .wrap .category-overlay .category-overlay-inner',
			]
		);

		$control->add_control(
			'caption_container_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .products-categories-widget .products-category-item:hover .wrap .category-overlay .category-overlay-inner' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'caption_container_border_normal_border!' => '',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_control(
			'spacing_title_header',
			[
				'label' => 'Category Title',
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
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-title' => 'margin-top: {{TOP}}{{UNIT}};',
					'{{WRAPPER}} .products-categories-widget .products-category-item .wrap .category-overlay .category-count .category-count-inside' => 'padding-top: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

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
				'condition' => [
					'layout_type' => 'carousel',
				],
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
					'{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-prev div i, 
					{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-next div i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-prev div.position-on, 
					{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-next div.position-on' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-prev div.position-on, 
					{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-next div.position-on' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-prev div.position-on, 
					{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-next div.position-on',
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
							'{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-prev' . $state . ' div.position-on, 
							{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-next' . $state . ' div.position-on' => 'background-color: {{VALUE}};',
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
							'{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-prev' . $state . ' div.position-on, 
							{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-next' . $state . ' div.position-on' => 'color: {{VALUE}};',
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
							'{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-prev' . $state . ' div, 
							{{WRAPPER}} .products-categories-widget .extended-carousel-item .owl-nav .owl-next' . $state . ' div' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .products-categories-widget .owl-dots .owl-dot span' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .products-categories-widget .owl-dots' => 'margin-top: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .products-categories-widget .owl-dots .owl-dot' => 'margin: 0 calc({{SIZE}}{{UNIT}}/2)',
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
				'selector' => '{{WRAPPER}} .products-categories-widget .owl-dots .owl-dot span',
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
							'{{WRAPPER}} .products-categories-widget .owl-dots .owl-dot' . $state . ' span' => 'background-color: {{VALUE}};',
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
							'{{WRAPPER}} .products-categories-widget .owl-dots .owl-dot' . $state . ' span' => 'border-color: {{VALUE}};',
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

		if ($params['source'] == 'subcategories') {
			if (is_tax( 'product_cat' )) {
				$categories = get_terms('product_cat', array('fields' => 'slugs', 'parent' => thegem_templates_product_archive_source()->term_id ));
			} else {
				$categories = ['0'];
			}
		} else {
			$categories = $params['content_products_cat'];
		}

		$cat_args = [
			'taxonomy' => 'product_cat',
			'hide_empty' => $params['hide_empty'] == 'yes',
			'orderby' => $params['orderby'],
			'order' => $params['order'],
		];

		if ('order' === $params['orderby']) {
			$cat_args['orderby'] = 'meta_value_num';
			$cat_args['meta_key'] = 'order';
		}

		$sorted_categories = get_terms($cat_args);

		if ($params['layout_type'] === 'creative' && ($params['columns_desktop'] == '1x' || $params['columns_desktop'] == '2x' || $params['columns_desktop'] == '8x')) {
			$params['layout_type'] = 'grid';
		}

		if ($params['layout_type'] === 'carousel') {
			wp_enqueue_style('owl');
			wp_enqueue_script('owl');
		}

		if ($params['loading_animation'] === 'yes') {
			wp_enqueue_style('thegem-animations');
			wp_enqueue_script('thegem-items-animations');
			wp_enqueue_script('thegem-scroll-monitor');
		}

		if ($params['slider_scroll_init'] === 'yes') {
			wp_enqueue_script('thegem-scroll-monitor');
		}

		if (!empty($categories)) :
			if (in_array('0', $categories)) {
				$categories = get_terms('product_cat', array('fields' => 'slugs'));
			}

			if ($params['product_separator'] == 'yes' && intval($params['product_separator_width']['size']) % 2 !== 0) {
				$floor = floor(intval($params['product_separator_width']['size']) / 2);
				$ceil = ceil(intval($params['product_separator_width']['size']) / 2); ?>
				<style>
					.elementor-element-<?php echo $grid_uid; ?> .categories-set .products-category-item:before {
						transform: translateX(-<?php echo $floor; ?>px) !important;
						top: -<?php echo $floor; ?>px !important;
					}

					.elementor-element-<?php echo $grid_uid; ?> .categories-set .products-category-item:after {
						transform: translateX(<?php echo $ceil; ?>px) !important;
						top: -<?php echo $floor; ?>px !important;
					}

					.elementor-element-<?php echo $grid_uid; ?> .categories-set .products-category-item .item-separator-box:before {
						transform: translateY(-<?php echo $floor; ?>px) !important;
						left: -<?php echo $floor; ?>px !important;
					}

					.elementor-element-<?php echo $grid_uid; ?> .categories-set .products-category-item .item-separator-box:after {
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

			$thegem_sizes = get_thegem_portfolio_render_item_image_sizes($params);
			$item_classes = get_thegem_portfolio_render_item_classes($params);
			if ($params['columns_desktop'] == '100%') {
				echo apply_filters('thegem_portfolio_preloader_html', '<div class="preloader save-space"><div class="preloader-spin"></div></div>');
			} else if (($params['layout_type'] == 'carousel' && $params['disable_preloader'] == '') || $params['skeleton_loader'] == 'yes') { ?>
				<div class="preloader <?php echo $params['layout_type'] == 'carousel' ? 'skeleton-carousel' : 'save-space'; ?>">
					<div class="skeleton">
						<div class="skeleton-posts row categories-row caption-position-<?php echo $params['caption_position']; ?> aspect-ratio-<?php echo $params['image_aspect_ratio']; ?>">
							<?php for ($x = 0; $x < sizeof($categories); $x++) { ?>
								<div class="products-category-item <?php echo implode(" ", $item_classes); ?>"></div>
								<?php
								if (!empty($params['items_count']) && $x == $params['items_count']) {
									break;
								}
							} ?>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="portfolio-preloader-wrapper">

				<?php
				$gaps_size = isset($params['image_gaps']['size']) && $params['image_gaps']['size'] != '' ? $params['image_gaps']['size'] : 0;
				$gaps_size_tablet = isset($params['image_gaps_tablet']['size']) && $params['image_gaps_tablet']['size'] != '' ? $params['image_gaps_tablet']['size'] : $gaps_size;
				$gaps_size_mobile = isset($params['image_gaps_mobile']['size']) && $params['image_gaps_mobile']['size'] != '' ? $params['image_gaps_mobile']['size'] : $gaps_size_tablet;

				$this->add_render_attribute(
					'products-wrap',
					[
						'class' => [
							'products-categories-widget',
							'layout-type-' . $params['layout_type'],
							'portfolio-preset-' . $params['thegem_elementor_preset'],
							'caption-position-' . $params['caption_position'],
							'counts-visible-' . $params['product_counts'],
							($params['image_size'] == 'default' ? 'aspect-ratio-' . $params['image_aspect_ratio'] : ''),
							($params['caption_position'] == 'image' ? 'caption-container-preset-' . $params['caption_container_preset'] : 'caption-container-preset-' . $params['caption_container_preset_below']),
							($params['caption_position'] == 'image' ? 'caption-container-preset-color-' . $params['caption_container_preset_color'] : ''),
							($params['caption_position'] == 'image' ? 'caption-container-vertical-position-' . $params['caption_container_vertical_position'] : ''),
							($params['layout_type'] == 'carousel' ? 'extended-carousel-grid arrows-position-' . $params['arrows_navigation_position'] : ''),
							($params['loading_animation'] == 'yes' ? 'loading-animation' : ''),
							($params['loading_animation'] == 'yes' && $params['animation_effect'] ? 'item-animation-' . $params['animation_effect'] : ''),
							($gaps_size == 0 ? 'no-gaps' : ''),
							($params['shadowed_container'] == 'yes' ? 'shadowed-container' : ''),
							($params['columns_desktop'] == '100%' ? 'fullwidth-columns fullwidth-columns-desktop-' . $params['columns_100'] : ''),
							($params['columns_desktop'] != '100%' ? 'columns-desktop-' . str_replace("x", "", $params['columns_desktop']) : 'columns-desktop-' . $params['columns_100']),
							($params['columns_desktop'] == '100%' && ($gaps_size < 24 || $params['product_separator'] == 'yes') ? 'prevent-arrows-outside' : ''),
							('columns-tablet-' . str_replace("x", "", $params['columns_tablet'])),
							('columns-mobile-' . str_replace("x", "", $params['columns_mobile'])),
							($params['product_separator'] == 'yes' ? 'item-separator' : ''),
							($params['arrows_navigation_visibility'] == 'hover' ? 'arrows-hover' : ''),
							($params['slider_scroll_init'] === 'yes' || $params['slider_loop'] == 'yes' ? 'carousel-scroll-init' : ''),
							($params['layout_type'] === 'creative' && $params['scheme_apply_mobiles'] !== 'yes' ? 'creative-disable-mobile' : ''),
							($params['layout_type'] === 'creative' && $params['scheme_apply_tablets'] !== 'yes' ? 'creative-disable-tablet' : ''),
							($params['caption_position'] === 'below' ? 'caption-container-alignment-' . $params['caption_container_alignment_below'] : 'caption-container-alignment-' . $params['caption_container_alignment']),
							(($params['image_size'] == 'full' && empty($params['image_ratio_full']['size']) || !in_array($params['image_size'], ['full', 'default'])) ? 'full-image' : ''),
						],
						'data-portfolio-uid' => esc_attr($grid_uid),
						'data-columns-mobile' => esc_attr(str_replace("x", "", $params['columns_mobile'])),
						'data-columns-tablet' => esc_attr(str_replace("x", "", $params['columns_tablet'])),
						'data-columns-desktop' => $params['columns_desktop'] != '100%' ? esc_attr(str_replace("x", "", $params['columns_desktop'])) : esc_attr($params['columns_100']),
						'data-margin-mobile' => esc_attr($gaps_size_mobile),
						'data-margin-tablet' => esc_attr($gaps_size_tablet),
						'data-margin-desktop' => esc_attr($gaps_size),
						'data-dots' => $params['show_dots_navigation'] == 'yes' ? '1' : '0',
						'data-arrows' => $params['show_arrows_navigation'] == 'yes' ? '1' : '0',
						'data-loop' => $params['slider_loop'] == 'yes' ? '1' : '0',
						'data-sliding-animation' => $params['sliding_animation'],
						'data-autoscroll-speed' => $params['autoscroll'] == 'yes' ? $params['autoscroll_speed']['size'] : '0',
					]
				);
				?>

				<div <?php echo $this->get_render_attribute_string('products-wrap'); ?>>
					<div class="categories-row-outer <?php if ($params['columns_desktop'] == '100%'): ?>fullwidth-block no-paddings<?php endif; ?>">
						<div class="categories-row clearfix">
							<div class="categories-set">
								<?php
								if ($params['layout_type'] == 'creative') {
									$columns = $params['columns_desktop'] != '100%' ? str_replace("x", "", $params['columns_desktop']) : $params['columns_100'];
									$items_sizes = $this->schemes_list[$columns][$params['layout_scheme_' . $columns . 'x']];
									$items_count = $items_sizes['count'];
								} else if ($params['layout_type'] == 'carousel') { ?>
								<div class="extended-carousel-wrap">
									<div class="extended-carousel-item owl-carousel owl-theme owl-loaded">
										<div class="owl-stage-outer">
											<div class="owl-stage">
												<?php }

												$i = 0;
												foreach ($sorted_categories as $category) {
													if (in_array($category->slug, $categories)) {

														$thegem_highlight_type = 'disabled';
														if ($params['layout_type'] == 'creative') {
															$item_num = $i % $items_count;
															if (isset($items_sizes[$item_num])) {
																$thegem_highlight_type = $items_sizes[$item_num];
															}
														}

														$this->thegem_category_render_item($params, $thegem_sizes, $category, $thegem_highlight_type);

														$i++;

														if (!empty($params['items_count']) && $i == $params['items_count']) {
															break;
														}
													}
												}
												if ($params['layout_type'] == 'creative') {
													$this->thegem_category_render_item($params, $thegem_sizes);
												} else if ($params['layout_type'] == 'carousel') { ?>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
							</div><!-- .portflio-set -->
						</div><!-- .row-->
						<?php if ($params['layout_type'] == 'carousel' && $params['show_arrows_navigation'] == 'yes'): ?>
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
					</div><!-- .full-width -->
				</div><!-- .portfolio-->
			</div><!-- .portfolio-preloader-wrapper-->
		<?php

		else: ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo esc_html__('Please select product categories in "Categories" section', 'thegem') ?>
			</div>
		<?php endif;
		$post = $portfolio_posttemp;

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>

			<script type="text/javascript">
				(function ($) {

					setTimeout(function () {
						$('.elementor-element-<?php echo $this->get_id(); ?> .products-categories-widget').initCategoriesGalleries();
						$('.elementor-element-<?php echo $this->get_id(); ?> .products-categories-widget.layout-type-carousel').updateCategoriesGalleries();
					}, 1000);

				})(jQuery);

			</script>
		<?php endif;
	}

	public function thegem_category_render_item($params, $thegem_sizes, $category = null, $thegem_highlight_type = 'disabled') {

		$thegem_classes = array('products-category-item');

		if (!$category) {
			$thegem_classes[] = 'size-item';

			$category = (object)array(
				'term_id' => ' ',
				'name' => 'Size',
				'count' => '0',
			);
		}

		if ($thegem_highlight_type != 'disabled') {
			$thegem_classes[] = 'double-item-' . $thegem_highlight_type;

			$thegem_sizes = get_thegem_portfolio_render_item_image_sizes($params, $thegem_highlight_type);
		}

		if ($params['loading_animation'] === 'yes') {
			$thegem_classes[] = 'item-animations-not-inited';
		}

		if ($params['layout_type'] == 'carousel') {
			$thegem_classes[] = 'owl-item';
		}

		$preset_path = __DIR__ . '/templates/content-product-category-item.php';
		$preset_path_filtered = apply_filters('thegem_products_categories_item_preset', $preset_path);
		$preset_path_theme = get_stylesheet_directory() . '/templates/products-grid/content-product-category-item.php';

		if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
			include($preset_path_theme);
		} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
			include($preset_path_filtered);
		}

	}

	public function get_preset_data() {

		return array(

			'image-light-caption' => array(
				'caption_position' => 'image',
				'caption_separator' => 'yes',

				'caption_container_preset' => 'solid',
				'caption_container_preset_color' => 'light',
				'caption_container_vertical_position' => 'bottom',
				'caption_container_alignment' => 'center',
			),

			'image-dark-caption' => array(
				'caption_position' => 'image',
				'caption_separator' => 'yes',

				'caption_container_preset' => 'solid',
				'caption_container_preset_color' => 'dark',
				'caption_container_vertical_position' => 'bottom',
				'caption_container_alignment' => 'center',
			),

			'image-transparent-light-title' => array(
				'caption_position' => 'image',
				'caption_separator' => '',

				'caption_container_preset' => 'transparent',
				'caption_container_preset_color' => 'light',
				'caption_container_vertical_position' => 'bottom',
				'caption_container_alignment' => 'left',
			),

			'image-transparent-dark-title' => array(
				'caption_position' => 'image',
				'caption_separator' => '',

				'caption_container_preset' => 'transparent',
				'caption_container_preset_color' => 'dark',
				'caption_container_vertical_position' => 'top',
				'caption_container_alignment' => 'left',
			),

			'image-bold-title-light' => array(
				'caption_position' => 'image',
				'caption_separator' => '',

				'caption_container_preset' => 'bold',
				'caption_container_preset_color' => 'light',
				'caption_container_vertical_position' => 'center',
				'caption_container_alignment' => 'center',
			),

			'image-bold-title-dark' => array(
				'caption_position' => 'image',
				'caption_separator' => '',

				'caption_container_preset' => 'bold',
				'caption_container_preset_color' => 'dark',
				'caption_container_vertical_position' => 'center',
				'caption_container_alignment' => 'center',
			),

			'below-default' => array(
				'caption_position' => 'below',
				'caption_separator' => '',

				'caption_container_preset_below' => 'transparent',
				'caption_container_alignment_below' => 'center',
			),

			'below-bordered' => array(
				'caption_position' => 'below',
				'caption_separator' => '',

				'caption_container_preset_below' => 'white',
				'caption_container_alignment_below' => 'center',
				'caption_container_border_normal_border' => 'solid',
				'caption_container_border_normal_width' => ['top' => '0', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px'],
				'caption_container_border_normal_color' => '#dfe5e8',
			),

			'below-solid' => array(
				'caption_position' => 'below',
				'caption_separator' => '',

				'caption_container_preset_below' => 'gray',
				'caption_container_alignment_below' => 'center',
			),
		);
	}
}

if (defined('WC_PLUGIN_FILE') && function_exists('get_thegem_portfolio_render_item_image_sizes')) {
	\Elementor\Plugin::instance()->widgets_manager->register( new TheGem_ProductsCategories() );
}