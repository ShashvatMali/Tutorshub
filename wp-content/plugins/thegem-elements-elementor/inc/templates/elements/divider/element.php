<?php

namespace TheGem_Elementor\Widgets\TemplateDivider;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Template Divider.
 */
#[\AllowDynamicProperties]
class TheGem_TemplateDivider extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_DIVIDER_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_DIVIDER_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_DIVIDER_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TEMPLATE_DIVIDER_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-te-divider', THEGEM_ELEMENTOR_WIDGET_TEMPLATE_DIVIDER_URL . '/css/divider.css');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-divider';
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
		return __('Divider', 'thegem');
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
		return ['thegem-te-divider'];
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
			'section_general',
			[
				'label' => __('General', 'thegem'),
			]
		);

		$this->add_control(
			'direction',
			[
				'label' => __('Direction', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => [
					'vertical' => __('Vertical', 'thegem'),
					'horizontal' => __('Horizontal', 'thegem'),
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => __('Height', 'thegem'),
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
					'size' => 50,
					'unit' => 'px',
				],
				'condition' => [
					'direction' => 'vertical'
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-divider .gem-divider' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => __('Width', 'thegem'),
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
					'size' => 50,
					'unit' => 'px',
				],
				'condition' => [
					'direction' => 'horizontal',
					'horizontal_enable_full_width!' => '1',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'horizontal_enable_full_width',
			[
				'label' => __('Enable Full Width', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'selectors_dictionary' => [
					'1' => 'width: 100%;',
				],
				'selectors' => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'condition' => [
					'direction' => 'horizontal'
				],
				'description' => __('Enable stretching divider to full width of page content.', 'thegem'),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __('Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid' => __('Solid', 'thegem'),
					'stroked' => __('Stroked', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
			]
		);

		$this->add_responsive_control(
			'divider_weight',
			[
				'label' => __('Weight (px)', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'condition' => [
					'style' => array('solid', 'dotted', 'dashed')
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-divider .gem-divider' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'style' => array('solid', 'stroked', 'dotted', 'dashed')
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-divider .gem-divider' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$uniqid = $this->get_id();

		$this->add_render_attribute('divider', 'class', [
			'thegem-te-divider',
			'gem-divider-style-' . $settings['style'],
			'gem-divider-direction-' . $settings['direction'],
			$settings['horizontal_enable_full_width'] === '1' ? 'gem-divider-horizontal-full' : '',
		]);

		$divider_html = '';
		if ($settings['style'] == 'stroked') {
			if ($settings['direction'] == 'vertical') {
				$divider_html = '<svg width="1px" height="100%"><line x1="0" x2="1" y1="0" y2="100%" stroke="currentColor" stroke-width="2" stroke-linecap="black" stroke-dasharray="4, 4"/></svg>';
			} else {
				$divider_html = '<svg width="100%" height="1px"><line x1="0" x2="100%" y1="0" y2="0" stroke="currentColor" stroke-width="2" stroke-linecap="black" stroke-dasharray="4, 4"/></svg>';
			}
		} ?>

		<div <?php echo($this->get_render_attribute_string('divider')); ?>>
			<div class="gem-divider">
				<?php echo $divider_html; ?>
			</div>
		</div>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateDivider());