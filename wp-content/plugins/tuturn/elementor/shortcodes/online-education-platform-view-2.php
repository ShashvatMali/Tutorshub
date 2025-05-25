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

if (!class_exists('Tuturn_online_education_platform_sec')) {
    class Tuturn_online_education_platform_sec extends Widget_Base
    {

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_element_online_education_platform_sec';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Online Education Platform second', 'tuturn');
        }

        /**
         *
         * @since    1.0.0
         * @access   public
         * @var      icon
         */
        public function get_icon()
        {
            return 'eicon-kit-parts';
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
                    'tab'       => Controls_Manager::TAB_CONTENT,
                ]
            );
            $this->add_control(
                'main_heading',
                [
                    'label'         => esc_html__('Main heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXTAREA,
                    'placeholder'   => esc_html__('Type your Main heading here', 'tuturn'),
                ]
            );
            $this->add_control(
                'sub_heading',
                [
                    'label'         => esc_html__('Sub heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXTAREA,
                    'placeholder'   => esc_html__('Type your Sub heading here', 'tuturn'),
                ]
            );
            $this->add_control(
                'description',
                [
                    'label'         => esc_html__('description', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXTAREA,
                    'placeholder'   => esc_html__('Type your description heading here', 'tuturn'),
                ]
            );
            $this->add_control(
                'platforms',
                [
                    'label'     => esc_html__('Add Platforms', 'tuturn'),
                    'type'      => Controls_Manager::REPEATER,
                    'fields' => [
                        [
                            'name'          => 'image',
                            'type'          => Controls_Manager::MEDIA,
                            'label'         => esc_html__('Upload image', 'tuturn'),
                            'description'   => esc_html__('Upload image.(115x40)', 'tuturn'),
                            'default' => [
                                'url' => \Elementor\Utils::get_placeholder_image_src(),
                            ],
                        ],
                        [
                            'name'          => 'title',
                            'type'          => Controls_Manager::TEXTAREA,
                            'label'         => esc_html__('Title', 'tuturn'),
                            'description'   => esc_html__('Title', 'tuturn'),
                        ],
                        [
                            'name'          => 'description',
                            'type'          => Controls_Manager::TEXTAREA,
                            'label'         => esc_html__('Description', 'tuturn'),
                            'description'   => esc_html__('Description', 'tuturn'),
                        ],
                        [
                            'name'          => 'active',
                            'type'          => Controls_Manager::SELECT2,
                            'label'         => esc_html__('Active box', 'tuturn'),
                            'description'   => esc_html__('', 'tuturn'),
                            'default'       => 'inactive',
                            'options'       => [
                                'active'   => esc_html__('Active', 'tuturn'),
                                'inactive'  => esc_html__('Inactive', 'tuturn'),
                            ]
                        ]
                        
                    ]
                ]
            );
            $this->add_control(
                'button_text',
                [
                    'label'         => esc_html__('Button text', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'placeholder'   => esc_html__('Type your Sub heading here', 'tuturn'),
                ]
            );
            $this->add_control(
                'button_text_link',
                [
                    'label' => esc_html__( 'Link', 'tuturn' ),
                    'type' => \Elementor\Controls_Manager::URL,
                    'placeholder' => esc_html__( 'https://example.com', 'tuturn' ),
                    'default' => [
                        'url' => '',
                        'is_external' => true,
                        'nofollow' => true,
                        'custom_attributes' => '',
                    ],
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
            $settings       = $this->get_settings_for_display();
            $main_heading   = !empty($settings['main_heading']) ? $settings['main_heading'] : '';
            $sub_heading    = !empty($settings['sub_heading']) ? $settings['sub_heading'] : '';
            $description    = !empty($settings['description']) ? $settings['description'] : '';
            $button_text    = !empty($settings['button_text']) ? $settings['button_text'] : '';
            $button_text_link   = !empty($settings['button_text_link']['url']) ? $settings['button_text_link']['url'] : '';
            $platforms      = !empty($settings['platforms']) ? $settings['platforms'] : array();

            $default_zigzag = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/zigzag-line.svg');
            $default_dotted_background =  tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/dotted-background.png');
            ?>
            <div class="tu-main-section">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12 col-lg-8">
                            <div class="tu-maintitle text-center">
                                <?php if(!empty($default_zigzag)) {?>
                                    <img src="<?php echo esc_attr($default_zigzag); ?>" alt="<?php echo esc_attr($sub_heading);?>">
                                <?php } ?>
                                <?php if (!empty($sub_heading)) { ?>
                                    <h4><?php echo esc_html($sub_heading); ?></h4>
                                <?php } ?>
                                <?php if (!empty($main_heading)) { ?>
                                    <h2><?php echo esc_html($main_heading); ?></h2>
                                <?php } ?>
                                <?php if (!empty($description)) { ?>
                                    <p><?php echo esc_html($description); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($platforms as $platform) {
                            $platform_img = !empty($platform['image']['url']) ? $platform['image']['url'] : '';
                            $platform_title = !empty($platform['title']) ? $platform['title'] : '';
                            $platform_description = !empty($platform['description']) ? $platform['description'] : '';
                            $platform_active = !empty($platform['active']) ? $platform['active'] : '';

                            $active_class   = '';
                            if($platform_active == 'active'){
                                $active_class   = ' tu-activebox';
                            }
                            ?>
                            <div class="col-md-12 col-lg-6 col-xxl-4">
                                <div class="tu-eduplatform <?php echo esc_attr($active_class);?>">
                                    <figure class="tu-eduplatform_img">
                                        <?php if (!empty($platform_img)) { ?>
                                            <img src="<?php echo esc_url($platform_img); ?>" alt="<?php echo esc_attr($platform_title);?>">
                                        <?php } ?>
                                    </figure>
                                    <div class="tu-eduplatform_info">
                                        <?php if (!empty($platform_title)) { ?>
                                            <h5><?php echo esc_html($platform_title); ?></h5>
                                        <?php } ?>
                                        <?php if (!empty($platform_description)) { ?>
                                            <p><?php echo esc_html($platform_description); ?></p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if (!empty($button_text)) { ?>
                        <div class="tu-mainbtn">
                            <a href="<?php echo esc_url($button_text_link)?>" class="tu-primbtn-lg"><span><?php echo esc_html($button_text); ?></span><i class="icon icon-lock"></i></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
         }
    }
    Plugin::instance()->widgets_manager->register(new Tuturn_online_education_platform_sec);
}
