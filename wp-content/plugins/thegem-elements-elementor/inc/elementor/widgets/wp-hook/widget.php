<?php

namespace TheGem_Elementor\Widgets\WP_Hook;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for WP_Hook.
 */
#[\AllowDynamicProperties]
class TheGem_WP_Hook extends Widget_Base {

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
		return 'thegem-wp-hook';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('WordPress Hook', 'thegem');
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-product') {
			return ['thegem_single_product_builder'];
		}
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'product-archive') {
			return ['thegem_product_archive_builder'];
		}
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'checkout') {
			$categories[] = 'thegem_checkout_builder';
		}
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'cart') {
			$categories[] = 'thegem_cart_builder';
		}
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'cart') {
			$categories[] = 'thegem_cart_builder';
		}
		return ['thegem_elements'];
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'general',
			array(
				'label' => esc_html__( 'General', 'thegem' ),
				'tab'=> Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hook_type',
			array(
				'label' => esc_html__( 'Hook Type', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => array(
					'manual' => esc_html__( 'Manual Input', 'thegem' ),
					'woocommerce' => esc_html__( 'WooCommerce Hooks', 'thegem' ),
				),
				'default' => 'manual',
			)
		);

		$this->add_control(
			'manual_hook',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Hook Name', 'manual'),
				'condition' => [
					'hook_type' => 'manual',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'wc_hook_type',
			array(
				'label' => esc_html__( 'WooCommerce Hook Type', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => array(
					'product' => esc_html__( 'Product Page', 'thegem' ),
					'cart' => esc_html__( 'Cart Page', 'thegem' ),
					'checkout' => esc_html__( 'Checkout Page', 'thegem' ),
				),
				'default' => 'product',
				'condition' => [
					'hook_type' => 'woocommerce',
				],
			)
		);

		$this->add_control(
			'product_hook',
			array(
				'label' => esc_html__( 'Hook', 'thegem' ),
				'description' => esc_html__( 'Select which PHP hook do you want to display here.', 'thegem' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => array(
					'default' => esc_html__( 'Default for plugins compatibility', 'thegem' ),
					'woocommerce_before_single_product' => 'woocommerce_before_single_product',
					'woocommerce_before_single_product_summary' => 'woocommerce_before_single_product_summary',
					'woocommerce_product_thumbnails' => 'woocommerce_product_thumbnails',
					'woocommerce_single_product_summary' => 'woocommerce_single_product_summary',
					'woocommerce_before_add_to_cart_form' => 'woocommerce_before_add_to_cart_form',
					'woocommerce_before_variations_form' => 'woocommerce_before_variations_form',
					'woocommerce_before_add_to_cart_button' => 'woocommerce_before_add_to_cart_button',
					'woocommerce_before_single_variation' => 'woocommerce_before_single_variation',
					'woocommerce_single_variation' => 'woocommerce_single_variation',
					'woocommerce_after_single_variation' => 'woocommerce_after_single_variation',
					'woocommerce_after_add_to_cart_button' => 'woocommerce_after_add_to_cart_button',
					'woocommerce_after_variations_form' => 'woocommerce_after_variations_form',
					'woocommerce_after_add_to_cart_form' => 'woocommerce_after_add_to_cart_form',
					'woocommerce_product_meta_start' => 'woocommerce_product_meta_start',
					'woocommerce_product_meta_end' => 'woocommerce_product_meta_end',
					'woocommerce_share' => 'woocommerce_share',
					'woocommerce_after_single_product_summary' => 'woocommerce_after_single_product_summary',
					'woocommerce_after_single_product' => 'woocommerce_after_single_product',
				),
				'default' => 'default',
				'condition' => [
					'hook_type' => 'woocommerce',
					'wc_hook_type' => 'product',
				],
			)
		);

		$this->add_control(
			'cart_hook',
			array(
				'label' => esc_html__( 'Hook', 'thegem' ),
				'description' => esc_html__( 'Select which PHP hook do you want to display here.', 'thegem' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => array(
					'0' => esc_html__( 'Select', 'thegem' ),
					'woocommerce_before_cart' => 'woocommerce_before_cart',
					'woocommerce_after_cart_table' => 'woocommerce_after_cart_table',
					'woocommerce_cart_collaterals' => 'woocommerce_cart_collaterals',
					'woocommerce_after_cart' => 'woocommerce_after_cart',
				),
				'default' => '0',
				'condition' => [
					'hook_type' => 'woocommerce',
					'wc_hook_type' => 'cart',
				],
			)
		);

		$this->add_control(
			'checkout_hook',
			array(
				'label' => esc_html__( 'Hook', 'thegem' ),
				'description' => esc_html__( 'Select which PHP hook do you want to display here.', 'thegem' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => array(
					'0' => esc_html__( 'Select', 'thegem' ),
					'woocommerce_before_checkout_form' => 'woocommerce_before_checkout_form',
					'woocommerce_checkout_before_customer_details' => 'woocommerce_checkout_before_customer_details',
					'woocommerce_checkout_after_customer_details' => 'woocommerce_checkout_after_customer_details',
					'woocommerce_checkout_billing' => 'woocommerce_checkout_billing',
					'woocommerce_checkout_shipping' => 'woocommerce_checkout_shipping',
					'woocommerce_checkout_before_order_review_heading' => 'woocommerce_checkout_before_order_review_heading',
					'woocommerce_checkout_before_order_review' => 'woocommerce_checkout_before_order_review',
					'woocommerce_checkout_order_review' => 'woocommerce_checkout_order_review',
					'woocommerce_checkout_after_order_review' => 'woocommerce_checkout_after_order_review',
					'woocommerce_after_checkout_form' => 'woocommerce_after_checkout_form',
				),
				'default' => '0',
				'condition' => [
					'hook_type' => 'woocommerce',
					'wc_hook_type' => 'checkout',
				],
			)
		);

		$this->add_control(
			'clean_actions',
			array(
				'label' => esc_html__( 'Clean actions', 'thegem' ),
				'description' => esc_html__( 'You can clean all default WooCommerce PHP functions hooked to this action.', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'condition' => [
					'hook_type' => 'woocommerce',
				],
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label' => esc_html__( 'Alignment', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Centered', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'default' => 'left',
			)
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
		$settings = wp_parse_args(
			$this->get_settings_for_display(),
			array(
				'hook_type' => 'manual',
				'manual_hook' => '',
				'wc_hook_type' => 'product',
				'product_hook' => 'default',
				'cart_hook' => '0',
				'checkout_hook' => '0',
				'clean_actions' => 'yes',
				'alignment' => 'left',
			)
		);

		if($settings['hook_type'] === 'manual' && !empty(trim($settings['manual_hook']))) {
			echo '<div class="thegem-wp-hook thegem-wp-hook-'.esc_attr(trim($settings['manual_hook'])).' thegem-wp-hook-alignment-'.esc_attr($settings['alignment']).'">';
			do_action(trim($settings['manual_hook']));
			echo '</div>';
		}

		if($settings['hook_type'] === 'woocommerce') {
			$hook = '';

			if($settings['wc_hook_type'] === 'product') {
				$hook = $settings['product_hook'] === 'default' ? 'woocommerce_single_product_summary' : $settings['product_hook'];
				$product = thegem_templates_init_product();
			} elseif($settings['wc_hook_type'] === 'cart') {
				$hook = $settings['cart_hook'];
			}elseif($settings['wc_hook_type'] === 'checkout') {
				$hook = $settings['checkout_hook'];
			}

			if ( 'yes' === $settings['clean_actions'] ) {
				if ( 'woocommerce_checkout_billing' === $hook ) {
					remove_action( 'woocommerce_checkout_billing', array( WC()->checkout(), 'checkout_form_billing' ) );
				} elseif ( 'woocommerce_checkout_shipping' === $hook ) {
					remove_action( 'woocommerce_checkout_shipping', array( WC()->checkout(), 'checkout_form_shipping' ) );
				} elseif ( 'woocommerce_checkout_before_customer_details' === $hook ) {
					remove_action( 'woocommerce_checkout_before_customer_details', 'wc_get_pay_buttons', 30 );
				} elseif ( 'woocommerce_before_checkout_form' === $hook ) {
					remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
					remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
					remove_action( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10 );
				} elseif ( 'woocommerce_cart_collaterals' === $hook ) {
					remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
					remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
				} elseif ( 'woocommerce_before_cart' === $hook ) {
					remove_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
				} elseif ( 'woocommerce_before_single_product' === $hook ) {
					remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices' );
					remove_action( 'woocommerce_before_single_product', 'wc_print_notices' );
				} elseif ( 'woocommerce_before_single_product_summary' === $hook ) {
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
					remove_action('woocommerce_before_single_product_summary', 'woocommerce_template_single_meta', 35);
				} elseif ( 'woocommerce_product_thumbnails' === $hook ) {
					remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
				} elseif ( 'woocommerce_single_product_summary' === $hook ) {
					remove_action('woocommerce_single_product_summary', 'thegem_woocommerce_back_to_shop_button', 4);
					remove_action( 'woocommerce_single_product_summary', 'thegem_woocommerce_product_page_navigation', 5 );
					remove_action('woocommerce_single_product_summary', 'thegem_woocommerce_product_page_attribute', 6);
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 60 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating' );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
					remove_action('woocommerce_single_product_summary', 'thegem_woocommerce_size_guide', 35);
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_loop_add_to_cart', 30 );
				} elseif ( 'woocommerce_before_variations_form' === $hook ) {
					remove_action( 'woocommerce_before_variations_form', 'woocommerce_single_variation' );
				} elseif ( 'woocommerce_single_variation' === $hook ) {
					remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation' );
					remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
					remove_action( 'woocommerce_before_variations_form', 'woocommerce_single_variation' );
				} elseif ( 'woocommerce_after_single_product_summary' === $hook ) {
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' );
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
				} elseif ( 'woocommerce_checkout_order_review' === $hook ) {
					remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
					remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 20 );
					remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
					remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 10 );
				}
			}

			echo '<div class="thegem-wp-hook thegem-wp-hook-'.esc_attr($hook).' thegem-wc-hook-'.esc_attr($settings['wc_hook_type']).' thegem-wp-hook-alignment-'.esc_attr($settings['alignment']).'">';
			if ( 'woocommerce_before_checkout_form' === $hook || 'woocommerce_after_checkout_form' === $hook ) {
				do_action($hook, WC()->checkout());
			} else {
				do_action($hook);
			}
				echo '</div>';


			if($settings['wc_hook_type'] === 'product') {
				thegem_templates_close_product();
			}

		}
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_WP_Hook());