<?php

namespace TheGem_Elementor\Widgets\TemplatePortfolioGallery;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Blog Title.
 */

#[\AllowDynamicProperties]
class TheGem_TemplatePortfolioGallery extends Widget_Base {
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if ( !defined('THEGEM_TE_PORTFOLIO_GALLERY_DIR' )) {
			define('THEGEM_TE_PORTFOLIO_GALLERY_DIR', rtrim(__DIR__, ' /\\'));
		}

		if ( !defined('THEGEM_TE_PORTFOLIO_GALLERY_URL') ) {
			define('THEGEM_TE_PORTFOLIO_GALLERY_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-te-portfolio-gallery-style', THEGEM_TE_PORTFOLIO_GALLERY_URL . '/css/portfolio-gallery.css', array('owl'), null);
		wp_register_script('thegem-te-portfolio-gallery-script', THEGEM_TE_PORTFOLIO_GALLERY_URL . '/js//portfolio-gallery.js', array('jquery', 'owl'), null, true);

	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-portfolio-gallery';
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
		return get_post_type($post_id) !== 'thegem_templates' || thegem_get_template_type($post_id) === 'single-post' || thegem_get_template_type($post_id) === 'portfolio' || thegem_get_template_type($post_id) === 'title';
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'portfolio') {
			return __('Project Gallery', 'thegem');
		}
		return __('Post Gallery', 'thegem');
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
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'portfolio') return ['thegem_portfolio_builder'];
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'title') return ['thegem_title_area_builder'];
		if(get_post_type($post_id) === 'thegem_templates') return ['thegem_single_post_builder'];
		return ['thegem_single_post'];
	}

	public function get_style_depends() {
		return ['thegem-te-portfolio-gallery-style'];
	}

	public function get_script_depends() {
		return ['thegem-te-portfolio-gallery-script'];
	}

	/** Check is admin edit mode */
	public function is_admin_mode() {
		return is_admin() && Plugin::$instance->editor->is_edit_mode();
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-portfolio-gallery';
	}

	/** Get customize class */
	public function get_customize_class() {
		return ' .'.$this->get_widget_wrapper();
	}

	public function get_images_params($item, $id, $param) {
		$result = '';

		switch ($param) {
			case 'alt':
				$alt_custom = get_post_meta($id, '_wp_attachment_image_alt', true);
				$alt_default = $item->title;
				$result = !empty($alt_custom) ? esc_html($alt_custom) : esc_html($alt_default);
				break;
			case 'title':
				$title_custom = get_the_title($id);
				$title_default = $item->title;
				$result = !empty($title_custom) ? esc_html($title_custom) : esc_html($title_default);
				break;
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
			'type',
			[
				'label' => __('Gallery Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'carousel' => __('Carousel Grid', 'thegem'),
					'grid' => __('Grid', 'thegem'),
				],
				'default' => 'carousel',
			]
		);

		$this->add_responsive_control(
			'carousel_columns',
			[
				'label' => __('Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => __('1x columns', 'thegem'),
					'2' => __('2x columns', 'thegem'),
					'3' => __('3x columns', 'thegem'),
					'4' => __('4x columns', 'thegem'),
					'5' => __('5x columns', 'thegem'),
					'6' => __('6x columns', 'thegem'),
				],
				'desktop_default' => '4',
				'tablet_default' => '3',
				'mobile_default' => '2',
				'condition' => [
					'type' => 'carousel',
				],
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[
				'label' => __('Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => __('1x columns', 'thegem'),
					'2' => __('2x columns', 'thegem'),
					'3' => __('3x columns', 'thegem'),
					'4' => __('4x columns', 'thegem'),
					'5' => __('5x columns', 'thegem'),
					'6' => __('6x columns', 'thegem'),
				],
				'desktop_default' => '4',
				'tablet_default' => '3',
				'mobile_default' => '2',
				'condition' => [
					'type' => 'grid',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-grid .item' => 'width: calc(100% / {{VALUE}});',
				],
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
				'selectors' => [
					'{{WRAPPER}} .item-inner .image-inner' => 'aspect-ratio: {{SIZE}} !important;',
				],
				'description' => __('Specify a decimal value, i.e. instead of 3:4, specify 0.75. Use a dot as a decimal separator. Leave blank to show the original image ratio', 'thegem'),
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'carousel_gaps',
			[
				'label' => __('Gaps (px)', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition' => [
					'type' => 'carousel',
				],
			]
		);

		$this->add_responsive_control(
			'grid_gaps',
			[
				'label' => __('Gaps', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition' => [
					'type' => 'grid',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-grid' => 'margin: calc(-{{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .portfolio-grid .item' => 'padding: calc({{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		/*$this->add_responsive_control(
			'grid_image_count',
			[
				'label' => __('Max. Number of Images', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [''],
				'range' => [
					'' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'type' => 'grid',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-grid .item:nth-child(n+{{SIZE}})' => 'display: none !important;',
				],
			]
		);*/

		$this->add_control(
			'lightbox',
			[
				'label' => __('Lightbox Gallery', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __('Autoplay', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => 'carousel',
				],
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __('Autoplay Speed', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['ms', 's'],
				'range' => [
					'ms' => [
						'min' => 1000,
						'max' => 100000,
						'step' => 10,
					],
					's' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'ms',
					'size' => 5000,
				],
				'condition' => [
					'type' => 'carousel',
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'include_featured_image',
			[
				'label' => __('Include Featured Image', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __('Slider Loop', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->end_controls_section();

		// Arrows Section
		$this->start_controls_section(
			'section_arrows',
			[
				'label' => __('Arrows', 'thegem'),
				'condition' => [
					'type' => 'carousel',
				],
			]
		);

		$this->add_control(
			'arrows',
			[
				'label' => __('Arrows', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => 'carousel',
				],
			]
		);

		$this->add_control(
			'arrows_type',
			[
				'label' => __('Arrows Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'simple' => __('Simple', 'thegem'),
					'round' => __('Round Border', 'thegem'),
					'square' => __('Square Border', 'thegem'),
				],
				'default' => 'simple',
				'condition' => [
					'type' => 'carousel',
					'arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_icons',
			[
				'label' => __('Arrows Icons', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'custom' => __('Custom', 'thegem'),
				],
				'default' => 'default',
				'condition' => [
					'type' => 'carousel',
					'arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_prev_icon_select',
			[
				'label' => __('Select Prev Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'type' => 'carousel',
					'arrows' => 'yes',
					'arrows_icons' => 'custom',
				],
			]
		);

		$this->add_control(
			'arrows_next_icon_select',
			[
				'label' => __('Select Next Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'type' => 'carousel',
					'arrows' => 'yes',
					'arrows_icons' => 'custom',
				],
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'space-between' => __('Left & Right', 'thegem'),
					'bottom-centered' => __('Bottom Centered', 'thegem'),
				],
				'default' => 'space-between',
				'condition' => [
					'type' => 'carousel',
					'arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'condition' => [
					'type' => 'carousel',
					'arrows' => 'yes',
					'arrows_position' => 'space-between',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav.carousel-nav--space-between .nav-item' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrows_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'condition' => [
					'type' => 'carousel',
					'arrows' => 'yes',
					'arrows_position' => 'bottom-centered',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav.carousel-nav--bottom-centered' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrows_side_spacing',
			[
				'label' => __('Side Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'condition' => [
					'arrows' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav.carousel-nav--space-between .nav-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio-carousel-nav.carousel-nav--space-between .nav-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio-carousel-nav.carousel-nav--bottom-centered .nav-prev' => 'margin-right: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio-carousel-nav.carousel-nav--bottom-centered .nav-next' => 'margin-left: calc({{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		$this->end_controls_section();

		// Dots Section
		$this->start_controls_section(
			'section_dots',
			[
				'label' => __('Dots Navigation', 'thegem'),
				'condition' => [
					'type' => 'carousel',
				],
			]
		);

		$this->add_control(
			'dots',
			[
				'label' => __('Dots Navigation', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => 'carousel',
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label' => __('Dots Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'type' => 'carousel',
					'dots' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel .owl-dots .owl-dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'type' => 'carousel',
					'dots' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel .owl-dots' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_side_spacing',
			[
				'label' => __('Side Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'type' => 'carousel',
					'dots' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel .owl-dots .owl-dot' => 'margin-left: calc({{SIZE}}{{UNIT}}/2); margin-right: calc({{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		$this->end_controls_section();

		// Image Container
		$this->start_controls_section(
			'section_image_style',
			[
				'label' => __('Image Container', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
                        'step' => 10
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .item-inner' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 10
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .item-inner' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'label'       => __( 'Shadow', 'thegem' ),
				'name'        => 'image_shadow',
				'label_block' => true,
				'selector'    => '{{WRAPPER}} .item-inner'
			]
		);

		$this->end_controls_section();

		// Arrows Section Style
		$this->start_controls_section(
			'section_arrows_style',
			[
				'label' => __('Arrows', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_icons_size',
			[
				'label' => __('Icons Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'arrows' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .nav-prev i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio-carousel-nav .nav-next i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrows_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon' => 'min-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'arrows_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon' => 'min-height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'arrows_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon' => 'border-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'arrows_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'arrows_color_hover',
			[
				'label' => __('Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'arrows_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'arrows_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'arrows_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'arrows_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'arrows' => 'yes',
					'arrows_type' => array('round', 'square'),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel-nav .icon:hover' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

        //Dots Section Style
		$this->start_controls_section(
			'section_dots_style',
			[
				'label' => __('Dots Navigation', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label' => __('Dots Normal Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'dots' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel .owl-dots .owl-dot' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dots_color_hover',
			[
				'label' => __('Dots Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'dots' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel .owl-dots .owl-dot:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dots_color_active',
			[
				'label' => __('Dots Active Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'dots' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-carousel .owl-dots .owl-dot.active' => 'background-color: {{VALUE}};',
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
		$params = array_merge(array(
			'carousel_columns' => 4,
			'carousel_columns_tablet' => 3,
			'carousel_columns_mobile' => 2,
		), $settings);

		$params['carousel_columns'] = empty($params['carousel_columns']) ? 4 : $params['carousel_columns'];

		ob_start();
		$uniqid = uniqid('thegem-custom-').rand(1,9999);
		$portfolio = thegem_templates_init_portfolio();
		if (!empty($portfolio)) {
			$attachment_ids = explode(',', get_post_meta($portfolio->ID, 'thegem_portfolio_item_gallery_images', true));
			if($portfolio->post_type !== 'thegem_pf_item') {
				$attachment_ids = explode(',', get_post_meta($portfolio->ID, 'thegem_post_item_gallery_images', true));
			}
			if(!empty($params['include_featured_image']) && has_post_thumbnail($portfolio->ID)) {
				$attachment_ids = array_merge(array(get_post_thumbnail_id($portfolio->ID)), $attachment_ids);
			}
		}

		if (empty($portfolio) || empty($attachment_ids) || empty($attachment_ids[0])) {
			ob_end_clean();
			echo thegem_templates_close_portfolio(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		// Get images
		$attachments_count = count($attachment_ids);
		$items_output = $thumb_output = '';

		if($attachment_ids) {
			foreach($attachment_ids as $id) {
				$attachment = wp_get_attachment_image($id);

				if (empty($attachment )) continue;

				$image_url = wp_get_attachment_url($id);
				$image_args = array(
					'alt' => $this->get_images_params($portfolio, $id, 'alt'),
				);

				ob_start();
				echo wp_get_attachment_image($id, 'full', false, $image_args);
				$thumb_output = '<div class="image-inner">'.ob_get_clean().'</div>';

				if (!empty($params['lightbox'])) {
					$image_output = '<a class="lightbox item-inner" href="' . esc_url($image_url) . '" data-fancybox="portfolio-gallery-' . esc_attr($uniqid) . '" data-fancybox-group="portfolio-gallery-' . esc_attr($uniqid) . '" data-full-image-url="' . esc_url($image_url) . '">' . $thumb_output . '</a>';
				} else {
					$image_output = '<div class="item-inner">' . $thumb_output . '</div>';
				}

				$items_output .= '<div class="item" data-id="' . esc_attr($id) . '">'.$image_output.'</div>';
			}
		}

		// Set carousel navigation
		$carousel_nav_output = $carousel_nav_prev = $carousel_nav_next = '';
		if (!empty($params['arrows_icons']) && $params['arrows_icons'] == 'custom' && !empty($params['arrows_prev_icon_select']['value'])) {
			ob_start();
			Icons_Manager::render_icon($params['arrows_prev_icon_select'], ['aria-hidden' => 'true']);
			$carousel_nav_prev .= '<a href="javascript:void(0);" class="icon">'.ob_get_clean().'</a>';
		} else if (!empty($params['arrows_icons']) && $params['arrows_icons'] == 'default') {
			$carousel_nav_prev .= '<a href="javascript:void(0);" class="icon"><i class="icon-default"></i></a>';
		}

		if (!empty($params['arrows_icons']) && $params['arrows_icons'] == 'custom' && !empty($params['arrows_next_icon_select']['value'])) {
			ob_start();
			Icons_Manager::render_icon($params['arrows_next_icon_select'], ['aria-hidden' => 'true']);
			$carousel_nav_next = '<a href="javascript:void(0);" class="icon">'.ob_get_clean().'</a>';
		} else if (!empty($params['arrows_icons']) && $params['arrows_icons'] == 'default') {
			$carousel_nav_next = '<a href="javascript:void(0);" class="icon"><i class="icon-default"></i></a>';
		}

		$carousel_nav_output .= '<div class="nav-item nav-prev">'.$carousel_nav_prev.'</div>';
		$carousel_nav_output .= '<div class="nav-item nav-next">'.$carousel_nav_next.'</div>';
		$carousel_nav_class = 'carousel-nav--'.$params['arrows_type'].' '.'carousel-nav--'.$params['arrows_position'];

		$autoplay_speed = '';
		if (!empty($params['autoplay_speed']['size'])) {
			$autoplay_speed = $params['autoplay_speed']['unit'] === 's' ? $params['autoplay_speed']['size'] * 1000 : $params['autoplay_speed']['size'];
		}

		$aspect_ratio = !empty($params['image_ratio']['size']) ? 'image-aspect-ratio' : '';
		$params['element_class'] = implode(' ', array($this->get_widget_wrapper(), $aspect_ratio));
		?>

		<div class="<?= esc_attr($params['element_class']); ?>">
			<?php if ($params['type'] == 'carousel'): ?>
				<div class="portfolio-carousel owl-carousel"
					data-items-desktop="<?= $params['carousel_columns'] ?>"
					data-items-tablet="<?= $params['carousel_columns_tablet'] ?>"
					data-items-mobile="<?= $params['carousel_columns_mobile'] ?>"
					data-margin-desktop="<?= $params['carousel_gaps']['size'] ?>"
					data-margin-tablet="<?= empty($params['carousel_gaps_tablet']['size']) ? 0 : $params['carousel_gaps_tablet']['size']; ?>"
					data-margin-mobile="<?= empty($params['carousel_gaps_mobile']['size']) ? 0 : $params['carousel_gaps_mobile']['size']; ?>"
					data-dots="<?= $params['dots'] ?>"
					data-loop="<?= $params['loop'] ?>"
					data-length="<?= $attachments_count ?>"
					data-autoplay="<?= $params['autoplay'] ?>"
					data-autoplay-speed="<?= $autoplay_speed ?>">

					<?= $items_output ?>
				</div>

				<?php if (!empty($params['arrows'])): ?>
					<div class="portfolio-carousel-nav <?= $carousel_nav_class ?>">
						<?= $carousel_nav_output ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($params['type'] == 'grid'): ?>
				<div class="portfolio-grid">
					<?= $items_output ?>
				</div>
			<?php endif; ?>
		</div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		if ($this->is_admin_mode()) { ?>
			<script>
				(function ($) {
					setTimeout(function () {
						$('.elementor-element-<?= $this->get_id(); ?> .thegem-te-portfolio-gallery .portfolio-carousel').initPortfolioGallery();
					}, 100);
				})(jQuery);
			</script>
		<?php }

		echo thegem_templates_close_portfolio(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplatePortfolioGallery());
