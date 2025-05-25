<?php

namespace TheGem_Elementor\Widgets\TemplatePostTags;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Post Tags.
 */

#[\AllowDynamicProperties]
class TheGem_TemplatePostTags extends Widget_Base {
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
		return 'thegem-template-post-tags';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-post' || thegem_get_template_type($post_id) === 'loop-item' || thegem_get_template_type($post_id) === 'title';
	}
	
	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Post Tags', 'thegem');
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
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'title') return ['thegem_title_area_builder'];
		if(get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'loop-item') return ['thegem_loop_builder'];
		return ['thegem_single_post_builder'];
	}
	
	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}
	
	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-post-tags';
	}
	
	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}
		
		return ' .'.$this->get_widget_wrapper().' .post-tags';
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
			]
		);
		
		$this->add_control(
			'title',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Tags:', 'thegem'),
			]
		);
		
		$this->add_control(
			'text_style',
			[
				'label' => __('Text Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__title' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a' => 'letter-spacing: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__title' => '{{VALUE}};',
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a' => '{{VALUE}};',
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
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-tags__title span, {{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a',
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-tags__title span, {{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a',
			]
		);
		
		$this->add_control(
			'title_color',
			[
				'label' => __('Title Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__title span' => 'color: {{VALUE}};',
				],
			]
		);
  
		$this->add_control(
			'link_color',
			[
				'label' => __('Link Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'link_color_hover',
			[
				'label' => __('Link Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'link_background_color',
			[
				'label' => __('Link Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'link_background_color_hover',
			[
				'label' => __('Link Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'link_border_color',
			[
				'label' => __('Link Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a' => 'border-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'link_border_color_hover',
			[
				'label' => __('Link Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-tags__list a:hover' => 'border-color: {{VALUE}};',
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
		
		ob_start();
		$single_post = thegem_templates_init_post();
		$tags = get_the_tags();
  
		if (empty($single_post) || empty($tags)) {
			ob_end_clean();
			echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}
		
		$alignment = !empty($params['alignment']) ? 'post-tags--'.$params['alignment'] : '';
		$params['element_class'] = implode(' ', array($this->get_widget_wrapper(), $alignment));
		$text_styled = implode(' ', array($params['text_style'], $params['text_weight']));
		
		$tags_output = '';
		foreach ($tags as $tag) {
			$tag_link = get_tag_link($tag->term_id);
			$tags_output .= '<li><a href="' . $tag_link . '" title="' . $tag->name . '" class="' . $text_styled . '">' . $tag->name . '</a></li>';
		}
		
		?>

        <div class="<?= esc_attr($params['element_class']); ?>">
            <div class="post-tags">
				<?php if (!empty($params['title'])): ?>
                    <div class="post-tags__title">
                        <span class="<?= $text_styled ?>"><?= esc_html($params['title']) ?></span>
                    </div>
				<?php endif; ?>

                <div class="post-tags__list">
                    <ul><?= $tags_output ?></ul>
                </div>
            </div>
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplatePostTags());
