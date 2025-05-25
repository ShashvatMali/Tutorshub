<?php

namespace TheGem_Elementor\Widgets\TemplateLoopProductAddtoCart;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use TheGem_Elementor\Group_Control_Background_Light;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Product Price.
 */

#[\AllowDynamicProperties]
class TheGem_TemplateLoopProductAddtoCart extends Widget_Base {
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
		return 'thegem-template-loop-product-add-to-cart';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'loop-item';
	}
	
	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Product Add to Cart', 'thegem');
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
		return ['thegem_loop_builder'];
	}
	
	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}
	
	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-loop-product-add-to-cart';
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
			'add_to_cart_type',
			[
				'label' => __('Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'icon' => __('Icon', 'thegem'),
					'button' => __('Button', 'thegem'),
				],
				'default' => 'button',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'button_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
					'fullwidth' => [
						'title' => __('FullWidth', 'thegem'),
						'icon' => 'eicon-h-align-stretch',
					],
				],
				'default' => 'center',
				'frontend_available' => true,
				'condition' => [
					'add_to_cart_type' => 'button',
				],
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'center',
				'frontend_available' => true,
				'condition' => [
					'add_to_cart_type' => 'icon',
				],
			]
		);

		$this->add_control(
			'cart_button_show_icon', [
				'label' => __('Show Icon', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'cart_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Add To Cart', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'add_to_cart_type' => 'button',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'cart_icon',
			[
				'label' => __('Cart Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'select_options_button_text',
			[
				'label' => __('Select Options Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Select Options', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'add_to_cart_type' => 'button',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'select_options_icon',
			[
				'label' => __('Select Options Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'frontend_available' => true,
				'condition' => [
					'add_to_cart_type' => 'button',
				],
			]
		);

		$this->end_controls_section();

		$this->icon_style($this);

		$this->button_style($this);

	}

	/**
	 * Icon Style
	 * @access protected
	 */
	protected function icon_style($control) {

		$control->start_controls_section(
			'icon_style',
			[
				'label' => __('Icon Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'add_to_cart_type' => 'icon',
				],
			]
		);

		$control->add_responsive_control(
			'icon_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.icon a' => 'width: {{SIZE}}{{UNIT}}!important; height: {{SIZE}}{{UNIT}}!important; border-radius: calc({{SIZE}}{{UNIT}}/2)!important;',
					'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.icon a i, {{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.icon a:before' => 'font-size: calc({{SIZE}}{{UNIT}}/2) !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.icon a',
			]
		);
		$control->remove_control('icon_border_type_color');

		$control->start_controls_tabs('icon_style_tabs');

		$states_list = [
			'normal' => __('Normal', 'thegem'),
			'hover' => __('Hover', 'thegem'),
			'active' => __('Active', 'thegem'),
		];

		if (!empty($states_list)) {
			foreach ((array)$states_list as $stkey => $stelem) {
				$condition = [];
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('icon_tab_' . $stkey, [
					'label' => $stelem,
					'condition' => $condition
				]);

				$control->add_control(
					'icon_color_' . $stkey,
					[
						'label' => __('Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.icon a' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'icon_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.icon a' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'icon_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.icon a' . $state => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'icon_border_type_border!' => '',
						],
					]
				);

				$control->end_controls_tab();

			}
		}
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Button Style
	 * @access protected
	 */
	protected function button_style($control) {

		$control->start_controls_section(
			'button_style',
			[
				'label' => __('Button Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'add_to_cart_type' => 'button',
				],
			]
		);

		$control->add_control(
			'button_general_header',
			[
				'label' => __('General Settings', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$control->add_responsive_control(
			'button_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button .button' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button .button',
			]
		);
		$control->remove_control('button_border_type_color');

		$control->add_responsive_control(
			'button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$control->add_responsive_control(
			'button_icon_alignment',
			[
				'label' => __('Icon Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;'
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button .button' => '{{VALUE}}',
				],
				'condition' => [
					'cart_button_show_icon' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'button_icon_spacing',
			[
				'label' => __('Icon Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button .button .space' => 'width: calc({{SIZE}}{{UNIT}} - 5px)',
				],
				'condition' => [
					'cart_button_show_icon' => 'yes',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'button_text_typography',
				'selector' => '{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button .button',
			]
		);

		$control->add_control(
			'button_cart_header',
			[
				'label' => __('Add to Cart Button Colors', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('button_cart_tabs');

		$states_list = [
			'normal' => __('Normal', 'thegem'),
			'hover' => __('Hover', 'thegem'),
			'active' => __('Active', 'thegem'),
		];

		if (!empty($states_list)) {
			foreach ((array)$states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('button_cart_tab_' . $stkey, [
					'label' => $stelem,
				]);

				$control->add_control(
					'button_cart_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button.simple-type-button .button' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_cart_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button.simple-type-button .button' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_cart_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button.simple-type-button .button' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'button_cart_shadow_' . $stkey,
						'label' => __('Shadow', 'thegem'),
						'selector' => '{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button.simple-type-button .button' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'button_options_header',
			[
				'label' => __('Select Options Button Colors', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('button_options_tabs');

		if (!empty($states_list)) {
			foreach ((array)$states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					continue;
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('button_options_tab_' . $stkey, [
					'label' => $stelem,
				]);

				$control->add_control(
					'button_options_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button.variable-type-button .button' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_options_background_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button.variable-type-button .button' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'button_options_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button.variable-type-button .button' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'button_options_shadow_' . $stkey,
						'label' => __('Shadow', 'thegem'),
						'selector' => '{{WRAPPER}} .thegem-te-loop-product-add-to-cart .cart.type_button.variable-type-button .button' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();


		$control->end_controls_section();
	}

	public function thegem_get_add_to_cart_icon_text($params, $type = 'simple') {
		$icon = '';
		if ($params['add_to_cart_type'] == 'icon' || $params['cart_button_show_icon'] == 'yes' || $params['cart_button_show_icon'] == '1') {
			$pack = $type == 'variable' ? 'select_options_pack' : 'cart_button_pack';
			$type_icon = $type == 'variable' ? 'select_options_icon' : 'cart_icon';
			if (isset($params[$pack]) && $params[$type_icon . '_' . $params[$pack]] != '') {
				$icon = thegem_build_icon($params[$pack], $params[$type_icon . '_' . $params[$pack]]);
			} else if (!empty($params[$type_icon]['value'])) {
				ob_start();
				Icons_Manager::render_icon($params[$type_icon], ['aria-hidden' => 'true']);
				$icon = ob_get_clean();
			} else {
				$icon = '<i class="default"></i>';
			}
			if ($params['add_to_cart_type'] == 'button') {
				$icon .= '<span class="space"></span>';
			}
		}
		$text = esc_html($type == 'variable' ? $params['select_options_button_text'] : $params['cart_button_text']);
		if ($params['add_to_cart_type'] == 'button') {
			$text = '<span>' .$text . '</span>';
		}
		return $icon . $text;
	}

	public function thegem_get_add_to_cart_link($product, $params, $add_to_cart_args) {
		if (!isset($params['cart_hook']) || $params['cart_hook'] == '1' || $params['cart_hook'] == 'yes') {
			woocommerce_template_loop_add_to_cart($add_to_cart_args);
		} else {
			$defaults = array(
				'quantity'   => 1,
				'class'      => implode(
					' ',
					array_filter(
						array(
							'button',
							wc_wp_theme_get_element_class_name( 'button' ), // escaped in the template.
							'product_type_' . $product->get_type(),
							$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
							$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
						)
					)
				),
				'attributes' => array(
					'data-product_id'  => $product->get_id(),
					'data-product_sku' => $product->get_sku(),
					'aria-label'       => $product->add_to_cart_description(),
					'rel'              => 'nofollow',
				),
			);
			$args = wp_parse_args( $add_to_cart_args, $defaults );
			if (!empty($args['attributes']['aria-describedby'])) {
				$args['attributes']['aria-describedby'] = wp_strip_all_tags($args['attributes']['aria-describedby']);
			}
			if (isset($args['attributes']['aria-label'])) {
				$args['attributes']['aria-label'] = wp_strip_all_tags($args['attributes']['aria-label']);
			}
			echo sprintf(
				'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
				esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
				isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
				isset( $args['text'] ) ? $args['text'] : esc_html($product->add_to_cart_text())
			);
		}
	}

	public function thegem_get_add_to_cart($product, $add_to_cart_class, $params, $show_swatches) {
		$add_to_cart_args = [];
		$align = 'center';
		if($params['add_to_cart_type'] === 'icon' && isset($params['icon_alignment'])) {
			$align = $params['icon_alignment'];
		}
		if($params['add_to_cart_type'] === 'button' && isset($params['button_alignment'])) {
			$align = $params['button_alignment'];
		}
		$button_classes = implode(
			' ',
			array_filter(
				array(
					'cart',
					$params['add_to_cart_type'] == 'icon' ? 'icon' : 'type_button',
					$add_to_cart_class,
					'alignment-'.$align
				)
			)
		);

		if (in_array($product->get_type(), ['variable', 'external', 'grouped'])) {
			if ($product->get_type() === 'variable') {
				$add_to_cart_args['text'] = $this->thegem_get_add_to_cart_icon_text($params, 'variable');
			} ?>
			<span class="variable-type-button <?php echo esc_attr($button_classes); ?>">
				<?php $this->thegem_get_add_to_cart_link($product, $params, $add_to_cart_args); ?>
			</span>
			<?php if ($show_swatches) {
				$button_classes .= ' swatches-button';
			}
		}

		if ($product->get_type() === 'simple' || ($show_swatches && $product->get_type() === 'variable') || !in_array($product->get_type(), ['simple', 'variable', 'external', 'grouped'])) {
			$add_to_cart_args['text'] = $this->thegem_get_add_to_cart_icon_text($params); ?>
			<span class="simple-type-button <?php echo esc_attr($button_classes); ?>">
				<?php $this->thegem_get_add_to_cart_link($product, $params, $add_to_cart_args); ?>
			</span>
		<?php }
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

		// General params
		$params = array_merge(array(
			'add_to_cart_type' => 'buttons',
			'button_alignment' => 'center',
			'cart_button_show_icon' => 'yes',
			'cart_button_text' => __('Add To Cart', 'thegem'),
			'select_options_button_text' => __('Select Options', 'thegem'),
		), $settings);

		// Init Sku
		ob_start();
		$product = thegem_templates_init_product();
		if (empty($product)) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$notification_params = array(
			'stay_visible' => thegem_get_option('product_archive_stay_visible'),
			'added_cart_text' => thegem_get_option('product_archive_added_cart_text'),
			'view_cart_button_text' => thegem_get_option('product_archive_view_cart_button_text'),
			'checkout_button_text' => thegem_get_option('product_archive_checkout_button_text'),
			'added_wishlist_text' => thegem_get_option('product_archive_added_wishlist_text'),
			'view_wishlist_button_text' => thegem_get_option('product_archive_view_wishlist_button_text'),
			'removed_wishlist_text' => thegem_get_option('product_archive_removed_wishlist_text'),
			'mini_cart_type' => thegem_get_option('mini_cart_type'),
		);

		?>

		<div <?php if (!empty($params['element_id'])): ?>id="<?=esc_attr($params['element_id']); ?>"<?php endif;?> class="<?= $this->get_widget_wrapper() ?>">
			<?php if ($product->is_in_stock()) { $this->thegem_get_add_to_cart($product, '', $params, false); } ?>
		</div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateLoopProductAddtoCart());
}