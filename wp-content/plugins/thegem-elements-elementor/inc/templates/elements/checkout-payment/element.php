<?php

namespace TheGem_Elementor\Widgets\TemplateCheckoutPayment;

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
class TheGem_TemplateCheckoutPayment extends Widget_Base {
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
		return 'thegem-template-checkout-payment';
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
		return __('Payment Methods', 'thegem');
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
		return 'thegem-te-checkout-payment';
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

		// Content Section
		$this->start_controls_section(
			'content_section',
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
			]
		);

		$this->add_control(
			'payment_box_paddings',
			[
				'label' => __('Payment Box Paddings', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->end_controls_section();

		// Button Section
		$this->start_controls_section(
			'payment_button_section',
			[
				'label' => __('Payment Button', 'thegem'),
			]
		);

		$this->add_control(
			'payment_btn',
			[
				'label' => __('Payment Button', 'thegem'),
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
				'label' => __('Payment Button Alignment', 'thegem'),
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

		// Style -> Content
		$this->start_controls_section(
			'content_styles',
			[
				'label' => __('Content', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dividers_color',
			[
				'label' => __('Dividers Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' #payment.woocommerce-checkout-payment .payment_methods li' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' #payment.woocommerce-checkout-payment .payment_methods li' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'dividers' => '1',
				]
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .place-order' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .woocommerce-terms-and-conditions-checkbox-text' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment a' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => __('Links Color Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .place-order a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'payment_box_text_color',
			[
				'label' => __('Payment Box Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .payment_methods li .payment_box' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'payment_box_background',
			[
				'label' => __('Payment Box Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .payment_methods li .payment_box' => 'background-color: {{VALUE}} !important;',
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
				'label' => __('Checkbox & Radio Button', 'thegem'),
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .payment_methods li label' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .radio-sign:before' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .checkbox-sign:before' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment span.required' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .radio-sign' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .checkbox-sign' => 'background-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .radio-sign' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-checkout-payment .checkbox-sign' => 'border-color: {{VALUE}} !important;',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-checkout-payment .checkbox-sign' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		// Style -> Payment Button
		$this->start_controls_section(
			'payment_button_section_styles',
			[
				'label' => __('Payment Button', 'thegem'),
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
			'payment_btn' => 1,
			'payment_btn_alignment' => 'fullwidth',
			'dividers' => 1,
            'element_class' => $this->get_widget_wrapper()
		), $settings);

		ob_start();
		wc_load_cart();

		if (empty(WC()->cart)) {
			ob_end_clean();
			return thegem_templates_close_single_post($this->get_name(), $this->shortcode_settings(), '');
		}

		$checkout = WC()->checkout();
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) );

		$params['element_class'] .= empty($params['payment_btn']) ? ' place-order-btn--hide' : null;
		$params['element_class'] .= empty($params['payment_box_paddings']) ? ' payment-box-paddings--hide' : null;
		$params['element_class'] .= ' place-order-btn--'.$params['payment_btn_alignment'];

		do_action( 'woocommerce_review_order_before_payment' );
    ?>

    <div class="<?= $params['element_class'] ?>">
        <div id="payment" class="woocommerce-checkout-payment<?= (empty($params['dividers']) ? ' no-dividers' : ''); ?>">
            <?php if ( WC()->cart->needs_payment() ) : ?>
                <ul class="wc_payment_methods payment_methods methods">
                    <?php
                    if ( ! empty( $available_gateways ) ) {
                        foreach ( $available_gateways as $gateway ) {
                            wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
                        }
                    } else {
                        echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
                    }
                    ?>
                </ul>
            <?php endif; ?>
            <div class="form-row place-order">
                <noscript>
                    <?php
                    /* translators: $1 and $2 opening and closing emphasis tags respectively */
                    printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
                    ?>
                    <br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
                </noscript>

                <?php wc_get_template( 'checkout/terms.php' ); ?>

                <?php do_action( 'woocommerce_review_order_before_submit' ); ?>

                <?php if (!empty($params['payment_btn'])): ?>
                <div class="checkout-navigation-buttons">
                    <?php
                        thegem_button(array(
                            'tag' => 'button',
                            'text' => esc_attr( $order_button_text ),
                            'style' => 'flat',
                            'size' => 'small',
                            'position' => $params['payment_btn_alignment'],
                            'extra_class' => 'checkout-place-order',
                            'attributes' => array(
                                'id' => 'place_order',
                                'name' => 'woocommerce_checkout_place_order',
                                'value' => esc_attr( $order_button_text ),
                                'type' => 'submit',
                                'data-value' => esc_attr( $order_button_text ),
                                'class' => 'gem-button-tablet-size-small'
                            )
                        ), true);
                    ?>
                </div>
                <?php endif; ?>

                <?php do_action( 'woocommerce_review_order_after_submit' ); ?>

                <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
            </div>
        </div>

		<?php if ( is_ajax() || !defined('WC_GERMANIZED_VERSION') ) { do_action( 'woocommerce_review_order_after_payment' ); } ?>

    </div>

    <?php
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		echo $return_html;
	}

}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCheckoutPayment());
}