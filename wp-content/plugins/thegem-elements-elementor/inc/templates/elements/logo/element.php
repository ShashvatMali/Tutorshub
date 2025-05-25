<?php

namespace TheGem_Elementor\Widgets\TemplateLogo;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Logo.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateLogo extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		
		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LOGO_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LOGO_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LOGO_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LOGO_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_script('thegem-te-logo', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LOGO_URL . '/js/logo.js', array('jquery'), false, true);
		wp_register_style('thegem-te-logo', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LOGO_URL . '/css/logo.css');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-logo';
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
		return __('Logo', 'thegem');
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
		return ['thegem-te-logo'];
	}

	public function get_script_depends() {
		return ['thegem-te-logo'];
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
			'section_general',
			[
				'label' => __('General', 'thegem'),
			]
		);

		$this->add_control(
			'desktop_logo',
			[
				'label' => __('Desktop', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'desktop_logo_dark',
				'options' => [
					'desktop_logo_dark' => __('Desktop Logo (Dark)', 'thegem'),
					'desktop_logo_light' => __('Desktop Logo (Light)', 'thegem'),
					'sticky_logo_dark' => __('Sticky Header Logo (Dark)', 'thegem'),
					'sticky_logo_light' => __('Sticky Header Logo (Light)', 'thegem'),
				],
			]
		);

		$this->add_control(
			'mobile_logo',
			[
				'label' => __('Mobile', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'mobile_logo_dark',
				'options' => [
					'mobile_logo_dark' => __('Mobile Logo (Dark)', 'thegem'),
					'mobile_logo_light' => __('Mobile Logo (Light)', 'thegem'),
				],
			]
		);

		$this->add_control(
			'tablet_landscape_logo',
			[
				'label' => __('Tablet (Landscape)', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('As set for desktop', 'thegem'),
					'mobile' => __('As set for mobile', 'thegem'),
				],
			]
		);

		$this->add_control(
			'tablet_portrait_logo',
			[
				'label' => __('Tablet (Portrait)', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('As set for desktop', 'thegem'),
					'mobile' => __('As set for mobile', 'thegem'),
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

		$this->add_control(
			'styles_description',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Go to <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/general/logo-and-identity" target="_blank">Theme Options</a> to manage different types of your website logo.', 'thegem'),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		// Output Desktop Logo
		$output_desktop_logo = $output_mobile_logo = '';
		$echo = false;
		$is_light = false;
		if (get_the_ID() !== intval(thegem_get_option('header_builder_sticky')) && !empty($GLOBALS['thegem_custom_header_light'])) {
			$is_light = true;
		}
		if (!$is_light && isset($settings['desktop_logo']) && $settings['desktop_logo'] == 'desktop_logo_dark') {
			$output_desktop_logo = thegem_get_logo_img(esc_url(thegem_get_option('logo')), intval(thegem_get_option('logo_width')), 'tgp-exclude default', $echo);
		}
		if ((!$is_light && isset($settings['desktop_logo']) && $settings['desktop_logo'] == 'desktop_logo_light') || $is_light) {
			$output_desktop_logo = thegem_get_logo_img(esc_url(thegem_get_option('logo_light')), intval(thegem_get_option('logo_width')), 'tgp-exclude default light', $echo);
		}

		// Output Mobile Logo
		if (!$is_light && isset($settings['mobile_logo']) && $settings['mobile_logo'] == 'mobile_logo_dark') {
			$output_mobile_logo = thegem_get_logo_img(esc_url(thegem_get_option('small_logo')), intval(thegem_get_option('small_logo_width')), 'tgp-exclude small', $echo);
		}
		if ((!$is_light && isset($settings['mobile_logo']) && $settings['mobile_logo'] == 'mobile_logo_light') || $is_light) {
			$output_mobile_logo = thegem_get_logo_img(esc_url(thegem_get_option('small_logo_light')), intval(thegem_get_option('small_logo_width')), 'tgp-exclude small light', $echo);
		}

		// Output Sticky Logo
		if (isset($settings['desktop_logo']) && $settings['desktop_logo'] == 'sticky_logo_dark') {
			$output_desktop_logo = thegem_get_logo_img(esc_url(thegem_get_option('small_logo')), intval(thegem_get_option('small_logo_width')), 'tgp-exclude small', $echo);
		}
		if ((isset($settings['desktop_logo']) && $settings['desktop_logo'] == 'sticky_logo_light')) {
			$output_desktop_logo = thegem_get_logo_img(esc_url(thegem_get_option('small_logo_light')), intval(thegem_get_option('small_logo_width')), 'tgp-exclude small light', $echo);
		}
		
		// Output Custom Link
		$this->add_render_attribute('link', 'class', 'account-link');
		if ( ! empty( $settings['custom_link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['custom_link'] );
		} else {
			$this->add_link_attributes('link', ['url' => esc_url(thegem_home_url('/'))]);
		}
        
        ?>

		<div class="thegem-te-logo <?php echo wp_is_mobile() ? 'mobile-view' : 'desktop-view'; ?>"
			 data-tablet-landscape="<?php echo esc_attr($settings['tablet_landscape_logo']); ?>"
			 data-tablet-portrait="<?php echo esc_attr($settings['tablet_portrait_logo']); ?>">
			<div class="site-logo">
                <a <?php echo($this->get_render_attribute_string('link')); ?>>
					<?php if (thegem_get_option('logo')) { ?>
						<span class="logo">
							<span class="logo desktop"><?php echo $output_desktop_logo; ?></span>
							<span class="logo mobile"><?php echo $output_mobile_logo; ?></span>
						</span>
					<?php } else {
						echo bloginfo('name');
					} ?>
				</a>
			</div>
		</div>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateLogo());
