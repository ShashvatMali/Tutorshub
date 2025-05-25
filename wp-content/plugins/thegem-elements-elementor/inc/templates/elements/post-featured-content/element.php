<?php

namespace TheGem_Elementor\Widgets\TemplateFeaturedContent;

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
class TheGem_TemplateFeaturedContent extends Widget_Base {
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
		return 'thegem-template-featured-content';
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
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-post' || thegem_get_template_type($post_id) === 'title';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Featured Content', 'thegem');
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
		return ['thegem_single_post_builder'];
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-post-featured-content';
	}

	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}

		return ' .'.$this->get_widget_wrapper().' .featured-content';
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		// General Section
		$this->start_controls_section(
			'section_image',
			[
				'label' => __('Image', 'thegem'),
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'stretch' => __('Stretch', 'thegem'),
				],
				'default' => 'default',
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
				'condition' => [
					'size' => ['default'],
				],
				'default' => 'left',
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
		$featured_content = thegem_get_post_featured_content($single_post->ID);

		if (empty($single_post) || empty($featured_content)) {
			ob_end_clean();
			echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$format = get_post_format($single_post->ID);
		$isImage = ($format == '' || $format == 'image');

		$size = !empty($params['size']) && $isImage ? 'featured-image--'.$params['size'] : null;
		$alignment = !empty($params['alignment']) && $isImage ? 'featured-image--'.$params['alignment'] : null;
		$params['element_class'] = implode(' ', array(
			$size,
			$alignment,
			$this->get_widget_wrapper()
		));

		?>

        <div class="<?= esc_attr($params['element_class']); ?>">
            <?= $featured_content ?>
        </div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateFeaturedContent());
