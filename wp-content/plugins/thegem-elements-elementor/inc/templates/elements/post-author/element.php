<?php

namespace TheGem_Elementor\Widgets\TemplatePostAuthor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Blog Title.
 */

#[\AllowDynamicProperties]
class TheGem_TemplatePostAuthor extends Widget_Base {
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
		return 'thegem-template-post-author';
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
		return get_post_type($post_id) !== 'thegem_templates' || thegem_get_template_type($post_id) === 'single-post' || thegem_get_template_type($post_id) === 'title';
	}
	
	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Author Box', 'thegem');
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
		if(get_post_type($post_id) === 'thegem_templates') return ['thegem_single_post_builder'];
		return ['thegem_single_post'];
	}
	
	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}
	
	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-post-author';
	}
	
	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}
		
		return ' .'.$this->get_widget_wrapper().' .post-author';
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
				],
				'default' => 'left',
			]
		);
		
		$this->end_controls_section();
		
		// Avatar Section
		$this->start_controls_section(
			'section_avatar',
			[
				'label' => __('Avatar', 'thegem'),
			]
		);
		
		$this->add_control(
			'avatar',
			[
				'label' => __('Avatar', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'avatar_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'avatar' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'author_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'avatar' => 'yes',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Name Section
		$this->start_controls_section(
			'section_name',
			[
				'label' => __('Name', 'thegem'),
			]
		);
		
		$this->add_control(
			'name',
			[
				'label' => __('Name', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'name_font_style',
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
				'condition' => [
					'name' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'name_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'name' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'name_letter_spacing',
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
					'name' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__name span' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'name_text_transform',
			[
				'label' => __('Text Transform', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'name' => 'yes',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__name span' => '{{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Description Section
		$this->start_controls_section(
			'section_desc',
			[
				'label' => __('Description', 'thegem'),
			]
		);
		
		$this->add_control(
			'desc',
			[
				'label' => __('Description', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'desc_font_style',
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
				'condition' => [
					'desc' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'desc_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'desc' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'desc_letter_spacing',
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
					'desc' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__desc p' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'desc_text_transform',
			[
				'label' => __('Text Transform', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'desc' => 'yes',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__desc p' => '{{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Link Section
		$this->start_controls_section(
			'section_link',
			[
				'label' => __('Link', 'thegem'),
			]
		);
		
		$this->add_control(
			'link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);
		
		$this->add_control(
			'link_label',
			[
				'label' => __('Label', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('More posts by', 'thegem'),
				'condition' => [
					'link' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'link_font_style',
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
				'condition' => [
					'link' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'link_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'link' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'link_letter_spacing',
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
					'link' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__link a' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'link_text_transform',
			[
				'label' => __('Text Transform', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
				],
				'default' => '',
				'condition' => [
					'link' => 'yes',
				],
				'selectors_dictionary' => [
					'default' => '',
					'none' => 'text-transform: none;',
					'capitalize' => 'text-transform: capitalize;',
					'lowercase' => 'text-transform: lowercase;',
					'uppercase' => 'text-transform: uppercase;',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__link a' => '{{VALUE}};',
				],
			]
		);
  
		$this->end_controls_section();
		
		// General Section Style
		$this->start_controls_section(
			'section_general_style',
			[
				'label' => __('General', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'panel_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'panel_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'panel_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'panel_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => 'border-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'panel_padding',
			[
				'label' => esc_html__('Panel Paddings', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class() => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Name Section Style
		$this->start_controls_section(
			'section_name_style',
			[
				'label' => __('Name', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'name' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-author__name span',
				'condition' => [
					'name' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'name_text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-author__name span',
				'condition' => [
					'name' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'name_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'name' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__name span' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Description Section Style
		$this->start_controls_section(
			'section_desc_style',
			[
				'label' => __('Description', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'desc' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'desc_max_width',
			[
				'label' => __('Max Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 2000,
					],
				],
				'condition' => [
					'desc' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__desc' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-author__desc p',
				'condition' => [
					'desc' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'desc_text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-author__desc p',
				'condition' => [
					'desc' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'desc_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'desc' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__desc p' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		// Link Section Style
		$this->start_controls_section(
			'section_link_style',
			[
				'label' => __('Link', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'link' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'link_typography',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-author__link a',
				'condition' => [
					'link' => 'yes',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'link_text_shadow',
				'selector' => '{{WRAPPER}}'.$this->get_customize_class().' .post-author__link a',
				'condition' => [
					'link' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'link_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'link' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__link a' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'link_color_hover',
			[
				'label' => __('Text Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'link' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .post-author__link a:hover' => 'color: {{VALUE}};',
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
		
		$user_id = $single_post->post_author;
		$user_data = get_userdata( $user_id );
  
		if (empty($single_post) || empty($user_data)) {
			ob_end_clean();
			echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}
		
		$alignment = 'post-author--'.$params['alignment'];
		$params['element_class'] = implode(' ', array($this->get_widget_wrapper(), $alignment));
		$name_styled = implode(' ', array($params['name_font_style'], $params['name_font_weight']));
		$desc_styled = implode(' ', array($params['desc_font_style'], $params['desc_font_weight']));
		$link_styled = implode(' ', array($params['link_font_style'], $params['link_font_weight']));
		
		?>

        <div class="<?= esc_attr($params['element_class']); ?>">
            <div class="post-author">
		        <?php if (!empty($params['avatar'])):
			        $size = !empty($params['avatar_size']['size']) ? $params['avatar_size']['size'] : 100;
			        $style = '';
                    if ($params['alignment'] == 'center'){
	                    $top = !empty($params['avatar_size']['size']) ? round($params['avatar_size']['size'] / 2, 2).$params['avatar_size']['unit'] : null;
	                    $style = 'top: -'.$top.'';
                    }
                ?>
                
                <?php if ( !empty($params['author_link']) && get_the_author_meta('url', $user_id) ) : ?>
                    <a href="<?= esc_url( get_the_author_meta('url', $user_id) ); ?>" class="post-author__avatar" <?php if (!empty($style)) : ?>style="<?=$style?>"<?php endif; ?>>
				        <?= get_avatar( $user_id, $size ); ?>
                    </a>
		        <?php else : ?>
                    <div class="post-author__avatar" <?php if (!empty($style)) : ?>style="<?=$style?>"<?php endif; ?>><?= get_avatar( $user_id, $size ); ?></div>
		        <?php endif; ?>
		        <?php endif; ?>

                <div class="post-author__info">
			        <?php if (!empty($params['name'])): ?>
                        <div class="post-author__name">
                            <span class="<?= $name_styled ?>"><?= get_the_author_meta('display_name', $user_id) ?></span>
                        </div>
			        <?php endif; ?>
			
			        <?php if (!empty($params['desc'])): ?>
                        <div class="post-author__desc">
                            <p class="<?= $desc_styled ?>"><?= do_shortcode(nl2br(get_the_author_meta('description', $user_id))); ?></p>
                        </div>
			        <?php endif; ?>
			
			        <?php if (!empty($params['link'])): ?>
                        <div class="post-author__link">
                            <a href="<?= esc_url(get_author_posts_url( $user_id )); ?>" class="<?= $link_styled ?>">
						        <?php printf(esc_html__(''.$params['link_label'].' %s', 'thegem'), $user_data->data->display_name); ?>
                            </a>
                        </div>
			        <?php endif; ?>
                </div>
            </div>
        </div>
		
		<?php
		
		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
		
		echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplatePostAuthor());
