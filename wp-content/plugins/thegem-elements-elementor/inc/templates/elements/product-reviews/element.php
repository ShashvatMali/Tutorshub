<?php

namespace TheGem_Elementor\Widgets\TemplateProductReviews;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Product Reviews.
 */

#[\AllowDynamicProperties]
class TheGem_TemplateProductReviews extends Widget_Base {
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
		return 'thegem-template-product-reviews';
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
		return __('Product Reviews', 'thegem');
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
		return 'thegem-te-product-reviews';
	}
	
	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}
		
		return ' .'.$this->get_widget_wrapper().' .product-reviews';
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
			'columns',
			[
				'label' => __('Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1x' => __('One Column', 'thegem'),
					'2x' => __('Two Columns', 'thegem'),
				],
				'default' => '2x',
			]
		);
		
		$this->add_control(
			'inner_title',
			[
				'label' => __('"Reviews" Title', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'selectors_dictionary' => [
					'' => 'display: none;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title' => '{{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'inner_title_add',
			[
				'label' => __('"Add a review" Title', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'selectors_dictionary' => [
					'' => 'display: none;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title' => '{{VALUE}};',
				],
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
					'title-text-body' => __('Body', 'thegem'),
					'title-text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => '',
			]
		);
		
		$this->add_control(
			'text_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'light' => __('Default', 'thegem'),
					'bold' => __('Bold', 'thegem'),
				],
				'default' => 'light',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'text_transform',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .woocommerce-Reviews-title span' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-reply-title span' => '{{VALUE}};',
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
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' =>
                    '{{WRAPPER}} .thegem-te-product-reviews .product-reviews .woocommerce-Reviews .woocommerce-Reviews-title,
				    {{WRAPPER}} .thegem-te-product-reviews .product-reviews .woocommerce-Reviews .woocommerce-Reviews-title span,
				    {{WRAPPER}} .thegem-te-product-reviews .product-reviews .woocommerce-Reviews .comment-reply-title,
				    {{WRAPPER}} .thegem-te-product-reviews .product-reviews .woocommerce-Reviews .comment-reply-title span'
        
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' =>
					'{{WRAPPER}} .thegem-te-product-reviews .product-reviews .woocommerce-Reviews .woocommerce-Reviews-title,
				    {{WRAPPER}} .thegem-te-product-reviews .product-reviews .woocommerce-Reviews .woocommerce-Reviews-title span,
				    {{WRAPPER}} .thegem-te-product-reviews .product-reviews .woocommerce-Reviews .comment-reply-title,
				    {{WRAPPER}} .thegem-te-product-reviews .product-reviews .woocommerce-Reviews .comment-reply-title span'
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
			'stars_base_color',
			[
				'label' => __('Stars Base Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .star-rating:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form-rating .stars a:before' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'stars_rated_color',
			[
				'label' => __('Stars Rated Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .star-rating > span:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .woocommerce-Reviews .comment-form-rating .stars a.rating-on:before' => 'color: {{VALUE}};',
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
  
		?>

        <div <?php if (!empty($params['element_id'])): ?>id="<?=esc_attr($params['element_id']); ?>"<?php endif;?>
             class="<?= $this->get_widget_wrapper() ?>">

            <div class="product-reviews reviews-column-<?= $params['columns'] ?>">
		        <?= comments_template() ?>
            </div>
        </div>

        <script>
            (function ($) {
                const $wrap = $('.thegem-te-product-reviews .woocommerce-Reviews');
                const $titles = $('.woocommerce-Reviews-title, .woocommerce-Reviews-title span, .comment-reply-title, .comment-reply-title span', $wrap);
                const titleStyledClasses =
                    `<?= $params['text_style'] ?>
                     <?= $params['text_weight'] ?>`;

                $titles.addClass(titleStyledClasses);
				
				<?php if ($params['text_weight'] == 'bold'): ?>
                $titles.removeClass('light');
				<?php endif; ?>
            })(jQuery);
        </script>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateProductReviews());
}