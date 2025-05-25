<?php

namespace TheGem_Elementor\Widgets\TemplateLanguageSwitcher;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Language Switcher.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateLanguageSwitcher extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LANGUAGE_SWITCHER_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LANGUAGE_SWITCHER_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LANGUAGE_SWITCHER_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LANGUAGE_SWITCHER_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_script('thegem-te-language-switcher', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LANGUAGE_SWITCHER_URL . '/js/language-switcher.js', array('jquery'), false, true);
		wp_register_style('thegem-te-language-switcher', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_LANGUAGE_SWITCHER_URL . '/css/language-switcher.css');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-language-switcher';
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
		return __('Language Switcher', 'thegem');
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
			return ['thegem-te-language-switcher'];
		}
		return ['thegem-te-language-switcher'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['thegem-te-language-switcher'];
		}
		return ['thegem-te-language-switcher'];
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

		if ((function_exists('icl_object_id') && defined( 'ICL_SITEPRESS_VERSION' )) || defined( 'POLYLANG_VERSION' )) {

			$this->add_control(
				'documentation_text',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('Please refer to this <a href="https://docs.codex-themes.com/category/576-wpml" target="_blank">documentation</a> on configuring WPML. For Polylang please check this <a href="https://polylang.pro/doc/the-language-switcher/" target="_blank">article</a>', 'thegem'),
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
						'list' => __('List of languages', 'thegem'),
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
						'{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item__current' => 'margin-bottom: -{{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'type' => 'dropdown'
					],
				]
			);

			$this->add_control(
				'flag',
				[
					'label' => __('Flag', 'thegem'),
					'return_value' => '1',
					'default' => '1',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'native_name',
				[
					'label' => __('Native language name', 'thegem'),
					'return_value' => '1',
					'default' => '1',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'translated_name',
				[
					'label' => __('Translated language name', 'thegem'),
					'return_value' => '1',
					'default' => '',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'current_lang',
				[
					'label' => __('Current language', 'thegem'),
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
					'label' => __('Capitalize the language name', 'thegem'),
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
					'selector' => '{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item__current,
					{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item__wrapper ul li a,
					{{WRAPPER}} .thegem-te-language-switcher-list ul li a',
				]
			);

			$this->start_controls_tabs('lang_tabs');

			$this->start_controls_tab('lang_tab_normal', ['label' => __('Normal', 'thegem'),]);

			$this->add_control(
				'lang_color',
				[
					'label' => __('Text Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item__wrapper ul li a,
						{{WRAPPER}} .thegem-te-language-switcher-list ul li a' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab('lang_tab_hover', ['label' => __('Hover', 'thegem'),]);

			$this->add_control(
				'lang_color_hover',
				[
					'label' => __('Text Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item__wrapper ul li a:hover,
						{{WRAPPER}} .thegem-te-language-switcher-list ul li a:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab('lang_tab_active', ['label' => __('Active', 'thegem'),]);

			$this->add_control(
				'lang_color_active',
				[
					'label' => __('Text Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item__current,
						{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item:after,
						{{WRAPPER}} .thegem-te-language-switcher-list ul li a.active,
						{{WRAPPER}} .thegem-te-language-switcher-list ul li a.active:hover' => 'color: {{VALUE}};',
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
						'{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item__wrapper' => 'background-color: {{VALUE}}',
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
					'selector' => '{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item__wrapper',
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
						'{{WRAPPER}} .thegem-te-language-switcher-dropdown .dropdown-item__wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; margin-left: -{{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .thegem-te-language-switcher-list ul li' => 'padding: 0 calc({{SIZE}}{{UNIT}}/2);',
						'{{WRAPPER}} .thegem-te-language-switcher-list ul' => 'margin: 0 calc(-{{SIZE}}{{UNIT}}/2);',
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
					'raw' => __('<div class="thegem-param-alert">You need to install WPML Multilingual CMS or Polylang plugin.<br/> <a href="' . get_site_url() . '/wp-admin/plugins.php" target="_blank">Go to install plugins page.</a></div>', 'thegem'),
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

		<div class="thegem-te-language-switcher <?= esc_attr($uniqid) ?>">

			<?php if ((function_exists('icl_object_id') && defined( 'ICL_SITEPRESS_VERSION' )) || defined( 'POLYLANG_VERSION' )):

				//Get active languages

				$languages = array();
				if(function_exists('icl_object_id') && defined( 'ICL_SITEPRESS_VERSION' )) {
					$languages = apply_filters('wpml_active_languages', NULL, NULL);
				} elseif (defined( 'POLYLANG_VERSION' )) {
					$languages = pll_the_languages(array('raw' => 1));
					foreach ($languages as $slug => $lang) {
						$languages[$slug]['active'] = $lang['current_lang'];
						$languages[$slug]['country_flag_url'] = $lang['flag'];
						$languages[$slug]['native_name'] = $lang['name'];
						$languages[$slug]['translated_name'] = $lang['name'];
						$languages[$slug]['english_name'] = $lang['name'];
						$languages[$slug]['code'] = $lang['slug'];
						$settings['translated_name'] = 0;
					}
				}

				if (!empty($languages)) {
					$current_language = $not_current_languages = $all_languages = [];
					foreach ($languages as $lang) {
						$all_languages[] = $lang;
						if (!$settings['current_lang']) {
							foreach ($all_languages as $key => $item) {
								if ($item['active'] == 1) {
									unset($all_languages[$key]);
								}
							}
						}

						if ($lang['active']) {
							$current_language = $lang;
						} else {
							$not_current_languages[] = $lang;
						}
					}
				} else if (get_post_type() === 'thegem_templates' && thegem_get_template_type(get_the_id()) === 'header' && (function_exists('icl_object_id') && defined( 'ICL_SITEPRESS_VERSION' ))) {
					global $sitepress;
					$languages = $sitepress->get_active_languages();
					$current_lang = $sitepress->get_current_language();

					$current_language = $not_current_languages = $all_languages = [];
					foreach ($languages as $i => $lang) {
						$lang['country_flag_url'] = $sitepress->get_flag_url($lang['code']);
						$all_languages[] = $lang;

						if ($lang['code'] == $current_lang) {
							$current_language = $lang;
						} else {
							$not_current_languages[] = $lang;
						}
					}
				}

				if ($settings['type'] == 'dropdown'): ?>
					<div class="thegem-te-language-switcher-dropdown">
						<?php if (!empty($current_language)): ?>
							<?php
							$native_name = $settings['native_name'] ? $current_language['native_name'] : null;
							$translated_name = $settings['translated_name'] ? isset($current_language['translated_name']) ? $current_language['translated_name'] : $current_language['english_name'] : null;
							if ($settings['native_name'] && $settings['translated_name']) {
								$translated_name = ' (' . $translated_name . ')';
							}
							$current_lang_name = ($native_name || $translated_name) ? $native_name . $translated_name : null;
							?>
							<div class="dropdown-item <?php if ($settings['flag'] && empty($current_lang_name)): ?>flag-only<?php endif; ?> <?php if (empty($not_current_languages)) echo 'single'; ?>">
								<div class="dropdown-item__current">
									<?php if ($settings['flag']): ?>
										<i class="flag"><img src="<?= esc_url($current_language['country_flag_url']) ?>"
															 alt="<?= esc_html($current_language['code']) ?>"/></i>
									<?php endif; ?>

									<?php if (!empty($current_lang_name)): ?>
										<span class="name <?php if ($settings['capitalize_name']): ?>capitalize<?php endif; ?>"><?= esc_html($current_lang_name) ?></span>
									<?php endif; ?>
								</div>

								<?php if (!empty($not_current_languages)): ?>
									<div class="dropdown-item__wrapper">
										<ul>
											<?php foreach ($not_current_languages as $lng): ?>
												<?php
												$native_name = $settings['native_name'] ? $lng['native_name'] : null;
												$translated_name = $settings['translated_name'] ? isset($lng['translated_name']) ? $lng['translated_name'] : $lng['english_name'] : null;
												if ($settings['native_name'] && $settings['translated_name']) {
													$translated_name = ' (' . $translated_name . ')';
												}
												$lang_name = ($native_name || $translated_name) ? $native_name . $translated_name : null;
												?>
												<li>
													<a class="thegem-wpml-ls-link" href="<?php echo isset($lng['url']) ? esc_url($lng['url']) : ''; ?>">
														<?php if ($settings['flag']): ?>
															<i class="flag"><img
																		src="<?= esc_url($lng['country_flag_url']) ?>"
																		alt="<?= esc_html($lng['code']) ?>"/></i>
														<?php endif; ?>

														<?php if (!empty($lang_name)): ?>
															<span class="name <?php if ($settings['capitalize_name']): ?>capitalize<?php endif; ?>"><?= esc_html($lang_name) ?></span>
														<?php endif; ?>
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

				<?php if ($settings['type'] == 'list'): ?>
				<div class="thegem-te-language-switcher-list">
					<?php if (!empty($all_languages)): ?>
						<ul>
							<?php foreach ($all_languages as $lng): ?>
								<?php
								$native_name = $settings['native_name'] ? $lng['native_name'] : null;
								$translated_name = $settings['translated_name'] ? isset($lng['translated_name']) ? $lng['translated_name'] : $lng['english_name'] : null;
								if ($settings['native_name'] && $settings['translated_name']) {
									$translated_name = ' (' . $translated_name . ')';
								}
								$lang_name = ($native_name || $translated_name) ? $native_name . $translated_name : null;
								?>
								<li>
									<a href="<?= esc_url($lng['url']) ?>"
									   class="thegem-wpml-ls-link <?php if ($lng['active']): ?>active<?php endif; ?> <?php if ($settings['flag'] || !$settings['list_allow_prefix']): ?>flag-only<?php endif; ?>">
										<?php if ($settings['flag']): ?>
											<i class="flag"><img src="<?= esc_url($lng['country_flag_url']) ?>"
																 alt="<?= esc_html($lng['code']) ?>"/></i>
										<?php endif; ?>

										<?php if (!empty($lang_name)): ?>
											<span class="name <?php if ($settings['capitalize_name']): ?>capitalize<?php endif; ?>"><?= esc_html($lang_name) ?></span>
										<?php endif; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php else:
				$site_lang = explode('-', get_bloginfo('language')); ?>
				<div class="thegem-te-language-locale">
					<?= esc_html($site_lang[0]) ?>
				</div>
			<?php endif; ?>
		</div>

		<?php
		$post = $templates_posttemp;
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateLanguageSwitcher());