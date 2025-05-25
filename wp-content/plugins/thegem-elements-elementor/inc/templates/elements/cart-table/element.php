<?php

namespace TheGem_Elementor\Widgets\TemplateCartTable;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Product Title.
 */

#[\AllowDynamicProperties]
class TheGem_TemplateCartTable extends Widget_Base {
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
		return 'thegem-template-cart-table';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'cart';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Cart Table', 'thegem');
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
		return ['thegem_cart_builder'];
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-cart-table';
	}

	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		return ' .'.$this->get_widget_wrapper();
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'cart_table',
			[
				'label' => __('Cart Table', 'thegem'),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'compact' => __('Compact', 'thegem'),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'thumbnail_size',
			[
				'label' => __('Thumbnail Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'small' => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
				],
				'default' => 'small',
			]
		);

		$this->add_control(
			'column_headers',
			[
				'label' => __('Column Headers', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'column_headers_text_style',
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
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'column_headers' => '1',
				],
			]
		);

		$this->add_control(
			'column_headers_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => 'light',
				'condition' => [
					'column_headers' => '1',
				],
			]
		);

		$this->add_control(
			'column_headers_letter_spacing',
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
					'column_headers' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .shop_table thead tr th span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'column_headers_text_transform',
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
					'column_headers' => '1',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .shop_table thead tr th span' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_vertical_align',
			[
				'label' => __('Vertical Align', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __('Top', 'thegem'),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'thegem'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'middle',
				'condition' => [
					'layout' => '',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .shop_table tr td' => 'vertical-align: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'dividers',
			[
				'label' => __('Dividers', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'apply_coupon',
			[
				'label' => __('Apply Coupon', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'coupon_placeholder_text',
			[
				'label' => __('Placeholder Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Coupon code', 'woocommerce' ),
				'condition' => [
					'apply_coupon' => '1',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_heading',
			[
				'label' => __('Apply Coupon Button', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'apply_coupon' => '1',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Apply coupon', 'woocommerce' ),
				'condition' => [
					'apply_coupon' => '1',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_text_weight',
			[
				'label' => __('Text Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal' => __('Bold', 'thegem'),
					'thin' => __('Thin', 'thegem'),
				],
				'condition' => [
					'apply_coupon' => '1',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_text_transform',
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
					'apply_coupon' => '1',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-cart-form .actions .coupon button' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'update_cart_btn_heading',
			[
				'label' => __('Update Cart Button', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'update_cart_automatically',
			[
				'label' => __('Update Cart Automatically', 'thegem'),
				'return_value' => '1',
				'default' => '0',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'update_cart_btn_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Update cart', 'woocommerce' ),
				'condition' => [
					'update_cart_automatically!' => '1',
				],
			]
		);

		$this->add_control(
			'update_cart_btn_text_weight',
			[
				'label' => __('Text Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal' => __('Bold', 'thegem'),
					'thin' => __('Thin', 'thegem'),
				],
				'condition' => [
					'update_cart_automatically!' => '1',
				],
			]
		);

		$this->add_control(
			'update_cart_btn_text_transform',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .actions .submit-buttons button.button' => '{{VALUE}};',
				],
				'condition' => [
					'update_cart_automatically!' => '1',
				],
			]
		);

		$this->end_controls_section();

		// Style -> Headers
		$this->start_controls_section(
			'column_headers_styles',
			[
				'label' => __('Heading', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'column_headers' => '1',
				]
			]
		);

		$this->add_control(
			'column_headers_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table thead tr th span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'column_headers' => '1',
				]
			]
		);

		$this->end_controls_section();

		// Style -> Content
		$this->start_controls_section(
			'content_styles',
			[
				'label' => __('Table Content', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_text_color',
			[
				'label' => __('Text color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tbody td' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tbody td input[type="number"]' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table #coupon_code' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'content_links_text_color',
			[
				'label' => __('Links Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tbody td a' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'content_links_hover_color',
			[
				'label' => __('Links Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tbody td a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'content_subtotal_color',
			[
				'label' => __('Subtotal Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tbody td.product-subtotal' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'loading_overlay_color',
			[
				'label' => __('Loading Overlay Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .blockOverlay' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		// Style -> Apply Coupon
		$this->start_controls_section(
			'dividers_styles',
			[
				'label' => __('Dividers & Forms', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dividers_color',
			[
				'label' => __('Dividers & Borders Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tbody td' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tbody td input[type="number"]' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tbody td .quantity' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' form.woocommerce-cart-form.compact table.shop_table_responsive.shop_table.woocommerce-cart-form__contents tbody tr + tr' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tbody td .quantity button:before' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table #coupon_code' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tr td.product-remove .remove' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table tr td.product-remove .remove:before, {{WRAPPER}} '.$this->get_customize_class().' .shop_table tr td.product-remove .remove:after' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'coupon_placeholder_color',
			[
				'label' => __('Сoupon Input Placeholder Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .shop_table #coupon_code::placeholder' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'apply_coupon' => '1',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_border_radius',
			[
				'label' => __('Сoupon Input Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'apply_coupon' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .shop_table #coupon_code' => 'border-radius:{{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'qty_border_radius',
			[
				'label' => __('Quantity Control Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .shop_table .product-quantity .quantity' => 'border-radius:{{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		// Style -> Apply Coupon
		$this->start_controls_section(
			'apply_coupon_styles',
			[
				'label' => __('Apply Coupon Button', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'apply_coupon' => '1',
				]
			]
		);

		$this->add_responsive_control(
			'apply_coupon_btn_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 6,
					],
				],
				'condition' => [
					'apply_coupon' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .woocommerce-cart-form .actions .coupon button' => 'border-width: {{SIZE}}{{UNIT}} !important; line-height: calc(40{{UNIT}} - calc({{SIZE}}{{UNIT}}*2)) !important;',
				],
			]
		);

		$this->add_responsive_control(
			'apply_coupon_btn_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'apply_coupon' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .woocommerce-cart-form .actions .coupon button' => 'border-radius:{{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'apply_coupon' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .woocommerce-cart-form .actions .coupon button' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'apply_coupon' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .woocommerce-cart-form .actions .coupon button:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_background_color',
			[
				'label' => __('Background color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'apply_coupon' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .woocommerce-cart-form .actions .coupon button' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'apply_coupon' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .woocommerce-cart-form .actions .coupon button:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'apply_coupon' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .woocommerce-cart-form .actions .coupon button' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'apply_coupon_btn_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'apply_coupon' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .woocommerce-cart-form .actions .coupon button:hover' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		// Style -> Update Cart
		$this->start_controls_section(
			'update_cart_styles',
			[
				'label' => __('Update Cart Button', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'update_cart_automatically!' => '1',
				],
			]
		);

		$this->add_responsive_control(
			'update_cart_btn_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 6,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .actions .submit-buttons button.button' => 'border-width: {{SIZE}}{{UNIT}} !important; line-height: calc(40{{UNIT}} - calc({{SIZE}}{{UNIT}}*2)) !important;',
				],
			]
		);

		$this->add_responsive_control(
			'update_cart_btn_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .actions .submit-buttons button.button' => 'border-radius:{{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'update_cart_btn_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .actions .submit-buttons button.button' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'update_cart_btn_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .actions .submit-buttons button.button:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'update_cart_btn_background_color',
			[
				'label' => __('Background color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .actions .submit-buttons button.button' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'update_cart_btn_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .actions .submit-buttons button.button:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'update_cart_btn_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .actions .submit-buttons button.button' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'update_cart_btn_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .actions .submit-buttons button.button:hover' => 'border-color: {{VALUE}} !important;',
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
			'layout' => '',
			'thumbnail_size' => 'small',
			'column_headers' => 1,
			'column_headers_text_style' => '',
			'column_headers_font_weight' => 'light',
			'dividers' => 1,
			'apply_coupon' => 1,
			'coupon_placeholder_text' => __( 'Coupon code', 'woocommerce' ),
			'apply_coupon_btn_text_weight' => 'normal',
			'update_cart_btn_text_weight' => 'normal',
			'apply_coupon_btn_text' => __( 'Apply coupon', 'woocommerce' ),
			'update_cart_btn_text' => __( 'Update cart', 'woocommerce' ),
			'update_cart_automatically' => 0,
		), $settings);

		// Init Title
		ob_start();

		wc_load_cart();

		if (empty(WC()->cart)) {
			ob_end_clean();
			return thegem_templates_close_single_post($this->get_name(), $this->shortcode_settings(), '');
		}

		$headers_classes = implode(' ', array($params['column_headers_text_style'], $params['column_headers_font_weight']));
		?>

		<div class="<?= $this->get_widget_wrapper() ?>">
		<form class="woocommerce-cart-form <?php echo(!empty($params['update_cart_automatically']) ? ' update-cart-automatically' : ''); ?><?php echo(!empty($params['layout'] === 'compact') ? ' compact' : ''); ?>" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<div class="gem-table"><table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents<?php echo(empty($params['dividers']) ? ' no-dividers' : ''); ?>" cellspacing="0">
				<?php if(!empty($params['column_headers'])) : ?>
				<thead>
					<tr>
						<th class="product-name" colspan="3"><span class="<?= esc_attr($headers_classes); ?>"><?php esc_html_e( 'Product', 'woocommerce' ); ?></span></th>
						<th class="product-quantity"><span class="<?= esc_attr($headers_classes); ?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></span></th>
						<th class="product-subtotal"><span class="<?= esc_attr($headers_classes); ?>"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span></th>
					</tr>
				</thead>
				<?php endif; ?>
				<tbody>
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>
							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

								<td class="product-remove">
									<?php
										echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"></a>',
												esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
												esc_html__( 'Remove this item', 'woocommerce' ),
												esc_attr( $product_id ),
												esc_attr( $_product->get_sku() )
											),
											$cart_item_key
										);
									?>
								</td>

								<td class="product-thumbnail">
								<?php
								$product_image = $_product->get_image();
								if($params['thumbnail_size'] == 'medium') {
									if ( $_product->get_image_id() ) {
										$product_image = thegem_get_thumbnail_image($_product->get_image_id(), 'thegem-product-thumbnail-vertical-2x');
									} elseif ( $_product->get_parent_id() ) {
										$parent_product = wc_get_product( $_product->get_parent_id() );
										if ( $parent_product && $parent_product->get_image_id()) {
											$product_image = thegem_get_thumbnail_image($parent_product->get_image_id(), 'thegem-product-thumbnail-vertical-2x');
										}
									}
								}
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $product_image, $cart_item, $cart_item_key );

								if ( ! $product_permalink ) {
									echo $thumbnail; // PHPCS: XSS ok.
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
								}
								?>
								</td>

								<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
								<?php
								if ( ! $product_permalink ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
								} else {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
								}

								do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

								// Meta data.
								echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

								// Backorder notification.
								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
								}

								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
								?>
								</td>


								<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
								<?php
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $_product->get_max_purchase_quantity(),
											'min_value'    => '0',
											'product_name' => $_product->get_name(),
										),
										$_product,
										false
									);
								}

								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
								?>
								</td>

								<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
									<?php
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
									?>
								</td>
							</tr>
							<?php
						}
					}
					?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>

					<tr>
						<td colspan="6" class="actions"><div class="actions-inner">

							<?php if ( wc_coupons_enabled() && !empty($params['apply_coupon']) ) : ?>
								<div class="coupon">
									<input type="text" name="coupon_code" class="input-text coupon-code" id="coupon_code" value="" placeholder="<?= esc_attr($params['coupon_placeholder_text']); ?>" />
									<?php
										thegem_button(array(
											'tag' => 'button',
											'text' => esc_html($params['apply_coupon_btn_text']),
											'style' => 'outline',
											'size' => 'small',
											'text_weight' => $params['apply_coupon_btn_text_weight'],
											'attributes' => array(
												'name' => 'apply_coupon',
												'value' => esc_attr($params['apply_coupon_btn_text']),
												'type' => 'submit',
												'class' => 'button gem-button-tablet-size-small'
											)
										), true);
									?>
									<?php do_action( 'woocommerce_cart_coupon' ); ?>
								</div>
							<?php endif; ?>


							<div class="submit-buttons"<?php echo ($params['update_cart_automatically'] ? ' style="display: none;"' : ''); ?>>
								<?php
									thegem_button(array(
										'tag' => 'button',
										'text' => esc_html($params['update_cart_btn_text']),
										'size' => 'small',
										'extra_class' => 'update-cart',
										'text_weight' => $params['update_cart_btn_text_weight'],
										'attributes' => array(
											'name' => 'update_cart',
											'value' => esc_attr($params['update_cart_btn_text']),
											'type' => 'submit',
											'class' => 'button gem-button-tablet-size-small'
										)
									), true);
								?>

								<?php do_action( 'woocommerce_cart_actions' ); ?>
							</div>

							<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
						</div></td>
					</tr>

					<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				</tbody>
			</table></div>
			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>
		<script type="text/javascript">(function($){$('form:not(.cart) div.quantity:not(.buttons_added)').addClass('buttons_added').append('<button type="button" class="plus" >+</button>').prepend('<button type="button" class="minus" >-</button>');})(jQuery);</script>
		</div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_cart(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}

}

if(defined('WC_PLUGIN_FILE')){
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCartTable());
}