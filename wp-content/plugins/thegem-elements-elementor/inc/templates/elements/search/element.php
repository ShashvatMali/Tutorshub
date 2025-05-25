<?php

namespace TheGem_Elementor\Widgets\TemplateSearch;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Search.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateSearch extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCH_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCH_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCH_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCH_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-te-search', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCH_URL . '/css/search.css', array(), null);
		wp_register_style('thegem-te-search-fullscreen', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCH_URL . '/css/thegem-fullscreen-search.css', array(), null);
		wp_register_script('thegem-te-search', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_SEARCH_URL . '/js/search.js', array('jquery'), null, true);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-search';
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
		return __('Search Icon', 'thegem');
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
			return ['thegem-te-search-fullscreen',
				'thegem-te-search'];
		}
		return ['thegem-te-search'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-te-search'];
		}
		return ['thegem-te-search'];
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
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __('Search Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fullscreen',
				'options' => [
					'dropdown' => __('Dropdown', 'thegem'),
					'fullscreen' => __('Fullscreen Overlay', 'thegem'),
				],
			]
		);

		$this->add_control(
			'placeholder_text',
			[
				'label' => __('Placeholder Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Search...', 'thegem'),
				'condition' => [
					'layout' => 'dropdown'
				],
			]
		);

		$this->add_control(
			'layout_fullscreen_placeholder_text',
			[
				'label' => __('Placeholder Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Start typing to search...', 'thegem'),
				'condition' => [
					'layout' => 'fullscreen'
				],
			]
		);

		$this->add_control(
			'post_types_header',
			[
				'label' => __('Post Types', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'post_type_products',
			[
				'label' => __('Products', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'post_type_posts',
			[
				'label' => __('Blog Posts', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'post_type_pages',
			[
				'label' => __('Pages', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'post_type_portfolio',
			[
				'label' => __('Portfolio', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'results_header',
			[
				'label' => __('Live Search', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'layout' => 'fullscreen'
				],
			]
		);

		$this->add_control(
			'search_ajax',
			[
				'label' => __('AJAX Live Search', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout' => 'fullscreen'
				],
			]
		);

		$this->add_control(
			'products_auto_suggestions',
			[
				'label' => __('Products Auto-Suggestions', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 24,
					],
				],
				'default' => [
					'size' => 16,
					'unit' => '%',
				],
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'posts_auto_suggestions',
			[
				'label' => __('Posts Auto-Suggestions', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 16,
					],
				],
				'default' => [
					'size' => 8,
					'unit' => '%',
				],
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'posts_result_title',
			[
				'label' => __('Posts Results Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Results from Blog', 'thegem'),
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'pages_auto_suggestions',
			[
				'label' => __('Pages Auto-Suggestions', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 16,
					],
				],
				'default' => [
					'size' => 8,
					'unit' => '%',
				],
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'pages_result_title',
			[
				'label' => __('Pages Results Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Results from Pages', 'thegem'),
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'portfolio_auto_suggestions',
			[
				'label' => __('Portfolio Auto-Suggestions', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 16,
					],
				],
				'default' => [
					'size' => 8,
					'unit' => '%',
				],
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'portfolio_result_title',
			[
				'label' => __('Portfolio Results Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Results from Portfolio', 'thegem'),
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'view_results_button_text',
			[
				'label' => __('"View Results" Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('View all search results', 'thegem'),
				'condition' => [
					'layout' => 'fullscreen',
					'search_ajax' => '1'
				],
			]
		);

		$this->add_control(
			'popular_header',
			[
				'label' => __('Popular Searches', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'layout' => 'fullscreen'
				],
			]
		);

		$this->add_control(
			'popular',
			[
				'label' => __('Popular Searches', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'layout' => 'fullscreen'
				],
			]
		);

		$this->add_control(
			'popular_title',
			[
				'label' => __('Popular Searches Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Top Searches:', 'thegem'),
				'condition' => [
					'popular' => '1',
					'layout' => 'fullscreen'
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label' => __('Term', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'select_terms_data',
			[
				'label' => __('Select Search Terms', 'thegem'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __('Search Term 1', 'thegem'),
					],
				],
				'title_field' => '{{{ title }}}',
				'condition' => [
					'popular' => '1',
					'layout' => 'fullscreen'
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

		$this->add_control(
			'search_icon',
			[
				'label' => __('Search Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'close_icon',
			[
				'label' => __('Search Icon Close', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_responsive_control(
			'icon_size',
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
					'{{WRAPPER}} .thegem-te-search__item a' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .thegem-te-search__item a' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .thegem-te-search__item a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab('icon_tab_active', ['label' => __('Active', 'thegem'),]);
		$this->add_control(
			'icon_color_active',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .thegem-te-search__item a.active' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if (!defined('WC_PLUGIN_FILE')) {
			$settings['post_type_products'] = '';
		}

		$search_item_class = '';
		if ($settings['layout'] == 'fullscreen') {
			$search_item_class = 'te-menu-item-fullscreen-search';
			wp_enqueue_style('thegem-te-search-fullscreen');
		} ?>

		<div class="thegem-te-search">
			<div class="thegem-te-search__item <?php echo esc_html($search_item_class); ?>">
				<a href="#">
					<span class="open">
						<?php if ($settings['search_icon']['value']) {
							Icons_Manager::render_icon($settings['search_icon'], ['aria-hidden' => 'true']);
						} else { ?>
							<i class="default"></i>
						<?php } ?>
					</span>
					<span class="close">
						<?php if ($settings['close_icon']['value']) {
							Icons_Manager::render_icon($settings['close_icon'], ['aria-hidden' => 'true']);
						} else { ?>
							<i class="default"></i>
						<?php } ?>
					</span>
				</a>
				<div class="thegem-te-search-hide" style="display: none">
					<?php if ($settings['layout'] == 'dropdown') { ?>
						<div class="minisearch">
							<form role="search" id="searchform" class="sf" action="<?php echo esc_url(thegem_home_url('/')); ?>"
								  method="GET">
								<input id="searchform-input" class="sf-input" type="text"
									   placeholder="<?php echo esc_html($settings['placeholder_text']); ?>" name="s">
								<?php if ($settings['post_type_products'] == '1' && thegem_get_option('website_search_mixed_disabled') != '1') { ?>
									<input type="hidden" name="post_type" value="product" />
								<?php } ?>
								<span class="sf-submit-icon"></span>
								<input id="searchform-submit" class="sf-submit" type="submit" value="s">
							</form>
						</div>
					<?php } else {
						$settings['search_id'] = $this->get_id();

						if ($settings['search_ajax'] == 1) {
							$settings['products_auto_suggestions'] = $settings['products_auto_suggestions']['size'];
							$settings['posts_auto_suggestions'] = $settings['posts_auto_suggestions']['size'];
							$settings['pages_auto_suggestions'] = $settings['pages_auto_suggestions']['size'];
							$settings['portfolio_auto_suggestions'] = $settings['portfolio_auto_suggestions']['size'];
						}

						thegem_fullscreen_search_layout($settings);
					} ?>
				</div>

			</div>
		</div>

		<?php

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>

			<script type="text/javascript">
				(function ($) {

					setTimeout(function () {
						$('.elementor-element-<?php echo $this->get_id(); ?> .thegem-te-search').initSearchIcons();
					}, 1000);

				})(jQuery);

			</script>
		<?php endif;
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateSearch());