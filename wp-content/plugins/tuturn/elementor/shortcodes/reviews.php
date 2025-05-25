<?php
/**
 * Shortcode
 *
 *
 * @package    Tuturn
 * @subpackage Tuturn/admin/elementor/shortcodes/
 * @author     Amentotech <theamentotech@gmail.com>
 */

namespace Elementor;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Tuturm_reviews')) {
    class Tuturm_reviews extends Widget_Base
    {

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_element_reviews';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Tuturn | Reviews', 'tuturn');
        }

        /**
         *
         * @since    1.0.0
         * @access   public
         * @var      icon
         */
        public function get_icon()
        {
            return 'eicon-post-slider';
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
                    'label'         => esc_html__('Heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::WYSIWYG,
                    'placeholder'   => esc_html__('Type your main heading here', 'tuturn'),
                ]
            );

            $this->add_control(
                'view_type',
                [
                    'label'         => esc_html__( 'Choose View', 'tuturn' ),
                    'type'          => \Elementor\Controls_Manager::SELECT2,
                    'multiple'      => false,
                    'options'       => [
                        'v1'        => esc_html__( 'View One', 'tuturn' ),
                        'v2'        => esc_html__( 'View Two', 'tuturn' ),
                    ],
                    'default' => 'v1',
                ]
            );
            $this->add_control(
                'reviews',
                [
                    'label'     => esc_html__('Add Platforms', 'tuturn'),
                    'type'      => Controls_Manager::REPEATER,
                    'fields' => [
                        [
                            'name'          => 'review_media',
                            'type'          => Controls_Manager::MEDIA,
                            'label'         => esc_html__('Upload image', 'tuturn'),
                            'description'   => esc_html__('Upload image or leave it empty to hide.', 'tuturn'),
                            'default' => [
                                'url' => \Elementor\Utils::get_placeholder_image_src(),
                            ],
                        ],
                        [
                            'name'          => 'review_heading',
                            'type' => \Elementor\Controls_Manager::TEXT,
                            'label' => esc_html__( 'Review heading', 'tuturn' ),
                            'description'   => esc_html__('Add review content or leave it empty to hide.', 'tuturn'),

                        ],
                        [
                            'name'          => 'review_content',
                            'type'          => Controls_Manager::TEXTAREA,
                            'label'         => esc_html__('Review content', 'tuturn'),
                            'description'   => esc_html__('Add review content or leave it empty to hide.', 'tuturn'),
                        ],
                        [
                            'name'          => 'review_name',
                            'type'          => Controls_Manager::TEXT,
                            'label'         => esc_html__('Reviewer name', 'tuturn'),
                            'description'   => esc_html__('Add name or leave it empty to hide.', 'tuturn'),
                        ],
                        [
                            'name'          => 'review_address',
                            'type'          => Controls_Manager::TEXTAREA,
                            'label'         => esc_html__('Reviewer address', 'tuturn'),
                            'description'   => esc_html__('Add address or leave it empty to hide.', 'tuturn'),
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
            $settings               = $this->get_settings_for_display();
            $main_heading           = !empty($settings['main_heading']) ? $settings['main_heading'] : '';
            $view_type              = !empty($settings['view_type']) ? $settings['view_type'] : 'v1';
            $reviews                = !empty($settings['reviews']) ? $settings['reviews'] : array();
            $default_pattern        = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/pattren.svg');
            $comma_pattern          = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/commav2.png');
            $default_thumbnail      = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/placeholder-default.jpg');
            $default_placeholder    = !empty($tuturn_settings['empty_listing_image']['id']) ? wp_get_attachment_image_src($tuturn_settings['empty_listing_image']['id'], 'tu_blog_medium') : '';
            $default_placeholder    = !empty($default_placeholder) ? $default_placeholder[0] : $default_thumbnail;
            $view1_class            = ($view_type==='v1') ? 'tu-success-storiesvtwo' : '';
            $view_html              = '';
            $flag                   = rand(10,100)  ; 

            if(!empty($reviews)){
                foreach($reviews as $key=>$value){
                    $heading        = !empty($value['review_heading']) ? $value['review_heading'] : '';
                    $heading        = str_replace('{{','<span>',$heading);
                    $heading        = str_replace('}}','</span>',$heading);
                    $content        = !empty($value['review_content']) ? $value['review_content'] : '';
                    $review_name    = !empty($value['review_name']) ? $value['review_name'] : '';
                    $address        = !empty($value['review_address']) ? $value['review_address'] : '';
                    
                    if($view_type === 'v1'){
                        $heading        = !empty($heading) ? '<h5>'. esc_html($heading) .'</h5>' : '';
                        $content        = !empty($content) ? '<blockquote>“ '. esc_html($content) .' “</blockquote>' : '';
                        $review_name    = !empty($review_name) ? '<h4>'.esc_html($review_name).'</h4>' : '';
                        $address        = !empty($address) ? '<span>' . esc_html($address) . '</span>' : '';
                        $media          = !empty($value['review_media']['id']) ? wp_get_attachment_image_src($value['review_media']['id'], 'full') : array();
                        $media          = !empty($media) ? $media[0] : $default_placeholder;
                        $view_html      .= '<li class="splide__slide">
                                            <div class="tu-sucesstor_title tu-sucesstories">
                                                <img src="'. esc_url($media) .'" alt="'. esc_attr__('image', 'tuturn') .'">
                                                '. $heading .'
                                                '. $content .'
                                                '. $review_name .'
                                                '. $address .'
                                                <div class="tu-sucesstories_comma">
                                                    <img src="'. esc_url($comma_pattern) .'" alt="'. esc_attr__('image', 'tuturn') .'">
                                                </div>
                                            </div>
                                        </li>';
                    } else {
                        $heading        = !empty($heading) ? '<h3>'. esc_html($heading) .'</h3>' : '';
                        $content        = !empty($content) ? '<blockquote>“ '. esc_html($content) .' “</blockquote>' : '';
                        $review_name    = !empty($review_name) ? '<h4>'. esc_html($review_name) .'</h4>' : '';
                        $address        = !empty($address) ? '<span>' . esc_html($address) . '</span>' : '';
                        $media          = !empty($value['review_media']['id']) ? wp_get_attachment_image_src($value['review_media']['id'], 'tu_blog_medium') : array();
                        $media          = !empty($media) ? $media[0] : $default_placeholder;
                        $view_html .= '<li class="splide__slide">
                                            <div class="tu-sucesstor">
                                                <div class="tu-sucesstor_img">
                                                    <figure>
                                                        <img src="'. esc_url($media) .'" alt="'. esc_attr__('image', 'tuturn') .'">
                                                        <figcaption><img src="'. esc_url($comma_pattern) .'" alt="'. esc_attr__('image', 'tuturn') .'"></figcaption>
                                                    </figure>
                                                </div>
                                                <div class="tu-sucesstor_title">
                                                    '.$heading.'
                                                    '.$content.'
                                                    '. $review_name .'
                                                    '. $address .'
                                                </div>
                                            </div>
                                        </li>';
                    }
                }
            }
            if(!empty($reviews)){
                ?>
                <div>
                    <div class="tu-success-stories">
                        <div class="container">
                            <?php if (!empty($default_pattern)) { ?>
                                <div class="tu-sucesstor_pattren <?php echo esc_attr($view1_class); ?>">
                                <img src="<?php echo esc_url($default_pattern); ?>" alt="<?php esc_attr_e('image', 'tuturn'); ?>">
                                </div>
                            <?php } ?>
                            <?php if(!empty($main_heading)){
                                $main_heading  = str_replace('{{','<span>',$main_heading);
                                $main_heading  = str_replace('}}','</span>',$main_heading);
                                ?>
                                <div class="row tu-sucesstorslider_title">
                                    <div class="col-lg-8">
                                        <div class="tu-maintitle">
                                            <?php echo do_shortcode($main_heading); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div id="tu-sucessSlider-<?php echo esc_attr($flag);?>" class="splide tu-sucesstorieslider tu-splidearrow tu-splidedots">
                            <div class="splide__track">
                                <ul class="splide__list">
                                    <?php echo do_shortcode($view_html); ?>
                                </ul>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <?php
                $gap = ($view_type === 'v2') ? 100 : 24;
                $per_page = ($view_type==='v2') ? 1 : 3;
                $breakpoints    = '';
                if($view_type == 'v1'){
                    $breakpoints    = ' breakpoints: {
                        1399: {
                            perPage: 2,
                        },
                        991: {
                            perPage: 1,
                            arrows: false,
                            pagination: true,
                        },
                        767: {
                            perPage: 1,
                            arrows: false,
                            pagination: true,
                        }
                      }';
                } else {
                    $breakpoints    = ' breakpoints: {
                        991: {
                          arrows: false,
                          pagination: true,
                        }
                      }';
                }
                $slider_direction   = 'ltr';
                if ( is_rtl() ) {
                    $slider_direction   = 'rtl';
                }
                ?>
                <script>
                    jQuery(document).ready(function () {
                        var tu_sucessorslider = document.getElementById("tu-sucessSlider-<?php echo esc_js($flag);?>");

                        if(tu_sucessorslider !== null){
                            var splideslider = new Splide( "#tu-sucessSlider-<?php echo esc_js($flag);?>", {
                                direction: "<?php echo $slider_direction;?>",
                                type   : "loop",
                                perPage: <?php echo esc_js($per_page);?>,
                                perMove: 1,
                                gap: <?php echo esc_js($gap);?>,
                                pagination: false,
                                arrows: true,
                                <?php echo $breakpoints;?>
                            } );
                            splideslider.mount();
                        }
                    });
                </script>
            <?php 
            }    
        }
    }
    Plugin::instance()->widgets_manager->register(new Tuturm_reviews);
}
