<?php

namespace TheGem_Elementor\Widgets\TemplatePortfolioExcerpt;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Portfolio Description.
 */

#[\AllowDynamicProperties]
class TheGem_TemplatePortfolioExcerpt extends Widget_Base {
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
		return 'thegem-template-portfolio-excerpt';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'portfolio';
	}

	/**
	 * Retrieve the widget description.
	 *
	 * @access public
	 *
	 * @return string Widget description.
	 */
	public function get_title() {
		return __('Excerpt/Short Description', 'thegem');
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
		return ['thegem_portfolio_builder'];
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-portfolio-excerpt';
	}

	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}

		return ' .'.$this->get_widget_wrapper().' .portfolio-excerpt';
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
					'{{WRAPPER}}'.$this->get_customize_class(true) => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_tag',
			[
				'label' => __('HTML Tag', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => __('H1', 'thegem'),
					'h2' => __('H2', 'thegem'),
					'h3' => __('H3', 'thegem'),
					'h4' => __('H4', 'thegem'),
					'h5' => __('H5', 'thegem'),
					'h6' => __('H6', 'thegem'),
					'p' => __('p', 'thegem'),
					'div' => __('div', 'thegem'),
				],
				'default' => 'div',
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
					'{{WRAPPER}}'.$this->get_customize_class() => 'letter-spacing: {{SIZE}}{{UNIT}};',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class(),
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class(),
			]
		);

		$this->add_control(
			'max_width',
			[
				'label' => __('Max Width', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => 'max-width: {{VALUE}}px;',
				],
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
			'text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => 'color: {{VALUE}};',
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
		$params = array_merge(array(), $settings);
		$template_id = get_the_ID();

		ob_start();
		$portfolio = thegem_templates_init_portfolio();
		if (empty($portfolio) || empty($portfolio->post_excerpt)) {
			ob_end_clean();
			echo thegem_templates_close_portfolio(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$params['element_class'] = implode(' ', array($this->get_widget_wrapper()));
		$text_styled_class = implode(' ', array($params['text_style'], $params['text_weight']));

		?>

        <div class="<?= esc_attr($params['element_class']); ?>">
            <<?= $params['text_tag'] ?> class="portfolio-excerpt <?= $text_styled_class ?>">
                <?= $portfolio->post_excerpt; ?>
            </<?= $params['text_tag'] ?>>
        </div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_portfolio(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}

}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplatePortfolioExcerpt());
