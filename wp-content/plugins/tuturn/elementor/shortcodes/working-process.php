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

    if (!class_exists('Tuturn_index_operate')) {
        class Tuturn_index_operate extends Widget_Base
        {

            /**
             *
             * @since    1.0.0
             * @access   static
             * @var      base
             */
            public function get_name()
            {
                return 'tuturn_working_process';
            }

            /**
            *
            * @since    1.0.0
            * @access   static
            * @var      title
            */
            public function get_title()
            {
                return esc_html__('Working process', 'tuturn');
            }

            /**
            *
            * @since    1.0.0
            * @access   public
            * @var      icon
            */
            public function get_icon()
            {
                return 'eicon-slider-video';
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
                    'image_section',
                    [
                        'label'     => esc_html__('Image section', 'tuturn'),
                        'tab'       => Controls_Manager::TAB_CONTENT,
                    ]
                );

                $this->add_control(
                    'image',
                    [
                        'type'          => Controls_Manager::MEDIA,
                        'label'         => esc_html__('Display image', 'tuturn'),
                        'description'   => esc_html__('Add display image.', 'tuturn'),
                        'default' => [
                            'url' => \Elementor\Utils::get_placeholder_image_src(),
                        ],
                    ]
                );
                $this->end_controls_section();

                $this->start_controls_section(
                    'content_section',
                    [
                        'label'     => esc_html__('Content section', 'tuturn'),
                        'tab'       => Controls_Manager::TAB_CONTENT,
                    ]
                );
                $this->add_control(
                    'section_tagline',
                    [
                        'type'        => Controls_Manager::TEXT,
                        'label'       => esc_html__('Tagline', 'tuturn'),
                        'description' => esc_html__('Add tagline or leave it empty to hide.', 'tuturn'),
                        'label_block'   => true,
                    ]
                );
                $this->add_control(
                    'section_title',
                    [
                        'type'        => Controls_Manager::TEXT,
                        'label'       => esc_html__('Title', 'tuturn'),
                        'description' => esc_html__('Add title or leave it empty to hide.', 'tuturn'),
                        'label_block'   => true,
                    ]
                );
                $this->add_control(
                    'process_steps',
                    [
                        'label'     => esc_html__('Add process steps', 'tuturn'),
                        'type'      => Controls_Manager::REPEATER,
                        'fields'    => [
                            [
                                'name'          => 'icon',
                                'type'          => Controls_Manager::TEXT,
                                'label'         => esc_html__('Icon class', 'tuturn'),
                                'description'   => esc_html__('you can find icon here. https://feathericons.com/', 'tuturn'),
                                'label_block'   => true,
                            ],
                            [
                                'name'          => 'title',
                                'type'          => Controls_Manager::TEXT,
                                'label'         => esc_html__('Section title', 'tuturn'),
                                'description'   => esc_html__('Add title. leave it empty to hide.', 'tuturn'),
                                'label_block'   => true,
                            ],
                            [
                                'name'          => 'desc',
                                'type'          => Controls_Manager::TEXTAREA,
                                'label'         => esc_html__('Description', 'tuturn'),
                                'description'   => esc_html__('Add description. leave it empty to hide.', 'tuturn'),
                            ]
                        ]
                    ]
                );
                $this->end_controls_section();

            }

            protected function render()
            {
                $settings         = $this->get_settings_for_display();
                $image            = !empty($settings['image']['url']) ? $settings['image']['url'] : '';
                $section_tagline  = !empty($settings['section_tagline']) ? $settings['section_tagline'] : '';
                $section_title    = !empty($settings['section_title']) ? $settings['section_title'] : '';
                $process_steps    = !empty($settings['process_steps']) ? $settings['process_steps'] : array();
                $logo_image         = TUTURN_DIRECTORY_URI . 'public/images/zigzag-line.svg';

                $image_bg   = '';
                if(!empty($image)){
                    $image_bg   = ' style="background-image: url('.esc_url($image).');"';
                }
                ?>
                <div>
                    <div class="tu-processing-holder"<?php echo do_shortcode($image_bg);?>>
                        <div class="tu-processing-img"></div>
                        <div class="tu-betterresult tu-processing-content">
                            <?php if(!empty($section_tagline) || !empty($section_title) || !empty($logo_image)){?>
                                <div class="tu-maintitle">
                                    <?php if(!empty($logo_image)){?>
                                        <img src="<?php echo esc_url($logo_image)?>" alt="<?php esc_attr_e('image','tuturn')?>">
                                    <?php } 
                                    if(!empty($section_tagline)){?>
                                        <h4><?php echo esc_html($section_tagline)?></h4>
                                    <?php } 
                                    if(!empty($section_title)){?>
                                        <h2><?php echo esc_html($section_title) ?></h2>
                                    <?php } ?>
                                </div>
                            <?php } 
                            if(!empty($process_steps)){?>
                                <ul class="tu-processing-list">
                                    <?php
                                    foreach ($process_steps as $value) {
                                        $title    = !empty($value['title']) ? $value['title'] : '';
                                        $icon_class     = !empty($value['icon']) ? $value['icon'] : '';
                                        $description    = !empty($value['desc']) ? $value['desc'] : '';
                                        
                                        if(!empty($title) || !empty($icon_class) || !empty($description)){?>
                                            <li>
                                                <?php if(!empty($title) || !empty($icon_class)){?>
                                                    <div class="tu-processinglist-info">
                                                        <?php if(!empty($icon_class)){?>
                                                            <i class="<?php echo esc_attr($icon_class)?>"></i>
                                                        <?php } 
                                                        if(!empty($title)) {?>
                                                            <h4><?php echo esc_html($title)?></h4>
                                                        <?php } ?>
                                                    </div>
                                                <?php }
                                                if(!empty($description)) {?>
                                                    <p><?php echo esc_html($description)?></p>
                                                <?php } ?>
                                            </li>
                                        <?php }
                                    } ?>
                                </ul>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        Plugin::instance()->widgets_manager->register(new Tuturn_index_operate);
    }
