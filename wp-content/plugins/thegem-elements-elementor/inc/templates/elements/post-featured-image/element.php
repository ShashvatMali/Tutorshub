<?php

namespace TheGem_Elementor\Widgets\TemplateFeaturedImage;

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
class TheGem_TemplateFeaturedImage extends Widget_Base {
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
		return 'thegem-template-featured-image';
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
		return __('Featured Image', 'thegem');
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
		return 'thegem-te-post-featured-image';
	}

	/** Get customize class */
	public function get_customize_class($only_parent = false) {
		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}

		return ' .'.$this->get_widget_wrapper().' .featured-image';
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
			'size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'thegem'),
					'stretch' => __('Stretch', 'thegem'),
					'custom' => __('Custom', 'thegem'),
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
					'size' => ['default', 'custom'],
				],
				'default' => 'left',
			]
		);

		$this->add_control(
			'width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'size' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-post-featured-image .post-featured-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'size' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-post-featured-image .post-featured-image img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'alt',
			[
				'label' => __('Alt text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('', 'thegem'),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __('Image style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'border-8' => __('8px & border', 'thegem'),
					'border-16' => __('16px & border', 'thegem'),
					'border-outline-8' => __('8px outlined border', 'thegem'),
					'border-outline-20' => __('20px outlined border', 'thegem'),
					'border-shadow-20' => __('20px border with shadow', 'thegem'),
                    'border-combined' => __('Combined border', 'thegem'),
					'border-radius-20' => __('20px border radius', 'thegem'),
					'border-radius-55' => __('55px border radius', 'thegem'),
					'dashed-inside' => __('Dashed inside', 'thegem'),
					'dashed-outside' => __('Dashed outside', 'thegem'),
					'rounded-with-border' => __('Rounded with border', 'thegem'),
					'rounded-without-border' => __('Rounded without border', 'thegem')
				],
				'default' => '',
			]
		);

		$this->add_control(
			'action',
			[
				'label' => __('On click action', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('None', 'thegem'),
					'lightbox' => __('Open Lightbox', 'thegem'),
				],
				'default' => '',
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
		$featured_image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $single_post->ID ), "full" );

		if (empty($single_post) || empty($featured_image_data)) {
			ob_end_clean();
			echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$src = $featured_image_data[0];
		$alt = !empty($params['alt']) ? $params['alt'] : $single_post->post_name;
		$width = !empty($params['width']['size']) ? $params['width']['size'] : $featured_image_data[1];
		$height = !empty($params['height']['size']) ? $params['height']['size'] : $featured_image_data[2];
		$size = !empty($params['size']) ? 'featured-image--'.$params['size'] : null;
		$alignment = !empty($params['alignment']) ? 'featured-image--'.$params['alignment'] : null;
		$style = !empty($params['style']) ? 'featured-image--'.$params['style'] : null;

		$params['element_class'] = implode(' ', array(
			$size,
			$alignment,
			$style,
            $this->get_widget_wrapper(),
        ));

		?>

        <div class="<?= esc_attr($params['element_class']); ?>">
            <div class="post-featured-image">
		        <?php if (!empty($params['action'])): ?><a href="<?= esc_url($src) ?>" class="fancybox" rel="lightbox"><?php endif; ?>
                    <img src="<?= $src ?>" width="<?= esc_attr($width) ?>" height="<?= esc_attr($height) ?>" alt="<?= esc_attr($alt) ?>">
                <?php if (!empty($params['action'])): ?></a><?php endif; ?>
            </div>
        </div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_post(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateFeaturedImage());
