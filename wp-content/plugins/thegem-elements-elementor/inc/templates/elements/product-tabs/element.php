<?php

namespace TheGem_Elementor\Widgets\TemplateProductTabs;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Product Tabs.
 */

#[\AllowDynamicProperties]
class TheGem_TemplateProductTabs extends Widget_Base {
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
		return 'thegem-template-product-tabs';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-product';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Product Tabs', 'thegem');
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
		return ['thegem_single_product_builder'];
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-product-tabs';
	}

	/** Get customize class */
	public function get_customize_class() {
		return ' .'.$this->get_widget_wrapper();
	}

	/** Check is admin edit mode */
	public function is_admin_mode() {
		return is_admin() && Plugin::$instance->editor->is_edit_mode();
	}

	public function product_tabs_callback($params) {
		global $product, $post;

		// Get Additional Tabs Data
		$additional_tabs = array();
		$product_page_data = get_post_meta( $post->ID, 'thegem_product_page_data', true );
		if (!empty($params['additional_tabs']) && !empty($product_page_data['product_page_additional_tabs'])){
			if ($product_page_data['product_page_additional_tabs'] == 'default' && !empty(thegem_get_option('product_page_additional_tabs'))) {
				$additional_tabs = json_decode(thegem_get_option('product_page_additional_tabs_data'));
			} elseif($product_page_data['product_page_additional_tabs'] == 'custom'){
				$additional_tabs = json_decode($product_page_data['product_page_additional_tabs_data']);
			}
		} elseif(!empty($params['additional_tabs'])) {
			if (!empty(thegem_get_option('product_page_additional_tabs'))){
				$additional_tabs = json_decode(thegem_get_option('product_page_additional_tabs_data'));
			}
		}

		$tabs = array();
		$description_tab_callback = '';

		if($params['description_tab_source'] == 'page_builder') {
			$description_tab_callback = 'thegem_woocommerce_single_product_page_content';
		} else {
			$description_tab_callback = 'woocommerce_product_description_tab';
		}

		if ( !empty($params['description']) ) {
			$tabs['description'] = array(
				'title' => esc_html__( $params['description_title'], 'woocommerce'),
				'priority' => 10,
				'callback' => $description_tab_callback
			);
		} else {
			unset( $tabs['description'] );
		}

		if ( !empty($params['additional']) ) {
			$tabs['additional_information'] = array(
				'title'	=> esc_html__( $params['additional_title'], 'woocommerce'),
				'priority' => 20,
				'callback' => 'woocommerce_product_additional_information_tab',
			);
		} elseif ( isset( $tabs['additional_information'] ) ) {
			unset( $tabs['additional_information'] );
		}

		if ( !empty($params['reviews']) )  {
			$tabs['reviews'] = array(
				'title'	=> $product->get_review_count() > 0 ? sprintf(esc_html__( $params['reviews_title'], 'woocommerce' ).' <sup>%d</sup>', $product->get_review_count()) : esc_html__( $params['reviews_title']),
				'priority' => 30,
				'callback' => 'comments_template',
			);
		} elseif ( isset( $tabs['reviews'] )) {
			unset( $tabs['reviews'] );
		}

		// Thegem Additional Tabs
		if (!empty($additional_tabs)) {
			foreach ($additional_tabs as $tab) {
				$key = str_replace('_', '-', sanitize_title($tab->title));
				$text_content = ($tab->type == 'text' && !empty($tab->text_content)) ? $tab->text_content : '';
				$section_content = ($tab->type == 'section' && !empty($tab->section_content) && function_exists('thegem_product_tabs_template_section')) ? thegem_product_tabs_template_section($tab->section_content) : '';
				$priority = !empty($tab->priority) ? intval($tab->priority) : 100;

				if ( !empty($key) )  {
					$tabs[$key] = array(
						'title' => esc_html__($tab->title, 'thegem'),
						'priority' => $priority,
						'type' => 'additional_tab',
						'text_content' => $text_content,
						'section_content' => $section_content
					);
				} elseif ( isset( $tabs[$key] )) {
					unset( $tabs[$key] );
				}
			}
		}

		// YITH WooCommerce Tab Manager
		if (defined( 'YWTM_VERSION' )) {
			$ywtm_tabs = get_posts( array(
				'numberposts' => 100,
				'orderby' => 'date',
				'order' => 'ASC',
				'post_type' => 'ywtm_tab',
				'post_status'  => 'publish',
			));

			foreach ($ywtm_tabs as $tab) {
				$key = str_replace( '-', '_', sanitize_title($tab->post_title));
				$isActive = get_post_meta($tab->ID, '_ywtm_show_tab', true);

				if ( !empty($key) && $isActive )  {
					$ywtm_type = get_post_meta($tab->ID, '_ywtm_tab_type', true);
					$ywtm_product_ids = ($ywtm_type == 'product') ? get_post_meta($tab->ID, '_ywtm_tab_product', true) : '';
					$ywtm_data = get_post_meta($tab->ID, '_ywtm_text_tab', true);
					$ywtm_priority = get_post_meta($tab->ID, '_ywtm_order_tab', true);

					$tabs[$key] = array(
						'title'	=> $tab->post_title,
						'priority' => $ywtm_priority,
						'type' => 'ywtm_tab',
						'ywtm_tab' => array(
							'type' => $ywtm_type,
							'data' => $ywtm_data,
							'product_ids' => $ywtm_product_ids,
						)
					);
				} elseif ( isset( $key )) {
					unset( $tabs[$key] );
				}
			}
		}

		// Custom Product Tabs for WooCommerce
		if (thegem_is_plugin_active('yikes-inc-easy-custom-woocommerce-product-tabs/yikes-inc-easy-custom-woocommerce-product-tabs.php')) {
			$yikes_tabs = get_post_meta(get_the_ID(), 'yikes_woo_products_tabs', true);

			if (!empty($yikes_tabs)) {
				foreach ($yikes_tabs as $tab) {
					$key = str_replace( '-', '_', sanitize_title($tab['id']));

					if ( !empty($key) )  {
						$tabs[$key] = array(
							'title' => esc_html__($tab['title'], 'thegem'),
							'priority' => 100,
							'type' => 'yikes_type',
							'text_content' => $tab['content'],
						);
					} elseif ( isset( $key )) {
						unset( $tabs[$key] );
					}
				}
			}
		}

		// Sorting tabs by priority
		uasort($tabs, function ($a, $b) {
			if ($a['priority'] == $b['priority']) {
				return 0;
			}

			return ($a['priority'] < $b['priority']) ? -1 : 1;
		});

		return $tabs;
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
			'layout',
			[
				'label' => __('Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'tabs' => __('Tabs', 'thegem'),
					'accordion' => __('Accordion', 'thegem'),
					'one_by_one' => __('One by One', 'thegem'),
				],
				'default' => 'tabs',
			]
		);

		$this->add_control(
			'tabs_style',
			[
				'label' => __('Tabs Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'horizontal' => __('Horizontal Tabs', 'thegem'),
					'vertical' => __('Vertical Tabs', 'thegem'),
				],
				'default' => 'horizontal',
				'condition' => [
					'layout' => ['tabs'],
				],
			]
		);

		$this->add_control(
			'tabs_align_horizontal',
			[
				'label' => __('Tabs Alignment', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left' => __('Left', 'thegem'),
					'center' => __('Center', 'thegem'),
					'right' => __('Right', 'thegem'),
					'stretch' => __('Stretch', 'thegem'),
				],
				'default' => 'left',
				'condition' => [
					'layout' => ['tabs'],
					'tabs_style' => ['horizontal'],
				],
			]
		);

		$this->add_control(
			'tabs_align_vertical',
			[
				'label' => __('Tabs Alignment', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
				],
				'default' => 'left',
				'condition' => [
					'layout' => ['tabs'],
					'tabs_style' => ['vertical'],
				],
			]
		);

		$this->add_control(
			'accordion_height',
			[
				'label' => __('Accordion Item`s Height', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'full-height' => __('Full Height', 'thegem'),
					'fixed-height' => __('Fixed Height', 'thegem'),
				],
				'default' => 'full-height',
				'condition' => [
					'layout' => ['accordion'],
				],
			]
		);

		$this->add_control(
			'stretch_background',
			[
				'label' => __('Stretch Background', 'thegem'),
				'return_value' => '1',
				'default' => '0',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'layout' => ['one_by_one'],
				],
			]
		);

		$this->end_controls_section();

		// Description Section
		$this->start_controls_section(
			'section_description',
			[
				'label' => __('"Description" Section', 'thegem'),
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __('Description Section', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'description_title',
			[
				'label' => __('Description Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Description', 'thegem'),
				'condition' => [
					'description' => '1',
				],
			]
		);

		$this->add_control(
			'description_tab_source',
			[
				'label' => __('Description Tab Source', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Product Extra Description', 'thegem'),
					'page_builder' => __('Product Content (Page Builder)', 'thegem'),
				],
				'default' => 'default',
				'condition' => [
					'description' => '1',
				],
				'description' => __(' "Product Extra Description": description tab will be populated by content added to "Product Extra Description" text area in a product; "Page Builder": description tab will be populated by content created in page builder in a product. ', 'thegem'),
			]
		);

		$this->end_controls_section();

		// Additional Info Section
		$this->start_controls_section(
			'section_additional',
			[
				'label' => __('"Additional Info" Section', 'thegem'),
			]
		);

		$this->add_control(
			'additional',
			[
				'label' => __('Additional Info Section', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'additional_title',
			[
				'label' => __('Additional Info Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Additional Info', 'thegem'),
				'condition' => [
					'additional' => '1',
				],
			]
		);

		$this->end_controls_section();

		// Reviews Section
		$this->start_controls_section(
			'section_reviews',
			[
				'label' => __('"Reviews" Section', 'thegem'),
			]
		);

		$this->add_control(
			'reviews',
			[
				'label' => __('Reviews Section', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'reviews_title',
			[
				'label' => __('Reviews Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Reviews', 'thegem'),
				'condition' => [
					'reviews' => '1',
				],
			]
		);

		$this->add_control(
			'reviews_columns',
			[
				'label' => __('Reviews Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1x' => __('One Column', 'thegem'),
					'2x' => __('Two Columns', 'thegem'),
				],
				'default' => '2x',
				'condition' => [
					'reviews' => '1',
				],
			]
		);

		$this->add_control(
			'reviews_inner_title',
			[
				'label' => __('"Reviews" Title', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'reviews' => '1',
				],
				'selectors_dictionary' => [
					'' => 'display: none;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reviews_inner_title_add',
			[
				'label' => __('"Add a review" Title', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'reviews' => '1',
				],
				'selectors_dictionary' => [
					'' => 'display: none;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reviews_inner_title_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'title-h1' => __('Title H1', 'thegem'),
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
				'default' => '',
				'condition' => [
					'reviews' => '1',
				],
			]
		);

		$this->add_control(
			'reviews_inner_title_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'light' => __('Default', 'thegem'),
					'bold' => __('Bold', 'thegem'),
				],
				'default' => 'light',
				'condition' => [
					'reviews' => '1',
				],
			]
		);

		$this->add_control(
			'reviews_inner_title_letter_spacing',
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
				'condition' => [
					'reviews' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'reviews_inner_title_text_transform',
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
				'condition' => [
					'reviews' => '1',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title span' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title span' => '{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Reviews Section
		$this->start_controls_section(
			'section_additional_tabs',
			[
				'label' => __('Additional Tabs', 'thegem'),
			]
		);

		$this->add_control(
			'additional_tabs',
			[
				'label' => __('Additional Tabs', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->end_controls_section();

		// Style -> "General"
		$this->start_controls_section(
			'general_section_styles',
			[
				'label' => __('General', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tabs_titles_color',
			[
				'label' => __('Tabs Titles Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .product-tabs__nav-item' => 'color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .product-accordion__item-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .product-one-by-one__item-title h4' => 'color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .product-tabs__nav-slide' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tabs_titles_divider_color',
			[
				'label' => __('Tabs Titles Divider Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .product-tabs__nav-line' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-Reviews .comment-form input' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-Reviews .comment-form textarea' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-Reviews .comment-form .comment-form-rating select' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-Reviews .comment-form .comment-form-cookies-consent .checkbox-sign' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_back',
			[
				'label' => __('Description Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '#F4F6F7',
				'condition' => [
					'layout' => ['one_by_one'],
				],
			]
		);

		$this->add_control(
			'additional_back',
			[
				'label' => __('Additional Info Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'condition' => [
					'layout' => ['one_by_one'],
				],
			]
		);

		$this->add_control(
			'reviews_back',
			[
				'label' => __('Review Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '#F4F6F7',
				'condition' => [
					'layout' => ['one_by_one'],
				],
			]
		);

		$this->end_controls_section();

        // Style -> "Description" Section
		$this->start_controls_section(
			'description_section_styles',
			[
				'label' => __('"Description" Section', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'description' => '1',
				],
			]
		);

		$this->add_control(
			'description_title_color',
			[
				'label' => __('Title Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'description' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description h1, {{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .title-h1' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description h1, {{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .title-h1' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description h2, {{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .title-h2' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description h2, {{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .title-h2' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description h3, {{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .title-h3' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description h3, {{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .title-h3' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description h4, {{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .title-h4' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description h4, {{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .title-h4' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description h5, {{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .title-h5' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description h5, {{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .title-h5' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description h6, {{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .title-h6' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description h6, {{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .title-h6' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .title-xlarge' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .title-xlarge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'description' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .styled-subtitle' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .styled-subtitle' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .main-menu-item' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .main-menu-item' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .text-body' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .text-body' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description .text-body-tiny' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__item-body.description .text-body-tiny' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_padding',
			[
				'label' => esc_html__('Paddings', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'condition' => [
					'description' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__container.description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style -> "Additional Info" Section
		$this->start_controls_section(
			'additional_section_styles',
			[
				'label' => __('"Additional Info" Section', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'additional_titles_color',
			[
				'label' => __('Titles Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'additional' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' table.woocommerce-product-attributes .woocommerce-product-attributes-item__label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'additional_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'additional' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' table.woocommerce-product-attributes .woocommerce-product-attributes-item__value' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'additional_padding',
			[
				'label' => esc_html__('Paddings', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'condition' => [
					'additional' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.additional' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__container.additional' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style -> "Reviews" section
		$this->start_controls_section(
			'reviews_section_styles',
			[
				'label' => __('"Reviews" Section', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'reviews' => '1',
				],
			]
		);

		$this->add_control(
			'reviews_titles_color',
			[
				'label' => __('Titles Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'reviews' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reviews_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'reviews' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment_container .meta strong' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment_container .meta time' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-text .description' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form label' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form input' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form textarea' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form .comment-form-cookies-consent .checkbox-sign.checked::before' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-noreviews' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reviews_btn_text_color',
			[
				'label' => __('Button Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'reviews' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form .gem-button.submit' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-tabs__nav-item sup' => 'color: {{VALUE}};',
                ],
			]
		);

		$this->add_control(
			'reviews_btn_text_color_hover',
			[
				'label' => __('Button Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'reviews' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form .gem-button.submit:hover' => 'color: {{VALUE}};',
                ],
			]
		);

		$this->add_control(
			'reviews_btn_background_color',
			[
				'label' => __('Button Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'reviews' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form .gem-button.submit' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-tabs__nav-item sup' => 'background-color: {{VALUE}};',
                ],
			]
		);

		$this->add_control(
			'reviews_btn_background_color_hover',
			[
				'label' => __('Button Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'reviews' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form .gem-button.submit:hover' => 'background-color: {{VALUE}};',
                ],
			]
		);

		$this->add_control(
			'reviews_stars_base_color',
			[
				'label' => __('Reviews Stars Base Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'reviews' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .star-rating:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form-rating .stars a:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'reviews_stars_rated_color',
			[
				'label' => __('Reviews Stars Rated Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'reviews' => '1',
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .star-rating > span:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form-rating .stars a.rating-on:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'reviews_padding',
			[
				'label' => esc_html__('Paddings', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'condition' => [
					'reviews' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-accordion__item-body.reviews' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .product-one-by-one__container.reviews' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		// General params
		$params = array_merge(array(

		), $settings);

		// Init Sku
		ob_start();
		$product = thegem_templates_init_product();

		if (empty($product)) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$tabs = $this->product_tabs_callback($params);

		// YITH WooCommerce Tab Manager pro (check if choose product)
		if (thegem_is_plugin_active('yith-woocommerce-tab-manager-premium/init.php')) {
			foreach ($tabs as $key => $tab) {
				if (isset($tab['type']) && $tab['type'] == 'ywtm_tab') {
					if (isset($tab['ywtm_tab']['type']) && $tab['ywtm_tab']['type'] == 'product' && !empty($tab['ywtm_tab']['product_ids'])){
						if (!in_array($product->get_id(), $tab['ywtm_tab']['product_ids'])) {
							unset( $tabs[$key] );
						}
					}
				}
			}
		}

		$tabs_count = count($tabs);

		if (empty($tabs)) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		?>

        <div <?php if (!empty($params['element_id'])): ?>id="<?=esc_attr($params['element_id']); ?>"<?php endif;?>
             class="<?= $this->get_widget_wrapper() ?>">

	        <?php if ($params['layout'] == 'tabs'):
		        $tabs_class = 'product-tabs--'.$params['tabs_style'];
                $tabs_align = !empty($params['tabs_align_horizontal']) ? $params['tabs_align_horizontal'] : $params['tabs_align_vertical'];
		        $tabs_class_position = 'product-tabs__nav--'.$tabs_align;
		        ?>
                <div class="product-tabs <?=$tabs_class?>" data-type="<?= $params['tabs_style'] ?>">
			        <?php if ($tabs_count > 1) : ?>
                        <div class="product-tabs__nav <?= $tabs_class_position ?>">
                            <div class="product-tabs__nav-list">
						        <?php $is_first = true; foreach ( $tabs as $key => $tab ): ?>
                                    <div class="product-tabs__nav-item <?php if($is_first): ?>product-tabs__nav-item--active<?php $is_first = false; endif; ?>" data-id="<?= esc_attr( $key ); ?>">
                                        <span><?= wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ); ?></span>
                                    </div>
						        <?php endforeach; ?>
                            </div>
                            <div class="product-tabs__nav-line">
                                <div class="product-tabs__nav-slide" style="opacity: 0;"></div>
                            </div>
                        </div>
			        <?php endif; ?>
                    <div class="product-tabs__body" <?php if ($tabs_count == 1) : ?>style="max-width: 100%;"<?php endif; ?>>
				        <?php $is_first = true; foreach ( $tabs as $key => $tab ):
					        $short_class = (isset($tab['type']) && ($tab['type'] == 'ywtm_tab' || $tab['type'] == 'additional_tab')) ? 'description' : preg_replace("/\_.+/", "", $key);
                            $reviews_columns = ($short_class == 'reviews') ? 'reviews-column-'.$params['reviews_columns'] : null;
                        ?>
                            <div class="product-accordion__item">
                                <div class="product-accordion__item-title <?= esc_attr( $key ); ?> <?php if($is_first): ?>product-accordion__item--active<?php endif;?>" data-id="<?= esc_attr( $key ); ?>">
                                    <span><?= wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ); ?></span>
                                </div>
                                <div class="product-accordion__item-body <?= $short_class ?> <?= $reviews_columns ?>"
						             <?php if($is_first): ?>style="display: block;"<?php endif; $is_first = false;?> data-id="<?= esc_attr( $key ); ?>">
							         <?php
                                         if ( !empty( $tab['callback'] ) ) {
                                             $product = thegem_templates_init_product();
                                             call_user_func( $tab['callback'], $key, $tab );
                                         }

                                         if(isset($tab['type']) && $tab['type'] == 'additional_tab') {
                                             if (!empty($tab['text_content'])) {
                                                 echo $tab['text_content'];
                                             }

                                             if (!empty($tab['section_content'])) {
                                                 echo $tab['section_content'];
                                             }
                                         }

                                         if(isset($tab['type']) && $tab['type'] == 'ywtm_tab') {
                                             echo $tab['ywtm_tab']['data'];
                                         }

								         if(isset($tab['type']) && $tab['type'] == 'yikes_type') {
									         echo $tab['text_content'];
								         }
                                     ?>
                                </div>
                            </div>
				        <?php endforeach; ?>
                    </div>
                </div>
	        <?php endif; ?>

	        <?php if ($params['layout'] == 'accordion'):
		        $accordion_class = 'product-accordion--'.$params['accordion_height'];
		        ?>
                <div class="product-accordion <?=$accordion_class?>">
			        <?php $is_first = true; foreach ( $tabs as $key => $tab ):
				        $short_class = (isset($tab['type']) && ($tab['type'] == 'ywtm_tab' || $tab['type'] == 'additional_tab')) ? 'description' : preg_replace("/\_.+/", "", $key);
                        $reviews_columns = ($short_class == 'reviews') ? 'reviews-column-'.$params['reviews_columns'] : null;
                    ?>
                        <div class="product-accordion__item">
                            <div class="product-accordion__item-title <?= esc_attr( $key ); ?> <?php if($is_first):?>product-accordion__item--active<?php endif; ?>" data-id="<?= esc_attr( $key ); ?>">
                                <span><?= wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ); ?></span>
                            </div>
                            <div class="product-accordion__item-body <?= $short_class ?> <?= $reviews_columns ?>"
                                <?php if($is_first): ?>style="display: block;"<?php endif; $is_first = false;?> data-id="<?= esc_attr( $key ); ?>">
	                            <?php
                                    if ( !empty( $tab['callback'] ) ) {
                                        $product = thegem_templates_init_product();
                                        call_user_func( $tab['callback'], $key, $tab );
                                    }

                                    if(isset($tab['type']) && $tab['type'] == 'additional_tab') {
                                        if (!empty($tab['text_content'])) {
                                            echo $tab['text_content'];
                                        }

                                        if (!empty($tab['section_content'])) {
                                            echo $tab['section_content'];
                                        }
                                    }

                                    if(isset($tab['type']) && $tab['type'] == 'ywtm_tab') {
                                        echo $tab['ywtm_tab']['data'];
                                    }

		                            if(isset($tab['type']) && $tab['type'] == 'yikes_type') {
			                            echo $tab['text_content'];
		                            }
                                ?>
                            </div>
                        </div>
			        <?php endforeach; ?>
                </div>
	        <?php endif; ?>

	        <?php if ($params['layout'] == 'one_by_one'):
		        $one_by_one_back['description'] = $params['description_back'];
		        $one_by_one_back['additional_information'] = $params['additional_back'];
		        $one_by_one_back['reviews'] = $params['reviews_back'];
		        $isColorBack = !empty($one_by_one_back['description']) || !empty($one_by_one_back['additional_information']) || !empty($one_by_one_back['reviews']);
		        ?>
                <div class="product-one-by-one <?php if ($params['stretch_background']): ?>stretch<?php endif;?>">
			        <?php foreach ( $tabs as $key => $tab ):
				        $short_class = (isset($tab['type']) && ($tab['type'] == 'ywtm_tab' || $tab['type'] == 'additional_tab')) ? 'description' : preg_replace("/\_.+/", "", $key);
                        $reviews_columns = ($short_class == 'reviews') ? 'reviews-column-'.$params['reviews_columns'] : null;
                    ?>
                        <div class="product-one-by-one__item <?php if (!$isColorBack): ?>separator<?php endif;?>" <?php if (!empty($one_by_one_back[$key])): ?>style="background-color: <?=esc_attr($one_by_one_back[$key])?>;"<?php endif;?>>
                            <div class="product-one-by-one__container <?= $short_class ?> <?= $reviews_columns ?>">
						        <?php if ($key != 'reviews'): ?>
                                    <div class="product-one-by-one__item-title">
                                        <h4 class="light"><?= wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ); ?></h4>
                                    </div>
						        <?php endif; ?>
                                <div class="product-one-by-one__item-body">
	                                <?php
                                        if ( !empty( $tab['callback'] ) ) {
                                            $product = thegem_templates_init_product();
                                            call_user_func( $tab['callback'], $key, $tab );
                                        }

                                        if(isset($tab['type']) && $tab['type'] == 'additional_tab') {
                                            if (!empty($tab['text_content'])) {
                                                echo $tab['text_content'];
                                            }

                                            if (!empty($tab['section_content'])) {
                                                echo $tab['section_content'];
                                            }
                                        }

                                        if(isset($tab['type']) && $tab['type'] == 'ywtm_tab') {
                                            echo $tab['ywtm_tab']['data'];
                                        }

		                                if(isset($tab['type']) && $tab['type'] == 'yikes_type') {
			                                echo $tab['text_content'];
		                                }
                                    ?>
                                </div>
                            </div>
                        </div>
			        <?php endforeach; ?>
                </div>
	        <?php endif; ?>
        </div>

        <script>
            (function ($) {
                const $wrap = $('.thegem-te-product-tabs .woocommerce-Reviews');
                const $titles = $('.woocommerce-Reviews-title, .woocommerce-Reviews-title span, .comment-reply-title, .comment-reply-title span', $wrap);
                const titleStyledClasses =
                    `<?= $params['reviews_inner_title_style'] ?>
                     <?= $params['reviews_inner_title_font_weight'] ?>`;
                $titles.addClass(titleStyledClasses);

				<?php if ($params['reviews_inner_title_font_weight'] == 'bold'): ?>
                $titles.removeClass('light');
				<?php endif; ?>
            })(jQuery);
        </script>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}

}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateProductTabs());
}