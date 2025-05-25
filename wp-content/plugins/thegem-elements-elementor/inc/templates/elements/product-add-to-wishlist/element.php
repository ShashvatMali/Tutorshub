<?php

namespace TheGem_Elementor\Widgets\TemplateProductAddToWishlist;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

#[\AllowDynamicProperties]
class TheGem_TemplateProductAddToWishlist extends Widget_Base {
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
		return 'thegem-template-product-add-to-wishlist';
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
		return __('Product Add to Wishlist', 'thegem');
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
		return 'thegem-te-product-add-to-wishlist';
	}

	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}
		
		return ' .'.$this->get_widget_wrapper().' .product-add-to-wishlist';
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
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
					'{{WRAPPER}} .'.$this->get_widget_wrapper() => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __('Wishlist Text', 'thegem'),
				'default' => 'no',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'text_custom',
			[
				'label' => __('"Add to wishlist" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __('Add to Wishlist', 'thegem'),
				'condition' => [
					'text' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_remove',
			[
				'label' => __('"Remove from Wishlist" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __('Remove from Wishlist', 'thegem'),
				'condition' => [
					'text' => 'yes',
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
				'condition' => [
					'text' => 'yes',
				],
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
				'condition' => [
					'text' => 'yes',
					'text_style' => ['title-h1', 'title-h2', 'title-h3', 'title-h4', 'title-h5', 'title-h6', 'title-xlarge']
				],
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
				'condition' => [
					'text' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wishlist_icon',
			[
				'label' => __('Icon', 'thegem'),
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __('Wishlist Icon', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'wishlist_add_icon',
			[
				'label' => __('Wishlist Add', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'wishlist_remove_icon',
			[
				'label' => __('Wishlist Remove', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_horizontal_align',
			[
				'label' => __('Position to Text', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'condition' => [
					'text' => 'yes',
					'icon' => 'yes',
				],
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __('Top', 'thegem'),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
					'bottom' => [
						'title' => __('Bottom', 'thegem'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'left',
			]
		);

		$this->add_control(
			'icon_vertical_align',
			[
				'label' => __('Vertical Align', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'condition' => [
					'text' => 'yes',
					'icon' => 'yes',
				],
				'options' => [
					'top' => [
						'title' => __('Top', 'thegem'),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'thegem'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'center',
			]
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'wishlist_text_style',
			[
				'label' => __('Text Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'text' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .product-add-to-wishlist-text span',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .product-add-to-wishlist-text span',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .product-add-to-wishlist-text span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'text_hover_color',
			[
				'label' => __('Text Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().':hover  .product-add-to-wishlist-text span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wishlist_icon_style',
			[
				'label' => __('Icon Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().' .yith-wcwl-add-to-wishlist a i:before, {{WRAPPER}} '.$this->get_customize_class().' .gem-icon-add-to-wishlist:before, {{WRAPPER}} '.$this->get_customize_class().' .gem-icon-remove-to-wishlist:before' => 'font-size:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-left .product-add-to-wishlist-text' => 'margin-left:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-right .product-add-to-wishlist-text' => 'margin-right:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-top .product-add-to-wishlist-text' => 'margin-top:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-bottom .product-add-to-wishlist-text' => 'margin-bottom:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-left, {{WRAPPER}} '.$this->get_customize_class().'.icon-position-right' => 'min-height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-top, {{WRAPPER}} '.$this->get_customize_class().'.icon-position-bottom' => 'min-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-left .product-add-to-wishlist-text' => 'padding-left:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-right .product-add-to-wishlist-text' => 'padding-right:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-top .product-add-to-wishlist-text' => 'padding-top:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} '.$this->get_customize_class().'.icon-position-bottom .product-add-to-wishlist-text' => 'padding-bottom:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'text' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .yith-wcwl-add-to-wishlist a' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label' => __('Icon Hover Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().':hover .yith-wcwl-add-to-wishlist a' => 'color: {{VALUE}} !important;',
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
			'alignment' => 'left',
			'icon' => 'yes',
			'icon_horizontal_align' => 'left',
			'icon_vertical_align' => 'center',
			'text' => 'no',
			'text_custom' => __('Add to Wishlist', 'thegem'),
			'text_remove' => __('Remove from Wishlist', 'thegem'),
			'text_style' => '',
			'text_font_weight' => '',
			'text_font_style' => '',
		), $settings);

		// Init Wishlist
		ob_start();
		$product = thegem_templates_init_product();

		if (empty($product) || !defined( 'YITH_WCWL' )) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return ;
		}
		if ($params['icon'] !== 'yes' && ($params['text'] !== 'yes' || empty($params['text_custom']))) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return ;
		}

		?>

		<div class="<?= $this->get_widget_wrapper() ?>">

			<div class="product-add-to-wishlist<?= ($params['text'] === 'yes' && $params['text_custom'] ? ' with-text' : '')?><?= ($params['icon'] === 'yes' ? '' : ' without-icon')?> icon-position-<?= $params['icon_horizontal_align'] ?>">
				<?= do_shortcode( '[yith_wcwl_add_to_wishlist thegem_template="1" thegem_product_page="1"]' ); ?>

				<?php if ($params['text'] === 'yes' && $params['text_custom']) : ?>
					<div class="product-add-to-wishlist-text">
						<span class="<?=$params['text_style']?> <?=$params['text_weight']?> <?=$params['text_font_style']?>" data-add-text="<?= esc_html__($params['text_custom'], 'thegem'); ?>" data-remove-text="<?= esc_html__($params['text_remove'], 'thegem'); ?>">
							<?= esc_html__($params['text_custom'], 'thegem'); ?>
						</span>
					</div>
				<?php endif; ?>
				<?php if(!empty($settings['wishlist_add_icon']['value']) || !empty($settings['wishlist_remove_icon']['value'])) : ?>
					<div class="product-add-to-wishlist-custom-icons">
						<?php \Elementor\Icons_Manager::render_icon( $settings['wishlist_add_icon'], [ 'aria-hidden' => 'true', 'class' => 'custom-add-wishlist-icon gem-icon-add-to-wishlist' ] ); ?>
						<?php \Elementor\Icons_Manager::render_icon( $settings['wishlist_remove_icon'], [ 'aria-hidden' => 'true', 'class' => 'custom-remove-wishlist-icon gem-icon-remove-to-wishlist' ] ); ?>
					</div>
				<?php endif; ?>
				<script type="text/javascript">
				(function() {
					var wishlistAddIcon = document.querySelector('.elementor-element-<?php echo $this->get_id(); ?> .yith-wcwl-add-button a i');
					if(wishlistAddIcon) {
						var addIcon = document.querySelector('.elementor-element-<?php echo $this->get_id(); ?> .product-add-to-wishlist-custom-icons .gem-icon-add-to-wishlist');
						if(addIcon) {
							wishlistAddIcon.outerHTML = addIcon.outerHTML;
						}
						var wlText = document.querySelector('.elementor-element-<?php echo $this->get_id(); ?> .product-add-to-wishlist-text span');
						if(wlText) {
							wlText.innerHTML = wlText.dataset.addText;
						}
					}
					var wishlistRemoveIcon = document.querySelector('.elementor-element-<?php echo $this->get_id(); ?> .yith-wcwl-wishlistexistsremove a i');
					if(wishlistRemoveIcon) {
						var removeIcon = document.querySelector('.elementor-element-<?php echo $this->get_id(); ?> .product-add-to-wishlist-custom-icons .gem-icon-remove-to-wishlist');
						if(removeIcon) {
							wishlistRemoveIcon.outerHTML = removeIcon.outerHTML;
						}
						var wlText = document.querySelector('.elementor-element-<?php echo $this->get_id(); ?> .product-add-to-wishlist-text span');
						if(wlText) {
							wlText.innerHTML = wlText.dataset.removeText;
						}
					}
				})();
				</script>
			</div>

			<?= thegem_woocommerce_product_page_ajax_notification() ?>
			
		</div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		$return_html = $return_html;
		echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);

	}

}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateProductAddToWishlist());
}