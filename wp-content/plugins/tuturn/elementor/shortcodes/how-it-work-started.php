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

if (!class_exists('Tuturn_how_it_work_get_started')) {
    class Tuturn_how_it_work_get_started extends Widget_Base
    {
        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_how_work_get_started';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('How it work get started', 'tuturn');
        }

        /**
         *
         * @since    1.0.0
         * @access   public
         * @var      icon
         */
        public function get_icon()
        {
            return 'eicon-kit-details';
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
            $posts = tuturn_elementor_get_posts(array('page'));
            $posts = !empty($posts) ? $posts : array();
            //Content
            $this->start_controls_section(
                'content_section',
                [
                    'label' => esc_html__('Content', 'tuturn'),
                    'tab'   => Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'tagline',
                [
                    'type'          => Controls_Manager::TEXT,
                    'label'         => esc_html__('Section tagline', 'tuturn'),
                    'description'   => esc_html__('Add tagline text. leave it empty to hide.', 'tuturn'),
                    'label_block'   => true,
                ]
            );

            $this->add_control(
                'title',
                [
                    'type'          => Controls_Manager::TEXT,
                    'label'         => esc_html__('Section title', 'tuturn'),
                    'description'   => esc_html__('Add title text. leave it empty to hide.', 'tuturn'),
                    'label_block'   => true,
                ]
            );

            $this->add_control(
                'description',
                [
                    'type'          => Controls_Manager::TEXTAREA,
                    'label'         => esc_html__('Description', 'tuturn'),
                    'description'   => esc_html__('Add description. leave it empty to hide.', 'tuturn'),
                ]
            );

            $this->add_control(
                'text_start_student',
                [
                    'type'          => Controls_Manager::TEXT,
                    'label'         => esc_html__('Button text', 'tuturn'),
                    'description'   => esc_html__('Add button text.', 'tuturn'),
                    'label_block'   => true,
                ]
            );

            $this->add_control(
                'start_student_link',
                [
                    'type'          => Controls_Manager::SELECT2,
                    'label'         => esc_html__('Select page', 'tuturn'),
                    'desc'          => esc_html__('Select page to button navigation.', 'tuturn'),
                    'options'       => $posts,
                    'multiple'      => false,
                    'label_block'   => true,
                ]
            );

            $this->add_control(
                'text_join_instructor',
                [
                    'type'          => Controls_Manager::TEXT,
                    'label'         => esc_html__('Button text', 'tuturn'),
                    'description'   => esc_html__('Add button text.', 'tuturn'),
                    'label_block'   => true,
                ]
            );

            $this->add_control(
                'join_instructor_link',
                [
                    'type'          => Controls_Manager::SELECT2,
                    'label'         => esc_html__('Select page', 'tuturn'),
                    'desc'          => esc_html__('Select page to button navigation.', 'tuturn'),
                    'options'       => $posts,
                    'multiple'      => false,
                    'label_block'   => true,
                ]
            );

            $this->end_controls_section();
        }

        protected function render()
        {
            $settings             = $this->get_settings_for_display();
            $tagline              = !empty($settings['tagline']) ? $settings['tagline'] : '';
            $title                = !empty($settings['title']) ? $settings['title'] : '';
            $description          = !empty($settings['description']) ? $settings['description'] : '';
            $text_start_student   = !empty($settings['text_start_student']) ? $settings['text_start_student'] : '';
            $text_join_instructor = !empty($settings['text_join_instructor']) ? $settings['text_join_instructor'] : '';
            $start_student        = !empty($settings['start_student_link']) ? $settings['start_student_link'] : array();
            $start_student_link   = !empty(get_permalink($start_student)) ? get_permalink($start_student) : '';
            $join_instructor      = !empty($settings['join_instructor_link']) ? $settings['join_instructor_link'] : array();
            $join_instructor_link = !empty(get_permalink($join_instructor)) ? get_permalink($join_instructor) : '';
            $logo_image     = TUTURN_DIRECTORY_URI . 'public/images/zigzag-line.svg';
            ?>
            <div class="tu-main-section">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12 col-lg-8">
                            <?php if(!empty($tagline) || !empty($title) || !empty($description)){?>
                                <div class="tu-maintitle text-center">
                                    <?php if(!empty($logo_image)){?>
                                        <img src="<?php echo esc_url($logo_image)?>" alt="<?php echo esc_attr($tagline);?>">
                                    <?php } ?>
                                    <?php if(!empty($tagline)){?>
                                        <h4><?php echo esc_html($tagline)?></h4>
                                    <?php } 
                                    if(!empty($title)){?>
                                        <h2><?php echo esc_html($title) ?></h2>
                                    <?php } 
                                    if(!empty($description)){?>
                                        <p><?php echo esc_html($description)?></p>
                                    <?php } ?>
                                </div>
                            <?php } 

                            if( !is_user_logged_in(  ) || current_user_can('administrator')  ){
                                if(!empty($text_join_instructor) || !empty($text_start_student)){ ?>
                                    <ul class="tu-banner_list tu-banner_list-two">
                                        <?php if(!empty($text_start_student)){?>
                                            <li>
                                                <a href="<?php echo esc_url($start_student_link)?>" class="tu-primbtn tu-primbtn-gradient"><span><?php echo esc_html($text_start_student)?></span><i class="icon icon-chevron-right"></i></a>
                                            </li>
                                        <?php } if(!empty($text_join_instructor)){?>
                                            <li>
                                                <a href="<?php echo esc_url($join_instructor_link)?>" class="tu-secbtn"><span><?php echo esc_html($text_join_instructor)?></span><em><?php esc_html_e('It’s Free!','tuturn')?></em></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php }
                            } ?>
                        </div>
                    </div>
                </div>
            </div> 
            <?php 
        }
    }

    Plugin::instance()->widgets_manager->register(new Tuturn_how_it_work_get_started);
}
