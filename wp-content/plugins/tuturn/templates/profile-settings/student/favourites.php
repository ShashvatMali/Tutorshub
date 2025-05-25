<?php

/**
 * User favourite posts
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/Student
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post;
if (!empty($args) && is_array($args)) {
    extract($args);
}
$saved_items    = get_post_meta($profile_id, 'favourite_instructor', true);
?>
<div class="tu-favouriteitems">
    <div class="tu-boxwrapper">
        <div class="tu-boxtitle">
            <h3><?php esc_html_e('My saved instructors', 'tuturn'); ?></h3>
        </div>
        <?php if (!empty($saved_items)) {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $posts_per_page = get_option('posts_per_page');
            $tuturn_args = array(
                'post_type'         => array('in', 'tuturn-instructor'),
                'post_status'       => 'any',
                'posts_per_page'    => $posts_per_page,
                'paged'             => $paged,
                'orderby'           => 'date',
                'order'             => 'DESC',
                'post__in'          => $saved_items,
            );

            $tuturn_query = new WP_Query(apply_filters('tuturn_service_listings_args', $tuturn_args));
            $total_posts = $tuturn_query->found_posts;
            if ($tuturn_query->have_posts()) { ?>
                <ul class="tu-saveditems">
                    <?php do_action('tuturn_service_listing_before'); ?>
                    <?php
                    while ($tuturn_query->have_posts()) {
                        $tuturn_query->the_post();
                        $_is_verified       = get_post_meta($post->ID, '_is_verified', true);
                        $tuturn_options     = get_post_meta($post->ID, 'profile_details', true);
                        $tagline            = !empty($tuturn_options['tagline']) ? sanitize_text_field($tuturn_options['tagline']) : '';
                        $avatar = apply_filters(
                            'tuturn_avatar_fallback',
                            tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $post->ID),
                            array('width' => 50, 'height' => 50)
                        );
                        ?>
                        <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <?php do_action('tuturn_saved_item_before', $post); ?>
                            <div class="tu-savedwrapper">
                                <div class="tu-savedinfo">
                                    <?php if (!empty($avatar)) { ?>
                                        <figure>
                                            <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                        </figure>
                                    <?php } ?>
                                    <div class="tu-savedtites">
                                        <h4>
                                            <?php echo tuturn_get_username($post->ID); ?>
                                            <?php do_action('tuturn_saved_item_features', $post); ?>
                                            <?php if (!empty($_is_verified) && $_is_verified == 'yes') { ?>
                                                <a href="<?php the_permalink(); ?>"><i class="icon icon-check-circle tu-greenclr"></i></a>
                                            <?php } ?>
                                        </h4>
                                        <?php echo apply_filters('the_content', $tagline); ?>
                                    </div>
                                </div>
                                <div class="tu-savebtns">
                                    <a href="javascript:void(0);" class="tu-sb tu-plainbtn tu-saved-item tu-favrt-instrutor" data-profile_id="<?php echo intval($post->ID); ?>" data-current_user="<?php echo intval($user_identity); ?>">
                                        <?php esc_html_e('Remove from list', 'tuturn'); ?>
                                    </a>
                                    <a href="<?php the_permalink(); ?>" class="tu-primbtn" target="_blank"><?php esc_html_e('View profile', 'tuturn'); ?></a>
                                </div>
                            </div>
                            <?php do_action('tuturn_saved_item_before', $post); ?>
                        </li>
                    <?php
                    }
                    do_action('tuturn_service_listing_after');
                    ?>
                </ul>
                <?php
                if ($total_posts > $posts_per_page) {
                    tuturn_paginate($tuturn_query);
                }
            }
            wp_reset_postdata();
        } else {?>
            <div class="tu-emptydetails">
                <i class="icon icon-layers"></i>
                <h5><?php esc_html_e('No saved item found.', 'tuturn'); ?></h5>
            </div>
            <?php
        }
        ?>
    </div>
</div>