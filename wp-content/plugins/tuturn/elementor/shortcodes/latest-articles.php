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

if (!class_exists('Tuturn_lastest_article')) {
    class Tuturn_lastest_article extends Widget_Base
    {
        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      base
         */
        public function get_name()
        {
            return 'tuturn_element_latest_post';
        }

        /**
         *
         * @since    1.0.0
         * @access   static
         * @var      title
         */
        public function get_title()
        {
            return esc_html__('Latest article', 'tuturn');
        }

        /**
         *
         * @since    1.0.0
         * @access   public
         * @var      icon
         */
        public function get_icon()
        {
            return 'eicon-posts-grid';
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
            $post_categories = tuturn_elementor_get_taxonomies();
            $post_categories = !empty($post_categories) ? $post_categories : array();

            //Content
            $this->start_controls_section(
                'content_section',
                [
                    'label' => esc_html__('Content', 'tuturn'),
                    'tab'   => Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'title',
                [
                    'type'          => Controls_Manager::TEXT,
                    'label'         => esc_html__('Section title', 'tuturn'),
                    'description'   => esc_html__('Add text. leave it empty to hide.', 'tuturn'),
                ]
            );

            $this->add_control(
                'post_categories',
                [
                    'type'          => Controls_Manager::SELECT2,
                    'label'         => esc_html__('Categories', 'tuturn'),
                    'desc'          => esc_html__('Select categories.', 'tuturn'),
                    'options'       => $post_categories,
                    'multiple'      => true,
                    'label_block'   => true,
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
                    'default'   => 3,
                ]
            );
            $this->add_control(
                'postion',
                [
                    'type'      => Controls_Manager::SELECT2,
                    'label'     => esc_html__('Search result position', 'tuturn'),
                    'desc'      => esc_html__('Select position for display reult', 'tuturn'),
                    'default'   => 'right',
                    'options'   => [
                        'right' => esc_html__('Right ', 'tuturn'),
                        'left'  => esc_html__('Left', 'tuturn'),
                    ],
                ]
            );
            $this->add_control(
                'selected_view',
                [
                    'type'      => Controls_Manager::SELECT2,
                    'label'     => esc_html__('Select view', 'tuturn'),
                    'desc'      => esc_html__('Select view for blog main page', 'tuturn'),
                    'default'   => 'v2',
                    'options'   => [
                        'v1'    => esc_html__('V1', 'tuturn'),
                        'v2'    => esc_html__('V2', 'tuturn'),
                        'v3'    => esc_html__('V3', 'tuturn'),
                    ],
                ]
            );
            $this->add_control(
                'order_by',
                [
                    'type'      => Controls_Manager::SELECT2,
                    'label'     => esc_html__('Order by', 'tuturn'),
                    'desc'      => esc_html__('Show latest posts', 'tuturn'),
                    'default'   => 'ASC',
                    'options'   => [
                        'ASC'   => esc_html__('Ascending', 'tuturn'),
                        'DESC'  => esc_html__('Decending', 'tuturn'),
                    ]
                ]
            );
            $this->add_control(
                'show_pagination',
                [
                    'label' => esc_html__('Show Pagination', 'tuturn'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'tuturn'),
                    'label_off' => esc_html__('Hide', 'tuturn'),
                    'return_value' => 'yes',
                    'default' => 'no',
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
            global $paged, $post;
            $args = array();
            $settings           = $this->get_settings_for_display();
            $title              = !empty($settings['title']) ? $settings['title'] : '';
            $no_post_show       = !empty($settings['no_post_show']) ? $settings['no_post_show'] : 3;
            $post_category_ids  = !empty($settings['post_categories']) ? $settings['post_categories'] : array();
            $sort_by            = !empty($_GET['sort_by']) ? esc_html($_GET['sort_by']) : '';
            $order              = !empty($settings['order_by']) ? $settings['order_by'] : 'DESC';
            $selected_view      = !empty($settings['selected_view']) ? $settings['selected_view'] : 'v2';
            $show_pagination    = !empty($settings['show_pagination']) ? $settings['show_pagination'] : '';
            if(!empty($sort_by)){
                $order  = $sort_by;
            }
            $paged              = (get_query_var('paged')) ? get_query_var('paged') : 1;?>
            <div class="tu-bloggridwrapper">
                <div class="tu-blogtitle">
                    <?php if (!empty($title)) { ?>
                        <h3><?php echo esc_html($title) ?></h3>
                    <?php } ?>
                    <div class="tu-sortarea">
                        <div class="tu-sort-right-area">
                            <div class="tu-sortby">
                                <div class="tu-ordering-articles tu-select">
                                    <form id="article_search_form" action="">
                                        <select name="sort_by" class="form-control tu-selectv">
                                            <option value="desc" <?php if (!empty($sort_by) && $sort_by == 'desc') { echo esc_attr('selected'); } ?>>
                                                <?php esc_html_e('New to old', 'tuturn'); ?>
                                            </option>
                                            <option value="asc" <?php if (!empty($sort_by) && $sort_by == 'asc') { echo esc_attr('selected'); } ?>>
                                                <?php esc_html_e('Old to new', 'tuturn'); ?>
                                            </option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php

                    if (is_array($post_category_ids) && !empty($post_category_ids)) {
                        $args = array(
                            'post_type'         => 'post',
                            'paged'             => $paged,
                            'posts_per_page'    => $no_post_show,
                            'order'             => $order,
                            'category__in'      => $post_category_ids
                        );
                    } else {
                        $args = array(
                            'post_type'         => 'post',
                            'paged'             => $paged,
                            'posts_per_page'    => $no_post_show,
                            'order'             => $order,
                        );
                    }
                    $all_posts      = new \WP_Query(apply_filters('tuturn_latest_post_args', $args));
                    $total_posts    = $all_posts->found_posts;

                    if ($all_posts->have_posts()) {
                        while ($all_posts->have_posts()) {
                            $all_posts->the_post();?>
                            <?php if (!empty($selected_view)) { ?>
                                <?php tuturn_get_template_part('blog-template/blog-list-view-' . $selected_view); ?><?php
                                   }
                        }
                        if (!empty($total_posts) && !empty($no_post_show) && $total_posts > $no_post_show && !empty($show_pagination) && $show_pagination === 'yes') {
                            tuturn_paginate($all_posts, 'tu-pagination');
                        }
                        wp_reset_postdata();
                    }   ?>
                </div>
            </div>
        <?php   
        }
    }
    Plugin::instance()->widgets_manager->register(new Tuturn_lastest_article);
}
