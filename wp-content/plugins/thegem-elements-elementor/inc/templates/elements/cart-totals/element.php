<?php

namespace TheGem_Elementor\Widgets\TemplateCartTotals;

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
class TheGem_TemplateCartTotals extends Widget_Base {
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
		return 'thegem-template-cart-totals';
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
		return __('Cart Totals', 'thegem');
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
		return 'thegem-te-cart-totals';
	}
	
	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}
		
		return ' .'.$this->get_widget_wrapper().' .cart_totals';
	}
	
	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		
		// Heading Section
		$this->start_controls_section(
			'section_heading',
			[
				'label' => __('Heading', 'thegem'),
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
					'{{WRAPPER}}'.$this->get_customize_class().' .cart_totals-inner .cart_totals_title span' => 'text-align: {{VALUE}};',
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
					'title-main-menu' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'title-h3',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .cart_totals-inner .cart_totals_title span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .cart_totals-inner .cart_totals_title span' => '{{VALUE}};',
				],
			]
		);
  
		$this->end_controls_section();
		
		// Divider Section
		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Content', 'thegem'),
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
				'selectors_dictionary' => [
					'' => 'border: 0;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .cart_totals-inner table.shop_table th' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .cart_totals-inner table.shop_table td' => '{{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Checkout Button Section
		$this->start_controls_section(
			'section_checkout_button',
			[
				'label' => __('Checkout Button', 'thegem'),
			]
		);
		
		$this->add_control(
			'checkout_btn',
			[
				'label' => __('Checkout Button', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'checkout_btn_alignment',
			[
				'label' => __('Checkout Button Alignment', 'thegem'),
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
					'checkout_btn' => '1',
				],
			]
		);

		$this->end_controls_section();
		
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
		
		$this->add_responsive_control(
			'heading_spacing',
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
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .cart_totals-inner .cart_totals_title span' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'heading_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner .cart_totals_title span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'heading' => '1',
				]
			]
		);
		
		$this->end_controls_section();
		
		// Style -> Content
		$this->start_controls_section(
			'content_section_styles',
			[
				'label' => __('Content', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'panel_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'panel_border_radius',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .cart_totals-inner' => 'border-radius:{{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'panel_padding',
			[
				'label' => esc_html__('Paddings', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .cart_totals-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
  
		$this->add_control(
			'text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner table.shop_table th' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner table.shop_table td' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner table.shop_table td a' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator button' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner table.shop_table td a:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator button:hover' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner table.shop_table td .amount' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner table.shop_table .cart-discount td' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'dividers_color',
			[
				'label' => __('Dividers Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner table.shop_table th' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .cart_totals-inner table.shop_table td' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'dividers' => '1',
				]
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
				'label' => __('Form', 'thegem'),
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row label' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-totals label' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'input_text_color',
			[
				'label' => __('Input Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row input.input-text' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .select2-selection__rendered' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .select2-selection__arrow' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .checkbox-sign:before' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-totals .radio-sign.checked:before' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'input_background_color',
			[
				'label' => __('Input Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row input.input-text' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .select2-selection__rendered' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .checkbox-sign' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-totals .radio-sign' => 'background-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row input.input-text' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .select2-selection--single' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .checkbox-sign' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-totals .radio-sign' => 'border-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .select2-selection__placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row input.input-text::placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row textarea.input-text::placeholder' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
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
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row input.input-text' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .select2-selection--single' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-shipping-calculator .form-row .select2-selection__rendered' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		
		$this->end_controls_section();
        
        // Style -> Checkout Button
		$this->start_controls_section(
			'checkout_button_section_styles',
			[
				'label' => __('Checkout Button', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'checkout_btn' => '1',
				]
			]
		);
		
		$this->add_responsive_control(
			'checkout_btn_border_width',
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
					'checkout_btn' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .wc-proceed-to-checkout .gem-button' => 'border-width: {{SIZE}}{{UNIT}} !important; line-height: calc(40{{UNIT}} - calc({{SIZE}}{{UNIT}}*2)) !important;',
				],
			]
		);
		
		$this->add_responsive_control(
			'checkout_btn_border_radius',
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
					'checkout_btn' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .wc-proceed-to-checkout .gem-button' => 'border-radius:{{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		
		$this->add_control(
			'checkout_btn_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'checkout_btn' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .wc-proceed-to-checkout .gem-button' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'checkout_btn_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'checkout_btn' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .wc-proceed-to-checkout .gem-button:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'checkout_btn_background_color',
			[
				'label' => __('Background color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'checkout_btn' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .wc-proceed-to-checkout .gem-button' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'checkout_btn_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'checkout_btn' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .wc-proceed-to-checkout .gem-button:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'checkout_btn_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'checkout_btn' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .wc-proceed-to-checkout .gem-button' => 'border-color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'checkout_btn_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'checkout_btn' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .'.$this->get_widget_wrapper().' .wc-proceed-to-checkout .gem-button:hover' => 'border-color: {{VALUE}} !important;',
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
			'element_class' => $this->get_widget_wrapper()
        ), $settings);
		
		// Init Title
		ob_start();
		$uniqid = uniqid('thegem-custom-').rand(1,9999);
		wc_load_cart();

		if (empty(WC()->cart)) {
			ob_end_clean();
			return thegem_templates_close_single_post($this->get_name(), $this->shortcode_settings(), '');
		}

		$cart_totals_title_class = implode(' ', array($params['heading_text_style'], $params['heading_font_weight']));
		$cart_totals_btn_class = 'checkout-btn--'.$params['checkout_btn_alignment'];
		$params['element_class'] .= empty($params['heading']) ? ' cart-totals-title--hide' : null;
		$params['element_class'] .= empty($params['checkout_btn']) ? ' checkout-btn--hide' : null;
  
		add_action( 'woocommerce_before_cart_totals', 'woocommerce_cart_totals_wrap_start', 1 );
		add_action( 'woocommerce_after_cart_totals', 'woocommerce_cart_totals_wrap_end', 100 );
		WC()->cart->calculate_totals();
  
		?>
		
		<div class="<?= $params['element_class'] ?> <?= esc_attr($uniqid); ?>" data-btn-classes="<?=$cart_totals_btn_class?>">
            <div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">
				
				<?php do_action( 'woocommerce_before_cart_totals' ); ?>

                <?php if (!empty($params['heading'])): ?>
                    <div class="cart_totals_title">
                        <span class="<?= $cart_totals_title_class ?>">
                            <?php esc_html_e( 'Cart totals', 'woocommerce' ); ?>
                        </span>
                    </div>
                <?php endif; ?>

                <table cellspacing="0" class="shop_table shop_table_responsive <?= empty($params['heading']) ? 'shop_table--hide-separator' : null ?>">

                    <tr class="cart-subtotal">
                        <th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                        <td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
                    </tr>
					
					<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                        <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                            <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                            <td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                        </tr>
					<?php endforeach; ?>
					
					<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
						
						<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
						
						<?php wc_cart_totals_shipping_html(); ?>
						
						<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
					
					<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>

                        <tr class="shipping">
                            <th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
                            <td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><?php woocommerce_shipping_calculator(); ?></td>
                        </tr>
					
					<?php endif; ?>
					
					<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                        <tr class="fee">
                            <th><?php echo esc_html( $fee->name ); ?></th>
                            <td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
                        </tr>
					<?php endforeach; ?>
					
					<?php
					if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
						$taxable_address = WC()->customer->get_taxable_address();
						$estimated_text  = '';
						
						if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
							/* translators: %s location. */
							$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
						}
						
						if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
							foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
								?>
                                <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                                    <th><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
                                    <td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                                </tr>
								<?php
							}
						} else {
							?>
                            <tr class="tax-total">
                                <th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
                                <td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
                            </tr>
							<?php
						}
					}
					?>
					
					<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

                    <tr class="order-total">
                        <th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
                        <td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
                    </tr>
					
					<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

                </table>
		
		        <?php if (!empty($params['checkout_btn'])): ?>
                    <div class="wc-proceed-to-checkout checkout-btn--<?=$params['checkout_btn_alignment']?>">
                        <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
                    </div>
		        <?php endif; ?>
				
				<?php do_action( 'woocommerce_after_cart_totals' ); ?>

            </div>

            <script type="text/javascript">
                (function ($) {
                    $(document.body).on('updated_cart_totals', function () {
                        const $wrapper = $('.thegem-te-cart-totals.<?= esc_attr($uniqid) ?>');
                        const btnClass = $wrapper.data('btn-classes');

                        $('.cart_totals_title', $wrapper).html(`<span class="<?= $cart_totals_title_class ?>"><?php esc_html_e( 'Cart totals', 'woocommerce' ); ?></span>`);
                        $('.wc-proceed-to-checkout', $wrapper).addClass(btnClass);
                    });
                })(jQuery);
            </script>
		</div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_cart(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCartTotals());
}