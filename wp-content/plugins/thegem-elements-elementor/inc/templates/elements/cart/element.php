<?php

namespace TheGem_Elementor\Widgets\TemplateCart;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Cart.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateCart extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CART_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CART_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CART_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CART_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_script('thegem-te-cart', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CART_URL . '/js/cart.js', array('jquery'), false, true);
		wp_register_style('thegem-te-cart', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CART_URL . '/css/cart.css');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-cart';
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
	/*public function show_in_panel() {
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'header';
	}*/

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Cart', 'thegem');
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
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'header') return ['thegem_header_builder'];
		return ['thegem_elements'];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-te-cart'];
		}
		return ['thegem-te-cart'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-te-cart'];
		}
		return ['thegem-te-cart'];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('General', 'thegem'),
			]
		);

		$this->add_responsive_control(
			'minicart_spacing',
			[
				'label' => __('Mini Cart Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-cart.desktop-view .minicart' => 'top: calc(100% + {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'icon_header',
			[
				'label' => __('Cart Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cart_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'tiny' => __('Tiny', 'thegem'),
					'small' => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'custom' => __('Custom', 'thegem'),
				],
			]
		);

		$this->add_responsive_control(
			'icon_size_custom',
			[
				'label' => __('Custom Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'icon_size' => 'custom'
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-cart .menu-item-cart' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .thegem-te-cart .menu-item-cart .te-cart-icon i' => 'font-size: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important; line-height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->start_controls_tabs('icon_tabs');

		$this->start_controls_tab('icon_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$this->add_control(
			'icon_color_normal',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-cart .menu-item-cart' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('icon_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-cart:hover .menu-item-cart' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('icon_tab_active', ['label' => __('Active', 'thegem'),]);

		$this->add_control(
			'icon_color_active',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-cart .menu-item-cart.active-page' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'amount_header',
			[
				'label' => __('Amount Label', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_type',
			[
				'label' => __('Amount label type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => [
					'circle' => __('Circle', 'thegem'),
					'label' => __('Label', 'thegem'),
				],
			]
		);

		$this->start_controls_tabs('label_tabs');

		$this->start_controls_tab('label_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$this->add_control(
			'label_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .minicart-item-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_background',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-cart .minicart-item-count,
					{{WRAPPER}} .thegem-te-cart.label-count .minicart-item-count:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('label_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$this->add_control(
			'label_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .minicart-menu-link:hover .minicart-item-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_background_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-cart .minicart-menu-link:hover .minicart-item-count,
					{{WRAPPER}} .thegem-te-cart.label-count .minicart-menu-link:hover .minicart-item-count:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$uniqid = $this->get_id();

		$label = $settings['label_type'] == 'circle' ? 'circle-count' : 'label-count';
		$view_type = wp_is_mobile() ? 'mobile-view' : 'desktop-view';

		$count = thegem_get_cart_count();
		$linkClass = $count == 0 ? 'empty' : '';
		$linkClass .= is_cart() || is_checkout() ? ' active-page' : '';

		$mini_cart_type = thegem_get_option('mini_cart_type');
		?>

		<div class="thegem-te-cart <?php echo esc_attr($label); ?> <?php echo esc_attr($view_type); ?> cart-icon-size-<?php echo esc_attr($settings['icon_size']); ?>">
			<div class="menu-item-cart">
				<div class="te-cart-icon">
					<?php if ($settings['cart_icon']['value']) {
						Icons_Manager::render_icon($settings['cart_icon'], ['aria-hidden' => 'true']);
					} else {
						if (thegem_get_option('cart_icon_pack') && thegem_get_option('cart_icon')) {
							wp_enqueue_style('icons-' . thegem_get_option('cart_icon_pack'));
						} ?>
						<i class="default"></i>
					<?php } ?>
				</div>
				<a href="<?php echo esc_url(get_permalink(wc_get_page_id('cart'))); ?>"
				   class="minicart-menu-link <?php echo esc_attr($linkClass); ?>">
					<span class="minicart-item-count "><?php echo esc_html($count); ?></span>
				</a>

				<?php if (!empty($mini_cart_type) && $mini_cart_type == 'dropdown'): ?>
					<?php if (!is_admin() && !\Elementor\Plugin::$instance->editor->is_edit_mode() && !((is_checkout() || is_cart()) && class_exists( '\WooCommerce\PayPalCommerce\PluginModule' )) && (!empty($GLOBALS['thegem_template_type']) && $GLOBALS['thegem_template_type'] === 'header')): ?>
						<div class="minicart">
							<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
						</div>
						<div class="mobile-minicart-overlay"></div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>

			<script type="text/javascript">
				(function ($) {
					setTimeout(function () {
						$().initCartScripts();
					}, 1000);
				})(jQuery);
			</script>
		<?php endif;
	}
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCart());
}