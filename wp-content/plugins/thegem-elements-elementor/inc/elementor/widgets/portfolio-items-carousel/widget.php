<?php

namespace TheGem_Elementor\Widgets\PortfolioCarousel;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use TheGem_Elementor\Group_Control_Background_Light;
use WP_Query;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Portfolio Carousel.
 */
#[\AllowDynamicProperties]
class TheGem_PortfolioCarousel extends Widget_Base {

	public function __construct($data = [], $args = null) {

		$template_type = isset($GLOBALS['thegem_template_type']) ? $GLOBALS['thegem_template_type'] : thegem_get_template_type(get_the_ID());
		$this->is_portfolio_post = $template_type === 'portfolio';

		if (isset($data['settings'])) {

			if ($this->is_portfolio_post) {
				if (isset($data['settings']['source_type']) && $data['settings']['source_type'] == 'custom') {
					$data['settings']['query_type'] = 'portfolios';
					unset($data['settings']['source_type']);
				}
				if (!isset($data['settings']['query_type'])) {
					$data['settings']['query_type'] = 'related';
				}
			}
		}

		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_EXTENDED_PORTFOLIO_CAROUSEL_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_EXTENDED_PORTFOLIO_CAROUSEL_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_EXTENDED_PORTFOLIO_CAROUSEL_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_EXTENDED_PORTFOLIO_CAROUSEL_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-portfolio-carousel-styles',
			THEGEM_ELEMENTOR_WIDGET_EXTENDED_PORTFOLIO_CAROUSEL_URL . '/assets/css/thegem-portfolio-carousel.css',
			array('owl', 'thegem-portfolio'),
			null
		);

		wp_register_script(
			'thegem-portfolio-carousel-scripts',
			THEGEM_ELEMENTOR_WIDGET_EXTENDED_PORTFOLIO_CAROUSEL_URL . '/assets/js/thegem-portfolio-carousel.js',
			array('jquery', 'owl', 'thegem-portfolio-grid-extended'),
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
		return 'thegem-portfolio-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Portfolio Carousel', 'thegem');
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'portfolio') {
			return ['thegem_portfolio_builder'];
		}
		return ['thegem_portfolios'];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-button',
				'thegem-animations',
				'thegem-hovers-default',
				'thegem-hovers-zooming-blur',
				'thegem-hovers-horizontal-sliding',
				'thegem-hovers-vertical-sliding',
				'thegem-hovers-zoom-overlay',
				'thegem-hovers-gradient',
				'thegem-hovers-circular',
				'thegem-hovers-disabled',
				'thegem-portfolio-carousel-styles'];
		}
		return ['thegem-portfolio-carousel-styles'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'jquery-dlmenu',
				'jquery-touch-punch',
				'jquery-ui-slider',
				'thegem-animations',
				'thegem-items-animations',
				'thegem-scroll-monitor',
				'thegem-portfolio-carousel-scripts'];
		}
		return ['thegem-portfolio-carousel-scripts'];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
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
		return 'default';
	}

	protected function select_portfolio_custom_fields() {
		$out   = [];
		$pt_data = thegem_theme_options_get_page_settings('portfolio');
		$custom_fields = !empty($pt_data['custom_fields']) ? $pt_data['custom_fields'] : null;

		if (empty($custom_fields)) {
			return $out;
		}

		$custom_fields_data = !empty($pt_data['custom_fields_data']) ? json_decode($pt_data['custom_fields_data'], true) : null;
		if (!empty($custom_fields_data)) {
			foreach($custom_fields_data as $field) {
				$out[$field['key']] = $field['title'];
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
			'section_layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'columns_desktop',
			[
				'label' => __('Columns Desktop', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '4x',
				'options' => [
					'2x' => __('2x columns', 'thegem'),
					'3x' => __('3x columns', 'thegem'),
					'4x' => __('4x columns', 'thegem'),
					'5x' => __('5x columns', 'thegem'),
					'6x' => __('6x columns', 'thegem'),
					'100%' => __('100% width', 'thegem'),
				],
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
			'image_ratio',
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
					'image_size' => 'full',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'image_ratio_default',
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
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item:not(.custom-ratio, .double-item) .image-inner:not(.empty)' => 'aspect-ratio: {{SIZE}} !important; height: auto;',
				],
				'description' => __('Leave blank to show the original image ratio', 'thegem'),
				'condition' => [
					'image_size' => 'default',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'disable_preloader', [
				'label' => __('Disable Preloader', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_portfolios',
			[
				'label' => __('Portfolios', 'thegem'),
			]
		);

		if ($this->is_portfolio_post) {
			$query_type_default = 'related';
		} else {
			$query_type_default = 'portfolios';
		}

		$this->add_control(
			'query_type',
			[
				'label' => __('Show', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'portfolios' => __('Portfolio Items', 'thegem'),
					'related' => __('Related', 'thegem'),
				],
				'default' => $query_type_default,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'taxonomy_related',
			[
				'label' => __('Related by', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => thegem_select_portfolio_taxonomies(),
				'default' => ['thegem_portfolios'],
				'frontend_available' => true,
				'condition' => [
					'query_type' => 'related',
				],
			]
		);

		$this->add_control(
			'source',
			[
				'label' => __('Source', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => array_merge(
					thegem_select_portfolio_taxonomies(),
					['posts' => __('Portfolio Items', 'thegem')]
				),
				'default' => ['thegem_portfolios'],
				'frontend_available' => true,
				'condition' => [
					'query_type' => 'portfolios',
				],
			]
		);

		foreach (thegem_select_portfolio_taxonomies() as $tax_name => $tax_label) {

			$this->add_control(
				'content_portfolios_' . $tax_name,
				[
					'label' => sprintf(__('Select %s', 'thegem'), $tax_label),
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'options' => get_thegem_select_taxonomy_terms($tax_name),
					'frontend_available' => true,
					'label_block' => true,
					'condition' => [
						'query_type' => 'portfolios',
						'source' => $tax_name,
					],
				]
			);
		}

		$this->add_control(
			'content_portfolios_posts',
			[
				'label' => __('Select Portfolio Items', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => 'thegem_pf_item',
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'condition' => [
					'query_type' => 'portfolios',
					'source' => 'posts',
				],
				'description' => __('Add portfolio by title.', 'thegem'),
			]
		);

		$this->add_control(
			'order_by',
			[
				'label' => __('Order By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'default' => __('Default', 'thegem'),
					'date' => __('Date', 'thegem'),
					'id' => __('ID', 'thegem'),
					'author' => __('Author', 'thegem'),
					'title' => __('Title', 'thegem'),
					'modified' => __('Last modified date', 'thegem'),
					'comment_count' => __('Number of comments', 'thegem'),
					'rand' => __('Random', 'thegem'),
				],
				'default' => 'default',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __('Sort Order', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'default' => __('Default', 'thegem'),
					'desc' => __('Descending', 'thegem'),
					'asc' => __('Ascending', 'thegem'),
				],
				'default' => 'default',
				'frontend_available' => true,
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
				'frontend_available' => true,
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
			'exclude_portfolios',
			[
				'label' => __('Exclude Portfolio Items', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => 'thegem_pf_item',
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'description' => __('Add portfolio by title.', 'thegem'),
				'condition' => [
					'exclude_type' => 'manual',
				],
			]
		);

		$this->add_control(
			'exclude_portfolio_terms',
			[
				'label' => __('Exclude Portfolio Terms', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_taxonomy_terms_by_query',
				'render' => 'thegem_get_taxonomy_terms_by_id',
				'post_type' => 'thegem_pf_item',
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'description' => __('Add portfolio term by name.', 'thegem'),
				'condition' => [
					'exclude_type' => 'term',
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
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'image_hover_effect_page',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Cyan Breeze', 'thegem'),
					'zooming-blur' => __('Zooming White', 'thegem'),
					'horizontal-sliding' => __('Horizontal Sliding', 'thegem'),
					'vertical-sliding' => __('Vertical Sliding', 'thegem'),
					'zoom-overlay' => __('Zoom & Overlay', 'thegem'),
					'disabled' => __('Disabled', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
				],
			]
		);

		$this->add_control(
			'image_hover_effect_hover_image',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Cyan Breeze', 'thegem'),
					'zooming-blur' => __('Zooming White', 'thegem'),
					'horizontal-sliding' => __('Horizontal Sliding', 'thegem'),
					'vertical-sliding' => __('Vertical Sliding', 'thegem'),
					'gradient' => __('Gradient', 'thegem'),
					'circular' => __('Circular Overlay', 'thegem'),
					'zoom-overlay' => __('Zoom & Overlay', 'thegem'),
					'disabled' => __('Disabled', 'thegem'),
				],
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'hover',
						],
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'image',
						],
					]
				]
			]
		);

		$this->add_control(
			'hover_elements_size',
			[
				'label' => __('Hover Elements Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'small' => __('Small', 'thegem'),
					'big' => __('Big', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'icons_show', [
				'label' => __('Hover Icons', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'title_description_header',
			[
				'label' => __('Title & Description', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$portfolio_fields = [
			'title' => __('Title', 'thegem'),
			'description' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'sets' => __('Additional Meta', 'thegem'), // Old Categories Checkbox
			'likes' => __('Likes', 'thegem'),
		];

		foreach ($portfolio_fields as $ekey => $elem) {

			$name = 'portfolio_show_' . $ekey;
			$conditions = '';
			$default = 'yes';

			if ($ekey == 'date') {

				$this->add_control(
					'meta_header',
					[
						'label' => __('Meta', 'thegem'),
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

			} else if ($ekey == 'likes') {
				$default = '';

				$conditions = [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'page',
						],
					]
				];
			}

			$this->add_control(
				$name, [
					'label' => $elem,
					'default' => $default,
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('Show', 'thegem'),
					'label_off' => __('Hide', 'thegem'),
					'frontend_available' => true,
					'conditions' => $conditions,
				]
			);

			if ($ekey == 'sets') {
				
				$this->add_control(
					'additional_meta_type',
					[
						'label' => __('Select Meta Type', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'default' => 'taxonomies',
						'options' => array_merge(
							[
								'taxonomies' => __('Taxonomies', 'thegem'),
								'details' => __('Project Details', 'thegem'),
								'custom_fields' => __('Custom Fields (TheGem)', 'thegem'),
							],
							thegem_get_acf_plugin_groups()
						),
						'frontend_available' => true,
						'condition' => [
							'portfolio_show_sets' => 'yes',
						]
					]
				);

				$this->add_control(
					'additional_meta_taxonomies',
					[
						'label' => __( 'Select Taxonomy', 'thegem' ),
						'type' => Controls_Manager::SELECT,
						'options' => thegem_select_portfolio_taxonomies(),
						'default' => 'thegem_portfolios',
						'frontend_available' => true,
						'condition' => [
							'portfolio_show_sets' => 'yes',
							'additional_meta_type' => 'taxonomies',
						],
					]
				);

				$options = thegem_select_portfolio_details();
				$default = !empty($options) ? array_keys($options)[0] : '';

				$this->add_control(
					'additional_meta_details',
					[
						'label' => __( 'Select Field', 'thegem' ),
						'type' => Controls_Manager::SELECT,
						'options' => $options,
						'default' => $default,
						'frontend_available' => true,
						'condition' => [
							'portfolio_show_sets' => 'yes',
							'additional_meta_type' => 'details',
						],
						'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/single-pages/portfolio" target="_blank">Theme Options -> Single Pages -> Portfolio Page</a> to manage your custom fields.', 'thegem')
					]
				);

				$options = $this->select_portfolio_custom_fields();
				$default = !empty($options) ? array_keys($options)[0] : '';

				$this->add_control(
					'additional_meta_custom_fields',
					[
						'label' => __( 'Select Field', 'thegem' ),
						'type' => Controls_Manager::SELECT,
						'options' => $options,
						'default' => $default,
						'frontend_available' => true,
						'condition' => [
							'portfolio_show_sets' => 'yes',
							'additional_meta_type' => 'custom_fields',
						],
						'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/single-pages/post" target="_blank">Theme Options -> Single Pages -> Post</a> to manage your custom fields.', 'thegem')
					]
				);

				if (class_exists( 'ACF' ) && !empty(thegem_get_acf_plugin_groups())) {
					foreach (thegem_get_acf_plugin_groups() as $gr => $label) {

						$options = thegem_get_acf_plugin_fields_by_group($gr);
						$default = !empty($options) ? array_keys($options)[0] : '';

						$this->add_control(
							'additional_meta_custom_fields_acf_' . $gr,
							[
								'label' => __('Select Custom Field', 'thegem'),
								'type' => Controls_Manager::SELECT,
								'options' => $options,
								'default' => $default,
								'frontend_available' => true,
								'condition' => [
									'portfolio_show_sets' => 'yes',
									'additional_meta_type' => $gr,
								],
								'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/edit.php?post_type=acf-field-group" target="_blank">ACF -> Field Groups</a> to manage your custom fields.', 'thegem'),
							]
						);
					}
				}

				$this->add_control(
					'additional_meta_click_behavior',
					[
						'label' => __( 'Click Behavior', 'thegem' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'archive_link' => __('Link to Post Archive', 'thegem'),
							'disabled' => __('Disabled', 'thegem'),
						],
						'default' => 'archive_link',
						'frontend_available' => true,
						'condition' => [
							'portfolio_show_sets' => 'yes',
							'additional_meta_type' => 'taxonomies',
							'additional_meta_taxonomies!' => 'thegem_portfolios',
						]
					]
				);

				$this->add_control(
					'additional_meta_click_behavior_meta',
					[
						'type' => Controls_Manager::HIDDEN,
						'default' => 'disabled',
						'frontend_available' => true,
						'conditions' => [
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'portfolio_show_sets',
									'operator' => '=',
									'value' => 'yes',
								],
								[
									'relation' => 'or',
									'terms' => [
										[
											'name' => 'additional_meta_taxonomies',
											'operator' => '=',
											'value' => 'thegem_portfolios',
										],
										[
											'name' => 'additional_meta_type',
											'operator' => '!=',
											'value' => 'taxonomies',
										],
									]
								]
							]
						]
					]
				);
			}
		}

		$this->add_control(
			'details_header',
			[
				'label' => __('Project Details', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'portfolio_show_details', [
				'label' => __( 'Show Project Details', 'thegem' ),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'thegem'),
				'label_off' => __('No', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'details_layout',
			[
				'label' => __('Details Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => [
					'vertical' => __('Vertical', 'thegem'),
					'inline' => __('Inline', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'portfolio_show_details' => 'yes',
					'caption_position' => 'page',
				],
			]
		);

		$this->add_control(
			'details_style',
			[
				'label' => __('Details Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => __('Text', 'thegem'),
					'labels' => __('Labels', 'thegem'),
				],
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'portfolio_show_details',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'details_layout',
									'operator' => '=',
									'value' => 'inline',
								],
								[
									'name' => 'caption_position',
									'operator' => '!=',
									'value' => 'page',
								],
							]
						]
					]
				]
			]
		);

		$this->add_control(
			'details_position',
			[
				'label' => __('Details Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => [
					'top' => __('Top', 'thegem'),
					'bottom' => __('Bottom', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'portfolio_show_details' => 'yes',
					'caption_position' => 'page',
					'details_layout' => 'inline',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'attribute_title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Title', 'thegem'),
				'default' => __('Attribute', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'attribute_type',
			[
				'label' => __('Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'details',
				'options' => array_merge(
					[
						'details' => __('Project Details', 'thegem'),
						'taxonomies' => __('Taxonomies', 'thegem'),
						'custom_fields' => __('Custom Fields (TheGem)', 'thegem'),
					],
					thegem_get_acf_plugin_groups()
				),
			]
		);

		$options = thegem_select_portfolio_details();
		$default = !empty($options) ? array_keys($options)[0] : '';

		$repeater->add_control(
			'attribute_details',
			[
				'label' => __( 'Select Field', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => $default,
				'condition' => [
					'attribute_type' => 'details',
				],
				'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/single-pages/portfolio" target="_blank">Theme Options -> Single Pages -> Portfolio Page</a> to manage your custom fields.', 'thegem')
			]
		);

		$repeater->add_control(
			'attribute_taxonomies',
			[
				'label' => __( 'Select Taxonomy', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => thegem_select_portfolio_taxonomies(),
				'default' => 'category',
				'condition' => [
					'attribute_type' => 'taxonomies',
				],
			]
		);

		$options = $this->select_portfolio_custom_fields();
		$default = !empty($options) ? array_keys($options)[0] : '';

		$repeater->add_control(
			'attribute_custom_fields',
			[
				'label' => __( 'Select Field', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => $default,
				'condition' => [
					'attribute_type' => 'custom_fields',
				],
				'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/single-pages/portfolio" target="_blank">Theme Options -> Single Pages -> Portfolio Page</a> to manage your custom fields.', 'thegem')
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
							'attribute_type' => $gr,
						],
						'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/edit.php?post_type=acf-field-group" target="_blank">ACF -> Field Groups</a> to manage your custom fields.', 'thegem'),
					]
				);
			}
		}

		$repeater->add_control(
			'attribute_meta_type',
			[
				'label' => __('Field Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => __('Text', 'thegem'),
					'number' => __('Number', 'thegem'),
				],
			]
		);

		$repeater->add_control(
			'attribute_price_format',
			[
				'label' => __('Price Format', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'disabled',
				'options' => [
					'disabled' => __('Disabled', 'thegem'),
					'wp_locale' => __('WP Locale', 'thegem'),
					'custom_locale' => __('Custom Locale', 'thegem'),
				],
				'condition' => [
					'attribute_meta_type' => 'number',
				],
			]
		);

		$repeater->add_control(
			'attribute_price_format_locale',
			[
				'label' => __('Custom Locale', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'condition' => [
					'attribute_meta_type' => 'number',
					'attribute_price_format' => 'custom_locale',
				],
				'description' => __('Enter locale, e.g. en_GB. See <a href="https://wpcentral.io/internationalization/" target="_blank">complete locale list</a>.', 'thegem'),
			]
		);

		$repeater->add_control(
			'attribute_price_format_prefix',
			[
				'label' => __('Prefix', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'condition' => [
					'attribute_meta_type' => 'number',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'attribute_price_format_suffix',
			[
				'label' => __('Suffix', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'condition' => [
					'attribute_meta_type' => 'number',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'attribute_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'repeater_details',
			[
				'type' => Controls_Manager::REPEATER,
				'label' => __('Attributes', 'thegem'),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ attribute_title }}}',
				'default' => [
					[
						'attribute_title' => __('Attribute 1', 'thegem'),
					]
				],
				'frontend_available' => true,
				'condition' => [
					'portfolio_show_details' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'button_header',
			[
				'label' => __('Button', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'caption_position' => 'page',
				],
			]
		);

		$this->add_control(
			'show_readmore_button',
			[
				'label' => __( 'Show "Read More" Button', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
				],
			]
		);

		$this->add_control(
			'blog_readmore_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Read More', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
					'show_readmore_button' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'readmore_button_link',
			[
				'label' => __( 'Button Link', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Link to Single Post', 'thegem'),
					'custom' => __('Custom Link', 'thegem'),
				],
				'default' => 'default',
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
					'show_readmore_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_button_custom_link',
			[
				'label' => __( 'Custom Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'options' => false,
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
					'show_readmore_button' => 'yes',
					'readmore_button_link' => 'custom',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'readmore_button_id',
			[
				'label' => __( 'Button ID', 'thegem' ),
				'type' => Controls_Manager::TEXT,
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
					'show_readmore_button' => 'yes',
					'readmore_button_link' => 'custom',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'disable_portfolio_link_header',
			[
				'label' => __('Extras', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'disable_portfolio_link', [
				'label' => __( 'Disable Link to Portfolio Page', 'thegem' ),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'thegem'),
				'label_off' => __('No', 'thegem'),
				'frontend_available' => true,
			]
		);

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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'arrows_navigation_position',
			[
				'label' => __('Arrows Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'outside',
				'options' => [
					'outside' => __('Outside Items', 'thegem'),
					'on' => __('On Items', 'thegem'),
				],
				'condition' => [
					'show_arrows_navigation' => 'yes',
				],
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
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
					'frontend_available' => true,
					'condition' => [
						'social_sharing' => 'yes',
					]
				]
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_animations',
			[
				'label' => __('Animations', 'thegem'),
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
				'frontend_available' => true,
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
				'frontend_available' => true,
				'condition' => [
					'loading_animation' => 'yes',
				],
			]
		);

		$this->add_control(
			'loading_animation_mobile',
			[
				'label' => __('Enable animation on mobile', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'loading_animation' => 'yes',
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
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
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'skeleton_loader',
			[
				'label' => __('Skeleton Preloader on grid loading', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'disable_preloader' => '',
					'columns_desktop!' => '100%',
				],
			]
		);

		$this->add_control(
			'ajax_preloader_type',
			[
				'label' => __('AJAX Preloader Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'minimal' => __('Minimal', 'thegem'),
				],
				'frontend_available' => true,
				'conditions' => [
					'terms' => [
						[
							'name' => 'disable_preloader',
							'value' => '',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'skeleton_loader',
									'value' => '',
								],
								[
									'name' => 'columns_desktop',
									'operator' => '=',
									'value' => '100%',
								]
							]
						]
					]
				]
			]
		);

		$this->add_control(
			'minimal_preloader_color',
			[
				'label' => __('Preloader Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .preloader-spin-new' => 'border-color: {{VALUE}};'
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'disable_preloader',
							'value' => '',
						],
						[
							'name' => 'ajax_preloader_type',
							'value' => 'minimal',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'skeleton_loader',
									'value' => '',
								],
								[
									'name' => 'columns_desktop',
									'operator' => '=',
									'value' => '100%',
								]
							]
						]
					]
				]
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

		/* Likes Style */
		$this->likes_style($control);

		/* Hover Icons (Custom) */
		$this->hover_icons_style($control);

		/* Project Details */
		$this->project_details_style($control);

		/* Readmore  Button Style */
		$this->readmore_button_styles($control);

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
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.extended-carousel-grid .fullwidth-block' => 'padding: 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.extended-carousel-grid.has-shadowed-items .owl-carousel .owl-stage-outer' => 'padding: calc({{SIZE}}{{UNIT}}/2) !important; margin: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .extended-carousel-grid:not(.inited) .portfolio-item,
					{{WRAPPER}} .skeleton-posts .portfolio-item' => 'padding: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .extended-carousel-grid:not(.inited) .owl-stage,
					{{WRAPPER}} .skeleton-posts.portfolio-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2);',
				]
			]
		);

		$control->add_control(
			'image_height',
			[
				'label' => __('Image Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item:not(.double-item) .image-inner' => 'height: {{SIZE}}{{UNIT}} !important; padding-bottom: 0 !important; aspect-ratio: initial !important;',
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item:not(.double-item) .gem-simple-gallery .gem-gallery-item a' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'image_size' => 'default',
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
				'frontend_available' => true,
				'description' => __('Activate for better image quality in case of using in fullwidth section', 'thegem'),
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid.caption-position-page .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .portfolio.portfolio-grid.caption-position-hover .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid.caption-position-image .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.portfolio-grid:not(.shadowed-container) .portfolio-item .image, {{WRAPPER}} .portfolio.portfolio-grid.shadowed-container .portfolio-item .wrap',
			]
		);

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

		$control->start_controls_tabs('image_tabs');
		$control->start_controls_tab('image_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
			'image_opacity_normal',
			[
				'label' => __('Opacity', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image-inner' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image-inner',
			]
		);


		$control->end_controls_tab();
		$control->start_controls_tab('image_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
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
							'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.portfolio-grid.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.portfolio-grid.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.portfolio-grid.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
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
									'operator' => '!=',
									'value' => 'page',
								],
								[
									'name' => 'image_hover_effect_hover_image',
									'operator' => '!=',
									'value' => 'disabled',
								]
							]
						],
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
									'operator' => '!=',
									'value' => 'disabled',
								]
							]
						]
					]
				]
			]
		);

		$control->remove_control('image_hover_overlay_image');

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_hover_css',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item:hover .image-inner',
			]
		);

		$control->add_control(
			'icons_header',
			[
				'label' => __('Icons', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'icons_show' => 'yes',
				],
			]
		);

		$control->add_control(
			'icons_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image .overlay .links a.icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image .overlay .links .overlay-line:after' => 'background: {{VALUE}};'
				],
				'condition' => [
					'icons_show' => 'yes',
				],
			]
		);

		$control->add_control(
			'icons_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image .overlay .links a.icon i' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'icons_show' => 'yes',
					'caption_position' => 'hover',
					'image_hover_effect_hover_image' => 'zooming-blur'
				],
			]
		);


		$control->add_responsive_control(
			'icons_size',
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item:not(.hover-zooming-blur) .image .overlay .links a.icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item:not(.hover-zooming-blur) .image .overlay .links a.icon i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item:not(.hover-zooming-blur) .image .overlay .links a.icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid.hover-zooming-blur .portfolio-item .image .overlay .links a.icon, {{WRAPPER}} .portfolio.portfolio-grid.hover-gradient .portfolio-item .image .overlay .links a.icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid.hover-zooming-blur .portfolio-item .image .overlay .links a.icon i, {{WRAPPER}} .portfolio.portfolio-grid.hover-gradient .portfolio-item .image .overlay .links a.icon i' => 'font-size: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.portfolio-grid.hover-zooming-blur .portfolio-item .image .overlay .links a.icon svg, {{WRAPPER}} .portfolio.portfolio-grid.hover-gradient .portfolio-item .image .overlay .links a.icon svg' => 'width: calc({{SIZE}}{{UNIT}}/2); height: calc({{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'icons_show' => 'yes',
				],
			]
		);

		$control->add_control(
			'icons_customize',
			[
				'label' => __('Want to customize?', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'icons_show' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

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

		$caption_options = [
			'title' => __('Title', 'thegem'),
			'subtitle' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'set' => __('Category', 'thegem'),
		];

		foreach ($caption_options as $ekey => $elem) {

			if ($ekey == 'set') {
				$selector = '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap .overlay .caption .' . $ekey . ',
				{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap .overlay .caption .info .set a';
			} else if ($ekey == 'title') {
				$selector = '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap .overlay .caption .title span';
			} else {
				$selector = '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap .overlay .caption .' . $ekey;
			}

			$control->add_control(
				$ekey . '_header',
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			if ($ekey == 'subtitle') {

				$control->add_control(
					'description_preset',
					[
						'label' => 'Description Size Preset',
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
							'main-menu-item' => __('Main Menu', 'thegem'),
							'text-body' => __('Body', 'thegem'),
							'text-body-tiny' => __('Tiny Body', 'thegem'),
						],
						'default' => 'text-body',
						'frontend_available' => true,
						'condition' => [
							'portfolio_show_description' => 'yes',
						],
					]
				);

				$control->add_control(
					'truncate_description',
					[
						'label' => __('Truncate Description (Lines)', 'thegem'),
						'type' => Controls_Manager::NUMBER,
						'min' => 1,
						'max' => 10,
						'step' => 1,
						'default' => 2,
						'selectors' => [
							'{{WRAPPER}} .portfolio-item .caption .subtitle' => 'max-height: initial !important;',
							'{{WRAPPER}} .portfolio-item .caption .subtitle span' => 'white-space: initial; display: -webkit-box; -webkit-line-clamp: {{VALUE}}; line-clamp: {{VALUE}}; -webkit-box-orient: vertical;',
							'{{WRAPPER}} .portfolio-item .caption .subtitle a, {{WRAPPER}} .portfolio-item .caption .subtitle p' => 'white-space: initial; overflow: initial;',
						],
						'condition' => [
							'portfolio_show_description' => 'yes',
						]
					]
				);

			}

			if ($ekey == 'title') {

				$control->add_control(
					'title_preset',
					[
						'label' => 'Title Size Preset',
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
							'main-menu-item' => __('Main Menu', 'thegem'),
							'text-body' => __('Body', 'thegem'),
							'text-body-tiny' => __('Tiny Body', 'thegem'),
						],
						'default' => 'title-h5',
						'frontend_available' => true,
						'condition' => [
							'portfolio_show_title' => 'yes',
						],
					]
				);

				$control->add_control(
					'title_tag',
					[
						'label' => __('HTML Tag', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'h1' => 'H1',
							'h2' => 'H2',
							'h3' => 'H3',
							'h4' => 'H4',
							'h5' => 'H5',
							'h6' => 'H6',
							'div' => 'div',
							'span' => 'span',
							'p' => 'p',
						],
						'default' => 'div',
						'frontend_available' => true,
						'condition' => [
							'portfolio_show_title' => 'yes',
						],
					]
				);

				$control->add_control(
					'title_transform',
					[
						'label' => 'Title Font Transform',
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => __('Default', 'thegem'),
							'none' => __('None', 'thegem'),
							'lowercase' => __('Lowercase', 'thegem'),
							'uppercase' => __('Uppercase', 'thegem'),
							'capitalize' => __('Capitalize', 'thegem'),
						],
						'default' => '',
						'condition' => [
							'portfolio_show_title' => 'yes',
						],
						'selectors' => [
							'{{WRAPPER}} .portfolio-item .caption .title span' => 'text-transform: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'title_font_weight',
					[
						'label' => __('Title Font weight', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => __('Default', 'thegem'),
							'light' => __('Thin', 'thegem'),
						],
						'default' => '',
						'frontend_available' => true,
						'condition' => [
							'portfolio_show_title' => 'yes',
						],
					]
				);

				$control->add_control(
					'truncate_titles',
					[
						'label' => __('Truncate Title (Lines)', 'thegem'),
						'type' => Controls_Manager::NUMBER,
						'min' => 1,
						'max' => 10,
						'step' => 1,
						'default' => 2,
						'selectors' => [
							'{{WRAPPER}} .portfolio-item .caption .title span' => 'max-height: initial; white-space: initial; display: -webkit-box; -webkit-line-clamp: {{VALUE}}; line-clamp: {{VALUE}}; -webkit-box-orient: vertical;',
						],
						'condition' => [
							'portfolio_show_title' => 'yes',
						]
					]
				);
			}

			if ($ekey == 'date') {


				$control->add_control(
					'truncate_info',
					[
						'label' => __('Truncate Date and Sets (Lines)', 'thegem'),
						'type' => Controls_Manager::NUMBER,
						'min' => 1,
						'max' => 10,
						'step' => 1,
						'selectors' => [
							'{{WRAPPER}} .portfolio-item .caption .info' => 'max-height: initial; white-space: initial; display: -webkit-box; -webkit-line-clamp: {{VALUE}}; line-clamp: {{VALUE}}; -webkit-box-orient: vertical;',
						],
						'condition' => [
							'portfolio_show_description' => 'yes',
						]
					]
				);
			}

			$control->add_group_control(Group_Control_Typography::get_type(),
				[
					'label' => __('Typography', 'thegem'),
					'name' => $ekey . '_typography',
					'selector' => $selector,
					'condition' => [
						'caption_position' => ['hover', 'image'],
					]
				]
			);

			$control->add_control(
				$ekey . '_color',
				[
					'label' => __('Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						$selector => 'color: {{VALUE}};',
					],
					'condition' => [
						'caption_position' => ['hover', 'image'],
					]
				]
			);

			if ($ekey == 'set') {

				$control->add_control(
					'category_in_text',
					[
						'label' => __('"in" Text', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'input_type' => 'text',
						'default' => __('in', 'thegem'),
						'frontend_available' => true,
						'condition' => [
							'caption_position' => ['hover'],
						],
						'dynamic' => [
							'active' => true,
						],
					]
				);

				$control->add_control(
					'category_in_color',
					[
						'label' => __('"in" Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .caption .info .set .in_text' => 'color: {{VALUE}};',
						],
						'condition' => [
							'caption_position' => ['hover'],
						]
					]
				);
			}

			$control->start_controls_tabs($ekey . '_tabs', [
				'condition' => [
					'caption_position' => ['page'],
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
					if ($ekey == 'set') {
						$selector = '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item' . $state . ' .caption .info .set a';
					} else if ($ekey == 'title') {
						$selector = '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item' . $state . ' .caption .title span';
					} else {
						$selector = '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item' . $state . ' .caption .' . $ekey;
					}

					$control->start_controls_tab($ekey . '_tab_' . $stkey, [
						'label' => $stelem,
					]);

					$control->add_group_control(Group_Control_Typography::get_type(),
						[
							'label' => __('Typography', 'thegem'),
							'name' => $ekey . '_typography_' . $stkey,
							'selector' => $selector,
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
						]
					);

					$control->end_controls_tab();

				}
			}

			$control->end_controls_tabs();
		}

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
				],
				'frontend_available' => true,
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap > .caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid.title-on-page .portfolio-item .wrap' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius:{{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap > .caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap > .caption' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap > .caption .caption-separator' => 'margin-{{VALUE}}: 0',
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-likes' => 'text-align: -webkit-{{VALUE}}',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'caption_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap > .caption',
			]
		);

		$control->start_controls_tabs('caption_container_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				if ($stkey == 'active') {
					continue;
				}
				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('caption_container_tab_' . $stkey, ['label' => $stelem]);

				$control->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name' => 'caption_container_background_' . $stkey,
						'label' => __('Background Type', 'thegem'),
						'types' => ['classic', 'gradient'],
						'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item' . $state . ' .wrap > .caption',
					]
				);
				$control->remove_control('image_hover_overlay_image');

				$control->remove_control('caption_container_background_' . $stkey . '_image');

				$control->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'caption_container_border_' . $stkey,
						'label' => __('Border', 'thegem'),
						'fields_options' => [
							'color' => [
								'default' => '#dfe5e8',
							],
						],
						'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item' . $state . ' .wrap > .caption',
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'spacing_title_header',
			[
				'label' => 'Title',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'spacing_title',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap > .caption .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'spacing_description_header',
			[
				'label' => 'Description',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'spacing_description',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap > .caption .subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Likes Style
	 * @access protected
	 */
	protected function likes_style($control) {

		$control->start_controls_section(
			'likes_style',
			[
				'label' => __('Likes Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'caption_position' => 'page',
				],
			]
		);

		$control->add_control(
			'likes_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'frontend_available' => true,
			]
		);

		$control->start_controls_tabs('likes_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}

				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('likes_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'likes_icon_color_' . $stkey,
					[
						'label' => __('Icon Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-likes .zilla-likes' . $state . ' i' => 'color: {{VALUE}}; transition: all 0.3s;',
						]
					]
				);

				$control->add_control(
					'likes_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-likes .zilla-likes' . $state => 'color: {{VALUE}};',
						]
					]
				);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'likes_typography_' . $stkey,
						'selector' => '{{WRAPPER}} .portfolio-likes .zilla-likes' . $state,
					]
				);


				$control->end_controls_tab();

			}
		}
		$control->end_controls_tabs();

		$control->end_controls_section();
	}


	/**
	 * Hover Icons (Custom)
	 * @access protected
	 */
	protected function hover_icons_style($control) {

		$control->start_controls_section(
			'hover_icons_style',
			[
				'label' => __('Hover Icons (Custom)', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icons_customize' => 'yes',
				]
			]
		);

		$control->add_control(
			'important_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Hover icons depend on types of portfolio item (image, link, video etc.). If some type is not selected, corresponding icon will not appear. ', 'thegem'),
				'content_classes' => 'elementor-control-field-description',
			]
		);

		$icons_list = [
			'share' => __('Sharing Button', 'thegem'),
			'self-link' => __('Portfolio Page', 'thegem'),
			'full-image' => __('Fancybox Image', 'thegem'),
			'inner-link' => __('Internal Link', 'thegem'),
			'outer-link' => __('External Link', 'thegem'),
			'video' => __('Video', 'thegem')
		];

		foreach ($icons_list as $ekey => $elem) {

			$condition = [];

			$add_selector = '';
			if ($ekey == 'share') {
				$condition = [
					'social_sharing' => 'yes'
				];
				$add_selector = ', {{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image .overlay .links .socials-item-icon';
			}

			$control->add_control(
				'hover_icon_header_' . $ekey,
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'condition' => $condition
				]
			);

			$control->add_control(
				'hover_icon_' . $ekey,
				[
					'label' => __('Icon', 'thegem'),
					'type' => Controls_Manager::ICONS,
					'frontend_available' => true,
					'condition' => $condition
				]
			);

			$control->add_control(
				'hover_icon_color_' . $ekey,
				[
					'label' => __('Icon Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image .overlay .links a.' . $ekey . ' i' . $add_selector => 'color: {{VALUE}};',
						'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image .overlay .links a.' . $ekey . ' svg' . $add_selector => 'fill: {{VALUE}};',
					],
					'condition' => $condition
				]
			);

			$control->add_control(
				'hover_icon_rotate_' . $ekey,
				[
					'label' => __('Rotate', 'thegem'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 360,
							'step' => 15,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image .overlay .links a.' . $ekey . ' i' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
						'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .image .overlay .links a.' . $ekey . ' svg' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					],
					'condition' => $condition
				]
			);

			$control->end_controls_tab();

		}

		$control->end_controls_section();
	}


	/**
	 * Project Details
	 * @access protected
	 */
	protected function project_details_style($control) {

		$control->start_controls_section(
			'project_details_style',
			[
				'label' => __('Project Details', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'portfolio_show_details' => 'yes',
				],
			]
		);

		$control->add_control(
			'details_alignment_vertical',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'justify',
				'options' => [
					'justify' => __('Justify', 'thegem'),
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_alignment_inline',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'center' => __('Center', 'thegem'),
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
				],
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'details_layout',
							'operator' => '=',
							'value' => 'inline',
						],
						[
							'name' => 'caption_position',
							'operator' => '!=',
							'value' => 'page',
						],
					]
				]
			]
		);

		$control->add_control(
			'details_divider_header',
			[
				'label' => __('Divider', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_divider_show', [
				'label' => __('Divider', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'thegem'),
				'label_off' => __('No', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_divider_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details.layout-vertical.with-divider .details-item' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'details_divider_show' => 'yes',
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_label_header',
			[
				'label' => __('Field Label', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_label_preset',
			[
				'label' => 'Size Preset',
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
					'main-menu-item' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'text-body-tiny',
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_label_transform',
			[
				'label' => 'Font Transform',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details .details-item .label' => 'text-transform: {{VALUE}};',
				],
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_label_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'details_label_typography',
				'selector' => '{{WRAPPER}} .portfolio-item .details .details-item .label',
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_label_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details .details-item .label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_responsive_control(
			'details_icon_size',
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
					'{{WRAPPER}} .portfolio-item .details .details-item .label i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio-item .details .details-item .label svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_colon_show', [
				'label' => __('Colon', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'thegem'),
				'label_off' => __('No', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'page',
					'details_layout' => 'vertical',
				],
			]
		);

		$control->add_control(
			'details_separator',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Separator', 'thegem'),
				'default' => '',
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'details_layout',
							'operator' => '=',
							'value' => 'inline',
						],
						[
							'name' => 'caption_position',
							'operator' => '!=',
							'value' => 'page',
						],
					]
				]
			]
		);

		$control->add_control(
			'details_value_header',
			[
				'label' => __('Field Value', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'details_value_preset',
			[
				'label' => 'Size Preset',
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
					'main-menu-item' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'text-body-tiny',
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'details_value_transform',
			[
				'label' => 'Font Transform',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details .details-item .value' => 'text-transform: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'details_value_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'frontend_available' => true,
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'details_value_typography',
				'selector' => '{{WRAPPER}} .portfolio-item .details .details-item .value',
			]
		);

		$control->add_control(
			'details_value_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details .details-item .value' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'details_value_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details .details-item' => 'border-color: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'details_style',
							'operator' => '=',
							'value' => 'labels',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'details_layout',
									'operator' => '=',
									'value' => 'inline',
								],
								[
									'name' => 'caption_position',
									'operator' => '!=',
									'value' => 'page',
								],
							]
						]
					]
				]


			]
		);

		$control->add_responsive_control(
			'details_icon_size_value',
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
					'{{WRAPPER}} .portfolio-item .details .details-item .value i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: 1.2;',
					'{{WRAPPER}} .portfolio-item .details .details-item .value svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'details_layout',
							'operator' => '=',
							'value' => 'inline',
						],
						[
							'name' => 'caption_position',
							'operator' => '!=',
							'value' => 'page',
						],
					]
				]
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Readmore  Button Style
	 * @access protected
	 */
	protected function readmore_button_styles( $control ) {

		$control->start_controls_section(
			'readmore_button_section',
			[
				'label' => __( '"Read More" Button Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'caption_position' => 'page',
					'show_readmore_button' => 'yes',
				]
			]
		);

		$control->add_control(
			'readmore_button_type',
			[
				'label' => __('Button Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'outline',
				'options' => [
					'flat' => __('Flat', 'thegem'),
					'outline' => __('Outline', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'readmore_button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem' ),
					'tiny' => __('Tiny', 'thegem' ),
					'small' => __('Small', 'thegem' ),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem' ),
					'giant' => __('Giant', 'thegem' ),
				],
				'frontend_available' => true,
			]
		);

		$control->add_responsive_control(
			'readmore_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'readmore_button_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'none'   => __('None', 'thegem'),
					'solid'  => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'readmore_button_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'readmore_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-inner-wrapper-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'readmore_button_tabs' );
		$control->start_controls_tab(
			'readmore_button_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);

		$control->add_control(
			'readmore_button_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'readmore_button_typography',
				'selector' => '{{WRAPPER}} .read-more-button .gem-button-container .gem-button',
			]
		);

		$control->add_responsive_control(
			'readmore_button_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'readmore_button_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'readmore_button_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .read-more-button .gem-button-container .gem-button',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'readmore_button_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);

		$control->add_control(
			'readmore_button_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button:hover span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button:hover .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button:hover .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'readmore_button_typography_hover',
				'selector' => '{{WRAPPER}} .read-more-button .gem-button-container .gem-button:hover span',
			]
		);

		$control->add_responsive_control(
			'readmore_button_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'readmore_button_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .read-more-button .gem-button-container .gem-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'readmore_button_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .read-more-button .gem-button-container .gem-button:hover',
			]
		);

		$control->end_controls_tab();
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
					'{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-prev div i, 
					{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-next div i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-prev div.position-on, 
					{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-next div.position-on' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-prev div.position-on, 
					{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-next div.position-on' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-prev div.position-on, 
					{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-next div.position-on',
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
							'{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-prev' . $state . ' div.position-on, 
							{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-next' . $state . ' div.position-on' => 'background-color: {{VALUE}};',
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
							'{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-prev' . $state . ' div.position-on, 
							{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-next' . $state . ' div.position-on' => 'color: {{VALUE}};',
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
							'{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-prev' . $state . ' div, 
							{{WRAPPER}} .extended-carousel-grid .extended-carousel-item .owl-nav .owl-next' . $state . ' div' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .extended-carousel-grid .owl-dots .owl-dot span' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .extended-carousel-grid .owl-dots' => 'margin-top: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .extended-carousel-grid .owl-dots .owl-dot' => 'margin: 0 calc({{SIZE}}{{UNIT}}/2)',
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
				'selector' => '{{WRAPPER}} .extended-carousel-grid .owl-dots .owl-dot span',
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
							'{{WRAPPER}} .extended-carousel-grid .owl-dots .owl-dot' . $state . ' span' => 'background-color: {{VALUE}};',
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
							'{{WRAPPER}} .extended-carousel-grid .owl-dots .owl-dot' . $state . ' span' => 'border-color: {{VALUE}};',
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
		$widget_uid = $this->get_id();
		$single_post_id = thegem_templates_init_portfolio() ? thegem_templates_init_portfolio()->ID : get_the_ID();

		if ($settings['exclude_type'] == 'current') {
			$settings['exclude_portfolios'] = [$single_post_id];
		} else if ($settings['exclude_type'] == 'term' && !empty($settings['exclude_portfolio_terms'])) {
			$settings['exclude_portfolios'] = thegem_get_posts_query_section_exclude_ids($settings['exclude_portfolio_terms'], 'thegem_pf_item');
		}

		$taxonomy_filter = $portfolios_posts = [];

		if ($settings['query_type'] == 'related') {

			$taxonomies = $settings['taxonomy_related'];
			if (!empty($taxonomies)) {
				foreach ($taxonomies as $tax) {
					$taxonomy_filter[$tax] = [];
					$tax_terms = get_the_terms($single_post_id, $tax);
					if (!empty($tax_terms) && !is_wp_error($tax_terms)) {
						foreach ($tax_terms as $term) {
							$taxonomy_filter[$tax][] = $term->slug;
						}
					}
				}
			}
			$settings['related_tax_filter'] = $taxonomy_filter;

			if ($settings['exclude_portfolios']) {
				$settings['exclude_portfolios'][] = $single_post_id;
			} else {
				$settings['exclude_portfolios'] = [$single_post_id];
			}
		} else {
			foreach ($settings['source'] as $source) {
				if ($source == 'posts') {
					$portfolios_posts = $settings['content_portfolios_posts'];
				} else {
					$tax_terms = $settings['content_portfolios_' . $source];
					if (!empty($tax_terms)) {
						$taxonomy_filter[$source] = $tax_terms;
					}
				}
			}
		}

		$grid_uid = 'grid_' . $widget_uid;
		$grid_uid_url = $grid_uid.'-';

		if (isset($settings['order_by']) && $settings['order_by'] != 'default') {
			$settings['orderby'] = $settings['order_by'];
		} else {
			$settings['orderby'] = 'menu_order ID';
		}

		if (!isset($settings['order']) || $settings['order'] == 'default') {
			$settings['order'] = 'asc';
		}

		if (!isset($settings['category_in_text'])) {
			$settings['categories_in_text'] = __('in', 'thegem');
		}

		if ($settings['caption_position'] == 'page') {
			$settings['hover_effect'] = $settings['image_hover_effect_page'];
		} else {
			$settings['hover_effect'] = $settings['image_hover_effect_hover_image'];
		}

		$details_list = thegem_select_portfolio_details();
		$custom_fields_list = $this->select_portfolio_custom_fields();
		$meta_list = array_merge($details_list, $custom_fields_list);
		if (class_exists( 'ACF' )){
			foreach (thegem_get_acf_plugin_groups() as $gr=>$value){
				$meta_list = array_merge($meta_list, thegem_get_acf_plugin_fields_by_group($gr));
			}
		}

		if (!empty($settings['image_ratio_default']['size'])) {
			$settings['image_aspect_ratio'] = 'custom';
			$settings['image_ratio_custom'] = $settings['image_ratio_default']['size'];
		}

		$taxonomy_filter_current = $taxonomy_filter;
		$categories_filter = [];
		if (isset($_GET[$grid_uid_url . 'category'])) {
			$categories_filter = explode(",", $_GET[$grid_uid_url . 'category']);
			$taxonomy_filter_current['thegem_portfolios'] = $categories_filter;
		}

		if ($settings['loading_animation'] === 'yes') {
			wp_enqueue_style('thegem-animations');
			wp_enqueue_script('thegem-items-animations');
			wp_enqueue_script('thegem-scroll-monitor');
		}

		wp_enqueue_style('thegem-hovers-' . $settings['hover_effect']);

		if ($settings['show_readmore_button'] == 'yes') {
			wp_enqueue_style('thegem-button');
		}

		$settings['image_shadow_box_shadow_type'] = empty($params['image_shadow_box_shadow_type']) ? 'no' : $params['image_shadow_box_shadow_type'];

		$page = 1;

		if (!empty($_GET[$grid_uid_url . 'page'])) {
			$page = $_GET[$grid_uid_url . 'page'];
		}

		$items_per_page = $settings['items_per_page'] ? intval($settings['items_per_page']) : 8;

		if (!empty($_GET[$grid_uid_url . 'orderby'])) {
			$orderby = $_GET[$grid_uid_url . 'orderby'];
		} else {
			$orderby = $settings['orderby'];
		}

		if (!empty($_GET[$grid_uid_url . 'order'])) {
			$order = $_GET[$grid_uid_url . 'order'];
		} else {
			$order = $settings['order'];
		}

		$portfolios_filters_tax_url = $portfolios_filters_meta_url = $meta_filter_current = [];
		foreach($_GET as $key => $value) {
			if (strpos($key, $grid_uid_url . 'filter_tax_') === 0) {
				$attr = str_replace($grid_uid_url . 'filter_tax_', '', $key);
				$portfolios_filters_tax_url['tax_' . $attr] = $taxonomy_filter_current[$attr] = explode(",", $value);
			} else if (strpos($key, $grid_uid_url . 'filter_meta_') === 0) {
				$attr = str_replace($grid_uid_url . 'filter_meta_', '', $key);
				$portfolios_filters_meta_url['meta_' . $attr] = $meta_filter_current[$attr] = explode(",", $value);
			}
		}
		$attributes_filter = array_merge($portfolios_filters_tax_url, $portfolios_filters_meta_url);
		if (empty($attributes_filter)) { $attributes_filter = null; }

		$search_current = null;
		if (!empty($_GET[$grid_uid_url . 's'])) {
			$search_current = $_GET[$grid_uid_url . 's'];
		}

		if (($settings['query_type'] == 'portfolios' && empty($settings['source'])) ||
			($settings['query_type'] == 'related' && empty($settings['taxonomy_related']))) { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select portfolios in "Portfolios" section', 'thegem') ?>
			</div>
			<?php
			return;
		}

		$portfolio_loop = thegem_get_portfolio_posts($portfolios_posts, $taxonomy_filter_current, $meta_filter_current, $search_current, 'content', $page, $items_per_page, $orderby, $order, intval($settings['offset']), $settings['exclude_portfolios']);

		$portfolio_title = '';

		if ($portfolio_loop->have_posts() || !empty($categories_filter) || !empty($attributes_filter) || !empty($search_current)) {
			$settings['thegem_elementor_preset'] = 'alternative';
			$settings['layout'] = 'justified';
			$settings['ignore_highlights'] = 'yes';

			$item_classes = get_thegem_portfolio_render_item_classes($settings);
			$item_classes[] = 'owl-item';
			$thegem_sizes = get_thegem_portfolio_render_item_image_sizes($settings);

			if ($settings['disable_preloader'] == '') {
				if ($settings['columns_desktop'] == '100%' || !$settings['skeleton_loader'] == 'yes') {
					$spin_class = 'preloader-spin';
					if ($settings['ajax_preloader_type'] == 'minimal') {
						$spin_class = 'preloader-spin-new';
					}
					echo apply_filters('thegem_portfolio_preloader_html', '<div class="preloader save-space"><div class="' . $spin_class . '"></div></div>');
				} else { ?>
					<div class="preloader skeleton-carousel">
						<div class="skeleton">
							<div class="skeleton-posts row portfolio-row">
								<?php for ($x = 0; $x < $portfolio_loop->post_count; $x++) {
									echo thegem_portfolio_grid_render_item($settings, $item_classes);
								} ?>
							</div>
						</div>
					</div>
				<?php }
			} ?>
			<div class="portfolio-preloader-wrapper<?php echo !empty($settings['sidebar_position']) ? ' panel-sidebar-position-' . $settings['sidebar_position'] : ''; ?>">
				<?php if ($portfolio_title) { ?>
					<h3 class="title portfolio-title"><?php echo $portfolio_title; ?></h3>
				<?php } ?>

				<?php
				if ($settings['caption_position'] == 'hover') {
					$title_on = 'hover';
				} else {
					$title_on = 'page';
				}

				$gaps_size = isset($settings['image_gaps']['size']) && $settings['image_gaps']['size'] != '' ? $settings['image_gaps']['size'] : 0;
				$gaps_size_tablet = isset($settings['image_gaps_tablet']['size']) && $settings['image_gaps_tablet']['size'] != '' ? $settings['image_gaps_tablet']['size'] : $gaps_size;
				$gaps_size_mobile = isset($settings['image_gaps_mobile']['size']) && $settings['image_gaps_mobile']['size'] != '' ? $settings['image_gaps_mobile']['size'] : $gaps_size_tablet;

				$this->add_render_attribute(
					'portfolio-wrap',
					[
						'class' => [
							'portfolio portfolio-grid extended-portfolio-grid extended-portfolio-carousel extended-carousel-grid no-padding disable-isotope portfolio-style-justified',
							'background-style-' . $settings['caption_container_preset'],
							'hover-' . $settings['hover_effect'],
							'title-on-' . $title_on,
							'caption-position-' . $settings['caption_position'],
							'hover-elements-size-' . $settings['hover_elements_size'],
							'version-alternative',
							'arrows-position-' . $settings['arrows_navigation_position'],
							($settings['loading_animation'] == 'yes' ? 'loading-animation' : ''),
							($settings['loading_animation'] == 'yes' && $settings['animation_effect'] ? 'item-animation-' . $settings['animation_effect'] : ''),
							($settings['loading_animation'] == 'yes' && $settings['loading_animation_mobile'] == 'yes' ? 'enable-animation-mobile' : ''),
							($gaps_size == 0 ? 'no-gaps' : ''),
							($settings['image_shadow_box_shadow_type'] == 'yes' ? 'has-shadowed-items' : ''),
							($settings['shadowed_container'] == 'yes' ? 'shadowed-container' : ''),
							($settings['columns_desktop'] == '100%' ? 'fullwidth-columns fullwidth-columns-' . $settings['columns_100'] : ''),
							($settings['columns_desktop'] == '100%' && $gaps_size < 24 ? 'prevent-arrows-outside' : ''),
							($settings['caption_position'] == 'image' && $settings['hover_effect'] == 'gradient' ? 'hover-gradient-title' : ''),
							($settings['caption_position'] == 'image' && $settings['hover_effect'] == 'circular' ? 'hover-circular-title' : ''),
							($settings['caption_position'] == 'hover' || $settings['caption_position'] == 'image' ? 'hover-title' : ''),
							($settings['columns_desktop'] != '100%' ? 'columns-' . str_replace("x", "", $settings['columns_desktop']) : ''),
							'columns-tablet-' . str_replace("x", "", $settings['columns_tablet']),
							'columns-mobile-' . str_replace("x", "", $settings['columns_mobile']),
							($settings['arrows_navigation_visibility'] == 'hover' ? 'arrows-hover' : ''),
							(($settings['image_size'] == 'full' && empty($settings['image_ratio']['size']) || !in_array($settings['image_size'], ['full', 'default'])) ? 'full-image' : ''),
							($settings['ajax_preloader_type'] == 'minimal' ? 'minimal-preloader' : ''),
						],
						'data-portfolio-uid' => esc_attr($grid_uid),
						'data-columns-mobile' => esc_attr(str_replace("x", "", $settings['columns_mobile'])),
						'data-columns-tablet' => esc_attr(str_replace("x", "", $settings['columns_tablet'])),
						'data-columns-desktop' => $settings['columns_desktop'] != '100%' ? esc_attr(str_replace("x", "", $settings['columns_desktop'])) : esc_attr($settings['columns_100']),
						'data-margin-mobile' => esc_attr($gaps_size_mobile),
						'data-margin-tablet' => esc_attr($gaps_size_tablet),
						'data-margin-desktop' => esc_attr($gaps_size),
						'data-hover' => esc_attr($settings['hover_effect']),
						'data-dots' => $settings['show_dots_navigation'] == 'yes' ? '1' : '0',
						'data-arrows' => $settings['show_arrows_navigation'] == 'yes' ? '1' : '0',
						'data-loop' => $settings['slider_loop'] == 'yes' ? '1' : '0',
						'data-sliding-animation' => $settings['sliding_animation'],
						'data-autoscroll-speed' => $settings['autoscroll'] == 'yes' ? $settings['autoscroll_speed']['size'] : '0',
					]
				); ?>

				<div <?php echo $this->get_render_attribute_string('portfolio-wrap'); ?>>
					<?php
					$has_meta_filtering = false;
					$post_type = 'thegem_pf_item'; ?>

					<div class="portfolio-row-outer <?php if ($settings['columns_desktop'] == '100%') { ?>fullwidth-block no-paddings<?php } ?>">
						<div class="portfolio-row clearfix">
							<div class="portfolio-set">
								<div class="extended-carousel-wrap">
									<div class="extended-carousel-item owl-carousel owl-theme owl-loaded">
										<div class="owl-stage-outer">
											<div class="owl-stage">
												<?php
												if ($portfolio_loop->have_posts()) {
													while ($portfolio_loop->have_posts()) {
														$portfolio_loop->the_post();
														echo thegem_portfolio_grid_render_item($settings, $item_classes, $thegem_sizes, get_the_ID());
													}
												} else { ?>
													<div class="portfolio-item not-found">
														<div class="found-wrap">
															<div class="image-inner empty"></div>
															<div class="msg">
																<?php echo $settings['not_found_text']; ?>
															</div>
														</div>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>

							</div><!-- .portflio-set -->
						</div><!-- .row-->
						<?php if ($settings['show_arrows_navigation'] == 'yes'): ?>
							<div class="slider-prev-icon position-<?php echo $settings['arrows_navigation_position']; ?>">
								<?php if ($settings['arrows_navigation_left_icon']['value']) {
									Icons_Manager::render_icon($settings['arrows_navigation_left_icon'], ['aria-hidden' => 'true']);
								} else { ?>
									<i class="default"></i>
								<?php } ?>
							</div>
							<div class="slider-next-icon position-<?php echo $settings['arrows_navigation_position']; ?>">
								<?php if ($settings['arrows_navigation_right_icon']['value']) {
									Icons_Manager::render_icon($settings['arrows_navigation_right_icon'], ['aria-hidden' => 'true']);
								} else { ?>
									<i class="default"></i>
								<?php } ?>
							</div>
						<?php endif; ?>

					</div><!-- .full-width -->
				</div><!-- .portfolio-->
			</div><!-- .portfolio-preloader-wrapper-->
		<?php }

		thegem_templates_close_portfolio($this->get_name(), $this->get_title(), $portfolio_loop->have_posts());

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()) { ?>
			<script type="text/javascript">
				(function ($) {

					setTimeout(function () {
						if (!$('.elementor-element-<?php echo $widget_uid; ?> .extended-portfolio-carousel').length) {
							return;
						}
						$('.elementor-element-<?php echo $widget_uid; ?> .extended-portfolio-carousel').initPortfolioCarousels();
					}, 1000);

					elementor.channels.editor.on('change', function (view) {
						var changed = view.elementSettingsModel.changed;

						if (changed.image_gaps !== undefined || changed.caption_container_padding !== undefined ||
							changed.spacing_title !== undefined || changed.spacing_description !== undefined) {
							setTimeout(function () {
								$('.elementor-element-<?php echo $widget_uid; ?> .extended-carousel-item').trigger('refresh.owl.carousel');
							}, 500);
						}
					});

				})(jQuery);

			</script>
		<?php }
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_PortfolioCarousel());
