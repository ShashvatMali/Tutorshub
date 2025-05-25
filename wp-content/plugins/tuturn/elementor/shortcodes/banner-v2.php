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

if (!class_exists('Tuturn_home_banner_v2')) {
    class Tuturn_home_banner_v2 extends Widget_Base
    {
        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'Tuturn_home_banner_v2';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Home banner v2', 'tuturn');
        }

        /**
         *
         * @since    1.0.0
         * @access   public
         * @var      icon
         */
        public function get_icon()
        {
            return 'eicon-banner';
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
            $categories = tuturn_elementor_get_taxonomies('product', 'product_cat');
            $posts = tuturn_elementor_get_posts(array('page'));
            $posts = !empty($posts) ? $posts : array();
            $this->start_controls_section(
                'content_section',
                [
                    'label'     => esc_html__('Content', 'tuturn'),
                    'tab'       => Controls_Manager::TAB_CONTENT,
                ]
            );
            $this->add_control(
                'banner-image-1',
                [
                    'type'        => Controls_Manager::MEDIA,
                    'label'       => esc_html__(' image', 'tuturn'),
                    'description' => esc_html__('Add an image.', 'tuturn'),
                    'default' => [
                        'url' => \Elementor\Utils::get_placeholder_image_src(),
                    ],
                ]
            );
            $this->add_control(
                'banner-image-2',
                [
                    'type'        => Controls_Manager::MEDIA,
                    'label'       => esc_html__(' image', 'tuturn'),
                    'description' => esc_html__('Add right bottom image.', 'tuturn'),
                    'default' => [
                        'url' => \Elementor\Utils::get_placeholder_image_src(),
                    ],
                ]
            );
            $this->add_control(
                'banner-image-3',
                [
                    'type'        => Controls_Manager::MEDIA,
                    'label'       => esc_html__(' image', 'tuturn'),
                    'description' => esc_html__('Add left bottom image.', 'tuturn'),
                    'default' => [
                        'url' => \Elementor\Utils::get_placeholder_image_src(),
                    ],
                ]
            );

            $this->add_control(
                'main_heading',
                [
                    'label'         => esc_html__('Heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::WYSIWYG,
                    'default'       => esc_html__('Default title', 'tuturn'),
                    'placeholder'   => esc_html__('Type your title here', 'tuturn'),
                ]
            );
            $this->add_control(
                'labels_texts',
                [
                    'label'         => esc_html__('Add multiple label text', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'placeholder'   => esc_html__('Add multiple label text with "," seprated', 'tuturn'),
                    'description'   => esc_html__('Add multiple label text with "," seprated', 'tuturn'),
                ]
            );
            $this->add_control(
                'after_heading_button_link',
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
            $this->add_control(
                'sub_heading',
                [
                    'label'         => esc_html__('Sub heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXTAREA,
                    'default'       => esc_html__('You can also join as parent to explore Join today', 'tuturn'),
                    'placeholder'   => esc_html__('Type your sub heading here', 'tuturn'),
                ]
            );
            $this->add_control(
                'student_button',
                [
                    'label' => esc_html__('Student Button', 'tuturn'),
                    'type'  => \Elementor\Controls_Manager::TEXT,

                ]
            );
            $this->add_control(
                'student_button_link',
                [
                    'type'          => Controls_Manager::SELECT2,
                    'label'         => esc_html__('Select page', 'tuturn'),
                    'desc'          => esc_html__('Select page to navigation signup.', 'tuturn'),
                    'options'       => $posts,
                    'multiple'      => false,
                    'label_block'   => true,
                ]
            );
            $this->add_control(
                'intructor_button',
                [
                    'label'     => esc_html__('Instructor Button', 'tuturn'),
                    'type'      => \Elementor\Controls_Manager::TEXT,

                ]
            );
            $this->add_control(
                'intructor_button_link',
                [
                    'type'          => Controls_Manager::SELECT2,
                    'label'         => esc_html__('Select page', 'tuturn'),
                    'desc'          => esc_html__('Select page to navigation signup.', 'tuturn'),
                    'options'       => $posts,
                    'multiple'      => false,
                    'label_block'   => true,
                ]
            );
            $this->add_control(
                'small_image_text',
                [
                    'type'        => Controls_Manager::TEXT,
                    'label'       => esc_html__('Small image text', 'tuturn'),
                    'description' => esc_html__('Add Text.', 'tuturn'),
                ]
            );
            $this->add_control(
                'after_buttons_heading',
                [
                    'label'         => esc_html__('After Buttons heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXTAREA,
                    'default'       => esc_html__('You can also join as parent to explore Join today', 'tuturn'),
                    'placeholder'   => esc_html__('Type your sub heading here', 'tuturn'),
                ]
            );
            $this->add_control(
                'join_text',
                [
                    'type'        => Controls_Manager::TEXT,
                    'label'       => esc_html__('Join today text', 'tuturn'),
                    'description' => esc_html__('Add Text.', 'tuturn'),
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
            $settings                   = $this->get_settings_for_display();
            $heading                    = !empty($settings['main_heading']) ? $settings['main_heading'] : '';
            $after_heading_button_link  = !empty($settings['after_heading_button_link']['url']) ? $settings['after_heading_button_link']['url'] : '';           
            $labels_texts               = !empty($settings['labels_texts']) ? $settings['labels_texts'] : '';
            $sub_heading                = !empty($settings['sub_heading']) ? $settings['sub_heading'] : '';
            $student_button             = !empty($settings['student_button']) ? $settings['student_button'] : '';
            $student_button_link        = !empty($settings['student_button_link']) ? $settings['student_button_link'] : '';
            $student_button_link        = !empty(get_permalink($student_button_link)) ? get_permalink($student_button_link) : '';
            $intructor_button           = !empty($settings['intructor_button']) ? $settings['intructor_button'] : '';
            $intructor_button_link      = !empty($settings['intructor_button_link']) ? $settings['intructor_button_link'] : '';
            $intructor_button_link      = !empty(get_permalink($intructor_button_link)) ? get_permalink($intructor_button_link) : '';
            $after_buttons_heading      = !empty($settings['after_buttons_heading']) ? $settings['after_buttons_heading'] : '';
            $small_image_text           = !empty($settings['small_image_text']) ? $settings['small_image_text'] : '';
            $default_knob_image         = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/knob_line.svg');
            $banner_image_1             = !empty($settings['banner-image-1']['url']) ? $settings['banner-image-1']['url'] : '';
            $banner_image_2             = !empty($settings['banner-image-2']['url']) ? $settings['banner-image-2']['url'] : '';
            $banner_image_3             = !empty($settings['banner-image-3']['url']) ? $settings['banner-image-3']['url'] : '';
            $join_text                  = !empty($settings['join_text']) ? $settings['join_text'] : '';
            $labels_texts               = explode(",",$labels_texts);
            $data_labels                = implode(',', array_map(function($val){return sprintf("'%s'", $val);}, $labels_texts));?>
            <div class="tu-bannerv2">
                <div class="tu-particles">
                    <div id="tu-particlev2"></div>
                </div>
                <?php if(!empty($banner_image_1 )) {?>
                    <div class="tu-bannerv2_img">
                        <img src="<?php echo esc_url($banner_image_1); ?>" alt="<?php esc_attr_e('Banner image', 'tuturn') ?>">
                    </div>
                <?php } ?>
                <?php if(!empty($banner_image_2)){?>
                    <div class="tu-dottedimage">
                        <img src="<?php echo esc_url($banner_image_2)?>" alt="<?php esc_attr_e('Image' ,'tuturn') ?>">
                    </div>
                <?php } ?>
                <?php if(!empty($banner_image_3)){?>
                    <div class="tu-linedimage">
                        <img src="<?php echo esc_url($banner_image_3)?>" alt="<?php esc_attr_e('Image','tuturn')?>">
                    </div>
                <?php } ?>
                <div class="container">
                    <div class="row g-0">
                        <div class="col-xl-6">
                            <div class="tu-banner_title">
                                <?php if(!empty($heading)){
                                    $heading   = str_replace('{{','<span>',$heading);
                                    $heading   = str_replace('}}','</span>',$heading);
                                    echo do_shortcode($heading, false); 
                                } ?>
                                <div class="tu-changeable-content">
                                    <span class="tu-bannerinfo tu-typev2"></span>
                                </div>
                                <?php
                                if(!empty($sub_heading)){?>
                                    <p><?php echo esc_html($sub_heading); ?></p>
                                <?php } ?>
                                <ul class="tu-banner_list">
                                    <?php if (!empty($student_button) || !empty($small_image_text)) { ?>
                                        <li>
                                            <?php if (!empty($small_image_text)) { ?>
                                                <div class="tu-starthere">
                                                    <span><?php echo esc_html($small_image_text); ?></span>
                                                    <img src="<?php echo esc_url($default_knob_image); ?>" alt="<?php esc_attr_e('image', 'tuturn'); ?>">
                                                </div>
                                            <?php }
                                            if(!empty($student_button)){?>
                                                <a href="<?php echo esc_url($student_button_link) ?>" class="tu-primbtn tu-primbtn-gradient"><span><?php echo esc_html($student_button); ?></span><i class="icon icon-chevron-right"></i></a>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                    <?php if (!empty($intructor_button)) { ?>
                                        <li><a href="<?php echo esc_url($intructor_button_link)?>" class="tu-secbtn"><span><?php echo esc_html($intructor_button) ?></span>
                                            </a></li>
                                    <?php } ?>
                                </ul>
                                <?php if (!empty($after_buttons_heading)) { ?>
                                    <div class="tu-banner_explore">
                                        <i class="icon icon-shield"></i>
                                        <?php if(!empty($after_buttons_heading) || !empty($join_text)){?>
                                            <p>
                                                <?php echo do_shortcode($after_buttons_heading); ?>
                                                <?php if(!empty($join_text)){?>
                                                        <a href="<?php echo esc_url($intructor_button_link)?>"> <?php echo esc_html($join_text)?></a>
                                                <?php }?>
                                            </p>
                                        <?php } ?>

                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $typed_script   ="
                let lo_typetwo = document.querySelector('.tu-typev2')
            
                if( lo_typetwo !== null){
                   var typed = new Typed('.tu-typev2', {
                    strings:[".$data_labels."],
                    typeSpeed: 100,
                    backSpeed:100,
                    loop: true,
                    showCursor: false,
                  });
                } 
                jQuery( document ).ready(function($) {
                    let elementor_first_section = $('section.elementor-section').first().find('.tu-bannerv2');
                    if(elementor_first_section.length>0){
                        jQuery('header').addClass('tu-headerv2');
                    }                   
                });";
                wp_add_inline_script('typed', $typed_script, 'after');

                $community_ads_particles = '
                // particals
                var tu_particle = document.getElementById("tu-particlev2");
                if (tu_particle !== null) {
                    /* ---- particles.js config ---- */
                    particlesJS("tu-particlev2", {
                        "particles": {
                            "number": {
                            "value": 20,
                            },
                            "color": {
                            "value": ["#1DA1F2","#6A307D","#F97316"],
                            },
                            "opacity": {
                                "value": 0.4,
                                "random": true,
                            
                            },
                            size: {
                                value: 12,
                                random: true,
                            },
                            "line_linked": {
                                "enable": false,
                            },
                            "move": {
                                "enable": true,
                                "speed": 3,
                            }
                        },
                        "interactivity": {
                            "enable": false,
                            "detect_on": "canvas",
                            "events": {
                                "onhover": {
                                    "enable": false,
                                    "mode": "grab"
                                },
                                "onclick": {
                                    "enable": false,
                                    "mode": "push",
                                    "direction": "top-right",
                                },
                            },
                        },
                    });
                }';
                wp_add_inline_script('particles', $community_ads_particles, 'after');
        }
    }
    Plugin::instance()->widgets_manager->register(new Tuturn_home_banner_v2);
}
