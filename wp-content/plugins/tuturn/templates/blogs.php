<?php

/**
 * Template Name: Blog Template
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post, $wp, $tuturn_settings;
get_header();
$current_page_url                   = home_url(add_query_arg(array($_GET), $wp->request));
$section_col                        = 'col-xl-12';
$page_layout                        = get_post_meta($post->ID, 'tuturn_pagelayout_setting', true);
$grid_view_link                     = add_query_arg('display', 'v1', $current_page_url);
$list_view_link                     = add_query_arg('display', 'v2', $current_page_url);
$default_view                       = !empty($tuturn_settings['blog_listing_view']) ? $tuturn_settings['blog_listing_view'] : 'v2';
$blog_listing_selected_view         = !empty($_GET['display']) ? esc_html($_GET['display']) : $default_view;
$sort_by        = !empty($_GET['sort_by']) ? ($_GET['sort_by']) : 'DESC';
$select         = 'selected="selected"';
$select_asc     = !empty($sort_by) && $sort_by == 'asc' ? $select  : '';
$select_desc    = !empty($sort_by) && $sort_by == 'desc' ? $select  : '';

if (!empty($blog_listing_selected_view) &&  !in_array($blog_listing_selected_view, array('v1', 'v2', 'v3'))) {
    $blog_listing_selected_view = $default_view;
}

$page_layout_style  = !empty($page_layout) ? $page_layout : '';
$page_sidebar       = get_post_meta($post->ID, 'tuturn_pagelayout_sidebar', true);
$sidebar_selected   = !empty($page_sidebar) ? $page_sidebar : '';
$no_post_show       = !empty(get_option('posts_per_page')) ? get_option('posts_per_page') : 10;
$pg_page            = get_query_var('page') ? get_query_var('page') : 1;
$pg_paged           = get_query_var('paged') ? get_query_var('paged') : 1;
$paged              = max($pg_page, $pg_paged);
$current_page_url   = home_url(add_query_arg(array($_GET), $wp->request));
if($blog_listing_selected_view == 'v3'){
    $page_layout_style  = !empty($page_layout_style) ? 'right' : $page_layout_style;
}
if (!empty($page_layout_style) && ($page_layout_style == 'left' || $page_layout_style == 'right') && !empty($sidebar_selected) && is_active_sidebar($sidebar_selected)) {
    $section_col = 'col-xl-8 col-xxl-9';
}
?>
<div class="tu-main-section">
    <div class="container">
        <div class="row tu-blogs-bottom">
            <?php if (!empty($page_layout_style) && $page_layout_style == 'left' && !empty($sidebar_selected) && is_active_sidebar($sidebar_selected)) { ?>
                <div class="col-xl-4 col-xxl-3">
                    <div class="tu-asidewrapper">
                        <a href="javascript:void(0)" class="tu-dbmenu"><i class="icon icon-chevron-left"></i></a>
                        <div class="tu-aside-menu">
                            <?php dynamic_sidebar($sidebar_selected); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="<?php echo esc_attr($section_col); ?>">
                <?php if ($blog_listing_selected_view != 'v3') { ?>
                    <div class="tu-blogtitle">
                        <h3><?php esc_html_e('Explore whats new in trend', 'tuturn'); ?></h3>
                        <div class="tu-sortarea">
                            <div class="tu-sort-right-area">
                                <div class="tu-sortby">
                                    <div class="tu-selectv">
                                        <select class="form-control t" id="blog-sort">
                                            <option value="desc" <?php echo do_shortcode($select_desc); ?>><?php esc_html_e('New to old', 'tuturn'); ?></option>
                                            <option value="asc" <?php echo do_shortcode($select_asc); ?>><?php esc_html_e('Old to new', 'tuturn'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="tu-filter-btn">
                                    <a class="tu-listbtn <?php if (empty($_GET['display']) || $_GET['display'] != 'v1') {
                                        echo esc_attr(' active');
                                    } ?>" href="<?php echo esc_url($list_view_link); ?>"><i class="icon icon-list"></i>
                                    </a>
                                <a class="tu-listbtn <?php if (!empty($_GET['display']) && $_GET['display'] == 'v1') {
                                        echo esc_attr(' active');
                                    } ?>" href="<?php echo esc_url($grid_view_link); ?>"><i class="icon icon-grid"></i>
                                </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                    <?php
                    $args = array(
                        'posts_per_page'    => $no_post_show,
                        'paged'             => $paged,
                        'post_type'         => 'post',
                        'orderby'             => 'date',
                        'order'             => $sort_by,
                    );
                    $blog_query = new WP_Query(apply_filters('tuturn_latest_blog_post_args', $args));
                    $total_posts = $blog_query->found_posts;

                    if ($blog_query->have_posts()) {
                        while ($blog_query->have_posts()) {
                            $blog_query->the_post();
                            tuturn_get_template_part('blog-template/blog-list-view-' . $blog_listing_selected_view);
                        }
                        if (!empty($total_posts) && !empty($no_post_show) && $total_posts > $no_post_show) {
                            tuturn_paginate($blog_query, 'tu-pagination');
                        }
                        wp_reset_postdata();
                    }
                    ?>

                </div>
            </div>
            <?php if (!empty($page_layout_style) && $page_layout_style == 'right' && !empty($sidebar_selected) && is_active_sidebar($sidebar_selected)) { ?>
                <div class="col-xl-4 col-xxl-3">
                    <div class="tu-asidewrapper">
                        <a href="javascript:void(0)" class="tu-dbmenu"><i class="icon icon-chevron-left"></i></a>
                        <div class="tu-aside-menu">
                            <?php dynamic_sidebar($sidebar_selected); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
get_footer();
