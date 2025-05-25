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

if (!class_exists('Tuturn_approach_tutors')) {
    class Tuturn_approach_tutors extends Widget_Base
    {

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_approach_tutors';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Approach tutors', 'tuturn');
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
                'categories',
                [
                    'type'          => Controls_Manager::SELECT2,
                    'label'         => esc_html__('Select Categories', 'tuturn'),
                    'options'       => $categories,
                    'multiple'      => TRUE
                ]
            );
            $this->add_control(
                'sub_cat',
                [
                    'label'         => esc_html__('No of sub categories', 'tuturn'),
                    'type'          => \Elementor\Controls_Manager::NUMBER,
                    'placeholder'   => esc_html__('Number of sub categories to show', 'tuturn'),
                    'placeholder' => 3,
                    'min' => 1,
                    'max' => 50,
                    'step' => 1,
                    'default' => 5,
                ]
            );
            $this->add_control(
                'image_before',
                [
                    'type'          => Controls_Manager::MEDIA,
                    'label'         => esc_html__('Section image before', 'tuturn'),
                    'description'   => esc_html__('Add section before image.', 'tuturn'),
                    'default' => [
                        'url' => \Elementor\Utils::get_placeholder_image_src(),
                    ],
                ]
            );
            $this->add_control(
                'image_after',
                [
                    'type'          => Controls_Manager::MEDIA,
                    'label'         => esc_html__('Section image after', 'tuturn'),
                    'description'   => esc_html__('Add section after image.', 'tuturn'),
                    'default' => [
                        'url' => \Elementor\Utils::get_placeholder_image_src(),
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
            $sub_cat                    = !empty($settings['sub_cat']) ? $settings['sub_cat'] : 5;
            $categories                 = !empty($settings['categories']) ? $settings['categories'] : array();
            $instructor_search_url      = tuturn_get_page_uri('instructor_search');
            $rand_instructor            = rand(99, 9999);
            $default_zigzag             = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/zigzag-line.svg');
            $image_after                = !empty($settings['image_after']['url']) ? $settings['image_after']['url'] : '';
            $image_before               = !empty($settings['image_before']['url']) ? $settings['image_before']['url'] : '';            
            $flag                       = rand(10,100)  ; 

            if (empty($categories)) {
                $categories = get_terms(['post_type' => 'product', 'taxonomy' => 'product_cat', 'fields' => 'ids', 'number' => 8]);
            } ?>
           
            <div class="tu-footer tu-footer-<?php echo do_shortcode($flag);?>">
                <div class="container">
                    <?php if(!empty($heading) || !empty($sub_heading)){?>
                        <div class="tu-footer_maintitle">
                            <?php if(!empty($default_zigzag)) {?>
                                <img src="<?php echo esc_attr($default_zigzag); ?>" alt="<?php echo esc_attr($sub_heading);?>">
                            <?php } ?>
                            <?php if(!empty($heading)){?>
                                <h4><?php echo esc_html($heading)?></h4>
                            <?php } if(!empty($sub_heading)){?>
                                <h2><?php echo esc_html($sub_heading)?></h2>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if(is_array($categories) && !empty($categories) && class_exists('WooCommerce')){?>
                        <div class="row tu-footer_row">
                            <?php foreach($categories as $key=>$category){
                                $term_detail = get_term($category);
                                $term_name   = !empty($term_detail->name) ? $term_detail->name : '';
                                $term_slug   = !empty($term_detail->slug) ? $term_detail->slug : '';
                                $term_id     = !empty($term_detail->term_id) ? $term_detail->term_id : '';

                                $instrucotr_cat_search_url      = 'javascript:void(0);';
                                if(!empty($instructor_search_url)){
                                    $instrucotr_cat_search_url  = add_query_arg('categories', esc_attr($term_slug), $instructor_search_url);
                                }

                                if(!empty($term_name)){ ?>
                                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                        <?php if(!empty($term_name)) {?>
                                            <a href="<?php echo esc_url($instrucotr_cat_search_url);?>">
                                                <h4 class="tu-footertitle"><?php echo esc_html($term_name)?></h4>
                                            </a>
                                        <?php
                                        }
                                        $child_arg = array('hide_empty' => false,  'parent' => $term_id );
                                        $child_cat = get_terms( 'product_cat', $child_arg ); 

                                        if(!empty($child_cat)){?>
                                            <ul class="tu-footerlist">
                                                <?php 
                                                for($i=0;$i<$sub_cat;$i++){
                                                    $child_cat_info = !empty($child_cat[$i]) ? $child_cat[$i] : ''; 
                                                    if(!empty($child_cat_info)){
                                                        $sub_cat_name   = !empty($child_cat_info->name)  ? $child_cat_info->name : '';
                                                        $sub_cat_slug   = !empty($child_cat_info->slug) ? $child_cat_info->slug : '';
                                                        $sub_cat_count  = !empty($child_cat_info->count) ? $child_cat_info->count : 0;
                                                        $instructor_posts   = get_posts(array(
                                                            'post_type'     => 'tuturn-instructor', //post type
                                                            'numberposts'   => -1,
                                                            'tax_query'     => array(
                                                                array(
                                                                    'taxonomy'  => 'product_cat', //taxonomy name
                                                                    'field'     => 'id', //field to get
                                                                    'terms'     => $child_cat_info->term_id, //term id
                                                                )
                                                            )
                                                        ));
                                                        $term_count     = count($instructor_posts);

                                                        if(!empty($instructor_search_url)){
                                                            $instrucotr_sub_cat_search_url  = add_query_arg( array( 'categories' => esc_attr($term_slug), 'sub_categories[]' => $sub_cat_slug ), $instructor_search_url );
                                                        }
                                                        ?>
                                                        <li><a href="<?php echo esc_url($instrucotr_sub_cat_search_url)?>"><?php echo esc_html($sub_cat_name) ?><span>(<?php echo esc_attr($term_count);?>)</span></a></li><?php
                                                    }
                                                }

                                                if(!empty($instrucotr_cat_search_url)){?>
                                                    <li class="tu-footerlist-explore"><a href="<?php echo esc_url($instrucotr_cat_search_url)?>"><?php esc_html_e('Explore all','tuturn')?></a></li>
                                                <?php } ?>
                                            </ul>
                                        <?php } ?>
                                    </div>
                                    <?php
                                }
                            }?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <style scoped>
                .tu-footer-<?php echo esc_attr( $flag );?>::before{
                    background-image: url('<?php echo $image_before;?>');
                }
                .tu-footer-<?php echo esc_attr( $flag );?>::after{
                    background-image: url('<?php echo esc_url($image_after);?>');
                }
            </style>
            <?php
        }
    }
    Plugin::instance()->widgets_manager->register(new Tuturn_approach_tutors);
}