<?php

namespace TheGem_Elementor\Widgets\GalleryGrid;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use TheGem_Elementor\Group_Control_Background_Light;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Gallery Grid.
 */
#[\AllowDynamicProperties]
class TheGem_GalleryGrid extends Widget_Base {

	public function __construct($data = [], $args = null) {

		$template_type = isset($GLOBALS['thegem_template_type']) ? $GLOBALS['thegem_template_type'] : thegem_get_template_type(get_the_ID());
		$this->is_single_product_template = $template_type === 'single-product';
		$this->is_single_portfolio_template = $template_type === 'portfolio';
		$this->is_single_post_template = $template_type === 'single-post';
		$this->is_single_product = (empty($template_type) && is_singular('product')) || get_post_type() == 'product';
		$this->is_single_portfolio = (empty($template_type) && is_singular('thegem_pf_item')) || get_post_type() == 'thegem_pf_item';
		$this->is_single_post = (empty($template_type) && is_singular('post')) || get_post_type() == 'post';

		if (isset($data['settings']) && (empty($_REQUEST['action']) || !in_array($_REQUEST['action'], array('thegem_importer_process', 'thegem_templates_new', 'thegem_blocks_import')))) {

			if (!empty($data['settings']['columns']) && (empty($data['settings']['layout']) || $data['settings']['layout'] != 'creative')) {
				$data['settings']['columns_desktop'] = $data['settings']['columns'];
			}
			unset($data['settings']['columns']);

			if (!isset($data['settings']['source_type'])) {
				if (empty($data['settings']['galleries'])) {
					if ($this->is_single_product_template) {
						$data['settings']['source_type'] = 'product-gallery';
					} else if ($this->is_single_product_template) {
						$data['settings']['source_type'] = 'portfolio-gallery';
					} else if ($this->is_single_product_template) {
						$data['settings']['source_type'] = 'portfolio-gallery';
					}
				} else {
					$data['settings']['source_type'] = 'custom';
				}
			}
		}

		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_script('thegem-gallery-grid-scripts', THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_URL . '/assets/js/thegem-gallery-grid.js', array('jquery'), null, true);

	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-gallery-grid';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Gallery Grid', 'thegem');
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-post') {
			return ['thegem_single_post_builder'];
		}
		return ['thegem_elements'];
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
				'thegem-hovers-zoom-overlay',
				'thegem-hovers-disabled',
				'thegem-portfolio',
				'thegem-portfolio-filters-list',
				'thegem-gallery-grid-styles'];
		}
		return ['thegem-gallery-grid-styles'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-animations',
				'thegem-items-animations',
				'thegem-scroll-monitor',
				'thegem-isotope-metro',
				'thegem-isotope-masonry-custom',
				'thegem-gallery-grid-scripts'];
		}
		return ['thegem-gallery-grid-scripts'];
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
			'default' => __('No border (default)', 'thegem'),
			'1' => __('8px and border', 'thegem'),
			'2' => __('16px and border', 'thegem'),
			'3' => __('8px outlined border', 'thegem'),
			'4' => __('20px outlined border', 'thegem'),
			'5' => __('20px border with shadow', 'thegem'),
			'6' => __('Combined border', 'thegem'),
			'7' => __('20px border radius', 'thegem'),
			'8' => __('55px border radius', 'thegem'),
			'9' => __('Dashed inside', 'thegem'),
			'10' => __('Dashed outside', 'thegem'),
			'11' => __('Rounded with border', 'thegem'),
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
			'columns_desktop',
			[
				'label' => __('Columns Desktop', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '3x',
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
			'layout',
			[
				'label' => __('Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'justified',
				'options' => [
					'justified' => __('Justified Grid', 'thegem'),
					'masonry' => __('Masonry Grid', 'thegem'),
					'metro' => __('Metro Style', 'thegem'),
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
				],
				'condition' => [
					'columns_desktop' => '100%',
				],
				'description' => __('Number of columns for 100% width grid starting from 1920px resolutions', 'thegem'),
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
				'condition' => [
					'layout' => 'justified',
				]
			]
		);

		$this->add_control(
			'equal_height', [
				'label' => __('Equal Images Height', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout' => 'justified',
					'image_size' => 'full',
				],
				'description' => __('Equal height of full size images in a grid row. Works only in case no image ratio is specified', 'thegem'),
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
					'{{WRAPPER}} .gem-gallery-grid .gallery-item:not(.custom-ratio, .double-item) .image-wrap' => 'aspect-ratio: {{SIZE}} !important;',
				],
				'description' => __('Leave blank to show the original image ratio', 'thegem'),
				'condition' => [
					'layout' => 'justified',
					'image_size' => 'full',
					'equal_height' => '',
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
					'{{WRAPPER}} .gem-gallery-grid .gallery-item:not(.custom-ratio, .double-item) .image-wrap' => 'aspect-ratio: {{SIZE}} !important;',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Content', 'thegem'),
			]
		);

		if ($this->is_single_product || $this->is_single_product_template) {
			$this->add_control(
				'source_type',
				[
					'label' => __('Source', 'thegem'),
					'default' => $this->is_single_product_template ? 'product-gallery' : 'custom',
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'options' => [
						'product-gallery' => __('Product Gallery', 'thegem'),
						'custom' => __('Custom Selection', 'thegem'),
					],
				]
			);

		} else if ($this->is_single_portfolio || $this->is_single_portfolio_template) {
			$this->add_control(
				'source_type',
				[
					'label' => __('Source', 'thegem'),
					'default' => $this->is_single_portfolio_template ? 'portfolio-gallery' : 'custom',
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'options' => [
						'portfolio-gallery' => __('Project Gallery', 'thegem'),
						'custom' => __('Custom Selection', 'thegem'),
					],
				]
			);

		} else if ($this->is_single_post || $this->is_single_post_template) {
			$this->add_control(
				'source_type',
				[
					'label' => __('Source', 'thegem'),
					'default' => $this->is_single_post_template ? 'portfolio-gallery' : 'custom',
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'options' => [
						'portfolio-gallery' => __('Post Gallery', 'thegem'),
						'custom' => __('Custom Selection', 'thegem'),
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

		$repeater = new Repeater();

		$repeater->add_control(
			'gallery_title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Title', 'thegem'),
				'default' => __('New Gallery', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'multiple_gallery',
			[
				'type' => Controls_Manager::GALLERY,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'galleries',
			[
				'type' => Controls_Manager::REPEATER,
				'label' => __('Galleries', 'thegem'),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ gallery_title }}}',
				'condition' => [
					'source_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'hover_effect',
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
			]
		);

		$this->add_control(
			'title_show', [
				'label' => __('Title', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'description_show', [
				'label' => __('Description', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'icon_show', [
				'label' => __('Hover Icon', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'hover_effect!' => ['zoom-overlay', 'disabled'],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filters',
			[
				'label' => __('Filters', 'thegem'),
			]
		);
		
		$this->add_control(
			'show_filter', [
				'label' => __('Show Filters', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
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
				'condition' => [
					'show_filter' => 'yes',
				],
				'frontend_available' => true,
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
					'show_filter' => 'yes',
					'filter_style!' => 'buttons',
				],
			]
		);

		$this->add_control(
			'filter_query_type',
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
					'show_filter' => 'yes',
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
				'condition' => [
					'show_filter' => 'yes',
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
				'condition' => [
					'show_filter' => 'yes',
					'filters_sticky' => 'yes',
				],
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
					'show_filter' => 'yes',
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
					'show_filter' => 'yes',
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
					'show_filter' => 'yes',
					'truncate_filters' => 'yes',
				],
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
				'condition' => [
					'show_filter' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_options',
			[
				'label' => __('Options', 'thegem'),
			]
		);

		$this->add_control(
			'loading_animation',
			[
				'label' => __('Loading Animation', 'thegem'),
				'default' => 'no',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
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
				'condition' => [
					'loading_animation' => 'yes',
				],
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
			'ignore_highlights',
			[
				'label' => __('Ignore Highlighted Images', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'conditions' => [
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
									'operator' => '=',
									'value' => 'justified',
								],
								[
									'name' => 'disable_preloader',
									'operator' => '!=',
									'value' => 'yes',
								],
							]
						],
					]
				]
			]
		);

		$this->add_control(
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
				'description' => __('Metro grid auto sets the row\'s height. Specify max. allowed height for best appearance (380px recommended).', 'thegem'),
				'condition' => [
					'layout' => 'metro',
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

		$this->end_controls_section();

		$this->add_styles_controls($this);
	}

	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls($control) {
		$this->control = $control;

		/* Container Style */
		$this->container_style($control);

		/* Image Style */
		$this->image_style($control);

		/* Inner Border Style */
		$this->inner_border_style($control);

		/* Filter Buttons Style */
		$this->filter_buttons_style($control);
	}

	/**
	 * Image Styles
	 * @access protected
	 */
	protected function image_style($control) {
		$control->start_controls_section(
			'image_style',
			[
				'label' => __('Image Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .gallery-item .image-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gallery-item .overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item .wrap',
			]
		);

		$control->start_controls_tabs('image_tabs');
		$control->start_controls_tab('image_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
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
					'{{WRAPPER}} .gallery-item .overlay-wrap' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item img',
			]
		);

		$control->add_control(
			'image_blend_mode_normal',
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
					'{{WRAPPER}} .gallery-item .overlay-wrap' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('image_tabs_hover', ['label' => __('Hover', 'thegem'),]);

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
							'{{WRAPPER}} .gallery-item .overlay:before, .hover-circular .gallery-item .overlay-wrap .overlay .overlay-circle' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .gallery-item .overlay:before, .hover-circular .gallery-item .overlay-wrap .overlay .overlay-circle' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .gallery-item .overlay:before, .hover-circular .gallery-item .overlay-wrap .overlay .overlay-circle' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				]

			]
		);

		$control->remove_control('image_hover_overlay_image');

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'hover_css',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item .overlay-wrap:hover img',
			]
		);

		$control->add_control(
			'hover_blend_mode',
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
					'{{WRAPPER}} .gallery-item:hover .overlay-wrap' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$control->add_control(
			'iconheader',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'icon_show' => 'yes',
					'hover_effect!' => ['zoom-overlay', 'disabled'],
				],
			]
		);

		$control->add_control(
			'icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'icon_show' => 'yes',
					'hover_effect!' => ['zoom-overlay', 'disabled'],
				],
			]
		);

		$control->add_responsive_control(
			'icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gallery-item .overlay a.icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gallery-item .overlay .overlay-line' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'icon_show' => 'yes',
					'hover_effect!' => ['zoom-overlay', 'disabled'],
				],
			]
		);

		$control->add_control(
			'icon_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gallery-item .overlay a.icon i' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'icon_show' => 'yes',
					'hover_effect' => 'zooming-blur'
				],
			]
		);



		$control->add_responsive_control(
			'icon_size',
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
					'{{WRAPPER}} .gallery-item .overlay a.icon i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-gallery-grid:not(.hover-zooming-blur):not(.hover-gradient) .gallery-item .overlay a.icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-gallery-grid.hover-zooming-blur .gallery-item  .overlay a.icon i' => 'font-size: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .gem-gallery-grid.hover-gradient .gallery-item  .overlay a.icon i' => 'font-size: calc({{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'icon_show' => 'yes',
				],
			]
		);

		$control->add_control(
			'icon_rotate',
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
					'{{WRAPPER}} .gallery-item .overlay a.icon i' => 'display: inline-block; transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'icon_show' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

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
	 * Container Style
	 * @access protected
	 */
	protected function container_style($control) {
		$control->start_controls_section(
			'container_style',
			[
				'label' => __('Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'container_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .gallery-item .wrap',
			]
		);

		$control->remove_control('container_background_image');

		$control->add_responsive_control(
			'container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gallery-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .gem-gallery-grid .gallery-item .wrap',
			]
		);

		$control->add_responsive_control(
			'container_margin',
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
					'size' => 16,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-grid .gallery-item' => 'padding: calc({{SIZE}}{{UNIT}}/2) !important;',
					'{{WRAPPER}} .gem-gallery-grid .gallery-set' => 'margin-top: calc(-{{SIZE}}{{UNIT}}/2); margin-bottom: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .gem-gallery-grid .not-fullwidth-block ul,
					{{WRAPPER}} .gem-gallery-grid .not-fullwidth-block .portfolio-item-size-container' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .gem-gallery-grid .fullwidth-block' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',
				],
				'frontend_available' => true,
			]
		);

		$control->add_responsive_control(
			'container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gallery-item .wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item .wrap',
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Inner Border Style
	 * @access protected
	 */
	protected function inner_border_style($control) {
		$control->start_controls_section(
			'inner_style',
			[
				'label' => __('Inner Border Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'thegem_elementor_preset',
									'operator' => '=',
									'value' => '9'
								],
								[
									'name' => 'thegem_elementor_preset',
									'operator' => '=',
									'value' => '11'
								],
							],
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'inner_spacing',
			[
				'label' => __('Inner Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
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
					'{{WRAPPER}} .gallery-item .gem-wrapbox-inner:after' => 'top: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}}; bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gallery-item .overlay-wrap:after' => 'top: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}}; bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'inner_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item .gem-wrapbox-inner:after, {{WRAPPER}} .gallery-item .overlay-wrap:after',
			]
		);

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
					'show_filter' => 'yes',
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
				'selectors' => [
					'{{WRAPPER}} .portfolio .portfolio-filters' => 'text-align: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio .portfolio-filters a,
					{{WRAPPER}} .portfolio .portfolio-filters div.portfolio-filters-more-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .portfolio .portfolio-filters a,
					{{WRAPPER}} .portfolio .portfolio-filters div.portfolio-filters-more-button',
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
					'{{WRAPPER}} .portfolio .portfolio-filters a,
					{{WRAPPER}} .portfolio .portfolio-filters div.portfolio-filters-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .portfolio-top-panel' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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
							'{{WRAPPER}} .portfolio .portfolio-filters a' . $state . ',
							{{WRAPPER}} .portfolio .portfolio-filters div.portfolio-filters-more-button' . $state => 'background-color: {{VALUE}};',
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
							'{{WRAPPER}} .portfolio .portfolio-filters a' . $state . ',
							{{WRAPPER}} .portfolio .portfolio-filters div.portfolio-filters-more-button' . $state => 'border-color: {{VALUE}};',
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
							'{{WRAPPER}} .portfolio .portfolio-filters a' . $state . ',
							{{WRAPPER}} .portfolio .portfolio-filters div.portfolio-filters-more-button' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'filter_buttons_text_typography' . $stkey,
						'selector' => '{{WRAPPER}} .portfolio .portfolio-filters a' . $state . ' span,
							{{WRAPPER}} .portfolio .portfolio-filters div.portfolio-filters-more-button' . $state,
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
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'truncate_filters',
							'value' => 'yes',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'filter_style',
									'operator' => '!=',
									'value' => 'buttons',
								],
								[
									'name' => 'mobile_dropdown',
									'value' => 'yes',
								],
							]
						]
					]
				]
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'filters_more_button_dropdown_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio-filters-more .portfolio-filters-more-dropdown',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'truncate_filters',
							'value' => 'yes',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'filter_style',
									'operator' => '!=',
									'value' => 'buttons',
								],
								[
									'name' => 'mobile_dropdown',
									'value' => 'yes',
								],
							]
						]
					]
				]
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
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'truncate_filters',
							'value' => 'yes',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'filter_style',
									'operator' => '!=',
									'value' => 'buttons',
								],
								[
									'name' => 'mobile_dropdown',
									'value' => 'yes',
								],
							]
						]
					]
				]
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filters_more_button_dropdown_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-more .portfolio-filters-more-dropdown',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'truncate_filters',
							'value' => 'yes',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'filter_style',
									'operator' => '!=',
									'value' => 'buttons',
								],
								[
									'name' => 'mobile_dropdown',
									'value' => 'yes',
								],
							]
						]
					]
				]
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
					'{{WRAPPER}} .portfolio .portfolio-filters-resp .menu-toggle i' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio .portfolio-filters-resp .menu-toggle i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio .portfolio-filters-resp .menu-toggle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .portfolio .portfolio-filters-resp ul li a',
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
					'{{WRAPPER}} .portfolio .portfolio-filters-resp ul li a' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio .portfolio-filters-resp ul li' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio .portfolio-filters-resp ul li, {{WRAPPER}} .portfolio .portfolio-filters-resp ul' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'filter_style' => 'buttons',
				]
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
		$settings = $this->get_settings_for_display();
		$widget_uid = $this->get_id();
		$gallery_uid_url = 'grid_' . $widget_uid . '-';

		wp_enqueue_style('thegem-hovers-' . $settings['hover_effect']);

		if ($settings['loading_animation'] === 'yes') {
			wp_enqueue_style('thegem-animations');
			wp_enqueue_script('thegem-items-animations');
			wp_enqueue_script('thegem-scroll-monitor');
		}

		if ($settings['disable_preloader'] == 'yes' || $settings['equal_height'] == 'yes') {
			$settings['ignore_highlights'] = 'yes';
		}

		if ($settings['layout'] !== 'justified' || $settings['ignore_highlights'] !== 'yes') {

			if ($settings['layout'] == 'metro') {
				wp_enqueue_script('thegem-isotope-metro');
			} else {
				wp_enqueue_script('thegem-isotope-masonry-custom');
			}
		}

		$is_metro = 'metro' === $settings['layout'];
		$is_masonry = 'masonry' === $settings['layout'];

		$active_filter = [];
		if ($settings['show_filter'] == 'yes') {
			if (isset($_GET[$gallery_uid_url . 'filter'])) {
				$active_filter = explode(",", $_GET[$gallery_uid_url . 'filter']);
			} else if ($settings['filter_show_all'] != 'yes') {
				$active_filter = ['gallery-0'];
			}
		}

		$this->add_render_attribute(
			'main-slider-wrap',
			[
				'class' => [
					'gem-gallery-grid',
					' gallery-style-' . $settings['layout'],
					' hover-' . $settings['hover_effect'],
					($is_metro ? ' metro metro-item-style-' . $settings['thegem_elementor_preset'] . ' without-padding' : ' gaps-margin'),
					($is_masonry ? ' gallery-items-masonry' : ''),
					($settings['columns_desktop'] != '100%' ? 'columns-' . str_replace("x", "", $settings['columns_desktop']) : ''),
					'columns-tablet-' . str_replace("x", "", $settings['columns_tablet']),
					'columns-mobile-' . str_replace("x", "", $settings['columns_mobile']),
					($settings['loading_animation'] == 'yes' ? 'loading-animation' : ''),
					($settings['loading_animation'] == 'yes' && $settings['loading_animation_mobile'] == 'yes' ? 'enable-animation-mobile' : ''),
					($settings['loading_animation'] == 'yes' && $settings['animation_effect'] ? 'item-animation-' . $settings['animation_effect'] : ''),
					($settings['columns_desktop'] == '100%' ? ' fullwidth-columns fullwidth-columns-' . intval($settings['columns_100']) : ''),
					(Plugin::$instance->editor->is_edit_mode() ? 'lazy-loading-not-hide' : ''),
					($settings['layout'] == 'justified' && $settings['ignore_highlights'] == 'yes' ? 'disable-isotope' : ''),
					($settings['layout'] == 'justified' && $settings['image_size'] == 'full' && $settings['equal_height'] == 'yes' ? 'equal-height' : ''),
					(($settings['image_size'] == 'full' && empty($settings['image_ratio']['size']) || !in_array($settings['image_size'], ['full', 'default'])) ? 'full-image' : ''),
					($settings['ajax_preloader_type'] == 'minimal' ? 'minimal-preloader' : ''),
				],
				'data-uid' => esc_attr($widget_uid),
				'data-hover' => esc_attr($settings['hover_effect']),
				'data-filter' => esc_attr(json_encode($active_filter)),
			]
		);

		ob_start();
		$gallery_items = [];

		if ($settings['source_type'] == 'product-gallery') {

			$product = thegem_templates_init_product();
			if (!empty($product)) {
				$gallery_items = $product->get_gallery_image_ids();
				if ('variable' === $product->get_type()) {
					foreach ($product->get_available_variations() as $variation) {
						if (has_post_thumbnail($variation['variation_id'])) {
							$thumbnail_id = get_post_thumbnail_id($variation['variation_id']);
							if (!in_array($thumbnail_id, $gallery_items)) {
								$gallery_items[] = $thumbnail_id;
							}
						}
					}
				}
			}

		} else if ($settings['source_type'] == 'portfolio-gallery') {

			$portfolio = thegem_templates_init_portfolio();
			if (!empty($portfolio)) {
				$gallery_items = get_post_meta($portfolio->ID, 'thegem_portfolio_item_gallery_images', true);
				if($portfolio->post_type !== 'thegem_pf_item') {
					$gallery_items = get_post_meta($portfolio->ID, 'thegem_post_item_gallery_images', true);
				}
				$gallery_items = !empty($gallery_items) ? explode(',', $gallery_items) : array();
			}

		} else {

			$galleries = [];
			$gallery_items_filter_classes = [];

			if (!empty($settings['galleries'])) {
				foreach ($settings['galleries'] as $gallery) :
					$galleries[] = $gallery['multiple_gallery'];
				endforeach;
			}

			foreach ($galleries as $gallery_index => $gallery) {
				foreach ($gallery as $index => $item) {
					$item['id'] = apply_filters('wpml_object_id', $item['id'], 'attachment', true);
					if (!in_array($item['id'], $gallery_items, true)) {
						$gallery_items[] = $item['id'];
					}
					if (!isset($gallery_items_filter_classes[$item['id']])) {
						$gallery_items_filter_classes[$item['id']] = ['gallery-'.$gallery_index];
					} else {
						$gallery_items_filter_classes[$item['id']][] = 'gallery-'.$gallery_index;
					}
				}
			}
		}

		if (!empty($settings['image_ratio_default']['size'])) {
			$settings['image_aspect_ratio'] = 'custom';
			$settings['image_ratio_custom'] = $settings['image_ratio_default']['size'];
		}

		$gallery_uid = uniqid();

		$item_classes = get_thegem_portfolio_render_item_classes($settings);
		$thegem_sizes = get_thegem_portfolio_render_item_image_sizes($settings);

		if (!empty($gallery_items)) {

			if ($settings['columns_desktop'] == '100%' || $settings['ignore_highlights'] !== 'yes' || $settings['layout'] !== 'justified') {
				$spin_class = 'preloader-spin';
				if ($settings['ajax_preloader_type'] == 'minimal') {
					$spin_class = 'preloader-spin-new';
				}
				echo apply_filters('thegem_portfolio_preloader_html', '<div class="preloader save-space"><div class="' . $spin_class . '"></div></div>');
			} ?>

			<div class="gallery-preloader-wrapper">
				<div class="row">

					<div <?php echo $this->get_render_attribute_string('main-slider-wrap'); ?>>

						<div class="portfolio <?php echo $settings['columns_desktop'] == '100%' ? 'fullwidth-block' : 'not-fullwidth-block'; ?> ">

							<?php if ($settings['show_filter'] == 'yes') {
								wp_enqueue_style('thegem-portfolio-filters-list');

								if ($settings['columns_desktop'] == '100%' || $settings['fullwidth_section_sorting'] == 'yes') {
									if (isset($settings['container_margin']['size']) && $settings['container_margin']['size'] < 21) { ?>
										<style>
											.elementor-element-<?php echo $widget_uid; ?> .fullwidth-block .portfolio-top-panel:not(.gem-sticky-block),
											.elementor-element-<?php echo $widget_uid; ?> .portfolio-item.not-found .found-wrap {
												padding-left: 21px !important;
												padding-right: 21px !important;
											}
										</style>
									<?php }
									if (isset($settings['container_margin_tablet']['size']) && $settings['container_margin_tablet']['size'] < 21) { ?>
										<style>
											@media (min-width: 768px) and (max-width: 1024px) {
												.elementor-element-<?php echo $widget_uid; ?> .fullwidth-block .portfolio-top-panel:not(.gem-sticky-block),
												.elementor-element-<?php echo $widget_uid; ?> .portfolio-item.not-found .found-wrap {
													padding-left: 21px !important;
													padding-right: 21px !important;
												}
											}
										</style>
									<?php }
									if (isset($settings['container_margin_mobile']['size']) && $settings['container_margin_mobile']['size'] < 21) { ?>
										<style>
											@media (max-width: 767px) {
												.elementor-element-<?php echo $widget_uid; ?> .fullwidth-block .portfolio-top-panel:not(.gem-sticky-block),
												.elementor-element-<?php echo $widget_uid; ?> .portfolio-item.not-found .found-wrap {
													padding-left: 21px !important;
													padding-right: 21px !important;
												}
											}
										</style>
									<?php }
								} ?>
								<div class="portfolio-top-panel filter-type-default<?php
								echo $settings['filters_sticky'] == 'yes' ? ' filters-top-sticky' : '';
								echo $settings['mobile_dropdown'] == 'yes' ? ' filters-mobile-dropdown' : ''; ?>">
									<?php if ($settings['filters_sticky'] == 'yes') {
										wp_enqueue_script('thegem-sticky');
									} ?>
									<div class="portfolio-top-panel-row filter-style-<?php echo $settings['filter_style']; ?>">
										<div class="portfolio-top-panel-left <?php echo strtolower($settings['filter_query_type']) == 'and' ? 'multiple' : 'single'; ?>">
											<?php if ($settings['mobile_dropdown'] == 'yes') { ?>
												<div class="portfolio-filters portfolio-filters-mobile portfolio-filters-more">
													<div class="portfolio-filters-more-button title-h6">
														<div class="portfolio-filters-more-button-name"><?php echo $settings['filters_mobile_show_button_text']; ?></div>
														<span class="portfolio-filters-more-button-arrow"></span>
													</div>
													<div class="portfolio-filters-more-dropdown">
														<?php if ($settings['filter_show_all'] == 'yes') { ?>
															<a href="#" data-filter="*" class="<?php echo empty($active_filter) ? 'active' : ''; ?> all title-h6 <?php echo $settings['hover_pointer'] == 'yes' ? 'hover-pointer' : ''; ?>">
																<span <?php echo $settings['filter_style'] == 'buttons' ? 'class="light"' : ''; ?>>
																	<?php echo $settings['show_all_button_text']; ?>
																</span>
															</a>
														<?php }
														foreach ($settings['galleries'] as $i => $gallery) { ?>
															<a href="#" data-filter="gallery-<?php echo $i; ?>"
															   class="<?php echo in_array('gallery-' . $i, $active_filter) ? 'active' : ''; ?> title-h6 <?php echo $settings['hover_pointer'] == 'yes' ? 'hover-pointer' : ''; ?>">
																<span <?php echo $settings['filter_style'] == 'buttons' ? 'class="light"' : ''; ?>>
																	<?php echo $gallery['gallery_title']; ?>
																</span>
															</a>
														<?php } ?>
													</div>
												</div>
											<?php } ?>
											<div class="portfolio-filters">
												<?php if ($settings['filter_show_all'] == 'yes') { ?>
													<a href="#" data-filter="*" class="<?php echo empty($active_filter) ? 'active' : ''; ?> all title-h6 <?php echo $settings['hover_pointer'] == 'yes' ? 'hover-pointer' : ''; ?>">
														<span <?php echo $settings['filter_style'] == 'buttons' ? 'class="light"' : ''; ?>>
															<?php echo $settings['show_all_button_text']; ?>
														</span>
													</a>
												<?php }
												foreach ($settings['galleries'] as $i => $gallery) {
													if ($settings['truncate_filters'] == 'yes' && $i == $settings['truncate_filters_number']) { ?>
														<div class="portfolio-filters-more">
														<div class="portfolio-filters-more-button title-h6">
															<div
																class="portfolio-filters-more-button-name <?php echo $settings['filter_style'] == 'buttons' ? 'light' : ''; ?>"><?php echo $settings['filters_more_button_text']; ?></div>
															<?php if ($settings['filters_more_button_arrow'] == 'yes') { ?>
																<span
																	class="portfolio-filters-more-button-arrow"></span>
															<?php } ?>
														</div>
														<div class="portfolio-filters-more-dropdown">
													<?php } ?>
													<a href="#" data-filter="gallery-<?php echo $i; ?>"
													   class="<?php echo in_array('gallery-' . $i, $active_filter) ? 'active' : ''; ?> title-h6 <?php echo $settings['hover_pointer'] == 'yes' ? 'hover-pointer' : ''; ?>">
														<span <?php echo $settings['filter_style'] == 'buttons' ? 'class="light"' : ''; ?>>
															<?php echo $gallery['gallery_title']; ?>
														</span>
													</a>
													<?php if ($settings['truncate_filters'] == 'yes' && sizeof($settings['galleries']) > $settings['truncate_filters_number'] && $i == sizeof($settings['galleries']) - 1) { ?>
														</div>
														</div>
													<?php }
												} ?>
											</div>
											<?php if ($settings['filter_style'] == 'buttons') {
												wp_enqueue_script('jquery-dlmenu'); ?>
												<div class="portfolio-filters-resp <?php echo strtolower($settings['filter_query_type']) == 'and' ? 'multiple' : 'single'; ?>">
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
														<?php }
														foreach ($settings['galleries'] as $i => $gallery) { ?>
															<li>
																<a href="#" data-filter="gallery-<?php echo $i; ?>">
																	<?php echo $gallery['gallery_title']; ?>
																</a>
															</li>
														<?php } ?>
													</ul>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php } ?>
							
							<ul class="gallery-set clearfix"
								data-max-row-height="<?php echo $settings['metro_max_row_height'] ? $settings['metro_max_row_height']['size'] : ''; ?>">
								<?php
								foreach ($gallery_items as $img_id) :
									$thegem_item = get_post($img_id);

									if (empty($img_id) || !$thegem_item) {
										continue;
									}
									$thegem_highlight_type = 'disabled';
									if ($settings['ignore_highlights'] !== 'yes') {
										$thegem_highlight = (bool)get_post_meta($thegem_item->ID, 'highlight', true);
										if ($thegem_highlight) {
											$thegem_highlight_type = get_post_meta($thegem_item->ID, 'highligh_type', true);
											if (!$thegem_highlight_type) {
												$thegem_highlight_type = 'squared';
											}
										}
									} else {
										$thegem_highlight = false;
									}

									$thegem_attachment_link = get_post_meta($thegem_item->ID, 'attachment_link', true);
									$thegem_single_icon = true;

									if (!empty($thegem_attachment_link)) {
										$thegem_single_icon = false;
									}

									if ($thegem_highlight_type != 'disabled') {
										$item_thegem_sizes = get_thegem_portfolio_render_item_image_sizes($settings, $thegem_highlight_type);
									} else {
										$item_thegem_sizes = $thegem_sizes;
									}

									$thegem_size = $item_thegem_sizes[0];
									$thegem_sources = $item_thegem_sizes[1];

									$thegem_full_image_url = wp_get_attachment_image_src($thegem_item->ID, 'full');

									$is_size_item = false;

									$thegem_classes = array('gallery-item');

									if (isset($gallery_items_filter_classes[$img_id])) {
										foreach ($gallery_items_filter_classes[$img_id] as $filer_class) {
											$thegem_classes[] = $filer_class;
										}
									}

									if ($thegem_highlight_type != 'disabled' && $thegem_highlight_type != 'vertical') {
										$thegem_classes = array_merge($thegem_classes, get_thegem_portfolio_render_item_classes($settings, $thegem_highlight_type));
									} else {
										$thegem_classes = array_merge($thegem_classes, $item_classes);
									}

									if ($settings['layout'] != 'metro' && $thegem_highlight) {
										$thegem_classes[] = 'double-item';
									}

									if ($settings['layout'] != 'metro' && $thegem_highlight) {
										$thegem_classes[] = 'double-item-' . $thegem_highlight_type;
									}

									$thegem_wrap_classes = $settings['thegem_elementor_preset'];

									if ($settings['loading_animation'] === 'yes') {
										$thegem_classes[] = 'item-animations-not-inited';
									}

									if (empty($settings['icon']['value'])) {
										$thegem_classes[] = 'single-icon';
									}

									$preset_path = __DIR__ . '/templates/content-gallery-item.php';

									if (!empty($preset_path) && file_exists($preset_path)) {
										include($preset_path);
									}
									?>

								<?php endforeach; ?>
							</ul>

							<?php if ($settings['layout'] !== 'justified' || $settings['ignore_highlights'] !== 'yes') {
								$thegem_classes = array_merge(['gallery-item', 'size-item'], $item_classes);
								$is_size_item = true; ?>
								<div class="portfolio-item-size-container">
									<?php if (!empty($preset_path) && file_exists($preset_path)) {
										include($preset_path);
									} ?>
								</div>
							<?php } ?>

						</div>
					</div>
				</div>
			</div>
		<?php } else {
			wp_reset_postdata();
			if (Plugin::$instance->editor->is_edit_mode() ||
				($settings['source_type'] == 'product-gallery' && thegem_get_template_type(get_the_ID()) === 'single-product') ||
				($settings['source_type'] == 'portfolio-gallery' && thegem_get_template_type(get_the_ID()) === 'portfolio')) { ?>
				<div class="no-elements-gallery-grid">
					<i class="eicon-gallery-justified" aria-hidden="true"></i>
				</div>
			<?php }
		}

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script type="text/javascript">
				(function ($) {

					setTimeout(function () {
						if (!$('.elementor-element-<?php echo $widget_uid; ?> .gem-gallery-grid').length) {
							return;
						}
						$('.elementor-element-<?php echo $widget_uid; ?> .gem-gallery-grid').initGalleriesGrid();
					}, 1000);

					elementor.channels.editor.on('change', function (view) {
						var changed = view.elementSettingsModel.changed;

						if (changed.container_margin !== undefined || changed.justified_row_height !== undefined || changed.container_padding !== undefined) {
							setTimeout(function () {
								$('.elementor-element-<?php echo $widget_uid; ?> .gem-gallery-grid').initGalleriesGrid();
							}, 500);
						}
					});

				})(jQuery);

			</script>
		<?php endif;

		echo trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		wp_reset_postdata();
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_GalleryGrid());
