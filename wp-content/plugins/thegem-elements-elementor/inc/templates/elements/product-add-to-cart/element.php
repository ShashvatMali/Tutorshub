<?php

namespace TheGem_Elementor\Widgets\TemplateProductAddToCart;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

#[\AllowDynamicProperties]
class TheGem_TemplateProductAddToCart extends Widget_Base {
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
		return 'thegem-template-product-add-to-cart';
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
		return __('Product Add to Cart', 'thegem');
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
		return 'thegem-te-product-add-to-cart';
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		// Content -> Layout section
		$this->start_controls_section(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'general_layout',
			[
				'label' => __('General layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'column' => __('Vertical', 'thegem'),
					'row' => __('Inline', 'thegem'),
				],
				'default' => 'column',
			]
		);

		$this->add_control(
			'add_to_cart_section_layout',
			[
				'label' => __('"Add to cart" section layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'column' => __('Vertical', 'thegem'),
					'row' => __('Inline', 'thegem'),
				],
				'default' => 'row',
			]
		);

		$this->add_control(
			'attributes_section_layout',
			[
				'label' => __('"Attributes" section layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'column' => __('Vertical', 'thegem'),
					'row' => __('Inline', 'thegem'),
				],
				'default' => 'column',
			]
		);

		$this->add_control(
			'horizontal_alignment',
			[
				'label' => __('Horizontal Alignment', 'thegem'),
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
				],
				'default' => 'left',
			]
		);

		$this->add_control(
			'in_stock_amount',
			[
				'label' => __('In Stock Amount', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		// Content -> "Add to cart" section
		$this->start_controls_section(
			'add_to_cart_layout',
			[
				'label' => __('"Add to cart" button', 'thegem'),
			]
		);

		$this->add_control(
			'add_to_cart_btn',
			[
				'label' => __('Button', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'add_to_cart_btn_ajax',
			[
				'label' => __('AJAX Add to Cart', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
				'description' => sprintf(
					__('Note: this setting will not work in case the "Redirect to the cart page after successful addition" setting is activated in %1$sWooCommerce → Settings → Products → General%2$s.', 'thegem'),
					'<a href="' . admin_url('admin.php?page=wc-settings&tab=products') . '" target="_blank">',
					'</a>'
				),
			]
		);

		$this->add_control(
			'add_to_cart_btn_width',
			[
				'label' => __('Button width', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'fullwidth' => __('Fullwidth', 'thegem'),
					'custom' => __('Custom width', 'thegem'),
				],
				'condition' => [
					'add_to_cart_btn' => 'yes',
					'add_to_cart_section_layout' => 'column'
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'add_to_cart_btn_width_inline',
			[
				'label' => __('Button width', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'custom' => __('Custom width', 'thegem'),
				],
				'condition' => [
					'add_to_cart_btn' => 'yes',
					'add_to_cart_section_layout' => 'row'
				],
				'default' => 'default',
			]
		);

		$this->add_responsive_control(
			'add_to_cart_btn_width_custom',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'add_to_cart_section_layout',
									'operator' => '==',
									'value' => 'column',
								],
								[
									'name' => 'add_to_cart_btn_width',
									'operator' => '==',
									'value' => 'custom',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'add_to_cart_section_layout',
									'operator' => '==',
									'value' => 'row',
								],
								[
									'name' => 'add_to_cart_btn_width_inline',
									'operator' => '==',
									'value' => 'custom',
								],
							],
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .single_add_to_cart_button' => 'width:{{SIZE}}{{UNIT}};',
				],
				'description' => sprintf(
					__('To style "Add to cart" button go to %1$sTheme Options → WooCommerce → Elements & Styles → Buttons%2$s.', 'thegem'),
					'<a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/woocommerce/product-styles" target="_blank">',
					'</a>'
				),
			]
		);

		$this->end_controls_section();

		// Content -> "Add to wishlist" section
		$this->start_controls_section(
			'add_to_wishlist_layout',
			[
				'label' => __('"Add to wishlist" button', 'thegem'),
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
			]
		);

		$this->add_control(
			'add_to_wishlist_btn',
			[
				'label' => __('Button', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'description' => __('This works only if YITH WooCommerce Wishlist plugin is active', 'thegem'),
			]
		);

		$this->end_controls_section();

		// Content -> "Amount" section
		$this->start_controls_section(
			'amount_layout',
			[
				'label' => __('"Amount" control', 'thegem'),
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
			]
		);

		$this->add_control(
			'amount_control',
			[
				'label' => __('"Amount" control', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'amount_control_width',
			[
				'label' => __('"Amount" control width', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'fullwidth' => __('Fullwidth', 'thegem'),
					'custom' => __('Custom width', 'thegem'),
				],
				'condition' => [
					'amount_control' => 'yes',
				],
				'default' => 'default',

			]
		);

		$this->add_responsive_control(
			'amount_control_width_custom',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'amount_control_width' => 'custom',
					'amount_control' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .quantity' => 'width:{{SIZE}}{{UNIT}}; max-width: none;',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .quantity input' => 'width: calc({{SIZE}}{{UNIT}} - 80px);',
				],
			]
		);

		$this->end_controls_section();

		// Content -> "Attributes" section
		$this->start_controls_section(
			'attributes_layout',
			[
				'label' => __('"Attributes" section', 'thegem'),
			]
		);

		$this->add_control(
			'attributes',
			[
				'label' => __('Attributes', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'attribute_label',
			[
				'label' => __('Attribute label', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'side' => __('Side', 'thegem'),
					'top' => __('Top', 'thegem'),
					'hide' => __('Hide', 'thegem'),
				],
				'condition' => [
					'attributes' => 'yes',
				],
				'default' => 'side',

			]
		);

		$this->add_responsive_control(
			'attribute_label_width',
			[
				'label' => __('Labels column width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'condition' => [
					'attributes' => 'yes',
				],
				'description' => __('Set custom width for the labels column to align attribute values.', 'thegem'),
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart table.variations th.label' => 'min-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style -> "Add to cart" button
		$this->start_controls_section(
			'add_to_cart_styles',
			[
				'label' => __('"Add To Cart" Button', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'add_to_cart_btn_border_width',
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
					'add_to_cart_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .single_add_to_cart_button' => 'border-width: {{SIZE}}{{UNIT}} !important; line-height: calc(40{{UNIT}} - calc({{SIZE}}{{UNIT}}*2)) !important;',
				],
			]
		);

		$this->add_responsive_control(
			'add_to_cart_btn_border_radius',
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
					'add_to_cart_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .single_add_to_cart_button' => 'border-radius:{{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'add_to_cart_btn_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .single_add_to_cart_button' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'add_to_cart_btn_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .single_add_to_cart_button:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'add_to_cart_btn_background_color',
			[
				'label' => __('Background color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .single_add_to_cart_button' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'add_to_cart_btn_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .single_add_to_cart_button:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'add_to_cart_btn_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .single_add_to_cart_button' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'add_to_cart_btn_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'add_to_cart_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .single_add_to_cart_button:hover' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		// Style -> "Add to wishlist" button
		$this->start_controls_section(
			'add_to_wishlist_styles',
			[
				'label' => __('"Add To Wishlist" Button', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'add_to_wishlist_btn' => 'yes',
				],
			]
		);

		$this->add_control(
			'add_to_wishlist_btn_color',
			[
				'label' => __('Normal Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'add_to_wishlist_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .add_to_wishlist' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'add_to_wishlist_btn_color_hover',
			[
				'label' => __('Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'add_to_wishlist_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .add_to_wishlist:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'add_to_wishlist_btn_color_filled',
			[
				'label' => __('Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'add_to_wishlist_btn' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .remove_from_wishlist' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		// Style -> "Amount" control
		$this->start_controls_section(
			'amount_control_styles',
			[
				'label' => __('"Amount" control', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'amount_control' => 'yes',
					'add_to_cart_btn' => 'yes',
				],
			]
		);

		$this->add_control(
			'amount_control_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .quantity input, {{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .quantity button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'amount_control_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .quantity' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'amount_control_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .quantity' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'amount_control_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .quantity button:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Style -> "Attributes"
		$this->start_controls_section(
			'Attributes_control_styles',
			[
				'label' => __('"Attributes" Section', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'attribute_label_color',
			[
				'label' => __('Label Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'attributes' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart table.variations th.label' => 'color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart table.variations td.label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'attribute_element_text_color',
			[
				'label' => __('Element Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'attributes' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .thegem-combobox-wrap .thegem-combobox__trigger' => 'color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .thegem-combobox-wrap .thegem-combobox__options-item' => 'color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .gem-attribute-selector.type-label .gem-attribute-options li .label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'attribute_element_border_color',
			[
				'label' => __('Element Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'attributes' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .thegem-combobox-wrap .thegem-combobox__trigger' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .thegem-combobox-wrap .thegem-combobox__options' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .gem-attribute-selector .gem-attribute-options li:not(.selected)' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'attribute_element_background_color',
			[
				'label' => __('Element Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'attributes' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .thegem-combobox-wrap .thegem-combobox__trigger' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .thegem-combobox-wrap .thegem-combobox__options-item' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .gem-attribute-selector.type-label .gem-attribute-options li:not(.selected)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'attribute_element_text_color_active',
			[
				'label' => __('Element Text Color Active', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'attributes' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .thegem-combobox-wrap .thegem-combobox__options-item:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .gem-attribute-selector.type-label .gem-attribute-options li.selected .label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'attribute_element_border_color_active',
			[
				'label' => __('Element Border Color Active', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'attributes' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .gem-attribute-selector .gem-attribute-options li.selected' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'attribute_element_background_color_active',
			[
				'label' => __('Element Background Color Active', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'attributes' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .thegem-combobox-wrap .thegem-combobox__options-item:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .gem-attribute-selector.type-label .gem-attribute-options li.selected' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Style -> Price
		$this->start_controls_section(
			'price_styles',
			[
				'label' => __('Price', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __('Price color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .woocommerce-variation-price .price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_color_old',
			[
				'label' => __('Old price color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .woocommerce-variation-price .price del' => 'color: {{VALUE}};',
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .woocommerce-variation-price .price del:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_color_suffix',
			[
				'label' => __('Suffix color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' form.cart .woocommerce-variation-price .woocommerce-Price-currencySymbol' => 'color: {{VALUE}};',
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
			'general_layout' => 'column',
			'add_to_cart_section_layout' => 'row',
			'attributes_section_layout' => 'column',
			'horizontal_alignment' => 'left',
			'add_to_cart_btn' => 'yes',
			'add_to_cart_btn_ajax' => 'yes',
			'add_to_cart_btn_width' => 'default',
			'add_to_wishlist_btn' => 'yes',
			'amount_control' => 'yes',
			'amount_control_width' => 'default',
			'attributes' => 'yes',
			'attribute_label' => 'side',
			'attribute_label_width' => '',
			'in_stock_amount' => 'yes',
		), $settings);

		$params['add_to_cart_btn'] = $params['add_to_cart_btn'] === 'yes' ? 1 : 0;
		$params['add_to_cart_btn_ajax'] = $params['add_to_cart_btn_ajax'] === 'yes' ? 1 : 0;
		$params['add_to_wishlist_btn'] = $params['add_to_wishlist_btn'] === 'yes' ? 1 : 0;
		$params['amount_control'] = $params['amount_control'] === 'yes' ? 1 : 0;
		$params['attributes'] = $params['attributes'] === 'yes' ? 1 : 0;
		$params['in_stock_amount'] = $params['in_stock_amount'] === 'yes' ? 1 : 0;


		// Init Add_To_Cart
		ob_start();
		$product = thegem_templates_init_product();

		if (empty($product)) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return ;
		}

		$this->add_render_attribute( 'wrapper', 'class', $this->get_widget_wrapper() );

        if ($product->get_type() != 'external' && $product->get_type() != 'grouped' && 'yes' !== get_option( 'woocommerce_cart_redirect_after_add' )) {
	        $this->add_render_attribute( 'wrapper', 'data-ajax-load', $params['add_to_cart_btn_ajax'] );
        }

		$this->add_render_attribute( 'wrapper', 'class', 'general-layout-'.$params['general_layout'] );
		$this->add_render_attribute( 'wrapper', 'class', 'alignment-'.$params['horizontal_alignment'] );
		if($params['attributes']) {
			$this->add_render_attribute( 'wrapper', 'class', 'attributes-layout-'.$params['attributes_section_layout'] );
			$this->add_render_attribute( 'wrapper', 'class', 'attributes-label-position-'.$params['attribute_label'] );
		} else {
			$this->add_render_attribute( 'wrapper', 'class', 'attributes-hidden' );
		}
		if($params['add_to_cart_btn']) {
			$this->add_render_attribute( 'wrapper', 'class', 'add-to-cart-layout-'.$params['add_to_cart_section_layout'] );
			if($params['add_to_cart_section_layout'] === 'column' && $params['add_to_cart_btn_width'] === 'fullwidth') {
				$this->add_render_attribute( 'wrapper', 'class', 'add-to-cart-fullwidth' );
			}
		} else {
			$this->add_render_attribute( 'wrapper', 'class', 'add-to-cart-hidden' );
		}
		if(!$params['amount_control']) {
			$this->add_render_attribute( 'wrapper', 'class', 'amount-hidden' );
		} else {
			if($params['amount_control_width'] == 'fullwidth') {
				$this->add_render_attribute( 'wrapper', 'class', 'amount-fullwidth' );
			}
		}
		if(!$params['add_to_wishlist_btn']) {
			$this->add_render_attribute( 'wrapper', 'class', 'add-to-wishlist-hidden' );
		}
		if(!$params['in_stock_amount']) {
			$this->add_render_attribute( 'wrapper', 'class', 'in-stock-amount-hidden' );
		}

		?>

		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?= woocommerce_template_single_add_to_cart() ?>
			<?= thegem_woocommerce_product_page_ajax_notification() ?>
		</div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		//$return_html = $return_html;
		echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}

}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateProductAddToCart());
}