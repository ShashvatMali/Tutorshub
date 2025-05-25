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

if (!class_exists('Tuturn_home_banner_v3')) {
    class Tuturn_home_banner_v3 extends Widget_Base
    {

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_home_banner_v3';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Home banner v3', 'tuturn');
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
            $posts      = tuturn_elementor_get_posts(array('page'));
            $posts      = !empty($posts) ? $posts : array();
            $categories = !empty($categories) ? $categories : array();

            $this->start_controls_section(
                'content_section',
                [
                    'label'     => esc_html__('Content', 'tuturn'),
                    'tab'       => Controls_Manager::TAB_CONTENT,
                ]
            );
            $this->add_control(
                'image1',
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
                'image2',
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
                'image3',
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
                'sub_heading',
                [
                    'label'         => esc_html__('Sub heading', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::TEXTAREA,
                    'default'       => esc_html__('You can also join as parent to explore Join today', 'tuturn'),
                    'placeholder'   => esc_html__('Type your sub heading here', 'tuturn'),
                ]
            );

            $this->add_control(
                'search_input',
                [
                    'type'          => Controls_Manager::TEXT,
                    'label'       => esc_html__('Search placeholder', 'tuturn'),
                    'description'   => esc_html__('What are you looking for?', 'tuturn'),
                ]
            );

            $this->add_control(
                'search_button_text',
                [
                    'type'          => Controls_Manager::TEXT,
                    'label'         => esc_html__('Search button text', 'tuturn'),
                    'description'   => esc_html__('leave it empty. to hide it.', 'tuturn'),
                ]
            );
            $this->add_control(
                'post_categories',
                [
                    'type'          => Controls_Manager::SELECT2,
                    'label'         => esc_html__('Categories', 'tuturn'),
                    'desc'          => esc_html__('Select categories.', 'tuturn'),
                    'options'       => $categories,
                    'multiple'      => true,
                    'label_block'   => true,
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
            $settings                   = $this->get_settings_for_display();
            $heading                    = !empty($settings['main_heading']) ? $settings['main_heading'] : '';
            $sub_heading                = !empty($settings['sub_heading']) ? $settings['sub_heading'] : '';
            $search_input               = !empty($settings['search_input']) ? $settings['search_input'] : '';
            $default_knob_image         = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/knob_line.svg');
            $image1                     = !empty($settings['image1']['url']) ? $settings['image1']['url'] : '';
            $image2                     = !empty($settings['image2']['url']) ? $settings['image2']['url'] : '';
            $image3                     = !empty($settings['image3']['url']) ? $settings['image3']['url'] : '';
            $search_button_text         = !empty($settings['search_button_text']) ? $settings['search_button_text'] : '';
            $post_category_ids          = !empty($settings['post_categories']) ? $settings['post_categories'] : array();
            $instructor_search_url      = tuturn_get_page_uri('instructor_search');
            $exclude_uncategory         = !empty($tuturn_settings['hide_product_uncat'][0]) ? $tuturn_settings['hide_product_uncat'][0] : '';
            $uncategorized_obj          = !empty($exclude_uncategory) ? get_term_by( 'slug', $exclude_uncategory, 'product_cat' ) : '';
            $uncategorized_id           = !empty($uncategorized_obj) ? $uncategorized_obj->term_id : 0;

?>
            <div class="tu-bannerv3">
                <div class="tu-particles">
                    <div id="tu-particlev2"></div>
                </div>
                <?php if (!empty($image1)) { ?>
                    <div class="tu-bannerv2_img">
                        <img src="<?php echo esc_url($image1); ?>" alt="<?php esc_attr_e('Banner image', 'tuturn') ?>">
                    </div>
                <?php }
                if (!empty($image2)) { ?>
                    <div class="tu-dottedimage">
                        <img src="<?php echo esc_url($image2) ?>" alt="<?php esc_attr_e('Image', 'tuturn') ?> ">
                    </div>
                <?php }
                if (!empty($image3)) { ?>
                    <div class="tu-linedimage">
                        <img src="<?php echo esc_url($image3) ?>" alt="<?php esc_attr_e('Image', 'tuturn') ?>">
                    </div>
                <?php } ?>
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 col-xxl-7">
                            <div class="tu-banner_title">
                                <?php if (!empty($heading)) {
                                    $heading   = str_replace('{{', '<span>', $heading);
                                    $heading   = str_replace('}}', '</span>', $heading);
                                    echo do_shortcode($heading, false);
                                }
                                if (!empty($sub_heading)) { ?>
                                    <p><?php echo esc_html($sub_heading); ?></p>
                                <?php } ?>
                                <div class="tu-searchbar-wrapper">
                                    <div class="tu-appendinput">
                                        <div class="tu-starthere">
                                            <span><?php esc_html_e('Start from here', 'tuturn') ?> </span>
                                            <img src="<?php echo esc_url($default_knob_image) ?>" alt="<?php esc_attr_e('Search', 'tuturn') ?>">
                                        </div>
                                        <form id="tu-index-search-form" action="<?php echo esc_url(tuturn_get_page_uri('instructor_search')) ?>" method="GET" class="tu-searcbar">
                                            <div class="tu-searcbar">
                                                <div class="tu-inputicon">
                                                    <a href="javascript:void(0);"><i class="icon icon-search"></i></a>
                                                    <input name="keyword" type="text" class="form-control" placeholder="<?php echo esc_attr($search_input) ?>">
                                                </div>
                                                <?php
                                                if (class_exists('WooCommerce')) {
                                                    $cat_args = array(
                                                        'taxonomy'      => 'product_cat',
                                                        'orderby'       => 'name',
                                                        'order'         => 'ASC',
                                                        'hide_empty'    => false,
                                                        'parent'        => 0,
                                                    );
                                                    if(!empty($uncategorized_id)){
                                                        $cat_args['exclude'] = array($uncategorized_id);
                                                    }
                                                    $product_categories = get_terms($cat_args);

                                                    if (!empty($product_categories)) { ?>
                                                        <div class="tu-select">
                                                            <i class="icon icon-layers"></i>
                                                            <select name="categories" data-placeholder="<?php esc_html_e('Select category', 'tuturn'); ?>" data-placeholderinput="<?php esc_html_e('Select category', 'tuturn'); ?>" id="parent-category-search-dropdown" class="form-control tu-input-field select2-hidden-accessible tu-select-category" data-select2-id="parent-category-search-dropdown" tabindex="-1" aria-hidden="true">
                                                                <option value="-1"><?php esc_html_e('Select category', 'tuturn'); ?></option>
                                                                <?php
                                                                foreach ($product_categories as $key => $category) {
                                                                    if ($exclude_uncategory && $category->name == 'Uncategorized') {
                                                                        continue;
                                                                    }
                                                                ?>
                                                                    <option value="<?php echo esc_html($category->slug); ?>"><?php echo esc_html($category->name); ?></option>
                                                                <?php
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    <?php
                                                    }
                                                }

                                                if (!empty($search_button_text)) { ?>
                                                    <a href="javascript:void(0);" class="tu-primbtn-lg tu-primbtn-orange" id="tu-index-instructor-search"><?php echo esc_html($search_button_text); ?></a>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <?php if (!empty($post_category_ids)) { ?>
                                    <div class="tu-popularsearches">
                                        <h5><?php esc_html_e('Popular searches:', 'tuturn') ?></h5>
                                        <ul class="tu-popsearchitem">
                                            <?php foreach ($post_category_ids as $category) {
                                                $term_detail            = get_term($category);
                                                if (!empty($term_detail->name)) {
                                                    $instructor_search_url_ = 'javascript:void(0);';

                                                    if (!empty($instructor_search_url)) {
                                                        $instructor_search_url_ = add_query_arg('categories', esc_attr($term_detail->slug), $instructor_search_url);
                                                    }

                                                    $term_name  = !empty($term_detail->name) ? $term_detail->name : ''; ?>
                                                    <li><a href="<?php echo esc_url($instructor_search_url_) ?>"><?php echo esc_html($term_name) ?></a></li>
                                            <?php }
                                            } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php
            $typed_script   = " 
                jQuery( document ).ready(function($) {
                    let elementor_first_section = $('section.elementor-section').first().find('.tu-bannerv3');                 
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
    Plugin::instance()->widgets_manager->register(new Tuturn_home_banner_v3);
}
