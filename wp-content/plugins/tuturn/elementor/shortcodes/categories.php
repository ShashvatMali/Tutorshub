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

if (!class_exists('Tuturn_cats')) {
    class Tuturn_cats extends Widget_Base
    {

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_element_categories';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Categories', 'tuturn');
        }

        /**
         *
         * @since    1.0.0
         * @access   public
         * @var      icon
         */
        public function get_icon()
        {
            return 'eicon-product-categories';
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
                'description',
                [
                    'label'         => esc_html__('Description', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXT,
                    'placeholder'   => esc_html__('Add description or leave empty to hide', 'tuturn'),
                    'label_block'   => true
                ]
            );
            $this->add_control(
                'categories',
                [
                    'type'          => Controls_Manager::SELECT2,
                    'label'         => esc_html__('Select Categories', 'tuturn'),
                    'options'       => $categories,
                    'multiple'      => TRUE
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
            $settings                   = $this->get_settings_for_display();
            $heading                    = !empty($settings['main_heading']) ? $settings['main_heading'] : '';
            $sub_heading                = !empty($settings['sub_heading']) ? $settings['sub_heading'] : '';
            $description                = !empty($settings['description']) ? $settings['description'] : '';
            $categories                 = !empty($settings['categories']) ? $settings['categories'] : array();
            $button_text                = !empty($settings['button_text']) ? $settings['button_text'] : '';
            $button_text_link           = !empty($settings['button_text_link']['url']) ? $settings['button_text_link']['url'] : 'javascript:void(0)';
            $default_zigzag             = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/zigzag-line.svg');
            $default_cat_image          = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/placeholder.png');
            $instructor_search_url      = tuturn_get_page_uri('instructor_search');
            $rand_instructor            = rand(99, 9999);

            if (empty($categories)) {
                $categories = get_terms(['post_type' => 'product', 'taxonomy' => 'product_cat', 'fields' => 'ids', 'number' => 8]);
            }

            $slider_direction   = 'ltr';
            if ( is_rtl() ) {
                $slider_direction   = 'rtl';
            }
            ?>
            <div class="tu-main-section">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12 col-lg-8">
                            <?php if(!empty($heading) || !empty($description) || !empty($sub_heading)){?>
                                <div class="tu-maintitle text-center">
                                    <img src="<?php echo esc_url($default_zigzag); ?>" alt="<?php echo esc_attr($sub_heading);?>">
                                    <?php if(!empty($sub_heading)){ ?>
                                        <h4><?php echo esc_html($sub_heading); ?></h4>
                                    <?php } ?>
                                    <?php if(!empty($heading)){ ?>
                                        <h2><?php echo esc_html($heading); ?></h2>
                                    <?php } ?>   
                                    <?php if(!empty($description)){?>
                                        <p><?php echo esc_html($description)?></p>
                                    <?php }?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="tu-categoriesslider-<?php echo esc_attr($rand_instructor);?>" class="splide tu-categoriesslider tu-splidedots">
                        <div class="splide__track">
                            <ul class="splide__list">
                                <?php if (is_array($categories) && !empty($categories) && class_exists('WooCommerce')) {
                                    foreach($categories as $category){
                                        $term_detail        = get_term($category);
                                        $term_name          = !empty($term_detail->name) ? $term_detail->name : '';
                                        if(!empty($term_detail->term_id)){
                                            $thumbnail_id       = get_term_meta($category, 'thumbnail_id', true);
                                            $image              = !empty($thumbnail_id) ? wp_get_attachment_image_src($thumbnail_id, 'tu_category_dispaly') : '';
                                            $image              = !empty($image[0]) ? $image[0] : $default_cat_image;
                                            
                                            $instructor_posts = get_posts(array(
                                                'post_type' => 'tuturn-instructor', //post type
                                                'numberposts' => -1,
                                                'tax_query' => array(
                                                    array(
                                                        'taxonomy' => 'product_cat', //taxonomy name
                                                        'field' => 'id', //field to get
                                                        'terms' => $term_detail->term_id, //term id
                                                    )
                                                )
                                            ));

                                            $instrucotr_cat_search_url = 'javascript:void(0);';
                                            if(!empty($instructor_search_url)){
                                                $instrucotr_cat_search_url = add_query_arg('categories', esc_attr($term_detail->slug), $instructor_search_url);
                                            }

                                            $term_count = count($instructor_posts);
                                            ?>
                                            <li class="splide__slide">
                                                <a class="tu-categories_content" href="<?php echo esc_url($instrucotr_cat_search_url);?>">
                                                    <figure class="tu-categories_info">
                                                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($term_name); ?>">
                                                        <figcaption>
                                                            <div class="tu-categories_icon">
                                                                <i class="icon icon-plus"></i>
                                                            </div>
                                                            <div class="tu-categories_title">
                                                                <h5><?php echo esc_html($term_name); ?></h5>
                                                                <span><?php echo sprintf(esc_html__('%s Listings', 'tuturn'),$term_count); ?></span>
                                                            </div>
                                                        </figcaption>
                                                    </figure>
                                                </a>
                                            </li><?php
                                        } 
                                    } 
                                } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="tu-mainbtn">
                        <?php if(!empty($button_text)){ ?>
                            <a href="<?php echo esc_url($button_text_link); ?>" class="tu-primbtn-lg"><span><?php echo esc_html($button_text); ?></span><i class="icon icon-chevron-right"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <script>
            
            jQuery(document).ready(function () {
                let tu_categoriesslider = document.querySelector("#tu-categoriesslider-<?php echo esc_js($rand_instructor);?>");
                if(tu_categoriesslider !== null){
                    var splideslider = new Splide( "#tu-categoriesslider-<?php echo esc_js($rand_instructor);?>", {
                        type   : "loop",
                        direction: "<?php echo esc_js($slider_direction);?>",
                        perPage: 5,
                        perMove: 1,
                        gap: 24,
                        pagination: true,
                        arrows: false,
                        breakpoints: {
                            1399: {
                                perPage: 4,
                            },
                            1199: {
                                perPage: 3,
                            },
                            991: {
                                perPage: 2,
                            },
                            480: {
                                perPage: 1,
                            }
                        }
                    } );
                    splideslider.mount();
                    }
            });
            </script>

        <?php }
    }
    Plugin::instance()->widgets_manager->register(new Tuturn_cats);
}