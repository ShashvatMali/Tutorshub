<?php
/**
 * Blog listing view V2
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/blog-template
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post;
$post_title         = get_the_title($post->ID);
$image_id           = get_post_thumbnail_id($post->ID);
$post_img_url       = tuturn_prepare_image_source($image_id, 612, 400);
$post_url           = !empty($post->ID) ? get_the_permalink($post->ID) : '';
$contents           = !empty($post) ? get_the_excerpt($post) : '';
$author_id          = get_post_field('post_author', $post->ID);
$author_name        = get_the_author_meta('display_name', $author_id);
$post_date          = date_i18n(get_option('date_format'), strtotime(get_the_date())) ;
?>
<div class="col-sm-12">
    <div class="tu-articleitem tu-artical-list">
        <?php if(!empty($post_img_url)){ ?>
            <figure>
                <img src="<?php echo esc_url($post_img_url); ?>" alt="<?php echo esc_attr($post_title); ?>">
            </figure>
        <?php } ?>
        <div class="tu-articleinfo">
            <?php echo get_the_term_list($post->ID, 'category', '<ul class="tu-taglinks tu-taglinksm"><li>', '</li><li>', '</li></ul>'); ?>
            <?php if (!empty($post_title)) { ?>
            <div class="tu-arrticltitle">
                <h4><a href="<?php echo esc_url($post_url) ?>"><?php echo esc_html($post_title) ?></a></h4>
            </div>
        <?php } ?>
        <?php if (!empty($contents)) { ?>
            <div class="tu-description">
                <p><?php echo wp_trim_words($contents, 18, '...'); ?></p>
            </div>
        <?php } ?>
        <ul class="tu-articleauth">
            <li class="tu-articleauthor">
                <i class="icon icon-message-square"></i>
				<span><?php comments_number(esc_html__('0 Comments' , 'tuturn') , esc_html__('1 Comment' , 'tuturn') , esc_html__('% Comments' , 'tuturn')); ?></span>
            </li>
            <li class="tu-articleauthor">
                <i class="icon icon-calendar"></i>
                <span><?php echo esc_html($post_date); ?></span>
            </li>
        </ul>
        </div>
    </div>
</div>