<?php

/**
 * Shortcode
 *
 *
 * @package    Tuturn
 * @subpackage Tuturn/admin
 * @author     Amentotech <theamentotech@gmail.com>
 */

namespace Elementor;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Tuturn_available_courses')) {
    class Tuturn_available_courses extends Widget_Base{

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_element_online_available_courses';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Available Courses and Jobs', 'tuturn');
        }

        /**
         *
         * @since    1.0.0
         * @access   public
         * @var      icon
         */
        public function get_icon()
        {
            return 'eicon-table-of-contents';
        }

        /**
         *
         * @since    1.0.0
         * @access   public
         * @var      category of shortcode
         */
        public function get_categories()
        {
            return ['tuturn-elements'];
        }

        /**
         * Register category controls.
         * @since    1.0.0
         * @access   protected
         */

        protected function register_controls()
        {
            //Content
            $this->start_controls_section(
                'content_section',
                [
                    'label'     => esc_html__('Content', 'tuturn'),
                    'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );
            $this->add_control(
                'main_heading',
                [
                    'label'         => esc_html__('Heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::WYSIWYG,
                    'placeholder'   => esc_html__('Type your main heading here', 'tuturn'),
                ]
            );
            $this->add_control(
                'button_text',
                [
                    'label'         => esc_html__('Button Text', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'placeholder'   => esc_html__('Type for button text', 'tuturn'),
                ]
            );
            $this->add_control(
                'button_text_link',
                [
                    'label'         => esc_html__( 'Link', 'tuturn' ),
                    'type'          => \Elementor\Controls_Manager::URL,
                    'placeholder'   => esc_html__( 'Enter button URL.', 'tuturn' ),
                    'default' => [
                        'url'               => '',
                        'is_external'       => true,
                        'nofollow'          => true,
                        'custom_attributes' => '',
                    ],
                ]
            );
            $this->add_control(
                'platforms',
                [
                    'label'     => esc_html__('Add Platforms', 'tuturn'),
                    'type'      => \Elementor\Controls_Manager::REPEATER,
                    'fields' => [
                        [
                            'name'          => 'image',
                            'type'          => \Elementor\Controls_Manager::MEDIA,
                            'label'         => esc_html__('Upload image', 'tuturn'),
                            'description'   => esc_html__('Upload image.(115x40)', 'tuturn'),
                            'default' => [
                                'url' => \Elementor\Utils::get_placeholder_image_src(),
                            ],
                        ],
                        [
                            'name'          => 'couter_range',
                            'type' => \Elementor\Controls_Manager::NUMBER,
                            'label' => esc_html__( 'Counter range', 'tuturn' ),
                        ],
                        [
                            'name'          => 'counter_text',
                            'type'          => \Elementor\Controls_Manager::TEXT,
                            'label'         => esc_html__('Counter text', 'tuturn'),
                            'description'   => esc_html__('Extra counter text', 'tuturn'),
                        ],
                        [
                            'name'          => 'description',
                            'type'          => \Elementor\Controls_Manager::TEXTAREA,
                            'label'         => esc_html__('Description', 'tuturn'),
                            'description'   => esc_html__('Description', 'tuturn'),
                        ]
                    ]
                ]
            );

            $this->end_controls_section();
        }

        /**
         * Render shortcode
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {

            $settings           = $this->get_settings_for_display();
            $main_heading       = !empty($settings['main_heading']) ? $settings['main_heading'] : '';
            $button_text        = !empty($settings['button_text']) ? $settings['button_text'] : '';
            $button_text_link   = !empty($settings['button_text_link']['url']) ? $settings['button_text_link']['url'] : 'javascript:void(0)';
            $platforms          = !empty($settings['platforms']) ? $settings['platforms'] : array(); ?>
            <div class="tu-success-section tu-main-section">
                <div class="container">
                    <div class="row">
                        <?php if(!empty($main_heading)){
                            $main_heading  = str_replace('{{','<span>',$main_heading);
                            $main_heading  = str_replace('}}','</span>',$main_heading);
                            ?>
                            <div class="col-12">
                                <div class="tu-maintitle">
                                    <?php echo do_shortcode($main_heading); ?>
                                    <?php if(!empty($button_text_link)){?>
                                        <a href="<?php echo esc_url($button_text_link); ?>" class="tu-primbtn-lg"><span><?php echo esc_html($button_text); ?></span><i class="icon icon-chevron-right"></i></a>
                                    <?php }?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row gy-4">
                        <?php foreach ($platforms as $platform) {
                            $platform_img_id   = !empty($platform['image']['id']) ? $platform['image']['id'] : '';
                            $couter_range   = !empty($platform['couter_range']) ? $platform['couter_range'] : '';
                            $counter_text   = !empty($platform['counter_text']) ? $platform['counter_text'] : '';
                            $description    = !empty($platform['description']) ? $platform['description'] : '';
							$thumnail_size_image = !empty($platform_img_id ) ? wp_get_attachment_image_src($platform_img_id, 'tu_profile_thumbnail') : '';?>
                            <div class="col-12 col-md-6 col-xxl-3">
                                <div id="tu-counter" class="tu-oursuccess">
                                    <?php if (!empty($thumnail_size_image[0])) { ?>
										<figure class="tu-oursuccess_img">											
											<img src="<?php echo esc_url($thumnail_size_image[0]); ?>" alt="<?php echo esc_attr($description); ?>">										   
										</figure>
									 <?php } ?>
                                    <div class="tu-oursuccess_info">
                                        <?php if (!empty($couter_range)) { ?>
                                            <h4><span data-from="<?php echo intval(0); ?>" data-to="<?php echo absint($couter_range); ?>" data-speed="8000" data-refresh-interval="50"><?php echo absint($couter_range); ?></span><?php if(!empty($counter_text)){echo esc_html($counter_text); } ?></h4>
                                        <?php } ?>
                                        <?php if (!empty($description)) { ?>
                                            <p><?php echo esc_html($description); ?></p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
            $script = "
            jQuery(document).ready(function () {
                try {
                    var _tu_counter = jQuery('#tu-counter');
                    _tu_counter.appear(function () {
                        var _tu_counter = jQuery('.tu-oursuccess_info span');
                        _tu_counter.countTo({
                            formatter: function (value, options) {
                                return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
                            }
                        });
                    });
                } catch (err) {}
                });";
                wp_add_inline_script('tuturn-public', $script, 'after');
            }
    }
        Plugin::instance()->widgets_manager->register(new Tuturn_available_courses);
}
