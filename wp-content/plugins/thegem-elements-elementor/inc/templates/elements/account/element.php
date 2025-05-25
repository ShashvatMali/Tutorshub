<?php

namespace TheGem_Elementor\Widgets\TemplateAccount;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Account.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateAccount extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_ACCOUNT_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_ACCOUNT_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_ACCOUNT_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_ACCOUNT_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-te-account', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_ACCOUNT_URL . '/css/account.css');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-account';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'header';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('My Account Icon', 'thegem');
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
		return ['thegem_header_builder'];
	}

	public function get_style_depends() {
		return ['thegem-te-account'];
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


		$this->add_control(
			'account_icon',
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
					'{{WRAPPER}} .thegem-te-account .gem-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'custom_link',
			[
				'label' => __( 'Custom Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'show_external' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __('Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .thegem-te-account .gem-icon' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .thegem-te-account a:hover .gem-icon' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .thegem-te-account a.active-page .gem-icon' => 'color: {{VALUE}};',
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

		$this->add_render_attribute('link', 'class', 'account-link');

		if ( ! empty( $settings['custom_link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['custom_link'] );
			$url_parts = parse_url( home_url() );
			$current_url = $url_parts['scheme'] . "://" . $url_parts['host'] . add_query_arg( NULL, NULL );
			$current_parsed = parse_url($current_url);
			$link_parsed = parse_url($settings['custom_link']['url']);
			if(($current_parsed['scheme'] ?? null) === ($link_parsed['scheme'] ?? null) &&
				($current_parsed['host'] ?? null) === ($link_parsed['host'] ?? null) &&
				strpos($current_parsed['path'] ?? '', $link_parsed['path'] ?? '') === 0) {
				$this->add_render_attribute('link', 'class', 'active-page');
			}
		} else {
			$this->add_link_attributes('link', [
				'url' => defined( 'WC_PLUGIN_FILE' ) ? get_permalink(wc_get_page_id('myaccount')) : 'javascript:void(0)',
			]);
			if(defined( 'WC_PLUGIN_FILE' ) && is_account_page()) {
				$this->add_render_attribute('link', 'class', 'active-page');
			}
		} ?>

		<div class="thegem-te-account">
			<a <?php echo($this->get_render_attribute_string('link')); ?>>
			<div class="gem-icon gem-simple-icon gem-icon-size-<?php echo esc_attr($settings['icon_size']); ?>">
				<?php if ($settings['account_icon']['value']) {
					Icons_Manager::render_icon($settings['account_icon'], ['aria-hidden' => 'true']);
				} else { ?>
					<i class="default"></i>
				<?php } ?>
			</div>
			</a>
		</div>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateAccount());
