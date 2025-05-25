<?php

namespace TheGem_Elementor\Widgets\TemplateProductNavigation;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Product Navigation.
 */

#[\AllowDynamicProperties]
class TheGem_TemplateProductNavigation extends Widget_Base {
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
		return 'thegem-template-product-navigation';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-product';
	}
	
	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Product Navigation', 'thegem');
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
		return ['thegem_single_product_builder'];
	}
	
	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}
	
	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-product-navigation';
	}
	
	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}
		
		return ' .'.$this->get_widget_wrapper().' .product-navigation';
	}
	
	public function get_taxonomy_list() {
		$data = array();
		$taxonomies = get_taxonomies(['object_type' => ['product']], 'objects');
		
		if (!empty($taxonomies)) {
			foreach ($taxonomies as $taxonomy) {
				$data[$taxonomy->name] = ucwords($taxonomy->label);
			}
		}
		
		return $data;
	}
	
	public function get_default_taxonomy() {
		$taxonomies = $this->get_taxonomy_list();
		if (empty($taxonomies)) return;
		
		return array_keys($taxonomies)[1];
	}
 
	/** Output product navigate preview */
	public function product_preview_output($id) {
		$product = wc_get_product($id);
		
		$preview_output = '<div class="product-navigation__preview">';
		$preview_output .= '<div class="product-navigation__preview-image">' . get_the_post_thumbnail($id, 'thegem-product-thumbnail') . '</div>';
		$preview_output .= '<div class="product-navigation__preview-info">';
		$preview_output .= '<div class="product-navigation__preview-info-title">' . mb_strimwidth(get_the_title($id), '0', '20', '...') . '</div>';
		$preview_output .= '<div class="product-navigation__preview-info-price">' . $product->get_price_html() . '</div>';
		$preview_output .= '</div></div>';
		
		return $preview_output;
	}
	
	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		
		// General Section
		$this->start_controls_section(
			'section_general',
			[
				'label' => __('General', 'thegem'),
			]
		);
		
		$this->add_control(
			'nav_elements',
			[
				'label' => __('Prev/Next Product', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'navigate_by',
			[
				'label' => __('Navigate by', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'chronology' => __('Chronology', 'thegem'),
					'taxonomy' => __('Same Taxonomy', 'thegem'),
				],
				'default' => 'chronology',
				'condition' => [
					'nav_elements' => '1',
				],
			]
		);
		
		$this->add_control(
			'taxonomy',
			[
				'label' => __('Select Taxonomy', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_taxonomy_list(),
				'default' => $this->get_default_taxonomy(),
				'condition' => [
					'nav_elements' => '1',
					'navigate_by' => 'taxonomy',
				],
			]
		);
		
		$this->add_control(
			'preview_on_hover',
			[
				'label' => __('Product Preview on Hover', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'nav_elements' => '1',
				],
			]
		);
  
		$this->add_control(
			'back_to_shop',
			[
				'label' => __('Back to Shop', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'back_to_shop_link',
			[
				'label' => __('Back to Shop Link', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'main_shop' => __('Main Shop', 'thegem'),
					'category' => __('Category', 'thegem'),
					'custom_url' => __('Custom Url', 'thegem'),
				],
				'default' => 'main_shop',
				'condition' => [
					'back_to_shop' => '1',
				],
			]
		);
		
		$this->add_control(
			'back_to_shop_link_custom',
			[
				'label' => __('Custom Url', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'back_to_shop_link' => ['custom_url'],
				],
			]
		);
		
		$this->add_responsive_control(
			'alignment',
			[
				'label' => __('Alignment', 'thegem'),
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
				'selectors_dictionary' => [
					'left' => 'justify-content: left; text-align: left;',
					'right' => 'justify-content: right; text-align: right;',
					'center' => 'justify-content: center; text-align: center;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => '{{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Style Section
		$this->start_controls_section(
			'Style',
			[
				'label' => __('Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_responsive_control(
			'separator_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class() => 'margin-bottom:{{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'elements_color',
			[
				'label' => __('Elements Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .product-navigation__list li a:before' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'elements_color_hover',
			[
				'label' => __('Elements Color Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .product-navigation__list li a:hover:before' => 'color: {{VALUE}};',
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
		
		// General params
		$params = array_merge(array(
		
		), $settings);
		
		// Init Sku
		ob_start();
		$product = thegem_templates_init_product();
		if (empty($product)) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}
  
		$isNavigate = $params['nav_elements'] || $params['back_to_shop'];
		$is_taxonomy = !empty($params['navigate_by']) && $params['navigate_by'] == 'taxonomy' ? true : false;
		$taxonomy = !empty($params['taxonomy']) ? $params['taxonomy'] : 'category';
		$back_to_shop_url = 'javascript:void(0);';
		switch ( $params['back_to_shop_link'] ) {
			case 'main_shop':
				$back_to_shop_url = get_permalink( wc_get_page_id( 'shop' ) );
				break;
			case 'category':
				$terms = get_the_terms( $product->get_id(), 'product_cat' );
				foreach ( $terms as $term ) {
					$product_cat_id   = $term->term_id;
					$back_to_shop_url = get_term_link( $product_cat_id, 'product_cat' );
					break;
				}
				break;
			case 'custom_url':
				$back_to_shop_url = esc_url( $params['back_to_shop_link_custom'] );
				break;
		}
		
		?>

        <div <?php if (!empty($params['element_id'])): ?>id="<?=esc_attr($params['element_id']); ?>"<?php endif;?>
             class="<?= $this->get_widget_wrapper() ?>">
	
	        <?php if ( $isNavigate ): ?>
                <div class="product-navigation">
                    <ul class="product-navigation__list">
				        <?php if (($post = get_previous_post($is_taxonomy, '', $taxonomy)) && $params['nav_elements']): ?>
                            <li>
                                <a class="product-navigation__list-prev" href="<?= get_permalink( $post->ID ) ?>">
							        <?php if ( $params['preview_on_hover'] ): ?>
								        <?= $this->product_preview_output($post->ID) ?>
							        <?php endif; ?>
                                </a>
                            </li>
				        <?php endif; wp_reset_postdata(); $product = thegem_templates_init_product(); ?>
				
				        <?php if ( $params['back_to_shop'] ): ?>
                            <li>
                                <a class="product-navigation__list-back" href="<?= $back_to_shop_url ?>"></a>
                            </li>
				        <?php endif; ?>
				
				        <?php if (( $post = get_next_post($is_taxonomy, '', $taxonomy)) && $params['nav_elements']): ?>
                            <li>
                                <a class="product-navigation__list-next" href="<?= get_permalink( $post->ID ) ?>">
							        <?php if ( $params['preview_on_hover'] ): ?>
								        <?= $this->product_preview_output($post->ID) ?>
							        <?php endif; ?>
                                </a>
                            </li>
				        <?php endif; wp_reset_postdata(); $product = thegem_templates_init_product(); ?>
                    </ul>
                </div>
	        <?php endif; ?>
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateProductNavigation());
}
