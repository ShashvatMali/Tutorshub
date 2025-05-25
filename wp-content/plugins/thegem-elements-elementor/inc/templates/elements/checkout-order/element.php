<?php

namespace TheGem_Elementor\Widgets\TemplateCheckoutOrder;

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
class TheGem_TemplateCheckoutOrder extends Widget_Base {
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
		return 'thegem-template-checkout-order';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'checkout';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Order Review', 'thegem');
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
		return ['thegem_checkout_builder'];
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-checkout-order';
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

		// Heading Section
		$this->start_controls_section(
			'chackout_order',
			[
				'label' => __('Order Review', 'thegem'),
			]
		);

		$this->add_control(
			'heading',
			[
				'label' => __('Heading', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'heading_alignment',
			[
				'label' => __('Text Alignment', 'thegem'),
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
				'condition' => [
					'heading' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .checkout-order-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_text_style',
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
					'heading' => '1',
				],
			]
		);

		$this->add_control(
			'heading_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => 'light',
				'condition' => [
					'heading' => '1',
				],
			]
		);

		$this->add_control(
			'heading_letter_spacing',
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
					'heading' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .checkout-order-title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_text_transform',
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
					'heading' => '1',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .checkout-order-title' => '{{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'condition' => [
					'heading' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .checkout-order-title' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				],
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
			'divider_top',
			[
				'label' => __('Top Divider', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'dividers' => '1',
				]
			]
		);
  
		$this->add_control(
			'divider_bottom',
			[
				'label' => __('Bottom Divider', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'dividers' => '1',
				]
			]
		);

		$this->end_controls_section();
		
		// Button Section
		if (defined('WC_GERMANIZED_VERSION')) {
			$this->start_controls_section(
				'payment_button_section',
				[
					'label' => __('Buy Now Button', 'thegem'),
				]
			);
			
			$this->add_control(
				'payment_btn',
				[
					'label' => __('Button', 'thegem'),
					'return_value' => '1',
					'default' => '1',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
				]
			);
			
			$this->add_control(
				'payment_btn_alignment',
				[
					'label' => __('Button Alignment', 'thegem'),
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
						'fullwidth' => [
							'title' => __('Fullwidth', 'thegem'),
							'icon' => 'eicon-text-align-justify',
						],
					],
					'default' => 'fullwidth',
					'condition' => [
						'payment_btn' => '1',
					],
				]
			);
			
			$this->end_controls_section();
        }

		// Style -> Heading
		$this->start_controls_section(
			'heading_section_styles',
			[
				'label' => __('Heading', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'heading' => '1',
				]
			]
		);

		$this->add_control(
			'heading_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .checkout-order-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'heading' => '1',
				]
			]
		);

		$this->end_controls_section();

		// Style -> Dividers
		$this->start_controls_section(
			'dividers_styles',
			[
				'label' => __('Dividers', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'dividers' => '1',
				]
			]
		);

		$this->add_control(
			'dividers_color',
			[
				'label' => __('Dividers Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review table tbody td' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review table tbody th' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'dividers' => '1',
				]
			]
		);

		$this->end_controls_section();

		// Style -> Content
		$this->start_controls_section(
			'content_styles',
			[
				'label' => __('Content', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-payment-total th' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-payment-total td' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-review-order-table .cart_item' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-review-order-table .cart_item .product-title' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-review-order-table .cart_item .product-quantity' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-review-order-table .cart_item .variation' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-review-order-table .cart_item .product-total' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-item .woocommerce-help-tip' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-payment-total td p.wc-gzd-additional-info' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .wc-gzd-checkbox-placeholder label' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => __('Links Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-review-order-table a' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-payment-total a' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .wc-gzd-checkbox-placeholder a' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content a' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => __('Links Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-review-order-table a:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-payment-total a:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .wc-gzd-checkbox-placeholder a:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'total_text_color',
			[
				'label' => __('Subtotal & Total Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-payment-total th span' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-payment-total td' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-payment-total td span' => 'color: {{VALUE}} !important;',
				],
			]
		);
  
		$this->add_control(
			'amount_background',
			[
				'label' => __('Amount Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review table.woocommerce-checkout-review-order-table .product-quantity' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-item .woocommerce-help-tip' => 'background-color: {{VALUE}} !important;',
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
		
		// Style -> Form
		$this->start_controls_section(
			'form_styles',
			[
				'label' => __(!defined('WC_GERMANIZED_VERSION') ? 'Checkbox & Radio Button' : 'Form', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'label_text_color',
			[
				'label' => __('Label Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-shipping-methods label' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-gzd-legal-checkbox-text' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .wc-gzd-checkbox-placeholder label' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'input_marker_color',
			[
				'label' => __('Input Marker Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-shipping-methods .radio-sign:before' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-shipping-methods .checkbox-sign:before' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-shipping-methods span.required' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .wc-gzd-checkbox-placeholder .checkbox-sign:before' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content .radio-sign:before' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
		
		if (defined('WC_GERMANIZED_VERSION')) {
			$this->add_control(
				'input_text_color',
				[
					'label' => __('Input Text Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content input[type=text]' => 'color: {{VALUE}} !important;',
					],
				]
			);
			
			$this->add_control(
				'input_placeholder_color',
				[
					'label' => __('Input Placeholder Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content input[type=text]::placeholder' => 'color: {{VALUE}} !important;',
					],
				]
			);
        }
		
		$this->add_control(
			'input_background_color',
			[
				'label' => __('Input Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-shipping-methods .radio-sign' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-shipping-methods .checkbox-sign' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .wc-gzd-checkbox-placeholder .checkbox-sign' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content .radio-sign' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content input[type=text]' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'input_border_color',
			[
				'label' => __('Input Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-shipping-methods .radio-sign' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-shipping-methods .checkbox-sign' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .wc-gzd-checkbox-placeholder .checkbox-sign' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content .radio-sign' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content input[type=text]' => 'border-color: {{VALUE}} !important;',
				],
			]
		);
		
		if (defined('WC_GERMANIZED_VERSION')) {
			$this->add_control(
				'input_border_radius',
				[
					'label' => __('Input Border Radius', 'thegem'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} '.$this->get_customize_class().'#order_review .dhl-preferred-service-content input[type=text]' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
					],
				]
			);
			
			$this->add_control(
				'input_checkbox_border_radius',
				[
					'label' => __('Checkbox Border Radius', 'thegem'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} '.$this->get_customize_class().'#order_review .woocommerce-checkout-payment .checkbox-sign' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
						'{{WRAPPER}} '.$this->get_customize_class().'#order_review .wc-gzd-checkbox-placeholder .checkbox-sign' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
					],
				]
			);
        }
		
		$this->end_controls_section();
		
		// Style -> Buy Now Button
		if (defined('WC_GERMANIZED_VERSION')) {
			$this->start_controls_section(
				'payment_button_section_styles',
				[
					'label' => __('Buy Now Button', 'thegem'),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => [
						'payment_btn' => '1',
					]
				]
			);
			
			$this->add_responsive_control(
				'payment_btn_border_width',
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
						'payment_btn' => '1',
					],
					'selectors' => [
						'{{WRAPPER}} .'.$this->get_widget_wrapper().' .checkout-navigation-buttons .gem-button' => 'border-width: {{SIZE}}{{UNIT}} !important; line-height: calc(40{{UNIT}} - calc({{SIZE}}{{UNIT}}*2)) !important;',
					],
				]
			);
			
			$this->add_responsive_control(
				'payment_btn_border_radius',
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
						'payment_btn' => '1',
					],
					'selectors' => [
						'{{WRAPPER}} .'.$this->get_widget_wrapper().' .checkout-navigation-buttons .gem-button' => 'border-radius:{{SIZE}}{{UNIT}} !important;',
					],
				]
			);
			
			$this->add_control(
				'payment_btn_text_color',
				[
					'label' => __('Text Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'condition' => [
						'payment_btn' => '1',
					],
					'selectors' => [
						'{{WRAPPER}} .'.$this->get_widget_wrapper().' .checkout-navigation-buttons .gem-button' => 'color: {{VALUE}} !important;',
					],
				]
			);
			
			$this->add_control(
				'payment_btn_text_color_hover',
				[
					'label' => __('Text Color on Hover', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'condition' => [
						'payment_btn' => '1',
					],
					'selectors' => [
						'{{WRAPPER}} .'.$this->get_widget_wrapper().' .checkout-navigation-buttons .gem-button:hover' => 'color: {{VALUE}} !important;',
					],
				]
			);
			
			$this->add_control(
				'payment_btn_background_color',
				[
					'label' => __('Background color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'condition' => [
						'payment_btn' => '1',
					],
					'selectors' => [
						'{{WRAPPER}} .'.$this->get_widget_wrapper().' .checkout-navigation-buttons .gem-button' => 'background-color: {{VALUE}} !important;',
					],
				]
			);
			
			$this->add_control(
				'payment_btn_background_color_hover',
				[
					'label' => __('Background Color on Hover', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'condition' => [
						'payment_btn' => '1',
					],
					'selectors' => [
						'{{WRAPPER}} .'.$this->get_widget_wrapper().' .checkout-navigation-buttons .gem-button:hover' => 'background-color: {{VALUE}} !important;',
					],
				]
			);
			
			$this->add_control(
				'payment_btn_border_color',
				[
					'label' => __('Border Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'condition' => [
						'payment_btn' => '1',
					],
					'selectors' => [
						'{{WRAPPER}} .'.$this->get_widget_wrapper().' .checkout-navigation-buttons .gem-button' => 'border-color: {{VALUE}} !important;',
					],
				]
			);
			
			$this->add_control(
				'payment_btn_border_color_hover',
				[
					'label' => __('Border Color on Hover', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'condition' => [
						'payment_btn' => '1',
					],
					'selectors' => [
						'{{WRAPPER}} .'.$this->get_widget_wrapper().' .checkout-navigation-buttons .gem-button:hover' => 'border-color: {{VALUE}} !important;',
					],
				]
			);
			
			$this->end_controls_section();
        };
		
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
			'heading' => 1,
			'heading_text_style' => '',
			'heading_font_weight' => 'light',
			'dividers' => 1,
			'element_class' => $this->get_widget_wrapper()
		), $settings);
  
		$title_classes = implode(' ', array('checkout-order-title', $params['heading_text_style'], $params['heading_font_weight']));
		$title_tag = empty($params['heading_text_style']) ? 'h3' : 'div';
		
		$params['element_class'] .= empty($params['dividers']) ? ' hide-dividers' : null;
		$params['element_class'] .= empty($params['divider_top']) ? ' hide-divider-top' : null;
		$params['element_class'] .= empty($params['divider_bottom']) ? ' hide-divider-bottom' : null;
        
        if (defined('WC_GERMANIZED_VERSION')) {
	        $params['element_class'] .= empty($params['payment_btn']) ? ' place-order-btn--hide' : null;
	        $params['element_class'] .= ' place-order-btn--'.$params['payment_btn_alignment'];
        }

		ob_start();
		wc_load_cart();

		if (empty(WC()->cart)) {
			ob_end_clean();
			return thegem_templates_close_single_post($this->get_name(), $this->shortcode_settings(), '');
		}

		$checkout = WC()->checkout();
    ?>

    <div id="order_review" class="<?= $params['element_class']; ?>">
        <?php if (!empty($params['heading'])) : ?>
            <<?= $title_tag; ?> id="order_review_heading" class="<?= $title_classes; ?>"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></<?= $title_tag; ?>>
        <?php endif; ?>
		
		<?php if ( !is_ajax() && defined('WC_GERMANIZED_VERSION') ) { do_action( 'woocommerce_review_order_after_payment' ); } ?>
        
        <table class="shop_table woocommerce-checkout-review-order-table">
            <thead>
                <tr>
                    <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                    <th class="product-total"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                do_action( 'woocommerce_review_order_before_cart_contents' );
            
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            
                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                        ?>
                        <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                            <td class="product-name">
                                <div class="product-wrap">
                                <div class="product-image">
                                <?php
                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                                if ( ! $product_permalink ) {
                                    echo $thumbnail; // PHPCS: XSS ok.
                                } else {
                                    printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                }
                                ?>
                                </div>
                                <div class="product-info">
                                <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;'; ?>
                                <?php echo (!empty(wc_get_formatted_cart_item_data( $cart_item ))) ? '<br>'.wc_get_formatted_cart_item_data( $cart_item ) : ''; ?>
                                <br>
                                <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php echo '<br>'.wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                                </div>
                            </td>
                            <td class="product-total">
                                <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
            
                do_action( 'woocommerce_review_order_after_cart_contents' );
                ?>
            </tbody>
            <tfoot>
                <tr><td colspan="1000"><table class="shop_table woocommerce-checkout-payment-total">
                <tr class="cart-subtotal">
                    <th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                    <td><?php wc_cart_totals_subtotal_html(); ?></td>
                </tr>
            
                <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                    <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                        <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                        <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                    </tr>
                <?php endforeach; ?>
            
                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            
                    <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
            
                    <?php wc_cart_totals_shipping_html(); ?>
            
                    <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
            
                <?php endif; ?>
            
                <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                    <tr class="fee">
                        <th><?php echo esc_html( $fee->name ); ?></th>
                        <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
                    </tr>
                <?php endforeach; ?>
            
                <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
                    <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
                        <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
                            <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                                <th><?php echo esc_html( $tax->label ); ?></th>
                                <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr class="tax-total">
                            <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
                            <td><?php wc_cart_totals_taxes_total_html(); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
            
                <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
            
                <tr class="order-total">
                    <th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
                    <td><?php wc_cart_totals_order_total_html(); ?></td>
                </tr>
            
                <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
            </table></td></tr>
            </tfoot>
        </table>
		
		<?php if (defined('WC_GERMANIZED_VERSION')) { woocommerce_gzd_template_order_submit(); } ?>
    </div>

    <?php
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		echo $return_html;
	}

}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCheckoutOrder());
}