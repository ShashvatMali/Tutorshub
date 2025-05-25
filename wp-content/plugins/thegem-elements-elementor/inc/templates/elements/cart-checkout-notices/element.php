<?php

namespace TheGem_Elementor\Widgets\TemplateCartCheckoutNotices;

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
class TheGem_TemplateCartCheckoutNotices extends Widget_Base {
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
		return 'thegem-template-cart-checkout-notices';
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
		return get_post_type($post_id) === 'thegem_templates' && (thegem_get_template_type($post_id) === 'cart' || thegem_get_template_type($post_id) === 'single-product');
	}
	
	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('WooCommerce Notices', 'thegem');
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-product') {
			return ['thegem_single_product_builder'];
		}
		/*if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'checkout') {
			return ['thegem_checkout_builder'];
		}*/
	}
	
	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}
	
	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-cart-checkout-notices';
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
		
		// Style -> Update Notices
		$this->start_controls_section(
			'update_section_styles',
			[
				'label' => __('Update Notices', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'update_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-message' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-info' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'update_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-message:before' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-info:before' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'update_text_color',
			[
				'label' => __('Notice Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-message' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-info' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'update_link_color',
			[
				'label' => __('Link Normal Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-message a' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-info a' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'update_link_color_hover',
			[
				'label' => __('Link Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-message a:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-info a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Style -> Error Notices
		$this->start_controls_section(
			'error_section_styles',
			[
				'label' => __('Error Notices', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'error_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' ul.woocommerce-error' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .is-error' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'error_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' ul.woocommerce-error:before' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .is-error svg' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'error_text_color',
			[
				'label' => __('Notice Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' ul.woocommerce-error li' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .is-error' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'error_link_color',
			[
				'label' => __('Link Normal Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' ul.woocommerce-error li a' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .is-error a' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'error_link_color_hover',
			[
				'label' => __('Link Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' ul.woocommerce-error li a:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .is-error a:hover' => 'color: {{VALUE}} !important;',
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
		$params = array_merge(array(), $settings);
		
		// Init Title
		ob_start();
		
		if (!is_cart() && !is_checkout() && !is_product()) {
			ob_end_clean();
			echo thegem_templates_close_cart(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}
		
		?>

        <div class="<?= $this->get_widget_wrapper() ?>">
            <div class="cart-notices">
	            <?= woocommerce_output_all_notices(); ?>
            </div>
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_cart(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCartCheckoutNotices());