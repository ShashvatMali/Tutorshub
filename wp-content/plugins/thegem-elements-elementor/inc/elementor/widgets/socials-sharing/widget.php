<?php

namespace TheGem_Elementor\Widgets\TheGem_Social_Sharing;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Custom Menu.
 */
#[\AllowDynamicProperties]
class TheGem_Social_Sharing extends Widget_Base {

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_SOCIAL_SHARING_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_SOCIAL_SHARING_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_SOCIAL_SHARING_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_SOCIAL_SHARING_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */

	public function get_name() {

		return 'thegem-social-sharing';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */

	public function get_title() {

		return __('Social Sharing', 'thegem');
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

	public function get_style_depends() {
		return [];
	}

	public function get_script_depends() {
		return [];
	}

	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-socials-sharing';
	}

	/** Get customize class */
	public function get_customize_class() {
		return ' .'.$this->get_widget_wrapper().' .socials-sharing';
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
			'icons_type',
			[
				'label' => __('Icons Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'simple' => __('Simple', 'thegem'),
					'round' => __('Round Border', 'thegem'),
					'square' => __('Square Border', 'thegem'),
				],
				'default' => 'simple',
			]
		);

		$this->add_control(
			'icons_spacing_horizontal',
			[
				'label' => __('Spacing Horizontal', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' ul' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'.$this->get_customize_class().' ul li' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icons_spacing_vertical',
			[
				'label' => __('Spacing Vertical', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' ul li' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icons_size',
			[
				'label' => __('Icons Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'tiny' => __('Tiny', 'thegem'),
					'small' => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem'),
					'xlarge' => __('Extra Large', 'thegem'),
					'custom' => __('Custom', 'thegem'),
				],
				'default' => 'tiny',
			]
		);

		$this->add_control(
			'icons_size_custom',
			[
				'label' => __('Icons Custom Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'icons_size' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Icons Section
		$this->start_controls_section(
			'section_icons',
			[
				'label' => __('Icons', 'thegem'),
			]
		);

		$this->add_control(
			'facebook',
			[
				'label' => __('Facebook', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'twitter',
			[
				'label' => __('Twitter (X)', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'pinterest',
			[
				'label' => __('Pinterest', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'tumblr',
			[
				'label' => __('Tumblr', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'linkedin',
			[
				'label' => __('LinkedIn', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'reddit',
			[
				'label' => __('Reddit', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'telegram',
			[
				'label' => __('Telegram', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'whatsapp',
			[
				'label' => __('WhatsApp', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'viber',
			[
				'label' => __('Viber', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'xing',
			[
				'label' => __('Xing', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);


		$this->add_control(
			'threads',
			[
				'label' => __('Threads', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'return_value' => 'yes',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
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

		$this->add_control(
			'icons_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'icons_type' => ['round', 'square'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icons_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'icons_type' => ['round', 'square'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icons_border_width',
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
				'condition' => [
					'icons_type' => ['round', 'square'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icons_border_radius',
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
				'condition' => [
					'icons_type' => ['round', 'square'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icons_color',
			[
				'label' => __('Icons Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icons_color_hover',
			[
				'label' => __('Icons Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icons_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'icons_type' => ['round', 'square'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icons_background_color_hover',
			[
				'label' => __('Background Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'icons_type' => ['round', 'square'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icons_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'icons_type' => ['round', 'square'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icons_border_color_hover',
			[
				'label' => __('Border Color on Hover', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'icons_type' => ['round', 'square'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .socials-item:hover' => 'border-color: {{VALUE}};',
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

		$params = $this->get_settings_for_display();
		$uniqid = uniqid('thegem-custom-').rand(1,9999);

		$alignment = !empty($params['alignment']) ? 'socials-sharing--'.$params['alignment'] : '';
		$icons_type = !empty($params['icons_type']) ? 'socials-sharing--'.$params['icons_type'] : '';
		$icons_size = !empty($params['icons_size']) ? 'socials-sharing--'.$params['icons_size'] : '';
		$params['element_class'] = implode(' ', array(
			$this->get_widget_wrapper(), $alignment, $icons_type, $icons_size,
		));

		global $wp;
		$current_url = home_url( add_query_arg( array(), $wp->request ) );
		$title = function_exists('thegem_title') ? wp_strip_all_tags(thegem_title('', false)) : '';
		$description = is_singular() ? get_the_excerpt(get_queried_object_id()) : '';

        ?>

        <div id="<?php echo(esc_attr($uniqid)); ?>" class="<?= esc_attr($params['element_class']); ?>">
            <div class="socials-sharing">
                <ul>
					<?php if ($params['facebook']): ?>
                        <li><a class="socials-item facebook" target="_blank" href="<?= esc_url('https://www.facebook.com/sharer/sharer.php?u='.urlencode($current_url)); ?>" title="Facebook"><i class="socials-item-icon facebook"></i></a></li>
					<?php endif; ?>

					<?php if ($params['twitter']): ?>
                        <li><a class="socials-item twitter" target="_blank" href="<?= esc_url('https://twitter.com/intent/tweet?text='.urlencode($title.'&amp;url='.urlencode($current_url))); ?>" title="Twitter"><i class="socials-item-icon twitter"></i></a></li>
					<?php endif; ?>

					<?php if ($params['pinterest']): ?>
                        <li><a class="socials-item pinterest" target="_blank" href="<?= esc_url('https://pinterest.com/pin/create/button/?url='.urlencode($current_url).'&amp;description='.urlencode($title)); ?>" title="Pinterest"><i class="socials-item-icon pinterest"></i></a></li>
					<?php endif; ?>

					<?php if ($params['tumblr']): ?>
                        <li><a class="socials-item tumblr" target="_blank" href="<?= esc_url('https://tumblr.com/widgets/share/tool?canonicalUrl='.urlencode($current_url)); ?>" title="Tumblr"><i class="socials-item-icon tumblr"></i></a></li>
					<?php endif; ?>

					<?php if ($params['linkedin']): ?>
                        <li><a class="socials-item linkedin" target="_blank" href="<?= esc_url('https://www.linkedin.com/shareArticle?mini=true&url='.urlencode($current_url).'&amp;title='.urlencode($title)); ?>&amp;summary=<?php echo urlencode($description); ?>" title="LinkedIn"><i class="socials-item-icon linkedin"></i></a></li>
					<?php endif; ?>

					<?php if ($params['reddit']): ?>
                        <li><a class="socials-item reddit" target="_blank" href="<?= esc_url('https://www.reddit.com/submit?url='.urlencode($current_url).'&amp;title='.urlencode($title)); ?>" title="Reddit"><i class="socials-item-icon reddit"></i></a></li>
					<?php endif; ?>

					<?php if ($params['telegram']): ?>
                        <li><a class="socials-item telegram" target="_blank" href="<?= esc_url('https://t.me/share/url?url='.urlencode($current_url).'&amp;text='.urlencode($title)); ?>" title="Telegram"><i class="socials-item-icon telegram"></i></a></li>
					<?php endif; ?>

					<?php if ($params['whatsapp']): ?>
                        <li><a class="socials-item whatsapp" target="_blank" href="<?= esc_url('https://wa.me/?text='.urlencode($current_url)); ?>" title="WhatsApp"><i class="socials-item-icon whatsapp"></i></a></li>
					<?php endif; ?>

					<?php if ($params['viber']): ?>
                        <li><a class="socials-item viber" target="_blank" href="<?= 'viber://forward/?text='.urlencode($current_url); ?>" title="Viber"><i class="socials-item-icon viber"></i></a></li>
					<?php endif; ?>

					<?php if ($params['xing']): ?>
                        <li><a class="socials-item xing" target="_blank" href="<?= esc_url('https://www.xing.com/spi/shares/new?url='.urlencode($current_url)); ?>" title="Xing"><i class="socials-item-icon xing"></i></a></li>
					<?php endif; ?>

					<?php if ($params['threads']): ?>
                        <li><a class="socials-item threads" target="_blank" href="<?= esc_url('https://www.threads.net/intent/post?text='.urlencode($current_url)); ?>" title="Threads"><i class="socials-item-icon threads"></i></a></li>
					<?php endif; ?>
                </ul>
            </div>
        </div>

		<?php

	}
}

Plugin::instance()->widgets_manager->register(new TheGem_Social_Sharing());
