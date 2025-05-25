<?php

namespace TheGem_Elementor\Widgets\TemplateCheckoutThanksOrderOverview;

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
class TheGem_TemplateCheckoutThanksOrderOverview extends Widget_Base {
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
		return 'thegem-template-checkout-thanks-order-overview';
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
		return __('Order Overview', 'thegem');
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
		return 'thegem-te-checkout-thanks-order-overview';
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
			'list_alignment',
			[
				'label' => __('Alignment', 'thegem'),
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
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .cart_totals-inner .cart_totals_title span' => 'text-align: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Style -> Appearance
		$this->start_controls_section(
			'general_section_styles',
			[
				'label' => __('General', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'list_color',
			[
				'label' => __('List Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-order-overview li' => 'color: {{VALUE}};',
				],
			]
		);
  
		$this->add_control(
			'list_color_active',
			[
				'label' => __('List Color Active', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-order-overview li strong' => 'color: {{VALUE}};',
				],
			]
		);
  
		$this->add_control(
			'bullets_color',
			[
				'label' => __('Bullets Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-order-overview li:before' => 'color: {{VALUE}};',
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
		
		if (!is_checkout()) {
			ob_end_clean();
			echo thegem_templates_close_checkout_thanks(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}
		
		global $wp;
		$order_id = $wp->query_vars['order-received'];
		$order = wc_get_order( $order_id );
  
		$params['list_alignment'] = !empty($params['list_alignment']) ? 'list-alignment--'.$params['list_alignment'] : null;
		$params['element_class'] = implode(' ', array($params['element_class'], $params['list_alignment']));
		
		?>

        <div class="<?= $params['element_class'] ?>">
            <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details body-small">
                <li class="woocommerce-order-overview__order order">
			        <?php esc_html_e('Order number:', 'woocommerce'); ?>
                    <strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                </li>

                <li class="woocommerce-order-overview__date date">
			        <?php esc_html_e('Date:', 'woocommerce'); ?>
                    <strong><?php echo wc_format_datetime($order->get_date_created()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                </li>
		
		        <?php if (is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email()) : ?>
                    <li class="woocommerce-order-overview__email email">
				        <?php esc_html_e('Email:', 'woocommerce'); ?>
                        <strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                    </li>
		        <?php endif; ?>

                <li class="woocommerce-order-overview__total total">
			        <?php esc_html_e('Total:', 'woocommerce'); ?>
                    <strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                </li>
		
		        <?php if ($order->get_payment_method_title()) : ?>
                    <li class="woocommerce-order-overview__payment-method method">
				        <?php esc_html_e('Payment method:', 'woocommerce'); ?>
                        <strong><?php echo wp_kses_post($order->get_payment_method_title()); ?></strong>
                    </li>
		        <?php endif; ?>
            </ul>
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_checkout_thanks(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCheckoutThanksOrderOverview());
}