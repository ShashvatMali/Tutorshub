<?php

namespace TheGem_Elementor\Widgets\TemplateCheckoutThanksNotices;

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
class TheGem_TemplateCheckoutThanksNotices extends Widget_Base {
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
		return 'thegem-template-checkout-thanks-notices';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'checkout-thanks';
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
		return 'thegem-te-checkout-thanks-notices';
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
		
		// Style -> General
		$this->start_controls_section(
			'success_section_styles',
			[
				'label' => __('Success Notices', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'success_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--success' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
  
		$this->add_control(
			'success_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--success:before' => 'color: {{VALUE}} !important;',
				],
			]
		);
  
		$this->add_control(
			'success_text_color',
			[
				'label' => __('Notice Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--success' => 'color: {{VALUE}} !important;',
				],
			]
		);
  
		$this->add_control(
			'success_link_color',
			[
				'label' => __('Link Normal Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--success a' => 'color: {{VALUE}} !important;',
				],
			]
		);
  
		$this->add_control(
			'success_link_color_hover',
			[
				'label' => __('Link Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--success a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Style -> General
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--error' => 'background-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--error:before' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--error' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--error a' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-notice--error a:hover' => 'color: {{VALUE}} !important;',
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
            'list_alignment' => 'left',
            'element_class' => $this->get_widget_wrapper()
        ), $settings);
		
		// Init Title
		ob_start();
		
		global $wp;
		$order_id = $wp->query_vars['order-received'];
		$order = wc_get_order( $order_id );
		
		if (!is_checkout()) {
			ob_end_clean();
			echo thegem_templates_close_checkout_thanks(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}
		
		?>

        <div class="<?= $params['element_class'] ?>">
	        <?php if ($order->has_status('failed')): ?>
                <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
			        <?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?>
                </p>

                <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                    <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
			        <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
			        <?php endif; ?>
                </p>
	        <?php else: ?>
                <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
			        <?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Thank you. Your order has been received.', 'woocommerce'), null); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </p>
	        <?php endif; ?>
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_checkout_thanks(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCheckoutThanksNotices());
}