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

if (!class_exists('Tuturn_how_it_work')) {
    class Tuturn_how_it_work extends Widget_Base
    {

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_how_it_work';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('How it work', 'tuturn');
        }

        /**
         *
         * @since    1.0.0
         * @access   public
         * @var      icon
         */
        public function get_icon()
        {
            return 'eicon-person';
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
                    'label' => esc_html__('Content', 'tuturn'),
                    'tab'   => Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'sub-title',
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
            

            $this->end_controls_section();

                   //Content
            $this->start_controls_section(
                'step_section',
                [
                    'label' => esc_html__('How it work steps', 'tuturn'),
                    'tab'   => Controls_Manager::TAB_CONTENT,
                ]
            );
            $this->add_control(
                'steps',
                [
                    'label'   => esc_html__('Add steps', 'tuturn'),
                    'type'    => Controls_Manager::REPEATER,
                    'fields' => [      
                        [
                            'name'          => 'image',
                            'type'          => Controls_Manager::MEDIA,
                            'label'         => esc_html__('Upload image', 'tuturn'),
                            'description'   => esc_html__('Upload image', 'tuturn'),
                            'default' => [
                                'url' => \Elementor\Utils::get_placeholder_image_src(),
                            ],
                        ],
                        [
                            'name'        => 'number',
                            'type'        => Controls_Manager::TEXT,
                            'label'       => esc_html__('Step number', 'tuturn'),
                            'description' => esc_html__('Add step number.', 'tuturn'),
                            'label_block' => true,
                        ],
                        [
                            'name'        => 'step-title',
                            'type'        => Controls_Manager::TEXT,
                            'label'       => esc_html__('Step title', 'tuturn'),
                            'description' => esc_html__('Add step tile.', 'tuturn'),
                            'label_block' => true,
                        ],
                        [
                            'name'        => 'step-description',
                            'type'        => Controls_Manager::TEXTAREA,
                            'label'       => esc_html__('Step descriptiopn', 'tuturn'),
                            'description' => esc_html__('Add step description.', 'tuturn'),
                            'label_block' => true,
                        ]
                    ]
                ]
            );

            $this->end_controls_section();

        }

        protected function render()
        {
            $settings       = $this->get_settings_for_display();
            $title          = !empty($settings['title']) ? $settings['title'] : '';
            $steps          = !empty($settings['steps']) ? $settings['steps'] : '';
            $sub_title      = !empty($settings['sub-title']) ? $settings['sub-title'] : '';
            $description    = !empty($settings['description']) ? $settings['description'] : '';
            $logo_image     = TUTURN_DIRECTORY_URI . 'public/images/zigzag-line.svg';
            ?>
            <div class="tu-main-section">
                <div class="container">
                    <?php if(!empty($title) || !empty($sub_title) || !empty($description)) {?>
                        <div class="row justify-content-center">
                            <div class="col-md-12 col-lg-8">
                                <div class="tu-maintitle text-center">
                                    <?php if(!empty($logo_image)){?>
                                        <img src="<?php echo esc_url($logo_image)?>" alt="<?php echo esc_attr($sub_title);?>">
                                    <?php } ?>
                                    <?php if(!empty($sub_title)) {?>
                                        <h4><?php echo esc_html($sub_title);?></h4>
                                    <?php } 
                                    if(!empty($title)) {?>
                                        <h2><?php echo esc_html($title);?></h2>
                                    <?php } 
                                    if(!empty($description)) {?>
                                        <p><?php echo esc_html($description);?></p>
                                    <?php } ?>
                                </div>
                            </div> 
                        </div>
                    <?php } ?>
                    <?php if (!empty($steps)) { ?>
                        <div class="row tu-howit-steps gy-4">
                            <?php
                            $counter    = 1;
                            foreach ($steps as $step) {
                                $step_number        = !empty($step['number']) ? $step['number'] : ''; 
                                $step_title         = !empty($step['step-title']) ? $step['step-title'] : '';
                                $image              = !empty($step['image']['url']) ? $step['image']['url'] : '';  
                                $step_description   = !empty($step['step-description']) ? $step['step-description'] : '';   
                     
                                if(!empty($step_number) || !empty($step_title) || !empty($step_description) || !empty($image)){
                                    if(!empty($counter) && $counter === 1){
                                        $icon_class = "tu-step-tag tu-orange-bgclr";
                                    }
                                    if(!empty($counter) && $counter === 2){
                                        $icon_class = "tu-step-tag tu-purple-bgclr";
                                    }
                                    if(!empty(!empty($counter) && $counter === 3)) {
                                        $icon_class = "tu-step-tag tu-green-bgclr";
                                    }
                                    ?>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="tu-howit-steps_content">
                                            <?php if(!empty($image)) {?>
                                                <figure><img src="<?php echo esc_url($image)?>" alt="<?php echo esc_html($step_number)?>"></figure>
                                            <?php  }
                                            if(!empty($step_number) || !empty($step_title) || !empty($step_description)) {?>
                                                <div class="tu-howit-steps_info">
                                                    <?php if(!empty($step_number)){?>
                                                        <span class="<?php echo esc_attr($icon_class)?>"><?php echo esc_html($step_number)?></span>
                                                    <?php }
                                                    if(!empty($step_title)) {?>
                                                        <h5><?php echo esc_html($step_title)?></h5>
                                                    <?php } 
                                                    if(!empty($step_description)) {?>
                                                        <p><?php echo esc_html($step_description)?> </p>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php 
                                $counter++ ; 
                            } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php 
        }
        
    }

    Plugin::instance()->widgets_manager->register(new Tuturn_how_it_work);
}