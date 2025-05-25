<?php

namespace TheGem_Elementor\Widgets\TemplateCartCheckoutSteps;

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
class TheGem_TemplateCartCheckoutSteps extends Widget_Base {
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
		return 'thegem-template-cart-checkout-steps';
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
		return get_post_type($post_id) === 'thegem_templates' && (thegem_get_template_type($post_id) === 'cart' || thegem_get_template_type($post_id) === 'checkout' || thegem_get_template_type($post_id) === 'checkout-thanks');
	}
	
	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Checkout Steps', 'thegem');
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'cart') {
			return ['thegem_cart_builder'];
		}
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'checkout') {
			return ['thegem_checkout_builder'];
		}
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'checkout-thanks') {
			return ['thegem_checkout_thanks_builder'];
		}
	}
	
	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}
	
	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-cart-checkout-steps';
	}
	
	/** Get customize class */
	public function get_customize_class() {
		return ' .'.$this->get_widget_wrapper();
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
			'style',
			[
				'label' => __('Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Bold', 'thegem'),
					'elegant' => __('Elegant', 'thegem'),
				],
				'default' => 'default',
			]
		);
		
		$this->add_control(
			'custom_selection',
			[
				'label' => __('Custom Selection', 'thegem'),
				'return_value' => '1',
				'default' => '0',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'steps_text_style',
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
				'default' => '',
				'condition' => [
					'custom_selection' => '1',
				],
			]
		);
		
		$this->add_control(
			'steps_letter_spacing',
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
					'custom_selection' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-cart-checkout-steps-content .step' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-cart-checkout-steps-title .step' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'steps_text_transform',
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
					'custom_selection' => '1',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-cart-checkout-steps-content .step' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-cart-checkout-steps-title .step' => '{{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'step_1_text',
			[
				'label' => __('Step 1 Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('', 'thegem'),
			]
		);
  
		$this->add_control(
			'step_2_text',
			[
				'label' => __('Step 2 Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('', 'thegem'),
			]
		);
  
		$this->add_control(
			'step_3_text',
			[
				'label' => __('Step 3 Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('', 'thegem'),
			]
		);
		
		$this->end_controls_section();
		
		// Style -> Appearance
		$this->start_controls_section(
			'appearance_section_styles',
			[
				'label' => __('Appearance', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'steps_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-cart-checkout-steps-content .step' => 'color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-cart-checkout-steps-title .step' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'steps_color_active',
			[
				'label' => __('Text Color Active', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-cart-checkout-steps-content .step.active' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-cart-checkout-steps-title .step.active' => 'color: {{VALUE}};',
				],
			]
		);
  
		$this->add_control(
			'divider_color',
			[
				'label' => __('Divider Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'style' => 'elegant',
				],
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-cart-checkout-steps-content .step:not(.active)' => 'border-color: {{VALUE}};',
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
			'custom_selection' => '',
			'steps_text_style' => '',
        ), $settings);
		
		// Init Title
		ob_start();
		
		$steps_classes = !empty($params['custom_selection']) && !empty($params['steps_text_style']) ? $params['steps_text_style'] : null;
		
		?>

        <div class="<?= $this->get_widget_wrapper() ?>">
	
	        <?php if ($params['style'] == 'default'): ?>
                <div class="woocommerce-cart-checkout-steps woocommerce-cart-checkout-steps-title cart-checkout-steps--builder">
                    <div class="step step-cart <?= (!empty($steps_classes) ? $steps_classes : 'title-h2') ?> <?= (is_cart() || thegem_get_template_type(get_the_id()) === 'cart' ? 'active' : 'light'); ?>">
				        <?php !empty($params['step_1_text']) ? esc_html_e($params['step_1_text']) : esc_html_e('Shopping cart', 'thegem'); ?>
                    </div>
                    <div class="step step-checkout <?= (!empty($steps_classes) ? $steps_classes : 'title-h2') ?> <?php echo ((is_checkout() && !is_wc_endpoint_url('order-received') || thegem_get_template_type(get_the_id()) === 'checkout') ? 'active' : 'light'); ?>">
				        <?php !empty($params['step_2_text']) ? esc_html_e($params['step_2_text']) : esc_html_e('Checkout', 'thegem'); ?>
                    </div>
                    <div class="step step-order-complete <?= (!empty($steps_classes) ? $steps_classes : 'title-h2') ?> <?php echo ((is_checkout() && is_wc_endpoint_url('order-received') || thegem_get_template_type(get_the_ID()) === 'checkout-thanks') ? 'active' : 'light'); ?>">
				        <?php !empty($params['step_3_text']) ? esc_html_e($params['step_3_text']) : esc_html_e('Order complete', 'thegem'); ?>
                    </div>
                </div>
	        <?php endif; ?>
	
	        <?php if ($params['style'] == 'elegant'): ?>
                <div class="woocommerce-cart-checkout-steps woocommerce-cart-checkout-steps-content cart-checkout-steps--builder">
                    <div class="step step-cart <?= (!empty($steps_classes) ? $steps_classes : 'title-h6') ?> <?php echo (is_cart() || thegem_get_template_type(get_the_id()) === 'cart' ? 'active' : 'light'); ?>">
				        <?php !empty($params['step_1_text']) ? esc_html_e($params['step_1_text']) : esc_html_e('1. Shopping cart', 'thegem'); ?>
                    </div>
                    <div class="step step-checkout <?= (!empty($steps_classes) ? $steps_classes : 'title-h6') ?> <?php echo ((is_checkout() && !is_wc_endpoint_url('order-received') || thegem_get_template_type(get_the_id()) === 'checkout') ? 'active' : 'light'); ?>">
				        <?php !empty($params['step_2_text']) ? esc_html_e($params['step_2_text']) : esc_html_e('2. Checkout', 'thegem'); ?>
                    </div>
                    <div class="step step-order-complete <?= (!empty($steps_classes) ? $steps_classes : 'title-h6') ?> <?php echo ((is_checkout() && is_wc_endpoint_url('order-received') || thegem_get_template_type(get_the_ID()) === 'checkout-thanks') ? 'active' : 'light'); ?>">
				        <?php !empty($params['step_3_text']) ? esc_html_e($params['step_3_text']) : esc_html_e('3. Order complete', 'thegem'); ?>
                    </div>
                </div>
	        <?php endif; ?>
         
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_cart(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCartCheckoutSteps());
}