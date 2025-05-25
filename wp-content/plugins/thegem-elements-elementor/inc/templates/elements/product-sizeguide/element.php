<?php

namespace TheGem_Elementor\Widgets\TemplateProductSizeGuide;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Product SizeGuide.
 */

#[\AllowDynamicProperties]
class TheGem_TemplateProductSizeGuide extends Widget_Base {
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
		return 'thegem-template-product-size-guide';
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
		return __('Product Size Guide', 'thegem');
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
		return 'thegem-te-product-size-guide';
	}
	
	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}
		
		return ' .'.$this->get_widget_wrapper().' .product-size-guide';
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
			'source',
			[
				'label' => __('Source', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default from Theme Options', 'thegem'),
					'custom' => __('Custom Image', 'thegem'),
				],
				'default' => 'default',
			]
		);
		
		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'thegem' ),
				'type' => Controls_Manager::MEDIA,
				'frontend_available' => true,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'source' => 'custom',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'imagesize',
				'default' => 'full',
				'separator' => 'none',
				'condition' => [
					'source' => 'custom',
                    'image[url]!' => Utils::get_placeholder_image_src()
                ],
			]
		);
		
		$this->add_control(
			'title',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Size guide', 'thegem'),
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
					'{{WRAPPER}}'.$this->get_customize_class().' a' => '{{VALUE}};',
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
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' a',
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' a',
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
		
		if ($params['source'] == 'default') {
			$image_src = thegem_get_option('size_guide_image');
			$title = thegem_get_option('size_guide_text');
		} else {
			$image_src = ($params['image']['id']) ? Group_Control_Image_Size::get_attachment_image_src($params['image']['id'], 'imagesize', $params) : $params['image']['url'];
			$title = $params['title'];
        }
  
		if (empty($product) || empty($image_src)) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}
		
		?>

        <div <?php if (!empty($params['element_id'])): ?>id="<?=esc_attr($params['element_id']); ?>"<?php endif;?>
             class="<?= $this->get_widget_wrapper() ?>">

            <div class="product-size-guide">
                <a href="<?= esc_url($image_src); ?>" class="fancybox"><?= esc_html($title); ?></a>
            </div>
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
	
}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateProductSizeGuide());
}