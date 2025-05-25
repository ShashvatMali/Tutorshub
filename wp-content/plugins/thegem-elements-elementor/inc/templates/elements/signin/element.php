<?php

namespace TheGem_Elementor\Widgets\TemplateSignin;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Signin.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateSignin extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SIGNIN_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SIGNIN_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SIGNIN_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SIGNIN_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-te-signin', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SIGNIN_URL . '/css/signin.css');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-signin';
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
/*	public function show_in_panel() {
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
		return __('Sign In / Sign Out', 'thegem');
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
		return ['thegem-te-signin'];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-signin';
	}

	public function is_my_account_page_exist() {
		if (defined( 'WC_PLUGIN_FILE' ) && wc_get_page_id( 'myaccount' ) != '-1') {
			$post = get_post( wc_get_page_id( 'myaccount' ) );

			return !empty($post->post_status) && $post->post_status == 'publish';
		}

		return false;
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		// Layout Section
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'layout_type',
			[
				'label' => __('Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'link',
				'options' => [
					'link' => __('Icon/Link', 'thegem'),
					'btn' => __('Button', 'thegem'),
				],
			]
		);

		$this->end_controls_section();

		// Sign In Section
		$this->start_controls_section(
			'section_signin',
			[
				'label' => __('Sign In User', 'thegem'),
			]
		);

		$this->add_control(
			'signin_link_type',
			[
				'label' => __('Link Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'signin',
				'options' => [
					'signin' => __($this->is_my_account_page_exist() ? 'Sign In / My Account' : 'Sign In', 'thegem'),
					'custom' => __('Custom Selection', 'thegem'),
				],
			]
		);

		$this->add_control(
			'signin_link_custom',
			[
				'label' => __('Link Type', 'thegem' ),
				'type' => Controls_Manager::URL,
				'condition' => [
					'signin_link_type' => 'custom',
				],
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'show_external' => true,
			]
		);

		$this->add_control(
			'show_signin_icon',
			[
				'label' => __('Sign In Icon', 'thegem'),
				'return_value' => '1',
				'default' => '0',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'signin_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_signin_icon' => '1',
				],
			]
		);

		$this->add_control(
			'show_signin_text',
			[
				'label' => __('Sign In Text', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'signin_text',
			[
				'label' => __('Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Sign In', 'thegem'),
				'condition' => [
					'show_signin_text' => '1'
				],
			]
		);

		$this->end_controls_section();

		// Sign Out Section
		$this->start_controls_section(
			'section_signout',
			[
				'label' => __('Sign Out User', 'thegem'),
			]
		);

		$this->add_control(
			'signout_link_type',
			[
				'label' => __('Link Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'signout',
				'options' => [
					'signout' => __($this->is_my_account_page_exist() ? 'Sign Out / My Account' : 'Sign Out', 'thegem'),
					'custom' => __('Custom Selection', 'thegem'),
				],
			]
		);

		$this->add_control(
			'signout_link_custom',
			[
				'label' => __('Link Type', 'thegem' ),
				'type' => Controls_Manager::URL,
				'condition' => [
					'signout_link_type' => 'custom',
				],
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'show_external' => true,
			]
		);

		$this->add_control(
			'show_signout_icon',
			[
				'label' => __('Sign Out Icon', 'thegem'),
				'return_value' => '1',
				'default' => '0',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'signout_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_signout_icon' => '1',
				],
			]
		);

		$this->add_control(
			'show_username',
			[
				'label' => __('Sign Username', 'thegem'),
				'return_value' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'show_signout_text',
			[
				'label' => __('Sign Out Text', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'signout_text',
			[
				'label' => __('Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Sign Out', 'thegem'),
				'condition' => [
					'show_signout_text' => '1'
				],
			]
		);

		$this->end_controls_section();

		// Button Style Section
		$this->start_controls_section(
			'style_btn_section',
			[
				'label' => __('Button Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout_type' => 'btn',
				],
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
						'max' => 10,
					],
				],
				'condition' => [
					'layout_type' => 'btn',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link.signin-link-type--btn' => 'border-width: {{SIZE}}{{UNIT}};',
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
				'condition' => [
					'layout_type' => 'btn',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link.signin-link-type--btn' => 'border-radius:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn_background_color',
			[
				'label' => __('Background color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'layout_type' => 'btn',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link.signin-link-type--btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'layout_type' => 'btn',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link.signin-link-type--btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'layout_type' => 'btn',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link.signin-link-type--btn' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'layout_type' => 'btn',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link.signin-link-type--btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Icon Style Section
		$this->start_controls_section(
			'style_icon_section',
			[
				'label' => __('Icon Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .thegem-te-signin .signin-link-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
  
		$this->add_control(
			'icon_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => __('Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link:hover .signin-link-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Text Style Section
		$this->start_controls_section(
			'style_text_section',
			[
				'label' => __('Text Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_style',
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
			]
		);

		$this->add_control(
			'text_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'text_letter_spacing',
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
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link-text' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_transform',
			[
				'label' => __('Text Transform', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
				],
				'default' => '',
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link-text' => '{{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_max_width',
			[
				'label' => __('Max Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link-text' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label' => __('Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-signin .signin-link:hover .signin-link-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// General params
		$params = array_merge(array(
			'element_class' => $this->get_widget_wrapper()
		), $settings);
  
		$uniqid = uniqid('thegem-custom-').rand(1,9999);
  
		//Output SignIn Url
		$this->add_render_attribute('signin_link', 'class', 'signin-link signin-link-type--'.$params['layout_type'].'');
		if ( ! empty( $params['signin_link_custom']['url'] ) ) {
			$this->add_link_attributes('signin_link', $params['signin_link_custom'] );
		} else {
			$this->add_link_attributes('signin_link', [
				'url' => $this->is_my_account_page_exist() ? get_permalink( wc_get_page_id( 'myaccount' )) : wp_login_url(get_permalink()),
			]);
		}

		//Output SignOut Url
		$this->add_render_attribute('signout_link', 'class', 'signin-link signin-link-type--'.$params['layout_type'].'');
		if ( ! empty( $params['signout_link_custom']['url'] ) ) {
			$this->add_link_attributes('signout_link', $params['signout_link_custom'] );
		} else {
			$this->add_link_attributes('signout_link', [
				'url' => $this->is_my_account_page_exist() ? wp_logout_url(get_permalink( wc_get_page_id( 'myaccount' ))) : wp_logout_url(thegem_home_url()),
			]);
		}

		$text_styled_class = implode(' ', array($params['text_style'], $params['text_font_weight']));

		?>

		<div class="<?= $params['element_class'] ?> <?= esc_attr($uniqid); ?>">
			<?php if (is_user_logged_in()) : ?>
				<a <?= ($this->get_render_attribute_string('signout_link')); ?>>
					<?php if (!empty($params['show_signout_icon'])): ?>
						<div class="signin-link-icon gem-icon gem-simple-icon gem-icon-size-<?= esc_attr($params['icon_size']); ?>">
							<?php if ($params['signout_icon']['value']) {
								Icons_Manager::render_icon($params['signout_icon'], ['aria-hidden' => 'true']);
							} ?>
						</div>
					<?php endif; ?>

					<?php if (!empty($params['show_username'])): ?>
						<div class="signin-link-text <?=$text_styled_class ?>">
							<?php 
								$current_user = wp_get_current_user();
								echo $current_user->display_name;
								if(!empty($params['show_signout_text'])) {
									echo ' (' . $params['signout_text'] . ')';
								}
							?>
						</div>
					<?php else: ?>
						<?php if (!empty($params['show_signout_text'])): ?>
							<div class="signin-link-text <?=$text_styled_class ?>">
								<?= $params['signout_text'] ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</a>
			<?php else : ?>
				<a <?php echo($this->get_render_attribute_string('signin_link')); ?>>
					<?php if (!empty($params['show_signin_icon'])): ?>
						<div class="signin-link-icon gem-icon gem-simple-icon gem-icon-size-<?= esc_attr($params['icon_size']); ?>">
							<?php if ($params['signin_icon']['value']) {
								Icons_Manager::render_icon($params['signin_icon'], ['aria-hidden' => 'true']);
							} ?>
						</div>
					<?php endif; ?>

					<?php if (!empty($params['show_signin_text'])): ?>
						<div class="signin-link-text <?=$text_styled_class ?>">
							<?= $params['signin_text'] ?>
						</div>
					<?php endif; ?>
				</a>
			<?php endif;?>
		</div>

		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateSignin());
