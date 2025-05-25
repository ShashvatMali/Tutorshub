<?php

namespace TheGem_Elementor\Widgets\TemplateLoopFeaturedMedia;

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
use Elementor\Utils;
use Elementor\Core\Schemes;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Blog Title.
 */

#[\AllowDynamicProperties]
class TheGem_TemplateLoopFeaturedMedia extends Widget_Base {
	public function __construct($data = [], $args = null) {
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
		return 'thegem-template-loop-featured-media';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'loop-item';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Featured Media/Product Image', 'thegem');
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
		return ['thegem_single_post_builder'];
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-loop-featured-media';
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		// General Section
		$this->start_controls_section(
			'general',
			[
				'label' => __('General', 'thegem'),
			]
		);

		$this->add_control(
			'thumbnail',
			[
				'label' => __('Image Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'justify' => __('Thumbnail for Justified Grids', 'thegem'),
					'masonry' => __('Thumbnail for Masonry Grids', 'thegem'),
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
				'default' => 'justify',
			]
		);

		$this->add_control(
			'image_ratio',
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
				'condition' => [
					'thumbnail!' => 'masonry',
				],
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-featured-media:not(.custom-video-ratio).image-aspect-ratio .media-inner-wrap' => '--aspect-ratio: {{SIZE}} !important;',
				],
				'description' => __('Specify a decimal value, i.e. instead of 3:4, specify 0.75. Use a dot as a decimal separator. Leave blank to show the original image ratio', 'thegem'),
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'ignore_post_format_media',
			[
				'label' => esc_html__('Ignore Post Format Media', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'default' => '',
				'description' => __('If activated, only posts featured images will be displayed. All other post format media (like videos or galleries) will be ignored. ', 'thegem'),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __('None', 'thegem'),
					'post' => __('Page/Post Url', 'thegem'),
					'custom' => __('Custom', 'thegem'),
				],
				'default' => 'post',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link_custom',
			[
				'label' => __('Custom Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
				'condition' => [
					'link' => 'custom',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hover_effect',
			[
				'label' => esc_html__('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'default' => 'yes',
				'condition' => [
					'link[url]!' => '',
				],
			]
		);

		$this->end_controls_section();

		// Style Image Section
		$this->start_controls_section(
			'image_style',
			[
				'label' => __('Image', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'vw'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-featured-media .media-inner-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_max_width',
			[
				'label' => __('Max Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'vw'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-featured-media .media-inner-wrap' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'vh'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'condition' => [
					'thumbnail!' => 'masonry',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-featured-media .media-inner-wrap' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'centered' => [
						'title' => __('Center', 'elementor'),
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

		$this->add_control(
			'better_thumbs_quality',
			[
				'label' => esc_html__('Better Thumbnails Quality', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'default' => '',
				'condition' => [
					'thumbnail' => ['justify', 'masonry'],
				],
			]
		);

		$this->start_controls_tabs('image_tabs', ['separator' => 'before']);
		$this->start_controls_tab('image_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$this->add_responsive_control(
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
					'{{WRAPPER}} .thegem-te-loop-featured-media .media-inner-wrap img' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_background_normal',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'toggle' => true,
				'fields_options' => [
					'background' => [
						'label' => __('Overlay Type', 'Background Control', 'thegem'),
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(0, 0, 0, 0)',
					],
				],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .thegem-te-loop-featured-media .media-inner-wrap:before, {{WRAPPER}} .thegem-te-loop-featured-media.appearance-type-gallery .media-inner-wrap .portfolio-image-slider:before',
			]
		);

		$this->remove_control('image_background_normal_image');
		$this->remove_control('image_background_normal_image_tablet');
		$this->remove_control('image_background_normal_image_mobile');

		$this->add_responsive_control(
			'image_radius_normal',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-featured-media .media-inner-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow_normal',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-loop-featured-media .media-inner-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-loop-featured-media .media-inner-wrap img',
			]
		);

		$this->add_control(
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
					'{{WRAPPER}} .thegem-te-loop-featured-media .media-inner-wrap img' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
				'condition' => [
					'link[url]!' => '',
					'hover_effect' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_opacity_hover',
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
					'{{WRAPPER}} .thegem-te-loop-featured-media.image-hover-effect a:hover .media-inner-wrap img' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_background_hover',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'toggle' => true,
				'fields_options' => [
					'background' => [
						'label' => __('Overlay Type', 'Background Control', 'thegem'),
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(0, 0, 0, 0.3)',
					],
				],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .thegem-te-loop-featured-media.image-hover-effect a:hover .media-inner-wrap:before',
			]
		);

		$this->remove_control('image_background_hover_image');
		$this->remove_control('image_background_hover_image_tablet');
		$this->remove_control('image_background_hover_image_mobile');

		$this->add_responsive_control(
			'image_radius_hover',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-featured-media.image-hover-effect a:hover .media-inner-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow_hover',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-loop-featured-media.image-hover-effect a:hover .media-inner-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_hover',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-loop-featured-media.image-hover-effect a:hover .media-inner-wrap img',
			]
		);

		$this->add_control(
			'blend_mode_hover',
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
					'{{WRAPPER}} .thegem-te-loop-featured-media.image-hover-effect a:hover .media-inner-wrap img' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->add_control(
			'transition_hover',
			[
				'label' => __('Transition Duration', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['s', 'ms'],
				'range' => [
					's' => [
						'min' => 0.1,
						'max' => 3,
						'step' => 0.1,
					],
					'ms' => [
						'min' => 10,
						'max' => 3000,
						'step' => 10,
					],
				],
				'default' => [
					'size' => 0.3,
					'unit' => 's',
				],
				'separator' => 'before',
				'condition' => [
					'hover_effect' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-featured-media.image-hover-effect a .media-inner-wrap:before' => 'transition-duration: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-loop-featured-media.image-hover-effect a .media-inner-wrap img' => 'transition-duration: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-loop-featured-media.image-hover-effect a:hover .media-inner-wrap img' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// Style Container Section
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
				'selector' => '{{WRAPPER}} .thegem-te-loop-featured-media .featured-media',
			]
		);

		$this->remove_control('container_background_image');
		$this->remove_control('container_background_image_tablet');
		$this->remove_control('container_background_image_mobile');

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-loop-featured-media .featured-media',
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
					'{{WRAPPER}} .thegem-te-loop-featured-media .featured-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .thegem-te-loop-featured-media .featured-media' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-loop-featured-media .featured-media',
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

		if (empty($single_post)) {
			ob_end_clean();
			echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		// Post Data
		if (get_post_type($single_post->ID) == 'post' || get_post_type($single_post->ID) == 'page' || get_post_type($single_post->ID) == 'product') {
			$post_item_data = thegem_get_sanitize_post_data($single_post->ID);
		} else if ($single_post->post_type !== 'thegem_pf_item') {
			$post_item_data = thegem_get_sanitize_cpt_item_data($single_post->ID);
		}

		$post_format = get_post_format($single_post->ID);
		$is_ignore_post_format = !empty($params['ignore_post_format_media']);

		if ($single_post->post_type == 'thegem_pf_item') {
			$post_item_data = thegem_get_sanitize_pf_item_data($single_post->ID);

			switch ($post_item_data['grid_appearance_type']) {
				case 'featured_image':
					$post_format = 'image';
					break;
				case 'animated_gif':
					$post_format = 'gif';
					$post_item_data['gif'] = $post_item_data['grid_appearance_gif'];
					$post_item_data['gif_start'] = $post_item_data['grid_appearance_gif_start'];
					$post_item_data['gif_poster'] = $post_item_data['grid_appearance_gif_poster'];
					$post_item_data['gif_preload'] = $post_item_data['grid_appearance_gif_preload'];
					break;
				case 'video':
					$post_format = 'video';
					$post_item_data['video'] = $post_item_data['grid_appearance_video'];
					$post_item_data['video_type'] = $post_item_data['grid_appearance_video_type'];
					$post_item_data['video_aspect_ratio'] = $post_item_data['grid_appearance_video_aspect_ratio'];
					$post_item_data['video_play_on_mobile'] = $post_item_data['grid_appearance_video_play_on_mobile'];
					$post_item_data['video_overlay'] = $post_item_data['grid_appearance_video_overlay'];
					$post_item_data['video_poster'] = $post_item_data['grid_appearance_video_poster'];
					$post_item_data['video_start'] = $post_item_data['grid_appearance_video_start'];
					break;
				case 'gallery':
					$post_format = 'gallery';
					$post_item_data['gallery_autoscroll'] = $post_item_data['grid_appearance_gallery_autoscroll'];
					$post_item_data['gallery_autoscroll_speed'] = $post_item_data['grid_appearance_gallery_autoscroll_speed'];
					break;
			}
		}

		// Image thumbnails
		$thumbnail_settings = [];
		$thumbnail_id = get_post_thumbnail_id($single_post->ID);
		$is_post_thumbnail = has_post_thumbnail($single_post->ID);
		switch ($params['thumbnail']) {
			case 'justify':
				$ratio = !empty($params['better_thumbs_quality']) ? 'square-double' : 'square';
				$thumbnail_settings = ['layout' => 'justify', 'columns_desktop' => '2x', 'columns_tablet' => '2x', 'columns_mobile' => '1x', 'image_aspect_ratio' => $ratio];
				break;
			case 'masonry':
				$thumbnail_settings = ['layout' => 'masonry', 'columns_desktop' => '2x', 'columns_tablet' => '2x', 'columns_mobile' => '1x'];
				break;
			case 'full':
				$thumbnail_settings = ['image_size' => 'full'];
				break;
			default:
				$thumbnail_settings = ['image_size' => $params['thumbnail']];
				break;
		}
		$thumbnail_sources = get_thegem_portfolio_render_item_image_sizes($thumbnail_settings);

		// Gif data
		$gif_image_class = '';
		if ($post_format == 'gif') {
			$gif_preload = true;
			if ($post_item_data['gif_start'] == 'play_on_hover' && empty($post_item_data['gif_preload'])) {
				$gif_preload = false;
				$gif_image_class = 'gif-load-on-hover';
			}
		}

		// Video data
		$video_ratio_class = '';
		if ($post_format == 'video') {
			$video_ratio = !empty($post_item_data['video_aspect_ratio']) ? $post_item_data['video_aspect_ratio'] : '';
			$video_ratio_arr = explode(":", $video_ratio);
			if (!empty($video_ratio_arr[0]) && !empty($video_ratio_arr[1])) {
				$video_aspect_ratio = $video_ratio_arr[0] / $video_ratio_arr[1];
				if ($post_item_data['video_start'] !== 'open_in_lightbox') {
					$video_ratio_class = 'custom-video-ratio';
				}
			}
		}

		// Link data
		$is_link = false;
		if (isset($params['link']) && $params['link'] != 'none') {
			switch ($params['link']) {
				case 'post':
					$is_link = true;
					$this->add_render_attribute('a-wrapper', 'href', get_permalink($single_post));
					$this->add_render_attribute('a-wrapper', 'target', '_self');

					break;
				case 'custom':
					if (!empty($params['link_custom']['url'])) {
						$is_link = true;
						$this->add_render_attribute('a-wrapper', 'href', $params['link_custom']['url']);

						if (!empty($params['link_custom']['is_external'])) {
							$this->add_render_attribute('a-wrapper', 'target', '_blank');
						}

						if (!empty($params['link_custom']['nofollow'])) {
							$this->add_render_attribute('a-wrapper', 'rel', 'nofollow');
						}
					}

					break;
			}
		}

		// Custom classes
		$image_ratio = !empty($params['image_ratio']['size']) ? 'image-aspect-ratio' : '';
		$hover_effect = !empty($params['hover_effect']) ? 'image-hover-effect' : '';
		$image_position = !empty($params['image_position']) ? 'image-position-' . $params['image_position'] : '';
		$without_image = !$is_post_thumbnail ? 'without-image' : '';
		$appearance_type = !empty($post_format) && !$is_ignore_post_format ? 'appearance-type-' . $post_format : 'appearance-type-image';
		$loop_item_type = !empty($params['thumbnail']) ? 'loop-item-type-' . $params['thumbnail'] : '';

		$params['element_class'] = implode(' ', array(
			$this->get_widget_wrapper(),
			$image_ratio,
			$video_ratio_class,
			$gif_image_class,
			$hover_effect,
			$image_position,
			$without_image,
			$appearance_type,
			$loop_item_type,
        ));

		?>

		<div class="<?= esc_attr($params['element_class']); ?>">
			<?php if (($is_link && $post_format != 'gallery') || ($is_link && $is_ignore_post_format)): ?><a <?= $this->get_render_attribute_string( 'a-wrapper' ); ?>><?php endif; ?>
				<div class="featured-media">
					<div class="media-inner-wrap">
						<?php if ($post_format == '' || $post_format == 'image' || !empty($params['ignore_post_format_media'])): ?>
							<?= thegem_generate_picture($thumbnail_id, $thumbnail_sources[0], $thumbnail_sources[1], array('alt' => get_the_title())); ?>
						<?php else: ?>
							<?php if ($post_format == 'gif'):
								if (!empty($post_item_data['gif'])) {
									$gif_src = wp_get_attachment_image_src($post_item_data['gif'], 'full');
									if ($gif_src) { ?>
										<img width="<?php echo $gif_src[1]; ?>" height="<?php echo $gif_src[2]; ?>" class="gem-gif-portfolio" src="<?php if ($gif_preload) { echo $gif_src[0]; } ?>" <?php if (!$gif_preload) { echo 'data-src="' . $gif_src[0] . '"'; } ?>>
									<?php }
								} else {
									thegem_generate_picture($thumbnail_id, $thumbnail_sources[0], array(), array('alt' => get_the_title()));
								}

								if ($post_item_data['gif_start'] == 'play_on_hover') {
									if (!empty($post_item_data['gif_poster'])) { ?>
										<img class="gem-gif-poster" src="<?php echo $post_item_data['gif_poster']; ?>">
									<?php } else {
										thegem_generate_picture($post_item_data['gif'], $thumbnail_sources[0], $thumbnail_sources[1], array("class" => "gem-gif-poster"));
									}
								}
							endif; ?>

							<?php if ($post_format == 'video'):
								switch ($post_item_data['video_type']) {
									case 'youtube':
										$video_id = thegem_parcing_youtube_url($post_item_data['video']);
										$thegem_video_link = '//www.youtube.com/embed/' . $video_id . '?autoplay=1';
										$thegem_video_class = 'youtube';
										break;
									case 'vimeo':
										$video_id = thegem_parcing_vimeo_url($post_item_data['video']);
										$thegem_video_link = '//player.vimeo.com/video/' . $video_id . '?autoplay=1';
										$thegem_video_class = 'vimeo';
										break;
									default:
										$video_id = $thegem_video_link = $post_item_data['video'];
										$thegem_video_class = 'self_video';
								}
							?>
								<?php if ($post_item_data['video_start'] == 'open_in_lightbox') {
									if ($post_item_data['video_type'] == 'self' && !empty($post_item_data['video_poster'])) {
										$thumbnail_id = attachment_url_to_postid($post_item_data['video_poster']); ?>
										<div class="portfolio-item-link">
											<?= thegem_generate_picture($thumbnail_id, $thumbnail_sources[0], $thumbnail_sources[1], array('alt' => get_the_title())); ?>
										</div>
									<?php } else if ($is_post_thumbnail) { ?>
										<div class="portfolio-item-link">
											<?= thegem_generate_picture($thumbnail_id, $thumbnail_sources[0], $thumbnail_sources[1], array('alt' => get_the_title())); ?>
										</div>
									<?php } ?>
									<a href="<?php echo esc_url($thegem_video_link); ?>" class="portfolio-video-icon <?php echo $thegem_video_class; ?>" <?php if (isset($aspect_ratio)) { ?>data-ratio="<?php echo $aspect_ratio; ?>"<?php } ?> data-fancybox="thegem-portfolio" data-elementor-open-lightbox="no"></a>
								<?php } else {
								$play_on_mobile = true;
								if (!$post_item_data['video_play_on_mobile']) {
									echo thegem_generate_picture($thumbnail_id, $thumbnail_sources[0], $thumbnail_sources[1], array('alt' => get_the_title(), "class" => "video-image-mobile"));
									$play_on_mobile = false;
								}
								$autoplay = $post_item_data['video_start'] == 'autoplay'; ?>

								<div class="gem-video-portfolio type-<?= $post_item_data['video_type']; ?> <?= $autoplay ? 'autoplay' : 'play-on-hover'; ?> <?= $play_on_mobile ? '' : 'hide-on-mobile'; ?> <?= !$autoplay || $play_on_mobile ? 'run-embed-video' : ''; ?>" data-video-type="<?= $post_item_data['video_type']; ?>" data-video-id="<?= $video_id; ?>" <?php if (isset($video_aspect_ratio)) { ?>style="aspect-ratio: <?php echo $video_aspect_ratio; ?>"<?php } ?>>
									<?php echo thegem_portfolio_video_background(
										$post_item_data['video_type'],
										$video_id,
										$post_item_data['video_overlay'],
										$post_item_data['video_poster'],
										$autoplay,
										$play_on_mobile
									); ?>
								</div>
								<?php } ?>
							<?php endif; ?>

							<?php if ($post_format == 'audio'):
								echo thegem_get_post_featured_content($single_post->ID, '', false, [], true);
							endif; ?>

							<?php if ($post_format == 'quote'):
								echo thegem_get_post_featured_content($single_post->ID, '', false, [], false);
							endif; ?>

							<?php if ($post_format == 'gallery'): ?>
								<?php
									$thegem_gallery_images_urls = [];
									if ($single_post->post_type == 'thegem_pf_item') {
										$thegem_gallery_images_ids = get_post_meta(get_the_ID(), 'thegem_portfolio_item_gallery_images', true);
									} else {
										$thegem_gallery_images_ids = get_post_meta(get_the_ID(), 'thegem_post_item_gallery_images', true);
									}

									if ($thegem_gallery_images_ids) {
										$attachments_ids = array_filter(explode(',', $thegem_gallery_images_ids)); ?>
									<div class="portfolio-image-slider <?php if (!empty($post_item_data['gallery_autoscroll'])) { echo 'autoscroll'; } ?>" <?php if (!empty($post_item_data['gallery_autoscroll'])) { ?> data-interval="<?php echo isset($post_item_data['gallery_autoscroll_speed']) ? $post_item_data['gallery_autoscroll_speed'] : $post_item_data['gallery_autoscroll']; ?>"<?php } ?>>
										<?php foreach ($attachments_ids as $i => $slide) {
											$slide_image = wp_get_attachment_image($slide, $thumbnail_sources[0]);
											if (empty($slide_image)) {
												continue;
											}
											$thegem_gallery_images_urls[] = wp_get_attachment_image_url($slide, 'full'); ?>
											<div class="slide <?php echo $slide; ?>"<?php if ($i != 0) {echo ' style="opacity: 0;"';} ?>>
												<a href="<?php echo esc_url(get_permalink()); ?>">
													<?php echo $slide_image; ?>
												</a>
											</div>
										<?php } ?>
										<button class="btn btn-next" data-direction="next"></button>
										<button class="btn btn-prev" data-direction="prev"></button>
									</div>
								<?php } ?>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php if (($is_link && $post_format != 'gallery') || ($is_link && $is_ignore_post_format)): ?></a><?php endif; ?>
		</div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateLoopFeaturedMedia());
