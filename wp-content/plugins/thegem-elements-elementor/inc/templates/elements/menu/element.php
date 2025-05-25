<?php

namespace TheGem_Elementor\Widgets\TemplateMenu;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use TheGem_Mega_Menu_Walker;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Menu.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateMenu extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_script('thegem-te-menu', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_URL . '/js/menu.js', array('jquery'), false, true);
		wp_localize_script('thegem-te-menu', 'thegem_menu_data', array(
			'ajax_url' => esc_url(admin_url('admin-ajax.php')),
			'ajax_nonce' => wp_create_nonce('ajax_security'),
			'backLabel' => !empty(thegem_get_option('mobile_menu_back_text')) ? thegem_get_option('mobile_menu_back_text') : esc_html__('Back', 'thegem'),
			'showCurrentLabel' => !empty(thegem_get_option('mobile_menu_show_this_page_text')) ? thegem_get_option('mobile_menu_show_this_page_text') : esc_html__('Show this page', 'thegem'),
		));

		wp_register_style('thegem-te-menu', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_URL . '/css/menu.css');
		wp_register_style('thegem-te-menu-default', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_URL . '/css/menu-default.css');
		wp_register_style('thegem-te-menu-overlay', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_URL . '/css/menu-overlay.css');
		wp_register_style('thegem-te-menu-hamburger', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_URL . '/css/menu-hamburger.css');
		wp_register_style('thegem-te-menu-mobile-default', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_URL . '/css/menu-mobile-default.css');
		wp_register_style('thegem-te-menu-mobile-sliding', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_MENU_URL . '/css/menu-mobile-sliding.css');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-menu';
	}

	/**
	 * Show in panel.
	 *
	 * Whether to show the widget in the panel or not. By default returns true.
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 * @since 1.0.0
	 * @access public
	 *
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
		return __('Menu', 'thegem');
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
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-te-menu',
				'thegem-te-menu-default',
				'thegem-te-menu-overlay',
				'thegem-te-menu-hamburger',
				'thegem-te-menu-mobile-default',
				'thegem-te-menu-mobile-sliding'];
		}
		return ['thegem-te-menu'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['jquery-dlmenu',
				'thegem-te-menu'];
		}
		return ['thegem-te-menu'];
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

	public function thegem_add_menu_item_split_logo($items) {
		$items .= '<li class="menu-item menu-item-type-split-logo">';
		$items .= '<div class="logo-fullwidth-block" style="display: flex;justify-content: center;z-index:0;">';
		$items .= '</div>';
		$items .= '</li>';

		return $items;
	}

	public function thegem_templates_menu_search_widget($items) {
		$items .= '<li class="menu-item menu-item-widget menu-item-type-search-widget" style="display: none">';
		$items .= '<a href="#"></a>';
		$items .= '<div class="minisearch">';
		$items .= '<form role="search" class="sf" action="' . esc_url(thegem_home_url('/')) . '" method="GET">';
		$items .= '<input class="sf-input" type="text" placeholder="' . esc_html__('Search...', 'thegem') . '" name="s">';
		if (defined( 'WC_PLUGIN_FILE' ) && thegem_get_option('website_search_post_type_products') == '1' && thegem_get_option('website_search_mixed_disabled') != '1') {
			$items .= '<input type="hidden" name="post_type" value="product" />';
		}
		$items .= '<span class="sf-submit-icon"></span>';
		$items .= '<input class="sf-submit" type="submit" value="">';
		$items .= '</form>';
		$items .= '</div>';
		$items .= '</li>';

		return $items;
	}

	public function thegem_templates_menu_socials_widget($items, $args) {
		ob_start();
		thegem_print_socials('rounded');
		$socials = ob_get_clean();

		$items .= '<li class="menu-item menu-item-widget menu-item-type-socials-widget" style="display: none">';
		$items .= '<div class="menu-item-socials socials-colored">' . $socials . '</div>';
		$items .= '</li>';

		return $items;
	}

	public function thegem_templates_menu_mobile_socials_widget($items, $args) {
		ob_start();
		thegem_print_socials();
		$socials = ob_get_clean();

		$items .= '<li class="menu-item menu-item-widget menu-item-type-socials-widget" style="display: none">';
		$items .= '<div class="menu-item-socials">' . $socials . '</div>';
		$items .= '</li>';

		return $items;
	}

	public function thegem_templates_menu_insert_template($items, $args) {
		if(!empty($GLOBALS['thegem_menu_template'])) {
			$template_id = $GLOBALS['thegem_menu_template'];
			unset($GLOBALS['thegem_menu_template']);
			$items .= '<li class="menu-item menu-item-type-template" style="display: none">';
			$template_id = intval($template_id);
			if($template_id > 0 && $template = get_post($template_id) && !isset( $_GET['elementor-preview'] )) {
				$template_content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );
				$items .= '<div class="thegem-template-wrapper thegem-template-content thegem-template-' . esc_attr($template_id) . '">' . $template_content . '</div>';
			}
			$items .= '</li>';
		}
		return $items;
	}

    public function thegem_templates_menu_first_level_link_class( $classes, $item, $args ) {
        if(isset($args->first_level_link_class) && $item->menu_item_parent == 0) {
            $classes['class'] = $args->first_level_link_class;
        }
        return $classes;
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
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'source_header',
			[
				'label' => __('Menu Source', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'menu_source',
			[
				'label' => __('Menu Source', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_menu_list(),
				'description' => __('Go to the <a href="' . get_site_url() . '/wp-admin/nav-menus.php" target="_blank">Menus screen</a> to manage your menus.', 'thegem'),
			]
		);

		$this->add_control(
			'different_source_mobile',
			[
				'label' => __('Different Source for Mobile', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'menu_source_mobile',
			[
				'label' => __('Mobile Menu Source', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_menu_list(),
				'condition' => [
					'different_source_mobile' => '1',
				],
			]
		);

		$this->add_control(
			'layout_desktop_header',
			[
				'label' => __('Desktop Menu Layout', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'menu_layout_desktop',
			[
				'label' => __('Layout Desktop', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Horizontal', 'thegem'),
					'hamburger' => __('Hamburger', 'thegem'),
					'overlay' => __('Overlay', 'thegem'),
					'split' => __('Logo Split', 'thegem'),
//					'perspective' => __('Perspective', 'thegem'),
//					'vertical' => __('Vertical', 'thegem'),
				],
			]
		);

		$this->add_control(
			'split_logo',
			[
				'label' => __('Select Logo', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'desktop_logo_dark',
				'options' => [
					'desktop_logo_dark' => __('Desktop Logo (Dark)', 'thegem'),
					'desktop_logo_light' => __('Desktop Logo (Light)', 'thegem'),
					'mobile_logo_dark' => __('Mobile Logo (Dark)', 'thegem'),
					'mobile_logo_light' => __('Mobile Logo (Light)', 'thegem'),
				],
				'condition' => [
					'menu_layout_desktop' => 'split'
				],
			]
		);

		$this->add_control(
			'split_layout_type',
			[
				'label' => __('Split Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'full',
				'options' => [
					'full' => __('Fullwidth', 'thegem'),
					'standard' => __('Standard', 'thegem'),
				],
				'condition' => [
					'menu_layout_desktop' => 'split'
				],
			]
		);

		$this->add_control(
			'split_after_item',
			[
				'label' => __('Split after menu item', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 20,
				'step' => 1,
				'default' => 2,
				'condition' => [
					'menu_layout_desktop' => 'split'
				],
			]
		);

		$this->add_control(
			'split_logo_absolute',
			[
				'label' => __('Logo Center Absolute', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'menu_layout_desktop' => 'split',
					'split_layout_type' => ['full', 'standard'],
				],
			]
		);

		$this->add_responsive_control(
			'split_logo_margin',
			[
				'label' => __('Split Position Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'allowed_dimensions' => ['right', 'left'],
				'default' => [
					'left' => '0',
					'right' => '0',
					'isLinked' => false,
				],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .menu-item-type-split-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'menu_layout_desktop' => 'split',
					'split_layout_type' => 'standard',
					'split_logo_absolute!' => '1',
				],
			]
		);

		$this->add_responsive_control(
			'split_logo_margin_absolute',
			[
				'label' => __('Split Position Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'allowed_dimensions' => ['right', 'left'],
				'default' => [
					'left' => '150',
					'right' => '150',
					'isLinked' => false,
				],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .menu-item-type-split-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'menu_layout_desktop' => 'split',
					'split_layout_type' => 'standard',
					'split_logo_absolute' => '1',
				],
			]
		);

		$this->add_control(
			'hamburger_icon_size',
			[
				'label' => __('Hamburger Icon Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'default' => __('Default', 'thegem'),
					'small' => __('Small', 'thegem'),
				],
				'condition' => [
					'menu_layout_desktop' => ['hamburger', 'overlay'],
				],
			]
		);

		$this->add_control(
			'hamburger_overlay_menu_source',
			[
				'label' => __('Content for Offcanvas Area (Desktop)', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('As set in Menu Source above', 'thegem'),
					'template' => __('Global Section', 'thegem'),
				],
				'label_block' => true,
				'condition' => [
					'menu_layout_desktop' => ['hamburger', 'overlay'],
				],
				'description' => sprintf(__('You can use global section template to display in offcanvas area by selecting "Global Section". <a href="%s" target="_blank">Learn more</a>'), 'https://docs.codex-themes.com/category/191-section-template')
			]
		);

		$this->add_control(
			'hamburger_overlay_template',
			[
				'label' => __('Select Global Section', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => thegem_get_section_templates_list(),
				'condition' => [
					'menu_layout_desktop' => ['hamburger', 'overlay'],
					'hamburger_overlay_menu_source' => ['template'],
				],
				'thegem_template_link' => true,
				'thegem_template_type' => 'content'
			]
		);

		$this->add_control(
			'desktop_menu_stretch',
			[
				'label' => __('Stretch Menu on Desktop', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'menu_layout_desktop',
							'value' => 'default',
						],
						[
							'terms' => [
								[
									'name' => 'menu_layout_desktop',
									'value' => 'split',
								],
								[
									'name' => 'split_layout_type',
									'operator' => '!=',
									'value' => 'full-sliding',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'submenu_indicator',
			[
				'label' => __('Submenu Indicator', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'menu_layout_desktop!' => 'overlay'
				],
			]
		);

		$this->add_control(
			'submenu_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'submenu_indicator' => '1',
					'menu_layout_desktop!' => 'overlay'
				],
			]
		);

		$this->add_responsive_control(
			'submenu_icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
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
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu .nav-menu.submenu-icon > li.menu-item-has-children > a i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thegem-te-menu .nav-menu.submenu-icon > li.menu-item-has-children > a svg' => 'max-width: {{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'submenu_indicator' => '1',
					'menu_layout_desktop!' => 'overlay'
				],
			]
		);

		$this->add_responsive_control(
			'submenu_icon_margin',
			[
				'label' => __('Icon Left Margin', 'thegem'),
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
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu .nav-menu.submenu-icon > li.menu-item-has-children > a i,
					{{WRAPPER}} .thegem-te-menu .nav-menu.submenu-icon > li.menu-item-has-children > a svg' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'submenu_indicator' => '1',
					'menu_layout_desktop!' => 'overlay'
				],
			]
		);

		$this->add_control(
			'search_menu_widget',
			[
				'label' => __('Search In Menu', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'menu_layout_desktop' => ['hamburger', 'overlay'],
				],
			]
		);

		$this->add_control(
			'socials_menu_widget',
			[
				'label' => __('Socials In Menu', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'menu_layout_desktop' => 'hamburger',
				],
			]
		);

		$this->add_control(
			'layout_mobile_header',
			[
				'label' => __('Mobile Menu Layout', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'menu_layout_mobile',
			[
				'label' => __('Layout Mobile', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'overlay' => __('Overlay', 'thegem'),
					'slide-horizontal' => __('Slide Left', 'thegem'),
					'slide-vertical' => __('Slide Top', 'thegem'),
					'global-section' => __('Global Section', 'thegem'),
				],
				'description' => sprintf(__('You can use global section template to display your mobile navigation by selecting "Global Section". <a href="%s" target="_blank">Learn more</a>'), 'https://docs.codex-themes.com/category/714-mobile-navigation-builder')
			]
		);
		$this->add_control(
			'menu_mobile_global_section',
			[
				'label' => __('Select Global Section', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => thegem_get_section_templates_list(),
				'condition' => [
					'menu_layout_mobile' => ['global-section'],
				],
				'thegem_template_link' => true,
				'thegem_template_type' => 'mobile-menu'
			]
		);

		$this->add_control(
			'menu_layout_mobile_breakpoint',
			[
				'label' => __('Mobile Breakpoint', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1212,
				'step' => 1,
			]
		);

		$this->add_control(
			'mobile_menu_search',
			[
				'label' => __('Search In Mobile Menu', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->add_control(
			'mobile_menu_socials',
			[
				'label' => __('Socials In Mobile Menu', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->add_control(
			'layout_tablet_header',
			[
				'label' => __('Tablet Menu Layout', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'menu_layout_tablet_landscape',
			[
				'label' => __('Tablet (Landscape)', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('As set for desktop', 'thegem'),
					'mobile' => __('As set for mobiles', 'thegem'),
				],
			]
		);

		$this->add_control(
			'menu_layout_tablet_portrait',
			[
				'label' => __('Tablet (Portrait)', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'mobile',
				'options' => [
					'default' => __('As set for desktop', 'thegem'),
					'mobile' => __('As set for mobiles', 'thegem'),
				],
			]
		);

		$this->add_control(
			'menu_layout_tablet_breakpoint',
			[
				'label' => __('Tablet Breakpoint', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1212,
				'step' => 1,
			]
		);

		$this->add_control(
			'tablet_landscape_menu_stretch',
			[
				'label' => __('Stretch Menu on Tablet (Landscape)', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'menu_layout_desktop',
							'value' => 'default',
						],
						[
							'terms' => [
								[
									'name' => 'menu_layout_desktop',
									'value' => 'split',
								],
								[
									'name' => 'split_layout_type',
									'operator' => '!=',
									'value' => 'full-sliding',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'tablet_portrait_menu_stretch',
			[
				'label' => __('Stretch Menu on Tablet (Portrait)', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'menu_layout_desktop',
							'value' => 'default',
						],
						[
							'terms' => [
								[
									'name' => 'menu_layout_desktop',
									'value' => 'split',
								],
								[
									'name' => 'split_layout_type',
									'operator' => '!=',
									'value' => 'full-sliding',
								],
							],
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_spacing',
			[
				'label' => __('Dropdown Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
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
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view .dl-menu,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view > .dl-submenu' => 'top: calc(100% + {{SIZE}}{{UNIT}});',
					'{{WRAPPER}} .thegem-te-menu__default.desktop-view ul.nav-menu > li.menu-item-has-children,
					{{WRAPPER}} .thegem-te-menu__default.desktop-view ul.nav-menu > li.megamenu-template-enable' => 'margin-bottom: -{{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'menu_layout_desktop' => ['default', 'split']
				],
			]
		);

		$this->add_control(
			'styles_header',
			[
				'label' => __('Style Presets', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'thegem_elementor_preset',
			[
				'label' => __('Color Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'inherit',
				'options' => [
					'inherit' => __('Inherit Theme Options', 'thegem'),
					'light' => __('Light', 'thegem'),
					'dark' => __('Dark', 'thegem'),
				],
				'description' => __('Submenu & responsive menu color presets', 'thegem'),
			]
		);

		$this->add_control(
			'menu_lvl_1_text_presets',
			[
				'label' => __('Main Menu Text Presets', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'menu_lvl_1_text_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'title-text-body' => __('Body', 'thegem'),
					'title-text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'menu_lvl_1_font_weight',
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
			'menu_lvl_1_letter_spacing',
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
			        '{{WRAPPER}} .thegem-te-menu nav.desktop-view ul.nav-menu > li > a' => 'letter-spacing: {{SIZE}}{{UNIT}};',
			        '{{WRAPPER}} .thegem-te-menu nav.desktop-view ul.nav-menu > li > a ~ span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'menu_lvl_1_text_transform',
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
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu nav.desktop-view ul.nav-menu > li > a' => '{{VALUE}};',
					'{{WRAPPER}} .thegem-te-menu nav.desktop-view ul.nav-menu > li > a ~ span' => '{{VALUE}};',
				],
				'description' => __('To style default main menu typography go to <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/menu-and-header/typography" target="_blank">Theme Options → Menu & Header → Typography</a>.', 'thegem'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'main_menu_style_section',
			[
				'label' => __('Main Menu', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$pointer_types = [
			[
				'label' => __('FRAME', 'thegem'),
				'options' => [
					'frame-default' => __('Frame', 'thegem'),
					'frame-rounded' => __('Rounded Frame', 'thegem'),
				],
			],
			[
				'label' => __('LINE', 'thegem'),
				'options' => [
					'line-underline-1' => __('Underline #1', 'thegem'),
					'line-underline-2' => __('Underline #2', 'thegem'),
					'line-overline-1' => __('Overline #1', 'thegem'),
					'line-overline-2' => __('Overline #2', 'thegem'),
					'line-top-bottom' => __('Top & Bottom', 'thegem'),
				],
			],
			[
				'label' => __('BACKGROUND', 'thegem'),
				'options' => [
					'background-underline' => __('Background & Underline', 'thegem'),
					'background-color' => __('Background Color', 'thegem'),
					'background-rounded' => __('Rounded Background', 'thegem'),
					'background-extra-paddings' => __('Extra Paddings Background', 'thegem'),
				],
			],
			[
				'label' => __('TEXT', 'thegem'),
				'options' => [
					'text-color' => __('Text Color', 'thegem'),
				],
			],
		];

		$this->add_control(
			'menu_pointer_style_hover',
			[
				'label' => __('Hover Pointer', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'groups' => $pointer_types,
				'default' => 'text-color',
				'frontend_available' => true,
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
				],
			]
		);

		$this->add_control(
			'animation_framed',
			[
				'label' => __('Hover Animation', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'draw' => 'Draw',
					'corners' => 'Corners',
					'none' => 'None',
				],
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
					'menu_pointer_style_hover' => ['frame-default', 'frame-rounded'],
				],
			]
		);

		$this->add_control(
			'animation_line',
			[
				'label' => __('Hover Animation', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'slide-left' => 'Slide Left',
					'slide-right' => 'Slide Right',
					'grow' => 'Grow',
					'drop-in' => 'Drop In',
					'drop-out' => 'Drop Out',
					'none' => 'None',
				],
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
					'menu_pointer_style_hover' => ['line-underline-1', 'line-underline-2', 'line-overline-1', 'line-overline-2', 'line-top-bottom'],
				],
			]
		);

		$this->add_control(
			'animation_background',
			[
				'label' => __('Hover Animation', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'sweep-left' => 'Sweep Left',
					'sweep-right' => 'Sweep Right',
					'sweep-up' => 'Sweep Up',
					'sweep-down' => 'Sweep Down',
					'none' => 'None',
				],
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
					'menu_pointer_style_hover' => ['background-underline', 'background-color', 'background-rounded', 'background-extra-paddings'],
				],
			]
		);

		$this->add_control(
			'menu_pointer_style_active',
			[
				'label' => __('Active Pointer', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'groups' => $pointer_types,
				'default' => 'frame-default',
				'frontend_available' => true,
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
				],
			]
		);

		$this->add_control(
			'hamburger_icon_color',
			[
				'label' => __('Hamburger Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view .menu-toggle .menu-line-1, 
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view .menu-toggle .menu-line-2, 
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view .menu-toggle .menu-line-3' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'menu_layout_desktop' => ['hamburger', 'overlay'],
				],
			]
		);

		$this->add_control(
			'close_icon_color',
			[
				'label' => __('Close Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view .overlay-toggle-close .menu-line-1, 
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view .overlay-toggle-close .menu-line-2, 
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view .overlay-toggle-close .menu-line-3,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view .hamburger-toggle-close .menu-line-1, 
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view .hamburger-toggle-close .menu-line-2, 
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view .hamburger-toggle-close .menu-line-3' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'menu_layout_desktop' => ['hamburger', 'overlay'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_color_hamburger',
				'label' => __('Background Type', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-menu__hamburger.desktop-view ul.nav-menu',
				'condition' => [
					'menu_layout_desktop' => 'hamburger',
				],
			]
		);

		$this->add_control(
			'background_color_overlay_hamburger',
			[
				'label' => __('Overlay Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu__hamburger.desktop-view .hamburger-menu-back' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_desktop' => 'hamburger',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_color_overlay',
				'label' => __('Background Type', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-menu__overlay.desktop-view .overlay-menu-back',
				'condition' => [
					'menu_layout_desktop' => 'overlay',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'menu_typography',
				'selector' => '{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li .menu-item-parent-toggle',
			]
		);

		$this->start_controls_tabs('tabs_menu_item_style', [
			'condition' => [
				'menu_layout_desktop!' => 'overlay',
			]
		]);

		$this->start_controls_tab(
			'tab_menu_item_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_control(
			'color_menu_item',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_menu_item',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(:hover):not(:focus):not(.highlighted):not(.menu-item-active):not(.menu-item-current) > a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_desktop!' => ['hamburger', 'overlay'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$this->add_control(
			'color_menu_item_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current):hover > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_menu_item_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu.style-hover-framed > nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current):hover > a:before,
					{{WRAPPER}} .thegem-te-menu.style-hover-lined > nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current):hover > a,
					{{WRAPPER}} .thegem-te-menu.style-hover-background > nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current):hover > a:before,
					{{WRAPPER}} .thegem-te-menu.style-hover-text > nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current):hover > a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_desktop!' => ['hamburger', 'overlay'],
				],
			]
		);

		$this->add_control(
			'pointer_color_menu_item_hover',
			[
				'label' => __('Pointer Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .style-hover-framed nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:before,
						{{WRAPPER}} .style-hover-framed nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .style-hover-lined nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:before,
						{{WRAPPER}} .style-hover-lined nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after,
						{{WRAPPER}} .style-hover-background.style-hover-type-background-underline nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
				],
			]
		);

		$this->add_responsive_control(
			'pointer_width_menu_item_hover',
			[
				'label' => __('Pointer Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .style-hover-framed nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:before,
						{{WRAPPER}} .style-hover-framed nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .style-hover-lined nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:before,
						{{WRAPPER}} .style-hover-lined nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after,
						{{WRAPPER}} .style-hover-background.style-hover-type-background-underline nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .style-hover-framed.style-hover-animation-corners nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:before,
						{{WRAPPER}} .style-hover-framed.style-hover-animation-corners nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after,
						{{WRAPPER}} .style-hover-framed.style-hover-animation-draw nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:before,
						{{WRAPPER}} .style-hover-framed.style-hover-animation-draw nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .style-hover-framed.style-hover-animation-corners nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:before' => 'border-width: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .style-hover-framed.style-hover-animation-corners nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after' => 'border-width: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
					'{{WRAPPER}} .style-hover-framed.style-hover-animation-draw nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:before' => 'border-width: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .style-hover-framed.style-hover-animation-draw nav.desktop-view ul.nav-menu > li:not(.menu-item-active):not(.menu-item-current) > a:after' => 'border-width: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;',
				],
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_active',
			[
				'label' => __('Active', 'thegem'),
			]
		);

		$this->add_control(
			'color_menu_item_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.menu-item-active > a:hover,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.menu-item-current > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_menu_item_active',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu.style-active-framed > nav.desktop-view ul.nav-menu > li.menu-item-active > a:before,
					{{WRAPPER}} .thegem-te-menu.style-active-framed > nav.desktop-view ul.nav-menu > li.menu-item-current > a:before,
					{{WRAPPER}} .thegem-te-menu.style-active-lined > nav.desktop-view ul.nav-menu > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu.style-active-lined > nav.desktop-view ul.nav-menu > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu.style-active-background > nav.desktop-view ul.nav-menu > li.menu-item-active > a:before,
					{{WRAPPER}} .thegem-te-menu.style-active-background > nav.desktop-view ul.nav-menu > li.menu-item-current > a:before,
					{{WRAPPER}} .thegem-te-menu.style-active-text > nav.desktop-view ul.nav-menu > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu.style-active-text > nav.desktop-view ul.nav-menu > li.menu-item-current > a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_desktop!' => ['hamburger', 'overlay'],
				],
			]
		);

		$this->add_control(
			'pointer_color_menu_item_active',
			[
				'label' => __('Pointer Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .style-active-framed nav.desktop-view ul.nav-menu > li.menu-item-active > a:before,
						{{WRAPPER}} .style-active-framed nav.desktop-view ul.nav-menu > li.menu-item-current > a:before,
						{{WRAPPER}} .style-active-framed nav.desktop-view ul.nav-menu > li.menu-item-active > a:after,
						{{WRAPPER}} .style-active-framed nav.desktop-view ul.nav-menu > li.menu-item-current > a:after' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .style-active-lined nav.desktop-view ul.nav-menu > li.menu-item-active > a:before,
						{{WRAPPER}} .style-active-lined nav.desktop-view ul.nav-menu > li.menu-item-current > a:before,
						{{WRAPPER}} .style-active-lined nav.desktop-view ul.nav-menu > li.menu-item-active > a:after,
						{{WRAPPER}} .style-active-lined nav.desktop-view ul.nav-menu > li.menu-item-current > a:after,
						{{WRAPPER}} .style-active-background.style-active-type-background-underline nav.desktop-view ul.nav-menu > li.menu-item-active > a:after,
						{{WRAPPER}} .style-active-background.style-active-type-background-underline nav.desktop-view ul.nav-menu > li.menu-item-current > a:after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
				],
			]
		);

		$this->add_responsive_control(
			'pointer_width_menu_item_active',
			[
				'label' => __('Pointer Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .style-active-framed nav.desktop-view ul.nav-menu > li.menu-item-active > a:before,
						{{WRAPPER}} .style-active-framed nav.desktop-view ul.nav-menu > li.menu-item-current > a:before,
						{{WRAPPER}} .style-active-framed nav.desktop-view ul.nav-menu > li.menu-item-active > a:after,
						{{WRAPPER}} .style-active-framed nav.desktop-view ul.nav-menu > li.menu-item-current > a:after' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .style-active-lined nav.desktop-view ul.nav-menu > li.menu-item-active > a:before,
						{{WRAPPER}} .style-active-lined nav.desktop-view ul.nav-menu > li.menu-item-current > a:before,
						{{WRAPPER}} .style-active-lined nav.desktop-view ul.nav-menu > li.menu-item-active > a:after,
						{{WRAPPER}} .style-active-lined nav.desktop-view ul.nav-menu > li.menu-item-current > a:after,
						{{WRAPPER}} .style-active-background.style-active-type-background-underline nav.desktop-view ul.nav-menu > li.menu-item-active > a:after,
						{{WRAPPER}} .style-active-background.style-active-type-background-underline nav.desktop-view ul.nav-menu > li.menu-item-current > a:after' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->start_controls_tabs('tabs_menu_item_overlay_style',
			[
				'condition' => [
					'menu_layout_desktop' => 'overlay',
				],
			]);

		$this->start_controls_tab(
			'tab_menu_item_overlay_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_control(
			'color_menu_item_overlay',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_overlay_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$this->add_control(
			'color_menu_item_overlay_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li.menu-item:hover > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li.menu-item:hover > .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_overlay_active',
			[
				'label' => __('Active', 'thegem'),
			]
		);

		$this->add_control(
			'color_menu_item_overlay_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li.menu-item-active > span > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li.menu-item-current > span > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu__overlay.desktop-view ul.nav-menu li.menu-item-current > .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'menu_item_space_between',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li' => 'margin: 0 calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu' => 'margin: 0 calc(-{{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'menu_layout_desktop!' => ['hamburger', 'overlay'],
				],
			]
		);

		$this->add_responsive_control(
			'menu_item_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'menu_item_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > a:before,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > a:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'menu_layout_desktop' => ['default', 'split', 'hamburger'],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'submenu_style_section',
			[
				'label' => __('Submenu', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'menu_layout_desktop!' => 'overlay',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'submenu_typography',
				'selector' => '{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li a',
			]
		);

		$this->start_controls_tabs('tabs_submenu_item_style');

		$this->start_controls_tab(
			'tab_submenu_item_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_control(
			'text_color_level2',
			[
				'label' => __('Level 2 Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li > span > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li > a:before,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li > span > a:before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'text_color_level3',
			[
				'label' => __('Level 3+ Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul > li a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_level2',
			[
				'label' => __('Level 2 Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li > span > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_level3',
			[
				'label' => __('Level 3+ Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul > li a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_submenu_item_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$this->add_control(
			'text_color_level2_hover',
			[
				'label' => __('Level 2 Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li:hover > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li:hover > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li:hover > span > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li:hover > a:before,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li:hover > span > a:before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'text_color_level3_hover',
			[
				'label' => __('Level 3+ Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul li:hover > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_level2_hover',
			[
				'label' => __('Level 2 Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li:hover > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li:hover > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li:hover > span > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_level3_hover',
			[
				'label' => __('Level 3+ Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul li:hover > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'submenu_hover_pointer_color',
			[
				'label' => __('Submenu Pointer', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.menu-item-type-template) li:hover > a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_submenu_item_active',
			[
				'label' => __('Active', 'thegem'),
			]
		);

		$this->add_control(
			'text_color_level2_active',
			[
				'label' => __('Level 2 Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-active > span > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-active > a:before,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-active > span > a:before,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-current > span > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-current > a:before,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-current > span > a:before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'text_color_level3_active',
			[
				'label' => __('Level 3+ Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul li.menu-item-current > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_level2_active',
			[
				'label' => __('Level 2 Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-active > span > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li.menu-item-current > span > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_level3_active',
			[
				'label' => __('Level 3+ Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul li.menu-item-current > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'submenu_active_pointer_color',
			[
				'label' => __('Submenu Pointer', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.menu-item-type-template) li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.menu-item-type-template) li.menu-item-current > a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'submenu_border',
			[
				'label' => __('Border', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'submenu_level2_border_color',
			[
				'label' => __('Level 2 Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li > ul > li,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul > li span.megamenu-column-header,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul li' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'submenu_border' => '1'
				],
			]
		);

		$this->add_control(
			'submenu_level3_border_color',
			[
				'label' => __('Level 3+ Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul ul,
					{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li > ul li' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'submenu_border' => '1'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'submenu_box_shadow',
				'label' => __('Box Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable):not(.megamenu-template-enable):not(.menu-item-type-template) ul,
				{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li.megamenu-enable > ul',
			]
		);

		$this->add_responsive_control(
			'submenu_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.desktop-view ul.nav-menu > li:not(.megamenu-enable) > ul > li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'mobile_menu_style_section',
			[
				'label' => __('Mobile Menu', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'menu_layout_mobile!' => 'overlay',
				],
			]
		);

		$this->add_control(
			'hamburger_icon_color_mobile',
			[
				'label' => __('Hamburger Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.mobile-view .menu-toggle .menu-line-1, 
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view .menu-toggle .menu-line-2, 
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view .menu-toggle .menu-line-3' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_icon_color_mobile',
			[
				'label' => __('Close Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.mobile-view .mobile-menu-slide-close:before, 
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view .mobile-menu-slide-close:after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical', 'global-section'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'mobile_menu_typography',
				'selector' => '{{WRAPPER}} .thegem-te-menu > nav.mobile-view ul.nav-menu > li a',
				'condition' => [
					'menu_layout_mobile!' => 'global-section',
				],
			],
		);

		$this->add_control(
			'mobile_menu_social_icon_color',
			[
				'label' => __('Social Icons Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view li.menu-item-type-socials-widget > .menu-item-socials .socials-item, 
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view li.menu-item-type-socials-widget > .menu-item-socials .socials-item' => 'color: {{VALUE}};',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
					'mobile_menu_socials' => '1',
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_1',
			[
				'label' => __('1st Level', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_1_border',
			[
				'label' => __('Border', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'selectors_dictionary' => [
					'1' => '',
					'' => 'border: none !important;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.mobile-view ul.nav-menu > li,
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch' => '{{VALUE}};',
				],
				'condition' => [
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_1_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu ul.nav-menu > li,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch .sf-input' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'mobile_menu_lvl_1_border' => '1',
					'menu_layout_mobile' => 'default',
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_1_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu ul.nav-menu > li,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu ul.nav-menu > li,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu ul.nav-menu > li > a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'mobile_menu_lvl_1_border' => '1',
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'mobile_menu_lvl_1_box_shadow',
				'label' => __('Box Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-menu > nav.mobile-view ul.nav-menu',
				'condition' => [
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->start_controls_tabs('tabs_mobile_menu_lvl_1', [
			'condition' => [
				'menu_layout_mobile!' => 'global-section',
			],
		]);

		$this->start_controls_tab(
			'tab_mobile_menu_lvl_1_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_control(
			'mobile_menu_lvl_1_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch .sf-input,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch .sf-input::placeholder,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch .sf-submit-icon:before' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_1_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view li.menu-item-type-search-widget > .minisearch .sf-input,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view li.menu-item-type-search-widget > .minisearch .sf-input::placeholder,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view li.menu-item-type-search-widget > .minisearch .sf-input,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view li.menu-item-type-search-widget > .minisearch .sf-input::placeholder' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_1_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view li.menu-item-type-search-widget > .minisearch' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_1_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view .mobile-menu-slide-wrapper, 
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view .mobile-menu-slide-wrapper' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile_menu_lvl_1_active',
			[
				'label' => __('Active', 'thegem'),
			]
		);

		$this->add_control(
			'mobile_menu_lvl_1_color_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-current > .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_1_color_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li.menu-item-current > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li.menu-item-current > .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_1_background_color_active',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li.menu-item-current > a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_1_background_color_active',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view > li.menu-item-current > a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'mobile_menu_lvl_2',
			[
				'label' => __('2nd Level', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_2_border',
			[
				'label' => __('Border', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'selectors_dictionary' => [
					'1' => '',
					'' => 'border: none !important;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.mobile-view ul.nav-menu > li > ul > li,
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view > ul.sub-menu.level3 > li a' => '{{VALUE}};',
				],
				'condition' => [
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_2_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view > ul.sub-menu.level3 > li a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'mobile_menu_lvl_2_border' => '1',
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_2_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view > ul.sub-menu.level3 > li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view > ul.sub-menu.level3 > li a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'mobile_menu_lvl_2_border' => '1',
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->start_controls_tabs('tabs_mobile_menu_lvl_2', [
			'condition' => [
				'menu_layout_mobile!' => 'global-section',
			],
		]);

		$this->start_controls_tab(
			'tab_mobile_menu_lvl_2_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_control(
			'mobile_menu_lvl_2_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > span > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view > ul.sub-menu.level3 > li a' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_2_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > span > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view > ul.sub-menu.level3 > li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > span > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view > ul.sub-menu.level3 > li a' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_2_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > span > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view > ul.sub-menu.level3 > li a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_2_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > span > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view > ul.sub-menu.level3 > li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view li.menu-item-type-search-widget > .minisearch .sf-input,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > span > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view > ul.sub-menu.level3 > li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view li.menu-item-type-search-widget > .minisearch .sf-input' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile_menu_lvl_2_active',
			[
				'label' => __('Active', 'thegem'),
			]
		);

		$this->add_control(
			'mobile_menu_lvl_2_color_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li.menu-item-current > .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_2_color_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li.menu-item-current > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li.menu-item-current > .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_2_background_color_active',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li.menu-item-current > a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_2_background_color_active',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li.menu-item-current > a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'mobile_menu_lvl_3',
			[
				'label' => __('3+ Level', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_3_border',
			[
				'label' => __('Border', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'selectors_dictionary' => [
					'1' => '',
					'' => 'border: none !important;',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.mobile-view ul.nav-menu > li > ul > li > ul li,
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view > ul.sub-menu.level4 > li a' => '{{VALUE}};',
				],
				'condition' => [
					'menu_layout_mobile!' => 'global-section',
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_3_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view > ul.sub-menu.level4 > li a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'mobile_menu_lvl_3_border' => '1',
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_3_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view > ul.sub-menu.level4 > li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view > ul.sub-menu.level4 > li a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'mobile_menu_lvl_3_border' => '1',
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->start_controls_tabs('tabs_mobile_menu_lvl_3', [
			'condition' => [
				'menu_layout_mobile!' => 'global-section',
			],
		]);

		$this->start_controls_tab(
			'tab_mobile_menu_lvl_3_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_control(
			'mobile_menu_lvl_3_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view > ul.sub-menu.level4 > li a' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_3_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view > ul.sub-menu.level4 > li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view > ul.sub-menu.level4 > li a' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_3_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view > ul.sub-menu.level4 > li a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_3_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view > ul.sub-menu.level4 > li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view > ul.sub-menu.level4 > li a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile_menu_lvl_3_active',
			[
				'label' => __('Active', 'thegem'),
			]
		);

		$this->add_control(
			'mobile_menu_lvl_3_color_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-current > .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_3_color_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-current > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-active > .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-current > .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->add_control(
			'mobile_menu_lvl_3_background_color_active',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__default.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-current > a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => 'default',
				],
			]
		);

		$this->add_control(
			'mobile_menu_slide_lvl_3_background_color_active',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-horizontal.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu-mobile__slide-vertical.mobile-view ul.nav-menu > li > ul > li > ul li.menu-item-current > a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['slide-horizontal', 'slide-vertical'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'mobile_menu_global_section_background_color',
			[
				'label' => __('Mobile Nav Area Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__global-section.mobile-view .mobile-menu-slide-wrapper.mobile-menu-slide-global-section' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'menu_layout_mobile' => ['global-section'],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'mobile_menu_overlay_style_section',
			[
				'label' => __('Mobile Menu', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'menu_layout_mobile' => 'overlay',
				],
			]
		);

		$this->add_control(
			'hamburger_icon_color_mobile_overlay',
			[
				'label' => __('Hamburger Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.mobile-view .menu-toggle .menu-line-1, 
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view .menu-toggle .menu-line-2, 
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view .menu-toggle .menu-line-3' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_icon_color_mobile_overlay',
			[
				'label' => __('Close Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.mobile-view .overlay-toggle-close .menu-line-1, 
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view .overlay-toggle-close .menu-line-2, 
					{{WRAPPER}} .thegem-te-menu > nav.mobile-view .overlay-toggle-close .menu-line-3' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_menu_overlay_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu-mobile__overlay.mobile-view .overlay-menu-back' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'mobile_menu_overlay_typography',
				'selector' => '{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li,
				{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li a,
				{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li .menu-item-parent-toggle',
			]
		);

		$this->start_controls_tabs('tabs_mobile_menu_overlay');

		$this->start_controls_tab(
			'tab_mobile_menu_overlay_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$this->add_control(
			'mobile_menu_overlay_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch .sf-input,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch .sf-input::placeholder,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch .sf-submit-icon:before' => 'color: {{VALUE}}',
					'{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu > li.menu-item-type-search-widget > .minisearch .sf-input' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile_menu_overlay_active',
			[
				'label' => __('Active', 'thegem'),
			]
		);

		$this->add_control(
			'mobile_menu_overlay_color_active',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li.menu-item-active > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li.menu-item-active > span > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li.menu-item-active .menu-item-parent-toggle,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li.menu-item-current > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li.menu-item-current > span > a,
					{{WRAPPER}} .thegem-te-menu > nav.thegem-te-menu-mobile__overlay.mobile-view ul.nav-menu li.menu-item-current .menu-item-parent-toggle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'hamburger_canvas_section',
			[
				'label' => __('Off Canvas Content Area', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'menu_layout_desktop' => array('overlay', 'hamburger'),
				],
			]
		);

		$this->add_responsive_control (
			'hamburger_canvas_width',
			[
				'label' => __('Bar Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'devices' => [ 'desktop', 'tablet' ],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu__hamburger.desktop-view ul.nav-menu' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .thegem-te-menu__hamburger.desktop-view:not(.hamburger-active) ul.nav-menu' => 'transform: translateX({{SIZE}}{{UNIT}})',
					'{{WRAPPER}} .thegem-te-menu__hamburger.desktop-view.hamburger-active .hamburger-toggle-close' => 'transform: translateX(-{{SIZE}}{{UNIT}})',
				],
				'condition' => [
					'menu_layout_desktop' => ['hamburger'],
				],
			]
		);

		$this->add_responsive_control(
			'hamburger_canvas_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-menu__hamburger.desktop-view ul.nav-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .thegem-te-menu__overlay.desktop-view ul.nav-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'devices' => [ 'desktop', 'tablet' ],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ($settings['menu_layout_desktop'] == 'overlay') {
			wp_enqueue_style('thegem-te-menu-overlay');
		} else if ($settings['menu_layout_desktop'] == 'hamburger') {
			wp_enqueue_style('thegem-te-menu-hamburger');
		} else {
			wp_enqueue_style('thegem-te-menu-default');
		}

		if ($settings['menu_layout_mobile'] == 'overlay') {
			wp_enqueue_style('thegem-te-menu-overlay');
		} else if ($settings['menu_layout_mobile'] == 'slide-horizontal' || $settings['menu_layout_mobile'] == 'slide-vertical' || $settings['menu_layout_mobile'] == 'global-section') {
			wp_enqueue_style('thegem-te-menu-mobile-sliding');
		} else {
			wp_enqueue_style('thegem-te-menu-mobile-default');
			wp_enqueue_script('jquery-dlmenu');
			wp_localize_script('jquery-dlmenu', 'thegem_dlmenu_settings', array(
				'backLabel' => esc_html__('Back', 'thegem'),
				'showCurrentLabel' => esc_html__('Show this page', 'thegem')
			));
		}

		if (!empty($settings['menu_source']) && is_nav_menu($settings['menu_source']) && !empty(wp_get_nav_menu_items($settings['menu_source']))):

			$uniqid = $this->get_id();

			$settings = array_merge($settings, [
				'menu_class' => '',
				'is_desktop_default' => ($settings['menu_layout_desktop'] === 'default' || $settings['menu_layout_desktop'] == 'split'),
				'is_overlay' => $settings['menu_layout_desktop'] === 'overlay' || $settings['menu_layout_mobile'] == 'overlay',
				'is_hamburger' => $settings['menu_layout_desktop'] === 'hamburger',
				'is_mobile_default' => $settings['menu_layout_mobile'] == 'default',
				'is_mobile_sliding' => $settings['menu_layout_mobile'] == 'slide-horizontal' || $settings['menu_layout_mobile'] == 'slide-vertical' || $settings['menu_layout_mobile'] == 'global-section',
			]);

			if ($settings['is_mobile_default']) {
				$settings['menu_class'] .= 'dl-menu';
			}

			if (isset($settings['submenu_indicator']) && $settings['submenu_indicator'] == '1') {
				$settings['menu_class'] .= ' submenu-icon';
			}

			if (isset($settings['submenu_border']) && $settings['submenu_border'] !== '1') {
				$settings['menu_class'] .= ' submenu-hide-border';
			}

			// Menu type
			if ($settings['menu_layout_desktop'] == 'split') {
				$menu_type = 'default';
			} else {
				$menu_type = $settings['menu_layout_desktop'];
			}
			$menu_mobile_type = $settings['menu_layout_mobile'];

			//Search Widget Init
			$menu_widget = $menu_mobile_widget = '';
			if ((($settings['menu_layout_desktop'] == 'hamburger' || $settings['menu_layout_desktop'] == 'overlay') && $settings['search_menu_widget']) || $settings['mobile_menu_search']) {
				add_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_search_widget'), 10, 2);

				if ($settings['search_menu_widget']) {
					$menu_widget .= ' show-desktop-search';
				}

				if ($settings['mobile_menu_search']) {
					$menu_mobile_widget .= ' show-mobile-search';
				}
			} else {
				remove_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_search_widget'), 10, 2);
			}

			// Hamburger Socials Widget Init
			if ($settings['menu_layout_desktop'] == 'hamburger' && $settings['socials_menu_widget']) {
				$menu_widget .= ' show-desktop-socials';
				add_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_socials_widget'), 10, 2);
			} else {
				remove_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_socials_widget'), 10, 2);
			}

			//Mobile Menu Socials Widget Init
			if ($settings['is_mobile_sliding'] && $settings['mobile_menu_socials']) {
				$menu_mobile_widget .= ' show-mobile-socials';
				add_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_mobile_socials_widget'), 10, 2);
			} else {
				remove_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_mobile_socials_widget'), 10, 2);
			}

			//Split logo init
			if ($settings['menu_layout_desktop'] == 'split') {
				$settings['menu_class'] .= ' nav-menu--split';
				$output_logo = $output_logo_style = '';
				$echo = false;

				if ($settings['split_layout_type'] == 'full') {
					$settings['menu_class'] .= ' fullwidth-logo';
				}

				if ($settings['split_logo_absolute']) {
					$settings['menu_class'] .= ' absolute';
					$output_logo_style = 'opacity: 0';
				}

				$is_light = false;
				if (get_the_ID() !== intval(thegem_get_option('header_builder_sticky')) && !empty($GLOBALS['thegem_custom_header_light'])) {
					$is_light = true;
				}
				if (!$is_light && isset($settings['split_logo']) && $settings['split_logo'] == 'desktop_logo_dark') {
					$output_logo = thegem_get_logo_img(esc_url(thegem_get_option('logo')), intval(thegem_get_option('logo_width')), 'tgp-exclude default', $echo);
				}
				if ((!$is_light && isset($settings['split_logo']) && $settings['split_logo'] == 'desktop_logo_light') || ($settings['split_logo'] == 'desktop_logo_dark' && $is_light)) {
					$output_logo = thegem_get_logo_img(esc_url(thegem_get_option('logo_light')), intval(thegem_get_option('logo_width')), 'tgp-exclude default light', $echo);
				}
				if (!$is_light && isset($settings['split_logo']) && $settings['split_logo'] == 'mobile_logo_dark') {
					$output_logo = thegem_get_logo_img(esc_url(thegem_get_option('small_logo')), intval(thegem_get_option('small_logo_width')), 'tgp-exclude small', $echo);
				}
				if ((!$is_light && isset($settings['split_logo']) && $settings['split_logo'] == 'mobile_logo_light') || ($settings['split_logo'] == 'mobile_logo_dark' && $is_light)) {
					$output_logo = thegem_get_logo_img(esc_url(thegem_get_option('small_logo_light')), intval(thegem_get_option('small_logo_width')), 'tgp-exclude small light', $echo);
				}

				add_filter('wp_nav_menu_items', array($this, 'thegem_add_menu_item_split_logo'), 10, 2);
			}

			$different_source = '';
			if ($settings['different_source_mobile']) {
				$different_source = 'different-source-mobile';
			}

			$custom_css = '';

			// Breakpoints
			$desktop_breakpoint = 1212;
			$tablet_breakpoint = 980;
			if (isset($settings['menu_layout_tablet_breakpoint']) && !empty($settings['menu_layout_tablet_breakpoint'])){
				$tablet_breakpoint = intval($settings['menu_layout_tablet_breakpoint']);
			}

			$mobile_breakpoint = 768;
			if (isset($settings['menu_layout_mobile_breakpoint']) && !empty($settings['menu_layout_mobile_breakpoint'])){
				$mobile_breakpoint = intval($settings['menu_layout_mobile_breakpoint']);
			}

			// Horizontal menu stretch
			if ($settings['is_desktop_default'] && ($settings['desktop_menu_stretch'] || $settings['tablet_landscape_menu_stretch'] || $settings['tablet_portrait_menu_stretch'] || $settings['split_layout_type'] == 'full')) {
				$settings['menu_class'] .= ' nav-menu--stretch';
				$custom_css .= '.elementor-element-' . $uniqid . ' .elementor-widget-container, .elementor-element-' . $uniqid . ' .thegem-te-menu {width: 100%;}';

				if ($settings['desktop_menu_stretch'] || ($settings['menu_layout_desktop'] == 'split' && $settings['split_layout_type'] == 'full')) {
					$custom_css .= '.elementor-element-' . $uniqid . ' {flex: auto !important;}';
				} else {
					$custom_css .= '.elementor-element-' . $uniqid . ' {flex: initial !important;}';
				}
				if (($settings['tablet_landscape_menu_stretch'] && $settings['menu_layout_tablet_landscape'] == 'default') || ($settings['menu_layout_desktop'] == 'split' && $settings['split_layout_type'] == 'full' && $settings['menu_layout_tablet_landscape'] == 'default')) {
					$custom_css .= '@media screen and (max-width: '.$desktop_breakpoint.'px)  {.elementor-element-' . $uniqid . ' {flex: auto !important; }}';
				} else {
					$custom_css .= '@media screen and (max-width: '.$desktop_breakpoint.'px)  {.elementor-element-' . $uniqid . ' {flex: initial !important; }}';
				}
				if (($settings['tablet_portrait_menu_stretch'] && $settings['menu_layout_tablet_portrait'] == 'default') || ($settings['menu_layout_desktop'] == 'split' && $settings['split_layout_type'] == 'full' && $settings['menu_layout_tablet_portrait'] == 'default')) {
					$custom_css .= '@media screen and (max-width: '.($tablet_breakpoint - 1).'px) {.elementor-element-' . $uniqid . ' {flex: auto !important; }}';
				} else {
					$custom_css .= '@media screen and (max-width: '.($tablet_breakpoint - 1).'px) {.elementor-element-' . $uniqid . ' {flex: initial !important; }}';
				}
				$custom_css .= '@media screen and (max-width: '.($mobile_breakpoint - 1).'px) {.elementor-element-' . $uniqid . ' {flex: initial !important; }}';
			}

			$this->add_render_attribute(
				'thegem-te-menu-wrap',
				[
					'class' => [
						$uniqid,
						'thegem-te-menu',
						'menu--'.$settings['thegem_elementor_preset'].'-submenu menu-mobile--'.$settings['thegem_elementor_preset'],
						in_array($settings['menu_pointer_style_hover'], ['frame-default', 'frame-rounded']) ? 'style-hover-framed' : '',
						in_array($settings['menu_pointer_style_hover'], ['line-underline-1', 'line-underline-2', 'line-overline-1', 'line-overline-2', 'line-top-bottom']) ? 'style-hover-lined' : '',
						in_array($settings['menu_pointer_style_hover'], ['background-underline', 'background-color', 'background-rounded', 'background-extra-paddings']) ? 'style-hover-background' : '',
						in_array($settings['menu_pointer_style_hover'], ['text-color']) ? 'style-hover-text' : '',
						'style-hover-type-'.$settings['menu_pointer_style_hover'],
						'style-hover-animation-'.$settings['animation_framed'].$settings['animation_line'].$settings['animation_background'],
						in_array($settings['menu_pointer_style_active'], ['frame-default', 'frame-rounded']) ? 'style-active-framed' : '',
						in_array($settings['menu_pointer_style_active'], ['line-underline-1', 'line-underline-2', 'line-overline-1', 'line-overline-2', 'line-top-bottom']) ? 'style-active-lined' : '',
						in_array($settings['menu_pointer_style_active'], ['background-underline', 'background-color', 'background-rounded', 'background-extra-paddings']) ? 'style-active-background' : '',
						in_array($settings['menu_pointer_style_active'], ['text-color']) ? 'style-active-text' : '',
						'style-active-type-'.$settings['menu_pointer_style_active'],
					]
				]);

			// Menu level 1 link preset classes
			$menu_level_1_link_classes = implode(' ', array($settings['menu_lvl_1_text_style'], $settings['menu_lvl_1_font_weight']));
			add_filter( 'nav_menu_link_attributes', array($this, 'thegem_templates_menu_first_level_link_class'), 10, 3);

            ?>

			<div <?php echo $this->get_render_attribute_string('thegem-te-menu-wrap'); ?>>
				<nav id="<?= esc_attr($uniqid) ?>"
					 class="desktop-view menu-class-id-<?= esc_attr($uniqid) ?> thegem-te-menu__<?php echo esc_attr($menu_type); ?> thegem-te-menu-mobile__<?php echo esc_attr($menu_mobile_type); ?><?php echo $menu_mobile_type == 'global-section' ? ' thegem-te-menu-mobile__slide-horizontal' : ''; ?> <?php echo esc_attr($menu_widget) ?> <?php echo esc_attr($menu_mobile_widget) ?> <?php echo esc_attr($different_source) ?>"
					 data-tablet-landscape="<?php echo esc_attr($settings['menu_layout_tablet_landscape']); ?>"
					 data-tablet-portrait="<?php echo esc_attr($settings['menu_layout_tablet_portrait']); ?>"
					 data-desktop-breakpoint="<?= $desktop_breakpoint ?>"
					 data-tablet-breakpoint="<?= $tablet_breakpoint ?>"
					 data-mobile-breakpoint="<?= $mobile_breakpoint ?>"
					 role="navigation">
					<script type="text/javascript">
						// <![CDATA[
						(function ($) {
							const tabletLandscapeMaxWidth = <?= $desktop_breakpoint ?>;
							const tabletLandscapeMinWidth = <?= $tablet_breakpoint ?>;
							const tabletPortraitMaxWidth = <?= $tablet_breakpoint - 1 ?>;
							const tabletPortraitMinWidth = <?= $mobile_breakpoint ?>;
							let viewportWidth = $(window).width();
							let menu = $('#<?=esc_attr($uniqid)?>');
							if (menu.data("tablet-landscape") === 'default' && viewportWidth >= tabletLandscapeMinWidth && viewportWidth <= tabletLandscapeMaxWidth) {
								menu.removeClass('mobile-view').addClass('desktop-view');
							} else if (menu.data("tablet-portrait") === 'default' && viewportWidth >= tabletPortraitMinWidth && viewportWidth <= tabletPortraitMaxWidth) {
								menu.removeClass('mobile-view').addClass('desktop-view');
							} else if (viewportWidth <= tabletLandscapeMaxWidth) {
								menu.removeClass('desktop-view').addClass('mobile-view');
							} else {
								menu.removeClass('mobile-view').addClass('desktop-view');
							}
							$('.mobile-menu-slide-wrapper', menu).css('transform', 'translateX(-1000%)');
						})(jQuery);
						// ]]>
					</script>
					<?php if ($settings['is_mobile_default'] || $settings['is_mobile_sliding']) { ?>
						<button class="menu-toggle dl-trigger">
							<?php esc_html_e('Menu', 'thegem'); ?>
							<span class="menu-line-1"></span>
							<span class="menu-line-2"></span>
							<span class="menu-line-3"></span>
						</button>
					<?php }
					if ($settings['is_hamburger']) { ?>
						<button class="menu-toggle hamburger-toggle <?php echo esc_attr($settings['hamburger_icon_size']); ?>">
							<?php esc_html_e('Menu', 'thegem'); ?>
							<span class="menu-line-1"></span>
							<span class="menu-line-2"></span>
							<span class="menu-line-3"></span>
						</button>
					<?php }
					if ($settings['is_overlay']) { ?>
						<button class="menu-toggle overlay-toggle <?php echo esc_attr($settings['hamburger_icon_size']); ?>">
							<?php esc_html_e('Menu', 'thegem'); ?>
							<span class="menu-line-1"></span>
							<span class="menu-line-2"></span>
							<span class="menu-line-3"></span>
						</button>
					<?php }
					if ($settings['is_hamburger']) { ?>
						<div class="hamburger-menu-back">
							<button class="hamburger-toggle-close <?php echo esc_attr($settings['hamburger_icon_size']); ?>">
								<?php esc_html_e('Close', 'thegem'); ?>
								<span class="menu-line-1"></span>
								<span class="menu-line-2"></span>
								<span class="menu-line-3"></span>
							</button>
						</div>
					<?php }
					if ($settings['is_overlay']) { ?>
					<div class="overlay-menu-back">
						<button class="overlay-toggle-close <?php echo esc_attr($settings['hamburger_icon_size']); ?>">
							<?php esc_html_e('Close', 'thegem'); ?>
							<span class="menu-line-1"></span>
							<span class="menu-line-2"></span>
							<span class="menu-line-3"></span>
						</button>
					</div>
					<div class="overlay-menu-wrapper">
						<div class="overlay-menu-table">
							<div class="overlay-menu-row">
								<div class="overlay-menu-cell">
									<?php }
									if ($settings['is_mobile_sliding']) { ?>
									<div class="mobile-menu-slide-back"></div>
									<div class="mobile-menu-slide-wrapper<?php echo $menu_mobile_type == 'global-section' ? ' mobile-menu-slide-global-section' : ''; ?>">
										<button class="mobile-menu-slide-close"><?php esc_html_e('Close', 'thegem'); ?></button>
										<?php
											$template_id = intval($settings['menu_mobile_global_section']);
											if($template_id > 0 && $template = get_post($template_id) && !isset($_GET['elementor-preview'])) {
												echo '<div class="mobile-menu-visible-content">';
												echo '<div class="thegem-template-wrapper thegem-template-menu-global-section thegem-template-' . $template_id . '">';
												$thegem_template_type_outer = isset($GLOBALS['thegem_template_type']) ? $GLOBALS['thegem_template_type'] : '';
												$GLOBALS['thegem_template_type'] = 'menu-global-section';
												$GLOBALS['thegem_mobile_menu_template'] = $template_id;
												echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($template_id);
												unset($GLOBALS['thegem_mobile_menu_template']);
												unset($GLOBALS['thegem_template_type']);
												if (!empty($thegem_template_type_outer)) {
													$GLOBALS['thegem_template_type'] = $thegem_template_type_outer;
												}
												echo '</div>';
												echo '</div>';
											}
											echo '<div class="mobile-menu-hidden-content">';
										}

										$link_after = '';
										if (isset($settings['submenu_indicator']) && $settings['submenu_indicator'] == '1') {
											if (isset($settings['submenu_icon']['value']) && $settings['submenu_icon']['value']) {
												ob_start();
												Icons_Manager::render_icon($settings['submenu_icon'], ['aria-hidden' => 'true', 'class' => 'hidden-icon']);
												$link_after = ob_get_clean();
											} else {
												$link_after = '<i class="default"></i>';
											}
										}

										if ($settings['different_source_mobile']) {
											wp_nav_menu(array(
												"menu" => $settings['menu_source_mobile'],
												"menu_class" => 'nav-menu mobile-menu-source ' . $settings['menu_class'] . ' styled',
												"container" => null,
												"echo" => true,
												'link_after' => $link_after,
												"walker" => new TheGem_Mega_Menu_Walker
											));
										}

										if($settings['menu_layout_desktop'] == 'hamburger' || $settings['menu_layout_desktop'] == 'overlay') {
											if($settings['hamburger_overlay_menu_source'] == 'template' && !empty($settings['hamburger_overlay_template'])) {
												$GLOBALS['thegem_menu_template'] = $settings['hamburger_overlay_template'];
												add_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_insert_template'), 10, 2);
												$settings['menu_class'] .= ' hamburger-with-template';
											}
										}

										wp_nav_menu(array(
											"menu" => $settings['menu_source'],
											"menu_class" => 'nav-menu ' . $settings['menu_class'] . ' styled',
											"container" => null,
											"echo" => true,
											'link_after' => $link_after,
											"first_level_link_class" => $menu_level_1_link_classes,
											"walker" => new TheGem_Mega_Menu_Walker
										));

										remove_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_insert_template'), 10, 2);
										remove_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_search_widget'), 10, 2);
										remove_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_socials_widget'), 10, 2);
										remove_filter('wp_nav_menu_items', array($this, 'thegem_templates_menu_mobile_socials_widget'), 10, 2);
										remove_filter('wp_nav_menu_items', array($this, 'thegem_add_menu_item_split_logo'), 10, 2);
										remove_filter('nav_menu_link_attributes', array($this, 'thegem_templates_menu_first_level_link_class'), 10, 3);

										if ($settings['is_mobile_sliding']) { 
											$template_id = intval($settings['menu_mobile_global_section']);
											if($template_id > 0 && $template = get_post($template_id) && !isset($_GET['elementor-preview'])) {
												echo '</div>';
											}
										?>
									</div>
								<?php }
								if ($settings['is_overlay']) { ?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				</nav>
			</div>
			<?php

			// Append split logo
			if ($settings['menu_layout_desktop'] == 'split' && !empty($output_logo)) { ?>
				<script>
					(function ($) {

						$('.elementor-element-<?php echo esc_attr($uniqid); ?>').closest('.elementor-widget-wrap').css('flex-wrap', 'nowrap !important');

						$('.elementor-element-<?php echo esc_attr($uniqid); ?> .menu-item-type-split-logo > .logo-fullwidth-block').html('<div class="site-logo" style="<?php echo esc_attr($output_logo_style); ?>"><a href="<?php echo esc_url(thegem_home_url('/'))?>" rel="home"><span class="logo"><?php echo $output_logo; ?></span></a></div>');

						$('.elementor-element-<?php echo esc_attr($uniqid); ?>').each(function () {
							$(this).find('.nav-menu:not(.mobile-menu-source) > .menu-item').each(function (index) {
								if ($(this).hasClass('menu-item-type-split-logo')) {
									$(this).css('order', <?php echo $settings['split_after_item']; ?>);
								} else {
									$(this).css('order', index + 1);
								}
							});
						});

					})(jQuery);

				</script>
				<?php if ($settings['split_logo_absolute']): ?>
					<script type="text/javascript">
						(function ($) {

							let $menu = $('.menu-class-id-<?=esc_attr($uniqid)?>');
							$menu.each(function () {
								let $widgetWrap = $(this).closest('.elementor-widget-wrap');
								let $fullwidth = $(this).find(".logo-fullwidth-block");
								let $logo = $('.site-logo', $fullwidth);

								$fullwidth.addClass('menu-id-<?=esc_attr($uniqid)?>');

								$widgetWrap.prepend($fullwidth);
								$logo.css('opacity', '1');
								if ($menu.hasClass('desktop-view')) {
									$fullwidth.addClass('desktop-view');
								} else {
									$fullwidth.addClass('mobile-view');
								}
							});

						})(jQuery);
					</script>
				<?php endif;

			}

			// Print custom css
			if (!empty($custom_css)) { ?>
				<style><?php echo esc_js($custom_css); ?></style>
			<?php }
			if (is_admin() && Plugin::$instance->editor->is_edit_mode()):

				if (!$settings['split_logo_absolute']) { ?>
					<script type="text/javascript">
						(function ($) {
							if ($('.menu-id-<?=esc_attr($uniqid)?>').length) {
								$('.menu-id-<?=esc_attr($uniqid)?>').remove();
							}
						})(jQuery);
					</script>
				<?php } ?>

				<script type="text/javascript">
					(function ($) {

						elementor.channels.editor.on('change', function (view) {
							var changed = view.elementSettingsModel.changed;

							if (changed.split_logo_margin !== undefined || changed.split_logo_margin_absolute !== undefined) {
								setTimeout(function () {
									fullwidth_block_update($fullwidth);
								}, 500);
							}
						});

						setTimeout(function () {
							$().initMenuScripts();
						}, 1000);
					})(jQuery);
				</script>
			<?php endif; ?>
		<?php else: ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo esc_html__('Please select Menu Source', 'thegem') ?>
			</div>
		<?php endif;

	}

	public function get_preset_data() {

		return array(
			'inherit' => array(),
			'light' => array(
				'text_color_level2' => '#5f727f',
				'background_color_level2' => '#f4f6f7',
				'text_color_level2_hover' => '#3c3950',
				'background_color_level2_hover' => '#ffffff',
				'text_color_level2_active' => '#3c3950',
				'background_color_level2_active' => '#ffffff',
				'text_color_level3' => '#5f727f',
				'background_color_level3' => '#ffffff',
				'text_color_level3_hover' => '#ffffff',
				'background_color_level3_hover' => '#494c64',
				'text_color_level3_active' => '#00bcd4',
				'background_color_level3_active' => '#ffffff',
				'submenu_level2_border_color' => '#dfe5e8',
				'submenu_level3_border_color' => '#dfe5e8',

				'background_color_overlay_background' => 'classic',
				'background_color_overlay_color' => '#ffffff',
				'color_menu_item_overlay' => '#212331',
				'color_menu_item_overlay_hover' => '#00bcd4',
				'color_menu_item_overlay_active' => '#00bcd4',
				'close_icon_color' => '#212331',

				'mobile_menu_lvl_1_color' => '#5f727f',
				'mobile_menu_lvl_1_background_color' => '#f4f6f7',
				'mobile_menu_lvl_1_color_active' => '#3c3950',
				'mobile_menu_lvl_1_background_color_active' => '#ffffff',
				'mobile_menu_lvl_2_color' => '#5f727f',
				'mobile_menu_lvl_2_background_color' => '#f4f6f7',
				'mobile_menu_lvl_2_color_active' => '#3c3950',
				'mobile_menu_lvl_2_background_color_active' => '#ffffff',
				'mobile_menu_lvl_3_color' => '#5f727f',
				'mobile_menu_lvl_3_background_color' => '#f4f6f7',
				'mobile_menu_lvl_3_color_active' => '#3c3950',
				'mobile_menu_lvl_3_background_color_active' => '#ffffff',
				'mobile_menu_lvl_1_border_color' => '#dfe5e8',
				'mobile_menu_lvl_2_border_color' => '#dfe5e8',
				'mobile_menu_lvl_3_border_color' => '#dfe5e8',

				'mobile_menu_slide_lvl_1_color' => '#5f727f',
				'mobile_menu_slide_lvl_1_background_color' => '#dfe5e8',
				'mobile_menu_slide_lvl_1_color_active' => '#3c3950',
				'mobile_menu_slide_lvl_1_background_color_active' => '#dfe5e8',
				'mobile_menu_slide_lvl_2_color' => '#5f727f',
				'mobile_menu_slide_lvl_2_background_color' => '#f0f3f2',
				'mobile_menu_slide_lvl_2_color_active' => '#3c3950',
				'mobile_menu_slide_lvl_2_background_color_active' => '#f0f3f2',
				'mobile_menu_slide_lvl_3_color' => '#5f727f',
				'mobile_menu_slide_lvl_3_background_color' => '#ffffff',
				'mobile_menu_slide_lvl_3_color_active' => '#ffffff',
				'mobile_menu_slide_lvl_3_background_color_active' => '#494c64',
				'mobile_menu_slide_lvl_1_border_color' => '#dfe5e8',
				'mobile_menu_slide_lvl_2_border_color' => '#dfe5e8',
				'mobile_menu_slide_lvl_3_border_color' => '#dfe5e8',
				'close_icon_color_mobile' => '#3c3950',
				'mobile_menu_social_icon_color' => '#99a9b5',

				'mobile_menu_overlay_background_color' => '#ffffff',
				'mobile_menu_overlay_color' => '#212331',
				'mobile_menu_overlay_color_active' => '#00bcd4',
				'close_icon_color_mobile_overlay' => '#00bcd4',
			),
			'dark' => array(
				'text_color_level2' => '#99a9b5',
				'background_color_level2' => '#393d50',
				'text_color_level2_hover' => '#ffffff',
				'background_color_level2_hover' => '#212331',
				'text_color_level2_active' => '#ffffff',
				'background_color_level2_active' => '#212331',
				'text_color_level3' => '#99a9b5',
				'background_color_level3' => '#212331',
				'text_color_level3_hover' => '#ffffff',
				'background_color_level3_hover' => '#131121',
				'text_color_level3_active' => '#00bcd4',
				'background_color_level3_active' => '#212331',
				'submenu_level2_border_color' => '#494c64',
				'submenu_level3_border_color' => '#494c64',

				'background_color_overlay_background' => 'classic',
				'background_color_overlay_color' => '#212331',
				'color_menu_item_overlay' => '#ffffff',
				'color_menu_item_overlay_hover' => '#00bcd4',
				'color_menu_item_overlay_active' => '#00bcd4',
				'close_icon_color' => '#ffffff',

				'mobile_menu_lvl_1_color' => '#99a9b5',
				'mobile_menu_lvl_1_background_color' => '#212331',
				'mobile_menu_lvl_1_color_active' => '#ffffff',
				'mobile_menu_lvl_1_background_color_active' => '#181828',
				'mobile_menu_lvl_2_color' => '#99a9b5',
				'mobile_menu_lvl_2_background_color' => '#212331',
				'mobile_menu_lvl_2_color_active' => '#ffffff',
				'mobile_menu_lvl_2_background_color_active' => '#181828',
				'mobile_menu_lvl_3_color' => '#99a9b5',
				'mobile_menu_lvl_3_background_color' => '#212331',
				'mobile_menu_lvl_3_color_active' => '#3c3950',
				'mobile_menu_lvl_3_background_color_active' => '#181828',
				'mobile_menu_lvl_1_border_color' => '#494c64',
				'mobile_menu_lvl_2_border_color' => '#494c64',
				'mobile_menu_lvl_3_border_color' => '#494c64',

				'mobile_menu_slide_lvl_1_color' => '#99a9b5',
				'mobile_menu_slide_lvl_1_background_color' => '#212331',
				'mobile_menu_slide_lvl_1_color_active' => '#ffffff',
				'mobile_menu_slide_lvl_1_background_color_active' => '#212331',
				'mobile_menu_slide_lvl_2_color' => '#99a9b5',
				'mobile_menu_slide_lvl_2_background_color' => '#393d4f',
				'mobile_menu_slide_lvl_2_color_active' => '#ffffff',
				'mobile_menu_slide_lvl_2_background_color_active' => '#393d4f',
				'mobile_menu_slide_lvl_3_color' => '#99a9b5',
				'mobile_menu_slide_lvl_3_background_color' => '#494c64',
				'mobile_menu_slide_lvl_3_color_active' => '#3c3950',
				'mobile_menu_slide_lvl_3_background_color_active' => '#00bcd4',
				'mobile_menu_slide_lvl_1_border_color' => '#494c64',
				'mobile_menu_slide_lvl_2_border_color' => '#494c64',
				'mobile_menu_slide_lvl_3_border_color' => '#494c64',
				'close_icon_color_mobile' => '#ffffff',
				'mobile_menu_social_icon_color' => '#99a9b5',

				'mobile_menu_overlay_background_color' => '#212331',
				'mobile_menu_overlay_color' => '#ffffff',
				'mobile_menu_overlay_color_active' => '#00bcd4',
				'close_icon_color_mobile_overlay' => '#00bcd4',
			),
		);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateMenu());
