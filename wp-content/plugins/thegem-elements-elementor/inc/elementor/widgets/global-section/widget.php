<?php

namespace TheGem_Elementor\Widgets\Template;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;

if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Global Section.
 */
#[\AllowDynamicProperties]
class TheGem_Template extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Global Section', 'thegem');
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
		return ['thegem_elements'];
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_template',
			[
				'label' => esc_html__( 'Global Section', 'thegem' ),
			]
		);

		$templates = thegem_get_section_templates_list();

		if(1 || (is_array($templates) && count($templates))) {
			$this->add_control(
				'template_id',
				[
					'label' => esc_html__( 'Select Global Section', 'thegem' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'options' => $templates,
					'thegem_template_link' => true,
					'thegem_template_type' => 'content'
				]
			);
		} else {
			$this->add_control(
				'empty_output',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => '<p><b>'.esc_html__('No Global Sections Found', 'thegem').'</b></p><p>&nbsp;</p>'.
						'<p>'.sprintf(__('To create new global section go to <a href="%s" target="_blank">TheGem Templates Builder &rarr; Global Sections</a>', 'thegem'), add_query_arg(array('post_type' => 'thegem_templates', 'templates_type' => 'content'), admin_url( 'edit.php' )).'#open-modal').'</p><p>&nbsp;</p>'.
						'<p>'.sprintf(__('Check <a href="%s" target="_blank">documentation</a>', 'thegem'), 'https://docs.codex-themes.com/article/540-section-templates').'</p>',
					'content_classes' => 'elementor-descriptor',
				]
			);
		}

		$this->end_controls_section();

	}



	protected function render() {
		$template_id = $this->get_settings( 'template_id' );
		$template_id = intval($template_id);
		$return_html = '<p>'.esc_html__('Global Section', 'thegem').'</p>';
		if($template_id > 0 && $template = get_post($template_id)) {
			$return_html = Plugin::instance()->frontend->get_builder_content_for_display( $template_id );
			if(\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				$template_link = add_query_arg(array('post' => $template_id, 'action' => 'elementor'), admin_url( 'post.php' ));
				$button = '<a class="thegem-template-edit-button gem-button gem-button-size-small gem-button-style-flat gem-button-text-weight-thin" href="'.$template_link.'" onclick="window.open(this.href, \'_blank\');">'.esc_html__('Edit Template ', 'thegem').'</a>';
				$return_html = $button.$return_html;
			}
			$return_html = '<div class="thegem-template-wrapper thegem-template-content thegem-template-' . esc_attr($template_id) . '">' . $return_html . '</div>';
		}
	
		echo $return_html;
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_Template());