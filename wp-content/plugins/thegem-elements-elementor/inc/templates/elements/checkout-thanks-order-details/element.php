<?php

namespace TheGem_Elementor\Widgets\TemplateCheckoutThanksOrderDetails;

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
class TheGem_TemplateCheckoutThanksOrderDetails extends Widget_Base {
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
		return 'thegem-template-checkout-thanks-order-details';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'checkout-thanks';
	}
	
	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Order Details', 'thegem');
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'checkout-thanks') {
			return ['thegem_checkout_thanks_builder'];
		}
	}
	
	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}
	
	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-checkout-thanks-order-details';
	}
	
	/** Get customize class */
	public function get_customize_class() {
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
			'section_heading',
			[
				'label' => __('Heading', 'thegem'),
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
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-order-details__title' => 'text-align: {{VALUE}};',
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
					'title-main-menu' => __('Main Menu', 'thegem'),
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
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-order-details__title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-order-details__title' => '{{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
  
		
		// Divider Section
		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Content', 'thegem'),
			]
		);
		
		$this->add_control(
			'dividers',
			[
				'label' => __('Dividers', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
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
		
		$this->add_responsive_control(
			'heading_spacing',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-order-details__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'heading_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .woocommerce-order-details__title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'heading' => '1',
				]
			]
		);
		
		$this->end_controls_section();
		
		// Style -> Content
		$this->start_controls_section(
			'content_section_styles',
			[
				'label' => __('Content', 'thegem'),
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
					'{{WRAPPER}} '.$this->get_customize_class().' table.woocommerce-table--order-details th' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} '.$this->get_customize_class().' table.woocommerce-table--order-details td' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'link_color',
			[
				'label' => __('Links Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' table.woocommerce-table--order-details td a' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'link_hover_color',
			[
				'label' => __('Links Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' table.woocommerce-table--order-details td a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'total_text_color',
			[
				'label' => __('Subtotal & Total Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' table.woocommerce-table--order-details td.product-total' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_control(
			'dividers_color',
			[
				'label' => __('Dividers Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' table.woocommerce-table--order-details th' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} '.$this->get_customize_class().' table.woocommerce-table--order-details td' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'dividers' => '1',
				]
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
		
		// General params
		$params = array_merge(array(
            'element_class' => $this->get_widget_wrapper()
        ), $settings);
		
		// Init Title
		ob_start();
		
		if (!is_checkout()) {
			ob_end_clean();
			echo thegem_templates_close_checkout_thanks(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}
		
		global $wp;
		$order_id = $wp->query_vars['order-received'];
		$order = wc_get_order( $order_id );
		
		$order_items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
		$show_purchase_note = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', array('completed', 'processing')));
		$downloads = $order->get_downloadable_items();
		$show_downloads = $order->has_downloadable_item() && $order->is_download_permitted();
		
		if ($show_downloads) {
			wc_get_template('order/order-downloads.php', array('downloads' => $downloads, 'show_title' => true));
		}
		
		$title_classes = implode(' ', array('woocommerce-order-details__title', $params['heading_text_style'], $params['heading_font_weight']));
		$title_tag = empty($params['heading_text_style']) ? 'h3' : 'div';
		
		$params['element_class'] .= empty($params['dividers']) ? ' hide-dividers' : null;
		
		?>

        <div class="<?= $params['element_class'] ?>">
            <div class="order-details-column">
		        <?php if (!empty($params['heading'])) : ?>
                    <<?= $title_tag; ?> class="<?= $title_classes; ?>"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></<?= $title_tag; ?>>
	            <?php endif; ?>

                <div class="gem-table">
                    <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
                        <thead>
                        <tr>
                            <th class="woocommerce-table__product-name product-name" colspan="2"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                            <th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                            <th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
                        </tr>
                        </thead>
    
                        <tbody>
                        <?php
                        do_action( 'woocommerce_order_details_before_order_table_items', $order );
                
                        foreach ( $order_items as $item_id => $item ) {
                            $product = $item->get_product();
                    
                            wc_get_template(
                                'order/order-details-item.php',
                                array(
                                    'order' => $order,
                                    'item_id' => $item_id,
                                    'item' => $item,
                                    'show_purchase_note' => $show_purchase_note,
                                    'purchase_note' => $product ? $product->get_purchase_note() : '',
                                    'product' => $product,
                                )
                            );
                        }
                
                        do_action( 'woocommerce_order_details_after_order_table_items', $order );
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_checkout_thanks(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCheckoutThanksOrderDetails());
}