<?php

namespace TheGem_Elementor\Widgets\TemplateWishlist;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Wishlist.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateWishlist extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_WISHLIST_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_WISHLIST_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_WISHLIST_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_WISHLIST_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_script('thegem-te-wishlist', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_WISHLIST_URL . '/js/wishlist.js', array('jquery'), false, true);
		wp_register_style('thegem-te-wishlist', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_WISHLIST_URL . '/css/wishlist.css');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-wishlist';
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
		return __('Wishlist', 'thegem');
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

	public function is_wishlist_exist() {
		return (defined( 'WC_PLUGIN_FILE' ) && defined( 'YITH_WCWL' ));
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
		return ['thegem-te-wishlist'];
	}

	public function get_script_depends() {
		return ['thegem-te-wishlist'];
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
			'section_layout',
			[
				'label' => __('General', 'thegem'),
			]
		);

		if ($this->is_wishlist_exist()) {
			$this->add_control(
				'wishlist_icon',
				[
					'label' => __('Icon', 'thegem'),
					'type' => Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'icon_size',
				[
					'label' => __('Icon Size', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'default' => 'small',
					'options' => [
						'tiny' => __('Tiny', 'thegem'),
						'small' => __('Small', 'thegem'),
						'medium' => __('Medium', 'thegem'),
						'custom' => __('Custom', 'thegem'),
					],
				]
			);

			$this->add_responsive_control(
				'icon_size_custom',
				[
					'label' => __('Custom Size', 'thegem'),
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
					'condition' => [
						'icon_size' => 'custom'
					],
					'selectors' => [
						'{{WRAPPER}} .thegem-te-wishlist .gem-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'style_section',
				[
					'label' => __('Style', 'thegem'),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

			$this->start_controls_tabs('icon_tabs');
			$this->start_controls_tab('icon_tab_normal', ['label' => __('Normal', 'thegem'),]);
			$this->add_control(
				'icon_color_normal',
				[
					'label' => __('Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-wishlist .gem-icon' => 'color: {{VALUE}};',
					],
				]
			);
			$this->end_controls_tab();

			$this->start_controls_tab('icon_tab_hover', ['label' => __('Hover', 'thegem'),]);
			$this->add_control(
				'icon_color_hover',
				[
					'label' => __('Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-wishlist a:hover .gem-icon' => 'color: {{VALUE}};',
					],
				]
			);
			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'label_header',
				[
					'label' => __('Label', 'thegem'),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'label_color',
				[
					'label' => __('Label Text Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-wishlist .wishlist-items-count' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'label_background',
				[
					'label' => __('Label Background Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-wishlist .wishlist-items-count' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_section();
        } else {
			$this->add_control(
				'plugin_not_active',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('<div class="thegem-param-alert">You need to install YITH WooCommerce Wishlist.<br/> <a href="' . get_site_url() . '/wp-admin/plugins.php" target="_blank">Go to install plugins page.</a></div>', 'thegem'),
					'content_classes' => 'elementor-descriptor',
				]
			);
        }

	}

	protected function render() {
		if (!$this->is_wishlist_exist()) return;

		$settings = $this->get_settings_for_display();
		$uniqid = $this->get_id(); ?>

		<div class="thegem-te-wishlist">
			<a class="wishlist-link" href="<?php echo esc_url( YITH_WCWL()->get_wishlist_url() ); ?>">
				<div class="gem-icon gem-simple-icon gem-icon-size-<?php echo esc_attr($settings['icon_size']); ?>">
					<?php if ($settings['wishlist_icon']['value']) {
						Icons_Manager::render_icon($settings['wishlist_icon'], ['aria-hidden' => 'true']);
					} else { ?>
						<i class="default"></i>
					<?php } ?>
				</div>

				<div class="wishlist-items-count" style="<?php if(yith_wcwl_count_all_products() < 1): ?>display: none;<?php endif;?>">
					<?php echo esc_html(yith_wcwl_count_all_products()); ?>
				</div>
			</a>
		</div>
		<?php

		add_action('thegem_header_end', [__CLASS__, 'add_inline_update_script']);
	}

	static function add_inline_update_script() {
		if(defined('WP_CACHE') && WP_CACHE) : ?>
<script type="text/javascript">
(function($) {
	$.get( '<?php echo admin_url('admin-ajax.php'); ?>', {
		action: 'yith_wcwl_update_wishlist_count'
	}, function( data ) {
		if (data.count > 0) {
			$('.wishlist-items-count').show().html( data.count );
		} else {
			$('.wishlist-items-count').hide();
		}
	});
})(jQuery);
</script>
		<?php endif;
	}

}

if(defined('WC_PLUGIN_FILE')){
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateWishlist());
}
