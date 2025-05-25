<?php
namespace TheGem_Elementor\DynamicTags;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Popup extends Tag {

	public function get_name() {
		return 'thegem-popup';
	}

	public function get_title() {
		return esc_html__( 'Popup', 'thegem' );
	}

	public function get_group() {
		return 'thegem';
	}

	public function get_categories() {
		return [ TagsModule::URL_CATEGORY ];
	}

	protected function register_controls() {
		$this->add_control(
			'action',
			[
				'label' => esc_html__( 'Action', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'open',
				'options' => [
					'open' => esc_html__( 'Open Popup', 'thegem' ),
					'close' => esc_html__( 'Close Popup', 'thegem' ),
				],
			]
		);

		$templates = array();
		$templates_list = thegem_get_templates('popup');
		foreach ($templates_list as $template) {
			$templates[$template->ID] = $template->post_title . ' (ID = ' . $template->ID . ')';
		}

		if(is_array($templates) && count($templates)) {
			$this->add_control(
				'template_id',
				[
					'label' => esc_html__( 'Select Popup', 'thegem' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'options' => $templates,
					'condition' => [
						'action' => 'open',
					],
				]
			);
		} else {
			$this->add_control(
				'empty_output',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => '<p><b>'.esc_html__('No Popups Found', 'thegem').'</b></p><p>&nbsp;</p>'.
						'<p>'.sprintf(__('To create new popup go to <a href="%s" target="_blank">TheGem Templates Builder &rarr; Popups</a>', 'thegem'), add_query_arg(array('post_type' => 'thegem_templates', 'templates_type' => 'popup'), admin_url( 'edit.php' )).'#open-modal').'</p><p>&nbsp;</p>',
						//'<p>'.sprintf(__('Check <a href="%s" target="_blank">documentation</a>', 'thegem'), 'https://docs.codex-themes.com/article/540-section-templates').'</p>',
					'content_classes' => 'elementor-descriptor',
					'condition' => [
						'action' => 'open',
					],
				]
			);
		}

	}

	public function render() {
		$settings = $this->get_settings();

		if('close' === $settings['action']) {
			echo Plugin::generate_action_link( 'popupClose', [] );
		} else {
			if(!empty($settings['template_id'])) {
				echo Plugin::generate_action_link( 'popupOpen', array('id' => $settings['template_id']) );
			}
			if(!isset($GLOBALS['thegem_dynamic_popup_to_display'])) {
				$GLOBALS['thegem_dynamic_popup_to_display'] = array();
			}
			if(!in_array($settings['template_id'], $GLOBALS['thegem_dynamic_popup_to_display'])) {
				$GLOBALS['thegem_dynamic_popup_to_display'][] = $settings['template_id'];
			}
		}
	}

}
