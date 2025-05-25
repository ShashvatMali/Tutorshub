<?php
/**
 * Shortcode
 *
 *
 * @package    Tuturn
 * @subpackage Tuturn/elementor/
 * @author     Amentotech <theamentotech@gmail.com>
 */

namespace Elementor;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Tuturn_Instructors')) {
    class Tuturn_Instructors extends Widget_Base
    {

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_element_instructors';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Instructors', 'tuturn');
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
            $categories = tuturn_elementor_get_taxonomies('tuturn-instructor', 'product_cat');
            $categories = !empty($categories) ? $categories : array();
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
                    'label'         => esc_html__('Heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'placeholder'   => esc_html__('Type your Heading here', 'tuturn'),
                    'label_block'   => true
                ]
            );
            $this->add_control(
                'sub_heading',
                [
                    'label'         => esc_html__('Sub Heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'placeholder'   => esc_html__('Type your Sub Heading here', 'tuturn'),
                    'label_block'   => true
                ]
            );
            $this->add_control(
                'heading_desc',
                [
                    'label'         => esc_html__('Description', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXTAREA,
                    'placeholder'   => esc_html__('Type your description here', 'tuturn'),
                ]
            );
            $this->add_control(
                'listing_type',
                [
                    'type' => Controls_Manager::SELECT,
                    'label' => esc_html__('Show instructor by', 'tuturn'),
                    'description'   => esc_html__('Select type to list instructor', 'tuturn'),
                    'default'       => '',
                    'options'       => [
                        ''          => esc_html__('Select instructor listing type', 'tuturn'),
                        'random'    => esc_html__('Random from all instructor', 'tuturn'),
                        'recent'    => esc_html__('Recent from all instructor', 'tuturn'),
                        'ids'       => esc_html__('By IDs', 'tuturn'),
                    ]
                ]
            );
            $this->add_control(
                'instrutor_ids',
                [
                    'label'         => esc_html__( 'Instructor id', 'tuturn' ),
                    'description'   => esc_html__('Please add comma instructor ids.', 'tuturn'),

                    'type' => \Elementor\Controls_Manager::TEXT,
                    'condition' => [
                        'listing_type' => 'ids',
                    ],
                ]
            );
            $this->add_control(
                'no_post_show',
                [
                    'type'      => Controls_Manager::NUMBER,
                    'label'     => esc_html__('No. of post', 'tuturn'),
                    'desc'      => esc_html__('Select no of posts to display.', 'tuturn'),
                    'min'       => 1,
                    'max'       => 500,
                    'step'      => 1,
                    'default'   => 9,
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
                    'placeholder'   => esc_html__( 'https://example.com', 'tuturn' ),
                    'default' => [
                        'url'               => '',
                        'is_external'       => true,
                        'nofollow'          => true,
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
            global $tuturn_settings;
            $meta_query_args            = array();
            $settings                   = $this->get_settings_for_display();
            $heading                    = !empty($settings['main_heading']) ? $settings['main_heading'] : '';
            $sub_heading                = !empty($settings['sub_heading']) ? $settings['sub_heading'] : '';
            $heading_desc               = !empty($settings['heading_desc']) ? $settings['heading_desc'] : '';
            $listing_type               = !empty($settings['listing_type']) ? $settings['listing_type'] : '';
            $instrutor_ids              = !empty($settings['instrutor_ids']) ? $settings['instrutor_ids'] : array();
            $no_post_show               = !empty($settings['no_post_show']) ? $settings['no_post_show'] : 10;
            $button_text                = !empty($settings['button_text']) ? $settings['button_text'] : '';
            $button_text_link           = !empty($settings['button_text_link']['url']) ? $settings['button_text_link']['url'] : 'javascript:void(0)';
            $default_zigzag             = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/zigzag-line.svg');
            $pg_page                    = get_query_var('paged') ? get_query_var('paged') : 1;
            $instructor_rand            = rand(99, 9999);
            $selected_ids               = !empty($instrutor_ids) ? explode(",",$instrutor_ids) : '';
            $identity_verification_listings = !empty($tuturn_settings['identity_verification_listings']) ?  $tuturn_settings['identity_verification_listings'] : false;
            $identity_verification          = !empty($tuturn_settings['identity_verification']) ?  $tuturn_settings['identity_verification'] : false;
            
            // prepared query args
            $tuturn_args = array(
                'post_type'         => 'tuturn-instructor',
                'posts_per_page'    => $no_post_show,
                'paged'             => $pg_page,
                'post_status'       => 'publish',
            );
            //order by
            if(!empty($listing_type) && ( $listing_type == 'random' )){
                $tuturn_args['orderby'] = 'rand';
            }

            if(!empty($listing_type) && ( $listing_type == 'ids' )){
                $tuturn_args['post__in'] =  $selected_ids;
            }

            if(!empty($listing_type) && ( $listing_type == 'recent' )){
                $tuturn_args['orderby']    = 'ID';
                $tuturn_args['order']      = 'DESC';
            }

            if(!empty($identity_verification_listings) && !empty($identity_verification) && ( $identity_verification == 'tutors' || $identity_verification == 'both' )){
                $meta_query_args[]  = array(
                    'key'       => 'identity_verified',
                    'value'     => 'yes',
                    'compare'   => '=',
                );   
                $query_relation           = array('relation' => 'AND',);
                $meta_query_args          = array_merge($query_relation, $meta_query_args);
                $tuturn_args['meta_query'] = $meta_query_args;
            }

            //specific posts
            $tuturn_args['meta_key']        = '_is_verified';
            $tuturn_args['meta_value']      = 'yes';
            $tuturn_args['meta_compare']    = '=';
           

            $slider_direction   = 'ltr';
            if ( is_rtl() ) {
                $slider_direction   = 'rtl';
            }
            
            $tuturn_query = new \WP_Query(apply_filters('tuturn_instructor_listings_args', $tuturn_args));?>
            <?php if($tuturn_query->have_posts()){ ?>
            <div class="tu-main-section instructor-listing-wrapper">
                <div class="container">
                    <?php if(!empty($heading) || !empty($sub_heading) || !empty($heading_desc)){ ?>
                    <div class="row justify-content-center">
                        <div class="col-md-12 col-lg-8">
                            <div class="tu-maintitle text-center">
                                <?php if(!empty($heading)){ ?>
                                    <img src="<?php echo esc_url($default_zigzag); ?>" alt="<?php esc_attr_e('Images','tuturn'); ?>">
                                <?php } ?>
                                <?php if(!empty($sub_heading)){ ?>
                                    <h4><?php echo esc_html($sub_heading); ?></h4>
                                <?php } ?>
                                <?php if(!empty($heading)){ ?>
                                    <h2><?php echo esc_html($heading); ?></h2>
                                <?php } ?>
                                <?php if(!empty($heading_desc)){ ?>
                                    <p><?php echo esc_html($heading_desc); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php
                    $id_class = 'tu-featurelist'; ?>
                    <div id="<?php echo esc_attr_e($id_class); ?>-<?php echo esc_attr($instructor_rand);?>" class="splide <?php echo esc_attr($id_class); ?> tu-splidedots">
                        <div class="splide__track">
                            <ul class="splide__list">
                                <?php 
                                while($tuturn_query->have_posts()){
                                    $tuturn_query->the_post(); ?>
                                        <li class="splide__slide">
                                            <?php tuturn_get_template_part( 'instructor-search/instructor-search-view-v4');?>
                                        </li>
                                    <?php
                                }
                                wp_reset_postdata();
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="tu-mainbtn">
                        <a href="<?php echo esc_url($button_text_link); ?>" class="tu-primbtn-lg"><span><?php echo esc_html($button_text); ?></span><i class="icon icon-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <script>
            jQuery(document).ready(function () {
                let instrucotrslider = document.querySelector("#<?php echo esc_js($id_class);?>-<?php echo esc_js($instructor_rand);?>");
                if(instrucotrslider !== null){
                    var splideslider = new Splide( "#<?php echo esc_js($id_class);?>-<?php echo esc_js($instructor_rand);?>", {
                        type : "loop",
                        direction: "<?php echo esc_js($slider_direction);?>",
                        perPage: 4,
                        perMove: 1,
                        gap: 24,
                        pagination: true,
                        arrows: false,
                        breakpoints: {
                            1400: {
                                perPage: 3,
                            },
                            1199: {
                                perPage: 2,
                            },
                            767: {
                                perPage: 1,
                            }
                        }
                    } );
                    splideslider.mount();
                }
            });
            </script>
            <?php 
            }
        }
    }
    Plugin::instance()->widgets_manager->register(new Tuturn_Instructors);
}