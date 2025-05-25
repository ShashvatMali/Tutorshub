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

    if (!class_exists('TuturnSubmitQuestion')) {
        class TuturnSubmitQuestion extends Widget_Base
        {

            /**
             *
             * @since    1.0.0
             * @access   static
             * @var      base
             */
            public function get_name()
            {
                return 'tuturn_submit_question';
            }

            /**
            *
            * @since    1.0.0
            * @access   static
            * @var      title
            */
            public function get_title()
            {
                return esc_html__('Submit question', 'tuturn');
            }

            /**
            *
            * @since    1.0.0
            * @access   public
            * @var      icon
            */
            public function get_icon()
            {
                return 'eicon-help-o';
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
                $posts = tuturn_elementor_get_posts(array('wpcf7_contact_form'));
                $posts = !empty($posts) ? $posts : array();

                $this->start_controls_section(
                    'content_section',
                    [
                        'label'   => esc_html__('Questions content', 'tuturn'),
                        'tab'     => Controls_Manager::TAB_CONTENT,
                    ]
                );

                $this->add_control(
                    'sec_tagline',
                    [
                        'type'        => Controls_Manager::TEXT,
                        'label'       => esc_html__('Tagline', 'tuturn'),
                        'description' => esc_html__('Add tagline. leave it empty to hide.', 'tuturn'),
                    ]
                );

                $this->add_control(
                    'sec_title',
                    [
                        'type'        => Controls_Manager::TEXT,
                        'label'       => esc_html__('Title', 'tuturn'),
                        'description' => esc_html__('Add title. leave it empty to hide.', 'tuturn'),
                    ]
                );

                $this->add_control(
                    'sec_desc',
                    [
                        'type'        => Controls_Manager::TEXTAREA,
                        'label'       => esc_html__('Description', 'tuturn'),
                        'row'         => 5,
                        'description' => esc_html__('Add title. leave it empty to hide.', 'tuturn'),
                    ]
                );
                $this->add_control(
                    'btn_title',
                    [
                        'type'        => Controls_Manager::TEXT,
                        'label'       => esc_html__('Button title', 'tuturn'),
                        'description' => esc_html__('Add button title. leave it empty to hide.', 'tuturn'),
                    ]
                );
                $this->add_control(
                    'question_form',
                    [
                        'label'       => esc_html__('Form', 'tuturn'),
                        'description' => esc_html__('Choose form', 'tuturn'),
                        'type'        => \Elementor\Controls_Manager::SELECT2,
                        'options'     => $posts,
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
                global $current_user;
                $settings         = $this->get_settings_for_display();
                $sec_tagline      = !empty($settings['sec_tagline']) ? $settings['sec_tagline'] : '';
                $sec_title        = !empty($settings['sec_title']) ? $settings['sec_title'] : '';
                $sec_desc         = !empty($settings['sec_desc']) ? $settings['sec_desc'] : '';
                $btn_title        = !empty($settings['btn_title']) ? $settings['btn_title'] : '';
                $question_form_id = !empty($settings['question_form']) ? $settings['question_form'] : '';?>
                <div class="tu-submit-question tu-faq-section">
                     <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-xl-10 col-md-12">
                                <div class="tu-question-section">
                                    <div class="tu-faq-search_title">
                                        <?php if(!empty($sec_tagline)){ ?>
                                            <h5><?php echo esc_html($sec_tagline); ?></h5>
                                        <?php } 
                                            if(!empty($sec_title)){ ?>
                                                <h2><?php echo esc_html($sec_title); ?></h2>
                                        <?php } ?>
                                        <?php if(!empty($sec_desc)){ ?>
                                            <div class="tu-question_desc">
                                                <p><?php echo esc_html($sec_desc); ?></p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php if (!empty($question_form_id)) { ?>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#submitquestion" class="tu-primbtn-lg"><span><?php echo esc_html($btn_title); ?></span><i class="icon icon-edit-3"></i></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php  if (!empty($question_form_id)) {
                    $form_title = get_the_title($question_form_id);?>
                    <div class="modal fade tu-profilepopup tu-submitpopup" id="submitquestion" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog tu-modaldialog" role="document">
                            <div class="modal-content">
                                <div class="tu-popuptitle">
                                    <h4><?php echo esc_html($form_title); ?></h4>
                                    <a href="javascript:void(0);" class="close"><i class="icon icon-x" data-bs-dismiss="modal"></i></a>
                                </div>
                                <div class="modal-body">
                                    <?php echo do_shortcode('[contact-form-7 id="' . $question_form_id . '"]'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        Plugin::instance()->widgets_manager->register(new TuturnSubmitQuestion);
    }
