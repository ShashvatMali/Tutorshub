<?php

namespace TheGem_Elementor\Widgets\Portfolio;

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
 * Elementor widget for Portfolio Grid.
 */
#[\AllowDynamicProperties]
class TheGem_Portfolio extends Widget_Base {

	public function __construct($data = [], $args = null) {

		$template_type = isset($GLOBALS['thegem_template_type']) ? $GLOBALS['thegem_template_type'] : thegem_get_template_type(get_the_ID());
		$this->is_portfolio_post = $template_type === 'portfolio';

		if (isset($data['settings'])) {

			if (!empty($data['settings']['columns']) && (empty($data['settings']['layout']) || $data['settings']['layout'] != 'creative')) {
				$data['settings']['columns_desktop'] = $data['settings']['columns'];
			}
			unset($data['settings']['columns']);

			if (!empty($data['settings']['source'])) {
				if (in_array('categories', $data['settings']['source'])) {
					$data['settings']['source'] = array_map(function ($value) {
						return $value == 'categories' ? 'thegem_portfolios' : $value;
					}, $data['settings']['source']);
				}
			}

			if (!empty($data['settings']['content_portfolios_cat']) && !in_array('0', $data['settings']['content_portfolios_cat'])) {
				$data['settings']['content_portfolios_thegem_portfolios'] = $data['settings']['content_portfolios_cat'];
			}
			unset($data['settings']['content_portfolios_cat']);

			if (!empty($data['settings']['content_portfolios_thegem_portfolios']) && in_array('0', $data['settings']['content_portfolios_thegem_portfolios'])) {
				unset($data['settings']['content_portfolios_thegem_portfolios']['0']);
			}

			if ($this->is_portfolio_post) {
				if (isset($data['settings']['source_type']) && $data['settings']['source_type'] == 'custom') {
					$data['settings']['query_type'] = 'portfolios';
					unset($data['settings']['source_type']);
				}
				if (!isset($data['settings']['query_type'])) {
					$data['settings']['query_type'] = 'related';
				}
			}

			if (!isset($data['settings']['portfolio_show_filter'])) {
				if ($this->is_portfolio_post) {
					$data['settings']['portfolio_show_filter'] = '';
				} else {
					$data['settings']['portfolio_show_filter'] = 'yes';
				}
			}

			if (!isset($data['settings']['portfolio_show_sorting'])) {
				if ($this->is_portfolio_post) {
					$data['settings']['portfolio_show_sorting'] = '';
				} else {
					$data['settings']['portfolio_show_sorting'] = 'yes';
				}
			}

			if (!isset($data['settings']['ignore_highlights'])) {
				if ($this->is_portfolio_post) {
					$data['settings']['ignore_highlights'] = 'yes';
				} else {
					$data['settings']['ignore_highlights'] = '';
				}
			}

			$selected_hidden = ['filter_buttons_hidden_selected_typography',
				'filter_buttons_hidden_selected_border_radius',
				'filter_buttons_hidden_selected_border_type',
				'filter_buttons_hidden_selected_text_padding',
				'filter_buttons_hidden_selected_space_between',
				'filter_buttons_hidden_selected_text_color_normal',
				'filter_buttons_hidden_selected_text_color_hover',
				'filter_buttons_hidden_selected_background_color_normal',
				'filter_buttons_hidden_selected_background_color_hover'];

			foreach ($selected_hidden as $param) {
				if (isset($data['settings'][$param])) {
					$data['settings'][str_replace('hidden', 'standard', $param)] = $data['settings'][$param];
					unset($data['settings'][$param]);
				}
			}
		}

		parent::__construct($data, $args);

		$localize = array(
			'action' => 'portfolio_grid_load_more',
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('portfolio_ajax-nonce')
		);
		wp_localize_script('thegem-portfolio-grid-extended', 'thegem_portfolio_ajax', $localize);

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
			'2' => [
				'2a' => [
					'count' => 5,
					0 => 'vertical',
				],
				'2b' => [
					'count' => 5,
					3 => 'vertical',
				],
				'2c' => [
					'count' => 4,
					0 => 'vertical',
					2 => 'vertical',
				],
				'2d' => [
					'count' => 4,
					0 => 'horizontal',
					1 => 'vertical',
				],
				'2e' => [
					'count' => 5,
					0 => 'horizontal',
				],
				'2f' => [
					'count' => 4,
					0 => 'horizontal',
					1 => 'horizontal',
				],
				'2g' => [
					'count' => 5,
					2 => 'horizontal',
				],
				'2h' => [
					'count' => 4,
					0 => 'horizontal',
					3 => 'horizontal',
				],
			]
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
		return 'thegem-portfolio';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Portfolio Grid', 'thegem');
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
				'thegem-portfolio-filters-list',
				'thegem-portfolio'];
		}
		return ['thegem-portfolio'];
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
				'thegem-isotope-metro',
				'thegem-isotope-masonry-custom',
				'thegem-portfolio-grid-extended'];
		}
		return ['thegem-portfolio-grid-extended'];
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
		$out = array(
			'default' => __('Classic Style', 'thegem'),
			'alternative' => __('Alternative Style', 'thegem'),
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
			'portfolio_uid',
			[
				'label' => __('Grid ID', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => $this->get_id(),
				'frontend_available' => true,
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

		$this->add_control(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'justified',
				'options' => [
					'justified' => __('Justified Grid', 'thegem'),
					'masonry' => __('Masonry Grid', 'thegem'),
					'metro' => __('Metro Style', 'thegem'),
					'creative' => __('Creative Grid', 'thegem'),
				],
				'frontend_available' => true,
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
				'default' => '2x',
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
				'default' => '1x',
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
					'frontend_available' => true,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'layout',
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
									'name' => 'layout',
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
					'layout' => 'creative',
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
					'layout' => 'creative',
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
					'thumbnail' => __('WordPress Thumbnail', 'thegem'),
					'medium' => __('WordPress Medium', 'thegem'),
					'medium_large' => __('WordPress Medium Large', 'thegem'),
					'large' => __('WordPress Large', 'thegem'),
					'1536x1536' => __('1536x1536', 'thegem'),
					'2048x2048' => __('2048x2048', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'layout' => 'justified',
				]
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
					'layout' => 'justified',
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
					'layout' => 'justified',
					'image_size' => 'default',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'disable_preloader', [
				'label' => __('Disable Grid Preloader', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout' => 'justified',
					'columns_desktop!' => '100%',
				]
			]
		);

		$this->add_control(
			'hide_filters_sorting', [
				'label' => __('Hide Filter & Sorting', 'thegem'),
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
			'image_hover_effect_hover',
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
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'thegem_elementor_preset',
									'operator' => '=',
									'value' => 'alternative',
								],
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'image',
								],
							]
						]
					]
				]
			]
		);

		$this->add_control(
			'image_hover_effect_image',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'gradient',
				'options' => [
					'gradient' => __('Gradient', 'thegem'),
					'circular' => __('Circular Overlay', 'thegem'),
					'disabled' => __('Disabled', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'image',
					'thegem_elementor_preset!' => 'alternative',
				],
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
				$conditions = [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'page',
						],
						[
							'name' => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'alternative',
						]
					]
				];
			}

			$this->add_control(
				$name, [
					'label' => $elem,
					'default' => 'yes',
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
							'filtering' => __('Filtering', 'thegem'),
							'archive_link' => __('Link to Post Archive', 'thegem'),
							'disabled' => __('Disabled', 'thegem'),
						],
						'default' => 'filtering',
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
						'label' => __( 'Click Behavior', 'thegem' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'filtering' => __('Filtering', 'thegem'),
							'disabled' => __('Disabled', 'thegem'),
						],
						'default' => 'filtering',
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
			'section_filters',
			[
				'label' => __('Filters & Sorting', 'thegem'),
			]
		);

		$this->add_control(
			'filters_header',
			[
				'label' => __('Filters', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
				],
			]
		);

		$this->add_control(
			'filter_type',
			[
				'label' => __('Filter Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default Filter', 'thegem'),
					'extended' => __('Extended Filter', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
				],
			]
		);

		$this->add_control(
			'filter_style',
			[
				'label' => __('Filter & Sorting Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'buttons',
				'options' => [
					'buttons' => __('Buttons', 'thegem'),
					'tabs-default' => __('Tabs Default', 'thegem'),
					'tabs-alternative' => __('Tabs Alternative', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'default',
				],
			]
		);

		if ($this->is_portfolio_post) {
			$filter_default = '';
		} else {
			$filter_default = 'yes';
		}

		$this->add_control(
			'portfolio_show_filter', [
				'label' => __('Show Filters', 'thegem'),
				'default' => $filter_default,
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
				],
			]
		);

		$this->add_control(
			'mobile_dropdown', [
				'label' => __('Filters Mobile Dropdown', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_filter' => 'yes',
					'filter_type' => 'default',
					'filter_style!' => 'buttons',
				],
			]
		);

		$this->add_control(
			'filter_by',
			[
				'label' => __( 'Filter By', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'multiple' => true,
				'options' => thegem_select_portfolio_taxonomies(),
				'default' => 'thegem_portfolios',
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'default',
					'portfolio_show_filter' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_subcats',
			[
				'label' => __( 'Show Subcategories', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'default',
					'portfolio_show_filter' => 'yes',
				],
			]
		);

		$this->add_control(
			'attribute_click_behavior',
			[
				'label' => __( 'Click Behavior', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'filtering' => __('Filtering', 'thegem'),
					'archive_link' => __('Link to Post Archive', 'thegem'),
				],
				'default' => 'filtering',
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'default',
					'portfolio_show_filter' => 'yes',
					'filter_by!' => 'thegem_portfolios',
				]
			]
		);

		$this->add_control(
			'attribute_order_by',
			[
				'label' => __('Order By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name' => __('Name', 'thegem'),
					'term_order' => __('Term Order', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'default',
					'portfolio_show_filter' => 'yes',
				],
			]
		);

		$this->add_control(
			'attribute_query_type',
			[
				'label' => __('Query Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'or',
				'options' => [
					'and' => __('AND', 'thegem'),
					'or' => __('OR', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'default',
					'portfolio_show_filter' => 'yes',
				],
			]
		);

		$this->add_control(
			'filters_style',
			[
				'label' => __('Filters Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => [
					'standard' => __('Horizontal', 'thegem'),
					'sidebar' => __('Sidebar', 'thegem'),
					'hidden' => __('Hidden Sidebar', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'extended',
					'portfolio_show_filter' => 'yes',
				],
			]
		);

		$this->add_control(
			'sidebar_position',
			[
				'label' => __('Sidebar Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'extended',
					'portfolio_show_filter' => 'yes',
					'filters_style' => 'sidebar',
				],
			]
		);

		$this->add_control(
			'sidebar_sticky',
			[
				'label' => __('Sticky Sidebar', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'extended',
					'portfolio_show_filter' => 'yes',
					'filters_style' => 'sidebar',
				],
			]
		);

		$this->add_control(
			'filters_sticky',
			[
				'label' => __('Sticky Filters', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'hide_filters_sorting',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name' => 'portfolio_show_filter',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'filter_type',
									'value' => 'default',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'filter_type',
											'value' => 'extended',
										],
										[
											'name' => 'filters_style',
											'value' => 'standard',
										],
									],
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'filters_sticky_color',
			[
				'label' => __('Sticky Filters Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-top-panel.sticky-fixed .portfolio-top-panel' => 'background: {{VALUE}};'
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'hide_filters_sorting',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name' => 'filters_sticky',
							'value' => 'yes',
						],
						[
							'name' => 'portfolio_show_filter',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'filter_type',
									'value' => 'default',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'filter_type',
											'value' => 'extended',
										],
										[
											'name' => 'filters_style',
											'value' => 'standard',
										],
									],
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'filters_scroll_top', [
				'label' => __('Scroll To Top', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'extended',
					'portfolio_show_filter' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'attribute_title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Title', 'thegem'),
				'default' => __('Filter by', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'show_title', [
				'label' => __('Show Title', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
			]
		);

		$repeater->add_control(
			'attribute_type',
			[
				'label' => __('Filter By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'taxonomies',
				'frontend_available' => true,
				'options' => array_merge(
					[
						'taxonomies' => __('Taxonomies', 'thegem'),
						'details' => __('Project Details', 'thegem'),
						'custom_fields' => __('Custom Fields (TheGem)', 'thegem'),
					],
					thegem_get_acf_plugin_groups()
				),
			]
		);

		$repeater->add_control(
			'attribute_taxonomies',
			[
				'label' => __( 'Select Taxonomy', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'multiple' => true,
				'options' => thegem_select_portfolio_taxonomies(),
				'default' => 'thegem_portfolios',
				'frontend_available' => true,
				'condition' => [
					'attribute_type' => 'taxonomies',
				],
			]
		);

		$repeater->add_control(
			'attribute_taxonomies_hierarchy', [
				'label' => __('Hierarchy', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'attribute_type' => 'taxonomies',
				],
			]
		);

		$repeater->add_control(
			'attribute_taxonomies_collapsible', [
				'label' => __('Collapsible', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'attribute_type' => 'taxonomies',
					'attribute_taxonomies_hierarchy' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'attribute_taxonomies_click_behavior',
			[
				'label' => __( 'Click Behavior', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'filtering' => __('Filtering', 'thegem'),
					'archive_link' => __('Link to Post Archive', 'thegem'),
				],
				'default' => 'filtering',
				'frontend_available' => true,
				'condition' => [
					'attribute_type' => 'taxonomies',
				]
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
				'condition' => [
					'attribute_type!' => 'taxonomies',
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
					'attribute_type!' => 'taxonomies',
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
					'attribute_type!' => 'taxonomies',
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
					'attribute_type!' => 'taxonomies',
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
					'attribute_type!' => 'taxonomies',
					'attribute_meta_type' => 'number',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'attribute_order_by',
			[
				'label' => __('Order By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name' => __('Name', 'thegem'),
					'term_order' => __('Term Order', 'thegem'),
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'attribute_type',
							'operator' => '=',
							'value' => 'taxonomies',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'attribute_type',
									'operator' => '!=',
									'value' => 'taxonomies',
								],
								[
									'name' => 'attribute_meta_type',
									'operator' => '=',
									'value' => 'text',
								],
							]
						]
					]
				]
			]
		);

		$repeater->add_control(
			'attribute_query_type',
			[
				'label' => __('Query Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'and',
				'options' => [
					'and' => __('AND', 'thegem'),
					'or' => __('OR', 'thegem'),
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'attribute_type',
							'operator' => '=',
							'value' => 'taxonomies',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'attribute_type',
									'operator' => '!=',
									'value' => 'taxonomies',
								],
								[
									'name' => 'attribute_meta_type',
									'operator' => '=',
									'value' => 'text',
								],
							]
						]
					]
				]
			]
		);

		$repeater->add_control(
			'attribute_display_type',
			[
				'label' => __('Display Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'list',
				'options' => [
					'list' => __('List', 'thegem'),
					'dropdown' => __('Dropdown', 'thegem'),
				],
				'description' => __('Works only for sidebar and hidden sidebar filter types', 'thegem'),
			]
		);

		$repeater->add_control(
			'attribute_display_dropdown_open',
			[
				'label' => __('Open Dropdown', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover' => __('On Hover', 'thegem'),
					'click' => __('On Click', 'thegem'),
				],
				'condition' => [
					'attribute_display_type' => 'dropdown',
				],
			]
		);

		$this->add_control(
			'repeater_attributes',
			[
				'type' => Controls_Manager::REPEATER,
				'label' => __('Attributes', 'thegem'),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ attribute_title }}}',
				'default' => [
					[
						'attribute_title' => __('Categories', 'thegem'),
					]
				],
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'extended',
					'portfolio_show_filter' => 'yes',
				],
			]
		);

		$this->add_control(
			'filters_preloading',
			[
				'label' => __('Filter Preloading', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_filter' => 'yes',
					'filter_type' => 'default',
				],
				'description' => __('If enabled, items in the filter buttons will be preloaded on the current page.', 'thegem'),
			]
		);

		$this->add_control(
			'filter_show_all', [
				'label' => __('“Show All” Button', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_filter' => 'yes',
					'filter_type' => 'default',
				],
			]
		);

		$this->add_control(
			'truncate_filters', [
				'label' => __('Truncate Filters', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_filter' => 'yes',
					'filter_type' => 'default',
				],
			]
		);

		$this->add_control(
			'truncate_filters_number',
			[
				'label' => __('Number of filters to show', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'step' => 1,
				'default' => 5,
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_filter' => 'yes',
					'filter_type' => 'default',
					'truncate_filters' => 'yes',
				],
			]
		);

		if ($this->is_portfolio_post) {
			$sorting_default = '';
		} else {
			$sorting_default = 'yes';
		}

		$this->add_control(
			'sorting_header',
			[
				'label' => __('Sorting', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'hide_filters_sorting',
							'operator' => '!=',
							'value' => 'yes',
						],
						[

							'relation' => 'or',
							'terms' => [
								[
									'name' => 'filter_type',
									'operator' => '=',
									'value' => 'extended',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'filter_type',
											'operator' => '=',
											'value' => 'default',
										], [
											'name' => 'filter_style',
											'operator' => '!=',
											'value' => 'buttons',
										],
									]
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'filter_type',
											'operator' => '=',
											'value' => 'default',
										], [
											'name' => 'filter_style',
											'operator' => '=',
											'value' => 'buttons',
										], [
											'name' => 'order_by',
											'operator' => '=',
											'value' => 'default',
										], [
											'name' => 'order',
											'operator' => '=',
											'value' => 'default',
										],
									]
								]
							],
						]
					],
				]
			]
		);

		$this->add_control(
			'portfolio_show_sorting', [
				'label' => __('Sorting on Frontend', 'thegem'),
				'default' => $sorting_default,
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'hide_filters_sorting',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'filter_type',
									'operator' => '=',
									'value' => 'extended',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'filter_type',
											'operator' => '=',
											'value' => 'default',
										], [
											'name' => 'filter_style',
											'operator' => '!=',
											'value' => 'buttons',
										],
									]
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'filter_type',
											'operator' => '=',
											'value' => 'default',
										], [
											'name' => 'filter_style',
											'operator' => '=',
											'value' => 'buttons',
										], [
											'name' => 'order_by',
											'operator' => '=',
											'value' => 'default',
										], [
											'name' => 'order',
											'operator' => '=',
											'value' => 'default',
										],
									]
								]
							],
						]
					],
				]
			]
		);

		$this->add_control(
			'sorting_type',
			[
				'label' => __('Sorting Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'extended' => __('Extended', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'blog_show_sorting' => 'yes',
					'filter_type' => 'extended',
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
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_sorting' => 'yes',
					'filter_type' => 'extended',
					'sorting_type' => 'extended',
				]
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
					'sort_by!' => ['title', 'date'],
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
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_sorting' => 'yes',
					'filter_type' => 'extended',
					'sorting_type' => 'extended',
				]
			]
		);

		$this->add_control(
			'search_header',
			[
				'label' => __('Search', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_search', [
				'label' => __('Portfolios Search', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
				]
			]
		);

		$this->add_control(
			'show_search_as',
			[
				'label' => __('Show as', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon' => __('Icon', 'thegem'),
					'input' => __('Input', 'thegem'),
				],
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'hide_filters_sorting',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name' => 'show_search',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'filter_type',
									'value' => 'default',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'filter_type',
											'value' => 'extended',
										],
										[
											'name' => 'filters_style',
											'value' => 'standard',
										],
									],
								],
							],
						],
					],
				]
			]
		);

		$this->add_control(
			'search_reset_filters', [
				'label' => __('Reset Filters on Search', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'show_search' => 'yes',
				],
			]
		);

		$this->add_control(
			'live_search', [
				'label' => __('Live Search', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'show_search' => 'yes',
				],
			]
		);

		$this->add_control(
			'search_by',
			[
				'label' => __('Search By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => [
					'content' => __('Content', 'thegem'),
					'meta' => __('Meta Fields', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'filters_text_labels_header',
			[
				'label' => __('Text Labels', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filters_text_labels_all_text',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('“Show All” Text', 'thegem'),
				'input_type' => 'text',
				'default' => __('Show All', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'extended',
					'portfolio_show_filter' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filters_text_labels_clear_text',
			[
				'label' => __('“Clear Filters” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Clear Filters', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filters_text_labels_search_text',
			[
				'label' => __('“Search” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Search...', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'show_search' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filter_buttons_hidden_show_text',
			[
				'label' => __('“Show Filters” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Show Filters', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'extended',
					'portfolio_show_filter' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filter_buttons_hidden_sidebar_title',
			[
				'label' => __('Sidebar Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Filter', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'extended',
					'portfolio_show_filter' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filter_buttons_hidden_filter_by_text',
			[
				'label' => __('“Filter By” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Filter By', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'filter_type' => 'extended',
					'portfolio_show_filter' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'extras_header',
			[
				'label' => __('Extras', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'fullwidth_section_sorting',
			[
				'label' => __('Used in fullwidth section (no gaps)', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'description' => __('Activate to add extra padding', 'thegem'),
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'portfolio_show_filter',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'name' => 'portfolio_show_sorting',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'not_found_text',
			[
				'label' => __('“No Items Found” Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'label_block' => true,
				'default' => __('No items were found matching your selection', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pagination',
			[
				'label' => __('Pagination', 'thegem'),
			]
		);

		$this->add_control(
			'items_per_page',
			[
				'label' => __('Items Per Page', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => 8,
				'frontend_available' => true,
				'description' => __('Use -1 to show all', 'thegem'),
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => __('Pagination', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => __('Pagination Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal' => __('Numbers', 'thegem'),
					'more' => __('Load More Button', 'thegem'),
					'scroll' => __('Infinite Scroll', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'load_more_show_all',
			[
				'label' => __('Show all on load more', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'pagination_type' => 'more',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'next_page_preloading',
			[
				'label' => __('Next page preloading', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'show_pagination' => 'yes',
				],
				'description' => __('If enabled, items for the next page will be preloaded on the current page.', 'thegem'),
			]
		);

		$this->add_control(
			'reduce_html_size',
			[
				'label' => __('Reduce HTML Size', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'items_on_load',
			[
				'label' => __('Items on Page Load', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 8,
				'frontend_available' => true,
				'condition' => [
					'reduce_html_size' => 'yes',
				],
				'description' => __('Number of items to load on initial page load. The rest will be loaded on user scroll to reduce HTML size of the page for better pagespeed performance.', 'thegem'),
			]
		);

		$this->add_control(
			'more_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Load More', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'pagination_type' => 'more',
					'show_pagination' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'more_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'frontend_available' => true,
				'condition' => [
					'pagination_type' => 'more',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_stretch_full_width',
			[
				'label' => __('Stretch to Full Width', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'pagination_type' => 'more',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_show_separator',
			[
				'label' => __('Separator', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'more_stretch_full_width!' => 'yes',
					'pagination_type' => 'more',
					'show_pagination' => 'yes',
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

		$ignore_highlights_default = '';
		if ($this->is_portfolio_post) {
			$ignore_highlights_default = 'yes';
		}

		$this->add_control(
			'ignore_highlights',
			[
				'label' => __('Ignore Highlighted Portfolios', 'thegem'),
				'default' => $ignore_highlights_default,
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'layout',
							'operator' => '!=',
							'value' => 'justified',
						],
						[
							'name' => 'columns_desktop',
							'value' => '100%',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'layout',
									'value' => 'justified',
								],
								[
									'name' => 'disable_preloader',
									'operator' => '!=',
									'value' => 'yes',
								],
							]
						]
					]
				]
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
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'columns_desktop',
							'operator' => '!=',
							'value' => '100%',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'layout',
									'operator' => '!=',
									'value' => 'justified',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'layout',
											'value' => 'justified',
										],
										[
											'name' => 'disable_preloader',
											'operator' => '!=',
											'value' => 'yes',
										],
									]
								]
							]
						]
					]
				]
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
				'condition' => [
					'ajax_preloader_type' => 'minimal',
				],
			]
		);

		$this->add_control(
			'disable_bottom_margin',
			[
				'label' => __('Disable Grid\'s Bottom Margin', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_metro',
			[
				'label' => __('Metro Options', 'thegem'),
				'condition' => [
					'layout' => 'metro',
				],
			]
		);

		$this->add_responsive_control(
			'metro_max_row_height',
			[
				'label' => __('Max. row\'s height in metro grid (px)', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 600,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 380
				],
				'frontend_available' => true,
				'description' => __('Metro grid auto sets the row\'s height. Specify max. allowed height for best appearance (380px recommended).', 'thegem'),
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

		/* Filter Buttons Style */
		$this->filter_buttons_style($control);

		/* Filter Style (Standard) */
		$this->filter_buttons_standard_style($control);

		/* Filter Style (Sidebar) */
		$this->filter_buttons_hidden_style($control);

		/* Selected Filters Style */
		$this->selected_filters_style($control);

		/* Sorting Style */
		$this->sorting_style($control);

		/* Sorting Style */
		$this->sorting_style_extended($control);

		/* Search Style */
		$this->search_style($control);

		/* Pagination Style */
		$this->pagination_style($control);

		/* Pagination More Style */
		$this->pagination_more_style($control);

		/* Likes Style */
		$this->likes_style($control);

		/* Hover Icons (Custom) */
		$this->hover_icons_style($control);

		/* Project Details */
		$this->project_details_style($control);

		/* Readmore  Button Style */
		$this->readmore_button_styles($control);
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
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item,
					 {{WRAPPER}} .skeleton-posts.portfolio-row .portfolio-item' => 'padding: calc({{SIZE}}{{UNIT}}/2) !important;',
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-row,
					 {{WRAPPER}} .skeleton-posts.portfolio-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.portfolio-grid.fullwidth-columns .portfolio-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2) 0;',
					'{{WRAPPER}} .portfolio.portfolio-grid .fullwidth-block:not(.no-paddings)' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid .fullwidth-block .portfolio-row' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.portfolio-grid .fullwidth-block .portfolio-top-panel' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid.fullwidth-columns .with-filter-sidebar .filter-sidebar' => 'padding-left: {{SIZE}}{{UNIT}};',
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
					'layout' => 'justified',
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
									'operator' => '=',
									'value' => 'hover',
								],
								[
									'name' => 'image_hover_effect_hover',
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
					'image_hover_effect_hover' => 'zooming-blur'
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
							'default' => __('Default', 'thegem'),
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
						'default' => 'default',
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
							'default' => __('Default', 'thegem'),
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
						'default' => 'default',
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

					if ($ekey == 'set' && $stkey == 'normal') {

						$control->add_control(
							'category_in_text_page',
							[
								'label' => __('"in" Text', 'thegem'),
								'type' => Controls_Manager::TEXT,
								'input_type' => 'text',
								'default' => __('in', 'thegem'),
								'frontend_available' => true,
								'condition' => [
									'thegem_elementor_preset!' => 'alternative',
								],
								'dynamic' => [
									'active' => true,
								],
							]
						);

						$control->add_control(
							'category_in_color_page',
							[
								'label' => __('"in" Color', 'thegem'),
								'type' => Controls_Manager::COLOR,
								'label_block' => false,
								'selectors' => [
									'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .caption .info .set .in_text' => 'color: {{VALUE}};',
								],
								'condition' => [
									'thegem_elementor_preset!' => 'alternative',
								]
							]
						);

					}

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
				'default' => 'white',
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


		$control->add_control(
			'spacing_separator_header',
			[
				'label' => 'Separator',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset!' => 'alternative',
				]
			]
		);

		$control->add_responsive_control(
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item .wrap > .caption .caption-separator' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'thegem_elementor_preset!' => 'alternative',
				]
			]
		);

		$control->start_controls_tabs('spacing_separator_tabs', [
			'condition' => [
				'thegem_elementor_preset!' => 'alternative',
			],
		]);

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				if ($stkey == 'active') {
					continue;
				}
				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('spacing_separator_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'spacing_separator_color' . $stkey,
					[
						'label' => __('Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item' . $state . ' .wrap > .caption .caption-separator' => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'spacing_separator_size' . $stkey,
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
							'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-item' . $state . ' .wrap > .caption .caption-separator' => 'width: {{SIZE}}{{UNIT}}',
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
	 * Filter Buttons Style
	 * @access protected
	 */
	protected function filter_buttons_style($control) {

		$control->start_controls_section(
			'filter_buttons_style',
			[
				'label' => __('Filter Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_filter' => 'yes',
					'filter_type' => 'default',
				]
			]
		);

		$control->add_control(
			'filter_buttons_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'left' => __('Left', 'thegem'),
					'right ' => __('Right', 'thegem'),
					'center' => __('Centered', 'thegem'),
				],
				'condition' => [
					'portfolio_show_sorting' => '',
					'show_search' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters' => 'text-align: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters a,
					{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters div.portfolio-filters-more-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_buttons_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters a,
					{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters div.portfolio-filters-more-button',
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters a,
					{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters div.portfolio-filters-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_control(
			'hover_pointer', [
				'label' => __('Hover Pointer', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'filter_style!' => 'buttons',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_bottom_spacing',
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-top-panel' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->start_controls_tabs('filter_buttons_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					$state = '.active';
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('filter_buttons_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'filter_buttons_background_color' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters a' . $state . ',
							{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters div.portfolio-filters-more-button' . $state => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'filter_style' => 'buttons',
						]
					]
				);

				$control->add_control(
					'filter_buttons_border_color' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters a' . $state . ',
							{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters div.portfolio-filters-more-button' . $state => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'filter_style' => 'buttons',
						]
					]
				);

				$control->add_control(
					'filter_buttons_text_color' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters a' . $state . ',
							{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters div.portfolio-filters-more-button' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'filter_buttons_text_typography' . $stkey,
						'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters a' . $state . ' span,
							{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters div.portfolio-filters-more-button' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'show_all_button_text',
			[
				'label' => __('“Show All” Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Show All', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'filter_show_all' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'filters_mobile_show_button_text',
			[
				'label' => __('“Show Filters” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Show Filters', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'portfolio_show_filter' => 'yes',
					'filter_type' => 'default',
					'filter_style!' => 'buttons',
					'mobile_dropdown' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'filters_more_button_text',
			[
				'label' => __('“More” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('More', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'truncate_filters' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'filters_more_button_arrow', [
				'label' => __('“More” Arrow', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'truncate_filters' => 'yes',
				],
			]
		);

		$control->add_control(
			'filters_more_button_dropdown_header',
			[
				'label' => __('Dropdown', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'truncate_filters' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'filters_more_button_dropdown_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio-filters-more .portfolio-filters-more-dropdown',
			]
		);

		$control->add_responsive_control(
			'filters_more_button_dropdown_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-more .portfolio-filters-more-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filters_more_button_dropdown_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-more .portfolio-filters-more-dropdown',
			]
		);

		$control->add_control(
			'filter_responsive_header',
			[
				'label' => __('Filter in responsive mode', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_control(
			'filter_responsive_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'frontend_available' => true,
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_control(
			'filter_responsive_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters-resp .menu-toggle i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_responsive_control(
			'filter_responsive_icon_size',
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters-resp .menu-toggle i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters-resp .menu-toggle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'filter_responsive_typography',
				'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters-resp ul li a',
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_control(
			'filter_responsive_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters-resp ul li a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_control(
			'filter_responsive_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters-resp ul li' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_control(
			'filter_responsive_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters-resp ul li, {{WRAPPER}} .portfolio.portfolio-grid .portfolio-filters-resp ul' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'filter_style' => 'buttons',
				]
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Filter Style (Standard)
	 * @access protected
	 */
	protected function filter_buttons_standard_style($control) {

		$control->start_controls_section(
			'filter_buttons_standard_style',
			[
				'label' => __('Filter Style (Standard)', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_filter' => 'yes',
					'filter_type' => 'extended',
					'filters_style' => 'standard',
				]
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'filter_buttons_standard_typography',
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-standard,
				{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button,
				{{WRAPPER}} .portfolio.portfolio-grid .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-form input',
			]
		);

		$control->add_control(
			'filter_buttons_standard_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .name,
					{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'filter_buttons_standard_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .name,
					{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'background: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .name,
					{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_buttons_standard_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .name,
				{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button',
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .name,
					{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_bottom_spacing',
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-top-panel' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_space_between',
			[
				'label' => __('Space Between', 'thegem'),
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
					'{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item' => 'margin-right: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->add_control(
			'filter_buttons_standard_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'left',
				'options' => [
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
					'center' => __('Center', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'filter_buttons_standard_dropdown_header',
			[
				'label' => __('Dropdown', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'filter_buttons_standard_dropdown_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-standard:not(.single-filter, .style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list',
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_dropdown_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.single-filter, .style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_buttons_standard_dropdown_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-standard:not(.single-filter, .style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list',
			]
		);

		$control->start_controls_tabs('filter_buttons_standard_dropdown_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				$addition_selector = '';
				$addition_selector_normal = '';
				if ($stkey == 'active') {
					$state = '.active';
					$addition_selector = '{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-range,
					{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle + span,
					{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle';
				} else if ($stkey == 'hover') {
					$state = ':hover';
				} else {
					$addition_selector_normal = ', {{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount,
					{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount .slider-amount-text';
				}

				$control->start_controls_tab('filter_buttons_standard_dropdown_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'filter_buttons_standard_dropdown_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .portfolio-filter-item-list ul li a' . $state . $addition_selector_normal => 'color: {{VALUE}};',
							$addition_selector => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'filter_buttons_standard_dropdown_price_range_background_color_' . $stkey,
					[
						'label' => __('Number Range Background', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-filters-list.style-standard .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount' . $state => 'background-color: {{VALUE}};',
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
	 * Filter Style (Sidebar)
	 * @access protected
	 */
	protected function filter_buttons_hidden_style($control) {

		$control->start_controls_section(
			'filter_buttons_style_hidden',
			[
				'label' => __('Filter Style (Sidebar)', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'portfolio_show_filter' => 'yes',
					'filter_type' => 'extended',
				]
			]
		);

		$control->add_control(
			'filter_buttons_responsive_sidebar_header',
			[
				'label' => __('Sidebar', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'filters_style' => 'sidebar',
				]
			]
		);

		$control->add_responsive_control(
			'items_list_max_height',
			[
				'label' => __('Filter List Max Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'description' => __('Limit the filter attributes displayed in the list using the scrollbar. Leave blank for a complete list.', 'thegem'),
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter-item-list' => 'max-height: {{SIZE}}{{UNIT}}; padding-right: 10px;',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_sidebar_separator_width',
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
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filter-item,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filter-item,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filter-item,
					{{WRAPPER}} .portfolio-filters-list.style-hidden .widget-area .widget,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar .widget-area .widget,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .widget-area .widget' => 'border-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filter-item,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filter-item,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filter-item,
					{{WRAPPER}} .portfolio-filters-list.style-hidden .widget-area .widget,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar .widget-area .widget,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .widget-area .widget' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'filter_buttons_hidden_sidebar_typography',
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item,
				{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input,
				{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input::placeholder',
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_typography_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Filter titles inherit style settings from sidebar widgets and can be adjusted in theme options under "Typography -> Elements -> Sidebar Widgets"', 'thegem'),
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->start_controls_tabs('filter_buttons_hidden_sidebar_selected_tabs', [
			'condition' => [
				'filters_style!' => 'standard',
			]
		]);

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				$addition_selector = '';
				$addition_selector_normal = '';
				if ($stkey == 'active') {
					$state = '.active';
					$addition_selector = '{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-range,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-range,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-range,
					{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle + span,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle + span,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle + span,
					{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle';
				} else if ($stkey == 'hover') {
					$state = ':hover';
				} else {
					$addition_selector_normal = ', {{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount,
					{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount .slider-amount-text,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount .slider-amount-text,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount .slider-amount-text';
				}

				$control->start_controls_tab('filter_buttons_hidden_sidebar_selected_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'filter_buttons_hidden_sidebar_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filter-item .portfolio-filter-item-list ul li a' . $state . ',
							{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filter-item .portfolio-filter-item-list ul li a' . $state . ',
							{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filter-item .portfolio-filter-item-list ul li a' . $state . $addition_selector_normal => 'color: {{VALUE}};',
							$addition_selector => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'filter_buttons_hidden_sidebar_range_background_color_' . $stkey,
					[
						'label' => __('Number Range Background', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount' . $state . ',
							{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount' . $state . ',
							{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->end_controls_tab();
			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'filter_buttons_show_filters_header',
			[
				'label' => __('Show Filters', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filters_style' => 'hidden',
				]
			]
		);

		$control->add_control(
			'filter_buttons_show_responsive_filters_header',
			[
				'label' => __('Responsive Mode - Show Filters', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => [
					'filters_style!' => 'hidden',
				]
			]
		);

		$control->add_control(
			'show_filters_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'left',
				'options' => [
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
					'center' => __('Center', 'thegem'),
				],
				'selectors_dictionary' => [
					'left' => is_rtl() ? 'margin-left: auto;' : 'margin-right: auto;',
					'center' => 'margin-right: auto; margin-left: auto;',
					'right' => is_rtl() ? 'margin-right: auto;' : 'margin-left: auto;',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => '{{VALUE}}',
				],
			]
		);

		$control->add_control(
			'filter_buttons_hidden_show_icon', [
				'label' => __('Show Icon', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'filter_buttons_hidden_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'color: {{VALUE}};',
				],
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'filter_buttons_hidden_typography',
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button',
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_buttons_hidden_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button',
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_bottom_spacing',
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
				'condition' => [
					'filters_style!' => 'standard',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-top-panel' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_header',
			[
				'label' => __('Sidebar', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filters_style' => 'hidden',
				]
			]
		);

		$control->add_control(
			'filter_buttons_responsive_hidden_sidebar_header',
			[
				'label' => __('Responsive Mode - Hidden Sidebar', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filters_style!' => 'hidden',
				]
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Sidebar Title Typography', 'thegem'),
				'name' => 'filter_buttons_hidden_sidebar_title_typography',
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area h2',
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_title_color',
			[
				'label' => __('Sidebar Title Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area h2' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('"Filter by" Typography', 'thegem'),
				'name' => 'filter_buttons_hidden_sidebar_filterby_typography',
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filters-area-scrollable .widget-title,
				{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filters-area-scrollable .widget-title,
				{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filters-area-scrollable .widget-title',
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_filterby_color',
			[
				'label' => __('"Filter by" Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filters-area-scrollable .widget-title,
				{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filters-area-scrollable .widget-title,
				{{WRAPPER}} .portfolio-filters-list.style-sidebar .portfolio-filters-area-scrollable .widget-title' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_sidebar_width',
			[
				'label' => __('Sidebar Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filters-outer .portfolio-filters-area,
					 {{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filters-outer .portfolio-filters-area,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar-mobile .portfolio-filters-outer .portfolio-filters-area' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'filter_buttons_hidden_sidebar_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filters-outer .portfolio-filters-area,
				{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filters-outer .portfolio-filters-area,
				{{WRAPPER}} .portfolio-filters-list.style-sidebar-mobile .portfolio-filters-outer .portfolio-filters-area',
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_overlay_color',
			[
				'label' => __('Overlay Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filters-outer,
					{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filters-outer,
					{{WRAPPER}} .portfolio-filters-list.style-sidebar-mobile .portfolio-filters-outer' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_buttons_hidden_sidebar_overlay_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-hidden .portfolio-filters-outer .portfolio-filters-area,
				{{WRAPPER}} .portfolio-filters-list.style-standard-mobile .portfolio-filters-outer .portfolio-filters-area,
				{{WRAPPER}} .portfolio-filters-list.style-sidebar-mobile .portfolio-filters-outer .portfolio-filters-area',
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_close_icon_color',
			[
				'label' => __('“Close” Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-close-filters' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'duplicate_selected_in_sidebar', [
				'label' => __('Duplicate Selected in Sidebar', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'filters_style' => 'sidebar',
				]
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Selected Filters Style
	 * @access protected
	 */
	protected function selected_filters_style($control) {

		$control->start_controls_section(
			'selected_style',
			[
				'label' => __('Selected Filters', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'filter_buttons_standard_selected_typography',
				'selector' => '{{WRAPPER}} .portfolio-selected-filters .portfolio-selected-filter-item',
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_selected_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-selected-filters .portfolio-selected-filter-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_buttons_standard_selected_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-selected-filters .portfolio-selected-filter-item',
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_selected_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-selected-filters .portfolio-selected-filter-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_selected_space_between',
			[
				'label' => __('Space Between', 'thegem'),
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
					'{{WRAPPER}} .portfolio-selected-filters .portfolio-selected-filter-item' => 'margin-right: {{SIZE}}{{UNIT}} !important',
				]
			]
		);

		$control->start_controls_tabs('filter_buttons_standard_selected_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('filter_buttons_standard_selected_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'filter_buttons_standard_selected_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-selected-filters .portfolio-selected-filter-item' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'filter_buttons_standard_selected_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-selected-filters .portfolio-selected-filter-item' . $state => 'background-color: {{VALUE}};',
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
	 * Sorting Style
	 * @access protected
	 */
	protected function sorting_style($control) {

		$control->start_controls_section(
			'sorting_style',
			[
				'label' => __('Sorting Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'order_by' => 'default',
					'order' => 'default',
					'portfolio_show_sorting' => 'yes',
					'filter_type' => 'default',
					'filter_style' => 'buttons',
				]
			]
		);

		$control->add_control(
			'switch_background_color',
			[
				'label' => __('Switch Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .sorting-switcher' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'switch_color',
			[
				'label' => __('Switch Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .sorting-switcher:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'sorting_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-sorting-sep' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'sorting_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-sorting label' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'sorting_text_typography',
				'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .portfolio-sorting label',
			]
		);

		$control->add_control(
			'sorting_date_text',
			[
				'label' => __('“Date” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Date', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'sorting_name_text',
			[
				'label' => __('“Name” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Name', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'sorting_desc_text',
			[
				'label' => __('“Desc” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Desc', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'sorting_asc_text',
			[
				'label' => __('“Asc” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Asc', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);


		$control->end_controls_section();
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
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'hide_filters_sorting',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name' => 'portfolio_show_sorting',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'filter_type',
									'operator' => '=',
									'value' => 'extended',
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'filter_type',
											'operator' => '=',
											'value' => 'default',
										], [
											'name' => 'filter_style',
											'operator' => '!=',
											'value' => 'buttons',
										],
									]
								]
							],
						],
					],
				]
			]
		);

		$control->add_control(
			'sorting_extended_text',
			[
				'label' => __('“Default sorting” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Default sorting', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
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

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'sorting_extended_text_typography',
				'selector' => '{{WRAPPER}} .portfolio-sorting-select div.portfolio-sorting-select-current,
				{{WRAPPER}} .portfolio-sorting-select ul li',
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
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-top-panel' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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

		$control->add_control(
			'sorting_extended_dropdown_title_text',
			[
				'label' => __('“Sort by title (A-Z)” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Sort by title (A-Z)', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'sorting_extended_dropdown_title_desc_text',
			[
				'label' => __('“Sort by title (Z-A)” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Sort by title (Z-A)', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'sorting_extended_dropdown_latest_text',
			[
				'label' => __('“Sort by latest” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Sort by latest', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'sorting_extended_dropdown_oldest_text',
			[
				'label' => __('“Sort by oldest” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Sort by oldest', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
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
	 * Search Style
	 * @access protected
	 */
	protected function search_style($control) {

		$control->start_controls_section(
			'search_style',
			[
				'label' => __('Portfolios Search', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'hide_filters_sorting!' => 'yes',
					'show_search' => 'yes',
				]
			]
		);

		$control->add_control(
			'filter_buttons_standard_search_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-button,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter .portfolio-search-filter-button' => 'color: {{VALUE}};',
				],
				'condition' => [
					'filters_style' => 'standard',
				]
			]
		);

		$control->add_control(
			'filter_buttons_standard_search_icon_background_color',
			[
				'label' => __('Icon Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'filters_style' => 'standard',
				]
			]
		);

		$control->add_control(
			'filter_buttons_standard_search_icon_border_color',
			[
				'label' => __('Icon Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-button' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'filters_style' => 'standard',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_search_input_border_radius',
			[
				'label' => __('Input Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-form input,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'filters_style' => 'standard',
				]
			]
		);

		$control->add_control(
			'filter_buttons_standard_search_input_color',
			[
				'label' => __('Input Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-form input,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input' => 'color: {{VALUE}};',
				]
			]
		);

		$control->add_control(
			'filter_buttons_standard_search_input_background_color',
			[
				'label' => __('Input Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-form input,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'filters_style' => 'standard',
				]
			]
		);

		$control->add_control(
			'filter_buttons_hidden_search_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter .portfolio-search-filter-button,
					{{WRAPPER}} .portfolio-filters-list .widget_product_search button:before,
					{{WRAPPER}} .portfolio.portfolio-grid .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-button' => 'color: {{VALUE}};',
				],
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_search_input_border_radius',
			[
				'label' => __('Input Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input,
					{{WRAPPER}} .portfolio-filters-list .widget_product_search input,
					{{WRAPPER}} .portfolio.portfolio-grid .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-form input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->add_control(
			'filter_buttons_hidden_search_input_color',
			[
				'label' => __('Input Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input,
					{{WRAPPER}} .portfolio-filters-list .widget_product_search input,
					{{WRAPPER}} .portfolio.portfolio-grid .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-form input' => 'color: {{VALUE}};',
				],
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->add_control(
			'filter_buttons_hidden_search_input_background_color',
			[
				'label' => __('Input Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input,
					{{WRAPPER}} .portfolio-filters-list .widget_product_search input,
					{{WRAPPER}} .portfolio.portfolio-grid .portfolio-top-panel .portfolio-top-panel-right .portfolio-search-filter .portfolio-search-filter-form input' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'filters_style!' => 'standard',
				]
			]
		);

		$control->end_controls_tabs();

		$control->end_controls_section();
	}


	/**
	 * Pagination Style
	 * @access protected
	 */
	protected function pagination_style($control) {

		$control->start_controls_section(
			'pagination_normal_style',
			[
				'label' => __('Pagination Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['pagination_type' => 'normal']
			]
		);

		$control->add_responsive_control(
			'pagination_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
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
				'default' => [
					'size' => 100,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .gem-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_control(
			'pagination_numbers_header',
			[
				'label' => __('Numbers', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'pagination_numbers_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'pagination_numbers_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$control->start_controls_tabs('pagination_numbers_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					$state = '.current';
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}
				$control->start_controls_tab('pagination_numbers_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'pagination_numbers_background_color' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-pagination a' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'pagination_numbers_border_type' . $stkey,
						'label' => __('Border', 'thegem'),
						'selector' => '{{WRAPPER}} .gem-pagination a' . $state,
					]
				);

				$control->add_control(
					'pagination_numbers_text_color' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-pagination a' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'pagination_numbers_text_typography' . $stkey,
						'selector' => '{{WRAPPER}} .gem-pagination a' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'pagination_arrows_header',
			[
				'label' => __('Arrows', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'pagination_arrows_left_icon',
			[
				'label' => __('Left Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'pagination_arrows_right_icon',
			[
				'label' => __('Right Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'frontend_available' => true,
			]
		);

		$control->add_responsive_control(
			'pagination_arrows_icon_size',
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
					'{{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .prev i, {{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .next i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};
					font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'pagination_arrows_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .prev, {{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'pagination_arrows_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .prev, {{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

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
					'pagination_arrows_background_color' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .prev' . $state . ', {{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .next' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'pagination_arrows_border_type' . $stkey,
						'label' => __('Border Type', 'thegem'),
						'selector' => '{{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .prev' . $state . ', {{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .next' . $state,
					]
				);

				$control->add_control(
					'pagination_arrows_icon_color' . $stkey,
					[
						'label' => __('Icon Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .prev' . $state . ', {{WRAPPER}} .portfolio.portfolio-grid .gem-pagination .next' . $state => 'color: {{VALUE}};',
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
	 * Pagination More Style
	 * @access protected
	 */
	protected function pagination_more_style($control) {

		$control->start_controls_section(
			'pagination_more_style',
			[
				'label' => __('"Load More" Button Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['pagination_type' => 'more']
			]
		);

		$control->add_responsive_control(
			'pagination_more_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
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
				'default' => [
					'size' => 100,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-grid .portfolio-load-more' => 'margin-top: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_control(
			'pagination_more_button_type',
			[
				'label' => __('Button Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'flat',
				'options' => [
					'flat' => __('Flat', 'thegem'),
					'outline' => __('Outline', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$control->add_responsive_control(
			'pagination_more_button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'small',
				'options' => [
					'small' => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$control->add_responsive_control(
			'pagination_more_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_more_button_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-load-more button',
			]
		);

		$control->add_responsive_control(
			'pagination_more_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('pagination_more_button_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}

				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('pagination_more_button_tab_' . $stkey, ['label' => $stelem]);

				$control->add_responsive_control(
					'pagination_more_button_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-load-more button' . $state . ' span' => 'color: {{VALUE}};',
							'{{WRAPPER}} .portfolio-load-more button' . $state . ' i:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .portfolio-load-more button' . $state . ' svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'pagination_more_button_typography_' . $stkey,
						'selector' => '{{WRAPPER}} .portfolio-load-more button' . $state . ' span',
					]
				);

				$control->add_responsive_control(
					'pagination_more_button_bg_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-load-more button' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'pagination_more_button_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-load-more button' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'pagination_more_button_shadow_' . $stkey,
						'label' => __('Shadow', 'thegem'),
						'selector' => '{{WRAPPER}} .portfolio-load-more button' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_responsive_control(
			'pagination_more_button_icon_align',
			[
				'label' => __('Icon Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'left',
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
				'frontend_available' => true,
			]
		);

		$control->add_responsive_control(
			'pagination_more_button_icon_spacing',
			[
				'label' => __('Icon Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
					'{{WRAPPER}} .portfolio-load-more button.gem-button-icon-position-right .gem-button-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio-load-more button.gem-button-icon-position-left .gem-button-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'pagination_more_button_separator_header',
			[
				'label' => __('Separator', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'more_show_separator' => 'yes',
				],
			]
		);

		$control->add_control(
			'pagination_more_button_separator_style_active',
			[
				'label' => __('Separator Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'gem-button-separator-type-single',
				'options' => [
					'gem-button-separator-type-single' => __('Single', 'thegem'),
					'gem-button-separator-type-square' => __('Square', 'thegem'),
					'gem-button-separator-type-soft-double' => __('Soft Double', 'thegem'),
					'gem-button-separator-type-strong-double' => __('Strong Double', 'thegem'),
				],
				'condition' => [
					'more_show_separator' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		// Size Strong Double & Soft Double & Single
		$control->add_responsive_control(
			'pagination_more_button_separator_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' =>
						[
							'gem-button-separator-type-single',
							'gem-button-separator-type-soft-double',
							'gem-button-separator-type-strong-double',
						],
					'more_show_separator' => 'yes',
				],
				'size_units' => ['%',],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'size' => 50,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder' => 'width:{{SIZE}}{{UNIT}}; flex-grow: initial;',
				],
			]
		);

		// Size Square
		$control->add_responsive_control(
			'pagination_more_button_separator_size_square',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-square',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'size' => 50,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-type-square' => 'padding:0 calc( 50% - {{SIZE}}{{UNIT}} );',
				],
			]
		);

		// Weight Strong Double
		$control->add_responsive_control(
			'pagination_more_button_separator_double_weight',
			[
				'label' => __('Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-strong-double',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder .gem-button-separator-line' => 'border-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Weight Soft Double
		$control->add_responsive_control(
			'pagination_more_button_separator_soft_weight',
			[
				'label' => __('Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-soft-double',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder .gem-button-separator-line' => 'border-top:{{SIZE}}{{UNIT}} solid; border-bottom:{{SIZE}}{{UNIT}} solid;',
				],
			]
		);

		// Weight Single
		$control->add_responsive_control(
			'pagination_more_button_separator_single_weight',
			[
				'label' => __('Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-single',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder .gem-button-separator-line' => 'border-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Height Strong Double & Soft
		$control->add_responsive_control(
			'pagination_more_button_separator_double_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' =>
						[
							'gem-button-separator-type-strong-double',
							'gem-button-separator-type-soft-double',
						],
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder .gem-button-separator-line' => 'height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Height Square
		$control->add_responsive_control(
			'pagination_more_button_separator_square_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-square',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-type-square .gem-button-separator-button' => 'margin: {{SIZE}}{{UNIT}} 0;',
				],
			]
		);

		// Color
		$control->add_control(
			'pagination_more_button_color_border',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'more_show_separator' => 'yes',
				],
				'default' => '#b6c6c9',
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-line' => 'border-color:{{VALUE}}; color:{{VALUE}};',
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-type-square svg line' => 'stroke:{{VALUE}};',
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
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render() {
		$settings = array_intersect_key($this->get_settings_for_display(), array_flip($this->get_frontend_settings_keys()));
		$settings['style_uid'] = $widget_uid = $this->get_id();

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

		$grid_uid = $settings['portfolio_uid'];
		$grid_uid_url = $grid_uid . '-';

		if ($settings['hide_filters_sorting'] == 'yes') {
			$settings['portfolio_show_filter'] = '';
			$settings['portfolio_show_sorting'] = '';
			$settings['show_search'] = '';
		}

		if ($settings['layout'] == 'creative') {
			$settings['ignore_highlights'] = 'yes';
		}

		if ($settings['disable_preloader'] == 'yes') {
			$settings['ignore_highlights'] = 'yes';
			$settings['skeleton_loader'] = '';
		}

		if ($settings['portfolio_show_sorting'] == 'yes' && $settings['filter_type'] == 'default' && $settings['filter_style'] == 'buttons') {
			$settings['orderby'] = 'date';
		} else if (isset($settings['order_by']) && $settings['order_by'] != 'default') {
			$settings['orderby'] = $settings['order_by'];
		} else {
			$settings['orderby'] = 'menu_order ID';
		}

		if ($settings['portfolio_show_sorting'] == 'yes' && $settings['filter_type'] == 'default' && $settings['filter_style'] == 'buttons') {
			$settings['order'] = 'desc';
		} else if (!isset($settings['order']) || $settings['order'] == 'default') {
			$settings['order'] = 'asc';
		}

		if ($settings['category_in_text']) {
			$settings['categories_in_text'] = $settings['category_in_text'];
		} else if ($settings['category_in_text_page']) {
			$settings['categories_in_text'] = $settings['category_in_text_page'];
		} else {
			$settings['categories_in_text'] = __('in', 'thegem');
		}

		if ($settings['caption_position'] == 'hover') {
			$settings['hover_effect'] = $settings['image_hover_effect_hover'];
		} else if ($settings['caption_position'] == 'page') {
			$settings['hover_effect'] = $settings['image_hover_effect_page'];
		} else {
			if ($settings['thegem_elementor_preset'] == 'alternative') {
				$settings['hover_effect'] = $settings['image_hover_effect_hover'];
			} else {
				$settings['hover_effect'] = $settings['image_hover_effect_image'];
			}
		}

		$details_list = thegem_select_portfolio_details();
		$custom_fields_list = $this->select_portfolio_custom_fields();
		$meta_list = array_merge($details_list, $custom_fields_list);
		if (class_exists( 'ACF' )){
			foreach (thegem_get_acf_plugin_groups() as $gr=>$value){
				$meta_list = array_merge($meta_list, thegem_get_acf_plugin_fields_by_group($gr));
			}
		}

		if ($settings['search_by'] == 'meta') {
			$settings['search_by'] = array_keys($meta_list);
		}

		if (!empty($settings['image_ratio_default']['size'])) {
			$settings['image_aspect_ratio'] = 'custom';
			$settings['image_ratio_custom'] = $settings['image_ratio_default']['size'];
		}

		$localize = array(
			'data' => $settings,
			'action' => 'portfolio_grid_load_more',
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('portfolio_ajax-nonce')
		);
		wp_localize_script('thegem-portfolio-grid-extended', 'thegem_portfolio_ajax_' . $widget_uid, $localize);
		$settings['action'] = 'portfolio_grid_load_more';

		if ($settings['portfolio_show_filter'] == 'yes' && $settings['filter_type'] == 'default') {
			if (isset($taxonomy_filter[$settings['filter_by']])) {
				$terms = $taxonomy_filter[$settings['filter_by']];
				foreach ($terms as $key => $term) {
					$terms[$key] = get_term_by('slug', $term, $settings['filter_by'] );
					if (!$terms[$key]) {
						unset($terms[$key]);
					} elseif(isset($settings['show_subcats']) && $settings['show_subcats'] === 'yes') {
						$subterms = get_term_children($terms[$key]->term_id, $settings['filter_by']);
						foreach($subterms as $subterm) {
							$terms[$subterm] = get_term_by('term_id', $subterm, $settings['filter_by'] );
						}
					}
				}
			} else {
				$exclude_arg = array();
				if ($settings['exclude_type'] == 'term' && !empty($settings['exclude_portfolio_terms'])) {
					foreach ($settings['exclude_portfolio_terms'] as $exclude_term) {
						$arr = explode("|", $exclude_term);
						if(!empty($arr[1])) {
							$exclude_arg[] = $arr[1];
						}
					}
				}
				$terms = get_terms(array(
					'taxonomy' => $settings['filter_by'],
					'orderby' => $settings['attribute_order_by'],
					'exclude' => $exclude_arg,
				));
			}
			if ($settings['filter_by'] == 'thegem_portfolios' && $settings['attribute_order_by'] == 'term_order') {
				usort($terms, 'portolios_cmp');
			}
		}

		$taxonomy_filter_current = $taxonomy_filter;
		$categories_filter = [];
		if (isset($_GET[$grid_uid_url . 'category'])) {
			$categories_filter = explode(",", $_GET[$grid_uid_url . 'category']);
			$taxonomy_filter_current['thegem_portfolios'] = $categories_filter;
		} else if ($settings['portfolio_show_filter'] == 'yes' && $settings['filter_type'] == 'default' && $settings['filter_show_all'] != 'yes' && $settings['filter_by'] == 'thegem_portfolios') {
			foreach ($terms as $term) {
				$categories_filter = [$term->slug];
				break;
			}
			$taxonomy_filter_current['thegem_portfolios'] = $categories_filter;
		}

		if ($settings['loading_animation'] === 'yes') {
			wp_enqueue_style('thegem-animations');
			wp_enqueue_script('thegem-items-animations');
			wp_enqueue_script('thegem-scroll-monitor');
		}

		wp_enqueue_style('thegem-hovers-' . $settings['hover_effect']);

		if ($settings['pagination_type'] == 'more') {
			wp_enqueue_style('thegem-button');
		} else if ($settings['pagination_type'] == 'scroll') {
			wp_enqueue_script('thegem-scroll-monitor');
		}

		if ($settings['layout'] !== 'creative' && ($settings['layout'] !== 'justified' || $settings['ignore_highlights'] !== 'yes')) {
			if ($settings['layout'] == 'metro') {
				wp_enqueue_script('thegem-isotope-metro');
			} else {
				wp_enqueue_script('thegem-isotope-masonry-custom');
			}
		}

		if ($settings['show_readmore_button'] == 'yes') {
			wp_enqueue_style('thegem-button');
		}

		$page = 1;
		$next_page = 0;

		if (!empty($_GET[$grid_uid_url . 'page'])) {
			$page = $_GET[$grid_uid_url . 'page'];
		}

		if ($page !== 1) {
			$settings['reduce_html_size'] = '';
		}
		$items_per_page = $settings['items_per_page'] ? intval($settings['items_per_page']) : 8;
		if ($settings['reduce_html_size'] == 'yes') {
			$items_on_load = $settings['items_on_load'] ? intval($settings['items_on_load']) : 8;
			if ($items_on_load >= $items_per_page) {
				$settings['reduce_html_size'] = '';
				$items_on_load = $items_per_page;
			}
		} else {
			$items_on_load = $items_per_page;
		}

		$selected_orderby = $selected_order = 'default';

		if (!empty($_GET[$grid_uid_url . 'orderby'])) {
			$orderby = $_GET[$grid_uid_url . 'orderby'];
			$selected_orderby = $orderby;
		} else {
			$orderby = $settings['orderby'];
		}

		if (!empty($_GET[$grid_uid_url . 'order'])) {
			$order = $_GET[$grid_uid_url . 'order'];
			if ($settings['sorting_type'] == 'extended') {
				$selected_order = $order;
			} else {
				if ($selected_orderby != 'default') {
					$selected_orderby .= '-' . $order;
				}
			}
		} else {
			$order = $settings['order'];
		}

		$taxonomies_list = get_thegem_select_post_type_taxonomies('thegem_pf_item');
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
		if (empty($portfolios_filters_tax_url) && $settings['portfolio_show_filter'] == 'yes' && $settings['filter_type'] == 'default' && $settings['filter_show_all'] != 'yes' && $settings['filter_by'] != 'thegem_portfolios') {
			$active_tax = '';
			foreach ($terms as $term) {
				$active_tax = $term->slug;
				break;
			}
			$portfolios_filters_tax_url['tax_' . $settings['filter_by']] = $taxonomy_filter_current[$settings['filter_by']] = [$active_tax];
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

		$portfolio_loop = thegem_get_portfolio_posts($portfolios_posts, $taxonomy_filter_current, $meta_filter_current, $search_current, $settings['search_by'], $page, $items_on_load, $orderby, $order, intval($settings['offset']), $settings['exclude_portfolios']);

		$portfolio_title = '';

		if ($portfolio_loop->have_posts() || !empty($categories_filter) || !empty($attributes_filter) || !empty($search_current)) {

			$max_page = ceil(($portfolio_loop->found_posts - intval($settings['offset'])) / $items_per_page);

			if ($settings['reduce_html_size'] == 'yes') {
				$next_page = $portfolio_loop->found_posts > $items_on_load ? 2 : 0;
				$next_page_pagination = $max_page > $page ? $page + 1 : 0;
			} else {
				$next_page = $next_page_pagination = $max_page > $page ? $page + 1 : 0;
			}

			$item_classes = get_thegem_portfolio_render_item_classes($settings);
			$thegem_sizes = get_thegem_portfolio_render_item_image_sizes($settings);

			if ($settings['columns_desktop'] == '100%' || (($settings['ignore_highlights'] !== 'yes' || in_array($settings['layout'], ['masonry', 'metro'])) && $settings['skeleton_loader'] !== 'yes')) {
				$spin_class = 'preloader-spin';
				if ($settings['ajax_preloader_type'] == 'minimal') {
					$spin_class = 'preloader-spin-new';
				}
				echo apply_filters('thegem_portfolio_preloader_html', '<div class="preloader save-space"><div class="' . $spin_class . '"></div></div>');
			} else if ($settings['skeleton_loader'] == 'yes') { ?>
				<div class="preloader save-space">
					<div class="skeleton">
						<div class="skeleton-posts portfolio-row">
							<?php for ($x = 0; $x < $portfolio_loop->post_count; $x++) {
								echo thegem_portfolio_grid_render_item($settings, $item_classes);
							} ?>
						</div>
					</div>
				</div>
			<?php } ?>
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
				$this->add_render_attribute(
					'portfolio-wrap',
					[
						'class' => [
							'portfolio portfolio-grid extended-portfolio-grid no-padding',
							$settings['show_pagination'] === 'yes' ? 'portfolio-pagination-' . $settings['pagination_type'] : 'portfolio-pagination-disabled',
							'portfolio-style-' . $settings['layout'],
							'background-style-' . $settings['caption_container_preset'],
							'hover-' . $settings['hover_effect'],
							'title-on-' . $title_on,
							'caption-position-' . $settings['caption_position'],
							'hover-elements-size-' . $settings['hover_elements_size'],
							'version-' . $settings['thegem_elementor_preset'],
							($settings['loading_animation'] == 'yes' ? 'loading-animation' : ''),
							($settings['loading_animation'] == 'yes' && $settings['animation_effect'] ? 'item-animation-' . $settings['animation_effect'] : ''),
							($settings['loading_animation'] == 'yes' && $settings['loading_animation_mobile'] == 'yes' ? 'enable-animation-mobile' : ''),
							($settings['image_gaps']['size'] == 0 ? 'no-gaps' : ''),
							($settings['shadowed_container'] == 'yes' ? 'shadowed-container' : ''),
							($settings['columns_desktop'] == '100%' || $settings['fullwidth_section_sorting'] == 'yes' ? 'fullwidth-columns' : ''),
							($settings['columns_desktop'] == '100%' ? 'fullwidth-columns-' . $settings['columns_100'] : ''),
							($settings['caption_position'] == 'image' && $settings['image_hover_effect_image'] == 'gradient' ? 'hover-gradient-title' : ''),
							($settings['caption_position'] == 'image' && $settings['image_hover_effect_image'] == 'circular' ? 'hover-circular-title' : ''),
							($settings['caption_position'] == 'hover' || $settings['caption_position'] == 'image' ? 'hover-title' : ''),
							($settings['layout'] == 'masonry' && $settings['columns_desktop'] != '1x' ? 'portfolio-items-masonry' : ''),
							($settings['columns_desktop'] != '100%' ? 'columns-' . str_replace("x", "", $settings['columns_desktop']) : ''),
							'columns-tablet-' . str_replace("x", "", $settings['columns_tablet']),
							'columns-mobile-' . str_replace("x", "", $settings['columns_mobile']),
							($settings['layout'] == 'creative' || ($settings['layout'] == 'justified' && $settings['ignore_highlights'] == 'yes') ? 'disable-isotope' : ''),
							($settings['next_page_preloading'] == 'yes' && $settings['show_pagination'] === 'yes' ? 'next-page-preloading' : ''),
							($settings['filters_preloading'] == 'yes' ? 'filters-preloading' : ''),
							($settings['layout'] == 'creative' && $settings['scheme_apply_mobiles'] !== 'yes' ? 'creative-disable-mobile' : ''),
							($settings['layout'] == 'creative' && $settings['scheme_apply_tablets'] !== 'yes' ? 'creative-disable-tablet' : ''),
							($settings['disable_bottom_margin'] == 'yes' ? 'disable-bottom-margin' : ''),
							(($settings['image_size'] == 'full' && empty($settings['image_ratio']['size']) || !in_array($settings['image_size'], ['full', 'default'])) ? 'full-image' : ''),
							($settings['ajax_preloader_type'] == 'minimal' ? 'minimal-preloader' : ''),
							($settings['reduce_html_size'] == 'yes' ? 'reduce-size' : ''),
						],
						'data-style-uid' => esc_attr($widget_uid),
						'data-portfolio-uid' => esc_attr($grid_uid),
						'data-current-page' => esc_attr($page),
						'data-per-page' => esc_attr($items_per_page),
						'data-next-page' => esc_attr($next_page),
						'data-pages-count' => esc_attr($max_page),
						'data-hover' => esc_attr($settings['hover_effect']),
						'data-portfolio-filter' => esc_attr(json_encode($categories_filter)),
						'data-portfolio-filter-attributes' => esc_attr(json_encode($attributes_filter)),
						'data-portfolio-filter-search' => esc_attr($search_current),
					]
				); ?>

				<div <?php echo $this->get_render_attribute_string('portfolio-wrap'); ?>>
					<?php
					$search_right = $settings['show_search'] == 'yes' && ($settings['portfolio_show_filter'] != 'yes' || $settings['filter_type'] == 'default' || $settings['filters_style'] == 'standard');
					$has_right_panel = $settings['filter_type'] == 'default' || $settings['portfolio_show_sorting'] == 'yes' || $search_right;
					$has_meta_filtering = false;
					$selected_shown = false;
					if ($settings['portfolio_show_sets'] == 'yes') {
						$meta_type = isset($settings['additional_meta_type']) ? $settings['additional_meta_type'] : 'taxonomies';
						if ($meta_type == 'taxonomies') {
							$meta_taxonomies = isset($settings['additional_meta_taxonomies']) ? $settings['additional_meta_taxonomies'] : 'thegem_portfolios';
							if ($meta_taxonomies == 'thegem_portfolios') {
								$behavior = isset($settings['additional_meta_click_behavior_meta']) ? $settings['additional_meta_click_behavior_meta'] : 'filtering';
							} else {
								$behavior = isset($settings['additional_meta_click_behavior']) ? $settings['additional_meta_click_behavior'] : 'filtering';
							}
						} else {
							$behavior = $settings['additional_meta_click_behavior_meta'];
						}
						if ($behavior == 'filtering') {
							$has_meta_filtering = true;
						}
					}
					$post_type = 'thegem_pf_item';  ?>

					<div class="portfolio-row-outer <?php if ($settings['columns_desktop'] == '100%' || $settings['fullwidth_section_sorting'] == 'yes') { ?>fullwidth-block no-paddings<?php } ?>">
					
						<?php if ($settings['portfolio_show_filter'] == 'yes' && $settings['filter_type'] == 'extended' && $settings['filters_style'] == 'sidebar') { ?>
						<div class="with-filter-sidebar <?php echo $settings['sidebar_sticky'] == 'yes' ? 'sticky-sidebar' : ''; ?>">
							<?php if ($settings['sidebar_sticky'] == 'yes') {
								wp_enqueue_script('thegem-sticky');
							} ?>
							<div class="filter-sidebar <?php echo $settings['portfolio_show_sorting'] == 'yes' ? 'left' : ''; ?>">
								<?php include(locate_template(array('gem-templates/portfolio/filters.php'))); ?>
							</div>
							<div class="content">
								<?php } ?>
								<?php if (($settings['portfolio_show_filter'] == 'yes' || $settings['portfolio_show_sorting'] == 'yes' || $has_meta_filtering)) {
									if ($settings['columns_desktop'] == '100%' || $settings['fullwidth_section_sorting'] == 'yes') {
										if (isset($settings['image_gaps']['size']) && $settings['image_gaps']['size'] < 21) { ?>
											<style>
												.elementor-element-<?php echo $widget_uid; ?> .fullwidth-block .portfolio-top-panel:not(.gem-sticky-block),
												.elementor-element-<?php echo $widget_uid; ?> .portfolio-item.not-found .found-wrap {
													padding-left: 21px !important;
													padding-right: 21px !important;
												}

												.elementor-element-<?php echo $widget_uid; ?> .with-filter-sidebar .filter-sidebar {
													padding-left: 21px !important;
												}
											</style>
										<?php }
										if (isset($settings['image_gaps_tablet']['size']) && $settings['image_gaps_tablet']['size'] < 21) { ?>
											<style>
												@media (min-width: 768px) and (max-width: 1024px) {
													.elementor-element-<?php echo $widget_uid; ?> .fullwidth-block .portfolio-top-panel:not(.gem-sticky-block),
													.elementor-element-<?php echo $widget_uid; ?> .portfolio-item.not-found .found-wrap {
														padding-left: 21px !important;
														padding-right: 21px !important;
													}

													.elementor-element-<?php echo $widget_uid; ?> .with-filter-sidebar .filter-sidebar {
														padding-left: 21px !important;
													}
												}
											</style>
										<?php }
										if (isset($settings['image_gaps_mobile']['size']) && $settings['image_gaps_mobile']['size'] < 21) { ?>
											<style>
												@media (max-width: 767px) {
													.elementor-element-<?php echo $widget_uid; ?> .fullwidth-block .portfolio-top-panel:not(.gem-sticky-block),
													.elementor-element-<?php echo $widget_uid; ?> .portfolio-item.not-found .found-wrap {
														padding-left: 21px !important;
														padding-right: 21px !important;
													}

													.elementor-element-<?php echo $widget_uid; ?> .with-filter-sidebar .filter-sidebar {
														padding-left: 21px !important;
													}
												}
											</style>
										<?php }
									} ?>
									<div class="portfolio-top-panel filter-type-<?php echo $settings['filter_type'];
									echo $settings['filter_type'] == 'extended' && $settings['filters_style'] == 'sidebar' ? ' sidebar-filter' : '';
									echo ($settings['portfolio_show_sorting'] != 'yes' && !$search_right && ($settings['portfolio_show_filter'] != 'yes' || ($settings['filter_type'] == 'extended' && $settings['filters_style'] != 'standard'))) ? ' selected-only' : '';
									echo $search_right ? ' panel-with-search' : '';
									echo $settings['filters_sticky'] == 'yes' ? ' filters-top-sticky' : '';
									echo $settings['mobile_dropdown'] == 'yes' ? ' filters-mobile-dropdown' : ''; ?>">
										<?php if ($settings['filters_sticky'] == 'yes') {
											wp_enqueue_script('thegem-sticky');
										} ?>
										<div class="portfolio-top-panel-row<?php echo $settings['filter_type'] == 'default' ? ' filter-style-' . $settings['filter_style'] : ''; ?>">
											<?php if ($settings['portfolio_show_filter'] == 'yes') {
												if ($settings['filter_type'] == 'default') {
													if ($settings['filter_by'] != 'thegem_portfolios') {
														$categories_filter = isset($portfolios_filters_tax_url['tax_' . $settings['filter_by']]) ? $portfolios_filters_tax_url['tax_' . $settings['filter_by']] : [];
													}
													if (!is_wp_error($terms) && count($terms) > 0) { ?>
														<div class="portfolio-top-panel-left <?php echo strtolower($settings['attribute_query_type']) == 'and' ? 'multiple' : 'single'; ?>" <?php if ($settings['filter_by'] !== 'thegem_portfolios') { ?>
															data-filter-by="<?php echo 'tax_'.$settings['filter_by']; ?>"
														<?php } ?>>
															<?php
															if ($settings['mobile_dropdown'] == 'yes') { ?>
																<div class="portfolio-filters portfolio-filters-mobile portfolio-filters-more">
																	<div class="portfolio-filters-more-button title-h6">
																		<div class="portfolio-filters-more-button-name"><?php echo $settings['filters_mobile_show_button_text']; ?></div>
																		<span class="portfolio-filters-more-button-arrow"></span>
																	</div>
																	<div class="portfolio-filters-more-dropdown">
																		<?php if ($settings['filter_show_all'] == 'yes') { ?>
																			<a href="#" data-filter="*"
																			   class="<?php echo empty($categories_filter) ? 'active' : ''; ?> all title-h6 <?php echo $settings['hover_pointer'] == 'yes' ? 'hover-pointer' : ''; ?>">
																					<span <?php echo $settings['filter_style'] == 'buttons' ? 'class="light"' : ''; ?>>
																						<?php echo $settings['show_all_button_text']; ?>
																					</span>
																			</a>
																		<?php }
																		foreach ($terms as $term) {
																			if ($settings['attribute_click_behavior'] == 'archive_link') {
																				$link = get_term_link($term->slug, $settings['filter_by']);
																			} else {
																				$link = '#';
																			} ?>
																			<a href="<?php echo $link; ?>" <?php if ($settings['attribute_click_behavior'] !== 'archive_link') { ?>
																			   data-filter=".<?php echo $term->slug; ?>"
																			   <?php } ?>class="<?php echo in_array($term->slug, $categories_filter) ? 'active' : ''; ?> title-h6 <?php echo $settings['hover_pointer'] == 'yes' ? 'hover-pointer' : ''; ?>">
																				<span <?php echo $settings['filter_style'] == 'buttons' ? 'class="light"' : ''; ?>>
																					<?php echo $term->name; ?>
																				</span>
																			</a>
																		<?php } ?>
																	</div>
																</div>
															<?php } ?>
															<div class="portfolio-filters filter-by-<?php echo $settings['filter_by']; ?>">
																<?php if ($settings['filter_show_all'] == 'yes') { ?>
																	<a href="#" data-filter="*" class="<?php echo empty($categories_filter) ? 'active' : ''; ?> all title-h6 <?php echo $settings['hover_pointer'] == 'yes' ? 'hover-pointer' : ''; ?>">
																		<span <?php echo $settings['filter_style'] == 'buttons' ? 'class="light"' : ''; ?>>
																			<?php echo $settings['show_all_button_text']; ?>
																		</span>
																	</a>
																<?php }
																$i = 0;
																foreach ($terms as $term) {
																	if ($settings['truncate_filters'] == 'yes' && $i == $settings['truncate_filters_number']) { ?>
																		<div class="portfolio-filters-more">
																			<div class="portfolio-filters-more-button title-h6">
																				<div class="portfolio-filters-more-button-name <?php echo $settings['filter_style'] == 'buttons' ? 'light' : ''; ?>"><?php echo $settings['filters_more_button_text']; ?></div>
																				<?php if ($settings['filters_more_button_arrow'] == 'yes') { ?>
																					<span class="portfolio-filters-more-button-arrow"></span>
																				<?php } ?>
																			</div>
																			<div class="portfolio-filters-more-dropdown">
																	<?php }
																	if ($settings['attribute_click_behavior'] == 'archive_link') {
																		$link = get_term_link($term->slug, $settings['filter_by']);
																	} else {
																		$link = '#';
																	} ?>
																	<a href="<?php echo $link; ?>" <?php if ($settings['attribute_click_behavior'] !== 'archive_link') { ?>
																		data-filter=".<?php echo $term->slug; ?>"
																	<?php } ?>class="<?php echo in_array($term->slug, $categories_filter) ? 'active' : ''; ?> title-h6 <?php echo $settings['hover_pointer'] == 'yes' ? 'hover-pointer' : ''; ?>">
																		<?php if (get_option('portfoliosets_' . $term->term_id . '_icon_pack') && get_option('portfoliosets_' . $term->term_id . '_icon')) {
																			echo thegem_build_icon(get_option('portfoliosets_' . $term->term_id . '_icon_pack'), get_option('portfoliosets_' . $term->term_id . '_icon'));
																		} ?>
																		<span <?php echo $settings['filter_style'] == 'buttons' ? 'class="light"' : ''; ?>>
																			<?php echo $term->name; ?>
																		</span>
																	</a>
																	<?php if ($settings['truncate_filters'] == 'yes' && sizeof($terms) > $settings['truncate_filters_number'] && $i == sizeof($terms) - 1) { ?>
																			</div>
																		</div>
																	<?php }
																	$i++;
																} ?>
															</div>
															<?php if ($settings['filter_style'] == 'buttons') {
																wp_enqueue_script('jquery-dlmenu'); ?>
																<div class="portfolio-filters-resp <?php echo strtolower($settings['attribute_query_type']) == 'and' ? 'multiple' : 'single'; ?>">
																	<button class="menu-toggle dl-trigger">
																		<?php _e('Portfolio filters', 'thegem'); ?>
																		<?php if ($settings['filter_responsive_icon'] && $settings['filter_responsive_icon']['value']) {
																			Icons_Manager::render_icon($settings['filter_responsive_icon'], ['aria-hidden' => 'true']);
																		} else { ?>
																			<span class="menu-line-1"></span>
																			<span class="menu-line-2"></span>
																			<span class="menu-line-3"></span>
																		<?php } ?>
																	</button>
																	<ul class="dl-menu">
																		<?php if ($settings['filter_show_all'] == 'yes') { ?>
																			<li>
																				<a href="#" data-filter="*">
																					<?php echo $settings['show_all_button_text']; ?>
																				</a>
																			</li>
																		<?php } ?>
																		<?php foreach ($terms as $term) { ?>
																			<li>
																				<?php if ($settings['attribute_click_behavior'] == 'archive_link') {
																					$link = get_term_link($term->slug, $settings['filter_by']);
																				} else {
																					$link = '#';
																				} ?>
																				<a href="<?php echo $link; ?>" <?php if ($settings['attribute_click_behavior'] !== 'archive_link') { ?>
																				   data-filter=".<?php echo $term->slug; ?>"<?php } ?>>
																					<?php echo $term->name; ?>
																				</a>
																			</li>
																		<?php } ?>
																	</ul>
																</div>
															<?php } ?>
														</div>
													<?php }
												} else { ?>
													<div class="portfolio-top-panel-left <?php echo esc_attr($settings['filter_buttons_standard_alignment']); ?>">
														<?php if ($settings['portfolio_show_filter'] == 'yes' && $settings['filters_style'] != 'sidebar') {
															include(locate_template(array('gem-templates/portfolio/filters.php')));
														}
														if (($settings['portfolio_show_filter'] == 'yes' && $settings['filters_style'] == 'sidebar') || $settings['portfolio_show_filter'] != 'yes') {
															$selected_shown = true;
															include(locate_template(array('gem-templates/portfolio/selected-filters.php')));
														} ?>
													</div>
												<?php }
											} else { ?>
												<div class="portfolio-top-panel-left"><?php
//													if ($has_meta_filtering) {
														$selected_shown = true;
														include(locate_template(array('gem-templates/portfolio/selected-filters.php')));
//													} ?>
												</div>
											<?php } ?>
											<?php if ($has_right_panel) { ?>
												<div class="portfolio-top-panel-right">
													<?php if ($settings['portfolio_show_sorting'] == 'yes') {
														if ($settings['filter_type'] == 'default' && $settings['filter_style'] == 'buttons') { ?>
															<div class="portfolio-sorting title-h6">
																<div class="orderby light">
																	<label for="" data-value="date"><?php echo $settings['sorting_date_text']; ?></label>
																	<a href="javascript:void(0);" class="sorting-switcher" data-current="date"></a>
																	<label for="" data-value="title"><?php echo $settings['sorting_name_text']; ?></label>
																</div>
																<div class="portfolio-sorting-sep"></div>
																<div class="order light">
																	<label for="" data-value="desc"><?php echo $settings['sorting_desc_text']; ?></label>
																	<a href="javascript:void(0);" class="sorting-switcher" data-current="desc"></a>
																	<label for="" data-value="asc"><?php echo $settings['sorting_asc_text']; ?></label>
																</div>
															</div>
														<?php } else {
															if ($settings['sorting_type'] == 'extended') {
																if (!empty($settings['repeater_sort'])) { ?>
																	<div class="portfolio-sorting-select open-dropdown-<?php
																	echo $settings['sorting_dropdown_open']; ?>">
																		<div class="portfolio-sorting-select-current">
																			<div class="portfolio-sorting-select-name">
																				<?php
																				if ($selected_orderby == 'default') {
																					echo esc_html($settings['sorting_extended_text']);
																				} else {
																					foreach ($settings['repeater_sort'] as $item) {
																						if (in_array($item['sort_by'], ['date', 'title'])) {
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
																						if ($selected_orderby == $sort_by && $selected_order == $item['sort_order']) {
																							echo esc_html($item['title']);
																							break;
																						}
																					}
																				} ?>
																			</div>
																			<span class="portfolio-sorting-select-current-arrow"></span>
																		</div>
																		<ul>
																			<li class="default <?php echo $selected_orderby == 'default' ? 'portfolio-sorting-select-current' : ''; ?>"
																				data-orderby="default" data-order="default">
																				<?php echo esc_html($settings['sorting_extended_text']); ?>
																			</li>
																			<?php foreach ($settings['repeater_sort'] as $item) {
																				if (in_array($item['sort_by'], ['date', 'title'])) {
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
																				<li class="<?php echo $selected_orderby == $sort_by && $selected_order == $item['sort_order'] ? 'portfolio-sorting-select-current' : ''; ?>"
																					data-orderby="<?php echo esc_attr($sort_by); ?>" data-order="<?php echo esc_attr($item['sort_order']); ?>">
																					<?php echo esc_html($item['title']); ?>
																				</li>
																			<?php } ?>
																		</ul>
																	</div>
																<?php }
															} else { ?>
																<div class="portfolio-sorting-select">
																	<div class="portfolio-sorting-select-current">
																		<div class="portfolio-sorting-select-name">
																			<?php
																			switch ($selected_orderby) {
																				case "title-asc":
																					echo esc_html($settings['sorting_extended_dropdown_title_text']);
																					break;
																				case "title-desc":
																					echo esc_html($settings['sorting_extended_dropdown_title_desc_text']);
																					break;
																				case "date-desc":
																					echo esc_html($settings['sorting_extended_dropdown_latest_text']);
																					break;
																				case "date-asc":
																					echo esc_html($settings['sorting_extended_dropdown_oldest_text']);
																					break;
																				default:
																					echo esc_html($settings['sorting_extended_text']);
																			} ?>
																		</div>
																		<span class="portfolio-sorting-select-current-arrow"></span>
																	</div>
																	<ul>
																		<li class="default <?php echo $selected_orderby == 'default' ? 'portfolio-sorting-select-current' : ''; ?>"
																			data-orderby="<?php echo esc_attr($settings['orderby']); ?>"
																			data-order="<?php echo esc_attr($settings['order']); ?>"><?php echo esc_html($settings['sorting_extended_text']); ?></li>
																		<li class="<?php echo $selected_orderby == 'title-asc' ? 'portfolio-sorting-select-current' : ''; ?>"
																			data-orderby="title" data-order="asc"><?php echo esc_html($settings['sorting_extended_dropdown_title_text']); ?>
																		</li>
																		<li class="<?php echo $selected_orderby == 'title-desc' ? 'portfolio-sorting-select-current' : ''; ?>"
																			data-orderby="title" data-order="desc"><?php echo esc_html($settings['sorting_extended_dropdown_title_desc_text']); ?>
																		</li>
																		<li class="<?php echo $selected_orderby == 'date-desc' ? 'portfolio-sorting-select-current' : ''; ?>"
																			data-orderby="date" data-order="desc"><?php echo esc_html($settings['sorting_extended_dropdown_latest_text']); ?>
																		</li>
																		<li class="<?php echo $selected_orderby == 'date-asc' ? 'portfolio-sorting-select-current' : ''; ?>"
																			data-orderby="date" data-order="asc"><?php echo esc_html($settings['sorting_extended_dropdown_oldest_text']); ?>
																		</li>
																	</ul>
																</div>
														<?php }
														}
													} ?>

													<?php if ($search_right) { ?>
														<span>&nbsp;</span>
														<form class="portfolio-search-filter<?php echo $settings['filters_style'] != 'standard' ? ' mobile-visible' : '';
														echo $settings['live_search'] == 'yes' ? ' live-search' : '';
														echo $settings['search_reset_filters'] == 'yes' ? ' reset-filters' : '';
														echo $settings['show_search_as'] == 'input' ? ' input-style' : ''; ?>"
															role="search" action="">
															<div class="portfolio-search-filter-form">
																<input type="search"
																	   placeholder="<?php echo esc_attr($settings['filters_text_labels_search_text']); ?>"
																	   value="<?php echo esc_attr($search_current); ?>">
															</div>
															<div class="portfolio-search-filter-button"></div>
														</form>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
										<?php if ($settings['filter_type'] == 'extended' && $settings['portfolio_show_filter'] == 'yes') {
											$selected_shown = true;
											include(locate_template(array('gem-templates/portfolio/selected-filters.php')));
										} ?>
									</div>
								<?php }
								if (!$selected_shown) { ?>
									<div class="portfolio-top-panel selected-only">
										<?php include(locate_template(array('gem-templates/portfolio/selected-filters.php'))); ?>
									</div>
								<?php } ?>

								<div class="row portfolio-row">
									<div class="portfolio-set clearfix"
										 data-max-row-height="<?php echo $settings['metro_max_row_height'] ? $settings['metro_max_row_height']['size'] : ''; ?>">
										<?php
										if ($settings['layout'] == 'creative') {
											$creative_blog_schemes_list = [
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
												'2' => [
													'2a' => [
														'count' => 5,
														0 => 'vertical',
													],
													'2b' => [
														'count' => 5,
														3 => 'vertical',
													],
													'2c' => [
														'count' => 4,
														0 => 'vertical',
														2 => 'vertical',
													],
													'2d' => [
														'count' => 4,
														0 => 'horizontal',
														1 => 'vertical',
													],
													'2e' => [
														'count' => 5,
														0 => 'horizontal',
													],
													'2f' => [
														'count' => 4,
														0 => 'horizontal',
														1 => 'horizontal',
													],
													'2g' => [
														'count' => 5,
														2 => 'horizontal',
													],
													'2h' => [
														'count' => 4,
														0 => 'horizontal',
														3 => 'horizontal',
													],
												]
											];
											$columns = $settings['columns_desktop'] != '100%' ? str_replace("x", "", $settings['columns_desktop']) : $settings['columns_100'];
											$items_sizes = $creative_blog_schemes_list[$columns][$settings['layout_scheme_' . $columns . 'x']];
											$items_count = $items_sizes['count'];
										}
										$i = 0;
										if ($portfolio_loop->have_posts()) {
											while ($portfolio_loop->have_posts()) {
												$portfolio_loop->the_post();
												$thegem_highlight_type_creative = null;
												if ($settings['layout'] == 'creative') {
													$thegem_highlight_type_creative = 'disabled';
													$item_num = $i % $items_count;
													if (isset($items_sizes[$item_num])) {
														$thegem_highlight_type_creative = $items_sizes[$item_num];
													}
												}
												echo thegem_portfolio_grid_render_item($settings, $item_classes, $thegem_sizes, get_the_ID(), $thegem_highlight_type_creative);
												if ($settings['layout'] == 'creative' && $i == 0) {
													echo thegem_portfolio_grid_render_item($settings, ['size-item'], $thegem_sizes);
												}
												$i++;
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

									</div><!-- .portflio-set -->
									<?php if ($settings['layout'] != 'creative') { ?>
										<div class="portfolio-item-size-container">
											<?php echo thegem_portfolio_grid_render_item($settings, $item_classes, $thegem_sizes); ?>
										</div>
									<?php } ?>
								</div><!-- .row-->

								<?php
								/** Pagination */
								if ($settings['show_pagination'] === 'yes' && $next_page_pagination > 0) {
									if ($settings['pagination_type'] == 'normal') { ?>
										<div class="portfolio-navigator gem-pagination">
											<a href="#" class="prev">
												<?php if ($settings['pagination_arrows_left_icon']['value']) {
													Icons_Manager::render_icon($settings['pagination_arrows_left_icon'], ['aria-hidden' => 'true']);
												} else { ?>
													<i class="default"></i>
												<?php } ?>
											</a>
											<div class="pages"></div>
											<a href="#" class="next">
												<?php if ($settings['pagination_arrows_right_icon']['value']) {
													Icons_Manager::render_icon($settings['pagination_arrows_right_icon'], ['aria-hidden' => 'true']);
												} else { ?>
													<i class="default"></i>
												<?php } ?>
											</a>
										</div>
									<?php } else if ($settings['pagination_type'] == 'more') {

										$separator_enabled = !empty($settings['more_show_separator']) ? true : false;

										// Container
										$classes_container = 'gem-button-container gem-widget-button ';

										if ($separator_enabled) {
											$classes_container .= 'gem-button-position-center gem-button-with-separator ';
										} else {
											if ('yes' === $settings['more_stretch_full_width']) {
												$classes_container .= 'gem-button-position-fullwidth ';
											}
										}
										$attr_container = [
											'class' => $classes_container,
										];
										$this->add_render_attribute('attr_container', $attr_container);

										// Separator
										$classes_separator = 'gem-button-separator ';

										if (!empty($settings['pagination_more_button_separator_style_active'])) {

											$classes_separator .= esc_attr($settings['pagination_more_button_separator_style_active']);
										}
										$attr_separator = [

											'class' => $classes_separator,
										];

										$this->add_render_attribute('attr_separator', $attr_separator);

										// Link

										$this->add_render_attribute(
											'button-wrap',
											[
												'class' => [
													'load-more-button gem-button',
													'gem-button-size-' . $settings['pagination_more_button_size'],
													'gem-button-style-' . $settings['pagination_more_button_type'],
													'gem-button-icon-position-' . $settings['pagination_more_button_icon_align'],
													'gem-button-text-weight-normal',
												],
											]
										); ?>

										<div class="portfolio-load-more">
											<div class="inner">
												<?php include(locate_template(array('gem-templates/portfolio/more-button.php'))); ?>
											</div>
										</div>
									<?php } else if ($settings['pagination_type'] == 'scroll') { ?>
										<div class="portfolio-scroll-pagination"></div>
									<?php }
								} ?>

								<?php if ($settings['portfolio_show_filter'] == 'yes' && $settings['filters_style'] == 'sidebar') { ?>
							</div>
						</div>
					<?php } ?>

					</div><!-- .full-width -->
				</div><!-- .portfolio-->
			</div><!-- .portfolio-preloader-wrapper-->
		<?php }

		thegem_templates_close_portfolio($this->get_name(), $this->get_title(), $portfolio_loop->have_posts());

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()) { ?>
			<script type="text/javascript">
				if (typeof widget_settings == 'undefined') {
					var widget_settings = [];
				}
				widget_settings['<?php echo $widget_uid ?>'] = <?php echo json_encode($settings) ?>;

				!function(s){function a(a,e){s(window).outerWidth()<e?a.hasClass("style-standard")?a.addClass("style-standard-mobile"):a.hasClass("style-sidebar")&&a.addClass("style-sidebar-mobile"):a.hasClass("style-standard")?a.removeClass("style-standard-mobile"):a.hasClass("style-sidebar")&&a.removeClass("style-sidebar-mobile")}s(".portfolio-filters-list").each(function(){if(!s(this).hasClass("style-hidden")&&!s(this).hasClass("prevent-hidden-mobile")){let e=s(this),t=e.data("breakpoint")?e.data("breakpoint"):992;a(s(this),t),s(window).on("resize",function(){a(e,t)})}})}(jQuery);

				(function ($) {

					setTimeout(function () {
						if (!$('.elementor-element-<?php echo $widget_uid; ?> .portfolio-grid').length) {
							return;
						}
						$('.elementor-element-<?php echo $widget_uid; ?> .portfolio-grid').initExtendedPortfolioGrids();
					}, 1000);

					elementor.channels.editor.on('change', function (view) {
						var changed = view.elementSettingsModel.changed;

						if (changed.image_gaps !== undefined || changed.caption_container_padding !== undefined ||
							changed.spacing_title !== undefined || changed.spacing_description !== undefined) {
							setTimeout(function () {
								$('.elementor-element-<?php echo $widget_uid; ?> .portfolio-grid').initExtendedPortfolioGrids();
							}, 500);
						}
					});

				})(jQuery);

			</script>
		<?php }
	}

	public function get_preset_data() {

		return array(

			'default' => array(
				'caption_container_preset' => 'white',
				'portfolio_show_likes' => 'yes',
				'title_preset' => 'default',
				'truncate_titles' => '',
				'truncate_description' => '',
			),
			'alternative' => array(
				'caption_container_preset' => 'transparent',
				'portfolio_show_likes' => '',
				'title_preset' => 'title-h5',
				'truncate_titles' => '2',
				'truncate_description' => '2',
			)
		);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_Portfolio());
