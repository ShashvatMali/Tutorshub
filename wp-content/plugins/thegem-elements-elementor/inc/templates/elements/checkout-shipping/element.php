<?php

namespace TheGem_Elementor\Widgets\TemplateCheckoutShipping;

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
class TheGem_TemplateCheckoutShipping extends Widget_Base {
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
		return 'thegem-template-checkout-shipping';
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
		return __('Shipping Details', 'thegem');
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
		return 'thegem-te-checkout-shipping';
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
			'checkout_shipping',
			[
				'label' => __('Shipping Details', 'thegem'),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'compact' => __('Compact', 'thegem'),
				],
				'default' => '',
				'separator' => 'after',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .checkout-shipping-title' => 'text-align: {{VALUE}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .checkout-shipping-title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .checkout-shipping-title' => '{{VALUE}};',
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
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .checkout-shipping-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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

		$this->add_control(
			'heading_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .checkout-shipping-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'heading' => '1',
				]
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
			'input_text_color',
			[
				'label' => __('Input Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row input.input-text' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row textarea.input-text' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row .select2-selection__rendered' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row .select2-selection__arrow' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row .checkbox-sign:before' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row input.input-text' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row textarea.input-text' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row .select2-selection__rendered' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row .checkbox-sign' => 'background-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row input.input-text' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row textarea.input-text' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row .select2-selection--single' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row .checkbox-sign' => 'border-color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'input_border_color_error',
			[
				'label' => __('Input Border Color Error', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row.woocommerce-invalid input.input-text' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row.woocommerce-invalid textarea.input-text' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row label abbr' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row .select2-selection__placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row input.input-text::placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row textarea.input-text::placeholder' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'label_text_color',
			[
				'label' => __('Label Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row label' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' .form-row.create-account span' => 'color: {{VALUE}} !important;',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .form-row input.input-text' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}}'.$this->get_customize_class().' .form-row textarea.input-text' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}}'.$this->get_customize_class().' .form-row .select2-selection--single' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}}'.$this->get_customize_class().' .form-row .select2-selection__rendered' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .form-row .checkbox-sign' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
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
			'layout' => '',
			'heading' => 1,
			'heading_text_style' => '',
			'heading_font_weight' => 'light',
		), $settings);
		$title_classes = implode(' ', array('checkout-shipping-title', $params['heading_text_style'], $params['heading_font_weight']));
		$title_tag = empty($params['heading_text_style']) ? 'h3' : 'div';

		ob_start();
		wc_load_cart();

		if (empty(WC()->cart)) {
			ob_end_clean();
			return thegem_templates_close_single_post($this->get_name(), $this->shortcode_settings(), '');
		}

		$checkout = WC()->checkout();
?>

		<div class="<?= $this->get_widget_wrapper(); ?> <?= esc_attr($params['layout']); ?>">
<div class="woocommerce-shipping-fields">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>

		<?php if (!empty($params['heading'])) : ?>
			<<?= $title_tag; ?> id="ship-to-different-address-title" class="<?= $title_classes; ?>"><?php esc_html_e( 'Different address?', 'thegem' ); ?></<?= $title_tag; ?>>
		<?php endif; ?>

		<p id="ship-to-different-address" class="form-row">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox gem-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" /> <span><?php esc_html_e( 'Ship to a different address?', 'woocommerce' ); ?></span>
			</label>
		</p>

		<div class="shipping_address">

			<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

			<div class="woocommerce-shipping-fields__field-wrapper">
				<?php
				$fields = $checkout->get_checkout_fields( 'shipping' );

				foreach ( $fields as $key => $field ) {
					woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
				}
				?>
				<div class="clear"></div>
			</div>

			<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

		</div>

	<?php endif; ?>
</div>
<div class="woocommerce-additional-fields">
	<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>

		<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>
			<?php if (!empty($params['heading'])) : ?>
				<<?= $title_tag; ?> class="<?= $title_classes; ?>"><?php esc_html_e( 'Additional information', 'woocommerce' ); ?></<?= $title_tag; ?>>
			<?php endif; ?>
		<?php endif; ?>

		<div class="woocommerce-additional-fields__field-wrapper">
			<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
				<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
</div>
		</div>

<?php
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		echo $return_html;
	}

}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCheckoutShipping());
}