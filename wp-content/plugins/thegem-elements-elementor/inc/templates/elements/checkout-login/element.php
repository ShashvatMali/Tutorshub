<?php

namespace TheGem_Elementor\Widgets\TemplateCheckoutLogin;

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
class TheGem_TemplateCheckoutLogin extends Widget_Base {
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
		return 'thegem-template-checkout-login';
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
		return __('Customer Login', 'thegem');
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
		return 'thegem-te-checkout-login';
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

		$this->start_controls_section(
			'checkout_login',
			[
				'label' => __('Customer Login', 'thegem'),
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __('Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Returning customer?', 'woocommerce' ),
			]
		);

		$this->add_control(
			'link_text',
			[
				'label' => __('Link Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Click here to login', 'woocommerce' ),
			]
		);
		
		$this->add_control(
			'separator',
			[
				'label' => __('Separator', 'thegem'),
				'return_value' => '1',
				'default' => '0',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'separator_position',
			[
				'label' => __('Separator Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'condition' => [
					'separator' => '1',
				],
			]
		);
		
		$this->add_responsive_control(
			'separator_spacing',
			[
				'label' => __('Separator Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'separator' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .checkout-notice .separator' => 'margin: 0 {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'text_styles',
			[
				'label' => __('Text', 'thegem'),
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
					'{{WRAPPER}} '.$this->get_customize_class().' .checkout-login-notice' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => __('Link Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .checkout-login-notice a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => __('Link Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .checkout-login-notice a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'popup_styles',
			[
				'label' => __('Popup', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'popup_background_color',
			[
				'label' => __('Popup Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Popup Title Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup h3' => 'color: {{VALUE}} !important;',
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
					'#checkout-login-popup .form-row input.input-text' => 'color: {{VALUE}} !important;',
					'#checkout-login-popup .form-row .checkbox-sign:before' => 'color: {{VALUE}} !important;',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'input_background_color',
			[
				'label' => __('Input Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup .form-row input.input-text' => 'background-color: {{VALUE}} !important;',
					'#checkout-login-popup .form-row .checkbox-sign' => 'background-color: {{VALUE}} !important;',
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
					'#checkout-login-popup .form-row input.input-text' => 'border-color: {{VALUE}} !important;',
					'#checkout-login-popup .checkbox-sign' => 'border-color: {{VALUE}} !important;',
					'#checkout-login-popup .checkout-login .login .lost_password:before' => 'background-color: {{VALUE}} !important;',
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
					'#checkout-login-popup label' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'required_marker_color',
			[
				'label' => __('Required Marker Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup label .required' => 'color: {{VALUE}} !important;',
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
					'#checkout-login-popup input.input-text' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'popup_link_color',
			[
				'label' => __('Link Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup a' => 'color: {{VALUE}};',
					'#checkout-login-popup .fancybox-close-small' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'popup_link_hover_color',
			[
				'label' => __('Link Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup a:hover' => 'color: {{VALUE}};',
					'#checkout-login-popup .fancybox-close-small:hover' =>  'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_btn_heading',
			[
				'label' => __('Login Form Button', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'btn_border_width',
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
				'selectors' => [
					'#checkout-login-popup .checkout-login .checkout-login-button button' => 'border-width: {{SIZE}}{{UNIT}} !important; line-height: calc(40{{UNIT}} - calc({{SIZE}}{{UNIT}}*2)) !important;',
				],
			]
		);

		$this->add_responsive_control(
			'btn_border_radius',
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
					'#checkout-login-popup .checkout-login .checkout-login-button button' => 'border-radius:{{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'btn_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup .checkout-login .checkout-login-button button' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'btn_text_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup .checkout-login .checkout-login-button button:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'btn_background_color',
			[
				'label' => __('Background color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup .checkout-login .checkout-login-button button' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'btn_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup .checkout-login .checkout-login-button button:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'btn_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup .checkout-login .checkout-login-button button' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'btn_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'#checkout-login-popup .checkout-login .checkout-login-button button:hover' => 'border-color: {{VALUE}} !important;',
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
			'text' => __( 'Returning customer?', 'woocommerce' ),
			'link_text' => __( 'Click here to login', 'woocommerce' ),
		), $settings);

		if(thegem_get_template_type(get_the_ID()) === 'checkout') {
			if('no' === get_option( 'woocommerce_enable_checkout_login_reminder' )) {
				echo '<div class="thegem-te-checkout-login template-checkout-empty-output default-background">'.__('Customer Login', 'thegem').'</div>'; return ;
			}
		} elseif(is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' )) {
			return ;
		}

		ob_start();
        ?>

		<div class="<?= $this->get_widget_wrapper(); ?>">
            <div class="checkout-notice checkout-login-notice">
	            <?php if(!empty($params['separator']) && $params['separator_position'] == 'left'): ?>
                    <span class="separator"></span>
	            <?php endif; ?>
                <?= esc_html($params['text']); ?>
                <a href="#" class="showlogin checkout-show-login-popup"><?= esc_html($params['link_text']); ?></a>
	            <?php if(!empty($params['separator']) && $params['separator_position'] == 'right'): ?>
                    <span class="separator"></span>
	            <?php endif; ?>
            </div>
		</div>

    <?php
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		echo $return_html;
	}

}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCheckoutLogin());
}