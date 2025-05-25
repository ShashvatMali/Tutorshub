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

if (!class_exists('Tuturn_online_education_platform')) {
    class Tuturn_online_education_platform extends Widget_Base
    {

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_element_online_education_platform';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Online Education Platform', 'tuturn');
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
                'button_text',
                [
                    'label'         => esc_html__('Explore all text', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'placeholder'   => esc_html__('Add button text.leave empty to hide it', 'tuturn'),
                ]
            );
            $this->add_control(
                'image',
                [
                    'label'     => esc_html__('Add Image', 'tuturn'),
                    'type'      => Controls_Manager::MEDIA,
                    'description' => esc_html__('Add an image.', 'tuturn'),
                    'default' => [
                        'url' => \Elementor\Utils::get_placeholder_image_src(),
                    ],

                ]
            );

            $this->add_control(
                'text1',
                [
                    'label'         => esc_html__('Text 1', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'placeholder'   => esc_html__('Type your link text here', 'tuturn'),
                ]
            );
            $this->add_control(
                'text2',
                [
                    'label'         => esc_html__('Text 2', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'placeholder'   => esc_html__('Type your link text here', 'tuturn'),
                ]
            );
            $this->add_control(
                'button_text_link',
                [
                    'label'         => esc_html__( 'Link', 'tuturn' ),
                    'type'          => \Elementor\Controls_Manager::URL,
                    'placeholder'   => esc_html__( 'https://example.com', 'tuturn' ),
                    'default'       => [
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
            $image          = !empty($settings['image']['url']) ? $settings['image']['url'] : '';
            $button_text    = !empty($settings['button_text']) ? $settings['button_text'] : '';
            $text1          = !empty($settings['text1']) ? $settings['text1'] : '';
            $text2          = !empty($settings['text2']) ? $settings['text2'] : '';
            $button_text_link = !empty($settings['button_text_link']['url']) ? $settings['button_text_link']['url'] : '';
            $default_zigzag   = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/zigzag-line.svg');
            $default_dotted_background =  tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/dotted-background.png');?>

            <div class="tu-main-section tu-adu-platform">
                <div class="container">
                    <div class="row align-items-center gy-4">
                        <div class="col-md-12 col-lg-6">
                            <div class="tu-maintitle">
                                <?php  if(!empty($default_zigzag)){ ?>
                                    <img src="<?php echo esc_attr($default_zigzag); ?>" alt="<?php esc_attr_e('Image','tuturn'); ?>">
                                <?php   }?>
                                <?php if (!empty($sub_heading)) { ?>
                                    <h4><?php echo esc_html($sub_heading); ?></h4>
                                <?php } ?>
                                <?php if (!empty($main_heading)) { ?>
                                    <h2><?php echo esc_html(($main_heading)); ?></h2>
                                <?php } ?>
                                <?php if (!empty($description)) { ?>
                                    <p><?php echo esc_html($description); ?></p>
                                <?php } ?>
                                <?php if (!empty($button_text)) { ?>
                                    <a href="<?php echo esc_url($button_text_link) ?>" class="tu-primbtn-lg"><span><?php echo esc_html($button_text) ?></span><i class="icon icon-chevron-right"></i></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="tu-betterresult">
                                <?php if (!empty($image)) { ?>
                                    <figure>
                                        <img src="<?php echo esc_url($image); ?>" alt="<?php esc_attr_e('Banner', 'tuturn'); ?>">
                                    </figure>
                                <?php } ?>
                                <img src="<?php echo esc_url($default_dotted_background) ?>" alt="<?php esc_attr_e('Banner', 'tuturn'); ?>">
                                <?php if(!empty($text1) || !empty($text2)){?>
                                    <div class="tu-resultperson">
                                        <?php if (!empty($text1)) { ?>
                                            <h6><?php echo esc_html($text1); ?></h6>
                                        <?php } ?>
                                        <?php if (!empty($text2)) { ?>
                                            <h5><?php echo esc_html($text2); ?></h5>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
    }
    Plugin::instance()->widgets_manager->register(new Tuturn_online_education_platform);
}
