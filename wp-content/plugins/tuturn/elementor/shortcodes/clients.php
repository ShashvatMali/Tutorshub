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

if (!class_exists('Tuturn_about_us_clients')) {
    class Tuturn_about_us_clients extends Widget_Base
    {

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_element_about_us_client';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Clients', 'tuturn');
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

        protected function register_controls(){
            //Content
            $this->start_controls_section(
                'content_section',
                [
                    'label'     => esc_html__('Content', 'tuturn'),
                    'tab'       => Controls_Manager::TAB_CONTENT,
                ]
            );
            $this->add_control(
                'clients',
                [
                    'label'     => esc_html__('Add client', 'tuturn'),
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
        protected function render() {
            $settings   = $this->get_settings_for_display();
            $clients    = !empty($settings['clients']) ? $settings['clients'] : array(); ?>
            <?php if(!empty($clients)) {?>  
                <div>
                    <div class="tu-brand">
                        <div class="container">
                            <ul class="tu-brand_list">
                                <?php foreach ($clients as $client)   {
                                    $client_img = !empty($client['image']['url']) ? $client['image']['url'] : '';
                                    if (!empty($client_img)) { ?>
                                        <li><a href="javascript:void(0);"><img src="<?php echo esc_url($client_img) ;?>" alt="<?php esc_attr_e('Client image','tuturn'); ?>"></a></li>
                                    <?php
                                    }
                                } ?>
                            </ul>  
                        </div>
                    </div>
                </div> 
            <?php
            }   
        } 
    }
    Plugin::instance()->widgets_manager->register(new Tuturn_about_us_clients);
}