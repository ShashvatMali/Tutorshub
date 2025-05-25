<?php

namespace TheGem_Elementor\Widgets\TemplateProductSharing;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Product Sharing.
 */

#[\AllowDynamicProperties]
class TheGem_TemplateProductSharing extends Widget_Base {
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
		return 'thegem-template-product-sharing';
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
		return __('Product Sharing', 'thegem');
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
		return 'thegem-te-product-sharing';
	}

	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}

		return ' .'.$this->get_widget_wrapper().' .product-sharing';
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
					'justify' => [
						'title' => __('Justified', 'thegem'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'left',
				'selectors_dictionary' => [
					'left' => 'justify-content: left; text-align: left;',
					'right' => 'justify-content: right; text-align: right;',
					'center' => 'justify-content: center; text-align: center;',
					'justify' => 'justify-content: space-between; text-align: justify;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => '{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Title Section
		$this->start_controls_section(
			'section_title',
			[
				'label' => __('Title', 'thegem'),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __('Share Icons Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Share', 'thegem'),
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
					'{{WRAPPER}}'.$this->get_customize_class().' .product-sharing__title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .product-sharing__title' => '{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Icons Section
		$this->start_controls_section(
			'section_icons',
			[
				'label' => __('Icons', 'thegem'),
			]
		);

		$this->add_control(
			'facebook',
			[
				'label' => __('Facebook', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'twitter',
			[
				'label' => __('Twitter (X)', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'pinterest',
			[
				'label' => __('Pinterest', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'tumblr',
			[
				'label' => __('Tumblr', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'linkedin',
			[
				'label' => __('LinkedIn', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'reddit',
			[
				'label' => __('Reddit', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'whatsapp',
			[
				'label' => __('Whatsapp', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
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
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .product-sharing__title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .product-sharing__title',
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
					'{{WRAPPER}}'.$this->get_customize_class() => 'margin-bottom:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Title Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-sharing__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icons_color',
			[
				'label' => __('Icons Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-sharing__list a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icons_color_hover',
			[
				'label' => __('Icons Color Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-sharing__list a:hover > i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icons_size',
			[
				'label' => __('Icons Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'tiny' => __('Tiny', 'thegem'),
					'small' => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem'),
					'xlarge' => __('Extra Large', 'thegem'),
					'custom' => __('Custom', 'thegem'),
				],
				'default' => 'tiny',
				'selectors_dictionary' => [
					'tiny' => 'font-size: 16px;',
					'small' => 'font-size: 24px;',
					'medium' => 'font-size: 48px;',
					'large' => 'font-size: 96px;',
					'xlarge' => 'font-size: 144px;',
					'custom' => '',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-sharing__list a > i' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'icons_size_custom',
			[
				'label' => __('Icons Custom Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'icons_size' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-sharing__list a > i' => 'font-size:{{SIZE}}{{UNIT}};',
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

		// Init Sharing
		ob_start();
		$product = thegem_templates_init_product();
		if (empty($product)) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$title_styled_class = thegem_te_product_text_styled($params);

		$post_image = '';
		$attachment_id = get_post_thumbnail_id(get_the_ID());
		if ($attachment_id) {
			$post_image = thegem_generate_thumbnail_src($attachment_id, 'thegem-blog-timeline-large');
			if ($post_image && $post_image[0]) {
				$post_image = $post_image[0];
			}
		}

		?>

        <div <?php if (!empty($params['element_id'])): ?>id="<?=esc_attr($params['element_id']); ?>"<?php endif;?>
             class="<?= $this->get_widget_wrapper() ?>">

            <div class="product-sharing socials-sharing socials socials-colored-hover">
		        <?php if ($params['title']): ?>
                    <div class="product-sharing__title <?= $title_styled_class ?>">
	                    <?= esc_html_e($params['title']) ?>:
                    </div>
		        <?php endif; ?>
                <div class="product-sharing__list">
			        <?php if ($params['facebook']): ?>
                        <a class="socials-item" target="_blank" href="<?= esc_url('https://www.facebook.com/sharer/sharer.php?u='.urlencode(get_permalink())); ?>" title="Facebook"><i class="socials-item-icon facebook"></i></a>
			        <?php endif; ?>
			        <?php if ($params['twitter']): ?>
                        <a class="socials-item" target="_blank" href="<?= esc_url('https://twitter.com/intent/tweet?text='.urlencode(get_the_title()).'&amp;url='.urlencode(get_permalink())); ?>" title="Twitter"><i class="socials-item-icon twitter"></i></a>
			        <?php endif; ?>
			        <?php if ($params['pinterest']): ?>
                        <a class="socials-item" target="_blank" href="<?= esc_url('https://pinterest.com/pin/create/button/?url=' . urlencode(get_permalink()) . '&amp;description=' . urlencode(get_the_title()) . ($post_image ? '&amp;media=' . urlencode($post_image) : '' )); ?>" title="Pinterest"><i class="socials-item-icon pinterest"></i></a>
			        <?php endif; ?>
			        <?php if ($params['tumblr']): ?>
                        <a class="socials-item" target="_blank" href="<?= esc_url('http://tumblr.com/widgets/share/tool?canonicalUrl='.urlencode(get_permalink())); ?>" title="Tumblr"><i class="socials-item-icon tumblr"></i></a>
			        <?php endif; ?>
			        <?php if ($params['linkedin']): ?>
                        <a class="socials-item" target="_blank" href="<?= esc_url('https://www.linkedin.com/shareArticle?mini=true&url='.urlencode(get_permalink()).'&amp;title='.urlencode(get_the_title())); ?>&amp;summary=<?php echo urlencode(get_the_excerpt()); ?>" title="LinkedIn"><i class="socials-item-icon linkedin"></i></a>
			        <?php endif; ?>
			        <?php if ($params['reddit']): ?>
                        <a class="socials-item" target="_blank" href="<?= esc_url('https://www.reddit.com/submit?url='.urlencode(get_permalink()).'&amp;title='.urlencode(get_the_title())); ?>" title="Reddit"><i class="socials-item-icon reddit"></i></a>
			        <?php endif; ?>
	                <?php if ($params['whatsapp']): ?>
		                <a class="socials-item" target="_blank" href="<?= esc_url('https://wa.me/?text='.urlencode(get_permalink())); ?>" title="WhatsApp"><i class="socials-item-icon whatsapp"></i></a>
	                <?php endif; ?>
                </div>
            </div>
        </div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}

}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateProductSharing());
}
