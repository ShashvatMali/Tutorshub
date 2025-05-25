<?php

namespace TheGem_Elementor\Widgets\TemplateCurrencySwitcher;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Currency Switcher.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateCurrencySwitcher extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CURRENCY_SWITCHER_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CURRENCY_SWITCHER_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CURRENCY_SWITCHER_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CURRENCY_SWITCHER_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_script('thegem-te-currency-switcher', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CURRENCY_SWITCHER_URL . '/js/currency-switcher.js', array('jquery'), false, true);
		wp_register_style('thegem-te-currency-switcher', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_CURRENCY_SWITCHER_URL . '/css/currency-switcher.css');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-currency-switcher';
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
		return __('Currency Switcher', 'thegem');
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
			return ['thegem-te-currency-switcher'];
		}
		return ['thegem-te-currency-switcher'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-te-currency-switcher'];
		}
		return ['thegem-te-currency-switcher'];
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

		if (function_exists('icl_object_id') && defined( 'ICL_SITEPRESS_VERSION' )) {

			$this->add_control(
				'documentation_text',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('Please refer to this <a href="https://wpml.org/documentation/related-projects/woocommerce-multilingual/multi-currency-support-woocommerce/" target="_blank">documentation</a> on configuring currencies.', 'thegem'),
					'content_classes' => 'elementor-descriptor',
				]
			);

			$this->add_control(
				'type',
				[
					'label' => __('Switcher Type', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'default' => 'dropdown',
					'options' => [
						'dropdown' => __('Dropdown', 'thegem'),
						'list' => __('List of currencies', 'thegem'),
					],
				]
			);

			$this->add_responsive_control(
				'dropdown_spacing',
				[
					'label' => __('Dropdown Spacing', 'thegem'),
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
					'default' => [
						'size' => 20,
						'unit' => 'px',
					],
					'selectors' => [
						'{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item__current' => 'margin-bottom: -{{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'type' => 'dropdown'
					],
				]
			);

			$this->add_control(
				'currency_native_name',
				[
					'label' => __('Native name', 'thegem'),
					'return_value' => '1',
					'default' => '1',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'currency_translated_name',
				[
					'label' => __('Translated name', 'thegem'),
					'return_value' => '1',
					'default' => '',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'symbol',
				[
					'label' => __('Symbol', 'thegem'),
					'return_value' => '1',
					'default' => '',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'show_current',
				[
					'label' => __('Show Current', 'thegem'),
					'return_value' => '1',
					'default' => '1',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
					'condition' => [
						'type' => 'list'
					],
				]
			);

			$this->add_control(
				'capitalize_name',
				[
					'label' => __('Capitalize name', 'thegem'),
					'return_value' => '1',
					'default' => '',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'label' => __('Typography', 'thegem'),
					'name' => 'results_title_typography',
					'separator' => 'before',
					'selector' => '{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item__current,
					{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item__wrapper ul li a,
					{{WRAPPER}} .thegem-te-currency-switcher-list ul .switcher-list-item.active span,
					{{WRAPPER}} .thegem-te-currency-switcher-list ul li a',
				]
			);

			$this->start_controls_tabs('lang_tabs');

			$this->start_controls_tab('lang_tab_normal', ['label' => __('Normal', 'thegem'),]);

			$this->add_control(
				'text_color',
				[
					'label' => __('Text Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item__wrapper ul li a,
						{{WRAPPER}} .thegem-te-currency-switcher-list ul li a,
						{{WRAPPER}} .thegem-te-currency-switcher-list ul li a:before' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab('lang_tab_hover', ['label' => __('Hover', 'thegem'),]);

			$this->add_control(
				'text_color_hover',
				[
					'label' => __('Text Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item__wrapper ul li a:hover,
						{{WRAPPER}} .thegem-te-currency-switcher-list ul li a:hover,
						{{WRAPPER}} .thegem-te-currency-switcher-list ul li a:hover:before' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab('lang_tab_active', ['label' => __('Active', 'thegem'),]);

			$this->add_control(
				'text_color_active',
				[
					'label' => __('Text Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item__current,
						{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item:after,
						{{WRAPPER}} .thegem-te-currency-switcher-list ul .switcher-list-item.active span,
						{{WRAPPER}} .thegem-te-currency-switcher-list ul .switcher-list-item.active:before' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'dropdown_header',
				[
					'label' => __('Dropdown', 'thegem'),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'type' => 'dropdown'
					],
				]
			);

			$this->add_control(
				'dropdown_bg_color',
				[
					'label' => __('Background Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item__wrapper' => 'background-color: {{VALUE}}',
					],
					'condition' => [
						'type' => 'dropdown'
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'dropdown_box_shadow',
					'label' => __('Box Shadow', 'thegem'),
					'selector' => '{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item__wrapper',
					'condition' => [
						'type' => 'dropdown'
					],
				]
			);

			$this->add_responsive_control(
				'dropdown_padding',
				[
					'label' => __('Padding', 'thegem'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .thegem-te-currency-switcher-dropdown .dropdown-item__wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; margin-left: -{{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'type' => 'dropdown'
					],
				]
			);

			$this->add_control(
				'list_header',
				[
					'label' => __('List', 'thegem'),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'type' => 'list'
					],
				]
			);

			$this->add_control(
				'list_allow_prefix',
				[
					'label' => __('Arrow Prefix', 'thegem'),
					'return_value' => '1',
					'default' => '1',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
					'condition' => [
						'type' => 'list',
						'flag' => '',
					],
				]
			);

			$this->add_responsive_control(
				'list_space_between',
				[
					'label' => __('Space Between', 'thegem'),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .thegem-te-currency-switcher-list ul li' => 'padding: 0 calc({{SIZE}}{{UNIT}}/2);',
						'{{WRAPPER}} .thegem-te-currency-switcher-list ul' => 'margin: 0 calc(-{{SIZE}}{{UNIT}}/2);',
					],
					'condition' => [
						'type' => 'list'
					],
				]
			);

		} else {

			$this->add_control(
				'plugin_not_active',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('<div class="thegem-param-alert">You need to install WPML Multilingual CMS.<br/> <a href="' . get_site_url() . '/wp-admin/plugins.php" target="_blank">Go to install plugins page.</a></div>', 'thegem'),
					'content_classes' => 'elementor-descriptor',
				]
			);
		}

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$uniqid = $this->get_id();
		global $post;
		$templates_posttemp = $post; ?>

		<div class="thegem-te-currency-switcher currency-widget <?= esc_attr($uniqid) ?>">

			<?php if ( function_exists('icl_object_id') && defined( 'ICL_SITEPRESS_VERSION' ) && defined( 'WCML_VERSION' )):

				//Get active languages
				global $woocommerce_wpml;
				$currencies = [];

				if ($woocommerce_wpml->multi_currency) {
					$all_currencies = $woocommerce_wpml->multi_currency->get_currencies(true);

					foreach ($all_currencies as $key => $curr) {
						if ( $curr['languages'][ICL_LANGUAGE_CODE] == 1) {
							foreach (get_woocommerce_currencies() as $k => $name){
								if ( $key == $k ) {
									$currencies[$k]['code'] = $k;
									$currencies[$k]['name'] = $k[0].strtolower($k[1].$k[2]);
									$currencies[$k]['full_name'] = $name;
									$currencies[$k]['symbol'] = get_woocommerce_currency_symbol($k);
								}
							}
						}
					}
				}

				if (!empty( $currencies )) {
					$current_currency = $other_currencies = [];
					$wc_currency = get_woocommerce_currency();
					foreach ($currencies as $key => $curr) {
						if ( $wc_currency == $key ) {
							$current_currency = $curr;
						} else {
							$other_currencies[] = $curr;
						}
					}
				}

				if ($settings['type'] == 'dropdown'): ?>
					<div class="thegem-te-currency-switcher-dropdown currency-dropdown-widget wcml_currency_switcher">
						<?php if (!empty($current_currency)): ?>
							<div class="dropdown-item <?php if (empty($other_currencies)) echo 'single'; ?>">
								<div class="dropdown-item__current">
                                    <span class="name <?php if ($settings['capitalize_name']): ?>capitalize<?php endif; ?>">
                                          <?php if ($settings['currency_native_name']): ?>
											  <?= esc_html( $current_currency['name'] ) ?>
										  <?php endif; ?>

										  <?php if ($settings['currency_translated_name']): ?>
											  <?= esc_html( $current_currency['full_name'] ) ?>
										  <?php endif; ?>

										  <?php if ($settings['symbol']): ?>
											  <?= esc_html( $current_currency['symbol'] ) ?>
										  <?php endif; ?>
                                    </span>
								</div>

								<?php if (!empty($other_currencies)): ?>
									<div class="dropdown-item__wrapper">
										<ul>
											<?php foreach( $other_currencies as $curr ): ?>
												<li>
													<a href="" rel="<?= esc_html( $curr['code'] ) ?>">
                                                        <span class="name <?php if ($settings['capitalize_name']): ?>capitalize<?php endif; ?>">
                                                           <?php if ($settings['currency_native_name']): ?>
															   <?= esc_html( $curr['name'] ) ?>
														   <?php endif; ?>

														   <?php if ($settings['currency_translated_name']): ?>
															   <?= esc_html( $curr['full_name'] ) ?>
														   <?php endif; ?>

														   <?php if ($settings['symbol']): ?>
															   <?= esc_html( $curr['symbol'] ) ?>
														   <?php endif; ?>
                                                        </span>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if($settings['type'] == 'list'): ?>
				<div class="thegem-te-currency-switcher-list wcml-horizontal-list wcml_currency_switcher">
					<?php if (!empty($currencies)): ?>
						<ul>
							<?php if ($settings['show_current']): ?>
								<li>
									<div class="switcher-list-item active">
                                            <span class="name <?php if ($settings['capitalize_name']): ?>capitalize<?php endif; ?>">
                                                <?php if ($settings['currency_native_name']): ?>
													<?= esc_html( $current_currency['name'] ) ?>
												<?php endif; ?>

												<?php if ($settings['currency_translated_name']): ?>
													<?= esc_html( $current_currency['full_name'] ) ?>
												<?php endif; ?>

												<?php if ($settings['symbol']): ?>
													<?= esc_html( $current_currency['symbol'] ) ?>
												<?php endif; ?>
                                            </span>
									</div>
								</li>
							<?php endif; ?>

							<?php foreach( $other_currencies as $curr ): ?>
								<li>
									<a href="" rel="<?= esc_html( $curr['code'] ) ?>" class="switcher-list-item">
                                            <span class="name <?php if ($settings['capitalize_name']): ?>capitalize<?php endif; ?>">
                                                 <?php if ($settings['currency_native_name']): ?>
													 <?= esc_html( $curr['name'] ) ?>
												 <?php endif; ?>

												 <?php if ($settings['currency_translated_name']): ?>
													 <?= esc_html( $curr['full_name'] ) ?>
												 <?php endif; ?>

												 <?php if ($settings['symbol']): ?>
													 <?= esc_html( $curr['symbol'] ) ?>
												 <?php endif; ?>
                                            </span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php else:
				$site_currency = get_woocommerce_currency(); ?>
				<div class="thegem-te-currency-switcher-list">
					<ul>
						<li>
							<div class="switcher-list-item active disable">
								<span class="name"><?= esc_html(ucfirst($site_currency)) ?></span>
							</div>
						</li>
					</ul>
				</div>
			<?php endif; ?>
		</div>

		<?php
		$post = $templates_posttemp;
	}
}


if(defined('WC_PLUGIN_FILE')){
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateCurrencySwitcher());
}