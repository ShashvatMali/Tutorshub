<?php

namespace TheGem_Elementor\Widgets\TemplateMenuSecondary;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use TheGem_Mega_Menu_Walker;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Menu.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateMenuSecondary extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_SECONDARY_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_SECONDARY_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_SECONDARY_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_SECONDARY_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}
		
		wp_register_style('thegem-te-menu-secondary', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_SECONDARY_URL . '/css/menu-secondary.css');
		wp_register_style('thegem-te-menu-secondary-editor', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_SECONDARY_URL . '/css/menu-secondary-editor.css');
		wp_register_script('thegem-te-menu-secondary', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_SECONDARY_URL . '/js/menu-secondary.js', array('jquery'), false, true);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-menu-secondary';
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
		return __('Secondary Menu', 'thegem');
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
		//return ['thegem_elements'];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-te-menu-secondary',
				'thegem-te-menu-secondary-editor'];
		}
		return ['thegem-te-menu-secondary'];
	}

	public function get_script_depends() {
		return ['thegem-te-menu-secondary'];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	protected function get_menu_list() {
		$menus = get_terms('nav_menu');
		$menus_list = [];
		foreach ($menus as $menu) {
			$menus_list[$menu->slug] = $menu->name;
		}
		return $menus_list;
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
			'menu_source',
			[
				'label' => __('Menu Source', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_menu_list(),
				'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/nav-menus.php" target="_blank">Menus screen</a> to manage your menus', 'thegem'),
			]
		);

		$this->add_control(
			'type',
			[
				'label' => __('Menu Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'list',
				'options' => [
					'list' => __('List', 'thegem'),
					'dropdown' => __('Dropdown', 'thegem'),
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_spacing',
			[
				'label' => __('Dropdown Spacing', 'thegem'),
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
					'{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu > li.menu-item-has-children,
					{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item' => 'margin-bottom: -{{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'results_title_typography',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu > li a,
				{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item__wrapper ul li a,
				{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item__current',
			]
		);

		$this->start_controls_tabs('menu_tabs');

		$this->start_controls_tab('menu_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$this->add_control(
			'menu_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item__wrapper ul li a,
					{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu > li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('menu_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$this->add_control(
			'menu_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item__wrapper ul li a:hover,
					{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu > li a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('menu_tab_active', ['label' => __('Active', 'thegem'),]);

		$this->add_control(
			'menu_color_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item__current,
					{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item:after,
					{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu li.menu-item-active > a:hover,
					{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu li.menu-item-current > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'dropdown_header',
			[
				'label' => __('Dropdown', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'type' => 'dropdown'
				],
			]
		);

		$this->add_control(
			'dropdown_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item__wrapper' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'type' => 'dropdown'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'dropdown_box_shadow',
				'label' => __('Box Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item__wrapper',
				'condition' => [
					'type' => 'dropdown'
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-secondary-dropdown .dropdown-item__wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; margin-left: -{{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'type' => 'dropdown'
				],
			]
		);

		$this->add_control(
			'list_header',
			[
				'label' => __('List', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'type' => 'list'
				],
			]
		);

		$this->add_control(
			'list_allow_prefix',
			[
				'label' => __('Arrow Prefix', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'type' => 'list',
				],
			]
		);

		$this->add_responsive_control(
			'list_space_between',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu > li' => 'padding: 0 calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .thegem-te-menu-secondary-nav ul.nav-menu' => 'margin: 0 calc(-{{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'type' => 'list'
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if (!empty($settings['menu_source'])): ?>

			<div class="thegem-te-menu-secondary">

				<?php if($settings['type'] == 'list'): ?>
					<div class="thegem-te-menu-secondary-nav <?php echo !$settings['list_allow_prefix'] ? 'disable-prefix' : ''; ?>">
						<?php
						wp_nav_menu(array(
							"menu" => $settings['menu_source'],
							"menu_class" => 'nav-menu styled',
							"container" => false,
							"echo" => true,
							"walker" => new TheGem_Mega_Menu_Walker
						)); ?>
					</div>
				<?php endif; ?>

				<?php if($settings['type'] == 'dropdown'):
					// Get dropdown current title
					$current_item_title = '';
					if ( $menu_items = wp_get_nav_menu_items( $settings['menu_source'] ) ) {
						foreach ( $menu_items as $menu_item ) {
							if ($menu_item->object_id == get_queried_object_id()) {
								$current_item_title = $menu_item->title;
							} elseif($menu_item->menu_order == 1) {
								$current_item_title = $menu_item->title;
							}
						}
					} ?>
					<div class="thegem-te-menu-secondary-dropdown">
						<div class="dropdown-item">
							<div class="dropdown-item__current"><?= $current_item_title ?></div>
							<div class="dropdown-item__wrapper">
								<?php
								wp_nav_menu(array(
									"menu" => $settings['menu_source'],
									"menu_class" => 'nav-menu styled',
									"container" => false,
									"echo" => true,
									"walker" => new TheGem_Mega_Menu_Walker
								)); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php else: ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo esc_html__('Please select Menu Source', 'thegem') ?>
			</div>
		<?php endif;

	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateMenuSecondary());